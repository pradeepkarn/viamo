<?php

use Dompdf\Dompdf;
use Dompdf\Options;
class Label_ctrl
{
	// function generate_pdf($orddata)
	// {
	// 	$home = home;
	// 	// Create a Dompdf instance
	// 	$obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// 	$obj_pdf->SetCreator(PDF_CREATOR);
	// 	$obj_pdf->SetTitle("Shipping Label");
	// 	$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
	// 	$obj_pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	// 	$obj_pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	// 	$obj_pdf->SetDefaultMonospacedFont('');
	// 	$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	// 	$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
	// 	$obj_pdf->setPrintHeader(false);
	// 	$obj_pdf->setPrintFooter(false);
	// 	$obj_pdf->SetAutoPageBreak(true, 10);
	// 	$obj_pdf->SetFont('', '', 9);
	// 	$obj_pdf->AddPage("L");
	// 	$pdfdata = render_template("apps/view/pages/label-print.php", $orddata);
	// 	$obj_pdf->writeHTML($pdfdata);
	// 	// Output PDF
	// 	$pdffile = RPATH . "/media/docs/labels/expiringcontracts.pdf";
	// 	$obj_pdf->Output($pdffile, 'F');
	// 	return true;
	// }
	function genrate_dom_pdf($orddata)
	{
		// Initialize Dompdf
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isPhpEnabled', true);
		$options->set('isHtml5Powered', true);

		$dompdf = new Dompdf($options);

		$html = render_template("apps/view/pages/label-print.php", $orddata);

		// Output PDF
		$pdffile = RPATH . "/media/docs/labels/expiringcontracts.pdf";

		$dompdf->loadHtml($html);

		// Set paper size (optional)
		$dompdf->setPaper('A4', 'portrait');

		// Render PDF (first step)
		$dompdf->render();

		// Output PDF to browser
		$dompdf->stream($pdffile, array('Attachment' => 0));
	}
	function genratePDF() {
		
	}
}
