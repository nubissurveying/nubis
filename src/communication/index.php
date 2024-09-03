<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

error_reporting(0);
ini_set('display_errors', 0);

set_include_path(dirname(getcwd()));

require_once('constants.php');
require_once('functions.php');
require_once("dbConfig.php");
$loaded = dbConfig::load("../conf.php");
require_once("config.php");

if (Config::allowCommunication() == false) {
    exit;
}

if (decryptC(loadvar("k"), Config::smsComponentKey()) != Config::communicationAccessKey()) {
    exit;
}

require_once('database.php');
require_once('communication.php');

date_default_timezone_set(Config::timezone());

$p = loadvar('p');
$urid = loadvar('urid');
$db = new Database();
$communication = new Communication();


$returnValue = 'error';
if ($p == 'upload') { //upload data!
    $communication->storeUpload($_POST['query'], $urid);
    $communication->importTable($_POST['query']);
    $returnValue = 'ok';
} elseif ($p == 'updateavailable') { //is there an update available?
    $returnValue = 'no';
    if (sizeof($communication->getUserQueries($urid)) > 0) {
        $returnValue = 'yes';
    }
    if (sizeof($communication->getUserScripts($urid)) > 0) {
        $returnValue = 'yes';
    }
} elseif ($p == 'receive') { //receive the update
    $returnValue = '';
    if (sizeof($communication->getUserQueries($urid)) > 0) { //sql
        foreach ($communication->getUserQueries($urid) as $row) {
            if (trim($row['sqlcode']) != '') {
                $returnValue .= '1!~!~!' . ($row['sqlcode']) . "!~!~!";
            }
        }
    }
    if (sizeof($communication->getUserScripts($urid)) > 0) { //scripts
        foreach ($communication->getUserScripts($urid) as $row) {
            if (trim($row['sqlcode']) != '') {
                $returnValue .= '2~' . $row['filename'] . '!~!~!' . ($row['sqlcode']) . "!~!~!";
            }
        }
    }
} elseif ($p == 'datareceived') {
    $communication->setUpdateReceived($urid);
    $returnValue = 'ok';
}

echo $returnValue;

?>