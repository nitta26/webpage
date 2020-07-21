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
		echo "<p>h: ".h($link, $_POST['start'], $_POST['goal'])."</p>";

		$result = station_neighbors($link, $_POST['start']);
		foreach($result as $row) {
			echo "<p>result: ".$row['station_name']." ".$row['line_cd']."</p>";
		}

		$hoge = new node($row['station_name'], $row['line_cd']);
		$hoge->fval = 20;
		$hoge->debug();
		$foo = new node('都庁前');
		$foo->fval = 50;
		$foo->debug();
		$foh = new node('代々木');
		$foh->fval = 10;
		$foh->debug();
		$hog = new node('西新宿');
		$hog->fval = 30;
		$hog->debug();
		$fog = new node('南新宿');
		$fog->fval = 5;
		$ff = new node('高田馬場');
		$ff->fval = 15;
		$hh = new node('千川');
		$hh->fval = 70;
		$kk = new node('日暮里');
		$kk->fval = 60;
		$aa = new node('東京');
		$aa->fval = 80;
		$bb = new node('西日暮里');
		$bb->fval = 65;
		$cc = new node('目白');
		$cc->fval = 12;
		$dd = new node('駒込');
		$dd->fval = 13;

		$tree = new binary_tree();
		echo "<p>".is_null($tree->root)."</p>";
		$tree->insert($hoge);
		$tree->insert($foo);
		$tree->insert($foh);
		$tree->insert($hog);
		$tree->insert($fog);
		$tree->insert($ff);
		$tree->insert($hh);
		$tree->insert($kk);
		$tree->insert($aa);
		$tree->insert($bb);
		$tree->insert($cc);
		$tree->insert($dd);
		echo "root";
		$tree->root->debug();
		echo "left";
		$tree->root->left->debug();
		echo "right";
		$tree->root->right->debug();
		//echo "right left";
		//$tree->root->right->left->debug();
		echo "min";
		$tree->tree_min($tree->root)->debug();
		$tree->tree_min_node()->debug();
		echo "successeor:";
		$succ = $tree->successor($foo);
		$succ[0]->debug();
		$succ[1]->debug();

		echo "find";
		$abc = $tree->find('駒込', 13);
		$abc->debug();
		if(is_null($abc)){
			echo "<p>null</p>";
		} else {
			echo "<p>notnull</p>";
		}

		echo "del:";
		//$tree->root->debug();
		//$tree->del($tree->root, $hog, $tree->root, True);
		//$foo->debug();
		//$tree->root->debug();
		//$hog->debug();
		//$tree->del($tree->root, $hh, $tree->root, True);
		//$foo->debug();
		//$tree->del($tree->root, $ff, $tree->root, True);
		//$tree->del($tree->root, $ff, $tree->root, True);
		//$foh->debug();
		//$tree->del($tree->root, $foo, $tree->root, True);
		$tree->del_node($foo);
		$tree->root->debug();
		$hh->debug();
		$kk->debug();
		$foo->debug();
		$cc->debug();

		echo "del2";
		$tree->del_node($hoge);
		$tree->root->debug();
		$ff->debug();
		$cc->debug();

		echo "<p>-- search --</p>";
		astar($link, $_POST['start'], $_POST['goal']);

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

