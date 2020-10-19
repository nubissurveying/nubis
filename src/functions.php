<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

function loadvar($var, $default = "") {

// checks if $$var is known as post_var, get_var or cookie
    //global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS;
    // array

    if (isset($_POST[$var]) && is_array($_POST[$var])) {
        $arr = array();
        foreach ($_POST[$var] as $v) {
            $arr[] = prepareLoadedString($v);
        }
        return $arr;
    }

    // not array
    if (isset($_POST[$var]) && $_POST[$var] != "") {
        return (prepareLoadedString($_POST[$var]));
    } elseif (isset($_GET[$var]) && $_GET[$var] != "") {
        return (prepareLoadedString($_GET[$var]));
    } elseif (isset($_COOKIE[$var])) {
        return (prepareLoadedString($_COOKIE[$var]));
    } else {
        return $default;
    }
}

function loadvarAllowHTML($var, $default = "") {

    // checks if $var is known as post_var, get_var or cookie
    // array
    if (isset($_POST[$var]) && is_array($_POST[$var])) {
        $arr = array();
        foreach ($_POST[$var] as $v) {
            $arr[] = prepareLoadedString($v, false);
        }
        return $arr;
    }

    // not array
    if (isset($_POST[$var]) && $_POST[$var] != "") {
        return (prepareLoadedString($_POST[$var], false));
    } elseif (isset($_GET[$var]) && $_GET[$var] != "") {
        return (prepareLoadedString($_GET[$var], false));
    } elseif (isset($_COOKIE[$var])) {
        return (prepareLoadedString($_COOKIE[$var], false));
    } else {
        return $default;
    }
}

function loadvarSurvey($var, $default = "") {

// checks if $$var is known as post_var, get_var or cookie
    //global $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS;
    // array

    if (isset($_POST[$var]) && is_array($_POST[$var])) {
        $arr = array();
        foreach ($_POST[$var] as $v) {
            $arr[] = prepareLoadedStringSurvey($v);
        }
        return $arr;
    }

    // not array
    if (isset($_POST[$var]) && $_POST[$var] != "") {
        return (prepareLoadedStringSurvey($_POST[$var]));
    } elseif (isset($_GET[$var]) && $_GET[$var] != "") {
        return (prepareLoadedStringSurvey($_GET[$var]));
    } elseif (isset($_COOKIE[$var])) {
        return (prepareLoadedStringSurvey($_COOKIE[$var]));
    } else {
        return $default;
    }
}

function getClientIp() {

    $ipaddress = '';

    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');

    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');

    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');

    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');

    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');

    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';



    return $ipaddress;
}

function generateRandomPrimkey($length = 8) {
    $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }
    return $result;
}

/* javascript functions */

function determinedJavascriptEnabled() {

    return isset($_SESSION[JAVASCRIPT_INDICATOR]);
}

function isJavascriptEnabled() {



    // check if we have a javascript indicator

    if (loadvar(JAVASCRIPT_CHOSEN) == 1) {

        if (loadvar(JAVASCRIPT_INDICATOR) == 1) {

            $_SESSION[JAVASCRIPT_INDICATOR] = false;
        } else {

            $_SESSION[JAVASCRIPT_INDICATOR] = true;
        }
    }



    if (isset($_SESSION[JAVASCRIPT_INDICATOR])) {

        return $_SESSION[JAVASCRIPT_INDICATOR];
    }



    return true;
}

$registeredscripts = array();

function getScript($script) {
    if (contains(getURL(), "/tester")) {
        return '<script type="text/javascript" src="../' . $script . '"></script>';
    }
    return '<script type="text/javascript" src="' . $script . '"></script>';
}

function getCss($css) {
    if (contains(getURL(), "/tester")) {
        return '<link type="text/css" rel="stylesheet" href="../' . $css . '">';
    }
    return '<link type="text/css" rel="stylesheet" href="' . $css . '">';
}

function registerScript($script) {
    global $registeredscripts;
    $registeredscripts[] = strtoupper($script);
}

function isRegisteredScript($script) {
    global $registeredscripts;
    if (inArray($script, $registeredscripts)) {
        return true;
    }
    return false;
}

function minifyScript($str) {
    if (Config::useDynamicMinify() == false) {
        return $str;
    }
    return \JShrink\Minifier::minify($str);
}

/* survey functions */

function doCommit() {
    if (Config::useTransactions() == true) {
        global $transdb;
        if ($transdb) {
            $transdb->commitTransaction();
        }
    }
}

function loadProgressBar($suid, $seid, $version) {

    global $db;

    $q = "select progressbar from " . Config::dbSurvey() . "_engines where suid=" . $suid . " and seid=" . $seid . " and version=" . $version;

    $r = $db->selectQuery($q);

    if ($row = $db->getRow($r)) {

        if ($row["progressbar"] != "") {

            return unserialize(gzuncompress($row["progressbar"]));
        }
    }

    return null;
}

function loadSetFillClasses($suid, $seid, $version) {
    global $db;
    $q = "select setfills from " . Config::dbSurvey() . "_engines where suid=" . $suid . " and seid=" . $seid . " and version=" . $version;
    $r = $db->selectQuery($q);
    if ($row = $db->getRow($r)) {
        if ($row["setfills"] != "") {
            return unserialize(gzuncompress($row["setfills"]));
        }
    }
    return null;
}

function loadEngine($suid, $primkey, $phpid, $version, $seid, $doState = true, $doContext = true) {



    /* check if we loaded it before */

    global $db;

    $enginename = CLASS_ENGINE . $seid;

    try {

        $engineclass = new ReflectionClass($enginename);

        if ($engineclass) {

            return $engineclass->newInstance($suid, $primkey, $phpid, $version, $seid, $doState, $doContext);
        }
    } catch (Exception $e) {
        
    }

    $q = "select engine from " . Config::dbSurvey() . "_engines where suid=" . $suid . " and seid=" . $seid . " and version=" . $version;
    $r = $db->selectQuery($q);
    $code = "";

    // get compiled code
    if ($row = $db->getRow($r)) {
        ob_start();
        $code = unserialize(gzuncompress($row["engine"]));
    } 
    // no compiled code found, create empty engine to continue
    else {
        if ($code == "") {
            $code = "class " . CLASS_ENGINE . $seid . " extends BasicEngine
{
    protected function doAction(\$rgid = '')
    {
        switch (\$rgid) {
            default:
                \$this->doEnd();
                break;
        }
    }
    public function getFirstAction()
    {
        
    }
}";
        }
    }
    
    // interpret compiled code
    eval($code);

    // get any error messages
    $contents = ob_get_clean();
    if ($contents != "") {

        if (stripos($contents, "error") !== false) {
            echo Language::messageSurveyUnavailable();
            doExit();
        }
    }

    $enginename = CLASS_ENGINE . $seid;
    try {
        $engineclass = new ReflectionClass($enginename);

        if ($engineclass) {
            return $engineclass->newInstance($suid, $primkey, $phpid, $version, $seid, $doState, $doContext);
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    //}

    /* something went wrong */

    //echo Language::messageSurveyUnavailable();
    //doExit();
}

/* version functions */

function setSurveyVersion($l) {
    return; //TODO
    if (isSurveyVersion($l)) {
        global $version;
        $version = $l;
    }
}

function getSurveyVersion($survey = "") {

    /* SMS */
    if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
        if ($survey == "") {
            global $survey;
        }
        return $survey->getCurrentVersion();
    }

    /* SURVEY */
    global $version;

    /* check here for session/data/or something */
    $version = 1;
    return $version;
}

/* execution mode functions */

function getSurveyExecutionMode() {
    if (isset($_SESSION[SURVEY_EXECUTION_MODE])) {
        return $_SESSION[SURVEY_EXECUTION_MODE];
    }
    return SURVEY_EXECUTION_MODE_NORMAL;
}

/* mode functions */

function getDefaultSurveyMode() {
    global $defaultmode;
    if ($defaultmode != null) {
        return $defaultmode;
    }

    global $survey;
    $defaultmode = $survey->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_MODE)->getValue(); // $survey->getDefaultMode(); //
    return $defaultmode;
}

function isSurveyMode($l) {

    if (trim($l) == "" || !is_numeric($l)) {
        return false;
    }

    /* forego checks below */
    if (Config::checkComponents() == false) {
        return true;
    }

    global $mode;
    if ($l == $mode) {
        return true;
    }

    // check in modes
    return inArray($l, array_keys(Common::surveyModes()));
}

function getSurveyModeAllowChange() {

    global $survey, $modechange;

    // not retrieved before
    if ($modechange == "") {
        $modechange = $survey->getChangeMode();
    }

    if ($modechange == "") {
        $modechange = MODE_CHANGE_PROGRAMMATIC_ALLOWED;
    }

    return $modechange;
}

function setSurveyMode($l, $flooding = false) {
    global $mode;
    if ($mode == $l) {
        return;
    }
    if (getSurveyModeAllowChange() != MODE_CHANGE_NOTALLOWED || $flooding) {
        if (isSurveyMode($l)) {
            $mode = $l;
        }
    }
}

function getSurveyMode() {

    /* SMS */
    if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
        if (loadvar(SMS_POST_MODE) != '') {
            $l = loadvar(SMS_POST_MODE);
            if (isSurveyMode($l)) {
                $_SESSION['SURVEY_MODE'] = $l;
                return $_SESSION['SURVEY_MODE'];
            }
        }

        if (isset($_SESSION['SURVEY_MODE'])) {
            if (isSurveyMode($_SESSION['SURVEY_MODE'])) {
                return $_SESSION['SURVEY_MODE'];
            }
        }

        $user = new User($_SESSION['URID']);
        $utype = $user->getUserType();
        switch ($utype) {
            case USER_SYSADMIN:
                $default = getDefaultSurveyMode();
                $_SESSION['SURVEY_MODE'] = $default;
                return $default;
            case USER_TRANSLATOR:
                $modes = $user->getModes(getSurvey());
                $default = getDefaultSurveyMode();
                if (inArray($default, $modes)) {
                    $_SESSION['SURVEY_MODE'] = $default;
                    return $default;
                }
                $_SESSION['SURVEY_MODE'] = $modes[0];
                return $modes[0];
            default:
                $default = getDefaultSurveyMode();
                $_SESSION['SURVEY_MODE'] = $default;
                return $default;
        }
    }

    /* SURVEY */

    // check for new mode      
    global $engine, $mode;

    /* global mode has been set! (via one of the options below, so no need to repeat) */
    if (isSurveyMode($mode)) {
        return $mode;
    }

    /* get from loadvar (IF ALLOWED) */
    if (getSurveyModeAllowChange() != MODE_CHANGE_NOTALLOWED) {
        $l = loadvarSurvey(POST_PARAM_MODE);
        if (isSurveyMode($l)) {
            $mode = $l;
            $_SESSION["PARAMS"][SESSION_PARAM_MODE] = $l;
            return $mode;
        }

        $l = getFromSessionParams(SESSION_PARAM_NEWMODE);
        if (isSurveyMode($l)) {
            $_SESSION["PARAMS"][SESSION_PARAM_MODE] = $l;
            unset($_SESSION["PARAMS"][SESSION_PARAM_NEWMODE]);
            $mode = $l;
            return $l;
        }
    }

    // check for old mode
    $l = getFromSessionParams(SESSION_PARAM_MODE);
    if (isSurveyMode($l)) {
        $mode = $l;
        return $l;
    }

    // default mode
    $l = getDefaultSurveyMode();
    if (isSurveyMode($l)) {
        $mode = $l;
        return $mode;
    }

    /* everything failed */
    $mode = MODE_CASI;
    return $mode;
}

/* survey functions */

function isSurvey($l) {

    if (trim($l) == "" || !is_numeric($l)) {
        return false;
    }

    /* forego checks below */
    if (Config::checkComponents() == false) {
        return true;
    }

    // if equal to current suid, then no need to check
    // (we already did so before, otherwise suid would not have been set)
    global $suid;
    if ($l == $suid) {
        return true;
    }

    // check if real survey
    $sv = new Surveys();
    $surveys = $sv->getSurveyIdentifiers();
    foreach ($surveys as $surv) {
        if ($surv == $l) {
            return true;
        }
    }

    return false;
}

function getDefaultSurvey() {
    global $defaultsurvey;
    if ($defaultsurvey != null) {
        return $defaultsurvey;
    }

    // check if set on survey
    $surveys = new Surveys();
    $surveys = $surveys->getSurveys();
    foreach ($surveys as $s) {
        if ($s->getDefaultSurvey() == DEFAULT_SURVEY_YES) {
            $defaultsurvey = $s->getSuid();
            return $defaultsurvey;
        }
    }

    // not set on any survey, then fall back on global one
    $survey = new Survey(); // not through global survey object, since this method is called if no suid was given as post/get variable
    $defaultsurvey = $survey->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_SURVEY)->getValue();
    return $defaultsurvey;
}

function setSurvey($l) {
    global $suid, $survey;
    if ($suid == $l && $survey->getSuid() == $l) {
        return;
    }
    if (isSurvey($l)) {
        $suid = $l;
        $survey = new Survey($suid);
    }
}

function getSurvey() {

    /* SMS */
    if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
        if (loadvar(SMS_POST_SURVEY) != "") {
            $_SESSION['SUID'] = loadvar(SMS_POST_SURVEY);
        }

        if (isset($_SESSION['SUID'])) {
            return $_SESSION['SUID'];
        }

        $surveys = new Surveys();
        $suid = $surveys->getFirstSurvey(true);
        if (isSurvey($l)) {
            $_SESSION['SUID'] = $suid;
            return $suid;
        }
        $_SESSION['SUID'] = 1;
        return 1;
    }

    /* SURVEY */

    // check for new survey  
    global $engine, $suid;


    /* get from loadvar */
    $l = loadvarSurvey(POST_PARAM_SUID);
    if (isSurvey($l)) {
        $suid = $l;
        $_SESSION["PARAMS"][SESSION_PARAM_SURVEY] = $l;
        return $suid;
    }

    $l = getFromSessionParams(SESSION_PARAM_NEWSURVEY);
    if (isSurvey($l)) {
        $_SESSION["PARAMS"][SESSION_PARAM_SURVEY] = $l;
        unset($_SESSION["PARAMS"][SESSION_PARAM_NEWSURVEY]);
        $suid = $l;
        return $suid;
    }

    // check for old survey
    $l = getFromSessionParams(SESSION_PARAM_SURVEY);
    if (isSurvey($l)) {
        $suid = $l;
        $_SESSION["PARAMS"][SESSION_PARAM_SURVEY] = $l;
        return $suid;
    }

    /* global suid has been set (via setting below, so no need to repeat) */
    if (isSurvey($suid)) {
        return $suid;
    }

    /* check for default survey */
    $l = getDefaultSurvey();
    if (isSurvey($l)) {
        $suid = $l;
        $_SESSION["PARAMS"][SESSION_PARAM_SURVEY] = $l;
        return $suid;
    }

    /* everything else failed */
    $surveys = new Surveys();
    $suid = $surveys->getFirstSurvey(true);
    if ($suid == "") {
        $display = new Display();
        echo $display->displayError(Language::messageSurveyUnavailable());
        doExit();
    }

    $_SESSION["PARAMS"][SESSION_PARAM_SURVEY] = $suid;
    return $suid;
}

/* language functions */



/* sms language functions */

function isSMSLanguage($l) {

    if (trim($l) == "") {

        return false;
    }



    // check in languages array

    $langs = LanguageBase::getSMSLanguagesArray();

    foreach ($langs as $lang) {

        if ($lang["value"] == $l) {

            return true;
        }
    }

    return false;
}

function getSMSLanguage() {



    /* SMS */

    if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {

        if (loadvar("smslanguage") != '') {

            $l = loadvar("smslanguage");

            if (isSMSLanguage($l)) {

                $_SESSION['SMS_LANGUAGE'] = $l;

                return $_SESSION['SMS_LANGUAGE'];
            }
        }

        if (isset($_SESSION['SMS_LANGUAGE'])) {

            if (isSMSLanguage($_SESSION['SMS_LANGUAGE'])) {

                return $_SESSION['SMS_LANGUAGE'];
            }
        }

        /* check db */

        return 1; // default is English
    }
}

function getSMSLanguagePostFix($l) {

    $langs = LanguageBase::getSMSLanguagesArray();

    foreach ($langs as $key => $lang) {

        if ($lang["value"] == $l) {

            return $key;
        }
    }

    return 'en'; // english
}

/* survey language functions */

function isSurveyLanguage($l) {

    if (trim($l) == "" || !is_numeric($l)) {
        return false;
    }

    /* forego checks below */
    if (Config::checkComponents() == false) {
        return true;
    }

    global $language;
    if ($l == $language) {
        return true;
    }

    // check in languages array
    $langs = LanguageBase::getLanguagesArray();
    foreach ($langs as $lang) {
        if ($lang["value"] == $l) {
            //if (file_exists("/languages/language" . getSurveyLanguagePostFix($l) . ".php")) {
            return true;
            //}
        }
    }
    return false;
}

function getDefaultSurveyLanguage() {

    global $defaultlanguage;

    if ($defaultlanguage != "") {

        return $defaultlanguage;
    }

    global $survey;

    $defaultlanguage = $survey->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_LANGUAGE, getSurveyMode())->getValue();



    // if empty, then try with default mode

    if ($defaultlanguage == "") {

        $defaultlanguage = $survey->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_LANGUAGE, getDefaultSurveyMode())->getValue();
    }

    return $defaultlanguage;
}

function getSurveyLanguageAllowChange() {

    global $survey, $languagechange;

    // not retrieved before
    if ($languagechange == "") {
        $languagechange = $survey->getChangeLanguage(getSurveyMode()); //getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_LANGUAGE, getSurveyMode())->getValue();
    }

    if ($languagechange == "") {
        $languagechange = LANGUAGE_CHANGE_PROGRAMMATIC_ALLOWED;
    }

    return $languagechange;
}

function setSurveyLanguage($l, $flooding = false) {
    global $language;
    if ($language == $l) {
        return;
    }
    if (getSurveyLanguageAllowChange() != LANGUAGE_CHANGE_NOTALLOWED || $flooding == true) {
        if (isSurveyLanguage($l)) {
            $language = $l;
        }
    }
}

function switchSurveyLanguageTranslator($l) {
    if (isSurveyLanguage($l)) {
        $_SESSION['SURVEY_LANGUAGE'] = $l;
    }
}

function getSurveyLanguage() {

    /* SMS */
    if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
        if (loadvar(SMS_POST_LANGUAGE) != '') {
            $l = loadvar(SMS_POST_LANGUAGE);
            if (isSurveyLanguage($l)) {
                $_SESSION['SURVEY_LANGUAGE'] = $l;
                return $_SESSION['SURVEY_LANGUAGE'];
            }
        }

        if (isset($_SESSION['SURVEY_LANGUAGE'])) {
            if (isSurveyLanguage($_SESSION['SURVEY_LANGUAGE'])) {
                return $_SESSION['SURVEY_LANGUAGE'];
            }
        }

        /* check user */
        $user = new User($_SESSION['URID']);
        $utype = $user->getUserType();
        switch ($utype) {
            case USER_SYSADMIN:
                $default = getDefaultSurveyLanguage();
                $_SESSION['SURVEY_LANGUAGE'] = $default;
                return $default;
            case USER_TRANSLATOR:
                $languages = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
                $default = getDefaultSurveyLanguage();
                if (inArray($default, $languages)) {
                    $_SESSION['SURVEY_LANGUAGE'] = $default;
                    return $default;
                }

                $_SESSION['SURVEY_LANGUAGE'] = $languages[0];
                return $languages[0];
            default:
                $default = getDefaultSurveyLanguage();
                $_SESSION['SURVEY_LANGUAGE'] = $default;
                return $default;
        }
    }

    /* SURVEY */

    // check for new language  
    global $engine, $language;

    /* global language has been set! (via setting below, so no need to repeat) */
    if (isSurveyLanguage($language)) {
        return $language;
    }

    /* get from loadvar (IF ALLOWED) */
    if (getSurveyLanguageAllowChange() != LANGUAGE_CHANGE_NOTALLOWED) {
        $l = loadvarSurvey(POST_PARAM_LANGUAGE);
        if (isSurveyLanguage($l)) {
            $language = $l;
            $_SESSION["PARAMS"][SESSION_PARAM_LANGUAGE] = $l;
            return $language;
        }

        $l = getFromSessionParams(SESSION_PARAM_NEWLANGUAGE);
        if (isSurveyLanguage($l)) {
            $_SESSION["PARAMS"][SESSION_PARAM_LANGUAGE] = $l;
            unset($_SESSION["PARAMS"][SESSION_PARAM_NEWLANGUAGE]);
            $language = $l;
            return $l;
        }
    }

    // check for old language from session
    $l = getFromSessionParams(SESSION_PARAM_LANGUAGE);

    if (isSurveyLanguage($l)) {
        $language = $l;
        return $l;
    }

    // default language
    $l = getDefaultSurveyLanguage();
    if (isSurveyLanguage($l)) {
        $language = $l;
        return $language;
    }

    /* everything else failed */
    $language = 1; // english
    return 1;
}

function getSurveyLanguagePostFix($l) {
    $langs = LanguageBase::getLanguagesArray();
    foreach ($langs as $key => $lang) {
        if ($lang["value"] == $l) {
            return '_' . $key;
        }
    }
    return '_en'; // english
}

/* template functions */

function getSurveyTemplateAllowChange() {

    global $survey, $templatechange;

    // not retrieved before
    if ($templatechange == "") {
        $languagechange = $survey->getChangeTemplate(); //getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_LANGUAGE, getSurveyMode())->getValue();
    }

    if ($templatechange == "") {
        $templatechange = TEMPLATE_CHANGE_PROGRAMMATIC_ALLOWED;
    }

    return $templatechange;
}

function isSurveyTemplate($t) {

    // forgo any checks if existing section
    if (trim($t) == "" || !is_numeric($t)) {
        return false;
    }

    /* forego checks below */
    if (Config::checkComponents() == false) {
        return true;
    }

    global $template;
    if ($t == $template) {
        return true;
    }

    return inArray($l, array_keys(Common::surveyOverallTemplates()));
}

function getSurveyTemplate() {

    /* SURVEY */

    // check for new template  
    global $survey, $template;

    /* global template has been set! (via setting below, so no need to repeat) */
    if (isSurveyTemplate($template)) {           
        return $template;
    }

    /* get from loadvar (IF ALLOWED) */
    if (getSurveyTemplateAllowChange() != TEMPLATE_CHANGE_NOTALLOWED) {
        $l = loadvarSurvey(POST_PARAM_TEMPLATE);
        if (isSurveyTemplate($l)) {
            $template = $l;
            $_SESSION["PARAMS"][SESSION_PARAM_TEMPLATE] = $l;
            return $template;
        }

        $l = getFromSessionParams(SESSION_PARAM_NEWTEMPLATE);
        if (isSurveyTemplate($l)) {
            $_SESSION["PARAMS"][SESSION_PARAM_TEMPLATE] = $l;
            unset($_SESSION["PARAMS"][SESSION_PARAM_NEWTEMPLATE]);
            $template = $l;
            return $l;
        }

        // check in submitted answers
        $vars = splitString("/~/", getFromSessionParams(SESSION_PARAM_VARIABLES));
        if (inArray(VARIABLE_TEMPLATE, $vars)) {
            $cnt = 1;
            foreach ($vars as $var) {
                if (strtoupper($var) == strtoupper(VARIABLE_TEMPLATE)) {
                    $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                    if (isSurveyTemplate($answer)) {
                        $template = $answer;
                        $_SESSION["PARAMS"][SESSION_PARAM_TEMPLATE] = $template;
                        unset($_SESSION["PARAMS"][SESSION_PARAM_NEWTEMPLATE]);
                        return $answer;
                    }
                    break;
                }
            }
        }
    }

    // check for old template from session
    $l = getFromSessionParams(SESSION_PARAM_TEMPLATE);
    if (isSurveyTemplate($l)) {
        $template = $l;
        return $l;
    }

    // default template from survey    
    return $survey->getTemplate();
}

function setSurveyTemplate($l) {
    global $template;
    if ($template == $l) {
        return;
    }
    if (getSurveyTemplateAllowChange() != TEMPLATE_CHANGE_NOTALLOWED) {
        if (isSurveyTemplate($l)) {
            $template = $l;
        }
    }
}

/* section functions */

function getBaseSectionSeid($suid) {
    global $baseseid;
    if ($baseseid != "") {
        return $baseseid;
    }

    // check in _sections table
    global $db;
    $query = "select seid from " . Config::dbSurvey() . "_sections where suid=" . $suid . " and name='Base'";
    $res = $db->selectQuery($query);
    if ($res) {
        if ($db->getNumberOfRows($res) > 0) {
            $row = $db->getRow($res);
            return $row["seid"];
        }
    }

    // all failed, then assume 1
    return 1;
}

function getSurveySection($suid = "", $primkey = "") {

    /* declare */
    $seid = "";
    global $currentseid;

    /* returning to survey or starting */
    if (getFromSessionParams(SESSION_PARAM_RGID) == '') {

        /* check in session first (overrides last state) */
        $seid = getFromSessionParams(SESSION_PARAM_SEID);
        if (isSurveySection($seid)) {
            $currentseid = $seid;
            return $seid;
        }

        /* check for last state */
        global $db;
        $result = $db->selectQuery('select seid from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($suid) . '  and primkey = "' . prepareDatabaseString($primkey) . '" and mainseid=' . getSurveyMainSection($suid, $primkey) . ' order by stateid desc limit 0,1');

        /* we are re-entering */
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $seid = $row["seid"];
            if (isSurveySection($seid)) {
                $currentseid = $seid;
                return $seid;
            }
        }

        /* we are starting the survey and no session parameter, then assume root section */
        $currentseid = getBaseSectionSeid($suid);
        return $currentseid;
    }

    // in survey
    else {

        /* button action */
        if (isset($_POST['navigation'])) {


            /* back button */
            if ($_POST['navigation'] == Language::buttonBack()) {

                /* check for last state to determine which section we are going to */
                global $db;
                $result = $db->selectQuery('select seid from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($suid) . '  and primkey = "' . prepareDatabaseString($primkey) . '" and mainseid=' . getSurveyMainSection($suid, $primkey) . ' order by stateid desc limit 0,1');
                if ($db->getNumberOfRows($result) > 0) {
                    $row = $db->getRow($result);
                    $seid = $row["seid"];
                    if (isSurveySection($seid)) {
                        $currentseid = $seid;
                        return $seid;
                    }
                }
            }

            // update button 
            else if ($_POST['navigation'] == Language::buttonUpdate()) {

                /* section does not change, so return from session */
                $seid = getFromSessionParams(SESSION_PARAM_SEID);
                if (isSurveySection($seid)) {
                    $currentseid = $seid;
                    return $seid;
                }
            }

            // next/RF/DK
            else {

                /* section may change, but this is handled by the current section engine
                 * calling the nex section engine, so we keep the same section */
                $seid = getFromSessionParams(SESSION_PARAM_SEID);
                if (isSurveySection($seid)) {
                    $currentseid = $seid;
                    return $seid;
                }
            }
        }

        /* everything failed, then assume root section */
        $currentseid = getBaseSectionSeid($suid);
        return $currentseid;
    }

    /* check last state */
    $currentseid = getBaseSectionSeid($suid);
    return $currentseid;
}

function getSurveyMainSection($suid, $primkey) {

    /* declare */
    $seid = "";
    global $currentmainseid;

    /* returning to survey or starting */
    if (getFromSessionParams(SESSION_PARAM_RGID) == '') {

        /* check in session first (overrides last state) */
        $seid = getFromSessionParams(SESSION_PARAM_MAINSEID);
        if (isSurveySection($seid)) {
            $currentmainseid = $seid;
            return $seid;
        }

        /* check for last state */
        global $db;
        $result = $db->selectQuery('select mainseid from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($suid) . '  and primkey = "' . prepareDatabaseString($primkey) . '" order by stateid desc limit 0,1');

        /* we are re-entering */
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $seid = $row["mainseid"];
            if (isSurveySection($seid)) {
                $currentmainseid = $seid;
                return $seid;
            }
        }

        /* we are starting the survey and no session parameter, then assume root section */
        $currentmainseid = getBaseSectionSeid($suid);
        return $currentmainseid;
    }

    // in survey
    else {

        /* button action */
        if (isset($_POST['navigation'])) {

            /* back button */
            if ($_POST['navigation'] == Language::buttonBack()) {

                /* check for last state to determine which section we are going to */
                global $db;
                $result = $db->selectQuery('select mainseid from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($suid) . '  and primkey = "' . prepareDatabaseString($primkey) . '" order by stateid desc limit 0,1');
                if ($db->getNumberOfRows($result) > 0) {
                    $row = $db->getRow($result);
                    $seid = $row["mainseid"];
                    if (isSurveySection($seid)) {
                        $currentmainseid = $seid;
                        return $seid;
                    }
                }
            }

            // update button
            else if ($_POST['navigation'] == Language::buttonUpdate()) {

                /* section does not change, so return from session */
                $seid = getFromSessionParams(SESSION_PARAM_MAINSEID);
                if (isSurveySection($seid)) {
                    $currentmainseid = $seid;
                    return $seid;
                }
            }

            // next/RF/DK 
            else {

                /* section may change, but this is handled by the current section engine
                 * calling the nex section engine, so we keep the same section */
                $seid = getFromSessionParams(SESSION_PARAM_MAINSEID);
                if (isSurveySection($seid)) {
                    $currentmainseid = $seid;
                    return $seid;
                }
            }
        }

        /* everything failed, then assume root section */
        $currentmainseid = getBaseSectionSeid($suid);
        return $currentmainseid;
    }

    /* check last state */
    $currentmainseid = getBaseSectionSeid($suid);
    return $currentmainseid;
}

function isSurveySection($seid) {

    // forgo any checks if existing section
    if (trim($seid) == "" || !is_numeric($seid)) {
        return false;
    }

    /* forego checks below */
    if (Config::checkComponents() == false) {
        return true;
    }

    global $currentseid;
    if ($seid == $currentseid) {
        return true;
    }

    global $survey;
    $secs = $survey->getSectionIdentifiers();
    foreach ($secs as $sec) {
        if ($sec == $seid) {
            return true;
        }
    }

    return false;
}

/* string functions */

function convertHTLMEntities($str, $quotes = ENT_QUOTES, $encoding = "UTF-8") {
    return htmlentities($str, $quotes, $encoding);
}

// http://forums.devnetwork.net/viewtopic.php?f=1&t=50827
function reversenat($str_a, $str_b) {

    switch (strnatcasecmp($str_a, $str_b)) {

        case 0:

            $ret = 0;

            break;

        case 1:

            $ret = -1;

            break;

        case -1:

            $ret = 1;

            break;
    }

    return $ret;
}

function compareLength($a, $b) {

    if (strlen($a) > strlen($b)) {

        return true;
    }

    return false;
}

// http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions

function startsWith($haystack, $needle) {
    return $needle === "" || stripos($haystack, $needle) === 0;
}

// http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions

function endsWith($haystack, $needle) {

    return $needle === "" || strtoupper(substr($haystack, -strlen($needle))) === strtoupper($needle);
}

function equals($str, $str1) {
    if (strtoupper($str) == $str1) {
        return true;
    }
    return false;
}

// http://stackoverflow.com/questions/4366730/how-to-check-if-a-string-contains-specific-words

function contains($string, $find) {

    if (stripos($string, $find) !== false) {

        return true;
    }

    return false;
}

function splitString($pattern, $str, $flag = PREG_SPLIT_NO_EMPTY, $limit = -1) {
    return preg_split($pattern, $str, $limit, $flag);
}

function preparePattern($pattern) {

    $pattern = preg_quote($pattern);
    $arr = array("/", "*"); // seems these don't get backslashed by preg_quote

    foreach ($arr as $a) {
        $pattern = str_replace($a, '\\' . $a, $pattern);
    }

    return $pattern;
}

function prepareClassExtension($text) {
    $classextension = strtoupper(str_replace("[", "_", $text));
    $classextension = str_replace("]", "_", $classextension);
    $classextension = str_replace(".", "_", $classextension);
    $classextension = str_replace(",", "_", $classextension);
    $classextension = str_replace(" ", "", $classextension);
    $classextension = str_replace("'", "_", $classextension);
    $classextension = str_replace('"', "_", $classextension);
    //$classextension = str_replace("*", "TIMES", $classextension);
    $classextension = str_replace("+", "PLUS", $classextension);
    $classextension = str_replace("-", "MINUS", $classextension);
    $classextension = str_replace("%", "MOD", $classextension);
    $classextension = str_replace("/", "DIVIDE", $classextension);
    $classextension = str_replace(INDICATOR_FILL_NOVALUE, "", $classextension);
    return $classextension;
}

function getReferences($text, $indicator) {

    if (trim($text) == "") {
        return array();
    }

    $fills = array();
    $split = "(\\" . $indicator . ")";

    $fillList = preg_split($split, $text);
    for ($cnt = 1; $cnt < sizeof($fillList); $cnt++) {

        // set fill
        $fillRef = $fillList[$cnt];

        // get everything between [ and ] 
        // (adapted from http://stackoverflow.com/questions/10104473/php-capturing-text-between-square-brackets)
        $matches = array();
        preg_match("/\[(.*?)\]/", $fillList[$cnt], $matches);
        if (isset($matches[1])) {
            $fillRef = str_replace($matches[1], TEXT_RANDOM_FILL, $fillRef);
        }
        $fill = preg_split("([\s+\-%=<>(){}!?'\"&*~@#|/:;$,\r\n])", $fillRef);

        /* if ends with a dot, then remove it (possible leftover from 'Hi, My Name is ^FLName.') */
        $cnt1 = 0;
        while (endsWith($fill[0], ".")) {
            $fill[0] = substr($fill[0], 0, strrpos($fill[0], "."));
            $cnt1++;
            if ($cnt1 > 500) {
                break;
            }
        }
        if (isset($matches[1])) {
            $fills[] = str_replace(TEXT_RANDOM_FILL, $matches[1], $fill[0]);
        } else {
            $fills[] = $fill[0];
        }
    }

    return array_unique($fills);
}

function excludeText($rule, &$removed) {
    $pattern = '/[\'|\"][^\r\n\"\']*[\'|\"]/';
    $matches = array();
    preg_match_all($pattern, $rule, $matches);
    for ($i = 0; $i < sizeof($matches[0]); $i++) {
        $key = '~' . TEXT_RANDOM . $i . '~';
        $removed[$key] = $matches[0][$i];
        $rule = str_replace($matches[0][$i], $key, $rule);
    }
    return $rule;
}

function includeText($rule, $excluded) {
    foreach ($excluded as $key => $value) {
        $rule = str_replace($key, $value, $rule);
    }
    return $rule;
}

// Adapted from: http://stackoverflow.com/questions/8304191/regex-to-add-spacing-between-sentences-in-a-string-in-php
function hideModuleNotations($rule, $replace) {
    return preg_replace(array('/[.](?![\s0-9$])/'), array($replace), $rule);
    //return preg_replace(array('/(?<=[a-zA-Z])[.](?![\s$])/'), array($replace), $rule);
    //return preg_replace(array("/(\b\.\b)/"), array($replace), $rule);
}

function showModuleNotations($rule, $replace) {
    return str_replace($replace, ".", $rule);
}

function showModuleNotationsPreserve($rule, $replace) {
    return str_replace($replace, "." . '"."' . ".", $rule);
}

function prepareDatabaseString($string, $striptags = true) {
    if ($striptags) {
        $string = strip_tags($string);
    }
    return escapeString($string);
}

function prepareExportString($string) {
    $string = str_replace('"', EXPORT_PLACEHOLDER_QUOTE, $string);
    $string = str_replace(',', EXPORT_PLACEHOLDER_COMMA, $string);
    $string = str_replace("\r\n", EXPORT_PLACEHOLDER_LINEBREAK, $string);
    return $string;
}

function prepareImportString($val) {
    $val = str_replace('"', '', $val);
    $val = str_replace(EXPORT_PLACEHOLDER_QUOTE, '"', $val);
    $val = str_replace(EXPORT_PLACEHOLDER_COMMA, ',', $val);
    $val = str_replace(EXPORT_PLACEHOLDER_LINEBREAK, "\r\n", $val);
    return $val;
}

function escapeString($string) {
    global $db;
    if ($db) {
        return $db->escapeString($string);
    }
    return addslashes($string);
}

function prepareLoadedString($string, $striptags = true) {
    if ($striptags) {
        return strip_tags($string);
    }
    return $string;
}

function prepareLoadedStringSurvey($string) {
    return htmlspecialchars(strip_tags($string), ENT_COMPAT | ENT_HTML401, 'UTF-8', false); // no double encoding   
}

function replacePlaceHolders($array, $text) {

    foreach ($array as $k => $v) {

        $text = str_replace($k, $v, $text);
    }

    return $text;
}

function getInvalidSetString($var, $invalid) {

    if ($invalid == PLACEHOLDER_INVALIDSET_SELECTED) {

        return $invalid;
    }

    $subs = explode(SEPARATOR_COMPARISON, $invalid);
    foreach ($subs as $sub) {
        $expl = explode(",", $sub);
        $names = array();
        foreach ($expl as $e) {
            if (contains($e, "-")) {
                $sub = explode("-", $e);
                $subnames = array();
                for ($j = $sub[0]; $j <= $sub[1]; $j++) {
                    $subnames[] = $var->getOptionLabel($j);
                }
                $names[] = "{" . arrayToString($subnames) . "}";
            } else {
                $names[] = $var->getOptionLabel($e);
            }
        }
        $namestrings[] = arrayToString($names);
    }

    if (sizeof($namestrings) == 1) {
        return $namestrings[0];
    }

    return "(" . implode(") OR (", $namestrings) . ")";
}

function getInvalidSubsetString($var, $invalidsub) {

    if ($invalidsub == PLACEHOLDER_INVALIDSUBSET_SELECTED) {
        return $invalidsub;
    }

    $subs = explode(SEPARATOR_COMPARISON, $invalidsub);
    foreach ($subs as $sub) {
        $expl = explode(",", $sub);
        $names = array();
        foreach ($expl as $e) {
            if (contains($e, "-")) {
                $sub = explode("-", $e);
                $subnames = array();
                for ($j = $sub[0]; $j <= $sub[1]; $j++) {
                    $subnames[] = $var->getOptionLabel($j);
                }
                $names[] = "{" . arrayToString($subnames) . "}";
            } else {
                $names[] = $var->getOptionLabel($e);
            }
        }
        $namestrings[] = arrayToString($names);
    }

    if (sizeof($namestrings) == 1) {
        return $namestrings[0];
    }

    return "(" . implode(") OR (", $namestrings) . ")";
}

/* array functions */

function flatten($array, $prefix = '') {

    $result = array();

    foreach ($array as $key => $value) {

        if (is_array($value)) {

            $result = $result + flatten($value, $prefix . $key . ',');
        } else {

            $result[$prefix . $key] = $value;
        }
    }

    return $result;
}

function arrayToString($array, $connector = ",", $last = " and ") {

    $string = "";

    for ($i = 0; $i < sizeof($array); $i++) {

        if ($i > 0) {

            if (($i + 1) < sizeof($array)) {

                $string .= ", ";
            } else if (($i + 1) == sizeof($array)) {

                $string .= " and ";
            }
        }

        $string .= $array[$i];
    }

    return $string;
}

function generateRandom($array1, $array2) {
    if (is_array($array2) === false) {
        $array2 = explode('-', $array2);
    }
    if (is_array($array1) === false) {
        $array1 = explode('-', $array1);
    }
    foreach ($array1 as $key => $value) {
        if (in_array($value, $array2)) {
            unset($array1[$key]);
        }
    }
    $new_array = array_values($array1);
    shuffle($new_array);
    return $new_array[0];
}

function dataexportSort($arr1, $arr2) {
    //array("sectionposition" => $section->getPosition(), "seid" => $vd->getSeid(), "varposition" => $vd->getPosition(), "varname" => strtoupper($vd->getName()), "vars" => $arr);

    if ($arr1["order"] < $arr2["order"]) {
        return -1;
    }
    if ($arr1["order"] > $arr2["order"]) {
        return 1;
    }

    // one section before another
    if ($arr1["sectionposition"] < $arr2["sectionposition"]) {
        return -1;
    }
    if ($arr1["sectionposition"] > $arr2["sectionposition"]) {
        return 1;
    }

    // same section position
    // different section, order by seid (or by name?)
    if ($arr1["seid"] != $arr2["seid"]) {
        if ($arr1["seid"] < $arr2["seid"]) {
            return -1;
        }
        if ($arr1["seid"] > $arr2["seid"]) {
            return 1;
        }
    }

    // same section
    // one variable before another
    if ($arr1["varposition"] < $arr2["varposition"]) {
        return -1;
    }
    if ($arr1["varposition"] > $arr2["varposition"]) {
        return 1;
    }

    // same variable position
    // different variable, order by vsid (or by name?)
    if ($arr1["vsid"] != $arr2["vsid"]) {
        if ($arr1["vsid"] < $arr2["vsid"]) {
            return -1;
        }
        if ($arr1["vsid"] > $arr2["vsid"]) {
            return 1;
        }
    }
    return 0;
}

function dataexportSubSort($arr1, $arr2) {
    
}

function stringSort($str1, $str2, $case = 1) {
    if ($case == 1) {
        return strcasecmp($str1, $str2);
    } else {
        return strcmp($str1, $str2);
    }
}

function inArray($str, $array, $casesensitive = 1) {

    // http://stackoverflow.com/questions/2166512/php-case-insensitive-in-array-function

    if ($casesensitive == 1) { // case insensitive
        $lengths = array_map('strlen', $array);
        if (sizeof($lengths) > 0 && strlen($str) > max($lengths)) { // if input string longer than longest array element, then no need to do preg_grep
            return false;
        }
        return preg_grep('/^' . preg_quote($str, '/') . '$/i', $array);
    } else { // case sensitive
        return in_array($str, $array);
    }
}

function shuffleArray($array, $start = 1, $preservekeys = false) {
    if ($preservekeys == true) {
        return shuffleArrayAssoc($array);
    }
    shuffle($array);
    $random = array();
    $cnt = $start;
    foreach ($array as $a) {
        $random[$cnt] = $a;
        $cnt++;
    }
    return $random;
}

// http://stackoverflow.com/questions/4102777/php-random-shuffle-array-maintaining-key-value
function shuffleArrayAssoc($list) {
    if (!is_array($list))
        return $list;

    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key) {
        $random[$key] = $list[$key];
    }
    return $random;
}

function getOptionsOrderNormalReverse($variable) {
    global $engine;
    $var = $engine->getVariableDescriptive($variable);
    $options = $engine->getFill($variable, $var, SETTING_OPTIONS);
    $codes = array();
    foreach ($options as $option) {
        $codes[$option["code"]] = $option["code"];
    }
    $rand = mt_rand(1, 2);
    if ($rand == 1) {
        return $codes;
    } else {
        return array_reverse($codes, true);
    }
    return array();
}

function getOptionsOrderNormalReverseFixed($variable, $fixed = array()) {

    global $engine;
    $var = $engine->getVariableDescriptive($variable);
    $options = $engine->getFill($variable, $var, SETTING_OPTIONS);
    $codes = array();
    foreach ($options as $option) {
        $codes[$option["code"]] = $option["code"];
    }
    $rand = mt_rand(1, 2);
    if ($rand == 1) {
        return $codes;
    } else {
        $randomcodes = array();
        $append = array();
        foreach ($options as $k => $option) {
            if (inArray($option["code"], $fixed)) {
                $append[$option["code"]] = $option["code"];
                $codes[$option["code"]] = null;
                unset($codes[$option["code"]]);
            }
        }
        $arr = array_reverse($codes, true);
        foreach ($append as $a) {
            $arr[$a] = $a;
        }
        return $arr;
    }
    return array();
}

function getOptionsOrderRandomPreserveGroups($variable, $groups = array()) {
    global $engine;
    $var = $engine->getVariableDescriptive($variable);
    $options = $engine->getFill($variable, $var, SETTING_OPTIONS);
    $order = array();
    $groups = shuffleArray($groups);
    $cnt = 1;
    foreach ($groups as $group) {
        $gr = explode(",", $group);
        foreach ($gr as $g) {
            $order[$cnt] = $g;
            $cnt++;
        }
    }
    return $order;
}

function incrementArrayIndices($arr, $plus = 1) {
    $new_array = array();
    foreach ($arr as $key => $value) {
        $new_array[$key + $plus] = $value;
    }
    return $new_array;
}

/* variable functions */

function getBasicName($name) {

    $pos = strrpos($name, ".");
    $posbracket = strrpos($name, "[");

    // dots and brackets

    if ($pos > -1 && $posbracket > -1) {

        // bracket comes before dot, then strip away brackets and get basic name of result
        if ($posbracket < $pos) {

            $posendbracket = strpos($name, "]", $posbracket + 1);

            // bracket closes before dot: mod1[cnt].Q1
            if ($posendbracket < $pos) {
                $name = substr($name, strrpos($name, "]") + 1);
                return getBasicName($name);
            }

            // bracket closes after dot: mod1[mod2.Q1]
            else {
                $name = substr($name, 0, strpos($name, "["));
                return getBasicName($name);
            }
        }

        // dot comes before bracket, then take everything after the dot and get basic name of result:         
        else {
            $name = substr($name, $pos + 1);
            return getBasicName($name);
        }
    }

    // dots only
    else if ($pos > -1) {
        $name = substr($name, $pos + 1);
    }

    // brackets only
    else if ($posbracket > -1) {
        $name = substr($name, 0, strpos($name, "[")); // . substr($name, strrpos($name, "]") + 1);
    }

    // nothing 
    else {
        /* nothing to do */
    }

    // strip away any set of enum indicators (_number_) at the end of the basic name
    $name = preg_replace("/(_[0-9]+_)\b/", "", $name);

    // return result
    return $name;
}

function isArrayReference($name) {

    $pos = strrpos($name, ".");
    $posbracket = strrpos($name, "[");

    // dots and brackets
    if ($pos > -1 && $posbracket > -1) {

        // bracket comes before dot, then strip away brackets and get basic name of result
        if ($posbracket < $pos) {
            $posendbracket = strpos($name, "]", $posbracket + 1);

            // bracket closes before dot: mod1[cnt].Q1
            if ($posendbracket < $pos) {
                $name = substr($name, strrpos($name, "]") + 1);
                return isArrayReference($name);
            }

            // bracket closes after dot: mod1[mod2.Q1]
            else {
                $name = substr($name, 0, strpos($name, "["));
                return getBasicName($name);
            }
        }

        // dot comes before bracket, then take everything after the dot and get basic name of result:         
        else {
            $name = substr($name, $pos + 1);
            return isArrayReference($name);
        }
    }
    // dots only
    else if ($pos > -1) {
        return false;
    }
    // brackets only
    else if ($posbracket > -1) {
        return true;
    }

    return false;
}

/* session functions */

function deleteAllCookies() {
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 1000);
            setcookie($name, '', time() - 1000, '/');
        }
    }
}

function getSessionPath() {
    $script = dirname($_SERVER['SCRIPT_NAME']);
    if ($script != "") {
        return $script;
    }
    return '/';
}

function endSession() {

    //deleteAllCookies();
    session_unset();

    session_destroy();

    $_SESSION = array();

    session_write_close();
    setcookie(session_name(), '', time() - 1000, getSessionPath());
    setcookie(session_name(), false, time() - 1000, getSessionPath());
    //setcookie(session_name(), '', 0, '/');
    setcookie(session_name(), '', 0, getSessionPath());
    setcookie(session_name(), '', 0, '/');
    unset($_COOKIE[session_name()]);

    session_regenerate_id(true);
}

function clearSession() {
    $id = "";
    if (isset($_SESSION['URID'])) {
        $id = $_SESSION['URID'];
    }
    $_SESSION = array();
    if ($id != "") {
        $_SESSION['URID'] = $id;
    }
    session_write_close();
}

function getSessionParams() {
    if (isset($_SESSION['PARAMS'])) {
        return $_SESSION['PARAMS'];
    }
    return null;
}

function setSessionParamsHref($params, $link, $title = "", $noajax = "") {

    return '<a ' . $noajax . " " . $title . ' href="' . setSessionParams($params) . '">' . $link . '</a>';
}

function setSessionParams($params) {

    $encoded = strtr(base64_encode(addslashes(gzcompress(serialize($params), 9))), '+/=', '-_,');

    return 'index.php?r=' . $encoded;
}

function setSessionParamsPost($params) {

    $encoded = strtr(base64_encode(addslashes(gzcompress(serialize($params), 9))), '+/=', '-_,');

    return '<input type="hidden" id="r" name="r" value="' . $encoded . '">';
}

function setSessionsParamString($params) {

    return strtr(base64_encode(addslashes(gzcompress(serialize($params), 9))), '+/=', '-_,');
}

function getAjaxParamsPost($encoded) {
    return unserialize(gzuncompress(stripslashes(base64_decode(strtr($encoded, '-_,', '+/=')))));
}

function getSessionParamsPost($encoded, $var = 'PARAMS') {

    $_SESSION[$var] = unserialize(gzuncompress(stripslashes(base64_decode(strtr($encoded, '-_,', '+/=')))));
}

function getFromSessionParams($param, $ignorer = false) {
    if (loadvar('r') == '') {
        if (!inArray($param, array(SESSION_PARAM_LANGUAGE, SESSION_PARAM_MODE, SESSION_PARAM_VERSION, SESSION_PARAM_MAINSEID, SESSION_PARAM_SEID))) {
            return ''; // no submitted session post, so ignore anything in session from before (excluding language, mode)
        }
        //if (loadvarSurvey(POST_PARAM_NEW_PRIMKEY) == '1') { // interview start, then ignore everything!
        //     return '';
        //}
    }

    if (isset($_SESSION['PARAMS']) && isset($_SESSION['PARAMS'][$param])) {
        return strip_tags($_SESSION['PARAMS'][$param]);
    }
    return '';
}

function setSessionParameter($param, $value) {

    $_SESSION['PARAMS'][$param] = $value;
}

/* script functions */

function getBase() {
    return substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR));
}

function getURL() {
    $info = null;
    if (isset($_SERVER['PATH_INFO']) && sizeof($_SERVER['PATH_INFO']) > 0) {
        $info = pathinfo($_SERVER['PATH_INFO']);
    }

    if ($info == null || ($info != null && isset($_SERVER['PATH_INFO']))) {
        $info = pathinfo($_SERVER['REQUEST_URI']);
    }
    return $info["dirname"];
}

function setPath() {

    $path = ini_get("include_path");

    $base = substr(__FILE__, 0, strrpos(__FILE__, DIRECTORY_SEPARATOR));

    $delimiter = ":";

    if (isWindows()) {

        $delimiter = ";";
    }



    /* add locations */

    $path .= $delimiter . $base;

    $path .= $delimiter . $base . DIRECTORY_SEPARATOR . "admin";
    $path .= $delimiter . $base . DIRECTORY_SEPARATOR . "custom";
    $path .= $delimiter . $base . DIRECTORY_SEPARATOR . "display";
    $path .= $delimiter . $base . DIRECTORY_SEPARATOR . "display" . DIRECTORY_SEPARATOR . "templates";
    $path .= $delimiter . $base . DIRECTORY_SEPARATOR . "language";

    $path .= $delimiter . $base . DIRECTORY_SEPARATOR . "templates";



    /* update path */

    ini_set("include_path", $path);
}

// http://stackoverflow.com/questions/5879043/php-script-detect-whether-running-under-linux-or-windows

function isWindows() {

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

        return true;
    }

    return false;
}

function myUnset($par) {

    if (isset($par)) {

        unset($par);
    }
}

function checkIsSet($var, $default = '') {

    if (isset($var)) {

        return $var;
    }

    return $default;
}

/*  encryption */

/* http://stackoverflow.com/questions/606179/what-encryption-algorithm-is-best-for-encrypting-cookies  */

function encryptC($text, $salt) {
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}

function decryptC($text, $salt) {
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
}

// http://www.eatyourvariables.com/php/mysql-compatible-aes-encryption-and-decryption
function aes_encrypt($val, $key) {
    return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, makeMySQLCompatible($key), $val, MCRYPT_MODE_ECB);
}

// http://www.eatyourvariables.com/php/mysql-compatible-aes-encryption-and-decryption
function aes_decrypt($val, $key) {
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, makeMySQLCompatible($key), $val, MCRYPT_MODE_ECB);
}

// http://www.eatyourvariables.com/php/mysql-compatible-aes-encryption-and-decryption
function makeMySQLCompatible($paymentAesKey) {
    $finalKey = array();
    $i = 128 / 8;

    while ($i != 0) {
        $finalKey[] = 0x00;
        $i--;
    }

    for ($i = 0, $d = 0; $i < strlen($paymentAesKey); $i++, $d++) {
        if ($d == count($finalKey)) {
            $d = 0; //reset location of final key to 0
        }

        $finalKey[$d] ^= ord($paymentAesKey[$i]);
    }

    $output = '';
    foreach ($finalKey as $char) {
        $output .= chr($char);
    }

    return $output;
}

//http://stackoverflow.com/questions/12864582/javascript-prompt-cancel-button-to-terminate-the-function
function confirmAction($message, $key) {
    $returnStr = ' onclick="';
    $returnStr .= ' result=prompt(\'' . $message . '\', \'\');';
    $returnStr .= ' if (result == \'' . $key . '\'){ ';
    $returnStr .= ' return true;';
    $returnStr .= ' } else { ';
    $returnStr .= ' return false;';
    $returnStr .= ' }"';
    return $returnStr;
}

/* frequently used survey functions */

// http://stackoverflow.com/questions/3776682/php-calculate-age
function calculateAge($year, $month, $day) {
    $currentyear = date("Y");
    return (date("md", date("U", mktime(0, 0, 0, $day, $month, $year))) > date("md") ? (($currentyear - $year) - 1) : ($currentyear - $year));
}

function cardinal($answer) {
    if (startsWith($answer, SEPARATOR_SETOFENUMERATED)) {
        $answer = substr($answer, 1);
    }
    if (trim($answer) == "") {
        return 0;
    }
    return sizeof(explode(SEPARATOR_SETOFENUMERATED, $answer));
}

function isTestMode() {
    if ($_SESSION[SURVEY_EXECUTION_MODE] == SURVEY_EXECUTION_MODE_TEST) {
        return true;
    }
    return false;
}

function captureScreenshot($result) {

    global $engine, $survey, $db;

    $l = getSurveyLanguage();
    $m = getSurveyMode();
    $v = getSurveyVersion();
    $key = $survey->getDataEncryptionKey();

    $stateid = $engine->getStateId();
    if ($engine->getForward() == true) {
        $stateid++;
    }


    //$screen = gzcompress(preg_replace($i, $ii, $result), 9);
    $screen = gzcompress($result, 9);
    if ($stateid == "") {
        $stateid = 1;
    }

    $primkey = $engine->getPrimaryKey();
    $bp = new BindParam();
    $suid = $engine->getSuid();
    $scid = null;

    $bp->add(MYSQL_BINDING_INTEGER, $scid);
    $bp->add(MYSQL_BINDING_INTEGER, $suid);
    $bp->add(MYSQL_BINDING_STRING, $primkey);
    $bp->add(MYSQL_BINDING_INTEGER, $stateid);
    $bp->add(MYSQL_BINDING_STRING, $screen);
    $bp->add(MYSQL_BINDING_INTEGER, $m);
    $bp->add(MYSQL_BINDING_INTEGER, $l);
    $bp->add(MYSQL_BINDING_INTEGER, $v);

    if ($key == "") {
        $query = "insert into " . Config::dbSurveyData() . "_screendumps(scdid, suid, primkey, stateid, screen, mode, language, version) values (?,?,?,?,?,?,?,?)";
    } else {
        $query = "insert into " . Config::dbSurveyData() . "_screendumps(scdid, suid, primkey, stateid, screen, mode, language, version) values (?,?,?,?,aes_encrypt(?, '" . $key . "'),?,?,?)";
    }

    $db->executeBoundQuery($query, $bp->get());
    return "";
}

/* USER FUNCTIONS */

function checkUserAccess() {
    $user = new User($_SESSION['URID']);
    $cm = getSurveyMode();
    $cl = getSurveyLanguage();
    $modes = $user->getModes();
    $languages = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
    if (!inArray($cm, $modes) || !inArray($cl, $languages)) {
        return false;
    }
    return true;
}

function leadingZeros($value, $numofzeros) {
    return sprintf("%0" . $numofzeros . "d", $value);
}

function encodeSession($params) {
    return strtr(base64_encode(addslashes(gzcompress(serialize($params), 9))), '+/=', '-_,');
}

function cutOffString($str, $length = 80) {
    $returnStr = $str;
    if (mb_strlen($str) > $length) {
        $returnStr = mb_substr($str, 0, $length, 'HTML-ENTITIES') . '...';
    }
    return $returnStr;
}

function createCSV($export, $filename) {
    ob_end_clean();
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    header("Pragma: no-cache");
    header("Expires: 0");
    $header = "";
    $fields = mysqli_fetch_fields($export);
    foreach ($fields as $val) {
        $header .= $val->name . ",";
    }
    $data = '';
    while ($row = mysqli_fetch_assoc($export)) {
        $line = '';
        foreach ($row as $value) {
            $line .= getValueForCsv($value, ',');
        }
        $data .= trim($line) . "\n";
    }
    $data = str_replace("\r", "", $data);
    if ($data == "") {
        $data = "\n(0) Records Found!\n";
    }
    print "$header\n$data";
    exit();
}

function getValueForCsv($value, $sep = ',' /* "\t" */) {
    if ((!isset($value) ) || ( $value == "" )) {
        $value = '""' . $sep;
    } else {
        if ($value == '0000-00-00 00:00:00') {
            $value = '""' . $sep;
        } else {
            $value = str_replace('"', '""', $value);
            $value = '"' . $value . '"' . $sep;
        }
    }
    return $value;
}

//http://php.net/manual/en/function.str-getcsv.php
function parse_csv($str) {
    //match all the non-quoted text and one series of quoted text (or the end of the string)
    //each group of matches will be parsed with the callback, with $matches[1] containing all the non-quoted text,
    //and $matches[3] containing everything inside the quotes
    $str = preg_replace_callback('/([^"]*)("((""|[^"])*)"|$)/s', 'parse_csv_quotes', $str);

    //remove the very last newline to prevent a 0-field array for the last line
    $str = preg_replace('/\n$/', '', $str);

    //split on LF and parse each line with a callback
    return array_map('parse_csv_line', explode("\n", $str));
}

//replace all the csv-special characters inside double quotes with markers using an escape sequence
function parse_csv_quotes($matches) {
    //anything inside the quotes that might be used to split the string into lines and fields later,
    //needs to be quoted. The only character we can guarantee as safe to use, because it will never appear in the unquoted text, is a CR
    //So we're going to use CR as a marker to make escape sequences for CR, LF, Quotes, and Commas.
    $str = str_replace("\r", "\rR", $matches[3]);
    $str = str_replace("\n", "\rN", $str);
    $str = str_replace('""', "\rQ", $str);
    $str = str_replace(',', "\rC", $str);

    //The unquoted text is where commas and newlines are allowed, and where the splits will happen
    //We're going to remove all CRs from the unquoted text, by normalizing all line endings to just LF
    //This ensures us that the only place CR is used, is as the escape sequences for quoted text
    return preg_replace('/\r\n?/', "\n", $matches[1]) . $str;
}

//split on comma and parse each field with a callback
function parse_csv_line($line) {
    return array_map('parse_csv_field', explode(',', $line));
}

//restore any csv-special characters that are part of the data
function parse_csv_field($field) {
    $field = str_replace("\rC", ',', $field);
    $field = str_replace("\rQ", '"', $field);
    $field = str_replace("\rN", "\n", $field);
    $field = str_replace("\rR", "\r", $field);
    return $field;
}

function getCommunicationServer() {
    $server = dbConfig::defaultCommunicationServer();
    if (is_array($server)) {
        return $server[$_SESSION['COMMSERVER']];
    }
    return $server;
}

function getTextmodeStr($pre = 't1.') {
    $strTestmode = ' ' . $pre . 'primkey not like "999%" AND';
    $user = new User($_SESSION['URID']);
    if ($user->getTestMode() == 1) {
        $strTestmode = ' ' . $pre . 'primkey like "999%" AND';
    }
    return $strTestmode;
}

function excludeTestCases($pre = 't1.') {
    return $pre . 'primkey not like "999%" AND';
}

function getFromArray($array, $field1, $field2, $field3) {
    if (isset($array[$field1][$field2][$field3])) {
        return $array[$field1][$field2][$field3];
    }
    return '0';
}

function isScriptRunLocally() {
    return $_SERVER['SERVER_ADDR'] == '127.0.0.1';
}

function isMainNurse($user) {
    return $user->isMainNurse();
    //return $user->getUrid() == 51;
}

function isLabNurse($user) {
    return $user->isLabNurse();
    //return ($user->getUrid() == 62 || $user->getUrid() == 63);
}

function isFieldNurse($user) {
    return $user->isFieldNurse();
    //return ($user->getUrid() == 72 || $user->getUrid() == 73 || $user->getUrid() == 74);
}

function isVisionTestNurse($user) {
    return $user->isVisionNurse();
    //return ($user->getUrid() == 77);
}

function human_filesize($bytes, $decimals = 2) {
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function gen_password($length = 8) {
    mt_srand((double) microtime() * 1000000);
    $password = "";
    $chars = "abcdefghijkmnpqrstuvwxyz123456";
    for ($i = 0; $i < $length; $i++) {
        $x = mt_rand(0, strlen($chars) - 1);
        $password .= $chars{$x};
    }
    $chars = "23456789";
    for ($i = 0; $i < 2; $i++) {
        $x = mt_rand(0, strlen($chars) - 1);
        $password .= $chars{$x};
    }
    return $password;
}

function tempdebug() {
    $time = microtime(true);
    $dFormat = "l jS F, Y - H:i:s";
    $mSecs = $time - floor($time);
    $mSecs = substr($mSecs, 1);
    $return = '$time ==' . $time;
    return $return . "<hr>";
}

function doExit() {
    if ($_SESSION['SYSTEM_ENTRY'] != USCIC_SMS) {
        $_SESSION['REQUEST_IN_PROGRESS'] = null;
        unset($_SESSION['REQUEST_IN_PROGRESS']);
    }
    exit;
}

?>