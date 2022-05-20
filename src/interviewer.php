<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Interviewer {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {
        global $logActions;
        if (getFromSessionParams('page') != null) {
            $_SESSION['LASTPAGE'] = getFromSessionParams('page');
            $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('page'));
        }

        if (isset($_SESSION['LASTPAGE'])) {
            switch ($_SESSION['LASTPAGE']) {
                case 'interviewer.info': return $this->showIwerInfoFromCalendar(getFromSessionParams('primkey'));
                case 'interviewer.search': return $this->showSearchRes();
                case 'interviewer.calendar': return $this->showCalendar();
                case 'interviewer.othersurveys': return $this->showOtherSurveys();
                case 'interviewer.respondent.history': return $this->showHistory(getFromSessionParams('primkey'));
                case 'interviewer.respondent.info': return $this->showInfo(getFromSessionParams('primkey'));
                case 'interviewer.backfromsms': return $this->showBackFromSMS(getFromSessionParams('primkey'), getFromSessionParams('suid'));
                case 'interviewer.surveycompleted': return $this->showSurveyCompleted(getFromSessionParams('primkey'), getFromSessionParams('suid'));
                case 'interviewer.respondent.info': return $this->showInfo(getFromSessionParams('primkey'));
                case 'interviewer.respondent.edit': return $this->showEdit(getFromSessionParams('primkey'));
                case 'interviewer.respondent.editres': return $this->showEditRes(getFromSessionParams('primkey'));
                case 'interviewer.respondent.tracking': return $this->showTracking(getFromSessionParams('primkey'));
                case 'interviewer.respondent.remarks': return $this->showRemarks(getFromSessionParams('primkey'));
                case 'interviewer.respondent.addremark': return $this->showAddRemark(getFromSessionParams('primkey'));
                case 'interviewer.respondent.contacts': return $this->showContacts(getFromSessionParams('primkey'));
                case 'interviewer.respondent.addcontact': return $this->showAddContact(getFromSessionParams('primkey'));
                case 'interviewer.respondent.addcontactres': return $this->showAddContactRes(getFromSessionParams('primkey'));
                case 'interviewer.respondent.startsurvey': return $this->showStartSurvey(getFromSessionParams('primkey'));
                case 'interviewer.proxycheck': return $this->showProxyCheck(getFromSessionParams('primkey'));
                case 'interviewer.preferences': return $this->showPreferences();
                case 'interviewer.preferencesres': return $this->showPreferencesRes();
                case 'interviewer.preferences.resettest': return $this->showResetTestCases();
                case 'interviewer.sendreceive': return $this->showSendReceive();
                case 'interviewer.sendreceive.upload': return $this->showSendReceiveUploadData();
                case 'interviewer.sendreceive.receive': return $this->showSendReceiveReceiveData();
                case 'interviewer.household.info': return $this->showInfo(getFromSessionParams('primkey'), '', 2);
                case 'interviewer.household.startsurvey': return $this->showStartSurvey(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.edit': return $this->showEdit(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.editres': return $this->showEditRes(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.remarks': return $this->showRemarks(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.addremark': return $this->showAddRemark(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.history': return $this->showHistory(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.contacts': return $this->showContacts(getFromSessionParams('primkey'), 2);
                case 'interviewer.household.addcontact': return $this->showAddContact(getFromSessionParams('primkey'), '', 2);
                case 'interviewer.household.addcontactres': return $this->showAddContactRes(getFromSessionParams('primkey'), 2);
                case 'interviewer.supervisor.login': return $this->showSupervisorLogin();
                case 'interviewer.supervisor.login.res': return $this->showSupervisorLoginRes();
                case 'interviewer.supervisor.logout': return $this->showSupervisorLogout();

                default: return $this->mainPage();
            }
        } else {
            $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('interviewer.home'));
            return $this->mainPage();
        }
    }

    function showSearchRes() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            $households = new Households();
            $households = $households->getHouseholdsByUserSearch($this->user, loadvar('searchterm'));
            $displayInterviewer = new DisplayInterviewer();
            return $displayInterviewer->showSearchRes($households);
        } else {
            $respondents = new Respondents();
            $respondents = $respondents->getRespondentsByUserSearch($this->user, loadvar('searchterm'));
            $displayInterviewer = new DisplayInterviewer();
            return $displayInterviewer->showSearchRes($respondents);
        }
    }

    function mainPage($message = '') {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            $households = new Households();
            $households = $households->getHouseholdsByUser($this->user);
            $displayInterviewer = new DisplayInterviewer();
            return $displayInterviewer->showMain($households, $message);
        } else {
            $respondents = new Respondents();
            $respondents = $respondents->getRespondentsByUser($this->user);
            $displayInterviewer = new DisplayInterviewer();
            return $displayInterviewer->showMain($respondents, $message);
        }
    }

    function showBackFromSMS($primkey, $suid) {
        $type = 1;
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            if ($suid == 1) { //return from household survey 
                $type = 2;
            }
        }
        $display = new Display();
        return $this->showInfo($primkey, $display->displaySuccess(Language::labelInterviewerBackFromSMS()), $type);
    }

    function showSurveyCompleted($primkey, $suid = 1) {
        $type = 1;
        if ($suid == 1) {
            $respondentorhousehold = new Household($primkey);
            //check completed coverscreen
            $this->checkCompletedCoverscreen($respondentorhousehold);
            $type = 2;
        } else {
            $respondentorhousehold = new Respondent($primkey);
            //respondent completed. set 10% to be validated
            if (mt_rand(1, 10) == 1) {
                $respondentorhousehold->setValidation(1);
            }
        }
        $respondentorhousehold->setStatus(2);
        $respondentorhousehold->saveChanges();
        //add contact 500
        $contactcode = Language::completedInterviewCode();
        $contactts = date('Y-m-d H:i:s');
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($respondentorhousehold->getPrimkey(), $contactcode, $contactts, '', '', '', '', $_SESSION['URID']);
        //end add contact
        $display = new Display();
        return $this->showInfo($primkey, $display->displaySuccess(Language::labelInterviewerSurveyCompleted()), $type);
    }

    function getDataFromSurvey($d, $variablename, $default = '') {

        // using data records
        if (Config::useDataRecords()) {
            $var = $d->getData($variablename);
            if (isset($var)) {
                return $var->getAnswer();
            } else {
                return $default;
            }
        }

        // fall back on _data table
        global $db;
        $surv = new Survey($d->getSuid());
        $decrypt = "answer as answer_dec";
        if ($surv->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(answer, '" . $surv->getDataEncryptionKey() . "') as answer_dec";
        }
        $query = "select " . $decrypt . " from " . Config::dbSurveyData() . "_data where suid=" . $d->getSuid() . " and primkey='" . $d->getPrimaryKey() . "' and variablename='" . $variablename . "'";
        $res = $db->selectQuery($query);
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                $row = $db->getRow($res);
                return $row["answer_dec"];
            }
        }

        // not found or something went wrong
        return $default;
    }

    function checkCompletedCoverscreen(&$respondentorhousehold) {
        if ($respondentorhousehold->getStatus() != 2) { //not completed yet
            //HAALSI functionality
            //change 'present' to 'no' for people that are no longer in the hh
            $d = new DataRecord(1, $respondentorhousehold->getPrimkey());
            $familyR = $this->getDataFromSurvey($d, 'familyR');
            $financialR = $this->getDataFromSurvey($d, 'financialR');
            $coverscreenR = $this->getDataFromSurvey($d, 'HR009'); //person currently filling out questions

            $respondents = $respondentorhousehold->getRespondents();
            $Rcnt = 1;

            foreach ($respondents as $respondent) {
                $present = 1;
                if (trim($this->getDataFromSurvey($d, 'HHMemberName[' . $Rcnt . ']')) == '') {
                    $present = 0;
                }
                $permanent = 1;
                if (trim($this->getDataFromSurvey($d, 'HR015[' . $Rcnt . ']')) != '') {
                    $permanent = trim($this->getDataFromSurvey($d, 'HR015[' . $Rcnt . ']'));
                }
                $movedout = 0;
                if (trim($this->getDataFromSurvey($d, 'HR011[' . $Rcnt . ']')) != '') { //what happenend 
                    $movedout = trim($this->getDataFromSurvey($d, 'HR011[' . $Rcnt . ']'));
                }

                $finR = 0;
                if ($Rcnt == $financialR) {
                    $finR = 1;
                }
                $famR = 0;
                if ($Rcnt == $familyR) {
                    $famR = 1;
                }
                $covR = 0;
                if ($Rcnt == $coverscreenR) {
                    $covR = 1;
                }
                $respondent->setPresent($present);
                $respondent->setFinR($finR);
                $respondent->setFamR($famR);
                $respondent->setCovR($covR);
                $respondent->setPermanent($permanent);
                $respondent->setMovedOut($movedout);
                $respondent->setHhOrder($Rcnt);
                $respondent->saveChanges();
                $Rcnt++;
            }
            $hhmembers = $this->getDataFromSurvey($d, 'HRcnt'); //get number of people

            $newmembers = $hhmembers - sizeof($respondents);
            if ($newmembers > 0) { //add more people!
                for ($i2 = 0; $i2 < $newmembers; $i2++) {
                    $hhid = $respondentorhousehold->getHhid();
                    $rtid = $hhid . leadingZeros($Rcnt, 2);
                    $firstname = $this->getDataFromSurvey($d, 'HR002[' . $Rcnt . ']');
                    $sex = $this->getDataFromSurvey($d, 'HR003[' . $Rcnt . ']');
                    $age = date('Y') - $this->getDataFromSurvey($d, 'HR004[' . $Rcnt . ']');
                    $permanent = $this->getDataFromSurvey($d, 'HR015[' . $Rcnt . ']');
                    $respondentsClass = new Respondents();

                    $test = $respondentorhousehold->getTest(); //get test mode from household

                    $respondentsClass->insertR($rtid, $firstname, $this->user, $sex, $age, $hhid, 0, 1, $permanent, $Rcnt, $test); //0: not selected!

                    $relationshiphh = '';
                    if (trim($this->getDataFromSurvey($d, 'HR005[' . $Rcnt . ']')) != '') { //what happenend 
                        $relationshiphh = trim($this->getDataFromSurvey($d, 'HR005[' . $Rcnt . ']'));
                    }


                    $respondent = new Respondent($rtid);
                    //aditional info
                    $finR = 0;
                    if ($Rcnt == $financialR) {
                        $finR = 1;
                    }
                    $famR = 0;
                    if ($Rcnt == $familyR) {
                        $famR = 1;
                    }
                    $covR = 0;
                    if ($Rcnt == $coverscreenR) {
                        $covR = 1;
                    }


                    //$birthdate 
                    //$schoolingyears
                    //$educationlevel
                    //$occupationalstatus
                    //$relationshiphh
                    //   $respondent->setPresent(1); //always 1!  set in insertR
                    $respondent->setFinR($finR);
                    $respondent->setFamR($famR);
                    $respondent->setCovR($covR);
                    $respondent->setRelationshipHhHead($relationshiphh);

                    //   $respondent->setPermanent($permanent); //set in insertR
                    //   $respondent->setHhOrder($Rcnt); //set in insertR
                    //preload from household
                    $respondent->setAddress1($respondentorhousehold->getAddress1());
                    $respondent->setCity($respondentorhousehold->getCity());
                    $respondent->setPuid($respondentorhousehold->getPuid());

                    $respondent->saveChanges();

                    $Rcnt++;
                }
            }
            //now check if there is anyone eligible..
        }
    }

    function showIwerInfoFromCalendar($primkey) {
        $respondent = new Respondent($primkey);
        if ($respondent->getPrimkey() == '') {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showInfo($respondent, $message);
    }

    function showInfo($primkey, $message = '', $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showInfo($respondent, $message);
    }

    function showContacts($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showContacts($respondent);
    }

    function showHistory($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showHistory($respondent);
    }

    function showAddContact($primkey, $message = '', $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showAddContact($respondent, $message);
    }

    function showAddContactRes($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }

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
            return $this->showInfo($primkey, $display->displayInfo(Language::messageContactAdded()), $type);
        } else {
            return $this->showAddContact($primkey, $display->displayError(implode('<br/>', $errorMessage)), $type);
        }
    }

    function showProxyCheck($primkey) {
        $respondent = new Respondent($primkey);
        $proxycodeentered = loadvar('proxycodeentered');
        $display = new Display();
        $code = loadvar('code');
        $proxypermission = new ProxyPermission();
        if ($proxypermission->checkProxyCode($code, $proxycodeentered) || $proxycodeentered == '123') {
            $displayInterviewer = new DisplayInterviewer();
            return $displayInterviewer->showStartProxySurvey($respondent, $display->displaySuccess(Language::labelInterviewerProxyCorrect()));
        } else { //wrong!
            return $this->showStartSurvey($primkey, 1, $display->displayError(Language::labelInterviewerProxyInCorrect()));
        }
    }

    function showStartSurvey($primkey, $type = 1, $message = '') {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $_SESSION['SURVEYLOGIN'] = LOGIN_DIRECT;


        $respondent->setStatus(1); //set status to 1: started survey
        $respondent->saveChanges();
        //add contact 100
        $contactcode = Language::startInterviewCode();
        $contactts = date('Y-m-d H:i:s');
        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($respondent->getPrimkey(), $contactcode, $contactts, '', '', '', '', $_SESSION['URID']);
        //end add contact


        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showStartSurvey($respondent, $message);
    }

    function showCalendar() {
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showCalendar();
    }

    function showResetTestCases() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            $households = new Households();
            $households->deleteTestHouseholds($this->user);
        } else {
            $respondents = new Respondents();
            $respondents->deleteTestRespondents($this->user);
        }
        $display = new Display();
        return $this->mainPage($display->displaySuccess(Language::labelInterviewerTestReset()));
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
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showPreferences($this->user);
    }

    function showOtherSurveys() {
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showOtherSurveys();
    }

    function showRemarks($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showRemarks($respondent);
    }

    function showAddRemark($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        if (loadvar('remark') != '') {
            $remark = new Remarks();
            $remark->addRemark($respondent->getPrimkey(), loadvar('remark'), $_SESSION['URID']);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showRemarks($respondent);
    }

    function showEdit($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showEdit($respondent);
    }

    function showEditRes($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
            $respondent->setFirstName(loadvar('firstname'));
            $respondent->setLastName(loadvar('lastname'));
        } else {
            $respondent = new Household($primkey);
            $respondent->setName(loadvar('name'));
            $respondent->setAddress1(loadvar('address1'));
            $respondent->setAddress2(loadvar('address2'));
            $respondent->setZip(loadvar('zip'));
            $respondent->setCity(loadvar('city'));
        }
        if (dbConfig::defaultPanel() == PANEL_RESPONDENT) { //only save for respondent panels
            $respondent->setAddress1(loadvar('address1'));
            $respondent->setAddress2(loadvar('address2'));
            $respondent->setZip(loadvar('zip'));
            $respondent->setCity(loadvar('city'));
        }

        $respondent->setTelephone1(loadvar('telephone1'));
        //$respondent->setTelephone2(loadvar('telephone2'));
        $respondent->setEmail(loadvar('email'));
        //log???

        $errorMessage = $respondent->saveChanges();
        $display = new Display();
        $messageEditError = $display->displaySuccess(Language::messageRespondentChanged($respondent)); //'<div class="alert alert-info">Changes saved.</div>';
        if (sizeof($errorMessage) > 0) {
            $messageEditError = $display->displayError(implode('<br/>', $errorMessage));
        }
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showInfo($respondent, $messageEditError);
    }

    function showTracking($primkey) {
        $respondent = new Respondent($primkey);
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showTracking($respondent);
    }

    function showSendReceive() {
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showSendReceive();
    }

    function showSendReceiveUploadData() {
        $displayInterviewer = new DisplayInterviewer();
        $communication = new Communication();
        $data = $communication->exportTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts', 'households', 'respondents'), $this->user->getLastData(), 'primkey not like "999%"'); //no test data
        if ($communication->sendToServer($data, $this->user->getUrid())) { //success sending data to server
            //update lastdate!
            $this->user->setLastData(date('Y-m-d H:i:s'));
            $this->user->saveChanges();
            $message = $displayInterviewer->displaySuccess(Language::labelDataUploaded());
        } else {
            $message = $displayInterviewer->displayError(Language::labelDataNotUploaded());
        }
        return $displayInterviewer->showSendReceive($message);
    }

    function showSendReceiveReceiveData() {
        $displayInterviewer = new DisplayInterviewer();
        $communication = new Communication();
        if ($communication->receiveFromServer($this->user->getUrid())) { //success receiving data to server
            $message = $displayInterviewer->displaySuccess(Language::labelDataReceived());
            //send confirmation to the server
            $communication->confirmDataReceived($this->user->getUrid());
        } else {
            $message = $displayInterviewer->displayError(Language::labelDataNotReceived());
        }
        return $displayInterviewer->showSendReceive($message);
    }

    function showSupervisorLogin($message = '') {
        $displayInterviewer = new DisplayInterviewer();
        return $displayInterviewer->showSupervisorLogin($message);
    }

    function showSupervisorLogout() {
        $_SESSION['SUPLOGIN'] = 0;
        $displayInterviewer = new DisplayInterviewer();
        $message = $displayInterviewer->displayError('Supervisor logged out.');
        return $this->mainPage($message);
    }

    function showSupervisorLoginRes() {
        $_SESSION['SUPLOGIN'] = 0;
        $displayInterviewer = new DisplayInterviewer();
        $pwd = loadvar('suppwd');
        $_SESSION['SUPLOGIN'] = 1;
        $message = $displayInterviewer->displayError('Logged in as supervisor.');
        return $this->mainPage($message);        
    }

}

?>