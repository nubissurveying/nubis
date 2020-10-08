<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */


class Track {
    
    private $suid;
    private $object;
    private $objecttype;
    
    function __construct($suid, $object, $objecttype = 1) {
        $this->suid = $suid;
        $this->object = $object;
        $this->objecttype = $objecttype;
    }
    
    
    function addEntry($setting, $value) {
        global $db;
        $query = "insert into " . Config::dbSurvey() . "_tracks (urid, suid, object, objecttype, setting, value, language, mode, version) values (?, ?,?,?,?,?,?,?,?)";
        $mode = getSurveyMode();        
        $language = getSurveyLanguage();
        $version = getSurveyVersion();
        
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_INTEGER, $_SESSION['URID']);
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->object);
        $bp->add(MYSQL_BINDING_INTEGER, $this->objecttype);
        $bp->add(MYSQL_BINDING_STRING, $setting);
        $bp->add(MYSQL_BINDING_STRING, $value);
        $bp->add(MYSQL_BINDING_INTEGER, $language);
        $bp->add(MYSQL_BINDING_INTEGER, $mode);    
        $bp->add(MYSQL_BINDING_INTEGER, $version);
        $db->executeBoundQuery($query, $bp->get());
    }
    
    function getEntries($setting, $language = "", $mode = "", $version = "") {
        global $db;
        if ($language == "") {
            $language = getSurveyLanguage();
        }
        if ($mode == "") {
            $mode = getSurveyMode();
        }
        if ($version == "") {
            $version = getSurveyVersion();
        }
        $arr = array();
        $query = "select * from " . Config::dbSurvey() . "_tracks where suid=" . prepareDatabaseString($this->suid) . " and object=" . prepareDatabaseString($this->object) . " and objecttype=" . prepareDatabaseString($this->objecttype) . " and setting='" . prepareDatabaseString($setting) . "' and language=" . prepareDatabaseString($language) . " and mode=" . prepareDatabaseString($mode) . " and version=" . prepareDatabaseString($version) . " order by ts desc";
        //echo $query;
        $res = $db->selectQuery($query);
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $arr[] = $row;
                }
            }
        }
        return $arr;
    }
    
    function getEntry($trid) {
        global $db;
        $query = "select * from " . Config::dbSurvey() . "_tracks where trid=" . prepareDatabaseString($trid);        
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {                
                $arr = $db->getRow($res);
            }
        }        
        return $arr;
    }
}

?>