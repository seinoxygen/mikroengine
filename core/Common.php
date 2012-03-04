<?php

function __autoload($class){
    $filename = BASEPATH . 'core/' . $class . '.php';
    if(file_exists($filename)){
        include_once($filename);
    }
}

function &get_instance(){
    return Controller::get_instance();
}

function &load_class($class, $path = 'classes'){
    
    static $classes;
    
    if(isset($classes[$class])){
        return $classes[$class];
    }
    
    if(class_exists($class) === false){
        require_once(BASEPATH.$path.'/'.$class.'.php');
    }
    
    is_loaded($class);
    
    $classes[$class] = new $class();
    return $classes[$class];
}

function is_loaded($class = ''){

    static $_is_loaded = array();

    if ($class != ''){
        $_is_loaded[strtolower($class)] = $class;
    }

    return $_is_loaded;
}

function error($errno, $errstr, $errfile, $errline){
    $EX = &load_class('Exceptions', 'core');
    $EX->display_php_error($errno, $errstr, $errfile, $errline);
}