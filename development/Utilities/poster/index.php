<?php

require __DIR__.'/parse.php';

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Poster</title>
<script type="text/javascript" src="jquery-1.6.2.min.js"></script>
<link rel="stylesheet" href="poster.css" />
</head>
<body>
<div class="toolbar panel">
	<div class="row" style="width:270px;">
		<label>Section</label>
		<select id="selectSection" style="width:260px;" onchange="selectSection(this.value)" \>
		<? foreach ($sections as $k=>$v) if (!$v['isPersistent']) { ?>
		<option value="<?=$k;?>"><?=$v['name'];?></option>
		<? } ?>
		</select>
	</div>
	<div class="row" style="width:210px;">
		<label>Parameter preset</label>
		<select id="selectPreset" style="width:200px; height:22px;" onchange="selectPreset(this.value);">
		<option value="">New</option>
		</select>
	</div>
	<div class="button float" onclick="selectPreset()" style="width:40px;">Load</div>
	<div class="red button float" onclick="deletePreset()" style="width:50px;">Delete</div>
	<div class="row" style="width:210px;">
		<label>Preset name</label>
		<input id="presetName" type="text" style="width:200px; height:16px;"/>
	</div>
	<div class="green button float" onclick="savePreset()" style="width:50px;">Save</div>
</div>
<div class="clear"></div>
<div class="mainform">

	<div id="formcontainer" class="form panel">
	<h3 id="title"></h3>
		<div id="void" style="display:none;">
		<form id="tempform"></form>
		</div>
		<form id="form" action="" method="" enctype="multipart/form-data" target="_BLANK">
		</form>
		<div class="clear"></div>
		<div class="button-block">
			<div class="button" onclick="cleanForm()">Clear</div>
			<div id="_submit" class="red button" onclick="submit()">Submit</div>
			<? if ($ini['main']['xdebug']): ?>
			<br/>
			<label>X-Debug</label>
			<input type="checkbox" id="xdebug" onchange="checkXDebug(this)"/>
			<? endif; 
			//XDEBUG_SESSION_START
			?>
		</div>
	</div>
	<!--iframe id="output" style="float:left; width:400px; height:500px;">sdfsdf
	</iframe-->
</div>
<pre>
<?php


?>
</pre>
<script type="text/javascript">
	var sections = <?=json_encode($sections);?>;
	var presets = <?=json_encode($db);?>;
	var saveOnSubmit = <?=(int)(bool)$ini['main']['saveOnSubmit'];?>;
	var xDebugSession = "<?=$ini['main']['xdebug']?>";
</script>
<script type="text/javascript" src="poster.js"></script>
</body>
</html>