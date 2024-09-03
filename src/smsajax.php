<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class SmsAjax {

    function __construct() {
        
    }

    function getPage($p) {
        switch ($p) {
            case 'sysadmin.survey.sections': return $this->showSections();
            case 'sysadmin.survey.addsection': return $this->addSection();
            case 'sysadmin.survey.surveys': return $this->showSurveys();
            case 'sysadmin.survey.types': return $this->showTypes();
            case 'sysadmin.survey.settings': return $this->showSettings();
            case 'sysadmin.survey.addsurvey': return $this->addSurvey();
            case 'sysadmin.users': return $this->showUsers();
            case 'sysadmin.inline.editcontent': return $this->showEditInlineRes();
            case 'sysadmin.inline.getcontent': return $this->showEditInlineGetContent();
            case 'sysadmin.history.getentry': return $this->showHistoryGetEntry();
            case 'sysadmin.autocomplete': return $this->showAutoComplete();
            case 'sysadmin.autocompletecodemirror': return $this->showAutoCompleteCodemirror();
        }
        //$this->testLog();
    }

    /* AUTO COMPLETE FUNCTIONS */

    function showAutoComplete() {
        global $survey;
        return json_encode($survey->getVariableDescriptiveNames());
    }

    function showAutoCompleteCodemirror() {
        global $survey;
        $arr = array_merge($survey->getVariableDescriptiveNames(), $survey->getSectionNames(), $survey->getGroupNames());
        return json_encode($arr);
    }

    /* HISTORY LOAD FUNCTIONS */

    function showHistoryGetEntry() {
        require_once("track.php");
        $track = new Track("", "", "");
        $entry = $track->getEntry(loadvar("trid"));
        return $entry["value"];
        exit;
    }

    /* INLINE GET FUNCTIONS */

    function showEditInlineGetContent() {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        require_once('compiler.php');
        require_once('track.php');
        $target = loadvar("target");
        if (startsWith($target, "vsid_")) {
            return $this->showGetVariableText(getFromSessionParams(SESSION_PARAM_SURVEY), str_ireplace("vsid_", "", $target));
        } else if (startsWith($target, "seid_")) {
            return $this->showGetSectionText(getFromSessionParams(SESSION_PARAM_SURVEY), str_ireplace("seid_", "", $target));
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        exit;
    }

    function showGetSectionText($suid, $seid) {
        $survey = new Survey($suid);
        $section = $survey->getSection($seid);
        $texttype = loadvar("texttype");
        switch ($texttype) {
            case SETTING_PAGE_HEADER:
                return $section->getHeader();
            case SETTING_PAGE_FOOTER:
                return $section->getFooter();
            default:
                return '';
        }
    }

    function showGetVariableText($suid, $vsid) {
        $survey = new Survey($suid);
        $var = $survey->getVariableDescriptive($vsid);
        $texttype = loadvar("texttype");
        switch ($texttype) {
            case SETTING_QUESTION:
                return $var->getQuestion();
            case SETTING_OPTIONS:
                $code = loadvar("answercode");
                return $var->getOptionLabel($code);
            case SETTING_PRETEXT:
                return $var->getPreText();
            case SETTING_POSTTEXT:
                return $var->getPostText();
            default:
                return '';
        }
    }

    /* INLINE EDIT FUNCTIONS */

    function showEditInlineRes() {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        require_once('compiler.php');
        require_once('track.php');

        $l = getSurveyLanguage();
        if (file_exists("language/language" . getSurveyLanguagePostFix($l) . ".php")) {
            require_once('language' . getSurveyLanguagePostFix($l) . '.php'); // language  
        } else {
            require_once('language_en.php'); // fall back on english language file
        }
        $target = loadvar("target");
        if (startsWith($target, "vsid_")) {
            $this->showEditVariableRes(getFromSessionParams(SESSION_PARAM_SURVEY), str_ireplace("vsid_", "", $target));
        } else if (startsWith($target, "seid_")) {
            $this->showEditSectionRes(getFromSessionParams(SESSION_PARAM_SURVEY), str_ireplace("seid_", "", $target));
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        
    }

    function showEditSectionRes($suid, $seid) {
        $survey = new Survey($suid);
        $section = $survey->getSection($seid);
        $texttype = loadvar("texttype");
        $value = loadvarAllowHTML("text");
        switch ($texttype) {
            case SETTING_PAGE_HEADER:
                $section->setHeader($value);
                break;
            case SETTING_PAGE_FOOTER:
                $section->setFooter($value);
                break;
            default:
                break;
        }
        $section->save();
        $compiler = new Compiler($suid, getSurveyVersion($survey));
        $mess = $compiler->generateSections(array($section));
        $mess = $compiler->generateGetFillsSections(array($section));
        $mess = $compiler->generateInlineFieldsSections(array($section));
    }

    function showEditVariableRes($suid, $vsid) {
        $survey = new Survey($suid);
        $var = $survey->getVariableDescriptive($vsid);
        $texttype = loadvar("texttype");
        $value = loadvarAllowHTML("text");
        switch ($texttype) {
            case SETTING_QUESTION:
                $var->setQuestion($value);
                break;
            case SETTING_PRETEXT:
                $var->setPretext($value);
                break;
            case SETTING_POSTTEXT:
                $var->setPostText($value);
                break;
            case SETTING_OPTIONS:
                $code = loadvar("answercode");
                $current = explode("\r\n", $var->getOptionsText());
                foreach ($current as $k => $c) {
                    if (startsWith($c, $code . " ")) {
                        $current[$k] = $code . ' ' . $value;
                        break;
                    }
                }
                $var->setOptionsText(implode("\r\n", $current));
            default:
                break;
        }
        $var->save();
        $compiler = new Compiler($suid, getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));        
        $mess = $compiler->generateInlineFields(array($var));
    }

    function showSections() {
        $returnStr = '';
        $survey = new Survey($_SESSION['SUID']);
        $sections = $survey->getSections();
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr .= $displaySysAdmin->showSections($sections);
        return $returnStr;
    }

    function showUsers() {
        $returnStr = '';
        $users = new Users();
        $usertype = loadvar('usertype', USER_INTERVIEWER);
        $displayUsers = new DisplayUsers();
        $returnStr .= $displayUsers->showUsersList($users->getUsersByType($usertype));
        return $returnStr;
    }

    function addSection() {
        $survey = new Survey($_SESSION['SUID']);
        $survey->addSection(loadvar('section'));
    }

    function showSurveys() {
        $returnStr = '';
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys();
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr .= $displaySysAdmin->showSurveyList($surveys);
        return $returnStr;
    }

    function addSurvey() {
        
    }

    function showSettings() {
        $returnStr = '';
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr .= $displaySysAdmin->showSettingsList();
        return $returnStr;
    }

    function showTypes() {
        $returnStr = '';
        $survey = new Survey($_SESSION['SUID']);
        $types = $survey->getTypes();
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr .= $displaySysAdmin->showTypes($types);
        return $returnStr;
    }

    function testLog() {
        $my_file = '/tmp/smsajax.html';


        $handle = fopen($my_file, 'w') or die('Cannot open file:  ' . $my_file);
        $data = 'TO calendar.php!!!' . date('H:i:s') . '<br/>';
        $data .= var_export($_POST, true);
        $data .= '<hr>';
        $data .= var_export($_GET, true);
        $data .= '<hr>';
        fwrite($handle, $data);

        /*
          $data .= loadvar('id');
          $data .= '<hr>';
          $data .= loadvar('year');
          $data .= '<hr>';

          $data .= '<hr>';
         */


        /*
          ob_start () ;
          phpinfo () ;
          $data .= ob_get_contents () ;
          ob_end_clean () ;
         */


        //fwrite($handle, $data);
    }

}

?>