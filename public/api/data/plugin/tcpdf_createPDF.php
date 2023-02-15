<?php
	//////////////////////////// Erzeugung des PDF Dokuments \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
	
		// TCPDF Library laden
		require_once(dirname(__FILE__).'/tcpdf/tcpdf.php');
		 
		// Erstellung des PDF Dokuments
		//$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		
		function createPDF($ownPDF, $settings) {
			
			$backToRoot = dirname(__FILE__).'/../../';
			
			$pdf = $ownPDF;
			// Dokumenteninformationen
			$pdf->SetCreator(PDF_CREATOR);
			if (!isset($settings['autor'])) {
				$settings['autor'] = '';
			}
			$pdf->SetAuthor($settings['autor']);
			if (!isset($settings['titel'])) {
				$settings['titel'] = 'PDF';
			}
			$pdf->SetTitle($settings['titel']);
			if (!isset($settings['untertitel'])) {
				$settings['untertitel'] = '';
			}
			$pdf->SetSubject($settings['untertitel']);
			 
			 
			// Header und Footer Informationen
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->SetHeaderData($settings['headerfooter'], PDF_HEADER_LOGO_WIDTH, $settings['titel'], $settings['untertitel']);
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
			 
			// Auswahl des Font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			 
			// Auswahl der Margins
			//$pdf->SetMargins(10, $settings['margin_top'], 10); // <-- Margin left, top, right
			$pdf->SetMargins($settings["headerfooter"]["margin_leftRight"] + $settings["headerfooter"]["margin_addLeft"], $settings["headerfooter"]["margin_top"], $settings["headerfooter"]["margin_leftRight"]);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			 
			// Automatisches Autobreak der Seiten
			//$pdf->SetAutoPageBreak(TRUE, $settings['margin_bottom']); // <-- Margin bottom
			$pdf->SetAutoPageBreak(TRUE, $settings["headerfooter"]["margin_bottom"]); // <-- Margin bottom
			 
			// Image Scale 
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
			 
			// Schriftart
			$pdf->SetFont($settings["headerfooter"]["font"], '', $settings["headerfooter"]["font_size"]);
			 
			// Neue Seite
			$pdf->AddPage();
			$pdf->writeHTML($settings['html'], true, false, true, false, '');
				
			//Ausgabe der PDF
			 
			//Variante 1: PDF direkt an den Benutzer senden:
			//$pdf->Output($pdfName, 'I');
			 
			//Variante 2: PDF im Verzeichnis abspeichern:
			$save = explode('/', $settings['save']);
			unset($save[count($save) - 1]);
			if (!is_dir($backToRoot.implode('/', $save))) {
				mkdir($backToRoot.implode('/', $save));
			}
			if ($settings['save'] != "") {
				$pdf->Output($backToRoot.$settings['save'], 'F');
				//echo 'PDF herunterladen: <a href="erzeugtePDFs/'.$pdfName.'" target="_blank">'.$pdfName.'</a>';
			}
		}
?>