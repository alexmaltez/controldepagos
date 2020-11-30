<?php

	$currDir = dirname(__FILE__);
	include_once("{$currDir}/lib.php");
	@include_once("{$currDir}/hooks/movimientos.php");
	include_once("{$currDir}/movimientos_dml.php");

	// mm: can the current member access this page?
	$perm = getTablePermissions('movimientos');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = 'movimientos';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`movimientos`.`id`" => "id",
		"IF(    CHAR_LENGTH(`alumnos1`.`carnet`), CONCAT_WS('',   `alumnos1`.`carnet`), '') /* Carnet */" => "carnet",
		"`movimientos`.`descripcion`" => "descripcion",
		"`movimientos`.`comentarios`" => "comentarios",
		"DATE_FORMAT(`movimientos`.`fecha_pago`, '%e/%c/%Y %l:%i%p')" => "fecha_pago",
		"`movimientos`.`importe_nio`" => "importe_nio",
		"`movimientos`.`tipo_pago`" => "tipo_pago",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`movimientos`.`id`',
		2 => '`alumnos1`.`carnet`',
		3 => 3,
		4 => 4,
		5 => '`movimientos`.`fecha_pago`',
		6 => '`movimientos`.`importe_nio`',
		7 => 7,
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`movimientos`.`id`" => "id",
		"IF(    CHAR_LENGTH(`alumnos1`.`carnet`), CONCAT_WS('',   `alumnos1`.`carnet`), '') /* Carnet */" => "carnet",
		"`movimientos`.`descripcion`" => "descripcion",
		"`movimientos`.`comentarios`" => "comentarios",
		"DATE_FORMAT(`movimientos`.`fecha_pago`, '%e/%c/%Y %l:%i%p')" => "fecha_pago",
		"`movimientos`.`importe_nio`" => "importe_nio",
		"`movimientos`.`tipo_pago`" => "tipo_pago",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`movimientos`.`id`" => "ID",
		"IF(    CHAR_LENGTH(`alumnos1`.`carnet`), CONCAT_WS('',   `alumnos1`.`carnet`), '') /* Carnet */" => "Carnet",
		"`movimientos`.`descripcion`" => "Descripcion",
		"`movimientos`.`comentarios`" => "Comentarios",
		"`movimientos`.`fecha_pago`" => "Fecha pago",
		"`movimientos`.`importe_nio`" => "Importe CS",
		"`movimientos`.`tipo_pago`" => "Tipo pago",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`movimientos`.`id`" => "id",
		"IF(    CHAR_LENGTH(`alumnos1`.`carnet`), CONCAT_WS('',   `alumnos1`.`carnet`), '') /* Carnet */" => "carnet",
		"`movimientos`.`descripcion`" => "descripcion",
		"`movimientos`.`comentarios`" => "comentarios",
		"DATE_FORMAT(`movimientos`.`fecha_pago`, '%e/%c/%Y %l:%i%p')" => "fecha_pago",
		"`movimientos`.`importe_nio`" => "importe_nio",
		"`movimientos`.`tipo_pago`" => "tipo_pago",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = ['carnet' => 'Carnet', ];

	$x->QueryFrom = "`movimientos` LEFT JOIN `alumnos` as alumnos1 ON `alumnos1`.`id`=`movimientos`.`carnet` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm['view'] == 0 ? 1 : 0);
	$x->AllowDelete = $perm['delete'];
	$x->AllowMassDelete = true;
	$x->AllowInsert = $perm['insert'];
	$x->AllowUpdate = $perm['edit'];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = (getLoggedAdmin() !== false);
	$x->AllowSavingFilters = (getLoggedAdmin() !== false);
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = (getLoggedAdmin() !== false);
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'movimientos_view.php';
	$x->RedirectAfterInsert = 'movimientos_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Movimientos';
	$x->TableIcon = 'resources/table_icons/table_money.png';
	$x->PrimaryKey = '`movimientos`.`id`';

	$x->ColWidth = [150, 150, 150, 150, 150, 150, ];
	$x->ColCaption = ['Carnet', 'Descripcion', 'Comentarios', 'Fecha pago', 'Importe CS', 'Tipo pago', ];
	$x->ColFieldName = ['carnet', 'descripcion', 'comentarios', 'fecha_pago', 'importe_nio', 'tipo_pago', ];
	$x->ColNumber  = [2, 3, 4, 5, 6, 7, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/movimientos_templateTV.html';
	$x->SelectedTemplate = 'templates/movimientos_templateTVS.html';
	$x->TemplateDV = 'templates/movimientos_templateDV.html';
	$x->TemplateDVP = 'templates/movimientos_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// mm: build the query based on current member's permissions
	$DisplayRecords = $_REQUEST['DisplayRecords'];
	if(!in_array($DisplayRecords, ['user', 'group'])) { $DisplayRecords = 'all'; }
	if($perm['view'] == 1 || ($perm['view'] > 1 && $DisplayRecords == 'user' && !$_REQUEST['NoFilter_x'])) { // view owner only
		$x->QueryFrom .= ', `membership_userrecords`';
		$x->QueryWhere = "WHERE `movimientos`.`id`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='movimientos' AND LCASE(`membership_userrecords`.`memberID`)='" . getLoggedMemberID() . "'";
	} elseif($perm['view'] == 2 || ($perm['view'] > 2 && $DisplayRecords == 'group' && !$_REQUEST['NoFilter_x'])) { // view group only
		$x->QueryFrom .= ', `membership_userrecords`';
		$x->QueryWhere = "WHERE `movimientos`.`id`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='movimientos' AND `membership_userrecords`.`groupID`='" . getLoggedGroupID() . "'";
	} elseif($perm['view'] == 3) { // view all
		// no further action
	} elseif($perm['view'] == 0) { // view none
		$x->QueryFields = ['Not enough permissions' => 'NEP'];
		$x->QueryFrom = '`movimientos`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: movimientos_init
	$render = true;
	if(function_exists('movimientos_init')) {
		$args = [];
		$render = movimientos_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// column sums
	if(strpos($x->HTML, '<!-- tv data below -->')) {
		// if printing multi-selection TV, calculate the sum only for the selected records
		if(isset($_REQUEST['Print_x']) && is_array($_REQUEST['record_selector'])) {
			$QueryWhere = '';
			foreach($_REQUEST['record_selector'] as $id) {   // get selected records
				if($id != '') $QueryWhere .= "'" . makeSafe($id) . "',";
			}
			if($QueryWhere != '') {
				$QueryWhere = 'where `movimientos`.`id` in ('.substr($QueryWhere, 0, -1).')';
			} else { // if no selected records, write the where clause to return an empty result
				$QueryWhere = 'where 1=0';
			}
		} else {
			$QueryWhere = $x->QueryWhere;
		}

		$sumQuery = "SELECT SUM(`movimientos`.`importe_nio`) FROM {$x->QueryFrom} {$QueryWhere}";
		$res = sql($sumQuery, $eo);
		if($row = db_fetch_row($res)) {
			$sumRow = '<tr class="success">';
			if(!isset($_REQUEST['Print_x'])) $sumRow .= '<td class="text-center"><strong>&sum;</strong></td>';
			$sumRow .= '<td class="movimientos-carnet"></td>';
			$sumRow .= '<td class="movimientos-descripcion"></td>';
			$sumRow .= '<td class="movimientos-comentarios"></td>';
			$sumRow .= '<td class="movimientos-fecha_pago"></td>';
			$sumRow .= "<td class=\"movimientos-importe_nio text-right\">{$row[0]}</td>";
			$sumRow .= '<td class="movimientos-tipo_pago"></td>';
			$sumRow .= '</tr>';

			$x->HTML = str_replace('<!-- tv data below -->', '', $x->HTML);
			$x->HTML = str_replace('<!-- tv data above -->', $sumRow, $x->HTML);
		}
	}

	// hook: movimientos_header
	$headerCode = '';
	if(function_exists('movimientos_header')) {
		$args = [];
		$headerCode = movimientos_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once("{$currDir}/header.php"); 
	} else {
		ob_start();
		include_once("{$currDir}/header.php");
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: movimientos_footer
	$footerCode = '';
	if(function_exists('movimientos_footer')) {
		$args = [];
		$footerCode = movimientos_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once("{$currDir}/footer.php"); 
	} else {
		ob_start();
		include_once("{$currDir}/footer.php");
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
