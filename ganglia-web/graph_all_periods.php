<!DOCTYPE html>
<html>
<head>
<title>Ganglia: Metric <?php if (isset($_GET['g'])) echo $_GET['g']; else echo $_GET['m']; ?></title>
<style>
.img_view {
  float: left;
  margin: 0 0 10px 10px;
}
</style>

<?php
$query_string = "";

// build a query string but drop r and z since those designate time window and size. Also if the 
// get arguments are an array rebuild them. For example with hreg (host regex)
$ignore_keys_list = array("r", "z", "st", "cs", "ce", "hc");

foreach ($_GET as $key => $value) {
  if ( ! in_array($key, $ignore_keys_list) && ! is_array($value))
    $query_string_array[] = "$key=$value";

  // $_GET argument is an array. Rebuild it to pass it on
  if ( is_array($value) ) {
    foreach ( $value as $index => $value2 )
      $query_string_array[] = $key . "[]=" . $value2;

  }

}

// If we are in the mobile mode set the proper graph sizes
if ( isset($_GET['mobile'])) {
  $largesize = "mobile";
  $xlargesize = "mobile";
} else {
  $largesize = "large";
  $xlargesize = "xlarge";  
}

// Join all the query_string arguments
$query_string = "&" . join("&", $query_string_array);

if (isset($_GET['h']))
  $description = $_GET['h'];
else if (isset($_GET['c']))
  $description = $_GET['c'];
else if (isset($_GET['aggregate']) )
  $description = "Aggregate graph";
else
  $description = "Unknown";

if ( isset($_GET['flot'])) {
?>
<style>
.flotgraph {
  height: 300px;
  width:  600px;
}
</style>
<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/excanvas.compiled.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.flot.js"></script>

<script type="text/javascript">
  var default_time = 'hour';
  var metric = "<?php if (isset($_GET['g'])) echo $_GET['g']; else echo $_GET['m']; ?>";
  var base_url = "<?php print 'graph.php?' . $_GET['m'] . $query_string ?>" + "&r=" + default_time;
</script>
<script type="text/javascript" src="js/create-flot-graphs.js"></script>
<?php
}
?>
</head>
<body>
<?php
// Skip printing the 
if ( ! isset($_GET['aggregate'] )  ) {
?>
<b>Host/Cluster/Aggregate: </b><?php print $description ?>&nbsp;<b>Metric/Graph: </b><?php if (isset($_GET['g'])) echo $_GET['g']; else echo $_GET['m']; ?><br />
<?php
}

include_once "./eval_conf.php";

foreach ( $conf['time_ranges'] as $key => $value ) {
    print '<div class="img_view">' .
    '<a href="./graph.php?r=' . $key . $query_string .'&csv=1"><img alt="Export to CSV" height=16 width=16 src="img/csv.png"></a> ' .
    '<a href="./graph.php?r=' . $key . $query_string .'&json=1"><img alt="Export to JSON" height=16 width=16 src="img/js.png"></a>' . '<br />';
  if ( isset($_GET['flot'])) {
    print '<div id="placeholder_' . $key . '" class="flotgraph img_view"></div>';
  } else {
    print '<a href="./graph.php?r=' . $key . '&z=' . $xlargesize . $query_string . '"><img alt="Last ' . $key . '" src="graph.php?r=' . $key . '&z=' . $largesize . $query_string . '"></a>';
  }
	print '</div>';
}
// The div below needs to be added to clear float left since in aggregate view things
// will start looking goofy
?>
<div style="clear: left"></div>
</body>
</html>
