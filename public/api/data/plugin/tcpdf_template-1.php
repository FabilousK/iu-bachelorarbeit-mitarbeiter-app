<?php
	require_once(dirname(__FILE__).'/tcpdf_createPDF.php');
	function createPdfTemplate1($pdfdatenNeu){
		$pdfdaten = [
			'bg'=>'',
			'titel'=>'',
			'untertitel'=>'',
			'html'=>'',
			'speicherpfad'=>'',
			'font'=>'Helvetica',
			'font_size'=>'15',
			'showSiteNumbers'=>false,
			'headertext'=>[],
			'page_width'=>210,
			'page_height'=>297,
			'margin_leftRight'=>0,
			'margin_addLeft'=>0,
			'margin_top_firstSite'=>0,
			'margin_top'=>0,
			'margin_bottom_firstSite'=>0,
			'margin_bottom'=>0,
		];
		foreach($pdfdatenNeu as $pk=>$pv) {
			$pdfdaten[$pk] = $pv;
		}
		$settings = [
			'titel'=>$pdfdaten["titel"], // Titel der Datei
			'untertitel'=>$pdfdaten["untertitel"], // Untertitel
			'html'=>$pdfdaten["html"], // Dateiinhalt
			'save'=>$pdfdaten['speicherpfad'], // Pfad und Dateiname ausgehend von data/temp Bsp.: '/auswertung_2021_12_31.pdf'
			'headerfooter'=>[
				'page_width'=>$pdfdaten['page_width'], // Papierbreite in mm
				'page_height'=>$pdfdaten['page_height'], // Papierhöhe in mm
				'margin_leftRight'=>$pdfdaten['margin_leftRight'], // Platz Rechts und Links
				'margin_addLeft'=>$pdfdaten['margin_addLeft'], // Zusätzlicher Abstand Links z.B. zum Abheften
				'margin_top_firstSite'=>$pdfdaten['margin_top_firstSite'], // Platz für den Header auf der ersten Seite
				'margin_top'=>$pdfdaten['margin_top'], // Platz für den Header
				'margin_bottom_firstSite'=>$pdfdaten['margin_bottom_firstSite'], // Platz für den Footer auf der ersten Seite
				'margin_bottom'=>$pdfdaten['margin_bottom'], // Platz für den Footer
				'font'=>$pdfdaten['font'], // Schreibmaschine: courier
				'font_size'=>$pdfdaten['font_size'], // 
				'bg'=>$pdfdaten['bg'], // Hintergrundbild/Briefvorlage aus store/pdf/set_img/tcpdf_BGXX.jpg Bsp.: '01'
				'showSiteNumbers'=>$pdfdaten["showSiteNumbers"],
				'headertext'=> $pdfdaten["headertext"],
			],
		];
		/*$settings['headerfooter']['headertext']['1-1'] = [
			'size'=>5,
			'style'=>'U',
			'text'=>'',
			'textSeiteEins'=>'',
		];*/
		$pageFormat = array($pdfdaten['page_width'], $pdfdaten['page_height']);
		$orientation = 'P';
		if ($pdfdaten['page_height'] < $pdfdaten['page_width']) {
			$orientation = 'L';
		}
		$pdf = new MYPDF($orientation, PDF_UNIT, $pageFormat, true, 'UTF-8', false);
		createPDF($pdf, $settings);
	}
?>