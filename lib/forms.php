<?php

require_once("lib/db.php");

define("DATATYPE_INT",	1);
define("DATATYPE_FLOAT",	2);
define("DATATYPE_TEXT",	3);
define("DATATYPE_BOOL",	4);

function form_get_value($name, $type) {
	if (!isset($_REQUEST[$name])) {
		return false;
	}
	$value = trim($_REQUEST[$name]);
	if ($value==="") {
			return false;
	}
	switch ($type) {
		case DATATYPE_INT:		$value = intval($value);
								break;
		case DATATYPE_FLOAT:	$value = floatval($value);
								break;
		case DATATYPE_TEXT:	$value = strval($value);
								break;
		case DATATYPE_BOOL:	$value = strtolower($value);
								if ($value=="on") {
									$value = true;
								} elseif ($value=="off") {
									$value = false;
								} else {
									$value = boolval($value);
								}
								break;
	}
	return $value;
}

function show_error($message) {
	echo "<div class=\"alert alert-danger\" role=\"alert\">".$message."</div>".PHP_EOL;
}

?>
