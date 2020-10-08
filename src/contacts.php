<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Contacts {

    var $contactsArray = array();
    var $contactsByUridArray = array();
    var $completedByUridArray = array();
    var $refusalsByUridArray = array();
    var $lastQuery = '';

    function __construct() {
        
    }

    function addContact($primkey, $contactcode, $contactts, $contactwith, $contactperson, $remark, $event, $urid) {
        global $db;
        $errorMessage = array();
        if ($contactcode <= 0) {
            $errorMessage[] = 'Please enter a contact outcome.';
        } elseif ($contactts == '') {
            $errorMessage[] = 'Please enter a contact day and time.';
        } else {
            $followup = Language::optionsDispositionContactCode(); //  ::optionsDispositionContactCode();
            $proxy = 0;
            $proxyname = '';
            if ($followup[$contactcode][0] == '1') {
                if ($contactwith == '2') {
                    $proxy = 1;
                    $proxyname = $contactperson;
                    if ($contactperson == '') {
                        $errorMessage[] = 'Please enter the name of the person who you spoke to.';
                    }
                } elseif ($contactwith == '') {
                    $errorMessage[] = 'Please enter who you contacted.';
                }
            }
        }
        if (sizeof($errorMessage) == 0) {
            $query = 'REPLACE INTO ' . Config::dbSurvey() . '_contacts (primkey, code, contactts, proxy, proxyname, remark, event, urid) VALUES (';
            $query .= '"' . prepareDatabaseString($primkey) . '", ';
            $query .= $contactcode . ', ';
            $query .= '"' . prepareDatabaseString(date('Y-m-d H:i:s', strtotime($contactts))) . '", ';
            $query .= $proxy . ', ';
            if ($proxyname != '') {
                $query .= 'aes_encrypt("' . prepareDatabaseString($proxyname) . '", "' . Config::smsContactNameKey() . '"), ';
            } else {
                $query .= 'NULL, ';
            }
            if ($remark != '') {
                $query .= 'aes_encrypt("' . prepareDatabaseString($remark) . '", "' . Config::smsContactRemarkKey() . '"), ';
            } else {
                $query .= 'NULL, ';
            }
            if ($event != '') {
                $query .= '"' . prepareDatabaseString(date('Y-m-d H:i:s', strtotime($event))) . '", ';
            } else {
                $query .= 'NULL, ';
            }
            $query .= $urid . ')';
            $this->lastQuery = $query;
            //echo $query;
            //exit;
            $db->executeQuery($query);
            if (isset($this->contactsArray[$primkey])) {
                myUnset($this->contactsArray[$primkey]);
            }
            if (isset($this->contactsByUridArray[$urid])) {
                myUnset($this->contactsByUridArray[$urid]);
            }
            if (isset($this->completedByUridArray[$urid])) {
                myUnset($this->completedByUridArray[$urid]);
            }
            if (isset($this->refusalsByUridArray[$urid])) {
                myUnset($this->refusalsByUridArray[$urid]);
            }
        }
        return $errorMessage;
    }

    function getLastQuery() {
        return $this->lastQuery;
    }

    function getContacts($primkey) {
        global $db;
        if (isset($this->contactsArray[$primkey])) {
            $contacts = $this->contactsArray[$primkey];
        } else {
            $contacts = array();
            $query = 'select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid where ' . getTextmodeStr() . ' primkey = "' . prepareDatabaseString($primkey) . '" order by t1.contactts desc';
//                    echo '<br/><br/><br/>' . $query;
            $result = $db->selectQuery($query);
            while ($row = $db->getRow($result)) {
                $contacts[] = new Contact($row);
            }

            $this->contactsArray[$primkey] = $contacts;
        }
        return $contacts;
    }

    function getAppointments($urid) {
        global $db;
        $appointments = array();
        $query = 'select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid where ' . getTextmodeStr() . ' t1.urid = ' . prepareDatabaseString($urid) . ' and event is not null order by t1.contactts desc';
        $result = $db->selectQuery($query);
//echo $query;
        while ($row = $db->getRow($result)) {
            $appointments[] = new Contact($row);
        }
        return $appointments;
    }

    function getContactsByUrid($urid) {
        global $db;
        if (isset($this->contactsByUridArray[$urid])) {
            $contacts = $this->contactsByUridArray[$urid];
        } else {
            $contacts = array();
            $result = $db->selectQuery('select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid where ' . getTextmodeStr() . ' t1.urid = ' . prepareDatabaseString($urid) . ' order by t1.contactts desc');
            while ($row = $db->getRow($result)) {
                $contacts[] = new Contact($row);
            }
            $this->contactsByUridArray[$urid] = $contacts;
        }
        return $contacts;
    }

    function getCompletedByUrid($urid) {
        global $db;
        if (isset($this->completedByUridArray[$urid])) {
            $contacts = $this->completedByUridArray[$urid];
        } else {
            $contacts = array();
            $csidQuery = ' AND code = 500 ';  //completed  = 500

            $query = 'select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 ';
            $query .= 'left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid ';
            $query .= 'left join ' . Config::dbSurvey() . '_respondents as t3 on t1.primkey = t3.primkey ';
            $query .= 'where t3.primkey is not null AND ' . getTextmodeStr() . ' t1.urid = ' . prepareDatabaseString($urid) . $csidQuery . ' order by t1.contactts desc';
            $result = $db->selectQuery($query);
            while ($row = $db->getRow($result)) {
                $contacts[] = new Contact($row);
            }
            $this->completedByUridArray[$urid] = $contacts;
        }
        return $contacts;
    }

    function getRefusalsByUrid($urid) {
        global $db;
        if (isset($this->refusalsByUridArray[$urid])) {
            $contacts = $this->refusalsByUridArray[$urid];
        } else {
            $contacts = array();
            //$csidQuery = ' AND code = 103 ';
            //   $result = $db->selectQuery('select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid where t1.urid = ' . prepareDatabaseString($urid) . $csidQuery . ' order by t1.contactts desc');
//echo '<br/><br/><br/>'. 'select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid where t1.urid = ' . prepareDatabaseString($urid) . ' order by t1.contactts desc';

            $query = 'select *, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark_dec, aes_decrypt(proxyname, "' . Config::smsContactNameKey() . '") as proxyname_dec from ' . Config::dbSurvey() . '_contacts as t1 ';
            $query .= 'left join ' . Config::dbSurvey() . '_users as t2 on t1.urid = t2.urid ';
            $query .= 'left join ' . Config::dbSurvey() . '_respondents as t3 on t1.primkey = t3.primkey ';
            $query .= 'where t3.primkey is not null AND ' . getTextmodeStr() . ' t1.urid = ' . prepareDatabaseString($urid) . ' order by t1.contactts desc';


            $result = $db->selectQuery($query);
            while ($row = $db->getRow($result)) {
                $contact = new Contact($row);
                if ($contact->isRefusal()) {
                    $contacts[] = $contact;
                }
            }
            $this->refusalsByUridArray[$urid] = $contacts;
        }
        return $contacts;
    }

}

?>