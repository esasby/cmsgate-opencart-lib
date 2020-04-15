<?php

use esas\cmsgate\view\admin\AdminViewFieldsOpencart;
use esas\cmsgate\view\client\ClientViewFieldsOpencart;

return array(
    AdminViewFieldsOpencart::BREADCRUMBS_MAIN => 'Главная',
    AdminViewFieldsOpencart::BREADCRUMBS_EXTENSIONS => 'Расширения',
    AdminViewFieldsOpencart::BREADCRUMBS_EXTENSIONS_PAYMENTS => 'Оплата',

    AdminViewFieldsOpencart::MODULE_SORT_ORDER_LABEL => 'Порядок сортировки',
    AdminViewFieldsOpencart::MODULE_SORT_ORDER_DESCRIPTION => 'Определяет порядок отображения модулей оплаты клиенту',
    AdminViewFieldsOpencart::MODULE_STATUS_LABEL => 'Статус',
    AdminViewFieldsOpencart::MODULE_STATUS_DESCRIPTION => 'Разрешен ли прием платежей через данный модуль',
    AdminViewFieldsOpencart::MODULE_STATUS_ENABLE => 'Включен',
    AdminViewFieldsOpencart::MODULE_STATUS_DISABLE => 'Выключен',

    ClientViewFieldsOpencart::BREADCRUMBS_HOME => 'Главная',
    ClientViewFieldsOpencart::BREADCRUMBS_BASKET => 'Корзина',
    ClientViewFieldsOpencart::BREADCRUMBS_CHECKOUT => 'Оформление',
    ClientViewFieldsOpencart::BREADCRUMBS_SUCCESS => 'Успех',
);