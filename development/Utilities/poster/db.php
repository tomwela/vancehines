<?php

define('DB_FILE', __DIR__.'/db');
error_reporting(E_ALL & ~E_NOTICE);

function loadDB() {
	return @unserialize(file_get_contents(DB_FILE)) ?: array();
}

function saveDB($db) {
	file_put_contents(DB_FILE, serialize($db));
}


switch ($_REQUEST['a']) {
	case 'save':
		//$sect = $_REQUEST['section'];
		//$fv = json_decode($_REQUEST['fv'], true);
		
		$db = json_decode(file_get_contents('php://input'), true);
		if (!count($db))
			die('nothing to save');
		
		//$db[$sect] = $fv;
		saveDB($db);
		echo 'ok';
		break;
	case '1212load':
		$sect = $_REQUEST['section'];
		if (!$sect )
			die('section?');
		$db = loadDB();
		echo json_encode($db[$sect]);
		break;
	default:
		echo 'no action';
}

?>