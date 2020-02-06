<?php

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 30.09.2018
 * Time: 15:19
 */

namespace esas\cmsgate\view\admin;

use esas\cmsgate\ConfigFields;
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

    /**
     * @var string
     */
    private $headingTitle;


    /**
     * ConfigFieldsRenderOpencart constructor.
     */
    public function __construct($managedFields, $registry, $headingTitle)
    {
        parent::__construct($managedFields);
        $this->addCmsManagedFields($registry);
        $loader = $registry->get("load");
        $loader->model('localisation/order_status');
        $orderStatuses = $registry->get("model_localisation_order_status")->getOrderStatuses();
        foreach ($orderStatuses as $orderStatus) {
            $this->orderStatuses[] = new ListOption($orderStatus['order_status_id'], $orderStatus['name']);
        }
        $this->headingTitle = $headingTitle;
    }

    /**
     * @return string
     */
    public function getHeadingTitle()
    {
        return $this->headingTitle;
    }


    private function addCmsManagedFields($registry) {
        $language = $registry->get('language');
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
                self::elementLabel($configField),
                self::elementValidationError($configField),
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
                self::elementLabel($configField),
                self::elementValidationError($configField),
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
                self::elementLabel($configField),
                self::elementValidationError($configField),
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
                self::elementLabel($configField),
                self::elementValidationError($configField),
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