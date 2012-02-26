<?php

// Default format response.
$config['format'] = 'json';

// The table name that will host all keys and limits.
$config['table'] = '';

/*
 * Column in table where is stored api key.
 */
$config['table_key'] = '';

/*
 * Column in table that will save the total calls by the api key.
 * Must be integer.
 */
$config['table_limit'] = '';

/**
 * Column in table that will host the time of the last call.
 * Must be integer.
 */
$config['table_time'] = '';

/**
 * Key name usedn in posts and gets so fetch the api key.
 * 
 * GET: http://www.domain.com/resource/method/key/e10adc3949ba59ab
 * POST: $_POST['key']
 */
$config['key'] = 'key';
