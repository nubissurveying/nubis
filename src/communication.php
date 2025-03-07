<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Communication {

    function __construct() {
        
    }
    
    function isServerReachable() {
        // dbConfig::defaultServer();
        error_reporting(0);
        //http://forums.winamp.com/showthread.php?t=166910
        $servername = substr(getCommunicationServer(), 0, strpos(getCommunicationServer(), '/'));
        $fp = fsockopen($servername, '80', $errno, $errstr, 2);
        if ($fp) { //up
            fclose($fp);
            return true;
        }
        return false;
    }

    function isUpdateAvailable($urid) {
        $postUrl = getCommunicationServer();
        $data = array('k' => encryptC(Config::communicationAccessKey(), Config::smsComponentKey()), 'p' => 'updateavailable', 'urid' => $urid);
        $result = $this->curlToServer($data, $postUrl);        
        if (trim($result) == 'yes') {
            return true;
        }
        return false;
    }

    function confirmDataReceived($urid) {
        $postUrl = getCommunicationServer();
        $data = array('k' => encryptC(Config::communicationAccessKey(), Config::smsComponentKey()), 'p' => 'datareceived', 'urid' => $urid);
        $result = $this->curlToServer($data, $postUrl);

        if (trim($result) == 'yes') {
            return true;
        }
        return false;
    }

    function exportTables($tables, $ts = '', $extraCondition = '') {
        global $db;
        $return = '';
        $wherets = ' WHERE 1 = 1';
        if (trim($ts) != '') {
            $wherets .= ' AND ts > "' . prepareDatabaseString($ts) . '"';
        }
        if (trim($extraCondition) != '') {
            $wherets .= ' AND ' . $extraCondition;
        }
        foreach ($tables as $table) {

            $result = $db->selectQuery('select * from ' . Config::dbSurvey() . '_' . prepareDatabaseString($table) . $wherets);
            if ($db->getNumberOfRows($result) > 0) {
                $finfo = $result->fetch_fields();
                $fieldnames = array();
                foreach ($finfo as $val) {
                    $fieldnames[] = $val->name;
                }
                $num_fields = sizeof($finfo);

                $return.= 'REPLACE INTO ' . Config::dbSurvey() . '_' . $table . ' (' . implode(',', $fieldnames) . ') VALUES ';
                $first = true;
                while ($row = $db->getRow($result)) {
                    if (!$first) {
                        $return.= ', ';
                    }
                    $return.= ' ( ';
                    for ($j = 0; $j < $num_fields; $j++) {
                        $rowUp = addslashes($row[$j]);
                        //$rowUp = ereg_replace("\n", "\\n", $rowUp);
                        $rowUp = str_replace(array("\n"), '\\n', $rowUp); // ereg_replace is out in php 7.0
                        if (isset($rowUp)) {
                            $return.= '"' . $rowUp . '"';
                        } else {
                            $return.= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return.= ',';
                        }
                    }
                    $return.= ")";
                    $first = false;
                }

                $return .= ";\n";
            }
        }

        return $return;
    }

    function removeFromTables($tables, $ts = '', $extraCondition = '') {
        $return = '';
        foreach ($tables as $table) {
            $return.= 'delete from ' . Config::dbSurvey() . '_' . $table . ' WHERE ' . $extraCondition . ";\n";
        }
        return $return;
    }

    function sendToServer($str, $urid) {
        $postUrl = getCommunicationServer();
        $str = urlencode($this->encryptAndCompress($str));
        $data = array('k' => encryptC(Config::communicationAccessKey(), Config::smsComponentKey()), 'p' => 'upload', 'urid' => $urid, 'query' => $str);

        $result = $this->curlToServer($data, $postUrl);
        
        if (trim($result) == 'ok') {
            return true;
        }
        return false;
    }

    function receiveFromServer($urid) {
        $postUrl = getCommunicationServer();
        $data = array('k' => encryptC(Config::communicationAccessKey(), Config::smsComponentKey()), 'p' => 'receive', 'urid' => $urid);

        $result = $this->curlToServer($data, $postUrl);        
        $this->importData($result);        
        return true;       
    }

    function importData($str) {
        global $db;

        $lines = explode("!~!~!", trim($str));

        for ($i = 0; $i < sizeof($lines); $i = $i + 2) {

            $parameters = explode('~', $lines[$i]);
            $line = $lines[$i + 1];
            $deflate = $this->decryptAndUncompress(($line));

            if ($parameters[0] == 1) { //sql

                $linessql = explode("\n", $deflate);
                foreach ($linessql as $linesql) {
                    if (trim($linesql) != '') {
                        $db->executeQuery($linesql);
                    }
                }
            } elseif ($parameters[0] == 2) { //scripts
                $filename = getcwd() . $parameters[1];
                //$filename = '/tmp/haalsi/' . $parameters[1];
                //check for access!
                if (is_writable(dirname($filename))) {
                    file_put_contents($filename, $deflate);
                }
            }
        }
    }

    function importTable($str) {
        global $db;
        $deflate = $this->decryptAndUncompress($str);
//      $deflate = gzuncompress($str);
        $lines = explode("\n", $deflate);
        foreach ($lines as $line) {
            if (trim($line) != '') {
                $db->executeQuery($line);
            }
        }
    }

    function getMetaDataUpdate($extraTables = array()) {
        $tables = array_merge(array('settings'), $extraTables);
        return $this->exportTables($tables);
    }

    function addSQLToUser($updateSql, $urid, $compress = false) {
        global $db;
        if ($updateSql != '') {
            $query = 'insert into ' . Config::dbSurvey() . '_communication ';
            $query .= ' (urid, insertts, sqlcode) values (';
            $query .= prepareDatabaseString($urid) . ', ';
            $query .= '"' . prepareDatabaseString(date('Y-m-d H:i:s')) . '", ';
            $query .= 'COMPRESS(AES_ENCRYPT("' . addslashes($updateSql) . '", "' . Config::smsCommunicationKey() . '")) ';
            $query .= ')';
            $db->executeQuery($query);
        }
    }

    function addScriptToUser($localfile, $filename, $urid) {
        global $db;
        $str = file_get_contents($localfile);
        $query = 'insert into ' . Config::dbSurvey() . '_communication ';
        $query .= ' (urid, insertts, sqlcode, filename, datatype) values (';
        $query .= prepareDatabaseString($urid) . ', ';
        $query .= '"' . prepareDatabaseString(date('Y-m-d H:i:s')) . '", ';
        $query .= 'COMPRESS(AES_ENCRYPT("' . addslashes($str) . '", "' . Config::smsCommunicationKey() . '")), ';
        $query .= '"' . prepareDatabaseString($filename) . '", 2)';
        $db->executeQuery($query);
        return $query;
    }

    function getUserScripts($urid) {
        return $this->getUserScriptsOrQueries($urid, 2);
    }

    function getUserQueries($urid) {
        return $this->getUserScriptsOrQueries($urid, 1);
    }

    function getAllUserQueries($urid) {
        return $this->getUserScriptsOrQueries($urid, 1, false);
    }

    function getAllUserCommunication($urid) {
        return array_merge($this->getAllUserQueries($urid), $this->getUserScripts($urid));
    }

    function getUserScriptsOrQueries($urid, $datatype = 1, $notReceivedCheck = true) {
        global $db;
        $rows = array();
        $receivedStr = '';
        if ($notReceivedCheck) {
            $receivedStr = ' and received = 0';
        }
        $query = 'select * from ' . Config::dbSurvey() . '_communication where datatype = ' . prepareDatabaseString($datatype) . $receivedStr . ' and urid = ' . prepareDatabaseString($urid) . ' and direction = 1 order by ts asc, hnid asc';  //was desc        
        $result = $db->selectQuery($query);
        while ($row = $db->getRow($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    function setUpdateReceived($urid) {
        global $db;
        $query = 'update ' . Config::dbSurvey() . '_communication set received = 1, receivedts = "' . prepareDatabaseString(date('Y-m-d H:i:s')) . '" where urid = ' . prepareDatabaseString($urid);
        return $db->selectQuery($query);
    }

    function encryptAndCompress($str) {
        global $db;
        $query = 'SELECT COMPRESS(AES_ENCRYPT("' . addslashes($str) . '","' . Config::smsCommunicationKey() . '"))';
        $result = $db->selectQuery($query);
        $row = $db->getRow($result);
        return $row[0];
    }

    function decryptAndUncompress($str) {
        global $db;
        $query = 'SELECT AES_DECRYPT(UNCOMPRESS("' . addslashes($str) . '"),"' . Config::smsCommunicationKey() . '")';
        $result = $db->selectQuery($query);
        $row = $db->getRow($result);
        return $row[0];
    }

    //http://stackoverflow.com/questions/7121479/listing-all-the-folders-subfolders-and-files-in-a-directory-using-php
    function getScriptFiles(&$files, $dir, $basedir = '') {
        $ffs = scandir($dir);
        foreach ($ffs as $tt) {
            if ($tt != '.' && $tt != '..') {
                if (is_dir($dir . '/' . $tt)) {
                    $this->getScriptFiles($files, $dir . '/' . $tt, $basedir . '/' . $tt);
                } else {
                    $files[$dir . '/' . $tt] = $basedir . '/' . $tt;
                }
            }
        }
    }

    function assignHousehold(Household $household, $newurid) {
        // _household and _respondents
        $data = $this->exportTables(array('households'), '', 'primkey = "' . prepareDatabaseString($household->getPrimkey()) . '"');
        $data .= "\n";
        $data .= $this->exportTables(array('respondents'), '', 'hhid = "' . prepareDatabaseString($household->getPrimkey()) . '"');
        $this->addSQLToUser($data, $newurid);
    }
    
    function assignLab($household, $respondent, $lab, $newurid) {
        // _household, _respondents and _lab
        $data = $this->exportTables(array('households'), '', 'primkey = "' . prepareDatabaseString($household->getPrimkey()) . '"');
        $data .= "\n";
        $data .= $this->exportTables(array('respondents'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');
        $this->addSQLToUser($data, $newurid);
        $data .= "\n";
        $data = $this->exportTables(array('lab'), '', 'primkey = "' . prepareDatabaseString($lab->getPrimkey()) . '"');
        $this->addSQLToUser($data, $selurid);
    }

    function reassignHousehold(Household $household, $oldurid, $newurid) {
        $oldUser = new User($oldurid);
        //add to new iwer first.. then remove from old.

        if ($newurid != -1) { //back to agency
            //insert data into new iwer
            $data = $this->exportTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts'), '', 'primkey = "' . prepareDatabaseString($household->getPrimkey()) . '"');
            $this->addSQLToUser($data, $newurid);
            //insert data into new iwer (for respodnents)
            foreach ($household->getSelectedRespondentsWithFinFamR() as $respondent) {
                $data = $this->exportTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');
                $this->addSQLToUser($data, $newurid);
            }

            //get data for household and respondents and add to new urid
            $data = $this->exportTables(array('households'), '', 'primkey = "' . prepareDatabaseString($household->getPrimkey()) . '"');
            $data .= "\n";
            $data .= $this->exportTables(array('respondents'), '', 'hhid = "' . prepareDatabaseString($household->getPrimkey()) . '"');
            $this->addSQLToUser($data, $newurid);
        }

        if ($oldUser->getUserType() == USER_INTERVIEWER) {//not if this isn't an interviewer
            //remove data from old interviewer  
            $data = $this->removeFromTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts'), '', 'primkey = "' . prepareDatabaseString($household->getPrimkey()) . '"');
            $this->addSQLToUser($data, $oldurid);
            //remove data from old interviewer  (for respondents)
            foreach ($household->getSelectedRespondentsWithFinFamR() as $respondent) {
                $data = $this->exportTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');
                $this->addSQLToUser($data, $oldurid);
            }
            //now remove from oldurid
            $data = $this->removeFromTables(array('households'), '', 'primkey = "' . prepareDatabaseString($household->getPrimkey()) . '"');
            $data .= "\n";
            $data .= $this->removeFromTables(array('respondents'), '', 'hhid = "' . prepareDatabaseString($household->getPrimkey()) . '"');

            $this->addSQLToUser($data, $oldurid);
        }
    }
    
    function reassignRespondent(Respondent $respondent, $oldurid, $newurid) {
        $oldUser = new User($oldurid);
        //add to new iwer first.. then remove from old.

        if ($newurid != -1) { //back to agency
            //insert data into new iwer
            $data = $this->exportTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');
            $this->addSQLToUser($data, $newurid);
            
            //get data for household and respondents and add to new urid
            $data .= $this->exportTables(array('respondents'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');
            $this->addSQLToUser($data, $newurid);
        }

        if ($oldUser->getUserType() == USER_INTERVIEWER) {//not if this isn't an interviewer
            //remove data from old interviewer  
            $data = $this->removeFromTables(array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');
            $this->addSQLToUser($data, $oldurid);
            
            //now remove from oldurid
            $data .= $this->removeFromTables(array('respondents'), '', 'primkey = "' . prepareDatabaseString($respondent->getPrimkey()) . '"');

            $this->addSQLToUser($data, $oldurid);
        }
    }

    function removeRecord($hnid) {
        global $db;
        $query = 'delete from ' . Config::dbSurvey() . '_communication where hnid = ' . prepareDatabaseString($hnid);
        return $db->executeQuery($query);
    }

    function storeUpload($updateSql, $urid) {
        global $db;
        if ($updateSql != '') {
            $query = 'insert into ' . Config::dbSurvey() . '_communication ';
            $query .= ' (urid, insertts, sqlcode, direction) values (';
            $query .= prepareDatabaseString($urid) . ', ';
            $query .= '"' . prepareDatabaseString(date('Y-m-d H:i:s')) . '", ';
            $query .= '"' . addslashes($updateSql) . '", ';
//            $query .= 'COMPRESS(AES_ENCRYPT("' . addslashes($updateSql) . '", "' . Config::smsCommunicationKey() . '")), ';
            $query .= '2)';
            $db->executeQuery($query);
        }
    }

    function getLastUploaded($urid) {
        global $db;
        $query = 'select max(ts) as lastupdated from ' . Config::dbSurvey() . '_communication where direction = 2 and urid = ' . prepareDatabaseString($urid);
        $result = $db->selectQuery($query);
        $row = $db->getRow($result);
        return $row['lastupdated'];
    }

    function curlToServer($fields, $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));


        return curl_exec($ch);
    }

}

?>