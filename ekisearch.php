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

		//$query_start 
		//= "SELECT station_name, lon, lat FROM m_station WHERE station_name like '".$_POST['start']."'limit 1;";
		//$query_goal 
		//= "SELECT station_name, lon, lat FROM m_station WHERE station_name like '".$_POST['goal']."'limit 1;";
		//$result_start = sql_select($link, $query_start);
		//$result_goal  = sql_select($link, $query_goal);
		//if($result_start->num_rows<1 || $result_goal->num_rows<1) {
		//	echo "<p>駅が見つかりませんでした</p>";
		//	return;
		//}
		//$start_row = $result_start->fetch_assoc();
		//$goal_row  = $result_goal->fetch_assoc();
		//$start_lon = $start_row['lon']; 
		//$start_lat = $start_row['lat'];
		//$goal_lon  = $goal_row['lon'];
		//$goal_lat  = $goal_row['lat'];
		//mysqli_free_result($result_start);
		//mysqli_free_result($result_goal);
		//echo "<p>start:".$start_lon." ".$start_lat."</p>";
		//echo "<p>goal :".$goal_lon." ".$goal_lat."</p>";
		//$jsstart_lon = json_encode($start_lon);
		//$jsstart_lat = json_encode($start_lat);
		//$jsgoal_lon = json_encode($goal_lon);
		//$jsgoal_lat = json_encode($goal_lat);
		$jsstart_lon = json_encode($start[0]);
		$jsstart_lat = json_encode($start[1]);
		$jsgoal_lon = json_encode($goal[0]);
		$jsgoal_lat = json_encode($goal[1]);

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

