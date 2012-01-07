<?php
/*
 * Router paths:
 * 
 * Router paths are componed with a real path and a virtual path contained in the config array.
 * The real path is stored as key in the array, while the virtual path is the value of that key.
 * 
 * For example:
 * A controller called about with a function called site (about/site) could be accessed as site 
 * using the following configuration.
 * 
 * $config['router_routes'] = array(
 *   'about/site' => 'site',
 * );
 */

// Default controller.
$config['router_default'] = 'about';

// Routes.
$config['router_routes'] = array(
    'about' => 'dir/about',
    'about/index' => 'dir/about/index',
    'about/template' => 'dir/about/template',
    'about/index/[any]' => 'dir/about/index/[any]'
);