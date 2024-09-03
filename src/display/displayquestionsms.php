<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayQuestionSms extends DisplayQuestionBasic {

    function showHeader($title, $style = '', $extra = '') {        
        $returnStr = parent::showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= $this->showNavBar();        

//        $returnStr .= $this->engine->getDisplayed();
        $this->padding = true;
//      $returnStr .= 'SMS balk hier!'; 
//      $returnStr .= parent::showLanguage(); 

        return $returnStr;
    }
    
    function showSurveyHeader($title, $style = '', $extra = '') {
        $returnStr = parent::showHeader(Language::messageSMSTitle(), '<link href="css/uscic.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= $this->showNavBar();

//        $returnStr .= $this->engine->getDisplayed();
        $this->padding = true;
//      $returnStr .= 'SMS balk hier!'; 
//      $returnStr .= parent::showLanguage(); 

        return $returnStr;
    }

    function showLanguage() {
        return '';
    }

    public function showNavBar() {
        $returnStr = $this->showCalculator();
//language

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

        // begin language
        global $survey;
        if (getSurveyLanguageAllowChange() == LANGUAGE_CHANGE_RESPONDENT_ALLOWED) {

            $allowed = explode("~", $survey->getAllowedLanguages(getSurveyMode()));
            if (sizeof($allowed) > 1) {
                $returnStr .= '<li class="dropdown">';
                $returnStr .= '   <a href="#" class="dropdown-toggle" data-toggle="dropdown">Language <b class="caret"></b></a><ul class="dropdown-menu">';

                $langs = Language::getLanguagesArray(); //getSurveyLanguages($this->engine->survey);
                foreach ($langs as $lang) {
                    if (inArray($lang["value"], $allowed)) {
                        $check = '';
                        if ($lang["value"] == getSurveyLanguage()) {
                            $check = ' <span class="glyphicon glyphicon-ok"></span>';
                        }
                        $returnStr .= '<li><a href=# onclick=\'document.getElementById("r").value="' . setSessionsParamString(array_merge(array(SESSION_PARAM_SURVEY => $survey->getSuid(), SESSION_PARAM_PRIMKEY => $this->engine->getPrimaryKey(), SESSION_PARAM_RGID => $rgid, SESSION_PARAM_VARIABLES => $variablenames, SESSION_PARAM_GROUP => $template, SESSION_PARAM_MODE => getSurveyMode(), SESSION_PARAM_LANGUAGE => getSurveyLanguage(), SESSION_PARAM_TEMPLATE => getSurveyTemplate(), SESSION_PARAM_TIMESTAMP => time(), SESSION_PARAM_SEID => $this->engine->getSeid(), SESSION_PARAM_MAINSEID => $this->engine->getMainSeid()), array(SESSION_PARAM_NEWLANGUAGE => $lang["value"]))) . '"; document.getElementById("navigation").value="' . NAVIGATION_LANGUAGE_CHANGE . '"; ' . $click . ' document.getElementById("form").submit(); \'>' . $lang["name"] . $check . '</a></li>';
                    }
                }
                $returnStr .= '</ul></li>';
            }
            //end language
        }

        $user = new User($_SESSION['URID']);
        $returnStr .= '<li class="dropdown">
              <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . $user->getName() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">
										<li class="dropdown-header">' . $this->engine->primkey . '</li>
                                                                                <li class="dropdown-header">' . $variablenamesfull . '</li>';



        //$returnStr .= '<li><a href=# data-toggle="modal" data-target="#calculator">Calculator</a></li>';

        $returnStr .= '<li><a href="#" data-toggle="modal" data-target="#calculator"><span class="glyphicon glyphicon-th"></span> ' . Language::linkCalculator() . '</a></li>';

        
        $windowopen = 'window.open(\'tester/' . setSessionParams(array('k' => encryptC(Config::testerKey(), Config::smsComponentKey()), 'type' => "2", 'testpage' => 'watch', 'watchurid' => $_SESSION['URID'], 'watchsuid' => $this->engine->getSuid(), 'watchseid' => $this->engine->getSeid(), 'watchmainseid' => $this->engine->getMainSeid(), 'watchrgid' => $rgid, 'watchdisplayed' => $variablenames, 'watchlanguage' => getSurveyLanguage(), 'watchmode' => getSurveyMode(), 'watchversion' => getSurveyVersion(), 'watchprimkey' => $this->engine->getPrimarykey())) . '\', \'popupWindow\', \'width=770,height=650,scrollbars=yes,top=100,left=100\'); return false;';
        $javascript = ' onclick="' . $windowopen . '"';
        $returnStr .= '<li><a style="cursor: pointer;" ' . $javascript . '><span class="glyphicon glyphicon-zoom-in"></span> ' . Language::linkWatch() . '</a></li>';
        $windowopen = 'window.open(\'tester/' . setSessionParams(array('k' => encryptC(Config::testerKey(), Config::smsComponentKey()),'type' => "2", 'testpage' => 'update', 'watchurid' => $_SESSION['URID'], 'watchsuid' => $this->engine->getSuid(), 'watchseid' => $this->engine->getSeid(), 'watchmainseid' => $this->engine->getMainSeid(), 'watchrgid' => $rgid, 'watchdisplayed' => $variablenames, 'watchlanguage' => getSurveyLanguage(), 'watchmode' => getSurveyMode(), 'watchversion' => getSurveyVersion(), 'watchprimkey' => $this->engine->getPrimarykey())) . '\', \'popupWindow\', \'width=1200,height=650,scrollbars=yes,top=100,left=100\'); return false;';
        $javascript = ' onclick="' . $windowopen . '"';
        $returnStr .= '<li><a style="cursor: pointer;" ' . $javascript . '><span class="glyphicon glyphicon-zoom-in"></span> ' . Language::linkUpdate() . '</a></li>';
        $first = $this->engine->isFirstState();   
        if ($first == false || ($first == true && $this->engine->getForward() == true)) {            
            if ($this->engine->getForward() == true) {
                
                $stateid = $this->engine->getStateId() + 1;
            }
            else {
                $stateid = $this->engine->getStateId();
            }            
            $windowopen = 'window.open(\'tester/' . setSessionParams(array('k' => encryptC(Config::testerKey(), Config::smsComponentKey()), 'type' => "2", 'testpage' => 'jumpback', 'jumpurid' => $_SESSION['URID'], 'jumpsuid' => $this->engine->getSuid(), 'jumpstateid' => $stateid, 'jumpprimkey' => $this->engine->getPrimaryKey())) . '\', \'popupWindow\', \'width=770,height=300,scrollbars=yes,top=100,left=100\'); return false;';
            $javascript = ' onclick="' . $windowopen . '"';
            $returnStr .= '<li><a style="cursor: pointer;" ' . $javascript . '><span class="glyphicon glyphicon-arrow-left"></span> ' . Language::linkJumpBack() . '</a></li>';
        }
        
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'interviewer.backfromsms', 'primkey' => $this->engine->primkey, 'suid' => $this->engine->getSuid())) . '&se=' . addslashes(USCIC_SMS) . '"><span class="glyphicon glyphicon-home"></span> ' . Language::linkBackToSMS() . '</a></li>                   
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

        $returnStr .= setSessionParamsPost(array('page' => 'interviewer.surveycompleted', 'primkey' => $this->engine->primkey, 'suid' => $this->engine->getSuid()));
//	  	  $returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . addslashes(encryptC($this->primkey, Config::directLoginKey())) . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . getSurveyLanguage() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SE . '" value="' . USCIC_SMS . '">';
        $returnStr .= '</form>';
        $returnStr .= '<script>';
        $returnStr .= '$(document).ready(function(){ $("form:first").submit(); }); ';
        $returnStr .= '</script></body><html>';
        return $returnStr;
//        return '<a href="' . setSessionParams(array('page' => 'interviewer.surveycompleted', 'primkey' => $this->engine->primkey, 'suid' => $this->engine->getSuid())) . '&se=' . addslashes(USCIC_SMS) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkBackToSMS() . '</a>';
    }

    function showCompletedSurvey() {
        global $survey;
        $returnStr = $this->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<form method="post" action="index.php">';

        $returnStr .= setSessionParamsPost(array('page' => 'interviewer.backfromsms', 'primkey' => $this->engine->primkey, 'suid' => $this->engine->getSuid()));
//	  	  $returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . addslashes(encryptC($this->primkey, Config::directLoginKey())) . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . getSurveyLanguage() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SE . '" value="' . USCIC_SMS . '">';
        $returnStr .= '</form>';
        $returnStr .= '<script>';
        $returnStr .= '$(document).ready(function(){ $("form:first").submit(); }); ';
        $returnStr .= '</script></body><html>';
        return $returnStr;
//        return '<a href="' . setSessionParams(array('page' => 'interviewer.backfromsms', 'primkey' => $this->engine->primkey, 'suid' => $this->engine->getSuid())) . '&se=' . addslashes(USCIC_SMS) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkBackToSMS() . '</a>';
    }

    function showCalculator() {
        $returnStr .= '  
<!-- Modal -->
<div class="modal fade" id="calculator" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:405px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Calculator</h4>
      </div>
      <div class="modal-body">
      <style>
.calculator {
  padding: 20px;
  margin-top: 20px;
  background-color: #ccc;
  border-radius: 5px;
  /*this is to remove space between divs that are inline-block*/
  font-size: 0;
}
 
.calculator > input[type=text] {
  width: 100%;
  height: 50px;
  border: none;
  background-color: #eee;
  text-align: right;
  font-size: 30px;
  padding-right: 10px;
}
 
.calculator .row { margin-top: 10px; }
 
.calculator .key {
  width: 78.7px;
  display: inline-block;
  background-color: black;
  color: white;
  font-size: 3rem;
  margin-right: 5px;
  border-radius: 5px;
  height: 50px;
  line-height: 50px;
  text-align: center;
}
.calculator .key:hover { cursor: pointer; }
.key.last { margin-right: 0px; }
.key.action { background-color: #646060; }
</style>
<div class="calculator">
  <input type="text" readonly id=answercalc>
  <div class="row">
    <div class="key">1</div>
    <div class="key">2</div>
    <div class="key">3</div>
    <div class="key last">0</div>
  </div>
  <div class="row">
    <div class="key">4</div>
    <div class="key">5</div>
    <div class="key">6</div>
    <div class="key last action instant">cl</div>
  </div>
  <div class="row">
    <div class="key">7</div>
    <div class="key">8</div>
    <div class="key">9</div>
    <div class="key last action instant">=</div>
  </div>
  <div class="row">
    <div class="key action">+</div>
    <div class="key action">-</div>
    <div class="key action">x</div>
    <div class="key last action">/</div>
  </div>
</div>
</div>
    </div>
  </div>
</div> 

<script>

function moveToSurvey(){
  $("#answer1").val($("#answercalc").val());
}

</script>


      <script src="js/jquery.calc.js"></script>
      <script src="js/calculator.js"></script>

';




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