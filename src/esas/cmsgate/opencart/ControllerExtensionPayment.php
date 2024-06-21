<?php
namespace esas\cmsgate\opencart;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\OpencartVersion;
use esas\cmsgate\utils\StringUtils;

if (class_exists('Controller')) {
    class MiddleController extends \Controller
    {
    }
} elseif (class_exists('\Opencart\System\Engine\Controller')) {
    class MiddleController extends \Opencart\System\Engine\Controller
    {
    }
}

class ControllerExtensionPayment extends MiddleController
{
    protected $extensionName;

    /**
     * AdminControllerExtensionPayment constructor.
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->extensionName = Registry::getRegistry()->getPaySystemName();
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
                $this->language->load('extension/payment/' . $this->extensionName);
            case OpencartVersion::v4_x:
                $this->language->load('extension/cmsgate_opencart_' . $this->extensionName . '/payment/' . $this->extensionName);
        }
    }


    public function getView($viewName = null)
    {
        if (StringUtils::isNullOrEmptyString($viewName))
            $viewName = $this->extensionName;
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
                return 'payment/' . $viewName . '.tpl';
            case OpencartVersion::v2_3_x:
                return 'extension/payment/' . $viewName . '.tpl';
            case OpencartVersion::v3_x:
                return 'extension/payment/' . $viewName;
            case OpencartVersion::v4_x:
                return 'extension/cmsgate_opencart_' . $viewName . '/payment/' . $viewName;
        }
    }

    public function i18n(&$data, array $fields)
    {
        foreach ($fields as $field) {
            $data[$field] = $this->language->get($field);
        }
    }
}