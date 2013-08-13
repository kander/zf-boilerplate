<?php
$application = require_once __DIR__ . '/../../application/prepareApplication.php';
$application->bootstrap();

if (isset($_GET['WSDL'])) {
	$autodiscover = new \Boilerplate\Webservice\Soap\AutoDiscover();
	$autodiscover->setUri('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/ws/soap.php');
	$autodiscover->setClass("\\App\\Webservice\\Calls");
	$autodiscover->handle();
} elseif (isset($_GET['INTERNALWSDL'])) {
    $autodiscover = new Zend_Soap_AutoDiscover();
    $autodiscover->setClass('\\App\\Webservice\\Calls');
    $autodiscover->setUri('http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/ws/soap.php');
    $autodiscover->handle();
} else {
    $options = array('soap_version' => SOAP_1_2);
    $server = new \Boilerplate\Webservice\Soap\Server('http://'.$_SERVER['SERVER_NAME'].'/ws/soap.php?INTERNALWSDL=1', $options);
    $server->setObject(new \App\Webservice\Calls());
    $server->handle();
}