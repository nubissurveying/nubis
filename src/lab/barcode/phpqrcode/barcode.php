<?php
exit;
  global $text;
  if (isset($_GET['number'])){
    $text = $_GET['number'];
  }
//  $data = 'tinyurl.com/qa27v73?b=' . $_GET['data'];
  $data = '' . $_GET['data'];

  include "qrlib.php";    
  $errorCorrectionLevel = 'L';
  $matrixPointSize = 2.5;
  if (isset($_GET['size'])){
    $matrixPointSize = $_GET['size'];
  }
	

  QRcode::png($data, false, $errorCorrectionLevel, $matrixPointSize, 1);    