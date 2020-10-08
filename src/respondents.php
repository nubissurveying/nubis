<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Respondents {

    static function getDeIdentified() {

        $query = 'aes_decrypt(firstname, \'' . Config::smsPersonalInfoKey() . '\') as firstname_dec, ';
        $query .= 'aes_decrypt(lastname, \'' . Config::smsPersonalInfoKey() . '\') as lastname_dec, ';
        $query .= 'aes_decrypt(age, \'' . Config::smsPersonalInfoKey() . '\') as age_dec, ';
        $query .= 'aes_decrypt(sex, \'' . Config::smsPersonalInfoKey() . '\') as sex_dec, ';
        $query .= 'aes_decrypt(birthdate, \'' . Config::smsPersonalInfoKey() . '\') as birthdate_dec, ';
        $query .= 'aes_decrypt(address1, \'' . Config::smsPersonalInfoKey() . '\') as address1_dec, ';
        $query .= 'aes_decrypt(address2, \'' . Config::smsPersonalInfoKey() . '\') as address2_dec, ';
        $query .= 'aes_decrypt(zip, \'' . Config::smsPersonalInfoKey() . '\') as zip_dec, ';
        $query .= 'aes_decrypt(city, \'' . Config::smsPersonalInfoKey() . '\') as city_dec, ';
        $query .= 'aes_decrypt(longitude, \'' . Config::smsPersonalInfoKey() . '\') as longitude_dec, ';
        $query .= 'aes_decrypt(latitude, \'' . Config::smsPersonalInfoKey() . '\') as latitude_dec, ';
        $query .= 'aes_decrypt(email, \'' . Config::smsPersonalInfoKey() . '\') as email_dec, ';
        $query .= 'aes_decrypt(telephone1, \'' . Config::smsPersonalInfoKey() . '\') as telephone1_dec, ';
        $query .= 'aes_decrypt(telephone2, \'' . Config::smsPersonalInfoKey() . '\') as telephone2_dec, ';
        $query .= 'aes_decrypt(logincode, \'' . Config::loginCodeKey() . '\') as logincode_dec ';
        if (dbConfig::defaultSeparateInterviewAddress()) {
            $query .= Respondents::getExtraDeidentified();
        }
        return $query;
    }

    static function getExtraDeidentified() {
        $extra = ', aes_decrypt(original_firstname, \'' . Config::smsPersonalInfoKey() . '\') as original_firstname_dec ';
        $extra .= ', aes_decrypt(original_lastname, \'' . Config::smsPersonalInfoKey() . '\') as original_lastname_dec ';
        $extra .= ', aes_decrypt(original_telephone1, \'' . Config::smsPersonalInfoKey() . '\') as original_telephone1_dec ';
        $extra .= ', aes_decrypt(interview_address1, \'' . Config::smsPersonalInfoKey() . '\') as interview_address1_dec ';
        $extra .= ', aes_decrypt(interview_address2, \'' . Config::smsPersonalInfoKey() . '\') as interview_address2_dec ';
        $extra .= ', aes_decrypt(interview_zip, \'' . Config::smsPersonalInfoKey() . '\') as interview_zip_dec ';
        $extra .= ', aes_decrypt(interview_city, \'' . Config::smsPersonalInfoKey() . '\') as interview_city_dec ';
        $extra .= ', aes_decrypt(interview_state, \'' . Config::smsPersonalInfoKey() . '\') as interview_state_dec ';
        return $extra;
    }

    function Respondents() {
        
    }

    function getRespondentsForFollowup() {
        global $db;
        $respondents = array();
        $query = 'select primkey from ' . Config::dbSurvey() . '_contacts as t1 left join ' . Config::dbSurvey() . '_respondents as t2 on t1.primkey = t2.primkey where t1.code = 500 and t2.selected = 1 and t1.primkey not like "999%" and t1.ts < DATE_SUB(now(), INTERVAL 6 MONTH) LIMIT 0, 300';
// urid = "' . prepareDatabaseString($urid) . '"';
//      echo '<br/><br/><br/>' . $query;
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
//echo $row[8];
//echo '-';
//print_r($row);
            $respondents[] = new Respondent($row['primkey']);
        }
        return $respondents;
    }

    function getRespondentsByUser(User $user, $filter = 0) {
        global $db;
        $respondents = array();
        $test = ' and test = 0';
        // this can be the supervisor looking
        $currentUser = new User($_SESSION['URID']);
        if ($currentUser->isTestMode()) {
            $test = ' and test = 1';
        }
        if ($currentUser->getRegionFilter() > 0 && $currentUser->getPuid() > 0) { //only certain region
            $test = ' and puid = ' . $currentUser->getPuid();
        }
        $result = $db->selectQuery('select *, ' . $this->getDeIdentified() . ' from ' . Config::dbSurvey() . '_respondents where urid = ' . prepareDatabaseString($user->getUrid()) . $test);
        while ($row = $db->getRow($result)) {
            $respondents[] = new Respondent($row);
        }
        if ($currentUser->getTestMode() && sizeof($respondents) == 0 && $currentUser->getRegionFilter() <= 0) { //psu filter!!
            if ($currentUser->getUserType() == USER_INTERVIEWER) { //only add if interviewer!
                if (dbConfig::defaultPanel() != PANEL_HOUSEHOLD) { //only if not household sample
                    $respondents = $this->addTestRespondents($user);
                }
            }
        }
        if ($filter > 0) //a filter!!
            $respondents = $this->filterRespondents($respondents, $filter);
        return $respondents;
    }

    function filterRespondents($respondents, $filter) {

        foreach ($respondents as $key => $respondent) {

            switch ($filter) {

                case 1:

                    if (!$respondent->isCompleted())
                        unset($respondents[$key]);

                    break;



                case 2:  //resist

                    if (!$respondent->isRefusal()) //keep
                        unset($respondents[$key]);

                    break;



                case 3: //non sample

                    if (!$respondent->isNonSample()) //keep
                        unset($respondents[$key]);

                    break;



                case 4: //incomplete

                    if ($respondent->isCompleted())
                        unset($respondents[$key]);

                    break;

                case 5: //validation
                    if (!$respondent->needsValidation()) //if not requires validation, remove
                        unset($respondents[$key]);
                    break;
            }
        }

        return $respondents;
    }

    function deleteTestRespondents($user) {

        if ($user->isTestMode()) { //only in test mode!!
            $respondents = $this->getRespondentsByUser($user);


            foreach ($respondents as $respondent) {
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_data');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_states');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_actions');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_screendumps');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_datarecords');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_times');
//haalsi only
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_pictures');

                $this->removeRespondentFromTable($respondent->getPrimKey(), '_respondents');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_remarks');
                $this->removeRespondentFromTable($respondent->getPrimKey(), '_contacts');
            }
        }
    }

    function removeRespondentFromTable($primkey, $table) {

        global $db;
        $query = 'delete from ' . Config::dbSurvey() . $table . ' where primkey = \'' . prepareDatabaseString($primkey) . '\'';
        $result = $db->selectQuery($query);
    }

    function addTestRespondents($user, $hhid = '', $num = 4, $present = 0) {
        $respondents = array();
        //4 'normal' respondents
        for ($i = 1; $i <= $num; $i++) {
            if ($hhid == '') { //respondent sample
                $primkey = '999' . leadingZeros($user->getUrid(), 4) . leadingZeros($i, 1);
            } else { //household sample
                $primkey = $hhid . leadingZeros($i, 2);
            }
            //SET TESTING NAME FOR R
            $respondentname = 'name ' . $i;
            $names = Language::labelTestRespondents();
            if (isset($names[$i])) {
                $respondentname = $names[$i];
            }
            //END SET TESTING NAME
            $respondents[] = $this->insertR($primkey, $respondentname, $user, rand(1, 2), rand(65, 80), $hhid, 1, $present, $i);
        }
        return $respondents;
    }

    function insertR($primkey, $firstname, $user, $sex, $age, $hhid = '', $selected = 1, $present = 0, $permanent = 1, $hhorder = 0, $test = 1) {
        global $db;
        $urid = -1;
        if ($user != null) {
            $urid = $user->getUrid();
        }
        $query = 'replace into ' . Config::dbSurvey() . '_respondents (primkey, firstname, urid, test, hhid, sex, age, selected, present, permanent, hhorder) values (\'' . $primkey . '\', aes_encrypt(\'' . $firstname . '\', \'' . Config::smsPersonalInfoKey() . '\'), ' . prepareDatabaseString($urid) . ', ' . $test . ', \'' . $hhid . '\', aes_encrypt(\'' . $sex . '\', \'' . Config::smsPersonalInfoKey() . '\'), aes_encrypt(\'' . $age . '\', \'' . Config::smsPersonalInfoKey() . '\'), ' . $selected . ', ' . $present . ', ' . $permanent . ', ' . $hhorder . ')';
        //echo '<br/><br/><Br/>' . $query . '<hr>';
        $result = $db->selectQuery($query);
        return new Respondent($primkey);
    }

    function getRespondentsSearch($user, $searchterm) {
        return $this->getRespondentsByUserSearch($user, $searchterm, false);
    }

    function getRespondentsByBarcode($user, $searchterm) {
        global $db;
        $respondents = array();

        $query = 'select primkey from ' . Config::dbSurveyData() . '_lab where 
              aes_decrypt(barcode, \'' . Config::filePictureKey() . '\') = \'' . prepareDatabaseString($searchterm) . '\' or
              aes_decrypt(labbarcode, \'' . Config::filePictureKey() . '\') = \'' . prepareDatabaseString($searchterm) . '\'';
//echo $query;
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            if ($row['primkey'] != '') {
                $respondents[] = new Respondent($row['primkey']);
            }
        }
        if (sizeof($respondents) == 0) { //nothing found yet
            global $survey;
            $query = 'select primkey from ' . Config::dbSurveyData() . '_data where variablename="bs021" and cast(aes_decrypt(answer, \'' . $survey->getDataEncryptionKey() . '\') as char) = \'' . prepareDatabaseString($searchterm) . '\'';
            $result = $db->selectQuery($query);
            if ($result != null && $db->getNumberOfRows($result) > 0) {
                $row = $db->getRow($result);
                $respondents[] = new Respondent($row['primkey']);
            }
        }


        return $respondents;
    }

    function getRespondentsByUserSearch($user, $searchterm, $uridcheck = true) {
        global $db;
        $respondents = array();
        $test = 'test = 0';
        if ($user->isTestMode()) {
            $test = 'test = 1';
        }
        //$userstr = ' 1 = 1 ';
        if ($uridcheck) {
            $test .= ' AND t1.urid = ' . prepareDatabaseString($user->getUrid());
        }
        //search through respondent table
        $query = 'select *, aes_decrypt(firstname, \'' . Config::smsPersonalInfoKey() . '\') as firstname_dec, 
            aes_decrypt(lastname, \'' . Config::smsPersonalInfoKey() . '\') as lastname_dec 
            from ' . Config::dbSurvey() . '_respondents as t1
            where ' . $test . ' and (
              t1.primkey like "%' . prepareDatabaseString($searchterm) . '%" or
              aes_decrypt(firstname, \'' . Config::smsPersonalInfoKey() . '\') like "%' . prepareDatabaseString($searchterm) . '%" or
              aes_decrypt(lastname, \'' . Config::smsPersonalInfoKey() . '\') like "%' . prepareDatabaseString($searchterm) . '%"
            )';

        $result = $db->selectQuery($query);

        while ($row = $db->getRow($result)) {
            $respondents[$row['primkey']] = new Respondent($row);
        }

        //search through remarks

        $query = 'select *, aes_decrypt(firstname, \'' . Config::smsPersonalInfoKey() . '\') as firstname_dec, 

            aes_decrypt(lastname, \'' . Config::smsPersonalInfoKey() . '\') as lastname_dec 



            from ' . Config::dbSurvey() . '_remarks as t1

              left join ' . Config::dbSurvey() . '_respondents as t2

              on t1.primkey = t2.primkey

            where t2.' . $test . ' and (

              aes_decrypt(remark, \'' . Config::smsRemarkKey() . '\') like "%' . prepareDatabaseString($searchterm) . '%" 

            )';

        // echo '<br/><br/><br/>' . $query;

        $result = $db->selectQuery($query);

        while ($row = $db->getRow($result)) {

            $respondents[$row['primkey']] = new Respondent($row);
        }

        return $respondents;
    }

    function getRespondentByLoginCode($logincode) {
        global $db;
        $query = 'select *, ' . $this->getDeIdentified() . ' from ' . Config::dbSurvey() . '_respondents where aes_decrypt(logincode, \'' . Config::loginCodeKey() . '\') = \'' . prepareDatabaseString($logincode) . '\'';
//echo $query;
        if ($result = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($result) > 0) {
                $row = $db->getRow($result);
                return new Respondent($row);
            }
        }
        return null;
    }

    function getUnassigned($psu = -1) {
        
    }

    function getRespondentsByUrid($urid) {
        global $db;
        $respondents = array();
        $query = 'select primkey from ' . Config::dbSurvey() . '_lab where urid = \'' . prepareDatabaseString($urid) . '\'';
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $respondents[] = new Respondent($row['primkey']);
        }

        return $respondents;
    }

}

?>