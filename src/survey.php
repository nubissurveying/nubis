<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Survey extends Component {

    var $survey;
    private $defaults;

    function __construct($rowOrSuid = "") {
        $this->setObjectType(OBJECT_SURVEY);
        if (is_array($rowOrSuid)) {
            $this->survey = $rowOrSuid;
            $this->setObjectName($this->getSuid());

            /* add settings */
            $this->addSettings($this->getSettings());
        } else {
            if ($rowOrSuid != "") {
                global $db;
                $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_surveys where suid  = ' . prepareDatabaseString($rowOrSuid));
                $this->survey = $db->getRow($result);

                $this->setObjectName($this->getSuid());

                /* add settings */
                $this->addSettings($this->getSettings());
            }
        }
        $this->defaults = null;
    }

    function getName() {
        return $this->survey['name'];
    }

    function setName($name) {
        $this->survey['name'] = $name;
        $this->setSettingValue(SETTING_NAME, $name);
    }

    function getDescription() {
        return $this->survey['description'];
    }

    function setDescription($description) {
        $this->survey['description'] = $description;
    }

    function getSuid() {
        return $this->survey['suid'];
    }

    function setSuid($suid) {
        $this->survey["suid"] = $suid;
    }

    /* object retrieval functions */

    function getVariables($seid = '') {
        global $db;
        if ($seid != '') {
            $seid = ' AND seid = ' . $seid;
        }
        $variables = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . $seid);
        while ($row = $db->getRow($result)) {
            $variables[] = new Variable($row);
        }
        return $variables;
    }

    function getVariable($vsid) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and vsid = ' . prepareDatabaseString($vsid));
        return new Variable($db->getRow($result));
    }

    function getVariableDescriptives($seid = '', $order = '', $asc = '') {
        global $db;
        if ($seid != '') {
            $seid = ' AND seid = ' . $seid;
        }
        if ($order != '') {
            $order = " order by " . prepareDatabaseString($order) . ' ' . prepareDatabaseString($asc) . ", variablename asc";
        } else {
            $order = " order by position asc, variablename asc";
        }

        $variables = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . $seid . $order);
        while ($row = $db->getRow($result)) {
            $variables[] = new VariableDescriptive($row);
        }
        return $variables;
    }

    function getVariableDescriptive($vsid) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and vsid = ' . prepareDatabaseString($vsid));
        return new VariableDescriptive($db->getRow($result));
    }

    function getVariableDescriptiveByName($name) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and variablename = "' . prepareDatabaseString($name) . '"');
        return new VariableDescriptive($db->getRow($result));
    }

    function getVariableDescriptiveNames($seid = '', $order = '', $asc = '') {
        global $db;
        if ($seid != '') {
            $seid = ' AND seid = ' . $seid;
        }
        if ($order != '') {
            $order = " order by " . prepareDatabaseString($order) . ' ' . prepareDatabaseString($asc) . ", variablename asc";
        } else {
            $order = " order by variablename asc";
        }

        $variables = array();
        $result = $db->selectQuery('select variablename from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . $seid . $order);
        while ($row = $db->getRow($result)) {
            $variables[] = $row["variablename"];
        }
        return $variables;
    }

    function getPreviousVariableDescriptive($var) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and seid= ' . prepareDatabaseString($var->getSeid()) . ' and position < ' . prepareDatabaseString($var->getPosition()) . ' order by position desc');
        return new VariableDescriptive($db->getRow($result));
    }

    function getNextVariableDescriptive($var) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and seid= ' . prepareDatabaseString($var->getSeid()) . ' and position > ' . prepareDatabaseString($var->getPosition()) . ' order by position asc');
        return new VariableDescriptive($db->getRow($result));
    }

    function getVariableDescriptivesOfType($tyd) {
        global $db;
        $variables = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_variables where suid  = ' . prepareDatabaseString($this->getSuid()) . " and tyd = " . prepareDatabaseString($tyd));
        while ($row = $db->getRow($result)) {
            $variables[] = new VariableDescriptive($row);
        }
        return $variables;
    }

    function getPreviousType($type) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_types where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and tyd < ' . prepareDatabaseString($type->getTyd()) . ' order by tyd desc');
        return new Type($db->getRow($result));
    }

    function getNextType($type) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_types where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and tyd > ' . prepareDatabaseString($type->getTyd()) . ' order by tyd asc');
        return new Type($db->getRow($result));
    }

    function getType($tyd) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_types where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and tyd = ' . prepareDatabaseString($tyd));
        return new Type($db->getRow($result));
    }

    function getTypeByName($name) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_types where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and name = "' . prepareDatabaseString($name) . '"');
        return new Type($db->getRow($result));
    }

    function getNumberOfTypes() {
        global $db;
        $surveys = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_types');
        return $db->getNumberOfRows($result);
    }

    function getTypes($used = false) {
        global $db;
        $types = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_types where suid  = ' . prepareDatabaseString($this->getSuid()));
        while ($row = $db->getRow($result)) {
            $type = new Type($row);
            if ($used == false || $type->isUsed()) {
                $types[] = $type;
            }
        }
        return $types;
    }

    function getPreviousGroup($group) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and gid < ' . prepareDatabaseString($group->getGid()) . ' order by gid desc');
        return new Group($db->getRow($result));
    }

    function getNextGroup($group) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and gid > ' . prepareDatabaseString($group->getGid()) . ' order by gid asc');
        return new Group($db->getRow($result));
    }

    function getGroup($id) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and gid = ' . prepareDatabaseString($id));
        return new Group($db->getRow($result));
    }

    function getGroupByName($name) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and name = "' . prepareDatabaseString($name) . '"');
        return new Group($db->getRow($result));
    }

    function getGroups() {
        global $db;
        $groups = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()));
        while ($row = $db->getRow($result)) {
            $groups[] = new Group($row);
        }
        return $groups;
    }

    function getGroupNames() {
        global $db;
        $groups = array();
        $result = $db->selectQuery('select name from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()));
        while ($row = $db->getRow($result)) {
            $groups[] = $row["name"];
        }
        return $groups;
    }

    function getNumberOfGroups() {
        global $db;
        $surveys = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_groups where suid  = ' . prepareDatabaseString($this->getSuid()));
        return $db->getNumberOfRows($result);
    }

    function getNumberOfSections() {
        global $db;
        $surveys = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()));
        return $db->getNumberOfRows($result);
    }

    function getSections($order = '', $asc = '') {
        global $db;
        $sections = array();
        if ($order != '') {
            $order = " order by " . prepareDatabaseString($order) . ' ' . prepareDatabaseString($asc) . ", name asc";
        } else {
            $order = " order by position asc, name asc";
        }

        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()) . $order);
        //echo 'select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()) . $order;
        while ($row = $db->getRow($result)) {
            $sections[] = new Section($row);
        }
        return $sections;
    }

    function getSectionIdentifiers() {
        global $db;
        $ids = array();
        $result = $db->selectQuery('select seid from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()));
        while ($row = $db->getRow($result)) {
            $ids[] = $row["seid"];
        }
        return $ids;
    }

    function getSectionNames() {
        global $db;
        $ids = array();
        $result = $db->selectQuery('select name from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()));
        while ($row = $db->getRow($result)) {
            $ids[] = $row["name"];
        }
        return $ids;
    }

    function getSection($seid) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and seid = ' . prepareDatabaseString($seid));
        return new Section($db->getRow($result));
    }

    function getSectionByName($name) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and name = "' . prepareDatabaseString($name) . '"');
        return new Section($db->getRow($result));
    }

    function getPreviousSection($section) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and position < ' . prepareDatabaseString($section->getPosition()) . ' order by position desc');
        return new Section($db->getRow($result));
    }

    function getNextSection($section) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_sections where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and position > ' . prepareDatabaseString($section->getPosition()) . ' order by position asc');
        return new Section($db->getRow($result));
    }

    function addSection($name) {
        global $db;
        return $db->executeQuery('INSERT INTO ' . Config::dbSurvey() . '_sections (suid, name) VALUES (' . prepareDatabaseString($this->getSuid()) . ', "' . prepareDatabaseString($name) . '")');
    }

    function addVersion($name, $description) {
        global $db;
        $db->executeQuery("insert into " . Config::dbSurvey() . "_versions (suid, name, description) values (" . prepareDatabaseString($this->suid) . "," . "'" . $name . "'," . "'" . $description . "')");
    }

    function getVersion($vnid) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_versions where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and vnid = ' . prepareDatabaseString($vnid));
        return new Version($db->getRow($result));
    }

    function getVersionbyName($name) {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_versions where suid  = ' . prepareDatabaseString($this->getSuid()) . ' and name = "' . prepareDatabaseString($name)) . "'";
        return new Version($db->getRow($result));
    }

    function getCurrentVersion() {
        global $db;
        $result = $db->selectQuery('select max(vnid) as max from ' . Config::dbSurvey() . '_versions where suid  = ' . prepareDatabaseString($this->getSuid()));
        if ($result) {
            $row = $db->getRow($result);
            if ($row['max'] == "") {
                return 1;
            }
            return $row['max'];
        }
        return 1;
    }

    function getVersions() {
        global $db;
        $versions = array();
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_versions where suid=' . prepareDatabaseString($this->getSuid()));
        while ($row = $db->getRow($result)) {
            $versions[] = new Version($row);
        }
        return $versions;
    }

    function getSettingDirectly($object, $objecttype, $name, $mode = "", $language = "") {
        global $db;
        $settings = array();
        $query = "select * from " . Config::dbSurvey() . "_settings where object=" . prepareDatabaseString($object) . " and objecttype=" . prepareDatabaseString($objecttype) . " and name='" . prepareDatabaseString($name) . "'";
        if ($this->getSuid() != "") {
            $query .= " and suid=" . prepareDatabaseString($this->getSuid());
        }
        if ($mode != "") {
            $query .= " and mode=" . prepareDatabaseString($mode);
        }
        if ($language != "") {
            $query .= " and language=" . prepareDatabaseString($language);
        }
        //echo$query;
        if ($result = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($result) > 0) {
                $row = $db->getRow($result);
                return new Setting($row);
            }
        }
        return new Setting();
    }

    function setSettingDirectly($object, $objecttype, $name, $value, $mode = "", $language = "") {
        $setting = new Setting();
        $setting->setSuid($this->getSuid());
        $setting->setLanguage($language);
        $setting->setMode($mode);
        $setting->setName($name);
        $setting->setObject($object);
        $setting->setObjectType($objecttype);
        $setting->setValue($value);
        $setting->save();
    }

    function getPosition() {
        return $this->survey["position"];
    }

    function setPosition($position) {
        $this->survey["position"] = $position;
    }

    function remove() {
        global $db;
        if (!isset($this->survey['suid'])) {
            return;
        }
        $query = "delete from " . Config::dbSurvey() . "_surveys where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_context where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_engines where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_progressbars where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_screens where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_settings where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_routing where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_next where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_groups where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_types where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_sections where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_variables where suid = " . prepareDatabaseString($this->getSuid());
        $db->executeQuery($query);
    }

    function copy() {

        /* copy survey */
        global $db;
        $query = "select max(suid) as max from " . Config::dbSurvey() . "_surveys";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $suid = $row["max"] + 1;
        $oldsuid = $this->getSuid();
        $sections = $this->getSections();
        $types = $this->getTypes();
        $groups = $this->getGroups();

        $this->setObjectName($suid);
        $this->setSuid($suid);
        $this->setName($this->getName() . "_cl");
        $this->save();

        /* copy sections */
        foreach ($sections as $section) {
            $section->copy($this->getSuid(), 1, false); // no suffix
        }

        /* copy types */
        foreach ($types as $type) {
            $old = $type->getTyd();
            $type->copy($this->getSuid(), 1); // no suffix
            // update variables with type!
            $query = "update " . Config::dbSurvey() . "_variables set tyd=" . $type->getTyd() . " where suid=" . $this->getSuid() . " and tyd=" . $old;
            $db->executeQuery($query);
            //echo $query . "<hr>";
        }

        /* copy groups */
        foreach ($groups as $group) {
            $group->copy($this->getSuid(), 1); // no suffix
        }
    }

    function save() {
        global $db;

        if (!isset($this->survey['suid'])) {
            $query = "select max(suid) as max from " . Config::dbSurvey() . "_surveys";
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $suid = $row["max"] + 1;
            $this->setSuid($suid);
        }
        if (!isset($this->survey['position'])) {
            /* set position */
            $query = "select max(position) as max from " . Config::dbSurvey() . "_surveys";
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $pos = $row["max"] + 1;
            $this->setPosition($pos);
        }
        $query = "replace into " . Config::dbSurvey() . "_surveys (suid, name, description, position) values(";
        $query .= prepareDatabaseString($this->getSuid()) . ",";
        $query .= "'" . prepareDatabaseString($this->getName()) . "',";
        $query .= "'" . prepareDatabaseString($this->getDescription()) . "',";
        $query .= "'" . prepareDatabaseString($this->getPosition()) . "'";
        $query .= ")";
        $db->executeQuery($query);

        /* save settings */
        $settings = $this->getSettingsArray();
        foreach ($settings as $key => $setting) {
            $setting->setObjectName($this->getSuid());
            $setting->setSuid($this->getSuid());
            $setting->save();
        }
    }

    /* survey level settings that can not be overridden by variable/type/group */

    function getTitle($default = true) {
        if ($this->getSettingValue(SETTING_TITLE, $default) != "") {
            return $this->getSettingValue(SETTING_TITLE, $default);
        }
        return Language::messageTitle();
    }

    function setTitle($value) {
        $this->setSettingValue(SETTING_TITLE, $value);
    }

    function getPageHeader($default = true) {
        if ($this->getSettingValue(SETTING_PAGE_HEADER, $default) != "") {
            return $this->getSettingValue(SETTING_PAGE_HEADER, $default);
        }
        return "";
    }

    function setPageHeader($value) {
        $this->setSettingValue(SETTING_PAGE_HEADER, $value);
    }

    function getPageJavascript($default = true) {
        if ($this->getSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $default) != "") {
            return $this->getSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $default);
        }
        return "";
    }

    function setPageJavascript($value) {
        $this->setSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $value);
    }

    function getPageStyle($default = true) {
        if ($this->getSettingValue(SETTING_STYLE_WITHIN_PAGE, $default) != "") {
            return $this->getSettingValue(SETTING_STYLE_WITHIN_PAGE, $default);
        }
        return "";
    }

    function setPageStyle($value) {
        $this->setSettingValue(SETTING_STYLE_WITHIN_PAGE, $value);
    }

    function getPageFooter($default = true) {
        if ($this->getSettingValue(SETTING_PAGE_FOOTER, $default) != "") {
            return $this->getSettingValue(SETTING_PAGE_FOOTER, $default);
        }
        return "";
    }

    function setPageFooter($value) {
        $this->setSettingValue(SETTING_PAGE_FOOTER, $value);
    }

    function getScripts($default = true) {
        if ($this->getSettingValue(SETTING_SCRIPTS, $default) != "") {
            return $this->getSettingValue(SETTING_SCRIPTS, $default);
        }
        return "";
    }

    function setScripts($value) {
        $this->setSettingValue(SETTING_SCRIPTS, $value);
    }

    function getOnNext($default = true) {
        if ($this->getSettingValue(SETTING_ON_NEXT, $default) != "") {
            return $this->getSettingValue(SETTING_ON_NEXT, $default);
        }
        return "";
    }

    function setOnNext($value) {
        $this->setSettingValue(SETTING_ON_NEXT, $value);
    }

    function getOnBack($default = true) {
        if ($this->getSettingValue(SETTING_ON_BACK, $default) != "") {
            return $this->getSettingValue(SETTING_ON_BACK, $default);
        }
        return "";
    }

    function setOnBack($value) {
        $this->setSettingValue(SETTING_ON_BACK, $value);
    }

    function getOnDK() {
        if ($this->getSettingValue(SETTING_ON_DK, $default) != "") {
            return $this->getSettingValue(SETTING_ON_DK, $default);
        }
        return "";
    }

    function setOnDK($value) {
        $this->setSettingValue(SETTING_ON_DK, $value);
    }

    function getOnRF($default = true) {
        if ($this->getSettingValue(SETTING_ON_RF, $default) != "") {
            return $this->getSettingValue(SETTING_ON_RF, $default);
        }
        return "";
    }

    function setOnRF($value) {
        $this->setSettingValue(SETTING_ON_RF, $value);
    }

    function getOnNA($default = true) {
        if ($this->getSettingValue(SETTING_ON_NA, $default) != "") {
            return $this->getSettingValue(SETTING_ON_NA, $default);
        }
        return "";
    }

    function setOnNA($value) {
        $this->setSettingValue(SETTING_ON_NA, $value);
    }

    function getOnUpdate($default = true) {
        if ($this->getSettingValue(SETTING_ON_UPDATE, $default) != "") {
            return $this->getSettingValue(SETTING_ON_UPDATE, $default);
        }
        return "";
    }

    function setOnUpdate($value) {
        $this->setSettingValue(SETTING_ON_UPDATE, $value);
    }

    function getOnLanguageChange($default = true) {
        if ($this->getSettingValue(SETTING_ON_LANGUAGE_CHANGE, $default) != "") {
            return $this->getSettingValue(SETTING_ON_LANGUAGE_CHANGE, $default);
        }
        return "";
    }

    function setOnLanguageChange($value) {
        $this->setSettingValue(SETTING_ON_LANGUAGE_CHANGE, $value);
    }

    function getOnModeChange($default = true) {
        if ($this->getSettingValue(SETTING_ON_MODE_CHANGE, $default) != "") {
            return $this->getSettingValue(SETTING_ON_MODE_CHANGE, $default);
        }
        return "";
    }

    function setOnModeChange($value) {
        $this->setSettingValue(SETTING_ON_MODE_CHANGE, $value);
    }

    function getOnVersionChange($default = true) {
        if ($this->getSettingValue(SETTING_ON_VERSION_CHANGE, $default) != "") {
            return $this->getSettingValue(SETTING_ON_VERSION_CHANGE, $default);
        }
        return "";
    }

    function setOnVersionChange($value) {
        $this->setSettingValue(SETTING_ON_VERSION_CHANGE, $value);
    }

    function getClickNext($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_NEXT, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_NEXT, $default);
        }
        return "";
    }

    function setClickNext($value) {
        $this->setSettingValue(SETTING_CLICK_NEXT, $value);
    }

    function getClickBack($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_BACK, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_BACK, $default);
        }
        return "";
    }

    function setClickBack($value) {
        $this->setSettingValue(SETTING_CLICK_BACK, $value);
    }

    function getClickDK() {
        if ($this->getSettingValue(SETTING_CLICK_DK, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_DK, $default);
        }
        return "";
    }

    function setClickDK($value) {
        $this->setSettingValue(SETTING_CLICK_DK, $value);
    }

    function getClickRF($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_RF, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_RF, $default);
        }
        return "";
    }

    function setClickRF($value) {
        $this->setSettingValue(SETTING_CLICK_RF, $value);
    }

    function getClickNA($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_NA, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_NA, $default);
        }
        return "";
    }

    function setClickNA($value) {
        $this->setSettingValue(SETTING_CLICK_NA, $value);
    }

    function getClickUpdate($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_UPDATE, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_UPDATE, $default);
        }
        return "";
    }

    function setClickUpdate($value) {
        $this->setSettingValue(SETTING_CLICK_UPDATE, $value);
    }

    function getClickLanguageChange($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_LANGUAGE_CHANGE, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_LANGUAGE_CHANGE, $default);
        }
        return "";
    }

    function setClickLanguageChange($value) {
        $this->setSettingValue(SETTING_CLICK_LANGUAGE_CHANGE, $value);
    }

    function getClickModeChange($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_MODE_CHANGE, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_MODE_CHANGE, $default);
        }
        return "";
    }

    function setClickModeChange($value) {
        $this->setSettingValue(SETTING_CLICK_MODE_CHANGE, $value);
    }

    function getClickVersionChange($default = true) {
        if ($this->getSettingValue(SETTING_CLICK_VERSION_CHANGE, $default) != "") {
            return $this->getSettingValue(SETTING_CLICK_VERSION_CHANGE, $default);
        }
        return "";
    }

    function setClickVersionChange($value) {
        $this->setSettingValue(SETTING_CLICK_VERSION_CHANGE, $value);
    }

    /* language functions */

    function getReentryLanguage($mode = "") {
        $reentry = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_REENTRY_LANGUAGE, $mode)->getValue();

        // if empty, then try with default mode
        if ($reentry == "") {
            $reentry = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_REENTRY_LANGUAGE, getDefaultSurveyMode())->getValue();
        }
        if ($reentry == "") {
            $reentry = LANGUAGE_REENTRY_NO;
        }
        return $reentry;
    }

    function setReentryLanguage($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_REENTRY_LANGUAGE, $value, getSurveyMode(), 1); // language here is a dummy, never used        
    }

    function getBackLanguage($mode) {
        $back = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_BACK_LANGUAGE, $mode)->getValue();

        // if empty, then try with default mode
        if ($back == "") {
            $back = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_BACK_LANGUAGE, getDefaultSurveyMode())->getValue();
        }
        if ($back == "") {
            return BACK_REENTRY_NO;
        }
        return $back;
    }

    function setBackLanguage($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_BACK_LANGUAGE, $value, getSurveyMode(), 1); // language here is a dummy, never used        
    }

    function getChangeLanguage($mode) {
        $languagechange = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_LANGUAGE, $mode)->getValue();

        // if empty, then try with default mode
        if ($languagechange == "") {
            $languagechange = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_LANGUAGE, getDefaultSurveyMode())->getValue();
        }
        if ($languagechange == "") {
            return LANGUAGE_CHANGE_NOTALLOWED;
        }
        return $languagechange;
    }

    function setChangeLanguage($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_LANGUAGE, $value, getSurveyMode(), 1); // language here is a dummy, never used
    }

    function getDefaultLanguage($mode, $default = true) {
        $defaultlanguage = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_LANGUAGE, $mode)->getValue();

        // if empty, then try with default mode
        if ($default && $defaultlanguage == "") {
            $defaultlanguage = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_LANGUAGE, getDefaultSurveyMode())->getValue();
        }
        return $defaultlanguage;
    }

    function setDefaultLanguage($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_LANGUAGE, $value, getSurveyMode(), 1); // language here is a dummy, never used
    }

    function getAllowedLanguages($mode, $default = true) {
        $allowed = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_ALLOWED_LANGUAGES, $mode)->getValue();
        if ($default && $allowed == "") {
            $allowed = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_ALLOWED_LANGUAGES, getDefaultSurveyMode())->getValue();
        }
        if (trim($allowed) == "") {
            $allowed = 1; // default of english allowed
        }
        return $allowed;
    }

    function setAllowedLanguages($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_ALLOWED_LANGUAGES, $value, getSurveyMode(), 1); // mode and language here are dummies, never used
    }

    /* mode functions */

    function getReentryMode() {
        $r = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_REENTRY_MODE)->getValue();
        if ($r == "") {
            $r = MODE_REENTRY_NO;
        }
        return $r;
    }

    function setReentryMode($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_REENTRY_MODE, $value, 1, 1); // mode and language here are dummies, never used
    }

    function getBackMode() {
        $r = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_BACK_MODE)->getValue();
        if ($r == "") {
            $r = LANGUAGE_BACK_NO;
        }
        return $r;
    }

    function setBackMode($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_BACK_MODE, $value, 1, 1); // mode and language here are dummies, never used
    }

    function getChangeMode() {
        $r = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_MODE)->getValue();
        if ($r == "") {
            $r = MODE_CHANGE_NOTALLOWED;
        }
        return $r;
    }

    function setChangeMode($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_CHANGE_MODE, $value, 1, 1); // mode and language here are dummies, never used
    }

    function getDefaultMode() {
        return $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_MODE)->getValue();
    }

    function setDefaultMode($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_DEFAULT_MODE, $value, 1, 1); // mode and language here are dummies, never used
    }

    function getAllowedModes() {
        $allowed = $this->getSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_ALLOWED_MODES)->getValue();
        if ($allowed == "") {
            $allowed = implode("~", array_keys(Common::surveyModes()));
        }
        return $allowed;
    }

    function setAllowedModes($value) {
        $this->setSettingDirectly(USCIC_SURVEY, OBJECT_SURVEY, SETTING_ALLOWED_MODES, $value, 1, 1); // mode and language here are dummies, never used
    }

    /* access functions */

    function getAccessType($default = true) {
        if ($this->getSettingValue(SETTING_ACCESS_TYPE, $default) != "") {
            return $this->getSettingValue(SETTING_ACCESS_TYPE, $default);
        }
        return LOGIN_ANONYMOUS;
    }

    function setAccessType($value) {
        $this->setSettingValue(SETTING_ACCESS_TYPE, $value);
    }

    /* function getAccessReturn($default = true) {
      return $this->getSettingValue(SETTING_ACCESS_RETURN, $default);
      }

      function setAccessReturn($value) {
      $this->setSettingValue(SETTING_ACCESS_RETURN, $value);
      } */

    function getAccessReturnAfterCompletionAction($default = true) {
        if ($this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $default) != "") {
            return $this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $default);
        }
        return $this->getDefaultValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION);
    }

    function setAccessReturnAfterCompletionAction($value) {
        $this->setSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $value);
    }

    function getAccessReturnAfterCompletionRedoPreload($default = true) {
        if ($this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $default) != "") {
            return $this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $default);
        }
        return $this->getDefaultValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO);
    }

    function setAccessReturnAfterCompletionRedoPreload($value) {
        $this->setSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $value);
    }

    function getAccessReentryAction($default = true) {
        if ($this->getSettingValue(SETTING_ACCESS_REENTRY_ACTION, $default) != "") {
            return $this->getSettingValue(SETTING_ACCESS_REENTRY_ACTION, $default);
        }
        return $this->getDefaultValue(SETTING_ACCESS_REENTRY_ACTION);
    }

    function setAccessReentryAction($value) {
        $this->setSettingValue(SETTING_ACCESS_REENTRY_ACTION, $value);
    }

    function getAccessReentryRedoPreload($default = true) {
        if ($this->getSettingValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $default) != "") {
            return $this->getSettingValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $default);
        }
        return $this->getDefaultValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO);
    }

    function setAccessReentryRedoPreload($value) {
        $this->setSettingValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $value);
    }

    function getAccessDatesFrom($default = true) {
        return $this->getSettingValue(SETTING_ACCESS_DATES_FROM, $default);
    }

    function setAccessDatesFrom($value) {
        $this->setSettingValue(SETTING_ACCESS_DATES_FROM, $value);
    }

    function getAccessDatesTo($default = true) {
        return $this->getSettingValue(SETTING_ACCESS_DATES_TO, $default);
    }

    function setAccessDatesTo($value) {
        $this->setSettingValue(SETTING_ACCESS_DATES_TO, $value);
    }

    function getAccessTimesFrom($default = true) {
        return $this->getSettingValue(SETTING_ACCESS_TIMES_FROM, $default);
    }

    function setAccessTimesFrom($value) {
        $this->setSettingValue(SETTING_ACCESS_TIMES_FROM, $value);
    }

    function getAccessTimesTo($default = true) {
        return $this->getSettingValue(SETTING_ACCESS_TIMES_TO, $default);
    }

    function setAccessTimesTo($value) {
        $this->setSettingValue(SETTING_ACCESS_TIMES_TO, $value);
    }

    function getAccessDevice($default = true) {
        return $this->getSettingValue(SETTING_ACCESS_DEVICE, $default);
    }

    function setAccessDevice($value) {
        $this->setSettingValue(SETTING_ACCESS_DEVICE, $value);
    }

    /* display functions */

    function getChangeTemplate($default = 0) {
        $val = $this->getSettingValue(SETTING_SURVEY_CHANGE_TEMPLATE, $default);
        if ($val != "") {
            return $val;
        }
        return $default;
    }

    function setChangeTemplate($value) {
        $this->setSettingValue(SETTING_SURVEY_CHANGE_TEMPLATE, $value);
    }

    function getTemplate($default = 0) {
        $val = $this->getSettingValue(SETTING_SURVEY_TEMPLATE, $default);
        if ($val != "") {
            return $val;
        }
        return $default;
    }

    function setTemplate($value) {
        $this->setSettingValue(SETTING_SURVEY_TEMPLATE, $value);
    }

    /* data functions */

    function getDataEncryptionKeyDirectly($mode, $language, $defaultmode, $defaultlanguage) {
        $val = $this->getSettingValueDirectAjax(SETTING_DATA_ENCRYPTION_KEY, $mode, $language, $defaultmode, $defaultlanguage);
        if ($val != "") {
            return decryptC($val, Config::dataEncryptionKey());
        }
        return "";
    }

    function getDataEncryptionKey($default = true) {
        $val = $this->getSettingValue(SETTING_DATA_ENCRYPTION_KEY, $default);
        if ($val != "") {
            return decryptC($val, Config::dataEncryptionKey());
        }
        return "";
    }

    function setDataEncryptionKey($value) {
        $this->setSettingValue(SETTING_DATA_ENCRYPTION_KEY, encryptC($value, Config::dataEncryptionKey()));
    }

    /* BELOW FOLLOW SETTINGS THAT TYPES/GROUPS/VARIABLES CAN OVERRIDE */

    /* function for handling default values of overall functions */

    function setDefaults() {
        $this->defaults = array(
            SETTING_IFEMPTY => IF_EMPTY_WARN,
            SETTING_IFERROR => IF_ERROR_NOTALLOW,
            SETTING_GROUP_TABLE_STRIPED => TABLE_NO,
            SETTING_GROUP_TABLE_BORDERED => TABLE_NO,
            SETTING_GROUP_TABLE_CONDENSED => TABLE_NO,
            SETTING_GROUP_TABLE_HOVERED => TABLE_NO,
            SETTING_QUESTION_ALIGNMENT => ALIGN_LEFT,
            SETTING_QUESTION_FORMATTING => "",
            SETTING_ANSWER_ALIGNMENT => ALIGN_LEFT,
            SETTING_ANSWER_FORMATTING => "",
            SETTING_BUTTON_ALIGNMENT => ALIGN_CENTER,
            SETTING_BUTTON_FORMATTING => "",
            SETTING_HEADER_ALIGNMENT => ALIGN_CENTER,
            SETTING_HEADER_FORMATTING => "",
            SETTING_TABLE_WIDTH => TABLE_WIDTH,
            SETTING_QUESTION_COLUMN_WIDTH => TABLE_QUESTION_COLUMN_WIDTH,
            SETTING_BACK_BUTTON => BUTTON_YES,
            SETTING_NEXT_BUTTON => BUTTON_YES,
            SETTING_DK_BUTTON => BUTTON_NO,
            SETTING_RF_BUTTON => BUTTON_NO,
            SETTING_NA_BUTTON => BUTTON_NO,
            SETTING_UPDATE_BUTTON => BUTTON_NO,
            SETTING_CLOSE_BUTTON => BUTTON_NO,
            SETTING_REMARK_BUTTON => BUTTON_NO,
            SETTING_REMARK_SAVE_BUTTON => BUTTON_NO,
            SETTING_BACK_BUTTON_LABEL => Language::buttonBack(),
            SETTING_NEXT_BUTTON_LABEL => Language::buttonNext(),
            SETTING_DK_BUTTON_LABEL => Language::buttonDK(),
            SETTING_RF_BUTTON_LABEL => Language::buttonRF(),
            SETTING_NA_BUTTON_LABEL => Language::buttonNA(),
            SETTING_UPDATE_BUTTON_LABEL => Language::buttonUpdate(),
            SETTING_REMARK_BUTTON_LABEL => Language::buttonRemark(),
            SETTING_REMARK_SAVE_BUTTON_LABEL => Language::buttonRemarkSave(),
            SETTING_CLOSE_BUTTON_LABEL => Language::buttonClose(),
            SETTING_PROGRESSBAR_SHOW => PROGRESSBAR_BAR,
            SETTING_PROGRESSBAR_TYPE => PROGRESSBAR_WHOLE,
            SETTING_PROGRESSBAR_FILLED_COLOR => PROGRESSBAR_FILLED_COLOR,
            SETTING_PROGRESSBAR_REMAIN_COLOR => PROGRESSBAR_REMAIN_COLOR,
            SETTING_PROGRESSBAR_WIDTH => PROGRESSBAR_WIDTH,
            SETTING_ERROR_PLACEMENT => ERROR_PLACEMENT_WITH_QUESTION,
            SETTING_EMPTY_MESSAGE => Language::errorCheckRequired(),
            SETTING_ERROR_MESSAGE_INTEGER => Language::errorCheckInteger(),
            SETTING_ERROR_MESSAGE_DOUBLE => Language::errorCheckDouble(),
            SETTING_ERROR_MESSAGE_PATTERN => Language::errorCheckPattern(),
            SETTING_ERROR_MESSAGE_RANGE => Language::errorCheckRange(),
            SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR => Language::errorCheckMaximumCalendar(),
            SETTING_ERROR_MESSAGE_MINIMUM_LENGTH => Language::errorCheckMinLength(),
            SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH => Language::errorCheckMaxLength(),
            SETTING_ERROR_MESSAGE_MINIMUM_WORDS => Language::errorCheckMinWords(),
            SETTING_ERROR_MESSAGE_MAXIMUM_WORDS => Language::errorCheckMaxWords(),
            SETTING_ERROR_MESSAGE_MINIMUM_SELECT => Language::errorCheckSelectMin(),
            SETTING_ERROR_MESSAGE_MAXIMUM_SELECT => Language::errorCheckSelectMax(),
            SETTING_ERROR_MESSAGE_EXACT_SELECT => Language::errorCheckSelectExact(),
            SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT => Language::errorCheckSelectInvalidSubset(),
            SETTING_ERROR_MESSAGE_INVALID_SELECT => Language::errorCheckSelectInvalidSet(),
            SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE => Language::errorCheckInlineExclusive(),
            SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE => Language::errorCheckInlineInclusive(),
            SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED => Language::errorCheckInlineMinRequired(),
            SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED => Language::errorCheckInlineMaxRequired(),
            SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED => Language::errorCheckInlineExactRequired(),
            SETTING_ERROR_MESSAGE_EXCLUSIVE => Language::errorCheckExclusive(),
            SETTING_ERROR_MESSAGE_INCLUSIVE => Language::errorCheckInclusive(),
            SETTING_ERROR_MESSAGE_MINIMUM_RANK => Language::errorCheckRankMin(),
            SETTING_ERROR_MESSAGE_MAXIMUM_RANK => Language::errorCheckRankMax(),
            SETTING_ERROR_MESSAGE_EXACT_RANK => Language::errorCheckRankExact(),
            SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED => Language::errorCheckMinRequired(),
            SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED => Language::errorCheckMaxRequired(),
            SETTING_ERROR_MESSAGE_EXACT_REQUIRED => Language::errorCheckExactRequired(),
            SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED => Language::errorCheckUniqueRequired(),
            SETTING_ERROR_MESSAGE_SAME_REQUIRED => Language::errorCheckSameRequired(),
            SETTING_ERROR_MESSAGE_INLINE_ANSWERED => Language::errorCheckInlineAnswered(),
            SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED => Language::errorCheckEnumeratedEntered(),
            SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED => Language::errorCheckSetOfEnumeratedEntered(),
            SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO => Language::errorCheckComparisonEqualTo(),
            SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO => Language::errorCheckComparisonNotEqualTo(),
            SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE => Language::errorCheckComparisonEqualToIgnoreCase(),
            SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE => Language::errorCheckComparisonNotEqualToIgnoreCase(),
            SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO => Language::errorCheckComparisonGreaterEqualTo(),
            SETTING_ERROR_MESSAGE_COMPARISON_GREATER => Language::errorCheckComparisonGreater(),
            SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO => Language::errorCheckComparisonSmallerEqualTo(),
            SETTING_ERROR_MESSAGE_COMPARISON_SMALLER => Language::errorCheckComparisonSmaller(),
            SETTING_INLINE_EXCLUSIVE => INLINE_NO,
            SETTING_INLINE_INCLUSIVE => INLINE_YES,
            SETTING_INLINE_MINIMUM_REQUIRED => "",
            SETTING_INLINE_MAXIMUM_REQUIRED => "",
            SETTING_INLINE_EXACT_REQUIRED => "",
            SETTING_KEEP => KEEP_ANSWER_NO,
            SETTING_ARRAY => ARRAY_ANSWER_NO,
            SETTING_HIDDEN => HIDDEN_NO,
            SETTING_HIDDEN_ROUTING => HIDDEN_NO,
            SETTING_HIDDEN_PAPER_VERSION => HIDDEN_NO,
            SETTING_HIDDEN_TRANSLATION => HIDDEN_NO,
            SETTING_MINIMUM_RANGE => ANSWER_RANGE_MINIMUM,
            SETTING_MAXIMUM_RANGE => ANSWER_RANGE_MAXIMUM,
            SETTING_OTHER_RANGE => "",
            SETTING_PATTERN => ANSWER_PATTERN,
            SETTING_INPUT_MASK => "",
            SETTING_INPUT_MASK_PLACEHOLDER => "",
            SETTING_MINIMUM_LENGTH => ANSWER_STRING_MIN_LENGTH,
            SETTING_MAXIMUM_LENGTH => ANSWER_STRING_MAX_LENGTH,
            SETTING_MINIMUM_OPEN_LENGTH => ANSWER_OPEN_MIN_LENGTH,
            SETTING_MAXIMUM_OPEN_LENGTH => ANSWER_OPEN_MAX_LENGTH,
            SETTING_MINIMUM_WORDS => ANSWER_OPEN_MIN_WORDS,
            SETTING_MAXIMUM_WORDS => ANSWER_OPEN_MAX_WORDS,
            SETTING_MINIMUM_SELECTED => "",
            SETTING_MAXIMUM_SELECTED => "",
            SETTING_EXACT_SELECTED => "",
            SETTING_INVALID_SELECTED => "",
            SETTING_INVALIDSUB_SELECTED => "",
            SETTING_MINIMUM_RANKED => "",
            SETTING_MAXIMUM_RANKED => "",
            SETTING_EXACT_RANKED => "",
            SETTING_RANK_COLUMN => RANK_COLUMN_ONE,
            SETTING_MAXIMUM_CALENDAR => ANSWER_CALENDAR_MAXSELECTED,
            SETTING_KNOB_ROTATION => KNOB_ROTATION_CLOCKWISE,
            SETTING_SLIDER_INCREMENT => DEFAULT_INCREMENT,
            SETTING_SLIDER_TOOLTIP => TOOLTIP_YES,
            SETTING_SLIDER_ORIENTATION => ORIENTATION_HORIZONTAL,
            SETTING_SLIDER_TEXTBOX => TEXTBOX_YES,
            SETTING_SLIDER_TEXTBOX_LABEL => Language::labelSliderTextBox(),
            SETTING_SLIDER_FORMATER => "return value;",
            SETTING_SLIDER_PRESELECTION => SLIDER_PRESELECTION_YES,
            SETTING_GROUP_EXACT_REQUIRED => "",
            SETTING_GROUP_MINIMUM_REQUIRED => "",
            SETTING_GROUP_MAXIMUM_REQUIRED => "",
            SETTING_GROUP_EXCLUSIVE => GROUP_NO,
            SETTING_GROUP_INCLUSIVE => GROUP_NO,
            SETTING_GROUP_SAME_REQUIRED => GROUP_NO,
            SETTING_GROUP_UNIQUE_REQUIRED => GROUP_NO,
            SETTING_INPUT_MASK_ENABLED => INPUT_MASK_NO,
            SETTING_INPUT_MASK_PLACEHOLDER => "",
            SETTING_HEADER_SCROLL_DISPLAY => TABLE_SCROLL,
            SETTING_HEADER_FIXED => TABLE_NO,
            SETTING_FOOTER_DISPLAY => ENUM_FOOTER_NO,
            SETTING_ENUMERATED_ORIENTATION => ORIENTATION_VERTICAL,
            SETTING_ENUMERATED_BORDERED => ENUMERATED_YES,
            SETTING_ENUMERATED_SPLIT => ENUMERATED_NO,
            SETTING_ENUMERATED_ORDER => ORDER_OPTION_FIRST,
            SETTING_ENUMERATED_CUSTOM => "",
            SETTING_PLACEHOLDER => "",
            SETTING_ENUMERATED_TEXTBOX => TEXTBOX_NO,
            SETTING_ENUMERATED_TEXTBOX_LABEL => Language::labelEnumeratedTextBox(),
            SETTING_ENUMERATED_LABEL => ENUMERATED_LABEL_LABEL_ONLY,
            SETTING_ENUMERATED_CLICK_LABEL => CLICK_LABEL_YES,
            SETTING_KEYBOARD_BINDING_ENABLED => KEYBOARD_BINDING_NO,
            SETTING_KEYBOARD_BINDING_BACK => Language::keyboardBindingBack(),
            SETTING_KEYBOARD_BINDING_NEXT => Language::keyboardBindingNext(),
            SETTING_KEYBOARD_BINDING_DK => Language::keyboardBindingDK(),
            SETTING_KEYBOARD_BINDING_RF => Language::keyboardBindingRF(),
            SETTING_KEYBOARD_BINDING_NA => Language::keyboardBindingNA(),
            SETTING_KEYBOARD_BINDING_UPDATE => Language::keyboardBindingUpdate(),
            SETTING_KEYBOARD_BINDING_REMARK => Language::keyboardBindingRemark(),
            SETTING_KEYBOARD_BINDING_CLOSE => Language::keyboardBindingClose(),
            SETTING_SHOW_SECTION_HEADER => SECTIONHEADER_YES,
            SETTING_SHOW_SECTION_FOOTER => SECTIONFOOTER_YES,
            SETTING_SCREENDUMPS => SCREENDUMPS_NO,
            SETTING_PARADATA => Config::logParadata(),
            SETTING_ACCESS_REENTRY_ACTION => REENTRY_SAME_SCREEN,
            SETTING_ACCESS_REENTRY_PRELOAD_REDO => PRELOAD_REDO_NO,
            SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION => AFTER_COMPLETION_NO_REENTRY,
            SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO => PRELOAD_REDO_NO,
            SETTING_DATA_KEEP_ONLY => DATA_KEEP_ONLY_NO,
            SETTING_DATA_KEEP => DATA_KEEP_YES,
            SETTING_DATA_INPUTMASK => DATA_INPUTMASK_YES,
            SETTING_DATA_SKIP => DATA_SKIP_NO,
            SETTING_DATA_SKIP_POSTFIX => "_skip",
            SETTING_OUTPUT_SETOFENUMERATED => SETOFENUMERATED_BINARY,
            SETTING_OUTPUT_VALUELABEL_WIDTH => VALUELABEL_WIDTH_FULL,
            SETTING_DATA_STORE_LOCATION => STORE_LOCATION_INTERNAL,
            SETTING_DATA_STORE_LOCATION_EXTERNAL => "",
            SETTING_SLIDER_LABEL_PLACEMENT => SLIDER_LABEL_PLACEMENT_BOTTOM,
            SETTING_DKRFNA => Config::individualDKRFNA(),
            SETTING_DKRFNA_SINGLE => Config::individualDKRFNASingle(),
            SETTING_DKRFNA_INLINE => Config::individualDKRFNAInline(),
            SETTING_LOGIN_ERROR => Language::messageCheckLoginCode(),
            SETTING_TIMEOUT => Config::warnTimeout(),
            SETTING_TIMEOUT_LENGTH => Config::sessionTimeout(),
            SETTING_TIMEOUT_LOGOUT => Config::sessionLogoutURL(),
            SETTING_TIMEOUT_REDIRECT => Config::sessionRedirectURL(),
            SETTING_TIMEOUT_ALIVE_BUTTON => Language::sessionExpiredKeepAliveButton(),
            SETTING_TIMEOUT_LOGOUT_BUTTON => Language::sessionExpiredLogoutButton(),
            SETTING_TIMEOUT_TITLE => Language::sessionExpiredTitle(),
            SETTING_VALIDATE_ASSIGNMENT => VALIDATE_ASSIGNMENT_NO,
            SETTING_APPLY_CHECKS => APPLY_CHECKS_NO,
            SETTING_TABLE_MOBILE => GROUP_YES,
            SETTING_TABLE_MOBILE_LABELS => GROUP_YES,
            SETTING_ENUMERATED_TEXTBOX_POSTTEXT => "",
            SETTING_SLIDER_TEXTBOX_POSTTEXT => "",
            SETTING_COMBOBOX_DEFAULT => Language::labelDropdownNothing(),
            SETTING_SPINNER_TYPE => SPINNER_TYPE_HORIZONTAL,
            SETTING_SPINNER => SPINNER_NO,
            SETTING_SPINNER_UP => "glyphicon glyphicon-chevron-plus",
            SETTING_SPINNER_DOWN => "glyphicon glyphicon-chevron-minus",
            SETTING_SPINNER_STEP => 1,
            SETTING_TEXTBOX_MANUAL => MANUAL_YES,
            SETTING_TABLE_MOBILE_LABELS => MOBILE_LABEL_YES,
            SETTING_DATE_DEFAULT_VIEW => "",
            SETTING_DATETIME_COLLAPSE => DATE_COLLAPSE_YES,
            SETTING_DATETIME_SIDE_BY_SIDE => DATE_SIDE_BY_SIDE_NO
        );
    }

    function getDefaults() {
        if ($this->defaults == null) {
            $this->setDefaults();
        }
        return $this->defaults;
    }

    function getDefaultValue($setting) {

        /* for survey pages always return the found setting value */
        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SURVEY) {
            //if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_SURVEY_RETRIEVAL) {
            if ($this->defaults == null) {
                $this->setDefaults();
            }
            return $this->defaults[$setting];
        }
        /* for admin pages return found setting IF survey editing;
         * otherwise return 'Follow survey'
         */ else {

            /* current page */
            $page = $_SESSION['LASTPAGE'];

            // we are editing a variable, type or group
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                if (contains($page, "variable") || contains($page, "type") || contains($page, "group")) {
                    return SETTING_FOLLOW_GENERIC;
                }
            }
            if ($this->defaults == null) {
                $this->setDefaults();
            }
            return $this->defaults[$setting];
        }
    }

    /* output functions */
    function getStoreLocation($default = true) {
        if ($this->getSettingValue(SETTING_DATA_STORE_LOCATION) != "") {
            return $this->getSettingValue(SETTING_DATA_STORE_LOCATION, $default);
        };
        return $this->getDefaultValue(SETTING_DATA_STORE_LOCATION);
    }

    function setStoreLocation($value) {
        $this->setSettingValue(SETTING_DATA_STORE_LOCATION, $value);
    }
    
    function getStoreLocationExternal($default = true) {
        if ($this->getSettingValue(SETTING_DATA_STORE_LOCATION_EXTERNAL) != "") {
            return $this->getSettingValue(SETTING_DATA_STORE_LOCATION_EXTERNAL, $default);
        }
        return $this->getDefaultValue(SETTING_DATA_STORE_LOCATION_EXTERNAL);
    }

    function setStoreLocationExternal($value) {
        $this->setSettingValue(SETTING_DATA_STORE_LOCATION_EXTERNAL, $value);
    }
    
    function getOutputValueLabelWidth($default = true) {
        if ($this->getSettingValue(SETTING_OUTPUT_VALUELABEL_WIDTH) != "") {
            return $this->getSettingValue(SETTING_OUTPUT_VALUELABEL_WIDTH, $default);
        }
        return $this->getDefaultValue(SETTING_OUTPUT_VALUELABEL_WIDTH);
    }

    function setOutputValueLabelWidth($value) {
        $this->setSettingValue(SETTING_OUTPUT_VALUELABEL_WIDTH, $value);
    }

    function getOutputSetOfEnumeratedBinary($default = true) {
        if ($this->getSettingValue(SETTING_OUTPUT_SETOFENUMERATED) != "") {
            return $this->getSettingValue(SETTING_OUTPUT_SETOFENUMERATED, $default);
        }
        return $this->getDefaultValue(SETTING_OUTPUT_SETOFENUMERATED);
    }

    function setOutputSetOfEnumeratedBinary($value) {
        $this->setSettingValue(SETTING_OUTPUT_SETOFENUMERATED, $value);
    }

    function getDataKeepOnly($default = true) {
        if ($this->getSettingValue(SETTING_DATA_KEEP_ONLY) != "") {
            return $this->getSettingValue(SETTING_DATA_KEEP_ONLY, $default);
        }
        return $this->getDefaultValue(SETTING_DATA_KEEP_ONLY);
    }

    function setDataKeepOnly($value) {
        $this->setSettingValue(SETTING_DATA_KEEP_ONLY, $value);
    }

    function getDataKeep($default = true) {
        if ($this->getSettingValue(SETTING_DATA_KEEP) != "") {
            return $this->getSettingValue(SETTING_DATA_KEEP, $default);
        }
        return $this->getDefaultValue(SETTING_DATA_KEEP);
    }

    function setDataKeep($value) {
        $this->setSettingValue(SETTING_DATA_KEEP, $value);
    }

    function getDataSkipVariable($default = true) {
        if ($this->getSettingValue(SETTING_DATA_SKIP) != "") {
            return $this->getSettingValue(SETTING_DATA_SKIP, $default);
        }
        return $this->getDefaultValue(SETTING_DATA_SKIP);
    }

    function setDataSkipVariable($value) {
        $this->setSettingValue(SETTING_DATA_SKIP, $value);
    }

    function getDataSkipVariablePostFix($default = true) {
        if ($this->getSettingValue(SETTING_DATA_SKIP_POSTFIX) != "") {
            return $this->getSettingValue(SETTING_DATA_SKIP_POSTFIX, $default);
        }
        return $this->getDefaultValue(SETTING_DATA_SKIP_POSTFIX);
    }

    function setDataSkipVariablePostFix($value) {
        $this->setSettingValue(SETTING_DATA_SKIP_POSTFIX, $value);
    }

    function getDataInputMask($default = true) {
        if ($this->getSettingValue(SETTING_DATA_INPUTMASK) != "") {
            return $this->getSettingValue(SETTING_DATA_INPUTMASK, $default);
        }
        return $this->getDefaultValue(SETTING_DATA_INPUTMASK);
    }

    function setDataInputMask($value) {
        $this->setSettingValue(SETTING_DATA_INPUTMASK, $value);
    }

    function getScreendumpStorage($default = true) {
        if ($this->getSettingValue(SETTING_SCREENDUMPS) != "") {
            return $this->getSettingValue(SETTING_SCREENDUMPS, $default);
        }
        return $this->getDefaultValue(SETTING_SCREENDUMPS);
    }

    function setScreendumpStorage($text) {
        $this->setSettingValue(SETTING_SCREENDUMPS, $text);
    }

    function getParadata($default = true) {
        if ($this->getSettingValue(SETTING_PARADATA) != "") {
            return $this->getSettingValue(SETTING_PARADATA, $default);
        }
        return $this->getDefaultValue(SETTING_PARADATA);
    }

    function setParadata($text) {
        $this->setSettingValue(SETTING_PARADATA, $text);
    }

    function getHidden($default = true) {
        if ($this->getSettingValue(SETTING_HIDDEN) != "") {
            return $this->getSettingValue(SETTING_HIDDEN, $default);
        }
        return $this->getDefaultValue(SETTING_HIDDEN);
    }

    function setHidden($text) {
        $this->setSettingValue(SETTING_HIDDEN, $text);
    }

    function getHiddenPaperVersion($default = true) {
        if ($this->getSettingValue(SETTING_HIDDEN_PAPER_VERSION) != "") {
            return $this->getSettingValue(SETTING_HIDDEN_PAPER_VERSION, $default);
        }
        return $this->getDefaultValue(SETTING_HIDDEN_PAPER_VERSION);
    }

    function setHiddenPaperVersion($text) {
        $this->setSettingValue(SETTING_HIDDEN_PAPER_VERSION, $text);
    }

    function getHiddenRouting($default = true) {
        if ($this->getSettingValue(SETTING_HIDDEN_ROUTING) != "") {
            return $this->getSettingValue(SETTING_HIDDEN_ROUTING, $default);
        }
        return $this->getDefaultValue(SETTING_HIDDEN_ROUTING);
    }

    function setHiddenRouting($text) {
        $this->setSettingValue(SETTING_HIDDEN_ROUTING, $text);
    }

    function getHiddenTranslation($default = true) {
        if ($this->getSettingValue(SETTING_HIDDEN_TRANSLATION) != "") {
            return $this->getSettingValue(SETTING_HIDDEN_TRANSLATION, $default);
        }
        return $this->getDefaultValue(SETTING_HIDDEN_TRANSLATION);
    }

    function setHiddenTranslation($text) {
        $this->setSettingValue(SETTING_HIDDEN_TRANSLATION, $text);
    }

    /* general functions */

    function getKeep($default = true) {
        if ($this->getSettingValue(SETTING_KEEP) != "") {
            return $this->getSettingValue(SETTING_KEEP, $default);
        }
        return $this->getDefaultValue(SETTING_KEEP);
    }

    function setKeep($text) {
        $this->setSettingValue(SETTING_KEEP, $text);
    }

    function getArray($default = true) {
        if ($this->getSettingValue(SETTING_ARRAY) != "") {
            return $this->getSettingValue(SETTING_ARRAY, $default);
        }
        return $this->getDefaultValue(SETTING_ARRAY);
    }

    function setArray($text) {
        $this->setSettingValue(SETTING_ARRAY, $text);
    }

    /* section header/footer functions */

    function getShowSectionHeader($default = true) {
        if ($this->getSettingValue(SETTING_SHOW_SECTION_HEADER) != "") {
            return $this->getSettingValue(SETTING_SHOW_SECTION_HEADER, $default);
        }
        return $this->getDefaultValue(SETTING_SHOW_SECTION_HEADER);
    }

    function setShowSectionHeader($text) {
        $this->setSettingValue(SETTING_SHOW_SECTION_HEADER, $text);
    }

    function getShowSectionFooter($default = true) {
        if ($this->getSettingValue(SETTING_SHOW_SECTION_FOOTER) != "") {
            return $this->getSettingValue(SETTING_SHOW_SECTION_FOOTER, $default);
        }
        return $this->getDefaultValue(SETTING_SHOW_SECTION_FOOTER);
    }

    function setShowSectionFooter($text) {
        $this->setSettingValue(SETTING_SHOW_SECTION_FOOTER, $text);
    }

    /* display functions */

    function getPlaceholder($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_PLACEHOLDER, $default) != "") {
            return $this->getSettingValue(SETTING_PLACEHOLDER, $default);
        }
        return $this->getDefaultValue(SETTING_PLACEHOLDER);
    }

    function setPlaceholder($text) {
        $this->setSettingValue(SETTING_PLACEHOLDER, $text);
    }

    function getTableStriped($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_TABLE_STRIPED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_TABLE_STRIPED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_TABLE_STRIPED);
    }

    function setTableStriped($text) {

        $this->setSettingValue(SETTING_GROUP_TABLE_STRIPED, $text);
    }

    function getTableBordered($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_TABLE_BORDERED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_TABLE_BORDERED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_TABLE_BORDERED);
    }

    function setTableBordered($text) {

        $this->setSettingValue(SETTING_GROUP_TABLE_BORDERED, $text);
    }

    function getTableCondensed($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_TABLE_CONDENSED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_TABLE_CONDENSED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_TABLE_CONDENSED);
    }

    function setTableCondensed($text) {

        $this->setSettingValue(SETTING_GROUP_TABLE_CONDENSED, $text);
    }

    function getTableHovered($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_TABLE_HOVERED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_TABLE_HOVERED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_TABLE_HOVERED);
    }

    function setTableHovered($text) {

        $this->setSettingValue(SETTING_GROUP_TABLE_HOVERED, $text);
    }

    function getQuestionAlignment($default = true) {
        if ($this->getSettingValue(SETTING_QUESTION_ALIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_QUESTION_ALIGNMENT, $default);
        }
        return $this->getDefaultValue(SETTING_QUESTION_ALIGNMENT);
    }

    function setQuestionAlignment($value) {
        $this->setSettingValue(SETTING_QUESTION_ALIGNMENT, $value);
    }

    function getQuestionFormatting($default = true) {
        if ($this->getSettingValue(SETTING_QUESTION_FORMATTING, $default) != "") {
            return $this->getSettingValue(SETTING_QUESTION_FORMATTING, $default);
        }
        return $this->getDefaultValue(SETTING_QUESTION_FORMATTING);
    }

    function setQuestionFormatting($value) {
        $this->setSettingValue(SETTING_QUESTION_FORMATTING, $value);
    }

    function getAnswerAlignment($default = true) {
        if ($this->getSettingValue(SETTING_ANSWER_ALIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_ANSWER_ALIGNMENT, $default);
        }
        return $this->getDefaultValue(SETTING_ANSWER_ALIGNMENT);
    }

    function setAnswerAlignment($value) {
        $this->setSettingValue(SETTING_ANSWER_ALIGNMENT, $value);
    }

    function getAnswerFormatting($default = true) {
        if ($this->getSettingValue(SETTING_ANSWER_FORMATTING, $default) != "") {
            return $this->getSettingValue(SETTING_ANSWER_FORMATTING, $default);
        }
        return $this->getDefaultValue(SETTING_ANSWER_FORMATTING);
    }

    function setAnswerFormatting($value) {
        $this->setSettingValue(SETTING_ANSWER_FORMATTING, $value);
    }
    
    function getFooterDisplay($default = true) {
        if ($this->getSettingValue(SETTING_FOOTER_DISPLAY, $default) != "") {
            return $this->getSettingValue(SETTING_FOOTER_DISPLAY, $default);
        }
        return $this->getDefaultValue(SETTING_FOOTER_DISPLAY);
    }

    function setFooterDisplay($value) {
        $this->setSettingValue(SETTING_FOOTER_DISPLAY, $value);
    }

    function getHeaderAlignment($default = true) {
        if ($this->getSettingValue(SETTING_HEADER_ALIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_HEADER_ALIGNMENT, $default);
        }
        return $this->getDefaultValue(SETTING_HEADER_ALIGNMENT);
    }

    function setHeaderAlignment($value) {
        $this->setSettingValue(SETTING_HEADER_ALIGNMENT, $value);
    }

    function getHeaderFormatting($default = true) {
        if ($this->getSettingValue(SETTING_HEADER_FORMATTING, $default) != "") {
            return $this->getSettingValue(SETTING_HEADER_FORMATTING, $default);
        }
        return $this->getDefaultValue(SETTING_HEADER_FORMATTING);
    }

    function setHeaderFormatting($value) {
        $this->setSettingValue(SETTING_HEADER_FORMATTING, $value);
    }

    function getHeaderFixed($default = true) {
        if ($this->getSettingValue(SETTING_HEADER_FIXED, $default) != "") {
            return $this->getSettingValue(SETTING_HEADER_FIXED, $default);
        }
        return $this->getDefaultValue(SETTING_HEADER_FIXED);
    }

    function setHeaderFixed($value) {
        $this->setSettingValue(SETTING_HEADER_FIXED, $value);
    }

    function getHeaderScrollDisplay($default = true) {
        if ($this->getSettingValue(SETTING_HEADER_SCROLL_DISPLAY, $default) != "") {
            return $this->getSettingValue(SETTING_HEADER_SCROLL_DISPLAY, $default);
        }
        return $this->getDefaultValue(SETTING_HEADER_SCROLL_DISPLAY);
    }

    function setHeaderScrollDisplay($value) {
        $this->setSettingValue(SETTING_HEADER_SCROLL_DISPLAY, $value);
    }

    function getQuestionColumnWidth($default = true) {
        if ($this->getSettingValue(SETTING_QUESTION_COLUMN_WIDTH, $default) != "") {
            return $this->getSettingValue(SETTING_QUESTION_COLUMN_WIDTH, $default);
        }
        return $this->getDefaultValue(SETTING_QUESTION_COLUMN_WIDTH);
    }

    function setQuestionColumnWidth($value) {
        $this->setSettingValue(SETTING_QUESTION_COLUMN_WIDTH, $value);
    }

    function getTableWidth($default = true) {
        if ($this->getSettingValue(SETTING_TABLE_WIDTH, $default) != "") {
            return $this->getSettingValue(SETTING_TABLE_WIDTH, $default);
        }
        return $this->getDefaultValue(SETTING_TABLE_WIDTH);
    }

    function setTableWidth($value) {
        $this->setSettingValue(SETTING_TABLE_WIDTH, $value);
    }

    function getTableMobile($default = true) {
        if ($this->getSettingValue(SETTING_TABLE_MOBILE, $default) != "") {
            return $this->getSettingValue(SETTING_TABLE_MOBILE, $default);
        }
        return $this->getDefaultValue(SETTING_TABLE_MOBILE);
    }

    function setTableMobile($value) {
        $this->setSettingValue(SETTING_TABLE_MOBILE, $value);
    }

    function getTableMobileLabels($default = true) {
        if ($this->getSettingValue(SETTING_TABLE_MOBILE_LABELS, $default) != "") {
            return $this->getSettingValue(SETTING_TABLE_MOBILE_LABELS, $default);
        }
        return $this->getDefaultValue(SETTING_TABLE_MOBILE_LABELS);
    }

    function setTableMobileLabels($value) {
        $this->setSettingValue(SETTING_TABLE_MOBILE_LABELS, $value);
    }

    function getButtonAlignment($default = true) {
        if ($this->getSettingValue(SETTING_BUTTON_ALIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_BUTTON_ALIGNMENT, $default);
        }
        return $this->getDefaultValue(SETTING_BUTTON_ALIGNMENT);
    }

    function setButtonAlignment($value) {
        $this->setSettingValue(SETTING_BUTTON_ALIGNMENT, $value);
    }

    function getButtonFormatting($default = true) {
        if ($this->getSettingValue(SETTING_BUTTON_FORMATTING, $default) != "") {
            return $this->getSettingValue(SETTING_BUTTON_FORMATTING, $default);
        }
        return $this->getDefaultValue(SETTING_BUTTON_FORMATTING);
    }

    function setButtonFormatting($value) {
        $this->setSettingValue(SETTING_BUTTON_FORMATTING, $value);
    }

    /* button functions */

    function getShowBackButton($default = true) {
        if ($this->getSettingValue(SETTING_BACK_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_BACK_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_BACK_BUTTON);
    }

    function getShowNextButton($default = true) {
        if ($this->getSettingValue(SETTING_NEXT_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_NEXT_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_NEXT_BUTTON);
    }

    function getShowDKButton($default = true) {
        if ($this->getSettingValue(SETTING_DK_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_DK_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_DK_BUTTON);
    }

    function getShowRFButton($default = true) {
        if ($this->getSettingValue(SETTING_RF_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_RF_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_RF_BUTTON);
    }

    function getShowUpdateButton($default = true) {
        if ($this->getSettingValue(SETTING_UPDATE_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_UPDATE_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_UPDATE_BUTTON);
    }

    function getShowNAButton($default = true) {
        if ($this->getSettingValue(SETTING_NA_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_NA_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_NA_BUTTON);
    }

    function getShowRemarkButton($default = true) {
        if ($this->getSettingValue(SETTING_REMARK_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_REMARK_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_REMARK_BUTTON);
    }

    function getShowRemarkSaveButton($default = true) {
        if ($this->getSettingValue(SETTING_REMARK_SAVE_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_REMARK_SAVE_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_REMARK_SAVE_BUTTON);
    }

    function getShowCloseButton($default = true) {
        if ($this->getSettingValue(SETTING_CLOSE_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_CLOSE_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_CLOSE_BUTTON);
    }

    function setShowNAButton($value) {
        $this->setSettingValue(SETTING_NA_BUTTON, $value);
    }

    function setShowBackButton($value) {
        $this->setSettingValue(SETTING_BACK_BUTTON, $value);
    }

    function setShowNextButton($value) {
        $this->setSettingValue(SETTING_NEXT_BUTTON, $value);
    }

    function setShowDKButton($value) {
        $this->setSettingValue(SETTING_DK_BUTTON, $value);
    }

    function setShowRFButton($value) {
        $this->setSettingValue(SETTING_RF_BUTTON, $value);
    }

    function setShowUpdateButton($value) {
        $this->setSettingValue(SETTING_UPDATE_BUTTON, $value);
    }

    function setShowRemarkButton($value) {
        $this->setSettingValue(SETTING_REMARK_BUTTON, $value);
    }

    function setShowRemarkSaveButton($value) {
        $this->setSettingValue(SETTING_REMARK_SAVE_BUTTON, $value);
    }

    function setShowCloseButton($value) {
        $this->setSettingValue(SETTING_CLOSE_BUTTON, $value);
    }

    function getLabelBackButton($default = true) {
        $val = $this->getSettingValue(SETTING_BACK_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_BACK_BUTTON_LABEL);
    }

    function getLabelNextButton($default = true) {
        $val = $this->getSettingValue(SETTING_NEXT_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_NEXT_BUTTON_LABEL);
    }

    function getLabelDKButton($default = true) {
        $val = $this->getSettingValue(SETTING_DK_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_DK_BUTTON_LABEL);
    }

    function getLabelRFButton($default = true) {
        $val = $this->getSettingValue(SETTING_RF_BUTTON_LABEL, $default);
        if ($val != "") {
            return $this->getSettingValue(SETTING_RF_BUTTON_LABEL, $default);
        }
        return $this->getDefaultValue(SETTING_RF_BUTTON_LABEL);
    }

    function getLabelNAButton($default = true) {
        $val = $this->getSettingValue(SETTING_NA_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_NA_BUTTON_LABEL);
    }

    function getLabelUpdateButton($default = true) {
        $val = $this->getSettingValue(SETTING_UPDATE_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_UPDATE_BUTTON_LABEL);
    }

    function getLabelRemarkButton($default = true) {
        $val = $this->getSettingValue(SETTING_REMARK_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_REMARK_BUTTON_LABEL);
    }

    function getLabelRemarkSaveButton($default = true) {
        $val = $this->getSettingValue(SETTING_REMARK_SAVE_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_REMARK_SAVE_BUTTON_LABEL);
    }

    function getLabelCloseButton($default = true) {
        $val = $this->getSettingValue(SETTING_CLOSE_BUTTON_LABEL, $default);
        if ($val != "") {
            return $val;
        }
        return $this->getDefaultValue(SETTING_CLOSE_BUTTON_LABEL);
    }

    function setLabelBackButton($value) {
        $this->setSettingValue(SETTING_BACK_BUTTON_LABEL, $value);
    }

    function setLabelNextButton($value) {
        $this->setSettingValue(SETTING_NEXT_BUTTON_LABEL, $value);
    }

    function setLabelDKButton($value) {
        $this->setSettingValue(SETTING_DK_BUTTON_LABEL, $value);
    }

    function setLabelRFButton($value) {
        $this->setSettingValue(SETTING_RF_BUTTON_LABEL, $value);
    }

    function setLabelNAButton($value) {
        $this->setSettingValue(SETTING_NA_BUTTON_LABEL, $value);
    }

    function setLabelUpdateButton($value) {
        $this->setSettingValue(SETTING_UPDATE_BUTTON_LABEL, $value);
    }

    function setLabelRemarkButton($value) {
        $this->setSettingValue(SETTING_REMARK_BUTTON_LABEL, $value);
    }

    function setLabelCloseButton($value) {
        $this->setSettingValue(SETTING_CLOSE_BUTTON_LABEL, $value);
    }

    function setLabelRemarkSaveButton($value) {
        $this->setSettingValue(SETTING_REMARK_SAVE_BUTTON_LABEL, $value);
    }

    /* progressbar functions */

    function getShowProgressBar($default = true) {
        if ($this->getSettingValue(SETTING_PROGRESSBAR_SHOW, $default) != "") {
            return $this->getSettingValue(SETTING_PROGRESSBAR_SHOW, $default);
        }
        return $this->getDefaultValue(SETTING_PROGRESSBAR_SHOW);
    }

    function setShowProgressBar($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_SHOW, $value);
    }

    function getProgressBarType($default = true) {
        if ($this->getSettingValue(SETTING_PROGRESSBAR_TYPE, $default) != "") {
            return $this->getSettingValue(SETTING_PROGRESSBAR_TYPE, $default);
        }
        return $this->getDefaultValue(SETTING_PROGRESSBAR_TYPE);
    }

    function setProgressBarType($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_TYPE, $value);
    }

    function getProgressBarFillColor($default = true) {
        if ($this->getSettingValue(SETTING_PROGRESSBAR_FILLED_COLOR, $default) != "") {
            return $this->getSettingValue(SETTING_PROGRESSBAR_FILLED_COLOR, $default);
        }
        return $this->getDefaultValue(SETTING_PROGRESSBAR_FILLED_COLOR);
    }

    function setProgressBarFillColor($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_FILLED_COLOR, $value);
    }

    function getProgressBarRemainColor($default = true) {
        if ($this->getSettingValue(SETTING_PROGRESSBAR_REMAIN_COLOR, $default) != "") {
            return $this->getSettingValue(SETTING_PROGRESSBAR_REMAIN_COLOR, $default);
        }
        return $this->getDefaultValue(SETTING_PROGRESSBAR_REMAIN_COLOR);
    }

    function setProgressBarRemainColor($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_REMAIN_COLOR, $value);
    }

    function getProgressBarWidth($default = true) {
        if ($this->getSettingValue(SETTING_PROGRESSBAR_WIDTH, $default) != "") {
            return $this->getSettingValue(SETTING_PROGRESSBAR_WIDTH, $default);
        }
        return $this->getDefaultValue(SETTING_PROGRESSBAR_WIDTH);
    }

    function setProgressBarWidth($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_WIDTH, $value);
    }

    /* assistance functions */

    function getErrorPlacement($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_PLACEMENT, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_PLACEMENT, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_PLACEMENT);
    }

    function setErrorPlacement($value) {
        $this->setSettingValue(SETTING_ERROR_PLACEMENT, $value);
    }

    function getLoginError($default = true) {
        if ($this->getSettingValue(SETTING_LOGIN_ERROR, $default) != "") {
            return $this->getSettingValue(SETTING_LOGIN_ERROR, $default);
        }
        return $this->getDefaultValue(SETTING_LOGIN_ERROR);
    }

    function setLoginError($text) {
        $this->setSettingValue(SETTING_LOGIN_ERROR, $text);
    }

    function getEmptyMessage($default = true) {
        if ($this->getSettingValue(SETTING_EMPTY_MESSAGE, $default) != "") {
            return $this->getSettingValue(SETTING_EMPTY_MESSAGE, $default);
        }
        return $this->getDefaultValue(SETTING_EMPTY_MESSAGE);
    }

    function setEmptyMessage($text) {
        $this->setSettingValue(SETTING_EMPTY_MESSAGE, $text);
    }

    function getErrorMessageInteger($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INTEGER, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INTEGER, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INTEGER);
    }

    function setErrorMessageInteger($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INTEGER, $text);
    }

    function getErrorMessageDouble($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_DOUBLE, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_DOUBLE, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_DOUBLE);
    }

    function setErrorMessageDouble($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_DOUBLE, $text);
    }

    function getErrorMessagePattern($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_PATTERN, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_PATTERN, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_PATTERN);
    }

    function setErrorMessagePattern($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_PATTERN, $text);
    }

    function getErrorMessageRange($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_RANGE, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_RANGE, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_RANGE);
    }

    function setErrorMessageRange($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_RANGE, $text);
    }

    function getErrorMessageMaximumCalendar() {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR);
    }

    function setErrorMessageMaximumCalendar($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR, $text);
    }

    function getErrorMessageMinimumLength($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH);
    }

    function setErrorMessageMinimumLength($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH, $text);
    }

    function getErrorMessageMaximumLength($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH);
    }

    function setErrorMessageMaximumLength($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH, $text);
    }

    function getErrorMessageMinimumWords($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS);
    }

    function setErrorMessageMinimumWords($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS, $text);
    }

    function getErrorMessageMaximumWords($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS);
    }

    function setErrorMessageMaximumWords($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS, $text);
    }

    function getErrorMessageSelectMinimum($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT);
    }

    function setErrorMessageSelectMinimum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT, $text);
    }

    function getErrorMessageSelectMaximum($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT);
    }

    function setErrorMessageSelectMaximum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT, $text);
    }

    function getErrorMessageSelectExact($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_SELECT, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_SELECT, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_EXACT_SELECT);
    }

    function setErrorMessageSelectExact($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXACT_SELECT, $text);
    }

    function getErrorMessageRankMinimum($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK);
    }

    function setErrorMessageRankMinimum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK, $text);
    }

    function getErrorMessageRankMaximum($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK);
    }

    function setErrorMessageRankMaximum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK, $text);
    }

    function getErrorMessageRankExact($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_RANK, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_RANK, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_EXACT_RANK);
    }

    function setErrorMessageRankExact($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXACT_RANK, $text);
    }

    function getErrorMessageSelectInvalidSubset($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT);
    }

    function setErrorMessageSelectInvalidSubset($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT, $text);
    }

    function getErrorMessageSelectInvalidSet($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SELECT, $default) != "") {
            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SELECT, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INVALID_SELECT);
    }

    function setErrorMessageSelectInvalidSet($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INVALID_SELECT, $text);
    }

    function getErrorMessageInlineExclusive($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE);
    }

    function setErrorMessageInlineExclusive($value) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE, $value);
    }

    function getErrorMessageInlineInclusive($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE);
    }

    function setErrorMessageInlineInclusive($value) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE, $value);
    }

    function getErrorMessageInlineMinimumRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED);
    }

    function setErrorMessageInlineMinimumRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED, $text);
    }

    function getErrorMessageInlineMaximumRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED);
    }

    function setErrorMessageInlineMaximumRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED, $text);
    }

    function getErrorMessageInlineExactRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED);
    }

    function setErrorMessageInlineExactRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED, $text);
    }

    function getErrorMessageExclusive($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_EXCLUSIVE, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_EXCLUSIVE, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_EXCLUSIVE);
    }

    function setErrorMessageExclusive($value) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXCLUSIVE, $value);
    }

    function getErrorMessageInclusive($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INCLUSIVE, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INCLUSIVE, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INCLUSIVE);
    }

    function setErrorMessageInclusive($value) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INCLUSIVE, $value);
    }

    function getErrorMessageMinimumRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED);
    }

    function setErrorMessageMinimumRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED, $text);
    }

    function getErrorMessageMaximumRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED);
    }

    function setErrorMessageMaximumRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED, $text);
    }

    function getErrorMessageExactRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_EXACT_REQUIRED);
    }

    function setErrorMessageExactRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXACT_REQUIRED, $text);
    }

    function getErrorMessageUniqueRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED, $default);
        }
//echo 'hhhh';
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED);
    }

    function setErrorMessageUniqueRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED, $text);
    }

    function getErrorMessageSameRequired($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_SAME_REQUIRED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_SAME_REQUIRED, $default);
        }

        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_SAME_REQUIRED);
    }

    function setErrorMessageSameRequired($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_SAME_REQUIRED, $text);
    }

    function getErrorMessageInlineAnswered($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED);
    }

    function setErrorMessageInlineAnswered($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED, $text);
    }

    function getErrorMessageEnumeratedEntered($default = true) {
        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED);
    }

    function setErrorMessageEnumeratedEntered($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED, $text);
    }

    function getErrorMessageSetOfEnumeratedEntered($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED);
    }

    function setErrorMessageSetOfEnumeratedEntered($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED, $text);
    }

    function getErrorMessageComparisonEqualTo($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO);
    }

    function setErrorMessageComparisonEqualTo($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO, $text);
    }

    function getErrorMessageComparisonNotEqualTo($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO);
    }

    function setErrorMessageComparisonNotEqualTo($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO, $text);
    }

    function getErrorMessageComparisonEqualToIgnoreCase($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE);
    }

    function setErrorMessageComparisonEqualToIgnoreCase($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE, $text);
    }

    function getErrorMessageComparisonNotEqualToIgnoreCase($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE);
    }

    function setErrorMessageComparisonNotEqualToIgnoreCase($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $text);
    }

    function getErrorMessageComparisonGreaterEqualTo($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO);
    }

    function setErrorMessageComparisonGreaterEqualTo($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO, $text);
    }

    function getErrorMessageComparisonGreater($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER);
    }

    function setErrorMessageComparisonGreater($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER, $text);
    }

    function getErrorMessageComparisonSmallerEqualTo($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO);
    }

    function setErrorMessageComparisonSmallerEqualTo($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO, $text);
    }

    function getErrorMessageComparisonSmaller($default = true) {

        if ($this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER, $default) != "") {

            return $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER, $default);
        }
        return $this->getDefaultValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER);
    }

    function setErrorMessageComparisonSmaller($text) {

        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER, $text);
    }

    /* validation functions */

    function getIfEmpty($default = true) {
        if ($this->getSettingValue(SETTING_IFEMPTY, $default) != "") {
            return $this->getSettingValue(SETTING_IFEMPTY, $default);
        }
        return $this->getDefaultValue(SETTING_IFEMPTY);
    }

    function setIfEmpty($value) {
        $this->setSettingValue(SETTING_IFEMPTY, $value);
    }

    function getIfError($default = true) {
        if ($this->getSettingValue(SETTING_IFERROR, $default) != "") {
            return $this->getSettingValue(SETTING_IFERROR, $default);
        }
        return $this->getDefaultValue(SETTING_IFERROR);
    }

    function setIfError($value) {
        $this->setSettingValue(SETTING_IFERROR, $value);
    }

    /* range functions */

    function getMinimum($default = true) {
        if ($this->getSettingValue(SETTING_MINIMUM_RANGE, $default) != "") {
            return $this->getSettingValue(SETTING_MINIMUM_RANGE, $default);
        }
        return $this->getDefaultValue(SETTING_MINIMUM_RANGE);
    }

    function setMinimum($value) {
        $this->setSettingValue(SETTING_MINIMUM_RANGE, $value);
    }

    function getOtherValues($default = true) {
        if ($this->getSettingValue(SETTING_OTHER_RANGE, $default) != "") {
            return $this->getSettingValue(SETTING_OTHER_RANGE, $default);
        }
        return $this->getDefaultValue(SETTING_OTHER_RANGE);
    }

    function setOtherValues($value) {
        $this->setSettingValue(SETTING_OTHER_RANGE, $value);
    }

    function getMaximum($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_RANGE, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_RANGE, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_RANGE);
    }

    function setMaximum($value) {
        $this->setSettingValue(SETTING_MAXIMUM_RANGE, $value);
    }

    /* set of enumerated functions */

    function getEnumeratedDisplay($default = true) {
        if ($this->getSettingValue(SETTING_ENUMERATED_ORIENTATION) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_ORIENTATION);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_ORIENTATION);
    }

    function setEnumeratedDisplay($value) {
        $this->setSettingValue(SETTING_ENUMERATED_ORIENTATION, $value);
    }

    function getEnumeratedCustom($default = true) {
        if ($this->getSettingValue(SETTING_ENUMERATED_CUSTOM) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_CUSTOM);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_CUSTOM);
    }

    function setEnumeratedCustom($value) {
        $this->setSettingValue(SETTING_ENUMERATED_CUSTOM, $value);
    }

    function getEnumeratedOrder($default = true) {
        if ($this->getSettingValue(SETTING_ENUMERATED_ORDER) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_ORDER);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_ORDER);
    }

    function setEnumeratedOrder($value) {
        $this->setSettingValue(SETTING_ENUMERATED_ORDER, $value);
    }

    function getEnumeratedSplit($default = true) {
        if ($this->getSettingValue(SETTING_ENUMERATED_SPLIT) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_SPLIT);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_SPLIT);
    }

    function setEnumeratedSplit($value) {
        $this->setSettingValue(SETTING_ENUMERATED_SPLIT, $value);
    }

    function getEnumeratedBordered($default = true) {
        if ($this->getSettingValue(SETTING_ENUMERATED_BORDERED) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_BORDERED);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_BORDERED);
    }

    function setEnumeratedBordered($value) {
        $this->setSettingValue(SETTING_ENUMERATED_BORDERED, $value);
    }

    function getEnumeratedTextbox($default = true) {
        if ($this->getSettingValue(SETTING_ENUMERATED_TEXTBOX) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_TEXTBOX);
    }

    function setEnumeratedTextbox($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX, $value);
    }

    function getEnumeratedTextBoxLabel() {
        if ($this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_TEXTBOX_LABEL);
    }

    function setEnumeratedTextboxLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, $value);
    }

    function getEnumeratedTextBoxPostText() {
        if ($this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT);
    }

    function setEnumeratedTextboxPostText($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT, $value);
    }

    function getEnumeratedLabel() {
        if ($this->getSettingValue(SETTING_ENUMERATED_LABEL) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_LABEL);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_LABEL);
    }

    function setEnumeratedLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_LABEL, $value);
    }

    function getEnumeratedClickLabel() {
        if ($this->getSettingValue(SETTING_ENUMERATED_CLICK_LABEL) != "") {
            return $this->getSettingValue(SETTING_ENUMERATED_CLICK_LABEL);
        }
        return $this->getDefaultValue(SETTING_ENUMERATED_CLICK_LABEL);
    }

    function setEnumeratedClickLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_CLICK_LABEL, $value);
    }

    function getMinimumSelected($default = true) {
        if ($this->getSettingValue(SETTING_MINIMUM_SELECTED, $default) != "") {
            return $this->getSettingValue(SETTING_MINIMUM_SELECTED, $default);
        }
        return $this->getDefaultValue(SETTING_MINIMUM_SELECTED);
    }

    function getExactSelected($default = true) {
        if ($this->getSettingValue(SETTING_EXACT_SELECTED, $default) != "") {
            return $this->getSettingValue(SETTING_EXACT_SELECTED, $default);
        }
        return $this->getDefaultValue(SETTING_EXACT_SELECTED);
    }

    function getMaximumSelected($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_SELECTED, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_SELECTED, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_SELECTED);
    }

    function getInvalidSelected($default = true) {
        if ($this->getSettingValue(SETTING_INVALID_SELECTED, $default) != "") {
            return $this->getSettingValue(SETTING_INVALID_SELECTED, $default);
        }
        return $this->getDefaultValue(SETTING_INVALID_SELECTED);
    }

    function getInvalidSubSelected($default = true) {
        if ($this->getSettingValue(SETTING_INVALIDSUB_SELECTED, $default) != "") {
            return $this->getSettingValue(SETTING_INVALIDSUB_SELECTED, $default);
        }
        return $this->getDefaultValue(SETTING_INVALIDSUB_SELECTED);
    }

    function setMinimumSelected($value) {
        $this->setSettingValue(SETTING_MINIMUM_SELECTED, $value);
    }

    function setExactSelected($value) {
        $this->setSettingValue(SETTING_EXACT_SELECTED, $value);
    }

    function setMaximumSelected($value) {
        $this->setSettingValue(SETTING_MAXIMUM_SELECTED, $value);
    }

    function setInvalidSelected($value) {
        $this->setSettingValue(SETTING_INVALID_SELECTED, $value);
    }

    function setInvalidSubSelected($value) {
        $this->setSettingValue(SETTING_INVALIDSUB_SELECTED, $value);
    }

    /* ranker function */

    function getRankColumn($default = true) {
        if ($this->getSettingValue(SETTING_RANK_COLUMN) != "") {
            return $this->getSettingValue(SETTING_RANK_COLUMN);
        }
        return $this->getDefaultValue(SETTING_RANK_COLUMN);
    }

    function setRankColumn($value) {
        $this->setSettingValue(SETTING_RANK_COLUMN, $value);
    }

    function getMinimumRanked($default = true) {
        if ($this->getSettingValue(SETTING_MINIMUM_RANKED, $default) != "") {
            return $this->getSettingValue(SETTING_MINIMUM_RANKED, $default);
        }
        return $this->getDefaultValue(SETTING_MINIMUM_RANKED);
    }

    function getExactRanked($default = true) {
        if ($this->getSettingValue(SETTING_EXACT_RANKED, $default) != "") {
            return $this->getSettingValue(SETTING_EXACT_RANKED, $default);
        }
        return $this->getDefaultValue(SETTING_EXACT_RANKED);
    }

    function getMaximumRanked($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_RANKED, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_RANKED, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_RANKED);
    }

    function setMinimumRanked($value) {
        $this->setSettingValue(SETTING_MINIMUM_RANKED, $value);
    }

    function setExactRanked($value) {
        $this->setSettingValue(SETTING_EXACT_RANKED, $value);
    }

    function setMaximumRanked($value) {
        $this->setSettingValue(SETTING_MAXIMUM_RANKED, $value);
    }

    /* date functions */

    function getMaximumDatesSelected($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_CALENDAR, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_CALENDAR, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_CALENDAR);
    }

    function setMaximumDatesSelected($value) {
        $this->setSettingValue(SETTING_MAXIMUM_CALENDAR, $value);
    }

    /* string and open functions */

    function getPattern($default = true) {
        if ($this->getSettingValue(SETTING_PATTERN, $default) != "") {
            return $this->getSettingValue(SETTING_PATTERN, $default);
        }
        return $this->getDefaultValue(SETTING_PATTERN);
    }

    function getMinimumLength($default = true) {
        if ($this->getSettingValue(SETTING_MINIMUM_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_MINIMUM_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_MINIMUM_LENGTH);
    }

    function getMaximumLength($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_LENGTH);
    }

    function getMinimumOpenLength($default = true) {
        if ($this->getSettingValue(SETTING_MINIMUM_OPEN_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_MINIMUM_OPEN_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_MINIMUM_OPEN_LENGTH);
    }

    function getMaximumOpenLength($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_OPEN_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_OPEN_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_OPEN_LENGTH);
    }

    function getMinimumWords($default = true) {
        if ($this->getSettingValue(SETTING_MINIMUM_WORDS, $default) != "") {
            return $this->getSettingValue(SETTING_MINIMUM_WORDS, $default);
        }
        return $this->getDefaultValue(SETTING_MINIMUM_WORDS);
    }

    function getMaximumWords($default = true) {
        if ($this->getSettingValue(SETTING_MAXIMUM_WORDS, $default) != "") {
            return $this->getSettingValue(SETTING_MAXIMUM_WORDS, $default);
        }
        return $this->getDefaultValue(SETTING_MAXIMUM_WORDS);
    }

    function setPattern($value) {
        $this->setSettingValue(SETTING_PATTERN, $value);
    }

    function setMinimumLength($value) {
        $this->setSettingValue(SETTING_MINIMUM_LENGTH, $value);
    }

    function setMaximumLength($value) {
        $this->setSettingValue(SETTING_MAXIMUM_LENGTH, $value);
    }

    function setMinimumOpenLength($value) {
        $this->setSettingValue(SETTING_MINIMUM_OPEN_LENGTH, $value);
    }

    function setMaximumOpenLength($value) {
        $this->setSettingValue(SETTING_MAXIMUM_OPEN_LENGTH, $value);
    }

    function setMinimumWords($value) {
        $this->setSettingValue(SETTING_MINIMUM_WORDS, $value);
    }

    function setMaximumWords($value) {
        $this->setSettingValue(SETTING_MAXIMUM_WORDS, $value);
    }

    /* input mask functions */

    function getInputMask($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_INPUT_MASK, $default) != "") {
            return $this->getSettingValue(SETTING_INPUT_MASK, $default);
        }
        return $this->getDefaultValue(SETTING_INPUT_MASK);
    }

    function getInputMaskEnabled($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_INPUT_MASK_ENABLED, $default) != "") {
            return $this->getSettingValue(SETTING_INPUT_MASK_ENABLED, $default);
        }
        return $this->getDefaultValue(SETTING_INPUT_MASK_ENABLED);
    }

    function getInputMaskPlaceholder($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_INPUT_MASK_PLACEHOLDER, $default) != "") {
            return $this->getSettingValue(SETTING_INPUT_MASK_PLACEHOLDER, $default);
        }
        return $this->getDefaultValue(SETTING_INPUT_MASK_PLACEHOLDER);
    }

    function setInputMask($value) {
        $this->setSettingValue(SETTING_INPUT_MASK, $value);
    }

    function setInputMaskEnabled($value) {
        $this->setSettingValue(SETTING_INPUT_MASK_ENABLED, $value);
    }

    function setInputMaskPlaceholder($value) {
        $this->setSettingValue(SETTING_INPUT_MASK_PLACEHOLDER, $value);
    }

    function getValidateAssignment($default = true) {
        if ($this->getSettingValue(SETTING_VALIDATE_ASSIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_VALIDATE_ASSIGNMENT, $default);
        }
        return $this->getDefaultValue(SETTING_VALIDATE_ASSIGNMENT);
    }

    function isValidateAssignment() {
        if ($this->getValidateAssignment() == VALIDATE_ASSIGNMENT_YES) {
            return true;
        }
        return false;
    }

    function setValidateAssignment($value) {
        $this->setSettingValue(SETTING_VALIDATE_ASSIGNMENT, $value);
    }

    function getApplyChecks($default = true) {
        if ($this->getSettingValue(SETTING_APPLY_CHECKS, $default) != "") {
            return $this->getSettingValue(SETTING_APPLY_CHECKS, $default);
        }
        return $this->getDefaultValue(SETTING_APPLY_CHECKS);
    }

    function isApplyChecks() {
        if ($this->getApplyChecks() == APPLY_CHECKS_YES) {
            return true;
        }
        return false;
    }

    function setApplyChecks($value) {
        $this->setSettingValue(SETTING_APPLY_CHECKS, $value);
    }

    /* date time picker functions */

    function getDateFormat($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DATE_FORMAT, $default) != "") {
            return $this->getSettingValue(SETTING_DATE_FORMAT, $default);
        }
        return "YYYY-MM-DD";
    }

    function setDateFormat($value) {
        $this->setSettingValue(SETTING_DATE_FORMAT, $value);
    }

    function getTimeFormat($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIME_FORMAT, $default) != "") {
            return $this->getSettingValue(SETTING_TIME_FORMAT, $default);
        }

        $ushourformat = Config::usFormatSurvey();
        $seconds = Config::secondsSurvey();
        if ($ushourformat == "true") {
            if ($seconds == "true") {
                $format = "hh:mm:ss A";
            } else {
                $format = "hh:mm A";
            }
        } else {
            if ($seconds == "true") {
                $format = "HH:mm:ss";
            } else {
                $format = "HH:mm";
            }
        }
        return $format;
    }

    function setTimeFormat($value) {
        $this->setSettingValue(SETTING_TIME_FORMAT, $value);
    }

    function getDateTimeFormat($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DATETIME_FORMAT, $default) != "") {
            return $this->getSettingValue(SETTING_DATETIME_FORMAT, $default);
        }

        $ushourformat = Config::usFormatSurvey();
        $seconds = Config::secondsSurvey();
        if ($ushourformat == "true") {
            if ($seconds == "true") {
                $format = "YYYY-MM-DD hh:mm:ss A";
            } else {
                $format = "YYYY-MM-DD hh:mm A";
            }
        } else {
            $format = "YYYY-MM-DD HH:mm:ss";
        }

        return $format;
    }

    function setDateTimeFormat($value) {
        $this->setSettingValue(SETTING_DATETIME_FORMAT, $value);
    }
    
    function getDateDefaultView($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DATE_DEFAULT_VIEW, $default) != "") {
            return $this->getSettingValue(SETTING_DATE_DEFAULT_VIEW, $default);
        }
        return $this->getDefaultValue(SETTING_DATE_DEFAULT_VIEW);
    }

    function setDateDefaultView($value) {
        $this->setSettingValue(SETTING_DATE_DEFAULT_VIEW, $value);
    }
    
    function getDateTimeCollapse($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DATETIME_COLLAPSE, $default) != "") {
            return $this->getSettingValue(SETTING_DATETIME_COLLAPSE, $default);
        }
        return $this->getDefaultValue(SETTING_DATETIME_COLLAPSE);
    }

    function setDateTimeCollapse($value) {
        $this->setSettingValue(SETTING_DATETIME_COLLAPSE, $value);
    }
    
    function getDateTimeSideBySide($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DATETIME_SIDE_BY_SIDE, $default) != "") {
            return $this->getSettingValue(SETTING_DATETIME_SIDE_BY_SIDE, $default);
        }
        return $this->getDefaultValue(SETTING_DATETIME_SIDE_BY_SIDE);
    }

    function setDateTimeSideBySide($value) {
        $this->setSettingValue(SETTING_DATETIME_SIDE_BY_SIDE, $value);
    }

    /* keyboard binding functions */

    function getKeyboardBindingEnabled($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_ENABLED, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_ENABLED, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_ENABLED);
    }

    function setKeyboardBindingEnabled($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_ENABLED, $value);
    }

    function getKeyboardBindingBack($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_BACK, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_BACK, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_BACK);
    }

    function setKeyboardBindingBack($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_BACK, $value);
    }

    function getKeyboardBindingNext($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_NEXT, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_NEXT, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_NEXT);
    }

    function setKeyboardBindingNext($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_NEXT, $value);
    }

    function getKeyboardBindingDK($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_DK, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_DK, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_DK);
    }

    function setKeyboardBindingDK($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_DK, $value);
    }

    function getKeyboardBindingRF($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_RF, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_RF, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_RF);
    }

    function setKeyboardBindingRF($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_RF, $value);
    }

    function getKeyboardBindingUpdate($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_UPDATE, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_UPDATE, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_UPDATE);
    }

    function setKeyboardBindingUpdate($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_UPDATE, $value);
    }

    function getKeyboardBindingNA($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_NA, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_NA, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_NA);
    }

    function setKeyboardBindingNA($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_NA, $value);
    }

    function getKeyboardBindingRemark($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_REMARK, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_REMARK, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_REMARK);
    }

    function setKeyboardBindingRemark($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_REMARK, $value);
    }

    function getKeyboardBindingClose($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_KEYBOARD_BINDING_CLOSE, $default) != "") {
            return $this->getSettingValue(SETTING_KEYBOARD_BINDING_CLOSE, $default);
        }
        return $this->getDefaultValue(SETTING_KEYBOARD_BINDING_CLOSE);
    }

    function setKeyboardBindingClose($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_CLOSE, $value);
    }

    function getIndividualDKRFNA($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DKRFNA, $default) != "") {
            return $this->getSettingValue(SETTING_DKRFNA, $default);
        }
        return $this->getDefaultValue(SETTING_DKRFNA);
    }

    function getIndividualDKRFNASingle($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DKRFNA_SINGLE, $default) != "") {
            return $this->getSettingValue(SETTING_DKRFNA_SINGLE, $default);
        }
        return $this->getDefaultValue(SETTING_DKRFNA_SINGLE);
    }

    function getIndividualDKRFNAInline($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_DKRFNA_INLINE, $default) != "") {
            return $this->getSettingValue(SETTING_DKRFNA_INLINE, $default);
        }
        return $this->getDefaultValue(SETTING_DKRFNA_INLINE);
    }

    function getTimeout($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT);
    }

    function getTimeoutLength($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT_LENGTH, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT_LENGTH, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT_LENGTH);
    }

    function getTimeoutTitle($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT_TITLE, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT_TITLE, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT_TITLE);
    }

    function getTimeoutLogoutURL($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT_LOGOUT, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT_LOGOUT, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT_LOGOUT);
    }

    function getTimeoutRedirectURL($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT_REDIRECT, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT_REDIRECT, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT_REDIRECT);
    }

    function getTimeoutAliveButton($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT_ALIVE_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT_ALIVE_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT_ALIVE_BUTTON);
    }

    function getTimeoutLogoutButton($default = true) {

        /* variable level setting */
        if ($this->getSettingValue(SETTING_TIMEOUT_LOGOUT_BUTTON, $default) != "") {
            return $this->getSettingValue(SETTING_TIMEOUT_LOGOUT_BUTTON, $default);
        }
        return $this->getDefaultValue(SETTING_TIMEOUT_LOGOUT_BUTTON);
    }

    function setIndividualDKRFNAInline($value) {
        $this->setSettingValue(SETTING_DKRFNA_INLINE, $value);
    }

    function setIndividualDKRFNASingle($value) {
        $this->setSettingValue(SETTING_DKRFNA_SINGLE, $value);
    }

    function setIndividualDKRFNA($value) {
        $this->setSettingValue(SETTING_DKRFNA, $value);
    }

    function setTimeout($value) {
        $this->setSettingValue(SETTING_TIMEOUT, $value);
    }

    function setTimeoutLength($value) {
        $this->setSettingValue(SETTING_TIMEOUT_LENGTH, $value);
    }

    function setTimeoutTitle($value) {
        $this->setSettingValue(SETTING_TIMEOUT_TITLE, $value);
    }

    function setTimeoutLogoutURL($value) {
        $this->setSettingValue(SETTING_TIMEOUT_LOGOUT, $value);
    }

    function setTimeoutRedirectURL($value) {
        $this->setSettingValue(SETTING_TIMEOUT_REDIRECT, $value);
    }

    function setTimeoutAliveButton($value) {
        $this->setSettingValue(SETTING_TIMEOUT_ALIVE_BUTTON, $value);
    }

    function setTimeoutLogoutButton($value) {
        $this->setSettingValue(SETTING_TIMEOUT_LOGOUT_BUTTON, $value);
    }

    /* spinner function */

    function getSpinner($default = true) {
        if ($this->getSettingValue(SETTING_SPINNER) != "") {
            return $this->getSettingValue(SETTING_SPINNER);
        }
        return $this->getDefaultValue(SETTING_SPINNER);
    }

    function setSpinner($value) {
        $this->setSettingValue(SETTING_SPINNER, $value);
    }

    function getSpinnerType($default = true) {
        if ($this->getSettingValue(SETTING_SPINNER_TYPE) != "") {
            return $this->getSettingValue(SETTING_SPINNER_TYPE);
        }
        return $this->getDefaultValue(SETTING_SPINNER_TYPE);
    }

    function setSpinnerType($value) {
        $this->setSettingValue(SETTING_SPINNER_TYPE, $value);
    }

    function getSpinnerUp($default = true) {
        if ($this->getSettingValue(SETTING_SPINNER_UP) != "") {
            return $this->getSettingValue(SETTING_SPINNER_UP);
        }
        return $this->getDefaultValue(SETTING_SPINNER_UP);
    }

    function setSpinnerUp($value) {
        $this->setSettingValue(SETTING_SPINNER_UP, $value);
    }

    function getSpinnerDown($default = true) {
        if ($this->getSettingValue(SETTING_SPINNER_DOWN) != "") {
            return $this->getSettingValue(SETTING_SPINNER_DOWN);
        }
        return $this->getDefaultValue(SETTING_SPINNER_DOWN);
    }

    function setSpinnerDown($value) {
        $this->setSettingValue(SETTING_SPINNER_DOWN, $value);
    }

    function getSpinnerIncrement($default = true) {
        if ($this->getSettingValue(SETTING_SPINNER_STEP) != "") {
            return $this->getSettingValue(SETTING_SPINNER_STEP);
        }
        return $this->getDefaultValue(SETTING_SPINNER_STEP);
    }

    function setSpinnerIncrement($value) {
        $this->setSettingValue(SETTING_SPINNER_STEP, $value);
    }

    function getTextboxManual($default = true) {
        if ($this->getSettingValue(SETTING_TEXTBOX_MANUAL) != "") {
            return $this->getSettingValue(SETTING_TEXTBOX_MANUAL);
        }
        return $this->getDefaultValue(SETTING_TEXTBOX_MANUAL);
    }

    function setTextboxManual($value) {
        $this->setSettingValue(SETTING_TEXTBOX_MANUAL, $value);
    }

    /* knob function */

    function getKnobRotation($default = true) {
        if ($this->getSettingValue(SETTING_KNOB_ROTATION) != "") {
            return $this->getSettingValue(SETTING_KNOB_ROTATION);
        }
        return $this->getDefaultValue(SETTING_KNOB_ROTATION);
    }

    function setKnobRotation($value) {
        $this->setSettingValue(SETTING_KNOB_ROTATION, $value);
    }

    /* slider functions */

    function getTextbox($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_TEXTBOX) != "") {
            return $this->getSettingValue(SETTING_SLIDER_TEXTBOX);
        }
        return $this->getDefaultValue(SETTING_SLIDER_TEXTBOX);
    }

    function setTextbox($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX, $value);
    }

    function getTextBoxLabel() {
        if ($this->getSettingValue(SETTING_SLIDER_TEXTBOX_LABEL) != "") {
            return $this->getSettingValue(SETTING_SLIDER_TEXTBOX_LABEL);
        }
        return $this->getDefaultValue(SETTING_SLIDER_TEXTBOX_LABEL);
    }

    function setTextboxLabel($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $value);
    }

    function getTextBoxPostText() {
        if ($this->getSettingValue(SETTING_SLIDER_TEXTBOX_POSTTEXT) != "") {
            return $this->getSettingValue(SETTING_SLIDER_TEXTBOX_POSTTEXT);
        }
        return $this->getDefaultValue(SETTING_SLIDER_TEXTBOX_POSTTEXT);
    }

    function setTextboxPostText($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX_POSTTEXT, $value);
    }
    
    function getSliderPreSelection($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_PRESELECTION) != "") {
            return $this->getSettingValue(SETTING_SLIDER_PRESELECTION);
        }
        return $this->getDefaultValue(SETTING_SLIDER_PRESELECTION);
    }

    function setSliderPreSelection($value) {
        $this->setSettingValue(SETTING_SLIDER_PRESELECTION, $value);
    }
    
    function getSliderFormater($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_FORMATER) != "") {
            return $this->getSettingValue(SETTING_SLIDER_FORMATER);
        }
        return $this->getDefaultValue(SETTING_SLIDER_FORMATER);
    }

    function setSliderFormater($value) {
        $this->setSettingValue(SETTING_SLIDER_FORMATER, $value);
    }

    function getTooltip($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_TOOLTIP) != "") {
            return $this->getSettingValue(SETTING_SLIDER_TOOLTIP);
        }
        return $this->getDefaultValue(SETTING_SLIDER_TOOLTIP);
    }

    function setTooltip($value) {
        $this->setSettingValue(SETTING_SLIDER_TOOLTIP, $value);
    }

    function getSliderOrientation($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_ORIENTATION) != "") {
            return $this->getSettingValue(SETTING_SLIDER_ORIENTATION);
        }
        return $this->getDefaultValue(SETTING_SLIDER_ORIENTATION);
    }

    function setSliderOrientation($value) {
        $this->setSettingValue(SETTING_SLIDER_ORIENTATION, $value);
    }

    function getSliderLabelPlacement($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_LABEL_PLACEMENT) != "") {
            return $this->getSettingValue(SETTING_SLIDER_LABEL_PLACEMENT);
        }
        return $this->getDefaultValue(SETTING_SLIDER_LABEL_PLACEMENT);
    }

    function setSliderLabelPlacement($value) {
        $this->setSettingValue(SETTING_SLIDER_LABEL_PLACEMENT, $value);
    }

    function getIncrement($default = true) {
        if ($this->getSettingValue(SETTING_SLIDER_INCREMENT) != "") {
            return $this->getSettingValue(SETTING_SLIDER_INCREMENT);
        }
        return $this->getDefaultValue(SETTING_SLIDER_INCREMENT);
    }

    function setIncrement($value) {
        $this->setSettingValue(SETTING_SLIDER_INCREMENT, $value);
    }

    /* inline elements */

    function getInlineExclusive($default = true) {
        if ($this->getSettingValue(SETTING_INLINE_EXCLUSIVE, $default) != "") {
            return $this->getSettingValue(SETTING_INLINE_EXCLUSIVE, $default);
        }
        return $this->getDefaultValue(SETTING_INLINE_EXCLUSIVE);
    }

    function setInlineExclusive($text) {

        $this->setSettingValue(SETTING_INLINE_EXCLUSIVE, $text);
    }

    function getInlineInclusive($default = true) {
        if ($this->getSettingValue(SETTING_INLINE_INCLUSIVE, $default) != "") {
            return $this->getSettingValue(SETTING_INLINE_INCLUSIVE, $default);
        }
        return $this->getDefaultValue(SETTING_INLINE_INCLUSIVE);
    }

    function setInlineInclusive($text) {

        $this->setSettingValue(SETTING_INLINE_INCLUSIVE, $text);
    }

    function getInlineMinimumRequired($default = true) {
        if ($this->getSettingValue(SETTING_INLINE_MINIMUM_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_INLINE_MINIMUM_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_INLINE_MINIMUM_REQUIRED);
    }

    function setInlineMinimumRequired($text) {
        $this->setSettingValue(SETTING_INLINE_MINIMUM_REQUIRED, $text);
    }

    function getInlineMaximumRequired($default = true) {
        if ($this->getSettingValue(SETTING_INLINE_MAXIMUM_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_INLINE_MAXIMUM_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_INLINE_MAXIMUM_REQUIRED);
    }

    function setInlineMaximumRequired($text) {

        $this->setSettingValue(SETTING_INLINE_MAXIMUM_REQUIRED, $text);
    }

    function getInlineExactRequired($default = true) {
        if ($this->getSettingValue(SETTING_INLINE_EXACT_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_INLINE_EXACT_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_INLINE_EXACT_REQUIRED);
    }

    function setInlineExactRequired($text) {

        $this->setSettingValue(SETTING_INLINE_EXACT_REQUIRED, $text);
    }

    /* group functions */

    function getExclusive($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_EXCLUSIVE, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_EXCLUSIVE, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_EXCLUSIVE);
    }

    function setExclusive($text) {

        $this->setSettingValue(SETTING_GROUP_EXCLUSIVE, $text);
    }

    function getInclusive($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_INCLUSIVE, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_INCLUSIVE, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_INCLUSIVE);
    }

    function setInclusive($text) {

        $this->setSettingValue(SETTING_GROUP_INCLUSIVE, $text);
    }

    function getMinimumRequired($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_MINIMUM_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_MINIMUM_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_MINIMUM_REQUIRED);
    }

    function setMinimumRequired($text) {

        $this->setSettingValue(SETTING_GROUP_MINIMUM_REQUIRED, $text);
    }

    function getMaximumRequired($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_MAXIMUM_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_MAXIMUM_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_MAXIMUM_REQUIRED);
    }

    function setMaximumRequired($text) {

        $this->setSettingValue(SETTING_GROUP_MAXIMUM_REQUIRED, $text);
    }

    function getExactRequired($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_EXACT_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_EXACT_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_EXACT_REQUIRED);
    }

    function setExactRequired($text) {
        $this->setSettingValue(SETTING_GROUP_EXACT_REQUIRED, $text);
    }

    function getUniqueRequired($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_UNIQUE_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_UNIQUE_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_UNIQUE_REQUIRED);
    }

    function setUniqueRequired($text) {
        $this->setSettingValue(SETTING_GROUP_UNIQUE_REQUIRED, $text);
    }

    function getSameRequired($default = true) {
        if ($this->getSettingValue(SETTING_GROUP_SAME_REQUIRED, $default) != "") {
            return $this->getSettingValue(SETTING_GROUP_SAME_REQUIRED, $default);
        }
        return $this->getDefaultValue(SETTING_GROUP_SAME_REQUIRED);
    }

    function setSameRequired($text) {
        $this->setSettingValue(SETTING_GROUP_SAME_REQUIRED, $text);
    }

    function getMultiColumnQuestiontext($default = true) {
        if ($this->getSettingValue(SETTING_MULTICOLUMN_QUESTIONTEXT, $default) != "") {
            return $this->getSettingValue(SETTING_ACCESS_TYPE, $default);
        }
        return MULTI_QUESTION_NO;
    }

    function setMultiColumnQuestiontext($value) {
        $this->setSettingValue(SETTING_MULTICOLUMN_QUESTIONTEXT, $value);
    }

    function getComboBoxNothingLabel($default = true) {
        if ($this->getSettingValue(SETTING_COMBOBOX_DEFAULT, $default) != "") {
            return $this->getSettingValue(SETTING_COMBOBOX_DEFAULT, $default);
        }

        return $this->getDefaultValue(SETTING_COMBOBOX_DEFAULT);
    }

    function setComboBoxNothingLabel($value) {
        $this->setSettingValue(SETTING_COMBOBOX_DEFAULT, $value);
    }

    /* TRANSLATION FUNCTIONS */

    function isTranslated() {
        if ($this->isTranslatedLayout() == false) {
            return false;
        }
        if ($this->isTranslatedAssistance() == false) {
            return false;
        }
        if ($this->isTranslatedSections() == false) {
            return false;
        }
        if ($this->isTranslatedTypes() == false) {
            return false;
        }
        return true;
    }

    function isTranslatedSections() {
        $sections = $this->getSections();
        foreach ($sections as $section) {
            if ($section->isTranslated() == false) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedTypes() {
        $types = $this->getTypes();
        foreach ($types as $type) {

            // exclude types not in use
            if ($type->isUsed() == false) {
                continue;
            }

            if ($type->isTranslated() == false) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedGroups() {
        $groups = $this->getGroups();
        foreach ($groups as $g) {
            if ($g->isTranslated() == false) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedLayout() {
        $arr = array(
            SETTING_BACK_BUTTON_LABEL,
            SETTING_NEXT_BUTTON_LABEL,
            SETTING_DK_BUTTON_LABEL,
            SETTING_RF_BUTTON_LABEL,
            SETTING_UPDATE_BUTTON_LABEL,
            SETTING_NA_BUTTON_LABEL,
            SETTING_REMARK_BUTTON_LABEL,
            SETTING_CLOSE_BUTTON_LABEL
        );

        $mode = getSurveyMode();
        $default = $this->getDefaultLanguage($mode);
        foreach ($arr as $a) {
            $s = $this->getSetting($a, false);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedAssistance() {
        $arr = array(
            SETTING_EMPTY_MESSAGE,
            SETTING_ERROR_MESSAGE_MINIMUM_LENGTH,
            SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH,
            SETTING_ERROR_MESSAGE_MINIMUM_WORDS,
            SETTING_ERROR_MESSAGE_MAXIMUM_WORDS,
            SETTING_ERROR_MESSAGE_PATTERN,
            SETTING_ERROR_MESSAGE_DOUBLE,
            SETTING_ERROR_MESSAGE_INTEGER,
            SETTING_ERROR_MESSAGE_MINIMUM_SELECT,
            SETTING_ERROR_MESSAGE_MAXIMUM_SELECT,
            SETTING_ERROR_MESSAGE_EXACT_SELECT,
            SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT,
            SETTING_ERROR_MESSAGE_INVALID_SELECT,
            SETTING_ERROR_MESSAGE_RANGE,
            SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR,
            SETTING_ERROR_MESSAGE_INLINE_ANSWERED,
            SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED,
            SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE,
            SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE,
            SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_EXACT_REQUIRED,
            SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_EXCLUSIVE,
            SETTING_ERROR_MESSAGE_INCLUSIVE,
            SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED
        );

        $mode = getSurveyMode();
        $default = $this->getDefaultLanguage($mode);
        foreach ($arr as $a) {
            $s = $this->getSetting($a, false);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }

    function getReportedIssues() {
        global $db;
        $query = "select * from " . Config::dbSurvey() . "_issues where suid=" . $this->getSuid();
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $arr[] = $row;
                }
            }
        }
        return $arr;
    }

    function getDefaultSurvey() {
        return $this->getSettingDirectly($this->getSuid(), OBJECT_SURVEY, SETTING_DEFAULT_SURVEY)->getValue();
    }

    function setDefaultSurvey($value) {
        //echo $this->getSuid() . '----' . $value;
        $this->setSettingDirectly($this->getSuid(), OBJECT_SURVEY, SETTING_DEFAULT_SURVEY, $value);
    }

}

?>