<?php

$csv = $_POST['csv'];
if ($csv) {
	header('Content-type: text/csv');
	header('Content-disposition: attachment;filename=chart.csv');
	echo $csv;
}

?>