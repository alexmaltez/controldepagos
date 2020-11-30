<?php

function movimientos_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('movimientos');
	if(!$arrPerm['insert']) return false;

	$data = [
		'carnet' => Request::val('carnet', ''),
		'descripcion' => Request::val('descripcion', ''),
		'comentarios' => Request::val('comentarios', ''),
		'fecha_pago' => Request::dateComponents('fecha_pago', ''),
		'importe_nio' => Request::val('importe_nio', ''),
		'tipo_pago' => Request::val('tipo_pago', ''),
	];

	if($data['carnet'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Carnet': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['descripcion'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Descripcion': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['fecha_pago'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Fecha pago': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['importe_nio'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Importe CS': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['tipo_pago'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Tipo pago': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}

	// hook: movimientos_before_insert
	if(function_exists('movimientos_before_insert')) {
		$args = [];
		if(!movimientos_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('movimientos', backtick_keys_once($data), $error);
	if($error)
		die("{$error}<br><a href=\"#\" onclick=\"history.go(-1);\">{$Translation['< back']}</a>");

	$recID = db_insert_id(db_link());

	update_calc_fields('movimientos', $recID, calculated_fields()['movimientos']);

	// hook: movimientos_after_insert
	if(function_exists('movimientos_after_insert')) {
		$res = sql("SELECT * FROM `movimientos` WHERE `id`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args=[];
		if(!movimientos_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('movimientos', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(!empty($_REQUEST['SelectedID'])) movimientos_copy_children($recID, $_REQUEST['SelectedID']);

	return $recID;
}

function movimientos_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$uploads_dir = realpath(dirname(__FILE__) . '/../' . $Translation['ImageFolder']);
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function movimientos_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('movimientos', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: movimientos_before_delete
	if(function_exists('movimientos_before_delete')) {
		$args = [];
		if(!movimientos_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	sql("DELETE FROM `movimientos` WHERE `id`='{$selected_id}'", $eo);

	// hook: movimientos_after_delete
	if(function_exists('movimientos_after_delete')) {
		$args = [];
		movimientos_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='movimientos' AND `pkValue`='{$selected_id}'", $eo);
}

function movimientos_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('movimientos', $selected_id, 'edit')) return false;

	$data = [
		'carnet' => Request::val('carnet', ''),
		'descripcion' => Request::val('descripcion', ''),
		'comentarios' => Request::val('comentarios', ''),
		'fecha_pago' => Request::dateComponents('fecha_pago', ''),
		'importe_nio' => Request::val('importe_nio', ''),
		'tipo_pago' => Request::val('tipo_pago', ''),
	];

	if($data['carnet'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Carnet': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['descripcion'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Descripcion': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['fecha_pago'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Fecha pago': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['importe_nio'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Importe CS': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	if($data['tipo_pago'] === '') {
		echo StyleSheet() . "\n\n<div class=\"alert alert-danger\">{$Translation['error:']} 'Tipo pago': {$Translation['field not null']}<br><br>";
		echo '<a href="" onclick="history.go(-1); return false;">' . $Translation['< back'] . '</a></div>';
		exit;
	}
	// get existing values
	$old_data = getRecord('movimientos', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: movimientos_before_update
	if(function_exists('movimientos_before_update')) {
		$args = ['old_data' => $old_data];
		if(!movimientos_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'movimientos', 
		backtick_keys_once($set), 
		['`id`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="movimientos_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('movimientos', $data['selectedID'], calculated_fields()['movimientos']);

	// hook: movimientos_after_update
	if(function_exists('movimientos_after_update')) {
		$res = sql("SELECT * FROM `movimientos` WHERE `id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['id'];
		$args = ['old_data' => $old_data];
		if(!movimientos_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update ownership data
	sql("UPDATE `membership_userrecords` SET `dateUpdated`='" . time() . "' WHERE `tableName`='movimientos' AND `pkValue`='" . makeSafe($selected_id) . "'", $eo);
}

function movimientos_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $ShowCancel = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;

	// mm: get table permissions
	$arrPerm = getTablePermissions('movimientos');
	if(!$arrPerm['insert'] && $selected_id=='') { return ''; }
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if($selected_id && $_REQUEST['dvprint_x'] != '') {
		$dvprint = true;
	}

	$filterer_carnet = thisOr($_REQUEST['filterer_carnet'], '');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: carnet
	$combo_carnet = new DataCombo;
	// combobox: descripcion
	$combo_descripcion = new Combo;
	$combo_descripcion->ListType = 0;
	$combo_descripcion->MultipleSeparator = ', ';
	$combo_descripcion->ListBoxHeight = 10;
	$combo_descripcion->RadiosPerLine = 1;
	if(is_file(dirname(__FILE__).'/hooks/movimientos.descripcion.csv')) {
		$descripcion_data = addslashes(implode('', @file(dirname(__FILE__).'/hooks/movimientos.descripcion.csv')));
		$combo_descripcion->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions($descripcion_data)));
		$combo_descripcion->ListData = $combo_descripcion->ListItem;
	} else {
		$combo_descripcion->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions("Matricula;;Mensualidad;;Clase Adicional;;Examen Sificiencia;;Examen Rescate;;Pago por Tutoria;;Abono por: ;;Otros")));
		$combo_descripcion->ListData = $combo_descripcion->ListItem;
	}
	$combo_descripcion->SelectName = 'descripcion';
	$combo_descripcion->AllowNull = false;
	// combobox: fecha_pago
	$combo_fecha_pago = new DateCombo;
	$combo_fecha_pago->DateFormat = "dmy";
	$combo_fecha_pago->MinYear = 1900;
	$combo_fecha_pago->MaxYear = 2100;
	$combo_fecha_pago->DefaultDate = parseMySQLDate('', '');
	$combo_fecha_pago->MonthNames = $Translation['month names'];
	$combo_fecha_pago->NamePrefix = 'fecha_pago';
	// combobox: tipo_pago
	$combo_tipo_pago = new Combo;
	$combo_tipo_pago->ListType = 0;
	$combo_tipo_pago->MultipleSeparator = ', ';
	$combo_tipo_pago->ListBoxHeight = 10;
	$combo_tipo_pago->RadiosPerLine = 1;
	if(is_file(dirname(__FILE__).'/hooks/movimientos.tipo_pago.csv')) {
		$tipo_pago_data = addslashes(implode('', @file(dirname(__FILE__).'/hooks/movimientos.tipo_pago.csv')));
		$combo_tipo_pago->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions($tipo_pago_data)));
		$combo_tipo_pago->ListData = $combo_tipo_pago->ListItem;
	} else {
		$combo_tipo_pago->ListItem = explode('||', entitiesToUTF8(convertLegacyOptions("Efectivo;;Tarjeta;;Transferencia")));
		$combo_tipo_pago->ListData = $combo_tipo_pago->ListItem;
	}
	$combo_tipo_pago->SelectName = 'tipo_pago';
	$combo_tipo_pago->AllowNull = false;

	if($selected_id) {
		// mm: check member permissions
		if(!$arrPerm['view']) return '';

		// mm: who is the owner?
		$ownerGroupID = sqlValue("SELECT `groupID` FROM `membership_userrecords` WHERE `tableName`='movimientos' AND `pkValue`='" . makeSafe($selected_id) . "'");
		$ownerMemberID = sqlValue("SELECT LCASE(`memberID`) FROM `membership_userrecords` WHERE `tableName`='movimientos' AND `pkValue`='" . makeSafe($selected_id) . "'");

		if($arrPerm['view'] == 1 && getLoggedMemberID() != $ownerMemberID) return '';
		if($arrPerm['view'] == 2 && getLoggedGroupID() != $ownerGroupID) return '';

		// can edit?
		$AllowUpdate = 0;
		if(($arrPerm['edit'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['edit'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['edit'] == 3) {
			$AllowUpdate = 1;
		}

		$res = sql("SELECT * FROM `movimientos` WHERE `id`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'movimientos_view.php', false);
		}
		$combo_carnet->SelectedData = $row['carnet'];
		$combo_descripcion->SelectedData = $row['descripcion'];
		$combo_fecha_pago->DefaultDate = $row['fecha_pago'];
		$combo_tipo_pago->SelectedData = $row['tipo_pago'];
		$urow = $row; /* unsanitized data */
		$hc = new CI_Input();
		$row = $hc->xss_clean($row); /* sanitize data */
	} else {
		$combo_carnet->SelectedData = $filterer_carnet;
		$combo_descripcion->SelectedText = ( $_REQUEST['FilterField'][1] == '3' && $_REQUEST['FilterOperator'][1] == '<=>' ? $_REQUEST['FilterValue'][1] : '');
		$combo_tipo_pago->SelectedText = ( $_REQUEST['FilterField'][1] == '7' && $_REQUEST['FilterOperator'][1] == '<=>' ? $_REQUEST['FilterValue'][1] : '');
	}
	$combo_carnet->HTML = '<span id="carnet-container' . $rnd1 . '"></span><input type="hidden" name="carnet" id="carnet' . $rnd1 . '" value="' . html_attr($combo_carnet->SelectedData) . '">';
	$combo_carnet->MatchText = '<span id="carnet-container-readonly' . $rnd1 . '"></span><input type="hidden" name="carnet" id="carnet' . $rnd1 . '" value="' . html_attr($combo_carnet->SelectedData) . '">';
	$combo_descripcion->Render();
	$combo_tipo_pago->Render();

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_carnet__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['carnet'] : $filterer_carnet); ?>"};

		jQuery(function() {
			setTimeout(function() {
				if(typeof(carnet_reload__RAND__) == 'function') carnet_reload__RAND__();
			}, 10);
		});
		function carnet_reload__RAND__() {
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint) { ?>

			$j("#carnet-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c) {
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_carnet__RAND__.value, t: 'movimientos', f: 'carnet' },
						success: function(resp) {
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="carnet"]').val(resp.results[0].id);
							$j('[id=carnet-container-readonly__RAND__]').html('<span id="carnet-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=alumnos_view_parent]').hide(); } else { $j('.btn[id=alumnos_view_parent]').show(); }


							if(typeof(carnet_update_autofills__RAND__) == 'function') carnet_update_autofills__RAND__();
						}
					});
				},
				width: '100%',
				formatNoMatches: function(term) { /* */ return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 5,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page) { /* */ return { s: term, p: page, t: 'movimientos', f: 'carnet' }; },
					results: function(resp, page) { /* */ return resp; }
				},
				escapeMarkup: function(str) { /* */ return str; }
			}).on('change', function(e) {
				AppGini.current_carnet__RAND__.value = e.added.id;
				AppGini.current_carnet__RAND__.text = e.added.text;
				$j('[name="carnet"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=alumnos_view_parent]').hide(); } else { $j('.btn[id=alumnos_view_parent]').show(); }


				if(typeof(carnet_update_autofills__RAND__) == 'function') carnet_update_autofills__RAND__();
			});

			if(!$j("#carnet-container__RAND__").length) {
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_carnet__RAND__.value, t: 'movimientos', f: 'carnet' },
					success: function(resp) {
						$j('[name="carnet"]').val(resp.results[0].id);
						$j('[id=carnet-container-readonly__RAND__]').html('<span id="carnet-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=alumnos_view_parent]').hide(); } else { $j('.btn[id=alumnos_view_parent]').show(); }

						if(typeof(carnet_update_autofills__RAND__) == 'function') carnet_update_autofills__RAND__();
					}
				});
			}

		<?php } else { ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_carnet__RAND__.value, t: 'movimientos', f: 'carnet' },
				success: function(resp) {
					$j('[id=carnet-container__RAND__], [id=carnet-container-readonly__RAND__]').html('<span id="carnet-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=alumnos_view_parent]').hide(); } else { $j('.btn[id=alumnos_view_parent]').show(); }

					if(typeof(carnet_update_autofills__RAND__) == 'function') carnet_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_contents());
	ob_end_clean();


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/movimientos_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/movimientos_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Movimiento details', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', ($_REQUEST['Embedded'] ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return movimientos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return movimientos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
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
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return movimientos_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
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
		$jsReadOnly .= "\tjQuery('#carnet').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#carnet_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#descripcion').replaceWith('<div class=\"form-control-static\" id=\"descripcion\">' + (jQuery('#descripcion').val() || '') + '</div>'); jQuery('#descripcion-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#comentarios').replaceWith('<div class=\"form-control-static\" id=\"comentarios\">' + (jQuery('#comentarios').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#fecha_pago').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#fecha_pagoDay, #fecha_pagoMonth, #fecha_pagoYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#importe_nio').replaceWith('<div class=\"form-control-static\" id=\"importe_nio\">' + (jQuery('#importe_nio').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#tipo_pago').replaceWith('<div class=\"form-control-static\" id=\"tipo_pago\">' + (jQuery('#tipo_pago').val() || '') + '</div>'); jQuery('#tipo_pago-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif($AllowInsert) {
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
			$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(carnet)%%>', $combo_carnet->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(carnet)%%>', $combo_carnet->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(carnet)%%>', urlencode($combo_carnet->MatchText), $templateCode);
	$templateCode = str_replace('<%%COMBO(descripcion)%%>', $combo_descripcion->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(descripcion)%%>', $combo_descripcion->SelectedData, $templateCode);
	$templateCode = str_replace('<%%COMBO(fecha_pago)%%>', ($selected_id && !$arrPerm[3] ? '<div class="form-control-static">' . $combo_fecha_pago->GetHTML(true) . '</div>' : $combo_fecha_pago->GetHTML()), $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(fecha_pago)%%>', $combo_fecha_pago->GetHTML(true), $templateCode);
	$templateCode = str_replace('<%%COMBO(tipo_pago)%%>', $combo_tipo_pago->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(tipo_pago)%%>', $combo_tipo_pago->SelectedData, $templateCode);

	/* lookup fields array: 'lookup field name' => array('parent table name', 'lookup field caption') */
	$lookup_fields = array('carnet' => array('alumnos', 'Carnet'), );
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
	$templateCode = str_replace('<%%UPLOADFILE(descripcion)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(comentarios)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(fecha_pago)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(importe_nio)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(tipo_pago)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', safe_html($urow['id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', html_attr($row['id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode($urow['id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(carnet)%%>', safe_html($urow['carnet']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(carnet)%%>', html_attr($row['carnet']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(carnet)%%>', urlencode($urow['carnet']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(descripcion)%%>', safe_html($urow['descripcion']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(descripcion)%%>', html_attr($row['descripcion']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(descripcion)%%>', urlencode($urow['descripcion']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(comentarios)%%>', safe_html($urow['comentarios']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(comentarios)%%>', html_attr($row['comentarios']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(comentarios)%%>', urlencode($urow['comentarios']), $templateCode);
		$templateCode = str_replace('<%%VALUE(fecha_pago)%%>', @date('d/m/Y', @strtotime(html_attr($row['fecha_pago']))), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(fecha_pago)%%>', urlencode(@date('d/m/Y', @strtotime(html_attr($urow['fecha_pago'])))), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(importe_nio)%%>', safe_html($urow['importe_nio']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(importe_nio)%%>', html_attr($row['importe_nio']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(importe_nio)%%>', urlencode($urow['importe_nio']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(tipo_pago)%%>', safe_html($urow['tipo_pago']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(tipo_pago)%%>', html_attr($row['tipo_pago']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(tipo_pago)%%>', urlencode($urow['tipo_pago']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(carnet)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(carnet)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(descripcion)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(descripcion)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(comentarios)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(comentarios)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(fecha_pago)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(fecha_pago)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(importe_nio)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(importe_nio)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(tipo_pago)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(tipo_pago)%%>', urlencode(''), $templateCode);
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
	$rdata = $jdata = get_defaults('movimientos');
	if($selected_id) {
		$jdata = get_joined_record('movimientos', $selected_id);
		if($jdata === false) $jdata = get_defaults('movimientos');
		$rdata = $row;
	}
	$templateCode .= loadView('movimientos-ajax-cache', array('rdata' => $rdata, 'jdata' => $jdata));

	// hook: movimientos_dv
	if(function_exists('movimientos_dv')) {
		$args=[];
		movimientos_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}