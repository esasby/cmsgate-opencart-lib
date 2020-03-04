<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 28.02.2020
 * Time: 10:36
 */

namespace esas\cmsgate\view;

use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;

class ViewBuilderOpencart extends ViewBuilder
{
    public static function elementAdminMessages()
    {
        return
            parent::elementMessages(
                "alert alert-success",
                "alert alert-danger",
                "alert alert-danger"
            );

    }
    
    public static function elementMessage($class, $text)
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
}