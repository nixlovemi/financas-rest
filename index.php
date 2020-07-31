<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['AMBIENTE_DEV'] = true;

require_once("classes/Response.php");
$Response = new Response();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization');

// the following constant will help ensure all other PHP files will only work as part of this API.
if (!defined('CONST_INCLUDE_KEY')) {
    define('CONST_INCLUDE_KEY', 'd4e2ad09-b1c3-4d70-9a9a-0e6115031985');
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
if (in_array($requestMethod, ["GET", "POST", "PUT", "DELETE"])) {
    require_once('classes/ApiHandler.php');
    $ApiHandler = new ApiHandler();
    $ApiHandler->execRequest();
} else {
    $Response->showError('Requested method is not available!');
}