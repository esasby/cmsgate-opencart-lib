<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 21.02.2020
 * Time: 15:01
 */

namespace esas\cmsgate\wrappers;


use esas\cmsgate\Registry;
use esas\cmsgate\utils\OpencartVersion;

class SystemSettingsWrapperOpencart extends SystemSettingsWrapper
{
    protected $opencartRegistry;
    private $url;
    private $session;
    private $extensionName;

    /**
     * @param $registry
     */
    public function __construct($registry)
    {
        parent::__construct();
        $this->opencartRegistry = $registry;
        $this->url = $this->opencartRegistry->get("url");
        $this->session = $registry->get('session');
        $this->extensionName = Registry::getRegistry()->getPaySystemName();
    }

    /**
     * Для удобства работы в IDE и подсветки синтаксиса.
     * @return $this
     */
    public static function getInstance() {
        return Registry::getRegistry()->getSystemSettingsWrapper();
    }

    public function getOpencartRegistry()
    {
        return $this->opencartRegistry;
    }

    public function linkAdminHome()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
                return $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
            case OpencartVersion::v4_x:
                return $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);
            default:
                return "";
        }
    }

    public function linkAdminExtensions()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);
            case OpencartVersion::v4_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
            default:
                return "";
        }
    }

    public function linkAdminExtensionsPayment()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
                return $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
            case OpencartVersion::v4_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
            default:
                return "";
        }
    }

    public function linkAdminExtensionSettings($action = null)
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
                return $this->url->link($this->extensionPayment() . ($action != null ? '/' . $action : ""), 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link($this->extensionPayment() . ($action != null ? '/' . $action : ""), 'user_token=' . $this->session->data['user_token'], true);
            case OpencartVersion::v4_x:
                return $this->url->link($this->extensionPayment() . ($action != null ? '.' . $action : ""), 'user_token=' . $this->session->data['user_token'], true);
            default:
                return "";
        }
    }

    public function linkCatalogCheckout()
    {
        return $this->versionLink('checkout/checkout', '', '', true);
    }

    public function linkCatalogCheckoutSuccess()
    {
        return $this->versionLink('checkout/success', '', '', true);
    }

    public function linkCatalogExtension($action = null, $args = null, $secure = false)
    {
        return $this->versionLink($this->extensionPayment(), $action, $args, $secure);
    }

    private function versionLink($path, $action = null, $params = null, $secure = true) {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
                return $this->url->link($path . ($action != null ? '/' . $action : ""), $params == null ? "" : $params, 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link($path . ($action != null ? '/' . $action : ""), $params == null ? "" : $params, true);
            case OpencartVersion::v4_x:
                return $this->url->link($path . ($action != null ? '.' . $action : ""), $params == null ? "" : $params, true);
        }
    }

    private function extensionPayment() {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
                return 'payment/' . $this->extensionName;
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
                return 'extension/payment/' . $this->extensionName;
            case OpencartVersion::v4_x:
                return 'extension/cmsgate_opencart_hutkigrosh/payment/' . $this->extensionName;
        }
    }
}
