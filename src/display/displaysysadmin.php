<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplaySysAdmin extends Display {

    public function __construct() {
        parent::__construct();
    }

    function showSysAdminHeader($title, $extra = '') {

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
        $returnStr .= $this->getDirtyForms();
        $returnStr .= $this->displayAutoCompleteScripts();
        $returnStr .= $this->displayDraggable();
        return $returnStr;
    }

    public function showMain() {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms')) . '" class="list-group-item">' . Language::linkSms() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.surveys')) . '" class="list-group-item">' . Language::linkSurvey() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.output')) . '" class="list-group-item">' . Language::linkOutput() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools')) . '" class="list-group-item">' . Language::linkTools() . '</a>';
        $returnStr .= '</div>';
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
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
    <div class="waitmodal"></div>
';
    }

    public function showNavBar() {
        if (loadvar(POST_PARAM_AJAX_LOAD) == AJAX_LOAD) {
            return;
        }
        $smsActive = '';
        $surveyActive = '';
        $outputActive = '';
        $toolsActive = '';

        if (!isset($_SESSION['LASTPAGE'])) {
            $_SESSION['LASTPAGE'] = 'sysadmin.survey';
        }
        if (startsWith($_SESSION['LASTPAGE'], 'sysadmin.sms')) {
            $smsActive = ' active';
            $surveyActive = '';
            $outputActive = '';
            $toolsActive = '';
        }
        if (startsWith($_SESSION['LASTPAGE'], 'sysadmin.survey')) {
            $smsActive = '';
            $surveyActive = ' active';
            $outputActive = '';
            $toolsActive = '';
        }
        if (startsWith($_SESSION['LASTPAGE'], 'sysadmin.output')) {
            $smsActive = '';
            $surveyActive = '';
            $outputActive = ' active';
            $toolsActive = '';
        }
        if (startsWith($_SESSION['LASTPAGE'], 'sysadmin.tools')) {
            $smsActive = '';
            $surveyActive = '';
            $outputActive = '';
            $toolsActive = ' active';
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
            <a href="' . setSessionParams(array('page' => 'sysadmin.home')) . '" class="navbar-brand">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
              <li' . $smsActive . '>' . setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()) . '</li>
              <li class="dropdown' . $surveyActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkSurvey() . ' <b class="caret"></b></a>';

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        $returnStr .= '<ul class="dropdown-menu">';
        if (sizeof($surveys) > 0) {
            foreach ($surveys as $survey) {
                $span = '';
                if (isset($_SESSION['SUID']) && $_SESSION['SUID'] == $survey->getSuid()) {
                    $span = ' <span class="glyphicon glyphicon-chevron-down"></span>';
                }
                $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey', 'suid' => $survey->getSuid()), $survey->getName() . $span, "", POST_PARAM_NOAJAX . "=" . NOAJAX) . '</li>';
            }
        } else {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.addsurvey'), Language::labelSurveysAddNewCaps()) . '</li>';
        }
        $returnStr .= '</ul>';
        $returnStr .= '</li>';
        $returnStr .= '<li class="dropdown' . $outputActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkOutput() . ' <b class="caret"></b></a>';
        $returnStr .= '<ul class="dropdown-menu">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.data'), '<span class="glyphicon glyphicon-save"></span> ' . Language::linkData()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.statistics'), '<span class="glyphicon glyphicon-stats"></span> ' . Language::linkStatistics()) . '</li>';
        $returnStr .= '<li class="divider"></li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.documentation'), '<span class="glyphicon glyphicon-file"></span> ' . Language::linkDocumentation()) . '</li>';
        $returnStr .= '</ul></li>';

        $returnStr .= '<li class="dropdown' . $toolsActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkTools() . ' <b class="caret"></b></a>';
        $returnStr .= '<ul class="dropdown-menu">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.batcheditor'), '<span class="glyphicon glyphicon-tag"></span> ' . Language::linkBatchEditor()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.check'), '<span class="glyphicon glyphicon-check"></span> ' . Language::linkChecker()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.compile'), '<span class="glyphicon glyphicon-cog"></span> ' . Language::linkCompiler()) . '</li>';
        if (Config::xiExtension()) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.xicompile'), '<span class="glyphicon glyphicon-share"></span> ' . Language::linkXiCompiler()) . '</li>';
        }
        $returnStr .= '<li class="divider"></li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.test'), '<span class="glyphicon glyphicon-comment"></span> ' . Language::linkTester()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.issues'), '<span class="glyphicon glyphicon-thumbs-down"></span> ' . Language::linkReported()) . '</li>';
        $returnStr .= '<li class="divider"></li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.export'), '<span class="glyphicon glyphicon-export"></span> ' . Language::linkExport()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.import'), '<span class="glyphicon glyphicon-import"></span> ' . Language::linkImport()) . '</li>';
        $returnStr .= '<li class="divider"></li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.clean'), '<span class="glyphicon glyphicon-trash"></span> ' . Language::linkCleaner()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.flood'), '<span class="glyphicon glyphicon-random"></span> ' . Language::linkFlood()) . '</li>';
        $returnStr .= '</ul></li></ul>';


        $user = new User($_SESSION['URID']);
        $returnStr .= '<ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . $user->getUsername() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">
        		<li><a href="' . setSessionParams(array('page' => 'sysadmin.preferences')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkPreferences() . '</a></li>';
        if ($user->getUserType() == USER_SYSADMIN && $user->getUserSubType() == USER_SYSADMIN_MAIN) {
            $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.users')) . '"><span class="glyphicon glyphicon-user"></span> ' . Language::linkUsers() . '</a></li>';
        }

        $returnStr .= '<li class="divider"></li>
                   <li><a ' . POST_PARAM_NOAJAX . '=' . NOAJAX . ' href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>
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
                . setSessionParamsPost(array("page" => "sysadmin.search")) . '
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

    function showSections($sections) {
        $returnStr = '';
        if (sizeof($sections) > 0) {
            $user = new User($_SESSION['URID']);
            $returnStr = $this->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#sectiontable').dataTable(
                                {
                                    \"iDisplayLength\": " . $user->getItemsInTable() . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    colVis: {
                                        activate: \"mouseover\",
                                        exclude: [ 0, 1, 2 ]
                                    }
                                    }    
                                ).rowReordering(
                                    { 
                                     sURL: \"" . setSessionParams(array("page" => "sysadmin.survey.ordersection")) . "&updatesessionpage=2\"
                                     
                                   }
                                );
                                         
                       });</script>

                        "; //


            $returnStr .= $this->displayPopoverScript();
            $returnStr .= $this->displayCookieScripts();
            $returnStr .= '<table id="sectiontable" class="table table-striped table-bordered table-condensed table-hover">';
            $returnStr .= '<thead><tr><th style="display: none;">Position</th><th></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th></tr></thead><tbody>';
            $cnt = 1;
            foreach ($sections as $section) {
                $returnStr .= '<tr id="' . $section->getSeid() . '"><td style="display: none;">' . $cnt . '</td><td>';
                $cnt++;
                $content = '<a id="' . $section->getSeid() . '_edit" title="' . Language::linkEditTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.editsection', 'seid' => $section->getSeid())) . '"><span class="glyphicon glyphicon-edit"></span></a>';
                $content .= '&nbsp;&nbsp;<a id="' . $section->getSeid() . '_copy" title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copysection', 'seid' => $section->getSeid())) . '"><span class="glyphicon glyphicon-copyright-mark"></span></a>';

                $tagclass = 'class=""';
                if (isset($_COOKIE['uscicsectioncookie'])) {
                    $cookievalue = $_COOKIE['uscicsectioncookie'];
                    if (inArray($section->getSuid() . "~" . $section->getSeid(), explode("-", $cookievalue))) {
                        $tagclass = 'class="uscic-cookie-tag-active"';
                    }
                }

                $content .= '&nbsp;&nbsp;<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscicsectioncookie\',\'' . $section->getSuid() . "~" . $section->getSeid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href=""><span class="glyphicon glyphicon-tag"></span></a>';

                $surveys = new Surveys();
                if ($surveys->getNumberOfSurveys() > 1) {
                    $content .= '&nbsp;&nbsp;<a id="' . $section->getSeid() . '_move" title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movesection', 'seid' => $section->getSeid())) . '"><span class="glyphicon glyphicon-move"></span></a>';
                }
                if (!inArray($section->getName(), Common::surveyCoreSections())) {
                    $content .= '&nbsp;&nbsp;<a id="' . $section->getSeid() . '_remove" title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removesection', 'seid' => $section->getSeid())) . '"><span class="glyphicon glyphicon-remove"></span></a>';
                }
                $returnStr .= '<a rel="popover" id="' . $section->getSeid() . '_popover" data-placement="right" data-html="true" data-toggle="popover" data-trigger="hover" href="' . setSessionParams(array('page' => 'sysadmin.survey.section', 'seid' => $section->getSeid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= '</td><td>' . $section->getName() . '</td><td>' . $section->getDescription() . '</td></tr>';
                $returnStr .= $this->displayPopover("#" . $section->getSeid() . '_popover', $content);
            }
            $returnStr .= '</tbody></table>';
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoSectionsYet());
        }
        return $returnStr;
    }

    function showSurveys($message = "") {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . Language::headerSurveys() . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= $message;

        $returnStr .= '<div id=surveydiv></div>';
        $surveys = new Surveys();
        $returnStr .= $this->showSurveyList($surveys->getSurveys(false));
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showSurveyList($surveys) {
        $returnStr = '';
        if (sizeof($surveys) > 0) {
            $user = new User($_SESSION['URID']);
            $returnStr = $this->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#surveytable').dataTable(
                                {   
                                    \"iDisplayLength\": " . $user->getItemsInTable() . ",
                                    bFilter: false, 
                                    bInfo: false,
                                    paginate: false,
                                    dom: 'C<\"clear\">lfrtip',                                 
                                    colVis: {
                                        activate: \"mouseover\",
                                        exclude: [ 0,1 ]
                                    }
                                    }
                                ).rowReordering(
                                    { 
                                     sURL: \"" . setSessionParams(array("page" => "sysadmin.survey.ordersurvey")) . "&updatesessionpage=2\"
                                     
                                   }
                                );                                         
                       });</script>

                        "; //

            $returnStr .= $this->displayPopoverScript();
            $returnStr .= '<table id="surveytable" class="table table-striped table-bordered table-condensed table-hover">';
            $returnStr .= '<thead><tr><th style="display: none;">' . Language::labelTypeEditGeneralPosition() . '</th><th></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th></tr></thead><tbody>';
            $cnt = 1;
            foreach ($surveys as $survey) {
                $returnStr .= '<tr id="' . $survey->getSuid() . '"><td style="display: none;">' . $cnt . '</td><td>';
                $cnt++;
                $content = '<a id="' . $survey->getSuid() . '_edit" title="' . Language::linkEditTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.editsurvey', 'suid' => $survey->getSuid())) . '"><span class="glyphicon glyphicon-edit"></span></a>';
                $content .= '&nbsp;&nbsp;<a id="' . $survey->getSuid() . '_copy" title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copysurvey', 'suid' => $survey->getSuid())) . '"><span class="glyphicon glyphicon-copyright-mark"></span></a>';
                if ($survey->getDefaultSurvey() != DEFAULT_SURVEY_YES) {
                    $content .= '&nbsp;&nbsp;<a id="' . $survey->getSuid() . '_remove" title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removesurvey', 'suid' => $survey->getSuid())) . '"><span class="glyphicon glyphicon-remove"></span></a>';
                }
                $returnStr .= '<a rel="popover" id="' . $survey->getSuid() . '_popover" data-placement="right" data-html="true" data-toggle="popover" data-trigger="hover" href="' . setSessionParams(array('page' => 'sysadmin.survey', 'suid' => $survey->getSuid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';

                if ($survey->getDefaultSurvey() != DEFAULT_SURVEY_YES) {
                    $returnStr .= '</td><td>' . $survey->getName() . '</td><td>' . $survey->getDescription() . '</td></tr>';
                } else {
                    $returnStr .= '</td><td>' . $survey->getName() . Language::defaultSurveyIndicator() . '</td><td>' . $survey->getDescription() . '</td></tr>';
                }
                $returnStr .= $this->displayPopover("#" . $survey->getSuid() . '_popover', $content);
            }
            $returnStr .= '</tbody></table>';
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoSurveysYet());
        }
        $returnStr .= '<br/><a href="' . setSessionParams(array('page' => 'sysadmin.survey.addsurvey')) . '">' . Language::labelSurveysAddNew() . '</a>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        return $returnStr;
    }

    function getSurveyTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(0);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . Language::labelSections() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(2);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . Language::labelTypes() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(3);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . Language::labelGroups() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_survey\').val(1);$(\'#surveysidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . Language::labelSettings() . '</span></a>';
        return $returnStr;
    }

    function showSurvey($message = "") {

        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
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
            $returnStr .= $this->showSections($survey->getSections());
            $returnStr .= '<a href="' . setSessionParams(array('page' => 'sysadmin.survey.addsection')) . '">' . Language::labelSectionsAddNew() . '</a>';
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 1) {
            $returnStr .= $this->showSettingsList();
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 2) {
            $returnStr .= $this->showTypes($survey->getTypes());
        } else if ($_SESSION['VRFILTERMODE_SURVEY'] == 3) {
            $returnStr .= $this->showGroups($survey->getGroups());
        } else {
            $returnStr .= $this->showSections($survey->getSections());
            $returnStr .= '<a href="' . setSessionParams(array('page' => 'sysadmin.survey.addsection')) . '">' . Language::labelSectionsAddNew() . '</a>';
        }
        $returnStr .= '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '</div>';

        $returnStr .= '</div></div></div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showSection($seid, $message = '') {
        $user = new User($_SESSION['URID']);
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.section', 'seid' => $seid), $section->getName()) . '</li>';
        if (!$user->hasNavigationInBreadCrumbs()) {
            if ($_SESSION['VRFILTERMODE_SECTION'] == 0) {
                $returnStr .= '<li class="active">' . Language::labelVariables() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_SECTION'] == 1) {
                $returnStr .= '<li class="active">' . Language::labelRouting() . '</li>';
            }
            /* elseif ($_SESSION['VRFILTERMODE_SECTION'] == 3) {
              $returnStr .= '<li class="active">' . Language::labelGroups() . '</li>';
              } */ else {
                $returnStr .= '<li class="active">' . Language::labelVariables() . '</li>';
            }
        }
        $returnStr .= '</ol>';

//CONTENT

        $returnStr .= $message;

        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div style="" class="col-xs-12 col-sm-9">';


        if ($user->hasNavigationInBreadCrumbs()) {
            $active = array_fill(0, 16, 'label-primary');
            $active[$_SESSION['VRFILTERMODE_SECTION']] = 'label-default';
            if ($_SESSION['VRFILTERMODE_SECTION'] == 0) {
                $returnStr .= ' <span class="label ' . $active[0] . '">' . Language::labelVariables() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_section\').val(0);$(\'#sectionsidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . Language::labelVariables() . '</span></a>';
            }
            if ($_SESSION['VRFILTERMODE_SECTION'] == 1) {
                $returnStr .= ' <span class="label ' . $active[1] . '">' . Language::labelRouting() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_section\').val(1);$(\'#sectionsidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . Language::labelRouting() . '</span></a>';
            }
            /* if ($_SESSION['VRFILTERMODE_SECTION'] == 3) {
              $returnStr .= ' <span class="label ' . $active[3] . '">' . Language::labelGroups() . '</span>';
              } else {
              $returnStr .= ' <a onclick="$(\'#vrfiltermode_section\').val(3);$(\'#sectionsidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . Language::labelGroups() . '</span></a>';
              } */
        }

        $returnStr .= '<div class="well" style="background-color:white;">';

        if ($_SESSION['VRFILTERMODE_SECTION'] == 0) { //show variables
            $returnStr .= $this->showVariables($survey->getVariableDescriptives($seid, "position", "asc"));
        } elseif ($_SESSION['VRFILTERMODE_SECTION'] == 1) { //show routing!
            $returnStr .= $this->showRouting($seid);
        } elseif ($_SESSION['VRFILTERMODE_SECTION'] == 3) { //show groups!
            $returnStr .= $this->showGroups($survey->getGroups());
        } else {
            $returnStr .= $this->showTextBase($seid);
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

    function showRouting($seid) {
        $returnStr .= '<form id="hiddenform" name="hiddenform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.clickrouting'));
        $returnStr .= "<input type=hidden name=action id=action />";
        $returnStr .= '</form>';
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editrouting'));
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION["SEID"] = $seid;
        $section = $survey->getSection($seid);
        $returnStr .= '<textarea style="width: 100%; height: 350px;" id="routing" name="routing">';
        $returnStr .= $section->getRouting();
        $returnStr .= '</textarea>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="Save"/>';

        $returnStr .= '<span class="pull-right">';
        $returnStr .= '<button class="btn btn-default" data-toggle="modal" data-target="#listModal">' . Language::labelVariables() . '</button>';

        $track = new Track($_SESSION['SUID'], $seid, OBJECT_SECTION);
        $history = $track->getEntries(SETTING_ROUTING);
        if (sizeof($history) > 0) {
            $returnStr .= '<div class="btn-group">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ' . Language::labelHistory() . ' <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">';
            foreach ($history as $h) {
                $us = new User($h["urid"]);
                $returnStr .= '<li><a href="#" data-toggle="modal" data-target="#historyModal" data-trackid="' . $h["trid"] . '">' . $h["ts"] . ' (' . $us->getUsername() . ')</a></li>';
            }
            $returnStr .= '</ul>
        </div>';
        }

        $returnStr .= '<button class="btn btn-default" data-toggle="modal" data-target="#compiledModal">' . Language::labelCompiledCode() . '</button>';
        $returnStr .= '</span>';
        $returnStr .= '</form>';

        $user = new User($_SESSION['URID']);
        if ($user->hasRoutingAutoIndentation()) {
            $returnStr .= $this->getCodeMirror("height: 400px;");
            $line = getFromSessionParams('routingline');
            $extra = '';
            if ($line > 0) { // coming in from search result
                $extra .= "jumpToLine(" . $line . ");";
            }
            $returnStr .= '<link rel="stylesheet" href="js/codemirror/addon/hint/show-hint.css"/>
                <script src="js/codemirror/addon/hint/show-hint.js"></script>
                <script src="js/codemirror/addon/hint/nubis-hint.js"></script>';


            $returnStr .= '<script type="text/javascript">
                
                CodeMirror.commands.autocomplete = function(cm) {
                    cm.showHint({hint: CodeMirror.hint.nubis});
                  }
                function jumpToLine(i) {
                    var editor = $("#routing").data("CodeMirrorInstance");
                        // editor.getLineHandle does not help as it does not return the reference of line.
                        editor.setCursor(i);
                        window.setTimeout(function() {
                           editor.setLineClass(i, null, "center-me");
                           var line = $(".CodeMirror-lines .center-me");
                           var h = line.parent();

                           $(".CodeMirror-scroll").scrollTop(0).scrollTop(line.offset().top - $(".CodeMirror-scroll").offset().top - Math.round($(".CodeMirror-scroll").height()/2));
                       }, 200);
                    }
                $(document).ready(function() {
                
                    function words(str) {
                        var obj = [], words = str.split(" ");
                        for (var i = 0; i < words.length; ++i) {
                            obj[i] = words[i];
                        }
                        return obj;
                    }
                    var keywords = words("cardinal card group subgroup endgroup endsubgroup empty nonresponse dk rf do enddo endif for and array if then elseif else in mod not or to inline inspect fill");
                    var mirrorchanged = false;
                    var editor = CodeMirror.fromTextArea(document.getElementById("routing"), {mode: "text/x-nubis", lineNumbers: true, extraKeys: {"Ctrl-Space": "autocomplete"}});
                    $("#routing").data("CodeMirrorInstance", editor);
                    editor.on("dblclick", function(cm, event) {
                       if (event.ctrlKey) {                          
                          if (mirrorchanged == false) {
                              $("#editform").dirtyForms("setClean");                          
                          }
                          var sel = cm.getSelection().toLowerCase();
                          if ($.inArray(sel, keywords) == -1) { 
                            $("#action").val(sel);
                            $("#hiddenform").submit();                                        
                          }
                       }
                    });                    

                    editor.on("change", function(from, to, text, removed, origin) {
                        $("#routing").dirtyForms("setDirty");
                        mirrorchanged = true;
                    });

                    function format() {
                        var totalLines = editor.lineCount();  
                        editor.autoFormatRange({line:0, ch:0}, {line:totalLines});
                    }
                    
                    var historyeditor = CodeMirror.fromTextArea(document.getElementById("historycontent"), {mode: "text/x-nubis", lineNumbers: true});
                    $("#historyModal").on("show.bs.modal", function (event) {
                        event.stopImmediatePropagation();
                        var element = $(event.relatedTarget); // element that triggered the modal
                        var recipient = element.data("trackid"); // Extract info from data-* attributes
                        $.get("index.php?' . POST_PARAM_SMS_AJAX . '=' . SMS_AJAX_CALL . '&p=sysadmin.history.getentry&trid=" + recipient, null, function (data) {
                              historyeditor.setValue(data.trim());
                        }, "text"); 
                        });
                        
                     // jump to line
                     ' .
                    $extra . '
                });
            </script>';
        }



        $returnStr .= '<div class="modal fade" id="compiledModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">' . Language::labelRoutingCompiledCode() . '\'' . $section->getName() . '\'</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
           <textarea style="width: 100%; height: 100%;" rows=30 class="form-control">' . $section->getCompiledCode() . '</textarea>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
';
        // history modal
        if (sizeof($history) > 0) {
            $returnStr .= '<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">' . Language::labelRoutingHistory() . $section->getName() . '</h4>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                       <textarea id=historycontent style="width: 100%; height: 100%;"></textarea>
                     </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
                </div><!-- /.modal-content -->
              </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->';
        }

        $returnStr .= '<div class="modal fade" id="listModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Variables in \'' . $section->getName() . '\'</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
           <textarea style="width: 100%; height: 400px;" rows=30 class="form-control">';

        $vars = $survey->getVariableDescriptives($section->getSeid());
        foreach ($vars as $v) {
            $returnStr .= $v->getName() . "\r\n";
        }

        $returnStr .= '</textarea>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
';

        return $returnStr;
    }

    function showTextBase($seid) {
        $returnStr = '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittextbase'));
        $returnStr .= '<textarea class="form-control" name="question" cols="80" rows=20 name="routing">
//Variables + routing text base like Blaise

PRIM_KEY / "PRIMARY KEY": STRING
Q1 "How old are you?": RANGE(0..120)

RULES

Q1


</textarea>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="Save"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* DROPDOWNS */

    function showVisibility($current) {
        $array = array(VARIABLE_VISIBLE => Language::labelVisibilityVisible(), VARIABLE_HIDDEN => Language::labelVisibilityHidden());
        return $this->showDropDown($array, $current);
        /*        $returnStr = '<select name="hidden" class="selectpicker show-tick">';
          foreach ($array as $key => $value) {
          $selected = "";
          if ($key == $current) {
          $selected = "SELECTED";
          }
          $returnStr .= "<option $selected value='" . $key . "'>" . $value . "</option>";
          }
          $returnStr .= "</select>";
          return $returnStr; */
    }

    function showDropDown($array, $current, $name = 'hidden', $id = '') {
        $returnStr = '<select id="' . $id . '" name="' . $name . '" class="selectpicker show-tick">';
        foreach ($array as $key => $value) {
            $selected = "";
            if ($key == $current) {
                $selected = "SELECTED";
            }
            $returnStr .= "<option $selected value='" . $key . "'>" . $value . "</option>";
        }
        $returnStr .= "</select>";
        return $returnStr;
    }

    function showAnswerType($type) {
        $array = Common::getAnswerTypes();
        if (isset($array[$type])) {
            return $array[$type];
        }
        return '';
    }

    function showAnswerTypes($current = "", $type = -1) {
        /* see constants.php for full list */

        $array = Common::getAnswerTypes();

        $returnStr = '<select id="answertype" name="answertype" class="selectpicker show-tick">';
        if ($type != -1) {
            $returnStr .= "<option " . $selected[SETTING_FOLLOW_TYPE] . " value=" . SETTING_FOLLOW_TYPE . ">" . Language::optionsFollowType() . "</option>";
        }
        foreach ($array as $key => $value) {
            $selected = "";
            if ($key == $current) {
                $selected = "SELECTED";
            }
            $returnStr .= "<option $selected value='" . $key . "'>" . $value . "</option>";
        }

        $returnStr .= "</select>";
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#answertype").change(function (e) {
                                                    if (this.value == 2 || this.value == 3 || this.value == 15 || this.value == 16 || this.value == 17) {
                                                        $("#categories").show();
                                                        $("#customanswer").hide();
                                                        $("#section").hide();
                                                    }   
                                                    else if (this.value == 99) {
                                                        $("#categories").hide();
                                                        $("#section").hide();                                                        
                                                        $("#customanswer").show();                                                            
                                                    }
                                                    else if (this.value == 13) {
                                                        $("#categories").hide();
                                                        $("#section").show();                                                        
                                                        $("#customanswer").hide();                                                            
                                                    }
                                                    else {
                                                        $("#categories").hide();
                                                        $("#section").hide();                                                        
                                                        $("#customanswer").hide();
                                                    }
                                                });
                                                })';
        $returnStr .= "</script>";
        return $returnStr;
    }

    function showSurveySideBar($survey, $filter = 0) {
        $returnStr = '<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">';
        $returnStr .= '<center><span class="label label-default">' . $survey->getName() . '</span></center>';
        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        $returnStr .= $this->displaySurveysideBarFilter($survey, $filter);
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div></div>';
        return $returnStr;
    }

    function displaySurveySideBarFilter($survey, $filter = 0) {
        $active = array('', '', '', '');
        $active[$filter] = ' active';
        $params = getSessionParams();

        /* no mode/language dropdowns on edit routing page */
        $page = $_SESSION['LASTPAGE'];
        if (inArray($page, array("sysadmin.survey.section")) && $_SESSION['VRFILTERMODE_SECTION'] == 1) {
            
        } else {

            /* mode drop down (not for mode attributes) */
            $user = new User($_SESSION['URID']);
            if (!inArray($page, array("sysadmin.survey.editsettingsmode"))) {
                $returnStr = '<form id=modeform method="post">';
                $returnStr .= '<input type=hidden name=r value="' . setSessionsParamString($params) . '">';
                $returnStr .= $this->displayModesAdmin("surveymode", "surveymode", getSurveyMode(), "", implode("~", $user->getModes($survey->getSuid())));
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

            if (!inArray($page, array("sysadmin.survey.editsettingsmode", "sysadmin.survey.editsettingslanguage"))) {
                /* language dropdown */
                $returnStr .= '<form id=languageform method="post">';
                $returnStr .= '<input type=hidden name=r value="' . setSessionsParamString($params) . '">';
                $returnStr .= $this->displayLanguagesAdmin("surveylanguage", "surveylanguage", getSurveyLanguage(), true, false, true, "", $user->getLanguages($survey->getSuid(), getSurveyMode()), false);
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
        }

        $returnStr .= '<form method="post" id="surveysidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_survey" id="vrfiltermode_survey" value="' . $filter . '">';
        $returnStr .= '<div class="btn-group">';

        $returnStr .= '<div class="btn-group">';
        $returnStr .= '<button  title="' . Language::labelSections() . '" class="btn btn-default' . $active[0] . ' dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" onclick="$(\'#vrfiltermode_survey\').val(0);$(\'#surveysidebar\').submit();"><span class="glyphicon glyphicon-tasks"></span></button>';

        $returnStr .= '<ul class="dropdown-menu" role="menu">';
        $sections = $survey->getSections();
        foreach ($sections as $section) {
            $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.section', 'seid' => $section->getSeid())) . '">' . $section->getName() . '</a></li>';
        }
        $returnStr .= '</ul>';
        $returnStr .= '</div>';

        $returnStr .= '<button title="' . Language::labelTypes() . '" class="btn btn-default' . $active[2] . '" onclick="$(\'#vrfiltermode_survey\').val(2);$(\'#surveysidebar\').submit();"><span class="glyphicon glyphicon-list-alt"></span></button>';
        $returnStr .= '<button  title="' . Language::labelGroups() . '" class="btn btn-default' . $active[3] . '" onclick="$(\'#vrfiltermode_survey\').val(3);$(\'#surveysidebar\').submit();"><span class="glyphicon glyphicon-th-large"></span></button>';

        $returnStr .= '<div class="btn-group">';
        $returnStr .= '<button  title="' . Language::labelSettings() . '" class="btn btn-default' . $active[1] . ' dropdown-toggle" data-hover="dropdown" data-toggle="dropdown" onclick="$(\'#vrfiltermode_survey\').val(1);$(\'#surveysidebar\').submit();"><span class="glyphicon glyphicon-flash"></span></button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsaccess', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsAccess() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsassistance', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsAssistance() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingslayout', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsLayout() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsgeneral', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsGeneral() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsinteractive', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsInteractive() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsmode', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsMode() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingslanguage', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsLanguage() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsnavigation', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsNavigation() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsdata', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsData() . '</a></li>';
        $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'sysadmin.survey.editsettingsvalidation', 'suid' => $survey->getSuid())) . '">' . Language::labelSettingsValidation() . '</a></li>';
        $returnStr .= '</ul>';
        $returnStr .= '</div>';

        $returnStr .= '</div>';
        $returnStr .= '</form>';

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
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'sysadmin.survey.section', 'seid' => $previous->getSeid()), '<span class="glyphicon glyphicon-chevron-left"></span>', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getSeid() != "" && $next->getSeid() != $section->getSeid()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'sysadmin.survey.section', 'seid' => $next->getSeid()), '<span class="glyphicon glyphicon-chevron-right"></span>', 'title="' . $next->getName() . '"') . '</span>';
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
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.section'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_section" id="vrfiltermode_section" value="' . $filter . '">';

        $returnStr .= '<div class="btn-group">';
        $returnStr .= '<div class="btn-group-sm">';
        $returnStr .= '<button title="' . Language::linkEditTooltip() . '" class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><span class="glyphicon glyphicon-edit"></span></button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';
        $returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_section\').val(0);$(\'#sectionsidebar\').submit();">' . Language::labelVariables() . '</a></li>';
        $returnStr .= '<li><a ' . $active[1] . ' onclick="$(\'#vrfiltermode_section\').val(1);$(\'#sectionsidebar\').submit();">' . Language::labelRouting() . '</a></li>';
        $returnStr .= '</ul>';

        $tagclass = 'class="btn btn-default"';
        if (isset($_COOKIE['uscicsectioncookie'])) {
            $cookievalue = $_COOKIE['uscicsectioncookie'];
            if (inArray($section->getSuid() . "~" . $section->getSeid(), explode("-", $cookievalue))) {
                $tagclass = 'class="btn btn-default uscic-cookie-tag-active"';
            }
        }

        $returnStr .= '<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscicsectioncookie\',\'' . $section->getSuid() . "~" . $section->getSeid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a>';

        if (!inArray($section->getName(), Common::surveyCoreSections())) {
            $returnStr .= '<a title="' . Language::linkRefactorTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.refactorsection', 'seid' => $section->getSeid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-registration-mark"></span></a>';
        }
        $returnStr .= '<a title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copysection', 'seid' => $section->getSeid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-copyright-mark"></span></a>';

        if (!inArray($section->getName(), Common::surveyCoreSections())) {

            $surveys = new Surveys();
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<a title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movesection', 'seid' => $section->getSeid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-move"></span></a>';
            }

            $returnStr .= '<a title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removesection', 'seid' => $section->getSeid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>';
        }
        $returnStr .= '</div>';


        $returnStr .= '</div>';
        $returnStr .= '</form>';
        $returnStr .= $this->displayCookieScripts();
        return $returnStr;
    }

    /* SURVEYS */

    function showSurveyHeader($survey, $actiontype, $message) {

        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        if ($survey->getSuid() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';
        }
        $returnStr .= '<li class="active">' . $actiontype . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function showSurveyFooter($survey) {
        $returnStr = '</div>';
        $returnStr .= '</div>';
        $returnStr .= '</div></div>'; //container and wrap        
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showEditSurvey($suid, $message = "") {
        $survey = new Survey($suid);

        if ($survey->getSuid() != "") {
            $returnStr = $this->showSurveyHeader($survey, Language::headerEditSurvey(), $message);
        } else {
            $returnStr = $this->showSurveyHeader($survey, Language::headerAddSurvey(), $message);
        }

        if ($survey->getSuid() != "") {
            $returnStr .= '<form id="editform" method="post">';
        } else {
            $returnStr .= '<form id="editform" method="post" ' . POST_PARAM_NOAJAX . '=' . NOAJAX . '>';
        }
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsurveyres', 'suid' => $survey->getSuid()));
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::editSurveyName() . '</td><td><input type="text" class="form-control" name="name" value="' . convertHTLMEntities($survey->getName(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::editSurveyTitle() . '</td><td><input type="text" class="form-control" name="' . SETTING_TITLE . '" value="' . convertHTLMEntities($survey->getTitle(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::editSurveyDescription() . '</td><td><textarea cols="60" rows="3" class="form-control" name="' . SETTING_DESCRIPTION . '">' . $survey->getDescription() . '</textarea></td></tr>';


        $surveys = new Surveys();
        $surveys = $surveys->getNumberOfSurveys();
        if (($survey->getSuid() != "" && $surveys > 1) || ($survey->getSuid() == "" && $surveys >= 1)) {
            $returnStr .= $this->displayComboBox();
            $selected = array(DEFAULT_SURVEY_YES => '', DEFAULT_SURVEY_NO => '');
            $selected[$survey->getDefaultSurvey()] = " SELECTED ";
            $returnStr .= '<tr><td align=top>' . Language::editSurveyDefault() . '</td><td>
                                <select class="selectpicker show-tick" name="' . SETTING_DEFAULT_SURVEY . '">
                                    <option ' . $selected[DEFAULT_SURVEY_NO] . ' value=' . DEFAULT_SURVEY_NO . ' />' . Language::optionsDefaultSurveyNo() . '</option>
                                    <option ' . $selected[DEFAULT_SURVEY_YES] . 'value=' . DEFAULT_SURVEY_YES . ' />' . Language::optionsDefaultSurveyYes() . '</option>
                        </select></td></tr>';
        }

        $returnStr .= '</table>';

        if ($survey->getSuid() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        $returnStr .= $this->showSurveyFooter($survey);
        return $returnStr;
    }

    function showCopySurvey($suid, $message = "") {
        $survey = new Survey($suid);
        $returnStr = $this->showSurveyHeader($survey, Language::headerCopySurvey(), $message);

        $returnStr .= $this->showSurveyFooter($survey);
        return $returnStr;
    }

    function showRemoveSurvey($suid, $message = "") {
        $survey = new Survey($suid);
        $returnStr = $this->showSurveyHeader($survey, Language::headerRemoveSurvey(), $message);
        $returnStr .= $this->displayWarning(Language::messageRemoveSurvey($survey->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.removesurveyres', 'suid' => $survey->getSuid()));
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= $this->showSurveyFooter($survey);
        return $returnStr;
    }

    /* SECTIONS */

    function showSectionHeader($survey, $section, $type, $message) {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';
        if ($section->getSeid() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.section', 'seid' => $section->getSeid()), $section->getName()) . '</li>';
        }
        $returnStr .= '<li class="active">' . $type . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function showSectionFooter($survey) {
        $returnStr = '</div>';
        $returnStr .= $this->showSurveySideBar($survey, $_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= $this->showSectionSideBar($survey, $_SESSION['VRFILTERMODE_SECTION']);
        $returnStr .= '</div>';
        $returnStr .= '</div></div>'; //container and wrap        
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showEditSection($seid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        if ($section->getSeid() != "") {
            $returnStr = $this->showSectionHeader($survey, $section, Language::headerEditSection(), $message);
        } else {
            $returnStr = $this->showSectionHeader($survey, $section, Language::headerAddSection(), $message);
            $section->setHeader(loadvarAllowHTML(SETTING_SECTION_HEADER));
            $section->setFooter(loadvarAllowHTML(SETTING_SECTION_FOOTER));
            $section->setDescription(loadvarAllowHTML(SETTING_DESCRIPTION));
        }

        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsectionres', 'seid' => $section->getSeid()));
        $returnStr .= '<table>';

        if (!inArray($section->getName(), Common::surveyCoreSections())) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralName() . '</td><td><input type="text" class="form-control" name="' . SETTING_NAME . '" value="' . convertHTLMEntities($section->getName(), ENT_QUOTES) . '"></td></tr>';
        } else {
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralName() . '</td><td><span>' . convertHTLMEntities($section->getName(), ENT_QUOTES) . '</span></td></tr>';
            $returnStr .= '<input type=hidden name="' . SETTING_NAME . '" value="' . $section->getName() . '" />';
        }

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }
        $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralDescription() . '</td><td><textarea cols="60" rows="3" class="form-control" name="' . SETTING_DESCRIPTION . '">' . convertHTLMEntities($section->getDescription()) . '</textarea></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelSectionEditHeader() . '</td><td><textarea cols="60" rows="3" class="form-control' . $tinymce . '" id="' . SETTING_SECTION_HEADER . '" name="' . SETTING_SECTION_HEADER . '">' . ($section->getHeader()) . '</textarea></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelSectionEditFooter() . '</td><td><textarea cols="60" rows="3" class="form-control' . $tinymce . '" id="' . SETTING_SECTION_FOOTER . '" name="' . SETTING_SECTION_FOOTER . '">' . convertHTLMEntities($section->getFooter()) . '</textarea></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralHidden() . '</td><td>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->showVisibility($section->getHidden());
        $returnStr .= '</td></tr>';
        $returnStr .= '</table></div>';

        if ($section->getSeid() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        $returnStr .= $this->showSectionFooter($survey);
        return $returnStr;
    }

    function showRefactorSection($seid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $returnStr = $this->showSectionHeader($survey, $section, Language::headerRefactorSection(), $message);
        $returnStr .= $this->displayWarning(Language::messageRefactorSection($section->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.refactorsectionres', 'seid' => $section->getSeid()));
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelTypeRefactor() . '</td>';
        $returnStr .= "<td><input class='form-control' type=text name=" . SETTING_NAME . " /></td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRefactor() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showSectionFooter($survey);
        return $returnStr;
    }

    function showMoveSection($seid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $returnStr = $this->showSectionHeader($survey, $section, Language::headerMoveSection(), $message);
        $returnStr .= $this->displayWarning(Language::messageMoveSection($section->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.movesectionres', 'seid' => $section->getSeid()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();

        $surveys = new Surveys();
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], $_SESSION['SUID']) . '</tr>';
        }
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showSectionFooter($survey);
        return $returnStr;
    }

    function showCopySection($seid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $returnStr = $this->showSectionHeader($survey, $section, Language::headerCopySection(), $message);
        $returnStr .= $this->displayWarning(Language::messageCopySection($section->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.copysectionres', 'seid' => $section->getSeid()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], '') . '</tr>';

        $returnStr .= "<tr><td>" . Language::labelTypeCopySuffix() . "</td>";

        $returnStr .= "<td><select class='selectpicker show-tick' name=includesuffix>";
        $returnStr .= "<option value=" . INPUT_MASK_YES . ">" . Language::optionsInputMaskYes() . "</option>";
        $returnStr .= "<option value=" . INPUT_MASK_NO . ">" . Language::optionsInputMaskNo() . "</option>";
        $returnStr .= "</select></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';

        $returnStr .= $this->showSectionFooter($survey);
        return $returnStr;
    }

    function showRemoveSection($seid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($seid);
        $returnStr = $this->showSectionHeader($survey, $section, Language::headerRemoveSection(), $message);

        $returnStr .= $this->displayWarning(Language::messageRemoveSection($section->getName()));

        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.removesectionres', 'seid' => $section->getSeid()));

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showSectionFooter($survey);
        return $returnStr;
    }

    /* VARIABLES */

    function showVariableHeader($survey, $section, $variable, $type, $message) {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.section', 'seid' => $section->getSeid()), $section->getName()) . '</li>';
        if ($variable->getVsid() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.variable', 'vsid' => $variable->getVsid()), $variable->getName()) . '</li>';
        }
        if ($variable->getVsid() != "") {
            /*    if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0) {
              $returnStr .= '<li class="active">' . Language::headerEditTypeGeneral() . '</li>';
              } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 1) {
              $returnStr .= '<li class="active">' . Language::headerEditTypeVerification() . '</li>';
              } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 2) {
              $returnStr .= '<li class="active">' . Language::headerEditTypeLayout() . '</li>';
              } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 3) {
              $returnStr .= '<li class="active">' . Language::headerEditTypeAssistance() . '</li>';
              } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 4) {
              $returnStr .= '<li class="active">' . Language::headerEditTypeFill() . '</li>';
              } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 5) {
              $returnStr .= '<li class="active">' . Language::headerEditTypeOutput() . '</li>';
              } else {
              $returnStr .= '<li class="active">' . Language::headerEditTypeInteractive() . '</li>';
              } */
        } else {
            $returnStr .= '<li class="active">' . Language::headerAddVariable() . '</li>';
        }
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

    function showVariables($variables) {

        if (sizeof($variables) > 0) {
            $user = new User($_SESSION['URID']);
            $returnStr = $this->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#variabletable').dataTable(
                                {
                                    \"iDisplayLength\": " . $user->getItemsInTable() . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    colVis: {
                                        activate: \"mouseover\",
                                        exclude: [ 0, 1, 2 ]
                                    }
                                    }    
                                ).rowReordering(
                                    { 
                                     sURL: \"" . setSessionParams(array("page" => "sysadmin.survey.ordervariable")) . "&updatesessionpage=2\"
                                     
                                   }
                                );
                                         
                       });</script>

                        "; //
            $returnStr .= $this->displayPopoverScript();
            $returnStr .= $this->displayCookieScripts();
            $returnStr .= '<table id="variabletable" class="table table-striped table-bordered table-condensed table-hover">';
            $returnStr .= '<thead><tr><th style="display: none;">' . Language::labelTypeEditGeneralPosition() . '</th><th></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralQuestion() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralDescription() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralAnswerType() . '</th></tr></thead><tbody>';
            $cnt = 1;
            foreach ($variables as $variable) {
                $style = "";
                $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
                if ($variable->getHidden() == HIDDEN_YES) {
                    $style = "style='color: red;'";
                }
                $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
                $returnStr .= '<tr ' . $style . ' id="' . $variable->getVsid() . '"><td style="display: none;">' . $cnt . '</td><td>';
                $cnt++;
                $content = '<a title="' . Language::linkEditTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.editvariable', 'vsid' => $variable->getVsid())) . '"><span class="glyphicon glyphicon-edit"></span></a>';
                $content .= '&nbsp;&nbsp;<a title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copyvariable', 'vsid' => $variable->getVsid())) . '"><span class="glyphicon glyphicon-copyright-mark"></span></a>';

                if (isset($_COOKIE['uscicvariablecookie'])) {
                    $cookievalue = $_COOKIE['uscicvariablecookie'];
                    if (inArray($variable->getSuid() . "~" . $variable->getVsid(), explode("-", $cookievalue))) {
                        $tagclass = 'class="btn btn-default uscic-cookie-tag-active"';
                    }
                }

                $content .= '&nbsp;&nbsp;<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscicvariablecookie\',\'' . $variable->getSuid() . "~" . $variable->getVsid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a>';

                $surveys = new Surveys();
                $survey = new Survey($_SESSION['SUID']);
                if ($surveys->getNumberOfSurveys() > 1 || $survey->getNumberOfSections() > 1) {
                    $content .= '&nbsp;&nbsp;<a id="' . $variable->getName() . '_move" title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movevariable', 'vsid' => $variable->getVsid())) . '"><span class="glyphicon glyphicon-move"></span></a>';
                }

                if (!inArray($variable->getName(), Common::surveyCoreVariables())) {
                    $content .= '&nbsp;&nbsp;<a id="' . $variable->getName() . '_remove" title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removevariable', 'vsid' => $variable->getVsid())) . '"><span class="glyphicon glyphicon-remove"></span></a>';
                }
                $returnStr .= '<a rel="popover" id="' . $variable->getName() . '_popover" data-placement="right" data-html="true" data-toggle="popover" data-trigger="hover" href="' . setSessionParams(array('page' => 'sysadmin.survey.editvariable', 'vsid' => $variable->getVsid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= '</td><td>' . $variable->getName() . '</td><td>' . convertHTLMEntities($variable->getQuestion(), ENT_QUOTES) . '</td><td>' . $variable->getDescription() . '</td><td>' . $this->showAnswerType($variable->getAnswerType()) . '</td></tr>';
                $returnStr .= $this->displayPopover("#" . $variable->getName() . '_popover', $content);
            }
            $returnStr .= '</tbody></table>';
        } else {
            $returnStr = $this->displayWarning(Language::messageNoVariablesYet());
        }
        $returnStr .= '<a href="' . setSessionParams(array('page' => 'sysadmin.survey.addvariable')) . '">' . Language::labelVariablesAddNew() . '</a>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        return $returnStr;
    }

    function showEditVariable($vsid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $var = $survey->getVariableDescriptive($vsid);
        if ($var->getVsid() != "") {
            $returnStr = $this->showVariableHeader($survey, $section, $var, Language::headerEditVariable(), $message);
        } else {
            $returnStr = $this->showVariableHeader($survey, $section, $var, Language::headerAddVariable(), $message);
        }

        /* edit existing variable */
        if ($var->getName() != "") {
            if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0) {
                $returnStr .= $this->showEditVariableGeneral($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 1) {
                $returnStr .= $this->showEditVariableVerification($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 2) {
                $returnStr .= $this->showEditVariableLayout($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 3) {
                $returnStr .= $this->showEditVariableAssistance($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 4) {
                $returnStr .= $this->showEditVariableFill($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 5) {
                $returnStr .= $this->showEditVariableOutput($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 7) {
                $returnStr .= $this->showEditVariableNavigation($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 8) {
                $returnStr .= $this->showEditVariableAccess($var);
            } elseif ($_SESSION['VRFILTERMODE_VARIABLE'] == 9) {
                $returnStr .= $this->showEditVariableCheck($var);
            } else {
                $returnStr .= $this->showEditVariableInteractive($var);
            }
        }
        /* new variable */ else {
            $returnStr .= $this->showEditVariableGeneral($var);
        }
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showVariableFooter($survey);
        return $returnStr;
    }

    function showAvailableTypes($current) {
        $survey = new Survey($_SESSION['SUID']);
        $types = $survey->getTypes();
        if (sizeof($types) == 0) {
            return "";
        }
        $returnStr = '<tr><td>' . Language::labelTypeEditGeneralVariableType() . '</td><td>';
        $returnStr .= "<select id='typeselect' class='selectpicker show-tick' name=" . SETTING_TYPE . ">";
        $selected = "";
        if ($current == "" || $current == -1) {
            $selected = " SELECTED";
        }
        $returnStr .= "<option $selected value=-1>" . Language::optionTypeNone() . "</option>";
        foreach ($types as $type) {
            $selected = "";
            if ($type->getTyd() == $current) {
                $selected = " SELECTED";
            }
            $returnStr .= "<option $selected value=" . $type->getTyd() . ">" . $type->getName() . "</option>";
        }
        $returnStr .= "</select>";
        $returnStr .= '</td></tr>';

        $returnStr .= '<script type="text/javascript">
            $( document ).ready(function() {
                        
                        $("#typeselect").change(function() {                        
                            if (this.value == -1) {
                                $("#arraytypeoption").remove();
                                $("#keeptypeoption").remove();
                                $("#answertypeoption").remove();
                            }
                            else {
                                $("#arraydrop option:first").after($("<option />", { "value": "' . SETTING_FOLLOW_TYPE . '", text: "' . Language::optionsFollowType() . '", id: "arraytypeoption" }));  
                                $("#keepdrop option:first").after($("<option />", { "value": "' . SETTING_FOLLOW_TYPE . '", text: "' . Language::optionsFollowType() . '", id: "keeptypeoption" }));  
                                $("#answertype").prepend("<option value=\'' . SETTING_FOLLOW_TYPE . '\' id=\'answertypeoption\'>' . Language::optionsFollowType() . '</option>");  
                            }
                            $("#arraydrop").selectpicker("refresh");
                            $("#keepdrop").selectpicker("refresh");
                            $("#answertype").selectpicker("refresh");
                        });   
                        });
                    </script>';
        return $returnStr;
    }

    function getVariableTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';
        if ($filter == 0) {
            $returnStr .= ' <span class="label label-default">' . Language::labelTypeEditGeneral() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(0);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . Language::labelGeneral() . '</span></a>';
        }
        if ($filter == 8) {
            $returnStr .= ' <span class="label label-default">' . Language::labelTypeEditAccess() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(8);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[8] . '">' . Language::labelAccess() . '</span></a>';
        }
        $survey = new Survey($_SESSION['SUID']);
        $variable = $survey->getVariableDescriptive($_SESSION['VSID']);
        $answertype = $variable->getAnswerType();
        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                if ($filter == 1) {
                    $returnStr .= ' <span class="label ' . $active[1] . '">' . Language::labelVerification() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(1);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . Language::labelVerification() . '</span></a>';
                }
            }
            if ($filter == 2) {
                $returnStr .= ' <span class="label ' . $active[2] . '">' . Language::labelLayout() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(2);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . Language::labelLayout() . '</span></a>';
            }
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                if ($filter == 3) {
                    $returnStr .= ' <span class="label ' . $active[3] . '">' . Language::labelAssistance() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(3);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . Language::labelAssistance() . '</span></a>';
                }
                if ($filter == 4) {
                    $returnStr .= ' <span class="label ' . $active[4] . '">' . Language::labelFill() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(4);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[4] . '">' . Language::labelFill() . '</span></a>';
                }
                if ($survey->isApplyChecks() == true) {
                    if ($filter == 9) {
                        $returnStr .= ' <span class="label ' . $active[9] . '">' . Language::labelCheck() . '</span>';
                    } else {
                        $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(9);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[9] . '">' . Language::labelCheck() . '</span></a>';
                    }
                }
                if ($filter == 6) {
                    $returnStr .= ' <span class="label ' . $active[6] . '">' . Language::labelInteractive() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(6);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[6] . '">' . Language::labelInteractive() . '</span></a>';
                }
            }
            if ($filter == 5) {
                $returnStr .= ' <span class="label ' . $active[5] . '">' . Language::labelOutput() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(5);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[5] . '">' . Language::labelOutput() . '</span></a>';
            }
        }
        if ($filter == 7) {
            $returnStr .= ' <span class="label label-default">' . Language::labelNavigation() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_variable\').val(7);$(\'#variablesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[7] . '">' . Language::labelNavigation() . '</span></a>';
        }

        return $returnStr;
    }

    function showContextMenu($id, $objecttype, $sub) {
        $returnStr = '<ul id="contextMenu" class="dropdown-menu" role="menu" style="display:none" >
    
<li class="dropdown">
              <a href="#">' . Language::labelHistory() . '</a>
              <ul class="dropdown-menu">
              <li><a tabindex="-1" href="#">' . Language::labelRefactor() . '</a></li>
</ul>

    <li><a tabindex="-1" href="#">' . Language::labelRefactor() . '</a></li>
    <li><a tabindex="-1" href="#">Something else here</a></li>
    <li class="divider"></li>
    <li><a tabindex="-1" href="#">Separated link</a></li>
</ul>
<script type="text/javascript" src="js/jquery.contextmenu.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
$( ".uscic-form-control-admin" ).contextMenu({
    menuSelector: "#contextMenu",
    menuSelected: function (invokedOn, selectedMenu) {
        var msg = "You selected the menu item \'" + selectedMenu.text() +
            "\' on the value \'" + invokedOn.text() + "\'";
        alert(msg);
    }
});
});
</script>';
        return $returnStr;
    }

    function showEditVariableGeneral($var) {
        $returnStr = '<form id="editform" method="post">';
        if ($var->getVsid() != "") {
            $returnStr .= $this->getVariableTopTab(0);
        } else {
            $var->setQuestion(loadvarAllowHTML(SETTING_QUESTION));
            $var->setAnswerType(loadvar(SETTING_ANSWERTYPE));
            $var->setDescription(loadvarAllowHTML(SETTING_DESCRIPTION));
            $var->setKeep(loadvar(SETTING_KEEP));
            $var->setArray(loadvar(SETTING_ARRAY));
            $var->setKeep(loadvar(SETTING_KEEP));
            $var->setTyd(loadvar(SETTING_TYPE));
            $t = $var->getAnswerType();
            if (inArray($t, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK))) {
                $var->setOptionsText(loadvarAllowHTML(SETTING_OPTIONS));
            } else if ($t == ANSWER_TYPE_CUSTOM) {
                $var->setAnswerTypeCustom(loadvar(SETTING_ANSWERTYPE_CUSTOM));
            } else if ($t == ANSWER_TYPE_SECTION) {
                $var->setSection(loadvar(SETTING_SECTION));
            }
        }
        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariablegeneralres', 'vsid' => $var->getVsid()));
        $returnStr .= '<table width=100%>';
        if (!inArray($var->getName(), Common::surveyCoreVariables())) {
            $returnStr .= '<tr><td width=15%>' . Language::labelTypeEditGeneralVariableName() . '</td><td colspan=2><input type="text" class="form-control" name="' . SETTING_NAME . '" value="' . convertHTLMEntities($var->getName(), ENT_QUOTES) . '"></td></tr>';
        } else {
            $returnStr .= '<tr><td width=15%>' . Language::labelTypeEditGeneralVariableName() . '</td><td colspan=2><span>' . convertHTLMEntities($var->getName(), ENT_QUOTES) . '</span></tr>';
            $returnStr .= '<input type=hidden name="' . SETTING_NAME . '" value="' . $var->getName() . '" />';
        }

        $returnStr .= $this->showAvailableTypes($var->getTyd());

        $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralDescription() . '</td><td colspan=2><input type="text" class="form-control" name="' . SETTING_DESCRIPTION . '" value="' . convertHTLMEntities($var->getDescription(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralQuestion() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control' . $tinymce . ' autocomplete" id="' . SETTING_QUESTION . '" name="' . SETTING_QUESTION . '">' . convertHTLMEntities($var->getQuestion(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralAnswerType() . '</td><td>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->showAnswerTypes($var->getAnswerType(), $var->getTyd());
        $returnStr .= '</td>';

        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }

        if ($answertype == ANSWER_TYPE_CUSTOM) {
            $returnStr .= '</td><td id="customanswer" style="display: block;"><input type="text" placeholder="' . Language::labelCustomFunctionCall() . '" class="form-control autocompletebasic" name="' . SETTING_ANSWERTYPE_CUSTOM . '" value="' . $this->displayTextSettingValue($var->getAnswerTypeCustom()) . '"></td>';
        } else {
            $returnStr .= '</td><td id="customanswer" style="display: none;"><input type="text" placeholder="' . Language::labelCustomFunctionCall() . '" class="form-control autocompletebasic" name="' . SETTING_ANSWERTYPE_CUSTOM . '" value="' . $this->displayTextSettingValue($var->getAnswerTypeCustom()) . '"></td>';
        }

        $returnStr .= '</tr>';


        /* categories needed */
        $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK);


        if (inArray($answertype, $array)) {
            $returnStr .= '<tr id="categories"><td valign=top>' . Language::labelTypeEditGeneralCategories() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control uscic-form-control-admin autocomplete" name="' . SETTING_OPTIONS . '">' . $this->displayTextSettingValue(convertHTLMEntities($var->getOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';
        } else {
            $returnStr .= '<tr id="categories" style="display: none;"><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control uscic-form-control-admin ' . $tinymce . ' autocomplete" name="' . SETTING_OPTIONS . '"></textarea></td></tr>';
        }

        $suid = $_SESSION['SUID'];
        if ($var->getVsid() != "") {
            $suid = $var->getSuid();
        }
        if ($answertype == ANSWER_TYPE_SECTION) {
            $returnStr .= "<tr id='section'><td>" . Language::labelTypeEditGeneralSection() . "</td>";
            $returnStr .= "<td>" . $this->displaySections(SETTING_SECTION, $var->getSection(), $suid, $var->getSeid()) . "</td></tr>";
        } else {
            $returnStr .= "<tr id='section' style='display: none;'><td>" . Language::labelTypeEditGeneralSection() . "</td>";
            $returnStr .= "<td>" . $this->displaySections(SETTING_SECTION, $var->getSection(), $suid, $var->getSeid()) . "</td></tr>";
        }

        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralArray() . "</td>";
        $returnStr .= "<td colspan=2>" . $this->displayIsArray($var->getArray(), $var->getTyd()) . "</td></tr>";

        /* no keep/hidden/encryption required */
        if (inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
            $returnStr .= "<input type=hidden name='" . SETTING_HIDDEN . "' value='" . HIDDEN_YES . "'>";
            $returnStr .= "<input type=hidden name='" . SETTING_KEEP . "' value='" . KEEP_ANSWER_NO . "'>";
        } else {
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralKeep() . "</td>";
            $returnStr .= "<td colspan=2>" . $this->displayIsKeep($var->getKeep(), $var->getTyd()) . "</td></tr>";
        }

        $returnStr .= '</table></div>';

        if ($var->getVsid() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditVariableLayout($variable) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariablelayoutres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->displayComboBox();
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        $survey = new Survey($_SESSION['SUID']);
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());
        } else {
            if ($variable->getTyd() > 0) {
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }

        /* header/footer setting */
        $returnStr .= $this->getVariableTopTab(2);
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsHeader() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_HEADER . '" name="' . SETTING_PAGE_HEADER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPageHeader(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsFooter() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_FOOTER . '" name="' . SETTING_PAGE_FOOTER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPageFooter(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsPlaceholder() . '</td><td>' . $helpstart . '<input name="' . SETTING_PLACEHOLDER . '" type="text" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPlaceholder(), ENT_QUOTES)) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutOverall() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditQuestionAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_QUESTION_ALIGNMENT, $variable->getQuestionAlignment(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditQuestionFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_QUESTION_FORMATTING, $variable->getQuestionFormatting(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditAnswerAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_ANSWER_ALIGNMENT, $variable->getAnswerAlignment(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>
                        <td>" . Language::labelTypeEditAnswerFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_ANSWER_FORMATTING, $variable->getAnswerFormatting(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditButtonAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_BUTTON_ALIGNMENT, $variable->getButtonAlignment(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_BUTTON_FORMATTING, $variable->getButtonFormatting(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= '</table></div>';

        if ($answertype == ANSWER_TYPE_TIME) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutTimePicker() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutFormat() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_TIME_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getTimeFormat(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_DATE) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDatePicker() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutFormat() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getDateFormat(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateDefault() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_DEFAULT_VIEW . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getDateDefaultView(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_DATETIME) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDateTimePicker() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutFormat() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATETIME_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getDateTimeFormat(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateDefault() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_DEFAULT_VIEW . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getDateDefaultView(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateTimeCollapse() . '</td>';
            $returnStr .= "<td>" . $this->displayCollapse(SETTING_DATETIME_COLLAPSE, $variable->getDateTimeCollapse(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateTimeSideBySide() . '</td>';
            $returnStr .= "<td>" . $this->displaySideBySide(SETTING_DATETIME_SIDE_BY_SIDE, $variable->getDateTimeSideBySide(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_SLIDER) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSlider() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutOrientation() . "</td>";
            $returnStr .= "<td>" . $this->displayOrientation(SETTING_SLIDER_ORIENTATION, $variable->getSliderOrientation(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>";
            $returnStr .= "<td>" . Language::labelTypeEditLayoutStep() . "</td>";
            $returnStr .= "<td>" . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getIncrement(), ENT_QUOTES)) . '">' . $helpend . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSliderLabelPlacement() . '</td>';
            $returnStr .= "<td>" . $this->displaySliderPlacement(SETTING_SLIDER_LABEL_PLACEMENT, $variable->getSliderLabelPlacement(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td><td>" . Language::labelTypeEditLayoutSliderLabels() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_LABELS . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getSliderLabels(), ENT_QUOTES)) . '"></td></tr>';

            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutTooltip() . "</td>";
            $returnStr .= "<td>" . $this->displayTooltip(SETTING_SLIDER_TOOLTIP, $variable->getTooltip(), true, $variable->getTyd()) . "</td><td><nobr/></td>
                    <td>" . Language::labelTypeEditLayoutDot() . "</td><td>" . $this->displaySliderMarker(SETTING_SLIDER_PRESELECTION, $variable->getSliderPreSelection(), true, $variable->getTyd()) . "</td>
                    </tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditFormatter() . '</td><td colspan=4><div class="input-group"><textarea style="min-width: 600px; width: 100%; min-height: 100px;" class="form-control autocompletebasic" id="' . SETTING_SLIDER_FORMATER . '" name="' . SETTING_SLIDER_FORMATER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getSliderFormater(), ENT_QUOTES)) . '</textarea><span class="input-group-addon"><i>If empty, follows survey</i></span></div></td></tr>';

            $returnStr .= "</table>";

            $user = new User($_SESSION['URID']);
            if ($user->hasRoutingAutoIndentation()) {
                $returnStr .= $this->getCodeMirror('height: 100px; width: 500px;');
                $returnStr .= '<script src="js/codemirror/mode/javascript/javascript.js"></script>';
                $returnStr .= '<script src="js/codemirror/mode/css/css.js"></script>';
                $returnStr .= '<script type="text/javascript">                                
                                $(document).ready(function() {
                                   var editor = CodeMirror.fromTextArea(document.getElementById("' . SETTING_SLIDER_FORMATER . '"), {mode: "text/javascript", lineNumbers: false});
                                });
                           </script>';
            }


            $returnStr .= "<br/><br/><table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
            $returnStr .= "<td>" . $this->displayTextBox(SETTING_SLIDER_TEXTBOX, $variable->getTextBox(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelSliderTextBoxBefore() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getTextBoxLabel(), ENT_QUOTES)) . '">' . $helpend . '</td>';
            $returnStr .= "<td width=25><nobr/></td><td>" . Language::labelSliderTextBoxAfter() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getTextBoxPostText(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSpinner() . '</td><td>' . $this->displaySpinner($variable->getSpinner(), true, $variable->getTyd()) . "</td>";
            $returnStr .= '<td width=25><nobr/></td><td>' . Language::labelTypeEditGeneralSpinnerType() . '</td><td>' . $this->displaySpinnerType($variable->getSpinnerType(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerUp() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_UP, $variable->getSpinnerUp()) . $helpend . "</td>";
            $returnStr .= "<td width=25><nobr/></td><td>" . Language::labelTypeEditGeneralSpinnerDown() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_DOWN, $variable->getSpinnerDown()) . $helpend . "</td></tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutManual() . '</td><td>' . $this->displayManual($variable->getTextboxManual(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_KNOB) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutKnob() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutStep() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getIncrement(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            //$returnStr .= "<tr><td>" . Language::labelTypeEditLayoutRotation() . "</td>";
            //$returnStr .= "<td>" . $this->displayRotation(SETTING_KNOB_ROTATION, $variable->getKnobRotation(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td><td>" . Language::labelTypeEditLayoutStep() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getIncrement(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
            $returnStr .= "<td>" . $this->displayTextBox(SETTING_SLIDER_TEXTBOX, $variable->getTextBox(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td></tr>";
            $returnStr .= "<tr><td>" . Language::labelSliderTextBoxBefore() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getTextBoxLabel(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= "<tr><td>" . Language::labelSliderTextBoxAfter() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getTextBoxPostText(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSpinner() . '</td><td>' . $this->displaySpinner($variable->getSpinner(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralSpinnerType() . '</td><td>' . $this->displaySpinnerType($variable->getSpinnerType(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerUp() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_UP, $variable->getSpinnerUp()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerDown() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_DOWN, $variable->getSpinnerDown()) . $helpend . "</td></tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutManual() . '</td><td>' . $this->displayManual($variable->getTextboxManual(), true, $variable->getTyd()) . "</td></tr>";

            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedRandomizer() . "</td>";
            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedRandomizer(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultOrder() . '</i></span></div></td></tr>';
            $returnStr .= "<tr><td>" . Language::labelTypeEditDropdownOptgroup() . "</td>";
            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_DROPDOWN_OPTGROUP . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getComboboxOptGroup(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyNoOptGroups() . '</i></span></div></td></tr>';            
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if (inArray($answertype, array(ANSWER_TYPE_RANK))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutEnumeratedLabel() . "</td><td>" . $this->displayEnumeratedLabelRank(SETTING_ENUMERATED_LABEL, $variable->getEnumeratedLabel(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedRandomizer() . "</td>";
            $returnStr .= '<td><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedRandomizer(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultOrder() . '</i></span></div></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutEnumeratedTemplate() . "</td>";
            $returnStr .= "<td>" . $this->displayEnumeratedTemplate(SETTING_ENUMERATED_ORIENTATION, $variable->getEnumeratedDisplay(), true, $variable->getTyd()) . "</td><td width=25><nobr/>";

            $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
            $current = $variable->getEnumeratedDisplay();
            $defaultsurvey = $survey->getEnumeratedDisplay();
            $defaulttype = -1;
            if ($variable->getTyd() > 0) {
                $type = $survey->getType($variable->getTyd());
                $defaulttype = $type->getEnumeratedDisplay();
            }
            $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;

            $s = "";
            if ($current == ORIENTATION_CUSTOM) {
                $s = "style='display: none;'";
            }
            $returnStr .= "<td id='custom1' $s>" . Language::labelTypeEditEnumeratedOrder() . "</td>";
            $returnStr .= "<td id='custom2' $s>" . $this->displayEnumeratedOrder(SETTING_ENUMERATED_ORDER, $variable->getEnumeratedOrder(), true, $variable->getTyd()) . "</td></tr>";

            if ($current == ORIENTATION_HORIZONTAL) {
                $returnStr .= "<tr id='horizontal1'><td>" . Language::labelTypeEditEnumeratedSplit() . "</td>";
            } else {
                $returnStr .= "<tr id='horizontal1' style='display: none;'><td>" . Language::labelTypeEditEnumeratedSplit() . "</td>";
            }

            $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_SPLIT, $variable->getEnumeratedSplit(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>
                            <td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
            $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, $variable->getHeaderAlignment(), true, $variable->getTyd()) . "</td></tr>";

            if ($current == ORIENTATION_HORIZONTAL) {
                $returnStr .= "<tr id='horizontal2'><td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
            } else {
                $returnStr .= "<tr id='horizontal2' style='display: none;'><td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
            }

            $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, $variable->getHeaderFormatting(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>";

            $returnStr .= "<td>" . Language::labelGroupEditBordered() . "</td>";
            $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_BORDERED, $variable->getEnumeratedBordered(), true, $variable->getTyd()) . "</td></tr>";

            if ($current == ORIENTATION_HORIZONTAL) {
                $returnStr .= "<tr id='horizontal5'><td>" . Language::labelTypeEditMobile() . "</td>";
            } else {
                $returnStr .= "<tr id='horizontal5' style='display: none;'><td>" . Language::labelTypeEditMobile() . "</td>";
            }

            $returnStr .= '<td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE, $variable->getTableMobile(), true, true) . '</td><td width=25><nobr/></td>';
            $returnStr .= '<td>' . Language::labelTypeEditMobileLabels() . '</td><td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE_LABELS, $variable->getTableMobileLabels(), true, true) . '</td>';
            $returnStr .= "</tr>";

            if ($current == ORIENTATION_CUSTOM) {
                $returnStr .= "<tr id=customtemplate><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
            } else {
                $returnStr .= "<tr id=customtemplate style='display: none;'><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
            }
            $returnStr .= '<td colspan=4>' . $helpstart . '<textarea style="width: 500px;" rows=5 class="form-control autocomplete" name="' . SETTING_ENUMERATED_CUSTOM . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedCustom(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= "</tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
            $returnStr .= "<td>" . $this->displayEnumeratedTextBox(SETTING_ENUMERATED_TEXTBOX, $variable->getEnumeratedTextBox(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>";
            $returnStr .= "<td>" . Language::labelTypeEditLayoutEnumeratedLabel() . "</td><td>" . $this->displayEnumeratedLabel(SETTING_ENUMERATED_LABEL, $variable->getEnumeratedLabel(), true, $variable->getTyd()) . "</td>";
            $returnStr .= '<tr id="horizontal6"><td>' . Language::labelTypeEditLayoutClickLabel() . '</td>';
            $returnStr .= "<td>" . $this->displayClickLabel(SETTING_ENUMERATED_CLICK_LABEL, $variable->getEnumeratedClickLabel(), true, $variable->getTyd()) . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelEnumeratedTextBoxBefore() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedTextBoxLabel(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>
                           <tr><td>' . Language::labelEnumeratedTextBoxAfter() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_TEXTBOX_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedTextBoxPostText(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= "</tr>";


            if ($current == ORIENTATION_VERTICAL) {
                $returnStr .= "<tr id='columns'><td>" . Language::labelTypeEditEnumeratedColumns() . "</td>";
            } else {
                $returnStr .= "<tr id='columns' style='display: none;'><td>" . Language::labelTypeEditEnumeratedColumns() . "</td>";
            }

            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyColumnsPlaceholder() . '" type="text" class="form-control" name="' . SETTING_ENUMERATED_COLUMNS . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedColumns(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyColumns() . '</i></span></div></td></tr>';

            $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedRandomizer() . "</td>";
            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEnumeratedRandomizer(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultOrder() . '</i></span></div></td></tr>';


            $returnStr .= '</table>';

            $returnStr .= "<script type='text/javascript'>";
            $returnStr .= '$( document ).ready(function() {
                                                    $("#' . SETTING_ENUMERATED_ORIENTATION . '").change(function (e) {
                                                        if (this.value == 3 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . '==3) || (this.value=="settingfollowtype" && ' . $defaulttype . '==3)) {
                                                            $("#customtemplate").show();
                                                            $("#horizontal1").hide();
                                                            $("#horizontal2").hide();
                                                            $("#horizontal5").hide();
                                                            $("#horizontal6").hide();
                                                            $("#custom1").hide();
                                                            $("#custom2").hide();
                                                            $("#columns").hide();
                                                        }  
                                                        else if (this.value == 1 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . '==1) || (this.value=="settingfollowtype" && ' . $defaulttype . '==3)) {
                                                            $("#horizontal1").show();
                                                            $("#horizontal2").show();
                                                            $("#horizontal5").show();
                                                            $("#horizontal6").show();
                                                            $("#custom1").show();
                                                            $("#custom2").show();
                                                            $("#customtemplate").hide();
                                                            $("#columns").hide();
                                                        }
                                                        else if (this.value == 2 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . '==2) || (this.value=="settingfollowtype" && ' . $defaulttype . '==3)) {
                                                            $("#customtemplate").hide();
                                                            $("#horizontal1").hide();
                                                            $("#horizontal2").hide();
                                                            $("#horizontal5").hide();
                                                            $("#horizontal6").hide();
                                                            $("#custom1").show();
                                                            $("#custom2").show();
                                                            $("#columns").show();
                                                        }
                                                        else {                                                        
                                                            $("#horizontal1").hide();
                                                            $("#horizontal2").hide();
                                                            $("#horizontal5").hide();
                                                            $("#horizontal6").hide();
                                                            $("#custom1").hide();
                                                            $("#custom2").hide();
                                                            $("#customtemplate").hide();
                                                            $("#columns").hide();
                                                        }
                                                    });
                                                    })';
            $returnStr .= "</script>";

            $returnStr .= '</div>';
        }

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutError() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutErrorPlacement() . "</td>";
        $returnStr .= "<td>" . $this->displayErrorPlacement(SETTING_ERROR_PLACEMENT, $variable->getErrorPlacement(), true, $variable->getTyd()) . "</td><td width=25><nobr/>";
        $returnStr .= "</tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutButtons() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayColorPicker();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td>";

        $returnStr .= "<td>" . $this->displayButton(SETTING_BACK_BUTTON, $variable->getShowBackButton(), true, $variable->getTyd()) . "</td>
                      <td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $variable->getLabelBackButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NEXT_BUTTON, $variable->getShowNextButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $variable->getLabelNextButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_DK_BUTTON, $variable->getShowDKButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $variable->getLabelDKButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_RF_BUTTON, $variable->getShowRFButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $variable->getLabelRFButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NA_BUTTON, $variable->getShowNAButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $variable->getLabelNAButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_UPDATE_BUTTON, $variable->getShowUpdateButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $variable->getLabelUpdateButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_BUTTON, $variable->getShowRemarkButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $variable->getLabelRemarkButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_SAVE_BUTTON, $variable->getShowRemarkSaveButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $variable->getLabelRemarkSaveButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_CLOSE_BUTTON, $variable->getShowCloseButton(), true, $variable->getTyd()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $variable->getLabelCloseButton()) . $helpend . "</td></tr>";

        $returnStr .= '</table></div>';
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutProgressBar() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarShow() . "</td>";
        $returnStr .= "<td>" . $this->displayProgressbar(SETTING_PROGRESSBAR_SHOW, $variable->getShowProgressBar(), true, $variable->getTyd()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarFillColor() . "</td>";
        $returnStr .= '<td><div class="input-group colorpicker">
          <input name="' . SETTING_PROGRESSBAR_FILLED_COLOR . '" type="text" value="' . $this->displayTextSettingValue($variable->getProgressBarFillColor()) . '" class="form-control" />
          <span class="input-group-addon"><i></i></span> (<i>' . $message . '</i>)
          </div></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutProgressBarWidth() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_PROGRESSBAR_WIDTH . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getProgressBarWidth(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutProgressBarValue() . '</td><td><div class="input-group"><input type="text" class="form-control autocompletebasic" name="' . SETTING_PROGRESSBAR_VALUE . '" value="' . convertHTLMEntities($variable->getProgressBarValue(), ENT_QUOTES) . '"><span class="input-group-addon">' . Language::helpProgressBarValue() . '</span></div></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSpinner() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralSpinner() . '</td><td>' . $this->displaySpinner($variable->getSpinner(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralSpinnerType() . '</td><td>' . $this->displaySpinnerType($variable->getSpinnerType(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerUp() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_UP, $variable->getSpinnerUp()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerDown() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_DOWN, $variable->getSpinnerDown()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerStep() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerStep(SETTING_SPINNER_STEP, $variable->getSpinnerIncrement()) . $helpend . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutManual() . '</td><td>' . $this->displayManual($variable->getTextboxManual(), true, $variable->getTyd()) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        if (Config::xiExtension()) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutXi() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralGroupXiTemplate() . '</td><td>
                            <select class="selectpicker show-tick" id="' . SETTING_GROUP_XI_TEMPLATE . '" name="' . SETTING_GROUP_XI_TEMPLATE . '">';
            $current = $variable->getXiTemplate();
            $entry = SETTING_FOLLOW_TYPE;
            $selected = "";
            if (strtoupper($entry) == strtoupper($current)) {
                $selected = "SELECTED";
            }
            if ($variable->hasType()) {
                $returnStr .= "<option $selected value='" . $entry . "'>" . Language::optionsFollowType() . "</option>";
            }

            if (file_exists(getBase() . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "xitemplates.php")) {
                $xitemplates = file_get_contents(getBase() . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "xitemplates.php", "r");
                ;
                $list = explode(");", $xitemplates);
                foreach ($list as $l) {
                    if (contains($l, " new Template")) {
                        $sub = explode("=", $l);
                        $selected = "";
                        $entry = trim(str_replace("\$", "", $sub[0]));
                        if (strtoupper($entry) == strtoupper($current)) {
                            $selected = "SELECTED";
                        }
                        $returnStr .= "<option $selected value='" . $entry . "'>" . $entry . "</option>";
                    }
                }
            }
            $returnStr .= '</select>    
                            </td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showEditVariableVerification($variable) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariablevalidationres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->getVariableTopTab(1);
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfEmpty() . "</td>";
        $returnStr .= "<td>" . $this->displayIfEmpty(SETTING_IFEMPTY, $variable->getIfEmpty(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfError() . "</td>";
        $returnStr .= "<td>" . $this->displayIfError(SETTING_IFERROR, $variable->getIfError(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= '</table></div>';

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());

            $message2 = Language::helpFollowTypeOnly($type->getName());
            $helpend2 = '<span class="input-group-addon"><i>' . $message2 . Language::helpComparison() . '</i></span></div>';
            $helpformat = '<span class="input-group-addon"><i>' . $message2 . Language::helpInvalidSet() . '</i></span></div>';
            ;
        } else {
            if ($variable->getTyd() > 0) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
                $message2 = Language::helpFollowTypeOnly($type->getName());
                $helpend2 = '<span class="input-group-addon"><i>' . $message2 . Language::helpComparison() . '</i></span></div>';
                $helpformat = '<span class="input-group-addon"><i>' . $message2 . Language::helpInvalidSet() . '</i></span></div>';
                ;
            } else {
                $helpend2 = '<span class="input-group-addon"><i>' . trim(Language::helpComparison()) . '</i></span></div>';
                $helpformat = '<span class="input-group-addon"><i>' . trim(Language::helpInvalidSet()) . '</i></span></div>';
                ;
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_RANK, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_OPEN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB, ANSWER_TYPE_CALENDAR))) {

            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationCriteria() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            switch ($answertype) {
                case ANSWER_TYPE_ENUMERATED:
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayExclusive(SETTING_INLINE_EXCLUSIVE, $variable->getInlineExclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditInclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayInclusive(SETTING_INLINE_INCLUSIVE, $variable->getInlineInclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMinRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MINIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($variable->getInlineMinimumRequired()) . '" class="form-control" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMaxRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MAXIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($variable->getInlineMaximumRequired()) . '" class="form-control" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExactRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_EXACT_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($variable->getInlineExactRequired()) . '" class="form-control" />' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayExclusive(SETTING_INLINE_EXCLUSIVE, $variable->getInlineExclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditInclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayInclusive(SETTING_INLINE_INCLUSIVE, $variable->getInlineInclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMinRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MINIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($variable->getInlineMinimumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMaxRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MAXIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($variable->getInlineMaximumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExactRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_EXACT_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($variable->getInlineExactRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                /* fall through */
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumSelected() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimumSelected()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumSelected() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximumSelected()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextExactSelected() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_EXACT_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getExactSelected()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextInvalidSubSet() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_INVALIDSUB_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getInvalidSubSelected()) . "'>" . $helpformat . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextInvalidSet() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_INVALID_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getInvalidSelected()) . "'>" . $helpformat . "</td></tr>";
                    break;
                case ANSWER_TYPE_OPEN:
                /* fall through */
                case ANSWER_TYPE_STRING:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumLength() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_LENGTH . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimumLength()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumLength() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_LENGTH . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximumLength()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumWords() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_WORDS . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimumWords()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumWords() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_WORDS . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximumWords()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextPattern() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_PATTERN . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue(convertHTLMEntities($variable->getPattern(), ENT_QUOTES)) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_SLIDER:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_KNOB:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_RANGE:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    $helpend1 = '<span class="input-group-addon"><i>Comma separated list; ' . $message . '</i></span></div>';
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeOther() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_OTHER_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getOtherValues()) . "'>" . $helpend1 . "</td></tr>";
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditCalendarMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_CALENDAR . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximumDatesSelected()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_RANK:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumRanked() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANKED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMinimumRanked()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumRanked() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANKED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getMaximumRanked()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextExactRanked() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_EXACT_RANKED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getExactRanked()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_CUSTOM:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control' value='" . $this->displayTextSettingValue($variable->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control' value='" . $this->displayTextSettingValue($variable->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    $helpend1 = '<span class="input-group-addon"><i>Comma separated list; ' . $message . '</i></span></div>';
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeOther() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_OTHER_RANGE . "' type='text' class='form-control' value='" . $this->displayTextSettingValue($variable->getOtherValues()) . "'>" . $helpend1 . "</td></tr>";
                    break;
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        /* numerical comparisons */
        if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationComparison() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonNotEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_NOT_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonNotEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonGreaterOrEqualThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_GREATER_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonGreaterEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonGreaterThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_GREATER . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonGreater()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonLessOrEqualThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_SMALLER_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonSmallerEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonLessThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_SMALLER . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonSmaller()) . "'>" . $helpend2 . "</td></tr>";

            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }
        /* string comparisons */ if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationComparison() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonEqualToIgnoreCase() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonEqualToIgnoreCase()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonNotEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_NOT_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonNotEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonNotEqualToIgnoreCase() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getComparisonNotEqualToIgnoreCase()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        //if (inArray($answertype, array(ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_DATE, ANSWER_TYPE_TIME, ANSWER_TYPE_DATETIME))) {
        if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationMasking() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditTextInputMaskEnable() . "</td>";
            $returnStr .= "<td>" . $this->displayInputMaskEnabled($variable->getInputMaskEnabled(), true, $variable->getTyd()) . "</td></tr>";

            if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER))) {
                $returnStr .= "<tr id='row1'><td>" . Language::labelTypeEditTextInputMask() . "</td>";
                $returnStr .= "<td style='width: 150px; max-width: 150px;'>" . $this->displayInputMasks(SETTING_INPUT_MASK, $variable->getInputMask(), true, $variable->getTyd()) . "</td>";

                if ($variable->getInputMask() == INPUTMASK_CUSTOM) {
                    $returnStr .= "<td id='inputmaskcell'><input name='" . SETTING_INPUT_MASK_CUSTOM . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getInputMaskCustom()) . "'></td></tr>";
                } else {
                    $returnStr .= "<td id='inputmaskcell' style='display: none;'><input name='" . SETTING_INPUT_MASK_CUSTOM . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue(convertHTLMEntities($variable->getInputMaskCustom(), ENT_QUOTES)) . "'></td></tr>";
                }

                $returnStr .= "<tr id='row2'><td>" . Language::labelTypeEditTextInputMaskPlaceholder() . "</td>";
                $returnStr .= "<td><input name='" . SETTING_INPUT_MASK_PLACEHOLDER . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($variable->getInputMaskPlaceholder()) . "'></td></tr>";

                $returnStr .= '<tr><td>' . Language::labelTypeEditValidationCallback() . '</td><td>' . $helpstart . '<textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_INPUT_MASK_CALLBACK . '" name="' . SETTING_INPUT_MASK_CALLBACK . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getInputMaskCallback(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';

                $user = new User($_SESSION['URID']);
                if ($user->hasRoutingAutoIndentation()) {
                    $returnStr .= $this->getCodeMirror('height: 100px; width: 500px;');
                    $returnStr .= '<script src="js/codemirror/mode/javascript/javascript.js"></script>';
                    $returnStr .= '<script src="js/codemirror/mode/css/css.js"></script>';
                    $returnStr .= '<script type="text/javascript">                                
                                $(document).ready(function() {
                                   var editor = CodeMirror.fromTextArea(document.getElementById("' . SETTING_INPUT_MASK_CALLBACK . '"), {mode: "text/javascript", lineNumbers: false});
                                });
                           </script>';
                }

                $returnStr .= '<script type="text/javascript">
                                $( document ).ready(function() {
                                                    $("#' . SETTING_INPUT_MASK . '").change(function (e) {
                                                        if (this.value == "' . INPUTMASK_CUSTOM . '") {
                                                            $("#inputmaskcell").show();
                                                        }   
                                                        else {
                                                            $("#inputmaskcell").hide();
                                                        }
                                                    });
                                                    })';
                $returnStr .= '</script>';
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationAssignment() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelValidateAssignment() . "</td>";
        $returnStr .= "<td>" . $this->displayValidateAssignment(SETTING_VALIDATE_ASSIGNMENT, $variable->getValidateAssignment()) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showEditVariableAssistance($variable) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariableassistanceres', 'vsid' => $variable->getVsid()));
        //$returnStr .= '<span class="label label-default">' . Language::labelTypeEditAssistanceTexts() . '</span>';
        $returnStr .= $this->getVariableTopTab(3);

        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';

        $helpstart = '<div class="input-group">';
        $helpstart2 = "";
        $helpend2 = "";
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());
            $helpstart2 = '<div class="input-group">';
            $message2 = Language::helpFollowTypeOnly($type->getName());
            $helpend2 = '<span class="input-group-addon"><i>' . $message2 . '</i></span></div>';
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

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }

        if (inArray($answertype, array(ANSWER_TYPE_STRING, ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePreText() . '</td><td>' . $helpstart2 . '<input style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_PRETEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPreText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePostText() . '</td><td>' . $helpstart2 . '<input style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPostText(), ENT_QUOTES)) . '">' . $helpend2 . '</td></tr>';
        }
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceHoverText() . '</td><td>' . $helpstart2 . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_HOVERTEXT . '" name="' . SETTING_HOVERTEXT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getHoverText(), ENT_QUOTES)) . '</textarea>' . $helpend2 . '</td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditAssistanceMessages() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_EMPTY_MESSAGE . '" name="' . SETTING_EMPTY_MESSAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getEmptyMessage()), ENT_QUOTES) . '</textarea>' . $helpend . '</td></tr>';

        switch ($answertype) {
            case ANSWER_TYPE_DOUBLE:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_DOUBLE . '" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageDouble(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_INTEGER:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INTEGER . '" name="' . SETTING_ERROR_MESSAGE_INTEGER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInteger(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_PATTERN . '" name="' . SETTING_ERROR_MESSAGE_PATTERN . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessagePattern(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMinimumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMinimumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_ENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEnumeratedEntered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '" name="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageEnumeratedEntered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_RANK:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinRank() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_RANK . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_RANK . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageRankMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxRank() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_RANK . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_RANK . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageRankMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactRank() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_RANK . '" name="' . SETTING_ERROR_MESSAGE_EXACT_RANK . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageRankExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSetOfEnumeratedEntered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED . '" name="' . SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSetOfEnumeratedEntered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectInvalidSubset(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageSelectInvalidSet(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_RANGE:
            /* fall through */
            case ANSWER_TYPE_KNOB:
            /* fall through */
            case ANSWER_TYPE_SLIDER:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_RANGE . '" name="' . SETTING_ERROR_MESSAGE_RANGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageRange(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_CALENDAR:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageMaximumCalendar(), ENT_QUOTES)) . '</textarea></td></tr>';
                break;
        }

        if (inArray($answertype, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonNotEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreaterEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonGreaterEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreater() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonGreater(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmallerEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonSmallerEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmaller() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonSmaller(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        }
        /* string comparisons */ else if (inArray($answertype, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualToIgnoreCase() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonEqualToIgnoreCase(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonNotEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualToIgnoreCase() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getErrorMessageComparisonNotEqualToIgnoreCase(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        }

        $returnStr .= '</table></div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditVariableOutput($var) {
        $returnStr = '<form id="editform" method="post">';
        if ($var->getVsid() != "") {
            $returnStr .= $this->getVariableTopTab(5);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $survey = new Survey($_SESSION['SUID']);
        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());
        } else {
            if ($var->getTyd() > 0) {
                $type = $survey->getType($var->getTyd());
                $message = Language::helpFollowType($type->getName());
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariableoutputres', 'vsid' => $var->getVsid()));
        $returnStr .= '<table>';
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHidden() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN, $var->getHidden(), true, $var->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenPaperVersion() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_PAPER_VERSION, $var->getHiddenPaperVersion(), true, $var->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenRouting() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_ROUTING, $var->getHiddenRouting(), true, $var->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenTranslation() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_TRANSLATION, $var->getHiddenTranslation(), true, $var->getTyd()) . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelDataStorage() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        // not a core variable, allow external location
        if (!inArray($var->getName(), Common::surveyCoreVariables())) {
            $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
            $current = $var->getStoreLocation();
            $defaultsurvey = $survey->getStoreLocation();
            $defaulttype = -1;
            if ($var->getTyd() > 0) {
                $type = $survey->getType($var->getTyd());
                $defaulttype = $type->getStoreLocation();
            }

            $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
            $hide = "";

            if ($current == 1) {
                $hide = "style='display: none;'";
            }
            $returnStr .= "<tr><td>" . Language::labelTypeEditOutputLocationStore() . "</td>";
            $returnStr .= "<td>" . $this->displayStoreLocation(SETTING_DATA_STORE_LOCATION, $var->getStoreLocation(), true, $var->getTyd()) . "</td><td width=25><nobr/></td>";
            $returnStr .= "<td " . $hide . " id='store1'>" . Language::labelVariableExternal() . "</td><td " . $hide . " id='store2'>" . $helpstart . "<input type=text class='form-control' name='" . SETTING_DATA_STORE_LOCATION_EXTERNAL . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($var->getStoreLocationExternal(), ENT_QUOTES)) . "'>" . $helpend . "</td></td>";
            $returnStr .= "</tr>";

            $returnStr .= "<script type='text/javascript'>";
            $returnStr .= '$( document ).ready(function() {
                                                        $("#' . SETTING_DATA_STORE_LOCATION . '").change(function (e) {
                                                            if (this.value > 1 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . ' > 1) || (this.value=="settingfollowtype" && ' . $defaulttype . ' > 1)) {
                                                                $("#store1").show();
                                                                $("#store2").show();
                                                            }  
                                                            else {                                                        
                                                                $("#store1").hide();
                                                                $("#store2").hide();
                                                            }
                                                        });
                                                        })';
            $returnStr .= "</script>";
        }
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputInputMask() . "</td>";
        $returnStr .= "<td>" . $this->displayDataInputMask(SETTING_DATA_INPUTMASK, $var->getDataInputMask(), true, $var->getTyd()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputScreendumps() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_SCREENDUMPS, $var->getScreendumpStorage(), true, $var->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputParadata() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_PARADATA, $var->getParadata(), true, $var->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelDataKeep() . "</td>";
        $returnStr .= "<td>" . $this->displayDataKeep(SETTING_DATA_KEEP, $var->getDataKeep(), true, $var->getTyd()) . "</td></tr>";

        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }
        $returnStr .= '</table></div>';

        // formatting
        $returnStr .= '<span class="label label-default">' . Language::labelDataFormat() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table>';
        $returnStr .= "<tr><td>" . Language::labelSkipVariable() . "</td>";
        $returnStr .= "<td>" . $this->displayDataSkip(SETTING_DATA_SKIP, $var->getDataSkipVariable(), true, $var->getTyd()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelSkipVariablePostFix() . "</td><td>" . $helpstart . "<input type=text class='form-control' name='" . SETTING_DATA_SKIP_POSTFIX . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($var->getDataSkipVariablePostFix(), ENT_QUOTES)) . "'>" . $helpend . "</td></td>";
        $returnStr .= "</tr>";


        /* extra needed */
        $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK);
        if (inArray($answertype, $array)) {
            $returnStr .= '<tr id="categories"><td valign=top>' . Language::labelTypeEditOutputCategories() . '</td><td colspan=4><textarea style="height: 120px;" class="form-control uscic-form-control-admin tinymce" name="' . SETTING_OUTPUT_OPTIONS . '">' . $this->displayTextSettingValue(convertHTLMEntities($var->getOutputOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';

            if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED))) {
                $returnStr .= "<tr><td>" . Language::labelTypeEditOutputSetOfEnumerated() . "</td>";
                $returnStr .= "<td>" . $this->displaySetOfEnumeratedOutput(SETTING_OUTPUT_SETOFENUMERATED, $var->getOutputSetOfEnumeratedBinary(), true, $var->getTyd()) . "</td></tr>";
            }
            $returnStr .= "<tr><td>" . Language::labelTypeEditOutputValueLabelWidth() . "</td>";
            $returnStr .= "<td>" . $this->displayValueLabelWidth(SETTING_OUTPUT_VALUELABEL_WIDTH, $var->getOutputValueLabelWidth(), true, $var->getTyd()) . "</td></tr>";
        }
        $returnStr .= '</table></div>';


        if ($var->getVsid() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditVariableFill($variable) {
        $returnStr .= '<form id="hiddenform" name="hiddenform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.clickrouting'));
        $returnStr .= "<input type=hidden name=action id=action />";
        $returnStr .= '</form>';
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariablefillres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->getVariableTopTab(4);

        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditFillText() . '</td><td><textarea  id="filltext" style="width: 100%;" rows=6 class="form-control autocompletebasic" name="filltext">' . convertHTLMEntities($variable->getFillText(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditFillCode() . '</td><td>';
        $returnStr .= '<textarea style="width: 100%; height: 200px;" id="fillcode" name="fillcode">';
        $returnStr .= convertHTLMEntities($variable->getFillCode(), ENT_QUOTES);
        $returnStr .= '</textarea></td></tr>';
        $returnStr .= '</table></div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';

        $user = new User($_SESSION['URID']);
        if ($user->hasRoutingAutoIndentation()) {

            $returnStr .= $this->getCodeMirror();
            $returnStr .= '<script src="js/codemirror/mode/nubis/nubis.js"></script>';
            $returnStr .= '<link rel="stylesheet" href="js/codemirror/addon/hint/show-hint.css"/>
                <script src="js/codemirror/addon/hint/show-hint.js"></script>
                <script src="js/codemirror/addon/hint/nubis-hint.js"></script>';
            $returnStr .= '<script type="text/javascript">
                
                            $(document).ready(function() {
                
                                CodeMirror.commands.autocomplete = function(cm) {
                                    cm.showHint({hint: CodeMirror.hint.nubis});
                                  }
                                var editor = CodeMirror.fromTextArea(document.getElementById("filltext"), {mode: "text/x-plain", lineNumbers: true});
                                var editor2 = CodeMirror.fromTextArea(document.getElementById("fillcode"), {mode: "text/x-nubis", lineNumbers: true, extraKeys: {"Ctrl-Space": "autocomplete"}});
                                function words(str) {
                                    var obj = [], words = str.split(" ");
                                    for (var i = 0; i < words.length; ++i) {
                                        obj[i] = words[i];
                                    }
                                    return obj;
                                }
                                var keywords = words("cardinal card group subgroup endgroup endsubgroup empty nonresponse dk rf do enddo endif for and array if then elseif else in mod not or to inline inspect fill");
                                var mirrorchanged = false;
                                editor2.on("dblclick", function(cm, event) {
                                   if (event.ctrlKey) {                          
                                      if (mirrorchanged == false) {
                                          $("#editform").dirtyForms("setClean");                          
                                      }
                                      var sel = cm.getSelection().toLowerCase();
                                      if ($.inArray(sel, keywords) == -1) { 
                                        $("#action").val(sel);
                                        $("#hiddenform").submit();                                        
                                      }
                                   }
                                });

                                editor2.on("change", function(from, to, text, removed, origin) {
                                    $("#routing").dirtyForms("setDirty");
                                    mirrorchanged = true;
                                });

                                function format() {
                                    var totalLines = editor2.lineCount();  
                                    editor2.autoFormatRange({line:0, ch:0}, {line:totalLines});
                                }                    
                            });

                            </script>';
        }
        return $returnStr;
    }

    function showEditVariableCheck($variable) {
        $returnStr .= '<form id="hiddenform" name="hiddenform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.clickrouting'));
        $returnStr .= "<input type=hidden name=action id=action />";
        $returnStr .= '</form>';
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariablecheckres', 'vsid' => $variable->getVsid()));
        $returnStr .= $this->getVariableTopTab(9);

        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditCheckText() . '</td><td><textarea  id="checktext" style="width: 100%;" rows=6 class="form-control autocompletebasic" name="checktext">' . convertHTLMEntities($variable->getCheckText(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditCheckCode() . '</td><td>';
        $returnStr .= '<textarea style="width: 100%; height: 200px;" id="checkcode" name="checkcode">';
        $returnStr .= convertHTLMEntities($variable->getCheckCode(), ENT_QUOTES);
        $returnStr .= '</textarea></td></tr>';
        $returnStr .= '</table></div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';

        $user = new User($_SESSION['URID']);
        if ($user->hasRoutingAutoIndentation()) {

            $returnStr .= $this->getCodeMirror();
            $returnStr .= '<script src="js/codemirror/mode/nubis/nubis.js"></script>';
            //$returnStr .= '<script type="text/javascript"></script>';
            $returnStr .= '<script type="text/javascript">
                
                            $(document).ready(function() {
                
                                var editor = CodeMirror.fromTextArea(document.getElementById("checktext"), {mode: "text/x-plain", lineNumbers: true});
                                var editor2 = CodeMirror.fromTextArea(document.getElementById("checkcode"), {mode: "text/x-nubis", lineNumbers: true});
                                function words(str) {
                                    var obj = [], words = str.split(" ");
                                    for (var i = 0; i < words.length; ++i) {
                                        obj[i] = words[i];
                                    }
                                    return obj;
                                }
                                var keywords = words("cardinal card group subgroup endgroup endsubgroup empty nonresponse dk rf do enddo endif for and array if then elseif else in mod not or to inline inspect fill");
                                var mirrorchanged = false;
                                editor2.on("dblclick", function(cm, event) {
                                   if (event.ctrlKey) {                          
                                      if (mirrorchanged == false) {
                                          $("#editform").dirtyForms("setClean");                          
                                      }
                                      var sel = cm.getSelection().toLowerCase();
                                      if ($.inArray(sel, keywords) == -1) { 
                                        $("#action").val(sel);
                                        $("#hiddenform").submit();                                        
                                      }
                                   }
                                });

                                editor2.on("change", function(from, to, text, removed, origin) {
                                    $("#routing").dirtyForms("setDirty");
                                    mirrorchanged = true;
                                });

                                function format() {
                                    var totalLines = editor2.lineCount();  
                                    editor2.autoFormatRange({line:0, ch:0}, {line:totalLines});
                                }                    
                            });

                            </script>';
        }
        return $returnStr;
    }

    function showEditVariableInteractive($variable) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariableinteractiveres', 'vsid' => $variable->getVsid()));

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());
        } else {
            if ($variable->getTyd() > 0) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';


        $returnStr .= $this->getVariableTopTab(6);
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveID() . '</td><td><div class="input-group"><input type="text" class="form-control autocompletebasic" name="' . SETTING_ID . '" value="' . convertHTLMEntities($variable->getID(), ENT_QUOTES) . '"><span class="input-group-addon"><i>Auto-generated if empty</i></span></div></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveInlineText() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '" name="' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getInlineJavascript(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditinteractivePageText() . '</td><td>' . $helpstart . '<textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_JAVASCRIPT_WITHIN_PAGE . '" name="' . SETTING_JAVASCRIPT_WITHIN_PAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPageJavascript(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveExtraJavascript() . '</td><td>' . $helpstart . '<textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_SCRIPTS . '" name="' . SETTING_SCRIPTS . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getScripts(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditInteractiveStyle() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveInlineStyle() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_STYLE_WITHIN_ELEMENT . '" name="' . SETTING_STYLE_WITHIN_ELEMENT . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getInlineStyle(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractivePageStyle() . '</td><td>' . $helpstart . '<textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_STYLE_WITHIN_PAGE . '" name="' . SETTING_STYLE_WITHIN_PAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($variable->getPageStyle(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '</table></div>';

        $user = new User($_SESSION['URID']);
        if ($user->hasRoutingAutoIndentation()) {
            $returnStr .= $this->getCodeMirror('height: 100px; width: 500px;');
            $returnStr .= '<script src="js/codemirror/mode/javascript/javascript.js"></script>';
            $returnStr .= '<script src="js/codemirror/mode/css/css.js"></script>';
            $returnStr .= '<script type="text/javascript">                                
                                $(document).ready(function() {
                                   var editor = CodeMirror.fromTextArea(document.getElementById("' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '"), {mode: "text/javascript", lineNumbers: false});
                                   var editor1 = CodeMirror.fromTextArea(document.getElementById("' . SETTING_JAVASCRIPT_WITHIN_PAGE . '"), {mode: "text/javascript", lineNumbers: true});
                                   var editor2 = CodeMirror.fromTextArea(document.getElementById("' . SETTING_STYLE_WITHIN_ELEMENT . '"), {mode: "text/x-javascript", lineNumbers: false});
                                   var editor3 = CodeMirror.fromTextArea(document.getElementById("' . SETTING_STYLE_WITHIN_PAGE . '"), {mode: "text/x-scss", lineNumbers: true});
                                });
                           </script>';
        }
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnSubmit() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_BACK, $variable->getOnBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_NEXT, $variable->getOnNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_DK, $variable->getOnDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_RF, $variable->getOnRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_NA, $variable->getOnNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_UPDATE, $variable->getOnUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_LANGUAGE_CHANGE, $variable->getOnLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_MODE_CHANGE, $variable->getOnModeChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnClick() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_BACK, $variable->getClickBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NEXT, $variable->getClickNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_DK, $variable->getClickDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_RF, $variable->getClickRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NA, $variable->getClickNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_UPDATE, $variable->getClickUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_LANGUAGE_CHANGE, $variable->getClickLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_MODE_CHANGE, $variable->getClickModeChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditVariableNavigation($variable) {
        $returnStr = '<form id="editform" method="post">';
        if ($variable->getVsid() != "") {
            $returnStr .= $this->getVariableTopTab(7);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());
        } else {
            if ($variable->getTyd() > 0) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariablenavigationres', 'vsid' => $variable->getVsid()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingEnabled() . "</td>";
        $returnStr .= "<td>" . $this->displayKeyBoardBindingDropdown(SETTING_KEYBOARD_BINDING_ENABLED, $variable->getKeyboardBindingEnabled(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingBack() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_BACK, $variable->getKeyboardBindingBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNext() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NEXT, $variable->getKeyboardBindingNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingDK() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_DK, $variable->getKeyboardBindingDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRF() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_RF, $variable->getKeyboardBindingRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNA() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NA, $variable->getKeyboardBindingNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingUpdate() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_UPDATE, $variable->getKeyboardBindingUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRemark() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_REMARK, $variable->getKeyboardBindingRemark()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingClose() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_CLOSE, $variable->getKeyboardBindingClose()) . $helpend . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNA() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA, $variable->getIndividualDKRFNA(), true, $variable->getTyd()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNAInline() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA_INLINE, $variable->getIndividualDKRFNAInline(), true, $variable->getTyd()) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditVariableAccess($variable) {
        $returnStr = '<form id="editform" method="post">';
        if ($variable->getVsid() != "") {
            $returnStr .= $this->getVariableTopTab(8);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $survey = new Survey($_SESSION['SUID']);
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
            $message = Language::helpFollowType($type->getName());
        } else {
            if ($variable->getTyd() > 0) {
                $survey = new Survey($_SESSION['SUID']);
                $type = $survey->getType($variable->getTyd());
                $message = Language::helpFollowType($type->getName());
            }
        }
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariableaccessres', 'vsid' => $variable->getVsid()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryAction(SETTING_ACCESS_REENTRY_ACTION, $variable->getAccessReentryAction(), true, $variable->getTyd()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentryPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryPreload(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $variable->getAccessReentryRedoPreload(), true, $variable->getTyd()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletion() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionReturn(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $variable->getAccessReturnAfterCompletionAction(), true, $variable->getTyd()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletionPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionPreload(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $variable->getAccessReturnAfterCompletionRedoPreload(), true, $variable->getTyd()) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showRemoveVariable($vsid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $variable = $survey->getVariableDescriptive($vsid);
        $returnStr = $this->showVariableHeader($survey, $section, $variable, Language::headerRemoveVariable(), $message);
        $returnStr .= $this->displayWarning(Language::messageRemoveVariable($variable->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.removevariableres', 'vsid' => $variable->getVsid()));
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showVariableFooter($survey);
        return $returnStr;
    }

    function showCopyVariable($vsid, $message = "") {

        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $variable = $survey->getVariableDescriptive($vsid);
        $returnStr = $this->showVariableHeader($survey, $section, $variable, Language::headerCopyVariable(), $message);
        $returnStr .= $this->displayWarning(Language::messageCopyVariable($variable->getName()));

        $surveys = new Surveys();
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= "<form name='repost' id='repost' method='post'>";
            $returnStr .= "<input type='hidden' name='news' id='news' value='' />";
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.movevariable', 'vsid' => $variable->getVsid()));
            $returnStr .= "</form>";
            $returnStr .= "<script type=text/javascript>";
            $returnStr .= "$(document).ready(function() {
                                $('#suid').change(function(e) {
                                    if (this.value != " . $variable->getSuid() . ") {
                                        $('#news').val(this.value);
                                        $('#repost').submit();
                                    }                                    
                                });
                                });
                                ";
            $returnStr .= "</script>";
        }

        if (loadvar("news") == "") {
            $seid = $variable->getSeid();
            $suid = $variable->getSuid();
        } else {
            $seid = "";
            $suid = loadvar("news");
        }

        $returnStr .= '<form method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.copyvariableres', 'vsid' => $variable->getVsid()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], '') . '</tr>';
        }

        $returnStr .= '<tr><td>' . Language::labelTypeCopySection() . '</td>';
        $returnStr .= "<td>" . $this->displaySections(SETTING_SECTION, $variable->getSeid(), $variable->getSuid(), "") . "</td></tr>";
        $returnStr .= '<tr><td>' . Language::labelTypeCopyNumber() . '</td>';
        $returnStr .= "<td><input style='width: 100px;' class='form-control' type=text name=numberofcopies value=1 /></td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeCopySuffix() . "</td>";

        $returnStr .= "<td><select class='selectpicker show-tick' name=includesuffix>";
        $returnStr .= "<option value=" . INPUT_MASK_YES . ">" . Language::optionsInputMaskYes() . "</option>";
        $returnStr .= "<option value=" . INPUT_MASK_NO . ">" . Language::optionsInputMaskNo() . "</option>";
        $returnStr .= "</select></td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showVariableFooter($survey);
        return $returnStr;
    }

    function showMoveVariable($vsid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $variable = $survey->getVariableDescriptive($vsid);
        $returnStr = $this->showVariableHeader($survey, $section, $variable, Language::headerMoveVariable(), $message);
        $returnStr .= $this->displayWarning(Language::messageMoveVariable($variable->getName()));

        $surveys = new Surveys();
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= "<form name='repost' id='repost' method='post'>";
            $returnStr .= "<input type='hidden' name='news' id='news' value='' />";
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.movevariable', 'vsid' => $variable->getVsid()));
            $returnStr .= "</form>";
            $returnStr .= "<script type=text/javascript>";
            $returnStr .= "$(document).ready(function() {
                                $('#suid').change(function(e) {
                                    if (this.value != " . $variable->getSuid() . ") {
                                        $('#news').val(this.value);
                                        $('#repost').submit();
                                    }                                    
                                });
                                });
                                ";
            $returnStr .= "</script>";
        }
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.movevariableres', 'vsid' => $variable->getVsid()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();

        if (loadvar("news") == "") {
            $seid = $variable->getSeid();
            $suid = $variable->getSuid();
        } else {
            $seid = "";
            $suid = loadvar("news");
        }
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $suid, '') . '</tr>';
        }

        $returnStr .= '<tr><td>' . Language::labelTypeMoveSection() . '</td>';
        $returnStr .= "<td>" . $this->displaySections(SETTING_SECTION, $seid, $suid, $seid) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showVariableFooter($survey);
        return $returnStr;
    }

    function showRefactorVariable($vsid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $variable = $survey->getVariableDescriptive($vsid);
        $returnStr = $this->showVariableHeader($survey, $section, $variable, Language::headerRefactorVariable(), $message);
        $returnStr .= $this->displayWarning(Language::messageRefactorVariable($variable->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.refactorvariableres', 'vsid' => $variable->getVsid()));
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelTypeRefactor() . '</td>';
        $returnStr .= "<td><input class='form-control' type=text name=" . SETTING_NAME . " /></td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRefactor() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showVariableFooter($survey);
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
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'sysadmin.survey.editvariable', 'vsid' => $previous->getVsid()), '<span class="glyphicon glyphicon-chevron-left"></span> ', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getVsid() != "" && $next->getVsid() != $var->getVsid()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'sysadmin.survey.editvariable', 'vsid' => $next->getVsid()), '<span class="glyphicon glyphicon-chevron-right"></span> ', 'title="' . $next->getName() . '"') . '</span>';
        }

        $returnStr .= '<center>' . $previoustext . '<span class="label label-default">' . $var->getName() . '</span>' . $nexttext . '</center>';
        $returnStr .= '<div class="well sidebar-nav">
            <ul class="nav">
              <li>';
        if ($var->getName() != "") {
            $returnStr .= $this->displayVariableSideBarFilter($survey, $filter);
        }
        $returnStr .= '</li></ul>';
        $returnStr .= '     </div></div>';
        return $returnStr;
    }

    function displayVariableSideBarFilter($survey, $filter = 0) {
        $active = array('', '', '', '', '', '', '', '', '', '', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<form method="post" id="variablesidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editvariable'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_variable" id="vrfiltermode_variable" value="' . $filter . '">';
        $returnStr .= '<div class="btn-group">';
        $variable = $survey->getVariableDescriptive($_SESSION['VSID']);
        $answertype = $variable->getAnswerType();
        $survey = new Survey($_SESSION['SUID']);
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
        }

        $returnStr .= '<div class="btn-group-sm">';
        $returnStr .= '<button title="' . Language::linkEditTooltip() . '" class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><span class="glyphicon glyphicon-edit"></span></button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';
        $returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_variable\').val(0);$(\'#variablesidebar\').submit();">' . Language::labelGeneral() . '</a></li>';
        $returnStr .= '<li><a ' . $active[8] . ' onclick="$(\'#vrfiltermode_variable\').val(8);$(\'#variablesidebar\').submit();">' . Language::labelAccess() . '</a></li>';

        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {

            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                $returnStr .= '<li><a ' . $active[1] . ' onclick="$(\'#vrfiltermode_variable\').val(1);$(\'#variablesidebar\').submit();">' . Language::labelVerification() . '</a></li>';
            }
            $returnStr .= '<li><a ' . $active[2] . ' onclick="$(\'#vrfiltermode_variable\').val(2);$(\'#variablesidebar\').submit();">' . Language::labelLayout() . '</a></li>';
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                $returnStr .= '<li><a ' . $active[3] . ' onclick="$(\'#vrfiltermode_variable\').val(3);$(\'#variablesidebar\').submit();">' . Language::labelAssistance() . '</a></li>';
                $returnStr .= '<li><a ' . $active[4] . ' onclick="$(\'#vrfiltermode_variable\').val(4);$(\'#variablesidebar\').submit();">' . Language::labelFill() . '</a></li>';
                if ($survey->isApplyChecks() == true) {
                    $returnStr .= '<li><a ' . $active[9] . ' onclick="$(\'#vrfiltermode_variable\').val(9);$(\'#variablesidebar\').submit();">' . Language::labelApplyChecks() . '</a></li>';
                }
                $returnStr .= '<li><a ' . $active[6] . ' onclick="$(\'#vrfiltermode_variable\').val(6);$(\'#variablesidebar\').submit();">' . Language::labelInteractive() . '</a></li>';
            }
            $returnStr .= '<li><a ' . $active[5] . ' onclick="$(\'#vrfiltermode_variable\').val(5);$(\'#variablesidebar\').submit();">' . Language::labelOutput() . '</a></li>';
        }
        $returnStr .= '<li><a ' . $active[7] . ' onclick="$(\'#vrfiltermode_variable\').val(7);$(\'#variablesidebar\').submit();">' . Language::labelNavigation() . '</a></li>';
        $returnStr .= '</ul>';


        $tagclass = 'class="btn btn-default"';
        if (isset($_COOKIE['uscicvariablecookie'])) {
            $cookievalue = $_COOKIE['uscicvariablecookie'];
            if (inArray($variable->getSuid() . "~" . $variable->getVsid(), explode("-", $cookievalue))) {
                $tagclass = 'class="btn btn-default uscic-cookie-tag-active"';
            }
        }
        $returnStr .= '<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscicvariablecookie\',\'' . $variable->getSuid() . "~" . $variable->getVsid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a>';

        if (!inArray($variable->getName(), Common::surveyCoreVariables())) {
            $returnStr .= '<a title="' . Language::linkRefactorTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.refactorvariable', 'vsid' => $variable->getVsid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-registration-mark"></span></a>';
        }
        $returnStr .= '<a title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copyvariable', 'vsid' => $variable->getVsid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-copyright-mark"></span></a>';

        if (!inArray($variable->getName(), Common::surveyCoreVariables())) {

            $surveys = new Surveys();
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<a title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movevariable', 'vsid' => $variable->getVsid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-move"></span></a>';
            }
            $returnStr .= '<a title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removevariable', 'vsid' => $variable->getVsid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>';
        }
        $returnStr .= '</div>';


        $returnStr .= '</div>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* TYPES */

    function showTypeHeader($survey, $type, $actiontype, $message = "") {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey', 'vrfiltermode_survey' => '2'), Language::headerTypes()) . '</li>';
        if ($type->getTyd() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.edittype', 'tyd' => $type->getTyd()), $type->getName()) . '</li>';
        }

        if ($type->getTyd() != "") {
            if ($_SESSION['VRFILTERMODE_TYPE'] == 0) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeGeneral() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 1) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeVerification() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 2) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeLayout() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 3) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeAssistance() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 4) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeOutput() . '</li>';
            } else {
                $returnStr .= '<li class="active">' . Language::headerEditTypeInteractive() . '</li>';
            }
        } else {
            $returnStr .= '<li class="active">' . Language::headerAddType() . '</li>';
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

    function showTypes($types) {
        $returnStr = '';
        if (sizeof($types) > 0) {
            $user = new User($_SESSION['URID']);
            $returnStr = $this->displayDataTablesScripts(array("colvis"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#typetable').dataTable(
                                {
                                    \"iDisplayLength\": " . $user->getItemsInTable() . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    colVis: {
                                        activate: \"mouseover\",
                                        exclude: [ 0, 1 ]
                                    }
                                    }    
                                );
                                         
                       });</script>

                        "; //

            $returnStr .= $this->displayPopoverScript();
            $returnStr .= $this->displayCookieScripts();
            $returnStr .= '<table id="typetable" class="table table-striped table-bordered table-condensed table-hover">';
            $returnStr .= '<thead><tr><th></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralCategories() . '</th></tr></thead><tbody>';

            foreach ($types as $type) {
                $returnStr .= '<tr><td>';
                $content = '<a id="' . $type->getTyd() . '_edit" title="' . Language::linkEditTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.edittype', 'tyd' => $type->getTyd())) . '"><span class="glyphicon glyphicon-edit"></span></a>';
                $content .= '&nbsp;&nbsp;<a id="' . $type->getTyd() . '_copy" title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copytype', 'tyd' => $type->getTyd())) . '"><span class="glyphicon glyphicon-copyright-mark"></span></a>';

                $tagclass = 'class=""';
                if (isset($_COOKIE['uscictypecookie'])) {
                    $cookievalue = $_COOKIE['uscictypecookie'];
                    if (inArray($type->getSuid() . "~" . $type->getTyd(), explode("-", $cookievalue))) {
                        $tagclass = 'class="uscic-cookie-tag-active"';
                    }
                }

                $content .= '&nbsp;&nbsp;<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscictypecookie\',\'' . $type->getSuid() . "~" . $type->getTyd() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href=""><span class="glyphicon glyphicon-tag"></span></a>';

                $surveys = new Surveys();
                if ($surveys->getNumberOfSurveys() > 1) {
                    $content .= '&nbsp;&nbsp;<a id="' . $type->getTyd() . '_move" title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movetype', 'tyd' => $type->getTyd())) . '"><span class="glyphicon glyphicon-move"></span></a>';
                }
                $content .= '&nbsp;&nbsp;<a id="' . $type->getTyd() . '_remove" title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removetype', 'tyd' => $type->getTyd())) . '"><span class="glyphicon glyphicon-remove"></span></a>';
                $returnStr .= '<a rel="popover" id="' . $type->getTyd() . '_popover" data-placement="right" data-html="true" data-toggle="popover" data-trigger="hover" href="' . setSessionParams(array('page' => 'sysadmin.survey.edittype', 'tyd' => $type->getTyd())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= '</td><td>' . $type->getName() . '</td><td>' . $type->getOptionsText() . '</td></tr>';
                $returnStr .= $this->displayPopover("#" . $type->getTyd() . '_popover', $content);
            }
            $returnStr .= '</tbody></table>';
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoTypesYet());
        }
        $returnStr .= '<a href="' . setSessionParams(array('page' => 'sysadmin.survey.addtype')) . '">' . Language::labelTypesAddNew() . '</a>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
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
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'sysadmin.survey.edittype', 'tyd' => $previous->getTyd()), '<span class="glyphicon glyphicon-chevron-left"></span>', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getTyd() != "" && $next->getTyd() != $type->getTyd()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'sysadmin.survey.edittype', 'tyd' => $next->getTyd()), '<span class="glyphicon glyphicon-chevron-right"></span>', 'title="' . $next->getName() . '"') . '</span>';
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
        $active = array('', '', '', '', '', '', '', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<form method="post" id="typesidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittype'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_type" id="vrfiltermode_type" value="' . $filter . '">';

        $returnStr .= '<div class="btn-group-sm">';
        $returnStr .= '<button title="' . Language::linkEditTooltip() . '" class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><span class="glyphicon glyphicon-edit"></span></button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';
        $returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_type\').val(0);$(\'#typesidebar\').submit();">' . Language::labelGeneral() . '</a></li>';
        $returnStr .= '<li><a ' . $active[8] . ' onclick="$(\'#vrfiltermode_type\').val(8);$(\'#typesidebar\').submit();">' . Language::labelAccess() . '</a></li>';
        $type = $survey->getType($_SESSION['TYD']);
        $answertype = $type->getAnswerType();
        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                $returnStr .= '<li><a ' . $active[1] . '" onclick="$(\'#vrfiltermode_type\').val(1);$(\'#typesidebar\').submit();">' . Language::labelVerification() . '</a></li>';
            }
            $returnStr .= '<li><a ' . $active[2] . '" onclick="$(\'#vrfiltermode_type\').val(2);$(\'#typesidebar\').submit();">' . Language::labelLayout() . '</a></li>';

            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                $returnStr .= '<li><a ' . $active[3] . '" onclick="$(\'#vrfiltermode_type\').val(3);$(\'#typesidebar\').submit();">' . Language::labelAssistance() . '</a></li>';
            }
            $returnStr .= '<li><a ' . $active[4] . '" onclick="$(\'#vrfiltermode_type\').val(4);$(\'#typesidebar\').submit();">' . Language::labelOutput() . '</a></li>';
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                $returnStr .= '<li><a ' . $active[5] . '" onclick="$(\'#vrfiltermode_type\').val(5);$(\'#typesidebar\').submit();">' . Language::labelInteractive() . '</a></li>';
            }
        }
        $returnStr .= '<li><a ' . $active[7] . ' onclick="$(\'#vrfiltermode_type\').val(7);$(\'#typesidebar\').submit();">' . Language::labelNavigation() . '</a></li>';
        $returnStr .= '</ul>';


        $tagclass = 'class="btn btn-default"';
        if (isset($_COOKIE['uscictypecookie'])) {
            $cookievalue = $_COOKIE['uscictypecookie'];
            if (inArray($type->getSuid() . "~" . $type->getTyd(), explode("-", $cookievalue))) {
                $tagclass = 'class="btn btn-default uscic-cookie-tag-active"';
            }
        }

        $returnStr .= '<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscictypecookie\',\'' . $type->getSuid() . "~" . $type->getTyd() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a>';
        $returnStr .= '<a title="' . Language::linkRefactorTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.refactortype', 'tyd' => $type->getTyd())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-registration-mark"></span></a>';
        $returnStr .= '<a title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copytype', 'tyd' => $type->getTyd())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-copyright-mark"></span></a>';
        $returnStr .= '<a title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movetype', 'tyd' => $type->getTyd())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-move"></span></a>';
        $returnStr .= '<a title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removetype', 'tyd' => $type->getTyd())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>';
        $returnStr .= '</div>';
        $returnStr .= '</form>';
        $returnStr .= $this->displayCookieScripts();

        return $returnStr;
    }

    function getTypeTopTab($filter) {
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';
        if ($filter == 0) {
            $returnStr .= ' <span class="label label-default">' . Language::labelTypeEditGeneral() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(0);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . Language::labelGeneral() . '</span></a>';
        }
        if ($filter == 8) {
            $returnStr .= ' <span class="label label-default">' . Language::labelTypeEditAccess() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(8);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[8] . '">' . Language::labelAccess() . '</span></a>';
        }
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($_SESSION['TYD']);
        $answertype = $type->getAnswerType();
        if (!inArray($answertype, array(ANSWER_TYPE_SECTION))) {
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                if ($filter == 1) {
                    $returnStr .= ' <span class="label ' . $active[1] . '">' . Language::labelVerification() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(1);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . Language::labelVerification() . '</span></a>';
                }
            }
            if ($filter == 2) {
                $returnStr .= ' <span class="label ' . $active[2] . '">' . Language::labelLayout() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(2);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . Language::labelLayout() . '</span></a>';
            }
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                if ($filter == 3) {
                    $returnStr .= ' <span class="label ' . $active[3] . '">' . Language::labelAssistance() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(3);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . Language::labelAssistance() . '</span></a>';
                }
                if ($filter == 6) {
                    $returnStr .= ' <span class="label ' . $active[6] . '">' . Language::labelInteractive() . '</span>';
                } else {
                    $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(6);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[6] . '">' . Language::labelInteractive() . '</span></a>';
                }
            }
            if ($filter == 4) {
                $returnStr .= ' <span class="label ' . $active[4] . '">' . Language::labelOutput() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(4);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[4] . '">' . Language::labelOutput() . '</span></a>';
            }
        }

        if ($filter == 7) {
            $returnStr .= ' <span class="label label-default">' . Language::labelNavigation() . '</span>';
        } else {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_type\').val(7);$(\'#typesidebar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[7] . '">' . Language::labelNavigation() . '</span></a>';
        }
        return $returnStr;
    }

    function showEditType($tyd, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);

        if ($type->getTyd() != "") {
            $returnStr = $this->showTypeHeader($survey, $type, Language::headerEditType(), $message);
        } else {
            $returnStr = $this->showTypeHeader($survey, $type, Language::headerAddType(), $message);
        }

        /* edit existing type */
        if ($type->getTyd() != "") {
            if ($_SESSION['VRFILTERMODE_TYPE'] == 0) {
                $returnStr .= $this->showEditTypeGeneral($type);
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 1) {
                $returnStr .= $this->showEditTypeVerification($type);
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 2) {
                $returnStr .= $this->showEditTypeLayout($type);
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 3) {
                $returnStr .= $this->showEditTypeAssistance($type);
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 4) {
                $returnStr .= $this->showEditTypeOutput($type);
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 7) {
                $returnStr .= $this->showEditTypeNavigation($type);
            } elseif ($_SESSION['VRFILTERMODE_TYPE'] == 8) {
                $returnStr .= $this->showEditTypeAccess($type);
            } else {
                $returnStr .= $this->showEditTypeInteractive($type);
            }
        }
        /* new type */ else {
            $returnStr .= $this->showEditTypeGeneral($type);
        }
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showTypeFooter($survey);
        return $returnStr;
    }

    function showEditTypeGeneral($type) {

        $returnStr = '<form id="editform" method="post">';
        if ($type->getTyd() != "") {
            $returnStr .= $this->getTypeTopTab(0);
        } else {
            $type->setAnswerType(loadvar(SETTING_ANSWERTYPE));
            $type->setKeep(loadvar(SETTING_KEEP));
            $type->setArray(loadvar(SETTING_ARRAY));
            $type->setKeep(loadvar(SETTING_KEEP));
            $t = $type->getAnswerType();
            if (inArray($t, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK))) {
                $type->setOptionsText(loadvarAllowHTML(SETTING_OPTIONS));
            } else if ($t == ANSWER_TYPE_CUSTOM) {
                $type->setAnswerTypeCustom(loadvar(SETTING_ANSWERTYPE_CUSTOM));
            } else if ($t == ANSWER_TYPE_SECTION) {
                $type->setSection(loadvar(SETTING_SECTION));
            }
        }

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypegeneralres', 'tyd' => $type->getTyd()));
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralName() . '</td><td><input type="text" class="form-control" name="' . SETTING_NAME . '" value="' . convertHTLMEntities($type->getName(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelTypeEditGeneralAnswerType() . '</td><td>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->showAnswerTypes($type->getAnswerType());

        $returnStr .= '</td>';

        $answertype = $type->getAnswerType();
        if ($answertype == ANSWER_TYPE_CUSTOM) {
            $returnStr .= '</td><td id="customanswer" style="display: block;"><input type="text" placeholder="' . Language::labelCustomFunctionCall() . '" class="form-control autocompletebasic" name="' . SETTING_ANSWERTYPE_CUSTOM . '" value="' . $this->displayTextSettingValue($type->getAnswerTypeCustom()) . '"></td>';
        } else {
            $returnStr .= '</td><td id="customanswer" style="display: none;"><input type="text" placeholder="' . Language::labelCustomFunctionCall() . '" class="form-control autocompletebasic" name="' . SETTING_ANSWERTYPE_CUSTOM . '" value="' . $this->displayTextSettingValue($type->getAnswerTypeCustom()) . '"></td>';
        }

        $returnStr .= '</tr>';

        /* categories needed */
        $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK);
        if (inArray($answertype, $array)) {
            $returnStr .= '<tr id="categories"><td valign=top>' . Language::labelTypeEditGeneralCategories() . '</td><td><textarea style="min-width: 600px; height: 120px;" class="form-control autocomplete" name="' . SETTING_OPTIONS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';
        } else {
            $returnStr .= '<tr id="categories" style="display: none;"><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td><textarea style="min-width: 600px; height: 120px;" class="form-control autocomplete" name="' . SETTING_OPTIONS . '"></textarea></td></tr>';
        }

        /* no keep/hidden required */
        if (!inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralArray() . "</td>";
            $returnStr .= "<td>" . $this->displayIsArray($type->getArray()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralKeep() . "</td>";
            $returnStr .= "<td>" . $this->displayIsKeep($type->getKeep()) . "</td></tr>";
        } else {
            $returnStr .= "<input type=hidden name='" . SETTING_HIDDEN . "' value='" . HIDDEN_YES . "'>";
            $returnStr .= "<input type=hidden name='" . SETTING_KEEP . "' value='" . KEEP_ANSWER_NO . "'>";
        }

        $returnStr .= '</table></div>';

        if ($type->getTyd() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditTypeOutput($type) {
        $returnStr = '<form id="editform" method="post">';
        if ($type->getTyd() != "") {
            $returnStr .= $this->getTypeTopTab(4);
            //$returnStr .= '<span class="label label-default">' . Language::labelTypeEditOutput() . '</span>';
        }
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypeoutputres', 'tyd' => $type->getTyd()));
        $returnStr .= '<table>';
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHidden() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN, $type->getHidden(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenPaperVersion() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_PAPER_VERSION, $type->getHiddenPaperVersion(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenRouting() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_ROUTING, $type->getHiddenRouting(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenTranslation() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_TRANSLATION, $type->getHiddenTranslation(), true) . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelDataStorage() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $current = $type->getStoreLocation();
        $defaultsurvey = $survey->getStoreLocation();
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        $hide = "";

        if ($current == 1) {
            $hide = "style='display: none;'";
        }
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputLocationStore() . "</td>";
        $returnStr .= "<td>" . $this->displayStoreLocation(SETTING_DATA_STORE_LOCATION, $type->getStoreLocation(), true) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td " . $hide . " id='store1'>" . Language::labelVariableExternal() . "</td><td " . $hide . " id='store2'>" . $helpstart . "<input type=text class='form-control' name='" . SETTING_DATA_STORE_LOCATION_EXTERNAL . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($type->getStoreLocationExternal(), ENT_QUOTES)) . "'>" . $helpend . "</td></td>";
        $returnStr .= "</tr>";

        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                    $("#' . SETTING_DATA_STORE_LOCATION . '").change(function (e) {
                                                        if (this.value > 1 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . ' > 1)) {
                                                            $("#store1").show();
                                                            $("#store2").show();
                                                        }  
                                                        else {                                                        
                                                            $("#store1").hide();
                                                            $("#store2").hide();
                                                        }
                                                    });
                                                    })';
        $returnStr .= "</script>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputInputMask() . "</td>";
        $returnStr .= "<td>" . $this->displayDataInputMask(SETTING_DATA_INPUTMASK, $type->getDataInputMask(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputScreendumps() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_SCREENDUMPS, $type->getScreendumpStorage(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputParadata() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_PARADATA, $type->getParadata(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelDataKeep() . "</td>";
        $returnStr .= "<td>" . $this->displayDataKeep(SETTING_DATA_KEEP, $type->getDataKeep(), true) . "</td></tr>";

        $answertype = $type->getAnswerType();
        $returnStr .= '</table></div>';

        // formatting
        $returnStr .= '<span class="label label-default">' . Language::labelDataFormat() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table>';
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= "<tr><td>" . Language::labelSkipVariable() . "</td>";
        $returnStr .= "<td>" . $this->displayDataSkip(SETTING_DATA_SKIP, $type->getDataSkipVariable(), true) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelSkipVariablePostFix() . "</td><td>" . $helpstart . "<input type=text class='form-control' name='" . SETTING_DATA_SKIP_POSTFIX . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($type->getDataSkipVariablePostFix(), ENT_QUOTES)) . "'>" . $helpend . "</td></td>";
        $returnStr .= "</tr>";


        /* extra needed */
        $array = array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK);
        if (inArray($answertype, $array)) {
            $returnStr .= '<tr id="categories"><td valign=top>' . Language::labelTypeEditOutputCategories() . '</td><td colspan=4><textarea style="height: 120px;" class="form-control uscic-form-control-admin tinymce" name="' . SETTING_OUTPUT_OPTIONS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getOutputOptionsText(), ENT_QUOTES)) . '</textarea></td></tr>';

            if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED))) {
                $returnStr .= "<tr><td>" . Language::labelTypeEditOutputSetOfEnumerated() . "</td>";
                $returnStr .= "<td>" . $this->displaySetOfEnumeratedOutput(SETTING_OUTPUT_SETOFENUMERATED, $type->getOutputSetOfEnumeratedBinary(), true) . "</td></tr>";
            }
            $returnStr .= "<tr><td>" . Language::labelTypeEditOutputValueLabelWidth() . "</td>";
            $returnStr .= "<td>" . $this->displayValueLabelWidth(SETTING_OUTPUT_VALUELABEL_WIDTH, $type->getOutputValueLabelWidth(), true) . "</td></tr>";
        }
        $returnStr .= '</table></div>';

        if ($type->getTyd() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditTypeNavigation($type) {
        $returnStr = '<form id="editform" method="post">';
        if ($type->getTyd() != "") {
            $returnStr .= $this->getTypeTopTab(7);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypenavigationres', 'tyd' => $type->getTyd()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingEnabled() . "</td>";
        $returnStr .= "<td>" . $this->displayKeyBoardBindingDropdown(SETTING_KEYBOARD_BINDING_ENABLED, $type->getKeyboardBindingEnabled(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingBack() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_BACK, $type->getKeyboardBindingBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNext() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NEXT, $type->getKeyboardBindingNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingDK() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_DK, $type->getKeyboardBindingDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRF() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_RF, $type->getKeyboardBindingRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNA() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NA, $type->getKeyboardBindingNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingUpdate() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_UPDATE, $type->getKeyboardBindingUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRemark() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_REMARK, $type->getKeyboardBindingRemark()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingClose() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_CLOSE, $type->getKeyboardBindingClose()) . $helpend . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNA() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA, $type->getIndividualDKRFNA(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNAInline() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA_INLINE, $type->getIndividualDKRFNAInline(), true) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditTypeAccess($type) {
        $returnStr = '<form id="editform" method="post">';
        if ($type->getTyd() != "") {
            $returnStr .= $this->getTypeTopTab(8);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypeaccessres', 'tyd' => $type->getTyd()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryAction(SETTING_ACCESS_REENTRY_ACTION, $type->getAccessReentryAction(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentryPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryPreload(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $type->getAccessReentryRedoPreload(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletion() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionReturn(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $type->getAccessReturnAfterCompletionAction(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletionPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionPreload(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $type->getAccessReturnAfterCompletionRedoPreload(), true) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditTypeLayout($type) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypelayoutres', 'tyd' => $type->getTyd()));

        $answertype = $type->getAnswerType();
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }

        /* header/footer setting */
        $returnStr .= $this->getTypeTopTab(2);
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsHeader() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_HEADER . '" name="' . SETTING_PAGE_HEADER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getPageHeader(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsFooter() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_FOOTER . '" name="' . SETTING_PAGE_FOOTER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getPageFooter(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsPlaceholder() . '</td><td>' . $helpstart . '<input name="' . SETTING_PLACEHOLDER . '" type="text" value="' . $this->displayTextSettingValue((convertHTLMEntities($type->getPlaceholder(), ENT_QUOTES))) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutOverall() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditQuestionAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_QUESTION_ALIGNMENT, $type->getQuestionAlignment(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditQuestionFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_QUESTION_FORMATTING, $type->getQuestionFormatting(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditAnswerAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_ANSWER_ALIGNMENT, $type->getAnswerAlignment(), true) . "</td><td width=25><nobr/></td>
                        <td>" . Language::labelTypeEditAnswerFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_ANSWER_FORMATTING, $type->getAnswerFormatting(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditButtonAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_BUTTON_ALIGNMENT, $type->getButtonAlignment(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_BUTTON_FORMATTING, $type->getButtonFormatting(), true) . "</td></tr>";
        $returnStr .= '</table></div>';

        if ($answertype == ANSWER_TYPE_TIME) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutTimePicker() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutFormat() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_TIME_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getTimeFormat(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_DATE) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDatePicker() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutFormat() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getDateFormat(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateDefault() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_DEFAULT_VIEW . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getDateDefaultView(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_DATETIME) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDateTimePicker() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutFormat() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATETIME_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getDateTimeFormat(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateDefault() . '</td>';
            $returnStr .= "<td>" . $helpstart . '<input  style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_DEFAULT_VIEW . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getDateDefaultView(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateTimeCollapse() . '</td>';
            $returnStr .= "<td>" . $this->displayCollapse(SETTING_DATETIME_COLLAPSE, $type->getDateTimeCollapse(), true) . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateTimeSideBySide() . '</td>';
            $returnStr .= "<td>" . $this->displaySideBySide(SETTING_DATETIME_SIDE_BY_SIDE, $type->getDateTimeSideBySide(), true) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_SLIDER) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSlider() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= $this->displayComboBox();
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutOrientation() . "</td>";
            $returnStr .= "<td>" . $this->displayOrientation(SETTING_SLIDER_ORIENTATION, $type->getSliderOrientation()) . "</td>";
            $returnStr .= "<td width=25><nobr/></td>";
            $returnStr .= "<td>" . Language::labelTypeEditLayoutStep() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getIncrement(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSliderLabelPlacement() . '</td>';
            $returnStr .= "<td>" . $this->displaySliderPlacement(SETTING_SLIDER_LABEL_PLACEMENT, $type->getSliderLabelPlacement(), true) . "</td><td width=25><nobr/></td><td>" . Language::labelTypeEditLayoutSliderLabels() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_LABELS . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getSliderLabels(), ENT_QUOTES)) . '"></td></tr>';


            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutTooltip() . "</td><td>" . $this->displayTooltip(SETTING_SLIDER_TOOLTIP, $type->getTooltip()) . "</td>
                    <td><nobr/></td><td>" . Language::labelTypeEditLayoutDot() . "</td><td>" . $this->displaySliderMarker(SETTING_SLIDER_PRESELECTION, $type->getSliderPreSelection()) . "</td>
                    </tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditFormatter() . '</td><td colspan=4><div class="input-group"><textarea style="min-width: 600px; width: 100%; min-height: 100px;" class="form-control autocompletebasic" id="' . SETTING_SLIDER_FORMATER . '" name="' . SETTING_SLIDER_FORMATER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getSliderFormater(), ENT_QUOTES)) . '</textarea><span class="input-group-addon"><i>If empty, follows survey</i></span></div></td></tr>';


            $returnStr .= "</table>";

            $user = new User($_SESSION['URID']);
            if ($user->hasRoutingAutoIndentation()) {
                $returnStr .= $this->getCodeMirror('height: 100px; width: 500px;');
                $returnStr .= '<script src="js/codemirror/mode/javascript/javascript.js"></script>';
                $returnStr .= '<script src="js/codemirror/mode/css/css.js"></script>';
                $returnStr .= '<script type="text/javascript">                                
                                $(document).ready(function() {
                                   var editor = CodeMirror.fromTextArea(document.getElementById("' . SETTING_SLIDER_FORMATER . '"), {mode: "text/javascript", lineNumbers: false});
                                });
                           </script>';
            }


            $returnStr .= "<br/><br/><table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
            $returnStr .= "<td>" . $this->displayTextBox(SETTING_SLIDER_TEXTBOX, $type->getTextBox(), true) . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelSliderTextBoxBefore() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getTextBoxLabel(), ENT_QUOTES)) . '">' . $helpend . '</td>';
            $returnStr .= "<td width=25><nobr/></td><td>" . Language::labelSliderTextBoxAfter() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getTextBoxPostText(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSpinner() . '</td><td>' . $this->displaySpinner($type->getSpinner(), true) . "</td>";
            $returnStr .= '<td width=25><nobr/></td><td>' . Language::labelTypeEditGeneralSpinnerType() . '</td><td>' . $this->displaySpinnerType($type->getSpinnerType(), true) . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerUp() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_UP, $type->getSpinnerUp()) . $helpend . "</td>";
            $returnStr .= "<td width=25><nobr/></td><td>" . Language::labelTypeEditGeneralSpinnerDown() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_DOWN, $type->getSpinnerDown()) . $helpend . "</td></tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutManual() . '</td><td>' . $this->displayManual($type->getTextboxManual()) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if ($answertype == ANSWER_TYPE_KNOB) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutKnob() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= $this->displayComboBox();
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutStep() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getIncrement(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            //$returnStr .= "<tr><td>" . Language::labelTypeEditLayoutRotation() . "</td>";
            //$returnStr .= "<td>" . $this->displayRotation(SETTING_KNOB_ROTATION, $type->getKnobRotation()) . "</td><td width=25><nobr/></td><td>" . Language::labelTypeEditLayoutStep() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getIncrement(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
            $returnStr .= "<td>" . $this->displayTextBox(SETTING_SLIDER_TEXTBOX, $type->getTextBox(), true) . "</td><td width=25><nobr/></td></tr>";
            $returnStr .= "<tr><td>" . Language::labelSliderTextBoxBefore() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getTextBoxLabel(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= "<tr><td>" . Language::labelSliderTextBoxAfter() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getTextBoxPostText(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSpinner() . '</td><td>' . $this->displaySpinner($type->getSpinner(), true) . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralSpinnerType() . '</td><td>' . $this->displaySpinnerType($type->getSpinnerType(), true) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerUp() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_UP, $type->getSpinnerUp()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerDown() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_DOWN, $type->getSpinnerDown()) . $helpend . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutManual() . '</td><td>' . $this->displayManual($type->getTextboxManual()) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedRandomizer() . "</td>";
            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedRandomizer(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultOrder() . '</i></span></div></td></tr>';
            $returnStr .= "<tr><td>" . Language::labelTypeEditDropdownOptgroup() . "</td>";
            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_DROPDOWN_OPTGROUP . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getComboboxOptGroup(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyNoOptGroups() . '</i></span></div></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if (inArray($answertype, array(ANSWER_TYPE_RANK))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutEnumeratedLabel() . "</td><td>" . $this->displayEnumeratedLabelRank(SETTING_ENUMERATED_LABEL, $type->getEnumeratedLabel(), true) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedRandomizer() . "</td>";
            $returnStr .= '<td><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedRandomizer(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultOrder() . '</i></span></div></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutEnumeratedTemplate() . "</td>";
            $returnStr .= "<td>" . $this->displayEnumeratedTemplate(SETTING_ENUMERATED_ORIENTATION, $type->getEnumeratedDisplay(), true) . "</td><td width=25><nobr/>";

            $survey = new Survey($_SESSION['SUID']);
            $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
            $current = $type->getEnumeratedDisplay();
            $defaultsurvey = $survey->getEnumeratedDisplay();
            $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;


            $s = "";
            if ($current == ORIENTATION_CUSTOM) {
                $s = "style='display: none;'";
            }
            $returnStr .= "<td id='custom1' $s>" . Language::labelTypeEditEnumeratedOrder() . "</td>";
            $returnStr .= "<td id='custom2' $s>" . $this->displayEnumeratedOrder(SETTING_ENUMERATED_ORDER, $type->getEnumeratedOrder(), true) . "</td></tr>";

            if ($current == ORIENTATION_HORIZONTAL) {
                $returnStr .= "<tr id='horizontal1'><td>" . Language::labelTypeEditEnumeratedSplit() . "</td>";
            } else {
                $returnStr .= "<tr id='horizontal1' style='display: none;'><td>" . Language::labelTypeEditEnumeratedSplit() . "</td>";
            }

            $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_SPLIT, $type->getEnumeratedSplit(), true) . "</td><td width=25><nobr/></td>
                            <td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
            $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, $type->getHeaderAlignment(), true) . "</td></tr>";

            if ($current == ORIENTATION_HORIZONTAL) {
                $returnStr .= "<tr id='horizontal2'><td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
            } else {
                $returnStr .= "<tr id='horizontal2' style='display: none;'><td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
            }

            $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, $type->getHeaderFormatting(), true) . "</td><td width=25><nobr/></td>";

            $returnStr .= "<td>" . Language::labelGroupEditBordered() . "</td>";
            $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_BORDERED, $type->getEnumeratedBordered(), true) . "</td></tr>";

            if ($current == ORIENTATION_HORIZONTAL) {
                $returnStr .= "<tr id='horizontal5'><td>" . Language::labelTypeEditMobile() . "</td>";
            } else {
                $returnStr .= "<tr id='horizontal5' style='display: none;'><td>" . Language::labelTypeEditMobile() . "</td>";
            }
            $returnStr .= '<td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE, $type->getTableMobile(), true) . '</td><td width=25><nobr/></td>';
            $returnStr .= '<td>' . Language::labelTypeEditMobileLabels() . '</td><td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE_LABELS, $type->getTableMobileLabels(), true) . '</td>';
            $returnStr .= "</tr>";

            if ($current == ORIENTATION_CUSTOM) {
                $returnStr .= "<tr id=customtemplate><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
            } else {
                $returnStr .= "<tr id=customtemplate style='display: none;'><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
            }
            $returnStr .= '<td colspan=4><textarea style="width: 500px;" rows=5 class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_CUSTOM . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedCustom(), ENT_QUOTES)) . '</textarea></td></tr>';
            $returnStr .= "</tr>";

            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
            $returnStr .= "<td>" . $this->displayEnumeratedTextBox(SETTING_ENUMERATED_TEXTBOX, $type->getEnumeratedTextBox(), true) . "</td><td width=25><nobr/></td>";
            $returnStr .= "<td>" . Language::labelTypeEditLayoutEnumeratedLabel() . "</td><td>" . $this->displayEnumeratedLabel(SETTING_ENUMERATED_LABEL, $type->getEnumeratedLabel(), true) . "</td>";
            $returnStr .= "</tr>";
            $returnStr .= '<tr id="horizontal6"><td>' . Language::labelTypeEditLayoutClickLabel() . '</td>';
            $returnStr .= "<td>" . $this->displayClickLabel(SETTING_ENUMERATED_CLICK_LABEL, $type->getEnumeratedClickLabel(), true) . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelEnumeratedTextBoxBefore() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedTextBoxLabel(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>
                           <tr><td>' . Language::labelEnumeratedTextBoxAfter() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_TEXTBOX_POSTTEXT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedTextBoxPostText(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';

            if ($current == ORIENTATION_VERTICAL) {
                $returnStr .= "<tr id='columns'><td>" . Language::labelTypeEditEnumeratedColumns() . "</td>";
            } else {
                $returnStr .= "<tr id='columns' style='display: none;'><td>" . Language::labelTypeEditEnumeratedColumns() . "</td>";
            }

            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyColumnsPlaceholder() . '" type="text" class="form-control" name="' . SETTING_ENUMERATED_COLUMNS . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedColumns(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyColumns() . '</i></span></div></td></tr>';

            $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedRandomizer() . "</td>";
            $returnStr .= '<td colspan=4><div class="input-group"><input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getEnumeratedRandomizer(), ENT_QUOTES)) . '"/><span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultOrder() . '</i></span></div></td></tr>';
            $returnStr .= '</table>';

            $returnStr .= "<script type='text/javascript'>";
            $returnStr .= '$( document ).ready(function() {
                                                    $("#' . SETTING_ENUMERATED_ORIENTATION . '").change(function (e) {
                                                        if (this.value == 3 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . '==3)) {
                                                            $("#customtemplate").show();
                                                            $("#horizontal1").hide();
                                                            $("#horizontal2").hide();
                                                            $("#horizontal5").hide();
                                                            $("#horizontal6").hide();
                                                            $("#custom1").hide();
                                                            $("#custom2").hide();
                                                            $("#columns").hide();
                                                        }  
                                                        else if (this.value == 1 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . '==1)) {
                                                            $("#horizontal1").show();
                                                            $("#horizontal2").show();
                                                            $("#horizontal5").show();
                                                            $("#horizontal6").show();
                                                            $("#custom1").show();
                                                            $("#custom2").show();
                                                            $("#customtemplate").hide();
                                                            $("#columns").hide();
                                                        }
                                                        else if (this.value == 2 || (this.value=="settingfollowgeneric" && ' . $defaultsurvey . '==2)) {
                                                            $("#customtemplate").hide();
                                                            $("#horizontal1").hide();
                                                            $("#horizontal2").hide();
                                                            $("#horizontal5").hide();
                                                            $("#horizontal6").hide();
                                                            $("#custom1").show();
                                                            $("#custom2").show();
                                                            $("#columns").show();
                                                        }
                                                        else {
                                                            $("#horizontal1").hide();
                                                            $("#horizontal2").hide();
                                                            $("#horizontal5").hide();
                                                            $("#horizontal6").hide();
                                                            $("#custom1").hide();
                                                            $("#custom2").hide();
                                                            $("#customtemplate").hide();
                                                            $("#columns").hide();
                                                        }
                                                    });
                                                    })';
            $returnStr .= "</script>";

            $returnStr .= '</div>';
        }

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutError() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutErrorPlacement() . "</td>";
        $returnStr .= "<td>" . $this->displayErrorPlacement(SETTING_ERROR_PLACEMENT, $type->getErrorPlacement(), true) . "</td><td width=25><nobr/>";
        $returnStr .= "</tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutButtons() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->displayColorPicker();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td>";

        $returnStr .= "<td>" . $this->displayButton(SETTING_BACK_BUTTON, $type->getShowBackButton(), true) . "</td>
                      <td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $type->getLabelBackButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NEXT_BUTTON, $type->getShowNextButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $type->getLabelNextButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_DK_BUTTON, $type->getShowDKButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $type->getLabelDKButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_RF_BUTTON, $type->getShowRFButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $type->getLabelRFButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NA_BUTTON, $type->getShowNAButton(), true) . "</td><td width=25><nobr/></td>            
                    <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $type->getLabelNAButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_UPDATE_BUTTON, $type->getShowUpdateButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $type->getLabelUpdateButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_BUTTON, $type->getShowRemarkButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $type->getLabelRemarkButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_SAVE_BUTTON, $type->getShowRemarkSaveButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $type->getLabelRemarkSaveButton()) . $helpend . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_CLOSE_BUTTON, $type->getShowCloseButton(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $type->getLabelCloseButton()) . $helpend . "</td></tr>";

        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutProgressBar() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table><tr><td>" . Language::labelTypeEditLayoutProgressBarShow() . "</td>";
        $returnStr .= "<td>" . $this->displayProgressbar(SETTING_PROGRESSBAR_SHOW, $type->getShowProgressBar(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarFillColor() . "</td>";
        $returnStr .= '<td><div class="input-group colorpicker">
          <input name="' . SETTING_PROGRESSBAR_FILLED_COLOR . '" type="text" value="' . $this->displayTextSettingValue($type->getProgressBarFillColor()) . '" class="form-control" />
          <span class="input-group-addon"><i></i></span><i>' . $message . '
          </div></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutProgressBarWidth() . '</td><td>' . $helpstart . '<input type="text" class="form-control" name="' . SETTING_PROGRESSBAR_WIDTH . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($type->getProgressBarWidth(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSpinner() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralSpinner() . '</td><td>' . $this->displaySpinner($type->getSpinner(), true) . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralSpinnerType() . '</td><td>' . $this->displaySpinnerType($type->getSpinnerType(), true) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerUp() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_UP, $type->getSpinnerUp()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerDown() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerUp(SETTING_SPINNER_DOWN, $type->getSpinnerDown()) . $helpend . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralSpinnerStep() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displaySpinnerStep(SETTING_SPINNER_STEP, $type->getSpinnerIncrement()) . $helpend . "</td></tr>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutManual() . '</td><td>' . $this->displayManual($type->getTextboxManual()) . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        if (Config::xiExtension()) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutXi() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralGroupXiTemplate() . '</td><td>
                            <select class="selectpicker show-tick" id="' . SETTING_GROUP_XI_TEMPLATE . '" name="' . SETTING_GROUP_XI_TEMPLATE . '">';
            $current = $type->getXiTemplate();
            if (file_exists(getBase() . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "xitemplates.php")) {
                $xitemplates = file_get_contents(getBase() . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "xitemplates.php", "r");
                ;
                $list = explode(");", $xitemplates);
                foreach ($list as $l) {
                    if (contains($l, " new Template")) {
                        $sub = explode("=", $l);
                        $selected = "";
                        $entry = trim(str_replace("\$", "", $sub[0]));
                        if (strtoupper($entry) == strtoupper($current)) {
                            $selected = "SELECTED";
                        }
                        $returnStr .= "<option $selected value='" . $entry . "'>" . $entry . "</option>";
                    }
                }
            }
            $returnStr .= '</select>    
                            </td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showEditTypeInteractive($type) {

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypeinteractiveres', 'tyd' => $type->getTyd()));
        $returnStr .= $this->getTypeTopTab(6);
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveInlineText() . '</td><td><textarea style="min-width: 600px; width: 100%; min-height: 100px;" class="form-control autocompletebasic" id="' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '" name="' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getInlineJavascript(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditinteractivePageText() . '</td><td>' . $helpstart . '<textarea style="min-width: 600px; width: 100%; min-height: 100px;" class="form-control autocompletebasic" id="' . SETTING_JAVASCRIPT_WITHIN_PAGE . '" name="' . SETTING_JAVASCRIPT_WITHIN_PAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getPageJavascript(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveExtraJavascript() . '</td><td>' . $helpstart . '<textarea style="min-width: 600px; width: 100%; min-height: 100px;"" class="form-control autocompletebasic" name="' . SETTING_SCRIPTS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getScripts()), ENT_QUOTES) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditInteractiveStyle() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractiveInlineStyle() . '</td><td><textarea style="min-width: 600px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_STYLE_WITHIN_ELEMENT . '" name="' . SETTING_STYLE_WITHIN_ELEMENT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getInlineStyle(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditInteractivePageStyle() . '</td><td>' . $helpstart . '<textarea style="min-width: 600px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_STYLE_WITHIN_PAGE . '" name="' . SETTING_STYLE_WITHIN_PAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getPageStyle(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '</table></div>';

        $user = new User($_SESSION['URID']);
        if ($user->hasRoutingAutoIndentation()) {
            $returnStr .= $this->getCodeMirror('height: 100px; width: 600px;');
            $returnStr .= '<script src="js/codemirror/mode/javascript/javascript.js"></script>';
            $returnStr .= '<script src="js/codemirror/mode/css/css.js"></script>';
            $returnStr .= '<script type="text/javascript">$(document).ready(function() {
                                   var editor = CodeMirror.fromTextArea(document.getElementById("' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '"), {mode: "text/javascript", lineNumbers: false});
                                   var editor1 = CodeMirror.fromTextArea(document.getElementById("' . SETTING_JAVASCRIPT_WITHIN_PAGE . '"), {mode: "text/javascript", lineNumbers: true});
                                   var editor2 = CodeMirror.fromTextArea(document.getElementById("' . SETTING_STYLE_WITHIN_ELEMENT . '"), {mode: "text/x-javascript", lineNumbers: false});
                                   var editor3 = CodeMirror.fromTextArea(document.getElementById("' . SETTING_STYLE_WITHIN_PAGE . '"), {mode: "text/x-scss", lineNumbers: true});
                                });
                           </script>';
        }

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnSubmit() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_BACK, $type->getOnBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_NEXT, $type->getOnNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_DK, $type->getOnDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_RF, $type->getOnRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_NA, $type->getOnNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_UPDATE, $type->getOnUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_LANGUAGE_CHANGE, $type->getOnLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_MODE_CHANGE, $type->getOnModeChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnClick() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_BACK, $type->getClickBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NEXT, $type->getClickNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_DK, $type->getClickDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_RF, $type->getClickRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NA, $type->getClickNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_UPDATE, $type->getClickUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_LANGUAGE_CHANGE, $type->getClickLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_MODE_CHANGE, $type->getClickModeChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditTypeVerification($type) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypevalidationres', 'tyd' => $type->getTyd()));
        $returnStr .= $this->getTypeTopTab(1);
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfEmpty() . "</td>";
        $returnStr .= "<td>" . $this->displayIfEmpty(SETTING_IFEMPTY, $type->getIfEmpty(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfError() . "</td>";
        $returnStr .= "<td>" . $this->displayIfError(SETTING_IFERROR, $type->getIfError(), true) . "</td></tr>";
        $returnStr .= '</table></div>';

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        $helpformat = '<span class="input-group-addon"><i>' . Language::helpInvalidSet() . '</i></span></div>';
        ;

        $t = $type->getAnswerType();
        if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_OPEN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_CALENDAR, ANSWER_TYPE_RANK, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationCriteria() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            switch ($t) {
                case ANSWER_TYPE_ENUMERATED:
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayExclusive(SETTING_INLINE_EXCLUSIVE, $type->getInlineExclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditInclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayInclusive(SETTING_INLINE_INCLUSIVE, $type->getInlineInclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMinRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MINIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($type->getInlineMinimumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMaxRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MAXIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($type->getInlineMaximumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExactRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_EXACT_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($type->getInlineExactRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayExclusive(SETTING_INLINE_EXCLUSIVE, $type->getInlineExclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditInclusive() . "</td>";
                    $returnStr .= "<td>" . $this->displayInclusive(SETTING_INLINE_INCLUSIVE, $type->getInlineInclusive(), true) . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMinRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MINIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($type->getInlineMinimumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditMaxRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_MAXIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($type->getInlineMaximumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                    $returnStr .= "<tr><td>" . Language::labelInlineEditExactRequired() . "</td>";
                    $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_INLINE_EXACT_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($type->getInlineExactRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
                /* fall through */
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumSelected() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimumSelected()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumSelected() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximumSelected()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextExactSelected() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_EXACT_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getExactSelected()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextInvalidSubSet() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_INVALIDSUB_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getInvalidSubSelected()) . "'>" . $helpformat . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextInvalidSet() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_INVALID_SELECTED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getInvalidSelected()) . "'>" . $helpformat . "</td></tr>";
                    break;
                case ANSWER_TYPE_OPEN:
                /* fall through */
                case ANSWER_TYPE_STRING:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumLength() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_LENGTH . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimumLength()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumLength() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_LENGTH . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximumLength()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumWords() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_WORDS . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimumWords()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumWords() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_WORDS . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximumWords()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextPattern() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_PATTERN . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getPattern()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_RANGE:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    $helpend1 = '<span class="input-group-addon"><i>Comma separated list; ' . $message . '</i></span></div>';
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeOther() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_OTHER_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getOtherValues()) . "'>" . $helpend1 . "</td></tr>";
                    break;
                case ANSWER_TYPE_SLIDER:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_KNOB:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditCalendarMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_CALENDAR . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximumDatesSelected()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_RANK:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMinimumRanked() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANKED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMinimumRanked()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextMaximumRanked() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANKED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getMaximumRanked()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditTextExactRanked() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_EXACT_RANKED . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getExactRanked()) . "'>" . $helpend . "</td></tr>";
                    break;
                case ANSWER_TYPE_CUSTOM:
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control' value='" . $this->displayTextSettingValue($type->getMinimum()) . "'>" . $helpend . "</td></tr>";
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control' value='" . $this->displayTextSettingValue($type->getMaximum()) . "'>" . $helpend . "</td></tr>";
                    $helpend1 = '<span class="input-group-addon"><i>Comma separated list; ' . $message . '</i></span></div>';
                    $returnStr .= "<tr><td>" . Language::labelTypeEditRangeOther() . "</td>";
                    $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_OTHER_RANGE . "' type='text' class='form-control' value='" . $this->displayTextSettingValue($type->getOtherValues()) . "'>" . $helpend1 . "</td></tr>";
                    break;
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        /* numerical comparisons */
        $message2 = trim(Language::helpComparison());
        $helpend2 = '<span class="input-group-addon"><i>' . $message2 . '</i></span></div>';
        if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationComparison() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonNotEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_NOT_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonNotEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonGreaterOrEqualThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_GREATER_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonGreaterEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonGreaterThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_GREATER . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonGreater()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonLessOrEqualThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_SMALLER_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonSmallerEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonLessThan() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_SMALLER . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonSmaller()) . "'>" . $helpend2 . "</td></tr>";

            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }
        /* string comparisons */ if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationComparison() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonEqualToIgnoreCase() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonEqualToIgnoreCase()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonNotEqualTo() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_NOT_EQUAL_TO . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonNotEqualTo()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditComparisonNotEqualToIgnoreCase() . "</td>";
            $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getComparisonNotEqualToIgnoreCase()) . "'>" . $helpend2 . "</td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME))) {
            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationMasking() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditTextInputMaskEnable() . "</td>";
            $returnStr .= "<td>" . $this->displayInputMaskEnabled($type->getInputMaskEnabled(), true) . "</td></tr>";

            if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER))) {
                $returnStr .= "<tr id='row1'><td>" . Language::labelTypeEditTextInputMask() . "</td>";
                $returnStr .= "<td style='width: 150px; max-width: 150px;'>" . $this->displayInputMasks(SETTING_INPUT_MASK, $type->getInputMask(), true) . "</td>";

                if ($type->getInputMask() == INPUTMASK_CUSTOM) {
                    $returnStr .= "<td id='inputmaskcell'><input name='" . SETTING_INPUT_MASK_CUSTOM . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue(convertHTLMEntities($type->getInputMaskCustom(), ENT_QUOTES)) . "'></td></tr>";
                } else {
                    $returnStr .= "<td id='inputmaskcell' style='display: none;'><input name='" . SETTING_INPUT_MASK_CUSTOM . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue(convertHTLMEntities($type->getInputMaskCustom(), ENT_QUOTES)) . "'></td></tr>";
                }


                $returnStr .= "<tr id='row2'><td>" . Language::labelTypeEditTextInputMaskPlaceholder() . "</td>";
                $returnStr .= "<td>" . $helpstart . "<input name='" . SETTING_INPUT_MASK_PLACEHOLDER . "' type='text' class='form-control autocompletebasic' value='" . $this->displayTextSettingValue($type->getInputMaskPlaceholder()) . "'>" . $helpend . "</td></tr>";

                $returnStr .= '<tr><td>' . Language::labelTypeEditValidationCallback() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_INPUT_MASK_CALLBACK . '" name="' . SETTING_INPUT_MASK_CALLBACK . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getInputMaskCallback(), ENT_QUOTES)) . '</textarea></td></tr>';

                $returnStr .= '<script type="text/javascript">
                                $( document ).ready(function() {
                                                    $("#' . SETTING_INPUT_MASK . '").change(function (e) {
                                                        if (this.value == "' . INPUTMASK_CUSTOM . '") {
                                                            $("#inputmaskcell").show();
                                                        }   
                                                        else {
                                                            $("#inputmaskcell").hide();
                                                        }
                                                    });
                                                    })';
                $returnStr .= '</script>';
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationAssignment() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelValidateAssignment() . "</td>";
        $returnStr .= "<td>" . $this->displayValidateAssignment(SETTING_VALIDATE_ASSIGNMENT, $type->getValidateAssignment()) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showEditTypeAssistance($type) {

        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.edittypeassistanceres', 'tyd' => $type->getTyd()));
        //$returnStr .= '<span class="label label-default">' . Language::labelTypeEditAssistanceTexts() . '</span>';
        $returnStr .= $this->getTypeTopTab(3);
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }
        if (inArray($type->getAnswerType(), array(ANSWER_TYPE_STRING, ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePreText() . '</td><td><input style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_PRETEXT . '" value="' . convertHTLMEntities($type->getPreText(), ENT_QUOTES) . '"></td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditAssistancePostText() . '</td><td><input style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_POSTTEXT . '" value="' . convertHTLMEntities($type->getPostText(), ENT_QUOTES) . '"></td></tr>';
        }

        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceHoverText() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_HOVERTEXT . '" name="' . SETTING_HOVERTEXT . '">' . convertHTLMEntities($type->getHoverText(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditAssistanceMessages() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_EMPTY_MESSAGE . '" name="' . SETTING_EMPTY_MESSAGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getEmptyMessage(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';

        $at = $type->getAnswerType();
        switch ($at) {
            case ANSWER_TYPE_DOUBLE:
                $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_DOUBLE . '" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageDouble(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_INTEGER:
                $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INTEGER . '" name="' . SETTING_ERROR_MESSAGE_INTEGER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInteger(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_PATTERN . '" name="' . SETTING_ERROR_MESSAGE_PATTERN . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessagePattern(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMinimumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumLength(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMinimumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumWords(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_ENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEnumeratedEntered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '" name="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageEnumeratedEntered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSetOfEnumeratedEntered() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '" name="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSetOfEnumeratedEntered(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectInvalidSubset(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageSelectInvalidSet(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_RANK:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinRank() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_RANK . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_RANK . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageRankMinimum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxRank() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_RANK . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_RANK . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageRankMaximum(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageExactRank() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_RANK . '" name="' . SETTING_ERROR_MESSAGE_EXACT_RANK . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageRankExact(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_RANGE:
            /* fall through */
            case ANSWER_TYPE_KNOB:
            /* fall through */
            case ANSWER_TYPE_SLIDER:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_RANGE . '" name="' . SETTING_ERROR_MESSAGE_RANGE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageRange(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
            case ANSWER_TYPE_CALENDAR:
                $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageMaximumCalendar(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
                break;
        }

        /* numeric comparison */
        if (inArray($at, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonNotEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreaterEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonGreaterEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreater() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonGreater(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmallerEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonSmallerEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmaller() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonSmaller(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        }
        /* string comparisons */ else if (inArray($at, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualToIgnoreCase() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonEqualToIgnoreCase(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualTo() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonNotEqualTo(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualToIgnoreCase() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '">' . $this->displayTextSettingValue(convertHTLMEntities($type->getErrorMessageComparisonNotEqualToIgnoreCase(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        }

        $returnStr .= '</table></div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showRefactorType($tyd, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);
        $returnStr = $this->showTypeHeader($survey, $type, Language::headerRefactorType(), $message);
        $returnStr .= $this->displayWarning(Language::messageRefactorType($type->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.refactortyperes', 'tyd' => $type->getTyd()));
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelTypeRefactor() . '</td>';
        $returnStr .= "<td><input class='form-control' type=text name=" . SETTING_NAME . " /></td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRefactor() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showTypeFooter($survey);
        return $returnStr;
    }

    function showMoveType($tyd, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);
        $returnStr = $this->showTypeHeader($survey, $type, Language::headerMoveType(), $message);
        $returnStr .= $this->displayWarning(Language::messageMoveType($type->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.movetyperes', 'tyd' => $type->getTyd()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();

        $surveys = new Surveys();
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], $_SESSION['SUID']) . '</tr>';
        }
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showTypeFooter($survey);
        return $returnStr;
    }

    function showCopyType($tyd, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);
        $returnStr = $this->showTypeHeader($survey, $type, Language::headerCopyType(), $message);
        $returnStr .= $this->displayWarning(Language::messageCopyType($type->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.copytyperes', 'tyd' => $type->getTyd()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], '') . '</tr>';
        $returnStr .= "<tr><td>" . Language::labelTypeCopySuffix() . "</td>";

        $returnStr .= "<td><select class='selectpicker show-tick' name=includesuffix>";
        $returnStr .= "<option value=" . INPUT_MASK_YES . ">" . Language::optionsInputMaskYes() . "</option>";
        $returnStr .= "<option value=" . INPUT_MASK_NO . ">" . Language::optionsInputMaskNo() . "</option>";
        $returnStr .= "</select></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';

        $returnStr .= $this->showTypeFooter($survey);
        return $returnStr;
    }

    function showRemoveType($tyd, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);
        $returnStr = $this->showTypeHeader($survey, $type, Language::headerRemoveType(), $message);
        $returnStr .= $this->displayWarning(Language::messageRemoveType($type->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.removetyperes', 'tyd' => $type->getTyd()));
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showTypeFooter($survey);
        return $returnStr;
    }

    /* GROUPS */

    function showGroupHeader($survey, $group, $type, $message) {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $section = $survey->getSection($_SESSION['SEID']);
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.section', 'seid' => $seid), $section->getName()) . '</li>';
        if ($group->getName() != "") {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.group', 'gid' => $group->getGid()), $group->getName()) . '</li>';
        }
        if ($group->getName() != "") {
            if ($_SESSION['VRFILTERMODE_GROUP'] == 0) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeGeneral() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 1) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeVerification() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 2) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeLayout() . '</li>';
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 3) {
                $returnStr .= '<li class="active">' . Language::headerEditTypeAssistance() . '</li>';
            }
        } else {
            $returnStr .= '<li class="active">' . Language::headerAddGroup() . '</li>';
        }
        $returnStr .= '</ol>';
        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
        $returnStr .= $message;
        return $returnStr;
    }

    function getGroupTopTab($filter) {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = '';
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';


        $group = $survey->getGroup($_SESSION['GID']);

        $returnStr = '';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(0);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . Language::labelGeneral() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(5);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[5] . '">' . Language::labelAccess() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(1);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . Language::labelVerification() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(2);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . Language::labelLayout() . '</span></a>';
        $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(3);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[3] . '">' . Language::labelAssistance() . '</span></a>';

        if ($group->getType() != GROUP_SUB) {
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(6);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[6] . '">' . Language::labelInteractive() . '</span></a>';
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(4);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[4] . '">' . Language::labelNavigation() . '</span></a>';
            $returnStr .= ' <a onclick="$(\'#vrfiltermode_group\').val(7);$(\'#groupsidebar\').submit();" style="text-decoration:none;"><span class="label ' . $active[7] . '">' . Language::labelOutput() . '</span></a>';
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

    function showGroups($groups) {

        if (sizeof($groups) > 0) {
            $user = new User($_SESSION['URID']);
            $returnStr = $this->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#grouptable').dataTable(
                                {
                                    \"iDisplayLength\": " . $user->getItemsInTable() . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    colVis: {
                                        activate: \"mouseover\",
                                        exclude: [ 0, 1 ]
                                    }
                                    }    
                                );
                                         
                       });</script>

                        "; //

            $returnStr .= $this->displayPopoverScript();
            $returnStr .= $this->displayCookieScripts();
            $returnStr .= '<table id="grouptable" class="table table-striped table-bordered table-condensed table-hover">';
            $returnStr .= '<thead><tr><th></th><th style="cursor: default;">' . Language::labelTypeEditGeneralName() . '</th><th style="cursor: default;">' . Language::labelTypeEditGeneralTemplate() . '</th></tr></thead><tbody>';
            $arr = Common::surveyTemplates();
            foreach ($groups as $group) {
                $returnStr .= '<tr><td>';
                $content = '<a title="' . Language::linkEditTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.editgroup', 'gid' => $group->getGid())) . '"><span class="glyphicon glyphicon-edit"></span></a>';
                $content .= '&nbsp;&nbsp;<a title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copygroup', 'gid' => $group->getGid())) . '"><span class="glyphicon glyphicon-copyright-mark"></span></a>';

                $tagclass = 'class=""';
                if (isset($_COOKIE['uscicgroupcookie'])) {
                    $cookievalue = $_COOKIE['uscicgroupcookie'];
                    if (inArray($group->getSuid() . "~" . $group->getGid(), explode("-", $cookievalue))) {
                        $tagclass = 'class="uscic-cookie-tag-active"';
                    }
                }

                $content .= '&nbsp;&nbsp;<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscicgroupcookie\',\'' . $group->getSuid() . "~" . $group->getGid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href=""><span class="glyphicon glyphicon-tag"></span></a>';

                $surveys = new Surveys();
                if ($surveys->getNumberOfSurveys() > 1) {
                    $content .= '&nbsp;&nbsp;<a id="' . $group->getName() . '_move" title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movegroup', 'gid' => $group->getGid())) . '"><span class="glyphicon glyphicon-move"></span></a>';
                }
                $content .= '&nbsp;&nbsp;<a id="' . $group->getName() . '_remove" title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removegroup', 'gid' => $group->getGid())) . '"><span class="glyphicon glyphicon-remove"></span></a>';
                $returnStr .= '<a rel="popover" id="' . $group->getName() . '_popover" data-placement="right" data-html="true" data-toggle="popover" data-trigger="hover" href="' . setSessionParams(array('page' => 'sysadmin.survey.editgroup', 'gid' => $group->getGid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= '</td><td>' . $group->getName() . '</td><td>' . $arr[$group->getTemplate()] . '</td></tr>';
                $returnStr .= $this->displayPopover("#" . $group->getName() . '_popover', $content);
            }
            $returnStr .= '</tbody></table>';
        } else {
            $returnStr = $this->displayWarning(Language::messageNoGroupsYet());
        }
        $returnStr .= '<a href="' . setSessionParams(array('page' => 'sysadmin.survey.addgroup')) . '">' . Language::labelGroupsAddNew() . '</a>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        return $returnStr;
    }

    function showEditGroup($gid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($gid);
        if ($group->getName() != "") {
            $returnStr = $this->showGroupHeader($survey, $group, Language::headerEditGroup(), $message);
        } else {
            $returnStr = $this->showGroupHeader($survey, $group, Language::headerAddGroup(), $message);
        }

        /* edit existing group */
        if ($group->getName() != "") {
            if ($_SESSION['VRFILTERMODE_GROUP'] == 0) {
                $returnStr .= $this->showEditGroupGeneral($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 1) {
                $returnStr .= $this->showEditGroupVerification($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 2) {
                $returnStr .= $this->showEditGroupLayout($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 3) {
                $returnStr .= $this->showEditGroupAssistance($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 4) {
                $returnStr .= $this->showEditGroupNavigation($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 5) {
                $returnStr .= $this->showEditGroupAccess($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 6) {
                $returnStr .= $this->showEditGroupInteractive($group);
            } elseif ($_SESSION['VRFILTERMODE_GROUP'] == 7) {
                $returnStr .= $this->showEditGroupOutput($group);
            }
        } else {
            $returnStr .= $this->showEditGroupGeneral($group);
        }

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showGroupFooter($survey);
        return $returnStr;
    }

    function showEditGroupGeneral($group) {
        $returnStr = '<form id="editform" method="post">';
        if ($group->getGid() != "") {
            $returnStr .= $this->getGroupTopTab(0);
        } else {
            $group->setTemplate(loadvarAllowHTML(SETTING_GROUP_TEMPLATE));
            if ($group->getTemplate() == TABLE_TEMPLATE_CUSTOM) {
                $group->setCustomTemplate(loadvarAllowHTML(SETTING_GROUP_CUSTOM_TEMPLATE));
            }
        }
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupgeneralres', 'gid' => $group->getGid()));
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralGroupName() . '</td><td><input type="text" class="form-control" name="' . SETTING_GROUP_NAME . '" value="' . convertHTLMEntities($group->getName(), ENT_QUOTES) . '"></td></tr>';
        $opendir = opendir(getBase() . DIRECTORY_SEPARATOR . "templates");
        if ($opendir) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralGroupTemplate() . '</td><td>
                            <select class="selectpicker show-tick" id="' . SETTING_GROUP_TEMPLATE . '" name="' . SETTING_GROUP_TEMPLATE . '">';
            $templates = Common::surveyTemplates();
            $current = $group->getTemplate();
            if ($current == "") {
                $current = TABLE_TEMPLATE_DEFAULT;
            }
            while (false !== ($entry = readdir($opendir))) {
                if (!is_dir($entry)) {
                    $entry = str_replace(".php", "", $entry);
                    if (inArray($entry, array_keys($templates))) {
                        $selected = "";
                        if (strtoupper($entry) == strtoupper($current)) {
                            $selected = "SELECTED";
                        }
                        $returnStr .= "<option $selected value='" . $entry . "'>" . $templates[$entry] . "</option>";
                    }
                }
            }
            $returnStr .= '</select>    
                            </td></tr>';
        }

        if ($group->getTemplate() == TABLE_TEMPLATE_CUSTOM) {
            $returnStr .= "<tr id=customtemplate><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
        } else {
            $returnStr .= "<tr id=customtemplate style='display: none;'><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
        }
        $returnStr .= '<td colspan=4><textarea style="width: 700px;" rows=20 class="form-control autocomplete" name="' . SETTING_GROUP_CUSTOM_TEMPLATE . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getCustomTemplate(), ENT_QUOTES)) . '</textarea></td></tr>';

        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                    $("#' . SETTING_GROUP_TEMPLATE . '").change(function (e) {
                                                        if (this.value == "' . TABLE_TEMPLATE_CUSTOM . '") {
                                                            $("#customtemplate").show();                                                           
                                                        }  
                                                        else {                                                            
                                                            $("#customtemplate").hide();
                                                        }                                                        
                                                    });
                                                    })';
        $returnStr .= "</script>";

        if (Config::xiExtension()) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralGroupXiTemplate() . '</td><td>
                            <select class="selectpicker show-tick" id="' . SETTING_GROUP_XI_TEMPLATE . '" name="' . SETTING_GROUP_XI_TEMPLATE . '">';
            $current = $group->getXiTemplate();
            if (file_exists(getBase() . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "xitemplates.php")) {
                $xitemplates = file_get_contents(getBase() . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "xitemplates.php", "r");
                ;
                $list = explode(");", $xitemplates);
                foreach ($list as $l) {
                    if (contains($l, " new Template")) {
                        $sub = explode("=", $l);
                        $selected = "";
                        $entry = trim(str_replace("\$", "", $sub[0]));
                        if (strtoupper($entry) == strtoupper($current)) {
                            $selected = "SELECTED";
                        }
                        $returnStr .= "<option $selected value='" . $entry . "'>" . $entry . "</option>";
                    }
                }
            }
            $returnStr .= '</select>    
                            </td></tr>';
        }

        $returnStr .= '</table></div>';

        if ($group->getName() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditGroupLayout($group) {
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgrouplayoutres', 'gid' => $group->getGid()));
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->displayColorPicker();

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }
        /* header/footer setting */
        $returnStr .= $this->getGroupTopTab(2);
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsHeader() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_HEADER . '" name="' . SETTING_PAGE_HEADER . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getPageHeader(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsFooter() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_FOOTER . '" name="' . SETTING_PAGE_FOOTER . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getPageFooter(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutOverall() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditButtonAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_BUTTON_ALIGNMENT, $group->getButtonAlignment(), true) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_BUTTON_FORMATTING, $group->getButtonFormatting(), true) . "</td></tr>";
        $returnStr .= '</table></div>';

        if (inArray($group->getTemplate(), array_keys(Common::surveyTableTemplates()))) {
            $returnStr .= '<span class="label label-default">' . Language::labelGroupEditTable() . '</span>';
            $returnStr .= "<div class='well'>";

            $returnStr .= "<table>";
            $returnStr .= '<tr><td>' . Language::labelGroupEditTableID() . '</td><td><div class="input-group"><input type="text" class="form-control" name="' . SETTING_GROUP_TABLE_ID . '" value="' . convertHTLMEntities($group->getTableID(), ENT_QUOTES) . '"><span class="input-group-addon"><i>Auto-generated if empty</i></span></div></td></tr>';


            $returnStr .= "<tr><td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
            $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, $group->getHeaderAlignment(), true) . "</td><td width=25><nobr/></td>            
                              <td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
            $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, $group->getHeaderFormatting(), true) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditHeaderFixed() . "</td>";
            $returnStr .= "<td>" . $this->displayHeaderFixed($group->getHeaderFixed(), true) . "</td><td width=25><nobr/></td>            
                               <td>" . Language::labelTypeEditHeaderScrollDisplay() . "</td>";
            $returnStr .= '<td>' . $helpstart . '<input type="text" class="form-control" name="' . SETTING_HEADER_SCROLL_DISPLAY . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($group->getHeaderScrollDisplay(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            
            $returnStr .= "<tr><td>" . Language::labelGroupEditBordered() . "</td>";
            $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_BORDERED, $group->getTableBordered(), true) . "</td><td width=25><nobr/>";
            $returnStr .= "<td>" . Language::labelGroupEditCondensed() . "</td>";
            $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_CONDENSED, $group->getTableCondensed(), true) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelGroupEditHovered() . "</td>";
            $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_HOVERED, $group->getTableHovered(), true) . "</td><td width=25><nobr/>";
            $returnStr .= "<td>" . Language::labelGroupEditStriped() . "</td>";
            $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_STRIPED, $group->getTableStriped(), true) . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditTableWidth() . "</td>";
            $returnStr .= '<td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_TABLE_WIDTH . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($group->getTableWidth(), ENT_QUOTES)) . '">' . $helpend . '</td><td width=25><nobr/></td>';
            $returnStr .= '<td>' . Language::labelTypeEditQuestionColumnWidth() . '</td><td>' . $helpstart . '<input type="text" class="form-control autocompletebasic" name="' . SETTING_QUESTION_COLUMN_WIDTH . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($group->getQuestionColumnWidth(), ENT_QUOTES)) . '">' . $helpend . '</td>';
            $returnStr .= "</tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditMobile() . "</td>";
            $returnStr .= '<td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE, $group->getTableMobile(), true) . '</td><td width=25><nobr/></td>';
            $returnStr .= '<td>' . Language::labelTypeEditMobileLabels() . '</td><td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE_LABELS, $group->getTableMobileLabels(), true) . '</td>';
            $returnStr .= "</tr>";


            // multi-column table set up            
            if (in_array($group->getTemplate(), array_keys(Common::surveyTableMultiColumnTables()))) {
                $helpend1 = '<span class="input-group-addon"><i>' . Language::labelIfEmptyDefaultHeaders() . '</i></span></div>';
                $returnStr .= "<tr><td>" . Language::labelTypeEditTableHeaders() . "</td>";
                $returnStr .= '<td>' . $helpstart . '<input placeholder="' . Language::labelIfEmptyDefaultOrderPlaceholder() . '" style="width: 200px;" type="text" class="form-control autocompletebasic" name="' . SETTING_TABLE_HEADERS . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($group->getTableHeaders(), ENT_QUOTES)) . '">' . $helpend1 . '</td>';
                $returnStr .= "<td width=25><nobr/></td><td>" . Language::labelTypeEditMultiColumnQuestion() . "</td>";
                $returnStr .= '<td>' . $this->displayMultiColumnQuestionText(SETTING_MULTICOLUMN_QUESTIONTEXT, $group->getMultiColumnQuestiontext(), true) . '</td>';
                $returnStr .= "</tr>";
            } else if (in_array($group->getTemplate(), array_keys(Common::surveyTableEnumTables()))) {
                $returnStr .= "<tr><td>" . Language::labelTypeEditFooterDisplay() . "</td>";
                $returnStr .= "<td>" . $this->displayFooterDisplay(SETTING_FOOTER_DISPLAY, $group->getFooterDisplay(), true) . "</td>"
                        . "<td colspan=3><nobr/></td></tr>";
            }

            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }
        if ($group->getType() != GROUP_SUB) {

            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutError() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutErrorPlacement() . "</td>";
            $returnStr .= "<td>" . $this->displayErrorPlacement(SETTING_ERROR_PLACEMENT, $group->getErrorPlacement(), true) . "</td><td width=25><nobr/>";
            $returnStr .= "</tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutButtons() . '</span>';
            $returnStr .= "<div class='well'>";

            $returnStr .= "<table>";
            $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td>";

            $returnStr .= "<td>" . $this->displayButton(SETTING_BACK_BUTTON, $group->getShowBackButton(), true) . "</td>
                          <td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $group->getLabelBackButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_NEXT_BUTTON, $group->getShowNextButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $group->getLabelNextButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_DK_BUTTON, $group->getShowDKButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $group->getLabelDKButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_RF_BUTTON, $group->getShowRFButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $group->getLabelRFButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_NA_BUTTON, $group->getShowNAButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $group->getLabelNAButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_UPDATE_BUTTON, $group->getShowUpdateButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $group->getLabelUpdateButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_BUTTON, $group->getShowRemarkButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $group->getLabelRemarkButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_SAVE_BUTTON, $group->getShowRemarkSaveButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $group->getLabelRemarkSaveButton()) . $helpend . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td>";
            $returnStr .= "<td>" . $this->displayButton(SETTING_CLOSE_BUTTON, $group->getShowCloseButton(), true) . "</td><td width=25><nobr/></td>            
                          <td>" . Language::labelTypeEditButtonLabel() . "</td>";
            $returnStr .= "<td>" . $helpstart . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $group->getLabelCloseButton()) . $helpend . "</td></tr>";

            $returnStr .= '</table></div>';

            $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutProgressBar() . '</span>';
            $returnStr .= "<div class='well'>";
            $returnStr .= "<table>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarShow() . "</td>";
            $returnStr .= "<td>" . $this->displayProgressbar(SETTING_PROGRESSBAR_SHOW, $group->getShowProgressBar(), true) . "</td></tr>";

            $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarFillColor() . "</td>";
            $returnStr .= '<td><div class="input-group colorpicker">
              <input name="' . SETTING_PROGRESSBAR_FILLED_COLOR . '" type="text" value="' . $this->displayTextSettingValue($group->getProgressBarFillColor()) . '" class="form-control" />
              <span class="input-group-addon"><i></i></span><i>' . $message . '</i>
              </div></td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutProgressBarWidth() . '</td><td>' . $helpstart . '<input type="text" class="form-control" name="' . SETTING_PROGRESSBAR_WIDTH . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($group->getProgressBarWidth(), ENT_QUOTES)) . '">' . $helpend . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutProgressBarValue() . '</td><td><div class="input-group"><input type="text" class="form-control" name="' . SETTING_PROGRESSBAR_VALUE . '" value="' . convertHTLMEntities($group->getProgressBarValue(), ENT_QUOTES) . '"><span class="input-group-addon">' . Language::helpProgressBarValue() . '</span></div></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        }

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showEditGroupOutput($group) {
        $returnStr = '<form id="editform" method="post">';
        if ($group->getGid() != "") {
            $returnStr .= $this->getGroupTopTab(7);
        }
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupoutputres', 'gid' => $group->getGid()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputScreendumps() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_SCREENDUMPS, $group->getScreendumpStorage(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputParadata() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_PARADATA, $group->getParadata(), true) . "</td></tr>";
        $returnStr .= '</table></div>';


        if ($group->getGid() != "") {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 200px;"></div>';
        return $returnStr;
    }

    function showEditGroupVerification($group) {

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupvalidationres', 'gid' => $group->getGid()));
        $returnStr .= $this->getGroupTopTab(1);
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfError() . "</td>";
        $returnStr .= "<td>" . $this->displayIfError(SETTING_IFERROR, $group->getIfError(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditExclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayExclusive(SETTING_GROUP_EXCLUSIVE, $group->getExclusive(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditInclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayExclusive(SETTING_GROUP_INCLUSIVE, $group->getInclusive(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditUnique() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_UNIQUE_REQUIRED, $group->getUniqueRequired(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditSame() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_SAME_REQUIRED, $group->getSameRequired(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditMinRequired() . "</td>";
        $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_GROUP_MINIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($group->getMinimumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
        $returnStr .= "<tr><td>" . Language::labelGroupEditMaxRequired() . "</td>";
        $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_GROUP_MAXIMUM_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($group->getMaximumRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
        $returnStr .= "<tr><td>" . Language::labelGroupEditExactRequired() . "</td>";
        $returnStr .= '<td>' . $helpstart . '<input name="' . SETTING_GROUP_EXACT_REQUIRED . '" type="text" value="' . $this->displayTextSettingValue($group->getExactRequired()) . '" class="form-control autocompletebasic" />' . $helpend . '</td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationMasking() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= '<tr><td>' . Language::labelTypeEditValidationCallback() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control autocompletebasic" id="' . SETTING_INPUT_MASK_CALLBACK . '" name="' . SETTING_INPUT_MASK_CALLBACK . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getInputMaskCallback(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</form>";

        return $returnStr;
    }

    function showEditGroupNavigation($group) {
        $returnStr = '<form id="editform" method="post">';
        if ($group->getGid() != "") {
            $returnStr .= $this->getGroupTopTab(4);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupnavigationres', 'gid' => $group->getGid()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingEnabled() . "</td>";
        $returnStr .= "<td>" . $this->displayKeyBoardBindingDropdown(SETTING_KEYBOARD_BINDING_ENABLED, $group->getKeyboardBindingEnabled(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingBack() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_BACK, $group->getKeyboardBindingBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNext() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NEXT, $group->getKeyboardBindingNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingDK() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_DK, $group->getKeyboardBindingDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRF() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_RF, $group->getKeyboardBindingRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNA() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NA, $group->getKeyboardBindingNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingUpdate() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_UPDATE, $group->getKeyboardBindingUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRemark() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_REMARK, $group->getKeyboardBindingRemark()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingClose() . "</td><td>" . $helpstart . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_CLOSE, $group->getKeyboardBindingClose()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNA() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA, $group->getIndividualDKRFNA(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNAInline() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA_INLINE, $group->getIndividualDKRFNAInline(), true) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditGroupInteractive($group) {

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupinteractiveres', 'gid' => $group->getGid()));
        $returnStr .= $this->getGroupTopTab(6);
        $returnStr .= '<div class="well">';
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnSubmit() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_BACK, $group->getOnBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_NEXT, $group->getOnNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_DK, $group->getOnDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_RF, $group->getOnRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_NA, $group->getOnNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_UPDATE, $group->getOnUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_LANGUAGE_CHANGE, $group->getOnLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_MODE_CHANGE, $group->getOnModeChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnVersionChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_ON_VERSION_CHANGE, $group->getOnVersionChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnClick() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_BACK, $group->getClickBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NEXT, $group->getClickNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_DK, $group->getClickDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_RF, $group->getClickRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NA, $group->getClickNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_UPDATE, $group->getClickUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_LANGUAGE_CHANGE, $group->getClickLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_MODE_CHANGE, $group->getClickModeChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditGroupAccess($group) {
        $returnStr = '<form id="editform" method="post">';
        if ($group->getGid() != "") {
            $returnStr .= $this->getGroupTopTab(5);
        }
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupaccessres', 'gid' => $group->getGid()));
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryAction(SETTING_ACCESS_REENTRY_ACTION, $group->getAccessReentryAction(), true) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentryPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryPreload(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $group->getAccessReentryRedoPreload(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletion() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionReturn(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $group->getAccessReturnAfterCompletionAction(), true) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletionPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionPreload(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $group->getAccessReturnAfterCompletionRedoPreload(), true) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showEditGroupAssistance($group) {
        $returnStr = '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroupassistanceres', 'gid' => $group->getGid()));
        $returnStr .= $this->getGroupTopTab(3);

        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowSurvey();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageExclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageInclusive(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageMinimumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageMaximumRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageExactRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageUniqueRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceSameRequired() . '</td><td>' . $helpstart . '<textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_SAME_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_SAME_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($group->getErrorMessageSameRequired(), ENT_QUOTES)) . '</textarea>' . $helpend . '</td></tr>';
        $returnStr .= '</table></div>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showRemoveGroup($gid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($gid);
        $returnStr = $this->showGroupHeader($survey, $group, Language::headerRemoveGroup(), $message);
        $returnStr .= $this->displayWarning(Language::messageRemoveGroup($group->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.removegroupres', 'gid' => $group->getGid()));
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= $this->showGroupFooter($survey);
        return $returnStr;
    }

    function showRefactorGroup($gid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($gid);
        $returnStr = $this->showGroupHeader($survey, $group, Language::headerRefactorGroup(), $message);
        $returnStr .= $this->displayWarning(Language::messageRefactorGroup($group->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.refactorgroupres', 'gid' => $group->getGid()));
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelTypeRefactor() . '</td>';
        $returnStr .= "<td><input class='form-control' type=text name=" . SETTING_NAME . " /></td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonRefactor() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showGroupFooter($survey);
        return $returnStr;
    }

    function showMoveGroup($gid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($gid);
        $returnStr = $this->showGroupHeader($survey, $group, Language::headerMoveGroup(), $message);
        $returnStr .= $this->displayWarning(Language::messageMoveGroup($group->getName()));
        $returnStr .= '<form method="post">';

        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.movegroupres', 'gid' => $group->getGid()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();

        $surveys = new Surveys();
        if ($surveys->getNumberOfSurveys() > 1) {
            $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], $_SESSION['SUID']) . '</tr>';
        }
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';
        $returnStr .= $this->showGroupFooter($survey);
        return $returnStr;
    }

    function showCopyGroup($gid, $message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $group = $survey->getGroup($gid);
        $returnStr = $this->showGroupHeader($survey, $group, Language::headerCopyGroup(), $message);
        $returnStr .= $this->displayWarning(Language::messageCopyGroup($group->getName()));
        $returnStr .= '<form method="post">';
        $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.copygroupres', 'gid' => $group->getGid()));
        $returnStr .= '<table width=100%>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID'], '') . '</tr>';
        $returnStr .= "<tr><td>" . Language::labelTypeCopySuffix() . "</td>";

        $returnStr .= "<td><select class='selectpicker show-tick' name=includesuffix>";
        $returnStr .= "<option value=" . INPUT_MASK_YES . ">" . Language::optionsInputMaskYes() . "</option>";
        $returnStr .= "<option value=" . INPUT_MASK_NO . ">" . Language::optionsInputMaskNo() . "</option>";
        $returnStr .= "</select></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '<div style="min-height: 100px; max-height: 100%;"></div>';

        $returnStr .= $this->showGroupFooter($survey);
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
            $previoustext = '<span class="pull-left">' . setSessionParamsHref(array('page' => 'sysadmin.survey.editgroup', 'gid' => $previous->getGid()), '<span class="glyphicon glyphicon-chevron-left"></span>', 'title="' . $previous->getName() . '"') . '</span>';
        }
        if ($next->getGid() != "" && $next->getGid() != $group->getGid()) {
            $nexttext = '<span class="pull-right">' . setSessionParamsHref(array('page' => 'sysadmin.survey.editgroup', 'gid' => $next->getGid()), '<span class="glyphicon glyphicon-chevron-right"></span>', 'title="' . $next->getName() . '"') . '</span>';
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
        $active = array('', '', '', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<form method="post" id="groupsidebar">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editgroup'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_group" id="vrfiltermode_group" value="' . $filter . '">';

        $group = $survey->getGroup($_SESSION['GID']);

        $returnStr .= '<div class="btn-group-sm">';
        $returnStr .= '<button title="' . Language::linkEditTooltip() . '" class="btn btn-default dropdown-toggle" data-hover="dropdown" data-toggle="dropdown"><span class="glyphicon glyphicon-edit"></span></button>';
        $returnStr .= '<ul class="dropdown-menu" role="menu">';
        $returnStr .= '<li><a ' . $active[0] . ' onclick="$(\'#vrfiltermode_group\').val(0);$(\'#groupsidebar\').submit();">' . Language::labelGeneral() . '</a></li>';
        $returnStr .= '<li><a ' . $active[5] . ' onclick="$(\'#vrfiltermode_group\').val(5);$(\'#groupsidebar\').submit();">' . Language::labelAccess() . '</a></li>';
        $returnStr .= '<li><a ' . $active[1] . '" onclick="$(\'#vrfiltermode_group\').val(1);$(\'#groupsidebar\').submit();">' . Language::labelVerification() . '</a></li>';
        $returnStr .= '<li><a ' . $active[2] . '" onclick="$(\'#vrfiltermode_group\').val(2);$(\'#groupsidebar\').submit();">' . Language::labelLayout() . '</a></li>';
        $returnStr .= '<li><a ' . $active[3] . '" onclick="$(\'#vrfiltermode_group\').val(3);$(\'#groupsidebar\').submit();">' . Language::labelAssistance() . '</a></li>';
        if ($group->getType() != GROUP_SUB) {
            $returnStr .= '<li><a ' . $active[6] . '" onclick="$(\'#vrfiltermode_group\').val(6);$(\'#groupsidebar\').submit();">' . Language::labelInteractive() . '</a></li>';
            $returnStr .= '<li><a ' . $active[4] . '" onclick="$(\'#vrfiltermode_group\').val(4);$(\'#groupsidebar\').submit();">' . Language::labelNavigation() . '</a></li>';
        }

        $returnStr .= '</ul>';

        $tagclass = 'class="btn btn-default"';
        if (isset($_COOKIE['uscicgroupcookie'])) {
            $cookievalue = $_COOKIE['uscicgroupcookie'];
            if (inArray($group->getSuid() . "~" . $group->getGid(), explode("-", $cookievalue))) {
                $tagclass = 'class="btn btn-default uscic-cookie-tag-active"';
            }
        }

        $returnStr .= '<a ' . $tagclass . ' onclick="var res = updateCookie(\'uscicgroupcookie\',\'' . $group->getSuid() . "~" . $group->getGid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a>';
        $returnStr .= '<a title="' . Language::linkRefactorTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.refactorgroup', 'gid' => $group->getGid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-registration-mark"></span></a>';
        $returnStr .= '<a title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.copygroup', 'gid' => $group->getGid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-copyright-mark"></span></a>';
        $returnStr .= '<a title="' . Language::linkMoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.movegroup', 'gid' => $group->getGid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-move"></span></a>';
        $returnStr .= '<a title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.survey.removegroup', 'gid' => $group->getGid())) . '" role="button" class="btn btn-default"><span class="glyphicon glyphicon-remove"></span></a>';
        $returnStr .= '</div>';
        $returnStr .= '</form>';
        return $returnStr;
    }

    /* settings pages */

    function showSettingsHeader($survey, $action = "", $message = "") {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container">';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.surveys'), Language::headerSurveys()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey'), $survey->getName()) . '</li>';

        if ($_SESSION['VRFILTERMODE_SETTING'] == 0) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsAccess() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 1) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsAssistance() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 2) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsData() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 3) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsLanguage() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 4) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsLayout() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 5) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsValidation() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 6) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsGeneral() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 7) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsMode() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 8) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsInteractive() . '</li>';
        } else if ($_SESSION['VRFILTERMODE_SETTING'] == 9) {
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey.settings'), Language::headerSettings()) . '</li>';
            $returnStr .= '<li class="active">' . Language::headerEditSettingsNavigation() . '</li>';
        } else {
            $returnStr .= '<li class="active">' . Language::headerSettings() . '</li>';
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

    function showSettingsList() {
        $returnStr = '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsaccess')) . '" class="list-group-item">' . Language::labelSettingsAccess() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsassistance')) . '" class="list-group-item">' . Language::labelSettingsAssistance() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingslayout')) . '" class="list-group-item">' . Language::labelSettingsLayout() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsgeneral')) . '" class="list-group-item">' . Language::labelSettingsGeneral() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsinteractive')) . '" class="list-group-item">' . Language::labelSettingsInteractive() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsmode')) . '" class="list-group-item">' . Language::labelSettingsMode() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingslanguage')) . '" class="list-group-item">' . Language::labelSettingsLanguage() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsnavigation')) . '" class="list-group-item">' . Language::labelSettingsNavigation() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsdata')) . '" class="list-group-item">' . Language::labelSettingsData() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.survey.editsettingsvalidation')) . '" class="list-group-item">' . Language::labelSettingsValidation() . '</a>';
        $returnStr .= '</div>';
        return $returnStr;
    }

    function showEditSettingsAccess($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsAccess());

        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsaccessres'));
        $returnStr .= '<span class="label label-default">' . Language::labelSettingsAccessEntry() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelSettingsAccessType() . '</td><td>';
        $returnStr .= $this->displayComboBox();
        $returnStr .= $this->displayAccessTypes($survey->getAccessType());
        $returnStr .= '</td></tr>';

        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryAction(SETTING_ACCESS_REENTRY_ACTION, $survey->getAccessReentryAction()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditAccessReentryPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryPreload(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $survey->getAccessReentryRedoPreload()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletion() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionReturn(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $survey->getAccessReturnAfterCompletionAction()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelSettingsAccessAfterCompletionPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionPreload(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $survey->getAccessReturnAfterCompletionRedoPreload()) . "</td></tr>";



        $returnStr .= "</table>";
        $returnStr .= "</div>";
        $returnStr .= '<span class="label label-default">' . Language::labelSettingsAccessTemporal() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelSettingsAccessDatesFrom() . '</td><td>';
        $returnStr .= $this->displayDateTimePicker(SETTING_ACCESS_DATES_FROM, SETTING_ACCESS_DATES_FROM, $survey->getAccessDatesFrom(), getSMSLanguagePostFix(getSMSLanguage()), "true", "false", Config::usFormatSMS());
        $returnStr .= '</td><td width=25><nobr/></td><td>' . Language::labelSettingsAccessDatesTo() . '</td><td>';
        $returnStr .= $this->displayDateTimePicker(SETTING_ACCESS_DATES_TO, SETTING_ACCESS_DATES_TO, $survey->getAccessDatesTo(), getSMSLanguagePostFix(getSMSLanguage()), "true", "false", Config::usFormatSMS());
        $returnStr .= '</td></tr>';


        $returnStr .= '<tr><td>' . Language::labelSettingsAccessTimesFrom() . '</td><td>';
        $returnStr .= $this->displayDateTimePicker(SETTING_ACCESS_TIMES_FROM, SETTING_ACCESS_TIMES_FROM, $survey->getAccessTimesFrom(), getSMSLanguagePostFix(getSMSLanguage()), "false", "true", Config::usFormatSMS(), Config::secondsSMS(), Config::minutesSMS());
        $returnStr .= '</td><td width=25><nobr/></td><td>' . Language::labelSettingsAccessTimesTo() . '</td><td>';
        $returnStr .= $this->displayDateTimePicker(SETTING_ACCESS_TIMES_TO, SETTING_ACCESS_TIMES_TO, $survey->getAccessTimesTo(), getSMSLanguagePostFix(getSMSLanguage()), "false", "true", Config::usFormatSMS(), Config::secondsSMS(), Config::minutesSMS());
        $returnStr .= '</td></tr>';

        $returnStr .= "</table>";
        $returnStr .= "</div>";


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= '</form></div>';
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsMode($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsMode());

        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $returnStr .= $message;

        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsmoderes'));
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditModeGeneral() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelSettingsModeDefault() . "</td>";
        $returnStr .= "<td>" . $this->displayModesAdmin(SETTING_DEFAULT_MODE, SETTING_DEFAULT_MODE, $survey->getDefaultMode(), "", $survey->getAllowedModes()) . "</td>";
        $returnStr .= "<tr><td>" . Language::labelSettingsModeAllowed() . "</td>";
        $returnStr .= "<td>" . $this->displayModesAdmin(SETTING_ALLOWED_MODES, SETTING_ALLOWED_MODES, $survey->getAllowedModes(), "multiple") . "</td>";
        $returnStr .= "<tr><td>" . Language::labelSettingsModeChange() . "</td>";
        $returnStr .= "<td>" . $this->displayModesChange($survey->getChangeMode()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSettingsModeReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayModeReentry($survey->getReentryMode()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSettingsModeBack() . "</td>";
        $returnStr .= "<td>" . $this->displayModeBack($survey->getBackMode()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSettingsModeAdd() . "</td>";
        $users = new Users();
        $returnStr .= "<td>" . $this->displayUsersUpdate($users->getUsers()) . "</td></tr>";
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= '</form></div>';
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsGeneral($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsGeneral());

        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';

        $returnStr .= $message;

        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsgeneralres'));
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditModeGeneral() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();

        $returnStr .= "<table>";
        $returnStr .= '<tr><td>' . Language::labelSettingsTitle() . '</td><td><input type="text" class="form-control" name="' . SETTING_TITLE . '" value="' . $survey->getTitle() . '"></td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= '</form></div>';
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsAssistance($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsAssistance());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsassistanceres'));
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditAssistanceMessages() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';

        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_EMPTY_MESSAGE . '" name="' . SETTING_EMPTY_MESSAGE . '">' . convertHTLMEntities($survey->getEmptyMessage(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_DOUBLE . '" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '">' . convertHTLMEntities($survey->getErrorMessageDouble(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INTEGER . '" name="' . SETTING_ERROR_MESSAGE_INTEGER . '">' . convertHTLMEntities($survey->getErrorMessageInteger(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_PATTERN . '" name="' . SETTING_ERROR_MESSAGE_PATTERN . '">' . convertHTLMEntities($survey->getErrorMessagePattern(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '">' . convertHTLMEntities($survey->getErrorMessageMinimumLength(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '">' . convertHTLMEntities($survey->getErrorMessageMaximumLength(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '">' . convertHTLMEntities($survey->getErrorMessageMinimumWords(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '">' . convertHTLMEntities($survey->getErrorMessageMaximumWords(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectMinimum(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectMaximum(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectExact(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectInvalidSubset(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '">' . convertHTLMEntities($survey->getErrorMessageSelectInvalidSet(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_RANGE . '" name="' . SETTING_ERROR_MESSAGE_RANGE . '">' . convertHTLMEntities($survey->getErrorMessageRange(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '">' . convertHTLMEntities($survey->getErrorMessageMaximumCalendar(), ENT_QUOTES) . '</textarea></td></tr>';


        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageEnumeratedEntered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '" name="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '">' . convertHTLMEntities($survey->getErrorMessageEnumeratedEntered(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageSetOfEnumeratedEntered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED . '" name="' . SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED . '">' . convertHTLMEntities($survey->getErrorMessageSetOfEnumeratedEntered(), ENT_QUOTES) . '</textarea></td></tr>';

        /* comparison */
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '">' . convertHTLMEntities($survey->getErrorMessageComparisonEqualTo(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualToIgnoreCase() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '">' . convertHTLMEntities($survey->getErrorMessageComparisonEqualToIgnoreCase(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '">' . convertHTLMEntities($survey->getErrorMessageComparisonNotEqualTo(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualToIgnoreCase() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '">' . convertHTLMEntities($survey->getErrorMessageComparisonNotEqualToIgnoreCase(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreaterEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '">' . convertHTLMEntities($survey->getErrorMessageComparisonGreaterEqualTo(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreater() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '">' . convertHTLMEntities($survey->getErrorMessageComparisonGreater(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmallerEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '">' . convertHTLMEntities($survey->getErrorMessageComparisonSmallerEqualTo(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmaller() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '">' . convertHTLMEntities($survey->getErrorMessageComparisonSmaller(), ENT_QUOTES) . '</textarea></td></tr>';

        /* inline */
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineAnswered(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineExclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineInclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineMinimumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineMaximumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInlineExactRequired(), ENT_QUOTES)) . '</textarea></td></tr>';

        /* group */
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageExclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '" name="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageInclusive(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageMinimumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageMaximumRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageExactRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageUniqueRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceSameRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_ERROR_MESSAGE_SAME_REQUIRED . '" name="' . SETTING_ERROR_MESSAGE_SAME_REQUIRED . '">' . $this->displayTextSettingValue(convertHTLMEntities($survey->getErrorMessageSameRequired(), ENT_QUOTES)) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceLoginError() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control' . $tinymce . '" id="' . SETTING_LOGIN_ERROR . '" name="' . SETTING_LOGIN_ERROR . '">' . convertHTLMEntities($survey->getLoginError(), ENT_QUOTES) . '</textarea></td></tr>';

        $returnStr .= '</table></div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= '</form></div>';
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsData($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsData());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsdatares'));
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<span class="label label-default">' . Language::labelDataVisibility() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHidden() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN, $survey->getHidden()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenPaperVersion() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_PAPER_VERSION, $survey->getHiddenPaperVersion()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenRouting() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_ROUTING, $survey->getHiddenRouting()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditGeneralHiddenTranslation() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_TRANSLATION, $survey->getHiddenTranslation()) . "</td></tr>";

        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelDataStorage() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $current = $survey->getStoreLocation();
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        $hide = "";

        if ($current == 1) {
            $hide = "style='display: none;'";
        }
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputLocationStore() . "</td>";
        $returnStr .= "<td>" . $this->displayStoreLocation(SETTING_DATA_STORE_LOCATION, $survey->getStoreLocation()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td " . $hide . " id='store1'>" . Language::labelVariableExternal() . "</td><td " . $hide . " id='store2'>" . $helpstart . "<input type=text class='form-control' name='" . SETTING_DATA_STORE_LOCATION_EXTERNAL . "' value='" . $this->displayTextSettingValue(convertHTLMEntities($survey->getStoreLocationExternal(), ENT_QUOTES)) . "'>" . $helpend . "</td></td>";
        $returnStr .= "</tr>";

        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                    $("#' . SETTING_DATA_STORE_LOCATION . '").change(function (e) {
                                                        if (this.value > 1) {
                                                            $("#store1").show();
                                                            $("#store2").show();
                                                        }  
                                                        else {                                                        
                                                            $("#store1").hide();
                                                            $("#store2").hide();
                                                        }
                                                    });
                                                    })';
        $returnStr .= "</script>";


        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputInputMask() . "</td>";
        $returnStr .= "<td>" . $this->displayDataInputMask(SETTING_DATA_INPUTMASK, $survey->getDataInputMask()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputScreendumps() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_SCREENDUMPS, $survey->getScreendumpStorage()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputParadata() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_PARADATA, $survey->getParadata()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelDataEncryptionKey() . "</td>";
        $returnStr .= "<td><input type=text class='form-control' name='" . SETTING_DATA_ENCRYPTION_KEY . "' value='" . convertHTLMEntities($survey->getDataEncryptionKey(), ENT_QUOTES) . "'></td></tr>";

        $returnStr .= "<tr><td>" . Language::labelDataKeepOnly() . "</td>";
        $returnStr .= "<td>" . $this->displayDataKeepOnly(SETTING_DATA_KEEP_ONLY, $survey->getDataKeepOnly()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelDataKeep() . "</td>";
        $returnStr .= "<td>" . $this->displayDataKeep(SETTING_DATA_KEEP, $survey->getDataKeep()) . "</td></tr>";
        $returnStr .= '</table></div>';


        $returnStr .= '<span class="label label-default">' . Language::labelDataFormat() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= "<tr><td>" . Language::labelSkipVariable() . "</td>";
        $returnStr .= "<td>" . $this->displayDataSkip(SETTING_DATA_SKIP, $survey->getDataSkipVariable()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelSkipVariablePostFix() . "</td><td><input type=text class='form-control' name='" . SETTING_DATA_SKIP_POSTFIX . "' value='" . convertHTLMEntities($survey->getDataSkipVariablePostFix(), ENT_QUOTES) . "'></td></td>";
        $returnStr .= "</tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputSetOfEnumerated() . "</td>";
        $returnStr .= "<td>" . $this->displaySetOfEnumeratedOutput(SETTING_OUTPUT_SETOFENUMERATED, $survey->getOutputSetOfEnumeratedBinary()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOutputValueLabelWidth() . "</td>";
        $returnStr .= "<td>" . $this->displayValueLabelWidth(SETTING_OUTPUT_VALUELABEL_WIDTH, $survey->getOutputValueLabelWidth()) . "</td></tr>";


        $returnStr .= '</table></div>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= "</form>";
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsValidation($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsValidation());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsvalidationres'));
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationResponse() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfEmpty() . "</td>";
        $returnStr .= "<td>" . $this->displayIfEmpty(SETTING_IFEMPTY, $survey->getIfEmpty()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIfError() . "</td>";
        $returnStr .= "<td>" . $this->displayIfError(SETTING_IFERROR, $survey->getIfError()) . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationMasking() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditTextInputMaskEnable() . "</td>";
        $returnStr .= "<td>" . $this->displayInputMaskEnabled($survey->getInputMaskEnabled()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditTextInputMaskPlaceholder() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_INPUT_MASK_PLACEHOLDER . "' type='text' class='form-control autocompletebasic' value='" . $survey->getInputMaskPlaceholder() . "'></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationGroup() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditExclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayExclusive(SETTING_GROUP_EXCLUSIVE, $survey->getExclusive()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditInclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_INCLUSIVE, $survey->getInclusive()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditUnique() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_UNIQUE_REQUIRED, $survey->getUniqueRequired()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditSame() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_SAME_REQUIRED, $survey->getSameRequired()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditMinRequired() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_GROUP_MINIMUM_REQUIRED . "' type='text' class='form-control autocompletebasic' value='" . $survey->getMinimumRequired() . "'></td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditMaxRequired() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_GROUP_MAXIMUM_REQUIRED . "' type='text' class='form-control autocompletebasic' value='" . $survey->getMaximumRequired() . "'></td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditExactRequired() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_GROUP_EXACT_REQUIRED . "' type='text' class='form-control autocompletebasic' value='" . $survey->getExactRequired() . "'></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditValidationAssignment() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelValidateAssignment() . "</td>";
        $returnStr .= "<td>" . $this->displayValidateAssignment(SETTING_VALIDATE_ASSIGNMENT, $survey->getValidateAssignment()) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditChecks() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelApplyChecks() . "</td>";
        $returnStr .= "<td>" . $this->displayApplyChecks(SETTING_APPLY_CHECKS, $survey->getApplyChecks()) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= "</form></div>";
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsInteractive($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsInteractive());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsinteractiveres'));
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditInteractiveTexts() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelTypeEditInteractiveExtraJavascript() . '</td><td><textarea style="width: 100%;" rows=15 class="form-control autocompletebasic" name="' . SETTING_SCRIPTS . '">' . convertHTLMEntities($survey->getScripts(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnSubmit() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_BACK, $survey->getOnBack()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_NEXT, $survey->getOnNext()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_DK, $survey->getOnDK()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_RF, $survey->getOnRF()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_NA, $survey->getOnNA()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_UPDATE, $survey->getOnUpdate()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_LANGUAGE_CHANGE, $survey->getOnLanguageChange()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $this->displayOnSubmit(SETTING_ON_MODE_CHANGE, $survey->getOnModeChange()) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditOnClick() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnBack() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_BACK, $survey->getClickBack()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNext() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NEXT, $survey->getClickNext()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnDK() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_DK, $survey->getClickDK()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnRF() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_RF, $survey->getClickRF()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnNA() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_NA, $survey->getClickNA()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnUpdate() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_UPDATE, $survey->getClickUpdate()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnLanguageChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_LANGUAGE_CHANGE, $survey->getClickLanguageChange()) . $helpend . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditOnModeChange() . "</td><td>" . $helpstart . $this->displayOnSubmit(SETTING_CLICK_MODE_CHANGE, $survey->getClickModeChange()) . $helpend . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= '</form></div>';
        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsLanguage($message = "") {

        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsLanguage());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingslanguageres'));
        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLanguageGeneral() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayComboBox();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelSettingsLanguageDefault() . "</td>";
        $returnStr .= "<td>" . $this->displayLanguagesAdmin(SETTING_DEFAULT_LANGUAGE, SETTING_DEFAULT_LANGUAGE, $survey->getDefaultLanguage(getSurveyMode()), true, false, false, "", $survey->getAllowedLanguages(getSurveyMode())) . "</td>";
        $returnStr .= "<tr><td>" . Language::labelSettingsLanguageAllowed() . "</td>";
        $returnStr .= "<td>" . $this->displayLanguagesAdmin(SETTING_ALLOWED_LANGUAGES, SETTING_ALLOWED_LANGUAGES, $survey->getAllowedLanguages(getSurveyMode()), true, false, false, "multiple") . "</td>";
        $returnStr .= "<tr><td>" . Language::labelSettingsLanguageChange() . "</td>";
        $returnStr .= "<td>" . $this->displayLanguagesChange($survey->getChangeLanguage(getSurveyMode())) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSettingsLanguageReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayLanguageReentry($survey->getReentryLanguage(getSurveyMode())) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSettingsLanguageBack() . "</td>";
        $returnStr .= "<td>" . $this->displayLanguageBack($survey->getBackLanguage(getSurveyMode())) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSettingsLanguageAdd() . "</td>";
        $users = new Users();
        $returnStr .= "<td>" . $this->displayUsersUpdate($users->getUsers()) . "</td></tr>";
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= "</form></div>";

        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsNavigation($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsLayout());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingsnavigationres'));

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditNavigation() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingEnabled() . "</td>";
        $returnStr .= "<td>" . $this->displayKeyBoardBindingDropdown(SETTING_KEYBOARD_BINDING_ENABLED, $survey->getKeyboardBindingEnabled()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingBack() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_BACK, $survey->getKeyboardBindingBack()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNext() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NEXT, $survey->getKeyboardBindingNext()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingDK() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_DK, $survey->getKeyboardBindingDK()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRF() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_RF, $survey->getKeyboardBindingRF()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingNA() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NA, $survey->getKeyboardBindingNA()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingUpdate() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_UPDATE, $survey->getKeyboardBindingUpdate()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingRemark() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_REMARK, $survey->getKeyboardBindingRemark()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditKeyboardBindingClose() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_CLOSE, $survey->getKeyboardBindingClose()) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNA() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA, $survey->getIndividualDKRFNA()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNASingle() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA_SINGLE, $survey->getIndividualDKRFNASingle()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditIndividualDKRFNAInline() . "</td><td>" . $this->displayIndividualDKRFNA(SETTING_DKRFNA_INLINE, $survey->getIndividualDKRFNAInline()) . "</td></tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditTimeout() . "</td><td>" . $this->displayTimeout(SETTING_TIMEOUT, $survey->getTimeout()) . "</td></tr>";
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$(document).ready(function() {
                            $("#' . SETTING_TIMEOUT . '").change(function() {
                                    if ($(this).val() == 1) {
                                        $(".timeoutclass").show();
                                    }
                                    else {
                                        $(".timeoutclass").hide();
                                    }
                                });
                        });';
        $returnStr .= "</script>";

        $helpstart = '<div class="input-group">';
        $message = Language::labelTypeEditTimeoutLengthNone();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';
        $timeoutclass = " class='timeoutclass'";
        $timeoutstyle = "";
        if ($survey->getTimeout() != TIMEOUT_YES) {
            $timeoutstyle = " style='display: none;'";
        }
        $returnStr .= "<tr " . $timeoutclass . $timeoutstyle . "><td>" . Language::labelTypeEditTimeoutLength() . "</td><td>" . $helpstart . "<input class='form-control autocompletebasic' type=text name='" . SETTING_TIMEOUT_LENGTH . "' value='" . $survey->getTimeoutLength() . "' />" . $helpend . "</td></tr>";
        $returnStr .= "<tr " . $timeoutclass . $timeoutstyle . "><td>" . Language::labelTypeEditTimeoutTitle() . "</td><td><input class='form-control autocompletebasic' type=text name='" . SETTING_TIMEOUT_TITLE . "' value='" . $survey->getTimeoutTitle() . "' /></td></tr>";
        $returnStr .= "<tr " . $timeoutclass . $timeoutstyle . "><td>" . Language::labelTypeEditTimeoutAliveButton() . "</td><td><input class='form-control autocompletebasic' type=text name='" . SETTING_TIMEOUT_ALIVE_BUTTON . "' value='" . $survey->getTimeoutAliveButton() . "' /></td></tr>";
        $returnStr .= "<tr " . $timeoutclass . $timeoutstyle . "><td>" . Language::labelTypeEditTimeoutLogoutButton() . "</td><td><input class='form-control autocompletebasic' type=text name='" . SETTING_TIMEOUT_LOGOUT_BUTTON . "' value='" . $survey->getTimeoutLogoutButton() . "' /></td></tr>";
        $returnStr .= "<tr " . $timeoutclass . $timeoutstyle . "><td>" . Language::labelTypeEditTimeoutLogoutURL() . "</td><td><input class='form-control autocompletebasic' type=text name='" . SETTING_TIMEOUT_LOGOUT . "' value='" . $survey->getTimeoutLogoutURL() . "' /></td></tr>";
        $returnStr .= "<tr " . $timeoutclass . $timeoutstyle . "><td>" . Language::labelTypeEditTimeoutRedirectURL() . "</td><td><input class='form-control autocompletebasic' type=text name='" . SETTING_TIMEOUT_REDIRECT . "' value='" . $survey->getTimeoutRedirectURL() . "' /></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= "</form></div>";

        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    function showEditSettingsLayout($message = "") {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showSettingsHeader($survey, Language::headerEditSettingsLayout());
        $returnStr .= $this->getSurveyTopTab($_SESSION['VRFILTERMODE_SURVEY']);
        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.survey.editsettingslayoutres'));

        /* header/footer setting */
        $returnStr .= '<span class="label label-default">' . Language::labelSettingsPage() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTemplate() . '</td><td>';
        $templateoptions = array();
        if ($dh = opendir("display/templates")) {
            while (($file = readdir($dh)) !== false) {
                if (startsWith(strtolower($file), TEMPLATE_NAME) && contains($file, "_")) {
                    $templateoptions[] = $file;
                }
            }
            closedir($dh);
        }

        $returnStr .= "<select class='selectpicker show-tick' name='" . SETTING_SURVEY_TEMPLATE . "'>";
        $templatenames = Common::surveyOverallTemplates();
        $custom = 1;
        foreach ($templateoptions as $t) {
            $end = substr($t, strpos($t, "_") + 1, 1);
            $selected = '';
            if ($survey->getTemplate() == $end) {
                $selected = "SELECTED";
            }
            if (isset($templatenames[$end])) {
                $name = $templatenames[$end];
            } else {
                $name = "Custom" . $custom;
                $custom++;
            }
            $returnStr .= "<option " . $selected . " value=" . $end . ">" . $name . "</option>";
        }
        $returnStr .= "</select></td></tr>";
        $user = new User($_SESSION['URID']);
        $tinymce = '';
        if ($user->hasHTMLEditor()) {
            $returnStr .= $this->getTinyMCE();
            $tinymce = ' tinymce';
        }

        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsHeader() . '</td><td><textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_HEADER . '" name="' . SETTING_PAGE_HEADER . '">' . convertHTLMEntities($survey->getPageHeader(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= '<tr><td valign=top style="width: 15%;">' . Language::labelSettingsFooter() . '</td><td><textarea style="width: 100%;" rows=6 class="form-control autocompletebasic' . $tinymce . '" id="' . SETTING_PAGE_FOOTER . '" name="' . SETTING_PAGE_FOOTER . '">' . convertHTLMEntities($survey->getPageFooter(), ENT_QUOTES) . '</textarea></td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutOverall() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditQuestionAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_QUESTION_ALIGNMENT, $survey->getQuestionAlignment()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditQuestionFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_QUESTION_FORMATTING, $survey->getQuestionFormatting()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditAnswerAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_ANSWER_ALIGNMENT, $survey->getAnswerAlignment()) . "</td><td width=25><nobr/></td>
                        <td>" . Language::labelTypeEditAnswerFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_ANSWER_FORMATTING, $survey->getAnswerFormatting()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditButtonAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_BUTTON_ALIGNMENT, $survey->getButtonAlignment()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_BUTTON_FORMATTING, $survey->getButtonFormatting()) . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutError() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutErrorPlacement() . "</td>";
        $returnStr .= "<td>" . $this->displayErrorPlacement(SETTING_ERROR_PLACEMENT, $survey->getErrorPlacement()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "</tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutButtons() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayColorPicker();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditBackButton() . "</td>";

        $returnStr .= "<td>" . $this->displayButton(SETTING_BACK_BUTTON, $survey->getShowBackButton()) . "</td>
                      <td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, $survey->getLabelBackButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditNextButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NEXT_BUTTON, $survey->getShowNextButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, $survey->getLabelNextButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditDKButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_DK_BUTTON, $survey->getShowDKButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, $survey->getLabelDKButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRFButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_RF_BUTTON, $survey->getShowRFButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, $survey->getLabelRFButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditNAButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NA_BUTTON, $survey->getShowNAButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, $survey->getLabelNAButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditUpdateButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_UPDATE_BUTTON, $survey->getShowUpdateButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, $survey->getLabelUpdateButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_BUTTON, $survey->getShowRemarkButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, $survey->getLabelRemarkButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditRemarkSaveButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_SAVE_BUTTON, $survey->getShowRemarkSaveButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, $survey->getLabelRemarkSaveButton()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditCloseButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_CLOSE_BUTTON, $survey->getShowCloseButton()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, $survey->getLabelCloseButton()) . "</td></tr>";

        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSection() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditSectionHeader() . "</td>";
        $returnStr .= "<td>" . $this->displaySectionHeader(SETTING_SHOW_SECTION_HEADER, $survey->getShowSectionHeader()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditSectionFooter() . "</td>";
        $returnStr .= "<td>" . $this->displaySectionFooter(SETTING_SHOW_SECTION_FOOTER, $survey->getShowSectionFooter()) . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutProgressBar() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarShow() . "</td>";
        $returnStr .= "<td>" . $this->displayProgressbar(SETTING_PROGRESSBAR_SHOW, $survey->getShowProgressBar()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarType() . "</td>";
        $returnStr .= "<td>" . $this->displayProgressbarType(SETTING_PROGRESSBAR_TYPE, $survey->getProgressBarType()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutProgressBarFillColor() . "</td>";
        $returnStr .= '<td><div class="input-group colorpicker">
          <input name=' . SETTING_PROGRESSBAR_FILLED_COLOR . ' type="text" value="' . $survey->getProgressBarFillColor() . '" class="form-control autocompletebasic" />
          <span class="input-group-addon"><i></i></span>
          </div></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutProgressBarWidth() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_PROGRESSBAR_WIDTH . '" value="' . convertHTLMEntities($survey->getProgressBarWidth(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelSettingsTable() . '</span>';
        $returnStr .= "<div class='well'>";

        $returnStr .= "<table>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, $survey->getHeaderAlignment()) . "</td><td width=25><nobr/></td>            
                      <td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, $survey->getHeaderFormatting()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditHeaderFixed() . "</td>";
        $returnStr .= "<td>" . $this->displayHeaderFixed($survey->getHeaderFixed()) . "</td><td width=25><nobr/></td>            
                               <td>" . Language::labelTypeEditHeaderScrollDisplay() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control" name="' . SETTING_HEADER_SCROLL_DISPLAY . '" value="' . convertHTLMEntities($survey->getHeaderScrollDisplay(), ENT_QUOTES) . '"></td></tr>';


        $returnStr .= "<tr><td>" . Language::labelGroupEditBordered() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_BORDERED, $survey->getTableBordered()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelGroupEditCondensed() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_CONDENSED, $survey->getTableCondensed()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelGroupEditHovered() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_HOVERED, $survey->getTableHovered()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelGroupEditStriped() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_STRIPED, $survey->getTableStriped()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditTableWidth() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control autocompletebasic" name="' . SETTING_TABLE_WIDTH . '" value="' . convertHTLMEntities($survey->getTableWidth(), ENT_QUOTES) . '"></td><td width=25><nobr/></td>';
        $returnStr .= "<td>" . Language::labelTypeEditQuestionColumnWidth() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control autocompletebasic" name="' . SETTING_QUESTION_COLUMN_WIDTH . '" value="' . convertHTLMEntities($survey->getQuestionColumnWidth(), ENT_QUOTES) . '"></td><td width=25><nobr/></td></tr>';

        $returnStr .= "<tr><td>" . Language::labelTypeEditMobile() . "</td>";
        $returnStr .= '<td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE, $survey->getTableMobile()) . '</td><td width=25><nobr/></td>';
        $returnStr .= '<td>' . Language::labelTypeEditMobileLabels() . '</td><td>' . $this->displayMobileLabels(SETTING_TABLE_MOBILE_LABELS, $survey->getTableMobileLabels()) . '</td>';
        $returnStr .= "</tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditMultiColumnQuestion() . "</td>";
        $returnStr .= '<td>' . $this->displayMultiColumnQuestionText(SETTING_MULTICOLUMN_QUESTIONTEXT, $survey->getMultiColumnQuestiontext()) . '</td><td width=25><nobr/></td>';
        $returnStr .= "</tr>";

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutEnumeratedTemplate() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedTemplate(SETTING_ENUMERATED_ORIENTATION, $survey->getEnumeratedDisplay()) . "</td><td width=25><nobr/>";
        $returnStr .= "<td>" . Language::labelTypeEditEnumeratedOrder() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedOrder(SETTING_ENUMERATED_ORDER, $survey->getEnumeratedOrder()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedSplit() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_SPLIT, $survey->getEnumeratedSplit()) . "</td><td width=25><nobr/></td>
                            <td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, $survey->getHeaderAlignment()) . "</td></tr>";

        $returnStr .= "<tr><td>" . Language::labelTypeEditEnumeratedFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, $survey->getHeaderFormatting()) . "</td><td width=25><nobr/></td>";

        $returnStr .= "<td>" . Language::labelGroupEditBordered() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_BORDERED, $survey->getEnumeratedBordered()) . "</td></tr>";

        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
        $returnStr .= "<td>" . $this->displayEnumeratedTextBox(SETTING_ENUMERATED_TEXTBOX, $survey->getEnumeratedTextBox()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelTypeEditLayoutEnumeratedLabel() . "</td><td>" . $this->displayEnumeratedLabel(SETTING_ENUMERATED_LABEL, $survey->getEnumeratedLabel()) . "</td></tr>";
        $returnStr .= "<tr><td>" . Language::labelSliderTextBoxBefore() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getEnumeratedTextBoxLabel(), ENT_QUOTES)) . '"></td>';
        $returnStr .= "<td width=25><nobr/></td><td>" . Language::labelSliderTextBoxAfter() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_ENUMERATED_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getEnumeratedTextBoxPostText(), ENT_QUOTES)) . '"></td>';
        $returnStr .= '</tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDateTimePicker() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTimeFormat() . '</td>';
        $returnStr .= '<td><input style="width: 350px;" type="text" class="form-control autocompletebasic" name="' . SETTING_TIME_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getTimeFormat(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateFormat() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control autocompletebasic" name="' . SETTING_DATE_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getDateFormat(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutDateTimeFormat() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control autocompletebasic" name="' . SETTING_DATETIME_FORMAT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getDateTimeFormat(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSlider() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutOrientation() . "</td>";
        $returnStr .= "<td>" . $this->displayOrientation(SETTING_SLIDER_ORIENTATION, $survey->getSliderOrientation()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelTypeEditLayoutTooltip() . "</td>";
        $returnStr .= "<td>" . $this->displayTooltip(SETTING_SLIDER_TOOLTIP, $survey->getTooltip()) . "</td></tr>";
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutSliderLabelPlacement() . '</td>';
        $returnStr .= "<td>" . $this->displaySliderPlacement(SETTING_SLIDER_LABEL_PLACEMENT, $survey->getSliderLabelPlacement()) . "</td><td width=25><nobr/></td>
                        <td>" . Language::labelTypeEditLayoutStep() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_INCREMENT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getIncrement(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
        $returnStr .= "<td>" . $this->displayTextBox(SETTING_SLIDER_TEXTBOX, $survey->getTextBox()) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelSliderTextBoxBefore() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getTextBoxLabel(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= "<tr><td>" . Language::labelSliderTextBoxAfter() . '</td><td><input type="text" class="form-control autocompletebasic" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getTextBoxPostText(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= '</tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDropdown() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td>" . Language::labelTypeEditLayoutDropdownDefault() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control autocompletebasic" name="' . SETTING_COMBOBOX_DEFAULT . '" value="' . $this->displayTextSettingValue(convertHTLMEntities($survey->getComboBoxNothingLabel(), ENT_QUOTES)) . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= "</form></div>";

        $returnStr .= $this->showSettingsFooter($survey);
        return $returnStr;
    }

    /* tools menu */

    function showTools() {
        $survey = new Survey($_SESSION['SUID']);
        $returnStr = $this->showToolsHeader();

        $returnStr .= '<div class="well" style="background-color:white;">';
        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.batcheditor')) . '" class="list-group-item">' . Language::linkBatchEditor() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.check')) . '" class="list-group-item">' . Language::linkChecker() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.compile')) . '" class="list-group-item">' . Language::linkCompiler() . '</a>';
        if (Config::xiExtension()) {
            $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.xicompile')) . '" class="list-group-item">' . Language::linkXiCompiler() . '</a>';
        }
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.test')) . '" class="list-group-item">' . Language::linkTester() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.issues')) . '" class="list-group-item">' . Language::linkReported() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.export')) . '" class="list-group-item">' . Language::linkExport() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.import')) . '" class="list-group-item">' . Language::linkImport() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.tools.clean')) . '" class="list-group-item">' . Language::linkCleaner() . '</a>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showToolsHeader($type = "", $message = "") {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools'), Language::linkTools()) . '</li>';
        if ($type != "") {
            $returnStr .= '<li class="active">' . $type . '</li>';
        }
        $returnStr .= '</ol>';
        $returnStr .= $message;
        return $returnStr;
    }

    function showClean($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsCleaner());
        $returnStr .= $content;

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {
            $returnStr .= '<form method="post">';
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.cleanres'));
            $returnStr .= '<span class="label label-default">' . Language::labelToolsCleanSurveys() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            foreach ($surveys as $survey) {
                $returnStr .= '<tr><td><label><input name=clean[] value="' . $survey->getSuid() . '" type="checkbox">' . $survey->getName() . '</label></td></tr>';
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelToolsCleanDataType() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td><label><input name=datatype[] value="' . SURVEY_EXECUTION_MODE_NORMAL . '" type="checkbox">' . Language::labelDataTypeNormal() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name=datatype[] value="' . SURVEY_EXECUTION_MODE_TEST . '" type="checkbox">' . Language::labelDataTypeTest() . '</label></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelToolsCleanPeriod() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelToolsCleanFrom() . ': </td><td>' . $this->displayDateTimePicker("from", "from", "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td><td>' . Language::labelToolsCleanTo() . ': </td><td>' . $this->displayDateTimePicker("to", "to", "", getSMSLanguagePostFix(getSMSLanguage()), "true", "true", "false") . '</td></tr>';

            $returnStr .= '</table>';

            $returnStr .= '</div>';


            $returnStr .= '<input type="submit" class="btn btn-default" ' . confirmAction(language::messageRemoveData(), 'REMOVE') . ' value="' . Language::buttonClean() . '"/>';
            $returnStr .= '</form>';
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showCheck($content) {
        $returnStr = $this->showToolsHeader(Language::headerToolsChecker());
        $returnStr .= $content;
        $survey = new Survey($_SESSION['SUID']);
        $user = new User($_SESSION['URID']);

        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="sysadmin.tools.check">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
        $returnStr .= "</form>";

        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.checkres'));
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';

        $returnStr .= '<tr><td>' . Language::labelOutputDocumentationSurvey() . '</td><td>' . $this->displaySurveys("survey", "survey", $_SESSION['SUID'], '', "onchange='document.getElementById(\"" . SMS_POST_SURVEY . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();'") . '</tr>';
        $returnStr .= '<tr><td>' . Language::labelOutputDocumentationMode() . '</td><td>' . $this->displayModesAdmin("surveymode", "surveymode", getSurveyMode(), "", implode("~", $user->getModes()), "onchange='document.getElementById(\"" . SMS_POST_MODE . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();'") . '</tr>';

        /* language dropdown */
        $langs = explode("~", $user->getLanguages($_SESSION['SUID'], getSurveyMode()));
        $default = $survey->getDefaultLanguage(getSurveyMode());
        if (!inArray($default, $langs)) {
            $langs[] = $default;
        }
        $returnStr .= '<tr><td>' . Language::labelOutputDocumentationLanguage() . '</td><td>' . $this->displayLanguagesAdmin("surveylanguage", "surveylanguage", getSurveyLanguage(), true, false, true, "", implode("~", $langs)) . '</tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_ROUTING . '" type="checkbox">' . Language::labelToolsCheckRouting() . '</label></td></tr>';
        $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_SECTION . '" type="checkbox">' . Language::labelToolsCompileSections() . '</label></td></tr>';
        $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_TYPE . '" type="checkbox">' . Language::labelToolsCompileTypes() . '</label></td></tr>';
        $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_VARIABLE . '" type="checkbox">' . Language::labelToolsCompileVariables() . '</label></td></tr>';
        $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_GROUP . '" type="checkbox">' . Language::labelToolsCompileGroup() . '</label></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonCheck() . '"/>';
        $returnStr .= '</form>';
        $returnStr .= '</div>';
//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showBatchEditor($message = "", $variablecookievalue = "", $typecookievalue = "", $groupcookievalue = "", $sectioncookievalue = "") {

        $returnStr = $this->showToolsHeader(Language::headerToolsBatchEditor(), $message);
        $returnStr .= $content;


        // look at cookies
        if ($typecookievalue == "" && isset($_COOKIE['uscictypecookie'])) {
            $typecookievalue = $_COOKIE['uscictypecookie'];
        }
        if ($variablecookievalue == "" && isset($_COOKIE['uscicvariablecookie'])) {
            $variablecookievalue = $_COOKIE['uscicvariablecookie'];
        }
        if ($sectioncookievalue == "" && isset($_COOKIE['uscicsectioncookie'])) {
            $sectioncookievalue = $_COOKIE['uscicsectioncookie'];
        }
        if ($groupcookievalue == "" && isset($_COOKIE['uscicgroupcookie'])) {
            $groupcookievalue = $_COOKIE['uscicgroupcookie'];
        }

        if ($variablecookievalue == "" && $sectioncookievalue == "" && $groupcookievalue == "" && $typecookievalue == "") {
            $returnStr .= $this->displayInfo(Language::messageToolsBatchEditorNotFound());
        } else {

            if ($_SESSION['VRFILTERMODE_BATCH'] == 0 && $variablecookievalue != "") {
                $returnStr .= $this->showToolsBatchEditorVariables($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
            } elseif ($_SESSION['VRFILTERMODE_BATCH'] == 1 && $typecookievalue != "") {
                $returnStr .= $this->showToolsBatchEditorTypes($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
            } elseif ($_SESSION['VRFILTERMODE_BATCH'] == 2 && $groupcookievalue != "") {
                $returnStr .= $this->showToolsBatchEditorGroups($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
            } elseif ($_SESSION['VRFILTERMODE_BATCH'] == 3 && $sectioncookievalue != "") {
                $returnStr .= $this->showToolsBatchEditorSections($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
            } else {
                if ($typecookievalue != "") {
                    $returnStr .= $this->showToolsBatchEditorTypes($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
                } else if ($groupcookievalue != "") {
                    $returnStr .= $this->showToolsBatchEditorGroups($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
                } else if ($sectioncookievalue != "") {
                    $returnStr .= $this->showToolsBatchEditorSections($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
                } else if ($variablecookievalue != "") {
                    $returnStr .= $this->showToolsBatchEditorVariables($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue);
                }
            }
        }
//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function getToolsBatchEditorTopTab($filter, $variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue) {
        $returnStr = '';
        $returnStr .= '<form method="post" id="batcheditorbar">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditor'));
        $returnStr .= '<input type="hidden" name="vrfiltermode_batch" id="vrfiltermode_batch" value="' . $filter . '">';
        $returnStr .= "</form>";
        $active = array_fill(0, 16, 'label-primary');
        $active[$filter] = 'label-default';
        if ($variablecookievalue) {
            if ($filter == 0) {
                $returnStr .= ' <span class="label label-default">' . Language::labelVariables() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_batch\').val(0);$(\'#batcheditorbar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[0] . '">' . Language::labelVariables() . '</span></a>';
            }
        }
        if ($typecookievalue) {
            if ($filter == 1) {
                $returnStr .= ' <span class="label label-default">' . Language::labelTypes() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_batch\').val(1);$(\'#batcheditorbar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[1] . '">' . Language::labelTypes() . '</span></a>';
            }
        }
        if ($groupcookievalue) {
            if ($filter == 2) {
                $returnStr .= ' <span class="label label-default">' . Language::labelGroups() . '</span>';
            } else {
                $returnStr .= ' <a onclick="$(\'#vrfiltermode_batch\').val(2);$(\'#batcheditorbar\').submit(); return false;" style="text-decoration:none;"><span class="label ' . $active[2] . '">' . Language::labelGroups() . '</span></a>';
            }
        }
        
        return $returnStr;
    }

    function showToolsBatchEditorVariables($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue) {
        $returnStr = $this->getToolsBatchEditorTopTab(0, $variablecookievalue != "", $sectioncookievalue != "", $groupcookievalue != "", $typecookievalue != "");

        $returnStr .= "<form method='post' id='reload' name='reload'>";
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditor'));
        $returnStr .= "</form>";

        $returnStr .= '<form id=actionform name=actionform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditorres'));
        $returnStr .= "<input type=hidden name=batchaction id=batchaction />";
        $returnStr .= "<input type=hidden name=vrfiltermode_batch id=vrfiltermode_batch value=0 />";
        $returnStr .= '<div class="well">';

        $vars = explode("-", $variablecookievalue);
        $returnStr1 = '';
        foreach ($vars as $var) {
            $varsplit = explode("~", $var);
            $survey = new Survey($varsplit[0]);
            $v = $survey->getVariableDescriptive($varsplit[1]);
            if ($v->getName() != "") {
                $returnStr1 .= '<tr>';
                $returnStr1 .= '<td>';
                $returnStr1 .= "<input class='selectedbox' name=selected[] type='checkbox' value='" . $var . "'>";
                $returnStr1 .= '</td>';
                $returnStr1 .= '<td>' . $v->getName() . '</td>';
//                $returnStr1 .= '<td>' . $v->getDescription() . '</td>';
                $returnStr1 .= '<tr>';
            }
        }
        if ($returnStr1 == "") {
            $returnStr .= $this->displayInfo(Language::messageToolsBatchEditorNoVariablesFound());
        } else {

            $returnStr .= $this->displayComboBox();
            $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">
                         <div class="col-xs-6 col-md-3">';

            $returnStr .= '<span class="label label-default">' . Language::labelToolsBatchEditorVariables() . '</span>';
            $returnStr .= '<div style="background-color: white;" class="well">';

            $returnStr .= '<table class="table table-bordered table-striped">';
            $returnStr .= '<tr>';
            $returnStr .= "<th><nobr/></th>";
            $returnStr .= "<th>" . Language::labelTypeEditGeneralName() . "</th>";
            $returnStr .= '</tr>';

            $returnStr .= $returnStr1;

            $returnStr .= '</table>';
            $returnStr .= $this->displayCookieScripts();

            $returnStr .= "<script type=text/javascript>
                           function selectAll() {
                            $('.selectedbox').prop('checked', true);
                           };
                           function unselectAll() {
                            $('.selectedbox').prop('checked', false);
                           };
                           </script>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='selectAll();' value='Select all'/>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='unselectAll();' value='Unselect all'/>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='clearCookie(\"uscicvariablecookie\"); $(\"#reload\").submit();' value='" . Language::buttonClear() . "'/>";

            $returnStr .= "</div>";


            $returnStr .= '</div>';

            // actions
            $returnStr .= '<div class="col-xs-12 col-md-9">';
            $returnStr .= '<span class="label label-default">' . Language::labelToolsBatchEditorActions() . '</span>';
            $returnStr .= '<div style="background-color: white;" class="well">';
            $returnStr .= '<span class="label label-default">' . Language::labelEdit() . '</span>';
            $returnStr .= '<div class="well">';


            $returnStr .= '<ul class="nav nav-pills nav-justified" role="tablist">';
            $returnStr .= '<li class="active"><a href="#general" role="tab" data-toggle="tab">' . Language::labelGeneral() . '</a></li>';
            $returnStr .= '<li><a href="#access" role="tab" data-toggle="tab">' . Language::labelAccess() . '</a></li>';
            $returnStr .= '<li><a href="#verification" role="tab" data-toggle="tab">' . Language::labelVerification() . '</a></li>';
            $returnStr .= '<li><a href="#display" role="tab" data-toggle="tab">' . Language::labelLayout() . '</a></li>';
            $returnStr .= '<li><a href="#assistance" role="tab" data-toggle="tab">' . Language::labelAssistance() . '</a></li>';
            $returnStr .= '<li><a href="#interactive" role="tab" data-toggle="tab">' . Language::labelInteractive() . '</a></li>';
            $returnStr .= '<li><a href="#output" role="tab" data-toggle="tab">' . Language::labelOutput() . '</a></li>';
            $returnStr .= '<li><a href="#navigation" role="tab" data-toggle="tab">' . Language::labelNavigation() . '</a></li>';
            $returnStr .= '</ul>';


            $returnStr .= $this->showToolsBatchEditorVariableTabs();

            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<table width=100%>';
            $surveys = new Surveys();
            $suid = loadvar("suid");
            if ($suid == "") {
                $suid = $_SESSION['SUID'];
                if ($suid == "") {
                    $suid = $surveys->getFirstSurvey(false);
                }
            }
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $suid) . '</tr>';
            }

            $returnStr .= '<tr><td>' . Language::labelTypeCopySection() . '</td>';
            $returnStr .= "<td>" . $this->displaySections('copysection', '', $_SESSION['SUID'], "") . "</td></tr>";

            $returnStr .= '</table>';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'copy\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelMove() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<table width=100%>';

            $surveys = new Surveys();
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID']) . '</tr>';
            }

            $returnStr .= '<tr><td>' . Language::labelTypeMoveSection() . '</td>';
            $returnStr .= "<td>" . $this->displaySections('movesection', '', $suid, "") . "</td></tr>";

            $returnStr .= '</table>';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'move\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelRemove() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'remove\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
            $returnStr .= '</div>';

            $returnStr .= '</div>';

            // close
            $returnStr .= '</div>';
            $returnStr .= '</div>';
        }

        $returnStr .= '</div>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showToolsBatchEditorVariableTabs($type = -1) {
        // tabs content
        $returnStr = '<div class="tab-content">';

        // general tab
        $returnStr .= '<div class="tab-pane active" id="general">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        if ($type == -1) {
            $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_DESCRIPTION . '_checkbox /></td><td>' . Language::labelTypeEditGeneralDescription() . '</td><td colspan=2><input type="text" class="form-control" name="' . SETTING_DESCRIPTION . '" value=""></td></tr>';
        }
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_ANSWERTYPE . '_checkbox /></td><td align=top>' . Language::labelTypeEditGeneralAnswerType() . '</td><td>';
        $returnStr .= $this->showAnswerTypes();
        $returnStr .= '</td><td id="customanswer" style="display: none;"><input type="text" placeholder="Please enter a custom function call" class="form-control" name="' . SETTING_ANSWERTYPE_CUSTOM . '" value=""></td></tr>';

        $returnStr .= '<tr id="categories" style="display: none;"><td><input type=checkbox value=1 name=' . SETTING_OPTIONS . '_checkbox /></td><td align=top>' . Language::labelTypeEditGeneralCategories() . '</td><td colspan=2><textarea style="height: 120px;" class="form-control uscic-form-control-admin" name="' . SETTING_OPTIONS . '"></textarea></td></tr>';

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ARRAY . "_checkbox /></td><td>" . Language::labelTypeEditGeneralArray() . "</td>";
        $returnStr .= "<td colspan=2>" . $this->displayIsArray() . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEEP . "_checkbox /></td><td>" . Language::labelTypeEditGeneralKeep() . "</td>";
        $returnStr .= "<td colspan=2>" . $this->displayIsKeep() . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        // access tab
        $returnStr .= '<div class="tab-pane" id="access">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_REENTRY_ACTION . "_checkbox /></td><td>" . Language::labelTypeEditAccessReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryAction(SETTING_ACCESS_REENTRY_ACTION, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_REENTRY_PRELOAD_REDO . "_checkbox /></td><td>" . Language::labelTypeEditAccessReentryPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryPreload(SETTING_ACCESS_REENTRY_PRELOAD_REDO, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION . "_checkbox /></td><td>" . Language::labelSettingsAccessAfterCompletion() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionReturn(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO . "_checkbox /></td><td>" . Language::labelSettingsAccessAfterCompletionPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionPreload(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, '', true) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';


        // verification tab
        $returnStr .= '<div class="tab-pane" id="verification">';
        $returnStr .= '<div class="well">';

        $returnStr .= '<span class="label label-default">' . Language::labelGeneral() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_IFEMPTY . "_checkbox /></td><td>" . Language::labelTypeEditIfEmpty() . "</td>";
        $returnStr .= "<td>" . $this->displayIfEmpty(SETTING_IFEMPTY, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_IFERROR . "_checkbox /></td><td>" . Language::labelTypeEditIfError() . "</td>";
        $returnStr .= "<td>" . $this->displayIfError(SETTING_IFERROR, '', true) . "</td></tr>";
        $returnStr .= '</table></div>';


        $returnStr .= '<span class="label label-default">' . Language::labelComparison() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_EQUAL_TO . "_checkbox /></td><td>" . Language::labelTypeEditComparisonEqualTo() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_EQUAL_TO . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_NOT_EQUAL_TO . "_checkbox /></td><td>" . Language::labelTypeEditComparisonNotEqualTo() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_NOT_EQUAL_TO . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_GREATER_EQUAL_TO . "_checkbox /></td><td>" . Language::labelTypeEditComparisonGreaterOrEqualThan() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_GREATER_EQUAL_TO . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_GREATER . "_checkbox /></td><td>" . Language::labelTypeEditComparisonGreaterThan() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_GREATER . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_SMALLER_EQUAL_TO . "_checkbox /></td><td>" . Language::labelTypeEditComparisonLessOrEqualThan() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_SMALLER_EQUAL_TO . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_SMALLER . "_checkbox /></td><td>" . Language::labelTypeEditComparisonLessThan() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_SMALLER . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MAXIMUM_CALENDAR . "_checkbox /></td><td>" . Language::labelTypeEditCalendarMaximum() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MAXIMUM_CALENDAR . "' type='text' class='form-control' value=''></td></tr>";

        $returnStr .= '</table></div>';


        $returnStr .= '<span class="label label-default">' . Language::labelStringOpen() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MINIMUM_LENGTH . "_checkbox /></td><td>" . Language::labelTypeEditTextMinimumLength() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MINIMUM_LENGTH . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MAXIMUM_LENGTH . "_checkbox /></td><td>" . Language::labelTypeEditTextMaximumLength() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MAXIMUM_LENGTH . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MINIMUM_WORDS . "_checkbox /></td><td>" . Language::labelTypeEditTextMinimumWords() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MINIMUM_WORDS . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MAXIMUM_WORDS . "_checkbox /></td><td>" . Language::labelTypeEditTextMaximumWords() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MAXIMUM_WORDS . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_PATTERN . "_checkbox /></td><td>" . Language::labelTypeEditTextPattern() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_PATTERN . "' type='text' class='form-control' value=''></td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE . "_checkbox /></td><td>" . Language::labelTypeEditComparisonEqualToIgnoreCase() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . "_checkbox /></td><td>" . Language::labelTypeEditComparisonNotEqualToIgnoreCase() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelRange() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MINIMUM_RANGE . "_checkbox /></td><td>" . Language::labelTypeEditRangeMinimum() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MINIMUM_RANGE . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MAXIMUM_RANGE . "_checkbox /></td><td>" . Language::labelTypeEditRangeMaximum() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MAXIMUM_RANGE . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_OTHER_RANGE . "_checkbox /></td><td>" . Language::labelTypeEditRangeOther() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_OTHER_RANGE . "' type='text' class='form-control' value=''></td></tr>";

        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelSetOfEnumerated() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MINIMUM_SELECTED . "_checkbox /></td><td>" . Language::labelTypeEditTextMinimumSelected() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MINIMUM_SELECTED . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_MAXIMUM_SELECTED . "_checkbox /></td><td>" . Language::labelTypeEditTextMaximumSelected() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_MAXIMUM_SELECTED . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_EXACT_SELECTED . "_checkbox /></td><td>" . Language::labelTypeEditTextExactSelected() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_EXACT_SELECTED . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelInline() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_INLINE_EXCLUSIVE . "_checkbox /></td><td>" . Language::labelInlineEditExclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayExclusive(SETTING_INLINE_EXCLUSIVE, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_INLINE_INCLUSIVE . "_checkbox /></td><td>" . Language::labelInlineEditInclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_INLINE_INCLUSIVE, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_INLINE_MINIMUM_REQUIRED . "_checkbox /></td><td>" . Language::labelInlineEditMinRequired() . "</td>";
        $returnStr .= '<td><input name="' . SETTING_INLINE_MINIMUM_REQUIRED . '" type="text" value="" class="form-control" /></td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_INLINE_MAXIMUM_REQUIRED . "_checkbox /></td><td>" . Language::labelInlineEditMaxRequired() . "</td>";
        $returnStr .= '<td><input name="' . SETTING_INLINE_MAXIMUM_REQUIRED . '" type="text" value="" class="form-control" /></td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_INLINE_EXACT_REQUIRED . "_checkbox /></td><td>" . Language::labelInlineEditExactRequired() . "</td>";
        $returnStr .= '<td><input name="' . SETTING_INLINE_EXACT_REQUIRED . '" type="text" value="" class="form-control" /></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelInputMask() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_INPUT_MASK_ENABLED . "_checkbox /></td><td>" . Language::labelTypeEditTextInputMaskEnable() . "</td>";
        $returnStr .= "<td>" . $this->displayInputMaskEnabled('', true) . "</td></tr>";
        $returnStr .= "<tr id='row1'><td><input type=checkbox value=1 name=" . SETTING_INPUT_MASK . "_checkbox /></td><td>" . Language::labelTypeEditTextInputMask() . "</td>";
        $returnStr .= "<td style='width: 150px; max-width: 150px;'>" . $this->displayInputMasks(SETTING_INPUT_MASK, '', true) . "</td>";
        $returnStr .= "<td id='inputmaskcell' style='display: none;'><input name='" . SETTING_INPUT_MASK_CUSTOM . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= "<tr id='row2'><td><input type=checkbox value=1 name=" . SETTING_INPUT_MASK_PLACEHOLDER . "_checkbox /></td><td>" . Language::labelTypeEditTextInputMaskPlaceholder() . "</td>";
        $returnStr .= "<td><input name='" . SETTING_INPUT_MASK_PLACEHOLDER . "' type='text' class='form-control' value=''></td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '<script type="text/javascript">
                            $( document ).ready(function() {
                                                $("#' . SETTING_INPUT_MASK . '").change(function (e) {
                                                    if (this.value == "' . INPUTMASK_CUSTOM . '") {
                                                        $("#inputmaskcell").show();
                                                    }   
                                                    else {
                                                        $("#inputmaskcell").hide();
                                                    }
                                                });
                                                })';
        $returnStr .= '</script>';

        $returnStr .= '</div>';
        $returnStr .= '</div>';


        // display tab
        $returnStr .= '<div class="tab-pane" id="display">';
        $returnStr .= '<div class="well">';

        /* header/footer setting */
        $returnStr .= '<span class="label label-default">' . Language::labelSettingsPage() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_PAGE_HEADER . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelSettingsHeader() . '</td><td><textarea style="width: 100%;" rows=6 class="form-control" name="' . SETTING_PAGE_HEADER . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_PAGE_FOOTER . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelSettingsFooter() . '</td><td><textarea style="width: 100%;" rows=6 class="form-control" name="' . SETTING_PAGE_FOOTER . '"></textarea></td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutOverall() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_QUESTION_ALIGNMENT . "_checkbox /></td><td>" . Language::labelTypeEditQuestionAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_QUESTION_ALIGNMENT, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_QUESTION_FORMATTING . "_checkbox /></td>
                      <td>" . Language::labelTypeEditQuestionFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_QUESTION_FORMATTING, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ANSWER_ALIGNMENT . "_checkbox /></td><td>" . Language::labelTypeEditAnswerAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_ANSWER_ALIGNMENT, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_ANSWER_FORMATTING . "_checkbox /></td>
                        <td>" . Language::labelTypeEditAnswerFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_ANSWER_FORMATTING, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_BUTTON_ALIGNMENT . "_checkbox /></td><td>" . Language::labelTypeEditButtonAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_BUTTON_ALIGNMENT, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_BUTTON_FORMATTING . "_checkbox /></td>            
                      <td>" . Language::labelTypeEditButtonFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_BUTTON_FORMATTING, '') . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutError() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ERROR_PLACEMENT . "_checkbox /></td><td>" . Language::labelTypeEditLayoutErrorPlacement() . "</td>";
        $returnStr .= "<td>" . $this->displayErrorPlacement(SETTING_ERROR_PLACEMENT, '') . "</td><td width=25><nobr/>";
        $returnStr .= "</tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutButtons() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayColorPicker();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_BACK_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditBackButton() . "</td>";

        $returnStr .= "<td>" . $this->displayButton(SETTING_BACK_BUTTON, '') . "</td>
                      </tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_BACK_BUTTON_LABEL . "_checkbox /></td>           
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_NEXT_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditNextButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NEXT_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_NEXT_BUTTON_LABEL . "_checkbox /></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_DK_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditDKButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_DK_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_DK_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_RF_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditRFButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_RF_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_RF_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_NA_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditNAButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NA_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_NA_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_UPDATE_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditUpdateButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_UPDATE_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_UPDATE_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditRemarkButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_SAVE_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditRemarkSaveButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_SAVE_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_SAVE_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_CLOSE_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditCloseButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_CLOSE_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_CLOSE_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSection() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SHOW_SECTION_HEADER . "_checkbox /></td><td>" . Language::labelTypeEditSectionHeader() . "</td>";
        $returnStr .= "<td>" . $this->displaySectionHeader(SETTING_SHOW_SECTION_HEADER, '') . "</td></tr>
                        <tr><td><input type=checkbox value=1 name=" . SETTING_SHOW_SECTION_FOOTER . "_checkbox /></td>            
                      <td>" . Language::labelTypeEditSectionFooter() . "</td>";
        $returnStr .= "<td>" . $this->displaySectionFooter(SETTING_SHOW_SECTION_FOOTER, '') . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutProgressBar() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_PROGRESSBAR_SHOW . "_checkbox /></td><td>" . Language::labelTypeEditLayoutProgressBarShow() . "</td>";
        $returnStr .= "<td>" . $this->displayProgressbar(SETTING_PROGRESSBAR_SHOW, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_PROGRESSBAR_FILLED_COLOR . "_checkbox /></td><td>" . Language::labelTypeEditLayoutProgressBarFillColor() . "</td>";
        $returnStr .= '<td><div class="input-group colorpicker">
          <input name=' . SETTING_PROGRESSBAR_FILLED_COLOR . ' type="text" value="' . '' . '" class="form-control" />
          <span class="input-group-addon"><i></i></span>
          </div></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_PROGRESSBAR_WIDTH . '_checkbox /></td><td>' . Language::labelTypeEditLayoutProgressBarWidth() . '</td><td><input type="text" class="form-control" name="' . SETTING_PROGRESSBAR_WIDTH . '" value="' . '' . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutSlider() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SLIDER_ORIENTATION . "_checkbox /></td><td>" . Language::labelTypeEditLayoutOrientation() . "</td><td>" . $this->displayOrientation(SETTING_SLIDER_ORIENTATION, '') . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SLIDER_TOOLTIP . "_checkbox /></td><td>" . Language::labelTypeEditLayoutTooltip() . "</td><td>" . $this->displayTooltip(SETTING_SLIDER_TOOLTIP, '') . "</td></tr>";
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_SLIDER_TEXTBOX . '_checkbox /></td><td>' . Language::labelTypeEditLayoutTextBox() . '</td><td>' . $this->displayTextBox(SETTING_SLIDER_TEXTBOX, '') . '</td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SLIDER_INCREMENT . "_checkbox /></td><td>" . Language::labelTypeEditLayoutStep() . '</td><td><input style="width: 350px;"  type="text" class="form-control" name="' . SETTING_SLIDER_INCREMENT . '" value="' . '' . '"></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_SLIDER_LABEL_PLACEMENT . '_checkbox /></td><td>' . Language::labelTypeEditLayoutSliderLabelPlacement() . '</td><td>' . $this->displaySliderPlacement(SETTING_SLIDER_LABEL_PLACEMENT, '') . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SLIDER_LABELS . "_checkbox /></td><td>" . Language::labelTypeEditLayoutSliderLabels() . '</td><td><input type="text" style="width: 350px;" class="form-control" name="' . SETTING_SLIDER_LABELS . '" value="' . '' . '"></td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SLIDER_TEXTBOX_LABEL . "_checkbox /></td><td>" . Language::labelSliderTextBoxBefore() . '</td><td><input type="text" style="width: 350px;" class="form-control" name="' . SETTING_SLIDER_TEXTBOX_LABEL . '" value="' . '' . '"></td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SLIDER_TEXTBOX_POSTTEXT . "_checkbox /></td><td>" . Language::labelSliderTextBoxAfter() . '</td><td><input type="text" style="width: 350px;" class="form-control" name="' . SETTING_SLIDER_TEXTBOX_POSTTEXT . '" value="' . '' . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutEnumerated() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ENUMERATED_ORIENTATION . "_checkbox /></td><td>" . Language::labelTypeEditLayoutEnumeratedTemplate() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedTemplate(SETTING_ENUMERATED_ORIENTATION, '') . "</td></tr><tr>";
        $returnStr .= "<td><input type=checkbox value=1 name=" . SETTING_ENUMERATED_ORDER . "_checkbox /></td><td>" . Language::labelTypeEditEnumeratedOrder() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedOrder(SETTING_ENUMERATED_ORDER, '') . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ENUMERATED_SPLIT . "_checkbox /></td><td>" . Language::labelTypeEditEnumeratedSplit() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_SPLIT, '') . "</td></tr><tr>
                            <td><input type=checkbox value=1 name=" . SETTING_HEADER_ALIGNMENT . "_checkbox /></td><td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HEADER_FORMATTING . "_checkbox /></td><td>" . Language::labelTypeEditEnumeratedFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, '') . "</td></tr><tr>";

        $returnStr .= "<td><input type=checkbox value=1 name=" . SETTING_ENUMERATED_BORDERED . "_checkbox /></td><td>" . Language::labelGroupEditBordered() . "</td>";
        $returnStr .= "<td>" . $this->displayEnumeratedSplit(SETTING_ENUMERATED_BORDERED, '') . "</td></tr>";

        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_ENUMERATED_TEXTBOX . '_checkbox /></td><td>' . Language::labelTypeEditLayoutTextBox() . '</td>';
        $returnStr .= "<td>" . $this->displayEnumeratedTextBox(SETTING_ENUMERATED_TEXTBOX, '') . "</td></tr><tr>";
        $returnStr .= "<td><input type=checkbox value=1 name=" . SETTING_ENUMERATED_TEXTBOX_LABEL . "_checkbox /></td><td>" . Language::labelTypeEditLayoutEnumeratedLabel() . "</td><td>" . $this->displayEnumeratedLabel(SETTING_ENUMERATED_LABEL, '') . "</td></tr>";

        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_ENUMERATED_TEXTBOX_LABEL . '_checkbox /></td><td>' . Language::labelEnumeratedTextBoxBefore() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control" name="' . SETTING_ENUMERATED_TEXTBOX_LABEL . '" value="' . '' . '"></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_ENUMERATED_TEXTBOX_POSTTEXT . '_checkbox /></td><td>' . Language::labelEnumeratedTextBoxAfter() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control" name="' . SETTING_ENUMERATED_TEXTBOX_POSTTEXT . '" value="' . '' . '"></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_ENUMERATED_RANDOMIZER . '_checkbox /></td><td>' . Language::labelTypeEditEnumeratedRandomizer() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control" name="' . SETTING_ENUMERATED_RANDOMIZER . '" value="' . '' . '"></td></tr>';

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutDateTimePicker() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_TIME_FORMAT . '_checkbox /></td><td>' . Language::labelTypeEditLayoutTimeFormat() . '</td>';
        $returnStr .= '<td><input style="width: 350px;" type="text" class="form-control" name="' . SETTING_TIME_FORMAT . '" value="' . '' . '"></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_DATE_FORMAT . '_checkbox /></td><td>' . Language::labelTypeEditLayoutDateFormat() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control" name="' . SETTING_DATE_FORMAT . '" value="' . '' . '"></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_DATETIME_FORMAT . '_checkbox /></td><td>' . Language::labelTypeEditLayoutDateTimeFormat() . '</td>';
        $returnStr .= '<td><input style="width: 350px;"  type="text" class="form-control" name="' . SETTING_DATETIME_FORMAT . '" value="' . '' . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<div class="tab-pane" id="assistance">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<span class="label label-default">' . Language::labelGeneral() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_EMPTY_MESSAGE . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceEmptyMessage() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_EMPTY_MESSAGE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_DOUBLE . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageDouble() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_DOUBLE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INTEGER . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInteger() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INTEGER . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_RANGE . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageRange() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_RANGE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreaterEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageGreater() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_GREATER . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmallerEqualTo() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageSmaller() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_SMALLER . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxCalendar() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR . '"></textarea></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelStringOpen() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageEqualToIgnoreCase() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageNotEqualToIgnoreCase() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_PATTERN . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessagePattern() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_PATTERN . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_LENGTH . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxLength() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMinWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_WORDS . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageMaxWords() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_WORDS . '"></textarea></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelSetOfEnumerated() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageEnumeratedEntered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMinSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_SELECT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageMaxSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_SELECT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageExactSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_SELECT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSubSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageInvalidSelect() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INVALID_SELECT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelTypeEditAssistanceErrorMessageSetOfEnumeratedEntered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED . '"></textarea></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';


        /* inline */
        $returnStr .= '<span class="label label-default">' . Language::labelInline() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineAnswered() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_ANSWERED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMinRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineMaxRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '_checkbox /></td><td valign=top style="width: 20%;">' . Language::labelTypeEditAssistanceErrorMessageInlineExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';


        // interactive tab
        $returnStr .= '<div class="tab-pane" id="interactive">';
        $returnStr .= '<div class="well">';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditInteractiveTexts() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '_checkbox /></td><td>' . Language::labelTypeEditInteractiveInlineText() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control" name="' . SETTING_JAVASCRIPT_WITHIN_ELEMENT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_JAVASCRIPT_WITHIN_PAGE . '_checkbox /></td><td>' . Language::labelTypeEditinteractivePageText() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control" name="' . SETTING_JAVASCRIPT_WITHIN_PAGE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_SCRIPTS . '_checkbox /></td><td>' . Language::labelTypeEditInteractiveExtraJavascript() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control" name="' . SETTING_SCRIPTS . '"></textarea></td></tr>';
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditInteractiveStyle() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_STYLE_WITHIN_ELEMENT . '_checkbox /></td><td>' . Language::labelTypeEditInteractiveInlineStyle() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control" name="' . SETTING_STYLE_WITHIN_ELEMENT . '"></textarea></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_STYLE_WITHIN_PAGE . '_checkbox /></td><td>' . Language::labelTypeEditInteractivePageStyle() . '</td><td><textarea style="min-width: 500px; width: 100%; min-height: 80px;" class="form-control" name="' . SETTING_STYLE_WITHIN_PAGE . '"></textarea></td></tr>';
        $returnStr .= '</table></div>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<div class="tab-pane" id="output">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<span class="label label-default">' . Language::labelDataOutput() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= '<table>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HIDDEN . "_checkbox /></td><td>" . Language::labelTypeEditGeneralHidden() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HIDDEN_PAPER_VERSION . "_checkbox /></td><td>" . Language::labelTypeEditGeneralHiddenPaperVersion() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_PAPER_VERSION, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HIDDEN_ROUTING . "_checkbox /></td><td>" . Language::labelTypeEditGeneralHiddenRouting() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_ROUTING, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HIDDEN_TRANSLATION . "_checkbox /></td><td>" . Language::labelTypeEditGeneralHiddenTranslation() . "</td>";
        $returnStr .= "<td>" . $this->displayHidden(SETTING_HIDDEN_TRANSLATION, '', true) . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelDataStorage() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_DATA_INPUTMASK . "_checkbox /></td><td>" . Language::labelTypeEditOutputInputMask() . "</td>";
        $returnStr .= "<td>" . $this->displayDataInputMask(SETTING_DATA_INPUTMASK, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_SCREENDUMPS . "_checkbox /></td><td>" . Language::labelTypeEditOutputScreendumps() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_SCREENDUMPS, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_PARADATA . "_checkbox /></td><td>" . Language::labelTypeEditOutputParadata() . "</td>";
        $returnStr .= "<td>" . $this->displayScreendumps(SETTING_PARADATA, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_DATA_KEEP . "_checkbox /></td><td>" . Language::labelDataKeep() . "</td>";
        $returnStr .= "<td>" . $this->displayDataKeep(SETTING_DATA_KEEP, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_DATA_SKIP . "_checkbox /></td><td>" . Language::labelSkipVariable() . "</td>";
        $returnStr .= "<td>" . $this->displayDataSkip(SETTING_DATA_SKIP, '', true) . "</td><td width=25><nobr/></td>";
        $returnStr .= "<td>" . Language::labelSkipVariablePostFix() . "</td><td><input type=text class='form-control' name='" . SETTING_DATA_SKIP_POSTFIX . "' value=''></td></td>";
        $returnStr .= "</tr>";


        $returnStr .= '</table></div>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<div class="tab-pane" id="navigation">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_ENABLED . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingEnabled() . "</td>";
        $returnStr .= "<td>" . $this->displayKeyBoardBindingDropdown(SETTING_KEYBOARD_BINDING_ENABLED, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_BACK . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingBack() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_BACK) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_NEXT . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingNext() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NEXT) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_DK . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingDK() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_DK) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_RF . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingRF() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_RF) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_NA . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingNA() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NA) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_UPDATE . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingUpdate() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_UPDATE) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_REMARK . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingRemark() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_REMARK) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_CLOSE . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingClose() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_CLOSE) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<input onclick="$(\'#batchaction\').val(\'edit\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</div>';
        return $returnStr;
    }

    function showToolsBatchEditorTypes($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue) {
        $returnStr = $this->getToolsBatchEditorTopTab(1, $variablecookievalue != "", $sectioncookievalue != "", $groupcookievalue != "", $typecookievalue != "");

        $returnStr .= "<form method='post' id='reload' name='reload'>";
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditor'));
        $returnStr .= "</form>";

        $returnStr .= '<form id=actionform name=actionform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditorres'));
        $returnStr .= "<input type=hidden name=batchaction id=batchaction />";
        $returnStr .= "<input type=hidden name=vrfiltermode_batch id=vrfiltermode_batch value=1 />";
        $returnStr .= '<div class="well">';

        $types = explode("-", $typecookievalue);
        $returnStr1 = '';
        foreach ($types as $typ) {
            $varsplit = explode("~", $typ);
            $survey = new Survey($varsplit[0]);
            $v = $survey->getType($varsplit[1]);
            if ($v->getName() != "") {
                $returnStr1 .= '<tr>';
                $returnStr1 .= '<td>';
                $returnStr1 .= "<input class='selectedboxtype' name=selected[] type='checkbox' value='" . $typ . "'>";
                $returnStr1 .= '</td>';
                $returnStr1 .= '<td>' . $v->getName() . '</td>';
                $returnStr1 .= '<tr>';
            }
        }
        if ($returnStr1 == "") {
            $returnStr .= $this->displayInfo(Language::messageToolsBatchEditorNoTypesFound());
        } else {

            $returnStr .= $this->displayComboBox();
            $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">
                         <div class="col-xs-6 col-md-3">';

            $returnStr .= '<span class="label label-default">' . Language::labelToolsBatchEditorTypes() . '</span>';
            $returnStr .= '<div style="background-color: white;" class="well">';

            $returnStr .= '<table class="table table-bordered table-striped">';
            $returnStr .= '<tr>';
            $returnStr .= "<th><nobr/></th>";
            $returnStr .= "<th>" . Language::labelTypeEditGeneralName() . "</th>";
            $returnStr .= '</tr>';

            $returnStr .= $returnStr1;

            $returnStr .= '</table>';
            $returnStr .= $this->displayCookieScripts();
            $returnStr .= "<script type=text/javascript>
                           function selectAllTypes() {
                            $('.selectedboxtype').prop('checked', true);
                           };
                           function unselectAllTypes() {
                            $('.selectedboxtype').prop('checked', false);
                           };
                           </script>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='selectAllTypes();' value='Select all'/>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='unselectAllTypes();' value='Unselect all'/>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='clearCookie(\"uscictypecookie\"); $(\"#reload\").submit();' value='" . Language::buttonClear() . "'/>";

            $returnStr .= "</div>";


            $returnStr .= '</div>';

            // actions
            $returnStr .= '<div class="col-xs-12 col-md-9">';
            $returnStr .= '<span class="label label-default">' . Language::labelToolsBatchEditorActions() . '</span>';
            $returnStr .= '<div style="background-color: white;" class="well">';
            $returnStr .= '<span class="label label-default">' . Language::labelEdit() . '</span>';
            $returnStr .= '<div class="well">';


            $returnStr .= '<ul class="nav nav-pills nav-justified" role="tablist">';
            $returnStr .= '<li class="active"><a href="#general" role="tab" data-toggle="tab">' . Language::labelGeneral() . '</a></li>';
            $returnStr .= '<li><a href="#access" role="tab" data-toggle="tab">' . Language::labelAccess() . '</a></li>';
            $returnStr .= '<li><a href="#verification" role="tab" data-toggle="tab">' . Language::labelVerification() . '</a></li>';
            $returnStr .= '<li><a href="#display" role="tab" data-toggle="tab">' . Language::labelLayout() . '</a></li>';
            $returnStr .= '<li><a href="#assistance" role="tab" data-toggle="tab">' . Language::labelAssistance() . '</a></li>';
            $returnStr .= '<li><a href="#interactive" role="tab" data-toggle="tab">' . Language::labelInteractive() . '</a></li>';
            $returnStr .= '<li><a href="#output" role="tab" data-toggle="tab">' . Language::labelOutput() . '</a></li>';
            $returnStr .= '<li><a href="#navigation" role="tab" data-toggle="tab">' . Language::labelNavigation() . '</a></li>';
            $returnStr .= '</ul>';


            $returnStr .= $this->showToolsBatchEditorVariableTabs(1);

            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<table width=100%>';
            $surveys = new Surveys();
            $suid = loadvar("suid");
            if ($suid == "") {
                $suid = $_SESSION['SUID'];
                if ($suid == "") {
                    $suid = $surveys->getFirstSurvey(false);
                }
            }
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $suid) . '</tr>';
            }

            $returnStr .= '</table>';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'copy\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
            $returnStr .= '</div>';

            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<span class="label label-default">' . Language::labelMove() . '</span>';
                $returnStr .= '<div class="well">';
                $returnStr .= '<table width=100%>';
                $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID']) . '</tr>';
                $returnStr .= '</table>';
                $returnStr .= '<input onclick="$(\'#batchaction\').val(\'move\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
                $returnStr .= '</div>';
            }

            $returnStr .= '<span class="label label-default">' . Language::labelRemove() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'remove\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
            $returnStr .= '</div>';

            $returnStr .= '</div>';


            // close
            $returnStr .= '</div>';
            $returnStr .= '</div>';
        }

        $returnStr .= '</div>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showToolsBatchEditorGroups($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue) {
        $returnStr = $this->getToolsBatchEditorTopTab(2, $variablecookievalue != "", $sectioncookievalue != "", $groupcookievalue != "", $typecookievalue != "");

        $returnStr .= "<form method='post' id='reload' name='reload'>";
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditor'));
        $returnStr .= "</form>";

        $returnStr .= '<form id=actionform name=actionform method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditorres'));
        $returnStr .= "<input type=hidden name=batchaction id=batchaction />";
        $returnStr .= "<input type=hidden name=vrfiltermode_batch id=vrfiltermode_batch value=2 />";
        $returnStr .= '<div class="well">';

        $groups = explode("-", $groupcookievalue);
        $returnStr1 = '';
        foreach ($groups as $group) {
            $varsplit = explode("~", $group);
            $survey = new Survey($varsplit[0]);
            $v = $survey->getGroup($varsplit[1]);
            if ($v->getName() != "") {
                $returnStr1 .= '<tr>';
                $returnStr1 .= '<td>';
                $returnStr1 .= "<input class='selectedgroupbox' name=selected[] type='checkbox' value='" . $group . "'>";
                $returnStr1 .= '</td>';
                $returnStr1 .= '<td>' . $v->getName() . '</td>';
                $returnStr1 .= '<tr>';
            }
        }
        if ($returnStr1 == "") {
            $returnStr .= $this->displayInfo(Language::messageToolsBatchEditorNoGroupsFound());
        } else {

            $returnStr .= $this->displayComboBox();
            $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">
                         <div class="col-xs-6 col-md-3">';

            $returnStr .= '<span class="label label-default">' . Language::labelToolsBatchEditorGroups() . '</span>';
            $returnStr .= '<div style="background-color: white;" class="well">';

            $returnStr .= '<table class="table table-bordered table-striped">';
            $returnStr .= '<tr>';
            $returnStr .= "<th><nobr/></th>";
            $returnStr .= "<th>" . Language::labelTypeEditGeneralName() . "</th>";
            $returnStr .= '</tr>';

            $returnStr .= $returnStr1;

            $returnStr .= '</table>';
            $returnStr .= $this->displayCookieScripts();
            $returnStr .= "<script type=text/javascript>
                           function selectAllGroup() {
                            $('.selectedgroupbox').prop('checked', true);
                           };
                           function unselectAllGroup() {
                            $('.selectedgroupbox').prop('checked', false);
                           };
                           </script>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='selectAllGroup();' value='Select all'/>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='unselectAllGroup();' value='Unselect all'/>";
            $returnStr .= "<input class='btn btn-default' type=button onclick='clearCookie(\"uscicgroupcookie\"); $(\"#reload\").submit();' value='" . Language::buttonClear() . "'/>";

            $returnStr .= "</div>";


            $returnStr .= '</div>';

            // actions
            $returnStr .= '<div class="col-xs-12 col-md-9">';
            $returnStr .= '<span class="label label-default">' . Language::labelToolsBatchEditorActions() . '</span>';
            $returnStr .= '<div style="background-color: white;" class="well">';
            $returnStr .= '<span class="label label-default">' . Language::labelEdit() . '</span>';
            $returnStr .= '<div class="well">';


            $returnStr .= '<ul class="nav nav-pills nav-justified" role="tablist">';
            $returnStr .= '<li class="active"><a href="#general" role="tab" data-toggle="tab">' . Language::labelGeneral() . '</a></li>';
            $returnStr .= '<li><a href="#access" role="tab" data-toggle="tab">' . Language::labelAccess() . '</a></li>';
            $returnStr .= '<li><a href="#verification" role="tab" data-toggle="tab">' . Language::labelVerification() . '</a></li>';
            $returnStr .= '<li><a href="#display" role="tab" data-toggle="tab">' . Language::labelLayout() . '</a></li>';
            $returnStr .= '<li><a href="#assistance" role="tab" data-toggle="tab">' . Language::labelAssistance() . '</a></li>';
            $returnStr .= '<li><a href="#navigation" role="tab" data-toggle="tab">' . Language::labelNavigation() . '</a></li>';
            $returnStr .= '</ul>';


            $returnStr .= $this->showToolsBatchEditorGroupTabs(1);

            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelCopy() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<table width=100%>';
            $surveys = new Surveys();
            $suid = loadvar("suid");
            if ($suid == "") {
                $suid = $_SESSION['SUID'];
                if ($suid == "") {
                    $suid = $surveys->getFirstSurvey(false);
                }
            }
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<tr><td>' . Language::labelTypeCopySurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $suid) . '</tr>';
            }

            $returnStr .= '</table>';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'copy\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonCopy() . '"/>';
            $returnStr .= '</div>';

            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<span class="label label-default">' . Language::labelMove() . '</span>';
                $returnStr .= '<div class="well">';
                $returnStr .= '<table width=100%>';
                $returnStr .= '<tr><td>' . Language::labelTypeMoveSurvey() . '</td><td>' . $this->displaySurveys("suid", "suid", $_SESSION['SUID']) . '</tr>';
                $returnStr .= '</table>';
                $returnStr .= '<input onclick="$(\'#batchaction\').val(\'move\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonMove() . '"/>';
                $returnStr .= '</div>';
            }

            $returnStr .= '<span class="label label-default">' . Language::labelRemove() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= '<input onclick="$(\'#batchaction\').val(\'remove\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonRemove() . '"/>';
            $returnStr .= '</div>';

            $returnStr .= '</div>';


            // close
            $returnStr .= '</div>';
            $returnStr .= '</div>';
        }

        $returnStr .= '</div>';

        $returnStr .= '</form>';
        return $returnStr;
    }

    function showToolsBatchEditorGroupTabs() {
        // tabs content
        $returnStr = '<div class="tab-content">';

        // general tab
        $returnStr .= '<div class="tab-pane active" id="general">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $opendir = opendir(getBase() . DIRECTORY_SEPARATOR . "templates");
        if ($opendir) {
            $returnStr .= '<tr><td>' . Language::labelTypeEditGeneralGroupTemplate() . '</td><td>
                            <select class="selectpicker show-tick" id="' . SETTING_GROUP_TEMPLATE . '" name="' . SETTING_GROUP_TEMPLATE . '">';
            $templates = Common::surveyTemplates();
            $current = TABLE_TEMPLATE_DEFAULT;
            while (false !== ($entry = readdir($opendir))) {
                if (!is_dir($entry)) {
                    $entry = str_replace(".php", "", $entry);
                    if (inArray($entry, array_keys($templates))) {
                        $selected = "";
                        if (strtoupper($entry) == strtoupper($current)) {
                            $selected = "SELECTED";
                        }
                        $returnStr .= "<option $selected value='" . $entry . "'>" . $templates[$entry] . "</option>";
                    }
                }
            }
            $returnStr .= '</select>    
                            </td></tr>';
        }

        $returnStr .= "<tr id=customtemplate style='display: none;'><td>" . Language::labelTypeEditEnumeratedCustom() . "</td>";
        $returnStr .= '<td colspan=4><textarea style="width: 650px;" rows=20 class="form-control" name="' . SETTING_GROUP_CUSTOM_TEMPLATE . '"></textarea></td></tr>';

        $returnStr .= '</table>';

        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                    $("#' . SETTING_GROUP_TEMPLATE . '").change(function (e) {
                                                        if (this.value == "' . TABLE_TEMPLATE_CUSTOM . '") {
                                                            $("#customtemplate").show();                                                           
                                                        }  
                                                        else {                                                            
                                                            $("#customtemplate").hide();
                                                        }                                                        
                                                    });
                                                    })';
        $returnStr .= "</script>";
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        // access tab
        $returnStr .= '<div class="tab-pane" id="access">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_REENTRY_ACTION . "_checkbox /></td><td>" . Language::labelTypeEditAccessReentry() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryAction(SETTING_ACCESS_REENTRY_ACTION, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_REENTRY_PRELOAD_REDO . "_checkbox /></td><td>" . Language::labelTypeEditAccessReentryPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessReentryPreload(SETTING_ACCESS_REENTRY_PRELOAD_REDO, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION . "_checkbox /></td><td>" . Language::labelSettingsAccessAfterCompletion() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionReturn(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO . "_checkbox /></td><td>" . Language::labelSettingsAccessAfterCompletionPreload() . "</td>";
        $returnStr .= "<td>" . $this->displayAccessAfterCompletionPreload(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, '', true) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';


        // verification tab
        $returnStr .= '<div class="tab-pane" id="verification">';
        $returnStr .= '<div class="well">';

        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_IFEMPTY . "_checkbox /></td><td>" . Language::labelTypeEditIfEmpty() . "</td>";
        $returnStr .= "<td>" . $this->displayIfEmpty(SETTING_IFEMPTY, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_IFERROR . "_checkbox /></td><td>" . Language::labelTypeEditIfError() . "</td>";
        $returnStr .= "<td>" . $this->displayIfError(SETTING_IFERROR, '', true) . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_EXCLUSIVE . "_checkbox /><td>" . Language::labelGroupEditExclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayExclusive(SETTING_GROUP_EXCLUSIVE, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_INCLUSIVE . "_checkbox /><td>" . Language::labelGroupEditInclusive() . "</td>";
        $returnStr .= "<td>" . $this->displayExclusive(SETTING_GROUP_INCLUSIVE, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_UNIQUE_REQUIRED . "_checkbox /><td>" . Language::labelGroupEditUnique() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_UNIQUE_REQUIRED, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_SAME_REQUIRED . "_checkbox /><td>" . Language::labelGroupEditSame() . "</td>";
        $returnStr .= "<td>" . $this->displayInclusive(SETTING_GROUP_SAME_REQUIRED, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_MINIMUM_REQUIRED . "_checkbox /><td>" . Language::labelGroupEditMinRequired() . "</td>";
        $returnStr .= '<td><input name="' . SETTING_GROUP_MINIMUM_REQUIRED . '" type="text" value="" class="form-control" /></td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_MAXIMUM_REQUIRED . "_checkbox /><td>" . Language::labelGroupEditMaxRequired() . "</td>";
        $returnStr .= '<td><input name="' . SETTING_GROUP_MAXIMUM_REQUIRED . '" type="text" value="" class="form-control" /></td></tr>';
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_EXACT_REQUIRED . "_checkbox /><td>" . Language::labelGroupEditExactRequired() . "</td>";
        $returnStr .= '<td><input name="' . SETTING_GROUP_EXACT_REQUIRED . '" type="text" value="" class="form-control" /></td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '</div>';
        $returnStr .= '</div>';


        // display tab
        $returnStr .= '<div class="tab-pane" id="display">';
        $returnStr .= '<div class="well">';

        /* header/footer setting */
        $returnStr .= '<span class="label label-default">' . Language::labelSettingsPage() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_SURVEY_TEMPLATE . '_checkbox /></td><td>' . Language::labelTypeEditLayoutTemplate() . '</td><td>';
        $returnStr .= '<input type="text" class="form-control" name="' . SETTING_SURVEY_TEMPLATE . '" value=""></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_PAGE_HEADER . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelSettingsHeader() . '</td><td><textarea style="width: 100%;" rows=6 class="form-control" name="' . SETTING_PAGE_HEADER . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_PAGE_FOOTER . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelSettingsFooter() . '</td><td><textarea style="width: 100%;" rows=6 class="form-control" name="' . SETTING_PAGE_FOOTER . '"></textarea></td></tr>';
        $returnStr .= "</table>";
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutOverall() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_BUTTON_ALIGNMENT . "_checkbox /></td><td>" . Language::labelTypeEditButtonAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_BUTTON_ALIGNMENT, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_BUTTON_FORMATTING . "_checkbox /></td>            
                      <td>" . Language::labelTypeEditButtonFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_BUTTON_FORMATTING, '') . "</td></tr>";
        $returnStr .= '</table></div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutError() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_ERROR_PLACEMENT . "_checkbox /></td><td>" . Language::labelTypeEditLayoutErrorPlacement() . "</td>";
        $returnStr .= "<td>" . $this->displayErrorPlacement(SETTING_ERROR_PLACEMENT, '') . "</td><td width=25><nobr/>";
        $returnStr .= "</tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutButtons() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= $this->displayColorPicker();
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_BACK_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditBackButton() . "</td>";

        $returnStr .= "<td>" . $this->displayButton(SETTING_BACK_BUTTON, '') . "</td>
                      </tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_BACK_BUTTON_LABEL . "_checkbox /></td>           
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_BACK_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_NEXT_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditNextButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NEXT_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_NEXT_BUTTON_LABEL . "_checkbox /></td>            
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_NEXT_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_DK_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditDKButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_DK_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_DK_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_DK_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_RF_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditRFButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_RF_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_RF_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_RF_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_NA_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditNAButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_NA_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_NA_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_NA_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_UPDATE_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditUpdateButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_UPDATE_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_UPDATE_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_UPDATE_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditRemarkButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_REMARK_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_SAVE_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditRemarkSaveButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_REMARK_SAVE_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_REMARK_SAVE_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_REMARK_SAVE_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_CLOSE_BUTTON . "_checkbox /></td><td>" . Language::labelTypeEditCloseButton() . "</td>";
        $returnStr .= "<td>" . $this->displayButton(SETTING_CLOSE_BUTTON, '') . "</td></tr>
            <tr><td><input type=checkbox value=1 name=" . SETTING_CLOSE_BUTTON_LABEL . "_checkbox /></td>             
                      <td>" . Language::labelTypeEditButtonLabel() . "</td>";
        $returnStr .= "<td>" . $this->displayButtonLabel(SETTING_CLOSE_BUTTON_LABEL, '') . "</td></tr>";

        $returnStr .= '</table></div>';


        $returnStr .= '<span class="label label-default">' . Language::labelTypeEditLayoutProgressBar() . '</span>';
        $returnStr .= "<div class='well'>";
        $returnStr .= "<table>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_PROGRESSBAR_SHOW . "_checkbox /></td><td>" . Language::labelTypeEditLayoutProgressBarShow() . "</td>";
        $returnStr .= "<td>" . $this->displayProgressbar(SETTING_PROGRESSBAR_SHOW, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_PROGRESSBAR_FILLED_COLOR . "_checkbox /></td><td>" . Language::labelTypeEditLayoutProgressBarFillColor() . "</td>";
        $returnStr .= '<td><div class="input-group colorpicker">
          <input name=' . SETTING_PROGRESSBAR_FILLED_COLOR . ' type="text" value="' . '' . '" class="form-control" />
          <span class="input-group-addon"><i></i></span>
          </div></td></tr>';
        $returnStr .= '<tr><td><input type=checkbox value=1 name=' . SETTING_PROGRESSBAR_WIDTH . '_checkbox /></td><td>' . Language::labelTypeEditLayoutProgressBarWidth() . '</td><td><input type="text" class="form-control" name="' . SETTING_PROGRESSBAR_WIDTH . '" value="' . '' . '"></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';

        // group
        $returnStr .= '<span class="label label-default">' . Language::labelSettingsTable() . '</span>';
        $returnStr .= "<div class='well'>";

        $returnStr .= "<table>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HEADER_ALIGNMENT . "_checkbox /></td><td>" . Language::labelTypeEditHeaderAlignment() . "</td>";
        $returnStr .= "<td>" . $this->displayAlignment(SETTING_HEADER_ALIGNMENT, '') . "</td></tr>
          <tr><td><input type=checkbox value=1 name=" . SETTING_HEADER_FORMATTING . "_checkbox /></td>
          <td>" . Language::labelTypeEditHeaderFormatting() . "</td>";
        $returnStr .= "<td>" . $this->displayFormatting(SETTING_HEADER_FORMATTING, '') . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_HEADER_FIXED . "_checkbox /></td><td>" . Language::labelTypeEditHeaderFixed() . "</td>";
        $returnStr .= "<td>" . $this->displayHeaderFixed('') . "</td></tr>
          <tr><td><input type=checkbox value=1 name=" . SETTING_HEADER_SCROLL_DISPLAY . "_checkbox /></td>
          <td>" . Language::labelTypeEditHeaderScrollDisplay() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control" name="' . SETTING_HEADER_SCROLL_DISPLAY . '" value="' . '' . '"></td></tr>';

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_TABLE_BORDERED . "_checkbox /></td><td>" . Language::labelGroupEditBordered() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_BORDERED, '') . "</td></tr><tr>";
        $returnStr .= "<td><input type=checkbox value=1 name=" . SETTING_GROUP_TABLE_CONDENSED . "_checkbox /></td><td>" . Language::labelGroupEditCondensed() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_CONDENSED, '') . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_GROUP_TABLE_HOVERED . "_checkbox /></td><td>" . Language::labelGroupEditHovered() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_HOVERED, '') . "</td></tr><tr>";
        $returnStr .= "<td><input type=checkbox value=1 name=" . SETTING_GROUP_TABLE_STRIPED . "_checkbox /></td><td>" . Language::labelGroupEditStriped() . "</td>";
        $returnStr .= "<td>" . $this->displayStriped(SETTING_GROUP_TABLE_STRIPED, '') . "</td></tr>";

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_TABLE_WIDTH . "_checkbox /></td><td>" . Language::labelTypeEditTableWidth() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control" name="' . SETTING_TABLE_WIDTH . '" value="' . '' . '"></td></tr><tr>';
        $returnStr .= "<td><input type=checkbox value=1 name=" . SETTING_QUESTION_COLUMN_WIDTH . "_checkbox /></td><td>" . Language::labelTypeEditQuestionColumnWidth() . "</td>";
        $returnStr .= '<td><input type="text" class="form-control" name="' . SETTING_QUESTION_COLUMN_WIDTH . '" value="' . '' . '"></td><td width=25><nobr/></td></tr>';

        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '</div>';

        $returnStr .= '<div class="tab-pane" id="assistance">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table width=100%>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXCLUSIVE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_INCLUSIVE . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceInclusive() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_INCLUSIVE . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMinimumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceMaximumRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceExactRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_EXACT_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceUniqueRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '<tr><td valign=top><input type=checkbox value=1 name=' . SETTING_ERROR_MESSAGE_SAME_REQUIRED . '_checkbox /></td><td valign=top style="width: 15%;">' . Language::labelGroupEditAssistanceSameRequired() . '</td><td><textarea style="width: 100%;" rows=2 class="form-control" name="' . SETTING_ERROR_MESSAGE_SAME_REQUIRED . '"></textarea></td></tr>';
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<div class="tab-pane" id="navigation">';
        $returnStr .= '<div class="well">';
        $returnStr .= '<table>';

        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_ENABLED . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingEnabled() . "</td>";
        $returnStr .= "<td>" . $this->displayKeyBoardBindingDropdown(SETTING_KEYBOARD_BINDING_ENABLED, '', true) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_BACK . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingBack() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_BACK) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_NEXT . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingNext() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NEXT) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_DK . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingDK() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_DK) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_RF . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingRF() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_RF) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_NA . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingNA() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_NA) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_UPDATE . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingUpdate() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_UPDATE) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_REMARK . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingRemark() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_REMARK) . "</td></tr>";
        $returnStr .= "<tr><td><input type=checkbox value=1 name=" . SETTING_KEYBOARD_BINDING_CLOSE . "_checkbox /></td><td>" . Language::labelTypeEditKeyboardBindingClose() . "</td><td>" . $this->displayButtonBinding(SETTING_KEYBOARD_BINDING_CLOSE) . "</td></tr>";
        $returnStr .= '</table>';
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<input onclick="$(\'#batchaction\').val(\'edit\'); $(\'#actionform\').submit(); " type="button" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';

        $returnStr .= '</div>';
        return $returnStr;
    }

    function showToolsBatchEditorSections($variablecookievalue, $sectioncookievalue, $groupcookievalue, $typecookievalue) {
        $returnStr = $this->getToolsBatchEditorTopTab(3, $variablecookievalue != "", $sectioncookievalue != "", $groupcookievalue != "", $typecookievalue != "");

        $returnStr .= "<form method='post' id='reload' name='reload'>";
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditor'));
        $returnStr .= "</form>";
        $returnStr .= '<form method="post">';
        $returnStr .= '<div class="well">';
        //$returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.batcheditor.copyvariableres', 'gid' => $group->getGid()));
        $returnStr .= '<table>';
        $returnStr .= '</table>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        $returnStr .= "</div>";
        $returnStr .= '</form>';
        return $returnStr;
    }

    function showCompile($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsCompiler());
        $returnStr .= $content;

        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {
            $returnStr .= '<form method="post">';
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.compileres'));
            $returnStr .= '<span class="label label-default">' . Language::labelToolsCompileSurveys() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            foreach ($surveys as $survey) {
                $returnStr .= '<tr><td><label><input name=compile[] value="' . $survey->getSuid() . '" type="checkbox">' . $survey->getName() . '</label></td></tr>';
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';

            $returnStr .= '<span class="label label-default">' . Language::labelToolsCompileComponents() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_SECTION . '" type="checkbox">' . Language::labelToolsCompileSections() . '</label></td></tr>';
            //$returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_SETTING . '" type="checkbox">' . Language::labelToolsCompileSettings() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_TYPE . '" type="checkbox">' . Language::labelToolsCompileTypes() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_VARIABLE . '" type="checkbox">' . Language::labelToolsCompileVariables() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_FILL . '" type="checkbox">' . Language::labelToolsCompileFills() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_INLINEFIELDS . '" type="checkbox">' . Language::labelToolsCompileInlineFields() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name=components[] value="' . SURVEY_COMPONENT_GROUP . '" type="checkbox">' . Language::labelToolsCompileGroup() . '</label></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';


            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonCompile() . '"/>';
            $returnStr .= '</form>';
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showXiCompile($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsXiCompiler());
        $returnStr .= $content;
        $returnStr .= $this->displayComboBox();
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {
            $returnStr .= '<form method="post">';
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.xicompileres'));
            $returnStr .= '<span class="label label-default">' . Language::labelToolsXiCompileCriteria() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelToolsCompileSurveys() . '</td><td>' . $this->displaySurveys("compile", "compile", $_SESSION['SUID'], "") . '</tr>';
            $returnStr .= '<tr><td>' . Language::labelToolsCompileModes() . '</td><td>' . $this->displayModesAdmin("ximode", "ximode", getSurveyMode()) . '</tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';

            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonDeploy() . '"/>';
            $returnStr .= '</form>';
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showXiCompileRes($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsXiCompiler());
        $returnStr .= $content;
        require_once("xicompiler.php");
        set_time_limit(0);
        $compile = loadvar("compile");
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $xi = new XiCompiler($compile, loadvar("ximode"), getSurveyVersion());
        $xi->generateTypes();
        $xi->generateVariableDescriptives();
        $seid = getBaseSectionSeid($_SESSION["SUID"]);
        $xi->generateRouting($seid);
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        $returnStr .= '<div>
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#vars" aria-controls="vars" role="tab" data-toggle="tab">' . Language::linkVariables() . '</a></li>
                                <li role="presentation" class=""><a href="#types" aria-controls="types" role="tab" data-toggle="tab">' . Language::linkTypes() . '</a></li>
                                <li role="presentation" class=""><a href="#routing" aria-controls="routing" role="tab" data-toggle="tab">' . Language::linkRouting() . '</a></li>                            
                            </ul>
                            <div class="tab-content">';

        // variables
        $returnStr .= "<div role='tabpanel' class='tab-pane active' id='vars'><textarea class='form-control' name=variablestext id=variablestext style='width: min-width 500px; width: 100%; min-height: 800px; height: 100%'>";
        $returnStr .= "<?php\r\n\r\n" . implode("\r\n", $xi->getVariableDescriptivesOutput()) . "\r\n\r\n?>";
        $returnStr .= "</textarea></div>";

        // types
        $returnStr .= "<div role='tabpanel' class='tab-pane' id='types'><textarea class='form-control' name=typestext id=typestext style='width: min-width 500px; width: 100%; min-height: 800px; height: 100%'>";
        $returnStr .= "<?php\r\n\r\n" . implode("\r\n", $xi->getTypesOutput()) . "\r\n\r\n?>";
        $returnStr .= "</textarea></div>";

        // routing
        $returnStr .= "<div role='tabpanel' class='tab-pane' id='routing'><textarea class='form-control' name=routingtext id=routingtext style='width: min-width 500px; width: 100%; min-height: 800px; height: 100%'>";
        $returnStr .= "<?php\r\n\r\n" . implode("", $xi->getRoutingOutput($seid)) . "\r\n\r\n?>";
        $returnStr .= "</textarea></div>";

        $returnStr .= "</div></div>";
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showExport() {
        $returnStr = $this->showToolsHeader(Language::headerToolsExporter());
        $returnStr .= $this->displayComboBox(); // <option value=1>Blaise</option>
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {

            $returnStr .= "<form id=refreshform1 method=post>";
            $returnStr .= '<input type=hidden name=page value="sysadmin.tools.export">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_EXPORTTYPE . '" id="' . SMS_POST_EXPORTTYPE . '_hidden" value="">';
            $returnStr .= "</form>";

            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="sysadmin.tools.export">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
            $returnStr .= "</form>";

            $returnStr .= '<form method="post">';
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.exportres'));
            $returnStr .= '<span class="label label-default">' . Language::labelToolsExportSettings() . '</span>';
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
            $returnStr .= "</select></td></tr>";
            $sel = array(EXPORT_TYPE_SERIALIZE => "", EXPORT_TYPE_SQL => "");
            if (loadvar(SMS_POST_EXPORTTYPE) != "") {
                $sel[loadvar(SMS_POST_EXPORTTYPE)] = "SELECTED";
            }

            $returnStr .= '<tr><td>' . Language::labelToolsExportType() . '</td>
                    <td><select ' . "onchange='document.getElementById(\"" . SMS_POST_EXPORTTYPE . "_hidden\").value=this.value; document.getElementById(\"refreshform1\").submit();'" . ' class="selectpicker show-tick" name="' . SETTING_EXPORT_TYPE . '">  
                    <option ' . $sel[EXPORT_TYPE_SERIALIZE] . ' value=' . EXPORT_TYPE_SERIALIZE . '>' . Language::labelToolsExportTypeSerialize() . '</option>
                    <option ' . $sel[EXPORT_TYPE_SQL] . '  value=' . EXPORT_TYPE_SQL . '>' . Language::labelToolsExportTypeSQL() . '</option>                                            
                    </select></td>
                    </tr>';

            if (loadvar(SMS_POST_EXPORTTYPE) == EXPORT_TYPE_SQL) {
                $returnStr .= '<tr><td>' . Language::labelToolsExportCreate() . '</td>
                    <td><select class="selectpicker show-tick" name="' . SETTING_EXPORT_CREATE . '">                    
                    <option value=' . EXPORT_CREATE_YES . '>' . Language::labelToolsExportCreateYes() . '</option>
                    <option value=' . EXPORT_CREATE_NO . '>' . Language::labelToolsExportCreateNo() . '</option>
                    </select></td>
                    </tr>';
                $returnStr .= '<tr><td>' . Language::labelToolsExportHistory() . '</td>
                    <td><select class="selectpicker show-tick" name="' . SETTING_EXPORT_HISTORY . '">                    
                    <option value=' . EXPORT_HISTORY_YES . '>' . Language::labelToolsExportHistoryYes() . '</option>
                    <option value=' . EXPORT_HISTORY_NO . '>' . Language::labelToolsExportHistoryNo() . '</option>
                    </select></td>
                    </tr>';
            }
            $returnStr .= '</table>';
            $returnStr .= '</div>';

            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonExport() . '"/>';
            $returnStr .= '</form>';
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }

        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showImport($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsImporter());
        $returnStr .= $this->displayComboBox();
        $returnStr .= $content;

        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="sysadmin.tools.import">';
        $returnStr .= '<input type=hidden name="' . SMS_POST_IMPORTTYPE . '" id="' . SMS_POST_IMPORTTYPE . '_hidden" value="">';
        $returnStr .= "</form>";

        $returnStr .= '<form method="post" enctype="multipart/form-data">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.importres'));
        $returnStr .= '<span class="label label-default">' . Language::labelToolsImportSettings() . '</span>';
        $returnStr .= '<div class="well well-sm">';
        $returnStr .= '<table>';
        $sel = array(IMPORT_TYPE_BLAISE => " SELECTED ", IMPORT_TYPE_MMIC => "", IMPORT_TYPE_NUBIS => "");
        $v = loadvar(SMS_POST_IMPORTTYPE);
        if ($v != "") {
            $sel[loadvar(SMS_POST_IMPORTTYPE)] = "SELECTED";
        } else {
            $v = IMPORT_TYPE_BLAISE;
        }
        $returnStr .= '<tr><td>' . Language::labelToolsImportType() . "</td>
                    <td><select onchange='document.getElementById(\"" . SMS_POST_IMPORTTYPE . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();' class='selectpicker show-tick' name='" . SETTING_IMPORT_TYPE . "'>" . '                    
                    <option ' . $sel[IMPORT_TYPE_NUBIS] . ' value=' . IMPORT_TYPE_NUBIS . '>' . Language::labelToolsImportTypeNubis() . '</option>
                    <option ' . $sel[IMPORT_TYPE_MMIC] . '  value=' . IMPORT_TYPE_MMIC . '>' . Language::labelToolsImportTypeMMIC() . '</option>
                    <option ' . $sel[IMPORT_TYPE_BLAISE] . '  value=' . IMPORT_TYPE_BLAISE . '>' . Language::labelToolsImportTypeBlaise() . '</option>
                    </select></td>
                    </tr>';
        $returnStr .= '<tr><td>' . Language::labelToolsImportTarget() . '</td>
                    <td><select class="selectpicker show-tick" name="' . SETTING_IMPORT_AS . '">                    
                    <option value=' . IMPORT_TARGET_ADD . '>' . Language::labelToolsImportTargetAdd() . '</option>
                    <option value=' . IMPORT_TARGET_REPLACE . '>' . Language::labelToolsImportTargetReplace() . '</option>';
        $returnStr .= '</select></td>
                    </tr>';

        if ($v == IMPORT_TYPE_BLAISE) {
            $surveys = new Surveys();
            if ($surveys->getNumberOfSurveys() > 1) {
                $returnStr .= '<tr ><td>' . Language::labelToolsImportSurvey() . '</td><td>' . $this->displaySurveys("targetsurvey", "targetsurvey", $_SESSION['SUID'], "") . '</tr>';
            } else {
                $returnStr .= "<input type=hidden name='targetsurvey' value='" . $_SESSION['SUID'] . "' />";
            }
        }

        $returnStr .= '</table>';
        $returnStr .= '</div>';

        if ($v == IMPORT_TYPE_MMIC) {
            $returnStr .= '<span class="label label-default">' . Language::labelToolsImportDatabase() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelDatabaseServer() . '</td><td><input class="form-control" type=text name="' . SETTING_IMPORT_SERVER . '" placeholder="localhost" /></td></tr>';
            $returnStr .= '<tr><td>' . Language::labelDatabaseName() . '</td><td><input class="form-control" type=text name="' . SETTING_IMPORT_DATABASE . '" value="" /></td></tr>';
            $returnStr .= '<tr><td>' . Language::labelDatabaseUsername() . '</td><td><input class="form-control" type=text name="' . SETTING_IMPORT_USER . '" value="" /></td></tr>';
            $returnStr .= '<tr><td>' . Language::labelDatabasePassword() . '</td><td><input class="form-control" type=password name="' . SETTING_IMPORT_PASSWORD . '" /></td></tr>';
            $returnStr .= '<tr><td>' . Language::labelDatabaseTablename() . '</td><td><input class="form-control" type=text name="' . SETTING_IMPORT_TABLE . '" value="" /></td></tr>';
            $returnStr .= '</table>';
            $returnStr .= '</div>';
        } else {
            $returnStr .= '<span class="label label-default">' . Language::labelToolsImportFile() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= '<div style="position:relative;"><a class="btn btn-primary" href="javascript:;">' . Language::buttonBrowse() . '
            <input type="file" style="position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)\';opacity:0;background-color:transparent;color:transparent;" name="' . SETTING_IMPORT_TEXT . '" size="40"  onchange=\'$("#upload-file-info").html($(this).val());\'>
        </a>
        &nbsp;
        <span class="label label-info" id="upload-file-info"></span></div>';
            $returnStr .= '</div>';
        }

        $returnStr .= '<input type="submit" class="btn btn-default" ' . confirmAction(language::messageImportSurvey(), 'IMPORT') . ' value="' . Language::buttonImport() . '"/>';
        $returnStr .= '</form>';

//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
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
            $returnStr .= '<input type=hidden name=page value="sysadmin.tools.test">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_MODE . '" id="' . SMS_POST_MODE . '_hidden" value="' . getSurveyMode() . '">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_LANGUAGE . '" id="' . SMS_POST_LANGUAGE . '_hidden" value="' . getSurveyLanguage() . '">';
            $returnStr .= "</form>";

            $returnStr .= "<form method=post>";
            $returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC(generateRandomPrimkey(8), Config::directLoginKey())) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
            
            
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
            $returnStr .= "<tr><td>" . Language::labelTestModeInput() . "</td><td>" . $this->displayModesAdmin(POST_PARAM_MODE, POST_PARAM_MODE, getSurveyMode(), "", $current->getAllowedModes(), "onchange='document.getElementById(\"" . SMS_POST_MODE . "_hidden\").value=this.value; document.getElementById(\"refreshform\").submit();'") . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelTestLanguage() . "</td><td>" . $this->displayLanguagesAdmin(POST_PARAM_LANGUAGE, POST_PARAM_LANGUAGE, getSurveyLanguage(), true, true, false, "", $current->getAllowedLanguages(getSurveyMode())) . "</td></tr>";
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

    function showIssues($content = "") {
        $returnStr = $this->showToolsHeader(Language::linkReported());
        $returnStr .= $content;
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
                $returnStr .= "<br/>" . '<div class="alert alert-warning">' . 'No reported problems found' . '</div>';
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
                $returnStr .= "<th>Reported by</th><th>Reported on</th><th>Category</th><th>Description</th><th>Primary key</th><th>Interview mode</th><th>Language</th>";
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

    function showFlood($content = "") {
        $returnStr = $this->showToolsHeader(Language::headerToolsFlooder());
        $returnStr .= $content;
        $surveys = new Surveys();
        $surveys = $surveys->getSurveys(false);
        if (sizeof($surveys) > 0) {
            $returnStr .= "<form method=post>";
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.tools.floodres'));
            $returnStr .= '<span class="label label-default">' . Language::labelToolsFloodSettings() . '</span>';
            $returnStr .= '<div class="well well-sm">';
            $returnStr .= $this->displayComboBox();
            $returnStr .= '<table>';
            $returnStr .= '<tr><td>' . Language::labelFloodSurvey() . "</td><td><select name=" . POST_PARAM_SUID . " class='selectpicker show-tick'>";
            foreach ($surveys as $survey) {
                $returnStr .= "<option value=" . $survey->getSuid() . '>' . $survey->getName() . '</option>';
            }
            $returnStr .= "</select></td></tr>";
            $returnStr .= "<tr><td>" . Language::labelFloodLanguage() . "</td><td>" . $this->displayLanguagesAdmin(POST_PARAM_LANGUAGE, POST_PARAM_LANGUAGE, getSurveyLanguage()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelFloodMode() . "</td><td>" . $this->displayModesAdmin(POST_PARAM_MODE, POST_PARAM_MODE, getSurveyMode()) . "</td></tr>";
            $returnStr .= "<tr><td>" . Language::labelFloodNumber() . "</td><td><input name='number' class='form-control' type='text' /></td></tr>";
            $returnStr .= '</table>';
            $returnStr .= '</div>';
            $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonFlood() . '</button>';
            $returnStr .= "</form>";
        } else {
            $returnStr .= $this->displayInfo(Language::messageNoSurveysAvailable());
        }
//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showPreferences($message = "") {
        $user = new User($_SESSION['URID']);
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . Language::headerPreferences() . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= $message;
        $returnStr .= '<form id="editform" method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.preferences.res'));
        $checked = '';
        if ($user->hasNavigationInBreadCrumbs()) {
            $checked = ' CHECKED';
        }
        $returnStr .= '<div class="checkbox"><label><input name=navigationinbreadcrumbs value="1" type="checkbox"' . $checked . '>' . Language::labelNavigationInBreadCrumbs() . '</label></div>';

        $checked = '';
        if ($user->hasHTMLEditor()) {
            $checked = ' CHECKED';
        }
        $returnStr .= '<div class="checkbox"><label><input name=htmleditor value="1" type="checkbox"' . $checked . '>' . Language::labelHTMLEditor() . '</label></div>';


        $checked = '';
        if ($user->hasRoutingAutoIndentation()) {
            $checked = ' CHECKED';
        }
        $returnStr .= '<div class="checkbox"><label><input name=routingautoindentation value="1" type="checkbox"' . $checked . '>' . Language::labelRoutingAutoIndentation() . '</label></div>';

        $count = $user->itemsInTable();
        if ($count == -1) {
            $count = "";
        }

        $returnStr .= "<table>";
        $helpstart = '<div class="input-group">';
        $message = Language::helpFollowEmpty();
        $helpend = '<span class="input-group-addon"><i>' . $message . '</i></span></div>';

        $returnStr .= "<tr><td>" . Language::labelTableListNumber() . "</td><td>" . $helpstart . ' <input name="itemsintable" type=text class="form-control" value="' . $count . '">' . $helpend . "</td></tr>";
        $returnStr .= "</table>";
        $returnStr .= '<br/>';
        $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::buttonSave() . '"/>';
        $returnStr .= '</form>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }
    
}

?>