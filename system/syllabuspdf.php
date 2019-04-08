<?php
session_start();
require('../config.php');
if(empty($_SESSION['person'])) header("Location:".BASEURL);
db_connect();
$crsId = $_REQUEST['crsid'];
$crsdata = mysqli_fetch_row(mysqli_query($db,"SELECT `crsname`, `fk_catid`, `crscode`, `crscredits`, `crsdescription`, `crsdesign`, `univname`, `facname`, `progname`, `lecture`, `laboratory`, `crscoordinator`, `lastreview` FROM `vb_courses` WHERE `crsid` = $crsId"));
//============================================================+
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
	public function Footer() {
		$cur_y = $this->GetY();
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		//set style for cell border
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
			$style = array(
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128B', '', $cur_y + $line_width, '', (($this->getFooterMargin() / 3) - $line_width), 0.3, $style, '');
		}
		if (empty($this->pagegroups)) {
			$pagenumtxt = $this->l['w_page'].' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $this->l['w_page'].' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY($cur_y);
		//Print page number
		if ($this->getRTL()) {
			$this->SetX($ormargins['right']);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
		} else {
			$this->SetX($ormargins['left']);
			$this->Cell(0, 0, 'Copyright '.date("Y").', Video Course Aggregator, All rights reserved', 'T', 0, 'L');
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
		}
	}
}
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(SITENAME);
$pdf->SetTitle($crsdata[0]);
$pdf->SetSubject('Course Syllabus (.PDF)');
$pdf->SetKeywords('video course, aggregator, youtube,');

// set default header data
$pdf->SetHeaderData('vca-logo.jpg', 30, $crsdata[0].' ('.$crsdata[2].')', 'Course Syllabus');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

/* NOTE:
 * *********************************************************
 * You can load external XHTML using :
 *
 * $html = file_get_contents('/path/to/your/file.html');
 *
 * External CSS files will be automatically loaded.
 * Sometimes you need to fix the path of the external CSS.
 * *********************************************************
 */
$fileContent = '
<table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr class="record1">
    <th width="18%" align="left" valign="top" scope="row">University:</th>
    <td width="82%" align="left" valign="top">'.$crsdata[6].'</td>
  </tr>
  <tr class="record2">
    <th align="left" valign="top" scope="row">Faculty:</th>
    <td align="left" valign="top">'.$crsdata[7].'</td>
  </tr>
  <tr class="record1">
    <th align="left" valign="top" scope="row">Programme:</th>
    <td align="left" valign="top">'.$crsdata[8].'</td>
  </tr>
  <tr class="record2">
    <th rowspan="2" align="left" valign="top" scope="row">Course Information:</th>
    <td align="left" valign="top"><em>Code: </em>'.$crsdata[2].' &nbsp;&nbsp;<em>Title: </em>'.$crsdata[0].'</td>
  </tr>
  <tr class="record2">
    <td align="left" valign="top"><em>Designation: </em>'.$crsdata[5].' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>Credit Hours: </em>'.$crsdata[3].'</td>
  </tr>
  <tr class="record1">
    <th align="left" valign="top" scope="row">Course Description:</th>
    <td align="left" valign="top">'.$crsdata[4].'</td>
  </tr>
  <tr class="record2">
    <th align="left" valign="top" scope="row">Lecture:</th>
    <td align="left" valign="top">'.$crsdata[9].'</td>
  </tr>
  <tr class="record1">
    <th align="left" valign="top" scope="row">Laboratory:</th>
    <td align="left" valign="top">'.$crsdata[10].'</td>
  </tr>
  <tr class="record2">
    <th align="left" valign="top" scope="row">Coordinator:</th>
    <td align="left" valign="top">'.$crsdata[11].'</td>
  </tr>
</table>
';

// define some HTML content with style
$html = <<<EOF
<style>
tr.record1{background-color:#EEE}
tr.record2{background-color:#FFF}
th, em {color:#006; font-family:"Courier New", Courier, monospace}
</style>
$fileContent
EOF;

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// reset pointer to the last page
$pdf->lastPage();

// add a page
/*$pdf->AddPage();
$html2 = <<<EOF
EOF;

// output the HTML content
$pdf->writeHTML($html2, true, false, true, false, '');
// reset pointer to the last page
$pdf->lastPage();
*/// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output($crsdata[0].' Syllabus.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
