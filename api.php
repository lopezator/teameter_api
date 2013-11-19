<?php
require_once('config/config.inc.php');
require_once 'vendor/slim/slim/Slim/Slim.php';
require_once('rb.php');

//Configurar la BD
R::setup("mysql:host=".$mysql_host.";dbname=".$mysql_dbname, $mysql_username, $mysql_password);

//Iniciar Slim
\Slim\Slim::registerAutoloader();

//Crear objeto Slim y especificar el contentType
$app = new \Slim\Slim();
$app->contentType('application/json');

//Funcion para formater el resultado
function returnResult($action, $success = true, $id = 0)
{
    echo json_encode([
        'action' => $action,
        'success' => $success,
        'id' => intval($id),
    ]);
}

//Select All
$app->get('/available_trends', function () {
	$avalaible_trends = R::findAll('available_trends');
	try {
		$avalaible_trends = R::exportAll($avalaible_trends);
		echo json_encode($avalaible_trends);
	}
	catch(Exception $ex) {
		//$success = $ex->getMessage(); //para debug
		returnResult('select', false, -1);
	}
});

//Select Por ID
/*$app->get('/foo/:bar', function ($bar) {
	
});*/

//Insert 
/*$app->post('/foo', function () use ($app) {

});*/

//Update
/*$app->put('/foo/:bar', function ($bar) use ($app) {

});*/

//Delete
/*$app->delete('/foo/:bar', function ($bar) {

});*/

//Lanzar
$app->run();