<?php
/**
 * vendor.discount
 *
 * @author Sergey Korshunov <sergey@korshunov.pro>
 * @copyright 2019 Sergey Korshunov
 */

namespace Vendor\Discount\Condition;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class Session
 * @package Vendor\Discount\Condition
 */
class Session extends \CSaleCondCtrlComplex
{
    const SESSION_CONTROL_ID = 'CondSession';

    /**
     * @return string
     */
    public static function GetClassName()
    {
        return __CLASS__;
    }

    /**
     * @return array|string
     */
    public static function GetControlID()
    {
        return [
            self::SESSION_CONTROL_ID
        ];
    }

    /**
     * @return array
     */
    public static function GetControlDescr()
    {
        $description = parent::GetControlDescr();
        $description['SORT'] = 1;
        return $description;
    }

    /**
     * @param $arControls
     * @return array
     */
    public static function GetShowIn($arControls)
    {
        if (!is_array($arControls))
            $arControls = array($arControls);
        return array_values(array_unique($arControls));
    }

    /**
     * @param $arParams
     * @return array
     */
    public static function GetControlShow($arParams)
    {
        $arControls = static::GetControls();
        $arResult = [
            'controlgroup' => true,
            'group' =>  true,
            'label' => GetMessage('VENDOR_DISCOUNT_COND_SESSION_GROUP_NAME'),
            'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
            'children' => []
        ];
        
        foreach ($arControls as &$arOneControl) {
            $arResult['children'][] = [
                'controlId' => $arOneControl['ID'],
                'group' => false,
                'label' => $arOneControl['LABEL'],
                'showIn' => static::GetShowIn($arParams['SHOW_IN_GROUPS']),
                'control' => [
                    $arOneControl['PREFIX'],
                    static::GetLogicAtom($arOneControl['LOGIC']),
                    static::GetValueAtom($arOneControl['JS_VALUE'])
                ]
            ];
        }
        unset($arOneControl);     
        
        return $arResult;
    }

    /**
     * @param bool $controlId
     * @return array|bool|mixed
     */
    public static function GetControls($controlId = false)
    {
        $controlList = [
            self::SESSION_CONTROL_ID => [
                'ID'         => self::SESSION_CONTROL_ID,
                'FIELD'      => 'COND_SESSION',
                'FIELD_TYPE' => 'string',
                'MULTIPLE'   => 'N',
                'GROUP'      => 'N',
                'LABEL'      => GetMessage('VENDOR_DISCOUNT_COND_SESSION_LABEL'),
                'PREFIX'     => GetMessage('VENDOR_DISCOUNT_COND_SESSION_PREFIX'),
                'LOGIC'      => static::getLogic([BT_COND_LOGIC_EQ]),
                'JS_VALUE' => array(
                    'type' => 'input',
                ),
                'PHP_VALUE' => [],
            ]
        ];

        foreach ($controlList as &$control) {
            if (!isset($control['PARENT'])) {
                $control['PARENT'] = true;
            }
            $control['MULTIPLE'] = 'N';
            $control['GROUP'] = 'N';
        }
        unset($control);

        if (false === $controlId) {
            return $controlList;
        }
        elseif (isset($controlList[$controlId])) {
            return $controlList[$controlId];
        }
        else {
            return false;
        }
    }

    /**
     * @param $arOneCondition
     * @param $arParams
     * @param $controlId
     * @param bool $subs
     * @return bool|mixed|string
     */
    public static function Generate($arOneCondition, $arParams, $controlId, $subs = false)
    {
        $result = '';
        if ($controlId === self::SESSION_CONTROL_ID) {
            $arControl = static::GetControls($controlId);

            $arValues = static::check($arOneCondition, $arParams, $arControl, false);

            if ($arValues && !empty($arValues['value'])) {
                $value = explode(';', $arValues['value']);
                $result = static::getClassName() . "::checkCondition({$value[0]}, '{$value[1]}')";
            }
        }
        return $result;
    }

    /**
     * @param $sessionVar
     * @param $value
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function checkCondition($sessionVar, $value)
    {
        $result = false;
        if(!empty($_SESSION[$sessionVar]) && $_SESSION[$sessionVar] == $value) {
            $result = true;
        }
        return $result;
    }
}
