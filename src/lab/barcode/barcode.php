<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */
exit;
$number = $_GET['number'];
$scale = 2;
$fontsize = 11;
header('Location: barcodegen/html/image.php?filetype=PNG&dpi=72&scale=' . $scale . '&rotation=0&font_family=Arial.ttf&font_size=' . $fontsize . '&text=' . $number . '&thickness=20&start=B&code=BCGcode128');

?>