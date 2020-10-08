<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Remarks {

    var $remarksArray = array();

    public function __construct() {
        
    }

    public function getRemarks($primkey) {
        global $db;
        if (isset($this->remarksArray[$primkey])) {
            $remarks = $this->remarksArray[$primkey];
        } else {
            $remarks = array();
            $query = 'select *, aes_decrypt(remark, "' . Config::smsRemarkKey() . '") as remark_dec, t1.ts as ts from ' . Config::dbSurvey() . '_remarks as t1 left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid where primkey = \'' . prepareDatabaseString($primkey) . '\' order by t1.ts desc';
            $result = $db->selectQuery($query);
            while ($row = $db->getRow($result)) {
                $remarks[] = $row;
            }
            //echo '<br/><br/><br/>';
            // echo $query;
            // echo '<br/>';
            //print_r($remarks);
            $this->remarksArray[$primkey] = $remarks;
        }
        return $remarks;
    }

    function addRemark($primkey, $remark, $urid) {
        global $db;
        $query = 'replace into ' . Config::dbSurvey() . '_remarks (primkey, remark, urid, ts) values (\'' . prepareDatabaseString($primkey) . '\', aes_encrypt(\'' . prepareDatabaseString($remark) . '\',\'' . Config::smsRemarkKey() . '\'), ' . $urid . ', \'' . date('Y-m-d H:i:s') . '\')';
//      echo '<br/><br/><br/>' . $query;
        $db->executeQuery($query);
        if (isset($this->remarksArray[$primkey])) {
            unset($this->remarksArray[primkey]); //remove from array so getremarks re-reads it.
        }
        return $query;
    }

}

?>