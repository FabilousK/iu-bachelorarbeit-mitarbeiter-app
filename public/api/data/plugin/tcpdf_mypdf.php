<?php
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		//global $APPSETTINGS_Anschrift;
		//$headerdata = $this->getHeaderData();
		// Logo
		//$image_file = 'https://'.strtolower($_SERVER['SERVER_NAME']).'/store/pdf/set_img/tcpdf_header_logo.jpg';
		//$this->Image($image_file, 0, 0, 210, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		//$this->SetFont('helvetica', '', 10);
		// Title
		//$align = 'L';
		
		//$this->Cell(5, 5, 'Lorem ipsum', 0, 0 /* gleiche Zeile */, $align, 0, '', 0, false, 'T', 'M');
		//$this->Cell(5, 5, 'dolor sit amet', 0, 1 /* neue Zeile */, $align, 0, '', 0, false, 'T', 'M');
		
		$backToRoot = dirname(__FILE__).'/../../';
		
		$aktuelleSeitenzahl = strval($this->getAliasNumPage());
		$headerdata = $this->getHeaderData();
		
		if (!isset($headerdata["logo"]["page_width"])) {
			$headerdata["logo"]["page_width"] = 210;
		}
		if (!isset($headerdata["logo"]["page_height"])) {
			$headerdata["logo"]["page_height"] = 297;
		}
		
		if($this->page == 1) {
			$this->SetMargins($headerdata["logo"]["margin_leftRight"], $headerdata["logo"]["margin_top_firstSite"], $headerdata["logo"]["margin_leftRight"]);
			$this->SetAutoPageBreak(TRUE, $headerdata["logo"]["margin_bottom_firstSite"]); // <-- Margin bottom
		} else {
			$this->SetMargins($headerdata["logo"]["margin_leftRight"], $headerdata["logo"]["margin_top"], $headerdata["logo"]["margin_leftRight"]);
			$this->SetAutoPageBreak(TRUE, $headerdata["logo"]["margin_bottom"]); // <-- Margin bottom
		}
		$this->SetFont($headerdata["logo"]["font"], '', $headerdata["logo"]["font_size"]);
		$align = 'L';
		
		if($headerdata["logo"]["bg"] != "") {
			$image_file = $backToRoot.$headerdata["logo"]["bg"];
			// get the current page break margin
			$bMargin = $this->getBreakMargin();
			// get current auto-page-break mode
			$auto_page_break = $this->AutoPageBreak;
			// disable auto-page-break
			$this->SetAutoPageBreak(false, 0);
			// set background image
			$this->Image($image_file, 0, 0, $headerdata["logo"]["page_width"], $headerdata["logo"]["page_height"], '', '', '', false, 300, '', false, false, 0);
			// restore auto-page-break status
			$this->SetAutoPageBreak($auto_page_break, $bMargin);
			// set the starting point for the page content
			$this->setPageMark();
		}
		$max_x = 32;
		$max_y = 65;
		for($i=1;$i<=$max_y;$i++){
			for($j=1;$j<=$max_x;$j++){
				if(!isset($headerdata["logo"]["headertext"][$i."-".$j]["font"])){
					$headerdata["logo"]["headertext"][$i."-".$j]["font"] = $headerdata["logo"]["font"];
				}
				if(!isset($headerdata["logo"]["headertext"][$i."-".$j]["size"])){
					$headerdata["logo"]["headertext"][$i."-".$j]["size"] = $headerdata["logo"]["font_size"];
				}
				if(!isset($headerdata["logo"]["headertext"][$i."-".$j]["style"])){
					$headerdata["logo"]["headertext"][$i."-".$j]["style"] = $headerdata["logo"]["font_size"];
				}
				if(!isset($headerdata["logo"]["headertext"][$i."-".$j]["text"])){
					$headerdata["logo"]["headertext"][$i."-".$j]["text"] = "";
				}
				if(!isset($headerdata["logo"]["headertext"][$i."-".$j]["textSeiteEins"])){
					$headerdata["logo"]["headertext"][$i."-".$j]["textSeiteEins"] = "";
				}
				if($this->page == 1) {
					$text = $headerdata["logo"]["headertext"][$i."-".$j]["textSeiteEins"];
				} else {
					$text = $headerdata["logo"]["headertext"][$i."-".$j]["text"];
				}
				$lastCol = 0;
				if($j==$max_x){
					$lastCol = 1;
				}
				$this->SetFont($headerdata["logo"]["headertext"][$i."-".$j]["font"], $headerdata["logo"]["headertext"][$i."-".$j]["style"], $headerdata["logo"]["headertext"][$i."-".$j]["size"]);
				$this->Cell((190/$max_x), 4, $text, 0, $lastCol, $align, 0, '', 0, false, 'T', 'M');
				$this->SetFont($headerdata["logo"]["font"], '', $headerdata["logo"]["font_size"]);
			}
		}
	}
	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		//$this->SetY(-40);
		// Set font
		//$this->SetFont('helvetica', '', 10);
		// content
		//$image_file = 'https://'.strtolower($_SERVER['SERVER_NAME']).'/store/pdf/set_img/tcpdf_header_logo.jpg';
		//$this->Image($image_file, 0, 267, 210, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		/*
		$this->Cell(90, 5, 'Lorem ipsum – dolor sit amet', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, '', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, 'Lorem ipsum', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, 'Lorem ipsum', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, 'Lorem ipsum', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, 'Lorem ipsum', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(90, 5, '', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
		*/
		// Page number
		$headerdata = $this->getHeaderData();
		if ($headerdata["logo"]["showSiteNumbers"]){
			$this->SetFont($headerdata["logo"]["font"], 'B', $headerdata["logo"]["font_size"] - 2);
			$this->Cell(190, 0, 'Seite '.$this->getAliasNumPage().' von '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
		}
	}
}
?>