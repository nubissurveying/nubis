<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplaySupervisor extends DisplayRespondent {

    public function __construct() {
        parent::__construct();
    }

    public function showMain() {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');

        $returnStr .= '<div id="wrap"><br/><br/><br/>';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelSuperVisorInterviewers() . '</li>';
        $returnStr .= '</ol>';

//CONTENT

        $returnStr .= '<br/>';

//END CONTENT
        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    public function displayInterviewers($interviewers, $message = '') {
//        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);

        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<br/><br/><br/>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelSuperVisorInterviewers() . '</li>';
//        $returnStr .= '<li>' . Language::labelInterviewers() . '</li>';
        $returnStr .= '</ol>';

//CONTENT
        if (sizeof($interviewers) > 0) {
            $returnStr .= '

<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
	<thead>
		<tr>
			<th>' . Language::labelSuperVisorInterviewersUrid() . '</th>
			<th>' . Language::labelSuperVisorInterviewersName() . '</th>
			<th>' . Language::labelSuperVisorInterviewersUsername() . '</th>
			<th>' . Language::labelSuperVisorInterviewersContacts() . '</th>
			<th>' . Language::labelSuperVisorInterviewersCompleted() . '</th>
			<th>' . Language::labelSuperVisorInterviewersRefused() . '</th>
                        <th>' . Language::labelSuperVisorInterviewersLastUpload() . '</th>
		</tr>
	</thead>
	<tbody>';
            $communication = new Communication();
            foreach ($interviewers as $interviewer) {
                $returnStr .= '<tr><td>' . $interviewer->getUrid() . '</td>';
                $returnStr .= '<td>' . setSessionParamsHref(array('page' => 'supervisor.interviewer.info', 'interviewer' => $interviewer->getUrid()), $interviewer->getName()) . '</td>';
                $returnStr .= '<td>' . $interviewer->getUsername() . '</td>';
                $returnStr .= '<td>' . sizeof($interviewer->getContacts()) . '</td>';
                $returnStr .= '<td>' . sizeof($interviewer->getCompleted()) . '</td>';
                $returnStr .= '<td>' . sizeof($interviewer->getRefusals()) . '</td>';
                $returnStr .= '<td>' . $communication->getLastUploaded($interviewer->getUrid()) . '</td>';
                $returnStr .= '</tr>';
            }

            $returnStr .= '</tbody></table>';
        } else {
            $returnStr .= $this->displayInfo(Language::labelSupervisorNoInterviewersAssigned());
        }


//END CONTENT
        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    public function showNavBar() {
        $search = true;
        $interviewersActive = ' class="active"';
        $unassignedSampleActive = '';
        $sampleActive = '';
        $reportsActive = '';

        if (getFromSessionParams('interviewer') != '') {
            $testUser = new User(getFromSessionParams('interviewer'));
            if ($testUser->getUserType() == USER_SUPERVISOR) {
                $interviewersActive = '';
                $sampleActive = ' class="active"';
            }
        }
        if (startsWith($_SESSION['LASTPAGE'], 'supervisor.sample')) {
            $interviewersActive = '';
            $sampleActive = ' class="active"';
        }
        if (startsWith($_SESSION['LASTPAGE'], 'supervisor.unassignedsample')) {
            $interviewersActive = '';
            $unassignedSampleActive = ' class="active"';
        }
        if (startsWith($_SESSION['LASTPAGE'], 'supervisor.reports')) {
            $interviewersActive = '';
            $reportsActive = ' class="active"';
        }
//TODO: Get from somewhere else!
        $user = new User($_SESSION['URID']);


        $returnStr = '
      <!-- Fixed navbar -->
      <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="' . setSessionParams(array('page' => 'supervisor.home')) . '">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
              <li' . $interviewersActive . '>' . setSessionParamsHref(array('page' => 'supervisor.interviewers'), Language::linkInterviewers()) . '</li>
              <li' . $sampleActive . '>' . setSessionParamsHref(array('page' => 'supervisor.sample'), Language::linkSample()) . '</li>
              <li' . $unassignedSampleActive . '>' . setSessionParamsHref(array('page' => 'supervisor.unassignedsample'), Language::linkUnassignedSample()) . '</li>
              <li' . $reportsActive . '>' . setSessionParamsHref(array('page' => 'supervisor.reports'), Language::linkReports()) . '</li>

            </ul>


            <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . $user->getName() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">
                    <li><a href="' . setSessionParams(array('page' => 'supervisor.preferences')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkPreferences() . '</a></li>';
        
        if ($user->getCommunication() != SEND_RECEIVE_WORKONSERVER) {
            $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'supervisor.sendreceive')) . '"><span class="glyphicon glyphicon-import"></span> ' . Language::linkSendReceive() . '</a></li>';
        }
        $returnStr .= '<li class="divider"></li>
                   <li><a href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>
                 </ul>
             </li>
            </ul>
';
        if ($search) {
            $returnStr .= '

<form class="navbar-form navbar-right" role="search">

<div class="input-group" style="width:250px;overflow:hidden;">

      <input type="text" class="form-control" name="searchterm">';

            $returnStr .= '<span class="input-group-btn">

        <button class="btn btn-default" type="submit">Search</button>

      </span>';

            $returnStr .= setSessionParamsPost(array('page' => 'supervisor.search'));

            $returnStr .= '</div>';

            $returnStr .='</form>

';
        }
        $returnStr .= '
          </div><!--/.nav-collapse -->
        </div>
      </div>
';
        return $returnStr;
    }

    function showBottomBar() {
        return '
    <div id="footer">
      <div class="container">
        <p class="text-muted credit" style="text-align:right">' . Language::nubisFooter() . '</p>
      </div>
    </div>
';
    }

    /*
      function displayInterviewer(User $interviewer) {
      //        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
      $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);

      $returnStr .= '<div id="wrap">';
      $returnStr .= $this->showNavBar();
      $returnStr .= '<div class="container"><p>';

      $returnStr .= '<ol class="breadcrumb">';
      $returnStr .= '<li class="active">Home</li>';
      $returnStr .= '<li>' . Language::labelInterviewers() . '</li>';
      $returnStr .= '</ol>';

      //CONTENT

      $returnStr .= '

      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
      <thead>
      <tr>
      <th>Id</th>
      <th>Name</th>
      </tr>
      </thead>
      <tbody>';

      $respondents = new Respondents();
      $respondents = $respondents->getRespondentsByUser($interviewer);
      foreach ($respondents as $respondent) {
      $returnStr .= '<tr><td>' . $respondent->getPrimkey() . '</td>';
      //            $returnStr .= '<td>' . setSessionParamsHref(array('page' => 'supervisor.interviewer.info', 'interviewer' => $interviewer->getUrid()), $interviewer->getName()) . '</td>';
      $returnStr .= '<td>' . $respondent->getName() . '</td>';
      $returnStr .= '</tr>';
      }

      $returnStr .= '</tbody></table>';



      //END CONTENT
      $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
      $returnStr .= $this->showBottomBar();

      $returnStr .= $this->showFooter(false);
      return $returnStr;
      }
     */

    function displayRespondent($respondentorhousehold) {

        $breadcrumps = '<ol class="breadcrumb">';
        $breadcrumps .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.home'), Language::labelSuperVisorInterviewers()) . '</li>';
        $breadcrumps .= '<li>' . $interviewer->getName() . '</li>';
        $breadcrumps .= '</ol>';

        $content = 'main interviewer page!';

        return $this->showSupervisorPageWithSideBar($content, $breadcrumps, $this->showInterviewerSideBar($interviewer));
    }

    function displayRespondentsFilter($filter = 0) {
        $returnStr = '';
        $active = array('', '', '', '', '', '', '', '');
        $active[$filter] = ' active';
        $returnStr .= '<input type="hidden" name="filtermode" id="filtermode" value="' . $filter . '">';
        $returnStr .= '<div id="filtermodeselector" class="btn-group">';
        foreach (Language::labelFilterSample() as $key => $filtertext) {
            $returnStr .= '<button type="button" class="btn btn-default' . $active[$key] . '" value=' . $key . '>' . $filtertext . '</button>';
        }
        $returnStr .= '</div>
      <script>
      $(\'#filtermodeselector button\').click(function() {
    	  $(\'#filtermodeselector button\').addClass(\'active\').not(this).removeClass(\'active\');
  		  $(\'#filtermode\').val($(this).val());
        $("#assignedsample").submit();
		  });
      </script>
    ';
        return $returnStr;
    }

    function displayInterviewerAssignedSample($interviewer, $message = '') {
        
        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);

        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $breadcrumps = '<ol class="breadcrumb">';
        if ($interviewer->getUserType() == USER_INTERVIEWER) {
            $breadcrumps .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.home'), Language::labelSuperVisorInterviewers()) . '</li>';
            $breadcrumps .= '<li>' . $interviewer->getName() . '</li>';
        } else {
            $breadcrumps .= '<li class="active">' . Language::linkSample() . '</li>';
        }
        $breadcrumps .= '</ol>';

        $returnStr .= '<br/><br/><br/>' . $breadcrumps;

        $content = '<form id=assignedsample>';
        $content .= setSessionParamsPost(array('page' => 'supervisor.interviewer.sample', 'interviewer' => $interviewer->getUrid()));
        $filtermode = 0;
        if (loadvar('filtermode') != '') {
            $filtermode = loadvar('filtermode', 0);
        } else {
            if (isset($_SESSION['FILTERMODE'])) {
                $filtermode = $_SESSION['FILTERMODE'];
            }
        }
        $_SESSION['FILTERMODE'] = $filtermode;

        $content .= $this->displayRespondentsFilter($filtermode);
        $content .= '</form>';
        $content .= '<br/>';
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            $households = new Households();
            $respondents = $households->getHouseholdsByUser($interviewer, $filtermode);
        } else {
            $respondents = new Respondents();
            $respondents = $respondents->getRespondentsByUser($interviewer, $filtermode);
        }
                
        if (sizeof($respondents) > 0) {
            $message = Language::messageRespondentsAssignedSupervisor($interviewer->getName());
            $currentUser = new User($_SESSION['URID']);
            if ($currentUser->getRegionFilter() > 0 && $currentUser->getPuid() > 0) { //only certain region
                $psu = new Psu($currentUser->getPuid());
                $message .= ' <b>' . Language::labelSupervisorFilterPsu() . ': ' . $psu->getCodeAndName() . '.';
            }
            $content .= $this->displaySuccess($message, "outcomehelp"); // 'Assigned respondents to ' . $interviewer->getName();
            $arr = array_values($respondents);
            if ($arr[0] instanceof Respondent) { //this is a respondent
                $content .= $this->showRespondentsTable($respondents, 'supervisor.interviewer.respondent');
            } else { //household
                $content .= $this->showHouseholdsTable($respondents, 'supervisor.');
            }
        } else {
            $message = Language::messageNoRespondentsAssignedSupervisor();
            $currentUser = new User($_SESSION['URID']);
            if ($currentUser->getRegionFilter() > 0 && $currentUser->getPuid() > 0) { //only certain region
                $psu = new Psu($currentUser->getPuid());
                $message .= ' <b>' . Language::labelSupervisorFilterPsu() . ': ' . $psu->getCodeAndName() . '.';
            }
            $content .= $this->displayWarning($message, "outcomehelp"); //'<div class="alert alert-warning" id="outcomehelp">' . Language::errorNoRespondentsAssignedSupervisor() . '</div>';
        }

        $returnStr .= $content;

        //END CONTENT
        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;


        //return $this->showSupervisorPageWithTableAndSideBar($content, $breadcrumps, $this->showInterviewerSideBar($interviewer));
    }

    function showSupervisorPageWithTableAndSideBar($content, $breadcrumps, $sideBar) {
        $returnStr = $this->displayHeaderForTableAndSideBar(Language::messageSMSTitle(), '');
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';


        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div class="col-xs-12 col-sm-9">';

        $returnStr .= '<br/><br/><br/>' . $breadcrumps;
//CONTENT

        $returnStr .= $content;

        $returnStr .= '</div>';
        $returnStr .= $sideBar;
        $returnStr .= '</div>';

//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function showSupervisorPageWithSideBar($content, $breadcrumps, $sideBar) {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">
                     <link href="css/uscicadmin.css" rel="stylesheet">
<script type="text/javascript" charset="utf-8">
$(document).ready(function () {
    if ($("[rel=tooltip]").length) {
        $("[rel=tooltip]").tooltip();
    }
});
</script>');
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';


        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
        $returnStr .= '<div class="col-xs-12 col-sm-9">';

        $returnStr .= $breadcrumps;
//CONTENT

        $returnStr .= $content;

        $returnStr .= '</div>';
        $returnStr .= $sideBar;
        $returnStr .= '</div>';

//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function showInterviewerSideBar($interviewer) {
        /*    $remarksStr = '';
          $remarks = $respondent->getRemarks();
          if (sizeof($remarks) > 0){
          $remarksStr = ' <span class="badge pull-right">' . sizeof($remarks) . '</span>';
          }
          $contactsStr = '';
          $contacts = $respondent->getContacts();
          if (sizeof($contacts) > 0){
          $contactsStr = ' <span class="badge pull-right">' . sizeof($contacts) . '</span>';
          } */
        return '

        <div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">
          <div class="well sidebar-nav">
            <ul class="nav">
              <li>' . $interviewer->getName() . '</li>
              <li class="active"><a href="' . setSessionParams(array('page' => 'supervisor.interviewer', 'interviewer' => $interviewer->getUrid())) . '"><span class="glyphicon glyphicon-user"></span> ' . Language::labelInfo() . '</a></li>
              <li><a href="' . setSessionParams(array('page' => 'supervisor.interviewer.sample', 'interviewer' => $interviewer->getUrid())) . '"><span class="glyphicon glyphicon-calendar"></span> ' . Language::labelAssignedSample() . '</a></li>
              <li><a href="' . setSessionParams(array('page' => 'supervisor.history', 'interviewer' => $interviewer->getUrid())) . '"><span class="glyphicon glyphicon-time"></span> ' . Language::labelHistory() . '</a></li>
              <li><a href="' . setSessionParams(array('page' => 'supervisor.remarks', 'interviewer' => $interviewer->getUrid())) . '"><span class="glyphicon glyphicon-comment"></span> ' . Language::labelRemarks() . '</a></li>
              <li><a href="' . setSessionParams(array('page' => 'supervisor.tracking', 'interviewer' => $interviewer->getUrid())) . '"><span class="glyphicon glyphicon-road"></span> ' . Language::labelTracking() . '</a></li>
              <li><a href="' . setSessionParams(array('page' => 'supervisor.interviewer.edit', 'interviewer' => $interviewer->getUrid())) . '"><span class="glyphicon glyphicon-pencil"></span> ' . Language::labelEdit() . '</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->';
    }

    /*   function displayInterviewerRespondentInfo(Respondent $respondent, $message = '') {
      $interviewer = new User($respondent->getUrid());
      $breadcrumps = '<ol class="breadcrumb">';
      $breadcrumps .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.home'), Language::labelHome()) . '</li>';
      $breadcrumps .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.interviewer', 'interviewer' => $interviewer->getUrid()), $interviewer->getName()) . '</li>';
      $breadcrumps .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.interviewer.respondent.info', 'primkey' => $respondent->getPrimkey()), $respondent->getName()) . '</li>';
      $breadcrumps .= '<li>' . 'Info' . '</li>';
      $breadcrumps .= '</ol>';

      $content = $respondent->getPrimkey();

      $content .= '<form method="post">';
      $content .= setSessionParamsPost(array('page' => 'supervisor.interviewer.respondent.reassign', 'primkey' => $respondent->getPrimkey()));


      $users = new Users();
      $users = $users->getUsersBySupervisor($_SESSION['URID']);
      $content .= $this->displayUsers($users, $interviewer->getUrid());
      $content .= '<br/>';
      $content .= '<input type="submit" class="btn btn-default" value="Reassign"/>';
      $content .= '</form>';







      return $this->showSupervisorPageWithTableAndSideBar($content, $breadcrumps, $this->showInterviewerSideBar($interviewer));


      } */

    public function displaySupervisorReports() {
        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelHome() . '</li>';
        $returnStr .= '</ol>';

//CONTENT

        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'supervisor.reports.statistics.response')) . '" class="list-group-item">' . Language::labelSupervisorResponse() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'supervisor.reports.statistics.contacts.graphs')) . '" class="list-group-item">' . Language::labelSupervisorContactGraphs() . '</a>';

        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'supervisor.reports.statistics.surveyinfo')) . '" class="list-group-item">' . Language::labelSupervisorSurveyInfo() . '</a>';


        $returnStr .= '</div>';



//END CONTENT
        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showOutputStatisticsContactsGraphs($seid) {
        $survey = new Survey(1);

        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelHome() . '</li>';
        $returnStr .= '</ol>';


        $urid = loadvar('selurid', 0);
        $rorh = loadvar('rorh', 1);
        $ceid = loadvar('ceid', 1);

        //echo '<br/><br/><br/>' . $urid . ':' . $rorh . ":" . $ceid;
//        $returnStr .= $this->displayInterviewerDropDown('supervisor.reports.statistics.contacts.graphs', $urid);
        //bbbbbbbbbb


        $returnStr .= '<nav class="navbar navbar-default" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand">' . Language::labelSupervisorSetFilter() . '</a>
   </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">';


        $returnStr .= '<form method="post" class="navbar-form navbar-left">';
        $returnStr .= setSessionParamsPost(array('page' => 'supervisor.reports.statistics.contacts.graphs'));

        // $content .= $sessionparams;
        $returnStr .= '<div class="form-group">';
        $returnStr .= $this->displayInterviewerSelect($urid);
        //$content .= $input; //$this->displayUsers($users, $respondentOrHousehold->getUrid());
        $returnStr .= $this->displayRespondentOrHousehold($rorh);
        $returnStr .= $this->displayContactType($ceid);


        $returnStr .= '</div>';
        $returnStr .= '<button type="submit" class="btn btn-default">' . Language::labelSupervisorGo() . '</button>';
        $returnStr .= '</form>
        </div>
      </div>
</nav>';


        //aaaaaaaaaaaaaaaaaaaaaa


        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';

        $returnStr .= $this->getContactData($urid, $rorh, $ceid);


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function getSurveyInfoData($rorh = 1) {
        $title = Language::messageSMSTitle();
        $sub = Language::labelSupervisorHHSurveyOutcome();
        $resporhhtext = Language::labelSupervisorHouseholds();
        if ($rorh == 2) {
            $sub = Language::labelSupervisorRespondentSurveyOutcome();
            $resporhhtext = Language::labelSupervisorRespondents();
        }


        $names = Language::labelSupervisorSurveyInfoNames();
        $actiontype = array('endtime', 'endtime', 107);

        $returnStr = '<script src="js/export-csv.js"></script>';
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
                text: '" . $sub . "'
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

                    text: '# " . $resporhhtext . "'
                },
                min: 0
            },

            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' " . $resporhhtext . "';

                }
            },
            
            series: [";

        foreach ($names as $key => $name) {
            if ($key == 1) {
                if ($key != 0) {
                    $returnStr .= ',';
                }
                $returnStr .= "{
                name: '" . $name . "',
                data: [";
                $returnStr .= $this->getFieldisNull($rorh, $actiontype[$key]);
                $returnStr .= "                ]
            }";
            } elseif ($key == 2) {
                if ($key != 0) {
                    $returnStr .= ',';
                }
                $returnStr .= "{
                name: '" . $name . "',
                data: [";
                $returnStr .= $this->getContactCodeData($actiontype[$key], 0, $rorh);
                $returnStr .= "                ]
            }";
            } else {
                if ($key != 0) {
                    $returnStr .= ',';
                }
                $returnStr .= "{
                name: '" . $name . "',
                data: [";
                $returnStr .= $this->getFieldNotNull($rorh, $actiontype[$key]);
                $returnStr .= "                ]
            }";
            }
        }
        $returnStr .= "
      ]
        });
    //}); 


</script>";

        return $returnStr;
    }

    function getResponseData($rorh) {
        $title = Language::projectTitle();

        $respondentorhousehold = Language::labelSupervisorRespondents();
        $sub = Language::labelSupervisorResponseGraphSubRespondents();
        if ($rorh == 1) {
            $sub = Language::labelSupervisorResponseGraphSubHouseholds();
            $respondentorhousehold = Language::labelSupervisorHouseholds();
        }
        $names = Language::labelSupervisorResponseNames();
        $actiontype = array('', 'begintime', 'endtime');


        $returnStr = '<script src="js/export-csv.js"></script>';
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
                text: '" . $sub . "'
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
                    text: '# " . $respondentorhousehold . "'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' " . $respondentorhousehold . "';
                }
            },
            
            series: [";

        foreach ($names as $key => $name) {
            if ($key == 0) {
                $returnStr .= "{
                name: '" . $name . "',
                data: [";
                $returnStr .= $this->getNumberOfContactsData($rorh);
                $returnStr .= "                ]
            }";
            } else {
                $returnStr .= ',';
                $returnStr .= "{
                name: '" . $name . "',
                data: [";
                $returnStr .= $this->getFieldNotNull($rorh, $actiontype[$key]);
                $returnStr .= "                ]
            }";
            }
        }
        $returnStr .= "
      ]
        });
    //}); 


</script>";

        return $returnStr;
    }

    function getUridQuery($prefix = 't2.') {
        $users = new Users();
        $users = $users->getUsersBySupervisor($_SESSION['URID']);
        $userStr .= '(';
        $i = 0;
        foreach ($users as $user) {
            if ($i > 0) {
                $userStr .= ' OR ';
            }
            $userStr .= ' ' . $prefix . 'urid = ' . $user->getUrid();
            $i++;
        }
        $userStr .= ') AND ' . $prefix . 'primkey is not null AND ';
        return $userStr;
    }

    function getNumberOfContactsData($rorh = 1) {
        global $db;
        $dataStr = '';
        $actions = array();

        //99900174
        $userStr = $this->getUridQuery();

        if ($rorh == 1) { //houseohld level
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurvey() . '_contacts as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_households as t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $userStr . getTextmodeStr() . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND  t2.primkey is not null group by DATE(t1.ts) order by DATE(t1.ts) asc';
        } else {
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurvey() . '_contacts as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_respondents t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $userStr . getTextmodeStr() . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND  t2.primkey is not null group by DATE(t1.ts) order by DATE(t1.ts) asc';
        }
        // echo '<br/><br/><br/>' . $query;
        $total = 0;
        $dataStr .= "[Date.UTC(" . date('Y,m,d', strtotime(date('Y-m-d', config::graphStartDate()) . " -1 month")) . "), 0   ],";
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $key = $row['dateobs'];
            $total += $row['cntobs'];
            $dataStr .= "[Date.UTC(" . substr($key, 0, 4) . ", " . (substr($key, 5, 2) - 1) . ", " . substr($key, 8, 2) . "), " . $total . "],";
        }
        $returnStr = rtrim($dataStr, ',');
        return $returnStr;
    }

    function getFieldNotNull($rorh = 1, $fieldname) {
        global $db;
        $dataStr = '';
        $actions = array();

        //99900174
        $userStr = $this->getUridQuery();

        if ($rorh == 1) { //houseohld level
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurveyData() . '_data as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_households t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $userStr . getTextmodeStr('t1.') . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND suid = ' . $rorh . ' and variablename="' . $fieldname . '" and answer is not null group by DATE(t1.ts) order by DATE(t1.ts) asc';
        } else {
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurveyData() . '_data as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_respondents t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $userStr . getTextmodeStr('t1.') . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND suid = ' . $rorh . ' and variablename="' . $fieldname . '" and answer is not null group by DATE(t1.ts) order by DATE(t1.ts) asc';
        }

        // echo '<br/><br/><br/>' . $query;

        $total = 0;
        $dataStr .= "[Date.UTC(" . date('Y,m,d', strtotime(date('Y-m-d', config::graphStartDate()) . " -1 months")) . "), 0   ],";
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $key = $row['dateobs'];
            $total += $row['cntobs'];
            $dataStr .= "[Date.UTC(" . substr($key, 0, 4) . ", " . (substr($key, 5, 2) - 1) . ", " . substr($key, 8, 2) . "), " . $total . "],";
        }
        $returnStr = rtrim($dataStr, ',');
        return $returnStr;
    }

    function getFieldIsNull($rorh = 1, $fieldname) {
        global $db;
        $dataStr = '';
        $actions = array();

        //99900174
        $userStr = $this->getUridQuery();

        if ($rorh == 1) { //houseohld level
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurveyData() . '_data as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_households t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $userStr . getTextmodeStr('t1.') . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND suid = ' . $rorh . ' and variablename="' . $fieldname . '" and answer is null group by DATE(t1.ts) order by DATE(t1.ts) asc';
        } else {
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurveyData() . '_data as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_respondents t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $userStr . getTextmodeStr('t1.') . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND suid = ' . $rorh . ' and variablename="' . $fieldname . '" and answer is null group by DATE(t1.ts) order by DATE(t1.ts) asc';
        }


        $total = 0;
        $dataStr .= "[Date.UTC(" . date('Y,m,d', strtotime(date('Y-m-d', config::graphStartDate()) . " -1 months")) . "), 0   ],";
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $key = $row['dateobs'];
            $total += $row['cntobs'];
            $dataStr .= "[Date.UTC(" . substr($key, 0, 4) . ", " . (substr($key, 5, 2) - 1) . ", " . substr($key, 8, 2) . "), " . $total . "],";
        }
        $returnStr = rtrim($dataStr, ',');
        return $returnStr;
    }

    function getContactData($urid = 0, $rorh = 1, $ceid = 1) {


        $title = Language::projectTitle();
        $sub = Language::labelSupervisorContactSub();

        if ($rorh == 2) { //resondent level
            if ($ceid == 1) {
                $contacts = Language::optionsDispositionContactCode(new Respondent());
            } else {
                $contacts = Language::optionsFinalDispositionContactCode(new Respondent());
            }
            $resporhhtext = Language::labelSupervisorRespondents();
        } else {
            if ($ceid == 1) {
                $contacts = Language::optionsDispositionContactCode(new Household());
            } else {
                $contacts = Language::optionsFinalDispositionContactCode(new Household());
            }
            $resporhhtext = Language::labelSupervisorHouseholds();
        }

        $names = array();
        $actiontype = array();
        foreach ($contacts as $key => $contact) {
            //if ($contact[5] == 1){ //all contacts
            $names[] = $contact[1];
            $actiontype[] = $key;
            //}
        }

        $returnStr = '<script src="js/export-csv.js"></script>';
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
                text: '" . $sub . "'
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
                    text: '# " . $resporhhtext . "'
                },
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        Highcharts.dateFormat('%e. %b', this.x) +': '+ this.y +' " . $resporhhtext . "';
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
            $returnStr .= $this->getContactCodeData($actiontype[$key], $urid, $rorh);
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

    function getContactCodeData($code, $urid = 0, $rorh = 2) {
        global $db;
        $dataStr = '';
        $actions = array();
        $uridstr = '';
        if ($urid > 0) {
            $uridstr = ' t1.urid = ' . $urid . ' AND ';
        } else {
            $uridstr = $this->getUridQuery('t2.');
        }


        if ($rorh == 1) { //houseohld level
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurvey() . '_contacts as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_households as t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $uridstr . getTextmodeStr() . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND t2.primkey is not null and t1.code = ' . $code . ' group by DATE(t1.ts) order by DATE(t1.ts) asc';
        } else {
            $query = 'select DATE(t1.ts) as dateobs, count(*) as cntobs from ' . dbConfig::dbSurvey() . '_contacts as t1 ';
            $query .= 'left join ' . dbConfig::dbSurvey() . '_respondents t2 on t2.primkey = t1.primkey ';
            $query .= 'where ' . $uridstr . getTextmodeStr() . ' t1.ts > "' . date('Y-m-d', config::graphStartDate()) . ' 23:59:99" AND t2.primkey is not null and t1.code = ' . $code . ' group by DATE(t1.ts) order by DATE(t1.ts) asc';


        }
//                echo '<br/><br/><br/>' . $query;
        $total = 0;
        $dataStr .= "[Date.UTC(" . date('Y,m,d', strtotime(date('Y-m-d', config::graphStartDate()) . " -1 months")) . "), 0   ],";
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $key = $row['dateobs'];
            $total += $row['cntobs'];
            $dataStr .= "[Date.UTC(" . substr($key, 0, 4) . ", " . (substr($key, 5, 2) - 1) . ", " . substr($key, 8, 2) . "), " . $total . "],";
        }
        $returnStr = rtrim($dataStr, ',');
        return $returnStr;
    }

    function showOutputStatisticsSurveyInfo($seid) {
        $survey = new Survey(1);

        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);
        $returnStr .= '<div id="wrap">'; //<br/><br/><br/>';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelHome() . '</li>';
        $returnStr .= '</ol>';
//CONTENT


        $rorh = loadvar('rorh', 1);
        $returnStr .= '<nav class="navbar navbar-default" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand">' . Language::labelSupervisorSetFilter() . '</a>
   </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">';


        $returnStr .= '<form method="post" class="navbar-form navbar-left">';
        $returnStr .= setSessionParamsPost(array('page' => 'supervisor.reports.statistics.surveyinfo'));

        // $content .= $sessionparams;
        $returnStr .= '<div class="form-group">';
        $returnStr .= $this->displayRespondentOrHousehold($rorh);
        $returnStr .= '</div>';
        $returnStr .= '<button type="submit" class="btn btn-default">' . Language::labelSupervisorGo() . '</button>';
        $returnStr .= '</form>
        </div>
      </div>
</nav>';




        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
//        echo '<br/><br/><br/><br><br/>' . $this->getContactData();
        $returnStr .= $this->getSurveyInfoData($rorh);


        /*        $returnStr .= '<table>';
          $returnStr .= '<tr><td>Completed Interviews</td><td></td></tr>';
          $returnStr .= '<tr><td>Non-Qualified</td><td></td></tr>';
          $returnStr .= '<tr><td>Suspends/Breakoffs</td><td></tr>';
          $returnStr .= '</table>'; */



//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showOutputResponse() {
        $survey = new Survey(1);

        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelHome() . '</li>';
        $returnStr .= '</ol>';




        $rorh = loadvar('rorh', 1);
        $returnStr .= '<nav class="navbar navbar-default" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand">Set filter</a>
   </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">';


        $returnStr .= '<form method="post" class="navbar-form navbar-left">';
        $returnStr .= setSessionParamsPost(array('page' => 'supervisor.reports.statistics.response'));

        // $content .= $sessionparams;
        $returnStr .= '<div class="form-group">';
        $returnStr .= $this->displayRespondentOrHousehold($rorh);
        $returnStr .= '</div>';
        $returnStr .= '<button type="submit" class="btn btn-default">' . Language::labelSupervisorGo() . '</button>';
        $returnStr .= '</form>
        </div>
      </div>
</nav>';



        $returnStr .= '<script src="js/highcharts.js"></script>';
        $returnStr .= '<script src="js/modules/exporting.js"></script>';
        $returnStr .= '<div id="chart1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>';
//        echo '<br/><br/><br/><br><br/>' . $this->getContactData();
//        echo '<hr><hr>';
        $returnStr .= $this->getResponseData($rorh);


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function setPrefix($refpage) {
        $refpageprefix = 'supervisor.';
        return $refpageprefix . $refpage;
    }

    function showRespondentPageWithSideBar($respondent, $content, $label) {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">
<link href="css/uscicadmin.css" rel="stylesheet">
<script type="text/javascript" charset="utf-8">

$(document).ready(function () {

    if ($("[rel=tooltip]").length) {

        $("[rel=tooltip]").tooltip();

    }

});

</script>');
        $returnStr .= '<div id="wrap"><br/><br/><br/>';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';





        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';

        $returnStr .= '<div class="col-xs-12 col-sm-9">';



        $returnStr .= '<ol class="breadcrumb">';

        if ($respondent instanceof Respondent) {
            $pageref = 'interviewer';
        } else {
            $pageref = 'interviewer.household';
        }
        $pageref = $this->setPrefix($pageref);
        $interviewer = new User($respondent->getUrid());

        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.home'), Language::labelSuperVisorInterviewers()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.interviewer.info', 'interviewer' => $interviewer->getUrid()), $interviewer->getName()) . '</li>';
        if ($respondent instanceof Respondent && dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level link
            $household = $respondent->getHousehold();

            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'supervisor.interviewer.household.info', 'primkey' => $household->getPrimkey()), Language::householdLabelCap()) . '</li>';
        }

//        $returnStr .= '<li>' . setSessionParamsHref(array('page' => $pageref . '.info', 'primkey' => $respondent->getPrimkey()), Language::householdOrRespondentLabelCap($respondent)) . '</li>';

        $returnStr .= '<li class="active">' . Language::householdOrRespondentLabelCap($respondent) . '</li>';

        $returnStr .= '</ol>';

//CONTENT



        $returnStr .= $content;



        $returnStr .= '</div>';

        $returnStr .= $this->showSectionSideBar($respondent);

        $returnStr .= '</div>';


//END CONTENT

        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter();

        return $returnStr;
    }

    function getRespondentActionButtons($respondentOrHousehold, $refpage) {
        $content = '';
        if ($respondentOrHousehold->hasFinalCode() && $respondentOrHousehold->isCompleted()) { //if not completed: can still be assigned to different iwer
            $content = $this->displayInfo(Language::labelSupervisorFinalCodeAssigned());
        } else {
            if ($respondentOrHousehold instanceof Household) {
                //          $content .= '<form method="post">';
                //            $content .= setSessionParamsPost(array('page' => 'supervisor.interviewer.respondent.reassign', 'primkey' => $respondentOrHousehold->getPrimkey()));

                $users = new Users();
                $users = $users->getUsersBySupervisor($_SESSION['URID']);
                // $content .= $this->displayUsers($users, $respondentOrHousehold->getUrid());
                // $content .= '<br/>';
                // $content .= '<input type="submit" class="btn btn-default" value="Reassign"/>';
                // $content .= '</form>';   

                $content .= $this->showActionBar(Language::labelSupervisorAssignToInterviewer(), $this->displayUsers($users, $respondentOrHousehold->getUrid(), 'uridsel', false, true), 'Reassign', setSessionParamsPost(array('page' => 'supervisor.interviewer.household.reassign', 'primkey' => $respondentOrHousehold->getPrimkey())), confirmAction('Are you sure you want to reassign this household? Make sure the intervier data for this household has been uploaded, otherwise data wil be lost! Type YES to continue.', 'YES'));
                if (!$respondentOrHousehold->hasFinalCode()) {
                    $content .= $this->showActionBar(Language::labelSupervisorAssignFinalStatus(), $this->displayFinalStatusCodesSelect($respondentOrHousehold->getUrid()), 'Set status', setSessionParamsPost(array('page' => 'supervisor.interviewer.household.contact.setstatus', 'primkey' => $respondentOrHousehold->getPrimkey())), confirmAction('Are you sure you want to reassign this household? Type YES to continue.', 'YES'));
                }
            } else {
                if (!$respondentOrHousehold->hasFinalCode()) {
                    $content .= $this->showActionBar(Language::labelSupervisorAssignFinalStatus(), $this->displayFinalStatusCodesSelect($users, $respondentOrHousehold->getUrid()), 'Set status', setSessionParamsPost(array('page' => 'supervisor.interviewer.household.respondent.contact.setstatus', 'primkey' => $respondentOrHousehold->getPrimkey())), confirmAction('Are you sure you want to assign a final status code to this respondent? Type YES to continue.', 'YES'));
                } else {
                    $content = $this->displayInfo(Language::labelSupervisorFinalCodeAssigned());
                }
            }
        }
        //NEEDS VALIDATION?
        if ($respondentOrHousehold instanceof Respondent) {
            if ($respondentOrHousehold->needsValidation()) {
                $beforeText = '<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal">' . Language::labelSupervisorCheckAnswers() . '</button>';

                $content .= '  
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">' . Language::labelSupervisorCheckRespondentAnswers() . '</h4>
      </div>
      <div class="modal-body">
        <table>
        <thead><tr><th>' . Language::labelSupervisorCheckRespondentQuestion() . '</th><th>' . Language::labelSupervisorCheckRespondentAnswer() . '</th></tr></thead>
	<tbody>';

                $validationQuestions = Language::validationQuestions();
                $survey = new Survey(2);
                foreach ($validationQuestions as $question) {
                    $var = $survey->getVariableDescriptiveByName(getBasicName($question));
                    $content .= '<tr><td>' . $question . ': ' . $var->getDescription() . '</td><td>';

                    $content .= '</td></tr>';
                }
                $content .= '</tbody></table>';
//         $content .= json_encode($survey);
                $content .= '
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">' . Language::buttonClose() . '</button>
      </div>
    </div>
  </div>
</div> 
';
                $content .= $this->showActionBar(Language::labelSupervisorCheckRespondentValidate(), $this->displayValidationStatus(Language::optionsSupervisorValidation(), $respondentOrHousehold->getUrid(), 'validationsel', false, $beforeText), 'Set validation status', setSessionParamsPost(array('page' => 'supervisor.interviewer.household.respondent.setvalidation', 'primkey' => $respondentOrHousehold->getPrimkey())));
            }
        }

        return $content;
    }

    function getRespondentContactButton($respondentOrHousehold, $refpage) {
        return '';    //nothing for supervisor mode
    }

    function showPreferences($user) {

        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet"><link href="css/uscicadmin.css" rel="stylesheet">');

        $returnStr .= '<div id="wrap"><br/><br/><br/>';

        $returnStr .= $this->showNavBar();

        $returnStr .= '<div class="container"><p>';

//CONTENT



        $returnStr .= '<h4>' . Language::linkPreferences() . '</h4>';



        $returnStr .= '<form method=post>';

        $returnStr .= setSessionParamsPost(array('page' => 'supervisor.preferencesres'));



        $returnStr .= '<input type="hidden" name="filter" id="filter" value="' . $user->getFilter() . '">';



        $returnStr .= '<div class="panel panel-default">

  <div class="panel-heading">

    <h3 class="panel-title">' . Language::labelSupervisorFilters() . '</h3>

  </div>

  <div class="panel-body">

';



        $returnStr .= '<table>';
        //$returnStr .= '<tr><td style="width:110px">Households/Respondents:</td><td>';

        /*    $returnStr .= '<div id="filterselector" class="btn-group">

          <button type="button" class="btn btn-default active" value=1>None</button>

          <button type="button" class="btn btn-default" value=2>Hide completed</button>

          </div></td><td colspan=2></td></tr>';
         */


        $returnStr .= '<input type=hidden name="region" id="region" value="' . $user->getRegionFilter() . '">';



        $returnStr .= '<tr><td style="width:110px">' . Language::labelSupervisorFilterRegion() . '</td><td>';

        $returnStr .= '<div id="regionselector" class="btn-group">

				<button type="button" class="btn btn-default active" value=0>' . Language::labelSupervisorFilterRegionAll() . '</button>

				<button type="button" class="btn btn-default" value=1>' . Language::labelSupervisorFilterRegionOne() . '</button>

			</div></td><td style="width:10px;"></td><td>';



        $returnStr .= '<div id="regiondiv" style="display: none">';

        $returnStr .= $this->displayPsus($user->getPuid());

        $returnStr .= '</div>';





        $returnStr .= '</td></tr></table>';





        $returnStr .= '

  </div>

</div>';



        $returnStr .= '<div class="panel panel-default">

  <div class="panel-heading">

    <h3 class="panel-title">' . Language::labelSettings() . '</h3>

  </div>

  <div class="panel-body">

';





        $returnStr .= '<input type="hidden" name="testmode" id="testmode" value="' . $user->getTestMode() . '">';

        $returnStr .= '<table><tr><td style="width:110px">' . Language::labelSupervisorSurvey() . ':</td><td>

    

			<div id="testmodeselector" class="btn-group">

				<button type="button" class="btn btn-default active" value=0>' . Language::labelNormalMode() . '</button>

				<button type="button" class="btn btn-default" value=1>' . Language::labelTestMode() . '</button>

			</div></td><td style="width:10px;"></td><td>';

        $returnStr .= '<div id="testmodediv" style="display: none">';
//        $returnStr .= '<a href="' . setSessionParams(array('page' => 'interviewer.preferences.resettest')) . '">' . Language::linkResetTestCases() . '</a>';
        $returnStr .= '</div>';

        $returnStr .= '</td></tr>';






        $returnStr .= '<tr><td style="width:110px">' . Language::labelCommunication() . '</td><td colspan=2>';

        $returnStr .= $this->displayCommunicationSelect($user->getCommunication());

        $returnStr .= '</td></tr>';

        $returnStr .= '</table>';





        $returnStr .= '<script>';



        $returnStr .= '$(\'#filterselector button\').click(function() {

		  $(\'#filterselector button\').addClass(\'active\').not(this).removeClass(\'active\');

		  $(\'#filter\').val("1");

		  if ($(this).val() == "2") {

		    $(\'#filter\').val("2");

		  }  

    });';





        $returnStr .= '$(\'#regionselector button\').click(function() {

		  $(\'#regiondiv\').css("display", "none");

		  $(\'#regionselector button\').addClass(\'active\').not(this).removeClass(\'active\');

		  $(\'#region\').val("0");

		  if ($(this).val() != 0) {

		    $(\'#regiondiv\').css("display", "block");

		    $(\'#region\').val("1");

		  }

		  }); ';





        $returnStr .= '$(\'#testmodeselector button\').click(function() {

		  $(\'#testmodediv\').css("display", "none");

		  $(\'#testmodeselector button\').addClass(\'active\').not(this).removeClass(\'active\');

		  $(\'#testmode\').val("0");

		  if ($(this).val() == "1") {

		    $(\'#testmodediv\').css("display", "block");

		    $(\'#testmode\').val("1");

		  }

		  });



if ($(\'#filter\').val() == "2"){

  $(\'#filterselector button\').click();

}

if ($(\'#region\').val() != 0){

  $(\'#regiondiv\').css("display", "block");

  $(\'#regionselector button\').click();

}

if ($(\'#testmode\').val() == "1"){

  $(\'#testmodediv\').css("display", "block");

  $(\'#testmodeselector button\').click();

}

</script>';





        $returnStr .= '

  </div>

</div>';



        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonSave() . '</button>';

        $returnStr .= '</form>';



//END CONTENT

        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter();

        return $returnStr;
    }

    function showSendReceive($message) {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet"><link href="css/uscicadmin.css" rel="stylesheet">');
        $returnStr .= '<div id="wrap"><br/><br/><br/>';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
//CONTENT
        $returnStr .= $message;

        $returnStr .= '<h4>Direct connection with server</h4>';


//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function showSample($message = '') {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet"><link href="css/uscicadmin.css" rel="stylesheet">');
        $returnStr .= '<div id="wrap"><br/><br/><br/>';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
//CONTENT
//        $returnStr .= '<h4>Assign households to interviewers</h4>';
        $returnStr .= $message;




//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function showUnassignedSample($message = '') {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet"><link href="css/uscicadmin.css" rel="stylesheet">');
        $returnStr .= '<div id="wrap"><br/><br/><br/>';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
//CONTENT
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
            $returnStr .= '<h4>' . Language::labelSupervisorUnassignedHouseholds() . '</h4>';
        }
        else {
            $returnStr .= '<h4>' . Language::labelSupervisorUnassignedRespondents() . '</h4>';
        }
        $returnStr .= $message;
        $displaySms = new DisplaySms();
        $returnStr .= $displaySms->showAvailableUnassignedHouseholds();

//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function displayFinalStatusCodesSelect($respondentOrHousehold) {
        $finalContactCodes = Language::optionsFinalDispositionContactCode($respondentOrHousehold);
        $returnStr = '<select name="csid" class="form-control" style="width:200px">';

        $selected = '';

        $returnStr .= '<option value="' . 0 . '"' . $selected . '>' . Language::labelDropdownNothing() . '</option>';
        foreach ($finalContactCodes as $key => $contactcode) {
            if ($contactcode[5] == 1) {
                $selected = '';
                $returnStr .= '<option value="' . $key . '"' . $selected . '>' . $key . ': ' . $contactcode[1] . '</option>';
            }
        }
        $returnStr .= '</select>';

        return $returnStr;
    }

    function showSearchRes($respondentsOrHouseholds) {

//        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);

        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<br/><br/><br/>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelSuperVisorInterviewers() . '</li>';
//        $returnStr .= '<li>' . Language::labelInterviewers() . '</li>';
        $returnStr .= '</ol>';

//CONTENT
        if (sizeof($respondentsOrHouseholds) > 0) {
            //$returnStr .= $this->displaySuccess($message, "outcomehelp"); // 'Assigned respondents to ' . $interviewer->getName();
            $returnStr .= sizeof($respondentsOrHouseholds) . ' ' . Language::messageRespondentsFoundSupervisor();
            $arr = array_values($respondentsOrHouseholds);
            if ($arr[0] instanceof Respondent) { //this is a respondent
                $returnStr .= $this->showRespondentsTable($respondentsOrHouseholds, 'supervisor.interviewer.respondent');
            } else { //household
                $returnStr .= $this->showHouseholdsTable($respondentsOrHouseholds, 'supervisor.');
            }
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoRespondentsSearchResSupervisor(), "outcomehelp"); // '<div class="alert alert-warning" id="outcomehelp">' . Language::errorNoRespondentsAssigned() . '</div>';
        }



//END CONTENT
        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

}

?>