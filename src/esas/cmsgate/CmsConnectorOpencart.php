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
    private $session;

    /**
     * @param $registry
     */
    public function __construct($registry)
    {
        parent::__construct();
        $this->opencartRegistry = $registry;
        $this->session = $registry->get('session');
    }

    /**
     * Для удобства работы в IDE и подсветки синтаксиса.
     * @return $this
     */
    public static function getInstance() {
        return Registry::getRegistry()->getSystemSettingsWrapper();
    }

    public function createCommonConfigForm($managedFields)
    {
        $configForm  = new ConfigFormOpencart(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionSettings("commonConfigFormAction"),
            null,
            $this->opencartRegistry);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_SAVE);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_DOWNLOAD_LOG);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_CANCEL);
//        $configForm->addSubmitButton(RequestParams::SAVE_AND_EXIT_BUTTON);
        $configForm->addCmsManagedFields();
        return $configForm;
    }

    public function createSystemSettingsWrapper()
    {
        return new SystemSettingsWrapperOpencart($this->opencartRegistry);
    }


    /**
     * По локальному id заказа возвращает wrapper
     * @param $orderId
     * @return OrderWrapper
     */
    public function createOrderWrapperByOrderId($orderId)
    {
        return new OrderWrapperOpencart($orderId, $this->opencartRegistry);
    }

    /**
     * Возвращает OrderWrapper для текущего заказа текущего пользователя
     * @return OrderWrapper
     */
    public function createOrderWrapperForCurrentUser()
    {
        $orderId = $this->session->data['order_id']; //todo check
        return $this->createOrderWrapperByOrderId($orderId);
    }

    /**
     * По номеру транзакции внешней система возвращает wrapper
     * @param $extId
     * @return OrderWrapper
     */
    public function createOrderWrapperByExtId($extId)
    {
        $opencartCmsgateModel = new ModelExtensionPayment($this->opencartRegistry);
        $orderId = $opencartCmsgateModel->getOrderIdByExtId($extId);
        if ($orderId == null || $orderId == '0')
            return null;
        return $this->createOrderWrapperByOrderId($orderId);
    }

    public function createConfigStorage()
    {
        return new ConfigStorageOpencart($this->opencartRegistry);
    }

    public function createLocaleLoader()
    {
        return new LocaleLoaderOpencart($this->opencartRegistry);
    }

    public function createCmsConnectorDescriptor()
    {
        return new CmsConnectorDescriptor(
            "cmsgate-opencart-lib",
            new VersionDescriptor(
                "v1.13.1",
                "2021-01-04"
            ),
            "Cmsgate Opencart connector",
            "https://bitbucket.esas.by/projects/CG/repos/cmsgate-opencart-lib/browse",
            VendorDescriptor::esas(),
            "opencart"
        );
    }

    public function getConstantConfigValue($key)
    {
        switch ($key) {
            case ConfigFields::useOrderNumber():
                return true;
            default:
                return parent::getConstantConfigValue($key);
        }
    }


}