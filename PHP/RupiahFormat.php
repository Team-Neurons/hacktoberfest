<?php 

function rupiah($amount){
	
	$format_rupiah = "Rp " . number_format($amount,2,',','.');
	return $format_rupiah;
 
}

?>