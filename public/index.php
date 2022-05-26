<?php
session_start();
include __DIR__ . '/../src/shop/autoloader.php';
require 'config.php';
if(
    !defined('SHOP_DB_HOST') ||
    !defined('SHOP_DB_USER') ||
    !defined('SHOP_DB_PASS') ||
    !defined('SHOP_EMAIL')
) {
    exit("SHOP not configured,define SHOP_DB_HOST, SHOP_DB_USER, SHOP_DB_PASS, SHOP_DB, SHOP_EMAIL");
}

use Shop\Controller\AbstractController;
use Shop\Exception\NotFoundException;
$params = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'],'/')) : ['page','index'];
$isAdmin = false;

$requestedController = array_shift($params); //get 1st element

if ("admin" === $requestedController) { //backend area
    define("SHOP_ADMIN", true);
    $requestedController = array_shift($params);
    $ctrlClass = 'Shop\\Controller\\Admin\\' . ucfirst($requestedController) . 'Controller';
} else {
    define("SHOP_ADMIN", false);
    $ctrlClass = 'Shop\\Controller\\' . ucfirst($requestedController) . 'Controller';
}

$requestedAction = array_shift($params);
if(!$requestedAction) {
    $requestedAction = 'index';
}

try {

    if (!class_exists($ctrlClass)) {
        throw new NotFoundException("Invalid path ($ctrlClass)");
    }

    /** @var AbstractController $ctrl */
    $ctrl = new $ctrlClass;
    $action = strtolower($requestedAction).'Action';

    if (!is_callable([$ctrl,$action])) {
        throw new NotFoundException("Invalid action");
    }
    

    if ($ctrl->beforeAction()) {
        $ctrl->$action($params);
        $ctrl->afterAction();
    }

    
    if($ctrl->isRedirect()) {
        $url = $ctrl->getRedirectTo();
        header('Location: ' . $url, true, $ctrl->isRedirectPermanent() ? 301 : 302);
        exit("Redirecting  to <a href=\"$url\">$url</a>");
    }
    $ctrl->flash();
} catch (NotFoundException $e) {
    header("HTTP/1.0 404 Not Found");
    echo $e->getMessage();
    exit();
} catch(Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
    exit();
}
exit();


function h($x) {
    return htmlspecialchars($x);
}
