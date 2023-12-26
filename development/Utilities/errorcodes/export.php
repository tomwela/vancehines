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
	die('<script type="text/javascript">window.location = "import.php";</script>');
}

$result = $db->q("SELECT * FROM {$cfg->getValue('table', 'db')}");

$myFile = ROOT_PATH . '/_temp/errormessage.txt';
$fh = fopen($myFile, 'w+');

if ($fh) {
	ftruncate($fh, 0);
	fwrite($fh, "<table>\n");
	fwrite($fh, "<tr><th>Error code</th><th>Message</th></tr>\n");
	
	foreach ($result as $item) {
		fwrite($fh, "<tr><td>{$item['errorID']}</td><td>{$item['message']}</td></tr>\n");
	}
	fwrite($fh, '</table>');
	fclose($fh);
} else {
	$msg = 'Can\'t create file. Please check folder \''.ROOT_PATH.'/_temp\'';
}

$objPHPExcel = new \PHPExcel();

$objPHPExcel->getProperties()->setCreator("Saritasa")
			->setTitle("Error Message Document")
			->setDescription("Error Message Document");

$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', 'Error code')
			->setCellValue('B1', 'Message');
$counter = 2;
foreach ($result as $item) {
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $counter, $item['errorID'])
				->setCellValue('B'. $counter, $item['message']);
	$counter++;
}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(ROOT_PATH . '/_temp/errormessage.xls');

if ($msg != '') {
	echo("<strong>{$msg}.</strong>");
}
?>
<strong>1. Table (HTML content) file: </strong> <a href="download.php?file=errormessage.txt">errormessage.txt</a> <br/>
<strong>2. Excel file: </strong> <a href="download.php?file=errormessage.xls">errormessage.xls</a> <br/>
</body>
</html>


