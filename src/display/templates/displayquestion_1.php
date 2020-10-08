<?php

/*
  ------------------------------------------------------------------------
  displayquestion_1.php is a template for DisplayQuestion. Customize your survey interface here.
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayQuestion extends DisplayQuestionBasic {

    function showLanguage() {
        global $survey;
        $allowed = explode("~", $survey->getAllowedLanguages(getSurveyMode()));
        if (sizeof($allowed) == 1) {
            return "";
        }
        $rgid = $this->engine->getRgid();
        $variablenames = $this->getRealVariables(explode("~", $this->engine->getDisplayed()));
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
        $current = getSurveyLanguage();
        $langs = Language::getLanguagesArray();
        foreach ($langs as $key => $lang) {
            if (inArray($lang["value"], $allowed)) {
                $check = '';
                if ($lang["value"] == getSurveyLanguage()) {
                    //              $check = ' <span class="glyphicon glyphicon-ok"></span>';
                } else {
                    $returnStr .= '<button type="button" class="btn btn-sm btn-warning" onclick=\'document.getElementById("r").value="' . setSessionsParamString(array_merge(array(SESSION_PARAM_LASTACTION => $this->engine->getLastSurveyAction(), SESSION_PARAM_SURVEY => $survey->getSuid(), SESSION_PARAM_PRIMKEY => $this->primkey, SESSION_PARAM_RGID => $rgid, SESSION_PARAM_VARIABLES => $variablenames, SESSION_PARAM_GROUP => $template, SESSION_PARAM_MODE => getSurveyMode(), SESSION_PARAM_TEMPLATE => getSurveyTemplate(), SESSION_PARAM_VERSION => getSurveyVersion(), SESSION_PARAM_LANGUAGE => $current, SESSION_PARAM_TIMESTAMP => time(), SESSION_PARAM_SEID => $this->engine->getSeid(), SESSION_PARAM_MAINSEID => $this->engine->getMainSeid()), array(SESSION_PARAM_NEWLANGUAGE => $lang["value"]))) . '"; document.getElementById("navigation").value="' . NAVIGATION_LANGUAGE_CHANGE . '"; ' . $click . ' document.getElementById("form").submit(); \'>' . $lang["name"] . $check . '</button>';
                }
            }
        }
        return $returnStr;
    }

    function displayBody() {
        $returnStr = '';
        
        if (Config::useAccessible()) {
            $returnStr .= '<link href="css/accessible.css" type="text/css" rel="stylesheet">';
            $returnStr .= "<header class='accessible_header' id='surveyheader' role='banner'></header>";
            $returnStr .= '<main class="accessible_main" id="surveymain" role="main">';
            $returnStr .= "<h1 class='nubis-accessible-hidden'>" . Language::labelAccessibleH1() . "</h1>";
        }
        
        $returnStr .= '<form id="form" role="form" method=post autocapitalize="off" autocorrect="off" autocomplete="off">';
        $returnStr .= '<div id="wrap">';
        $returnStr .= '<div class="container">';

        $returnStr .= '<div style="background-image: url(display/templates/images/uas_logo_background.png); height:100px; padding-top:45px">
		<img alt="USC" title="USC" src="display/templates/images/uas_name.png" style="z-index:2">
        	</div>';
        $returnStr .= '<div id="languagebar" style="background:#c7c7c2; height:30px;">';
        if (getSurveyLanguageAllowChange() == LANGUAGE_CHANGE_RESPONDENT_ALLOWED) {
            $returnStr .= $this->showLanguage();
        }
        $returnStr .= '</div>';
        $returnStr .= '<p>';        
        $returnStr .= "<input type=hidden id=navigation name=navigation>";
        $returnStr .= '<div class="panel panel-default"><div class="panel-body">';
        $returnStr .= '<div class="panel-body">';
        return $returnStr;
    }

    function redirect($page) {
        global $survey;
        $returnStr = $this->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<form method="post" action="../../../panel/index.php">';

        $returnStr .= $this->setSessionParamsPost(array('page' => $page));

        $returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . addslashes(encryptC($this->primkey, Config::directLoginKey())) . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . getSurveyLanguage() . '">';
        $returnStr .= '</form>';
        $returnStr .= '<script>';
        $returnStr .= '$(document).ready(function(){ $("form:first").submit(); }); ';
        $returnStr .= '</script></body><html>';
        return $returnStr;
    }

    function showEndSurvey() {
        global $survey;
        echo $this->redirect("completed." . $survey->getName());
        doExit();
    }

    function showCompletedSurvey() {
        global $survey;
        echo $this->redirect("");
        doExit();
    }

    function setSessionParamsPost($params) {
        $encoded = strtr(base64_encode(addslashes(gzcompress(serialize($params), 9))), '+/=', '-_,');
        return '<input type="hidden" id="r" name="r" value="' . $encoded . '">';
    }

}

?>