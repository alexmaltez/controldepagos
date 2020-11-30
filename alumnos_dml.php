<?php

function alumnos_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('alumnos');
	if(!$arrPerm['insert']) return false;

	$data = [
		'carnet' => Request::val('carnet', ''),
		'nombre_completo' => Request::val('nombre_completo', ''),
		'facultad' => Request::val('facultad', ''),
		'turno' => Request::val('turno', ''),
		'fecha_matri' => Request::dateComponents('fecha_matri', ''),
		'numero' => Request::val('numero', ''),
	];

	if($data['carnet'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'N&#250;mero de Carnet': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['nombre_completo'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Nombre y Apellido': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['facultad'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Facultad': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['turno'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Turno': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['fecha_matri'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Fecha Matricula': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['numero'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Numero': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}

	// hook: alumnos_before_insert
	if(function_exists('alumnos_before_insert')) {
		$args = [];
		if(!alumnos_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('alumnos', backtick_keys_once($data), $error);
	if($error)
		die("{$error}<br><a href=\"#\" onclick=\"history.go(-1);\">{$Translation['< back']}</a>");

	$recID = db_insert_id(db_link());

	update_calc_fields('alumnos', $recID, calculated_fields()['alumnos']);

	// hook: alumnos_after_insert
	if(function_exists('alumnos_after_insert')) {
		$res = sql("SELECT * FROM `alumnos` WHERE `id`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=[];
		if(!alumnos_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('alumnos', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(!empty($_REQUEST['SelectedID'])) alumnos_copy_children($recID, $_REQUEST['SelectedID']);

	return $recID;
}

function alumnos_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$uploads_dir = realpath(dirname(__FILE__) . '/../' . $Translation['ImageFolder']);
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function alumnos_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('alumnos', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: alumnos_before_delete
	if(function_exists('alumnos_before_delete')) {
		$args = [];
		if(!alumnos_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	// child table: movimientos
	$res = sql("SELECT `id` FROM `alumnos` WHERE `id`='{$selected_id}'", $eo);
	$id = db_fetch_row($res);
	$rires = sql("SELECT COUNT(1) FROM `movimientos` WHERE `carnet`='" . makeSafe($id[0]) . "'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'movimientos', $RetMsg);
		return $RetMsg;
	} elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation['confirm delete'];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'movimientos', $RetMsg);
		$RetMsg = str_replace('<Delete>', '<input type="button" class="button" value="' . $Translation['yes'] . '" onClick="window.location = \'alumnos_view.php?SelectedID=' . urlencode($selected_id) . '&delete_x=1&confirmed=1\';">', $RetMsg);
		$RetMsg = str_replace('<Cancel>', '<input type="button" class="button" value="' . $Translation[ 'no'] . '" onClick="window.location = \'alumnos_view.php?SelectedID=' . urlencode($selected_id) . '\';">', $RetMsg);
		return $RetMsg;
	}

	sql("DELETE FROM `alumnos` WHERE `id`='{$selected_id}'", $eo);

	// hook: alumnos_after_delete
	if(function_exists('alumnos_after_delete')) {
		$args = [];
		alumnos_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='alumnos' AND `pkValue`='{$selected_id}'", $eo);
}

function alumnos_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('alumnos', $selected_id, 'edit')) return false;

	$data = [
		'carnet' => Request::val('carnet', ''),
		'nombre_completo' => Request::val('nombre_completo', ''),
		'facultad' => Request::val('facultad', ''),
		'turno' => Request::val('turno', ''),
		'fecha_matri' => Request::dateComponents('fecha_matri', ''),
		'numero' => Request::val('numero', ''),
	];

	if($data['carnet'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'N&#250;mero de Carnet': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['nombre_completo'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Nombre y Apellido': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['facultad'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Facultad': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['turno'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Turno': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['fecha_matri'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Fecha Matricula': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['numero'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Numero': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	// get existing values
	$old_data = getRecord('alumnos', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: alumnos_before_update
	if(function_exists('alumnos_before_update')) {
		$args = ['old_data' => $old_data];
		if(!alumnos_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'alumnos', 
		backtick_keys_once($set), 
		['`id`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="alumnos_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('alumnos', $data['selectedID'], calculated_fields()['alumnos']);

	// hook: alumnos_after_update
	if(function_exists('alumnos_after_update')) {
		$res = sql("SELECT * FROM `alumnos` WHERE `id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['id'];
		$args = ['old_data' => $old_data];
		if(!alumnos_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update ownership data
	sql("UPDATE `membership_userrecords` SET `dateUpdated`='" . time() . "' WHERE `tableName`='alumnos' AND `pkValue`='" . makeSafe($selected_id) . "'", $eo);
}

function alumnos_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm = getTablePermissions('alumnos');
	if(!$arrPerm['insert'] && $selected_id=='') { return ''; }
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != '') {
		$dvprint = true;
	}


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: facultad
	$combo_facultad = new Combo;
	$combo_facultad->ListType = 0;
	$combo_facultad->MultipleSeparator = ', ';
	$combo_facultad->ListBoxHeight = 10;
	$combo_facultad->RadiosPerLine = 1;
	if(is_file(dirname(__FILE__).'/hooks/alumnos.facultad.csv')) {
		$facultad_data = addslashes(implode('', @file(dirname(__FILE__).'/hooks/alumnos.facultad.csv')));
		$combo_facultad->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions($facultad_data)));
		$combo_facultad->ListData = $combo_facultad->ListItem;
	} else {
		$combo_facultad->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions("Ingenier&#237;a;;Periodismo;;Turismo;;Administraci&#243;n;;Derecho")));
		$combo_facultad->ListData = $combo_facultad->ListItem;
	}
	$combo_facultad->SelectName = 'facultad';
	$combo_facultad->AllowNull = false;
	// combobox: turno
	$combo_turno = new Combo;
	$combo_turno->ListType = 0;
	$combo_turno->MultipleSeparator = ', ';
	$combo_turno->ListBoxHeight = 10;
	$combo_turno->RadiosPerLine = 1;
	if(is_file(dirname(__FILE__).'/hooks/alumnos.turno.csv')) {
		$turno_data = addslashes(implode('', @file(dirname(__FILE__).'/hooks/alumnos.turno.csv')));
		$combo_turno->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions($turno_data)));
		$combo_turno->ListData = $combo_turno->ListItem;
	} else {
		$combo_turno->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions("Matutino;;Vespertino;;Nocturno;;Sabatino;;Dominical;;OnLine")));
		$combo_turno->ListData = $combo_turno->ListItem;
	}
	$combo_turno->SelectName = 'turno';
	$combo_turno->AllowNull = false;
	// combobox: fecha_matri
	$combo_fecha_matri = new DateCombo;
	$combo_fecha_matri->DateFormat = "dmy";
	$combo_fecha_matri->MinYear = 1900;
	$combo_fecha_matri->MaxYear = 2100;
	$combo_fecha_matri->DefaultDate = parseMySQLDate('', '');
	$combo_fecha_matri->MonthNames = $Translation['month names'];
	$combo_fecha_matri->NamePrefix = 'fecha_matri';

	if($selected_id) {
		// mm: check member permissions
		if(!$arrPerm['view']) return '';

		// mm: who is the owner?
		$ownerGroupID = sqlValue("SELECT `groupID` FROM `membership_userrecords` WHERE `tableName`='alumnos' AND `pkValue`='" . makeSafe($selected_id) . "'");
		$ownerMemberID = sqlValue("SELECT LCASE(`memberID`) FROM `membership_userrecords` WHERE `tableName`='alumnos' AND `pkValue`='" . makeSafe($selected_id) . "'");

		if($arrPerm['view'] == 1 && getLoggedMemberID() != $ownerMemberID) return '';
		if($arrPerm['view'] == 2 && getLoggedGroupID() != $ownerGroupID) return '';

		// can edit?
		$AllowUpdate = 0;
		if(($arrPerm['edit'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['edit'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['edit'] == 3) {
			$AllowUpdate = 1;
		}

		$res = sql("SELECT * FROM `alumnos` WHERE `id`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'alumnos_view.php', false);
		}
		$combo_facultad->SelectedData = $row['facultad'];
		$combo_turno->SelectedData = $row['turno'];
		$combo_fecha_matri->DefaultDate = $row['fecha_matri'];
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
	} else {
		$combo_facultad->SelectedText = ( $_REQUEST['FilterField'][1] == '4' && $_REQUEST['FilterOperator'][1] == '<=>' ? $_REQUEST['FilterValue'][1] : '');
		$combo_turno->SelectedText = ( $_REQUEST['FilterField'][1] == '5' && $_REQUEST['FilterOperator'][1] == '<=>' ? $_REQUEST['FilterValue'][1] : '');
	}
	$combo_facultad->Render();
	$combo_turno->Render();

	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/alumnos_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/alumnos_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Alumno details', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', ($_REQUEST['Embedded'] ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return alumnos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return alumnos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if($_REQUEST['Embedded']) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!$_REQUEST['Embedded']) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate) {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return alumnos_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm[4]==1 && $ownerMemberID==getLoggedMemberID()) || ($arrPerm[4]==2 && $ownerGroupID==getLoggedGroupID()) || $arrPerm[4]==3) { // allow delete?
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" onclick="return confirm(\'' . $Translation['are you sure?'] . '\');" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', ($ShowCancel ? '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>' : ''), $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly .= "\tjQuery('#carnet').replaceWith('<div class=\"form-control-static\" id=\"carnet\">' + (jQuery('#carnet').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#nombre_completo').replaceWith('<div class=\"form-control-static\" id=\"nombre_completo\">' + (jQuery('#nombre_completo').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#facultad').replaceWith('<div class=\"form-control-static\" id=\"facultad\">' + (jQuery('#facultad').val() || '') + '</div>'); jQuery('#facultad-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#turno').replaceWith('<div class=\"form-control-static\" id=\"turno\">' + (jQuery('#turno').val() || '') + '</div>'); jQuery('#turno-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#fecha_matri').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#fecha_matriDay, #fecha_matriMonth, #fecha_matriYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#numero').replaceWith('<div class=\"form-control-static\" id=\"numero\">' + (jQuery('#numero').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif($AllowInsert) {
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(facultad)%%>', $combo_facultad->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(facultad)%%>', $combo_facultad->SelectedData, $templateCode);
	$templateCode = str_replace('<%%COMBO(turno)%%>', $combo_turno->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(turno)%%>', $combo_turno->SelectedData, $templateCode);
	$templateCode = str_replace('<%%COMBO(fecha_matri)%%>', ($selected_id && !$arrPerm[3] ? '<div class="form-control-static">' . $combo_fecha_matri->GetHTML(true) . '</div>' : $combo_fecha_matri->GetHTML()), $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(fecha_matri)%%>', $combo_fecha_matri->GetHTML(true), $templateCode);

	/* lookup fields array: 'lookup field name' => array('parent table name', 'lookup field caption') */
	$lookup_fields = array();
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent hspacer-md" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] && !$_REQUEST['Embedded']) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-success add_new_parent hspacer-md" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus-sign"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(id)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(carnet)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(nombre_completo)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(facultad)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(turno)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(fecha_matri)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(numero)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', safe_html($urow['id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', html_attr($row['id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode($urow['id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(carnet)%%>', safe_html($urow['carnet']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(carnet)%%>', html_attr($row['carnet']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(carnet)%%>', urlencode($urow['carnet']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(nombre_completo)%%>', safe_html($urow['nombre_completo']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(nombre_completo)%%>', html_attr($row['nombre_completo']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(nombre_completo)%%>', urlencode($urow['nombre_completo']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(facultad)%%>', safe_html($urow['facultad']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(facultad)%%>', html_attr($row['facultad']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(facultad)%%>', urlencode($urow['facultad']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(turno)%%>', safe_html($urow['turno']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(turno)%%>', html_attr($row['turno']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(turno)%%>', urlencode($urow['turno']), $templateCode);
		$templateCode = str_replace('<%%VALUE(fecha_matri)%%>', @date('d/m/Y', @strtotime(html_attr($row['fecha_matri']))), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(fecha_matri)%%>', urlencode(@date('d/m/Y', @strtotime(html_attr($urow['fecha_matri'])))), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(numero)%%>', safe_html($urow['numero']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(numero)%%>', html_attr($row['numero']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(numero)%%>', urlencode($urow['numero']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(carnet)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(carnet)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(nombre_completo)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(nombre_completo)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(facultad)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(facultad)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(turno)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(turno)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(fecha_matri)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(fecha_matri)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(numero)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(numero)%%>', urlencode(''), $templateCode);
	}

	// process translations
	foreach($Translation as $symbol=>$trans) {
		$templateCode = str_replace("<%%TRANSLATION($symbol)%%>", $trans, $templateCode);
	}

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if($_REQUEST['dvprint_x'] == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('alumnos');
	if($selected_id) {
		$jdata = get_joined_record('alumnos', $selected_id);
		if($jdata === false) $jdata = get_defaults('alumnos');
		$rdata = $row;
	}
	$templateCode .= loadView('alumnos-ajax-cache', array('rdata' => $rdata, 'jdata' => $jdata));

	// hook: alumnos_dv
	if(function_exists('alumnos_dv')) {
		$args=[];
		alumnos_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}