<?php

namespace esas\cmsgate\opencart;

use bgpb\cmsgate\RegistryBGPBOpencart;
use esas\cmsgate\Registry as CmsgateRegistry;
use esas\cmsgate\utils\Logger as CmsgateLogger;
use esas\cmsgate\view\ElementsOpencart;
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
            $data['heading_title'] = $this->language->get('heading_title');// Генерация хлебных крошек
            $data['breadcrumbs'] = $this->createBreadcrumbs();
            $data['cancel'] = RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkAdminExtensionsPayment();
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->i18n($data, ['heading_title', 'text_status', 'text_enabled', 'text_disabled', 'text_save', 'text_cancel']);
            $configForm = RegistryBGPBOpencart::getRegistry()->getConfigForm();
            $data['configForm'] = $configForm;
            $this->addExtraConfigForms($data);
            $data["messages"] = ElementsOpencart::elementMessages();
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
            'text' => $this->language->get('text_home'),
            'href' => RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkAdminHome(),
            'separator' => false
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('text_extension'),
            'href' => RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkAdminExtensionsPayment(),
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('heading_title'),
            'href' => RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkAdminExtensionSettings(),
            'separator' => ' :: '
        );// Кнопки
        return $breadcrumbs;
    }
}