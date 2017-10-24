<?php
/**
 * Created by PhpStorm.
 * User: vincent.montet
 * Date: 11.10.2017
 * Time: 13:38
 */

$directory = '../pages/images/utilisateurs/';
$gallery = scandir($directory);
// print_r($gallery);

$nbmod = 0;

foreach ($gallery as $k2 => $v2) {
    if (exif_imagetype($directory."/".$v2) == IMAGETYPE_JPEG) {

        $info = pathinfo($directory.'/'.$v2);

        if ($info['extension'] <> "jpg"){
            $updates .= "FROM " . $directory.'/'.$v2 ." TO ".$directory.'/'.str_replace(".JPG",".jpg",$v2) ."<br>" ;
            rename($directory.'/'.$v2, $directory.'/'.str_replace(".JPG",".jpg",$v2));
            ++$nbmod;
        }

    }
}

echo "Nombre d'update = " . $nbmod . "<br>";
echo $updates;