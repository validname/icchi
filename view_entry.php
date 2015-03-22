<?php

require_once("lib/constants.php");
require_once("lib/auth.php");
require_once("lib/forms.php");

$html_title = "View entry";
include("header.php");

if (!isset($id_entry)) {	// yes, it may already exists if we included this file from another
	$id_entry = form_get_value("id", DATATYPE_INT);
}

if ($id_entry===false) {
	show_error("Empty id of entry");
} else {
	// get data from db
	$db_result = db_query("SELECT * FROM entries WHERE id_entry=".$id_entry);
	if ($db_result===false) {
		show_error(db_get_last_error());
		$id_entry = false;
	}
	if (!db_num_rows($db_result)) {
		show_error("There is no entry with id=".$id_entry);
		$id_entry = false;
	} else {
		$tmp_array = db_fetch_assoc_array($db_result);
		$date = $tmp_array['date'];
		$title = db_strip_slashes($tmp_array['title']);
		$text = db_strip_slashes($tmp_array['text']);

		// get tags
		$tags = array();
		$db_result = db_query("SELECT t.tag FROM tags AS t INNER JOIN tags_in_entries AS te ON te.id_tag=t.id_tag WHERE te.id_entry=".$id_entry." ORDER BY t.tag");
		if ($db_result===false) {
			show_error(db_get_last_error());
		} else {
			while ( $tmp_array = db_fetch_num_array($db_result) ) {
				$tags[] = $tmp_array[0];
			}
		}
	}
}

if ($id_entry!==false) {
?>

	<div class="container">
		<!-- Entry -->
		<div style="border: 1px solid #E3E3E3; border-radius: 4px;">
			<div style="display: table-row">
				<div style="display: table-cell; padding: 3px;">
					<span class="label label-default"><?php echo $date; ?></span>
				</div>
				<div style="display: table-cell; padding: 3px; width: 100%;"><?php echo $title; ?></div>
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
			<div style="display: table-row;">
				<div style="display: table-cell; padding: 3px;">
					<?php foreach ($tags as $tmp_idx => $tag) {
						echo "<span class=\"label label-info\"><a href=\"list_entries.php?tags[]=".$tag."\">".$tag."</a></span>".PHP_EOL;
					} ?>
				</div>
			</div>
		</div>
		<!-- /Entry -->
	</div>

<?php
}
include("footer.php");
?>
