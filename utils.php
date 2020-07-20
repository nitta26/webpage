<?php
	function sql_connect() {
		// mysql connection
		$link = mysqli_connect('localhost', 'root', 'nitta', 'eki');
		if (mysqli_connect_errno()) {
			die("データベースに接続できません:" . mysqli_connect_error() . "\n");
		} else {
			echo "<p>データベースの接続に成功しました。</p>";
		}
		return $link;
	}

	function sql_close($link) {
		mysqli_close($link);
		echo "<p>データベースの接続を終了しました。</p>";
	}

	function sql_select($link, $query) {
		if($result = mysqli_query($link, $query)) {
			echo "<p>SELECTに成功しました</p>";
		} else {
			echo "<p>SELECTに失敗しました</p>";
		}
		return $result;
	}

	function station_position($link, $station_name) {
		$query = "SELECT lon, lat FROM m_station WHERE station_name='".$station_name."';";
		$result = sql_select($link, $query);
		if($result->num_rows<1) {
			echo "<p>駅が見つかりませんでした</p>";
			return array(0,0);
		}
		$row = $result->fetch_assoc();
		$lon = $row['lon'];
		$lat = $row['lat'];
		return array($lon, $lat);
	}	

	function station_distance($link, $station_name1, $station_name2) {
		// https://qiita.com/chiyoyo/items/b10bd3864f3ce5c56291
		$station1 = station_position($link, $station_name1);
		$station2 = station_position($link, $station_name2);
		//echo "<p>".$station1[0]." ".$station1[1]."</p>";
		//echo "<p>".$station2[0]." ".$station2[1]."</p>";
		//echo deg2rad(180);
		$radLat1 = deg2rad($station1[1]);
		$radLon1 = deg2rad($station1[0]);
		$radLat2 = deg2rad($station2[1]);
		$radLon2 = deg2rad($station2[0]);
		$r = 6378137.0;
		$averageLat = ($radLat1-$radLat2)/2;
		$averageLon = ($radLon1-$radLon2)/2;
		$dist = $r * 2 * asin(sqrt(pow(sin($averageLat), 2) 
		+ cos($radLat1) * cos($radLat2) * pow(sin($averageLon), 2)));
		//echo $dist;
		return $dist;
	}

?>

