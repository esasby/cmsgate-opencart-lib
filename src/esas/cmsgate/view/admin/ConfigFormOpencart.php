<?php

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 30.09.2018
 * Time: 15:19
 */

namespace esas\cmsgate\view\admin;

use esas\cmsgate\ConfigFieldsOpencart;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use esas\cmsgate\utils\UploadedFileWrapper;
use esas\cmsgate\view\admin\fields\ConfigField;
use esas\cmsgate\view\admin\fields\ConfigFieldCheckbox;
use esas\cmsgate\view\admin\fields\ConfigFieldFile;
use esas\cmsgate\view\admin\fields\ConfigFieldList;
use esas\cmsgate\view\admin\fields\ConfigFieldNumber;
use esas\cmsgate\view\admin\fields\ConfigFieldPassword;
use esas\cmsgate\view\admin\fields\ConfigFieldTextarea;
use esas\cmsgate\view\admin\fields\ListOption;
use esas\cmsgate\view\admin\validators\ValidatorInteger;
use esas\cmsgate\utils\OpencartVersion;

class ConfigFormOpencart extends ConfigFormHtml
{
    private $orderStatuses;

    private $registry;

    /**
     * ConfigFieldsRenderOpencart constructor.
     */
    public function __construct($managedFields, $formKey, $submitUrl, $submitButtons, $registry)
    {
        parent::__construct($managedFields, $formKey, $submitUrl, $submitButtons);
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
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
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
            case OpencartVersion::v4_x:
                return element::div(
                    attribute::clazz("card"),
                    element::div(
                        attribute::clazz("card-header"),
                        element::i(
                            attribute::clazz("fa-solid fa-pencil")
                        ),
                        element::content(" " . $this->getHeadingTitle())
                    ),
                    element::div(
                        attribute::clazz("card-body"),
                        element::form(
                            attribute::action($this->getSubmitUrl()),
                            attribute::method("post"),
                            attribute::enctype("multipart/form-data"),
                            attribute::id("form-checkout"),
                            parent::generate(), // добавляем поля
                            $this->elementSubmitButtons()
                        )
                    )
                );
        }
    }


    /**
     * Надо вызывать отдельно от конструктора, т.к. если для модуля будет несколько групп настроек в разных ConfigForm
     * возникает задвоение
     * @return $this
     */
    public function addCmsManagedFields()
    {
        $this->managedFields->addField(new ConfigFieldNumber(
            ConfigFieldsOpencart::sortOrder(),
            Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::MODULE_SORT_ORDER_LABEL),
            Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::MODULE_SORT_ORDER_DESCRIPTION),
            true,
            new ValidatorInteger(1, 20),
            1,
            20));
        $this->managedFields->addField(new ConfigFieldList(
            ConfigFieldsOpencart::status(),
            Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::MODULE_STATUS_LABEL),
            Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::MODULE_STATUS_DESCRIPTION),
            true, [
            new ListOption("1", Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::MODULE_STATUS_ENABLE)),
            new ListOption("0", Registry::getRegistry()->getTranslator()->translate(AdminViewFieldsOpencart::MODULE_STATUS_DISABLE))]));
        return $this;
    }

    public static function elementValidationError(ConfigField $configField)
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


    public static function elementLabel(ConfigField $configField)
    {
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
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
            case OpencartVersion::v4_x:
                return
                    element::label(
                        attribute::clazz("col-sm-2 col-form-label"),
                        attribute::forr("input-" . $configField->getKey()),
                        element::span(
                            attribute::data_toggle("tooltip"),
                            attribute::title($configField->getDescription()),
                            element::content($configField->getName())
                        )
                    );
        }

    }

    protected function elementInputSubmit($name, $value)
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
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
                return attribute::clazz("form-group" . ($configField->isRequired() ? ' required' : ''));
            case OpencartVersion::v4_x:
                return attribute::clazz("row mb-3" . ($configField->isRequired() ? ' required' : ''));
        }
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
        switch (OpencartVersion::getVersion()) {
            case OpencartVersion::v2_1_x:
            case OpencartVersion::v2_3_x:
            case OpencartVersion::v3_x:
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
            case OpencartVersion::v4_x:
                return
                    element::div(
                        self::attributeFormClass($configField),
                        self::elementValidationError($configField),
                        self::elementLabel($configField),
                        element::div(
                            attribute::clazz("col-sm-10"),
                            element::div(
                                attribute::clazz("form-check form-switch form-switch-lg"),
                                element::input(
                                    attribute::type("hidden"),
                                    attribute::name($configField->getKey()),
                                    attribute::value("0")
                                ),
                                element::input(
                                    attribute::type("checkbox"),
                                    attribute::name($configField->getKey()),
                                    self::attributeInputId($configField),
                                    attribute::value("1"),
                                    attribute::checked($configField->isChecked()),
                                    attribute::placeholder($configField->getName()),
                                    attribute::clazz("form-check-input")
                                )
                            )
                        )
                    );
        }
    }

    public function generateFileField(ConfigFieldFile $configField)
    {
        return
            element::div(
                self::attributeFormClass($configField),
                self::elementValidationError($configField),
                self::elementLabel($configField),
                element::div(
                    attribute::clazz("col-sm-10"),
                    element::input(
                        attribute::clazz("form-control"),
                        attribute::name($configField->getKey()),
                        attribute::type("file"),
                        attribute::placeholder($configField->getName()),
                        self::attributeInputId($configField)
                    ),
                    element::br(),
                    element::font(
                        attribute::color($this->getFileColor($configField->getValue())),
                        element::content($configField->getValue())
                    )
                )
            );
    }

    private function getFileColor($fileName) {
        $file = new UploadedFileWrapper($fileName);
        return $file->isExists() ? "green" : "red";
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