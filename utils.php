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

?>

