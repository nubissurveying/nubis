<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once('constants.php');
require_once('functions.php');
require_once("dbConfig.php");
require_once("config.php");

// define header
$returnStr = '<html><body>
<center>
Start parallel section';

// start form
$returnStr .= '<form method=post action=index.php>';

// start survey
$returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value=1>';

// set survey to start
$returnStr .= '<input type=hidden name=' . POST_PARAM_SUID . ' value=1>';

// set which section to start
$returnStr .= setSessionParamsPost(array(SESSION_PARAM_MAINSEID => 11, SESSION_PARAM_SEID => 11));

$returnStr .= '<input type=hidden name=' . POST_PARAM_RESET_EXTERNAL . ' value=1>';

// generate a random primary key to be used
$returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC(generateRandomPrimkey(8), Config::directLoginKey())) . '">';

// start a new interview each time
$returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';            

// set language to be used
$returnStr .= '<input type=hidden name=language value=1>';

// set interview mode to be used (1=CAPI, 2=CATI, 3=CASI, 4=CADI)
$returnStr .= '<input type=hidden name=mode value=3>';

// survey execution mode (0=normal, 1=test mode)
$returnStr .= '<input type=hidden name=executionmode value=1>';

// close
$returnStr .= '
<input type="submit" value="Start">
</form></center>
</body></html>';

echo $returnStr;

?>