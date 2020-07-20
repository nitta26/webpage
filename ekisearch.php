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
	// mysql connection
	//$link = mysqli_connect('localhost', 'root', 'nitta', 'eki');
	//if (mysqli_connect_errno()) {
	//	die("データベースに接続できません:" . mysqli_connect_error() . "\n");
	//} else {
	//	echo "データベースの接続に成功しました。\n";
	//}
	require "utils.php";
	$link = sql_connect();
	$query = "SELECT station_cd, station_name FROM m_station WHERE station_name like '%新宿%';";
	$result = sql_select($link, $query);
	foreach($result as $row) {
		echo "<p>".$row['station_cd']." ".$row['station_name']."</p>";
	}	
?>

<script src="//maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCGLc_VSFqDmSyR0DWVcdq5aAz-sL_XCSM"></script>
<script src="utils.js"></script>
<script>
var mapDiv = document.getElementById( "map-canvas" ) ;
// Map
var map = new google.maps.Map( mapDiv, {
	center: new google.maps.LatLng( 35.71, 139.8107 ) ,
	zoom: 11 ,
} ) ;
// Marker
map_plot(35.71, 139.8107);
</script>


</body>
</html>

