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
<h1>路線検索</h1>

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
		$link = sql_connect();
		$start= station_position($link, $_POST['start']);
		$goal = station_position($link, $_POST['goal']);
		//echo "<p>".$start[0]." ".$start[1]."</p>";
		//echo "<p>".$goal[0]." ".$goal[1]."</p>";
		$jsstart_lon = json_encode($start[0]);
		$jsstart_lat = json_encode($start[1]);
		$jsgoal_lon = json_encode($goal[0]);
		$jsgoal_lat = json_encode($goal[1]);

		echo "<p>-- search --</p>";
		$path = astar($link, $_POST['start'], $_POST['goal']);
		if(!is_null($path)) {
			//var_dump($path);
			foreach($path as $row) {
				if(!is_null($row[2])) {
					echo "<p> |</p>";
					echo "<p>[".$row[2]."]</p>";
					echo "<p>↓</p>";
				}
				echo "<p>".$row[3]."</p>";
			}
		}
		$jspath = json_encode($path);
		$jsnum = json_encode(count($path));

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
var path = JSON.parse('<?php echo $jspath; ?>');
var num  = JSON.parse('<?php echo $jsnum; ?>');
//document.write("hello world");
//document.write(start_lat);
//document.write(path);
//document.write(num);
//document.write(path[0][3]);

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

//map_line(start_lon, start_lat, goal_lon, goal_lat);
for(i=0; i<num-1; i++){
	map_line(path[i][0], path[i][1], path[i+1][0], path[i+1][1]);
}
</script>


</body>
</html>

