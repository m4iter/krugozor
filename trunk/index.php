<?php
//xdebug_start_trace($_SERVER['DOCUMENT_ROOT'].'/trace.txt');
//xdebug_stop_trace();

require('./configuration/base.php');

try
{
	$context = Base_Context::getInstance();
	$context->setRequest(Http_Request::getInstance())
	        ->setResponse(Http_Response::getInstance())
	        ->setDb(Db_Mysql_Base::create(
	                    ini_get('mysql.default_host'),
	                    ini_get('mysql.default_user'),
	                    ini_get('mysql.default_password')
	                )->setDatabase(DATABASE_NAME)
	               );

	$application = new Base_Application($context);
	$application->enabledDebugInfo(ENABLED_DEBUG_INFO)
	            ->getRoutesFromPhpFile('./configuration/routes.php')
	            ->run();
}
catch (Exception $e)
{
    $context->getResponse()->sendHeaders();

    echo '<div style="padding:10px"><h3>Фатальная ошибка:</h3><p>' . $e->getMessage() .
    '</p><p><pre>' . print_r($e->getTraceAsString(), 1) . '</pre></p></div>';
}