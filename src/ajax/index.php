<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */


error_reporting(E_ALL);

ini_set("display_errors", 1);


// include constants
require_once("../constants.php");

require_once("../functions.php");
require_once("../dbConfig.php");
//$_SESSION['SYSTEM_ENTRY'] = USCIC_SMS;
$loaded = dbConfig::load("../conf.php");
require_once("../config.php");
require_once("../object.php");
require_once("../component.php");
require_once("../setting.php");
require_once("../survey.php");

/* check for 'javascript enabled' indicator */
isJavascriptEnabled();

/* set path */
setPath();

/* database */
require_once('database.php');

$db = new Database();
if ($db->getDb() == null) { //no connection with DB.. Errormessage!
    exit;
}

ini_set('xdebug.max_nesting_level', 2000);
ini_set("error_reporting", "ALL");

if (loadvar('ajaxr') != '') {
    $params = getAjaxParamsPost(loadvar('ajaxr'));
    $_SESSION[SURVEY_EXECUTION_MODE] = $params["executionmode"];
}

require_once('surveyajax.php');
$ajax = new SurveyAjax($params);
$ajax->getPage(loadvar('p'));
exit;

?>