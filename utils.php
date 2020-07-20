<?php
	function sql_connect() {
		// mysql connection
		$link = mysqli_connect('localhost', 'root', 'nitta', 'eki');
		if (mysqli_connect_errno()) {
			die("データベースに接続できません:" . mysqli_connect_error() . "\n");
		} else {
			echo "データベースの接続に成功しました。\n";
		}
		return $link;
	}

	function sql_select($link, $query) {
		if($result = mysqli_query($link, $query)) {
			echo "SELECTに成功しました\n";
		} else {
			echo "SELECTに失敗しました。\n";
		}
		return $result;
	}
?>

