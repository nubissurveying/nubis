<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DataRecord {

    private $suid;
    private $primkey;
    private $completed;
    private $variable;
    private $data;
    private $newrecord;

    function __construct($suid = "", $prim = "") {
        $this->suid = $suid;
        $this->primkey = $prim;
        $this->newrecord = true;
        if ($suid != "" && $prim != "") {
            $this->loadRecord();
        }
    }
    
    function getSuid() {
        return $this->suid;
    }

    function getPrimaryKey() {
        return $this->primkey;
    }

    function getDataNames() {
        if (is_array($this->data)) {
            return array_keys($this->data);
        }
        return array();
    }

    function getCleanDataNames() {
        if (is_array($this->data)) {
            $arr = array();
            foreach ($this->data as $k => $v) {//figure out why this seems empty!
                if ($v->isDirty() == false) {
                    $arr[] = $k;
                }
            }
            return $arr;
        }
        return array();
    }

    function getData($variablename) {
        if (isset($this->data[strtoupper($variablename)])) {
            return $this->data[strtoupper($variablename)];
        }
        return null;
    }

    function getDataForVariable($variablename) {
        $arr = array();
        foreach ($this->data as $d) {
            if (strtoupper($d->getVariableDescriptiveName()) == strtoupper($variablename)) {
                $arr[] = $d;
            }
        }
        return $arr;
    }

    function setData($variable) {
        $this->data[strtoupper($variable->getDataName())] = $variable;
        //$this->datanames[] = array('name' => strtoupper($variable->getDataName()), 'seid' =>
    }

    function setAllData($data) {
        $this->data = $data;
    }

    function isCompleted() {
        if (Config::useDataRecords() == false) {
            global $db;
            $q = "select completed from " . Config::dbSurveyData() . "_data where suid=" . prepareDatabaseString($this->suid) . "  and primkey='" . prepareDatabaseString($this->primkey) . "' and variablename='prim_key'";
            $r = $db->selectQuery($q);
            if ($db->getNumberOfRows($r) > 0) {
                $row = $db->getRow($r);
                return $row["completed"] == INTERVIEW_COMPLETED;
            }
        }
        return $this->completed == INTERVIEW_COMPLETED;
    }

    function loadRecord() {

        if (Config::useDataRecords() == false) {
            return;
        }
        global $db, $survey;
        $key = $survey->getDataEncryptionKey();
        $data = "data as data_dec";
        if ($key != "") {
            $data = "aes_decrypt(data, '" . $key . "') as data_dec";
        }

        $q = "select suid, primkey, completed, $data from " . Config::dbSurveyData() . "_datarecords where suid=" . prepareDatabaseString($this->suid) . "  and primkey='" . prepareDatabaseString($this->primkey) . "'";
        $r = $db->selectQuery($q);
        if ($db->getNumberOfRows($r) > 0) {
            $row = $db->getRow($r);
            $this->suid = $row["suid"];
            $this->primkey = $row["primkey"];
            $this->completed = $row["completed"];
            $this->loadData($row["data_dec"]);
            $this->newrecord = false;
            return true;
        }
        /* no record found */
        $this->newrecord = true;
        return false;
    }

    function loadData($data) {
        if ($data != "") {
            $this->data = unserialize(gzuncompress($data));
        }
    }

    function setToComplete() {
        global $db;
        if (Config::useDataRecords() == true) {
            $query = "update " . Config::dbSurveyData() . "_datarecords set completed=" . INTERVIEW_COMPLETED . " where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($this->primkey) . "'";
            $db->executeQuery($query);
        }

        $query = "update " . Config::dbSurveyData() . "_data set completed=" . INTERVIEW_COMPLETED . ", ts=ts where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($this->primkey) . "'";
        $db->executeQuery($query);
    }

    function setToIncomplete() {
        global $db;
        if (Config::useDataRecords() == true) {
            $query = "update " . Config::dbSurveyData() . "_datarecords set completed=" . INTERVIEW_NOTCOMPLETED . " where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($this->primkey) . "'";
            $db->executeQuery($query);
        }

        $query = "update " . Config::dbSurveyData() . "_data set completed=" . INTERVIEW_NOTCOMPLETED . ", ts=ts where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($this->primkey) . "'";
        $db->executeQuery($query);
    }

    function saveRecord() {

        if (Config::useDataRecords() == false) {
            return;
        }

        global $db, $survey;
        $key = $survey->getDataEncryptionKey();
        $data = "?";
        if ($key != "") {
            $data = "aes_encrypt(?, '" . $key . "')";
        }

        $datanames = $this->getDataNames();
        $names = '';
        if (is_array($datanames)) {
            sort($datanames);
            $names = implode("~", $datanames);
        }

        if ($this->newrecord == true) {
            $query = "insert into " . Config::dbSurveyData() . "_datarecords (suid, primkey, datanames, data) values (?,?,?,$data)";
            $bp = new BindParam();
            $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
            $bp->add(MYSQL_BINDING_STRING, $this->primkey);
            $names = gzcompress($names, 9);
            $bp->add(MYSQL_BINDING_STRING, $names);
            $data = gzcompress(serialize($this->data), 9);
            $bp->add(MYSQL_BINDING_STRING, $data);
            $db->executeBoundQuery($query, $bp->get());
        } else {
            $query = "update " . Config::dbSurveyData() . "_datarecords set datanames=?, data=$data where suid=? and primkey=?";
            $bp = new BindParam();
            $names = gzcompress(implode("~", $datanames), 9);
            $bp->add(MYSQL_BINDING_STRING, $names);
            $data = gzcompress(serialize($this->data), 9);
            $bp->add(MYSQL_BINDING_STRING, $data);
            $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
            $bp->add(MYSQL_BINDING_STRING, $this->primkey);
            $db->executeBoundQuery($query, $bp->get());
        }
    }

}

?>