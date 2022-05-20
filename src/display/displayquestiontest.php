<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayQuestionTest extends DisplayQuestionBasic {
    
    private $editable;
    
    function __construct($prim, $engine) {
        parent::__construct($prim, $engine);        
        $user = new User($_SESSION['URID']);
        if ($user->getUserType() == USER_SYSADMIN) {
            parent::enableInlineEditable();             
            $this->editable = true;
        }
        else if($user->getUserType() == USER_TRANSLATOR) {
            $modes = $user->getModes();            
            if (inArray(getSurveyMode(), $modes)) {                
                $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
                if (inArray(getSurveyLanguage(), $langs)) {  
                    parent::enableInlineEditable(); 
                    $this->editable = true;
                }
            }
        }                
    }

    function showSurveyHeader($title, $style = '') {
        $returnStr = parent::showSurveyHeader(Language::messageSMSTitle(), $style);
        $returnStr .= $this->showNavBar();
        $user = $_SESSION['URID'];
        if ($this->editable == true) {
            $user = new User($_SESSION['URID']);
            if ($user->getUserType() == USER_SYSADMIN) {
                $returnStr .= $this->getTinyMCE(".uscic-inline-editable", 2, $this->getInlineEditIcon());
            }
            else {
                $returnStr .= $this->getTinyMCE(".uscic-inline-editable", 2, $this->getInlineEditIcon()); // allow to have all elements, because otherwise save will screw it up
            }
        }    

        $this->padding = true;
        return $returnStr;
    }

    function showLanguage() {
        return '';
    }

    public function showNavBar() {

        $returnStr = $this->getHeader();
        $rgid = $this->engine->getRgid();
        $variablenames = $this->getRealVariables(explode("~", $this->engine->getDisplayed()));        
        $variablenamesfull = $this->engine->getDisplayed();
        $template = $this->engine->getTemplate();
        
        $click = "";
        if ($template != "") {
            $group = $this->engine->getGroup($template);
            $click = $this->engine->replaceFills($group->getClickLanguageChange());
        }
        else {
            $vars = explode("~", $variablenames);
            $var = $this->engine->getVariableDescriptive($vars[0]);
            $click = $this->engine->replaceFills($var->getClickLanguageChange());
        }
        $click = str_replace("'", "", $click);
        
        $clickmode = "";
        if ($template != "") {
            $group = $this->engine->getGroup($template);
            $clickmode = $this->engine->replaceFills($group->getClickModeChange());
        }
        else {
            $vars = explode("~", $variablenames);
            $var = $this->engine->getVariableDescriptive($vars[0]);
            $clickmode = $this->engine->replaceFills($var->getClickModeChange());
        }
        $clickmode = str_replace("'", "", $clickmode);
        
        // begin language
        global $survey;
        $user = new User($_SESSION['URID']);
        $allowedmodes = $user->getModes();
        $allowedlanguages = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        $default = $survey->getDefaultLanguage(getSurveyMode());
        $ut = "sysadmin";
        if ($user->getUserType() == USER_TRANSLATOR) {
            $ut = "translator";
            if (!inArray($default, $allowedlanguages)) {
                $allowedlanguages[] = $default;
            }
        }  
        else if ($user->GetUserType() == USER_TESTER) {
            $ut = "tester";
        }
        
        if (getSurveyModeAllowChange() == MODE_CHANGE_RESPONDENT_ALLOWED) {
            if (sizeof($allowedmodes) > 1) {

                $template = $this->engine->getTemplate();
                $returnStr .= '<li class="dropdown">';
                $returnStr .= '   <a href="#" class="dropdown-toggle" data-toggle="dropdown">Mode <b class="caret"></b></a>';
                $returnStr .= '<ul class="dropdown-menu" role="menu">';
                $current = getSurveyMode();
                $modes = Common::surveyModes();
                foreach ($modes as $key => $mode) {
                    if (inArray($key, $allowedmodes)) {
                        $check = '';
                        if ($key == $current) {
                            $check = ' <span class="glyphicon glyphicon-ok"></span>';
                        }
                        $returnStr .= '<li><a href=# onclick=\'document.getElementById("r").value="' . setSessionsParamString(array_merge(array(SESSION_PARAM_SURVEY => $survey->getSuid(), SESSION_PARAM_PRIMKEY => $this->engine->getPrimaryKey(), SESSION_PARAM_RGID => $rgid, SESSION_PARAM_VARIABLES => $variablenames, SESSION_PARAM_GROUP => $template, SESSION_PARAM_MODE => getSurveyMode(), SESSION_PARAM_LANGUAGE => getSurveyLanguage(), SESSION_PARAM_TEMPLATE => getSurveyTemplate(), SESSION_PARAM_TIMESTAMP => time(), SESSION_PARAM_SEID => $this->engine->getSeid(), SESSION_PARAM_MAINSEID => $this->engine->getMainSeid()), array(SESSION_PARAM_NEWMODE => $key))) . '"; document.getElementById("navigation").value="' . addslashes(Language::buttonUpdate()) . '"; ' . $clickmode . ' document.getElementById("form").submit(); \'>' . $mode . $check . '</a></li>';
                    }
                }
                $returnStr .= '</ul></li>';
            }
        }

        if (getSurveyLanguageAllowChange() == LANGUAGE_CHANGE_RESPONDENT_ALLOWED) {
            if (sizeof($allowedlanguages) > 1) {
                $returnStr .= '<li class="dropdown">';
                $returnStr .= '   <a href="#" class="dropdown-toggle" data-toggle="dropdown">Language <b class="caret"></b></a><ul class="dropdown-menu">';                
                $langs = Language::getLanguagesArray(); //getSurveyLanguages($this->engine->survey);
                foreach ($langs as $lang) {
                    if (inArray($lang["value"], $allowedlanguages)) {
                        $check = '';
                        if ($lang["value"] == getSurveyLanguage()) {
                            $check = ' <span class="glyphicon glyphicon-ok"></span>';
                        }
                        $returnStr .= '<li><a href=# onclick=\'document.getElementById("r").value="' . setSessionsParamString(array_merge(array(SESSION_PARAM_SURVEY => $survey->getSuid(), SESSION_PARAM_PRIMKEY => $this->engine->getPrimaryKey(), SESSION_PARAM_RGID => $rgid, SESSION_PARAM_VARIABLES => $variablenames, SESSION_PARAM_GROUP => $template, SESSION_PARAM_MODE => getSurveyMode(), SESSION_PARAM_LANGUAGE => getSurveyLanguage(), SESSION_PARAM_TEMPLATE => getSurveyTemplate(), SESSION_PARAM_TIMESTAMP => time(), SESSION_PARAM_SEID => $this->engine->getSeid(), SESSION_PARAM_MAINSEID => $this->engine->getMainSeid()), array(SESSION_PARAM_NEWLANGUAGE => $lang["value"]))) . '"; document.getElementById("navigation").value="' . addslashes(Language::buttonUpdate()) . '"; ' . $click . ' document.getElementById("form").submit(); \'>' . $lang["name"] . $check . '</a></li>';
                    }
                }
                $returnStr .= '</ul></li>';
            }
        }

        $user = new User($_SESSION['URID']);
        $returnStr .= '<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . $user->getName() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">
										<li class="dropdown-header">' . $this->engine->getPrimaryKey() . '</li>
                                                                                <li class="dropdown-header">' . $variablenamesfull . '</li>';
        
        $windowopen = 'window.open(\'tester/' . setSessionParams(array('reporturid' => $_SESSION['URID'], 'testpage' => 'report', 'reportsuid' => $this->engine->getSuid(), 'reportseid' => $this->engine->getSeid(), 'reportmainseid' => $this->engine->getMainSeid(), 'reportrgid' => $rgid, 'reportdisplayed' => $variablenames, 'reportlanguage' => getSurveyLanguage(), 'reportmode' => getSurveyMode(), 'reportversion' => getSurveyVersion(), 'reportprimkey' => $this->engine->getPrimarykey())) . '\', \'popupWindow\', \'width=770,height=500,scrollbars=yes,top=100,left=100\'); return false;';
        $javascript = ' onclick="' . $windowopen . '"';
        $returnStr .= '<li><a style="cursor: pointer;" ' . $javascript . '><span class="glyphicon glyphicon-remove-sign"></span> ' . Language::linkReportProblem() . '</a></li>';
        $windowopen = 'window.open(\'tester/' . setSessionParams(array('testpage' => 'watch', 'watchurid' => $_SESSION['URID'], 'watchsuid' => $this->engine->getSuid(), 'watchseid' => $this->engine->getSeid(), 'watchmainseid' => $this->engine->getMainSeid(), 'watchrgid' => $rgid, 'watchdisplayed' => $variablenames, 'watchlanguage' => getSurveyLanguage(), 'watchmode' => getSurveyMode(), 'watchversion' => getSurveyVersion(), 'watchprimkey' => $this->engine->getPrimarykey())) . '\', \'popupWindow\', \'width=770,height=650,scrollbars=yes,top=100,left=100\'); return false;';
        $javascript = ' onclick="' . $windowopen . '"';
        $returnStr .= '<li><a style="cursor: pointer;" ' . $javascript . '><span class="glyphicon glyphicon-zoom-in"></span> ' . Language::linkWatch() . '</a></li>';
        $first = $this->engine->isFirstState();   
        if ($first == false || ($first == true && $this->engine->getForward() == true)) {            
            if ($this->engine->getForward() == true) {
                
                $stateid = $this->engine->getStateId() + 1;
            }
            else {
                $stateid = $this->engine->getStateId();
            }            
            $windowopen = 'window.open(\'tester/' . setSessionParams(array('testpage' => 'jumpback', 'jumpurid' => $_SESSION['URID'], 'jumpsuid' => $this->engine->getSuid(), 'jumpstateid' => $stateid, 'jumpprimkey' => $this->engine->getPrimaryKey())) . '\', \'popupWindow\', \'width=770,height=300,scrollbars=yes,top=100,left=100\'); return false;';
            $javascript = ' onclick="' . $windowopen . '"';
            $returnStr .= '<li><a style="cursor: pointer;" ' . $javascript . '><span class="glyphicon glyphicon-arrow-left"></span> ' . Language::linkJumpBack() . '</a></li>';
        }
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => $ut . '.tools.test', 'suid' => $this->engine->getSuid())) . '&se=' . addslashes(USCIC_SMS) . '"><span class="glyphicon glyphicon-home"></span> ' . Language::linkBackToNubis() . '</a></li>                   
                    <li class="divider"></li>
                   <li><a href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>
                 </ul>
             </li>
            </ul>
';
        
        $returnStr .= '</div><!--/.nav-collapse --> </div> </div>';
        return $returnStr;
    }

    function showEndSurvey() {
        global $survey;
        $returnStr = $this->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<form method="post" action="index.php">';

        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.test', 'suid' => $this->engine->getSuid()));
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SE . '" value="' . USCIC_SMS . '">';
        $returnStr .= '</form>';
        $returnStr .= '<script>';
        $returnStr .= '$(document).ready(function(){ $("form:first").submit(); }); ';
        $returnStr .= '</script></body><html>';
        return $returnStr;
    }

    function showCompletedSurvey() {
        global $survey;
        $returnStr = $this->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<form method="post" action="index.php">';

        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.test', 'suid' => $this->engine->getSuid()));
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SE . '" value="' . USCIC_SMS . '">';
        $returnStr .= '</form>';
        $returnStr .= '<script>';
        $returnStr .= '$(document).ready(function(){ $("form:first").submit(); }); ';
        $returnStr .= '</script></body><html>';
        return $returnStr;
    }

    function getHeader() {

        $returnStr .= '
      <!-- Fixed navbar -->
      <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">';
        return $returnStr;
    }

}

?>