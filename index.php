<?php
/**
 * Version @version@
 * Entry point SiteForeverCMS
 */
use Sfcms\Request;

if (preg_match('/^\/index/', $_SERVER['REQUEST_URI'])) {
    header('Status: 301');
    header('Location: /');
    exit();
}

define('ROOT', __DIR__);

require_once 'vendor/autoload.php';

Request::enableHttpMethodParameterOverride();
$request  = Request::createFromGlobals();

$env = preg_match('/^(?:127\.0\.0\.1|192\.168\.\d{1,3}\.\d{1,3})$/', $request->getClientIp()) ? 'dev' : 'prod';
$env = preg_match('/^test/', $request->getHost()) ? 'test' : $env;
$app = new App($env, 'prod'!=$env);
$app->run($request);
