<?php
namespace esas\cmsgate\opencart;

use esas\cmsgate\utils\Logger as CmsgateLogger;
use esas\cmsgate\Registry as CmsgateRegistry;
use esas\cmsgate\utils\OpencartVersion;
use Exception;
use Throwable as Th;

class AdminControllerExtensionPayment extends ControllerExtensionPayment
{
    /**
     * AdminControllerExtensionPayment constructor.
     */
    protected function showSettings()
    {
        try {
            $this->load->language('extension/payment/' . $this->extensionName);
            $this->document->setTitle($this->language->get('heading_title'));
            $configForm = CmsgateRegistry::getRegistry()->getConfigForm();
            $data['configForm'] = $configForm;// Сохранение или обновление данных
            if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($configForm->getManagedFields()->validateAll($this->request->post))) {
                $this->load->model('setting/setting');
                $this->editSettings();
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->linkExtensionsPayment());
            }// Установка языковых констант
            $data['heading_title'] = $this->language->get('heading_title');// Генерация хлебных крошек
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->linkHome(),
                'separator' => false
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_extension'),
                'href' => $this->linkExtensionsPayment(),
            );
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->linkExtensionSettings(),
                'separator' => ' :: '
            );// Кнопки
            $data['action'] = $this->linkExtensionSettings();
            $data['cancel'] = $this->linkExtensionsPayment();
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $this->i18n($data, ['heading_title', 'text_status', 'text_enabled', 'text_disabled', 'text_save', 'text_cancel']);
            $this->response->setOutput($this->load->view($this->getView(), $data));
        } catch (Th $e) {
            CmsgateLogger::getLogger("ControllerExtensionPaymentHutkiGrosh")->error("Exception", $e);
        } catch (Exception $e) { // для совместимости с php 5
            CmsgateLogger::getLogger("ControllerExtensionPaymentHutkiGrosh")->error("Exception", $e);
        }
    }

    public function editSettings() {
        switch (OpencartVersion::getVersion()){
            case OpencartVersion::v2_3_x:
                $this->model_setting_setting->editSetting($this->extensionName, $this->request->post);
                return;
            case OpencartVersion::v3_x:
                $this->model_setting_setting->editSetting('payment_' . $this->extensionName, $this->request->post);
                return;
        }
    }

    public function linkExtensionsPayment() {
        switch (OpencartVersion::getVersion()){
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        }
    }

    public function linkExtensionSettings() {
        switch (OpencartVersion::getVersion()){
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/payment/' . $this->extensionName, 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('extension/payment/' . $this->extensionName, 'user_token=' . $this->session->data['user_token'], true);
        }
    }

    public function linkHome() {
        switch (OpencartVersion::getVersion()){
            case OpencartVersion::v2_3_x:
                return $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        }
    }

}