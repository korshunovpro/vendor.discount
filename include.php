<?php
/**
 * Required for autoload!
 */

// CITY
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale', 'OnCondSaleControlBuildList',
    array(
        '\Vendor\Discount\Condition\DeliveryCity',
        'GetControlDescr'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale', 'OnCondCatControlBuildList',
    array(
        '\Vendor\Discount\Condition\DeliveryCity',
        'GetControlDescr'
    )
);

// COOKIE
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale', 'OnCondSaleControlBuildList',
    array(
        '\Vendor\Discount\Condition\Session',
        'GetControlDescr'
    )
);

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale', 'OnCondCatControlBuildList',
    array(
        '\Vendor\Discount\Condition\Session',
        'GetControlDescr'
    )
);