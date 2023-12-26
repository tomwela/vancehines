<?php

define('DB_FILE', __DIR__.'/db');
error_reporting(E_ALL & ~E_NOTICE);
function loadDB() {
	return @unserialize(file_get_contents(DB_FILE)) ?: (object)array();
}
$db = loadDB();

$ini = parse_ini_file(__DIR__.'/config.ini', true, INI_SCANNER_RAW);

// handle ini-sections inheritance
foreach ($ini as $sect=>&$info)
	foreach ($info as $k=>$v)
		if ($k=='@include' && isset($ini[$v])) {
			$info = array_merge($ini[$v], $info);
			unset ($info[$k]);
		}
					
// handle arrays in keys (eg. file[]=)
foreach ($ini as $sect=>&$info)
	if ($sect[0]=='!')
		foreach ($info as $k=>$v)
			if (is_array($v)) {
				foreach ($v as $i=>$val)
					$info[$k.'['.$i.']'] = $val;
				unset($info[$k]);
			}
			
// generate input form sections
$sectionsMap = $sections= array();

function iniSection2Form($name, $per = false) {
	global $sections, $sectionsMap, $ini;
	$new = array('name'=>substr($name,1));
	$sectionsMap[$name]=count($sections);
	foreach ($ini[$name] as $k=>$v) {
		if ($k[0]=='@') {
			$new[substr($k,1)] = $v;
			if ($k=='@persistent' && isset($ini[$v])) {
				if (!isset($sectionsMap[$v]))
					$sections[] = iniSection2Form($v, true) + array('isPersistent'=>true);
				$new['persistentID'] = $sectionsMap[$v];
			}
		} else {
			$v = explode('|',$v);
			$field = array('name'=>$k, 'label'=>'<b>'.$k.'</b>'.($v[1]?' - '.$v[1]:''), 'type'=>$v[0]);
			if ($field['type']=='select' && is_array($ar = json_decode($v[2],true)))
				$field['options'] = array_combine($ar = array_values($ar),$ar);
			elseif ($field['type']=='hidden')
				$field['value'] = $v[2] ?: '';
			if ($per) $field['persistent'] = true;
			$new['fields'][] = $field;
		}
	}
	return $new;
}

foreach ($ini as $name=>&$info)
	if ($name[0]=='!') {
		$sections[] = iniSection2Form($name);
	}

	//var_export($ini);
//$sections = array_values($sections);

?>