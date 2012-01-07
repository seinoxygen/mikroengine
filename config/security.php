<?php

// Protection against CSRF attacks.
$config['csrf_enable'] = true;
$config['csrf_token'] = 'csrf_token';
$config['csrf_life'] = 900; // 15 minutes
$config['csrf_ips'] = array();

// Ignore csrf check for the listed uris.
$config['csrf_uris'] = array();

// Captcha.
$config['captcha_url'] = 'http://localhost/mikroengine/data/captcha/';

$config['captcha_colors'] = array(
    'background' => 'bbbbbb',
    'border' => '000000',
    'noise' => '8c8c8c',
    'text' => '000000'
);

$config['captcha_life'] = 300; // 5 minutes
$config['captcha_height'] = 22;
$config['captcha_width'] = 100;
$config['captcha_sensitive'] = true; // Case sensitive
$config['captcha_chars'] = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$config['captcha_lenght'] = 5;
$config['captcha_noise'] = true;