<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman 

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Action {

    var $phpid;

    // some test code

    function __construct($phpid) {
        $this->phpid = $phpid;
    }

    function getAction() {
        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
            return $this->SMSEntry();
        } else { //normal survey entry
            return $this->surveyEntry();
        }
        return Language::messageSurveyNotAccessible();
    }

    function SMSEntry() {
        
        // check for return from tester, in which case we need to switch back to normal mode to look in the correct actions table
        if (isset($_SESSION[SURVEY_EXECUTION_MODE]) && $_SESSION[SURVEY_EXECUTION_MODE] == SURVEY_EXECUTION_MODE_TEST) {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }
        $logactions = new LogActions();
        $nosessionactions = $logactions->getNumberOfActionsBySession($this->phpid, USCIC_SMS);
        if ($nosessionactions == 0) { //no entry yet: ask for prim_key!  
            $logactions->addAction('', '', "loginstart", USCIC_SMS);
            $login = new Login($this->phpid);
            return $login->getSMSLoginScreen();
        } else {
            $loggedin = $logactions->getLoggedInSMSSession($this->phpid);
            if ($loggedin["count"] == 0) { //no prim_key (=username in sms) assigned to this sessionid. Assign if given (and check for pwd etc??)!
                $username = loadvar('username');
                $_SESSION['username'] = $username;
                if ($username != '' && loadvar('password') != '') { //check username!!
                    $login = new Login($this->phpid);
                    if ($login->checkSMSAccess()) {
                        $urid = $_SESSION['URID'];                        
                        $logactions->addAction('', $urid, "loggedin", USCIC_SMS, '1', false);
                        $sms = new SMS($urid, $this->phpid);
                        return $sms->getPage();
                    } else {
                        // incorrect login..start new session
                        endSession();
                        session_start();
                        session_regenerate_id(true);
                        $logactions->addAction('', '', "loginempty", USCIC_SMS, '1', false);
                        $login = new Login(session_id());
                        return $login->getSMSLoginScreen(Language::messageCheckUsernamePassword());
                    }
                } else {
                    $logactions->addAction('', '', "loginempty", USCIC_SMS, '1', false);
                    $login = new Login($this->phpid);
                    return $login->getSMSLoginScreen(Language::messageEnterUsernamePassword());
                }
            } else { //continue with the sms! EXTRA CHECK!!!!
                $_SESSION['URID'] = $loggedin["urid"];
                if (isset($_SESSION['URID'])) {
                    $sms = new SMS($_SESSION['URID'], $this->phpid);
                    return $sms->getPage();
                } else { //something went wrong.. no urid..start new session
                    endSession();
                    session_start();
                    session_regenerate_id(true);
                    $logactions->addAction('', '', "loginempty", USCIC_SMS, '1', false);
                    $login = new Login(session_id());
                    return $login->getSMSLoginScreen(Language::messageCheckUsernamePassword());
                }
            }
        }
    }

    function checkDateTime() {
        global $survey;

        $fromdate = $survey->getAccessDatesFrom();
        $todate = $survey->getAccessDatesTo();
        $fromtime = $survey->getAccessTimesFrom();
        $totime = $survey->getAccessTimesTo();

        if ($fromdate == "" && $todate == "" && $fromtime == "" && $totime == "") {
            return true;
        }

        if ($fromdate != "") {
            $fromdate = strtotime($fromdate . " 00:00:00");
            if ($fromdate > strtotime(date("Y-m-d H:i:s"))) {
                return false;
            }
        }

        if ($todate != "") {
            $todate = strtotime($todate . " 23:59:59");
            if ($todate < strtotime(date("Y-m-d H:i:s"))) {
                return false;
            }
        }

        if ($fromtime != "") {
            if (endsWith($fromtime, " AM")) {
                $fromtime = strtotime(date("Y-m-d") . ' ' . str_replace(" AM", "", $fromtime));
            } else if (endsWith($fromtime, " PM")) {
                $fromtime = strtotime(date("Y-m-d") . ' ' . str_replace(" PM", "", $fromtime) . "+12 hours");
            } else {
                $fromtime = strtotime(date("Y-m-d") . ' ' . $fromtime);
            }
            //$fromtime = strtotime(date("Y-m-d") . " " . $fromtime);
            if ($fromtime > strtotime(date("Y-m-d H:i:s"))) {
                return false;
            }
        }

        if ($totime != "") {
            if (endsWith($totime, " AM")) {
                $totime = strtotime(date("Y-m-d") . ' ' . str_replace(" AM", "", $totime));
            } else if (endsWith($fromtime, " PM")) {
                $totime = strtotime(date("Y-m-d") . ' ' . str_replace(" PM", "", $totime) . "+12 hours");
            } else {
                $totime = strtotime(date("Y-m-d") . ' ' . $totime);
            }
            //$fromtime = strtotime(date("Y-m-d") . " " . $fromtime);
            if ($totime < strtotime(date("Y-m-d H:i:s"))) {
                return false;
            }
        }

        return true;
    }

    function surveyEntry() {
        global $engine;        
        if (!isTestmode() && $this->checkDateTime() == false) {

            /* get whatever the language is (either post or default) and use it */
            $l = getSurveyLanguage();
            if (file_exists("language/language" . getSurveyLanguagePostFix($l) . ".php")) {
                require_once('language' . getSurveyLanguagePostFix($l) . '.php'); // language  
            } else {
                require_once('language_en.php'); // fall back on english language file
            }
            $login = new Login(session_id());
            return $login->getClosedScreen();
        }

        $logactions = new LogActions();
        $nosessionactions = $logactions->getNumberOfSurveyActionsBySession($this->phpid, USCIC_SURVEY);

        /* no entry yet, then ask for prim_key in login screen */
        if ($nosessionactions == 0 || loadvarSurvey(POST_PARAM_NEW_PRIMKEY) == '1') { //no entry yet: ask for prim_key!
            if (loadvarSurvey(POST_PARAM_NEW_PRIMKEY) == '1') {
                $logactions->deleteLoggedInSurveySession($this->phpid);
                
                // clear current session
                $_SESSION = array();
		session_destroy();
		session_start();

                /* set execution mode again */
                if (inArray(loadvar(POST_PARAM_SURVEY_EXECUTION_MODE), array(SURVEY_EXECUTION_MODE_NORMAL, SURVEY_EXECUTION_MODE_TEST))) {
                    $_SESSION[SURVEY_EXECUTION_MODE] = loadvar(POST_PARAM_SURVEY_EXECUTION_MODE);
                }
                if (!isset($_SESSION[SURVEY_EXECUTION_MODE])) {
                    $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL; // by default normal mode
                }
            }
            /* get whatever the language is (either post or default) and use it */
            $l = getSurveyLanguage();
            if (file_exists("language/language" . getSurveyLanguagePostFix($l) . ".php")) {
                require_once('language' . getSurveyLanguagePostFix($l) . '.php'); // language  
            } else {
                require_once('language_en.php'); // fall back on english language file
            }

            $logactions->addAction('', '', "loginstart", USCIC_SURVEY, 1);
            $login = new Login($this->phpid);
            return $login->getLoginScreen();
        } else { //entry: is this person logged in?   
            
            $loggedin = $logactions->getLoggedInSurveySession($this->phpid); // gets the last logged in action            

            /* no prim_key assigned to this sessionid. Assign if given (and check for pwd etc??)! */
            if ($loggedin["count"] == 0) {

                /* we don't have active session, so take the template we can get */
                global $survey;  
                
                require_once("display/templates/displayquestion_" . getSurveyTemplate() . ".php");

                // we don't have an active session, so fall back to whatever was passed along as language in post OR is the default language
                $l = getSurveyLanguage();
                if (file_exists("language/language" . getSurveyLanguagePostFix($l) . ".php")) {
                    require_once('language' . getSurveyLanguagePostFix($l) . '.php'); // language  
                } else {
                    require_once('language_en.php'); // fall back on english language file
                }

                $primkey = loadvarSurvey(POST_PARAM_PRIMKEY);
                $_SESSION['PRIMKEY'] = $primkey;
                if ($primkey != '' && strlen($primkey) < 20) { // make sure primkey is not encrypted!
                    //check!!!!!!
                    
                    $login = new Login($this->phpid);
                    if ($login->checkAccess()) {
                        $primkey = $_SESSION['PRIMKEY'];
                        $logactions->addAction($primkey, '', "loggedin", USCIC_SURVEY, 1);

                        // pass along primkey to load correct engine!
                        $engine = loadEngine(getSurvey(), $primkey, $this->phpid, getSurveyVersion(), getSurveySection(getSurvey(), $primkey));                        
                        $engine->setFirstForm(true);                        
                        return $engine->getNextQuestion();
                    } else {
                        // incorrect login..start new session
                        endSession();
                        session_start();
                        session_regenerate_id(true);
                        $logactions->addAction('', '', "loginempty", USCIC_SURVEY, 1);
                        $login = new Login(session_id());
                        global $survey;
                        return $login->getLoginScreen($survey->getLoginError());
                    }
                } else {

                    $logactions->addAction('', '', "loginempty", USCIC_SURVEY, 1);
                    $login = new Login($this->phpid);
                    global $survey;
                    if ($survey->getAccessType() == LOGIN_ANONYMOUS) {
                        return $login->getLoginScreen(Language::messageEnterPrimKey());
                    } else if ($survey->getAccessType() == LOGIN_LOGINCODE) {
                        return $login->getLoginScreen($survey->getLoginError());
                    } else {
                        return $login->getLoginScreen(Language::messageEnterPrimKeyDirectAccess());
                    }
                }
            } else { //continue interview! EXTRA CHECK!!! 

                /* update survey info with what we know from the last session action */
                setSurvey($loggedin["suid"]);

                /* include survey template now that we know which survey we are in */
                global $survey;                
                require_once("display/templates/displayquestion_" . getSurveyTemplate() . ".php");

                /* update interview mode with what we know from the last session action
                 * IF we are not changing the interview mode right now
                 */
                if (isset($_POST['navigation']) && $_POST['navigation'] != NAVIGATION_MODE_CHANGE && $survey->getReentryMode() == MODE_REENTRY_YES) {
                    setSurveyMode($loggedin["mode"]);
                }

                /* update language with what we know from the last session action 
                 * IF we are not changing the language right now
                 */
                if (isset($_POST['navigation']) && $_POST['navigation'] != NAVIGATION_LANGUAGE_CHANGE && $survey->getReentryLanguage(getSurveyMode()) == LANGUAGE_REENTRY_YES) {
                    setSurveyLanguage($loggedin["language"]);
                }
                
                /* update version with what we know from the last session action */
                setSurveyVersion($loggedin["version"]);

                // include language file
                $l = getSurveyLanguage();
                if (file_exists("language/language" . getSurveyLanguagePostFix($l) . ".php")) {
                    require_once('language' . getSurveyLanguagePostFix($l) . '.php'); // language  
                } else {
                    require_once('language_en.php'); // fall back on english language file
                }

                // pass along primkey to load correct engine!                
                $engine = loadEngine(getSurvey(), $loggedin["primkey"], $this->phpid, getSurveyVersion(), getSurveySection(getSurvey(), $loggedin["primkey"]));                

                /* handle button click */
                return $engine->getNextQuestion();
            }
        }
    }

}

?>