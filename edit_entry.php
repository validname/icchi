<?php

require_once("lib/constants.php");
require_once("lib/auth.php");
require_once("lib/forms.php");

$id_entry = form_get_value("id", DATATYPE_INT);
$date = form_get_value("date", DATATYPE_TEXT);
$title = form_get_value("title", DATATYPE_TEXT);
$text = form_get_value("text", DATATYPE_TEXT);
$tags = form_get_value("tags", DATATYPE_TEXT);

/*
 * submitted / show_errors	action		data to show
 * 0			0			show form	filled from db or empty form	
 * 0			1			(impossible state)
 * 1			0			redirect to show just edited form			
 * 1			1			show form	posted data						
 */

$show_form = true;
$submitted = form_get_value("submitted", DATATYPE_BOOL);

if ($submitted === true) {
	$show_form = false;
	// check submitted entry
	if ($date===false) {
		show_error("Empty date!");
		$show_form = true;
	}
	if ($title===false) {
		show_error("Empty title!");
		$show_form = true;
	}
	if ($text===false) {
		show_error("Empty text!");
		$show_form = true;
	}
	if (!$show_form) {
		// try to add new entry to db
		if (!$id_entry) {
			$query = "INSERT INTO entries VALUES(0,";
			$query .= "\"".db_add_slashes($date)."\",";
			$query .= "\"".db_add_slashes($title)."\",";
			$query .= "\"".db_add_slashes($text)."\")";
			$db_result = db_query($query, true);
			if ($db_result===false) {
				show_error(db_get_last_error());
				$show_form = true;
			} else {
				$id_entry = $db_result;
			}
		} else {
			$query = "UPDATE entries SET ";
			$query .= "date=\"".db_add_slashes($date)."\",";
			$query .= "title=\"".db_add_slashes($title)."\",";
			$query .= "text=\"".db_add_slashes($text)."\" ";
			$query .= "WHERE id_entry=".$id_entry;
			$db_result = db_query($query, true);
			if ($db_result===false) {
				show_error(db_get_last_error());
				$show_form = true;
			}
		}
		if (!$show_form) {
			$tags_ids = array();
			// break tags and insert them into db
			$tags_array = explode(",", $tags);
			foreach($tags_array as $tmp_idx => $tag ) {
				$tag = trim($tag);
				if( !$tag ) {
					continue;
				}
				// check for tag existense
				$db_result = db_query("SELECT id_tag FROM tags WHERE tag=\"".$tag."\"");
				if ($db_result===false) {
					show_error(db_get_last_error());
					$show_form = true;
					break;
				} else {
					if (!db_num_rows($db_result)) {
						// no tag in db, need to add
						$db_result = db_query("INSERT into tags VALUES(0,\"".$tag."\")", true);
						if ($db_result===false) {
							show_error(db_get_last_error());
							$show_form = true;
							break;
						} else {
							$id_tag = $db_result;
						}
					} else {
						$tmp_array = db_fetch_num_array($db_result);
						$id_tag = $tmp_array[0];
					}
				}
				$tags_ids[] = $id_tag;
				// got id_tag, check current link from tag to entry
				$db_result = db_query("SELECT id_tag FROM tags_in_entries WHERE id_entry=".$id_entry." AND id_tag=".$id_tag);
				if ($db_result===false) {
					show_error(db_get_last_error());
					$show_form = true;
					break;
				} else {
					if (!db_num_rows($db_result)) {
						// no link, need to make one
						$db_result = db_query("INSERT into tags_in_entries VALUES(".$id_entry.",".$id_tag.")", true);
						if ($db_result===false) {
							show_error(db_get_last_error());
							$show_form = true;
							break;
						} else {
							$id_link = $db_result;
						}
					}
				}
			}
		}
		if (!$show_form) {
			// delete old links from tags to entry
			$db_query = "DELETE FROM tags_in_entries WHERE id_entry=".$id_entry;
			if ( count($tags_ids) ) {
				$tmp_tags = implode(",", $tags_ids);
				$db_query .= " AND id_tag NOT IN (".$tmp_tags.")";
			}
			$db_result = db_query($db_query);
			if ($db_result===false) {
				show_error(db_get_last_error());
				$show_form = true;
			}
		}
	}

	if (!$show_form) {
		// form is submitted. Let's show it
		require("view_entry.php");
	}
}

if ($submitted === false || $show_form) {
	// show form
	if ($submitted === false) {
		if ($id_entry !== false) {
			$html_title = "Edit entry";
			// get data from db
			$db_result = db_query("SELECT * FROM entries WHERE id_entry=".$id_entry);
			if ($db_result===false) {
				show_error(db_get_last_error());
				$show_form = false;
			}
			if (!db_num_rows($db_result)) {
				show_error("There is no entry with id=".$id_entry);
				$show_form = false;
			} else {
				$tmp_array = db_fetch_assoc_array($db_result);
				$date = $tmp_array['date'];
				$title = db_strip_slashes($tmp_array['title']);
				$text = db_strip_slashes($tmp_array['text']);

				// get tags
				$tags = "";
				$db_result = db_query("SELECT t.tag FROM tags AS t INNER JOIN tags_in_entries AS te ON te.id_tag=t.id_tag WHERE te.id_entry=".$id_entry);
				if ($db_result===false) {
					show_error(db_get_last_error());
					$show_form = false;
				}
				if (db_num_rows($db_result)) {
					while ( $tmp_array = db_fetch_num_array($db_result) ) {
						if ($tags) {
								$tags .= ", ";
						}
						$tags .= $tmp_array[0];
					}
				}
			}
		} else {
			$html_title = "Add entry";
		}
	}
	// form
	$navbar_menu_title = NAVBAR_MENU_ADD_ENTRY;
	include("header.php");

	if( $show_form ) {
?>

<form action=<?php echo $_SERVER['PHP_SELF'] ?> id=main_form name=main_form method=GET>
	<input type=hidden id=submitted name=submitted value=1>
	<?php if ($id_entry) echo "<input type=hidden name=id value=".$id_entry.">".PHP_EOL; ?>
	<label for=date>Date/time:</label><input id=date name=date value="<?php echo $date; ?>"><br>
	<label for=title>Title:</label><input id=title name=title value="<?php echo $title; ?>"><br>
	<textarea id=text name=text rows=20 cols=80><?php echo $text; ?></textarea><br>
	<label for=tags>tags:</label><input id=tags name=tags value="<?php echo $tags; ?>"><br>
	<input type=button onClick='document.forms.namedItem("main_form").submit();' value="Submit">
</form>

<?php
	}
}
include("footer.php");
?>
