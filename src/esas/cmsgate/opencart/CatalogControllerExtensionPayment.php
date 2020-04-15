<?php

namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\CMSGateException;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\view\client\ClientViewFieldsOpencart;
use esas\cmsgate\view\ViewBuilderOpencart;
use esas\cmsgate\view\ViewFields;
use esas\cmsgate\view\ViewUtils;
use esas\cmsgate\wrappers\SystemSettingsWrapperOpencart;
use Exception;
use Throwable;

abstract class CatalogControllerExtensionPayment extends ControllerExtensionPayment
{

    /**
     * Безопасная обертка с отображение ошибок клиенту
     * @return mixed
     */
    public function index()
    {
        $data = array();
        try {
            $orderWrapper = Registry::getRegistry()->getOrderWrapper($this->session->data['order_id']);
            $this->addPaySystemIndexData($data, $orderWrapper);
        } catch (CMSGateException $e) {
            Logger::getLogger("confirmPage")->error("Exception:", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getClientMsg());
        } catch (Throwable $e) {
            Logger::getLogger("confirmPage")->error("Exception:", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            Logger::getLogger("confirmPage")->error("Exception:", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getMessage());
        }
        $this->addCommonIndexData($data);
        return $this->load->view($this->getView(Registry::getRegistry()->getPaySystemName()), $data);
    }

    protected function addCommonIndexData(&$data) {
        $data['sandboxMessage'] = ViewBuilderOpencart::elementSandboxMessage();
        $data['messages'] = ViewBuilderOpencart::elementClientMessages();
    }

    /**
     * @param $data
     * @param $orderWrapper
     * @throws Throwable
     */
    protected abstract function addPaySystemIndexData(&$data, $orderWrapper);

    protected function addCommon(&$data)
    {
        $data['breadcrumbs'] = $this->createBreadcrumbs();
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
    }

    protected function addCheckoutContinueButton(&$data)
    {
        $data['buttonContinue'] = ViewBuilderOpencart::elementButtonContinue(
            SystemSettingsWrapperOpencart::getInstance()->linkCatalogCheckoutSuccess(),
            Registry::getRegistry()->getTranslator()->translate(ViewFields::BUTTON_CONTINUE));
    }

    protected function createBreadcrumbs()
    {
        $breadcrumbs = array();
        $breadcrumbs[] = $this->createBreadcrumb(ClientViewFieldsOpencart::BREADCRUMBS_HOME, 'common/home');
        $breadcrumbs[] = $this->createBreadcrumb(ClientViewFieldsOpencart::BREADCRUMBS_BASKET, 'checkout/cart');
        $breadcrumbs[] = $this->createBreadcrumb(ClientViewFieldsOpencart::BREADCRUMBS_CHECKOUT, 'checkout/checkout');
        $breadcrumbs[] = $this->createBreadcrumb(ClientViewFieldsOpencart::BREADCRUMBS_SUCCESS, 'checkout/success');
        return $breadcrumbs;
    }

    private function createBreadcrumb($text, $link)
    {
        return array(
            'text' => Registry::getRegistry()->getTranslator()->translate($text),
            'href' => $this->url->link($link)
        );
    }



    protected function failure($error)
    {
        $this->session->data['error'] = $error;
        $this->response->redirect(SystemSettingsWrapperOpencart::getInstance()->linkCatalogCheckout());
    }

    protected function redirectFailure($loggerName, $ex)
    {
        $this->session->data['error'] = ViewUtils::logAndGetMsg($loggerName, $ex);
        $this->response->redirect(SystemSettingsWrapperOpencart::getInstance()->linkCatalogCheckout());
    }


}
