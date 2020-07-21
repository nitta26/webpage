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

		$radLat1 = deg2rad($station1[1]);
		$radLon1 = deg2rad($station1[0]);
		$radLat2 = deg2rad($station2[1]);
		$radLon2 = deg2rad($station2[0]);

		$r = 6378137.0;
		$averageLat = ($radLat1-$radLat2)/2;
		$averageLon = ($radLon1-$radLon2)/2;
		$dist = $r * 2 * asin(sqrt(pow(sin($averageLat), 2) 
		+ cos($radLat1) * cos($radLat2) * pow(sin($averageLon), 2)));

		return $dist;
	}

	function station_neighbors($link, $station_name) {
		$query = "SELECT s.station_name, s.line_cd
							FROM m_station s, m_join j
							WHERE (station_cd1
							IN (SELECT station_cd FROM m_station WHERE station_name='".$station_name."')
							AND j.station_cd2=s.station_cd)
							OR (station_cd2
							IN (SELECT station_cd from m_station WHERE station_name='".$station_name."')
							AND j.station_cd1=s.station_cd);";
		$result = sql_select($link, $query);
		return $result;
	}

	class node {
		public $sname;
		public $linecd;
		public $fval;
		public $left;
		public $right;
		public $parent;
		public $open;
		
		//function __construct($sname, $linecd) {
		function __construct($sname) {
			$this->sname = $sname;
			//$this->linecd= $linecd;
			$this->linecd= NULL;
			$this->fval = 0;
			$this->left = NULL;// node
			$this->right= NULL;// node
			$this->parent = NULL;// node
			$open = false;
		}
		
		function debug() {
			echo "<p>debug: ".$this->sname." ".$this->linecd." ".$this->fval;
			if(is_null($this->left)) { echo " left is null ";}
			else { echo " left:".$this->left->sname;}
			if(is_null($this->right)) { echo " right is null ";}
			else { echo " right:".$this->right->sname;}
			if($this->open) { echo " open ";}
			else { echo " close";}
			if(is_null($this->parent)) { echo " parent is null ";}
			else { echo " parent:".$this->parent->sname;}
			echo "</p>";
		}
	}

	class binary_tree {
		public $root;
		
		function __construct() {
			$this->root = NULL;
		}
			
		function insert($node) {
			if(is_null($this->root)) {
				$this->root = $node;
			} else {
				$tree_node = $this->root;
				$flag = True;
				while($flag) {
					if($tree_node->fval > $node->fval)
						if(is_null($tree_node->left)) {
							$tree_node->left = $node;
							$flag = False;
						} else {
							$tree_node = $tree_node->left;
						} else {
						if(is_null($tree_node->right)) {
							$tree_node->right = $node;
							$flag = False;
						} else {
							$tree_node = $tree_node->right;
						}
					}
				}
			}
		}

		function successor($node) {
			$pre = $node;
			$n = $node->right;
			while(!is_null($n->left)) {
				$pre = $n;
				$n = $n->left;
			}
			return array($n,$pre);
		}

		// https://qiita.com/maebaru/items/a47c2ef675a76e8816ab
		function del($n, $node, $pre, $right) {
			if(is_null($n)) {
				echo "first";
				return;
			}
			if($node->fval < $n->fval) {
				$this->del($n->left, $node, $n, False);
				return;
			} else if($node->fval > $n->fval) {
				$this->del($n->right, $node, $n, True);
				return;
			}
			else {
				if(is_null($n->left) && is_null($n->right)) {
					if($right) { $pre->right=NULL;}
					else { $pre->left=NULL;}
				} else if(is_null($n->left)) {
					if($right) { $pre->right = $n->right;}
					else { $pre->left = $n->right;}
				} else if(is_null($n->right))  {
					if($right) { $pre->right = $n->left;}
					else { $pre->left = $n->left;}
				} else {
					$succ = $this->successor($n);
					$tmp = $succ[0]->right;
					//if($right) { 
						//echo "right: ";
						//$pre->debug();
						//$succ[0]->debug();
						//$succ[1]->debug();
						if($right) { 
							$pre->right = $succ[0];
						} else {
							$pre->left = $succ[0];
						} 
						//$pre->debug();
						$succ[0]->left = $n->left;
						if($n->right->sname != $succ[0]->sname) { 
							$succ[0]->right = $n->right;
						}
						$succ[1]->left = $n;
						$n->right = $tmp;
						$n->left = NULL;
						//$n->debug();
						$this->del($succ[1], $n, $succ[1], False);
					//} 
				}  
			}
		}

		function del_node($node) {
			$this->del($this->root, $node, $this->root, True);
			$node->right = NULL;
			$node->left = NULL;
			$node->open = False;
		}

		// arg is root node and recurrently search
		function tree_min($node) {
			if(is_null($node->left)) {
				return $node;
			} else {
				return $this->tree_min($node->left);
			}
		}
	}

	function h($link, $s1, $s2) {
		return station_distance($link, $s1, $s2);
	}

	function astar($link, $s, $g) {
		// 1.
		$snode = new node($s);
		// 2.
		$gnode = new node($g);

		// 3.
		$snode->open = True;
		$snode->fval = h($link, $snode->sname, $gnode->sname);
		

		$snode->debug();
		$gnode->debug();
	}

?>

