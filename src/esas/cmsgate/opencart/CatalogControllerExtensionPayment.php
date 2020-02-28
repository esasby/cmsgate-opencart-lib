<?php

namespace esas\cmsgate\opencart;

use bgpb\cmsgate\RegistryBGPBOpencart;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use esas\cmsgate\view\Messages;
use esas\cmsgate\view\ViewUtils;

class CatalogControllerExtensionPayment extends ControllerExtensionPayment
{
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

    protected function createBreadcrumbs()
    {
        $breadcrumbs = array();
        $breadcrumbs[] = $this->createBreadcrumb('text_home', 'common/home');
        $breadcrumbs[] = $this->createBreadcrumb('text_basket', 'checkout/cart');
        $breadcrumbs[] = $this->createBreadcrumb('text_checkout', 'checkout/checkout');
        $breadcrumbs[] = $this->createBreadcrumb('text_success', 'checkout/success');
        return $breadcrumbs;
    }

    private function createBreadcrumb($text, $link)
    {
        return array(
            'text' => $this->language->get($text),
            'href' => $this->url->link($link)
        );
    }

    /**
     * Для добавления информационной надписи о режими "sandbox"
     * @return string
     */
    protected function elementSandboxMessage()
    {
        if (Registry::getRegistry()->getConfigWrapper()->isSandbox()) {
            return
                element::div(
                    attribute::clazz("alert alert-info"),
                    element::content(Registry::getRegistry()->getTranslator()->translate(Messages::SANDBOX_MODE_IS_ON))
                );
        } else
            return "";
    }

    protected function failure($error)
    {
        $this->session->data['error'] = $error;
        $this->response->redirect(RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkCatalogCheckout());
    }

    protected function redirectFailure($loggerName, $ex)
    {
        $this->session->data['error'] = ViewUtils::logAndGetMsg($loggerName, $ex);
        $this->response->redirect(RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkCatalogCheckout());
    }
}
