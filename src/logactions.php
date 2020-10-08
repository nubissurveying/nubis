<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class LogActions {

    var $logActionsArray = array();

    function __construct() {
        
    }

    function addAction($primkey, $urid, $page, $systemtype = USCIC_SMS, $actiontype = 1) {

        global $db;
        $query = 'INSERT INTO ' . Config::dbSurveyData() . '_actions (primkey, sessionid, urid, suid, ipaddress, systemtype, action, actiontype, params, language, mode, version) VALUES (';
        if ($primkey != '') {
            $query .= '\'' . prepareDatabaseString($primkey) . '\', ';
        } else {
            $query .= 'NULL, ';
        }
        $query .= '\'' . session_id() . '\', ';
        if ($urid != '') {
            $query .= '\'' . $urid . '\', ';
        } else {
            $query .= 'NULL, ';
        }
        if ($systemtype == USCIC_SURVEY) {
            $query .= getSurvey() . ', ';
        } else {
            $query .= 'NULL, ';
        }

        $query .= '\'' . prepareDatabaseString(getClientIp()) . '\', ';
        $query .= $systemtype . ', ';
        $query .= '\'' . prepareDatabaseString($page) . '\', ';
        $query .= $actiontype . ', ';
        if (Config::logParams()) { //log post vars?
            $query .= ' AES_ENCRYPT(\'' . prepareDatabaseString(serialize($_POST)) . '\', \'' . Config::logActionParamsKey() . '\'), ';
        } else {
            $query .= ' NULL, ';
        }

        if ($systemtype == USCIC_SURVEY) {
            $query .= getSurveyLanguage() . ', ';
            $query .= getSurveyMode() . ', ';
            $query .= getSurveyVersion();
        } else {
            $query .= 'NULL, NULL, NULL';
        }
        $query .= ")";

        $db->executeQuery($query);
        if (isset($this->LogActions[$primkey])) { //unset so it is read in again..
            unset($this->LogActions[$primkey]);
        }
    }

    function addSurveyAction($primkey, $urid, $page, $systemtype = USCIC_SMS, $actiontype = 1, $externalonly = array()) {

        global $db;
        $query = 'INSERT INTO ' . Config::dbSurveyData() . '_actions (primkey, sessionid, urid, suid, ipaddress, systemtype, action, actiontype, params, language, mode, version) VALUES (';
        if ($primkey != '') {
            $query .= '\'' . prepareDatabaseString($primkey) . '\', ';
        } else {
            $query .= 'NULL, ';
        }
        $query .= '\'' . session_id() . '\', ';
        if ($urid != '') {
            $query .= '\'' . $urid . '\', ';
        } else {
            $query .= 'NULL, ';
        }
        if ($systemtype == USCIC_SURVEY) {
            $query .= getSurvey() . ', ';
        } else {
            $query .= 'NULL, ';
        }

        $query .= '\'' . prepareDatabaseString(getClientIp()) . '\', ';
        $query .= $systemtype . ', ';
        $query .= '\'' . prepareDatabaseString($page) . '\', ';
        $query .= $actiontype . ', ';
        if (Config::logParams()) { //log post vars?
            
            // exclude any $_POST data for external storage only variables
            foreach ($externalonly as $k => $v) {
                if (isset($_POST[$v])) {
                    echo 'unsetting ' . $v;
                    unset($_POST[$v]);
                }
            }
            
            $query .= ' AES_ENCRYPT(\'' . prepareDatabaseString(serialize($_POST)) . '\', \'' . Config::logActionParamsKey() . '\'), ';
        } else {
            $query .= ' NULL, ';
        }

        if ($systemtype == USCIC_SURVEY) {
            $query .= getSurveyLanguage() . ', ';
            $query .= getSurveyMode() . ', ';
            $query .= getSurveyVersion();
        } else {
            $query .= 'NULL, NULL, NULL';
        }
        $query .= ")";

        $db->executeQuery($query);
        if (isset($this->LogActions[$primkey])) { //unset so it is read in again..
            unset($this->LogActions[$primkey]);
        }
    }

    function getActionsByPrimkey($primkey) {
        global $db;
        if (isset($this->LogActions[$primkey])) {
            $contacts = $this->LogActions[$primkey];
        } else {
            $actions = array();
            $query = 'SELECT * FROM ' . Config::dbSurveyData() . '_actions as t1 left join ' . Config::dbSurveyData() . '_users as t2 on t1.urid = t2.urid where primkey = \'' . prepareDatabaseString($primkey) . '\' and action like \'interviewer.%\' order by t1.asid desc';
            //echo '<br/><br/><br/>' . $query;
            $result = $db->selectQuery($query);
            while ($row = $db->getRow($result)) {
                $actions[] = new LogAction($row);
            }
            $this->LogActions[$primkey] = $actions;
        }
        return $actions;
    }

    function getNumberOfActionsBySession($sessionid, $systemtype) {
        global $db;
        $query = 'select count(*) as count from ' . Config::dbSurveyData() . '_actions where sessionid = \'' . prepareDatabaseString($sessionid) . '\' and systemtype = ' . $systemtype;
        if ($result = $db->selectQuery($query)) {
            $row = $db->getRow($result);
            return $row["count"];
        }
        return 0;
    }

    function getNumberOfSurveyActionsBySession($sessionid, $systemtype) {
        global $db;
        $query = 'select count(*) as count from ' . Config::dbSurveyData() . '_actions where sessionid = \'' . prepareDatabaseString($sessionid) . '\' and systemtype = ' . $systemtype;
        if ($result = $db->selectQuery($query)) {
            $row = $db->getRow($result);
            return $row["count"];
        }
        return 0;
    }

    function getLoggedInSMSSession($sessionid) {
        global $db;
        $query = 'select urid from ' . Config::dbSurveyData() . '_actions where sessionid = \'' . prepareDatabaseString($sessionid) . '\' and urid != \'\' and actiontype = 1 and systemtype = ' . USCIC_SMS;
        if ($result = $db->selectQuery($query)) {
            $num = $db->getNumberOfRows($result);
            $row = $db->getRow($result);
            return array("count" => $num, "urid" => $row["urid"]);
        }
        return array("count" => 0, "urid" => "");
    }

    function getLoggedInSurveySession($sessionid) {
        global $db;
        $query = 'select primkey, suid, language, mode, version from ' . Config::dbSurveyData() . '_actions where sessionid = \'' . prepareDatabaseString($sessionid) . '\' and primkey != \'\' and systemtype = ' . USCIC_SURVEY . " order by asid desc";
        if ($result = $db->selectQuery($query)) {
            $num = $db->getNumberOfRows($result);
            if ($num > 0) {
                $row = $db->getRow($result);
                return array("count" => $num, "primkey" => $row["primkey"], "suid" => $row["suid"], "language" => $row["language"], "mode" => $row["mode"], "version" => $row["version"]);
            }
        }
        return array("count" => 0, "primkey" => "", "suid" => "", "language" => "", "mode" => "", "version" => "");
    }

    function deleteLoggedInSurveySession($sessionid) {
        global $db;
        $query = 'delete from ' . Config::dbSurveyData() . '_actions where sessionid = \'' . prepareDatabaseString($sessionid) . '\' and primkey != \'\' and systemtype = ' . USCIC_SURVEY . " ";
        $result = $db->selectQuery($query);
    }

    function getLastSurveyAction($sessionid, $primkey) {
        global $db;
        $query = 'select asid from ' . Config::dbSurveyData() . '_actions where sessionid = \'' . prepareDatabaseString($sessionid) . '\' and primkey = \'' . prepareDatabaseString($primkey) . '\' and systemtype = ' . USCIC_SURVEY . " and actiontype != " . ACTION_WINDOW_IN . " and actiontype != " . ACTION_WINDOW_OUT . " order by asid desc limit 0,1";
        if ($result = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($result) == 0) {
                return 0;
            }
            $row = $db->getRow($result);
            return $row["asid"];
        }
        return -1;
    }

}

?>