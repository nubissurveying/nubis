<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayInterviewer extends DisplayRespondent {

    public function __construct() {

        parent::__construct();
    }

    public function showMain($respondentsOrHouseholds, $message = '') {

        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li class="active">' . Language::labelHome() . '</li>';
        $returnStr .= '</ol>';

//CONTENT
        $returnStr .= $message;
        if (sizeof($respondentsOrHouseholds) > 0) {
            $returnStr .= $this->displayInfo(Language::messageSelectRespondent());
            $arr = array_values($respondentsOrHouseholds);
            if ($arr[0] instanceof Respondent) { //this is a respondent
                $returnStr .= $this->showRespondentsTable($respondentsOrHouseholds);
            } else { //household
                $returnStr .= $this->showHouseholdsTable($respondentsOrHouseholds);
            }
        } else {
            $returnStr .= $this->displayWarning(Language::messageNoRespondentsAssigned(), "outcomehelp"); // '<div class="alert alert-warning" id="outcomehelp">' . Language::errorNoRespondentsAssigned() . '</div>';
        }
        
//END CONTENT

        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    /*

      function showMainHouseholds($households, $message){



      //"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]



      $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle(), $message);



      $returnStr .= '<div id="wrap">';

      $returnStr .= $this->showNavBar();

      $returnStr .= '<div class="container"><p>';



      $returnStr .= '<ol class="breadcrumb">';

      $returnStr .= '<li class="active">Home</li>';

      $returnStr .= '</ol>';



      //CONTENT

      $returnStr .= $message;



      if (sizeof($households) > 0) {

      $returnStr .= Language::messageSelectRespondent();

      $returnStr .= $this->showHouseholdsTable($households);

      }

      else {    $content .= '<tr><td colspan=3><b>HH members</b></td></tr>';



      $respondents = $household->getRespondents();

      foreach($respondents as $respondent){

      $content .= '<tr><td colspan=3>' . $respondent->getFirstname() . '</td></tr>';

      }



      $returnStr .= $this->displayWarning(Language::messageNoRespondentsAssigned(), "outcomehelp"); // '<div class="alert alert-warning" id="outcomehelp">' . Language::errorNoRespondentsAssigned() . '</div>';

      }



      //END CONTENT

      $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap

      $returnStr .= $this->showBottomBar();



      $returnStr .= $this->showFooter(false);

      return $returnStr;

      }



     */

    /*

      function showHouseholdInfo($household){

      $content = $message;



      $content .= '<div class="row">';

      $content .= '<div class="col-6 col-sm-6 col-lg-5">';



      $content .= $this->showInfoHouseholdSub($household);

      $content .= '</div>';

      $content .= '<div class="col-6 col-sm-6 col-lg-5">';

      $content .= '<table>';

      $content .= '<tr><td style="width:100px">Status:</td><td style="width:200px">' . $this->displayStatus($household) . '</td></tr>';

      $content .= '<tr><td># of contacts:</td><td>' . sizeof($household->getContacts()) . '</td></tr>';



      $content .= '<tr><td colspan=2><hr></td></tr>';



      $content .= '<tr><td colspan=3><b>HH members</b></td></tr>';



      $respondents = $household->getRespondents();

      foreach($respondents as $respondent){

      $content .= '<tr><td colspan=3>' . $respondent->getFirstname() . '</td></tr>';

      }





      $content .= '</table>';

      $content .= '</div>';



      $content .= '</div>';

      $content .= '<hr>';

      $content .= '<hr>';





      $content .= '<table width=100%><tr><td>';

      $content .= '<form method=post>';

      $content .= setSessionParamsPost(array('page' => 'interviewer.household.addcontact', 'hhid' => $household->getHhid()));

      $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonAddContact() . '</button>';

      $content .= '</form>';

      $content .= '</td><td align=right>';



      if ($household->getStatus() != 2){

      $content .= '<form method=post>';

      $content .= setSessionParamsPost(array('page' => 'interviewer.household.startsurvey', 'hhid' => $household->getHhid()));

      $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonStartSurvey() . '</button>';

      $content .= '</form>';

      }

      $content .= '</td></tr></table>';



      return $this->showInterviewHouseholdPageWithSideBar($household, $content, Language::labelInfoCap());

      }

     */


    /*

      function showInfoHouseholdSub($household, $edit = false){

      $returnStr = '<table>';

      $returnStr .= '<tr><td style="width:100px">Name:</td><td><b>' . $this->showInputBox('name', $household->getName(), $edit) . '</td></td></td></tr>';

      $returnStr .= '<tr><td valign=top>Address 1:</td><td colspan=2 style="width:200px">' . $this->showInputBox('address1', $household->getAddress1(), $edit) . '</td></tr>';

      $returnStr .= '<tr><td>Address 2:</td><td colspan=2>' . $this->showInputBox('address2', $household->getAddress2(), $edit) . '</td></tr>';

      $returnStr .= '<tr><td>City / Zip:</td><td>' . $this->showInputBox('city', $household->getCity(), $edit) . '</td><td>' . $this->showInputBox('zip', $household->getZip(), $edit) . '</td></tr>';

      $returnStr .= '<tr><td>Telephone:</td><td colspan=2>' . $this->showInputBox('telephone', $household->getTelephone1(), $edit) . '</td></tr>';



      $returnStr .= '<tr><td colspan=3><hr></td></tr>';



      $returnStr .= '<tr><td>PSU:</td><td>' . $household->getPuid() . '</td></tr>';

      $returnStr .= '<tr><td>GPS:</td><td></td></tr>';





      //    $returnStr .= '<tr><td>Fax:</td><td colspan=2>' . $this->showInputBox('fax', $household->getTelephone2(), $edit) . '</td></tr>';

      //    $returnStr .= '<tr><td>Email:</td><td colspan=2>' . $this->showInputBox('email', $respondent->getEmail(), $edit) . '</td></tr>';



      $returnStr .= '</table>';

      return $returnStr;

      } */

    function showAddContact($respondentOrHousehold, $message = '') {



        $refpage = 'interviewer.household';

        if ($respondentOrHousehold instanceof Respondent) {

            $refpage = 'interviewer.respondent';
        }

        $header = '

<link rel="stylesheet" type="text/css" href="bootstrap/css/sticky-footer-navbar.min.css">';        

        $returnStr = $this->showHeader(Language::messageSMSTitle(), $header);

        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();

        $returnStr .= '<div class="container"><p>';



        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';

        $returnStr .= '<div class="col-xs-12 col-sm-9">';





        $returnStr .= '<ol class="breadcrumb">';

        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'interviewer.home'), Language::labelHome()) . '</li>';

        if ($respondentOrHousehold instanceof Respondent && dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level link
            $household = $respondentOrHousehold->getHousehold();

            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'interviewer.household.info', 'primkey' => $household->getPrimkey()), Language::householdLabelCap()) . '</li>';
        }

        $returnStr .= '<li>' . setSessionParamsHref(array('page' => $refpage . '.info', 'primkey' => $respondentOrHousehold->getPrimkey()), Language::householdOrRespondentLabelCap($respondentOrHousehold)) . '</li>';

        $returnStr .= '<li class="active">' . Language::labelAddContactCap() . '</li>';

        $returnStr .= '</ol>';

//CONTENT





        $returnStr .= $message;

        $returnStr .= '<form method=post>';

        $returnStr .= setSessionParamsPost(array('page' => $refpage . '.addcontactres', 'primkey' => $respondentOrHousehold->getPrimkey()));

        $returnStr .= '<input type="hidden" name="contactwith" id="contactwith" value="' . loadvar('contactwith') . '">';

        $returnStr .= '<table width=100%>';


        $returnStr .= '<tr><td style="width:80px">Date/Time:</td><td style="width:220px">';

        if (loadvar('contactts') != '') {

            $returnStr .= $this->displayDateTimePicker('contactts', 'contactts', loadvar('contactts'), getSMSLanguagePostFix(getSMSLanguage()), "true", "true", Config::usFormatSMS());
        } else {

            $returnStr .= $this->displayDateTimePicker('contactts', 'contactts', date('Y-m-d H:i:s'), getSMSLanguagePostFix(getSMSLanguage()), "true", "true", Config::usFormatSMS());
        }

        $returnStr .= '</td><td colspan=2></td></tr>';

        $returnStr .= '<tr><td style="width:90px">' . Language::labelOutcome() . '</td><td valign=top>';



        $returnStr .= '<select class="form-control" name=contactcode id=outcomecode><option value=-1>' . Language::labelPleaseSelect() . '</option>';

        $dispositionCodes = Language::optionsDispositionContactCode($respondentOrHousehold);

        foreach ($dispositionCodes as $option => $dispositionCode) {
            if ($dispositionCode[5] == 1) { //display in dropdown
                $selected = '';
                if (loadvar('contactcode') == $option) {
                    $selected = ' SELECTED';
                }

                $returnStr .= '<option value="' . $option . '"' . $selected . '>' . $option . ': ' . $dispositionCode[1] . '</option>';
            }
        }

        $returnStr .= '</select></td><td style="width:10px"></td><td>

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



        $returnStr .= '<tr><td style="width:80px">' . Language::labelContactWhen() . '</td><td style="width:220px">';
        $returnStr .= $this->displayDateTimePicker('contactappointment', 'contactappointment', loadvar('contactappointment'), getSMSLanguagePostFix(getSMSLanguage()), "true", "true", Config::usFormatSMS());
        $returnStr .= '</td><td colspan=2></td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '<hr>';

        $returnStr .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonAddContact() . '</button>';

        $returnStr .= '</form><br/>';



        $returnStr .= $this->displayWarning(Language::messageSelectContactOutcome(), "outcomehelp"); //'<div class="alert alert-info" id="outcomehelp">' . Language::messageSelectContactOutcome() . '</div>';





        $returnStr .= '</div>';

        $returnStr .= $this->showSectionSideBar($respondentOrHousehold);

        $returnStr .= '</div>';

//END CONTENT



        $returnStr .= '

<script>





$(document).ready(function() {

    $(\'#outcomecode\').change(function() {

        $(\'#contactwithdiv\').css("display", "none");

        var element = $(this).find(\'option\').filter(\':selected\').val();';



        $check = array();

        $followup = Language::optionsDispositionContactCode($respondentOrHousehold);

        foreach ($followup as $option => $follow) {

            if ($follow[0] == '1') {

                $check[] = $option;
            }
        }

        $returnStr .= 'if (element == "' . implode('" || element == "', $check) . '") {';

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



        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter(false);



        return $returnStr;
    }

    function showCalendar() {

        $header = '

  <link rel="stylesheet" href="bootstrap/css/sticky-footer-navbar.min.css">	

	<link rel="stylesheet" href="css/calendar.css">

 ';



        $returnStr = $this->showHeader(Language::messageSMSTitle(), $header);

        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();

        $returnStr .= '<div class="container">';



        /*        $returnStr .= '<ol class="breadcrumb">';

          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'interviewer.home'), Language::linkInterviews()) . '</li>';

          $returnStr .= '<li class="active">Calendar</li>';

          $returnStr .= '</ol>'; */



//CONTENT



        $returnStr .= $this->displayCalendar();







//END CONTENT

        $returnStr .= '</div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter(false);

        return $returnStr;
    }

    function showPreferences($user) {

        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');

        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();

        $returnStr .= '<div class="container"><p>';

//CONTENT



        $returnStr .= '<h4>' . Language::linkPreferences() . '</h4>';



        $returnStr .= '<form method=post>';

        $returnStr .= setSessionParamsPost(array('page' => 'interviewer.preferencesres'));



        $returnStr .= '<input type="hidden" name="filter" id="filter" value="' . $user->getFilter() . '">';



        $returnStr .= '<div class="panel panel-default">

  <div class="panel-heading">

    <h3 class="panel-title">' . Language::labelInterviewerFilters() . '</h3>

  </div>

  <div class="panel-body">

';


        $active = array('', '', '', '');
        $active[$user->getFilter()] = ' active';
        $returnStr .= '<table><tr><td style="width:110px">' . Language::labelInterviewerHouseholds() . '</td><td colspan=3>';

        $returnStr .= '<div id="filterselector" class="btn-group">

				<button type="button" class="btn btn-default' . $active[1] . '" value=1>' . Language::labelInterviewerFiltersHideNone() . '</button>

				<button type="button" class="btn btn-default' . $active[2] . '" value=2>' . Language::labelInterviewerFiltersHideCompleted() . '</button>

				<button type="button" class="btn btn-default' . $active[3] . '" value=3>' . Language::labelInterviewerFiltersHideCompletedAndFinal() . '</button>

			</div></td></tr>';



        $returnStr .= '<input type=hidden name="region" id="region" value="' . $user->getRegionFilter() . '">';



        $returnStr .= '<tr><td>' . Language::labelInterviewerFilterRegion() . '</td><td style="width:195px">';

        $returnStr .= '<div id="regionselector" class="btn-group">

				<button type="button" class="btn btn-default active" value=0>' . Language::labelInterviewerFilterRegionAll() . '</button>

				<button type="button" class="btn btn-default" value=1>' . Language::labelInterviewerFilterRegionOne() . '</button>

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
        $returnStr .= '<table><tr><td style="width:110px">' . Language::labelSurvey() . ':</td><td>
			<div id="testmodeselector" class="btn-group">
				<button type="button" class="btn btn-default active" value=0>' . Language::labelNormalMode() . '</button>
				<button type="button" class="btn btn-default" value=1>' . Language::labelTestMode() . '</button>
			</div></td><td style="width:10px;"></td><td>';

        $returnStr .= '<div id="testmodediv" style="display: none"><a href="' . setSessionParams(array('page' => 'interviewer.preferences.resettest')) . '">' . Language::linkResetTestCases() . '</a></div>';

        $returnStr .= '</td></tr>';





        $returnStr .= '<tr><td style="width:110px">' . Language::labelCommunication() . '</td><td colspan=2>';

        $returnStr .= $this->displayCommunicationSelect($user->getCommunication());

        $returnStr .= '</td></tr>';

        $returnStr .= '</table>';





        $returnStr .= '<script>';



        $returnStr .= '$(\'#filterselector button\').click(function() {
		//  $(\'#filterselector button\').addClass(\'active\').not(this).removeClass(\'active\');
                  $(\'#filterselector button\').removeClass(\'active\');
                $(this).addClass(\'active\');
              // $(\'#filterselector button\').toggleClass("active");
		  $(\'#filter\').val("1");

		  if ($(this).val() == "2") {

		    $(\'#filter\').val("2");

		  }  

		  if ($(this).val() == "3") {

		    $(\'#filter\').val("3");

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



//if ($(\'#filter\').val() == "2"){

//  $(\'#filterselector button\').click();

//}

//if ($(\'#filter\').val() == "3"){

//  $(\'#filterselector button\').click();

//}


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
        // <table width=100%><tr><td align=right>v1.01</td></tr></table>
        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter();

        return $returnStr;
    }

    function showOtherSurveys() {

        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');

        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();

        $returnStr .= '<div class="container"><p>';



        /*        $returnStr .= '<ol class="breadcrumb">';

          $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'interviewer.home'), Language::linkInterviews()) . '</li>';

          $returnStr .= '<li class="active">Calendar</li>';

          $returnStr .= '</ol>'; */



//CONTENT



        $returnStr .= $this->displayWarning(Language::labelInterviewerWarningNoOtherSurveys());



//END CONTENT

        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter();

        return $returnStr;
    }

    function showBottomBar() {

        return ' </div>

    <div id="footer">

      <div class="container">

        <p class="text-muted credit" style="text-align:right">' . Language::nubisFooter() . '</p>

      </div>

    </div>

';
    }

    public function showNavBar() {

        $search = true;

        $interviewsActive = ' class="active"';

        $calendarActive = '';

        $otherSurveysActive = '';

        if (startsWith($_SESSION['LASTPAGE'], 'interviewer.calendar')) {

            $interviewsActive = '';

            $calendarActive = ' class="active"';

            $otherSurveysActive = '';

            $search = false;
        }

        if (startsWith($_SESSION['LASTPAGE'], 'interviewer.othersurveys')) {

            $interviewsActive = '';

            $calendarActive = '';

            $otherSurveysActive = ' class="active"';

            $search = false;
        }

//TODO: GET FROM SOMEHWERE ELSE...

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

            <a class="navbar-brand" href="' . setSessionParams(array('page' => 'interviewer.home')) . '">' . Language::messageSMSTitle() . '</a>

          </div>

          <div class="collapse navbar-collapse">

            <ul class="nav navbar-nav">

              <li' . $interviewsActive . '>' . setSessionParamsHref(array('page' => 'interviewer.home'), Language::linkInterviews()) . '</li>

              <li' . $calendarActive . '>' . setSessionParamsHref(array('page' => 'interviewer.calendar'), Language::linkCalendar()) . '</li>';


        //<li' . $otherSurveysActive . '>' . setSessionParamsHref(array('page' => 'interviewer.othersurveys'), Language::linkOtherSurveys()) . '</li>

        $returnStr .= '   </ul>





            <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">

              <a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . $user->getName() . ' <b class="caret"></b></a>

                 <ul class="dropdown-menu">

										<li><a href="' . setSessionParams(array('page' => 'interviewer.preferences')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkPreferences() . '</a></li>';

        if ($user->getCommunication() != SEND_RECEIVE_WORKONSERVER) {
            $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'interviewer.sendreceive')) . '"><span class="glyphicon glyphicon-import"></span> ' . Language::linkSendReceive() . '</a></li>';
        }

        /*$returnStr .= '<li class="divider"></li>';
        if ($_SESSION['SUPLOGIN'] == 1){
          $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'interviewer.supervisor.logout')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkSupervisorLogout() . '</a></li>';
        }
        else {
          $returnStr .= '<li><a href="' . setSessionParams(array('page' => 'interviewer.supervisor.login')) . '"><span class="glyphicon glyphicon-wrench"></span> ' . Language::linkSupervisorLogin() . '</a></li>';
        }*/
        
        
        
        $returnStr .= '<li class="divider"></li>

                   <li><a href="index.php?rs=1&se=2"><span class="glyphicon glyphicon-log-out"></span> ' . Language::linkLogout() . '</a></li>

                 </ul>

             </li>

            </ul>

';

        if ($search) {

            $returnStr .= '

<form class="navbar-form navbar-right" role="search">

<div class="input-group" style="width:175px;overflow:hidden;">

      <input type="text" class="form-control" name="searchterm">';

            $returnStr .= '<span class="input-group-btn">

        <button class="btn btn-default" type="submit">' . Language::buttonSearch() . '</button>

      </span>';

            $returnStr .= setSessionParamsPost(array('page' => 'interviewer.search'));

            $returnStr .= '</div>';

            $returnStr .='</form>

';
        }

        $returnStr .= '

          </div><!--/.nav-collapse -->

        </div>

      </div>

      <div id="content">';

        return $returnStr;
    }

    function showRespondentPageWithSideBar($respondent, $content, $label) {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">

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



        $returnStr .= '<ol class="breadcrumb">';
        if ($respondent instanceof Respondent) {
            $pageref = 'interviewer.respondent';
        } else {
            $pageref = 'interviewer.household';
        }
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'interviewer.home'), Language::labelHome()) . '</li>';
        if ($respondent instanceof Respondent && dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level link
            $household = $respondent->getHousehold();
            $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'interviewer.household.info', 'primkey' => $household->getPrimkey()), Language::householdLabelCap()) . '</li>';
        }
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => $pageref . '.info', 'primkey' => $respondent->getPrimkey()), Language::householdOrRespondentLabelCap($respondent)) . '</li>';
        $returnStr .= '<li class="active">' . $label . '</li>';
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

    

    function showStartProxySurvey($respondentOrHousehold, $message = '') {

        $respondentOrHousehold->setStatus(1); //set status to 1: started survey

        $respondentOrHousehold->saveChanges();

        $suid = 1;

        $refpage = 'interviewer.household';

        if ($respondentOrHousehold instanceof Respondent) {

            $suid = 2;

            $refpage = 'interviewer';
        }

        $content = $message;

        $content .= $this->displayWarning(Language::messageSMSSurveyStart($respondentOrHousehold)); //'<div class="alert alert-warning">' . Language::messageSMSSurveyStart($respondent) . '</div>';

        $content .= '<hr>';

        $content .= '<table width=100%><tr><td>';

        $content .= '<form method=post>';

        $content .= setSessionParamsPost(array('page' => $refpage . '.info', 'primkey' => $respondentOrHousehold->getPrimkey()));

        $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonBack() . '</button>';

        $content .= '</form>';

        $content .= '</td><td align=right>';

        $content .= "<form method=post>";

        $content .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';

        $content .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($respondentOrHousehold->getPrimkey(), Config::directLoginKey())) . '">';

        $content .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '2' . '">';  //2: start with burmese

        $content .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';

        $content .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';

        $content .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . $suid . '">';


        $content .= '<input type=hidden name=' . POST_PARAM_PRELOAD . ' value="' . encodeSession($respondentOrHousehold->getPreload(array('RProxy' => '1'))) . '">';


        //$content .= '<input type=hidden name=' . POST_PARAM_PRELOAD . ' value="' . encodeSession(array('RProxy' => '1')) . '">';
//    $content .= '<input type=hidden name=ss value=1>';

        $content .= '<button type="submit" id="startsurveybtn" class="btn btn-default navbar-btn">' . Language::buttonStartSurvey() . '</button>';

        $content .= "</form>";

        $content .= '</td></tr></table>';



        return $this->showRespondentPageWithSideBar($respondentOrHousehold, $content, Language::labelStartSurvey());
    }

    function showStartSurvey($respondentOrHousehold, $message = '') {
        $suid = 1;
        $refpage = 'interviewer.household';
        if ($respondentOrHousehold instanceof Respondent) {
            $suid = 2;
            $refpage = 'interviewer.respondent';
        }
        $content = $message;

        $content .= $this->displayWarning(Language::messageSMSSurveyStart($respondentOrHousehold)); //'<div class="alert alert-warning">' . Language::messageSMSSurveyStart($respondent) . '</div>';
        if ($respondentOrHousehold instanceof Respondent) { //proxy code?
            $proxyPermission = new ProxyPermission();
            $code = $proxyPermission->getRandomProxyCode();
            $content .= '
				<div id="contactwithdiv"><table><tr><td style="width:90px"><nobr>' . Language::labelContactWith() . '</td><td style="width:190px">
					<div id="selector" class="btn-group">
						<button type="button" class="btn btn-default" value=1>' . Language::labelInterviewerRespondent() . '</button>
						<button type="button" class="btn btn-default" value=2>' . Language::labelProxy() . '</button>
					</div></td><td style="width:340px">
				  <div id="contactperson" style="display: none"><table><tr><td valign=middle><nobr>' . Language::labelProxyCodeLabel($code) . ': </td><td> 
				    <form method=post><input type=hidden name="code" value="' . $code . '">';

            $content .= setSessionParamsPost(array('page' => $refpage . '.proxycheck', 'primkey' => $respondentOrHousehold->getPrimkey()));
            $content .= '
            <input type=text class="form-control" style="width:120px" id="proxycodeentered" name="proxycodeentered"></td><td>
            <button type="submit" id="checkproxycode" class="btn btn-default" value=2>' . Language::buttonCheck() . '</button></form>
            </td></tr></table>
				  </div></td></tr></table>
				</div>';
            $content .= '<script>
				$(document).ready(function() {
					$(\'#startsurveybtn\').attr("disabled", "disabled");
				});
				$(\'#selector button\').click(function() {
						$(\'#contactperson\').css("display", "none");

						$(\'#selector button\').addClass(\'active\').not(this).removeClass(\'active\');

						$(\'#contactwith\').val("1");

						if ($(this).val() == "2") {

							$(\'#startsurveybtn\').attr("disabled", "disabled"); //disable interview button

							$(\'#contactperson\').css("display", "block");

							$(\'#contactwith\').val("2");

						}

						else {

							$(\'#startsurveybtn\').removeAttr("disabled"); //enable interview button

						}

				});
				</script>

				';
        }



        $content .= '<hr>';
        $content .= '<table width=100%><tr><td>';
        $content .= '<form method=post>';
        
        $content .= setSessionParamsPost(array('page' => trim($refpage . '.info'), 'primkey' => $respondentOrHousehold->getPrimkey()));
        $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonBack() . '</button>';
        $content .= '</form>';
        $content .= '</td><td align=right>';
        $content .= "<form method=post>";
        $content .= '<input type=hidden name=' . POST_PARAM_SE . ' value="' . addslashes(USCIC_SURVEY) . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_PRIMKEY . ' value="' . addslashes(encryptC($respondentOrHousehold->getPrimkey(), Config::directLoginKey())) . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_LANGUAGE . ' value="' . '1' . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_MODE . ' value="' . '3' . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_URID . ' value="' . addslashes($_SESSION['URID']) . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_SURVEY_EXECUTION_MODE . ' value=' . SURVEY_EXECUTION_MODE_NORMAL . '>';
        $content .= '<input type=hidden name=' . POST_PARAM_NEW_PRIMKEY . ' value="1">';
        $content .= '<input type=hidden name=' . POST_PARAM_SUID . ' value="' . $suid . '">';
        $content .= '<input type=hidden name=' . POST_PARAM_PRELOAD . ' value="' . encodeSession($respondentOrHousehold->getPreload()) . '">';

        $content .= '<button type="submit" id="startsurveybtn" class="btn btn-default navbar-btn">' . Language::buttonStartSurvey() . '</button>';

        $content .= "</form>";

        $content .= '</td></tr></table>';

    return $this->showRespondentPageWithSideBar($respondentOrHousehold, $content, Language::labelStartSurvey());
    }

    
    function showSearchRes($respondentsOrHouseholds) {

        $returnStr = $this->displayHeaderForTable(Language::messageSMSTitle());



        $returnStr .= '<div id="wrap">';

        $returnStr .= $this->showNavBar();

        $returnStr .= '<div class="container"><p>';



        $returnStr .= '<ol class="breadcrumb">';

        $returnStr .= '<li class="active">Home</li>';

        $returnStr .= '</ol>';

//CONTENT
        if (sizeof($respondentsOrHouseholds) > 0) {
            $returnStr .= sizeof($respondentsOrHouseholds) . ' ' . Language::messageRespondentsFound();
            $arr = array_values($respondentsOrHouseholds);
            if ($arr[0] instanceof Respondent) { //this is a respondent
                $returnStr .= $this->showRespondentsTable($respondentsOrHouseholds);
            } else { //household
                $returnStr .= $this->showHouseholdsTable($respondentsOrHouseholds);
            }
        } else {

            $returnStr .= Language::messageNoRespondentsFound();
        }

//END CONTENT

        $returnStr .= '</p></div></div>   '; // </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();



        $returnStr .= $this->showFooter(false);

        return $returnStr;
    }

    function showSendReceive($message = "") {
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
//CONTENT
        $returnStr .= $message;

        $user = new User($_SESSION['URID']);

        if ($user->getCommunication() == SEND_RECEIVE_INTERNET) { //Internet
            $returnStr .= '<h4>' . Language::labelInterviewerInternetCommunication() . '</h4>';

            $returnStr .= $this->ShowCommunicationServerOptions();


            $communication = new Communication();
            if ($communication->isServerReachable()) { // Internet connection up
                if ($communication->isUpdateAvailable($user->getUrid())) { //receive
                    $returnStr .= $this->displayInfo(Language::labelInterviewerInternetUpdate());
                    //upload data
                    $returnStr .= "<form method=post>";
                    $returnStr .= setSessionParamsPost(array('page' => 'interviewer.sendreceive.receive'));
                    $returnStr .= '<button type="submit" class="btn btn-default" name="Receive" value="Receive">' . Language::labelInterviewerInternetReceive() . '</button>';
                    $returnStr .= "</form>";
                } else { //upload
                    //upload data
                    $returnStr .= "<form method=post>";
                    $returnStr .= setSessionParamsPost(array('page' => 'interviewer.sendreceive.upload'));
                    $returnStr .= '<button type="submit" class="btn btn-default" name="Upload" value="Upload">' . Language::labelInterviewerInternetUpload() . '</button>';
                    $returnStr .= "</form>";
                }
            } else {
                $returnStr .= $this->displayInfo(Language::labelInterviewerNoInternet());
            }
        }
        else if ($user->getCommunication() == SEND_RECEIVE_EXPORTSQL) { // SQL
            $returnStr .= '<h4>' . Language::labelInterviewerExport() . '</h4> <a href=export/index.php?urid=' . $user->getUrid() . '>' . Language::labelInterviewerExportRetrieve() . '</a>';
            $returnStr .= '<hr>';
            $returnStr .= '<h4>' . Language::labelInterviewerImport() . '</h4> 
                            <form enctype="multipart/form-data" method="post" role="form" action="export/index.php">
                            <div class="form-group">
                                     <input type="file" name="file" id="file" size="150">
                            </div>
                             <button type="submit" class="btn btn-default" name="Import" value="Import">' . Language::labelInterviewerImportData() . '</button>
                        </form>';
        }
        else if ($user->getCommunication() == SEND_RECEIVE_USB) {
            $returnStr .= $this->displayInfo(Language::labelInterviewerUSBCommunication());
        }


//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function showHeader($title, $style = '', $fastload = false) {
        return parent::showHeader($title, $style . '<link rel="stylesheet" type="text/css" href="css/uscicadmin.css">');
    }

    function showSupervisorLogin($message = ''){
        $returnStr = $this->showHeader(Language::messageSMSTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';
//CONTENT

        $returnStr .= $message;
        
        $returnStr .= '<form method=post>';

        $returnStr .= '<h4>Please enter the supervisor password below.</h4>';
        $returnStr .= setSessionParamsPost(array('page' => 'interviewer.supervisor.login.res'));
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>Password:</td><td><input type="password" name="suppwd" class="form-control"></td></tr>';
        $returnStr .= '<tr><td></td><td><input type="submit" value="login" class="btn btn-default"></td></tr>';
        $returnStr .= '</table>';
        
        
        $returnStr .= '</form>';
        

//END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();

        $returnStr .= $this->showFooter();
        return $returnStr;
    }
    
}

?>