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
     * SystemSettingsWrapperBGPBOpencart constructor.
     * @param $registry
     */
    public function __construct($registry)
    {
        $this->opencartRegistry = $registry;
        $this->url = $this->opencartRegistry->get("url");
        $this->session = $registry->get('session');
        $this->extensionName = Registry::getRegistry()->getPaySystemName();
    }

    public function linkAdminHome()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);
        }
    }

    public function linkAdminExtensionsPayment()
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);
        }
    }

    public function linkAdminExtensionSettings($action = null)
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link('extension/payment/' . $this->extensionName . ($action != null ? '/' . $action : ""), 'token=' . $this->session->data['token'], 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link('extension/payment/' . $this->extensionName . ($action != null ? '/' . $action : ""), 'user_token=' . $this->session->data['user_token'], true);
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
        return $this->versionLink('extension/payment/' . $this->extensionName, $action, $args, $secure);
    }

    private function versionLink($path, $action = null, $params = null, $secure = true) {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_3_x:
                return $this->url->link($path . ($action != null ? '/' . $action : ""), $params == null ? "" : $params, 'SSL');
            case OpencartVersion::v3_x:
                return $this->url->link($path . ($action != null ? '/' . $action : ""), $params == null ? "" : $params, true);
        }
    }
}