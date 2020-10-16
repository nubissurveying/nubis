<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayResearcher extends Display {

    public function __construct() {
        parent::__construct();
    }

    public function showMain() {
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';


        //respondents mode!

        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports')) . '" class="list-group-item">' . Language::linkReports() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data')) . '" class="list-group-item">' . Language::linkData() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.output.documentation')) . '" class="list-group-item">' . Language::linkDocumentation() . '</a>';
        //$returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.output.documentation'), '<span class="glyphicon glyphicon-file"></span> ' . Language::linkDocumentation()) . '</li>';
        //$returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.households')) . '" class="list-group-item">' . 'Households' . '</a>';
        //$returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.sample')) . '" class="list-group-item">' . 'Unassigned Sample' . '</a>';
        $returnStr .= '</div>';




        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showDocumenation() {
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//CONTENT
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelOutputDocumentation() . '</li>';
        $returnStr .= '</ol>';

        $communication = new Communication();
        $files = array();
        $communication->getScriptFiles($files, 'documentation');
        $oldDirStr = '';
        if (sizeof($files) > 0) {
            foreach ($files as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ($ext == 'html' || $ext == 'doc' || $ext == 'docx') {
                    $dirStr = '';
                    $dir = preg_replace('#/+#', '', dirname($file));
                    if ($dir != '') {
                        $dirStr = $dir . ': ';
                    }
                    if ($oldDirStr != $dirStr && $oldDirStr != '') {
                        $returnStr .= '<hr>';
                    }
                    $oldDirStr = $dirStr;

                    $returnStr .= $dirStr . '<a href="documentation' . $file . '" target="_blank">' . basename($file) . '</a><br/>';
                }
            }
        } else {
            $returnStr .= $this->displayWarning(Language::labelResearcherNoDocs());
        }

//ENDCONTENT        




        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showResearchHeader($title, $extra = '') {

        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }

        $extra2 = '<link href="js/formpickers/css/bootstrap-formhelpers.min.css" rel="stylesheet">
                  <link href="css/uscicadmin.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">                  
                    ';
        $returnStr = $this->showHeader(Language::messageSMSTitle(), $extra . $extra2);
        $returnStr .= $this->displayOptionsSidebar("optionssidebarbutton", "optionssidebar");
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
        $householdsActive = '';
        $reportsActive = '';
        $dataActive = '';
        $documentationActive = '';
        $sampleActive = '';
        if (startsWith(getFromSessionParams('page'), 'researcher.reports')) {
            $reportsActive = ' class="active"';
        }
        if (startsWith(getFromSessionParams('page'), 'researcher.documentation')) {
            $documentationActive = ' class="active"';
        }
        if (startsWith(getFromSessionParams('page'), 'researcher.data')) {
            $dataActive = ' class="active"';
        }
        if (startsWith(getFromSessionParams('page'), 'researcher.sample')) {
            $sampleActive = ' class="active"';
        }
        if (startsWith(getFromSessionParams('page'), 'researcher.households')) {
            $householdsActive = ' class="active"';
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
            <a class="navbar-brand" href="' . setSessionParams(array('page' => 'researcher.home')) . '">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">';


        $returnStr .= '<li' . $reportsActive . '>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::linkReports()) . '</li>';
        $returnStr .= '<li' . $dataActive . '>' . setSessionParamsHref(array('page' => 'researcher.data'), Language::linkData()) . '</li>';
        $returnStr .= '<li' . $documentationActive . '>' . setSessionParamsHref(array('page' => 'researcher.output.documentation'), Language::linkDocumentation()) . '</li>';
//        $returnStr .= '<li' . $sampleActive . '>' . setSessionParamsHref(array('page' => 'researcher.sample'), Language::linkUnassigned()) . '</li>';        
        $returnStr .= '</ul>';
        $user = new User($_SESSION['URID']);
        $returnStr .= '<ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . $user->getUsername() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">';
        // commenting out preferences option <li><a href="' . setSessionParams(array('page' => 'sysadmin.preferences')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkPreferences() . '</a></li>';
        //$returnStr .= '<li class="divider"></li>';
        $returnStr .= '<li><a ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>
                 </ul>
             </li>
            </ul>
';
        // $returnStr .= $this->showSearch();
        $returnStr .= '
          </div><!--/.nav-collapse -->
        </div>
      </div>
';

        $returnStr .= "<div id='content'>";

        return $returnStr;
    }

    function showReports() {
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelResearcherOutputReports() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.responseoverview')) . '" class="list-group-item">' . Language::labelResearcherResponseOverview() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.aggregates')) . '" class="list-group-item">' . Language::labelResearcherAggregates() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.paradata')) . '" class="list-group-item">' . Language::labelResearcherParadata() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.timings.distribution')) . '" class="list-group-item">' . Language::labelResearcherTimingsDistribution() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.timings.overtime')) . '" class="list-group-item">' . Language::labelResearcherTimingsOverTime() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.timings.perscreen')) . '" class="list-group-item">' . Language::labelResearcherTimingsPerScreen() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.platform')) . '" class="list-group-item">' . Language::labelResearcherPlatform() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.contact.graphs')) . '" class="list-group-item">' . Language::labelResearcherContactGraphs() . '</a>';
        $returnStr .= '</div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsResponse() {

        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }
        $survey = new Survey($_SESSION['SUID']);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        //respondents mode!

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::labelResearcherOutputReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherResponseOverview() . '</li>';

        $returnStr .= '</ol>';

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelResearcherResponseOverview() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        if (sizeof($surveys) > 0) {
            $returnStr .= $this->displayComboBox();
            $returnStr .= '<tr><td>' . Language::labelOutputScreenDumpsSurvey() . '</td><td>' . $this->displaySurveys("survey", "survey", $_SESSION["SUID"]) . '</td></tr>';
            $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#survey").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        }
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        $returnStr .= "</form>";

        $returnStr .= '</table><br/>';

        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
        $returnStr .= $this->getResponseData();

        $returnStr .= '</div>'; // well
        /// END NEW NEW NEW       
        //OVERVIEW        
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsAggregates($content = "") {
        $survey = new Survey($_SESSION['SUID']);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::headerReportsAggregates() . '</li>';
        $returnStr .= '</ol>';


        $returnStr .= $content;
        $returnStr .= $this->displayComboBox();
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {
            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="researcher.reports.aggregates">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
            $returnStr .= "</form>";

            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelTestSurvey() . "</td><td><select onchange='document.getElementById(\"" . SMS_POST_SURVEY . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();' name=" . POST_PARAM_SUID . " class='selectpicker show-tick'>";
            $current = new Survey(getSurvey());
            foreach ($surveys as $s) {
                $selected = "";
                if ($s->getSuid() == $current->getSuid()) {
                    $selected = "SELECTED";
                }
                $returnStr .= "<option $selected value=" . $s->getSuid() . '>' . $s->getName() . '</option>';
            }
            $returnStr .= "</select></td></tr>";
            $returnStr .= '</table><br/><br/>';

            $sections = $survey->getSections();
            foreach ($sections as $section) {
                $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.aggregates.section', 'seid' => $section->getSeid())) . '" class="list-group-item">' . $section->getName() . ' ' . $section->getDescription() . '</a>';
            }
            $returnStr .= "</div>";
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);

        return $returnStr;
    }

    function showReportsAggregatesSection($seid) {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.aggregates'), Language::headerReportsAggregates()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.aggregates', 'suid' => $suid), $survey->getName()) . '</li>';
        $returnStr .= '<li class="active">' . $section->getName() . '</li>';

        $returnStr .= '</ol>';

        $variables = $survey->getVariableDescriptives($seid);

        foreach ($variables as $variable) {
            if (!inArray($variable->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.aggregates.variable', 'seid' => $seid, 'vsid' => $variable->getVsid())) . '" class="list-group-item">' . $variable->getName() . ' ' . $variable->getDescription() . '</a>';
            }
        }

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsAggregatesVariable($seid, $vsid) {
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $variable = $survey->getVariableDescriptive($vsid);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.aggregates'), Language::headerReportsAggregates()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.aggregates', 'suid' => $suid), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.aggregates.section', 'suid' => $suid), $section->getName()) . '</li>';
        $returnStr .= '<li class="active">' . $variable->getName() . '</li>';


        $returnStr .= '</ol>';




        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelAggregateDetails() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        $returnStr .= "</form>";

        $returnStr .= '</table>';

        $returnStr .= '<br/><table>';
        $returnStr .= '<tr><td valign=top style="min-width: 100px;">' . Language::labelTypeEditGeneralQuestion() . ": </td><td valign=top>";
        $returnStr .= $variable->getQuestion() . "</td></tr>";
        $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditGeneralAnswerType() . ": </td><td valign=top>";
        $answertype = $variable->getAnswerType();
        $arr = Common::getAnswerTypes();
        $returnStr .= $arr[$answertype] . "</td></tr>";
        if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK))) {
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditGeneralCategories() . ": </td><td valign=top>";
            $returnStr .= str_replace("\r\n", "<br/>", $variable->getOptionsText()) . "</td></tr>";
        } else if (inArray($answertype, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditRangeMinimum() . ": </td><td valign=top>";
            $returnStr .= $variable->getMinimum() . "</td></tr>";
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditRangeMaximum() . ": </td><td valign=top>";
            $returnStr .= $variable->getMaximum() . "</td></tr>";
        }

        if ($variable->isArray()) {
            $returnStr .= $this->displayComboBox();
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditGeneralArrayInstance() . ": </td><td valign=top>";
            $options = $this->getArrayData($_SESSION['SUID'], $variable->getName());
            $returnStr .= "<form id=instanceform method=post>";
            $returnStr .= "<select class='selectpicker show-tick' id='arrayinstance' name='arrayinstance'>";
            foreach ($options as $op) {
                $returnStr .= "<option value='" . $op . "'>" . $op . "</option>";
            }
            $returnStr .= "</select>";
            $returnStr .= "</td></tr>";
            $params = getSessionParams();
            $params['vsid'] = $variable->getVsid();
            $returnStr .= setSessionParamsPost($params);
            $returnStr .= "</form>";
            $returnStr .= "<script type='text/javascript'>";
            $returnStr .= "$('#arrayinstance').change(function () {
                                $('#instanceform').submit();
                            });";
            $returnStr .= "</script>";
        }

        $returnStr .= "</table></div>";

        if (inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {

            $returnStr .= '<span class="label label-default">' . Language::labelAggregateData() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= $this->displayWarning(Language::messageNoAggregateData());
            $returnStr .= "</div>";
        } else {

            $returnStr .= '<span class="label label-default">' . Language::labelAggregateData() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $data = new Data();
            $brackets = array();
            $varname = $variable->getName();
            if ($variable->isArray()) {
                if (loadvar("arrayinstance") != "") {
                    $varname = loadvar("arrayinstance");
                } else {
                    $varname = $varname . "[1]";
                }
            }
            $aggdata = $data->getAggregrateData($variable, $varname, $brackets);
            //$aggdata = array(2,5);
            if (sizeof($aggdata) == 0) {
                $returnStr .= "<br>" . $this->displayWarning(Language::messageNoData());
            } else {

                $returnStr .= '<script src="js/highcharts.js"></script>';
                $returnStr .= '<script src="js/modules/exporting.js"></script>';
                $returnStr .= '<script src="js/export-csv.js"></script>';
                $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

                switch ($answertype) {
                    case ANSWER_TYPE_ENUMERATED:
                    // fall through
                    case ANSWER_TYPE_RANK:
                    // fall through    
                    case ANSWER_TYPE_SETOFENUMERATED:
                    // fall through 
                    case ANSWER_TYPE_DROPDOWN:
                    // fall through 
                    case ANSWER_TYPE_MULTIDROPDOWN:
                        $options = $variable->getOptions();
                        $brackets = array();
                        foreach ($options as $opt) {
                            $brackets[] = $opt["code"] . ' ' . $opt["label"];
                        }
                        $brackets[] = Language::labelOutputEmptyBracket();
                        $brackets[] = Language::labelOutputDKBracket();
                        $brackets[] = Language::labelOutputNABracket();
                        $brackets[] = Language::labelOutputRFBracket();
                        break;
                    case ANSWER_TYPE_INTEGER:
                    // fall through 
                    case ANSWER_TYPE_SLIDER:
                    // fall through 
                    case ANSWER_TYPE_KNOB:
                    // fall through    
                    case ANSWER_TYPE_RANGE:
                    // fall through 
                    case ANSWER_TYPE_DOUBLE:
                        $brackets[] = Language::labelOutputEmptyBracket();
                        $brackets[] = Language::labelOutputDKBracket();
                        $brackets[] = Language::labelOutputNABracket();
                        $brackets[] = Language::labelOutputRFBracket();
                        break;
                    default:
                        break;
                }

                $returnStr .= $this->createChart($variable->getName(), implode(",", $aggdata), $brackets);
            }
            $returnStr .= "</div>";
        }

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsContactGraphs() {
        $survey = new Survey(loadvar("survey"));

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherContactGraphs() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

        $returnStr .= $this->getContactData();
        $returnStr .= '</div>'; //container and wrap


        $returnStr .= '</div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsTimingsDistribution() {
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }

        $data = new Data();
        $data->generateTimings($_SESSION['SUID']);
        $survey = new Survey($_SESSION['SUID']);
        $timings = $data->getTimings($_SESSION['SUID']);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherTimingsDistribution() . '</li>';
        $returnStr .= '</ol>';

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelResearcherTimingsDistribution() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        //if (sizeof($surveys) > 0) {
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelOutputScreenDumpsSurvey() . '</td><td>' . $this->displaySurveys("survey", "survey", $_SESSION["SUID"]) . '</td></tr>';
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#survey").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        //}
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        $returnStr .= '</table><br/>';
        $returnStr .= "</form>";


        // high chart
        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

        $brackets = Language::timingsBrackets();
        $returnStr.= $this->getTimingsData($survey->getName(), implode(",", $timings), $brackets);


        $returnStr .= '</div>'; // well
        $returnStr .= '</div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsTimingsOverTime() {
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }

        $data = new Data();
        $data->generateTimings($_SESSION['SUID']);
        $survey = new Survey($_SESSION['SUID']);
        $brackets = array();
        $timings = $data->getTimingsDataOverTime($_SESSION['SUID'], $brackets);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherTimingsOverTime() . '</li>';
        $returnStr .= '</ol>';


        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::headerOutputStatisticsTimingsOverTime() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        //if (sizeof($surveys) > 0) {
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelOutputScreenDumpsSurvey() . '</td><td>' . $this->displaySurveys("survey", "survey", $_SESSION["SUID"]) . '</td></tr>';
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#survey").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        //}
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';

        $returnStr .= '</table><br/>';
        $returnStr .= "</form>";


        // high chart
        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
        $returnStr.= $this->getTimingsDataOverTime($survey->getName(), implode(",", $timings), $brackets);
        $returnStr .= '</div>'; // well

        $returnStr .= '</div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsTimingsPerScreen() {
        $suid = loadvar('survey');
        if ($suid == "") {
            $suid = $_SESSION['SUID'];
        }
        $survey = new Survey($suid);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherTimingsPerScreen() . '</li>';
        $returnStr .= '</ol>';

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelResearcherTimingsPerScreen() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        //if (sizeof($surveys) > 0) {
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelOutputScreenDumpsSurvey() . '</td><td>' . $this->displaySurveys("survey", "survey", $suid) . '</td></tr>';
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#survey").on("change", function(event) {
                                document.getElementById("sv").value = this.value;
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        //}
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        $returnStr .= "</form>";
        $returnStr .= '<form method="post">';
        $returnStr .= "<input type=hidden name='sv' id='sv' value=$suid />";
        $returnStr .= "<input type=hidden name='type' id='type' value=1 />";
        $returnStr .= "<input type=hidden name='" . DATA_OUTPUT_TYPEDATA . "' value=" . loadvar(DATA_OUTPUT_TYPEDATA) . " />";
        $returnStr .= setSessionParamsPost(array('page' => 'researcher.reports.timings.perscreen.res', "cnt" => 0));

        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = null;
        }

        $returnStr .= '<tr><td>' . Language::labelOutputScreenDumpsRespondent() . '</td><td>' . $this->displayRespondents($suid) . '</td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '<input type="submit" onclick="$(\'#type\').val(1);" class="btn btn-default" value="' . Language::buttonView() . '"/>';
        $returnStr .= '</form>';


        $returnStr .= '</div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsTimingsPerScreenRes() {
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }
        $data = new Data();
        $data->generateTimings($_SESSION['SUID']);
        $survey = new Survey($_SESSION['SUID']);
        $brackets = array();
        $timings = $data->getTimingsDataPerRespondent($_SESSION['SUID'], loadvar('respondent'), $brackets);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.timings.perscreen'), Language::labelResearcherTimingsPer()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherTimingsPerScreen() . '</li>';
        $returnStr .= '</ol>';

        // high chart
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

        $returnStr.= $this->getTimingsDataRespondent($survey->getName() . ' - ' . loadvar('respondent'), implode(",", $timings), $brackets);
        $returnStr .= '</div>'; // well

        $returnStr .= '</div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showData() {
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';


        //respondents mode!
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::linkData() . '</li>';
        $returnStr .= '</ol>';

        /*
          $returnStr .= $this->displayWarning(Language::labelResearcherWarningStata());

          $returnStr .= '<form method=post>';
          $surveyList = array();
          $surveys = new Surveys();
          foreach ($surveys->getSurveys() as $survey) {
          $surveyList[$survey->getSuid()] = $survey->getName();
          }

          $returnStr .= '<div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title">' . Language::labelResearcherDownloadSurveyData() . '</h3>
          </div>
          <div class="panel-body">';
          $returnStr .= '<table>';
          $returnStr .= '<script>

          function updateFilename(element){
          var surveys = ["", "' . implode('","', $surveyList) . '"];
          $("input[name=\'filename\']").val(\'' . dbConfig::dbSurvey() . '_\' + surveys[$(element).val()]);
          //alert($("input[name=\'filename\']").val());
          }

          function checkEmptyEncryption(){
          if ($("input[name=\'primkeyencryption\']").val() == \'\'){
          alert(\'' . Language::labelResearcherEncryption() . '!\');
          return false;
          }
          return true;
          }
          </script>';

          $returnStr .= '<tr><td>' . Language::labelOutputDataSurvey() . '</td><td><select id=survey name=survey class="form-control" onchange="updateFilename(this)">';
          foreach ($surveyList as $key => $survey) {
          $returnStr .= '<option value="' . $key . '">' . $survey . ' data (dta)</option>';
          }
          $returnStr .= '</select></td></tr>';
          $returnStr .= '<tr><td>' . Language::labelResearcherEncryptionKey() . '</td><td><input type=text class="form-control" name=primkeyencryption></td></tr>';
          $returnStr .= '</table>';
          $returnStr .= '<input type=hidden name="r" value="eNpLtDK0qi62MrFSKkhMT1WyLrYysrRSKq4sTkzJzczTyy8tKSgt0UtJLEkszsxLz0ktSi1Wsq4FXDDnKhLi">';
          $returnStr .= '<input type=hidden name="modes[]" value="1">';
          $returnStr .= '<input type=hidden name="modes[]" value="2">';
          $returnStr .= '<input type=hidden name="modes[]" value="3">';
          $returnStr .= '<input type=hidden name="languages[]" value="1">';
          $returnStr .= '<input type=hidden name="languages[]" value="2">';
          $returnStr .= '<input type=hidden name="typedata" value="2">';
          $returnStr .= '<input type=hidden name="completedinterviews" value="0">';
          $returnStr .= '<input type=hidden name="cleandata" value="2">';
          $returnStr .= '<input type=hidden name="filetype" value="1">';
          $returnStr .= '<input type=hidden name="filename" value="' . dbConfig::dbSurvey() . '_' . $surveyList[1] . '">';
          $returnStr .= '<input type=hidden name="primkeyindata" value="1">';
          $returnStr .= '<input type=hidden name="variableswithoutdata" value="1">';
          $returnStr .= '<input type=hidden name="fieldnamecase" value="1">';
          $returnStr .= '<input type=hidden name="includevaluelabels" value="1">';
          $returnStr .= '<input type=hidden name="includevaluelabelnumbers" value="1">';
          $returnStr .= '<input type=hidden name="markempty" value="1">';


          $returnStr .= '<br/><button type="submit" class="btn btn-default" onclick="return checkEmptyEncryption()">' . Language::labelResearcherDownloadData() . '</button>';
          $returnStr .= '</form>';

          $returnStr .= '</div></div>';
         */

        /*
          $syid = 2;
          $link = "?r=eNpLtDK0qi62MrFSKkhMT1WyLrYysrRSKq4sTkzJzczTyy8tKSgt0UtJLEkszsxLz0ktSi1Wsq4FXDDnKhLi&survey=$syid&modes[]=1&modes[]=2&modes[]=3&languages[]=1&typedata=2&completedinterviews=0&cleandata=2&filetype=1&filename=&primkeyindata=1&variableswithoutdata=1&primkeyencryption=&fieldnamecase=1&includevaluelabels=1&includevaluelabelnumbers=1&markempty=1";
          $returnStr .= '<a href="' . $link . '">' . 'Individual data (dta)' . '</a><br/>';
         */
        /*
          $returnStr .= '<div class="panel panel-default">
          <div class="panel-heading">
          <h3 class="panel-title">' . Language::labelResearcherDownloadOtherData() . '</h3>
          </div>
          <div class="panel-body">';


          $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.other', 'type' => '1')) . '">' . Language::labelResearcherDownloadHouseholds() . '</a><br/>';
          $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.other', 'type' => '2')) . '">' . Language::labelResearcherDownloadRespondents() . '</a><br/>';
          $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.other', 'type' => '3')) . '">' . Language::labelResearcherDownloadContacts() . '</a><br/>';
          $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.other', 'type' => '4')) . '">' . Language::labelResearcherDownloadRemarks() . '</a><br/>';



          $returnStr .= '</div></div>';
         */

        $returnStr .= $this->showDataList();



        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showDataList() {
        $returnStr = '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.rawdata')) . '" class="list-group-item">' . Language::labelShowRawData() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.timings')) . '" class="list-group-item">' . Language::labelShowTimingsData() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.paradata')) . '" class="list-group-item">' . Language::labelShowParadata() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.data.auxiliarydata')) . '" class="list-group-item">' . Language::labelShowAuxData() . '</a>';
        $returnStr .= '</div>';
        return $returnStr;
    }

    function showRawData() {
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.data'), Language::linkData()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelShowRawData() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $this->showDataSingleSurvey(); //showRawDataList();
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    /*
      function showRawDataList(){
      $returnStr = '<div class="list-group">';
      $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.datasingle')) . '" class="list-group-item">' . Language::labelDataSingle() . '</a>';
      $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.datamultiple')) . '" class="list-group-item">' . Language::labelDataMultiple() . '</a>';
      $returnStr .= '</div>';
      return $returnStr;

      }
     */

    function showDataSingleSurvey() {
        $suid = loadvar('survey');
        if ($suid == "") {
            $suid = $_SESSION['SUID'];
        }
        $survey = new Survey($suid);

        //$returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        //$returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        /*
          $returnStr .= '<ol class="breadcrumb">';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.data'), Language::linkData()) . '</li>';
          $returnStr .= '<li class="active">' . Language::labelShowRawData() . '</li>';
          $returnStr .= '</ol>';
         */
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="researcher.datasingle">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
        $returnStr .= "</form>";
        $returnStr .= '<form ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' id=surveyform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'researcher.datasingleres'));

        /* DATA CRITERIA */
//TODO:              limitToFields
//TODO:      primkeys

        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataSource() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataTable() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPE . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_OUTPUT_TYPE_DATARECORD_TABLE . ">" . Language::optionsDataDataRecordTable() . "</option>";
        $returnStr .= "<option value=" . DATA_OUTPUT_TYPE_DATA_TABLE . ">" . Language::optionsDataDataTable() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $user = new User($_SESSION['URID']);
        $modes = $user->getModes($suid);
        $langs = array();
        foreach ($modes as $m) {
            $langs = array_merge($langs, explode("~", $user->getLanguages($suid, $m)));
        }
        $langs = array_unique($langs);

        $returnStr .= '<tr><td>' . Language::labelOutputDataSurvey() . '</td><td>' . $this->displaySurveys(DATA_OUTPUT_SURVEY, DATA_OUTPUT_SURVEY, $suid, '', "") . '</td></tr>';
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#' . DATA_OUTPUT_SURVEY . '").change(function (e) {
                                                    $("#' . SMS_POST_SURVEY . '_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
        $returnStr .= "</script>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataMode() . '</td><td>' . $this->displayModesAdmin(DATA_OUTPUT_MODES, DATA_OUTPUT_MODES, MODE_CAPI . "~" . MODE_CATI . "~" . MODE_CASI, "multiple", implode("~", $modes)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataLanguage() . '</td><td>' . $this->displayLanguagesAdmin(DATA_OUTPUT_LANGUAGES, DATA_OUTPUT_LANGUAGES, implode("~", $langs), true, false, false, "multiple", implode("~", $langs)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';

        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelToolsCleanFrom() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_FROM, DATA_OUTPUT_FROM, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr><tr><td>' . Language::labelToolsCleanTo() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_TO, DATA_OUTPUT_TO, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr>';

        if (isset($_COOKIE['uscicvariablecookie'])) {
            $returnStr .= '<tr><td>' . Language::labelOutputDataVarlist() . '</td><td>';
            $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_SUBDATA . ">";
            $returnStr .= "<option value=" . SUBDATA_NO . ">" . Language::optionsSubDataNo() . "</option>";
            $returnStr .= "<option value=" . SUBDATA_YES . ">" . Language::optionsSubDataYes() . "</option>";
            $returnStr .= "</select>";
            $returnStr .= "</td></tr>";
        }

        $returnStr .= '<tr><td>' . Language::labelOutputDataCompleted() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_COMPLETED . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . INTERVIEW_NOTCOMPLETED . ">" . Language::optionsDataNotCompleted() . "</option>";
        $returnStr .= "<option value=" . INTERVIEW_COMPLETED . ">" . Language::optionsDataCompleted() . "</option>";
        $returnStr .= "</select>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataClean() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_CLEAN . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_CLEAN . ">" . Language::optionsDataClean() . "</option>";
        $returnStr .= "<option value=" . DATA_DIRTY . ">" . Language::optionsDataDirty() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataKeepOnly() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_KEEP_ONLY . ">";
        $returnStr .= "<option value=" . DATA_KEEP_NO . ">" . Language::optionsDataKeepNo() . "</option>";
        $returnStr .= "<option value=" . DATA_KEEP_YES . ">" . Language::optionsDataKeepYes() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataHidden() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_HIDDEN . ">";
        $returnStr .= "<option value=" . DATA_NOTHIDDEN . ">" . Language::optionsDataNotHidden() . "</option>";
        $returnStr .= "<option value=" . DATA_HIDDEN . ">" . Language::optionsDataHidden() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        /* format */

        /*
          exportDirectory
          encoding
          outputType
         * 
         */
        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataFormat() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataFileType() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_FILETYPE . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . FILETYPE_STATA . ">" . Language::optionsFileTypeStata() . "</option>";
        $returnStr .= "<option value=" . FILETYPE_CSV . ">" . Language::optionsFileTypeCSV() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataFileName() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_FILENAME . "' ><span class='input-group-addon'>" . Language::labelOutputDataFileNameNoExtension() . "</span></div>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKey() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_PRIMARY_KEY_IN_DATA . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . PRIMARYKEY_YES . ">" . Language::optionsPrimaryKeyInDataYes() . "</option>";
        $returnStr .= "<option value=" . PRIMARYKEY_NO . ">" . Language::optionsPrimaryKeyInDataNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKeyEncryption() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION . "' ><span class='input-group-addon'>" . Language::labelOutputDataPrimaryKeyEncryptionNo() . "</span></div>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataNoData() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_VARIABLES_WITHOUT_DATA . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . VARIABLES_WITHOUT_DATA_YES . ">" . Language::optionsVariablesNoDataInDataYes() . "</option>";
        $returnStr .= "<option value=" . VARIABLES_WITHOUT_DATA_NO . ">" . Language::optionsVariablesNoDataInDataNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataFieldname() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_FIELDNAME_CASE . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . FIELDNAME_LOWERCASE . ">" . Language::optionsFieldnameLowerCase() . "</option>";
        $returnStr .= "<option value=" . FIELDNAME_UPPERCASE . ">" . Language::optionsFieldnameUpperCase() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataValueLabel() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_INCLUDE_VALUE_LABELS . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . VALUELABEL_YES . ">" . Language::optionsValueLabelsYes() . "</option>";
        $returnStr .= "<option value=" . VALUELABEL_NO . ">" . Language::optionsValueLabelsNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataValueLabelNumbers() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . VALUELABELNUMBERS_YES . ">" . Language::optionsValueLabelNumbersYes() . "</option>";
        $returnStr .= "<option value=" . VALUELABELNUMBERS_NO . ">" . Language::optionsValueLabelNumbersNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataMarkEmpty() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_MARK_EMPTY . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . MARKEMPTY_IN_VARIABLE . ">" . Language::optionsMarkEmptyInVariable() . "</option>";
        $returnStr .= "<option value=" . MARKEMPTY_IN_SKIP_VARIABLE . ">" . Language::optionsMarkEmptyInSkipVariable() . "</option>";
        $returnStr .= "<option value=" . MARKEMPTY_NO . ">" . Language::optionsMarkEmptyNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonDownload() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        // $returnStr .= $this->showBottomBar();
        // $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showTimingsData() {
        $suid = loadvar('survey');
        if ($suid == "") {
            $suid = $_SESSION['SUID'];
        }
        $survey = new Survey($suid);


        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.data'), Language::linkData()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelShowTimingsData() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $this->displayComboBox();
        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="researcher.data.timing">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
        $returnStr .= "</form>";

        $returnStr .= '<form ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' id=surveyform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'researcher.data.timingsres'));

        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataSource() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataSurvey() . '</td><td>' . $this->displaySurveys(DATA_OUTPUT_SURVEY, DATA_OUTPUT_SURVEY, $suid, '', "") . '</td></tr>';
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#' . DATA_OUTPUT_SURVEY . '").change(function (e) {
                                                    $("#' . SMS_POST_SURVEY . '_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
        $returnStr .= "</script>";

        $user = new User($_SESSION['URID']);
        $modes = $user->getModes($suid);
        $langs = array();
        foreach ($modes as $m) {
            $langs = array_merge($langs, explode("~", $user->getLanguages($suid, $m)));
        }
        $langs = array_unique($langs);

        $returnStr .= '<tr><td>' . Language::labelOutputDataMode() . '</td><td>' . $this->displayModesAdmin(DATA_OUTPUT_MODES, DATA_OUTPUT_MODES, MODE_CAPI . "~" . MODE_CATI . "~" . MODE_CASI, "multiple", implode("~", $modes)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataLanguage() . '</td><td>' . $this->displayLanguagesAdmin(DATA_OUTPUT_LANGUAGES, DATA_OUTPUT_LANGUAGES, implode("~", $langs), true, false, false, "multiple", implode("~", $langs)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';

        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelToolsCleanFrom() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_FROM, DATA_OUTPUT_FROM, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr><tr><td>' . Language::labelToolsCleanTo() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_TO, DATA_OUTPUT_TO, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr>';

        if (isset($_COOKIE['uscicvariablecookie'])) {
            $returnStr .= '<tr><td>' . Language::labelOutputDataVarlist() . '</td><td>';
            $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_SUBDATA . ">";
            $returnStr .= "<option value=" . SUBDATA_NO . ">" . Language::optionsSubDataNo() . "</option>";
            $returnStr .= "<option value=" . SUBDATA_YES . ">" . Language::optionsSubDataYes() . "</option>";
            $returnStr .= "</select>";
            $returnStr .= "</td></tr>";
        }

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataFormat() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataFileName() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_FILENAME . "' ><span class='input-group-addon'>" . Language::labelOutputDataFileNameNoExtension() . "</span></div>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKey() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_PRIMARY_KEY_IN_DATA . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . PRIMARYKEY_YES . ">" . Language::optionsPrimaryKeyInDataYes() . "</option>";
        $returnStr .= "<option value=" . PRIMARYKEY_NO . ">" . Language::optionsPrimaryKeyInDataNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKeyEncryption() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION . "' ><span class='input-group-addon'>" . Language::labelOutputDataPrimaryKeyEncryptionNo() . "</span></div>";
        $returnStr .= "</td></tr>";


        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonDownload() . '"/>';
        $returnStr .= '</form>';






        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showParaData() {

        $suid = loadvar('survey');
        if ($suid == "") {
            $suid = $_SESSION['SUID'];
        }
        $survey = new Survey($suid);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.data'), Language::linkData()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelShowParadata() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $this->displayComboBox();
        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="researcher.data.paradata">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
        $returnStr .= "</form>";

        $returnStr .= '<form ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' id=surveyform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'researcher.data.paradatares'));

        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataSource() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $user = new User($_SESSION['URID']);
        $returnStr .= '<tr><td>' . Language::labelOutputDataSurvey() . '</td><td>' . $this->displaySurveys(DATA_OUTPUT_SURVEY, DATA_OUTPUT_SURVEY, $suid, '', "") . '</td></tr>';
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#' . DATA_OUTPUT_SURVEY . '").change(function (e) {
                                                    $("#' . SMS_POST_SURVEY . '_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
        $returnStr .= "</script>";
        
        $modes = $user->getModes($suid);
        $langs = array();
        foreach ($modes as $m) {
            $langs = array_merge($langs, explode("~", $user->getLanguages($suid, $m)));
        }
        $langs = array_unique($langs);

        $returnStr .= '<tr><td>' . Language::labelOutputDataMode() . '</td><td>' . $this->displayModesAdmin(DATA_OUTPUT_MODES, DATA_OUTPUT_MODES, MODE_CAPI . "~" . MODE_CATI . "~" . MODE_CASI, "multiple", implode("~", $modes)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataLanguage() . '</td><td>' . $this->displayLanguagesAdmin(DATA_OUTPUT_LANGUAGES, DATA_OUTPUT_LANGUAGES, implode("~", $langs), true, false, false, "multiple", implode("~", $langs)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';

        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelToolsCleanFrom() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_FROM, DATA_OUTPUT_FROM, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr><tr><td>' . Language::labelToolsCleanTo() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_TO, DATA_OUTPUT_TO, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr>';

        if (isset($_COOKIE['uscicvariablecookie'])) {
            $returnStr .= '<tr><td>' . Language::labelOutputDataVarlist() . '</td><td>';
            $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_SUBDATA . ">";
            $returnStr .= "<option value=" . SUBDATA_NO . ">" . Language::optionsSubDataNo() . "</option>";
            $returnStr .= "<option value=" . SUBDATA_YES . ">" . Language::optionsSubDataYes() . "</option>";
            $returnStr .= "</select>";
            $returnStr .= "</td></tr>";
        }

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataFormat() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataFileName() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_FILENAME . "' ><span class='input-group-addon'>" . Language::labelOutputDataFileNameNoExtension() . "</span></div>";
        $returnStr .= "</td></tr>";
        
        $returnStr .= '<tr><td>' . Language::labelOutputDataFileType() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_FILETYPE . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . FILETYPE_STATA . ">" . Language::optionsFileTypeStata() . "</option>";
        $returnStr .= "<option value=" . FILETYPE_CSV . ">" . Language::optionsFileTypeCSV() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataTypeParadata() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEPARADATA . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . PARADATA_RAW . ">" . Language::optionsParadataRaw() . "</option>";
        $returnStr .= "<option value=" . PARADATA_PROCESSED . ">" . Language::optionsParadataProcessed() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKey() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_PRIMARY_KEY_IN_DATA . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . PRIMARYKEY_YES . ">" . Language::optionsPrimaryKeyInDataYes() . "</option>";
        $returnStr .= "<option value=" . PRIMARYKEY_NO . ">" . Language::optionsPrimaryKeyInDataNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKeyEncryption() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION . "' ><span class='input-group-addon'>" . Language::labelOutputDataPrimaryKeyEncryptionNo() . "</span></div>";
        $returnStr .= "</td></tr>";


        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonDownload() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showAuxData() {

        $suid = loadvar('survey');
        if ($suid == "") {
            $suid = $_SESSION['SUID'];
        }
        $survey = new Survey($suid);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.data'), Language::linkData()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelShowAuxData() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $this->displayComboBox();
        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="researcher.data.auxiliarydata">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
        $returnStr .= "</form>";

        $returnStr .= '<form ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' id=surveyform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'researcher.data.auxiliarydatares'));

        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataSource() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataTable() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPE . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_OUTPUT_TYPE_DATARECORD_TABLE . ">" . Language::optionsDataDataRecordTable() . "</option>";
        $returnStr .= "<option value=" . DATA_OUTPUT_TYPE_DATA_TABLE . ">" . Language::optionsDataDataTable() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataSurvey() . '</td><td>' . $this->displaySurveys(DATA_OUTPUT_SURVEY, DATA_OUTPUT_SURVEY, $suid, '', "") . '</td></tr>';
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#' . DATA_OUTPUT_SURVEY . '").change(function (e) {
                                                    $("#' . SMS_POST_SURVEY . '_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
        $returnStr .= "</script>";
        $user = new User($_SESSION['URID']);
        $modes = $user->getModes($suid);
        $langs = array();
        foreach ($modes as $m) {
            $langs = array_merge($langs, explode("~", $user->getLanguages($suid, $m)));
        }
        $langs = array_unique($langs);

        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#' . DATA_OUTPUT_SURVEY . '").change(function (e) {
                                                    $("#' . SMS_POST_SURVEY . '_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
        $returnStr .= "</script>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataMode() . '</td><td>' . $this->displayModesAdmin(DATA_OUTPUT_MODES, DATA_OUTPUT_MODES, MODE_CAPI . "~" . MODE_CATI . "~" . MODE_CASI, "multiple", implode("~", $modes)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDataLanguage() . '</td><td>' . $this->displayLanguagesAdmin(DATA_OUTPUT_LANGUAGES, DATA_OUTPUT_LANGUAGES, implode("~", $langs), true, false, false, "multiple", implode("~", $langs)) . '</td></tr>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';

        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelToolsCleanFrom() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_FROM, DATA_OUTPUT_FROM, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr><tr><td>' . Language::labelToolsCleanTo() . ': </td><td>' . $this->displayDateTimePicker(DATA_OUTPUT_TO, DATA_OUTPUT_TO, "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr>';

        if (isset($_COOKIE['uscicvariablecookie'])) {
            $returnStr .= '<tr><td>' . Language::labelOutputDataVarlist() . '</td><td>';
            $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_SUBDATA . ">";
            $returnStr .= "<option value=" . SUBDATA_NO . ">" . Language::optionsSubDataNo() . "</option>";
            $returnStr .= "<option value=" . SUBDATA_YES . ">" . Language::optionsSubDataYes() . "</option>";
            $returnStr .= "</select>";
            $returnStr .= "</td></tr>";
        }

        $returnStr .= '<tr><td>' . Language::labelOutputDataCompleted() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_COMPLETED . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . INTERVIEW_NOTCOMPLETED . ">" . Language::optionsDataNotCompleted() . "</option>";
        $returnStr .= "<option value=" . INTERVIEW_COMPLETED . ">" . Language::optionsDataCompleted() . "</option>";
        $returnStr .= "</select>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataClean() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_CLEAN . ">";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . DATA_CLEAN . ">" . Language::optionsDataClean() . "</option>";
        $returnStr .= "<option value=" . DATA_DIRTY . ">" . Language::optionsDataDirty() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataKeepOnly() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_KEEP_ONLY . ">";
        $returnStr .= "<option value=" . DATA_KEEP_NO . ">" . Language::optionsDataKeepNo() . "</option>";
        $returnStr .= "<option value=" . DATA_KEEP_YES . ">" . Language::optionsDataKeepYes() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataHidden() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name=" . DATA_OUTPUT_HIDDEN . ">";
        $returnStr .= "<option value=" . DATA_NOTHIDDEN . ">" . Language::optionsDataNotHidden() . "</option>";
        $returnStr .= "<option value=" . DATA_HIDDEN . ">" . Language::optionsDataHidden() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        /* format */

        /*
          exportDirectory
          encoding
          outputType
         * 
         */
        $returnStr .= '<span class="label label-default">' . Language::labelOutputDataFormat() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDataFileName() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_FILENAME . "' ><span class='input-group-addon'>" . Language::labelOutputDataFileNameNoExtension() . "</span></div>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKey() . '</td><td>';
        $returnStr .= "<select class='selectpicker show-tick' name='" . DATA_OUTPUT_PRIMARY_KEY_IN_DATA . "'>";
        //$returnStr .= "<option></option>";
        $returnStr .= "<option value=" . PRIMARYKEY_YES . ">" . Language::optionsPrimaryKeyInDataYes() . "</option>";
        $returnStr .= "<option value=" . PRIMARYKEY_NO . ">" . Language::optionsPrimaryKeyInDataNo() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelOutputDataPrimaryKeyEncryption() . '</td><td>';
        $returnStr .= "<div class='input-group'><input type=text class='form-control' name='" . DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION . "' ><span class='input-group-addon'>" . Language::labelOutputDataPrimaryKeyEncryptionNo() . "</span></div>";
        $returnStr .= "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonDownload() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function getResponseData() {
        $survey = new Survey(getSurvey());
        $title = $survey->getTitle();
        $sub = Language::labelResponseDataSubtitle();
        $names = array(Language::labelResponseDataStarted(), Language::labelResponseDataCompleted());
        $actiontype = array('begintime', 'endtime');


        $returnStr = '<script src="../js/export-csv.js"></script>';
        $returnStr .= "<script type='text/javascript'>


var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
                type: 'spline',
                zoomType: 'x'
            },
            title: {
                text: '" . $title . "'
            },
            subtitle: {
                text: 'Source: " . Language::labelNubis() . "'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                }
            },
            yAxis: {
                title: {
                    text: '# " . Language::labelResponseDataRespondents() . "'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y + '" . Language::labelResponseDataRespondents() . "';
                }
            },
            
            series: [";

        foreach ($names as $key => $name) {
            if ($key != 0) {
                $returnStr .= ',';
            }
            $returnStr .= "{
                name: '" . $name . "',
                data: [";
            $returnStr .= $this->getFieldNotNull(getSurvey(), $actiontype[$key]);
            $returnStr .= "                ]
            }";
        }
        $returnStr .= "
      ]
        });
    //}); 


</script>";

        return $returnStr;
    }

    function getFieldNotNull($survey, $fieldname) {
        global $db;
        $dataStr = '';
        $actions = array();

        //99900174

        $query = 'select DATE(ts) as dateobs, count(*) as cntobs from ' . Config::dbSurveyData() . '_data where suid = ' . $survey . ' and variablename="' . $fieldname . '" and length(primkey) > ' . Config::getMinimumPrimaryKeyLength() . ' and length(primkey) < ' . Config::getMaximumPrimaryKeyLength() . '  and answer is not null group by DATE(ts) order by DATE(ts) asc';
        $total = 0;
        $dataStr .= "[Date.UTC(2014,  6, 20), 0   ],";
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $key = $row['dateobs'];
            $total += $row['cntobs'];
            $dataStr .= "[Date.UTC(" . substr($key, 0, 4) . ", " . (substr($key, 5, 2) - 1) . ", " . substr($key, 8, 2) . "), " . $total . "],";
        }
        $returnStr = rtrim($dataStr, ',');
        return $returnStr;
    }

    function showSample($message = '') {
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::linkUnassigned() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $message;

        $displaySms = new DisplaySms();
        $returnStr .= $displaySms->showAvailableUnassignedHouseholds();

        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.sample.download')) . '&puid= ' . loadvar('puid', 0) . '">' . Language::labelResearcherDownloadCSV() . '</a>';
        $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.sample.download.gps')) . '&puid= ' . loadvar('puid', 0) . '">' . Language::labelResearcherDownloadGPS() . '</a>';


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function getContactData() {
        $title = Language::messageSMSTitle();
        $sub = Language::labelResponseDataContactsSub();
        $names = Language::labelResponseDataContacts();
        $actiontype = array(101, 103, 109, 502, 504);


        //$returnStr = '<script src="js/export-csv.js"></script>';
        $returnStr .= "<script type='text/javascript'>


var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
                type: 'spline',
                zoomType: 'x'
            },
            title: {
                text: '" . $title . "'
            },
            subtitle: {
                text: 'Source: " . Language::labelNubis() . "'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                }
            },
            yAxis: {
                title: {
                    text: '# " . Language::labelResponseDataRespondents() . "'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y + '" . Language::labelResponseDataRespondents() . "';
                }
            },
            
            series: [";

        foreach ($names as $key => $name) {
            if ($key != 0) {
                $returnStr .= ',';
            }
            $returnStr .= "{
                name: '" . $name . "',
                data: [";
            $returnStr .= $this->getContactCodeData($actiontype[$key]);
            $returnStr .= "                ]
            }";
        }

        $returnStr .= "
      ]
        });
    //}); 


</script>";

        return $returnStr;
    }

    function getContactCodeData($code) {
        global $db;
        $dataStr = '';
        $actions = array();
        $query = 'select DATE(ts) as dateobs, count(*) as cntobs from ' . Config::dbSurvey() . '_contacts where code = ' . $code . ' group by DATE(ts) order by DATE(ts) asc';
        $total = 0;
        $dataStr .= "[Date.UTC(2014,  6, 20), 0   ],";
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $key = $row['dateobs'];
            $total += $row['cntobs'];
            $dataStr .= "[Date.UTC(" . substr($key, 0, 4) . ", " . (substr($key, 5, 2) - 1) . ", " . substr($key, 8, 2) . "), " . $total . "],";
        }
        $returnStr = rtrim($dataStr, ',');
        return $returnStr;
    }

    function createChart($title, $data, $brackets = array()) {

        $bracks = '';
        for ($i = 0; $i < sizeof($brackets); $i++) {
            $br = $brackets[$i];
            $bracks .= "'" . $br . "'";
            if ($i + 1 <= sizeof($brackets)) {
                $bracks .= ",";
            }
        }
        $returnStr = '<script src="js/export-csv.js"></script>';
        $returnStr .= "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
            type: 'column',
            zoomType: 'x'            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Source: " . Language::labelNubis() . "'
        },
        xAxis: {
            categories: [
                " . $bracks . "
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '" . Language::labelNumberOfRespondents() . "'
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },            
        series: 
        
        [{  
            name: '" . Language::labelNumberOfResponses() . "',
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function getTimingsData($title, $data, $brackets) {

        $bracks = '';
        for ($i = 0; $i < sizeof($brackets); $i++) {
            $br = $brackets[$i];
            $bracks .= "'" . $br . "'";
            if ($i + 1 <= sizeof($brackets)) {
                $bracks .= ",";
            }
        }
        //$returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
            type: 'column',
            zoomType: 'x'            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Source: " . Language::labelNubis() . "'
        },
        xAxis: {
            categories: [
                " . $bracks . "
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '" . Language::labelNumberOfRespondents() . "'
            }
        },
        tooltip: {
            headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
            pointFormat: '<tr>' +
                '<td style=\"padding:0\"><b>{point.y:.0f} " . Language::labelCompletedInterviews() . "</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },            
        series: 
        
        [{  
            name: '" . Language::labelCompletedInterviewsUpper() . "',
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function getTimingsDataOverTime($title, $data, $brackets) {

        $bracks = '';
        for ($i = 0; $i < sizeof($brackets); $i++) {
            $br = $brackets[$i];
            $bracks .= "'" . $br . "'";
            if ($i + 1 <= sizeof($brackets)) {
                $bracks .= ",";
            }
        }
        //$returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
            type: 'column',
            zoomType: 'x'            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Source: " . Language::labelNubis() . "'
        },
        xAxis: {
            categories: [
                " . $bracks . "
            ],
            crosshair: true
        },
        yAxis: {
            title: {
                text: '" . Language::labelTimeSpent() . "'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' " . Language::labelTimeSpentMinutes() . "'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },           
        series: 
        
        [{  
            name: '" . Language::labelTimeSpentAverage() . "',
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function displayRespondents($suid) {
        $data = new Data();
        $respondents = $data->getRespondentPrimKeys($suid, false, "ts");
        $returnStr = "<select class='selectpicker show-tick' name=respondent id=respondent>";
        foreach ($respondents as $respondent) {
            $returnStr .= "<option value='" . $respondent . "'>" . $respondent . "</option>";
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function getTimingsDataRespondent($title, $data, $brackets) {

        $bracks = '';
        for ($i = 0; $i < sizeof($brackets); $i++) {
            $br = $brackets[$i];
            $bracks .= "'" . $br . "'";
            if ($i + 1 <= sizeof($brackets)) {
                $bracks .= ",";
            }
        }
        //$returnStr .= '<script src="js/export-csv.js"></script>';
        $returnStr .= "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
            type: 'column',
            zoomType: 'x'            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Source: " . Language::labelNubis() . "'
        },
        xAxis: {
            categories: [
                " . $bracks . "
            ],
            crosshair: true
        },
        yAxis: {
            title: {
                text: '" . Language::labelTimeSpent() . "'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ' " . Language::labelTimeSpentMinutes() . "'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },           
        series: 
        
        [{  
            name: '" . Language::labelTimeSpentTotal() . "',
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function showReportsPlatform() {

        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }

        $survey = new Survey($_SESSION['SUID']);

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelResearcherPlatform() . '</li>';
        $returnStr .= '</ol>';


        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelResearcherPlatform() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        //if (sizeof($surveys) > 0) {
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelOutputScreenDumpsSurvey() . '</td><td>' . $this->displaySurveys("survey", "survey", $_SESSION["SUID"]) . '</td></tr>';
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#survey").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        //}
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        //$returnStr .= "<option></option>";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        $returnStr .= '</table><br/>';
        $returnStr .= '</div>';
        $returnStr .= "</form>";

        // high chart
        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<script src="js/export-csv.js"></script>';

        $data = new Data();
        $data = $data->getPlatformData($_SESSION['SUID']);

        // determine devices
        $devices = array();

        require_once("detection_bootstrap.php");
        $detect = new Mobile_Detect();

        $total = sizeof($data);
        if ($total == 0) {
            $total = 1;
        }
        $mobilecount = 0;
        $tabletcount = 0;
        $othercount = 0;

        $browsercounts = array();
        $oscounts = array();
        foreach ($data as $d) {
            $detect->setUserAgent($d);
            if ($detect->isMobile() && !$detect->isTablet()) {
                $mobilecount++;
            } else if ($detect->isTablet()) {
                $tabletcount++;
            } else {
                $othercount++;
            }

            $browser = new Browser($d);
            $name = $browser->getBrowser();
            if ($name == 'Navigator') { // rename if android mobile browser
                $name = "Android browser";
            }
            if (isset($browsercounts[ucwords($name)])) {
                $browsercounts[ucwords($name)]++;
            } else {
                $browsercounts[ucwords($name)] = 1;
            }

            //$os = new Os($d);
            //$name = $os->getName();
            $platform = $browser->getPlatform();
            if (isset($oscounts[ucwords($name)])) {
                $oscounts[ucwords($name)]++;
            } else {
                $oscounts[ucwords($name)] = 1;
            }
        }

        // high chart for device pie chart
        $returnStr.= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
        $returnStr.= $this->getDeviceData($survey->getName(), '{name: "Mobile", y: ' . (round($mobilecount / $total, 2)) * 100 . '}, {name: "Tablet", y: ' . (round($tabletcount / $total, 2)) * 100 . '}, {name: "Laptop/desktop/other", y: ' . (round($othercount / $total, 2)) * 100 . '}');

        // high chart for browsers
        $returnStr.= '<br/><br/><div id="chart2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

        $browsers = '';
        ksort($browsercounts);
        foreach ($browsercounts as $b => $count) {
            if ($browsers != '') {
                $browsers .= ",";
            }
            $browsers .= '{name: "' . $b . '", y: ' . (round($count / $total, 2)) * 100 . '}';
        }
        $returnStr.= $this->getBrowserData($survey->getName(), $browsers);

        // high chart for operating system
        $returnStr.= '<br/><br/><div id="chart3" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

        $os = '';
        ksort($oscounts);
        foreach ($oscounts as $b => $count) {
            if ($os != '') {
                $os .= ",";
            }
            $os .= '{name: "' . $b . '", y: ' . (round($count / $total, 2)) * 100 . '}';
        }
        $returnStr.= $this->getOSData($survey->getName(), $os);

        //
        $returnStr .= '</div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);

        return $returnStr;
    }

    function getBrowserData($title, $data) {

        $returnStr = "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart2',
            type: 'pie',
	     plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Browsers (Source: " . Language::labelNubis() . ")'
        },
        plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },           
        series: 
        
        [{  
            name: 'Browser',
            colorByPoint: true,
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function getOSData($title, $data) {

        $returnStr = "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart3',
            type: 'pie',
	     plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Operating systems (Source: " . Language::labelNubis() . ")'
        },
        plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },           
        series: 
        
        [{  
            name: 'Operating system',
            colorByPoint: true,
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function getDeviceData($title, $data) {

        //$returnStr = '<script src="../js/export-csv.js"></script>';
        $returnStr = "<script type='text/javascript'>
            

var chart = new Highcharts.Chart({

    chart: {
        renderTo: 'chart1',
            type: 'pie',
	     plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Devices (Source: " . Language::labelNubis() . ")'
        },
        plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },           
        series: 
        
        [{  
            name: 'Device type',
            colorByPoint: true,
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

    function showReportsParadata($content = "") {
        $survey = new Survey($_SESSION['SUID']);
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()), 'label' => Language::headerReports());
        $headers[] = array('link' => '', 'label' => Language::headerReportsParadata());

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li class="active">' . Language::headerReportsParadata() . '</li>';
        $returnStr .= '</ol>';


        $returnStr .= $content;
        $returnStr .= $this->displayComboBox();
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {
            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="researcher.reports.paradata">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
            $returnStr .= "</form>";

            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelTestSurvey() . "</td><td><select onchange='document.getElementById(\"" . SMS_POST_SURVEY . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();' name=" . POST_PARAM_SUID . " class='selectpicker show-tick'>";
            $current = new Survey(getSurvey());
            foreach ($surveys as $s) {
                $selected = "";
                if ($s->getSuid() == $current->getSuid()) {
                    $selected = "SELECTED";
                }
                $returnStr .= "<option $selected value=" . $s->getSuid() . '>' . $s->getName() . '</option>';
            }
            $returnStr .= "</select></td></tr>";
            $returnStr .= '</table><br/><br/>';

            $sections = $survey->getSections();
            foreach ($sections as $section) {
                $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.paradata.section', 'seid' => $section->getSeid())) . '" class="list-group-item">' . $section->getName() . ' ' . $section->getDescription() . '</a>';
            }
            $returnStr .= "</div>";
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsParadataSection($seid) {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerOutput()), 'label' => Language::headerOutputData());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.statistics'), Language::headerOutputStatistics()), 'label' => Language::headerOutputStatistics());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.paradata'), Language::headerOutputStatisticsParadata()), 'label' => Language::headerOutputStatisticsParadata());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.paradata', 'suid' => $suid), $survey->getName()), 'label' => $survey->getName());
        $headers[] = array('link' => '', 'label' => $section->getName());
        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.paradata'), Language::headerReportsParadata()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.paradata', 'suid' => $suid), $survey->getName()) . '</li>';
        $returnStr .= '<li class="active">' . $section->getName() . '</li>';

        $returnStr .= '</ol>';

        $variables = $survey->getVariableDescriptives($seid);
        foreach ($variables as $variable) {
            if (!inArray($variable->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'researcher.reports.paradata.variable', 'seid' => $seid, 'vsid' => $variable->getVsid())) . '" class="list-group-item">' . $variable->getName() . ' ' . $variable->getDescription() . '</a>';
            }
        }
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showReportsParadataVariable($seid, $vsid) {
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            if (loadvar(DATA_OUTPUT_TYPEDATA) == DATA_TEST) {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_TEST;
            } else {
                $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
            }
        } else {
            $_SESSION[SURVEY_EXECUTION_MODE] = SURVEY_EXECUTION_MODE_NORMAL;
        }
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $variable = $survey->getVariableDescriptive($vsid);
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerOutput()), 'label' => Language::headerOutputData());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.statistics'), Language::headerOutputStatistics()), 'label' => Language::headerOutputStatistics());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.paradata'), Language::headerOutputStatisticsParadata()), 'label' => Language::headerOutputStatisticsParadata());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.paradata', 'suid' => $suid), $survey->getName()), 'label' => $survey->getName());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'researcher.reports.paradata.section', 'seid' => $seid), $section->getName()), 'label' => $section->getName());

        $headers[] = array('link' => '', 'label' => $variable->getName());

        $returnStr = $this->showResearchHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports'), Language::headerReports()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.paradata'), Language::headerReportsParadata()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.paradata', 'suid' => $suid), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'researcher.reports.paradata.section', 'suid' => $suid), $section->getName()) . '</li>';
        $returnStr .= '<li class="active">' . $variable->getName() . '</li>';

        $returnStr .= '</ol>';

        $returnStr .= '<form id=surveyform method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelParadataDetails() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelOutputDataType() . '</td><td>';
        $returnStr .= "<select id='typedata' class='selectpicker show-tick' name=" . DATA_OUTPUT_TYPEDATA . ">";
        $selected = array('', '');
        if (loadvar(DATA_OUTPUT_TYPEDATA) != "") {
            $selected[loadvar(DATA_OUTPUT_TYPEDATA)] = "selected";
        }

        $returnStr .= "<option " . $selected[0] . " value=" . DATA_REAL . ">" . Language::optionsDataReal() . "</option>";
        $returnStr .= "<option " . $selected[1] . " value=" . DATA_TEST . ">" . Language::optionsDataTest() . "</option>";
        $returnStr .= "</select>";
        $returnStr .= "</td></tr>";
        $returnStr .= '<script type=text/javascript>
                        $(document).ready(function(){
                            $("#typedata").on("change", function(event) {
                                document.getElementById("surveyform").submit();
                            });
                        });
                    </script>';
        $returnStr .= "</form>";

        $returnStr .= '</table>';

        $returnStr .= '<br/><table>';
        $returnStr .= '<tr><td valign=top style="min-width: 100px;">' . Language::labelTypeEditGeneralQuestion() . ": </td><td valign=top>";
        $returnStr .= $variable->getQuestion() . "</td></tr>";
        $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditGeneralAnswerType() . ": </td><td valign=top>";
        $answertype = $variable->getAnswerType();
        $arr = Common::getAnswerTypes();
        $returnStr .= $arr[$answertype] . "</td></tr>";
        if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK))) {
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditGeneralCategories() . ": </td><td valign=top>";
            $returnStr .= str_replace("\r\n", "<br/>", $variable->getOptionsText()) . "</td></tr>";
        } else if (inArray($answertype, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditRangeMinimum() . ": </td><td valign=top>";
            $returnStr .= $variable->getMinimum() . "</td></tr>";
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditRangeMaximum() . ": </td><td valign=top>";
            $returnStr .= $variable->getMaximum() . "</td></tr>";
        }

        if ($variable->isArray()) {
            $returnStr .= $this->displayComboBox();
            $returnStr .= '<tr><td valign=top>' . Language::labelTypeEditGeneralArrayInstance() . ": </td><td valign=top>";
            $options = $this->getArrayData($_SESSION['SUID'], $variable->getName());
            $returnStr .= "<form id=instanceform method=post>";
            $returnStr .= "<select class='selectpicker show-tick' id='arrayinstance' name='arrayinstance'>";
            foreach ($options as $op) {
                $returnStr .= "<option value='" . $op . "'>" . $op . "</option>";
            }
            $returnStr .= "</select>";
            $returnStr .= "</td></tr>";
            $params = getSessionParams();
            $params['vsid'] = $variable->getVsid();
            $returnStr .= setSessionParamsPost($params);
            $returnStr .= "</form>";
            $returnStr .= "<script type='text/javascript'>";
            $returnStr .= "$('#arrayinstance').change(function () {
                                $('#instanceform').submit();
                            });";
            $returnStr .= "</script>";
        }

        $returnStr .= "</table></div>";

        $returnStr .= '<span class="label label-default">' . Language::labelAggregateData() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $data = new Data();
        $brackets = array();
        $varname = $variable->getName();
        if ($variable->isArray()) {
            if (loadvar("arrayinstance") != "") {
                $varname = loadvar("arrayinstance");
            } else {
                $varname = $varname . "[1]";
            }
        }
        $paradata = $data->getParaData($variable, $varname);

        //$aggdata = array(2,5);
        if (sizeof($paradata) == 0) {
            $returnStr .= "<br>" . $this->displayWarning(Language::messageNoData());
        } else {

            $returnStr .= '<script src="js/highcharts.js"></script>';
            $returnStr .= '<script src="js/modules/exporting.js"></script>';
            $returnStr .= '<script src="js/export-csv.js"></script>';
            $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
            $errorlabels = Language::errorCodeLabels();
            $brackets = array();
            foreach ($paradata as $k => $p) {
                if (isset($errorlabels[$k])) {
                    $brackets[] = $errorlabels[$k];
                }
            }
            $returnStr .= $this->createParadataChart($variable->getName(), implode(",", array_values($paradata)), $brackets);
        }
        $returnStr .= "</div>";

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function createParaDataChart($title, $data, $brackets = array()) {

        $bracks = '';
        for ($i = 0; $i < sizeof($brackets); $i++) {
            $br = $brackets[$i];
            $bracks .= "'" . $br . "'";
            if ($i + 1 <= sizeof($brackets)) {
                $bracks .= ",";
            }
        }
        $returnStr .= "<script type='text/javascript'>           
var chart = new Highcharts.Chart({
    chart: {
        renderTo: 'chart1',
            type: 'column',
            zoomType: 'x'            
        },
        title: {
            text: '" . $title . "'
        },
        subtitle: {
            text: 'Source: " . Language::labelNubis() . "'
        },
        xAxis: {
            categories: [
                " . $bracks . "
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '" . Language::labelErrors() . "'
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },            
        series:         
        [{  
            name: '" . Language::labelNumberOfTimes() . "',
            data: [" . $data . "]        
            ";

        $returnStr.= "                }]
            });
</script>";
        return $returnStr;
    }

}

?>