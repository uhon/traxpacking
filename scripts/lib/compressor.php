<?php


    $applicationPath = realpath(dirname(__FILE__).'/../../application');

    if(!defined('APPLICATION_PATH')) {
        define('APPLICATION_PATH', $applicationPath);
    }

    if(!defined('APPLICATION_ENV')) {
        define('APPLICATION_ENV', 'production');
    }

    set_include_path(implode(PATH_SEPARATOR,array(
        $applicationPath.'/../library',
        get_include_path()
    )));

    require_once($applicationPath . '/../library/Zend/Loader/Autoloader.php');

    $autoloader = Zend_Loader_Autoloader::getInstance();
    $autoloader->registerNamespace('Tp_');



$publicDir = APPLICATION_PATH . "/../public";
$compressedDir = $publicDir . "/compressed";

$styleFiles = Tp_Controller_Plugin_ViewSetup::getHeadStyles();
$scriptFiles = Tp_Controller_Plugin_ViewSetup::getHeadScripts();

$styleString = "";
$scriptString = "";

foreach($styleFiles as $file) {
    $fileContent = "\n\n/* #### MERGED: " . $file . "*/\n" . file_get_contents($publicDir . $file);

    $folder = "";
    $lastSlash = strrpos($file, '/');
    if($lastSlash !== false) {
        $folder = substr($file, 0, $lastSlash + 1);
    }

    $fileContent = preg_replace("/url\(([^\\/])/i", "url(" . $folder  . "\\1", $fileContent);

    $styleString .= $fileContent;
}

foreach($scriptFiles as $file) {

    $scriptString .= "\n\n/* #### MERGED: " . $file . "*/\n" . file_get_contents($publicDir . "/" . $file);
}

file_put_contents($compressedDir . "/traxpacking.css",  $styleString);
file_put_contents($compressedDir . "/traxpacking.js",  $scriptString);

exec("java -jar " . realpath(dirname(__FILE__)) . "/yuicompressor-2.4.7.jar $compressedDir/traxpacking.css -o $compressedDir/traxpacking-" . Tp_Shortcut::getVersion() . ".min.css");
exec("java -jar " .realpath(dirname(__FILE__)) . "/yuicompressor-2.4.7.jar $compressedDir/traxpacking.js -o $compressedDir/traxpacking-" . Tp_Shortcut::getVersion() . ".min.js");

echo 'Javascript and CSS are minified now under public/compressed!';
