<CTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>自分打刻</title>
<meta name="viewport" content="width=device-width">
<!--<meta http-equiv="refresh" content="10"; URL="time.php">-->
<!--<link rel="stylesheet" href="style.css">-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
</head>
<body>

<h1>自分打刻</h1>
<?php
  date_default_timezone_set('Asia/Tokyo');
  echo "<p>時刻：".date("Y 年 m 月 d 日 H 時 i 分 s 秒")."</p>";
?>

<form action="dakoku2.php" method="post">
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
  	//int_set('display_errors', "On");
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
        if($result = mysqli_query($link, $query)) {
                echo "<p>SELECT に成功しました</p>";
                $i=0;
                $olddate=date("Y-m-d H:i:s");
                foreach($result as $row) {
                        //var_dump($row);
                        echo "<p>".$i.":".$row['date']." ".$row['memo']." ".$row['status']."</p>";
                        $date1 = new DateTime($olddate);
                        $date2 = new DateTime($row['date']);
                        $interval = $date1->diff($date2);
                        echo $olddate;
                        echo $interval->format('%R%H');
                        echo $interval->format('%R%I');
                        $i++;
                }
        }
  mysqli_close($link);
        $hoge = 10;
        $sample = array('abc','def');
        $varjssample = json_encode($sample);
?>

<script>
        //var hoge = Number('<?php echo $hoge; ?>');
        //document.write(hoge+10);
        //var sample = JSON.parse('<?php echo $varjssample; ?>');
        //document.write(sample[0])

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
          data: [3, 3, 2, 1]
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

