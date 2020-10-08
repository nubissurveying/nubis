<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

set_include_path(dirname(dirname(getcwd())));
require_once('constants.php');
require_once('functions.php');

require_once('dbConfig.php');
require_once('config.php');
require_once('database.php');

date_default_timezone_set(Config::timezone());
$id = loadvar('id');
$fieldname = loadvar('fieldname');
$p = loadvar('p');
$db = new Database();
if ($id != '' && $fieldname != '') {
    if ($p == 'show') { //show image
        $query = 'select AES_DECRYPT(picture, "' . Config::filePictureKey() . '") as picture1 from ' . Config::dbSurveyData() . '_pictures where primkey="' . $id . '" and variablename = "' . $fieldname . '"';
        $result = $db->selectQuery($query);
        if ($result != null && $db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            ob_clean();
            header('Content-type: image/jpg');
            if ($row['picture'] != null) {
                print($row['picture1']);
            } else {  //display 'empty' image
                ob_clean();
                header('Content-type: image/jpg');
                echo file_get_contents('../../images/nopicture.png');
            }
            exit;
        } else { //display 'empty' image
            ob_clean();
            header('Content-type: image/jpg');
            echo file_get_contents('../../images/nopicture.png');
            exit;
        }
    } else { //store
        $query = 'replace into ' . Config::dbSurveyData() . '_pictures (primkey, variablename, picture) VALUES (';
        $query .= '"' . addslashes($id) . '", ';
        $query .= '"' . addslashes($fieldname) . '", ';
        //$query .= '"' . addslashes(base64_decode(implode("", $_POST))) . '") ';

        $query .= 'AES_ENCRYPT("' . addslashes(base64_decode(implode("", $_POST))) . '", "' . Config::filePictureKey() . '")) ';

        $db->executeQuery($query);
    }
}
?>