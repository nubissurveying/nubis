<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Version {

    var $version;

    function __construct($rowOrVnid = "") {
        if (is_array($rowOrVnid)) {
            $this->version = $rowOrVnid;
        } else {
            if ($rowOrVnid != "") {
                global $db;
                $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_versions where suid=' . prepareDatabaseString(getSurvey()) . ' and vnid = ' . prepareDatabaseString($rowOrVnid));
                $this->version = $db->getRow($result);
            }
        }
    }

    function getName() {
        return $this->version['name'];
    }

    function setName($name) {
        $this->version['name'] = $name;
    }

    function getDescription() {
        return $this->version['description'];
    }

    function setDescription($description) {
        $this->version['description'] = $description;
    }

    function getVnid() {
        return $this->version['vnid'];
    }

    function setVnid($vnid) {
        $this->version["vnid"] = $vnid;
    }

    function getSuid() {
        return $this->version['suid'];
    }

    function setSuid($suid) {
        $this->version["vnid"] = $suid;
    }

    function copy() {
        
    }

    function remove() {
        global $db;
        if (!isset($this->version['vnid'])) {
            return;
        }
        $query = "delete from " . Config::dbVersion() . "_versions where suid = " . prepareDatabaseString($this->getSuid()) . " and vnid=" . prepareDatabaseString($this->getVnid());
        $db->executeQuery($query);
    }

    function save() {
        global $db;

        if (!isset($this->version['vnid'])) {
            $query = "select max(vnid) as max from " . Config::dbSurvey() . "_versions";
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $vnid = $row["max"] + 1;
            $this->setSuid($vnid);
            $query = "replace into " . Config::dbVersion() . "_versions (suid, name, description) values(";
        }
        else {
            $query = "replace into " . Config::dbVersion() . "_versions (suid, vnid, name, description) values(";
        }

        $query .= $this->getSuid() . ",";
        if (isset($this->version['vnid'])) {
            $query .= prepareDatabaseString($this->getVnid()) . ",";
        }

        $query .= "'" . prepareDatabaseString($this->getName()) . "',";
        $query .= "'" . prepareDatabaseString($this->getDescription()) . "'";
        $query .= ")";
        $db->executeQuery($query);
    }

}

?>