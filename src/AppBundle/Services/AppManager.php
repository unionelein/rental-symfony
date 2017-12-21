<?php
/**
 * Created by PhpStorm.
 * User: Alexei
 * Date: 21.12.2017
 * Time: 13:55
 */

namespace AppBundle\Services;


class AppManager
{
    const TYPE = [
        "arenda" => 0,
        "pokypka" => 1
    ];

    public function __construct()
    {
    }

    public function getTypeByName($name)
    {
        switch ($name) {
            case "arenda": case null:
                return self::TYPE["arenda"];
            case "pokypka":
                return self::TYPE["pokypka"];
            default:
                return null;
        }
    }
}