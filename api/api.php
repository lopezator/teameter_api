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
$app->get('/students', function () {
	$students = R::findAll('students','ORDER BY student_name');
	try {
		$students = R::exportAll($students);
		echo json_encode($students);
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

//404 Not found
$app->notFound(function () use ($app) {
	$app->render('404.php');
});

//Lanzar
$app->run();