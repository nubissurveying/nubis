<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once("../constants.php");
require_once("../functions.php");
require_once("../dbConfig.php");

$_SESSION['SYSTEM_ENTRY'] = USCIC_SMS;
$loaded = dbConfig::load("../conf.php");
require_once("../config.php");
require_once("../globals.php");
require_once("../user.php");
require_once('reportissue.php');
require_once('watchwindow.php');
require_once('jumpback.php');
require_once('updater.php');
require_once("../display/templates/displayquestion_" . getSurveyTemplate() . ".php");

if (loadvar('r') != '') {
    getSessionParamsPost(loadvar('r'));
}

if (!isset($_SESSION[CONFIGURATION_ENCRYPTION_TESTER])) {
    $_SESSION[CONFIGURATION_ENCRYPTION_TESTER] = decryptC(getFromSessionParams("k"), Config::smsComponentKey());
}

if ($_SESSION[CONFIGURATION_ENCRYPTION_TESTER] != Config::testerKey()) {
    exit;
}

// include language
$l = getSMSLanguage();
if (file_exists("language/language" . getSMSLanguagePostFix($l) . ".php")) {
    require_once('language_' . getSMSLanguagePostFix($l) . '.php');
} else {
    require_once('language_en.php'); // fall back on english language  file
}
$_SESSION['SYSTEM_ENTRY'] = USCIC_SURVEY; // switch back to survey


$page = getFromSessionParams('testpage');

if (getFromSessionParams('type') != "2") {
    $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
}

$engine = null; // needed for updater
switch ($page) {
    case "watch":
        $watch = new Watcher();
        $watch->watch();        
        break;
    case "report":
        $reportissue = new ReportIssue();
        $reportissue->report();
        break;
    case "reportRes":
        $reportissue = new ReportIssue();
        $reportissue->reportRes();
        break;
    case "jumpback":        
        $jumper = new JumpBack();
        $jumper->jump();
        break;
    case "jumpbackRes":        
        $jumper = new JumpBack();
        $jumper->jumpRes();
        break;
    case "update":        
        $update = new Updater();
        $update->update();
        break;
    case "updateRes":        
        $update = new Updater();
        $update->updateRes();
        break;
    default:
        //$reportissue->report();
        break;
}
if (getFromSessionParams('type') != "2") {
    $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
}

?>