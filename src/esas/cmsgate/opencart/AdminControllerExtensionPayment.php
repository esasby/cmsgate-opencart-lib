<?php

namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry as CmsgateRegistry;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\Logger as CmsgateLogger;
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
            $this->load->language('extension/payment/' . $this->extensionName);
            $this->document->setTitle($this->language->get('heading_title'));
            $data['heading_title'] = $this->language->get('heading_title');// Генерация хлебных крошек
            $data['breadcrumbs'] = $this->createBreadcrumbs();
            $data['cancel'] = $this->linkExtensionsPayment();
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->i18n($data, ['heading_title', 'text_status', 'text_enabled', 'text_disabled', 'text_save', 'text_cancel']);
            $configForm = CmsgateRegistry::getRegistry()->getConfigForm();
            $configForm->setSubmitUrl($this->linkExtensionSettings("savesettings")); //todo перенести в Registry
            $data['configForm'] = $configForm;
            $this->addExtraConfigForms($data);
            $data["messages_info"] = Registry::getRegistry()->getMessenger()->getInfoMessages();
            $data["messages_error"] = Registry::getRegistry()->getMessenger()->getErrorMessages();
            $data["messages_warn"] = Registry::getRegistry()->getMessenger()->getWarnMessages();
            $this->response->setOutput($this->load->view($this->getView(), $data));
        } catch (Th $e) {
            CmsgateLogger::getLogger("ControllerExtensionPaymentHutkiGrosh")->error("Exception", $e);
        } catch (Exception $e) { // для совместимости с php 5
            CmsgateLogger::getLogger("ControllerExtensionPaymentHutkiGrosh")->error("Exception", $e);
        }
    }

    /**
     * При необходимости отображения дополнительный групп настроек, этот метод должен быть переопределен
     */
    public function addExtraConfigForms(&$data)
    {
    }

    public function savesettings()
    {
        $configForm = CmsgateRegistry::getRegistry()->getConfigForm();
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($configForm->getManagedFields()->validateAll($this->request->post))) {
            $this->load->model('setting/setting');
            CmsgateRegistry::getRegistry()->getConfigWrapper()->saveConfigs($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->linkExtensionsPayment());
        } else
            $this->showSettings();
    }

    protected function createBreadcrumbs()
    {
        $breadcrumbs[] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->linkHome(),
            'separator' => false
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->linkExtensionsPayment(),
        );
        $breadcrumbs[] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->linkExtensionSettings(),
            'separator' => ' :: '
        );// Кнопки
        return $breadcrumbs;
    }

    protected function linkExtensionsPayment()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        }
    }

    protected function linkExtensionSettings($action = null)
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/payment/' . $this->extensionName . ($action != null ? '/' . $action : ""), 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('extension/payment/' . $this->extensionName . ($action != null ? '/' . $action : ""), 'user_token=' . $this->session->data['user_token'], true);
        }
    }

    protected function linkHome()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        }
    }

}