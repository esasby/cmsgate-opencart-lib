<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.08.2018
 * Time: 12:21
 */

namespace esas\cmsgate;


class ConfigFieldsOpencart
{
    private static $cmsKeys;

    /**
     * В некоторых CMS используются определенные соглашения по именования настроек модулей (чаще всего префиксы).
     * Данный метод позволяет использовать в core cms-зависимые ключи (например на client view, при формировании html и т.д.)
     * @param $localkey
     * @return mixed
     */
    public static function getCmsRelatedKey($localkey)
    {
        if (self::$cmsKeys == null || !in_array($localkey, self::$cmsKeys)) {
            self::$cmsKeys[$localkey] = Registry::getRegistry()->getConfigWrapper()->createCmsRelatedKey($localkey);
        }
        return self::$cmsKeys[$localkey];
    }

    public static function status()
    {
        return self::getCmsRelatedKey("status");
    }

    public static function sortOrder()
    {
        return self::getCmsRelatedKey("sort_order");
    }
}