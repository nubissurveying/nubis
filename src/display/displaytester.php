<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayTester extends Display {


  public function __construct() {
      parent::__construct();
  }

  public function showMain(){
      
        $returnStr = $this->showTestHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'tester.tools.test')) . '" class="list-group-item">' . Language::linkTest() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'tester.tools.reported')) . '" class="list-group-item">' . Language::linkReported() . '</a>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
  }
  
  
  
  
  function showTestHeader($title, $extra = '') {

        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }
        $extra2 = '<link href="js/formpickers/css/bootstrap-formhelpers.min.css" rel="stylesheet">
                  <link href="css/uscicadmin.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">';
        $returnStr = $this->showHeader(Language::messageSMSTitle(), $extra . $extra2);
        $returnStr .= $this->bindAjax();
        return $returnStr;
    }
  
    
    
  function showBottomBar() {

        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }

        return '</div>
    <div id="footer">
      <div class="container">
        <p class="text-muted credit">' . Language::nubisFooter() . '</p>
      </div>
    </div>
    <div class="waitmodal"></div>';
    }

    public function showNavBar() {

        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }

        $testActive = '';
        $reportedActive = '';
        if (!isset($_SESSION['LASTPAGE'])) {
            $_SESSION['LASTPAGE'] = 'tester.home';
        }        
        if (strpos($_SESSION['LASTPAGE'], 'tester.tools.test') === 0) {
            $testActive = ' active';            
        }
        else if (strpos($_SESSION['LASTPAGE'], 'tester.tools.reported') === 0) {
            $testActive = '';
            $reportedActive = ' active';
        }
        
        $returnStr = '
      <!-- Fixed navbar -->
      <div id="mainnavbar" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="' . setSessionParams(array('page' => 'tester.home')) . '">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">';
        
            
        $returnStr .= '<li class="' . $testActive . '">' . setSessionParamsHref(array('page' => 'tester.tools.test'), Language::linkTest()) . '</li>';
        $returnStr .= '<li class="' . $reportedActive . '">' . setSessionParamsHref(array('page' => 'tester.tools.reported'), Language::linkReported()) . '</li>';
        
        $returnStr .= '</ul>';
        $user = new User($_SESSION['URID']);
        $returnStr .= '<ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . $user->getUsername() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">';
        $returnStr .= '<li><a ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>
                 </ul>
             </li>
            </ul>';
        
        $returnStr .= '
          </div><!--/.nav-collapse -->
        </div>
      </div>
';

        $returnStr .= "<div id='content'>";

        return $returnStr;
    }
    
    
    function showTest(){
        $returnStr = $this->showTestHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {

            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="tester.tools.test">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
            $returnStr .= "</form>";


            $returnStr .= "<form method=post>";
            $returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC(generateRandomPrimkey(8), Config::directLoginKey())) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';            
            
            $returnStr .= '<input type=hidden name=' . POST_PARAM_SURVEY_EXECUTION_MODE . ' value="' . SURVEY_EXECUTION_MODE_TEST . '">';
            $returnStr .= '<span class="label label-default">' . Language::labelToolsTestSettings() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= $this->displayComboBox();
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelTestSurvey() . "</td><td><select onchange='document.getElementById(\"" . SMS_POST_SURVEY . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();' name=" . POST_PARAM_SUID . " class='selectpicker show-tick'>";
            $current = new Survey(getSurvey());
            foreach ($surveys as $survey) {
                $selected = "";
                if ($survey->getSuid() == $current->getSuid()) {
                    $selected = "SELECTED";
                }
                $returnStr .= "<option $selected value=" . $survey->getSuid() . '>' . $survey->getName() . '</option>';
            }
            $returnStr .= "</select></td></tr>";
            $user = new User($_SESSION['URID']);
            $cm = getSurveyMode();
            $cl = getSurveyLanguage();
            $modes = $user->getModes(getSurvey());            
            $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
            $default = $current->getDefaultLanguage();
            if (!inArray($default, $langs)) {
                //$langs[] = $default;
            }

            $returnStr .= "<tr><td>" . Language::labelTestModeInput() . "</td><td>" . $this->displayModesAdmin(POST_PARAM_MODE, POST_PARAM_MODE, getSurveyMode(), "", implode("~", $modes), "onchange='document.getElementById(\"" . SMS_POST_MODE . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();'") . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTestLanguage() . "</td><td>" . $this->displayLanguagesAdmin(POST_PARAM_LANGUAGE, POST_PARAM_LANGUAGE, getSurveyLanguage(), true, true, false, "", implode("~", $langs)) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
            $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonTest() . '</button>';
            $returnStr .= "</form>";
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
        $returnStr .= '</p></div></div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;            
    }   
      
    function showReported($content = "") {        
        $returnStr = $this->showTestHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {

            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="sysadmin.tools.issues">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
            $returnStr .= "</form>";

            // get reported issues for survey
            global $survey;
            $issues = $survey->getReportedIssues();

            // no problems reported
            if (sizeof($issues) == 0) {
                $returnStr .= "<br/>" . '<div class="alert alert-warning">' . Language::labelNoProblemsReported() . '</div>';
            } else {

                $returnStr .= $this->displayComboBox();
                $returnStr .= '<span class="label label-default">Filter by</span>';
                $returnStr .= '<div class="well well-sm">';
                $returnStr .= '<table>';
                $returnStr .= '<tr><td>' . Language::labelTestSurvey() . "</td><td><select onchange='document.getElementById(\"" . SMS_POST_SURVEY . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();' name=" . POST_PARAM_SUID . " class='selectpicker show-tick'>";
                $current = new Survey(getSurvey());
                foreach ($surveys as $survey) {
                    $selected = "";
                    if ($survey->getSuid() == $current->getSuid()) {
                        $selected = "SELECTED";
                    }
                    $returnStr .= "<option $selected value=" . $survey->getSuid() . '>' . $survey->getName() . '</option>';
                }
                $returnStr .= "</select></td></tr></table></div>";

                $returnStr .= $this->displayDataTablesScripts(array("colvis", "rowreorder"));
                $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#issuetable').dataTable(
                                {
                                    \"iDisplayLength\": " . sizeof($issues) . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    searching: false,
                                    paging: false
                                    }    
                                );
                                         
                       });</script>

                        "; //

                $returnStr .= "<br/><table id='issuetable' class='table table-bordered table-striped'><thead>";
                $returnStr .= "<th>" . Language::labelReportedBy() . "</th><th>" . Language::labelReportedOn() . "</th><th>" . Language::labelReportedCategory() . "</th><th>" . Language::labelReportedDescription() . "</th><th>" . Language::labelReportedPrimaryKey() . "</th><th>" . Language::labelReportedMode() . "</th><th>" . Language::labelReportedLanguage() . "</th>";
                $returnStr .= "</thead><tbody>";
                $modes = Common::surveyModes();
                $languages = Language::getLanguagesArray();
                $cats = Language::reportProblemCategories();
                foreach ($issues as $is) {
                    $us = new User($is['urid']);
                    $returnStr .= "<tr>";
                    $returnStr .= "<td>" . $us->getUsername() . "</td>";
                    $returnStr .= "<td>" . $is["ts"] . "</td>";
                    $returnStr .= "<td>" . $cats[$is["category"]] . "</td>";
                    $returnStr .= "<td>" . $is["comment"] . "</td>";
                    $returnStr .= "<td>" . $is["primkey"] . "</td>";
                    $returnStr .= "<td>" . $modes[$is["mode"]] . "</td>";
                    $returnStr .= "<td>" . $languages[str_replace("_", "", getSurveyLanguagePostFix($is["language"]))]['name'] . "</td>";
                    $returnStr .= "</tr>";
                }
                $returnStr .= "</tbody></table>";
            }
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
        $returnStr .= '</p></div></div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }  
}

?>