<?php
	require_once(dirname(__FILE__).'/../connect.php');
	
	if (isset($_GET["saveHistory"])) {
		
		if ($_POST["token"] != "") {
			
			$user = getLoginByToken($_POST["token"]);
			if ($user['id'] > 0) {
				$sql = $pdo->prepare('UPDATE login SET json_historie = ? WHERE BINARY id = ?');
				$sql->execute([$_POST["historie"], $user["id"]]);
			}
			
			echo json_encode([]);
			
		}
		
	} else {
	
		$status['code'] = 'error';
		$status['info'] = '';
		$user = [];
		
		if ($_POST["token"] == "") {
			
			$salt = '7jIs533hQ&';
			$pwhash = sha1(md5($_POST["pw"]) . $salt . sha1($salt . $_POST["pw"]));
			$status['info'] = $pwhash;
			
			$sql = $pdo->prepare('SELECT * FROM login WHERE BINARY benutzername = ? AND passwort = ?');
			$sql->execute([$_POST["nn"], $pwhash]);
			foreach($sql->fetchAll() as $ausgabe) {
				$status['code'] = 'success';
				$status['info'] = '';
				$token = createToken();
				$historie = json_decode($ausgabe["json_historie"], true);
				$historie_lokal = getHilfestellungen(['codes'=>$historie]);
				$user = [
					'id'=>intval($ausgabe["id"]),
					'token'=>$token,
					'idRole'=>intval($ausgabe["id_nutzerrolle"]),
					'vorname'=>$ausgabe["vorname"],
					'nachname'=>$ausgabe["nachname"],
					'name'=>$ausgabe["vorname"].' '.$ausgabe["nachname"],
					'initialien'=>strtoupper(substr($ausgabe["vorname"], 0, 1)).strtoupper(substr($ausgabe["nachname"], 0, 1)),
					'historie'=>$historie,
					'historie_lokal'=>$historie_lokal,
				];
				$sql = $pdo->prepare('INSERT INTO login_sessions (id_login, token) VALUES (?, ?)');
				$sql->execute([$ausgabe["id"], $token]);
			}
		
		} else {
			
			$user = getLoginByToken($_POST["token"]);
			if ($user['id'] > 0) {
				$status['code'] = 'success';
				$status['info'] = '';
				$token = createToken();
				$user['token'] = $token;
				$sql = $pdo->prepare('UPDATE login_sessions SET token = ? WHERE BINARY token = ?');
				$sql->execute([$token, $_POST["token"]]);
			}
			/*$sql = $pdo->prepare('SELECT * FROM login_sessions WHERE BINARY token = ?');
			$sql->execute([$_POST["token"]]);
			foreach($sql->fetchAll() as $ausgabe2) {
				$sql = $pdo->prepare('SELECT * FROM login WHERE BINARY id = ?');
				$sql->execute([$ausgabe2["id_login"]]);
				foreach($sql->fetchAll() as $ausgabe) {
					$status['code'] = 'success';
					$status['info'] = '';
					$token = createToken();
					$user = [
						'id'=>intval($ausgabe["id"]),
						'token'=>$token,
						'idRole'=>intval($ausgabe["id_nutzerrolle"]),
						'vorname'=>$ausgabe["vorname"],
						'nachname'=>$ausgabe["nachname"],
						'name'=>$ausgabe["vorname"].' '.$ausgabe["nachname"],
						'initialien'=>strtoupper(substr($ausgabe["vorname"], 0, 1)).strtoupper(substr($ausgabe["nachname"], 0, 1)),
					];
					$sql = $pdo->prepare('UPDATE login_sessions SET token = ? WHERE BINARY id = ?');
					$sql->execute([$token, $ausgabe2["id"]]);
				}
			}*/
			
		}
		
		
		
		echo json_encode([
			'status'=>$status['code'],
			'statusInfo'=>$status['info'],
			'user'=>$user,
		]);
		
	}
?>