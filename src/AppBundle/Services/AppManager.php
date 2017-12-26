<?php
/**
 * Created by PhpStorm.
 * User: Alexei
 * Date: 21.12.2017
 * Time: 13:55
 */

namespace AppBundle\Services;


use Eventviva\ImageResize;

class AppManager
{
    const ARENDA = "arenda";
    const POKYPKA = "pokypka";

    const TYPE = [
        self::ARENDA => 0,
        self::POKYPKA => 1
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

    static public function saveImg($file)
    {
        $dir = is_dir('images/') ? 'images/' : __DIR__.'/../../../web/'.'images/';

        if (is_string($file)) {
            $file = $dir.$file;
        }

        $img = new ImageResize($file);

        switch ($img->source_type) {
            case 1: $type = '.gif'; break;
            case 2: $type = '.jpg'; break;
            case 3: $type = '.png'; break;
            default: $type = '.swf';
        }

        $imgName = 'img'.time().rand(1, 1000).$type;

        $img->quality_jpg = 90;
        $img->resizeToBestFit(500, 625);
        $img->save($dir.'big-'.$imgName);
        $img->resizeToBestFit(220, 150);
        $img->save($dir.'min-'.$imgName);

        return $imgName;
    }
}