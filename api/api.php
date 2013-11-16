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
		returnResult('select', false, $student_id);
	}
});

//Select Por ID
$app->get('/student/:student_id', function ($student_id) {
	$student = R::findOne('students','student_id=?',array($student_id));
	try {
		$student = R::exportAll($student);
		echo json_encode($student);
	}
	catch(Exception $ex) {
		//$success = $ex->getMessage(); //para debug
		returnResult('select', false, $student_id);
	}
});

//Insert 
$app->post('/student', function () use ($app) {
	$success = true;
    $student = R::dispense('students');
    $student->student_id = $app->request()->post('student_id');
    $student->student_name = $app->request()->post('student_name');
    $student->score = $app->request()->post('score');
    $student->date = R::isoDateTime();
    
    try {
   		R::store($student);
    }
    catch(Exception $ex) {
    	//$success = $ex->getMessage(); //para debug
    	$success = false;
    }
    
    returnResult('add', $success, $student_id);
});

//Update
$app->put('/student/:student_id', function ($student_id) use ($app) {
	$success = true;
	$student = R::findOne('students','student_id=?',array($student_id));
    $student->student_id = $app->request()->post('student_id');
    $student->student_name = $app->request()->post('student_name');
    $student->score = $app->request()->post('score');
    $student->date = $app->request()->post('date');
    
    try {
    	R::store($student);
    }
    catch(Exception $ex) {
    	//$success = $ex->getMessage(); //para debug
    	$success = false;
    }

    returnResult('edit', $success, $student_id);
});

//Delete
$app->delete('/student/:student_id', function ($student_id) {
	$success = true;
    $student = R::load('students', $student_id);
	
    try {
    	R::trash($student);
    }
    catch(Exception $ex) {
    	//$success = $ex->getMessage(); //para debug
    	$success = false;
    }

    returnResult('delete', $success, $student_id);
});

//Lanzar
$app->run();