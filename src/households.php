<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Households {

    static function getDeIdentified() {
        $query = 'aes_decrypt(name, "' . Config::smsPersonalInfoKey() . '") as name_dec, ';
        $query .= 'aes_decrypt(address1, "' . Config::smsPersonalInfoKey() . '") as address1_dec, ';
        $query .= 'aes_decrypt(address2, "' . Config::smsPersonalInfoKey() . '") as address2_dec, ';
        $query .= 'aes_decrypt(zip, "' . Config::smsPersonalInfoKey() . '") as zip_dec, ';
        $query .= 'aes_decrypt(city, "' . Config::smsPersonalInfoKey() . '") as city_dec, ';
        $query .= 'aes_decrypt(state, "' . Config::smsPersonalInfoKey() . '") as state_dec, ';
        $query .= 'aes_decrypt(longitude, "' . Config::smsPersonalInfoKey() . '") as longitude_dec, ';
        $query .= 'aes_decrypt(latitude, "' . Config::smsPersonalInfoKey() . '") as latitude_dec, ';
        $query .= 'aes_decrypt(email, "' . Config::smsPersonalInfoKey() . '") as email_dec, ';
        $query .= 'aes_decrypt(telephone1, "' . Config::smsPersonalInfoKey() . '") as telephone1_dec, ';
        $query .= 'aes_decrypt(telephone2, "' . Config::smsPersonalInfoKey() . '") as telephone2_dec ';
        return $query;
    }

    static function getShortDeIdentified() {

        $query = 'aes_decrypt(name, "' . Config::smsPersonalInfoKey() . '") as name, ';
        $query .= 'aes_decrypt(address1, "' . Config::smsPersonalInfoKey() . '") as address1, ';
        $query .= 'aes_decrypt(address2, "' . Config::smsPersonalInfoKey() . '") as address2, ';
        $query .= 'aes_decrypt(zip, "' . Config::smsPersonalInfoKey() . '") as zip, ';
        $query .= 'aes_decrypt(city, "' . Config::smsPersonalInfoKey() . '") as city, ';
        $query .= 'aes_decrypt(state, "' . Config::smsPersonalInfoKey() . '") as state, ';
        $query .= 'aes_decrypt(longitude, "' . Config::smsPersonalInfoKey() . '") as longitude, ';
        $query .= 'aes_decrypt(latitude, "' . Config::smsPersonalInfoKey() . '") as latitude, ';
        $query .= 'aes_decrypt(email, "' . Config::smsPersonalInfoKey() . '") as email, ';
        $query .= 'aes_decrypt(telephone1, "' . Config::smsPersonalInfoKey() . '") as telephone1, ';
        $query .= 'aes_decrypt(telephone2, "' . Config::smsPersonalInfoKey() . '") as telephone2 ';
        return $query;
    }

    function getHouseholdsByUser(User $user, $filter = 0) {

        //do something with the filter!

        $currentUser = new User($_SESSION['URID']);

        // this can be the supervisor looking

        global $db;

        $households = array();

        $test = ' and test = 0';

        if ($currentUser->isTestMode()) {

            $test = ' and test = 1';
        }

        if ($currentUser->getRegionFilter() > 0 && $currentUser->getPuid() > 0) { //only certain region
            $test = ' and puid = ' . $currentUser->getPuid();
        }

        //urid!!!  is this a supervisor?

        if ($user->getUserType() == USER_INTERVIEWER) { //only add if interviewer!
            $uridStr = 'urid = ' . prepareDatabaseString($user->getUrid());
        } else {

            $users = new Users();

            $uridStr = $users->getUsersBySupervisor($user->getUrid());

            $urids = array();

            foreach ($uridStr as $urid) {

                $urids[] = prepareDatabaseString($urid->getUrid());
            }

            $uridStr = 'urid = ' . implode(' or urid = ', $urids);
        }

        $query = 'select *, ' . $this->getDeIdentified() . ' from ' . Config::dbSurvey() . '_households where (' . $uridStr . ') ' . $test . ' ORDER by primkey';
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $households[] = new Household($row);
        }



        if ($currentUser->getTestMode() && sizeof($households) == 0 && $currentUser->getRegionFilter() <= 0) { //psu filter
            if ($currentUser->getUserType() == USER_INTERVIEWER) { //only add if interviewer!
                $households = $this->addTestHouseholds($user);
            }
        }

        if ($filter > 0) //a filter!!
            $households = $this->filterHouseholds($households, $filter);

        if ($user->getFilter() > 1) //a filter!!
            $households = $this->filterHouseholdsByUserFilter($households, $user->getFilter());

        return $households;
    }

    function filterHouseholdsByUserFilter($households, $filter) {



        foreach ($households as $key => $household) {

            switch ($filter) {

                case 2:

                    if ($household->isCompleted())
                        unset($households[$key]);

                    break;



                case 3:  //completed & final refusal

                    if ($household->isCompleted()) //keep
                        unset($households[$key]);

                    if ($household->hasFinalCode()) //keep
                        unset($households[$key]);

                    break;
            }
        }

        return $households;
    }

    function filterHouseholds($households, $filter) {

        foreach ($households as $key => $household) {

            switch ($filter) {

                case 1:

                    if (!$household->isCompleted())
                        unset($households[$key]);

                    break;



                case 2:  //resist

                    if (!$household->isHHOrRespondentRefusal()) //keep
                        unset($households[$key]);

                    break;



                case 3: //non sample

                    if (!$household->isNonSample()) //keep
                        unset($households[$key]);

                    break;



                case 4: //incomplete

                    if ($household->isCompleted())
                        unset($households[$key]);

                    break;



                case 5: //validation

                    if (!$household->needsValidation()) //if not requires validation, remove
                        unset($households[$key]);

                    break;



                case 6: //moved out

                    if (!$household->memberMovedOut())
                        unset($households[$key]);

                    break;



                case 7: //suspect

                    if (!$household->isSuspect())
                        unset($households[$key]);

                    break;
            }
        }

        return $households;
    }

    function addTestHouseholds($user) {

        $respondents = new Respondents();

        $households = array();

        //4 'normal' respondents

        for ($i = 1; $i <= 4; $i++) {

            $hhid = '999' . leadingZeros($user->getUrid(), 4) . $i . '00';

            $households[] = $this->insertH($hhid, 'hh name ' . $i, $user);

            $respondents->addTestRespondents($user, $hhid, 2);
        }

        return $households;
    }

    function insertH($primkey, $name, $user, $test = 1) {

        global $db;

        $urid = -1;

        if ($user != null) {

            $urid = $user->getUrid();
        }

        $query = 'replace into ' . Config::dbSurvey() . '_households (primkey, urid, name, test) values ("' . $primkey . '", ' . prepareDatabaseString($urid) . ', aes_encrypt("' . $name . '", "' . Config::smsPersonalInfoKey() . '"), ' . $test . ')';

        $result = $db->selectQuery($query);

        //add respondents

        return new Household($primkey);
    }

    function deleteTestHouseholds($user) {

        if ($user->isTestMode()) { //only in test mode!!
            $households = $this->getHouseholdsByUser($user);

            $respondents = new Respondents();



            foreach ($households as $household) {

                $respondents->removeRespondentFromTable($household->getPrimKey(), '_data');

                $respondents->removeRespondentFromTable($household->getPrimKey(), '_states');

                $respondents->removeRespondentFromTable($household->getPrimKey(), '_actions');

                $respondents->removeRespondentFromTable($household->getPrimKey(), '_screendumps');

                $respondents->removeRespondentFromTable($household->getPrimKey(), '_datarecords');

                $respondents->removeRespondentFromTable($household->getPrimKey(), '_times');



                $this->removeHouseholdFromTable($household->getHhid(), '_households');

                $respondents->removeRespondentFromTable($household->getHhid(), '_remarks');

                $respondents->removeRespondentFromTable($household->getHhid(), '_contacts');
            }

            //delete respondents

            $respondents = new Respondents();

            $respondents->deleteTestRespondents($user);
        }
    }

    function removeHouseholdFromTable($hhid, $table) {

        global $db;

        $query = 'delete from ' . Config::dbSurvey() . $table . ' where primkey = "' . prepareDatabaseString($hhid) . '"';

        $result = $db->selectQuery($query);
    }

    function getUnassigned($puid = -1) {
        global $db;
        $households = array();
        $query = $this->getUnassignedAsQuery($puid);
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $households[] = new Household($row);
        }

        return $households;
    }

    function getUnassignedAsQuery($puid = -1, $cleanQuery = false) {

        $psuStr = '';
        if ($puid > 0) {
            $psuStr = ' and puid = "' . $puid . '"';
        }

        $uridStr = ' urid <= 0 ';
        $currentUser = new User($_SESSION['URID']);
        if ($currentUser->getUserType() == USER_SUPERVISOR) {
            $uridStr = ' urid = ' . $currentUser->getUrid();
        }

        if ($cleanQuery) {
            $query = 'select primkey, ' . $this->getShortDeIdentified() . ' from ' . Config::dbSurvey() . '_households where ' . $uridStr . ' ' . $psuStr . ' order by primkey asc';
        } else {
            $query = 'select *, ' . $this->getDeIdentified() . ' from ' . Config::dbSurvey() . '_households where ' . $uridStr . ' ' . $psuStr . ' order by city_dec, address1_dec';
        }

        return $query;
    }

    function getHouseholdsSearch($user, $searchterm) {
        return $this->getRespondentsByUserSearch($user, $searchterm, false);
    }

    function getHouseholdsByUserSearch($user, $searchterm, $uridcheck = true) {

        global $db;

        $respondents = array();

        $test = 'test = 0';

        if ($user->isTestMode()) {

            $test = 'test = 1';
        }

        if ($uridcheck) {

            if ($user->getUserType() == USER_INTERVIEWER) { //only add if interviewer!
                $uridStr = 't1.urid = ' . prepareDatabaseString($user->getUrid());
            } else {

                $users = new Users();
                $uridStr = $users->getUsersBySupervisor($user->getUrid());
                $urids = array();
                foreach ($uridStr as $urid) {
                    $urids[] = prepareDatabaseString($urid->getUrid());
                }
                $uridStr = 't1.urid = ' . implode(' or t1.urid = ', $urids);
            }
            $test .= ' AND ( ' . $uridStr . ') ';
        }

        //search through respondent table
        $this->searchHouseholdAndRespondent($respondents, $searchterm, $test, 1); //respondents
        $this->searchHouseholdAndRespondent($respondents, $searchterm, $test, 2); //households

        //search through remarks
        $this->searchRemarks($respondents, $searchterm, $test, 1); //respondents
        $this->searchRemarks($respondents, $searchterm, $test, 2); //households
        
        //search through contacts
        $this->searchContacts($respondents, $searchterm, $test, 1); //respondents
        $this->searchContacts($respondents, $searchterm, $test, 2); //households

        return $respondents;
    }

    function searchRemarks(&$respondents, $searchterm, $uridStr, $type = 1) {

        global $db;
        $table = 'respondents';
        if ($type == 2) {
            $table = 'households';
        }

        $query = 'select * from ' . Config::dbSurvey() . '_remarks as t1

              left join ' . Config::dbSurvey() . '_' . $table . '  as t2

              on t1.primkey = t2.primkey

            where t2.' . $uridStr . ' and (

              UPPER(CAST(aes_decrypt(remark, "' . Config::smsRemarkKey() . '")AS CHAR)) like "%' . prepareDatabaseString(strtoupper($searchterm)) . '%" 

            )';

        $result = $db->selectQuery($query);

        while ($row = $db->getRow($result)) {

            if ($type == 1) {

                $respondents[$row['hhid']] = new Household($row['hhid']);
            } else {

                $respondents[$row['primkey']] = new Household($row['primkey']);
            }
        }
    }

    function searchContacts(&$respondents, $searchterm, $uridStr, $type = 1) {

        global $db;

        $table = 'respondents';

        if ($type == 2) {

            $table = 'households';
        }



        $query = 'select * from ' . Config::dbSurvey() . '_contacts as t1

              left join ' . Config::dbSurvey() . '_' . $table . '  as t2

              on t1.primkey = t2.primkey

            where t2.' . $uridStr . ' and (

              UPPER(CAST(aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '")AS CHAR)) like "%' . prepareDatabaseString(strtoupper($searchterm)) . '%" 

            )';





        $result = $db->selectQuery($query);

        while ($row = $db->getRow($result)) {

            if ($type == 1) {

                $respondents[$row['hhid']] = new Household($row['hhid']);
            } else {

                $respondents[$row['primkey']] = new Household($row['primkey']);
            }
        }
    }

    function searchHouseholdAndRespondent(&$respondents, $searchterm, $uridStr, $type = 1) {

        global $db;

        $table = 'respondents';

        $searchon = '

              UPPER(CAST(aes_decrypt(firstname, "' . Config::smsPersonalInfoKey() . '")AS CHAR)) like "%' . prepareDatabaseString(strtoupper($searchterm)) . '%" or

              UPPER(CAST(aes_decrypt(lastname, "' . Config::smsPersonalInfoKey() . '")AS CHAR)) like "%' . prepareDatabaseString(strtoupper($searchterm)) . '%"';

        if ($type == 2) {

            $table = 'households';

            $searchon = '

              UPPER(CAST(aes_decrypt(name, "' . Config::smsPersonalInfoKey() . '")AS CHAR)) like "%' . prepareDatabaseString(strtoupper($searchterm)) . '%" or

              UPPER(CAST(aes_decrypt(address1, "' . Config::smsPersonalInfoKey() . '")AS CHAR)) like "%' . prepareDatabaseString(strtoupper($searchterm)) . '%"';
        }



        $query = 'select * from ' . Config::dbSurvey() . '_' . $table . ' as t1

            where ' . $uridStr . ' and (

              t1.primkey like "%' . prepareDatabaseString($searchterm) . '%" or ' . $searchon . ')';

        $result = $db->selectQuery($query);

        while ($row = $db->getRow($result)) {

            if ($type == 1) {

                $respondents[$row['hhid']] = new Household($row['hhid']);
            } else {

                $respondents[$row['primkey']] = new Household($row['primkey']);
            }
        }
    }

}

?>