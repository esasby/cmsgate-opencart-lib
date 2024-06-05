<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 13.04.2020
 * Time: 12:23
 */

namespace esas\cmsgate;


use esas\cmsgate\descriptors\CmsConnectorDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\lang\LocaleLoaderOpencart;
use esas\cmsgate\opencart\ModelExtensionPayment;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormOpencart;
use esas\cmsgate\wrappers\OrderWrapper;
use esas\cmsgate\wrappers\OrderWrapperOpencart;
use esas\cmsgate\wrappers\SystemSettingsWrapperOpencart;

class CmsConnectorOpencart extends CmsConnector
{
    protected $opencartRegistry;

    /**
     * @param $registry
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getOpencartSession()
    {
        return $this->getOpencartRegistry()->get('session');
    }

    /**
     * @return mixed
     */
    public function getOpencartRegistry()
    {
        if (!isset($this->opencartRegistry) && isset($GLOBALS['registry']))
            $this->opencartRegistry = $GLOBALS['registry'];
        return $this->opencartRegistry;
    }

    /**
     * Для удобства работы в IDE и подсветки синтаксиса.
     * @return $this
     */
    public static function getInstance()
    {
        return Registry::getRegistry()->getCmsConnector();
    }

    public function createCommonConfigForm($managedFields)
    {
        $configForm = new ConfigFormOpencart(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionSettings("commonConfigFormAction"),
            null,
            $this->getOpencartRegistry());
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_SAVE);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_DOWNLOAD_LOG);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_CANCEL);
//        $configForm->addSubmitButton(RequestParams::SAVE_AND_EXIT_BUTTON);
        $configForm->addCmsManagedFields();
        return $configForm;
    }

    public function createSystemSettingsWrapper()
    {
        return new SystemSettingsWrapperOpencart($this->getOpencartRegistry());
    }


    /**
     * По локальному id заказа возвращает wrapper
     * @param $orderId
     * @return OrderWrapper
     */
    public function createOrderWrapperByOrderId($orderId)
    {
        return new OrderWrapperOpencart($orderId, $this->getOpencartRegistry());
    }

    /**
     * Возвращает OrderWrapper для текущего заказа текущего пользователя
     * @return OrderWrapper
     */
    public function createOrderWrapperForCurrentUser()
    {
        $orderId = $this->getOpencartSession()->data['order_id']; //todo check
        return $this->createOrderWrapperByOrderId($orderId);
    }

    /**
     * По номеру транзакции внешней система возвращает wrapper
     * @param $extId
     * @return OrderWrapper
     */
    public function createOrderWrapperByExtId($extId)
    {
        $opencartCmsgateModel = new ModelExtensionPayment($this->getOpencartRegistry());
        $orderId = $opencartCmsgateModel->getOrderIdByExtId($extId);
        if ($orderId == null || $orderId == '0')
            return null;
        return $this->createOrderWrapperByOrderId($orderId);
    }

    public function createConfigStorage()
    {
        return new ConfigStorageOpencart($this->getOpencartRegistry());
    }

    public function createLocaleLoader()
    {
        return new LocaleLoaderOpencart($this->getOpencartRegistry());
    }

    public function createCmsConnectorDescriptor()
    {
        return new CmsConnectorDescriptor(
            "cmsgate-opencart-lib",
            new VersionDescriptor(
                "v1.17.1",
                "2022-04-11"
            ),
            "Cmsgate Opencart connector",
            "https://bitbucket.esas.by/projects/CG/repos/cmsgate-opencart-lib/browse",
            VendorDescriptor::esas(),
            "opencart"
        );
    }

}