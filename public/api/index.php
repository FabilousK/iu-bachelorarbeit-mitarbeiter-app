<?php
	require_once(dirname(__FILE__).'/connect.php');
	
	if (isset($_GET["getFromId"])) {
		$status['code'] = 'error';
		$status['info'] = 'Kein Eintrag gefunden';
		$eintrag = [];
		$search = getHilfestellungen(['code'=>$_POST["code"]]);
		if (count($search) > 0) {
			$status['code'] = 'success';
			$status['info'] = '';
			$eintrag = $search[0];
			$tag = date("Y-m-d");
			if (!isset($_POST["id_login"])) {
				$_POST["id_login"] = 0;
			}
			$vorhanden = false;
			$sql = $pdo->prepare('SELECT * FROM hilfestellungen_aufrufe WHERE BINARY id_hilfestellung = ? AND id_login = ? AND date_tag = ?');
			$sql->execute([$eintrag["id"], $_POST["id_login"], $tag]);
			foreach($sql->fetchAll() as $ausgabe) {
				$vorhanden = true;
				$sql = $pdo->prepare('UPDATE hilfestellungen_aufrufe SET anzahl = anzahl + 1 WHERE BINARY id = ?');
				$sql->execute([$ausgabe["id"]]);
			}
			if (!$vorhanden) {
				$sql = $pdo->prepare('INSERT INTO hilfestellungen_aufrufe
				(id_hilfestellung, id_login, date_tag, anzahl) VALUES (?, ?, ?, ?)');
				$sql->execute([$eintrag["id"], $_POST["id_login"], $tag, '1']);
			}
		}
		echo json_encode([
			'status'=>$status['code'],
			'statusInfo'=>$status['info'],
			'eintrag'=>$eintrag,
		]);
	}
	
	if (isset($_GET["getAllLocals"])) {
		$status['code'] = 'error';
		$status['info'] = 'Kein Eintrag gefunden';
		$eintraege = [];
		$search = getHilfestellungen(['allLocals'=>true]);
		if (count($search) > 0) {
			$status['code'] = 'success';
			$status['info'] = '';
			$eintraege = $search;
		}
		echo json_encode([
			'status'=>$status['code'],
			'statusInfo'=>$status['info'],
			'eintraege'=>$eintraege,
		]);
	}
	
	if (isset($_GET["save"])) {
		$status['code'] = 'success';
		$status['info'] = '';
		$eintrag = ['id'=>0];
		$user = getLoginByToken($_POST["token"]);
		if (!$user["idRole"] == 2) {
			$status['code'] = 'error';
			$status['info'] = 'Login ungültig';
		} else {
			$eintrag = json_decode($_POST["eintrag"], true);
			$assets = $eintrag["assets"];
			$saveEintrag = [
				trim($eintrag['title']),
				($eintrag['html']),
				($eintrag['html_short']),
				date("Y-m-d H:i:s"),
				$eintrag['offline'],
			];
			if ($eintrag["id"] <= 0) {
				// Neuer Eintrag
				array_push($saveEintrag, date("Y-m-d H:i:s"));
				$code = createCode();
				array_push($saveEintrag, $code);
				$sql = $pdo->prepare('INSERT INTO hilfestellungen
				(title, html, html_short, date_lastEdit, offline, date_create, code)
				VALUES (?, ?, ?, ?, ?, ?, ?)');
				$sql->execute($saveEintrag);
				$eintrag = ['id'=>$pdo->lastInsertId(), 'code'=>$code];
			} else {
				// Eintrag bearbeiten
				array_push($saveEintrag, $eintrag["id"]);
				$sql = $pdo->prepare('UPDATE hilfestellungen SET
				title = ?,
				html = ?,
				html_short = ?,
				date_lastEdit = ?,
				offline = ?
				WHERE BINARY id = ?');
				$sql->execute($saveEintrag);
				$eintrag = ['id'=>$eintrag["id"], 'code'=>$eintrag["code"]];
			}
			// QR-Code erstellen
				require_once(dirname(__FILE__).'/data/plugin/phpqrcode/qrlib.php');
				$qrCodeFileName = 'qr_'.$eintrag["code"].'.png';
				$qrCodeAbsoluteFilePath = dirname(__FILE__).'/data/assets/'.$eintrag["id"].'';
				createPath($qrCodeAbsoluteFilePath);
				if (!file_exists($qrCodeAbsoluteFilePath.'/'.$qrCodeFileName) or 1 == 1) {
					QRcode::png('https://iu-bachelorarbeit.fkaltenbach.de/?h='.$eintrag["code"], $qrCodeAbsoluteFilePath.'/'.$qrCodeFileName);
					// PDF erstellen
						require_once(dirname(__FILE__).'/data/plugin/tcpdf_template-1.php');
						$html = '
							<table width="100%">
								<tr><td align="center">
									<img src="data/assets/'.$eintrag["id"].'/qr_'.$eintrag["code"].'.png" width="130px">
								</td></tr>
								<tr><td align="center">
									'.$eintrag["code"].'
								</td></tr>
							</table>
						';
						$pdfdatenNeu = [
							'titel'=>'QR-Code '.$eintrag["code"].'',
							//'bg'=>'data/assets/'.$eintrag["id"].'/qr_'.$eintrag["code"].'.png',
							'html'=>$html,
							'speicherpfad'=>'data/assets/'.$eintrag["id"].'/qr_'.$eintrag["code"].'.pdf',
							'page_width'=>50,
							'page_height'=>50,
						];
						createPdfTemplate1($pdfdatenNeu);
					//
				}
			//
			// Assets bearbeiten
				$sql = $pdo->prepare('SELECT * FROM hilfestellungen_assets WHERE BINARY id_hilfestellung = ?');
				$sql->execute([$eintrag["id"]]);
				foreach($sql->fetchAll() as $ausgabe) {
					$vorhanden = false;
					foreach($assets as $a) {
						if ($a["id"] == $ausgabe["id"]) {
							$sql = $pdo->prepare('UPDATE hilfestellungen_assets SET name = ? WHERE BINARY id = ?');
							$sql->execute([$a["name"], $ausgabe["id"]]);
							$vorhanden = true;
						}
					}
					if (!$vorhanden) {
						unlink(dirname(__FILE__).'/data/assets/'.$eintrag["id"].'/'.$ausgabe["id"].'.'.$ausgabe["type"].'');
						$sql = $pdo->prepare('DELETE FROM hilfestellungen_assets WHERE BINARY id = ?');
						$sql->execute([$ausgabe["id"]]);
					}
				}
			//
		}
		echo json_encode([
			'status'=>$status['code'],
			'statusInfo'=>$status['info'],
			'eintrag'=>$eintrag,
		]);
	}
	
	if (isset($_GET["addAssets"])) {
		$status['code'] = 'success';
		$status['info'] = '';
		$assets = [];
		
		$user = getLoginByToken($_POST["token"]);
		if (!$user["idRole"] == 2) {
			$status['code'] = 'error';
			$status['info'] = 'Login ungültig';
		} else {
			
			$path = dirname(__FILE__).'/data/assets/'.$_POST["idHilfestellung"].'';
			createPath($path);
			foreach($_FILES as $newFile) {
				$name_r = explode('.', $newFile['name']);
				$endung = strtolower(array_pop($name_r));
				if (in_array($endung, ['jpeg', 'png'])) {
					$endung = 'jpg';
				}
				$name = implode('.', $name_r);
				$sql = $pdo->prepare('INSERT INTO hilfestellungen_assets (id_hilfestellung, name, type) VALUES (?, ?, ?)');
				$sql->execute([$_POST["idHilfestellung"], $name, $endung]);
				$neueId = $pdo->lastInsertId();
				move_uploaded_file($newFile['tmp_name'], $path.'/'.$neueId.'.'.$endung.'');
				if ($endung == 'jpg') {
					resize_image($path.'/'.$neueId.'.'.$endung.'', 1500, 1500);
				}
				array_push($assets, [
					'id'=>$neueId,
					'name'=>$name,
					'type'=>$endung,
				]);
			}
			
			
		}
		
		echo json_encode([
			'status'=>$status['code'],
			'statusInfo'=>$status['info'],
			'assets'=>$assets,
		]);
	}
	
	if (isset($_GET["sendFeedback"])) {
		$status['code'] = 'success';
		$status['info'] = '';
		
		$user = getLoginByToken($_POST["token"]);
		if (!$user["idRole"] == 1) {
			$status['code'] = 'error';
			$status['info'] = 'Login ungültig';
		} else {
			$sql = $pdo->prepare('INSERT INTO hilfestellungen_feedback 
			(id_hilfestellung, id_login, rating, text, date_send)
			VALUES (?, ?, ?, ?, ?)');
			$sql->execute([$_POST["idHilfestellung"], $user["id"], $_POST["rating"], $_POST["text"], date("Y-m-d H:i:s")]);
		}
		
		echo json_encode([
			'status'=>$status['code'],
			'statusInfo'=>$status['info'],
		]);
	}
?>
