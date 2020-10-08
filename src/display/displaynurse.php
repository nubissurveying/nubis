<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayNurse extends Display {

    public function __construct() {
        parent::__construct();
    }

    public function showMain($message = '') {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        if (isVisionTestNurse(new User($_SESSION['URID']))) {


            $primkey = gen_password(10);
            $returnStr .= "<form method=post>";
            $returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($primkey, Config::directLoginKey())) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '1' . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_MODE . ' value="' . MODE_CAPI . '">';
            $returnStr .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . '5' . '">';
            $returnStr .= '<button type="submit" id="startsurveybtn" class="btn btn-default navbar-btn" style="width:200px">Start the vision test</button>';
            $returnStr .= "</form>";
        } else {
            $returnStr .= $this->showNavBar();
            $returnStr .= '<div class="container"><p>';


            $returnStr .= $message;
            if (isFieldNurse(new User($_SESSION['URID']))) {
                $respondents = new Respondents();
                $respondents = $respondents->getRespondentsByUrid($_SESSION['URID']);
                $returnStr .= '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
		<thead>
			<tr>
				<th>' . Language::labelNurseHouseholdID() . '</th><th>' . Language::labelNurseName() . '</th><th>' . Language::labelNurseDwellingID() . '</th><th>' . Language::labelNurseVillage() . '</th></tr>';
                foreach ($respondents as $respondent) {
                    $returnStr .= '<tr>';
                    $refpage = 'nurse.respondent.fieldnurse';
                    $returnStr .= '<td>' . setSessionParamsHref(array('page' => $refpage . '.info', 'primkey' => $respondent->getPrimkey()), $respondent->getPrimkey()) . '</td>';
                    $returnStr .= '<td>' . $respondent->getName() . '</td><td>' . $respondent->getAddress1() . '</td><td>' . $respondent->getCity() . '</td></tr>';
                }
                $returnStr .= '</table>';
            } else { //lab or lab nurse mode
                //respondents mode!
                $returnStr .= '<h4>' . Language::labelNurseScanBarcode() . '</h4>';

                $returnStr .= '<form id="searchform" role="search" autocomplete=off>';
                $returnStr .= setSessionParamsPost(array("page" => "nurse.respondents.search"));

                $returnStr .= '<div class="input-group" style="width:300px">
			  <input name="search" type="text" class="form-control" id="search">
			  <span class="input-group-btn">
				<button id="searchbutton" class="btn btn-default" type="submit">' . Language::labelSearch() . '</button>
			  </span>
			</div><!-- /input-group -->';

                $returnStr .= '<script>$("#search").focus();</script>';
                $returnStr .= '</form>';
                //$returnStr .= $this->showSearch();

                if (isLabNurse(new User($_SESSION['URID']))) {
                    $returnStr .= '<br/><hr>';


                    $returnStr .= '<b>' . Language::labelNurseFieldDBS() . '</b><br/><br/>';
                    global $db;
                    $query = 'select count(*) as cnt from ' . Config::dbSurveyData() . '_lab where fielddbsstatus = 1';
                    $result = $db->selectQuery($query);
                    if ($result != null) {
                        $row = $db->getRow($result);
                        if ($row['cnt'] > 0) {
                            $returnStr .= $this->displayInfo(Language::labelNurseToShip($row["cnt"]));


                            $returnStr .= '<a href="' . setSessionParams(array('page' => 'nurse.fielddbs.shiptolab')) . '" target="#">' . Language::labelNurseShipToLab() . '</a>';

                            $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
                            $returnStr .= '<a href="' . setSessionParams(array('page' => 'nurse.fielddbs.shiptolab.marked')) . '">' . Language::labelNurseMarkShipped() . '</a>';
                        } else {
                            $returnStr .= $this->displayInfo('There are currently no field DBS cards that need to be send to the lab');
                        }
                    }

                    $returnStr .= '<hr><b>' . Language::labelNurseLabName() . '</b><br/><br/>';
                    $returnStr .= '<a href="' . setSessionParams(array('page' => 'nurse.labblood.overview')) . '" target="#">' . Language::labelNurseLabBloodOverview() . '</a><br/>';
                    $returnStr .= '<a href="' . setSessionParams(array('page' => 'nurse.labdbs.overview')) . '" target="#">' . Language::labelNurseLabDBSOverview() . '</a><br/>';

                    //$returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
                    //$returnStr .= '<a href="' . setSessionParams(array('page' => 'nurse.fielddbs.shiptolab.marked')) . '">' . 'Mark these DBS cards as "shipped"' . '</a>';
                }


                if (!isLabNurse(new User($_SESSION['URID']))) {
                    //TEST
                    $returnStr .= '<hr><b>' . Language::labelNurseTestLab() . '</b><br/><br/>';


                    $primkey = gen_password(10);
                    $returnStr .= "<form method=post>";
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($primkey, Config::directLoginKey())) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '1' . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_MODE . ' value="' . MODE_CAPI . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . '3' . '">';
                    $returnStr .= '<button type="submit" id="startsurveybtn" class="btn btn-default navbar-btn" style="width:200px">' . Language::labelNurseStartSurvey() . '</button>';
                    $returnStr .= "</form>";

                    $primkey = gen_password(10);
                    $returnStr .= "<form method=post>";
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($primkey, Config::directLoginKey())) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '1' . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_MODE . ' value="' . MODE_CAPI . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . '5' . '">';
                    $returnStr .= '<button type="submit" id="startsurveybtn" class="btn btn-default navbar-btn" style="width:200px">' . Language::labelNurseStartVision() . '</button>';
                    $returnStr .= "</form>";

                    $primkey = gen_password(10);
                    $returnStr .= "<form method=post>";
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($primkey, Config::directLoginKey())) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '1' . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_MODE . ' value="' . MODE_CAPI . '">';
                    $returnStr .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . '6' . '">';
                    $returnStr .= '<button type="submit" id="startsurveybtn" class="btn btn-default navbar-btn" style="width:200px">' . Language::labelNurseAntropometrics() . '</button>';
                    $returnStr .= "</form>";
                }
            }
        }
//END TEST


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);

        return $returnStr;
    }

    function showNurseHeader($title, $extra = '') {

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

        $respondentsActive = ' active';
        $followActive = '';


        if (!isset($_SESSION['LASTPAGE'])) {
            $_SESSION['LASTPAGE'] = 'nurse.main';
        }
        if (strpos($_SESSION['LASTPAGE'], 'nurse.followup') === 0) {
            $followActive = ' active';
            $respondentsActive = '';
        }


        /*        $surveyActive = '';
          $outputActive = '';
          $toolsActive = '';
          if (!isset($_SESSION['LASTPAGE'])) {
          $_SESSION['LASTPAGE'] = 'sysadmin.survey';
          }
          if (strpos($_SESSION['LASTPAGE'], 'sysadmin.sms') === 0) {
          $smsActive = ' active';
          $surveyActive = '';
          $outputActive = '';
          $toolsActive = '';
          }
          if (strpos($_SESSION['LASTPAGE'], 'sysadmin.survey') === 0) {
          $smsActive = '';
          $surveyActive = ' active';
          $outputActive = '';
          $toolsActive = '';
          }
          if (strpos($_SESSION['LASTPAGE'], 'sysadmin.output') === 0) {
          $smsActive = '';
          $surveyActive = '';
          $outputActive = ' active';
          $toolsActive = '';
          }
          if (strpos($_SESSION['LASTPAGE'], 'sysadmin.tools') === 0) {
          $smsActive = '';
          $surveyActive = '';
          $outputActive = '';
          $toolsActive = ' active';
          } */

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
            <a class="navbar-brand">' . Language::messageSMSTitle() . '</a>
          </div>
          <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">';


        $returnStr .= '<li' . $respondentsActive . '>' . setSessionParamsHref(array('page' => 'nurse.respondents'), Language::linkInterviews()) . '</li>';
        $returnStr .= '<li' . $followActive . '>' . setSessionParamsHref(array('page' => 'nurse.followup'), Language::labelNurseFollowUp()) . '</li>';


//        $returnStr .'<li' . $smsActive . '>' . setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()) . '</li>';




        /*     $returnStr .= '<li class="dropdown' . $surveyActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkSurvey() . ' <b class="caret"></b></a>';

          $surveys = new Surveys();
          $surveys = $surveys->getSurveys();
          $returnStr .= '<ul class="dropdown-menu">';
          foreach ($surveys as $survey) {
          $span = '';
          if (isset($_SESSION['SUID']) && $_SESSION['SUID'] == $survey->getSuid()) {
          $span = ' <span class="glyphicon glyphicon-chevron-down"></span>';
          }
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.survey', 'suid' => $survey->getSuid()), $survey->getName() . $span) . '</li>';
          }
          $returnStr .= '</ul>';
          $returnStr .= '</li>'; */
        /*        $returnStr .= '<li class="dropdown' . $outputActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkOutput() . ' <b class="caret"></b></a>';
          $returnStr .= '<ul class="dropdown-menu">';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.data'), '<span class="glyphicon glyphicon-save"></span> ' . Language::linkData()) . '</li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.statistics'), '<span class="glyphicon glyphicon-stats"></span> ' . Language::linkStatistics()) . '</li>';
          $returnStr .= '<li class="divider"></li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.documentation'), '<span class="glyphicon glyphicon-file"></span> ' . Language::linkDocumentation()) . '</li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.output.screendumps'), '<span class="glyphicon glyphicon-screenshot"></span> ' . Language::linkScreendumps()) . '</li>';
          $returnStr .= '</ul></li>'; */

        /* $returnStr .= '<li class="dropdown' . $toolsActive . '"><a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . Language::linkTools() . ' <b class="caret"></b></a>';
          $returnStr .= '<ul class="dropdown-menu">';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.check'), '<span class="glyphicon glyphicon-check"></span> ' . Language::linkChecker()) . '</li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.compile'), '<span class="glyphicon glyphicon-cog"></span> ' . Language::linkCompiler()) . '</li>';
          $returnStr .= '<li class="divider"></li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.test'), '<span class="glyphicon glyphicon-comment"></span> ' . Language::linkTest()) . '</li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.flood'), '<span class="glyphicon glyphicon-random"></span> ' . Language::linkFlood()) . '</li>';
          $returnStr .= '<li class="divider"></li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.export'), '<span class="glyphicon glyphicon-export"></span> ' . Language::linkExport()) . '</li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.import'), '<span class="glyphicon glyphicon-import"></span> ' . Language::linkImport()) . '</li>';
          $returnStr .= '<li class="divider"></li>';
          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.tools.clean'), '<span class="glyphicon glyphicon-trash"></span> ' . Language::linkCleaner()) . '</li>';
          $returnStr .= '</ul></li>'; */
        $returnStr .= '</ul>';
        $user = new User($_SESSION['URID']);
        $returnStr .= '<ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a data-hover="dropdown" class="dropdown-toggle" data-toggle="dropdown">' . $user->getUsername() . ' <b class="caret"></b></a>
                 <ul class="dropdown-menu">
        		<li><a href="' . setSessionParams(array('page' => 'sysadmin.preferences')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkPreferences() . '</a></li>';
        if (isFieldNurse($user)) { //send/receive button
            $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'interviewer.sendreceive')) . '"><span class="glyphicon glyphicon-import"></span> ' . Language::linkSendReceive() . '</a></li>';
        }
        if ($user->getUserType() == USER_SYSADMIN) {
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

    function showSearchRes($respondentsOrHouseholds, $message = '') {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        //respondents mode!
        $returnStr .= '<h4>' . Language::labelNurseSearchResults() . '</h4>';

        $returnStr .= $message;




        if (sizeof($respondentsOrHouseholds) > 0) {
            $returnStr .= sizeof($respondentsOrHouseholds) . ' ' . Language::messageRespondentsFound();
            $t = array_values($respondentsOrHouseholds);
            if ($t[0] instanceof Respondent) {
                $returnStr .= $this->showRespondentsTable($respondentsOrHouseholds, 'nurse.respondent');
            } else { //household
                $returnStr .= $this->showHouseholdsTable($respondentsOrHouseholds, 'nurse.household');
            }
        } else {

            $returnStr .= $this->displayWarning(Language::messageNoRespondentsFound());
        }


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showSearch() {
        $returnStr = '<form id="searchform" class="navbar-form navbar-right" role="search" autocomplete=off>
                        <div class="input-group" style="width:250px">'
                . setSessionParamsPost(array("page" => "nurse.respondents.search")) . '
                          <input name="search" type="text" class="form-control">
                          <span class="input-group-btn">
                            <button id="searchbutton" class="btn btn-default" type="submit">' . Language::labelSearch() . '</button>
                          </span>
                        </div>
                        </form>';

        return $returnStr;
    }

    function showInfoButtons($respondent, $lab) {
        //respondents mode!

        $labstations = Language::labStations();
        $returnStr = '';
        $returnStr .= '<table class="table">'; //
        if (isMainNurse(new User($_SESSION['URID'])) && !isLabNurse(new User($_SESSION['URID']))) {
            //button for consent
            $returnStr .= '<tr><td style="width:200px">';
            if ($respondent->getAge() < 70) {
                $returnStr .= $labstations['1']['name'];
            } else {
                $returnStr .= Language::labelNurseRespondentInfo();
            }
            $returnStr .= '</td><td valign=center style="width:50px">';
            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.consent', 'primkey' => $respondent->getPrimkey()));
            $buttontext = Language::labelNurseConsentInfo();
            if ($lab->isRefusal()) {
                $buttontext = Language::labelNurseRespondentRefused();
            }
            $returnStr .= $this->showButton($buttontext, false, '', $lab->getConsent1() == 1);
//            $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . 'Enter consent info' . '</button>';
            $returnStr .= '</form>';
            $returnStr .= '</td><td valign=center style="width:50px">';

            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.labbarcode', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonScanLabBarcode(), $lab->getConsent1() != 1, '', $lab->getLabBarcode() != '');
            $returnStr .= '</form>';
            $returnStr .= '</td><td>';
            if ($respondent->getAge() < 70) {
                $windowopen = 'window.open(\'' . setSessionParams(array('page' => 'nurse.respondent.trackingsheet.print', 'primkey' => $respondent->getPrimkey())) . '\', \'popupWindow\', \'width=700,height=400,scrollbars=yes,top=100,left=100\');';
                $returnStr .= $this->showButton(Language::labelNurseButtonPrintTracking(), $lab->getLabBarcode() == '', $windowopen);
                //   $returnStr .= '</form>';
            }
            $returnStr .= '</td><td></td></tr>';
        }

        if (!isLabNurse(new User($_SESSION['URID']))) {

            if ($respondent->getAge() < 70) {

                $returnStr .= '<tr><td style="width:200px">' . $labstations['5b']['name'] . '</td><td colspan=3>';
                $returnStr .= $this->showStartButton($respondent, 5, $lab->getLabBarcode() == '', Language::labelNurseButtonVisionTest(), $lab->getVision() == 2);
                $returnStr .= $this->showStartButton($respondent, 6, $lab->getLabBarcode() == '', Language::labelNurseButtonAntropometrics(), $lab->getAnthropometrics() == 2);
                $returnStr .= '</td></tr>';

                if (!isMainNurse(new User($_SESSION['URID'])) && $lab->getLabBarcode() == '') {
                    $returnStr .= '<tr><td colspan=4>' . $this->displayError(Language::labelNurseErrorNoConsentBarcode()) . '</td></tr>';
                }

                $returnStr .= '<tr><td style="width:200px"><nobr>' . $labstations['6']['name'] . '</td><td colspan=3>';
                $returnStr .= $this->showStartButton($respondent, 3, $lab->getLabBarcode() == '', Language::labelNurseButtonStartLabSurvey(), $lab->getSurvey() == 2);
                $returnStr .= '</td></tr>';

                if (!isMainNurse(new User($_SESSION['URID'])) && $lab->getLabBarcode() == '') {
                    $returnStr .= '<tr><td colspan=4>' . $this->displayError(Language::labelNurseErrorNoConsentBarcode()) . '</td></tr>';
                }


                if (!isMainNurse(new User($_SESSION['URID'])) && $lab->getLabBarcode() == '') {
                    $returnStr .= '<tr><td colspan=4>' . $this->displayError(Language::labelNurseErrorNoConsentBarcode()) . '</td></tr>';
                }
            }

            if (isMainNurse(new User($_SESSION['URID']))) {
                $returnStr .= '<tr><td style="width:200px">';

                if ($respondent->getAge() < 70) {
                    $returnStr .= $labstations['9']['name'];
                } else {
                    $returnStr .= Language::labelNurseAssignFiles();
                }
                if ($respondent->getAge() >= 70) {
                    $returnStr .= '</td><td>';
                    if ($lab->getUrid() > 0) {
                        $userF = new User($lab->getUrid());
                        $returnStr .= Language::labelNurseRespondentAssignedTo() . $userF->getName();
                    } else {
                        $returnStr .= '<form method=post>';
                        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.assigntofieldnurse', 'primkey' => $respondent->getPrimkey()));
                        $returnStr .= $this->showButton(Language::labelNurseButtonAssignFieldNurse(), $lab->getConsent1() != 1, '', true /* $lab->getLabBarcode() != '' */);
                        $returnStr .= '</form>';
                    }
                }
                $returnStr .= '</td><td>';
                $windowopen = 'window.open(\'' . setSessionParams(array('page' => 'nurse.respondent.uploadfiles', 'primkey' => $respondent->getPrimkey())) . '\', \'popupWindow\', \'width=700,height=400,scrollbars=yes,top=100,left=100\');';
                $returnStr .= $this->showButton(Language::labelNurseButtonUploadUSB(), $lab->getLabBarcode() == '', $windowopen);
                $returnStr .= '</td><td valign=center>';
                if ($respondent->getAge() < 70) {
                    $returnStr .= $this->showStartButton($respondent, 4, $lab->getLabBarcode() == '', Language::labelNurseButtonDataEnterSheet(), $lab->getMeasures() == 2);
                }
                $returnStr .= '</td><td></td></tr>';
            }
        }
//LAB USER ONLY!!!

        if (!isScriptRunLocally() && isLabNurse(new User($_SESSION['URID']))) { //only locally at the lab
            $returnStr .= '<tr><td style="width:200px">' . Language::labelNurseDBSBlood() . '</td><td valign=center style="width:50px">';


            /*
              {
              var el=document.getElementById('button');
              el.onclick=function(){
              var my_text=prompt('Enter text here');
              if(my_text) alert(my_text); // for example I've made an alert
              }
              } */


            if ($lab->getFieldDBSStatus() == 0) {
                $returnStr .= '<form method=post id=dbsform>';
                $returnStr .= '<input type=hidden name=dbsdate id=dbsdate value="' . date("Y-m-d") . '">';
                $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.fielddbs.received', 'primkey' => $respondent->getPrimkey()));
                $windowopen = 'var my_text=prompt(\'' . Language::labelNurseEnterCollectionDate() . '\', \'' . date('Y-m-d') . '\'); if(my_text != \'\' && my_text !== null) {  $(\'#dbsdate\').val(my_text);$(\'form#dbsform\').submit();} else { return false; } ';
                $returnStr .= $this->showButton(Language::labelNurseButtonReceivedFieldDBS(), $lab->getBarcode() == '', $windowopen);
                $returnStr .= '</form>';
            } elseif ($lab->getFieldDBSStatus() == 1) {
                $returnStr .= '<form method=post id=dbsform>';
                $returnStr .= '<input type=hidden name=dbsdate id=dbsdate value="' . date("Y-m-d") . '">';
                $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.fielddbs.received.fromlab', 'primkey' => $respondent->getPrimkey()));
                $windowopen = 'var my_text=prompt(\'' . Language::labelNurseEnterReceivedDate() . '\', \'' . date('Y-m-d') . '\'); if(my_text == \'\' && my_text !== null) { $(\'#dbsdate\').val(my_text);$(\'form#dbsform\').submit(); } else { return false; }';
                $returnStr .= $this->showButton(Language::labelNurseButtonReceivedFieldDBSFromLab(), $lab->getBarcode() == '', $windowopen);
                $returnStr .= '</form>';
            } else {
                $returnStr .= $this->displayWarning($lab->displayFieldDBSStatus());
            }

            $returnStr .= '</td><td>';
            $returnStr .= '</td><td>';

            if ($lab->getLabBloodStatus() == 0) {
                $returnStr .= '<form method=post id=bloodform>';
                $returnStr .= '<input type=hidden name=blooddate id=blooddate value="' . date("Y-m-d") . '">';
                $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.blood.received.fromlab', 'primkey' => $respondent->getPrimkey()));
                $windowopen = 'var blood_text=prompt(\'' . Language::labelNurseBloodResultReceivedDate() . '\', \'' . date('Y-m-d') . '\'); if(blood_text == \'\' && blood_text !== null) { alert(\'test\'); $(\'#blooddate\').val(blood_text);$(\'form#bloodform\').submit(); } else { alert(\'no\'); return false; }';
                $returnStr .= $this->showButton(Language::labelNurseButtonReceivedBloodResultFromLab(), $lab->getBarcode() == '', $windowopen);
                $returnStr .= '</form>';
            } else {
                $returnStr .= $this->displayWarning(Language::labelNurseWarningReceivedBloodResultFromLab());
            }


            $returnStr .= '</td></tr>';



            $returnStr .= '<tr><td style="width:200px">' . Language::labelNursePrintForms() . '</td><td valign=center style="width:50px">';

            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.requestform', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonLabRequestForm(), $lab->getLabBarcode() == '');
            $returnStr .= '</form>';

            $returnStr .= '</td><td>';

            $returnStr .= '<form method=post>';
            $windowopen = 'window.open(\'' . setSessionParams(array('page' => 'nurse.respondent.smallbarcodes.print', 'primkey' => $respondent->getPrimkey())) . '\', \'popupWindow\', \'width=600,height=400,scrollbars=yes,top=100,left=100\');';
            $returnStr .= $this->showButton(Language::labelNurseButtonSmallLabCodes(), $lab->getLabBarcode() == '', $windowopen);
            $returnStr .= '</form>';

            $returnStr .= '</td><td>';

            $returnStr .= '<form method=post>';
            $windowopen = 'window.open(\'' . setSessionParams(array('page' => 'nurse.respondent.labbarcode.print', 'primkey' => $respondent->getPrimkey())) . '\', \'popupWindow\', \'width=400,height=200,scrollbars=yes,top=100,left=100\');';
            $returnStr .= $this->showButton(Language::labelNurseButtonReprintLabCodes(), $lab->getLabBarcode() == '', $windowopen);
            $returnStr .= '</form>';

            $returnStr .= '</td></tr>';


            $returnStr .= '<tr><td style="width:200px">' . Language::labelNurseStorageLocation() . '</td><td valign=center style="width:50px">';

            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.blood.storage', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonBloodStorage(), $lab->getLabBarcode() == '');
            $returnStr .= '</form>';

            $returnStr .= '</td><td>';

            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.dbs.storage', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonStorageLocation(), $lab->getLabBarcode() == '');
            $returnStr .= '</form>';
            $returnStr .= '</td><td>';



            $returnStr .= '</td></tr>';



            $returnStr .= '<tr><td style="width:200px">' . Language::labelNurseShippingForms() . '</td><td valign=center style="width:50px">';
            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.requestform.print', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonShippingForms(), $lab->getLabBarcode() == '');
            $returnStr .= '</form>';

            $returnStr .= '</td><td>';

            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.requestform.print', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonTube9(), $lab->getLabBarcode() == '');
            $returnStr .= '</form>';

            $returnStr .= '</td><td>';

            $returnStr .= '<form method=post>';
            $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.cd4results', 'primkey' => $respondent->getPrimkey()));
            $returnStr .= $this->showButton(Language::labelNurseButtonCD4Results(), $lab->getLabBarcode() == '');
            $returnStr .= '</form>';



            $returnStr .= '</td></tr>';
        }
        $returnStr .= '</table>';
        return $returnStr;
    }

    function showRespondentInfo($respondent, $message) {

        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';


        $returnStr .= $message;



        $returnStr .='<div id="tabs">
  <ul>
    <li><a href="#tabs-1">' . Language::labelNurseRespondent() . ': ' . $respondent->getPrimkey() . '</a></li>';
        if ($respondent->isSelected() && isMainNurse(new User($_SESSION['URID'])) || isLabNurse(new User($_SESSION['URID']))) {
            $returnStr .='<li><a href="#tabs-2">' . Language::labelNurseMoreInfo() . '</a></li>';
            if (isLabNurse(new User($_SESSION['URID']))) {

                $returnStr .='<li><a href="#tabs-3">' . Language::labelNurseFieldDBS() . '</a></li>';
                $returnStr .='<li><a href="#tabs-4">' . Language::labelNurseLabDBS() . '</a></li>';
                $returnStr .='<li><a href="#tabs-5">' . Language::labelNurseFiles() . '</a></li>';
            }


            $returnStr .= '
    <li><a href="#tabs-6">' . Language::labelEdit() . '</a></li>';
        }
        $returnStr .='
  </ul>
  <div id="tabs-1">
    <p>';


        $returnStr .= '<table><tr><td valign=top>';
        $lab = new Lab($respondent->getPrimkey());


        $returnStr .= '<table>';


        $returnStr .= '<tr><td>' . Language::labelNurseName() . ':</td><td colspan=2 style="width:200px">';
        if (!isLabNurse(new User($_SESSION['URID']))) {
            $returnStr .= $respondent->getName();
        }
        $returnStr .= '</td></tr>';
        if (!isLabNurse(new User($_SESSION['URID']))) {
            $info1 = $this->defaultDisplayInfoAddressColumns();
            //return array('address1_dec' => Language::labelDwelling(), 'city_dec' => Language::labelVillage()); 
            foreach ($info1 as $key => $info) {
                if ($respondent->getDataByField($key) != '') {
                    $returnStr .= '<tr><td style="width:150px">' . $info . ':</td><td colspan=2 style="width:200px">' . $this->showInputBox(rtrim($key, '_dec'), $respondent->getDataByField($key), false) . '</td></tr>';
                }
            }
        }
        $info2 = $this->defaultDisplayInfo2AddressColumns();
        //return array('telephone1_dec' => Language::labelTelephone()); 
        foreach ($info2 as $key => $info) {
            if ($respondent->getDataByField($key) != '') {
                $returnStr .= '<tr><td style="width:150px">' . $info . ':</td><td colspan=2 style="width:200px">' . $this->showInputBox(rtrim($key, '_dec'), $respondent->getDataByField($key), false) . '</td></tr>';
            }
        }

        $psu = new Psu($respondent->getPuid());
        $returnStr .= '<tr><td>' . Language::labelNursePSU() . ':</td><td colspan=2>';
        if (!isLabNurse(new User($_SESSION['URID']))) {
            $returnStr .= $psu->getNumberAndName();
        }

        $returnStr .= '</td></tr>';
        if ($respondent->getSex() == 1 || $respondent->getSex() == 2) {
            $sex = array(1 => Language::labelNurseSexMale(), 2 => Language::labelNurseSexFemale());
            $returnStr .= '<tr><td>' . Language::labelNurseSex() . ':</td><td colspan=2>' . $sex[$respondent->getSex()] . '</td></tr>';
        }
        $returnStr .= '<tr><td>' . Language::labelNurseAge() . ':</td><td colspan=2>' . $respondent->getAgeFromBirthDate() . '</td></tr>';

        if (isLabNurse(new User($_SESSION['URID']))) {
            $returnStr .= '<tr><td>' . Language::labelNurseAnon() . ':</td><td colspan=2>' . $lab->getHIVFinalAnon() . '</td></tr>';
            $returnStr .= '<tr><td>' . Language::labelNurseCD4Res() . ':</td><td colspan=2>' . $lab->getCD4res() . '</td></tr>';
        }
        $returnStr .= '</table>';

//  		  $returnStr .= '<table>';
//	      $returnStr .= '<tr><td>Anon #:</td><td colspan=2>' . $lab->getHIVFinalAnon() . '</td></tr>';
//  		  $returnStr .= '</table>';
        $returnStr .= '</td><td valign=top align=right>';


        if (!isLabNurse(new User($_SESSION['URID']))) { //no picture for floidy
            $fieldname = 'VC006';
            if ($respondent->hasPicture('lab')) {
                $fieldname = 'lab';
            }
            $returnStr .= '<img src="custom/picture/index.php?id=' . $respondent->getPrimkey() . '&fieldname=' . $fieldname . '&p=show" width="200">';
        }

        $returnStr .= '</td><td valign=top>';

        $returnStr .= '<table><tr><td>' . Language::labelNurseBarCode() . ':</td><td colspan=2>';
        if ($lab->getBarcode() != '') {
            $returnStr .= '<img src=lab/barcode/barcode.php?number=' . ($lab->getBarcode()) . '>';
        }

        $returnStr .= '</td></tr>';
        $returnStr .= '<tr><td colspan=3>&nbsp;</td></tr>';

        $returnStr .= '<tr><td>' . Language::labelNurseLabBarCode() . ':</td><td colspan=2>';
        $lab = new Lab($respondent->getPrimkey());
        if ($lab->getLabBarcode() != '') {
            $returnStr .= '<img src=lab/barcode/barcode.php?number=' . $lab->getLabBarcode() . '>';
        }

        $returnStr .= '</td></tr>

</table>';

        $returnStr .= '</td></tr>';







        $returnStr .= '</table>';
        if ($respondent->isSelected()) {
            $returnStr .= $this->showInfoButtons($respondent, $lab);

            $returnStr .= '
    </p>
  </div>';

            /*
              $returnStr .= $respondent->getBirthDate();
              $returnStr .= '---';
              $returnStr .= $respondent->getAgeFromBirthDate();
             */

            if (isMainNurse(new User($_SESSION['URID'])) || isLabNurse(new User($_SESSION['URID']))) {

                $returnStr .= '
	  <div id="tabs-2" style="min-height:200px">
		<p>';
                $returnStr .= '<table width=100%><tr><td valign=top width=50%>';
                if ($lab->getConsentUrid() == 0) {
                    $returnStr .= $this->displayWarning(Language::labelNurseNoConsent());
                } else {
                    $returnStr .= '<b>' . Language::labelNurseConsent() . $lab->getConsentTs() . '</b><br/> ';
                    for ($i = 1; $i < 5; $i++) {
                        if ($lab->getConsent($i) == 1) {
                            $returnStr .= Language::consentTypes()[$i] . '<br/>';
                        }
                    }
                }
                $returnStr .= '</td><td valign=top>';



                $returnStr .= '</td></tr></table>';

                $returnStr .= '</p></div></form>';


                if (isLabNurse(new User($_SESSION['URID']))) {

                    $returnStr .= '
	  <div id="tabs-3" style="min-height:200px">
		<p>';


                    $returnStr .= '<form method=post>';
                    $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.fielddbsoverview.edit', 'primkey' => $respondent->getPrimkey()));

                    $returnStr .= $this->displayWarning(Language::labelNurseStatus() . ': ' . $lab->displayFieldDBSStatus());
                    $returnStr .= '<br/><table>';
                    $returnStr .= '<tr><td></td><td><input type=text class="form-control" style="width:200px" name=fielddbscollected value="' . addslashes($lab->getFieldDBSCollectedDate()) . '"></td></tr>';
                    $returnStr .= '<tr><td>' . Language::labelNurseReceivedDate() . '</td><td><input type=text class="form-control" style="width:200px" name=fielddbsreceived value="' . addslashes($lab->getFieldDBSReceivedDate()) . '"></td></tr>';
                    $returnStr .= '<tr><td>' . Language::labelNurseShippedDate() . '</td><td><input type=text class="form-control" style="width:200px" name=fielddbsshipped value="' . addslashes($lab->getFieldDBSShipmentDate()) . '"></td></tr>';
                    $returnStr .= '<tr><td>' . Language::labelNurseResultsFromLab() . '</td><td><input type=text class="form-control" style="width:200px" name=fielddbsshipmentreturneddate value="' . addslashes($lab->getFieldDBSReceivedDateFromLab()) . '"></td></tr>';
                    $returnStr .= '<tr><td>' . Language::labelNurseResultsClinic() . '</td><td><input type=text class="form-control" style="width:200px" name=fielddbsclinicresultsissueddate value="' . addslashes($lab->getFieldDBSClinicResultsIssued()) . '"></td></tr>';

                    $returnStr .= '<tr><td>' . Language::labelNurseStatus() . '</td><td>';
                    $returnStr .= '<select name=fielddbsstatus class="form-control" style="width:250px">';
                    $statuss = $lab->fieldDBSStatus();
                    foreach ($statuss as $key => $status) {
                        $selected = '';
                        if ($key == $lab->getFieldDBSStatus()) {
                            $selected = ' SELECTED';
                        }
                        $returnStr .= '<option value=' . $key . $selected . '>' . $status . '</option>';
                    }
                    $returnStr .= '</select>';

                    $returnStr .= '</td></tr>';



                    $returnStr .= '</table>';
                    $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonChange() . '</button>';
                    $returnStr .= '</form>';


                    $returnStr .= '</p>
	  </div>';


                    $returnStr .= '
	  <div id="tabs-4" style="min-height:200px">
		<p>';


                    if (isLabNurse(new User($_SESSION['URID']))) {
                        $returnStr .= '<b>' . Language::labelNurseDBSCardLocation() . '</b><br/>';

                        $returnStr .= Language::labelNursePosition() . ': ' . $lab->getLabDBSPosition() . ' in ';
                        $returnStr .= $lab->displayPosition($lab->getLabDBSLocation());

                        $pop = $lab->getLabDbsLocationAsArray();
                        $returnStr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . setSessionParams(array('page' => 'nurse.labdbs.overview.res')) . '&stb=' . $pop[0] . '&str=' . $pop[1] . '&sts=' . $pop[2] . '&stf=' . $pop[3] . '" target="#">' . Language::labelNurseViewBoxContent() . '</a>';
                        $returnStr .= '<br/><br/>';
                        $returnStr .= '<b>' . Language::labelNurseBloodLocation() . ':</b><br/>';
                        $returnStr .= Language::labelNursePosition() . ': ' . $lab->getLabBloodPosition() . ' in ';
                        $returnStr .= $lab->displayPosition($lab->getLabBloodLocation());




                        $pop = $lab->getLabBloodLocationAsArray();
                        $returnStr .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . setSessionParams(array('page' => 'nurse.labblood.overview.res')) . '&stb=' . $pop[0] . '&str=' . $pop[1] . '&sts=' . $pop[2] . '&stf=' . $pop[3] . '" target="#">' . 'View box content' . '</a>';

                        $returnStr .= '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
		<a href="#" data-toggle="modal" data-target="#myModal">Vail Info</a>
		
		<div id="myModal" class="modal fade">
		    <div class="modal-dialog">
		        <div class="modal-content">
	KEY<br/>
	·         E                                              EDTA anticoagulant
	<br/><br/>
	·         Bar code abbreviations:
	<br/><br/>
	o    AAA1A1                 AWIGEN SAMPLE
	<br/>
	o    BC-AAA1A.1          BUFFY COAT AWIGEN ALLIQUOT 1
	<br/>
	o    BC-AAA1A.2          BUFFY COAT AWIGEN ALIQUOT 2   (NB:  all aliquots  with numbered suffix)
	<br/>
	o    PE-AAA1A.1          PLASMA EDTA AWIGEN  ALIQOUT 1
	<br/>
	o    PK-AAA1A.1          PLASMA Na Flouride/ K Oxalate AWIGEN ALIQOUT 1
	<br/>
	o    SR-AAA1A.1          SERUM RED AWIGEN ALIQUOT 1
	<br/>
	o    UR-AAA1A.1         URINE AWIGEN ALIQUOT 1
	<br/><br/>
	·         Na Flouride/K Oxalate as anticoagulant
	<br/><br/>
	·         Serum has no anticoagulant
		        </div>
		    </div>
		</div>';


                        $tests = $lab->getBloodTests();

                        $returnStr .= '<form method=post>';
                        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.labblood.tolab', 'primkey' => $respondent->getPrimkey()));


                        $returnStr .= '<br><br>';
                        $returnStr .= '<table class=table>';
                        $returnStr .= '<tr><th></th><th>' . Language::labelNurseBloodTestName() . '</th><th>' . Language::labelNurseBloodTestSize() . '</th><th>' . Language::labelNurseBloodTestPositionBox() . '</th><th>' . Language::labelNurseBloodTestFullBarCode() . '</th><th>' . Language::labelNurseBloodTestAvailable() . '</th></tr>';

                        foreach ($tests as $key => $test) {
                            $returnStr .= '<tr><td>';
                            if ($lab->getLabBloodNotCollectedByIndex($key) == '') { //checkbox
                                $returnStr .= '<input type=checkbox name="assignid[' . $key . ']" id=ass' . $key . '>';
                            }
                            $returnStr .= '</td><td>' . $test[0] . '</td><td>' . $test[1] . '</td><td>' . ($key + $lab->getLabBloodPosition() - 1) . '</td><td>' . $lab->getLabBarcode() . ':' . sprintf("%0" . 2 . "d", $key) . '</td><td>';
                            if ($lab->getLabBloodNotCollectedByIndex($key) != '') {
                                $returnStr .= '<font color=blue>' . Language::labelNurseBloodTestNotCollected() . '</font> ';
                            } elseif ($lab->getLabBloodSentToLabByIndex($key) != '') {
                                $returnStr .= '<font color=blue>' . Language::labelNurseBloodTestSentToLab() . $lab->getLabBloodSentToLabByIndex($key) . '</font> ';
                            } else {
                                $returnStr .= '<font color=green>' . Language::labelNurseBloodTestInFreezer() . '</font> ';
                            }
                            //         $returnStr .= '<a href=>Ship to the lab</a>';


                            $returnStr .= '</td></tr>';
                        }
                        $returnStr .= '</table>';

                        $returnStr .= '<script>
					function selectfirsttwo(){
							$("#ass1").prop("checked", true);
							$("#ass2").prop("checked", true);
							$("#ass7").prop("checked", true);
							$("#ass8").prop("checked", true);
							$("#ass13").prop("checked", true);
							$("#ass14").prop("checked", true);
							$("#ass16").prop("checked", true);
							$("#ass17").prop("checked", true);
							$("#ass20").prop("checked", true);
							$("#ass21").prop("checked", true);

					}
					</script>';

                        $returnStr .= '<a href="" onclick="selectfirsttwo(); return false;">' . Language::labelNurseBloodTestVialSelection() . '</a><br/>';
                        $returnStr .= Language::labelNurseBloodTestMarkSelected() . '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonShippedToLab() . '</button>';
                        $returnStr .= Language::labelNurseOr() . '<button type="submit" class="btn btn-default navbar-btn" name="notcollected">' . Language::labelNurseBloodTestNotCollected() . '</button>';
                        $returnStr .= '</form>';
                    }

                    $returnStr .= '</p>
	  </div>';
                }
                if (isLabNurse(new User($_SESSION['URID']))) {
                    $returnStr .= '
				<div id="tabs-5" style="min-height:200px">
					<p>';

                    $windowopen = 'window.open(\'' . setSessionParams(array('page' => 'nurse.respondent.uploadfiles', 'primkey' => $respondent->getPrimkey())) . '\', \'popupWindow\', \'width=700,height=400,scrollbars=yes,top=100,left=100\');';
                    $returnStr .= $this->showButton(Language::labelNurseButtonViewUpload(), $lab->getLabBarcode() == '', $windowopen);

                    $returnStr .= '</p>
				</div>';
                }


                $returnStr .= '
	  <div id="tabs-6" style="min-height:200px">
		<p>';

                //$returnStr .= takePicture('test');
                $returnStr .= '<form method=post>';
                $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.barcode', 'primkey' => $respondent->getPrimkey()));
                $returnStr .= $this->showButton(Language::labelNurseButtonScanBarcode());
                $returnStr .= '</form>';

                $returnStr .= '<form method=post>';
                $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.labbarcode', 'primkey' => $respondent->getPrimkey()));
                $returnStr .= $this->showButton(Language::labelNurseButtonScanLabCode());
                $returnStr .= '</form>';

                $returnStr .= '<form method=post>';
                $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.picture', 'primkey' => $respondent->getPrimkey()));
                $returnStr .= $this->showButton(Language::labelNurseButtonUpdatePicture());
                $returnStr .= '</form>';
            }

            $returnStr .= '</p>
	  </div>';
        } else {

            $returnStr .= '<br/><br/>' . $this->displayInfo(Language::labelNurseWarningNotEligible());
        }


        $returnStr .= '
	</div>

	<link rel="stylesheet" href="js/jqueryui/jquery-ui.bootstrap.css">
	<script src="js/jqueryui/jquery-ui.min.js"></script>
	<script>

	  $(function() {
		$( "#tabs" ).tabs();
	  });
	  </script>
	';








        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentLabBarcode($respondent, $message) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseLabBarCode() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $message;

        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseLabBarCode() . ': ' . $respondent->getPrimkey() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';
        $returnStr .= Language::labelNurseLabBarCodeScan() . '<br/><br/>';

        $lab = new Lab($respondent->getPrimkey());
        $returnStr .= '<form method=post autocomplete=off>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.labbarcode.res', 'primkey' => $respondent->getPrimkey()));


        $returnStr .= '<table><tr><td>' . Language::labelNurseLabBarCodeScan1() . '</td><td><input type="text" class="form-control" id="scan1" name="scan1" value="' . $lab->getLabBarcode() . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseLabBarCodeScan1() . '</td><td><input type="text" class="form-control" name="scan2" value="' . $lab->getLabBarcode() . '"></td></tr></table>';

        $returnStr .= '<script>$("#scan1").focus();
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';
        $returnStr .= '                </div></div>';

//end content

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentBarcode($respondent, $message) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . 'Field barcode' . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= $message;

        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseFieldBarCode() . $respondent->getPrimkey() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';
        $returnStr .= Language::labelNurseFieldBarCodeScan() . '<br/><br/>';

        $lab = new Lab($respondent->getPrimkey());
        $returnStr .= '<form method=post autocomplete=off>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.barcode.res', 'primkey' => $respondent->getPrimkey()));


        $returnStr .= '<table><tr><td>' . Language::labelNurseLabBarCodeScan1() . '</td><td><input type="text" class="form-control" id="scan1" name="scan1" value="' . $lab->getBarcode() . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseLabBarCodeScan2() . '</td><td><input type="text" class="form-control" name="scan2" value="' . $lab->getBarcode() . '"></td></tr></table>';

        $returnStr .= '<script>$("#scan1").focus();
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';
        $returnStr .= '                </div></div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentTakePicture($respondent) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseTakePicture() . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= takePicture('lab', $respondent->getPrimkey());

//end content
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentConsent($respondent) {
        $lab = new Lab($respondent->getPrimkey());

        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseConsentShort() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>Consent: ' . $respondent->getPrimkey() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';

        $returnStr .= '<form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.consent.res', 'primkey' => $respondent->getPrimkey()));

        $checked = '';
        if ($lab->isRefusal()) {
            $checked = ' CHECKED';
        }
        $returnStr .= '<input type=checkbox name=refusal id=refusal value=1' . $checked . '>' . Language::labelNurseRespondentRefusedParticipate() . '<br/>';

        $returnStr .= '<script>
$(document).ready(function() {
    //set initial state.
//    $("#refusal").val($(this).is(":checked"));
    updatedivs();

    $("#refusal").change(function() {
        updatedivs();
//        $("#refusal").val($(this).is(":checked")); 
      });
    });

      function updatedivs(){
        if($("#refusal").is(":checked")) {
          $(\'#consentform :input\').attr(\'disabled\', true);
          $(\'#consentform\').css("background","#dddddd");

          $(\'#consentform\').find("input[type=\'checkbox\']").prop("checked", false);


          $(\'#refusalreason :input\').attr(\'disabled\', false);
          $(\'#refusalreason\').css("background","#ffffff");
          $(\'#refusalreason\').show(250);


        }
        else {
          $(\'#consentform :input\').attr(\'disabled\', false);
          $(\'#consentform\').css("background","#ffffff");

          $(\'#refusalreason :input\').attr(\'disabled\', true);
          $(\'#refusalreason\').css("background","#dddddd");
          $("#refusalreason").find("input:text").val(""); 
          $(\'#refusalreason\').hide(250);


        }
      }

        </script>
        ';

//onclick="$(\'#consentform :input\').attr(\'disabled\', true);"
        $returnStr .= '<br/>';
        $returnStr .= '<div id=refusalreason style="background-color: #dddddd;">';
        $returnStr .= '<br><table>';
        $returnStr .= '<tr><td>' . Language::labelNurseRespondentRefusalReason() . '</td><td><input type=text name=reason class="form-control" value="' . addslashes($lab->getRefusalReason()) . '"></td></tr>';
        $returnStr .= '<tr><td>' . labelNurseRespondentRefusalDate() . '</td><td><input type=text name=refusaldate class="form-control" value="' . addslashes($lab->getRefusalDate()) . '"></td></tr>';
        $returnStr .= '</table><br/></div>';


        $returnStr .= '<div id=consentform style="background-color: #ffffff;"><br/>' . Language::labelNurseConsentFor() . '<br/><br/>';

        foreach (Language::consentTypes() as $key => $consent) {
            $checked = '';
            if ($lab->getConsent($key) == 1) {
                $checked = ' CHECKED';
            }
            $returnStr .= '<label><input type=checkbox name=consent[' . $key . '] value=1' . $checked . '> ' . $consent . '</label><br/>';
        }
        $returnStr .= '</div>';
        if ($lab->getConsentUrid() == 0) { //only if not assigned yet!
            $returnStr .= '<br/>' . Language::labelNurseStaffConsent();
            $returnStr .= '<select name="consenturid" class="form-control" style="width:220px">';

            $drivers = Language::labelNurseDrivers();
            foreach ($drivers as $i => $driver) {
//          for($i = 1; $i < 5; $i++){
                $returnStr .= '<option value=' . $i . '>' . $driver . ' (' . $i . ')</option>';
            }
            $returnStr .= '<option value=99>' . Language::labelNurseOtherStaff() . '</option>';

            $returnStr .= '</select>';
            $returnStr .= '<br/>';
        }
        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';




//end content

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showStartButton($respondentOrHousehold, $suid = 3, $alert = false, $btntext = 'Start', $ok = false) {
        $preload = array();
        $lab = new Lab($respondentOrHousehold->getPrimkey());
        if ($suid == 4) {
            if ($lab->getConsent2() == 1 || $lab->getConsent3() == 1) { //station 2 = YES
                $preload['RgetsStation2'] = '1';
            }
            if ($lab->getConsent4() == 1 && $lab->getConsent5() == 1) { //station 5a = YES
                $preload['RgetsStation5a'] = '1';
            }
        }

        $content = '';
        $content .= "<form method=post>";
        $content .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($respondentOrHousehold->getPrimkey(), Config::directLoginKey())) . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '1' . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
        $content .= '<input type=hidden name=' . POST_PARAM_MODE . ' value="' . MODE_CAPI . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . $suid . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_PRELOAD . ' value="' . encodeSession($respondentOrHousehold->getPreload($preload)) . '">';
        $disabled = '';
        $btntype = 'default';
        if ($alert) {
            $disabled = 'disabled=true';
            $btntype = 'danger';
        }

        if ($ok) {
            $disabled = 'disabled=true';
            $btntype = 'success';
            $type = 'submit';
        }


        $content .= '<button type="submit" id="startsurveybtn" class="btn btn-' . $btntype . ' navbar-btn" ' . $disabled . ' style="width:200px">' . $btntext . '</button>';
        $content .= "</form>";
        return $content;
    }

    function showButton($text, $alert = false, $javascript = '', $ok = false) {
        $disabled = '';
        $btntype = 'default';
        $type = 'submit';
        if ($alert) {
            $disabled = 'disabled=true';
            $btntype = 'danger';
        }
        if ($ok) {
            $disabled = '';
            $btntype = 'success';
            $type = 'submit';
        }
        if ($javascript != '') {
            $javascript = ' onclick="' . $javascript . '"';
            $type = 'button';
        }
        return '<button type="' . $type . '" class="btn btn-' . $btntype . ' navbar-btn" ' . $disabled . ' style="min-width:200px"' . $javascript . '>' . $text . '</button>';
    }

    function showFieldDBS() {

        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseDBSTToLab() . '</li>';
        $returnStr .= '</ol>';

        global $db;
        $query = 'select * from ' . Config::dbSurveyData() . '_lab where fielddbsstatus = 1';
        $result = $db->selectQuery($query);
        if ($result != null) {
            $returnStr .= '<table>';
            while ($row = $db->getRow($result)) {
                $lab = new Lab($row['primkey']);
                $returnStr .= '<tr><td>' . $lab->getBarCode() . '</td><td>' . $lab->getFieldDBSCollectedDate() . '</td></tr>';
            }
            $returnStr .= '</table>';
        }



//end content

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentBloodStorageLocation($respondent) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseBloodStorage() . '</li>';
        $returnStr .= '</ol>';





        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseBloodStorage() . ': ' . $respondent->getPrimkey() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';
        $returnStr .= Language::labelNurseBloodStoredAt() . '<br/><br/>';

        $lab = new Lab($respondent->getPrimkey());
        $returnStr .= '<form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.blood.storage.res', 'primkey' => $respondent->getPrimkey()));

        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtStartNumber() . '</td><td><input type=text class="form-control" style="width:80px" name=stp value="' . addslashes($lab->getLabBloodPosition()) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtBoxNumber() . '</td><td>' . $this->displayNumberSelect(20, 'stb', $lab->getLabBloodLocationByIndex(0)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtRackNumber() . '</td><td>' . $this->displayNumberSelect(6, 'str', $lab->getLabBloodLocationByIndex(1)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtShelveNumber() . '</td><td>' . $this->displayNumberSelect(3, 'sts', $lab->getLabBloodLocationByIndex(2)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtFreezerNumber() . '</td><td>' . $this->displayNumberSelect(4, 'stf', $lab->getLabBloodLocationByIndex(3)) . '</td></tr>';
        $returnStr .= '</table>';

        /*
          -->starting number
          24 boxes in a rack
          6 racks on one shelf
          3 shelves per freezer
          4 freezers
          starting number
         */


        $returnStr .= '<br/>';
        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';


//end content

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentDBSStorageLocation($respondent) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseDBSStorage() . '</li>';
        $returnStr .= '</ol>';





        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseDBSStorage() . ': ' . $respondent->getPrimkey() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';
        $returnStr .= Language::labelNurseDBSStoredAt() . '<br/><br/>';

        $lab = new Lab($respondent->getPrimkey());
        $returnStr .= '<form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.dbs.storage.res', 'primkey' => $respondent->getPrimkey()));

        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtOrderNumber() . '</td><td><input type=text class="form-control" style="width:80px" name=stp value="' . addslashes($lab->getLabDBSPosition()) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtBoxNumber() . '</td><td>' . $this->displayNumberSelect(2, 'stb', $lab->getLabDBSLocationByIndex(0)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtRackNumber() . '</td><td>' . $this->displayNumberSelect(5, 'str', $lab->getLabDBSLocationByIndex(1)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtShelveNumber() . '</td><td>' . $this->displayNumberSelect(10, 'sts', $lab->getLabDBSLocationByIndex(2)) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtFreezerNumber() . '</td><td>' . $this->displayNumberSelect(1, 'stf', $lab->getLabDBSLocationByIndex(3)) . '</td></tr>';
        $returnStr .= '</table>';

        /*
          -->starting number
          24 boxes in a rack
          6 racks on one shelf
          3 shelves per freezer
          4 freezers
          starting number
         */


        $returnStr .= '<br/>';
        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';


//end content

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function displayNumberSelect($max, $name, $selected = 0) {
        $returnStr = '';
        $returnStr .= '<select name=' . $name . ' class="form-control" style="width:80px">';
        for ($i = 1; $i <= $max; $i++) {
            $selectedstr = '';
            if ($selected == $i) {
                $selectedstr = ' SELECTED';
            }
            $returnStr .= '<option value=' . $i . $selectedstr . '>' . $i . '</option>';
        }
        $returnStr .= '</select>';

        return $returnStr;
    }

    function showRespondentLabRequest($respondent) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseLabRequestForm() . '</li>';
        $returnStr .= '</ol>';



        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseLabRequestForm() . ': ' . $respondent->getPrimkey() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';
        $returnStr .= '<b>' . Language::labelNurseLabRequestFormTitle() . '</b><br/><br/>';

        $lab = new Lab($respondent->getPrimkey());
        $returnStr .= '<form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.requestform.res', 'primkey' => $respondent->getPrimkey()));

        $requestForm = json_decode($lab->getRequestForm());

        $requestFormres = array();
        $requestFormres['date'] = '';
        $requestFormres['gender'] = 0;
        $requestFormres['entry1'] = '';
        $requestFormres['entry2'] = '';
        $requestFormres['entry3'] = '';
        $requestFormres['entry4'] = '';
        $requestFormres['entry5'] = '';
        $requestFormres['entry6'] = '';
        $requestFormres['comments'] = '';

        $requestFormres['collectedby'] = '';
        $requestFormres['collecteddate'] = '';
        $requestFormres['collectedtime'] = '';
        $requestFormres['receivedby'] = '';
        $requestFormres['receiveddate'] = '';
        $requestFormres['receivedtime'] = '';



        foreach ($requestForm as $key => $value) {
            $requestFormres[$key] = $value;
        }
        if ($requestFormres['date'] == '') {
            $requestFormres['date'] = date('Y-m-d');
        }

        $returnStr .= '<table class=table>';
        $returnStr .= '<tr><td style="width:120px">Date</td><td><input name=date type=text class="form-control" value="' . $requestFormres['date'] . '" style="width:120px"></tr>';
        $returnStr .= '<tr><td style="width:120px">' . Language::labelNurseLabBarCode() . '</td><td><input name=labbarcode type=text class="form-control" style="width:120px" value="' . addslashes($lab->getLabBarcode()) . '"></td><tr>';
        $returnStr .= '<tr><td style="width:120px">' . Language::labelNurseSex() . '</td><td><select name=gender class="form-control" style="width:120px">';

        $selected = array('', '', '', '', '', '', '');
        $selected[$requestFormres['gender']] = 'SELECTED';
        $returnStr .= '<option value=1 ' . $selected[1] . '>' . Language::labelNurseSexMale() . '</option>';
        $returnStr .= '<option value=2 ' . $selected[2] . '>' . Language::labelNurseSexFemale() . '</option>';
        $returnStr .= '</select></td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '<table class=table width="600px">';
        $returnStr .= '<tr><th colspan=2>STORAGE AT AGINCOURT LAB</th><th>PROCESSING & SHIPPING INSTRUCTIONS</th></tr>';
        $returnStr .= '<tr><td><input name=entry1 type=text class="form-control" style="width:40px" value="' . $requestFormres['entry1'] . '"></td>';
        $returnStr .= '<td width=35%>6 ML (No anticoagulant)- Lipid profile</td>';
        $returnStr .= '<td>Leave the tubes to clot @ room temperature 15-30 min;       						spun @ room temperature for 10 mins at 3000rpm; 							Aliquot 4 x 1ML; store 2 x 1ML @ -80 °C, ship 2 aliquots in 						< 3 days @4 °C </td>';
        $returnStr .= '</tr>';

        $returnStr .= '<tr><td><input name=entry2 type=text class="form-control" style="width:40px" value="' . $requestFormres['entry2'] . '"></td>';
        $returnStr .= '<td>4 ML (Anticoagulant Potassium Oxalate) HbA1c</td>';
        $returnStr .= '<td>Invert sample 5-10 times, let  sample   stand for 15-							30mins before spinning at room temperature for 10mins 							@3000rpm; store 2 x 1 ML plasma @     -80 °C  (ship 1 							cryovials).</td>';
        $returnStr .= '</tr>';

        $returnStr .= '<tr><td><input name=entry3 type=text class="form-control" style="width:40px" value="' . $requestFormres['entry3'] . '"></td>';
        $returnStr .= '<td>6 ML (EDTA as anticoagulant)</td>';
        $returnStr .= '<td>Invert tubes 5-10 times,  keep tubes at room 								temperature/fridge at 4  °C prior to removing the buffy 							coats; centrifuge samples @900-1100g for 10 min @room 						temperature; aliquot 2 x 1ML and store @-80 °C; aliquot  							0.5ML  buffy  from each tube and store @ -80 °C. ship 							both buffy tubes to SBIMB for DNA extraction.</td>';
        $returnStr .= '</tr>';

        $returnStr .= '<tr><td><input name=entry4 type=text class="form-control" style="width:40px" value="' . $requestFormres['entry4'] . '"></td>';
        $returnStr .= '<td>5MLCD4  STABILIZATION TUBE</td>';
        $returnStr .= '<td>Store @ 15-27 °C until     shipment</td>';
        $returnStr .= '</tr>';

        $returnStr .= '<tr><td><input name=entry5 type=text class="form-control" style="width:40px" value="' . $requestFormres['entry5'] . '"></td>';
        $returnStr .= '<td>20 ML Mid-stream Urine</td>';
        $returnStr .= '<td>Do not store above 28 °C until ready for processing; 							centrifuge @2500rpm for 5 minutes; aliquot supernatant 							into 4 cryovials  of 2 ML ach; store @-80 °C (ship 2 							cryovials) </td>';
        $returnStr .= '</tr>';

        $returnStr .= '<tr><td><input name=entry6 type=text class="form-control" style="width:40px" value="' . $requestFormres['entry6'] . '"></td>';
        $returnStr .= '<td>DBS card</td>';
        $returnStr .= '<td>Store -20 °C</td>';
        $returnStr .= '</tr>';

        $returnStr .= '</table>';


        $returnStr .= Language::labelNurseComments() . ':<br/><textarea name=comments cols=80 rows=3>' . $requestFormres['comments'] . '</textarea>';

        $returnStr .= '<table class=table width="600px">';
        $returnStr .= '<tr><td>' . Language::labelNurseCollectedBy() . '</td><td><input name=collectedby type=text class="form-control" style="width:180px" value="' . $requestFormres['collectedby'] . '"></td><td>Date</td><td><input name=collecteddate type=text class="form-control" style="width:120px" value="' . $requestFormres['collecteddate'] . '"></td><td>Time</td><td><input name=collectedtime type=text class="form-control" style="width:120px" value="' . $requestFormres['collectedtime'] . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseReceivedBy() . '</td><td><input name=receivedby type=text class="form-control" style="width:180px" value="' . $requestFormres['receivedby'] . '"></td><td>Date</td><td><input name=receiveddate type=text class="form-control" style="width:120px" value="' . $requestFormres['receiveddate'] . '"></td><td>Time</td><td><input name=receivedtime type=text class="form-control" style="width:120px" value="' . $requestFormres['receivedtime'] . '"></td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '</table>';

        $returnStr .= '<br/>';
        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';


//end content

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showLabDbsOverview() {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseLabDBSOverview() . '</li>';
        $returnStr .= '</ol>';



        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseDBSBoxOverview() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';

        $returnStr .= Language::labelNurseBoxAtLocation();
        $returnStr .= '<form method=post target="_blank">';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.labdbs.overview.res'));

        $returnStr .= '<table class="table">';
        $returnStr .= '<tr><td style="width:150px;">' . Language::labelNurseBloodStoredAtBoxNumber() . '</td><td>' . $this->displayNumberSelect(2, 'stb') . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtRackNumber() . '</td><td>' . $this->displayNumberSelect(5, 'str') . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtShelveNumber() . '</td><td>' . $this->displayNumberSelect(10, 'sts') . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtFreezerNumber() . '</td><td>' . $this->displayNumberSelect(1, 'stf') . '</td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonShow() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showLabBloodOverview() {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseLabBloodOverview() . '</li>';
        $returnStr .= '</ol>';



        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseBloodBoxOverview() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';

        $returnStr .= Language::labelNurseBoxAtLocation();
        $returnStr .= '<form method=post target="_blank">';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.labblood.overview.res'));

        $returnStr .= '<table class="table">';
        $returnStr .= '<tr><td style="width:150px;">' . Language::labelNurseBloodStoredAtBoxNumber() . '</td><td>' . $this->displayNumberSelect(20, 'stb') . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtRackNumber() . '</td><td>' . $this->displayNumberSelect(6, 'str') . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtShelveNumber() . '</td><td>' . $this->displayNumberSelect(3, 'sts') . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseBloodStoredAtFreezerNumber() . '</td><td>' . $this->displayNumberSelect(4, 'stf') . '</td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonShow() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentCD4($respondent) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseCD4Results() . '</li>';
        $returnStr .= '</ol>';





        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::labelNurseCD4Results() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';


        $returnStr .= '<form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.cd4results.res', 'primkey' => $respondent->getPrimkey()));
        $lab = new Lab($respondent->getPrimkey());
        $CD4date = $lab->getCD4date();
        if ($CD4date == '') {
            $CD4date = date('Y-m-d');
        }

        $returnStr .= '<table class="table">';
        $returnStr .= '<tr><td style="width:150px;">' . Language::labelNurseCD4ResultCode() . '</td><td><input style="width:120px" type=text name=cd4res class="form-control" value="' . addslashes($lab->getCD4res()) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseCD4ResultDate() . '</td><td><input style="width:120px" type=text name=cd4date class="form-control" value="' . $CD4date . '"></td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';

        $returnStr .= '                </div></div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentFieldNurseAssign($respondent) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

//begin content

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.search'), Language::labelSearch()) . '</li>';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.respondent.info', 'primkey' => $respondent->getPrimkey()), Language::labelNurseRespondent() . ' ' . $respondent->getPrimkey()) . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseAssignNurse() . '</li>';
        $returnStr .= '</ol>';

        $returnStr .= Language::labelNurseAssignNurseHomeVisit() . '<br/>';
        $users = new Users();

        $returnStr .= '<form method=post>';
        $returnStr .= setSessionParamsPost(array('page' => 'nurse.respondent.assigntofieldnurse.res', 'primkey' => $respondent->getPrimkey()));

        $returnStr .= '<select name=urid class="form-control" style="width:240px">';

        $fieldnurses = $users->getFieldNurses();
        foreach ($fieldnurses as $nurse) {
            $returnStr .= '<option value=' . $nurse->getUrid() . '>' . $nurse->getName() . '</option>';
        }
        $returnStr .= '</select>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::labelNurseButtonSave() . '</button>';
        $returnStr .= '</form>';


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showRespondentFieldNurseInfo($respondent, $message = '') {

        $lab = new Lab($respondent->getPrimkey());

        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.home'), Language::labelHome()) . '</li>';
        $returnStr .= '<li class="active">' . $respondent->getPrimkey() . '</li>';
        $returnStr .= '</ol>';

//CONTENT
        $returnStr .= $message;
        $displayRespondent = new DisplayRespondent();

        $returnStr .= '<div class="row">';
        $returnStr .= '<div class="col-md-5">';
        $returnStr .= $displayRespondent->showInfoSub($respondent);
        $returnStr .= '</div>';
        $returnStr .= '</div>';

        $returnStr .= '<hr>';
        $returnStr .= $this->showStartButton($respondent, 3, /* $lab->getLabBarcode() == '' */ false, 'Start survey', $lab->getSurvey() == 2);


        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showFollowup($message) {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
//        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.home'), 'Home') . '</li>';
        $returnStr .= '<li class="active">' . 'Followup' . '</li>';
        $returnStr .= '</ol>';

//CONTENT
        $returnStr .= $message;

        $respondents = new Respondents();
        $respondents = $respondents->getRespondentsForFollowup();

        if (sizeof($respondents) > 0) {
            $returnStr .= '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
	<thead>
		<tr>
			<th>' . Language::labelNurseRespondentID() . '</th><th>' . Language::labelNurseName() . '</th><th>' . Language::labelDwellingID() . '</th><th>' . Language::labelVillage() . '</th></tr>';
            foreach ($respondents as $respondent) {
                $returnStr .= '<tr>';
                $refpage = 'nurse.followup';
                $returnStr .= '<td>' . setSessionParamsHref(array('page' => $refpage . '.info', 'primkey' => $respondent->getPrimkey()), $respondent->getPrimkey()) . '</td>';
                $returnStr .= '<td>' . $respondent->getName() . '</td><td>' . $respondent->getAddress1() . '</td><td>' . $respondent->getCity() . '</td></tr>';
            }
            $returnStr .= '</table>';
        } else {
            $returnStr .= $this->displayError(Language::labelNurseWarningNoCalls());
        }
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showFollowupInfo($respondent, $message = '') {
        $returnStr = $this->showNurseHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
//        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'nurse.home'), 'Home') . '</li>';
        $returnStr .= '<li class="active">' . Language::labelNurseFollowUp() . '</li>';
        $returnStr .= '</ol>';

//CONTENT

        $returnStr .= $message;

        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelNurseFollowUpPhone1() . '</td><td>' . getData($respondent->getPrimkey(), 'TG003', 2) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseFollowUpPhone2() . '</td><td>' . getData($respondent->getPrimkey(), 'TG004', 2) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseFollowUpHouseholdHead() . '</td><td>' . getData($respondent->getPrimkey(), 'TG008', 2) . '</td></tr>';
        $returnStr .= '<tr><td>' . Language::labelNurseFollowUpSomeoneElse() . '</td><td>' . getData($respondent->getPrimkey(), 'TG010', 2) . '</td></tr>';
        $returnStr .= '</table>';

//CALL AND INFO
        $returnStr .= '<hr>';

        $refpage = 'nurse.followup';

        $returnStr .= '<form method=post>';

        $returnStr .= setSessionParamsPost(array('page' => $refpage . '.addcontactres', 'primkey' => $respondent->getPrimkey()));

        $returnStr .= '<input type="hidden" name="contactwith" id="contactwith" value="' . loadvar('contactwith') . '">';

        $returnStr .= '<table width=100%>';



        $returnStr .= '<tr><td style="width:80px">' . Language::labelNurseFollowUpDateTime() . '</td><td style="width:220px">';

        if (loadvar('contactts') != '') {

            $returnStr .= $this->displayDateTimePicker('contactts', 'contactts', loadvar('contactts'), getSMSLanguagePostFix(getSMSLanguage()), "true", "true", Config::usFormatSMS());
        } else {

            $returnStr .= $this->displayDateTimePicker('contactts', 'contactts', date('m/d/Y h:i a'), getSMSLanguagePostFix(getSMSLanguage()), "true", "true", Config::usFormatSMS());
        }

        $returnStr .= '</td><td colspan=2></td></tr>';

        $returnStr .= '<tr><td style="width:90px">' . Language::labelOutcome() . '</td><td valign=top colspan=2>';



        $returnStr .= '<select class="form-control" name=contactcode id=outcomecode style="width:300px"><option value=-1>' . Language::labelPleaseSelect() . '</option>';

        $dispositionCodes = Language::optionsDispositionFollowupContactCode($respondent);

        foreach ($dispositionCodes as $option => $dispositionCode) {
            if ($dispositionCode[5] == 1) { //display in dropdown
                $selected = '';
                if (loadvar('contactcode') == $option) {
                    $selected = ' SELECTED';
                }

                $returnStr .= '<option value="' . $option . '"' . $selected . '>' . $option . ': ' . $dispositionCode[1] . '</option>';
            }
        }

        $returnStr .= '</select></td><td>

    <div id="contactwithdiv" style="display: none"><table width=100%><tr><td style="width:90px">' . Language::labelContactWith() . '</td><td>

			<div id="selector" class="btn-group">

				<button type="button" class="btn btn-default" value=1>' . Language::labelHouseholdMember() . '</button>

				<button type="button" class="btn btn-default" value=2>' . Language::labelProxy() . '</button>

			</div></td></tr></table>

      <div id="contactperson" style="display: none"><table width=100%><tr><td style="width:90px">' . Language::labelProxyName() . '</td><td>

        <input type=text class="form-control" name="contactperson"></td></tr></table>

      </div>



    </div>';



        $returnStr .= '<tr><td valign=top>' . Language::labelRemark() . '</td><td colspan=3>';

        $returnStr .= '<textarea class="form-control" name="contactremark">' . loadvar('contactremark') . '</textarea>';

        $returnStr .= '</td></tr>';


        /*
          $returnStr .= '<tr><td style="width:80px">' . Language::labelAppointment() . '</td><td style="width:220px">';
          $returnStr .= $this->displayDateTimePicker('contactappointment', 'contactappointment', loadvar('contactappointment'), getSMSLanguagePostFix(getSMSLanguage()), "true", "true", Config::usHourFormatSMS());
          $returnStr .= '</td><td colspan=2></td></tr>';
         */

        $returnStr .= '</table>';



        $returnStr .= '<hr>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonAddContact() . '</button>';

        $returnStr .= '</form> <b> OR </b>';

        $returnStr .= $this->showStartButton($respondent, 7, false, 'Start follup survey', false);


        $returnStr .= '<br/>';



        $returnStr .= '

<script>





$(document).ready(function() {

    $(\'#outcomecode\').change(function() {

        $(\'#contactwithdiv\').css("display", "none");

        var element = $(this).find(\'option\').filter(\':selected\').val();';



        $check = array();

        $followup = Language::optionsDispositionFollowupContactCode($respondent);

        foreach ($followup as $option => $follow) {

            if ($follow[0] == '1') {

                $check[] = $option;
            }
        }

        $returnStr .= 'if (element == "' . implode($check, '" || element == "') . '") {';

        //$returnStr .= 'alert("ADASD");';

        $returnStr .= '$(\'#contactwithdiv\').css("display", "block"); }';

        $returnStr .= '   switch(element){';

        $messageDispositionCodes = Language::optionsDispositionContactCode($respondentOrHousehold);

        foreach ($messageDispositionCodes as $option => $message) {

            $returnStr .= 'case "' . $option . '":  $( "#outcomehelp" ).html("' . addslashes($message[2]) . '"); break;';
        }

        $returnStr .= 'default: $( "#outcomehelp" ).html("' . addslashes(Language::messageSelectContactOutcome()) . '");';

        $returnStr .= '    }



    });



  $(\'#outcomecode\').change(); //in case reloaded page



});



$(\'#selector button\').click(function() {

    $(\'#contactperson\').css("display", "none");

    $(\'#selector button\').addClass(\'active\').not(this).removeClass(\'active\');

    $(\'#contactwith\').val("1");

    if ($(this).val() == "2") {

      $(\'#contactperson\').css("display", "block");

      $(\'#contactwith\').val("2");

    }

});

if ($(\'#contactwith\').val() == "2"){

  $(\'#selector button\').click();

}

</script>

';




//END CONTENT

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

}

?>