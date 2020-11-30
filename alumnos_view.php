<?php

	$currDir = dirname(__FILE__);
	include_once("{$currDir}/lib.php");
	@include_once("{$currDir}/hooks/alumnos.php");
	include_once("{$currDir}/alumnos_dml.php");

	// mm: can the current member access this page?
	$perm = getTablePermissions('alumnos');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied'], false);
		echo '<script>setTimeout("window.location=\'index.php?signOut=1\'", 2000);</script>';
		exit;
	}

	$x = new DataList;
	$x->TableName = 'alumnos';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`alumnos`.`id`" => "id",
		"`alumnos`.`carnet`" => "carnet",
		"`alumnos`.`nombre_completo`" => "nombre_completo",
		"`alumnos`.`facultad`" => "facultad",
		"`alumnos`.`turno`" => "turno",
		"DATE_FORMAT(`alumnos`.`fecha_matri`, '%e/%c/%Y')" => "fecha_matri",
		"`alumnos`.`numero`" => "numero",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`alumnos`.`id`',
		2 => '`alumnos`.`carnet`',
		3 => 3,
		4 => 4,
		5 => 5,
		6 => '`alumnos`.`fecha_matri`',
		7 => '`alumnos`.`numero`',
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`alumnos`.`id`" => "id",
		"`alumnos`.`carnet`" => "carnet",
		"`alumnos`.`nombre_completo`" => "nombre_completo",
		"`alumnos`.`facultad`" => "facultad",
		"`alumnos`.`turno`" => "turno",
		"DATE_FORMAT(`alumnos`.`fecha_matri`, '%e/%c/%Y')" => "fecha_matri",
		"`alumnos`.`numero`" => "numero",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`alumnos`.`id`" => "ID",
		"`alumnos`.`carnet`" => "N&#250;mero de Carnet",
		"`alumnos`.`nombre_completo`" => "Nombre y Apellido",
		"`alumnos`.`facultad`" => "Facultad",
		"`alumnos`.`turno`" => "Turno",
		"`alumnos`.`fecha_matri`" => "Fecha Matricula",
		"`alumnos`.`numero`" => "Numero",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`alumnos`.`id`" => "id",
		"`alumnos`.`carnet`" => "carnet",
		"`alumnos`.`nombre_completo`" => "nombre_completo",
		"`alumnos`.`facultad`" => "facultad",
		"`alumnos`.`turno`" => "turno",
		"DATE_FORMAT(`alumnos`.`fecha_matri`, '%e/%c/%Y')" => "fecha_matri",
		"`alumnos`.`numero`" => "numero",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = [];

	$x->QueryFrom = "`alumnos` ";
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
	$x->AllowPrinting = (getLoggedAdmin() !== false);
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = (getLoggedAdmin() !== false);
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'alumnos_view.php';
	$x->RedirectAfterInsert = 'alumnos_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Alumnos';
	$x->TableIcon = 'resources/table_icons/client_account_template.png';
	$x->PrimaryKey = '`alumnos`.`id`';

	$x->ColWidth = [150, 150, 150, 150, 150, 150, ];
	$x->ColCaption = ['N&#250;mero de Carnet', 'Nombre y Apellido', 'Facultad', 'Turno', 'Fecha Matricula', 'Numero', ];
	$x->ColFieldName = ['carnet', 'nombre_completo', 'facultad', 'turno', 'fecha_matri', 'numero', ];
	$x->ColNumber  = [2, 3, 4, 5, 6, 7, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/alumnos_templateTV.html';
	$x->SelectedTemplate = 'templates/alumnos_templateTVS.html';
	$x->TemplateDV = 'templates/alumnos_templateDV.html';
	$x->TemplateDVP = 'templates/alumnos_templateDVP.html';

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
		$x->QueryWhere = "WHERE `alumnos`.`id`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='alumnos' AND LCASE(`membership_userrecords`.`memberID`)='" . getLoggedMemberID() . "'";
	} elseif($perm['view'] == 2 || ($perm['view'] > 2 && $DisplayRecords == 'group' && !$_REQUEST['NoFilter_x'])) { // view group only
		$x->QueryFrom .= ', `membership_userrecords`';
		$x->QueryWhere = "WHERE `alumnos`.`id`=`membership_userrecords`.`pkValue` AND `membership_userrecords`.`tableName`='alumnos' AND `membership_userrecords`.`groupID`='" . getLoggedGroupID() . "'";
	} elseif($perm['view'] == 3) { // view all
		// no further action
	} elseif($perm['view'] == 0) { // view none
		$x->QueryFields = ['Not enough permissions' => 'NEP'];
		$x->QueryFrom = '`alumnos`';
		$x->QueryWhere = '';
		$x->DefaultSortField = '';
	}
	// hook: alumnos_init
	$render = true;
	if(function_exists('alumnos_init')) {
		$args = [];
		$render = alumnos_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: alumnos_header
	$headerCode = '';
	if(function_exists('alumnos_header')) {
		$args = [];
		$headerCode = alumnos_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once("{$currDir}/header.php"); 
	} else {
		ob_start();
		include_once("{$currDir}/header.php");
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: alumnos_footer
	$footerCode = '';
	if(function_exists('alumnos_footer')) {
		$args = [];
		$footerCode = alumnos_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once("{$currDir}/footer.php"); 
	} else {
		ob_start();
		include_once("{$currDir}/footer.php");
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
