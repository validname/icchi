<?php

if (!isset($html_title)) {
	$html_title = "";
}
if (!isset($navbar_menu_title)) {
	$navbar_menu_title = NAVBAR_MENU_DEFAULT;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="favicon.ico">

    <title><?php echo $html_title; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Some custom changes -->
    <link href="css/custom.css" rel="stylesheet">
  </head>

  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="index.php"><?php echo PROJECT_NAME; ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?php if ($navbar_menu_title == NAVBAR_MENU_ADD_ENTRY) { echo " class=\"active\"";} ?>><a href="edit_entry.php">Add entry</a></li>
            <li<?php if ($navbar_menu_title == NAVBAR_MENU_LIST_ENTRIES) { echo " class=\"active\"";} ?>><a href="list_entries.php">Entries</a></li>
            <li<?php if ($navbar_menu_title == NAVBAR_MENU_TAGS) { echo " class=\"active\"";} ?>><a href="list_tags.php">Tags</a></li>
          </ul>
          <form class="navbar-form navbar-left" role="form">
            <div class="form-group">
              <input type="text" placeholder="" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Search</button>
          </form>
          <ul class="nav navbar-nav navbar-right">
            <li<?php if ($navbar_menu_title == NAVBAR_MENU_OPTIONS) { echo " class=\"active\"";} ?>><a href="options.php">Options</a></li>
            <li><a href="logout.php">Exit</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- / Fixed navbar -->
