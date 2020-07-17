<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>自分打刻</title>
<meta name="viewport" content="width=device-width">
<!--<meta http-equiv="refresh" content="10"; URL="time.php">-->
<link rel="stylesheet" href="style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
</head>
<body>

<h1>自分打刻</h1>
<?php
  date_default_timezone_set('Asia/Tokyo');
  echo "<p>時刻：".date("Y 年 m 月 d 日 H 時 i 分 s 秒")."</p>";
?>

<form action="dakoku6.php" method="post">
<p>メモ: <input name="memo" size="20">
<select name="status">
	<option value=1>作業</option>
	<option value=0>休憩</option>
	<option value=2>その他</option>
</select>
<input type="submit" name='submit' value="登録"></p>
</form>

<h1>円グラフ</h1>
<canvas id="myPieChart"></canvas>

<?php
	// mysql connection
	$link = mysqli_connect('localhost', 'root', 'nitta', 'mydb');
	if (mysqli_connect_errno()) {
		die("データベースに接続できません:" . mysqli_connect_error() . "\n");
	} else {
		echo "データベースの接続に成功しました。\n";
	}

	if (isset($_POST['submit'])) {
    if (isset($_POST['memo']) && $_POST['memo'] != '') {
     	$x = htmlspecialchars($_POST['memo']);
      $status = $_POST['status'];
      echo "<p>メモの内容は，「".$x."」 です。</p>";
      date_default_timezone_set('Asia/Tokyo');
      $query = "INSERT INTO timestamp VALUES ('".date("Y-m-d H:i:s")."','".$x."',".$status.");";
      if (mysqli_query($link, $query)) {
      	echo "<p>INSERT に成功しました。</p>";
      }
		}
	}

	//$query = "SELECT * from timestamp ORDER BY date DESC;";
	$query = "SELECT * from timestamp WHERE date LIKE CONCAT(CURDATE(), '%') ORDER BY date DESC;";
	$data = array();
	$color= array();
	$color_list=array("#58A27C","#3C00FF","#FAFF67","#BB5179");
	$memo = array();
	if($result = mysqli_query($link, $query)) {
		echo "<p>SELECT に成功しました</p>";
    $i=0;
		$olddate = new DateTime;
    foreach($result as $row) {
    	//echo "<p>".$i.":".$row['date']." ".$row['memo']." ".$row['status']."</p>";
			$date = new DateTime($row['date']);
			
			if($i==0){
    		$h = $date->format('H');
				$m = $date->format('i');
				$hm = 60*$h+$m;
				array_push($data, 24*60-$hm);
				$olddate = clone $date;
			} else {
				$interval = $date->diff($olddate);
				$h = $interval->h;
				$m = $interval->i;
				$hm = 60*$h+$m;
				array_unshift($data, $hm);
				array_unshift($memo, $row['memo']);
				$olddate = clone $date;
				if($row['status']==1){
					echo "work";
					array_unshift($color, $color_list[0]);
				} else if($row['status']==0) {
					echo "rest";
					array_unshift($color, $color_list[1]);
				} else {
					echo "other";
					array_unshift($color, $color_list[2]);
				}
			}
   		echo "<p>".$i.":".$row['date']." ".$row['memo']." ".$row['status']."</p>";
    	$i++;
		}
		$h = $olddate->format('H');
		$m = $olddate->format('i');
		$hm = 60*$h+$m;
		array_unshift($data, $hm);
		array_unshift($color, $color_list[3]);
		array_unshift($memo, "sleep");
	}
  mysqli_close($link);
  $hoge = 10;
  $sample = array('abc','def');
  $varjssample = json_encode($sample);
	$jsdata = json_encode($data);
	$jscolor= json_encode($color);
	$jsmemo= json_encode($memo);
?>

<script>
	var cdata = JSON.parse('<?php echo $jsdata; ?>');
	var color = JSON.parse('<?php echo $jscolor; ?>');
	var memo = JSON.parse('<?php echo $jsmemo; ?>');

  var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
      //labels: ["A型", "O型", "B型", "AB型"],
			labels: memo,
      datasets: [{
          //backgroundColor: [
          //    "#BB5179",
          //    "#FAFF67",
          //    "#58A27C",
          //    "#3C00FF"
          //],
					backgroundColor: color,
          //data: [5.5, 3, 2, 1]
          data: cdata
      }]
    },
    options: {
    title: {
    display: true,
    text: '時間割合（24h）'
      }
    }
  });
</script>

</body>
</html>

