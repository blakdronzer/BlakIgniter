<?php
/*
 * Subject          : Export pdf using dompdf
* Author           : Sanjay
* Version          : CodeIgniter_2.0.3
* Warning         : Any change in this file may cause abnormal behaviour of application.
*
*/

if ( ! function_exists('exportMeAsDOMPDF'))
{
	function exportMeAsDOMPDF($htmView,$fileName) {

		$CI =& get_instance();
		require_once(APPPATH . "libraries/dompdf_config.inc.php");
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($htmView);
		$dompdf->render();
		$dompdf->stream($fileName);		
		
	}
}

?>