<?php
/**
 * Авторизация
 * @author Nikolay Ermin (nikolay@ermin.ru)
 * @link http://ermin.ru
 * @link http://siteforever.ru
 */
 
session_start();

if ( ! empty( $_SESSION['sxd_auth'] ) ) {
    $auth   = 1;
    $db = $_SESSION['sxd_conf'];
    $CFG['my_host'] = $db['host'];
    $CFG['my_user'] = $db['login'];
    $CFG['my_pass'] = $db['password'];
    $CFG['my_db']   = $db['database'];
} else {
    $auth   = 0;
}