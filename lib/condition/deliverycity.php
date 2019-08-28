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
 * Class DeliveryCity
 * @package Vendor\Discount\Condition
 *
 * @todo: нужно добработать что бы был выбор местоположений, а не ввод текстом
 */
class DeliveryCity extends \CSaleCondCtrlComplex
{
    const DELIVERY_CITY_CONTROL_ID = 'CondDeliveryCity';

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
            self::DELIVERY_CITY_CONTROL_ID
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
            'label' => GetMessage('VENDOR_DISCOUNT_COND_DELIVERY_CITY_GROUP_NAME'),
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
            self::DELIVERY_CITY_CONTROL_ID => [
                'ID'         => self::DELIVERY_CITY_CONTROL_ID,
                'FIELD'      => 'COND_DELIVERY_CITY',
                'FIELD_TYPE' => 'string',
                'MULTIPLE'   => 'N',
                'GROUP'      => 'N',
                'LABEL'      => GetMessage('VENDOR_DISCOUNT_COND_DELIVERY_CITY_LABEL'),
                'PREFIX'     => GetMessage('VENDOR_DISCOUNT_COND_DELIVERY_CITY_PREFIX'),
                'LOGIC'      => static::getLogic([BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ]),
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

        if ($controlId === self::DELIVERY_CITY_CONTROL_ID) {
            $arControl = static::GetControls($controlId);

            $arValues = static::check($arOneCondition, $arParams, $arControl, false);

            if ($arValues && !empty($arValues['value'])) {
                $type = $arValues['logic'];
                $value = $arValues['value'];
                $result = static::getClassName() . "::checkCondition({$arParams['ORDER']}, '{$value}', '{$type}')";
            }
        }
        return $result;
    }

    /**
     * @param $arOrder
     * @param $value
     * @param $type
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     */
    public static function checkCondition($arOrder, $value, $type)
    {
        $result = false;

        if($arOrder['DELIVERY_LOCATION'] && $value) {
            $arLocation = (\Bitrix\Sale\Location\LocationTable::getByCode($arOrder['DELIVERY_LOCATION']))->fetch();

            if ($arLocation) {
                $arLocationName = (\Bitrix\Sale\Location\Name\LocationTable::getList(
                    ['filter' => ['=LOCATION_ID' => $arLocation['ID'], 'LANGUAGE_ID' => LANGUAGE_ID]]
                ))->fetch();
            }

            if ($type == 'Not' && !empty($arLocationName['NAME_UPPER']) && $arLocationName['NAME_UPPER'] !== strtoupper($value)) {
                $result = true;
            } else if ($type == 'Equal' && !empty($arLocationName['NAME_UPPER']) && $arLocationName['NAME_UPPER'] === strtoupper($value)) {
                $result = true;
            }
        }

        return $result;
    }
}
