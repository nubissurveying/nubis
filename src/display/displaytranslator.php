<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayTranslator extends Display {

    public function __construct() {
        parent::__construct();
    }

    public function showMain() {
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');

        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'translator.surveys')) . '" class="list-group-item">' . Language::linkSurvey() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'translator.output')) . '" class="list-group-item">' . Language::linkOutput() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'translator.tools')) . '" class="list-group-item">' . Language::linkTools() . '</a>';
        $returnStr .= '</div>';
        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showTranslatorHeader($title, $extra = '') {

        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }

        $extra2 = '<link href="js/formpickers/css/bootstrap-formhelpers.min.css" rel="stylesheet">
                  <link href="css/uscicadmin.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">                  
                    ';
        $returnStr = $this->showHeader(Language::messageSMSTitle(), $extra . $extra2);
        $returnStr .= $this->displayOptionsSidebar("optionssidebarbutton", "optionssidebar", "translator.search");
        $returnStr .= $this->bindAjax();
        $returnStr .= $this->getDirtyForms();
        return $returnStr;
    }

    public function showNavBar() {
        $returnStr = "";
        //$returnStr = $this->checkForm();
        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return $returnStr;
        }

        if (strpos($_SESSION['LASTPAGE'], 'translator.survey') === 0) {
            $surveyActive = ' active';
            $outputActive = '';
            $toolsActive = '';
        }
        if (strpos($_SESSION['LASTPAGE'], 'translator.output') === 0) {
            $surveyActive = '';
            $outputActive = ' active';
            $toolsActive = '';
        }
        if (strpos($_SESSION['LASTPAGE'], 'translator.tools') === 0) {
            $surveyActive = '';
            $outputActive = '';
            $toolsActive = ' active';
        }

        $user = new User($_SESSION['URID']);
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
            <a class="navbar-brand" href="' . setSessionParams(array('page' => 'translator.home')) . '">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">

            <li class="dropdown' . $surveyActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkSurvey() . ' <b class="caret"></b></a>';

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<ul class="dropdown-menu">';
        foreach ($surveys as $survey) {
            $span = '';
            if (isset($_SESSION['SUID']) && $_SESSION['SUID'] == $survey->getSuid()) {
                $span = ' <span class="glyphicon glyphicon-chevron-down"></span>';
            }
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey', 'suid' => $survey->getSuid()), $survey->getName() . $span) . '</li>';
        }
        $returnStr .= '</ul>';
        $returnStr .= '</li>';

        $returnStr .= '<li class="dropdown' . $outputActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkOutput() . ' <b class="caret"></b></a>';
        $returnStr .= '<ul class="dropdown-menu">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.output.documentation'), '<span class="glyphicon glyphicon-file"></span> ' . Language::linkDocumentation()) . '</li>';
        $returnStr .= '</ul></li>';

        $returnStr .= '<li class="dropdown' . $toolsActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkTools() . ' <b class="caret"></b></a>';
        $returnStr .= '<ul class="dropdown-menu">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.tools.test'), '<span class="glyphicon glyphicon-comment"></span> ' . Language::linkTester()) . '</li>';
        $returnStr .= '</ul></li>';

        $returnStr .= '</ul>
            <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . $user->getUsername() . '<b class="caret"></b></a>
                 <ul class="dropdown-menu">
			<li><a href="' . setSessionParams(array('page' => 'translator.preferences')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkPreferences() . '</a></li>
                    <li class="divider"></li>
                   <li><a href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>
                 </ul>
             </li>
            </ul>
';
        $returnStr .= $this->showSearch();
        
        $returnStr .= '
          </div><!--/.nav-collapse -->
        </div>
      </div>
';
        
        $returnStr .= "<div id='content'>";
        return $returnStr;
    }
    
    function showSearch() {
        $returnStr = '<form id="searchform" class="navbar-form navbar-right" role="search">
                        <div class="input-group" style="width:250px">'
                . setSessionParamsPost(array("page" => "translator.search")) . '
                          <input name="search" type="text" class="form-control">
                          <span class="input-group-btn">
                            <button id="searchbutton" class="btn btn-default" type="submit">' . Language::labelSearch() . '</button>
                          </span>
                        </div>
                        </form>

                        <script type="text/javascript">                            

                            // Attach a submit handler to the form
                            $( "#searchform" ).submit(function( event ) {

                            // Stop form from submitting normally
                            event.preventDefault();

                            // Get some values from elements on the page:
                            var $form = $( this ),
                            term = $form.find( "input[name=\'search\']" ).val(),
                            r = $form.find( "input[name=\'r\']" ).val(),
                            url = $form.attr( "action" );

                            // Send the data using post
                            var posting = $.post( url, { r: r, search: term, updatesessionpage: 2 } );

                            // Put the results in a div
                            posting.done(function( data ) {
                            $( "#optionssidebar" ).empty().append( $( data ));
                            });
                            
                            // open side bar
                            $.sidr(\'open\', \'optionssidebar\');
                            
                            });
                        </script>
        ';
        return $returnStr;
    }

    function showBottomBar() {
        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }
        $returnStr = '</div>
    <div id="footer">
      <div class="container">
        <p class="text-muted credit" style="text-align:right">' . Language::nubisFooter() . '</p>
      </div>
    </div>
    <div class="waitmodal"></div>
';
        return $returnStr;
    }

    function showSurveys($message = "") {

        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . Language::headerSurveys() . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= $message;
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {

            $returnStr .= '<table class="table table-striped table-bordered pre-scrollable table-condensed table-hover">';
            $show = false;
            foreach ($surveys as $survey) {
                if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                    $show = true;
                }
            }
            if ($show) {
                $returnStr .= '<tr><th></th><th width=15px>' . Language::labelTypeEditGeneralStatus() . '</th><th>' . Language::labelTypeEditGeneralName() . '</th><th>' . Language::labelTypeEditGeneralDescription() . '</th></tr>';
            } else {
                $returnStr .= '<tr><th></th><th>' . Language::labelTypeEditGeneralName(). '</th><th>' . Language::labelTypeEditGeneralDescription() . '</th></tr>';
            }

            foreach ($surveys as $survey) {
                $returnStr .= '<tr><td>';

                $span = "";
                if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                    $status = "glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($survey->isTranslated()) {
                        $status = "glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<td align=middle><span title="' . $statustext . '" class="' . $status . '"></span></td>';
                }
                $returnStr .= '<a data-placement="right" data-html="true" href="' . setSessionParams(array('page' => 'translator.survey', 'suid' => $survey->getSuid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= $span . '<td>' . $survey->getName() . '</td><td>' . $survey->getDescription() . '</td></tr>';
            }
            $returnStr .= '</table>';
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoSurveysYet());
        }
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function getSurveyTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';
        $survey = new Survey($_SESSION['SUID']);
        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedSections()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(0);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . $span . Language::labelSections() . '</span></a>';

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedTypes()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(2);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . $span . Language::labelTypes() . '</span></a>';


        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedGroups()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        if ($_SESSION['VRFILTERMODE_SECTION'] == 3) {
            $returnStr .= ' <span class="label ' . $active[3] . '">' . $span . Language::labelGroups() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(3);$(\'#surveysidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . $span . Language::labelGroups() . '</span></a>';
        }

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-ok";
            $statustext = Language::messageTranslationStatusComplete();
            if ($survey->isTranslatedLayout() == false || $survey->isTranslatedAssistance() == false) {
                $status = "glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(1);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . $span . Language::labelTexts() . '</span></a>';

        return $returnStr;
    }

    function showSurvey($message = "") {

        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());

        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . $survey->getName() . '</li>';

        if ($_SESSION['VRFILTERMODE_SURVEY'] == 0) {
            $returnStr .= '<li class="active">' . Language::headerSections() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 1) {
            $returnStr .= '<li class="active">' . Language::headerSettings() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 2) {
            $returnStr .= '<li class="active">' . Language::headerTypes() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 3) {
            $returnStr .= '<li class="active">' . Language::headerGroups() . '</li>';
        } else {
            $returnStr .= '<li class="active">' . Language::headerSections() . '</li>';
        }

        $returnStr .= '</ol>';

        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $returnStr .= $message;

        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';

        if ($_SESSION['VRFILTERMODE_SURVEY'] == 0) {
            $survey = new Survey($_SESSION['SUID']);
            $sections = $survey->getSections();
            $returnStr .= $this->showSections($sections);
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 1) {
            $returnStr .= $this->showSettingsList();
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 2) {
            $survey = new Survey($_SESSION['SUID']);
            $types = $survey->getTypes(true); /// exclude unused types
            $returnStr .= $this->showTypes($types);
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 3) {
            $survey = new Survey($_SESSION['SUID']);
            $groups = $survey->getGroups();
            $returnStr .= $this->showGroups($groups);
        } else {
            $survey = new Survey($_SESSION['SUID']);
            $sections = $survey->getSections();
            $returnStr .= $this->showSections($sections);
        }
        $returnStr .= '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '</div>';

        $returnStr .= '</div></div></div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showSurveySideBar($survey, $filter = 0) {
        $returnStr = '<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">';
        $returnStr .= '<center><span class="label label-default">' . $survey->getName() . '</span></center>';
        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        $returnStr .= $this->displaySurveySideBarFilter($survey, $filter);
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div></div>';
        return $returnStr;
    }

    function displaySurveySideBarFilter($survey, $filter = 0) {
        $active = array('', '', '', '');
        $active[$filter] = ' active';
        $params = getSessionParams();
        $user = new User($_SESSION['URID']);
        $modes = $user->getModes(getSurvey());

        /* mode drop down */
        if (sizeof($modes) > 1) {
            $returnStr = '<form id=modeform method="post">';
            $returnStr .= '<input type=hidden name=r value="' . setSessionsParamString($params) . '">';
            $returnStr .= $this->displayModesAdmin("surveymode", "surveymode", getSurveyMode(), "", implode("~", $modes));
            $returnStr .= '<script type=text/javascript>
                              function submitModeForm() {
                                    var values = $("#modeform").serialize();
                                        values += "&' . POST_PARAM_AJAX_LOAD . '=' . AJAX_LOAD . '&ignoreres=1";

                                        // Send the data using post
                                        var posting = $.post( $("#modeform").attr("action"), values );

                                        posting.done(function( data ) {       
                                          $("#content").html( $( data ).html());
                                          $("[data-hover=\'dropdown\']").dropdownHover();  
                                        });
                                }
                                        
                                $(document).ready(function(){
                                    $("#surveymode").on("change", function(event) {
                                        var dirty = $.DirtyForms.isDirty();                                        
                                        if (dirty) {
                                            var r = confirm("' . Language::labelUnsavedChangesMessageConfirm() . '");
                                            if (r == true) {
                                                submitModeForm();
                                            }
                                         }
                                        else {
                                            submitModeForm();
                                        }                                                                                                        
                                    });
                                });
                                </script>';
            $returnStr .= "</form>";
        }

        /* language dropdown */
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        $default = $survey->getDefaultLanguage();
        if (!inArray($default, $langs)) {
            $langs[] = $default;
        }

        if (sizeof($langs) > 1) {
            $returnStr .= '<form id=languageform method="post">';
            $returnStr .= '<input type=hidden name=r value="' . setSessionsParamString($params) . '">';
            $returnStr .= $this->displayLanguagesAdmin("surveylanguage", "surveylanguage", getSurveyLanguage(), true, false, true, "", implode("~", $langs));
            $returnStr .= '<script type=text/javascript>
                                    function submitLanguageForm() {
                                    var values = $("#languageform").serialize();
                                        values += "&' . POST_PARAM_AJAX_LOAD . '=' . AJAX_LOAD . '&ignoreres=1";

                                        // Send the data using post
                                        var posting = $.post( $("#modeform").attr("action"), values );

                                        posting.done(function( data ) {       
                                          $("#content").html( $( data ).html());
                                          $("[data-hover=\'dropdown\']").dropdownHover();  
                                        });
                                }
                                $(document).ready(function(){
                                    $("#surveylanguage").on("change", function(event) {
                                        var dirty = $.DirtyForms.isDirty();                                        
                                        if (dirty) {
                                            var r = confirm("' . Language::labelUnsavedChangesMessageConfirm() . '");
                                            if (r == true) {
                                                submitLanguageForm();
                                            }
                                         }
                                        else {
                                            submitLanguageForm();
                                        }                                                                                                        
                                    });
                                });
                                </script>';
            $returnStr .= "</form>";
        }

        $returnStr .= '<form method="post" id="surveysidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_survey" id="vrfiltermode_survey" value="' . $filter . '">';
        $returnStr .= '<div class="btn-group">';

        $returnStr .= '<div class="btn-group">';

        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedSections()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        $returnStr .= '<button class="btn btn-default' . $active[0] . ' dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" onclick="$(\'#vrfiltermode_survey\').val(0);$(\'#surveysidebar\').submit();">' . $span . Language::labelSections() . '</button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';

        $sections = $survey->getSections();
        foreach ($sections as $section) {
            $span = "";
            if ($show) {
                $status = "glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($section->isTranslated()) {
                    $status = "glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
            }
            $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'translator.survey.section', 'seid' => $section->getSeid())) . '">' . $span . $section->getName() . '</a></li>';
        }
        $returnStr .= '</ul>';
        $returnStr .= '</div>';

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedTypes()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        $returnStr .= '<button class="btn btn-default' . $active[2] . '" onclick="$(\'#vrfiltermode_survey\').val(2);$(\'#surveysidebar\').submit();">' . $span . Language::labelTypes() . '</button>';
        $returnStr .= '</div>';


        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedGroups()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        //$returnStr .= '<li><a ' . $active[3] . ' onclick="$(\'#vrfiltermode_section\').val(3);$(\'#sectionsidebar\').submit();">' . $span . Language::labelGroups() . '</a></li>';
        $returnStr .= '<button class="btn btn-default ' . $active[3] . '" onclick="$(\'#vrfiltermode_survey\').val(3);$(\'#sectionsidebar\').submit();">' . $span . Language::labelGroups() . '</button>';


        $returnStr .= '<div class="btn-group">';

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-ok";
            $statustext = Language::messageTranslationStatusComplete();
            if ($survey->isTranslatedLayout() == false || $survey->isTranslatedAssistance() == false) {
                $status = "glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        $returnStr .= '<button class="btn btn-default' . $active[1] . ' dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" onclick="$(\'#vrfiltermode_survey\').val(1);$(\'#surveysidebar\').submit();">' . $span . Language::labelTexts() . '</button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedAssistance()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'translator.survey.translatesettingsassistance', 'suid' => $survey->getSuid())) . '">' . $span . Language::labelSettingsAssistance() . '</a></li>';

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedLayout()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'translator.survey.translatesettingslayout', 'suid' => $survey->getSuid())) . '">' . $span . Language::labelSettingsLayout() . '</a></li>';
        $returnStr .= '</ul>';
        $returnStr .= '</div>';


        $returnStr .= '</form>';

        return $returnStr;
    }

    function showSections($sections) {
        $returnStr = '';
        if (sizeof($sections) > 0) {

            $returnStr .= '<table class="table table-striped table-bordered pre-scrollable table-condensed table-hover">';
            $survey = new Survey($_SESSION['SUID']);
            $show = false;
            if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                $show = true;
            }
            if ($show) {
                $returnStr .= '<tr><th width=15px></th><th style="cursor: default;" width=15px>' . Language::labelTypeEditGeneralStatus() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th></tr>';
            } else {
                $returnStr .= '<tr><th width=15px></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th></tr>';
            }

            foreach ($sections as $section) {
                $span = "";
                if ($show) {
                    $status = "glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($section->isTranslated()) {
                        $status = "glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<td align=middle><span title="' . $statustext . '" class="' . $status . '"></span></td>';
                }
                $returnStr .= '<tr><td>';
                $returnStr .= '<a data-placement="right" data-html="true" href="' . setSessionParams(array('page' => 'translator.survey.section', 'seid' => $section->getSeid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= $span . '<td>' . $section->getName() . '</td><td>' . $section->getDescription() . '</td></tr>';
            }
            $returnStr .= '</table>';
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoSectionsYet());
        }
        return $returnStr;
    }

    function showSection($seid, $message = '') {
        $user = new User($_SESSION['URID']);
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.section', 'seid' => $seid), $section->getName()) . '</li>';
        //if (!$user->hasNavigationInBreadCrumbs()) {
        if ($_SESSION['VRFILTERMODE_SECTION'] == 0) {
            $returnStr .= '<li class="active">' . Language::labelVariables() . '</li>';
        } elseif ($_SESSION['VRFILTERMODE_SECTION'] == 3) {
            $returnStr .= '<li class="active">' . Language::labelGroups() . '</li>';
        } else {
            $returnStr .= '<li class="active">' . Language::labelVariables() . '</li>';
        }
        //}
        $returnStr .= '</ol>';
        $returnStr .= $message;
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div style="" class="col-xs-12 col-sm-9">';

        if ($user->hasNavigationInBreadCrumbs()) {
            $active = array_fill(0, 16, 'label-primary');
            $active[$_SESSION['VRFILTERMODE_SECTION']] = 'label-default';

            $show = false;
            if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                $show = true;
            }

            $span = "";
            if ($show) {
                $status = "glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($section->isTranslatedVariables()) {
                    $status = "glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
            }

            if ($_SESSION['VRFILTERMODE_SECTION'] == 0) {
                $returnStr .= ' <span class="label ' . $active[0] . '">' . $span . Language::labelVariables() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_section\').val(0);$(\'#sectionsidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . $span . Language::labelVariables() . '</span></a>';
            }
        }

        $returnStr .= '<div class="well" style="background-color:white;">';


        if ($_SESSION['VRFILTERMODE_SECTION'] == 0) { //show variables
            $returnStr .= $this->showVariables($survey->getVariableDescriptives($seid, "position", "asc"));
        }
        //elseif ($_SESSION['VRFILTERMODE_SECTION'] == 3) { //show groups!
        //  $returnStr .= $this->showGroups($survey->getGroups());
        //} 
        else {
            $returnStr .= $this->showVariables($survey->getVariableDescriptives($seid, "position", "asc"));
        }
        $returnStr .= '</div>'; //end well
//END CONTENT

        $returnStr .= '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= $this->showSectionSideBar($survey, $_SESSION['VRFILTERMODE_SECTION']);
        $returnStr .= '</div>';


        $returnStr .= '</div></div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showSectionSideBar($survey, $filter = 0) {
        $returnStr = '<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">';

        $section = $survey->getSection($_SESSION['SEID']);
        $previous = $survey->getPreviousSection($section);
        $next = $survey->getNextSection($section);
        $previoustext = "";
        $nexttext = "";
        if ($previous->getSeid() != "" && $previous->getSeid() != $section->getSeid()) {
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'translator.survey.section', 'seid' => $previous->getSeid()), '<span class="glyphicon glyphicon-chevron-left"></span>', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getSeid() != "" && $next->getSeid() != $section->getSeid()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'translator.survey.section', 'seid' => $next->getSeid()), '<span class="glyphicon glyphicon-chevron-right"></span>', 'title="' . $next->getName() . '"') . '</span>';
        }

        $returnStr .= '<center>' . $previoustext . '<span class="label label-default">' . $section->getName() . '</span>' . $nexttext . '</center>';
        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        $returnStr .= $this->displayVariableRoutingFilter($survey, $filter); // . '<hr>' . $survey->getName() . ' sections:';
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div><!--/.well -->
        </div><!--/col-xs-6-->';
        return $returnStr;
    }

    function displayVariableRoutingFilter($survey, $filter = 0) {
        $active = array('', '', '', '', '');
        $active[$filter] = ' active';
        $section = $survey->getSection($_SESSION['SEID']);
        $returnStr .= '<form method="post" id="sectionsidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.section'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_section" id="vrfiltermode_section" value="' . $filter . '">';

        $returnStr .= '<div class="btn-group">';
        //$returnStr .= '<div class="btn-group">';
        //$returnStr .= '<button class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . Language::labelTranslate() . '</button>';
        //$returnStr .= '<ul class="dropdown-menu" role="menu">';


        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }

        $span = "";
        if ($show) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($section->isTranslatedVariables()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        //$returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_section\').val(0);$(\'#sectionsidebar\').submit();">' . $span . Language::labelVariables() . '</a></li>';
        $returnStr .= '<button class="btn btn-default ' . $active[0] . '" onclick="$(\'#vrfiltermode_section\').val(0);$(\'#sectionsidebar\').submit();">' . $span . Language::labelVariables() . '</button>';

        //$returnStr .= '</ul>';
        //$returnStr .= '</div>';
        $returnStr .= '</div>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* SETTINGS FUNCTIONS */

    function showTranslateSettingsAssistance($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerTranslateSettingsAssistance());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatesettingsassistanceres'));

        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true' aria-multiselectable='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getEmptyMessage(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageDouble(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageInteger(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessagePattern(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageMinimumLength(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageMaximumLength(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageMinimumWords(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageMaximumWords(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageSelectMinimum(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageSelectMaximum(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageSelectExact(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageSelectInvalidSubset(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageSelectInvalidSet(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageRange(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . convertHTLMEntities($survey->getErrorMessageMaximumCalendar(), ENT_QUOTES) . '</textarea></td></tr>';

            /* inline */
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea></td></tr>';

            /* group */
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageExclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageMinimumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageMaximumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageExactRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageUniqueRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= "<table width='100%'>";
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_EMPTY_MESSAGE . '">' . convertHTLMEntities($survey->getEmptyMessage(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '">' . convertHTLMEntities($survey->getErrorMessageDouble(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INTEGER . '">' . convertHTLMEntities($survey->getErrorMessageInteger(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_PATTERN . '">' . convertHTLMEntities($survey->getErrorMessagePattern(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '">' . convertHTLMEntities($survey->getErrorMessageMinimumLength(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '">' . convertHTLMEntities($survey->getErrorMessageMaximumLength(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '">' . convertHTLMEntities($survey->getErrorMessageMinimumWords(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '">' . convertHTLMEntities($survey->getErrorMessageMaximumWords(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectMinimum(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectMaximum(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectExact(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectInvalidSubset(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectInvalidSet(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_RANGE . '">' . convertHTLMEntities($survey->getErrorMessageRange(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '">' . convertHTLMEntities($survey->getErrorMessageMaximumCalendar(), ENT_QUOTES) . '</textarea></td></tr>';

        /* inline */
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea></td></tr>';

        /* group */
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageExclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageMinimumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageMaximumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageExactRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageUniqueRequired(), ENT_QUOTES)) . '</textarea></td></tr>';

        $returnStr .= '</table></div></div></div></div></div>';

        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= '</form>';
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showTranslateSettingsLayout($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerTranslateSettingsLayout());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatesettingslayoutres'));

        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;

        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $survey->getLabelBackButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $survey->getLabelNextButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $survey->getLabelDKButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $survey->getLabelRFButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $survey->getLabelNAButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $survey->getLabelUpdateButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $survey->getLabelRemarkButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $survey->getLabelRemarkSaveButton(), 'readonly') . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $survey->getLabelCloseButton(), 'readonly') . "</td></tr>";
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= "<table width='100%'>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $survey->getLabelBackButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $survey->getLabelNextButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $survey->getLabelDKButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $survey->getLabelRFButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $survey->getLabelNAButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $survey->getLabelUpdateButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $survey->getLabelRemarkButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $survey->getLabelRemarkSaveButton()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $survey->getLabelCloseButton()) . "</td></tr>";
        $returnStr .= '</table></div></div></div></div></div>';

        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= "</form>";

        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showSettingsList() {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = '<div class="list-group">';

        $span = "";
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedAssistance()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'translator.survey.translatesettingsassistance')) . '" class="list-group-item">' . $span . Language::labelSettingsAssistance() . '</a>';

        $span = "";
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $status = "glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($survey->isTranslatedLayout()) {
                $status = "glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'translator.survey.translatesettingslayout')) . '" class="list-group-item">' . $span . Language::labelSettingsLayout() . '</a>';
        $returnStr .= '</div>';
        return $returnStr;
    }

    function showSettings() {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey);

        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $returnStr .= $this->showSettingsList();

        $returnStr .= '</div>';

        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showSettingsHeader($survey, $actiontype, $message = "") {
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey'), $survey->getName()) . '</li>';

        if ($_SESSION['VRFILTERMODE_SETTING'] == 1) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.settings'), Language::headerTexts()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerTranslateSettingsAssistance() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 4) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.settings'), Language::headerTexts()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerTranslateSettingsLayout() . '</li>';
        }

        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function showSettingsFooter($survey) {
        $returnStr = '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '</div>';
        $returnStr .= '</div></div>'; //container and wrap        
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    /* TYPE FUNCTIONS */

    function showTypes($types) {
        $returnStr = '';
        if (sizeof($types) > 0) {
            $returnStr .= '<table class="table table-striped table-bordered pre-scrollable table-condensed table-hover">';

            $survey = new Survey($_SESSION['SUID']);
            if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                $returnStr .= '<tr><th width=15px></th><th style="cursor: default;" width=15px>' . Language::labelTypeEditGeneralStatus() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralCategories() . '</th></tr>';
            } else {
                $returnStr .= '<tr><th width=15px></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralCategories() . '</th></tr>';
            }
            foreach ($types as $type) {

                // exclude types not in use
                if ($type->isUsed() == false) {
                    continue;
                }
                
                $span = "";
                if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                    $status = "glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($type->isTranslated()) {
                        $status = "glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<td align=middle><span title="' . $statustext . '" class="' . $status . '"></span></td>';
                }
                $returnStr .= '<tr><td>';
                $returnStr .= '<a data-placement="right" data-html="true" href="' . setSessionParams(array('page' => 'translator.survey.translatetype', 'tyd' => $type->getTyd())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= $span . '<td>' . $type->getName() . '</td><td>' . $type->getOptionsText() . '</td></tr>';
            }
            $returnStr .= '</table>';
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoTypesYet());
        }
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        return $returnStr;
    }

    /* TYPES */

    function showTypeHeader($survey, $type, $actiontype, $message = "") {
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey', 'vrfiltermode_survey' => '2'), Language::headerTypes()) . '</li>';
        if ($type->getTyd() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.translatetype', 'tyd' => $type->getTyd()), $type->getName()) . '</li>';
        }

        if ($_SESSION['VRFILTERMODE_TYPE'] == 0) {
            $returnStr .= '<li class="active">' . Language::headerTranslateTypeGeneral() . '</li>';
        } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 2) {
            $returnStr .= '<li class="active">' . Language::headerTranslateTypeLayout() . '</li>';
        } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 3) {
            $returnStr .= '<li class="active">' . Language::headerTranslateTypeAssistance() . '</li>';
        }


        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function showTypeFooter($survey) {
        $returnStr = '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        if (isset($_SESSION['TYD'])) {
            $returnStr .= $this->showTypeSideBar($survey, $_SESSION['VRFILTERMODE_TYPE']);
        }
        $returnStr .= '</div>';
        $returnStr .= '</div></div>'; //container and wrap        
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showTypeSideBar($survey, $filter = 0) {
        $returnStr = '<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">';
        $type = $survey->getType($_SESSION['TYD']);
        $previous = $survey->getPreviousType($type);
        $next = $survey->getNextType($type);
        $previoustext = "";
        $nexttext = "";
        if ($previous->getTyd() != "" && $previous->getTyd() != $type->getTyd()) {
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'translator.survey.translatetype', 'tyd' => $previous->getTyd()), '<span class="glyphicon glyphicon-chevron-left"></span>', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getTyd() != "" && $next->getTyd() != $type->getTyd()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'translator.survey.translatetype', 'tyd' => $next->getTyd()), '<span class="glyphicon glyphicon-chevron-right"></span>', 'title="' . $next->getName() . '"') . '</span>';
        }

        $returnStr .= '<center>' . $previoustext . '<span class="label label-default">' . $type->getName() . '</span>' . $nexttext . '</center>';



        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        $returnStr .= $this->displayTypeSideBarFilter($survey, $filter);
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div></div>';
        return $returnStr;
    }

    function displayTypeSideBarFilter($survey, $filter = 0) {

        $returnStr = '';
        $active = array('', '', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<form method="post" id="typesidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatetype'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_type" id="vrfiltermode_type" value="' . $filter . '">';

        $returnStr .= '<div class="btn-group">';
        //$returnStr .= '<button class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . Language::labelTranslate() . '</button>';
        //$returnStr .= '<ul class="dropdown-menu" role="menu">';

        $span = "";
        $type = $survey->getType($_SESSION['TYD']);
        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        if ($show) {
            $status = " glyphicon glyphicon-remove";
            if ($type->isTranslatedGeneral()) {
                $status = " glyphicon glyphicon-ok";
            }
            $span = '<span style="padding-right: 10px;" class="' . $status . '"></span>';
        }

        //$returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_type\').val(0);$(\'#typesidebar\').submit();">' . $span . Language::labelGeneral() . '</a></li>';
        $returnStr .= '<button class="btn btn-default ' . $active[0] . '" onclick="$(\'#vrfiltermode_type\').val(0);$(\'#typesidebar\').submit();">' . $span . Language::labelGeneral() . '</button>';

        $answertype = $type->getAnswerType();
        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {

            if ($show) {
                $status = " glyphicon glyphicon-remove";
                if ($type->isTranslatedLayout()) {
                    $status = " glyphicon glyphicon-ok";
                }
                $span = '<span style="padding-right: 10px;" class="' . $status . '"></span>';
            }

            //$returnStr .= '<li><a ' . $active[2] . '" onclick="$(\'#vrfiltermode_type\').val(2);$(\'#typesidebar\').submit();">' . $span . Language::labelLayout() . '</a></li>';
            $returnStr .= '<button class="btn btn-default ' . $active[2] . '" onclick="$(\'#vrfiltermode_type\').val(2);$(\'#typesidebar\').submit();">' . $span . Language::labelLayout() . '</button>';
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {

                if ($show) {
                    $status = " glyphicon glyphicon-remove";
                    if ($type->isTranslatedAssistance()) {
                        $status = " glyphicon glyphicon-ok";
                    }
                    $span = '<span style="padding-right: 10px;" class="' . $status . '"></span>';
                }
                //$returnStr .= '<li><a ' . $active[3] . '" onclick="$(\'#vrfiltermode_type\').val(3);$(\'#typesidebar\').submit();">' . $span . Language::labelAssistance() . '</a></li>';
                $returnStr .= '<button class="btn btn-default ' . $active[3] . '" onclick="$(\'#vrfiltermode_type\').val(3);$(\'#typesidebar\').submit();">' . $span . Language::labelAssistance() . '</button>';
            }
        }
        //$returnStr .= '</ul>';
        $returnStr .= '</div>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function getTypeTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';

        $show = false;
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($_SESSION['TYD']);
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        if ($show) {
            $status = " glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($type->isTranslatedGeneral()) {
                $status = " glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        if ($filter == 0) {
            $returnStr .= ' <span class="label label-default">' . $span . Language::labelTypeEditGeneral() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(0);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . $span . Language::labelGeneral() . '</span></a>';
        }
        $answertype = $type->getAnswerType();
        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {

            $span = "";
            if ($show) {
                $status = " glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($type->isTranslatedLayout()) {
                    $status = " glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
            }

            if ($filter == 2) {
                $returnStr .= ' <span class="label ' . $active[2] . '">' . $span . Language::labelLayout() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(2);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . $span . Language::labelLayout() . '</span></a>';
            }
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {

                $span = "";
                if ($show) {
                    $status = " glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($type->isTranslatedAssistance()) {
                        $status = " glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
                }
                if ($filter == 3) {
                    $returnStr .= ' <span class="label ' . $active[3] . '">' . $span . Language::labelAssistance() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(3);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . $span . Language::labelAssistance() . '</span></a>';
                }
            }
        }
        return $returnStr;
    }

    function showTranslateType($tyd, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);
        $returnStr = $this->showTypeHeader($survey, $type, Language::headerTranslateType(), $message);

        if ($_SESSION['VRFILTERMODE_TYPE'] == 0) {
            $returnStr .= $this->showTranslateTypeGeneral($type);
        } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 2) {
            $returnStr .= $this->showTranslateTypeLayout($type);
        } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 3) {
            $returnStr .= $this->showTranslateTypeAssistance($type);
        }

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showTypeFooter($survey);
        return $returnStr;
    }

    function showTranslateTypeGeneral($type) {

        $returnStr = '<form id="editform" method="post">';
        if ($type->getTyd() != "") {
            $returnStr .= $this->getTypeTopTab(0);
        }

        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralName() . '</td><td><input disabled type="text" class="form-control" value="' . convertHTLMEntities($type->getName(), ENT_QUOTES) . '"></td></tr>';

            /* categories needed */
            $answertype = $type->getAnswerType();
            $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN);
            if (inArray($answertype, $array)) {
                $returnStr .= '<tr id="categories"><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td><textarea readonly style="min-width: 600px; height: 120px;" class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';
            }
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatetypegeneralres', 'tyd' => $type->getTyd()));
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralName() . '</td><td><input disabled type="text" class="form-control" value="' . convertHTLMEntities($type->getName(), ENT_QUOTES) . '"></td></tr>';

        /* categories needed */
        $answertype = $type->getAnswerType();
        $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN);
        if (inArray($answertype, $array)) {
            $returnStr .= '<tr id="categories"><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td><textarea style="min-width: 600px; height: 120px;" class="form-control" name="' . SETTING_OPTIONS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';
        }
        $returnStr .= '</table></div></div></div></div></div>';
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showTranslateTypeLayout($type) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatetypelayoutres', 'tyd' => $type->getTyd()));

        $answertype = $type->getAnswerType();
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        $returnStr .= $this->getTypeTopTab(2);

        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $type->getLabelBackButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $type->getLabelNextButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $type->getLabelDKButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $type->getLabelRFButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $type->getLabelNAButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $type->getLabelUpdateButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $type->getLabelRemarkButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $type->getLabelRemarkSaveButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $type->getLabelCloseButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= "<table width='100%'>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $type->getLabelBackButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $type->getLabelNextButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $type->getLabelDKButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $type->getLabelRFButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $type->getLabelNAButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $type->getLabelUpdateButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $type->getLabelRemarkButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $type->getLabelRemarkSaveButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $type->getLabelCloseButton()) . $helpend . "</td></tr>";
        $returnStr .= '</table></div></div></div></div></div>';

        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showTranslateTypeAssistance($type) {

        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatetypeassistanceres', 'tyd' => $type->getTyd()));
        $returnStr .= $this->getTypeTopTab(3);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();

        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            if (inArray($type->getAnswerType(), array(ANSWER_TYPE_STRING, ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
                $returnStr .= '<tr><td style="width: 15%;">' . Language::labelTypeEditAssistancePreText() . '</td><td><input type="text" class="form-control" readonly value="' . convertHTLMEntities($type->getPreText(), ENT_QUOTES) . '"></td></tr>';
                $returnStr .= '<tr><td style="width: 15%;">' . Language::labelTypeEditAssistancePostText() . '</td><td><input type="text" class="form-control" readonly value="' . convertHTLMEntities($type->getPostText(), ENT_QUOTES) . '"></td></tr>';
            }
            $returnStr .= '<tr><td style="width: 15%;">' . Language::labelTypeEditAssistanceHoverText() . '</td><td><input type="text" class="form-control" readonly value="' . convertHTLMEntities($type->getHoverText(), ENT_QUOTES) . '"></td></tr>';

            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getEmptyMessage(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';

            $at = $type->getAnswerType();
            switch ($at) {
                case ANSWER_TYPE_DOUBLE:
                    $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageDouble(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_INTEGER:
                    $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInteger(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_STRING:
                /* fall through */
                case ANSWER_TYPE_OPEN:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessagePattern(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMinimumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMinimumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_ENUMERATED:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                /* fall through */
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectInvalidSubset(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectInvalidSet(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */    
                case ANSWER_TYPE_SLIDER:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageRange(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumCalendar(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
            }
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";
        $returnStr .= '<table width=100%>';

        if (inArray($type->getAnswerType(), array(ANSWER_TYPE_STRING, ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
            $returnStr .= '<tr><td style="width: 15%;">' . Language::labelTypeEditAssistancePreText() . '</td><td><input type="text" class="form-control" name="' . SETTING_PRETEXT . '" value="' . convertHTLMEntities($type->getPreText(), ENT_QUOTES) . '"></td></tr>';
            $returnStr .= '<tr><td style="width: 15%;">' . Language::labelTypeEditAssistancePostText() . '</td><td><input type="text" class="form-control" name="' . SETTING_POSTTEXT . '" value="' . convertHTLMEntities($type->getPostText(), ENT_QUOTES) . '"></td></tr>';
        }
        $returnStr .= '<tr><td style="width: 15%;">' . Language::labelTypeEditAssistanceHoverText() . '</td><td><input type="text" class="form-control" name="' . SETTING_HOVERTEXT . '" value="' . convertHTLMEntities($type->getHoverText(), ENT_QUOTES) . '"></td></tr>';

        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_EMPTY_MESSAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getEmptyMessage(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';

        $at = $type->getAnswerType();
        switch ($at) {
            case ANSWER_TYPE_DOUBLE:
                $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageDouble(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_INTEGER:
                $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INTEGER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInteger(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_PATTERN . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessagePattern(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMinimumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMinimumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_ENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectInvalidSubset(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectInvalidSet(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_RANGE:
            /* fall through */
            case ANSWER_TYPE_KNOB:
                /* fall through */    
            case ANSWER_TYPE_SLIDER:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_RANGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageRange(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_CALENDAR:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumCalendar(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
        }
        $returnStr .= '</table></div></div></div></div></div>';
        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* VARIABLE FUNCTIONS */

    function showVariableHeader($survey, $section, $variable, $type, $message) {
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.section', 'seid' => $section->getSeid()), $section->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.translatevariable', 'vsid' => $variable->getVsid()), $variable->getName()) . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function showVariableFooter($survey) {
        $returnStr = '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= $this->showSectionSideBar($survey, $_SESSION['VRFILTERMODE_SECTION']);
        $returnStr .= $this->showVariableSideBar($survey, $_SESSION['VRFILTERMODE_VARIABLE']);
        $returnStr .= '</div>';
        $returnStr .= '</div></div>'; //container and wrap        
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showVariableSideBar($survey, $filter = 0) {
        $returnStr = '<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">';
        $var = $survey->getVariableDescriptive($_SESSION['VSID']);
        $previous = $survey->getPreviousVariableDescriptive($var);
        $next = $survey->getNextVariableDescriptive($var);
        $previoustext = "";
        $nexttext = "";
        if ($previous->getVsid() != "" && $previous->getVsid() != $var->getVsid()) {
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'translator.survey.translatevariable', 'vsid' => $previous->getVsid()), '<span class="glyphicon glyphicon-chevron-left"></span> ', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getVsid() != "" && $next->getVsid() != $var->getVsid()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'translator.survey.translatevariable', 'vsid' => $next->getVsid()), '<span class="glyphicon glyphicon-chevron-right"></span> ', 'title="' . $next->getName() . '"') . '</span>';
        }

        $returnStr .= '<center>' . $previoustext . '<span class="label label-default">' . $var->getName() . '</span>' . $nexttext . '</center>';
        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        $returnStr .= $this->displayVariableSideBarFilter($survey, $filter);
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div></div>';
        return $returnStr;
    }

    function displayVariableSideBarFilter($survey, $filter = 0) {
        $active = array('', '', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<form method="post" id="variablesidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatevariable'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_variable" id="vrfiltermode_variable" value="' . $filter . '">';
        //$returnStr .= '<div class="btn-group">';
        $returnStr .= '<div class="btn-group">';
        $variable = $survey->getVariableDescriptive($_SESSION['VSID']);
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
        }

        $returnStr .= '<div class="btn-group">';
        //$returnStr .= '<button class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . Language::labelTranslate() . '</button>';
        //$returnStr .= '<ul class="dropdown-menu" role="menu">';

        $span = "";
        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        if ($show) {
            $status = " glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($variable->isTranslatedGeneral()) {
                $status = " glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 10px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        //$returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_variable\').val(0);$(\'#variablesidebar\').submit();">' . $span . Language::labelGeneral() . '</a></li>';
        $returnStr .= '<button class="btn btn-default ' . $active[0] . '" onclick="$(\'#vrfiltermode_variable\').val(0);$(\'#variablesidebar\').submit();">' . $span . Language::labelGeneral() . '</button>';

        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {

            $span = "";
            if ($show) {
                $status = " glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($variable->isTranslatedLayout()) {
                    $status = " glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 10px;" title="' . $statustext . '" class="' . $status . '"></span>';
            }

            //$returnStr .= '<li><a ' . $active[2] . ' onclick="$(\'#vrfiltermode_variable\').val(2);$(\'#variablesidebar\').submit();">' . $span . Language::labelLayout() . '</a></li>';
            $returnStr .= '<button class="btn btn-default ' . $active[2] . '" onclick="$(\'#vrfiltermode_variable\').val(2);$(\'#variablesidebar\').submit();">' . $span . Language::labelLayout() . '</button>';
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {

                $span = "";
                if ($show) {
                    $status = " glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($variable->isTranslatedAssistance()) {
                        $status = " glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<span style="padding-right: 10px;" title="' . $statustext . '" class="' . $status . '"></span>';
                }
                //$returnStr .= '<li><a ' . $active[3] . ' onclick="$(\'#vrfiltermode_variable\').val(3);$(\'#variablesidebar\').submit();">' . $span . Language::labelAssistance() . '</a></li>';
                $returnStr .= '<button class="btn btn-default ' . $active[3] . '" onclick="$(\'#vrfiltermode_variable\').val(3);$(\'#variablesidebar\').submit();">' . $span . Language::labelAssistance() . '</button>';

                $span = "";
                if ($show) {
                    $status = " glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($variable->isTranslatedFill()) {
                        $status = " glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<span style="padding-right: 10px;" title="' . $statustext . '" class="' . $status . '"></span>';
                }
                //$returnStr .= '<li><a ' . $active[4] . ' onclick="$(\'#vrfiltermode_variable\').val(4);$(\'#variablesidebar\').submit();">' . $span . Language::labelFill() . '</a></li>';
                $returnStr .= '<button class="btn btn-default ' . $active[4] . '" onclick="$(\'#vrfiltermode_variable\').val(4);$(\'#variablesidebar\').submit();">' . $span . Language::labelFill() . '</button>';
            }
        }
        //$returnStr .= '</ul>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function getVariableTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';

        $survey = new Survey($_SESSION['SUID']);
        $variable = $survey->getVariableDescriptive($_SESSION['VSID']);

        $span = "";
        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        if ($show) {
            $status = " glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($variable->isTranslatedGeneral()) {
                $status = " glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        if ($filter == 0) {
            $returnStr .= ' <span class="label label-default">' . $span . Language::labelTypeEditGeneral() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(0);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . $span . Language::labelGeneral() . '</span></a>';
        }

        $answertype = $variable->getAnswerType();
        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {

            $span = "";
            if ($show) {
                $status = " glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($variable->isTranslatedLayout()) {
                    $status = " glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
            }

            if ($filter == 2) {
                $returnStr .= ' <span class="label ' . $active[2] . '">' . $span . Language::labelLayout() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(2);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . $span . Language::labelLayout() . '</span></a>';
            }
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {

                $span = "";
                if ($show) {
                    $status = " glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($variable->isTranslatedAssistance()) {
                        $status = " glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
                }
                if ($filter == 3) {
                    $returnStr .= ' <span class="label ' . $active[3] . '">' . $span . Language::labelAssistance() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(3);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . $span . Language::labelAssistance() . '</span></a>';
                }

                $span = "";
                if ($show) {
                    $status = " glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($variable->isTranslatedFill()) {
                        $status = " glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
                }
                if ($filter == 4) {
                    $returnStr .= ' <span class="label ' . $active[4] . '">' . $span . Language::labelFill() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(4);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[4] . '">' . $span . Language::labelFill() . '</span></a>';
                }
            }
        }

        return $returnStr;
    }

    function showVariables($variables) {

        if (sizeof($variables) > 0) {
            $returnStr .= '<table class="table table-striped table-bordered pre-scrollable table-condensed table-hover">';

            $survey = new Survey($_SESSION['SUID']);
            if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                $returnStr .= '<tr><th></th><th style="cursor: default;" width=15px>' . Language::labelTypeEditGeneralStatus() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralQuestion() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th></tr>';
            } else {
                $returnStr .= '<tr><th></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralQuestion() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th></tr>';
            }
            foreach ($variables as $variable) {

                $span = "";
                if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                    $status = "glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($variable->isTranslated()) {
                        $status = "glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<td align=middle><span title="' . $statustext . '" class="' . $status . '"></span></td>';
                }

                $returnStr .= '<tr><td>';
                $returnStr .= '<a data-placement="right" data-html="true" href="' . setSessionParams(array('page' => 'translator.survey.translatevariable', 'vsid' => $variable->getVsid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= $span . '<td>' . $variable->getName() . '</td><td>' . convertHTLMEntities($variable->getQuestion(), ENT_QUOTES) . '</td><td>' . $variable->getDescription() . '</td></tr>';
            }
            $returnStr .= '</table>';
        } else {
            $returnStr = $this->displayWarning(Language::messageNoVariablesYet());
        }
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        return $returnStr;
    }

    function showTranslateVariable($vsid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $var = $survey->getVariableDescriptive($vsid);
        $returnStr = $this->showVariableHeader($survey, $section, $var, Language::headerTranslateVariable(), $message);

        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0) {
            $returnStr .= $this->showTranslateVariableGeneral($var);
        } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 2) {
            $returnStr .= $this->showTranslateVariableLayout($var);
        } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 3) {
            $returnStr .= $this->showTranslateVariableAssistance($var);
        } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 4) {
            $returnStr .= $this->showTranslateVariableFill($var);
        }

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showVariableFooter($survey);
        return $returnStr;
    }

    function showTranslateVariableGeneral($var) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatevariablegeneralres', 'vsid' => $var->getVsid()));
        if ($var->getVsid() != "") {
            $returnStr .= $this->getVariableTopTab(0);
        }
        $returnStr .= '<div class="well" style="background-color:white;">';

        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= '<tr><td width=15%>' . Language::labelTypeEditGeneralVariableName() . '</td><td colspan=2><input disabled type="text" class="form-control" readonly value="' . convertHTLMEntities($var->getName(), ENT_QUOTES) . '"></td></tr>';
            $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralQuestion() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control" readonly>' . convertHTLMEntities($var->getQuestion(), ENT_QUOTES) . '</textarea></td></tr>';

            /* categories needed */
            $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN);
            $answertype = $var->getAnswerType();
            if ($answertype == SETTING_FOLLOW_TYPE) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($var->getTyd());
                $answertype = $type->getAnswerType();
            }

            if (inArray($answertype, $array)) {
                $returnStr .= '<tr id="categories"><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control uscic-form-control-admin" readonly>' . $this->displayTextSettingValue(convertHTLMEntities($var->getOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';
            }
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td width=15%>' . Language::labelTypeEditGeneralVariableName() . '</td><td colspan=2><input disabled type="text" class="form-control" name="' . SETTING_NAME . '" value="' . convertHTLMEntities($var->getName(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralQuestion() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control" name="' . SETTING_QUESTION . '">' . convertHTLMEntities($var->getQuestion(), ENT_QUOTES) . '</textarea></td></tr>';

        /* categories needed */
        $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN);
        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }

        if (inArray($answertype, $array)) {
            $returnStr .= '<tr id="categories"><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control uscic-form-control-admin" name="' . SETTING_OPTIONS . '">' . $this->displayTextSettingValue(convertHTLMEntities($var->getOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';
        }

        $returnStr .= '</table></div></div></div></div></div>';

        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showTranslateVariableFill($variable) {

        $returnStr .= '<link rel="stylesheet" href="js/codemirror/lib/codemirror.css">';
        $returnStr .= '<link rel="stylesheet" href="js/codemirror/addon/dialog/dialog.css">';
        $returnStr .= '<script src="js/codemirror/lib/codemirror.js"></script>';
        $returnStr .= '<script src="js/codemirror/mode/xml/xml.js"></script>';
        $returnStr .= '<script src="js/codemirror/addon/dialog/dialog.js"></script>';
        $returnStr .= '<script src="js/codemirror/addon/search/searchcursor.js"></script>';
        $returnStr .= '<script src="js/codemirror/addon/search/search.js"></script>';
        $returnStr .= '<script src="js/codemirror/mode/nubis/nubis.js"></script>';
        $returnStr .= '<style type="text/css">';
        $returnStr .= '     .CodeMirror {border-top: 1px solid black; border-bottom: 1px solid black;}
                            dt {font-family: monospace; color: #666;}
                       </style>';
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatevariablefillres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->getVariableTopTab(4);
        $survey = new Survey($_SESSION['SUID']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;

        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditFillText() . '</td><td><textarea id="filltextreadonly" readonly style="width: 100%;" rows=8 class="form-control">' . convertHTLMEntities($variable->getFillText(), ENT_QUOTES) . '</textarea></td></tr>';
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= "<table width='100%'>";
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditFillText() . '</td><td><textarea id="filltext" style="width: 100%;" rows=8 class="form-control" name="' . SETTING_FILLTEXT . '">' . convertHTLMEntities($variable->getFillText(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '</table></div></div></div></div></div>';
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= '</form>';
        $returnStr .= '<script type="text/javascript">
            var editor = CodeMirror.fromTextArea(document.getElementById("filltext"), {mode: "text/x-plain", lineNumbers: true});
            var editor2 = CodeMirror.fromTextArea(document.getElementById("filltextreadonly"), {mode: "text/x-plain", lineNumbers: true, readOnly: true});
            </script>';
        return $returnStr;
    }

    function showTranslateVariableLayout($variable) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatevariablelayoutres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->displayComboBox();
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
        } else {
            if ($variable->getTyd() > 0) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= $this->getVariableTopTab(2);

        $survey = new Survey($_SESSION['SUID']);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $variable->getLabelBackButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $variable->getLabelNextButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $variable->getLabelDKButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $variable->getLabelRFButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $variable->getLabelNAButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $variable->getLabelUpdateButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $variable->getLabelRemarkButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $variable->getLabelRemarkSaveButton(), 'readonly') . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $variable->getLabelCloseButton(), 'readonly') . $helpend . "</td></tr>";

            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= "<table width='100%'>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $variable->getLabelBackButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $variable->getLabelNextButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $variable->getLabelDKButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $variable->getLabelRFButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $variable->getLabelNAButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $variable->getLabelUpdateButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $variable->getLabelRemarkButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $variable->getLabelRemarkSaveButton()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $variable->getLabelCloseButton()) . $helpend . "</td></tr>";
        $returnStr .= '</table></div></div></div></div></div>';

        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showTranslateVariableAssistance($variable) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translatevariableassistanceres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->getVariableTopTab(3);

        $helpstart = '<div class="input-group">';
        $helpstart2 = "";
        $helpend2 = "";
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
        } else {
            if ($variable->getTyd() > 0) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
                $helpstart2 = '<div class="input-group">';
                $message2 = Language::helpFollowTypeOnly($type->getName());
                $helpend2 = '<span class="input-group-addon"><i>' . $message2 . '</i></span></div>';
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well" style="background-color:white;">';

        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            if (inArray($answertype, array(ANSWER_TYPE_STRING, ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
                $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePreText() . '</td><td>' . $helpstart2 . '<input type="text" class="form-control" readonly value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPreText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
                $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePostText() . '</td><td>' . $helpstart2 . '<input type="text" class="form-control" readonly value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPostText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
            }
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistanceHoverText() . '</td><td>' . $helpstart2 . '<input type="text" class="form-control" readonly value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getHoverText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 readonly class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEmptyMessage()), ENT_QUOTES) . '</textarea>' . $helpend . '</td></tr>';

            switch ($answertype) {
                case ANSWER_TYPE_DOUBLE:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageDouble(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_INTEGER:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInteger(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_STRING:
                /* fall through */
                case ANSWER_TYPE_OPEN:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessagePattern(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMinimumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMinimumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_ENUMERATED:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                /* fall through */
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectInvalidSubset(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectInvalidSet(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */    
                case ANSWER_TYPE_SLIDER:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageRange(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" readonly rows=2 class="form-control">' . convertHTLMEntities($variable->getErrorMessageMaximumCalendar(), ENT_QUOTES) . '</textarea></td></tr>';
                    break;
            }
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";

        $returnStr .= '<table width=100%>';

        if (inArray($answertype, array(ANSWER_TYPE_STRING, ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePreText() . '</td><td>' . $helpstart2 . '<input type="text" class="form-control" name="' . SETTING_PRETEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPreText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePostText() . '</td><td>' . $helpstart2 . '<input type="text" class="form-control" name="' . SETTING_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPostText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
        }
        $returnStr .= '<tr><td>' . Language::labelTypeEditAssistanceHoverText() . '</td><td>' . $helpstart2 . '<input type="text" class="form-control" name="' . SETTING_HOVERTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getHoverText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_EMPTY_MESSAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEmptyMessage()), ENT_QUOTES) . '</textarea>' . $helpend . '</td></tr>';

        switch ($answertype) {
            case ANSWER_TYPE_DOUBLE:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageDouble(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_INTEGER:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INTEGER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInteger(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_PATTERN . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessagePattern(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMinimumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMinimumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_ENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectInvalidSubset(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectInvalidSet(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_RANGE:
            /* fall through */
            case ANSWER_TYPE_KNOB:
                /* fall through */    
            case ANSWER_TYPE_SLIDER:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_RANGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageRange(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_CALENDAR:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '">' . convertHTLMEntities($variable->getErrorMessageMaximumCalendar(), ENT_QUOTES) . '</textarea></td></tr>';
                break;
        }
        $returnStr .= '</table></div></div></div></div></div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* GROUPS */

    function showGroupHeader($survey, $group, $type, $message) {

        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $section = $survey->getSection($_SESSION['SEID']);
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.section', 'seid' => $seid), $section->getName()) . '</li>';
        if ($group->getName() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.survey.translategroup', 'gid' => $group->getGid()), $group->getName()) . '</li>';
        }

        if ($_SESSION['VRFILTERMODE_GROUP'] == 2) {
            $returnStr .= '<li class="active">' . Language::headerTranslateTypeLayout() . '</li>';
        } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 3) {
            $returnStr .= '<li class="active">' . Language::headerTranslateTypeAssistance() . '</li>';
        }

        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function getGroupTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';

        $show = false;
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($_SESSION['GID']);
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        if ($show) {
            $status = " glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($group->isTranslatedLayout()) {
                $status = " glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        $group = $survey->getGroup($_SESSION['GID']);

        $returnStr = '';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(2);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . $span . Language::labelLayout() . '</span></a>';

        if ($group->getType() != GROUP_SUB) {
            if ($show) {
                $status = " glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($group->isTranslatedAssistance()) {
                    $status = " glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 5px;" title="' . $statustext . '" class="' . $status . '"></span>';
            }
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(3);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . $span . Language::labelAssistance() . '</span></a>';
        }
        return $returnStr;
    }

    function showGroupFooter($survey) {
        if ($_SESSION['VRFILTERMODE_GROUP'] == 0) {
            $returnStr = '<div style="min-height: 300px; max-height: 100%;"></div>';
        } else if ($_SESSION['VRFILTERMODE_GROUP'] == 1) {
            $returnStr = '<div style="min-height: 50px; max-height: 100%;"></div>';
        } else if ($_SESSION['VRFILTERMODE_GROUP'] == 2) {
            $returnStr = '<div style="min-height: 200px; max-height: 100%;"></div>';
        } else if ($_SESSION['VRFILTERMODE_GROUP'] == 3) {
            $returnStr = '<div style="min-height: 200px; max-height: 100%;"></div>';
        }
        $returnStr .= '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= $this->showSectionSideBar($survey, $_SESSION['VRFILTERMODE_SECTION']);
        if (isset($_SESSION['GID'])) {
            $returnStr .= $this->showGroupSideBar($survey, $_SESSION['VRFILTERMODE_GROUP']);
        }
        $returnStr .= '</div>';
        $returnStr .= '</div></div>'; //container and wrap        
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showGroupSideBar($survey, $filter = 0) {
        $returnStr = '<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">';
        $group = $survey->getGroup($_SESSION['GID']);
        $previous = $survey->getPreviousGroup($group);
        $next = $survey->getNextGroup($group);
        $previoustext = "";
        $nexttext = "";
        if ($previous->getGid() != "" && $previous->getGid() != $group->getGid()) {
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'translator.survey.translategroup', 'gid' => $previous->getGid()), '<span class="glyphicon glyphicon-chevron-left"></span>', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getGid() != "" && $next->getGid() != $group->getGid()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'translator.survey.translategroup', 'gid' => $next->getGid()), '<span class="glyphicon glyphicon-chevron-right"></span>', 'title="' . $next->getName() . '"') . '</span>';
        }

        $returnStr .= '<center>' . $previoustext . '<span class="label label-default">' . $group->getName() . '</span>' . $nexttext . '</center>';


        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        $returnStr .= $this->displayGroupSideBarFilter($survey, $filter);
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div></div>';
        return $returnStr;
    }

    function displayGroupSideBarFilter($survey, $filter = 0) {
        $active = array('', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<form method="post" id="groupsidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translategroup'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_group" id="vrfiltermode_group" value="' . $filter . '">';
        $group = $survey->getGroup($_SESSION['GID']);
        $returnStr .= '<div class="btn-group">';
        //$returnStr .= '<button class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . Language::labelTranslate() . '</button>';
        //$returnStr .= '<ul class="dropdown-menu" role="menu">';

        $span = "";
        $show = false;
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $show = true;
        }
        if ($show) {
            $status = " glyphicon glyphicon-remove";
            $statustext = Language::messageTranslationStatusIncomplete();
            if ($group->isTranslatedLayout()) {
                $status = " glyphicon glyphicon-ok";
                $statustext = Language::messageTranslationStatusComplete();
            }
            $span = '<span style="padding-right: 10px;" title="' . $statustext . '" class="' . $status . '"></span>';
        }

        //$returnStr .= '<li><a ' . $active[2] . '" onclick="$(\'#vrfiltermode_group\').val(2);$(\'#groupsidebar\').submit();">' . $span . Language::labelLayout() . '</a></li>';
        $returnStr .= '<button class="btn btn-default ' . $active[2] . '" onclick="$(\'#vrfiltermode_group\').val(2);$(\'#groupsidebar\').submit();">' . $span . Language::labelLayout() . '</button>';
        if ($group->getType() != GROUP_SUB) {

            $span = "";
            $show = false;
            if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                $show = true;
            }
            if ($show) {
                $status = " glyphicon glyphicon-remove";
                $statustext = Language::messageTranslationStatusIncomplete();
                if ($group->isTranslatedAssistance()) {
                    $status = " glyphicon glyphicon-ok";
                    $statustext = Language::messageTranslationStatusComplete();
                }
                $span = '<span style="padding-right: 10px;" title="' . $statustext . '"  class="' . $status . '"></span>';
            }
            //$returnStr .= '<li><a ' . $active[3] . '" onclick="$(\'#vrfiltermode_group\').val(3);$(\'#groupsidebar\').submit();">' . $span . Language::labelAssistance() . '</a></li>';
            $returnStr .= '<button class="btn btn-default ' . $active[3] . '" onclick="$(\'#vrfiltermode_group\').val(3);$(\'#groupsidebar\').submit();">' . $span . Language::labelAssistance() . '</button>';
        }
        //$returnStr .= '</ul>';
        //$returnStr .= '</div>';
        $returnStr .= '</div>';
        $returnStr .= '</form>';

        return $returnStr;
    }

    function showGroups($groups) {

        if (sizeof($groups) > 0) {
            $returnStr .= '<table class="table table-striped table-bordered pre-scrollable table-condensed table-hover">';

            $survey = new Survey($_SESSION['SUID']);
            if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                $returnStr .= '<tr><th width=15px></th><th style="cursor: default;" width=15px>' . Language::labelTypeEditGeneralStatus() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th></tr>';
            } else {
                $returnStr .= '<tr><th width=15px></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th></tr>';
            }
            foreach ($groups as $group) {

                $span = "";
                if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
                    $status = "glyphicon glyphicon-remove";
                    $statustext = Language::messageTranslationStatusIncomplete();
                    if ($group->isTranslated()) {
                        $status = "glyphicon glyphicon-ok";
                        $statustext = Language::messageTranslationStatusComplete();
                    }
                    $span = '<td align=middle><span title="' . $statustext . '" class="' . $status . '"></span></td>';
                }
                $returnStr .= '<tr><td>';
                $returnStr .= '<a data-placement="right" data-html="true" href="' . setSessionParams(array('page' => 'translator.survey.translategroup', 'gid' => $group->getGid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= $span . '<td>' . $group->getName() . '</td></tr>';
            }
            $returnStr .= '</table>';
        } else {
            $returnStr = $this->displayWarning(Language::messageNoGroupsYet());
        }
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        return $returnStr;
    }

    function showTranslateGroup($gid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($gid);
        $returnStr = $this->showGroupHeader($survey, $group, Language::headerTranslateGroup(), $message);
        if ($_SESSION['VRFILTERMODE_GROUP'] == 2) {
            $returnStr .= $this->showTranslateGroupLayout($group);
        } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 3) {
            $returnStr .= $this->showTranslateGroupAssistance($group);
        } else {
            $returnStr .= $this->showTranslateGroupLayout($group);
        }

        //$returnStr .= '<div style="min-height: 50px;"></div>';
        $returnStr .= $this->showGroupFooter($survey);
        return $returnStr;
    }

    function showTranslateGroupLayout($group) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translategrouplayoutres', 'gid' => $group->getGid()));
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->displayColorPicker();
        $returnStr .= $this->getGroupTopTab(2);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            if ($group->getType() != GROUP_SUB) {
                switchSurveyLanguageTranslator($l);
                $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
                $returnStr .= "<div class='panel panel-default'>";
                $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
                $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
                $returnStr .= "</div>";
                $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
                $returnStr .= "<div class='panel-body'>";
                $returnStr .= '<table width=100%>';
                $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $group->getLabelBackButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $group->getLabelNextButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $group->getLabelDKButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $group->getLabelRFButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $group->getLabelNAButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $group->getLabelUpdateButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $group->getLabelRemarkButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $group->getLabelRemarkSaveButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $group->getLabelCloseButton(), 'readonly') . $helpend . "</td></tr>";
                $returnStr .= '</table></div></div></div>';
                switchSurveyLanguageTranslator($language);
            }
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";
        if ($group->getType() != GROUP_SUB) {
            $returnStr .= '<table width=100%>';
            $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $group->getLabelBackButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $group->getLabelNextButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $group->getLabelDKButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $group->getLabelRFButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $group->getLabelNAButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $group->getLabelUpdateButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $group->getLabelRemarkButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $group->getLabelRemarkSaveButton()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td><td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $group->getLabelCloseButton()) . $helpend . "</td></tr>";
            $returnStr .= '</table></div></div></div></div></div>';
        }

        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= "</form>";
        //$returnStr .= "</div>";
        return $returnStr;
    }

    function showTranslateGroupAssistance($group) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.survey.translategroupassistanceres', 'gid' => $group->getGid()));
        $returnStr .= $this->getGroupTopTab(3);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $survey = new Survey($_SESSION['SUID']);
        $language = getSurveyLanguage();
        $returnStr .= "<div id='accordion' class='panel-group' role='tablist' aria-expanded='true'>";
        $l = $survey->getDefaultLanguage(getSurveyMode());
        $arr = Language::getLanguagesArray();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        if ($l != $language) {
            switchSurveyLanguageTranslator($l);
            $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($l))]['name'];
            $returnStr .= "<div class='panel panel-default'>";
            $returnStr .= "<div class='panel panel-heading' role='tab' id='headingOne'>";
            $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseOne" href="#collapseOne">' . $langlabel . '</a>';
            $returnStr .= "</div>";
            $returnStr .= '<div class="panel-collapse collapse in" id="collapseOne" role="tabpanel" labelledby="headingOne">';
            $returnStr .= "<div class='panel-body'>";
            $returnStr .= '<table width=100%>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" readonly rows=2 class="form-control">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageUniqueRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '</table></div></div></div>';
            switchSurveyLanguageTranslator($language);
        }

        $langlabel = $arr[str_replace("_", "", getSurveyLanguagePostFix($language))]['name'];
        $returnStr .= "<div class='panel panel-default'>";
        $returnStr .= "<div class='panel panel-heading' role='tab' id='headingTwo'>";
        $returnStr .= '<a role="button" data-toggle="collapse" aria-expanded="true" aria-controls="collapseTwo" href="#collapseTwo">' . $langlabel . '</a>';
        $returnStr .= "</div>";
        $returnStr .= '<div class="panel-collapse collapse in" id="collapseTwo" role="tabpanel" labelledby="headingTwo">';
        $returnStr .= "<div class='panel-body'>";
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageUniqueRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '</table></div></div></div></div></div>';

        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);
        $langs = explode("~", $user->getLanguages(getSurvey(), getSurveyMode()));
        if (getSurveyLanguage() != $survey->getDefaultLanguage(getSurveyMode())) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonTranslate() . '"/>';
        }
        else if (inArray(getSurveyLanguage(), $langs)) {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* TOOLS FUNCTIONS */

    function showToolsHeader($type) {
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'translator.tools'), Language::linkTools()) . '</li>';
        if ($type != "") {
            $returnStr .= '<li class="active">' . $type . '</li>';
        }
        $returnStr .= '</ol>';
        return $returnStr;
    }

    function showTools() {
        $returnStr = $this->showToolsHeader();
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'translator.tools.test')) . '" class="list-group-item">' . Language::linkTester() . '</a>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showTest($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsTester());
        $returnStr .= $content;
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {

            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="translator.tools.test">';
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
                $langs[] = $default;
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

    /* PREFERENCES FUNCTIONS */

    function showPreferences($message = "") {
        $user = new User($_SESSION['URID']);
        $returnStr = $this->showTranslatorHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . Language::headerPreferences() . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'translator.preferences.res'));
        $checked = '';
        if ($user->hasNavigationInBreadCrumbs()) {
            $checked = ' CHECKED';
        }
        $returnStr .= '<div class="checkbox"><label><input name=navigationinbreadcrumbs value="1" type="checkbox"' . $checked . '>' . Language::labelNavigationInBreadCrumbs() . '</label></div>';
        $checked = '';
        if ($user->hasRoutingAutoIndentation()) {
            $checked = ' CHECKED';
        }
        $returnStr .= '<div class="checkbox"><label><input name=navigationinbreadcrumbs value="1" type="checkbox"' . $checked . '>' . Language::labelNavigationInBreadCrumbs() . '</label></div>';



        $returnStr .= '<br/>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="Save"/>';
        $returnStr .= '</form>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

}

?>