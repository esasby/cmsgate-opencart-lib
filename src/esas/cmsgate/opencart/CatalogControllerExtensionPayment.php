<?php

namespace esas\cmsgate\opencart;

use bgpb\cmsgate\RegistryBGPBOpencart;

class CatalogControllerExtensionPayment extends ControllerExtensionPayment
{
    protected function addCommon(&$data) {
        $data['breadcrumbs'] = $this->createBreadcrumbs();

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
    }

    protected function createBreadcrumbs() {
        $breadcrumbs = array();
        $breadcrumbs[] = $this->createBreadcrumb('text_home','common/home');
        $breadcrumbs[] = $this->createBreadcrumb('text_basket','checkout/cart');
        $breadcrumbs[] = $this->createBreadcrumb('text_checkout','checkout/checkout');
        $breadcrumbs[] = $this->createBreadcrumb('text_success','checkout/success');
        return $breadcrumbs;
    }

    private function createBreadcrumb($text, $link) {
        return array(
            'text' => $this->language->get($text),
            'href' => $this->url->link($link)
        );
    }

    protected function failure($error)
    {
        $this->session->data['error'] = $error;
        $this->response->redirect(RegistryBGPBOpencart::getRegistry()->getSystemSettingsWrapper()->linkCatalogCheckout());
    }
}
