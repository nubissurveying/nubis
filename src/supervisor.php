<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Supervisor {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {
        if (getFromSessionParams('page') != null) {
            $_SESSION['LASTPAGE'] = getFromSessionParams('page');
        }

        //  echo '<br/><br/><br/>' . getFromSessionParams('page');
        if (isset($_SESSION['LASTPAGE'])) {
            switch ($_SESSION['LASTPAGE']) {
                case 'supervisor.search': return $this->showSearchRes();
                    break;
                case 'supervisor.preferences': return $this->showPreferences();
                    break;
                case 'supervisor.preferencesres': return $this->showPreferencesRes();
                    break;
                case 'supervisor.sendreceive': return $this->showSendReceive();
                    break;

                case 'supervisor.sample': return $this->showSample();
                    break;


                case 'supervisor.unassignedsample': return $this->showUnassignedSample();
                    break;
                case 'supervisor.unassignedsample.assign': return $this->showAssignSample();
                    break;

                case 'supervisor.interviewers': return $this->mainPage();
                    break;
                case 'supervisor.interviewer.info': return $this->showInterviewer();
                    break;



                case 'supervisor.interviewer.household.reassign': return $this->showRespondentReassign(getFromSessionParams('primkey'));
                    break;

                case 'supervisor.interviewer.household.contact.setstatus': return $this->showRespondentSetContactStatus(getFromSessionParams('primkey'), 2);
                    break;
                case 'supervisor.interviewer.household.respondent.contact.setstatus': return $this->showRespondentSetContactStatus(getFromSessionParams('primkey'));
                    break;




                case 'supervisor.interviewer.sample': return $this->showInterviewerAssignedSample();
                    break;



                case 'supervisor.interviewer.respondent.info': return $this->showInterviewerRespondentInfo(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.respondent.contacts': return $this->showInterviewerContacts(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.respondent.history': return $this->showInterviewerHistory(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.respondent.remarks': return $this->showRemarks(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.respondent.edit': return $this->showEdit(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.respondent.editres': return $this->showEditRes(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.respondent.addremark': return $this->showAddRemark(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.household.respondent.setvalidation': return $this->showSetValidation(getFromSessionParams('primkey'));
                    break;

                case 'supervisor.interviewer.household.info': return $this->showInterviewerHouseholdInfo(getFromSessionParams('primkey'));
                    break;
                case 'supervisor.interviewer.household.contacts': return $this->showInterviewerContacts(getFromSessionParams('primkey'), 2);
                    break;
                case 'supervisor.interviewer.household.history': return $this->showInterviewerHistory(getFromSessionParams('primkey'), 2);
                    break;
                case 'supervisor.interviewer.household.remarks': return $this->showRemarks(getFromSessionParams('primkey'), 2);
                    break;
                case 'supervisor.interviewer.household.edit': return $this->showEdit(getFromSessionParams('primkey'), 2);
                    break;
                case 'supervisor.interviewer.household.editres': return $this->showEditRes(getFromSessionParams('primkey'), 2);
                    break;
                case 'supervisor.interviewer.household.addremark': return $this->showAddRemark(getFromSessionParams('primkey'), 2);
                    break;


                case 'supervisor.reports': return $this->showSupervisorReports();
                    break;

                case "supervisor.reports.statistics.response": return $this->showOutputStaticsResponse();
                    break;
                case "supervisor.reports.statistics.contacts.graphs": return $this->showOutputStaticsContactsGraphs();
                    break;
                case "supervisor.reports.statistics.surveyinfo": return $this->showOutputStaticsSurveyInfo();
                    break;

                // case "supervisor.sample": return $this->showSample(); break;


                default: return $this->mainPage();
            }
        } else {
            return $this->mainPage();
        }
    }

    function mainPage() {
        $users = new Users();
        $users = $users->getUsersBySupervisor($this->user->getUrid());
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->displayInterviewers($users);
    }

    function showInterviewer($message = '') {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->displayInterviewerAssignedSample(new User(getFromSessionParams('interviewer')), $message);
    }

    function showRespondentReassign($primkey) {

        $household = new Household($primkey);
        $oldurid = $household->getUrid();
        $newurid = loadvar('uridsel');
        $display = new Display();
        if ($household->getUrid() != $newurid) { //check for double submit -> not yet assigned to this urid
            $household->setUrid($newurid); //for local!
            $household->saveChanges();
            $respondents = $household->getRespondents();
            foreach ($respondents as $respondent) {
                $respondent->setUrid($newurid);
                $respondent->saveChanges();
            }

            $communication = new Communication;
            $communication->reassignHousehold($household, $oldurid, $newurid);
            return $this->showInterviewer($display->displayInfo(Language::labelSupervisorHouseholdReassigned()));
        } else {
            return $this->showInterviewer($display->displayInfo(Language::labelSupervisorHouseholdNotReassigned()));
        }
    }

    function showRespondentSetContactStatus($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $contactcode = loadvar("csid");
        $contactts = date('Y-m-d H:i:s');

        $contacts = new Contacts();
        $errorMessage = $contacts->addContact($primkey, $contactcode, $contactts, '', '', '', '', $_SESSION['URID']);
        if ($respondent->getUrid() != $_SESSION['URID']) { //if assigned to other urid than this
            //add to communication!
            $communication = new Communication();
            $communication->addSQLToUser($contacts->getLastQuery(), $respondent->getUrid());
        }
        $display = new Display();
        if (sizeof($errorMessage) == 0) {
            return $this->showInterviewerRespondentInfo($primkey, $display->displayInfo(Language::messageContactAdded()), $type);
        } else {
            return $this->showInterviewerRespondentInfo($primkey, $display->displayError(implode('<br/>', $errorMessage)), $type);
        }
    }

    function showInterviewerAssignedSample() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->displayInterviewerAssignedSample(new User(getFromSessionParams('interviewer')));
    }

    function showInterviewerHouseholdInfo($primkey, $message = '') {
        return $this->showInterviewerRespondentInfo($primkey, $message, 2);
    }

    function showInterviewerContacts($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showContacts($respondent);
    }

    function showInterviewerHistory($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showHistory($respondent);
    }

    function showRemarks($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showRemarks($respondent);
    }

    function showAddRemark($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        if (loadvar('remark') != '') {
            $remark = new Remarks();
            $query = $remark->addRemark($respondent->getPrimkey(), loadvar('remark'), $_SESSION['URID']);
            $communication = new Communication();
            $communication->addSQLToUser($query, $respondent->getUrid(), true);
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showRemarks($respondent);
    }

    function showSetValidation($primkey, $type = 1) {
        //only for respondents 
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $respondent->setValidation(loadvar());
        $errorMessage = $respondent->saveChanges();
        $display = new Display();
        $messageEditError = $display->displaySuccess(Language::messageValidated($respondent)); //'<div class="alert alert-info">Changes saved.</div>';
        if (sizeof($errorMessage) > 0) {
            $messageEditError = $display->displayError(implode('<br/>', $errorMessage));
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showInfo($respondent, $messageEditError);
    }

    function showInterviewerRespondentInfo($primkey, $message = '', $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showInfo($respondent, $message);
    }

    function showEdit($primkey, $type = 1) {
        if ($type == 1) {
            $respondent = new Respondent($primkey);
        } else {
            $respondent = new Household($primkey);
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showEdit($respondent);
    }

    function showSupervisorReports() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->displaySupervisorReports();
    }

    function showOutputStaticsContactsGraphs() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showOutputStatisticsContactsGraphs(1);
    }

    function showOutputStaticsSurveyInfo() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showOutputStatisticsSurveyInfo(1);
    }

    function showOutputStaticsResponse() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showOutputResponse();
    }

    function showEditRes($primkey, $type = 1) {
        $communication = new Communication();
        if ($type == 1) {
            $respondent = new Respondent($primkey);
            $respondent->setFirstName(loadvar('firstname'), true);
            $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());
            $respondent->setLastName(loadvar('lastname'), true);
            $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());
        } else {
            $respondent = new Household($primkey);
            $respondent->setName(loadvar('name'), true);
        }
        $respondent->setAddress1(loadvar('address1'), true);
        $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());
        $respondent->setAddress2(loadvar('address2'), true);
        $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());
        $respondent->setZip(loadvar('zip'), true);
        $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());
        $respondent->setCity(loadvar('city'), true);
        $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());
        $respondent->setTelephone1(loadvar('telephone1'), true);
        $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());

        //$respondent->setTelephone2(loadvar('telephone2'));
        //$communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());

        $respondent->setEmail(loadvar('email'), true);
        $communication->addSQLToUser($respondent->getLastQuery(), $respondent->getUrid());

        //log???

        $errorMessage = $respondent->saveChanges();
        $display = new Display();
        $messageEditError = $display->displaySuccess(Language::messageRespondentChanged($respondent)); //'<div class="alert alert-info">Changes saved.</div>';
        if (sizeof($errorMessage) > 0) {
            $messageEditError = $display->displayError(implode('<br/>', $errorMessage));
        }
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showInfo($respondent, $messageEditError);
    }

    function showPreferences() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showPreferences($this->user);
    }

    function showPreferencesRes() {
        //$this->user->setFilter(loadvar('filter'));
        $this->user->setRegionFilter(loadvar('region'));
        $this->user->setTestMode(loadvar('testmode'));
        $this->user->setCommunication(loadvar('communication'));
        $this->user->setPuid(loadvar('puid'));
        $this->user->saveChanges();
        $display = new Display();
        return $this->mainPage($display->displaySuccess(Language::messagePreferencesSaved()));
    }

    function showSendReceive() {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showSendReceive();
    }

    function showSample($message = '') {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->displayInterviewerAssignedSample(new User($_SESSION['URID']));
    }

    function showUnassignedSample($message = '') {
        $displaySupervisor = new DisplaySupervisor();
        return $displaySupervisor->showUnassignedSample($message);
    }

    function showAssignSample() {
        $assignids = loadvar('assignid');
        $selurid = loadvar('selurid');
        if (sizeof($assignids) > 0 && ($selurid > 0 || $selurid == -1)) { //-1: back to sysadmin
            foreach ($assignids as $id) { //sysadmin mode: change on server 'only'
                $household = new Household($id);
                $household->setUrid($selurid);
                $household->saveChanges();
                $respondents = $household->getRespondents();
                foreach ($respondents as $respondent) {
                    $respondent->setUrid($selurid);
                    $respondent->saveChanges();
                }
                $communication = new Communication();
                $communication->assignHousehold($household, $selurid);
            }
            if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
                $message = Language::labelSupervisorHouseholdAssigned();
            } else {
                $message = Language::labelSupervisorRespondentAssigned();
            }
        } else {
            if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
                $message = Language::labelSupervisorHouseholdsNotAssigned();
            } else {
                $message = Language::labelSupervisorRespondentsNotAssigned();
            }
        }
        $display = new Display();
        return $this->showUnassignedSample($display->displayInfo($message));
    }

    function showSearchRes() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            $households = new Households();
            $households = $households->getHouseholdsByUserSearch($this->user, loadvar('searchterm'));
            $displaySupervisor = new DisplaySupervisor();
            return $displaySupervisor->showSearchRes($households);
        } else {
            $respondents = new Respondents();
            $respondents = $respondents->getRespondentsByUserSearch($this->user, loadvar('searchterm'));
            $displaySupervisor = new DisplaySupervisor();
            return $displaySupervisor->showSearchRes($respondents);
        }
    }

}

?>