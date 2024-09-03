<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Setting extends NubisObject {

    private $value;
    private $previousvalue;
    private $previousobject;
    private $previousobjecttype;
    private $previouslanguage;
    private $previousmode;
    private $previoussuid;
    private $changed;
    private $setting;

    function __construct($rowOrName = "", $object = "") {
        if (is_array($rowOrName)) {
            $this->setting = $rowOrName;
            $this->setSuid($this->setting["suid"]);
            $this->previousvalue = $this->getValue();
            $this->previousobject = $this->getObject();
            $this->previoussuid = $this->getSuid();
            $this->previouslanguage = $this->getLanguage();
            $this->previousmode = $this->getMode();
            $this->previousobjecttype = $this->getObjectType();
            $this->changed = false;
        }
    }

    function setSuid($suid) {
        parent::setSuid($suid);
        if ($suid != $this->previoussuid) {
            $this->changed = true;
        }
    }

    function getName() {
        return $this->setting["name"];
    }

    function setName($name) {
        $this->setting["name"] = $name;
    }

    function getLanguage() {
        return $this->setting["language"];
    }

    function setLanguage($language) {
        $this->setting["language"] = $language;
        if ($language != $this->previouslanguage) {
            $this->changed = true;
        }
    }

    function getMode() {
        return $this->setting["mode"];
    }

    function setMode($mode) {
        $this->setting["mode"] = $mode;
        if ($mode != $this->previousmode) {
            $this->changed = true;
        }
    }

    function getObject() {
        return $this->setting["object"];
    }

    function setObject($object) {
        $this->setting["object"] = $object;
        if ($object != $this->previousobject) {
            $this->changed = true;
        }
    }

    function getObjectType() {
        return $this->setting["objecttype"];
    }

    function setObjectType($objecttype) {
        $this->setting["objecttype"] = $objecttype;
        if ($objecttype != $this->previousobjecttype) {
            $this->changed = true;
        }
    }

    function getValue() {
        if (isset($this->setting["value"])) {
            return $this->setting["value"];
        }
        return "";
    }

    function setValue($value) {
        $this->setting["value"] = $value;
        if ("'" . $value . "'" != "'" . $this->previousvalue . "'") {
            $this->changed = true;
        }
    }

    function getTimestamp() {
        return $this->setting["ts"];
    }

    function copy() {
        
    }

    function remove() {
        global $db;
        $query = "delete from " . Config::dbSurvey() . "_settings where suid=" . prepareDatabaseString($this->getSuid()) . " and object=" . prepareDatabaseString($this->getObject()) . " and objecttype='" . prepareDatabaseString($this->getObjectType()) . "' and name='" . prepareDatabaseString($this->getName()) . "' and mode=" . prepareDatabaseString($this->getMode()) . " and language=" . prepareDatabaseString($this->getLanguage());
        $db->executeQuery($query);
    }

    function save() {

        // nothing changed, then don't save (so the timestamp remains the same, so it does not appear as if it needs translation again)!
        if ($this->changed == false) {
            return;
        }

        global $db;
        //$query = "replace into " . Config::dbSurvey() . "_settings (suid, object, objecttype, name, value, mode, language) values(";        
        $query = "replace into " . Config::dbSurvey() . "_settings (suid, object, objecttype, name, value, mode, language) values(?,?,?,?,?,?,?)";

        $suid = $this->getSuid();
        $object = $this->getObject();
        $objecttype = $this->getObjectType();
        $name = $this->getName();
        $value = $this->getValue();

        if ($this->getMode() != "") {
            $mode = $this->getMode();
        } else {
            $mode = getSurveyMode();
        }
        if ($this->getLanguage() != "") {
            $language = $this->getLanguage();
        } else {
            $language = getSurveyLanguage();
        }

        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_INTEGER, $suid);
        $bp->add(MYSQL_BINDING_INTEGER, $object);
        $bp->add(MYSQL_BINDING_STRING, $objecttype);
        $bp->add(MYSQL_BINDING_STRING, $name);
        $bp->add(MYSQL_BINDING_STRING, $value);
        $bp->add(MYSQL_BINDING_INTEGER, $mode);
        $bp->add(MYSQL_BINDING_STRING, $language);
        $db->executeBoundQuery($query, $bp->get());

        /* save history if value change */
        if ($this->previousvalue != $value) {
            $track = new Track($suid, $object, $objecttype);
            $track->addEntry($name, $value);
        }

        // update previous values now we saved
        $this->previousvalue = $this->getValue();
        $this->previousobject = $this->getObject();
        $this->previoussuid = $this->getSuid();
        $this->previouslanguage = $this->getLanguage();
        $this->previousmode = $this->getMode();
        $this->previousobjecttype = $this->getObjectType();
    }

}

?>