<?php

echo '<html><body>';
$data = 'http://localhost/dad/sms.html';

echo '<img src="http://localhost/haalsi/lab/barcode/phpqrcode/barcode.php?size=5&data=';
echo urlencode($data);
echo '">';
echo '</body></html>';

