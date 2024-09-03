<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Household {

    private $respondents = null;
    var $household;
    var $remarks;
    var $contacts;
    var $logactions;
    var $lastQuery = '';

    function __construct($rowOrHhid = "") {

        global $db;

        $this->remarks = new Remarks();

        $this->contacts = new Contacts();

        $this->logactions = new LogActions();

        if (is_array($rowOrHhid)) {

            $this->household = $rowOrHhid;
        } else {

            $result = $db->selectQuery('select *, ' . Households::getDeIdentified() . ' from ' . Config::dbSurvey() . '_households where primkey = "' . prepareDatabaseString($rowOrHhid) . '"');

            $this->household = $db->getRow($result);
        }
    }

    function getHhid() {

        return $this->household['primkey'];
    }

    function getPrimkey() {

        return $this->getHhid();
    }

    function getName() {

        return $this->household['name_dec'];
    }

    function getTest() {

        return $this->household['test'];
    }

    function setName($name, $setQuery = false) {

        if ($this->household['name_dec'] != $name) { //only set when different
            $this->household['name_dec'] = $name;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'name = AES_ENCRYPT("' . prepareDatabaseString($this->getName()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getAddress1() {

        return $this->household['address1_dec'];
    }

    function getDataByField($field) {

        if (isset($this->household[$field])) {

            return $this->household[$field];
        }

        return '';
    }

    function setAddress1($address, $setQuery = false) {

        if ($this->household['address1_dec'] != $address) { //only set when different
            $this->household['address1_dec'] = $address;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'adress1 = AES_ENCRYPT("' . prepareDatabaseString($this->getAddress1()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getAddress2() {

        return $this->household['address2_dec'];
    }

    function setAddress2($address, $setQuery = false) {

        if ($this->household['address2_dec'] != $address) { //only set when different
            $this->household['address2_dec'] = $address;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'adress2 = AES_ENCRYPT("' . prepareDatabaseString($this->getAddress2()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getZip() {

        return $this->household['zip_dec'];
    }

    function setZip($zip, $setQuery = false) {

        if ($this->household['zip_dec'] != $zip) { //only set when different
            $this->household['zip_dec'] = $zip;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'zip = AES_ENCRYPT("' . prepareDatabaseString($this->getZip()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getCity() {

        return $this->household['city_dec'];
    }

    function setCity($city, $setQuery = false) {

        if ($this->household['city_dec'] != $city) { //only set when different
            $this->household['city_dec'] = $city;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'city = AES_ENCRYPT("' . prepareDatabaseString($this->getCity()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }
    
    function getState() {

        return $this->household['state_dec'];
    }

    function setState($zip, $setQuery = false) {

        if ($this->household['state_dec'] != $zip) { //only set when different
            $this->household['state_dec'] = $zip;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'state = AES_ENCRYPT("' . prepareDatabaseString($this->getZip()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getLongitude() {

        return $this->household['longitude_dec'];
    }

    function setLongitude($longitude) {

        if ($this->household['longitude_dec'] != $longitude) { //only set when different
            $this->household['longitude_dec'] = $longitude;
        }
    }

    function getHhHead() {

        return $this->household['hhhead'];
    }

    function setHhHead($hhhead) {

        if ($this->household['hhhead'] != $hhhead) { //only set when different
            $this->household['hhhead'] = $hhhead;
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

        return $this->household['email_dec'];
    }

    function setEmail($email, $setQuery = false) {

        if ($this->household['email_dec'] != $email) { //only set when different
            $this->household['email_dec'] = $email;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'email = AES_ENCRYPT("' . prepareDatabaseString($this->getEmail()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getTelephone1() {

        return $this->household['telephone1_dec'];
    }

    function setTelephone1($telephone, $setQuery = false) {

        if ($this->household['telephone1_dec'] != $telephone) { //only set when different
            $this->household['telephone1_dec'] = $telephone;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'telephone1 = AES_ENCRYPT("' . prepareDatabaseString($this->getTelephone1()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getTelephone2() {

        return $this->household['telephone2_dec'];
    }

    function setTelephone2($telephone, $setQuery = false) {

        if ($this->household['telephone2_dec'] != $telephone) { //only set when different
            $this->household['telephone2_dec'] = $telephone;

            if ($setQuery) {

                $this->lastQuery = '';

                $user = new User($_SESSION['URID']);

                if ($user->getUserType() == USER_SUPERVISOR) {

                    $this->lastQuery = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

                    $this->lastQuery .= 'telephone2 = AES_ENCRYPT("' . prepareDatabaseString($this->getTelephone2()) . '", "' . Config::smsPersonalInfoKey() . '") ';

                    $this->lastQuery .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';
                }
            }
        }
    }

    function getStatus() {

        return $this->household['status'];
    }

    function setStatus($status) {

        $this->household['status'] = $status;
    }

    function getUrid() {

        return $this->household['urid'];
    }

    function setUrid($urid) {

        $this->household['urid'] = $urid;
    }

    function getPuid() {

        return $this->household['puid'];
    }

    function setPuid($puid) {

        $this->household['puid'] = $puid;
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

    function isHHOrRespondentRefusal() {

        //also check if all respondents are completed

        if ($this->isRefusal() == 2) {

            return true;
        } else {

            $respondents = $this->getSelectedRespondentsWithFinFamR();

            foreach ($respondents as $respondent) {

                if ($respondent->isRefusal()) {

                    return true;
                }
            }
        }

        return false;
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

    function hasFinalCode() {

        $contacts = $this->contacts->getContacts($this->getPrimkey());

        foreach ($contacts as $contact) {

            if ($contact->isFinalCode()) {

                return true;
            }
        }

        return false;
    }

    function isCompleted() {

        //also check if all respondents are completed

        if ($this->getStatus() == 2) {

            $respondents = $this->getSelectedRespondentsWithFinFamR();

            foreach ($respondents as $respondent) {

                if ($respondent->getStatus() != 2) {

                    return false;
                }
            }
        } else {

            return false;
        }

        return true;
    }

    function needsValidation() {

        //check all respondents

        $respondents = $this->getSelectedRespondentsWithFinFamR();

        foreach ($respondents as $respondent) {

            if ($respondent->needsValidation()) {

                return true;
            }
        }

        return false;
    }

    function memberMovedOut() {

        //check all respondents

        $respondents = $this->getOriginalSelectedRespondents(); //only look at members that were originally selected for the interview

        foreach ($respondents as $respondent) {

            if ($respondent->memberMovedOut()) {

                return true;
            }
        }

        return false;
    }

    function isSuspect() {

        //check all respondents

        $respondents = $this->getSelectedRespondentsWithFinFamR();

        foreach ($respondents as $respondent) {

            if ($respondent->isSuspect()) {

                return true;
            }
        }

        return false;
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

    function getInfo() {

        return '';

//    return $this->respondent['info_dec'];
    }

    function getContactPerson() {

        return '';

//    return $this->respondent['contactperson_dec'];
    }

    function saveChanges() {

        global $db;

        $errorMessage = array();

        $query = 'UPDATE ' . Config::dbSurvey() . '_households SET ';

        $query .= 'name = AES_ENCRYPT("' . prepareDatabaseString($this->getName()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'address1 = AES_ENCRYPT("' . prepareDatabaseString($this->getAddress1()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'address2 = AES_ENCRYPT("' . prepareDatabaseString($this->getAddress2()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'city = AES_ENCRYPT("' . prepareDatabaseString($this->getCity()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'puid = "' . prepareDatabaseString($this->getPuid()) . '", ';

        $query .= 'hhhead = "' . prepareDatabaseString($this->getHhHead()) . '", ';

        $query .= 'longitude = AES_ENCRYPT("' . prepareDatabaseString($this->getLongitude()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'latitude = AES_ENCRYPT("' . prepareDatabaseString($this->getLatitude()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'zip = AES_ENCRYPT("' . prepareDatabaseString($this->getZip()) . '", "' . Config::smsPersonalInfoKey() . '"), ';
        
        $query .= 'state = AES_ENCRYPT("' . prepareDatabaseString($this->getState()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'telephone1 = AES_ENCRYPT("' . prepareDatabaseString($this->getTelephone1()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'telephone2 = AES_ENCRYPT("' . prepareDatabaseString($this->getTelephone2()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'email = AES_ENCRYPT("' . prepareDatabaseString($this->getEmail()) . '", "' . Config::smsPersonalInfoKey() . '"), ';

        $query .= 'status = ' . prepareDatabaseString($this->getStatus()) . ', ';

        $query .= 'urid = ' . prepareDatabaseString($this->getUrid()) . ' ';

        $query .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';

        $db->executeQuery($query);

        return $errorMessage;
    }

    function getRespondents() {

        global $db;

        if ($this->respondents == null) {

            $this->respondents = array();

            $query = 'select *, ' . Respondents::getDeIdentified() . ' from ' . Config::dbSurvey() . '_respondents where hhid = "' . prepareDatabaseString($this->getHhid()) . '" order by hhorder';

            $result = $db->selectQuery($query);

            while ($row = $db->getRow($result)) {

                $this->respondents[] = new Respondent($row);
            }
        }

        return $this->respondents;
    }

    function getOriginalSelectedRespondents() {

        $respondents = $this->getRespondents();

        $selectedRespondents = array();

        foreach ($respondents as $respondent) {

            if ($respondent->isSelected()) {

                $selectedRespondents[] = $respondent;
            }
        }

        return $selectedRespondents;
    }

    function getSelectedRespondents() { //selected for survey
        $respondents = $this->getRespondents();

        $selectedRespondents = array();

        foreach ($respondents as $respondent) {

            if ($respondent->isSelected() && $respondent->isPresent()) {

                $selectedRespondents[] = $respondent;
            }
        }

        return $selectedRespondents;
    }

    function getSelectedRespondentsWithFinFamR() {

        $respondents = $this->getRespondents();

        $selectedRespondents = array();

        foreach ($respondents as $respondent) {

            if ($respondent->isPresent() && ($respondent->isSelected() || $respondent->isFamR() || $respondent->isFinR() )) {

                $selectedRespondents[] = $respondent;
            }
        }

        return $selectedRespondents;
    }

    function getLastQuery() {

        return $this->lastQuery;
    }

    function getPreload($startArray = array()) { //imported into survey from sms
        $preload = $startArray;

        $user = new User($_SESSION['URID']);



        $preload['urid'] = $user->getUrid();

        $preload['hhid'] = $this->getHhid();



        $preload['Village_Anon'] = $this->getAddress1();

        $preload['DwellingId'] = $this->getCity();

        return $preload;
    }

}

?>