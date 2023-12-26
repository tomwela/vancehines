<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Error - Message :: Generate lookup file</title>
</head>
<body>
<?php
require_once 'app.php';
$msg = '';

$cfg = new ConfigINI(APP_CONFIG_FILE);
$db = new edb($cfg->getValue('hostname', 'db'),
		$cfg->getValue('username', 'db'),
		$cfg->getValue('password', 'db'),
		$cfg->getValue('database', 'db'));

$result = $db->q("SHOW TABLES LIKE '{$cfg->getValue('table', 'db')}'");
if (count($result) <= 0) {
	die('<script type="text/javascript">window.location = "import.php";</script>');
}

$lookupFile = ROOT_PATH . '/../../lookup.json';
if (is_writable($lookupFile)) {
	$jsonObj = json_decode(file_get_contents($lookupFile), true);
	$usererrormessages = array();
	$result = $db->q("SELECT * FROM {$cfg->getValue('table', 'db')}");
	
	$msg = 'Ok';
	
	foreach ($result as $item) {
		$usererrormessages[$item['errorID']] = $item['message'];
	}
	$jsonObj['usererrormessages'] = $usererrormessages;
	
	file_put_contents($lookupFile, json_encode($jsonObj));
} else {
	$msg = "Create and change mode writeable file '{$lookupFile}'";
}

if ($msg != '') {
	echo("<strong>{$msg}.</strong>");
}
?>
</body>
</html>


