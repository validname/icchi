<?php

$db_last_error = "";

// open connection to MySQL server
function db_open() {
	global $db_cfg;
	global $db_last_error;

	$db_last_error = "";

	$host = $db_cfg['host'];
	if ($db_cfg['port']) {
		$host .= ':'.$db_cfg['port'];
	}

	$link = @mysql_connect($host, $db_cfg['user'], $db_cfg['pass']);
	if ($link !== false) {
		mysql_select_db($db_cfg['db'], $link);
		if (@mysql_select_db($db_cfg['db'], $link)) {
			$db_cfg['link'] = $link;
			$result = true;
		} else {
			$db_last_error = "There is no database with name '".$db_cfg['db']."', MySQL error was: ".mysql_error();
			$result = false;
		}
		$result |= @mysql_query("SET NAMES utf8");
		return $result;
	} else {
		$db_last_error = "Cannot connect to MySQL server. MySQL error was: ".mysql_error();
		return false;
	}
}

// execute queey on MySQL server
function db_query($query, $insert_mode = false) {
	global $db_cfg;
	global $db_last_error;

	$db_last_error = "";

	if (!(isset($db_cfg['link']) && $db_cfg['link'])) {
		if (!db_open()) {
			return false;
		}
	}
	$link = $db_cfg['link'];
	$result = @mysql_query($query, $link);
	if ($result === false) {
		$query = addslashes($query);
		$db_last_error = "There is error while executing query (".$query."): ".mysql_error();
	} else {
		if ($insert_mode === true ) {
			$result = @mysql_insert_id($link);
		}
	}
	return $result;
}

function db_get_last_error() {
	global $db_last_error;
	return $db_last_error;
}

// check non-empty SELECT results
function db_check_select_result($query_result) {
	if ($query_result == false) {
		return false;
	}
	if (!db_num_rows($query_result)) {
		return false;
	}
	return true;
}

// returns amount of SELECTed rows
function db_num_rows($query_result) {
	if ($query_result) {
		return @mysql_num_rows($query_result);
	} else {
		return 0;
	}
}

// returns associative array with SELCTed row
function db_fetch_assoc_array($query_result) {
	if ($query_result) {
		return @mysql_fetch_assoc($query_result);
	} else {
		return false;
	}
}

// returns numeric array with SELCTed row
function db_fetch_num_array($query_result) {
	if ($query_result) {
		return @mysql_fetch_row($query_result);
	} else {
		return false;
	}
}

// returns specified cell from SELCTed row
function db_get_cell($query_result, $row, $field=0) {
	if ($query_result) {
		return @mysql_result($query_result, $row, $field);
	} else {
		return false;
	}
}

// add slashes before slashes
function db_add_slashes($string) {
	return addslashes(str_replace("\\", "\\\\", $string));
}

// remove slashes before slashes
function db_strip_slashes($string) {
	return stripslashes($string);
}

?>
