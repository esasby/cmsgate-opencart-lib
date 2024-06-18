<?php

namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry;
use esas\cmsgate\Registry as CmsgateRegistry;
use esas\cmsgate\utils\FileUtils;
use esas\cmsgate\utils\Logger as CmsgateLogger;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\view\admin\AdminViewFieldsOpencart;
use esas\cmsgate\view\ViewBuilderOpencart;
use esas\cmsgate\wrappers\SystemSettingsWrapperOpencart;
use esas\cmsgate\utils\OpencartVersion;
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
            $this->load->language($this->getView());
            $this->document->setTitle($this->language->get('heading_title'));
            $data['heading_title'] = Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::ADMIN_PAYMENT_METHOD_NAME);
            $data['breadcrumbs'] = $this->createBreadcrumbs();
            $data['cancel'] = SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionsPayment();
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $configForm = Registry::getRegistry()->getConfigForm();
            $data['configForm'] = $configForm;
            $data['old_style'] = in_array(OpencartVersion::getVersion(), array(OpencartVersion::v2_1_x, OpencartVersion::v2_3_x, OpencartVersion::v3_x)) ? true : false;
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

    public function commonConfigFormAction()
    {
        try {
            if (isset($this->request->post[AdminViewFieldsOpencart::CONFIG_FORM_BUTTON_DOWNLOAD_LOG])) {
                FileUtils::downloadByPath(Logger::getLogFilePath());
            } else if (isset($this->request->post[AdminViewFieldsOpencart::CONFIG_FORM_BUTTON_CANCEL])) {
                $this->response->redirect(SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionsPayment());
            } else {
                $this->savesettings(CmsgateRegistry::getRegistry()->getConfigForm());
            }
        } catch (Th $e) {
            CmsgateLogger::getLogger("SaveSettings")->error("Exception", $e);
        } catch (Exception $e) { // для совместимости с php 5
            CmsgateLogger::getLogger("SaveSettings")->error("Exception", $e);
        }
        $this->showSettings();
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
        $adminHomeLink = SystemSettingsWrapperOpencart::getInstance()->linkAdminHome();
        if ($adminHomeLink  !== '') {
            $breadcrumbs[] = array(
                'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::BREADCRUMBS_MAIN),
                'href' => $adminHomeLink,
                'separator' => false
            );
        }
        $extensionsLink = SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensions();
        if ($extensionsLink  !== '') {
            $breadcrumbs[] = array(
                'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::BREADCRUMBS_EXTENSIONS),
                'href' => $extensionsLink,
            );
        }
        $extensionsPaymentLink = SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionsPayment();
        if ($extensionsPaymentLink  !== '') {
            $breadcrumbs[] = array(
                'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::BREADCRUMBS_EXTENSIONS_PAYMENTS),
                'href' => $extensionsPaymentLink,
            );
        }
        $extensionSettingsLink = SystemSettingsWrapperOpencart::getInstance()->linkAdminExtensionSettings();
        if (isset($extensionSettingsLink)) {
            $breadcrumbs[] = array(
                'text' => Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::ADMIN_PAYMENT_METHOD_NAME),
                'href' => $extensionSettingsLink,
                'separator' => ' :: '
            );// Кнопки
        }

        return $breadcrumbs;
    }

}