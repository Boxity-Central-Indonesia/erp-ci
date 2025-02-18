<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once APPPATH . '/third_party/TCPDF-6.3.5/tcpdf.php';
class Pdf extends TCPDF
{
	function __construct()
	{
		parent::__construct();
	}

	//Page header
    public function Header() {
    	// Position at 10 mm from top
    	$this->SetY(10);
        // Set font
        $this->SetFont('helvetica', 'B', 16);
        // Title
        $this->Cell(0, 15, strtoupper(dataPerusahaan('NamaPerusahaan')['ValueSetting']), 0, false, 'C', 0, '', 0, false, 'M', 'M');

        // Position at 16 mm from top
    	$this->SetY(16);
        // Set font
        $this->SetFont('times', '', 10);
        // Line2
        $line2 = dataPerusahaan('AlamatPerusahaan')['ValueSetting'] .' | Telepon '. dataPerusahaan('NoTelpPerusahaan')['ValueSetting'];
        $this->Cell(0, 17, $line2, 0, false, 'C', 0, '', 0, false, 'M', 'M');

		$style3 = array('width' => 0.5, 'cap' => 'round', 'join' => 'round', 'dash' => 0, 'color' => array(0, 0, 0));
		$this->Line(5, 20, $this->line_header, 20, $style3);

		$this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, $this->CustomHeaderText, 0, false, 'L', 0, '', 0, false, 'T', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, $this->CustomFooterText, 0, false, 'L', 0, '', 0, false, 'T', 'M');
    }
}
?>