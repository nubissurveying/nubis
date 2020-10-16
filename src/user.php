<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class User {

    var $user;
    var $contacts;
    private $access;

    function __construct($uridorrow, $createnew = false) {
        global $db;
        if ($uridorrow == '' && $createnew == true) { //create new user
            $result = $db->selectQuery('select max(urid) as maxurid from ' . Config::dbSurvey() . '_users');
            $row = $db->getRow($result);
            $uridorrow = $row['maxurid'] + 1;
            $result = $db->selectQuery('insert into ' . Config::dbSurvey() . '_users (urid) values (' . prepareDatabaseString($uridorrow) . ')');
        }
        if (is_array($uridorrow)) {
            $this->user = $uridorrow;
        } else {
            $result = $db->selectQuery('select ' . Users::getSelectQuery() . ' from ' . Config::dbSurvey() . '_users where urid = ' . prepareDatabaseString($uridorrow));
            $this->user = $db->getRow($result);
        }
        $this->contacts = new Contacts();
    }

    function getUrid() {
        return $this->user['urid'];
    }

    function getName() {
        return $this->user['name'];
    }

    function setName($name) {
        $this->user['name'] = $name;
    }

    function getUsername() {
        return $this->user['username'];
    }

    function setUsername($username) {
        $this->user['username'] = $username;
    }

    function getPassword() {
        return $this->user['password'];
    }

    function setPassword($password) {
        $this->user['password'] = $password;
    }

    function getUserType() {
        return $this->user['usertype'];
    }

    function setUserType($type) {
        $this->user['usertype'] = $type;
    }

    function getUserSubType() {
        return $this->user['usersubtype'];
    }

    function setUserSubType($type) {
        $this->user['usersubtype'] = $type;
    }

    function isMainNurse() {
        return $this->getUserSubType() == USER_NURSE_MAIN;
    }

    function isLabNurse() {
        return $this->getUserSubType() == USER_NURSE_LAB;
    }

    function isFieldNurse() {
        return $this->getUserSubType() == USER_NURSE_FIELD;
    }

    function isVisionNurse() {
        return $this->getUserSubType() == USER_NURSE_VISION;
    }

    function getSupervisor() {
        return $this->user['sup'];
    }

    function setSupervisor($sup) {
        $this->user['sup'] = $sup;
    }

    function getFilter() {
        return $this->user['filter'];
    }

    function setFilter($filter) {
        $this->user['filter'] = $filter;
    }

    function getRegionFilter() {
        return $this->user['regionfilter'];
    }

    function setRegionFilter($regionFilter) {
        $this->user['regionfilter'] = $regionFilter;
    }

    function getTestMode() {
        return $this->user['testmode'];
    }

    function setTestMode($testMode) {
        $this->user['testmode'] = $testMode;
    }

    function getCommunication() {
        return $this->user['communication'];
    }

    function setCommunication($communication) {
        $this->user['communication'] = $communication;
    }

    function isTestMode() {
        return $this->getTestMode() == 1;
    }

    function getLastData() {
        return $this->user['lastdata'];
    }

    function setLastData($date) {
        $this->user['lastdata'] = $date;
    }

    function getSurveysAccess() {
        $access = unserialize(gzuncompress($this->getAccess()));
        return array_keys($access);
    }

    function getLanguages($suid, $mode) {
        $access = unserialize(gzuncompress($this->getAccess()));
        if (isset($access[$suid])) {
            $arr = $access[$suid];
        } else {
            return "";
        }
        if (isset($arr[$mode])) {
            return $arr[$mode];
        }
        return "";
    }

    function setLanguages($suid, $mode, $ls) {
        $access = unserialize(gzuncompress($this->getAccess()));
        if (isset($access[$suid])) {
            $arr = $access[$suid];
            if (!is_array($arr)) {
                $arr = array($arr);
            }
        } else {
            $arr = array();
        }
        $arr[$mode] = $ls;
        $access[$suid] = $arr;
        $this->setAccess((gzcompress(serialize($access))));
    }

    function addLanguage($suid, $mode, $l) {
        $access = unserialize(gzuncompress($this->getAccess()));
        $arr = $access[$suid];
        if (isset($arr[$mode])) {
            $ls = explode("~", $arr[$mode]);
            if (!inArray($l, $ls)) {
                $ls[] = $l;
                $arr[$mode] = implode("~", $ls);
                $access[$suid] = $arr;
                $this->setAccess((gzcompress(serialize($access))));
            }
        }
    }

    function addMode($suid, $mode, $ls) {
        $access = unserialize(gzuncompress($this->getAccess()));
        $arr = $access[$suid];
        $arr[$mode] = $ls;
        $access[$suid] = $arr;
        $this->setAccess((gzcompress(serialize($access))));
    }

    function removeLanguage($suid, $mode, $l) {
        $access = unserialize(gzuncompress($this->getAccess()));
        $arr = $access[$suid];
        if (isset($arr[$mode])) {
            $ls = explode("~", $arr[$mode]);
            if (inArray($l, $ls)) {
                unset($ls[array_search($l, $ls)]);
                $arr[$mode] = implode("~", $ls);
                $access[$suid] = $arr;
            }
        }
        $this->setAccess((gzcompress(serialize($access))));
    }

    function removeMode($suid, $mode) {
        $access = unserialize(gzuncompress($this->getAccess()));
        $arr = $access[$suid];
        if (isset($arr[$mode])) {
            unset($arr[$mode]);
            $access[$suid] = $arr;
        }
        $this->setAccess((gzcompress(serialize($access))));
    }

    function removeSurvey($suid) {
        $access = unserialize(gzuncompress($this->getAccess()));
        if (isset($access[$suid])) {
            unset($access[$suid]);
        }
        $this->setAccess((gzcompress(serialize($access))));
    }

    function getModes($suid) {
        $access = unserialize(gzuncompress($this->getAccess()));
        $arr = $access[$suid];
        return array_keys($arr);
    }

    function getAccess() {
        return $this->user['access'];
    }

    function setAccess($access) {
        $this->user['access'] = $access;
    }

    function getSurveys() {
        return explode("~", $this->user['surveys']);
    }

    function setSurveys($arr) {
        $this->user['surveys'] = implode("~", $arr);
    }

    function saveChanges() {
        global $db;
        $query = 'UPDATE ' . Config::dbSurvey() . '_users SET ';
        $query .= 'username = ?, ';
        $query .= 'name = ?, ';
        $query .= 'settings = ?, ';
        $query .= 'password = aes_encrypt(?, "' . Config::smsPasswordKey() . '"), ';
        $query .= 'filter = ?, ';
        $query .= 'regionfilter = ?, ';
        $query .= 'testmode = ?, ';
        $query .= 'status= ?, ';
        $query .= 'sup= ?, ';
        $query .= 'usertype = ?, ';
        $query .= 'usersubtype = ?, ';
        $query .= 'access = ?, ';
        $query .= 'lastdata = ?,';
        $query .= 'communication = ? ';
        $query .= 'WHERE urid = ?';
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, $this->getUsername());
        $bp->add(MYSQL_BINDING_STRING, $this->getName());
        $bp->add(MYSQL_BINDING_STRING, $this->getSettings());
        $bp->add(MYSQL_BINDING_STRING, $this->getPassword());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getFilter());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getRegionFilter());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getTestMode());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getStatus());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getSupervisor());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getUserType());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getUserSubType());
        $bp->add(MYSQL_BINDING_STRING, $this->getAccess());
        $bp->add(MYSQL_BINDING_STRING, $this->getLastData());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getCommunication());
        $bp->add(MYSQL_BINDING_INTEGER, $this->getUrid());
        return $db->executeBoundQuery($query, $bp->get());
    }

    function delete() {
        global $db;
        $query = 'DELETE FROM ' . Config::dbSurvey() . '_users ';
        $query .= 'WHERE urid = ' . $this->getUrid();
        return $db->executeQuery($query);
    }

    function getContacts() {
        return $this->contacts->getContactsByUrid($this->getUrid());
    }

    function getCompleted() {
        return $this->contacts->getCompletedByUrid($this->getUrid());
    }

    function getRefusals() {
        return $this->contacts->getRefusalsByUrid($this->getUrid());
    }

    function setStatus($status) {
        $this->user['status'] = $status;
    }

    function getStatus() {
        return $this->user['status'];
    }

    function isActive() {
        return $this->getStatus() == 1;
    }

    function getNavigationInBreadCrumbs() {
        return $this->getSetting('navinbread', 1);
    }

    function setNavigationInBreadCrumbs($nav) {
        return $this->setSetting('navinbread', $nav);
    }

    function hasNavigationInBreadCrumbs() {
        return $this->getNavigationInBreadCrumbs() == 1;
    }

    function hasRoutingAutoIndentation() {
        return $this->getRoutingAutoIndentation() == 1;
    }

    function hasHTMLEditor() {
        return $this->getHTMLEditor() == 1;
    }

    function itemsInTable() {
        return $this->getItemsInTable();
    }

    function getRoutingAutoIndentation() {
        return $this->getSetting('autoindent', 1);
    }

    function getHTMLEditor() {
        return $this->getSetting('htmleditor', 1);
    }

    function getItemsInTable() {
        $res = $this->getSetting('itemsintable', -1);
        if ($res != "") {
            return $res;
        }
        return -1;
    }

    function setItemsInTable($items) {
        $this->setSetting('itemsintable', $items);
    }

    function setHTMLEditor($ed) {
        $this->setSetting('htmleditor', $ed);
    }

    function setRoutingAutoIndentation($routing) {
        return $this->setSetting('autoindent', $routing);
    }

    function getPuid() {
        return $this->getSetting('puid');
    }

    function setPuid($puid) {
        return $this->setSetting('puid', $puid);
    }

    function getSetting($name, $default = null) {
        $settings = unserialize(($this->getSettings()));
        if (isset($settings[$name])) {
            return $settings[$name];
        } else {
            return $default;
        }
    }

    function setSetting($name, $value) {
        $settings = unserialize(($this->getSettings()));
        $settings[$name] = $value;
        $this->setSettings((serialize($settings)));
    }

    function getSettings() {
        return $this->user['settings'];
    }

    function setSettings($settings) {
        $this->user['settings'] = $settings;
    }

}

?>