<?php @session_start();
require_once('inc/data.inc');
require_once('inc/authorize.inc');
require_permission(SET_UP_PERMISSION);
// TODO: Wipe out existing data (option)
// TODO: "delete racer", "delete all racers" (as super-powers on checkin page)
?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Import Roster</title>
<link rel="stylesheet" type="text/css" href="css/jquery.mobile-1.4.2.css"/>
<?php require('inc/stylesheet.inc'); ?>
<link rel="stylesheet" type="text/css" href="css/import-roster.css"/>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
// Turn off jQuery Mobile bullshit page loading hijack functionality, which
// tries to perform page loads via ajax, a "service" we don't really want.
// Fortunately, it's possible to turn that stuff off.
$(document).bind("mobileinit", function() {
				   $.extend($.mobile, {
                       ajaxEnabled: false
					 });
				 });
</script>
<script type="text/javascript" src="js/jquery-ui-1.10.4.min.js"></script>
<script type="text/javascript" src="js/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="js/jquery.mobile-1.4.2.min.js"></script>
<script type="text/javascript" src="js/dashboard-ajax.js"></script>
<script type="text/javascript" src="js/checkin.js"></script>
<script type="text/javascript" src="js/jquery.csv.js"></script>
<script type="text/javascript" src="js/import-roster.js"></script>
</head>
<body>
<?php
$banner_title = 'Import Roster';
require('inc/banner.inc');

try {
  $racers = read_single_value("SELECT COUNT(*) FROM RegistrationInfo", array());
} catch (PDOException $p) {
  $racers = -1;
}

?><div class="import_roster">

<div id="top_matter">

<div id="new_ranks">
</div>

<div id="controls">
  <div id="meta"></div>

  <?php if ($racers > 0) { ?>
    <p>There are already <?php echo $racers; ?> racer(s) registered.
    Re-initialize the database schema if you're trying to start fresh.</p>
  <?php } else if ($racers < 0) { ?>
    <p>The RegistrationInfo table could not be read: you should probably finish setting up the database.</p>
  <?php } ?>

  <form method="link" action="database-setup.php">
    <input type="submit" data-enhanced="true" value="Set Up Database"/>
  </form>

  <form method="link">
    <input type="submit" id="start_over_button" data-enhanced="true" class="hidden" value="Start Over"/>
  </form>

  <div id="import_button_div">
    <input class="hidden" type="button" id="import_button" data-enhanced="true" value="Import Roster"/>
  </div>

  <div id="encoding">
    <div id="encoding-guidance">

      <p>The choice of encoding primarily affects the treatment of accented
      or other "special" characters.  Comma-separated value (CSV) files produced
      by Microsoft Excel typically use Windows or Macintosh encoding, depending
      on platform.</p>

      <p>If you have a CSV file that contains only ASCII characters, then it won't
      matter which of these encodings you choose.  Also, trial and error is a
      perfectly acceptable method of figuring out what encoding renders your file
      correctly.</p>

    </div>

    <div class="encoding-div">
      <label for="encoding-select" id="encoding_message">
      Please select encoding (BEFORE selecting file to import):
      </label>
      <br/>    <input type="radio" name="encoding-select" data-enhanced="true" value="utf-8" checked="checked">UTF-8</input>
      <br/>    <input type="radio" name="encoding-select" data-enhanced="true" value="macintosh">Macintosh</input>
      <br/>    <input type="radio" name="encoding-select" data-enhanced="true" value="cp1252">Windows (cp1252)</input>
      <br/>    <input type="radio" name="encoding-select" data-enhanced="true" value="cp437">MS-DOS (cp437)</input>
    </div>
  </div>

  <div id="submit_message">Please select or drag a file to import.</div>

</div>

<div class="file_target">
  <input type="file" id="csv_file" name="csv_file"/>
</div>

<!-- top_matter --></div>

<div class="fields hidden">

<label for="header-row-present">File has a header row?</label>
<input type="checkbox" name="header-row-present" id="header-row-present" data-role="flipswitch" checked="checked"/>

<h3>Drag fields to label the data columns for import.</h3>

<div class="target"> <!-- targets for column labels -->
<table>
<tr>
<td data-home="lastname"><div data-field="lastname" class="field required">Last Name</div></td>
<td data-home="firstname"><div data-field="firstname" class="field required">First Name</div></td>
<td data-home="classname"><div data-field="classname" class="field required" id="den-name"><?php echo group_label(); ?></div></td>
<td data-home="carnumber"><div data-field="carnumber" class="field optional">Car Number</div></td>
<td data-home="carname"><div data-field="carname" class="field optional">Car Name</div></td>
<td data-home="subgroup"><div data-field="subgroup" class="field optional"><?php echo subgroup_label(); ?></div></td>
</tr>
</table>
</div>

</div>


<table id="csv_content">
</table>

</div>

<div class="footer">
Or instead: <a href="import-results.php">Import results exported from another race...</a>
</div>

<?php require_once('inc/ajax-pending.inc'); ?>
</body>
</html>