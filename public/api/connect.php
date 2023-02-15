<?php
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json; charset=utf-8');
	
	$timezone = 'Europe/Berlin';
	date_default_timezone_set($timezone);
	
	$pdo = new PDO('mysql:host=localhost;dbname=iu-bachelorarbeit', 'iu-bachelorarbeit', 'Fiyh1*796', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

	function br2nl($string){
		return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}
	
	function createToken() {
		$salt = sha1(rand(0,999999));
		$token = sha1(md5(rand(0,999999)) . $salt . sha1($salt . rand(0,999999)));
		return $token;
	}
	
	function getLoginByToken($token) {
		global $pdo;
		$user = [
			'id'=>0,
			'token'=>'',
			'idRole'=>0,
			'vorname'=>'',
			'nachname'=>'',
			'name'=>'',
			'initialien'=>'',
			'historie'=>[],
			'historie_lokal'=>[],
		];
		$sql = $pdo->prepare('SELECT * FROM login_sessions WHERE BINARY token = ?');
		$sql->execute([$token]);
		foreach($sql->fetchAll() as $ausgabe2) {
			$sql = $pdo->prepare('SELECT * FROM login WHERE BINARY id = ?');
			$sql->execute([$ausgabe2["id_login"]]);
			foreach($sql->fetchAll() as $ausgabe) {
				$historie = json_decode($ausgabe["json_historie"], true);
				$historie_lokal = getHilfestellungen(['codes'=>$historie]);
				$user = [
					'id'=>intval($ausgabe["id"]),
					'token'=>createToken(),
					'idRole'=>intval($ausgabe["id_nutzerrolle"]),
					'vorname'=>$ausgabe["vorname"],
					'nachname'=>$ausgabe["nachname"],
					'name'=>$ausgabe["vorname"].' '.$ausgabe["nachname"],
					'initialien'=>strtoupper(substr($ausgabe["vorname"], 0, 1)).strtoupper(substr($ausgabe["nachname"], 0, 1)),
					'historie'=>$historie,
					'historie_lokal'=>$historie_lokal,
				];
			}
		}
		return $user;
	}
	
	
	
	function createCode() {
		global $pdo;
		
		$vorhanden = true;
		while($vorhanden) {
			$vorhanden = false;
			/*
			//$zeichen = str_split('abcdefghijkmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.'0123456789');
			$zeichen = str_split('ABCDEFGHJKLMNOPQRSTUVWXYZ'.'0123456789', 1);
			shuffle($zeichen);
			$code = [];
			for($i = 1;$i<=1;$i++){
				$c = '';
				foreach (array_rand($zeichen, 8) as $k) {
					$c .= $zeichen[$k];
				}
				array_push($code, $c);
			}
			$code = implode('-', $code);
			*/
			$code = rand(10000, 9999999);
			$sql = $pdo->prepare('SELECT code FROM hilfestellungen WHERE BINARY code = ?');
			$sql->execute([$code]);
			foreach($sql->fetchAll() as $ausgabe) {
				$vorhanden = true;
			}
		}
		return $code;
	}
	
	function getHilfestellungen($filter) {
		global $pdo;
		$sqlFilter = '';
		$sqlData = [];
		if (isset($filter["code"])) {
			$sqlFilter = 'WHERE BINARY code = ? ORDER BY id DESC LIMIT 1';
			$sqlData = [$filter["code"]];
		}
		if (isset($filter["codes"])) {
			$sqlFilter = 'WHERE BINARY code IN ("'.implode('", "', $filter["codes"]).'")';
			$sqlData = [];
		}
		if (isset($filter["allLocals"])) {
			$sqlFilter = 'WHERE BINARY offline = "2"';
			$sqlData = [];
		}
		$eintraege = [];
		$sql = $pdo->prepare('SELECT * FROM hilfestellungen '.$sqlFilter.'');
		$sql->execute($sqlData);
		foreach($sql->fetchAll() as $ausgabe) {
			$aufrufe = 0;
			$sql = $pdo->prepare('SELECT * FROM hilfestellungen_aufrufe WHERE BINARY id_hilfestellung = ?');
			$sql->execute([$ausgabe["id"]]);
			foreach($sql->fetchAll() as $ausgabe2) {
				if (intval($ausgabe2["id_login"]) > 0) {
					$aufrufe += 1;
				} else {
					$aufrufe += intval($ausgabe2["anzahl"]);
				}
			}
			$assets = [];
			$sql = $pdo->prepare('SELECT * FROM hilfestellungen_assets WHERE BINARY id_hilfestellung = ? ORDER BY name ASC');
			$sql->execute([$ausgabe["id"]]);
			foreach($sql->fetchAll() as $ausgabe2) {
				array_push($assets, [
					'id'=>$ausgabe2["id"],
					'name'=>$ausgabe2["name"],
					'type'=>$ausgabe2["type"],
				]);
			}
			array_push($eintraege, [
				'id'=>$ausgabe['id'],
				'code'=>$ausgabe['code'],
				'assets'=>$assets,
				'title'=>$ausgabe['title'],
				'html'=>nl2br($ausgabe['html']),
				'html_short'=>nl2br($ausgabe['html_short']),
				'created'=>$ausgabe['date_create'],
				'created_de'=>date("d.m.Y H:i", strtotime($ausgabe['date_create'])),
				'lastEdit'=>$ausgabe['date_lastEdit'],
				'lastEdit_de'=>date("d.m.Y H:i", strtotime($ausgabe['date_lastEdit'])),
				'offline'=>intval($ausgabe['offline']),
				'fetched'=>date("Y-m-d H:i"),
				'fetched_de'=>date("d.m.Y H:i"),
				'aufrufe'=>$aufrufe,
			]);
		}
		return $eintraege;
	}
	
	function createPath($path) {
		if (is_dir($path)) 
			return true;
		$prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
		$return = createPath($prev_path);
		return ($return && is_writable($prev_path)) ? mkdir($path) : false;
	}
	
	function resize_image($file_name, $w, $h, $crop=FALSE) {
		$maxDim = 800;
		list($width, $height, $type, $attr) = getimagesize( $file_name );
		if ( $width > $maxDim || $height > $maxDim ) {
			$target_filename = $file_name;
			$ratio = $width/$height;
			if( $ratio > 1) {
				$new_width = $maxDim;
				$new_height = $maxDim/$ratio;
			} else {
				$new_width = $maxDim*$ratio;
				$new_height = $maxDim;
			}
			$src = imagecreatefromstring( file_get_contents( $file_name ) );
			$dst = imagecreatetruecolor( $new_width, $new_height );
			imagecopyresampled( $dst, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
			imagedestroy( $src );
			imagejpeg( $dst, $target_filename ); // adjust format as needed
			imagedestroy( $dst );
		}
	}
?>