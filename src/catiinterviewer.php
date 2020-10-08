<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class CatiInterviewer {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {
        global $logActions;
//        echo '<br/><br/><br/>-----' . getFromSessionParams('page') . ";;" . getFromSessionParams('primkey');
        if (getFromSessionParams('page') != null) {
            $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('page'));
            switch (getFromSessionParams('page')) {
                case 'catiinterviewer.answer': return $this->showPhoneAnswer(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.startsurvey': return $this->showCatiStartSurvey(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.noanswer.answeringmachine': return $this->showAnsweringMachine(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.noanswer.answeringmachine.res': return $this->showAnsweringMachineRes(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.noanswer.maxnumberrings': return $this->showMaxRings(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.noanswer.disconnected': return $this->showDisconnected(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.refusal': return $this->showRefusal(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.barriers': return $this->showBarriers(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.schedule': return $this->showSchedule(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.schedule.res': return $this->showScheduleRes(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.disconnect': return $this->showDisconnectDuringSurvey(getFromSessionParams('primkey'));
                    break;


                case 'interviewer.calendar': return $this->showCalendar();
                    break;
                case 'interviewer.othersurveys': return $this->showOtherSurveys();
                    break;
                case 'interviewer.history': return $this->showHistory(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.contacts': return $this->showContacts(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.info': return $this->showInfo(getFromSessionParams('primkey'));
                    break;
                case 'catiinterviewer.backfromsms': return $this->showBackFromSMS(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.backfromsms': return $this->showBackFromSMS(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.surveycompleted': return $this->showSurveyCompleted(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.info': return $this->showInfo(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.edit': return $this->showEdit(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.editres': return $this->showEditRes(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.tracking': return $this->showTracking(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.remarks': return $this->showRemarks(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.addremark': return $this->showAddRemark(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.addcontact': return $this->showAddContact(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.addcontactres': return $this->showAddContactRes(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.startsurvey': return $this->showStartSurvey(getFromSessionParams('primkey'));
                    break;
                case 'interviewer.preferences': return $this->showPreferences();
                    break;
                case 'interviewer.preferencesres': return $this->showPreferencesRes();
                    break;
                case 'interviewer.preferences.resettest': return $this->showResetTestCases();
                    break;
                case 'interviewer.sendreceive': return $this->showSendReceive();
                    break;
                default: return $this->mainPage();
            }
        } else {
            $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('interviewer.home'));
            return $this->mainPage();
        }
    }

    function showSearchRes() {
        $respondents = new Respondents();
        $respondents = $respondents->getRespondentsByUserSearch($this->user, loadvar('searchterm'));
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showSearchRes($respondents);
    }

    function showPhoneAnswer($primkey) {
        $respondent = new Respondent($primkey);
        //echo '----'  . $primkey . '=====';
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showPhoneAnswered($respondent);
    }

    function showSchedule($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showSchedule($respondent);
    }

    function showScheduleRes($primkey) {
        $respondent = new Respondent($primkey);

        $contactwith = '';
        $contactperson = '';
        $contactcode = 101;
        $contactts = date('Y-m-d H:i:s');
        $contactappointment = loadvar("contactappointment");
        $contactremark = '';
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $contactremark, $contactappointment, $_SESSION['URID']);

        $display = new Display();
        return $this->mainPage($display->displaySuccess('Appointment made'));
    }

    function showAnsweringMachine($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showAnswerMachine($respondent);
    }

    function showAnsweringMachineRes() {
        $respondent = new Respondent($primkey);

        $contactwith = '';
        $contactperson = '';
        $contactcode = 103;
        $contactts = date('Y-m-d H:i:s');
        $contactappointment = '';
        $contactremark = '';
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $contactremark, $contactappointment, $_SESSION['URID']);



        $display = new Display();
        return $this->mainPage($display->displaySuccess('Message left'));
    }

    function showMaxRings($primkey) {
        $respondent = new Respondent($primkey);

        $display = new Display();
        return $this->mainPage($display->displaySuccess('Max rings reached'));
    }

    function showDisconnectDuringSurvey($primkey) {
        $respondent = new Respondent($primkey);
        $display = new Display();
        return $this->mainPage($display->displaySuccess('Disconnected'));
    }

    function showRefusal($primkey) {
        $respondent = new Respondent($primkey);
        $contactwith = '';
        $contactperson = '';
        $contactcode = 102;
        $contactts = date('Y-m-d H:i:s');
        $contactappointment = '';
        $contactremark = '';
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $contactremark, $contactappointment, $_SESSION['URID']);


        $display = new Display();
        return $this->mainPage($display->displaySuccess('Coded as refusal'));
    }

    function showBackFromSMS($primkey, $message = '') {
        $respondent = new Respondent($primkey);
        //check for complete!
        $respondent->setStatus(2);
        $respondent->saveChanges();
        $display = new Display();
        return $this->mainPage($display->displaySuccess('Interview completed'));
    }

    function showBarriers($primkey) {
        $respondent = new Respondent($primkey);


        $contactwith = '';
        $contactperson = '';
        $contactcode = 105;
        $contactts = date('Y-m-d H:i:s');
        $contactappointment = '';
        $contactremark = '';
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $contactremark, $contactappointment, $_SESSION['URID']);




        $display = new Display();
        return $this->mainPage($display->displaySuccess('Coded as (language) barriers'));
    }

    function showDisconnected($primkey) {
        $respondent = new Respondent($primkey);

        $contactwith = '';
        $contactperson = '';
        $contactcode = 104;
        $contactts = date('Y-m-d H:i:s');
        $contactappointment = '';
        $contactremark = '';
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $contactremark, $contactappointment, $_SESSION['URID']);


        $display = new Display();
        return $this->mainPage($display->displaySuccess('Number disconnected'));
    }

    function showCatiStartSurvey($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showStartCatiSurvey($respondent);
    }

    function mainPage($message = '') {
        $respondents = new Respondents();
//        $respondents = $respondents->getRespondentsByUser($this->user);
        $respondents = $respondents->getAvailableCatiRespondentsByUser($this->user);

        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showMain($respondents, $message);
    }

    function showSurveyCompleted($primkey, $message = '') {
        $respondent = new Respondent($primkey);
        $respondent->setStatus(2);
        $respondent->saveChanges();
        $display = new Display();
        return $this->showInfo($primkey, $display->displaySuccess('Survey completed'));
    }

    function showInfo($primkey, $message = '') {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showInfo($respondent, $message);
    }

    function showContacts($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showContacts($respondent);
    }

    function showHistory($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showHistory($respondent);
    }

    function showAddContact($primkey, $message = '') {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showAddContact($respondent, $message);
    }

    function showAddContactRes($primkey) {
        $respondent = new Respondent($primkey);

        $contactwith = loadvar("contactwith");
        $contactperson = loadvar("contactperson");
        $contactcode = loadvar("contactcode");
        $contactts = loadvar("contactts");
        $contactappointment = loadvar("contactappointment");
        $contactremark = loadvar("contactremark");

        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $contactremark, $contactappointment, $_SESSION['URID']);

        $display = new Display();
        if (sizeof($errorMessage) == 0) {
            return $this->showInfo($primkey, $display->displayInfo(Language::messageContactAdded()));
        } else {
            return $this->showAddContact($primkey, $display->displayError(implode('<br/>', $errorMessage)));
        }
    }

    function showStartSurvey($primkey) {
        $respondent = new Respondent($primkey);
        $_SESSION['SURVEYLOGIN'] = LOGIN_DIRECT;
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showStartSurvey($respondent);
    }

    function showCalendar() {
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showCalendar();
    }

    function showResetTestCases() {
        $respondents = new Respondents();
        $respondents->deleteTestRespondents($this->user);
        $display = new Display();
        return $this->mainPage($display->displaySuccess('test cases reset!'));
    }

    function showPreferencesRes() {
        $this->user->setFilter(loadvar('filter'));
        $this->user->setRegionFilter(loadvar('region'));
        $this->user->setTestMode(loadvar('testmode'));
        $this->user->setCommunication(loadvar('communication'));
        $this->user->setPuid(loadvar('puid'));
        $this->user->saveChanges();
        $display = new Display();
        return $this->mainPage($display->displaySuccess(Language::messagePreferencesSaved()));
    }

    function showPreferences() {
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showPreferences($this->user);
    }

    function showOtherSurveys() {
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showOtherSurveys();
    }

    function showRemarks($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showRemarks($respondent);
    }

    function showAddRemark($primkey) {
        $respondent = new Respondent($primkey);
        if (loadvar('remark') != '') {
            $remark = new Remarks();
            $remark->addRemark($respondent->getPrimkey(), loadvar('remark'), $_SESSION['URID']);
        }
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showRemarks($respondent);
    }

    function showEdit($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showEdit($respondent);
    }

    function showEditRes($primkey) {
        $respondent = new Respondent($primkey);
        $respondent->setFirstName(loadvar('firstname'));
        $respondent->setLastName(loadvar('lastname'));
        $respondent->setAddress1(loadvar('address1'));
        $respondent->setAddress2(loadvar('address2'));
        $respondent->setZip(loadvar('zip'));
        $respondent->setCity(loadvar('city'));
        $respondent->setTelephone1(loadvar('telephone1'));
        $respondent->setTelephone2(loadvar('telephone2'));
        $respondent->setEmail(loadvar('email'));
        //log???

        $errorMessage = $respondent->saveChanges();
        $display = new Display();
        $messageEditError = $display->displaySuccess(Language::messageRespondentChanged($primkey)); //'<div class="alert alert-info">Changes saved.</div>';
        if (sizeof($errorMessage) > 0) {
            $messageEditError = $display->displayError(implode('<br/>', $errorMessage));
        }
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showInfo($respondent, $messageEditError);
    }

    function showTracking($primkey) {
        $respondent = new Respondent($primkey);
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showTracking($respondent);
    }

    function showSendReceive() {
        $displayCatiInterviewer = new DisplayCatiInterviewer();
        return $displayCatiInterviewer->showSendReceive();
    }

}

?>