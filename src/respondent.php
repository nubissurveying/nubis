<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Respondent {

    var $respondent;
    var $remarks;
    var $contacts;
    var $logactions;
    var $lastQuery = '';
    var $user = null;

    function __construct($rowOrPrimkey) {

        global $db;

        $this->remarks = new Remarks();

        $this->contacts = new Contacts();

        $this->logactions = new LogActions();

        if (is_array($rowOrPrimkey)) {

            $this->respondent = $rowOrPrimkey;
        } else {
            $query = 'select *, ' . Respondents::getDeIdentified() . ' from ' . Config::dbSurvey() . '_respondents where primkey = \'' . prepareDatabaseString($rowOrPrimkey) . '\'';
            $result = $db->selectQuery($query);
            $this->respondent = $db->getRow($result);
        }
        $this->user = new User($this->getUrid());
    }

    function getPrimkey() {

        return $this->respondent['primkey'];
    }

    function getLoginCode() {

        return $this->respondent['logincode_dec'];
    }

    function setLoginCode($logincode) {

        $this->respondent['logincode_dec'] = $logincode;
    }

    function getName() {

        return trim($this->getFirstname() . ' ' . $this->getLastname());
    }

    function getFirstname() {

        return $this->respondent['firstname_dec'];
    }

    function setFirstname($firstname, $setQuery = false) {
        if ($this->respondent['firstname_dec'] != $firstname) { //only set when different
            $this->respondent['firstname_dec'] = $firstname;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'firstname = AES_ENCRYPT(\'' . prepareDatabaseString($this->getFirstName()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getLastname() {

        return $this->respondent['lastname_dec'];
    }

    function setLastname($lastname, $setQuery = false) {
        if ($this->respondent['lastname_dec'] != $lastname) { //only set when different
            $this->respondent['lastname_dec'] = $lastname;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'lastname = AES_ENCRYPT(\'' . prepareDatabaseString($this->getLastName()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getAddress1() {

        return $this->respondent['address1_dec'];
    }

    function setAddress1($address, $setQuery = false) {
        if ($this->respondent['address1_dec'] != $address) { //only set when different
            $this->respondent['address1_dec'] = $address;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'address1 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getAddress1()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = ' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getAddress2() {

        return $this->respondent['address2_dec'];
    }

    function setAddress2($address, $setQuery = false) {
        if ($this->respondent['address2_dec'] != $address) { //only set when different
            $this->respondent['address2_dec'] = $address;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'address2 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getAddress2()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getZip() {

        return $this->respondent['zip_dec'];
    }

    function setZip($zip, $setQuery = false) {
        if ($this->respondent['zip_dec'] != $zip) { //only set when different
            $this->respondent['zip_dec'] = $zip;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'zip = AES_ENCRYPT(\'' . prepareDatabaseString($this->getZip()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getCity() {
        return $this->respondent['city_dec'];
    }

    function setCity($city, $setQuery = false) {
        if ($this->respondent['city_dec'] != $city) { //only set when different
            $this->respondent['city_dec'] = $city;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'city = AES_ENCRYPT(\'' . prepareDatabaseString($this->getCity()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getLongitude() {
        return $this->respondent['longitude_dec'];
    }

    function setLongitude($longitude) {
        if ($this->respondent['longitude_dec'] != $longitude) { //only set when different
            $this->respondent['longitude_dec'] = $longitude;
        }
    }

    function getLatitude() {
        return $this->household['latitude_dec'];
    }

    function setLatitude($latitude) {
        if ($this->household['latitude_dec'] != $latitude) { //only set when different
            $this->household['latitude_dec'] = $latitude;
        }
    }

    function getEmail() {
        return $this->respondent['email_dec'];
    }

    function setEmail($email, $setQuery = false) {
        if ($this->respondent['email_dec'] != $email) { //only set when different
            $this->respondent['email_dec'] = $email;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'email = AES_ENCRYPT(\'' . prepareDatabaseString($this->getEmail()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getTelephone1() {

        return $this->respondent['telephone1_dec'];
    }

    function setTelephone1($telephone, $setQuery = false) {
        if ($this->respondent['telephone1_dec'] != $telephone) { //only set when different
            $this->respondent['telephone1_dec'] = $telephone;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'telephone1 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getTelephone1()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getTelephone2() {

        return $this->respondent['telephone2_dec'];
    }

    function setTelephone2($telephone, $setQuery = false) {
        if ($this->respondent['telephone2_dec'] != $telephone) { //only set when different
            $this->respondent['telephone2_dec'] = $telephone;
            if ($setQuery) {
                $this->lastQuery = '';
                $user = new User($_SESSION['URID']);
                if ($user->getUserType() == USER_SUPERVISOR) {
                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
                    $this->lastQuery .= 'telephone2 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getTelephone2()) . '\', \'' . Config::smsPersonalInfoKey() . '\') ';
                    $this->lastQuery .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';
                }
            }
        }
    }

    function getHhid() {
        return $this->respondent['hhid'];
    }

    function getHhOrder() {
        return $this->respondent['hhorder'];
    }

    function setHhOrder($order) {
        $this->respondent['hhorder'] = $order;
    }

    function getHhHead() {
        return $this->respondent['hhhead'];
    }

    function setHhHead($hhhead) {
        if ($this->respondent['hhhead'] != $hhhead) { //only set when different
            $this->respondent['hhhead'] = $hhhead;
        }
    }

    function getRemarks() {
        return $this->remarks->getRemarks($this->getPrimkey());
    }

    function getContacts() {

        return $this->contacts->getContacts($this->getPrimkey());
    }

    function getHistory() {

        return $this->logactions->getActionsByPrimkey($this->getPrimkey());
    }

    function getLastContact() {

        $contacts = $this->contacts->getContacts($this->getPrimkey());

        if (sizeof($contacts) > 0) {

            return $contacts[0];
        }

        return null;
    }

    function isRefusal() {

        $contacts = $this->contacts->getContacts($this->getPrimkey());

        foreach ($contacts as $contact) {

            if ($contact->isRefusal()) {

                return true;
            }
        }

        return false;
    }

    function isCompleted() {

        return $this->getStatus() == 2;
    }

    function isStarted() {

        return $this->getStatus() == 1;
    }

    function isNonSample() {

        $contacts = $this->contacts->getContacts($this->getPrimkey());

        foreach ($contacts as $contact) {

            if ($contact->isNonSample()) {

                return true;
            }
        }

        return false;
    }

    function getStatus() {

        return $this->respondent['status'];
    }

    function setStatus($status) {

        $this->respondent['status'] = $status;
    }

    function getInfo() {

        return $this->respondent['info_dec'];
    }

    function getContactPerson() {

        return $this->respondent['contactperson_dec'];
    }

    function getPuid() {
        return $this->respondent['puid'];
    }

    function setPuid($puid) {
        $this->respondent['puid'] = $puid;
    }

    function getSelected() {

        return $this->respondent['selected'];
    }

    function setSelected($selected) {

        $this->respondent['selected'] = $selected;
    }

    function isSelected() {

        return ($this->getSelected() == 1);
    }

    function setPresent($present) {

        $this->respondent['present'] = $present;
    }

    function isPresent() {

        return ($this->getPresent() == 1);
    }

    function getPresent() {

        return $this->respondent['present'];
    }

    function getFinR() {

        return $this->respondent['finr'];
    }

    function setFinR($finr) {

        $this->respondent['finr'] = $finr;
    }

    function isFinR() {

        return ($this->getFinR() == 1);
    }

    function getFamR() {

        return $this->respondent['famr'];
    }

    function setFamR($famr) {

        $this->respondent['famr'] = $famr;
    }

    function isFamR() {

        return ($this->getFamR() == 1);
    }

    function getCovR() {

        return $this->respondent['covr'];
    }

    function setCovR($covr) {

        $this->respondent['covr'] = $covr;
    }

    function isCovR() {

        return ($this->getFamR() == 1);
    }

    function getSex() {

        return $this->respondent['sex_dec'];
    }

    function getAge() {

        return $this->respondent['age_dec'];
    }

    function getBirthDate() {
        return $this->respondent['birthdate_dec'];
    }

    function setBirthDate($birthdate) {
        $this->respondent['birthdate_dec'] = $birthdate;
    }

    function getAgeFromBirthDate() {
        if ($this->getBirthDate() != '') {
            return floor((time() - strtotime($this->getBirthDate())) / 31556926);
        } else {
            return $this->getAge();
        }
    }

    function getBirthYear() {
        $birthdate = $this->getBirthDate();
        if ($birthdate != '') {
            return date('Y', strtotime($birthdate));
        }
    }

    function getBirthMonth() {
        $birthdate = $this->getBirthDate();
        if ($birthdate != '') {
            return date('m', strtotime($birthdate));
        }
        return '';
    }

    function getConsentType() {
        if (isset($this->respondent['consenttype'])) {
            return $this->respondent['consenttype'];
        }
        return 0;
    }

    function setConsentType($consenttype) {
        $this->respondent['consenttype'] = $consenttype;
    }

    function getValidation() {
        if (isset($this->respondent['validation'])) {
            return $this->respondent['validation'];
        }
    }

    function setValidation($validation) {
        if (isset($this->respondent['validation'])) {
            $this->respondent['validation'] = $validation;
        }
    }

    function getMovedOut() {
        if (isset($this->respondent['movedout'])) {
            return $this->respondent['movedout'];
        }
    }

    function setMovedOut($movedout) {
        if (isset($this->respondent['movedout'])) {
            $this->respondent['movedout'] = $movedout;
        }
    }

    function getYearsOfSchooling() {

        return $this->respondent['schoolingyears'];
    }

    function setYearsOfSchooling($schoolingyears) {
        $this->respondent['schoolingyears'] = $schoolingyears;
    }

    function getEducationLevel() {

        return $this->respondent['educationlevel'];
    }

    function setEducationLevel($educationlevel) {
        $this->respondent['educationlevel'] = $educationlevel;
    }

    function getOccupationalStatus() {

        return $this->respondent['occupationalstatus'];
    }

    function getRelationshipHhHead() {

        return $this->respondent['relationshiphh'];
    }

    function setRelationshipHhHead($relationshiphh) {

        $this->respondent['relationshiphh'] = $relationshiphh;
    }

    function getSpousePrimkey() {
        return $this->respondent['spouseprimkey'];
    }

    function setSpousePrimkey($spouseprimkey) {
        $this->respondent['spouseprimkey'] = $spouseprimkey;
    }

    function getUrid() {
        if ($this->getHhHead() != '') {
            return $this->getHousehold()->getUrid();
        }
        return $this->respondent['urid'];
    }

    function setUrid($urid) {
        $this->respondent['urid'] = $urid;
    }

    function getPermanent() {
        return $this->respondent['permanent'];
    }

    function setPermanent($permanent) {
        $this->respondent['permanent'] = $permanent;
    }

    function getAwigenId() {
        return $this->respondent['awigenid'];
    }

    function saveChanges() {

        global $db;

        $errorMessage = array();

        $query = 'UPDATE ' . Config::dbSurvey() . '_respondents SET ';
        $query .= 'logincode = AES_ENCRYPT(\'' . prepareDatabaseString($this->getLoginCode()) . '\', \'' . Config::loginCodeKey() . '\'), ';

        $query .= 'firstname = AES_ENCRYPT(\'' . prepareDatabaseString($this->getFirstName()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'lastname = AES_ENCRYPT(\'' . prepareDatabaseString($this->getLastName()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'address1 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getAddress1()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'address2 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getAddress2()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'city = AES_ENCRYPT(\'' . prepareDatabaseString($this->getCity()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'puid = \'' . prepareDatabaseString($this->getPuid()) . '\', ';


        $query .= 'longitude = AES_ENCRYPT(\'' . prepareDatabaseString($this->getLongitude()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';
        $query .= 'latitude = AES_ENCRYPT(\'' . prepareDatabaseString($this->getLatitude()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';


        $query .= 'zip = AES_ENCRYPT(\'' . prepareDatabaseString($this->getZip()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'telephone1 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getTelephone1()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'telephone2 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getTelephone2()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'email = AES_ENCRYPT(\'' . prepareDatabaseString($this->getEmail()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';



        $query .= 'age = AES_ENCRYPT(\'' . prepareDatabaseString($this->getAge()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'sex = AES_ENCRYPT(\'' . prepareDatabaseString($this->getSex()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

        $query .= 'birthdate = AES_ENCRYPT(\'' . prepareDatabaseString($this->getBirthDate()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';



        $query .= 'schoolingyears = \'' . prepareDatabaseString($this->getYearsOfSchooling()) . '\', ';
        $query .= 'educationlevel = \'' . prepareDatabaseString($this->getEducationLevel()) . '\', ';
        $query .= 'occupationalstatus = \'' . prepareDatabaseString($this->getOccupationalStatus()) . '\', ';
        $query .= 'relationshiphh = \'' . prepareDatabaseString($this->getRelationshipHhHead()) . '\', ';
        $query .= 'spouseprimkey = \'' . prepareDatabaseString($this->getSpousePrimkey()) . '\', ';

        $query .= 'consenttype = \'' . prepareDatabaseString($this->getConsentType()) . '\', ';
        $query .= 'hhhead = \'' . prepareDatabaseString($this->getHhHead()) . '\', ';



        $query .= 'famr = \'' . prepareDatabaseString($this->getFamR()) . '\', ';
        $query .= 'finr = \'' . prepareDatabaseString($this->getFinR()) . '\', ';
        $query .= 'covr = \'' . prepareDatabaseString($this->getCovR()) . '\', ';

        if (isset($this->respondent['permanent'])) {
            $query .= 'permanent = \'' . prepareDatabaseString($this->getPermanent()) . '\', ';
        }
        if (isset($this->respondent['validation'])) {
            $query .= 'validation = \'' . prepareDatabaseString($this->getValidation()) . '\', ';
        }
        if (isset($this->respondent['movedout'])) {
            $query .= 'movedout = \'' . prepareDatabaseString($this->getMovedOut()) . '\', ';
        }

        if (isset($this->respondent['hhorder'])) {
            $query .= 'hhorder = \'' . prepareDatabaseString($this->getHhOrder()) . '\', ';
        }


        $query .= 'present = \'' . prepareDatabaseString($this->getPresent()) . '\', ';

        $query .= 'selected = \'' . prepareDatabaseString($this->getSelected()) . '\', ';

        if (dbConfig::defaultSeparateInterviewAddress()) {
            // begin custom personal networks
            $query .= 'original_firstname = AES_ENCRYPT(\'' . prepareDatabaseString($this->getOriginalFirstName()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

            $query .= 'original_lastname = AES_ENCRYPT(\'' . prepareDatabaseString($this->getOriginalLastName()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

            $query .= 'originalR = \'' . prepareDatabaseString($this->getOriginalR()) . '\', ';
            $query .= 'callbackOtherR = \'' . prepareDatabaseString($this->getCallbackOtherR()) . '\', ';

            $query .= 'interview_address1 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getInterviewAddress1()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';
            $query .= 'interview_address2 = AES_ENCRYPT(\'' . prepareDatabaseString($this->getInterviewAddress2()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';
            $query .= 'interview_zip = AES_ENCRYPT(\'' . prepareDatabaseString($this->getInterviewZip()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';
            $query .= 'interview_city = AES_ENCRYPT(\'' . prepareDatabaseString($this->getInterviewCity()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';
            $query .= 'interview_state = AES_ENCRYPT(\'' . prepareDatabaseString($this->getInterviewState()) . '\', \'' . Config::smsPersonalInfoKey() . '\'), ';

            $query .= 'interview_mode = \'' . prepareDatabaseString($this->getInterviewMode()) . '\', ';
            // end custom personal networks
        }
        $query .= 'status = ' . prepareDatabaseString($this->getStatus()) . ', ';
        $query .= 'urid = ' . prepareDatabaseString($this->getUrid()) . ' ';



        $query .= 'WHERE primkey = \'' . prepareDatabaseString($this->getPrimkey()) . '\'';

        $db->executeQuery($query);

        return $errorMessage;
    }

    function getHousehold() {

        return new Household($this->getHhid());
    }

    function needsValidation() {
        if ($this->getValidation() == 1) {
            return true;
        }
        return false;
    }

    /* CUSTOM PERSONAL NETWORKS */

    function getInterviewMode() {
        return $this->respondent['interview_mode'];
    }

    function setInterviewMode($mode) {
        $this->respondent['interview_mode'] = $mode;
    }

    function getOriginalR() {

        return $this->respondent['originalR'];
    }

    function setOriginalR($o) {

        $this->respondent['originalR'] = $o;
    }

    function getOriginalFirstname() {

        return $this->respondent['original_firstname_dec'];
    }

    function setOriginalFirstname($firstname) {

        $this->respondent['original_firstname_dec'] = $firstname;
    }

    function getOriginalLastname() {

        return $this->respondent['original_lastname_dec'];
    }

    function setOriginalLastname($lastname) {

        $this->respondent['original_lastname_dec'] = $lastname;
    }

    function getInterviewAddress1() {

        return $this->respondent['interview_address1_dec'];
    }

    function setInterviewAddress1($value) {

        $this->respondent['interview_address1_dec'] = $value;
    }

    function getInterviewAddress2() {

        return $this->respondent['interview_address2_dec'];
    }

    function setInterviewAddress2($value) {

        $this->respondent['interview_address2_dec'] = $value;
    }

    function getInterviewCity() {

        return $this->respondent['interview_city_dec'];
    }

    function setInterviewCity($value) {

        $this->respondent['interview_city_dec'] = $value;
    }

    function getInterviewZip() {

        return $this->respondent['interview_zip_dec'];
    }

    function setInterviewZip($value) {

        $this->respondent['interview_zip_dec'] = $value;
    }

    function getInterviewState() {

        return $this->respondent['interview_state_dec'];
    }

    function setInterviewState($value) {

        $this->respondent['interview_state_dec'] = $value;
    }

    function getCallbackOtherR() {
        return $this->respondent['callbackOtherR'];
    }

    function setCallbackOtherR($r) {
        $this->respondent['callbackOtherR'] = $r;
    }

    /* END CUSTOM PERSONAL NETWORKS */

    function getLastQuery() {
        $q = $this->lastQuery;
        $this->lastQuery = null; // reset
        return $q;
    }

    function hasFinalCode() {
        $contacts = $this->contacts->getContacts($this->getPrimkey());
        foreach ($contacts as $contact) {
            if ($contact->isFinalCode()) {
                return true;
            }
        }
        return false;
    }

    function getDataByField($field) {
        if (isset($this->respondent[$field])) {
            return $this->respondent[$field];
        }
        return '';
    }

    function memberMovedOut() {
        return $this->getMovedOut() == 1;
    }

    function memberDied() {
        return $this->getMovedOut() == 2;
    }

    function isSuspect() {
        return false;
    }

    function getPreload($startArray = array()) { //imported into survey from sms
        $preload = $startArray;
        $user = new User($_SESSION['URID']);

        $preload['urid'] = $user->getUrid();
        $preload['hhid'] = $this->getHhid();
        $preload['hhorder'] = $this->getHhOrder();
        $preload['RConsentType'] = $this->getConsentType();

        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
            $preload['Village_Anon'] = $this->getHousehold()->getAddress1();
            $preload['DwellingId'] = $this->getHousehold()->getCity();
        } else {
            $preload['Village_Anon'] = $this->getAddress1();
            $preload['DwellingId'] = $this->getCity();
        }
        return $preload;
    }

    function hasPicture($fieldname) {
        global $db;
        $query = 'select *, AES_DECRYPT(picture, \'' . Config::filePictureKey() . '\') as picture1 from ' . Config::dbSurveyData() . '_pictures where primkey=\'' . $this->getPrimkey() . '\' and variablename = \'' . $fieldname . '\'';
        $result = $db->selectQuery($query);
        if ($result != null) {
            $row = $db->getRow($result);
            if ($row['picture'] != null) {
                return true;
            }
        }
        return false;
    }

}

?>