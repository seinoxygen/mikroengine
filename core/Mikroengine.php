<?php
include_once('core/Common.php');

$old_error_handler = set_error_handler("error");

$CF = &load_class('Config', 'core');
$CF->load('application');

date_default_timezone_set($CF->get('app_timezone'));

// Load the defualt classes.
$BE = &load_class('Benchmark', 'core');
$BE->mark('app');

$EX = &load_class('Exceptions', 'core');

$IN = &load_class('Input', 'core');
$OU = &load_class('Output', 'core');

$UR = &load_class('Uri', 'core');

$RO = &load_class('Router', 'core');

$CF->load('database');
$db_enabled = $CF->get('db_enable');
if($db_enabled === true){
    $DB = &load_class('Database');
}

$MO = &load_class('Model', 'core');

// Load the controller.
include_once($RO->get_directory().$RO->get_class().'.php');

// Asign the routed segments.
$class = $RO->get_class();
$method = $RO->get_method();

// Check if class exists.
if(class_exists($class)){
    $ME = new $class();

    if(method_exists($ME, $method)){
        call_user_func_array(array(&$ME, $method), $RO->get_args());
    }
    else{
        $EX->display_error();
    }
}

// Check if the method wasn't called manually.
if(!headers_sent()){
    $ME->output->display();
}