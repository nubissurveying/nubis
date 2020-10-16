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
require_once("constants.php");
require_once("functions.php");
require_once("dbConfig.php");
$loaded = dbConfig::load();
require_once("config.php");

// start session if installed
if ($loaded == 3) {
    session_set_cookie_params(0, getSessionPath()); // set cookie path
    session_start();
}

if ((isset($_POST[POST_PARAM_FULLRESET]) && is_Numeric($_POST[POST_PARAM_FULLRESET])) || (isset($_GET[POST_PARAM_FULLRESET]) && is_Numeric($_GET[POST_PARAM_FULLRESET]))) { //reset session!
    endSession();
    $param = '';
    if (isset($_GET[POST_PARAM_SE]) && is_Numeric($_GET[POST_PARAM_SE])) {
        if ($_GET[POST_PARAM_SE] > 1) {
            $param = '?' . POST_PARAM_SE . '=' . $_GET[POST_PARAM_SE];
        }
    }
    if (isset($_POST[POST_PARAM_SE]) && is_Numeric($_POST[POST_PARAM_SE])) {
        if ($_POST[POST_PARAM_SE] > 1) {
            $param = '?' . POST_PARAM_SE . '=' . $_POST[POST_PARAM_SE];
        }
    }

    header('Location: index.php' . $param);
    exit;
}

//ss: used for direct login start from SMS to reset the session, but keep the post parameters!
if ((isset($_POST[POST_PARAM_RESET]) && is_Numeric($_POST[POST_PARAM_RESET])) || (isset($_GET[POST_PARAM_RESET]) && is_Numeric($_GET[POST_PARAM_RESET]))) { //reset session!
    //endSession();
    clearSession(); // resets session, but keeps session id
    $param = '';
    foreach ($_POST as $key => $value) {
        if ($key != POST_PARAM_RESET) { //going in circles :)
            $param .= $key . '=' . urlencode($value) . '&';
        }
    }
    $param = rtrim($param, '&');
    header('Location: index.php?' . $param);
    exit;
}

//ms: used for external start, but not from SMS!
if ((isset($_POST[POST_PARAM_RESET_EXTERNAL]) && is_Numeric($_POST[POST_PARAM_RESET_EXTERNAL])) || (isset($_GET[POST_PARAM_RESET_EXTERNAL]) && is_Numeric($_GET[POST_PARAM_RESET_EXTERNAL]))) { //reset session!
    clearSession(); // resets session, but keeps session id
    $param = '';
    foreach ($_POST as $key => $value) {
        if ($key != POST_PARAM_RESET_EXTERNAL) { //going in circles :)
            $param .= $key . '=' . urlencode($value) . '&';
        }
    }
    $param = rtrim($param, '&');
    header('Location: index.php?' . $param);
    exit;
}

//ts: used for test from sms!
if ((isset($_POST[POST_PARAM_RESET_TEST]) && is_Numeric($_POST[POST_PARAM_RESET_TEST])) || (isset($_GET[POST_PARAM_RESET_TEST]) && is_Numeric($_GET[POST_PARAM_RESET_TEST]))) { //reset session!
    clearSession(); // resets session, but keeps session id
    $param = '';
    foreach ($_POST as $key => $value) {
        if ($key != POST_PARAM_RESET_TEST) { //going in circles :)
            $param .= $key . '=' . urlencode($value) . '&';
        }
    }
    $param = rtrim($param, '&');
    header('Location: index.php?' . $param);
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > Config::sessionTimeout())) {    // last request was more than session timeout period
    $param = '?' . POST_PARAM_SE . '=' . $_SESSION['SYSTEM_ENTRY'];
    endSession();
    header('Location: index.php' . $param);
    exit;
}

$sesid = session_id();
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
if (isset($_GET[POST_PARAM_SE]) != '' && is_Numeric($_GET[POST_PARAM_SE])) {
    $_SESSION['SYSTEM_ENTRY'] = $_GET[POST_PARAM_SE];
}
if (isset($_POST[POST_PARAM_SE]) != '' && is_Numeric($_POST[POST_PARAM_SE])) {
    $_SESSION['SYSTEM_ENTRY'] = $_POST[POST_PARAM_SE];
}
if (!isset($_SESSION['SYSTEM_ENTRY'])) {
    $_SESSION['SYSTEM_ENTRY'] = Config::defaultStartup(); //default startup
}
if (!isset($_SESSION['COMMSERVER'])) {
    $_SESSION['COMMSERVER'] = 0;
}

/* session level survey locking (ignore ajax calls) */
if (loadvar(POST_PARAM_SMS_AJAX) != SMS_AJAX_CALL) { // not sms ajax call
    if ($_SESSION['SYSTEM_ENTRY'] != USCIC_SMS) {

        if (isset($_SESSION['REQUEST_IN_PROGRESS']) && $_SESSION['REQUEST_IN_PROGRESS'] == 1) {        
            $_SESSION['PREVIOUS_REQUEST_IN_PROGRESS'] = 1;
        }
        else {

            $_SESSION['REQUEST_IN_PROGRESS'] = 1;
            $_SESSION['PREVIOUS_REQUEST_IN_PROGRESS'] = null;
            unset($_SESSION['PREVIOUS_REQUEST_IN_PROGRESS']);
        }
    }
}

require_once('globals.php');
if (loadvar('r') != '') {
    
    // if real request (not second submitted one while first is still running), load session information
    if (!isset($_SESSION['PREVIOUS_REQUEST_IN_PROGRESS'])) {
        getSessionParamsPost(loadvar('r'));
    }
}

/* survey entry */
if ($_SESSION['SYSTEM_ENTRY'] != USCIC_SMS) {
    $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
    $engine = null; // global $engine object            
} else {
    $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    $l = getSMSLanguage();

    if (file_exists("language/language" . getSMSLanguagePostFix($l) . ".php")) {
        require_once('language_' . getSMSLanguagePostFix($l) . '.php');
    } else {
        require_once('language_en.php'); // fall back on english language  file
    }
}

if (loadvar(POST_PARAM_SMS_AJAX) == SMS_AJAX_CALL) { // sms ajax call
    require_once('smsajax.php');
    $ajax = new SmsAjax();
    echo $ajax->getPage(loadvar('p'));
} else { // handle action
    $action = new Action($sesid);
    echo $action->getAction();

    // clear session locking (if not already done by earlier script exit)
    doExit();
}

?>