<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Users {

    var $userArray = array();

    function __construct() {
        
    }

    static function getSelectQuery() {
        return '*, aes_decrypt(password, "' . Config::smsPasswordKey() . '") as password ';
    }

    function getUsersByType($type) {
        global $db;
        $users = array();
        $result = $db->selectQuery('SELECT ' . $this->getSelectQuery() . ' FROM ' . Config::dbSurvey() . '_users where usertype = ' . prepareDatabaseString($type));
        while ($row = $db->getRow($result)) {
            $users[] = new User($row);
        }
        return $users;
    }

    function getUsersByName($name) {
        global $db;
        $users = array();
        $result = $db->selectQuery('SELECT ' . $this->getSelectQuery() . ' FROM ' . Config::dbSurvey() . '_users where username = "' . prepareDatabaseString($name) . '"');
        while ($row = $db->getRow($result)) {
            $users[] = new User($row);
        }
        return $users;
    }

    function getUsersBySupervisor($urid) {
        global $db;
        if (isset($this->userArray[$_SESSION['URID']])) {
            $users = $this->userArray[$_SESSION['URID']];
        } else {
            $users = array();
            $result = $db->selectQuery('SELECT ' . $this->getSelectQuery() . ' FROM ' . Config::dbSurvey() . '_users where sup = ' . prepareDatabaseString($urid));
            while ($row = $db->getRow($result)) {
                $users[] = new User($row);
            }
            $this->userArray[$_SESSION['URID']] = $users;
        }
        return $users;
    }

    function getUsers() {
        global $db;
        if (isset($this->userArray[$_SESSION['URID']])) {
            $users = $this->userArray[$_SESSION['URID']];
        } else {
            $users = array();
            $result = $db->selectQuery('SELECT ' . $this->getSelectQuery() . ' FROM ' . Config::dbSurvey() . '_users');
            while ($row = $db->getRow($result)) {
                $users[] = new User($row);
            }
            $this->userArray[$_SESSION['URID']] = $users;
        }
        return $users;
    }

}

?>