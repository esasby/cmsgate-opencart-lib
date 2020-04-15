<?php

namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry as CmsgateRegistry;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\Logger as CmsgateLogger;
use esas\cmsgate\view\admin\AdminViewFieldsOpencart;
use esas\cmsgate\view\ViewBuilderOpencart;
use esas\cmsgate\wrappers\SystemSettingsWrapperOpencart;
use Exception;
use Throwable as Th;

class AdminControllerExtensionPayment extends ControllerExtensionPayment
{

    public function index()
    {
        $this->showSettings();
    }

    /**
     * AdminControllerExtensionPayment constructor.
     */
    protected function showSettings()
    {
        try {
            $this->load->language('extension/payment/' . $this->extensionName);
            $this->document->setTitle($this->language->get('heading_title'));
            $data['heading_title'] = Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::ADMIN_PAYMENT_METHOD_NAME);
            $data['breadcrumbs'] = $this->createBreadcrumbs();
            $data['cancel'] = SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionsPayment();
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $configForm = Registry::getRegistry()->getConfigForm();
            $data['configForm'] = $configForm;
            $this->addExtraConfigForms($data);
            $data["messages"] = ViewBuilderOpencart::elementAdminMessages();
            $this->response->setOutput($this->load->view($this->getView(), $data));
        } catch (Th $e) {
            CmsgateLogger::getLogger("ShowSettings")->error("Exception", $e);
        } catch (Exception $e) { // для совместимости с php 5
            CmsgateLogger::getLogger("ShowSettings")->error("Exception", $e);
        }
    }

    /**
     * При необходимости отображения дополнительный групп настроек, этот метод должен быть переопределен
     */
    public function addExtraConfigForms(&$data)
    {
    }

    public function savesettings($configForm = null)
    {
        try {
            if ($configForm == null)
                $configForm = CmsgateRegistry::getRegistry()->getConfigForm();
            if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
                $configForm->validate();
                $configForm->save();
            }
        } catch (Th $e) {
            CmsgateLogger::getLogger("SaveSettings")->error("Exception", $e);
        } catch (Exception $e) { // для совместимости с php 5
            CmsgateLogger::getLogger("SaveSettings")->error("Exception", $e);
        }
        $this->showSettings();
    }

    protected function createBreadcrumbs()
    {
        $breadcrumbs[] = array(
            'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::BREADCRUMBS_MAIN),
            'href' => SystemSettingsWrapperOpencart::getInstance()->linkAdminHome(),
            'separator' => false
        );
        $breadcrumbs[] = array(
            'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::BREADCRUMBS_EXTENSIONS),
            'href' => SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensions(),
        );
        $breadcrumbs[] = array(
            'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::BREADCRUMBS_EXTENSIONS_PAYMENTS),
            'href' => SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionsPayment(),
        );
        $breadcrumbs[] = array(
            'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::ADMIN_PAYMENT_METHOD_NAME),
            'href' => SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionSettings(),
            'separator' => ' :: '
        );// Кнопки

        return $breadcrumbs;
    }

}