<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayLogin extends Display {

    public function __construct() {
        parent::__construct();
    }
    
    public function showSurveyClosed() {        
        global $survey, $engine;        
        require_once("display/templates/displayquestion_" .  $survey->getTemplate() . ".php");
        $engine = loadEngine($survey->getSuid(), '', "", getSurveyVersion(), getBaseSectionSeid($survey->getSuid()));
        $do = $engine->getDisplayObject();
        $returnStr = $do->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= $do->displayBody();
        $returnStr .= $do->showClosedSurvey();        
        $returnStr .= '</form>';
        $returnStr .= "</div></div></div>";
        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    public function showLoginCode($message) {
        
        global $survey, $engine;
        require_once("display/templates/displayquestion_" .  $survey->getTemplate() . ".php");
        $engine = loadEngine($survey->getSuid(), '', "", getSurveyVersion(), getBaseSectionSeid($survey->getSuid()));
        $do = $engine->getDisplayObject();
        $returnStr = $do->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        //$returnStr .= '<form id="form" role="form" method=post>';
        $returnStr .= $do->displayBody();
        //$returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . $randomId . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SUID . '" value="' . $survey->getSuid() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_R . '" value="' . loadvar(POST_PARAM_R) . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . getSurveyLanguage() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_MODE . '" value="' . getSurveyMode() . '">';
        if ($message != "") {
            $returnStr .= $this->displayError($message);
        }
        $returnStr .= $do->showLoginSurvey();        
        $returnStr .= '<div class="panel-footer text-center">';
        $var = $engine->getVariableDescriptive(VARIABLE_LOGIN);
        $returnStr .= '<button type="submit" class="btn btn-default" value="' . $do->applyFormatting($var->getLabelNextButton(), $var->getButtonFormatting()) . '">' . $do->applyFormatting($var->getLabelNextButton(), $var->getButtonFormatting()) . '</button>';
        $returnStr .= '</form>';

        $returnStr .= "</div></div></div></div>";

        /* footer */
        $returnStr .= $this->showFooter();
        return $returnStr;        
    }

    public function showLoginAnonymous($randomId) {
        global $survey, $engine;
        require_once("display/templates/displayquestion_" .  $survey->getTemplate() . ".php");
        $engine = loadEngine($survey->getSuid(), $randomId, "", getSurveyVersion(), getBaseSectionSeid($survey->getSuid()));        
        $do = $engine->getDisplayObject();
        $returnStr = $do->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        //$returnStr .= '<form id="form" role="form" method=post>';
        $returnStr .= $do->displayBody();
        $returnStr .= $do->showWelcomeSurvey();
        $returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . $randomId . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SUID . '" value="' . $survey->getSuid() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_R . '" value="' . loadvar(POST_PARAM_R) . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . getSurveyLanguage() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_MODE . '" value="' . getSurveyMode() . '">';
        
        $returnStr .= '<div class="panel-footer text-center">';
        $var = $engine->getVariableDescriptive(VARIABLE_INTRODUCTION);
        $returnStr .= '<button type="submit" class="btn btn-default" value="' . $do->applyFormatting($var->getLabelNextButton(), $var->getButtonFormatting()) . '">' . $do->applyFormatting($var->getLabelNextButton(), $var->getButtonFormatting()) . '</button>';
        $returnStr .= '</form>';

        $returnStr .= "</div></div></div></div>";

        /* footer */
        $returnStr .= $this->showFooter();

        return $returnStr;
    }

    public function showLoginDirect($primkey, $message) {
        
        global $survey, $engine;
        require_once("display/templates/displayquestion_" .  $survey->getTemplate() . ".php");
        $returnStr = $this->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');

        if (true) {
            if ($primkey != '') {
                $returnStr .= '<form method="post" id="startform">';
                $returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . decryptC($primkey, Config::directLoginKey()) . '">';
                $returnStr .= '<input type=hidden name="' . POST_PARAM_SUID . '" value="' . $survey->getSuid() . '">';
                $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . loadvar(POST_PARAM_LANGUAGE) . '">';
                $returnStr .= '<input type=hidden name="' . POST_PARAM_PRELOAD . '" value="' . loadvar(POST_PARAM_PRELOAD) . '">';
                $returnStr .= '<input type=hidden name="' . POST_PARAM_MODE . '" value="' . loadvar(POST_PARAM_MODE) . '">';
                $returnStr .= '<input type=hidden name="' . POST_PARAM_URID . '" value="' . loadvar(POST_PARAM_URID) . '">';
                if (loadvar(POST_PARAM_URID) != '') {
                    $_SESSION['URID'] = loadvar(POST_PARAM_URID);
                }
                $returnStr .= '<div style="display: none;"><input type=submit></div>';
                $returnStr .= '</form>';
                $returnStr .= '<script>';
                $returnStr .= '$(document).ready(function(){ $("#startform").submit(); }); ';
                $returnStr .= '</script>';
            } else {                
                $returnStr .= '<div id="wrap">';
                $returnStr .= '<div class="container"><p>';
                $engine = loadEngine($survey->getSuid(), $primkey, '', getSurveyVersion(), getBaseSectionSeid($survey->getSuid()));
                $do = $engine->getDisplayObject();
                $returnStr .= $do->showDirectAccessOnlySurvey();
                //$returnStr .= Language::errorDirectLogin();
            }
        } else {
            $returnStr .= '<div id="wrap">';
            $returnStr .= '<div class="container"><p>';
            //$returnStr .= Language::errorDirectLogin();
            $engine = loadEngine($survey->getSuid(), $primkey, '', getSurveyVersion(), getBaseSectionSeid($survey->getSuid()));
            $do = $engine->getDisplayObject();
            $returnStr .= $do->showDirectAccessOnlySurvey();
        }
        /* footer */
        $returnStr .= $this->showFooter();

        return $returnStr;
    }

}

?>