<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 22.07.2019
 * Time: 14:56
 */

namespace esas\cmsgate\utils;


class OpencartVersion
{
    const v2_1_x = "2.1.x";
    const v2_3_x = "2.3.x";
    const v3_x = "3.x";
    const v4_x = "4.x";


    public static function getVersion()
    {
        if (preg_match("/^2.1.*/", VERSION)) {
            return self::v2_1_x;
        } elseif (preg_match("/^2.3.*/", VERSION)) {
            return self::v2_3_x;
        } elseif (preg_match("/^3.*/", VERSION)) {
            return self::v3_x;
        } elseif (preg_match("/^4.*/", VERSION)) {
            return self::v4_x;
        }
    }


}
