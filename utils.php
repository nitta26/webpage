<?php
	function sql_connect() {
		// mysql connection
		$link = mysqli_connect('localhost', 'root', 'nitta', 'eki');
		mysqli_set_charset($link, 'utf8');
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
			//echo "<p>SELECTに成功しました</p>";
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
			//return array(0,0);
			return NULL;
		}
		$row = $result->fetch_assoc();
		$lon = $row['lon'];
		$lat = $row['lat'];
		return array($lon, $lat);
	}	

	function line_name($link, $line_cd) {
		$query = "SELECT line_name FROM m_line WHERE line_cd=".$line_cd.";";
		$result = sql_select($link, $query);
		if($result->num_rows<1) {
			echo "<p>見つかりませんでした</p>";
			return NULL;
		}
		$row = $result->fetch_assoc();
		return $row['line_name'];
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
		public $close;
		
		//function __construct($sname, $linecd) {
		function __construct($sname) {
			$this->sname = $sname;
			$this->linecd= NULL;
			$this->fval = INF;
			$this->left = NULL;// node
			$this->right= NULL;// node
			$this->parent = NULL;// node
			$open = false;
			$close = false;
		}
		
		function debug() {
			echo "<p>debug: ".$this->sname." ".$this->linecd." ".$this->fval;
			if(is_null($this->left)) { echo " left is null ";}
			else { echo " left:".$this->left->sname;}
			if(is_null($this->right)) { echo " right is null ";}
			else { echo " right:".$this->right->sname;}
			if($this->open) { echo " open:ture";}
			else { echo " open:false";}
			if($this->close) { echo " close:true";}
			else { echo " close:false";}
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
					if($tree_node->fval >= $node->fval)
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
					if($right) { 
						$pre->right = $succ[0];
					} else {
						$pre->left = $succ[0];
					} 
					$succ[0]->left = $n->left;
					if($n->right->sname != $succ[0]->sname) { 
						$succ[0]->right = $n->right;
					}
					$succ[1]->left = $n;
					$n->right = $tmp;
					$n->left = NULL;
					$this->del($succ[1], $n, $succ[1], True);
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
		
		function tree_min_node() {
			return $this->tree_min($this->root);
		}
		
		function find($s, $val) {
			$n = $this->root;
			$i = 0;
			while($i < 500) {
				if(is_null($n)) {
					echo "NULL";
					return NULL;
				} else if($n->fval == $val && strcmp($n->sname,$s)==0) {
					return $n;
				} else if($n->fval < $val) {
					$n = $n->right;
				} else if($n->fval >= $val) {
					$n = $n->left;
				}
				$i = $i+1;
			}
		}
	}

	function h($link, $s1, $s2) {
		return station_distance($link, $s1, $s2);
	}

	function astar($link, $s, $g) {
		$open = array();
		$close = array();

		// 1.
		$snode = new node($s);
		// 2.
		$gnode = new node($g);

		// 3.
		$snode->open = True;
		$snode->fval = h($link, $snode->sname, $gnode->sname);
		$tree = new binary_tree();
		$root = new node('root');
		$root->fval = INF;
		$tree->insert($root);
		$tree->insert($snode); 
		$open = $open+array($snode->sname => $snode->fval);

		$i = 0;
		while($i < 500) {
		// 4.
		if(is_null($tree->root)) {
			echo "<p>tree is empty</p>";
		}
		else {
			//echo "<p>go to step 5</p>";
		}

		// 5.	
		$n = $tree->tree_min_node();

		// 6.
		if($n->sname == $gnode->sname) {
			echo "<p>search finish</p>";
			echo "<p>".$i."</p>";
			$path = array();
			$p = station_position($link, $n->sname);
			$l = line_name($link, $n->linecd);
			array_push($p, $l, $n->sname);
			//array_push($path, $p);
			array_unshift($path, $p);

			//$n->debug();
			//echo "<p>".line_name($link, $n->linecd)."</p>";
			$r = $n->parent;

			while(!is_null($r->parent)){
				$p = station_position($link, $r->sname);
				$l = line_name($link, $r->linecd);
				array_push($p, $l, $r->sname);
				//array_push($path, $p);
				array_unshift($path, $p);

				//$r->debug();
				//echo "<p>".line_name($link, $r->linecd)."</p>";
				$r = $r->parent;
			}
			$p = station_position($link, $r->sname);
			$l = NULL;
			array_push($p, $l, $r->sname);
			//array_push($path, $p);
			array_unshift($path, $p);
			//$r->debug();	

			return $path;
		} else {
			//echo "<p>continue search</p>";
			$n->close = True;
			$close = $close + array($n->sname => $n->fval);
			$n->open = False;
			unset($open[$n->sname]);
			$tree->del_node($n);
			
			// 7
			$result = station_neighbors($link, $n->sname);
			foreach($result as $row) {
				// 7.1
				$res = array_key_exists($row['station_name'], $open);
				if($res) {
					$val = $open[$row['station_name']];
					$m = $tree->find($row['station_name'], $val);
				} else {
					$m = new node($row['station_name']);
					$m->linecd = $row['line_cd'];
				}
				$gn = $n->fval - h($link, $n->sname, $gnode->sname);
				$cost = station_distance($link, $m->sname, $n->sname);
				$fd = $gn + $cost + h($link, $m->sname, $gnode->sname);

				// 7.2
				if(!$m->open && !$m->close) {
					$m->fval = $fd;
					$m->parent = $n;
					$m->open = True;
					$tree->insert($m);
					$open = $open+array($m->sname => $m->fval);
				} else if($m->open && $fd < $m->fval) {
					$tree->del_node($m);
					unset($open[$m->sname]);
					$m->fval = $fd;
					$m->parent = $n;
					$m->open = True;
					$tree->insert($m);
					$open = $open+array($m->sname => $m->fval);
				} else if($m->close && $fd < $m->fval) {
					$m->fval = $fd;
					$m->close = false;
					unset($close[$m->sname]);
					$m->open = true;
					$tree->insert($m);
					$open = $open + array($m->sname => $m->fval);
				}
			}
		}
		$i = $i+1;
		}
		echo "<p>".$i."</p>";
		return NULL;
	}

?>

