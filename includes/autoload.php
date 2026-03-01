<?php
if (!defined('ABSPATH')){
    exit;
}

spl_autoload_register(function($class){
    $prefix = 'ConverseLab\\';

    if(strpos($class, $prefix)!==0){
        return;
    }

    $relative_class=substr($class, strlen($prefix));

    $relative_class = str_replace('\\', '/', $relative_class);

    $file = CONVERSELAB_PATH . 'includes/classes/class-' . strtolower($relative_class) . '.php';


    if(empty($file)){
        return;
    }


    if(file_exists($file)){
        require_once $file;
    }
});