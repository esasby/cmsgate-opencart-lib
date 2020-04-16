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
    private $model_setting_setting;

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
        $this->model_setting_setting = $this->registry->get("model_setting_setting");
        $this->config = $this->model_setting_setting->getSetting(self::getSettingsName());
    }

    public static function getSettingsName()
    {
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
        return ('1' == $cmsConfigValue || 'true' == $cmsConfigValue) ? true : false; //уже boolean
    }

    public function createCmsRelatedKey($key)
    {
        return self::getSettingsName() . "_" . $key;
    }


    /**
     * Сохранение значения свойства в харнилища настроек конкретной CMS.
     *
     * @param string $key
     * @throws Exception
     */
    public function saveConfig($key, $value)
    {
        /**
         * в Opencart (2.3 точно) нет возможности одним методом создать или обновить одну настройку, т.к.:
         * model_setting_setting->editSetting сперва делает делит всех настроек, а потом инсерт одной
         * model_setting_setting->editSettingValue делает update и не подходит для случая первой инициализации
         */
        $currentSettings = $this->model_setting_setting->getSetting(ConfigStorageOpencart::getSettingsName());
        $currentSettings[$key] = $value;
        $this->model_setting_setting->editSetting(ConfigStorageOpencart::getSettingsName(), $currentSettings);
        $this->config[$key] = $value;
    }
}