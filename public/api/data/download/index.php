<?php
	require_once(dirname(__FILE__).'/../../connect.php');
	
	$file = $_POST['file'];
	$file_r = explode('.', $file);
	$type = array_pop($file_r);
	
	if (in_array($type, ['jpg', 'jpeg'])) {
		header('Content-Type: image/jpeg; charset=utf-8');
	}
	if (in_array($type, ['png'])) {
		header('Content-Type: image/png; charset=utf-8');
	}
	if (in_array($type, ['pdf'])) {
		header('Content-Type: application/pdf; charset=utf-8');
	}
	
	readfile(dirname(__FILE__).'/../../'.$file);
?>