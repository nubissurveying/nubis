<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Surveys {

    private $surveycnt;
    private $usersurveys;
    private $identifiers;

    function __construct() {
        
    }

    function getSurveys($all = true) {
        global $db;
        if (isset($this->usersurveys) && sizeof($this->usersurveys) > 0) {
            return $this->usersurveys;
        }
        $this->usersurveys = array();
        $this->identifiers = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_surveys');
        if ($result && $db->getNumberOfRows($result) > 0) {
            if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
                $user = new User($_SESSION['URID']);
                $avsurveys = $user->getSurveysAccess();
                while ($row = $db->getRow($result)) {
                    if (inArray($row["suid"], $avsurveys) || $all) {
                        $this->usersurveys[] = new Survey($row);
                        $this->identifiers[] = $row["suid"];
                    }
                }
            } else {
                while ($row = $db->getRow($result)) {
                    $this->usersurveys[] = new Survey($row);
                    $this->identifiers[] = $row["suid"];
                }
            }
        }
        return $this->usersurveys;
    }

    function getNumberOfSurveys($all = false) {

        if (isset($this->surveycnt) && $this->surveycnt > 0) {
            return $this->surveycnt;
        }
        global $db;
        $surveys = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_surveys');
        $this->surveycnt = 0;
        if ($result && $db->getNumberOfRows($result) > 0) {
            if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
                $user = new User($_SESSION['URID']);
                $avsurveys = $user->getSurveysAccess();
                while ($row = $db->getRow($result)) {
                    if (inArray($row["suid"], $avsurveys) || $all) {
                        $this->surveycnt++;
                    }
                }
            } else {
                while ($row = $db->getRow($result)) {
                    $this->surveycnt++;
                }
            }
        }
        return $this->surveycnt;
    }

    function getSurveyIdentifiers() {
        global $db;
        if (isset($this->identifiers) && sizeof($this->identifiers) > 0) {
            return $this->identifiers;
        }
        $this->identifiers = array();
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys');
        while ($row = $db->getRow($result)) {
            $this->identifiers[] = $row["suid"];
        }
        return $this->identifiers;
    }

    function getMaximumSuid() {
        global $db;
        $surveys = array();
        $result = $db->selectQuery('select max(suid) as max from ' . Config::dbSurvey() . '_surveys');
        if ($result) {
            $row = $db->getRow($result);
            return $row["max"];
        }
    }

    function getFirstSurvey($all = false) {
        global $db;
        $surveys = array();
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys order by suid asc');
        if ($result && $db->getNumberOfRows($result) > 0) {
            if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
                $user = new User($_SESSION['URID']);                
                $avsurveys = $user->getSurveysAccess();
                while ($row = $db->getRow($result)) {
                    if (inArray($row["suid"], $avsurveys) || $all) {
                        return $row["suid"];
                    }
                }
            } else {
                $row = $db->getRow($result);
                return $row["suid"];
            }
        }
        return "";
    }
    
    /* get survey identifier by type of survey */
    function getNurseLabSurvey() {
        global $db;
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys where nurselab=1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            return $row["suid"];
        }
        return "";
    }
    
    function setNurseLabSurvey($suid) {
        global $db;
        if ($suid == "") {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nurselab=0');
        }
        else {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nurselab=1 where suid=' . prepareDatabaseString($suid));
        }
    }
    
    function getNurseVisionSurvey() {
        global $db;
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys where nursevision=1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            return $row["suid"];
        }
        return "";
    }
    
    function setNurseVisionSurvey($suid) {
        global $db;
        if ($suid == "") {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nursevision=0');
        }
        else {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nursevision=1 where suid=' . prepareDatabaseString($suid));
        }
    }

    function getNurseAntropometricsSurvey() {
        global $db;
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys where nurseantropometrics=1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            return $row["suid"];
        }
        return "";
    }
    
    function setNurseAntropometricsSurvey($suid) {
        global $db;
        if ($suid == "") {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nurseantropometrics=0');
        }
        else {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nurseantropometrics=1 where suid=' . prepareDatabaseString($suid));
        }
    }

    function getNurseFollowUpSurvey() {
        global $db;
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys where nursefollowup=1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            return $row["suid"];
        }
        return "";
    }
    
    function setNurseFollowUpSurvey($suid) {
        global $db;
        if ($suid == "") {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nursefollowup=0');
        }
        else {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nursefollowup=1 where suid=' . prepareDatabaseString($suid));
        }
    }
    
    function getNurseDataSheetSurvey() {
        global $db;
        $result = $db->selectQuery('select suid from ' . Config::dbSurvey() . '_surveys where nursedatasheet=1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            return $row["suid"];
        }
        return "";
    }
    
    function setNurseDataSheetSurvey($suid) {
        global $db;
        if ($suid == "") {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nursedatasheet=0');
        }
        else {
            $result = $db->executeQuery('update ' . Config::dbSurvey() . '_surveys set nursedatasheet=1 where suid=' . prepareDatabaseString($suid));
        }
    }
}

?>