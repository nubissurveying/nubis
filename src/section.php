<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Section extends Component {

    var $section;

    function __construct($seidOrRow = "") {

        $this->setObjectType(OBJECT_SECTION);

        if (is_array($seidOrRow)) {

            $this->section = $seidOrRow;

            $this->setSuid($this->section["suid"]);

            $this->setObjectName($this->getSeid());



            /* add settings */

            $this->addSettings($this->getSettings());
        }
    }

    function getSeid() {

        return $this->section['seid'];
    }

    function setSeid($seid) {

        $this->section["seid"] = $seid;
    }

    function getParent() {

        return $this->section['pid'];
    }

    function setParent($pid) {

        $this->section["pid"] = $pid;
    }

    function getName() {
        return $this->section['name'];
    }

    function setName($name) {
        $this->section["name"] = $name;
        $this->setSettingValue(SETTING_NAME, $name);
    }

    function getDescription() {

        return $this->getSettingValue(SETTING_DESCRIPTION);
    }

    function setDescription($description) {

        $this->setSettingValue(SETTING_DESCRIPTION, $description);
    }

    function getPosition() {
        return $this->section["position"];
    }

    function setPosition($position) {
        $this->section["position"] = $position;
    }

    function getHidden() {
        return $this->getSettingValue(SETTING_HIDDEN);
    }

    function setHidden($text) {
        $this->setSettingValue(SETTING_HIDDEN, $text);
    }

    function isHidden() {
        return $this->getHidden() == HIDDEN_YES;
    }

    function getHeader() {
        return $this->getSettingValue(SETTING_SECTION_HEADER);
    }

    function setHeader($text) {
        $this->setSettingValue(SETTING_SECTION_HEADER, $text);
    }

    function getFooter() {
        return $this->getSettingValue(SETTING_SECTION_FOOTER);
    }

    function setFooter($text) {
        $this->setSettingValue(SETTING_SECTION_FOOTER, $text);
    }

    function storeRouting($routing) {

        global $db;

        //remove old ones

        $result = $db->executeQuery('delete from ' . Config::dbSurvey() . '_routing where suid = ' . prepareDatabaseString($this->getSuid()) . ' and seid = ' . prepareDatabaseString($this->getSeid()));

        $query = 'insert into ' . Config::dbSurvey() . '_routing (suid, seid, rgid, rule) VALUES  ';

        $routinglines = explode("\r\n", rtrim($routing));

        $lines = array();

        $cnt = 1;

        foreach ($routinglines as $line) {
            $lines[] = '(' . prepareDatabaseString($this->getSuid()) . ', ' . prepareDatabaseString($this->getSeid()) . ',' . $cnt . ', "' . prepareDatabaseString($line, false) . '")';

            $cnt++;
        }

        $query .= implode($lines, ',');

        $result = $db->executeQuery($query); //add new lines

        /* save history if value change */
        $track = new Track($this->getSuid(), $this->getObjectName(), $this->getObjectType());
        $track->addEntry(SETTING_ROUTING, $routing);
    }

    function getRouting() {

        global $db;

        $routing = '';

        $result = $db->selectQuery('select rule from ' . Config::dbSurvey() . '_routing where suid = ' . prepareDatabaseString($this->getSuid()) . ' and seid = ' . prepareDatabaseString($this->getSeid()) . ' order by rgid asc');

        while ($row = $db->getRow($result)) {

            $routing .= $row['rule'] . "\r\n";
        }

        return $routing;
    }

    function getCompiledCode() {

        global $db, $survey;

        $q = "select engine from " . Config::dbSurvey() . "_engines where suid=" . prepareDatabaseString($this->getSuid()) . " and seid=" . prepareDatabaseString($this->getSeid()) . " and version=" . prepareDatabaseString(getSurveyVersion($survey));

        $r = $db->selectQuery($q);

        if ($db->getNumberOfRows($r) > 0) {

            $row = $db->getRow($r);

            return unserialize(gzuncompress($row["engine"]));
        }

        return "";
    }

    /* TRANSLATION FUNCTIONS */

    function isTranslated() {
        if ($this->isTranslatedVariables() == false) {
            return false;
        }        
        return true;
    }

    function isTranslatedVariables() {
        global $survey;
        $variables = $survey->getVariableDescriptives($this->getSeid());
        foreach ($variables as $v) {
            if ($v->isTranslated() == false) {
                return false;
            }
        }
        return true;
    }

    function remove() {

        global $db;
        if (!isset($this->section['seid'])) {
            return;
        }

        $query = "delete from " . Config::dbSurvey() . "_sections where suid = " . prepareDatabaseString($this->getSuid()) . " and seid = " . prepareDatabaseString($this->getSeid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_settings where suid = " . prepareDatabaseString($this->getSuid()) . " and object = " . prepareDatabaseString($this->getObjectName()) . " and objecttype = " . prepareDatabaseString($this->getObjectType());
        $db->executeQuery($query);
        
        $query = "delete from " . Config::dbSurvey() . "_progressbars where suid = " . prepareDatabaseString($this->getSuid()) . " and seid = " . prepareDatabaseString($this->getSeid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_engines where suid = " . prepareDatabaseString($this->getSuid()) . " and seid = " . prepareDatabaseString($this->getSeid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_routing where suid = " . prepareDatabaseString($this->getSuid()) . " and seid = " . prepareDatabaseString($this->getSeid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_next where suid = " . prepareDatabaseString($this->getSuid()) . " and seid = " . prepareDatabaseString($this->getSeid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_screens where suid = " . prepareDatabaseString($this->getSuid()) . " and seid = " . prepareDatabaseString($this->getSeid());
        $db->executeQuery($query);
    }

    function move($suid) {

        if (!isset($this->section['seid'])) {
            return;
        }

        global $db;
        $query = "select max(seid) as max from " . Config::dbSurvey() . "_sections";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $seid = $row["max"] + 1;
        $oldseid = $this->getSeid();
        $oldsuid = $this->getSuid();
        
        /* move routing */
        $query = "update " . Config::dbSurvey() . "_routing set suid=" . $suid . ", seid=" . $seid . " where suid=" . $oldsuid . " and seid=" . $oldseid;
        $db->executeQuery($query);
        
        // remove in current survey
        $this->remove();

        $this->setObjectName($seid);
        $this->setSeid($seid);
        $this->setSuid($suid);

        /* set position */
        $query = "select max(position) as max from " . Config::dbSurvey() . "_sections where suid=" . $this->getSuid();
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $pos = $row["max"] + 1;
        $this->setPosition($pos);

        $this->save();

        /* move variables */
        $survey = new Survey($_SESSION['SUID']);
        $vars = $survey->getVariableDescriptives($oldseid);
        foreach ($vars as $var) {
            $var->move($suid, $this->getSeid());
        }        
    }

    function copy($newsuid = "", $suffix = 2, $types = true) {

        /* copy section */
        global $db;
        $query = "select max(seid) as max from " . Config::dbSurvey() . "_sections";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $seid = $row["max"] + 1;
        $oldseid = $this->getSeid();
        $oldsuid = $this->getSuid();
        $this->setObjectName($seid);
        $this->setSeid($seid);
        if ($suffix == 2) {
            $this->setName($this->getName() . "_cl");
        }
        if ($newsuid != "") {
            $this->setSuid($newsuid);
        }

        /* set position */
        $query = "select max(position) as max from " . Config::dbSurvey() . "_sections where suid=" . $this->getSuid();
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $pos = $row["max"] + 1;
        $this->setPosition($pos);

        $this->save();

        /* copy variables */
        $survey = new Survey($_SESSION['SUID']);
        $vars = $survey->getVariableDescriptives($oldseid);
        foreach ($vars as $var) {
            if ($suffix == 2) {
                $var->copy($var->getName() . "_cl", $newsuid, $this->getSeid(), $types);
            }
            else {
                $var->copy($var->getName(), $newsuid, $this->getSeid(), $types);
            }
        }
        
        /* copy routing */
        $query = "insert into " . Config::dbSurvey() . "_routing (suid, seid, rgid, rule, ts) select " . $this->getSuid() . "," . $this->getSeid() . ", rgid, rule, ts from  " . Config::dbSurvey() . "_routing where suid=" . $oldsuid . " and seid=" . $oldseid;
        $db->executeQuery($query);
    }

    function save() {

        global $db;



        if (!isset($this->section['seid'])) {

            $query = "select max(seid) as max from " . Config::dbSurvey() . "_sections";

            $r = $db->selectQuery($query);

            $row = $db->getRow($r);

            $seid = $row["max"] + 1;

            $this->setObjectName($seid);

            $this->setSeid($seid);

            /* set position */
            $query = "select max(position) as max from " . Config::dbSurvey() . "_sections where suid=" . $this->getSuid();
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $pos = $row["max"] + 1;
            $this->setPosition($pos);
        }



        $query = "replace into " . Config::dbSurvey() . "_sections (suid, seid, name, position) values(";

        $query .= prepareDatabaseString($this->getSuid()) . ",";

        $query .= prepareDatabaseString($this->getSeid()) . ",";

        $query .= "'" . prepareDatabaseString($this->getName()) . "', ";

        $order = $this->getPosition();
        if ($order == "") {
            $order = 1;
        }

        $query .= prepareDatabaseString($order);

        $query .= ")";

        $db->executeQuery($query);



        /* save settings */

        $settings = $this->getSettingsArray();

        foreach ($settings as $key => $setting) {

            $setting->setObject($this->getSeid());
            $setting->setSuid($this->getSuid());
            $setting->save();
        }
    }

}

?>