<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayRespondent extends Display {

    function setPrefix($refpage) {
        return $refpage;
    }

    function showInfoSub($respondentOrHousehold, $edit = false) {
        $returnStr = '<table>';

        if ($respondentOrHousehold instanceof Respondent) {
            $returnStr .= '<tr><td style="width:100px">' . Language::labelRespondentName() . ':</td><td><b>' . $this->showInputBox('firstname', $respondentOrHousehold->getFirstName(), $edit) . '</td><td align=left>' . $this->showInputBox('lastname', $respondentOrHousehold->getLastName(), $edit) . '</b></td></tr>';
        } else {
            $returnStr .= '<tr><td style="width:100px">' . Language::labelRespondentName() . ':</td><td><b>' . $this->showInputBox('name', $respondentOrHousehold->getName(), $edit) . '</td><td></td></tr>';
        }
        $info1 = $this->defaultDisplayInfoAddressColumns();
        //return array('address1_dec' => Language::labelDwelling(), 'city_dec' => Language::labelVillage()); 
        foreach ($info1 as $key => $info) {
            $info1edit = $edit;
            if ($edit == true && $respondentOrHousehold instanceof Respondent)
                $info1edit = false;
            $returnStr .= '<tr><td>' . $info . ':</td><td colspan=2 style="width:200px">' . $this->showInputBox(rtrim($key, '_dec'), $respondentOrHousehold->getDataByField($key), $info1edit) . '</td></tr>';
        }

        //SET THIS THROUGH defaultDisplayInfo1AddressColumns
//        $returnStr .= '<tr><td valign=top>Address 1:</td><td colspan=2 style="width:200px">' . $this->showInputBox('address1', $respondentOrHousehold->getAddress1(), $edit) . '</td></tr>';
//        $returnStr .= '<tr><td>Address 2:</td><td colspan=2>' . $this->showInputBox('address2', $respondentOrHousehold->getAddress2(), $edit) . '</td></tr>';
//        $returnStr .= '<tr><td>City / Zip:</td><td>' . $this->showInputBox('city', $respondentOrHousehold->getCity(), $edit) . '</td><td>' . $this->showInputBox('zip', $respondentOrHousehold->getZip(), $edit) . '</td></tr>';

        $returnStr .= '<tr><td colspan=3><hr></td></tr>';


        $info2 = $this->defaultDisplayInfo2AddressColumns();
        //return array('telephone1_dec' => Language::labelTelephone()); 
        foreach ($info2 as $key => $info) {
            $returnStr .= '<tr><td>' . $info . ':</td><td colspan=2 style="width:200px">' . $this->showInputBox(rtrim($key, '_dec'), $respondentOrHousehold->getDataByField($key), $edit) . '</td></tr>';
        }

        //SET THIS THROUGH defaultDisplayInfo2AddressColumns
//        $returnStr .= '<tr><td>Telephone:</td><td colspan=2>' . $this->showInputBox('telephone1', $respondentOrHousehold->getTelephone1(), $edit) . '</td></tr>';
//        $returnStr .= '<tr><td>Email:</td><td colspan=2>' . $this->showInputBox('email', $respondentOrHousehold->getEmail(), $edit) . '</td></tr>';
        //    $returnStr .= '<tr><td>Fax:</td><td colspan=2>' . $this->showInputBox('fax', $respondentOrHousehold->getTelephone2(), $edit) . '</td></tr>';
        //    $returnStr .= '<tr><td>Email:</td><td colspan=2>' . $this->showInputBox('email', $respondentOrHousehold->getEmail(), $edit) . '</td></tr>';

        if (!$edit) {
            $psu = new Psu($respondentOrHousehold->getPuid());
            $returnStr .= '<tr><td>' . Language::labelRespondentPSU() . ':</td><td colspan=2>' . $psu->getNumberAndName() . '</td></tr>';
            if ($respondentOrHousehold->getLatitude() != '' && $respondentOrHousehold->getLatitude() != 0) {
                $gpsLink = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $psu = new Psu($respondentOrHousehold->getPuid());
                    $gps = new GPS($psu->getCode(), $respondentOrHousehold->getAddress1());
                    $gpsLink = '<a target="_" href="http://maps.google.com/?q=' . $gps->getLatitude() . ',' . $gps->getLongitude() . '"><span class="glyphicon glyphicon-screenshot"></span></a>';
                }
                $returnStr .= '<tr><td valign=top>' . Language::labelRespondentGPS() . ': ' . $gpsLink . '</td><td colspan=2>';
                $returnStr .= 'lat: ' . $respondentOrHousehold->getLatitude() . '<br/>lon:' . $respondentOrHousehold->getLongitude();
                $returnStr .= '</td></tr>';
            }
        }

        $returnStr .= '</table>';
        return $returnStr;
    }

    function showInfo($respondentOrHousehold, $message = '', $refpageprefix = '') {
        $content = $message;
        $refpage = 'interviewer.household';
        if ($respondentOrHousehold instanceof Respondent) {
            $refpage = 'interviewer.respondent';
        }
        $refpage = $this->setPrefix($refpage);
        $content .= '<div class="row">';
        $content .= '<div class="col-md-5">';
//        $content .= '<div class="col-4 col-sm-4 col-lg-5">';
        $content .= $this->showInfoSub($respondentOrHousehold);
        $content .= '</div>';
        //$content .= '<div class="col-8 col-sm-8 col-lg-5">';
        $content .= '<div class="col-md-7">';
//        col-md-8
        $content .= '<table width=100%>';
        $content .= '<tr><td style="width:100px">' . Language::labelRespondentStatus() . ':</td><td style="width:200px">' . $this->displayStatus($respondentOrHousehold) . '</td></tr>';
        $content .= '<tr><td>' . Language::labelRespondentContacts() . ':</td><td>' . sizeof($respondentOrHousehold->getContacts()) . '</td></tr>';
        $content .= '<tr><td colspan=2><hr></td></tr>';
        $content .= '<tr><td colspan=3>';
        if ($respondentOrHousehold instanceof Household) {
            $content .= '<div class="alert alert-info" style="max-height: 160px; overflow-y:scroll;">';
            $content .= '<table width=100%><tr><td colspan=3><b>' . Language::labelRespondentHHMembers() . '</b></td><td></td></tr>';
            $nohhmemberselected = ($respondentOrHousehold->getStatus() == 2 && sizeof($respondentOrHousehold->getSelectedRespondents()) == 0);
            if ($nohhmemberselected) { //no one selected!
                $content .= '<b>' . Language::labelRespondentWarningNoOneSelected() . '</b>';
            }

            $respondents = $respondentOrHousehold->getRespondents();

            foreach ($respondents as $respondent) {
                $primkeyPopup = '<div data-toggle="tooltip" data-placement="top" title="' . $respondent->getPrimkey() . '">';

                if ($respondentOrHousehold->getStatus() == 2 && !$respondent->isPresent()) {
                    $content .= '<tr style="color:red"><td colspan=3><nobr>' . $primkeyPopup;
                    $content .= '<span class="glyphicon glyphicon-remove"></span> ';
                } elseif ($respondentOrHousehold->getStatus() != 2) {
                    $content .= '<tr><td colspan=3><nobr>' . $primkeyPopup;
                    $content .= '<span class="glyphicon glyphicon-user"></span> ';
                } elseif ($respondent->isPresent() && ($respondent->isSelected() || $respondent->isFinR() || $respondent->isFamR())) {
                    $content .= '<tr><td colspan=3><nobr>' . $primkeyPopup;
                    $content .= '<span class="glyphicon glyphicon-user"></span> ';
                    $content .= '<a href="' . setSessionParams(array('page' => $this->setPrefix('interviewer.respondent.info'), 'primkey' => $respondent->getPrimkey())) . '">';
                } else {
                    $content .= '<tr style="color:gray"><td colspan=3><nobr>' . $primkeyPopup;
                    $content .= '<span class="glyphicon glyphicon-eye-close"></span> ';
                }

                $content .= $respondent->getName() . $this->displayGender($respondent) . $this->displayAge($respondent) . $this->displayFinR($respondent) . $this->displayFamR($respondent) . $this->displaySelected($respondent, false);
                $content .= '</div>';
                if (!$nohhmemberselected && $respondent->isPresent() && ($respondent->isSelected() || $respondent->isFinR() || $respondent->isFamR())) {
                    $content .= '</a>';
                }
                $content .= '</td><td align=right><nobr>';
                $content .= $this->displayStatus($respondent, false);
                $content .= $this->displayMovedOut($respondent, false);

                $content .= '</td></tr>';

                if ($respondent->getMovedOut() == 1) { //new hh, show location! in modal
                    $content .= $this->showModalNewAddress($respondent);
                }
            }
            $content .= '</table></div>';
        } else { //respondent: show info on finR, famR ect
            $content .= '<div class="alert alert-info">';
            $content .= Language::labelRespondentSex() . ': ' . $this->displayGenderFull($respondentOrHousehold) . '<br/>';
            $content .= Language::labelRespondentAge() . ': ' . $this->displayAge($respondentOrHousehold) . '<br/>';
            $content .= Language::labelRespondentIndividualSurvey() . ': ' . $this->displayIndividualSurvey($respondentOrHousehold) . '<br/>';

            $content .= $this->displayFinR($respondentOrHousehold, true);
            if ($this->displayFinR($respondentOrHousehold, true) != '') {
                $content .= '<br/>';
            }
            $content .= $this->displayFamR($respondentOrHousehold, true);

            $content .= '</div>';
            /*
              if ($respondent->isSelected()){

              }
              if ($respondent->isFinR()){

              }
              if ($respondent->isFamR()){

              } */
        }
        $content .= '</td></tr>';

        $content .= '</table>';

        $content .= '</div>';

        $content .= '</div>';

        $content .= '<hr>';
        $content .= $this->getRespondentActionButtons($respondentOrHousehold, $refpage);
        return $this->showRespondentPageWithSideBar($respondentOrHousehold, $content, Language::labelInfoCap());
    }

    function showModalNewAddress($respondent) {
        $content = '  
<!-- Modal -->
<div class="modal fade" id="myModal' . $respondent->getHhOrder() . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">' . Language::buttonClose() . '</span></button>
        <h4 class="modal-title" id="myModalLabel">' . Language::labelRespondentNewAddress() . '</h4>
      </div>
      <div class="modal-body">';


        $d = new DataRecord(1, $respondent->getHhid());
        $var = $d->getData('HR016[' . $respondent->getHhOrder() . ']');
        if (isset($var)) {
            $content .= $var->getAnswer();
        } else {
            $content .= Language::labelRespondentAddressUnknown();
        }

        $content .= '
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">' . Language::buttonClose() . '</button>
      </div>
    </div>
  </div>
</div> 
';
        return $content;
    }

    function getRespondentActionButtons($respondentOrHousehold, $refpage) {
        $content = '';
        if ($respondentOrHousehold->hasFinalCode()) {
            $content = $this->displayInfo(Language::labelRespondentFinalAssigned());
        } else {
            $content .= '<table width=100%><tr><td>';
            $content .= '<form method=post>';
            $content .= setSessionParamsPost(array('page' => $refpage . '.addcontact', 'primkey' => $respondentOrHousehold->getPrimkey()));
            $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonAddContact() . '</button>';
            $content .= '</form>';
            $content .= '</td><td align=right>';
            if ($respondentOrHousehold->getStatus() != 2) {
                $content .= '<form method=post>';
                $content .= setSessionParamsPost(array('page' => $refpage . '.startsurvey', 'primkey' => $respondentOrHousehold->getPrimkey()));

                $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonStartSurvey() . '</button>';

                $content .= '</form>';
            }

            $content .= '</td></tr></table>';
        }
        return $content;
    }

    function getRespondentContactButton($respondentOrHousehold, $refpage) {
        $content = '';
        if ($respondentOrHousehold->hasFinalCode()) {
            
        } else {
            $content .= '<form method=post>';
            $content .= setSessionParamsPost(array('page' => $refpage . '.addcontact', 'primkey' => $respondentOrHousehold->getPrimkey()));
            $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonAddContact() . '</button>';
            $content .= '</form>';
        }
        return $content;
    }

    function showRespondentPageWithSideBar($respondent, $content, $label) {
        
    }

    function displayFamR($respondent, $glyph = false) {

        if ($respondent->isFamR()) {
            $returnStr = '';
            if ($glyph) {
                $returnStr .= ' <span class="glyphicon glyphicon-user"></span>';
            }
            return $returnStr . ' <i>Family R</i>';
        }

        return '';
    }

    function displayFinR($respondent, $glyph = false) {

        if ($respondent->isFinR()) {
            $returnStr = '';
            if ($glyph) {
                $returnStr .= ' <span class="glyphicon glyphicon-usd"></span>';
            }
            return $returnStr . ' <i>' . Language::labelRespondentFinancialR() . '</i>';
        }

        return '';
    }

    function displayGender($respondent) {

        if ($respondent->getSex() == 1) {

            return ' ' . Language::labelRespondentSexMale();
        } else if ($respondent->getSex() == 2) {

            return ' ' . Language::labelRespondentSexFemale();
        }

        return '';
    }

    function displayIndividualSurvey($respondent) {

        if ($respondent->getSelected() == 1) {
            return ' ' . Language::labelRespondentYes();
        } elseif ($respondent->getSelected() == 0) {
            return ' ' . Language::labelRespondentNo();
        }
        return '';
    }

    function displayGenderFull($respondent) {

        if ($respondent->getSex() == 1) {
            return ' ' . Language::labelRespondentSexMaleFull();
        } else if ($respondent->getSex() == 2) {

            return ' ' . Language::labelRespondentSexFemaleFull();
        }

        return '';
    }

    function displayAge($respondent) {

        if ($respondent->getAge() >= 0) {

            return ' ' . $respondent->getAge();
        }

        return '';
    }

    function showSectionSideBar($respondentOrHousehold) {
        $refpage = 'interviewer.household';
        if ($respondentOrHousehold instanceof Respondent) {
            $refpage = 'interviewer.respondent';
        }
        $refpage = $this->setPrefix($refpage);
        $remarksStr = '';

        $remarks = $respondentOrHousehold->getRemarks();
        if (sizeof($remarks) > 0) {

            $remarksStr = ' <span class="badge pull-right">' . sizeof($remarks) . '</span>';
        }

        $contactsStr = '';

        $contacts = $respondentOrHousehold->getContacts();

        if (sizeof($contacts) > 0) {

            $contactsStr = ' <span class="badge pull-right">' . sizeof($contacts) . '</span>';
        }



        $returnStr = '



<div class="col-xs-6 col-sm-3 sidebar-offcanvas" id="sidebar" role="navigation">

          <div class="well sidebar-nav">

            <ul class="nav"><li>';

        if ($respondentOrHousehold instanceof Respondent) {

            $returnStr .= Language::labelRespondentRespondent() . ' ';
        } else {

            $returnStr .= Language::labelRespondentHousehold() . 'Household ';
        }
        $returnStr .= $respondentOrHousehold->getPrimkey() . '</li>

              <li class="active"><a href="' . setSessionParams(array('page' => $refpage . '.info', 'primkey' => $respondentOrHousehold->getPrimkey())) . '"><span class="glyphicon glyphicon-user"></span> ' . Language::labelInfo() . '</a></li>

              <li><a href="' . setSessionParams(array('page' => $refpage . '.contacts', 'primkey' => $respondentOrHousehold->getPrimkey())) . '"><span class="glyphicon glyphicon-calendar"></span> ' . Language::labelContacts() . $contactsStr . '</a></li>

              <li><a href="' . setSessionParams(array('page' => $refpage . '.history', 'primkey' => $respondentOrHousehold->getPrimkey())) . '"><span class="glyphicon glyphicon-time"></span> ' . Language::labelHistory() . '</a></li>

              <li><a href="' . setSessionParams(array('page' => $refpage . '.remarks', 'primkey' => $respondentOrHousehold->getPrimkey())) . '"><span class="glyphicon glyphicon-comment"></span> ' . Language::labelRemarks() . $remarksStr . '</a></li>';



        if (dbConfig::defaultTracking()) {

            $returnStr .= '<li><a href="' . setSessionParams(array('page' => $refpage . '.tracking', 'primkey' => $respondentOrHousehold->getPrimkey())) . '"><span class="glyphicon glyphicon-road"></span> ' . Language::labelTracking() . '</a></li>';
        }

        $returnStr .= '<li><a href="' . setSessionParams(array('page' => $refpage . '.edit', 'primkey' => $respondentOrHousehold->getPrimkey())) . '"><span class="glyphicon glyphicon-pencil"></span> ' . Language::labelEdit() . '</a></li>

            </ul>

          </div><!--/.well -->

        </div><!--/span-->';

        return $returnStr;
    }

    function showContacts($respondentOrHousehold) {
        $content = '';
        $refpage = 'interviewer.household';
        if ($respondentOrHousehold instanceof Respondent) {
            $refpage = 'interviewer.respondent';
        }
        $refpage = $this->setPrefix($refpage);
        $contacts = $respondentOrHousehold->getContacts();
        if (sizeof($contacts) > 0) {
            $content .= '<div class="span12" style="!important;overflow-y: auto;" id="contactdiv">';
            $content .= '<table class="table table-striped table-bordered pre-scrollable">';  // style="overflow: auto;"
            $content .= '<thead><tr><th>' . Language::labelRespondentContactsContact() . '</th><th>' . Language::labelRespondentContactsInterviewer() . '</th><th>' . Language::labelRespondentContactsDateTime() . '</th><th>' . Language::labelRespondentContactsProxy() . '</td><th>' . Language::labelRespondentContactsRemark() . '</th><th>' . Language::labelRespondentContactsAppointment() . '</th></tr></thead><tbody>';
            //$dispositionCodes = Language::optionsDispositionContactCode();
            foreach ($contacts as $contact) {
                $content .= '<tr><td>' . $contact->getCode() . ': ' . $contact->getText($respondentOrHousehold) . '</td><td>' . $contact->getUsername() . '</td>';
                $content .= '<td><nobr>' . $contact->getContactTs() . '</td><td>';
                if ($contact->isProxy()) {
                    $content .= '<div rel="tooltip" title="' . convertHTLMEntities($contact->getProxyName(), ENT_QUOTES) . '" data-placement="top">' . Language::labelYesCap() . '</div>';
                } else {
                    $content .= Language::labelNoCap();
                }
                $content .= '</td><td>' . $contact->getRemark() . '</td><td>' . $contact->getEvent() . '</td></tr>';
            }
            $content .= '</tbody></table></div>';

            $content .= '<script>$( "#contactdiv" ).height(Math.round($(window).height() - 60 - 51 - 36 - 150));  </script>';
        } else {

            $content .= $this->displayInfo(Language::messageSMSNoContactsYet()); //'<div class="alert alert-info">' . Language::messageSMSNoContactsYet() . '</div>';
        }

        $content .= '<hr>';
        $content .= $this->getRespondentContactButton($respondentOrHousehold, $refpage);

        return $this->showRespondentPageWithSideBar($respondentOrHousehold, $content, Language::labelContacts());
    }

    function showHistory($respondent) {

        $content = '';

        $logactions = $respondent->getHistory();

        if (sizeof($logactions) > 0) {

            $content .= '<div class="span12" style="!important;overflow-y: auto;" id="historydiv">';

            $content .= '<table class="table table-striped table-bordered pre-scrollable">';  // style="overflow: auto;"

            $content .= '<thead><tr><th>' . Language::labelRespondentContactsAction() . '</th><th>' . Language::labelRespondentContactsInterviewer() . '</th><th>' . Language::labelRespondentContactsDateTime() . '</th></tr></thead><tbody>';

            foreach ($logactions as $action) {

                $content .= '<tr><td>' . $action->getAction() . '</td><td>' . $action->getUsername() . '</td><td><nobr>' . $action->getTs() . '</td></tr>';
            }

            $content .= '</tbody></table></div>';

            $content .= '<script>$( "#historydiv" ).height(Math.round($(window).height() - 60 - 51 - 36 - 110));</script>';
        } else {

            $content .= $this->displayInfo(Language::messageSMSNoHistoryYet()); //'<div class="alert alert-info">' . Language::messageSMSNoHistoryYet() . '</div>';
        }



        return $this->showRespondentPageWithSideBar($respondent, $content, Language::labelHistory());
    }

    function showRemarks($respondentOrHousehold) {

        $content = '';
        $refpage = 'interviewer.household';
        if ($respondentOrHousehold instanceof Respondent) {
            $refpage = 'interviewer.respondent';
        }
        $refpage = $this->setPrefix($refpage);
        $remarks = $respondentOrHousehold->getRemarks();
        if (sizeof($remarks) > 0) {
            $content .= '<div class="span12" style="!important;overflow-y: auto;" id="remarkdiv">';
            $content .= '<table class="table table-striped table-bordered pre-scrollable">';  // style="overflow: auto;"

            $content .= '<thead><tr><th>' . Language::labelRespondentContactsRemark() . '</th><th>' . Language::labelRespondentContactsInterviewer() . '</th><th>' . Language::labelRespondentContactsDateTime() . '</th></tr></thead><tbody>';
            foreach ($remarks as $remark) {

                $content .= '<tr><td>' . $remark['remark_dec'] . '</td><td>' . $remark['username'] . '</td><td><nobr>' . $remark['ts'] . '</td></tr>';
            }

            $content .= '</tbody></table></div>';

            $content .= '<script>$( "#remarkdiv" ).height(Math.round($(window).height() - 60 - 51 - 36 - 140));</script>';
        } else {
            $content .= $this->displayInfo(Language::messageSMSNoRemarksYet()); //'<div class="alert alert-info">' . Language::messageSMSNoRemarksYet() . '</div>';
        }
        $content .= '<hr>';

        $content .= "<form method=post>";

        $content .= setSessionParamsPost(array('page' => $refpage . '.addremark', 'primkey' => $respondentOrHousehold->getPrimkey()));

        $content .= '<div class="input-group">

      <input type="text" class="form-control" name="remark">

      <span class="input-group-btn">

        <button class="btn btn-default" type="submit">' . Language::labelRespondentAddRemark() . '</button>

      </span>

    </div><!-- /input-group -->';

        $content .= "</form>";



        return $this->showRespondentPageWithSideBar($respondentOrHousehold, $content, Language::labelRemarks());
    }

    function showEdit($respondentOrHousehold, $message = '') {

        $content = $message;
        $refpage = 'interviewer.household';
        if ($respondentOrHousehold instanceof Respondent) {
            $refpage = 'interviewer.respondent';
        }
        $refpage = $this->setPrefix($refpage);

        $content .= '<form method=post>';



        $content .= $this->showInfoSub($respondentOrHousehold, true);

        $content .= '<hr>';

        $content .= setSessionParamsPost(array('page' => $refpage . '.editres', 'primkey' => $respondentOrHousehold->getPrimkey()));

        $content .= '<button type="submit" class="btn btn-default navbar-btn">' . Language::buttonSave() . '</button>';

        $content .= '</form>';



        return $this->showRespondentPageWithSideBar($respondentOrHousehold, $content, Language::labelInfoCap());
    }

    function showTracking($respondent) {
        $content = '';
        $content .= 'Tracking page!';
        return $this->showRespondentPageWithSideBar($respondent, $content, Language::labelTracking());
    }

}

?>