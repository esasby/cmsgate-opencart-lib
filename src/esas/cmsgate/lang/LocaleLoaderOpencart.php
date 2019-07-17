<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.09.2018
 * Time: 13:09
 */

namespace esas\cmsgate\lang;


use Registry;

class LocaleLoaderOpencart extends LocaleLoaderCms
{

    /**
     * @var Registry
     */
    private $registry;

    /**
     * LocaleLoaderOpencart constructor.
     * @param $registry
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }


    public function getLocale()
    {
        return $this->registry->get("language")->get("code");
    }


}