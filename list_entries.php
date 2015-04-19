<?php
require_once("lib/constants.php");
require_once("lib/auth.php");
require_once("lib/forms.php");

$html_title = PROJECT_NAME;
$navbar_menu_title = NAVBAR_MENU_LIST_ENTRIES;

include("header.php");

// build search query
$db_query = "SELECT id_entry, DATE_FORMAT(date, \"".SQL_DATE_FORMAT."\") as date_string, DATE_FORMAT(date, \"".SQL_DATETIME_FORMAT."\") as datetime_string, title, SUBSTRING(text, 1, 512) as text, LENGTH(text) as length FROM entries ORDER by date";

$db_result = db_query($db_query);
if ($db_result===false) {
	show_error(db_get_last_error());
}
?>
	<div class="container">
<?php
if (db_num_rows($db_result)) {
	while ($entry = db_fetch_assoc_array($db_result)) {
		$id_entry = $entry['id_entry'];

		$date = $entry['date_string'];
		$datetime = $entry['datetime_string'];

		$title = db_strip_slashes($entry['title']);

		$text = db_strip_slashes($entry['text']);
		$text_length = $entry['length'];
		if ( strlen($text)!= $text_length ) {
			// entry text is trimmed at right
			$tmp_space_pos = strrpos($text, " ");
			if ( $tmp_space_pos ) {
				// cut text to the last space symbol
				$text = substr($text, 0, $tmp_space_pos);
			}
			$text .= "...";
		}
		// show entry
?>
		<!-- Entry -->
		<div style="border: 1px solid #E3E3E3; border-radius: 4px;">
			<div style="display: table-row">
				<div style="display: table-cell; padding: 3px;">
					<span class="label label-default" title="<?php echo $datetime; ?>"</span><?php echo $date; ?></span>
				</div>
				<div style="display: table-cell; padding: 3px; width: 100%;">
					<a href="view_entry.php?id=<?php echo $id_entry; ?>"><strong><?php echo $title ?></strong></a>
				</div>
				<div style="display: table-cell; padding: 3px; white-space: nowrap;">
					<button type="button" class="btn btn-default btn-xs" aria-label="Edit entry">
						<a href="edit_entry.php?id=<?php echo $id_entry; ?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
					</button>
					<button type="button" class="btn btn-default btn-xs" aria-label="Edit entry">
						<a href="delete_entry.php?id=<?php echo $id_entry; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
					</button>
				</div>
			</div>
			<div style="display: block; padding: 0px 3px;">
				<?php echo $text; ?>
			</div>
<?php
		// show tags
		$db_result2 = db_query("SELECT t.tag FROM tags AS t INNER JOIN tags_in_entries AS te ON te.id_tag=t.id_tag WHERE te.id_entry=".$id_entry." ORDER BY t.tag");
		if ($db_result2===false) {
			show_error(db_get_last_error());
		} elseif (db_num_rows($db_result2)) {
?>
			<div style="display: table-row;">
				<div style="display: table-cell; padding: 3px;">
<?php
			while ($tag_array = db_fetch_num_array($db_result2)) {
				$tag = $tag_array[0];
				echo "<span class=\"label label-info\"><a href=\"list_entries.php?tags[]=".$tag."\">".$tag."</a></span>".PHP_EOL;
			}
?>
				</div>
			</div>
<?php
		}
?>
		</div>
		<!-- /Entry -->
<?php
	}
}
?>
	</div>
<?php
include("footer.php");
?>
