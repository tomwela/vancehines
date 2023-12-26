<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Error - Message</title>
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
	$db->q("CREATE TABLE `{$cfg->getValue('table', 'db')}` (`errorID` int(11) unsigned NOT NULL, `message` TEXT, PRIMARY KEY (`errorID`)) DEFAULT CHARSET=utf8;");
}

if (isset($_REQUEST['clean'])) {
	$db->q("TRUNCATE TABLE `{$cfg->getValue('table', 'db')}`;");
	
	$msg = "Clean data success.";
}

if (!is_dir(ROOT_PATH . '/../../Temporary/_temp/')) {
	$msg = "Please create folder '".ROOT_PATH."/../../Temporary/_temp/'.";
}

if (isset($_FILES['bulk']) && $_FILES['bulk']['name'] != "") {
	$target_path = ROOT_PATH . '/../../Temporary/_temp/' . basename($_FILES['bulk']['name']);
	
	if(move_uploaded_file($_FILES['bulk']['tmp_name'], $target_path)) {
		$data = new Spreadsheet_Excel_Reader($target_path, true, 'UTF-8');
		$rows = $data->sheets[0]['cells'];
		$headRow = $rows[1];
		
		if ('error code' == strtolower($headRow[1]) && 'message' == strtolower($headRow[2])) {
			$i = 1;
			foreach ($rows as $row) {
				if ($i == 1) {
					$i++;
					continue;
				}
				
				$codeID = (int) $row[1];
				$message = (string) $row[2];
				$result = $db->one("SELECT errorID FROM `{$cfg->getValue('table', 'db')}` WHERE errorID={$codeID}");
				
				if ($result == null) {
					$db->insert($cfg->getValue('table', 'db'), array('errorID' => $codeID, 'message' => $message));
				} else {
					$db->update($cfg->getValue('table', 'db'), array('errorID' => $codeID, 'message' => $message), array('errorID' => $codeID));
				}
				
				$i++;
			}
			
			$msg = "Import data success.";
		} else {
			$msg = "Wrong file format, please try again!";
		}
		
		@unlink($target_path);
	} else {
		$msg = "There was an error uploading the file, please try again!";
	}
}
if ($msg != '') {
	echo("<strong>{$msg}</strong>");
}

$result = $db->q("SELECT * FROM {$cfg->getValue('table', 'db')}");
$links = '';
if (count($result) > 0) {
	$links = '<strong>Other actions</strong>:<br/>&nbsp; - <a href="lookup.php">Generate lookup file</a><br/>&nbsp; - <a href="export.php">Export (Table file and Excel file)</a>';
}
?>
<!-- multipart/form-data | application/x-www-form-urlencoded -->
	<form action="" name="frmBulk" method="post" enctype="multipart/form-data">
		<input type="file" name="bulk" id="bulk"/>
		<input type="submit" value="Submit"/><br/>
		<input type="checkbox" id="clean" name="clean" value="1" /> 
		<label for="clean">Clear the message table</label><br/>
		<a href="errormessage.xls">Use example file</a>
	</form>
	<div>
		<?=$links?>
	</div>
</body>
</html>


