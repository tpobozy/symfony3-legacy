<?php

require_once 'vendor/autoload.php';
require_once 'app/LegacyKernel.php';
require_once 'app/MainKernel.php';

use Symfony\Component\HttpFoundation\Request;

ini_set("display_errors", "1");
error_reporting(E_ALL & ~E_NOTICE);


$request = Request::createFromGlobals();

$app = new MainKernel('dev');

$response = $app->handle($request);
$response->prepare($request);
$response->send();


