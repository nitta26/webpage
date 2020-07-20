<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>路線検索</title>
<meta name="viewport" content="width=device-width">
<!--<meta http-equiv="refresh" content="10"; URL="time.php">-->
<link rel="stylesheet" href="style.css">
<style>
#map-canvas {
	width: 600px ;
	height: 600px ;
}
</style>
</head>
<body>

<form action="ekisearch.php" method="post">
<p>
出発：<input name="start" size="20">
目的地：<input name="goal" size="20">
<input type="submit" name="search" value="検索">
</p>
<div id="map-canvas"></div>

<?php
	require "utils.php";

	if(isset($_POST['search']) && (($_POST['start']=='') || ($_POST['goal']==''))) {
		echo "<p>station is not setted</p>";
	} else {
		echo "<p>search start</p>";

		$link = sql_connect();
		$start= station_position($link, $_POST['start']);
		$goal = station_position($link, $_POST['goal']);
		echo "<p>".$start[0]." ".$start[1]."</p>";
		echo "<p>".$goal[0]." ".$goal[1]."</p>";
		$jsstart_lon = json_encode($start[0]);
		$jsstart_lat = json_encode($start[1]);
		$jsgoal_lon = json_encode($goal[0]);
		$jsgoal_lat = json_encode($goal[1]);

		$dist = station_distance($link, $_POST['start'], $_POST['goal']);
		echo "<p>".$dist."</p>";

		$result = station_neighbors($link, $_POST['start']);
		foreach($result as $row) {
			echo "<p>result: ".$row['station_name']." ".$row['line_cd']."</p>";
		}

		sql_close($link);
	}
?>

<script src="//maps.googleapis.com/maps/api/js?libraries=places&key={key}"></script>
<script src="utils.js"></script>
<script>
var start_lon = JSON.parse('<?php echo $jsstart_lon; ?>');
var start_lat = JSON.parse('<?php echo $jsstart_lat; ?>');
var goal_lon = JSON.parse('<?php echo $jsgoal_lon; ?>');
var goal_lat = JSON.parse('<?php echo $jsgoal_lat; ?>');
//document.write("hello world");
//document.write(start_lat);

var mapDiv = document.getElementById( "map-canvas" ) ;
// Map
var map = new google.maps.Map( mapDiv, {
	center: new google.maps.LatLng( start_lat, start_lon ) ,
	zoom: 11 ,
} ) ;
// Marker
map_plot(start_lat, start_lon);
map_plot(goal_lat, goal_lon);
//map_plot(start_lon, start_lat);
</script>


</body>
</html>

