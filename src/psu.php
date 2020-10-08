<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Psu {

    var $psu;

    function __construct($rowOrPuid) {
        global $db;
        if (is_array($rowOrPuid)) {
            $this->psu = $rowOrPuid;
        } else {
            $query = 'select * from ' . Config::dbSurvey() . '_psus where puid = ' . prepareDatabaseString($rowOrPuid);
            $result = $db->selectQuery($query);
            $this->psu = $db->getRow($result);
        }
    }

    function getPuid() {
        return $this->psu['puid'];
    }

    function getName() {
        return $this->psu['name'];
    }

    function getCode() {
        return $this->psu['code'];
    }

    function getCodeAndName() {
        if (trim($this->getCode() != '')) {
            return $this->getCode() . ': ' . $this->getName();
        }
        return $this->getName();
    }

    function getNumberAndName() {
        if ($this->getPuid() > 0) {
            return $this->getPuid() . ': ' . $this->getName();
        }
    }

}

?>