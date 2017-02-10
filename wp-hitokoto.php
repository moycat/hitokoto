<?php
/*
Plugin Name: wp-hitokoto
Plugin URI: https://github.com/moycat/wp-hitokoto
Description: 由hitokoto本地源改制的WordPress插件
Version: 0.1
Author: hitokoto
Author URI: http://hitokoto.us/
*/

$hitokoto_db = null;
$hitokoto_now = null;

/**
 * Get a certain part of a piece.
 * @param $type
 * @param bool $print
 * @return string
 */
function hitokoto($type, $print = true)
{
    global $hitokoto_now;

    if ($hitokoto_now === null) {
        hitokoto_read();
    }

    $rt = '';

    if (isset($hitokoto_now[$type])) {
        $rt = $hitokoto_now[$type];
    }

    if ($print) {
        echo $rt;
        return strlen($rt);
    } else {
        return $rt;
    }
}

/**
 * Fetch a new piece to $hitokoto_now;
 */
function hitokoto_read()
{
    global $hitokoto_db, $hitokoto_now;

    if (!hitokoto_read_json()) {
        $hitokoto_now = [];
        return;
    }

    $hitokoto_now = $hitokoto_db[array_rand($hitokoto_db)];
}

/**
 * Init hitokoto.
 */
function hitokoto_read_json()
{
    static $read = -1;

    if ($read > -1) {
        return $read;
    }

    global $hitokoto_db;

    $data  = dirname(__FILE__) . '/hitokoto.json';
    $json  = file_get_contents($data);
    $hitokoto_db = json_decode($json, true);
    if (!$hitokoto_db) {
        return $read = 0;
    }
    return $read = count($hitokoto_db);
}

/**
 * Print the content of some piece only.
 */
function hitokoto_single()
{
    global $hitokoto_db;

    if (!hitokoto_read_json()) {
        echo '';
        return;
    }

    echo $hitokoto_db[array_rand($hitokoto_db)]['hitokoto'];
}

/**
 * Get an array of some piece.
 * @return null|array
 */
function hitokoto_full()
{
    global $hitokoto_db;

    if (!hitokoto_read_json()) {
        return null;
    }

    return $hitokoto_db[array_rand($hitokoto_db)];
}