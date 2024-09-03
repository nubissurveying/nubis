<?php
exit;

if (isset($_POST['p']) && $_POST['p'] == 'generate'){	


	$prefix = $_POST['prefix'];
	for($i = $_POST['start']; $i <= $_POST['end']; $i++){

		echo '<img src=html/image.php?filetype=PNG&dpi=72&scale=2&rotation=0&font_family=Arial.ttf&font_size=19&text=' . $prefix . '' . sprintf('%03d', $i) . '&thickness=20&start=B&code=BCGcode128>';
		echo '<br/><br/>';
	}

}
else {

	$prefix = 'LD1';
	if ( isset($_GET['pre'])   ){
		$prefix = $_GET['pre'];
	}

	echo '<html><body>';
	echo '<form method=post>';
	echo '<input type=hidden name=p value="generate">';
	echo '<input type=hidden name=prefix value="' . $prefix . '">';

	echo 'Enter a range for the barcodes that you want to print:</br>';
	echo $prefix . '<input name=start type=text value="001"><br/><';
	echo 'to<br/>';
	echo $prefix . '<input name=end type=text value="010"><br/>';

	echo '<input type=submit>';
	echo '</form>';
	echo '</body></html>';

}




