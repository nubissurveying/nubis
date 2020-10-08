<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

/* check for 'javascript enabled' indicator */
isJavascriptEnabled();

/* set path */
setPath();

/* database */
require('database.php');
global $loaded;
$db = new Database();
if ($db->getDb() == null) { //no connection with DB.. Errormessage!     
    if ($_SESSION['SYSTEM_ENTRY'] != USCIC_SMS) {
        if (file_exists("error.html")) {
            $contents = file_get_contents("error.html");
            if ($contents != "") {
                echo $contents;
                exit;
            }
        }
        echo "<html><body><font face=arial>System not available!</font></body></html>";
        doExit();
    }
    else {
        
        // in SMS mode and no correct config, then we run install        
        if ($loaded == 2) {
            require('install.php');        
            $install = new Install(loadvar('p'));
            doExit();
        }
        else if ($loaded == 1) {
            $contents = file_get_contents("errorsms.html");
            if ($contents != "") {
                echo str_replace('$Error$', 'NubiS could not locate its configuration file (conf.php).', $contents);
                exit;
            }
            echo "<html><body><font face=arial>NubiS could not locate its configuration file (conf.php).</font></body></html>";
            doExit();
        }
        else {
            $contents = file_get_contents("errorsms.html");
            if ($contents != "") {
                echo str_replace('$Error$', 'NubiS could not access the database. <br/>Please verify your configuration settings in the conf.php file.', $contents);
                doExit();
            }
            echo "<html><body><font face=arial>NubiS could not access the database. Please verify your configuration settings in the conf.php file.</font></body></html>";
            doExit();
        }
    }
}

if ($_SESSION['SYSTEM_ENTRY'] != USCIC_SMS && Config::useTransactions() == true) {
    $transdb = new Database();
    $transdb->beginTransaction();
}

ini_set("error_reporting", "ALL");

/* startup */
require('action.php');
require('login.php');

/* SMS admin extensions */
if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
    require('sms.php');
    require('sysadmin.php');
    require("compiler.php");
    require("checker.php");
    require("track.php");
    require('supervisor.php');
    require('lab.php');
    require('nurse.php');
    require('translator.php');
    require('researcher.php');    
}

/* SMS admin and survey extensions */
if (Config::smsUsage()) {
    require('interviewer.php');
    require('remarks.php');    
}

if (isTestmode() || Config::smsUsage()) {
    require('user.php');
    require('users.php');
    require('tester.php');
}

/* core objects */
require('object.php');
require('component.php');
require('basicengine.php');
require('basicfill.php');
require('basicinlinefield.php');
require('basiccheck.php');
require('variable.php');
require('variabledescriptive.php');
require('setting.php');
require('progressbar.php');
require('section.php');
require('type.php');
require('group.php');
require('state.php');
require('logaction.php');
require('logactions.php');
require('datarecord.php');
require('survey.php');
require('surveys.php');
require("languagebase.php");

/* SMS admin and survey extensions */
if (Config::smsUsage()) {
    require('households.php');
    require('household.php');
    require('respondents.php');
    require('respondent.php');
}

/* core display */
require('display/display.php');
require('display/displaylogin.php');
require('display/displayquestion.php');
require('templates/default.php');
require('templates/tabletemplate.php');
require('templates/multicolumntable.php');

/* core SMS in survey display */
require('display/displayrespondent.php'); // only core if SMS is used
require('display/displayinterviewer.php'); // only core if SMS is used
require('display/displaytester.php'); // only core if SMS is used

/* error checking */
require("errorcheck.php");
require("errorchecks.php");

/* answer type add-ons */
require('gps.php');
require('customfunctions.php');
require('customanswertypes.php');

/* SMS extensions */
if (Config::smsUsage()) {
    require('display/displayloginsms.php');
    require('display/displaysysadmin.php');
    require('display/displayoutput.php');
    require('display/displayusers.php');
    require('display/displaysupervisor.php');
    require('display/displaytranslator.php');
    require('display/displaysearch.php');
    require('display/displaysms.php');
    //require('display/displaynurse.php');
    require('display/displayresearcher.php');    
    require("data.php");
    require('dataexport.php');
    require('communication.php');
}

/* SMS admin and survey extensions */
if (Config::smsUsage()) {
    require('psu.php');
    require('psus.php');
    require('proxypermission.php');    
}

if (isTestmode() || Config::smsUsage()) {
    require('contact.php');
    require('contacts.php');    
}

/* check for execution mode */
if (inArray(loadvar(POST_PARAM_SURVEY_EXECUTION_MODE), array(SURVEY_EXECUTION_MODE_NORMAL, SURVEY_EXECUTION_MODE_TEST))) {
    $_SESSION[SURVEY_EXECUTION_MODE] = loadvar(POST_PARAM_SURVEY_EXECUTION_MODE);
}

if (!isset($_SESSION[SURVEY_EXECUTION_MODE])) {
    $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL; // by default normal mode
}

// set timezone
date_default_timezone_set(Config::timezone());
$logActions = new LogActions();

/* global variables */
$suid = getSurvey();
$survey = new Survey($suid);

/* set the template for the questions display */
require('displayquestionsms.php');
require('displayquestiontest.php');
require('displayquestionnurse.php');

/* js shrinker */
if (Config::useDynamicMinify()) {
    require('jshrink/minifier.php');
}

$mode = null; // wait with calling this until later!
$modechange = null;
$version = null; // wait with calling this until later!
$language = null; // wait with calling this until later!
$languagechange = null;
$template= null; // wait with calling this until later!
$templatechange = null;
$currentseid = null;
$currentmainseid = null;
$baseseid = null;
$defaultlanguage = null; //getDefaultSurveyLanguage();
$defaultmode = null; //getDefaultSurveyMode();

/* testing stuff */
//$queries = array();

?>