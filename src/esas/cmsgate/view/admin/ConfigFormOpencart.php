<?php

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 30.09.2018
 * Time: 15:19
 */

namespace esas\cmsgate\view\admin;

use esas\cmsgate\ConfigFields;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use esas\cmsgate\view\admin\fields\ConfigField;
use esas\cmsgate\view\admin\fields\ConfigFieldCheckbox;
use esas\cmsgate\view\admin\fields\ConfigFieldList;
use esas\cmsgate\view\admin\fields\ConfigFieldNumber;
use esas\cmsgate\view\admin\fields\ConfigFieldPassword;
use esas\cmsgate\view\admin\fields\ConfigFieldTextarea;
use esas\cmsgate\view\admin\fields\ListOption;
use esas\cmsgate\view\admin\validators\ValidatorInteger;

class ConfigFormOpencart extends ConfigFormHtml
{
    private $orderStatuses;

    private $registry;

    /**
     * ConfigFieldsRenderOpencart constructor.
     */
    public function __construct($managedFields, $headingTitle, $submitUrl, $submitButtons, $registry)
    {
        parent::__construct($managedFields, $headingTitle, $submitUrl, $submitButtons);
        $this->registry = $registry;
        $loader = $registry->get("load");
        $loader->model('localisation/order_status');
        $orderStatuses = $registry->get("model_localisation_order_status")->getOrderStatuses();
        foreach ($orderStatuses as $orderStatus) {
            $this->orderStatuses[] = new ListOption($orderStatus['order_status_id'], $orderStatus['name']);
        }
    }

    public function generate()
    {
        return element::div(
            attribute::clazz("panel panel-default"),
            element::div(
                attribute::clazz("panel-heading"),
                element::h1(
                    attribute::clazz("panel-title"),
                    element::i(
                        attribute::clazz("fa fa-pencil")
                    ),
                    element::content(" " . $this->getHeadingTitle())
                )
            ),
            element::div(
                attribute::clazz("panel-body"),
                element::form(
                    attribute::action($this->getSubmitUrl()),
                    attribute::method("post"),
                    attribute::enctype("multipart/form-data"),
                    attribute::id("config-form"),
                    attribute::clazz("form-horizontal"),
                    parent::generate(), // добавляем поля
                    $this->elementSubmitButtons()
                )
            )
        );
    }


    /**
     * Надо вызывать отдельно от конструктора, т.к. если для модуля будет несколько групп настроек в разных ConfigForm
     * возникает задвоение
     */
    public function addCmsManagedFields()
    {
        $language = $this->registry->get('language');
        $language->load('extension/payment/hutkigrosh');

        $this->managedFields->addField(new ConfigFieldNumber(
            ConfigFields::getCmsRelatedKey("sort_order"),
            $language->get('module_sort_order_label'),
            $language->get('module_sort_order_description'),
            true,
            new ValidatorInteger(1, 20),
            1,
            20));
        $this->managedFields->addField(new ConfigFieldList(
            ConfigFields::getCmsRelatedKey("status"),
            $language->get('module_status_label'),
            $language->get('module_status_description'),
            true, [
            new ListOption("1", $language->get('module_status_enable')),
            new ListOption("0", $language->get('module_status_disable'))]));
    }

    private static function elementValidationError(ConfigField $configField)
    {
        $validationResult = $configField->getValidationResult();
        if ($validationResult != null && !$validationResult->isValid())
            return
                element::div(
                    attribute::clazz("alert alert-danger"),
                    element::content($validationResult->getErrorTextSimple())
                );
        else
            return "";
    }


    private static function elementLabel(ConfigField $configField)
    {
        return
            element::label(
                attribute::clazz("col-sm-2 control-label"),
                attribute::forr("input-" . $configField->getKey()),
                element::span(
                    attribute::data_toggle("tooltip"),
                    attribute::title($configField->getDescription()),
                    element::content($configField->getName())
                )
            );
    }

    private function elementMessages()
    {
        $ret = "";
        if (Registry::getRegistry()->getMessenger()->getInfoMessages() != '') {
            $ret .= $this->elementMessage("alert alert-success", Registry::getRegistry()->getMessenger()->getInfoMessages());
        }
        if (Registry::getRegistry()->getMessenger()->getWarnMessages() != '') {
            $ret .= $this->elementMessage("alert alert-danger", Registry::getRegistry()->getMessenger()->getWarnMessages()); //todo поправить класс
        }
        if (Registry::getRegistry()->getMessenger()->getErrorMessages() != '') {
            $ret .= $this->elementMessage("alert alert-danger", Registry::getRegistry()->getMessenger()->getErrorMessages());
        }
        return $ret;
    }

    private function elementMessage($class, $text)
    {
        return
            element::div(
                attribute::clazz($class),
                element::i(
                    attribute::clazz("fa fa-exclamation-circle")
                ),
                element::content($text),
                element::button(
                    attribute::type("button"),
                    attribute::clazz("close"),
                    attribute::data_dismiss("alert")
                )
            );
    }

    private function elementSubmitButtons()
    {
        $ret = "";
        if (isset($this->submitButtons)) {
            foreach ($this->submitButtons as $buttonName => $buttonValue) {
                $ret .= self::elementInputSubmit($buttonName, $buttonValue) . "&nbsp;";
            }
        } else if (isset($this->submitUrl))
            $ret = self::elementInputSubmit("submit_button", Registry::getRegistry()->getTranslator()->translate(AdminViewFields::CONFIG_FORM_BUTTON_SAVE));
        return $ret;
    }

    private static function elementInputSubmit($name, $value)
    {
        return
            element::input(
                attribute::clazz("btn btn-primary"),
                attribute::type("submit"),
                attribute::name($name),
                attribute::value($value)
            );
    }

    private static function attributeFormClass(ConfigField $configField)
    {
        return attribute::clazz("form-group" . ($configField->isRequired() ? ' required' : ''));
    }

    private static function elementInput(ConfigField $configField, $type)
    {
        return
            element::input(
                attribute::clazz("form-control"),
                attribute::name($configField->getKey()),
                attribute::type($type),
                attribute::placeholder($configField->getName()),
                self::attributeInputId($configField),
                attribute::value($configField->getValue())
            );
    }

    private static function attributeInputId(ConfigField $configField)
    {
        return attribute::id("input-" . $configField->getKey());
    }

    function generateTextField(ConfigField $configField)
    {
        return
            element::div(
                self::attributeFormClass($configField),
                self::elementValidationError($configField),
                self::elementLabel($configField),
                element::div(
                    attribute::clazz("col-sm-10"),
                    self::elementInput($configField, "text")
                )
            );
    }

    function generateTextAreaField(ConfigFieldTextarea $configField)
    {
        return
            element::div(
                self::attributeFormClass($configField),
                self::elementValidationError($configField),
                self::elementLabel($configField),
                element::div(
                    attribute::clazz("col-sm-10"),
                    element::textarea(
                        self::attributeInputId($configField),
                        attribute::name($configField->getKey()),
                        attribute::clazz("form-control"),
                        attribute::rows($configField->getRows()),
                        attribute::cols($configField->getCols()),
                        attribute::placeholder($configField->getName()),
                        element::content($configField->getValue())
                    )
                )
            );
    }


    public function generatePasswordField(ConfigFieldPassword $configField)
    {
        return
            element::div(
                self::attributeFormClass($configField),
                self::elementValidationError($configField),
                self::elementLabel($configField),
                element::div(
                    attribute::clazz("col-sm-10"),
                    self::elementInput($configField, "password")
                )
            );
    }


    function generateCheckboxField(ConfigFieldCheckbox $configField)
    {
        return
            element::div(
                self::attributeFormClass($configField),
                self::elementValidationError($configField),
                self::elementLabel($configField),
                element::div(
                    attribute::clazz("col-sm-10"),
                    element::input(
                        attribute::type("checkbox"),
                        attribute::name($configField->getKey()),
                        self::attributeInputId($configField),
                        attribute::value("1"),
                        attribute::checked($configField->isChecked()),
                        attribute::placeholder($configField->getName()),
                        attribute::clazz("form-control")
                    )
                )
            );
    }

    function generateListField(ConfigFieldList $configField)
    {
        return
            element::div(
                self::attributeFormClass($configField),
                self::elementValidationError($configField),
                self::elementLabel($configField),
                element::div(
                    attribute::clazz("col-sm-10"),
                    element::select(
                        attribute::clazz("form-control"),
                        attribute::name($configField->getKey()),
                        self::attributeInputId($configField),
                        parent::elementOptions($configField)
                    )
                )
            );
    }

    /**
     * @return ListOption[]
     */
    public function createStatusListOptions()
    {
        return $this->orderStatuses;
    }
}