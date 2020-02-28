<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 28.02.2020
 * Time: 10:36
 */

namespace esas\cmsgate\view;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;

class ElementsOpencart
{
    public static function elementMessages()
    {
        $ret = "";
        $messages = Registry::getRegistry()->getMessenger()->getInfoMessagesArray();
        if (!empty($messages)) {
            foreach ($messages as $message)
                $ret .= self::elementMessage("alert alert-success", $message);
        }
        $messages = Registry::getRegistry()->getMessenger()->getWarnMessagesArray();
        if (!empty($messages)) {
            foreach ($messages as $message)
                $ret .= self::elementMessage("alert alert-danger", $message); //todo поправить класс
        }
        $messages = Registry::getRegistry()->getMessenger()->getErrorMessagesArray();
        if (!empty($messages)) {
            foreach ($messages as $message)
                $ret .= self::elementMessage("alert alert-danger", $message);
        }
        return $ret;
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