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

<form action="dakoku5.php" method="post">
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
 	echo "hello php";
	// mysql connection
	$link = mysqli_connect('localhost', 'root', 'nitta', 'mydb');
		echo "mysql";
	if (mysqli_connect_errno()) {
		die("データベースに接続できません:" . mysqli_connect_error() . "\n");
	} else {
		echo "データベースの接続に成功しました。\n";
	}

	if (isset($_POST['submit'])) {
  	echo "<p>".$_POST['status']."</p>";
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

	$query = "SELECT * from timestamp ORDER BY date DESC;";
	$data = array();
	if($result = mysqli_query($link, $query)) {
		echo "<p>SELECT に成功しました</p>";
    $i=0;
		//$olddate=date("Y-m-d H:i:s");
		$olddate = new DateTime;
		echo "old: ".$olddate->format('Y-m-d H:i:s');
    foreach($result as $row) {
    	//var_dump($row);
    	echo "<p>".$i.":".$row['date']." ".$row['memo']." ".$row['status']."</p>";
			$date = new DateTime($row['date']);
			//echo $date->format('Y-m-d H:i:s');
		  //echo "oldhoge: ".$olddate->format('Y-m-d H:i:s');
			
			if($i==0){
    	//$date1 = new DateTime($olddate);
    	//$date2 = new DateTime($row['date']);
    	//$interval = $date2->diff($date1);
    	//echo $olddate;
    	//echo $interval->format('%R%H');
    	//echo $interval->format('%R%I');
			////echo " ".var_dump($interval);
			//echo " ".var_dump($date2);
			//echo " ".$date2->format('H');
			//echo " ".$interval->h." ".$interval->i;
			//$foo = 24 - $interval->h;
			//$hoo = 24 - $date2->format('H');
			//echo " ".$foo." ".$hoo;
				$h = $date->format('H');
				$m = $date->format('i');
				$hm = 60*$h+$m;
				echo $h." ".$m." ".$hm;
				//array_push($data, $hm);
				array_push($data, 24*60-$hm);
				//echo "data: ".var_dump($data);
				$olddate = clone $date;
			} else {
				$interval = $date->diff($olddate);
				//echo "\n".$interval->format('%R%H')."\n";
				//echo "\n".$interval->h." ".$interval->i."\n";
				$h = $interval->h;
				$m = $interval->i;
				$hm = 60*$h+$m;
				array_unshift($data, $hm);
				//echo " ".var_dump($data);
				echo $h." ".$m." ".$hm;
				$olddate = clone $date;
			}
    	$i++;
		}
		echo "\n".$date->format('Y-m-d H-i-s');
		echo "\n".$olddate->format('Y-m-d H-i-s');
		$h = $olddate->format('H');
		$m = $olddate->format('i');
		$hm = 60*$h+$m;
		array_unshift($data, $hm);
	}
  mysqli_close($link);
  $hoge = 10;
  $sample = array('abc','def');
  $varjssample = json_encode($sample);
	$jsdata = json_encode($data);
?>

<script>
        //var hoge = Number('<?php echo $hoge; ?>');
        //document.write(hoge+10);
        //var sample = JSON.parse('<?php echo $varjssample; ?>');
        //document.write(sample[0])
				var cdata = JSON.parse('<?php echo $jsdata; ?>');
				document.write(cdata);

  var ctx = document.getElementById("myPieChart");
  var myPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ["A型", "O型", "B型", "AB型"],
      datasets: [{
          backgroundColor: [
              "#BB5179",
              "#FAFF67",
              "#58A27C",
              "#3C00FF"
          ],
          //data: [5.5, 3, 2, 1]
          data: cdata
      }]
    },
    options: {
      title: {
        display: true,
        text: '血液型 割合'
      }
    }
  });
</script>

</body>
</html>

