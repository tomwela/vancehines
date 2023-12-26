	var curSect, curSectID;
	var curPreset;
	var form = $('#form');

	$(document).ready(function() {
		$('#selectSection').val('0');
		selectSection(0);
		$('.button').mousedown(press).mouseup(unpress).mouseout(unpress);
	});
	
	function selectSection(n) {
		//alert('a='+a+'; b='+b);
		oldSect = curSect;
		curSect = sections[n];
		$('#title').html(curSect.title ? curSect.title : curSect.name);
		
		form.attr('action', (curSect.action ? curSect.action : '') + (curSect.actionAppendName ? curSect.name : ''));
		form.attr('method', curSect.method ? curSect.method.toUpperCase() : 'POST');
		//var movePer= (oldSect && oldSect.persistentID && curSect.persistentID && oldSect.persistentID==curSect.persistentID);
		if (oldSect && oldSect.persistentID) {
			form.find('.non-persistent').remove();
			sections[oldSect.persistentID].data = getFormValues();
		}
		form.empty();
		if (curSect.json != undefined) {
			$('#void').empty();
			form.clone().appendTo('#void');
			var form2 = $('#void form');
			form2.attr('id', 'form_json').append('<input type="hidden" id="json" name="'+curSect.json+'"/>');
		}
		var allFields = curSect.fields ? curSect.fields : [];
		if (curSect.persistentID && (typeof sections[curSect.persistentID].fields == 'object'))
			allFields = sections[curSect.persistentID].fields.concat(allFields);
		//console.log(allFields);
		var fieldhtml, col, fClass;
		var	colSize = curSect.maxRows ? curSect.maxRows : 8;
		for (var i in allFields) {
			field = allFields[i];
			
			if (i%colSize==0 || i==allFields.length) {
				form.append('<div class="col"></div>');
				col = form.find('.col:last');
			}
			fClass = field.type + ' ' + (field.persistent ? 'persistent' : 'non-persistent');
			switch (field.type) {
				case 'file': fieldhtml = '<input type="file" name="'+field.name+'"/>'; break;
				case 'hidden': fieldhtml = '<input type="hidden" name="'+field.name+'" value="'+field.value+'"/>'; break;
				case 'textarea': fieldhtml = '<textarea rows="0" cols="0" name="'+field.name+'" class="'+fClass+'"></textarea>'; break;
				case 'select': case 'bool':
					var opts = (field.type=='bool' ? {'':'',0:'false',1:'true'} : field.options);
					fieldhtml = '<select name="'+field.name+'" class="'+fClass+'">';
					for (k in opts)
						fieldhtml += '<option value="'+k+'">'+opts[k]+'</option>';
					fieldhtml += '</select>'; 
				break;
				default: fieldhtml = '<input type="text" name="'+field.name+'" class="'+fClass+'"/>'; break;
			}
			if (field.type!='hidden') fieldhtml = '<div class="row"><label>'+field.label+'</label>'+fieldhtml+'</div>'
			col.append(fieldhtml);
		}
		$('#form input[type=text]').keypress(function(e) {
			if ((e || event).keyCode==13) submit();
		});
		populatePresetList();
		selectPreset();
		/*jQuery.ajax('db.php', {
			'type': 'POST',
			'data': {'a': 'load', 'section':curSect.name},
			'dataType': 'json',
			'success': function(data, status) {
				if ('object' != typeof data) alert('Load session failed: '+data);
				for (var k in data) $('#form input[name="'+k+'"]').val(data[k]);
			}
		});*/
		$('#xdebug').attr('checked',false);
	}
	
	function populatePresetList() {
		var sel = $('#selectPreset');
		sel.html('');
		var name;
		if (typeof presets[curSect.name]!='object')
			presets[curSect.name] = [];
		for (var k in presets[curSect.name]) {
			sel.append('<option value='+k+'>'+presets[curSect.name][k].name+'</option>');
		}
	}
	
	function selectPreset(id) {
		if (id==undefined)
			id = $('#selectPreset').val();
		curPreset = presets[curSect.name][id];
		if (curPreset==undefined) {
			cleanForm();
		} else {
			for (var k in curPreset.fv) {
				form.find('[name="'+k+'"]').val(curPreset.fv[k]);
			}
			$('#presetName').val(curPreset.name);
		}
		$('#form .persistent').val('');
		if (curSect.persistentID && sections[curSect.persistentID].data)
			for (var k in sections[curSect.persistentID].data) {
				$('#form input[name="'+k+'"]').val(sections[curSect.persistentID].data[k]);
			}
	}
	
	function getFormValues(id) {
		var fm = id ? $('#'+id) : form;
		var ar = fm.serializeArray();
		var fv = new Object();
		for (var i in ar) fv[ar[i].name] = ar[i].value;
		return fv;
	}
	
	function savePreset() {
		var name = $('#presetName').val();
		if (!name) $('#presetName').val(name = 'Default');
		//console.log(name);
		var newPreset = {"name": name, "fv": getFormValues()};
		var id = presets[curSect.name].length;
		for (var i in presets[curSect.name])
			if (presets[curSect.name][i].name == name) {
				id = i;
				break;
			}
		presets[curSect.name].splice(id,1,newPreset)
		//console.log(presets[curSect.name]);
		jQuery.ajax('db.php?a=save', {
			'type': 'POST',
			'processData': false,
			'mimeType': 'application/json',
			'data': JSON.stringify(presets),
			'dataType': 'text',
			'success': function(data, status) {
				if (data!='ok') alert (data);
			}
		});
		populatePresetList();
		$('#selectPreset').val(id);
		//selectPreset(id);
	}
	
	function deletePreset() {
		var id = $('#selectPreset').val()
		presets[curSect.name].splice(id,1);
		populatePresetList();
	}
	
	function cleanForm() {
		form.find('input[type!=hidden],textarea').val('');
		$('#presetName').val('');
	}
	
	function submit() {
		var fv = getFormValues();
		var xdebug = $('xdebug').prop('checked');
		if (curSect.json != undefined) {
			for (var k in curSect.fields) {
				var f = curSect.fields[k].name;
        if (Boolean(String(fv[f])) == false) {
          delete fv[f];
          continue;
        }
				switch (curSect.fields[k].type) {
					case 'num': case 'int':
						fv[f] = Number(fv[f]); break;
					case 'bool': fv[f] = (fv[f]==1); break;
					case 'json': 
					  try { fv[f] = JSON.parse(fv[f]); } catch (e) { fv[f] = null; }
					break;
				}
			}
			$('#json').val( Object.keys(fv).length ? JSON.stringify(fv) : '');
			$('#form_json input[type=file]').remove();
			$('#form input[type=file]').clone().appendTo('#form_json');
			
			$('#form_json').submit();
		} else {
			$('#form').submit();
		}
		if (saveOnSubmit)
			savePreset();
	}
	
	function checkXDebug(el) {
		var actForm = $('#form'+(curSect.json!=undefined ? '_json' : ''));
		if (el.checked) {
			if (curSect.method.toLowerCase()=='get') {
				actForm.append('<input id="xDebugInput" type="hidden" name="XDEBUG_SESSION_START" value="'+xDebugSession+'"/>');
			} else {
				actForm.attr('action', actForm.attr('action') + '?XDEBUG_SESSION_START='+xDebugSession);
			}
		} else {
			$('#xDebugInput').remove();
			actForm.attr('action', (curSect.action ? curSect.action : '') + (curSect.actionAppendName ? curSect.name : ''));
		}
	}
	
	function press() {
		this.style.borderStyle = 'inset';
		//e.style.padding = '2px 0 0 2px';
		//e.style.width = '88px';
		//e.style.height = '16px';
	}
	function unpress() {
		this.style.borderStyle = 'outset';
		//e.style.padding = '0 0 0 0';
		//e.style.width = '90px';
		//e.style.height = '18px';
	}