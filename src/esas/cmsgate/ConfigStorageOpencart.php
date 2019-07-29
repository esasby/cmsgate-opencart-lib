<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 15.07.2019
 * Time: 13:14
 */

namespace esas\cmsgate;


use esas\cmsgate\utils\OpencartVersion;
use Exception;

class ConfigStorageOpencart extends ConfigStorageCms
{
    private $config;
    private $registry;

    /**
     * ConfigurationWrapperOpencart constructor.
     * @param $config
     */
    public function __construct($registry)
    {
        parent::__construct();
        $this->registry = $registry;
        $loader = $this->registry->get("load");
        $loader->model('setting/setting');
        $this->config = $this->registry->get("model_setting_setting")->getSetting(self::getSettingsName());
    }

    public static function getSettingsName() {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return Registry::getRegistry()->getPaySystemName();
            case OpencartVersion::v3_x:
                return 'payment_' . Registry::getRegistry()->getPaySystemName();
        }
    }


    /**
     * @param $key
     * @return string
     * @throws Exception
     */
    public function getConfig($key)
    {
        if (array_key_exists($key, $this->config))
            return $this->config[$key];
        else
            return "";
    }

    /**
     * @param $cmsConfigValue
     * @return bool
     * @throws Exception
     */
    public function convertToBoolean($cmsConfigValue)
    {
        return $cmsConfigValue; //уже boolean
    }

    public function createCmsRelatedKey($key) {
        return self::getSettingsName() . "_" . $key;
    }
}