<?php
require_once 'app.php';
$file = $_REQUEST['file'];

if ($file == null) {
	die('Invalid request: Wrong parameter!');
}

$filepath = ROOT_PATH.'/_temp/'.$file;
$filename = $file;

if (!file_exists($filepath) || !is_file($filepath)) {
	header('Content-Description: File Transfer');
	header("Pragma: public");
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private', false);
	header("Content-Type: text/plain");
	header('Content-Disposition: attachment; filename="empty.txt";');
	header('Content-Transfer-Encoding: binary');
		
	echo('File download don\'t exit');
	
	exit;
}

header('Content-Description: File Transfer');
header("Pragma: public");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header("Content-Type: ". mime_content_type($filepath));
header('Content-Disposition: attachment; filename="'.$filename.'";');
header('Content-Transfer-Encoding: binary');

readfile($filepath);
exit;