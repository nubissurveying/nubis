<?php
exit;
$number = $_GET['number'];

echo '<html><head><style type="text/css">

@page  
{ 
    size: auto;   /* auto is the initial value */ 

    /* this affects the margin in the printer settings */ 
    margin: 19mm 0mm 0mm 0mm;  
} 
body  
{ 
    /* this affects the margin on the content before sending to printer */ 
    margin: 0px;  
} 
  P.breakhere {page-break-before: always}


</style></head><body>';
//<div class=containingDiv style="width:46px; height:86px;"><img style="width:100%; height:100%;" src=http://localhost/barcode/phpqrcode/barcode.php?number=AG207:04&data=AG207:04></div>';

require_once('../../../lab.php');
$lab = new Lab(null);
//$type = $_GET['type'];

$ttarray = $lab->getBloodTests();

for ($i = sizeof($ttarray); $i > 0; $i--) {
//  $images[] = '<img src=http://localhost/barcode/phpqrcode/barcode.php?number=' . $number . ':' . sprintf("%0" . 2 . "d", $i) . '&data=' . $number . ':' . sprintf("%0" . 2 . "d", $i) . '>';

    $img = '<table border=0 cellspacing=0 cellpadding=0><tr><td valign=top><table border=0 cellspacing=0 cellpadding=0><tr><td>';
    $img .= '<img src=barcode.php?number=' . $number . ':' . sprintf("%0" . 2 . "d", $i) . '&data=' . $number . '' . sprintf("%0" . 2 . "d", $i) . '>';
    $img .= '</td></tr><tr><td><font style="font-size:13px; font-face:arial; font-weight: bold;">' . $number . ':' . sprintf("%0" . 2 . "d", $i) . '</td></tr>';
    $img .= '</font><tr><td><font style="font-size:11px; font-face:arial; font-weight: bold;">';
    $img .= substr($ttarray[$i][0], 0, 11);
    $img .= '</font></td></tr></table>';
    $img .= '</td><td><img src=../barcodegen/html/image.php?filetype=PNG&dpi=300&scale=1&rotation=90&font_family=Arial.ttf&font_size=0&text=' . $number . '&thickness=20&start=B&code=BCGcode128></td></tr></table>';
    $images[] = $img; //'<img src=barcode.php?number=' . $number . ':' . sprintf("%0" . 2 . "d", $i) . '&data=' . $number . '' . sprintf("%0" . 2 . "d", $i) . '>';
}

echo implode('<P class="breakhere">', $images);
//dbs code
echo '<P class="breakhere">';
echo '<img src=../barcode.php?scale=1&number=' . $number . '>';
echo '
</body></html>
';





