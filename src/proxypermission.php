<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class ProxyPermission {

    function __construct() {
        
    }

    function getRandomProxyCode() {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_proxypermission ORDER BY RAND() LIMIT 1');
        $row = $db->getRow($result);
        return $row['startcode'];
    }

    function checkProxyCode($startcode, $permissioncode) {
        global $db;
        $query = 'select count(*) as cnt from ' . Config::dbSurvey() . '_proxypermission where startcode=\'' . prepareDatabaseString($startcode) . '\' and permissioncode = \'' . prepareDatabaseString($permissioncode) . '\'';
        $result = $db->selectQuery($query);
        $row = $db->getRow($result);
        return $row['cnt'] > 0;
    }

}

?>