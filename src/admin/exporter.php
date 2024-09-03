<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */
           
class Exporter {

    private $suid;

    function __construct($suid) {
        $this->suid = $suid;
    }

    function export() {
        $type = loadvar(SETTING_EXPORT_TYPE);
        switch ($type) {
            case EXPORT_TYPE_SQL:
                $this->exportSQL();
                break;
            case EXPORT_TYPE_SERIALIZE:
                $this->exportNubis();
                break;
        }
    }

    function exportSQL() {
        global $db;
        $returnStr = "";
        $history = loadvar(SETTING_EXPORT_HISTORY);
        $create = loadvar(SETTING_EXPORT_CREATE);
        $alltables = Common::allTables();

        // create table statements
        if ($create == EXPORT_CREATE_YES) {
            foreach ($alltables as $export) {
                $create = "SHOW CREATE TABLE " . Config::dbSurvey() . prepareDatabaseString($export);
                $rescreate = $db->selectQuery($create);
                if ($rescreate) {
                    $row2 = $db->getRow($rescreate);
                    $str = str_ireplace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row2[1]);
                    $str = str_ireplace(Config::dbSurvey() . $export, EXPORT_PLACEHOLDER_TABLE . $export, $str);
                    if ($returnStr != "") {
                        $returnStr .= "\r\n";
                    }
                    $returnStr .= $str . EXPORT_SQL_DELIMITER . "\r\n";
                }
            }
        }
        
        // content
        $exporttables = Common::surveyExportTables();

        /* based off https://davidwalsh.name/backup-mysql-database-php */
        foreach ($exporttables as $export) {
            if (strtoupper($export) == strtoupper("_tracks") && $history != EXPORT_HISTORY_YES) {
                continue;
            }
            $query = 'select * from ' . Config::dbSurvey() . prepareDatabaseString($export) . ' where suid=' . prepareDatabaseString($this->suid);
            $result = $db->selectQuery($query);
            $num_fields = $db->getNumberOfFields($result);
            $fields = $db->getFields($result);
            $num_fields = sizeof($fields);
            $fieldstr = ""; // `
            for ($i = 0; $i < $num_fields; $i++) {
                if ($fieldstr != "") {
                    $fieldstr .= ",";
                }
                $fi = $fields[$i];
                $fieldstr .= "`" . $fi->name . "`";
            }
            // add content (table name and suid are added as placeholders)
            while ($row = $db->getRow($result)) {
                $returnStr .= IMPORT_STATEMENT_INSERT . ' ' . EXPORT_PLACEHOLDER_TABLE . $export . ' (' . $fieldstr . IMPORT_STATEMENT_INSERT_VALUES;
                for ($j = 0; $j < $num_fields; $j++) {
                    $fi = $fields[$j];
                    if (strtoupper($fi->name) == strtoupper(EXPORT_COLUMN_SUID)) {
                        $returnStr .= '"' . EXPORT_PLACEHOLDER_SUID . '"';
                    } else if (strtoupper($fi->name) == strtoupper(EXPORT_COLUMN_URID)) {
                        $returnStr .= '"' . EXPORT_PLACEHOLDER_URID . '"';
                    } else {
                        if (isset($row[$j])) {
                            $returnStr .= '"' . prepareDatabaseString($row[$j]) . '"';
                        } else {
                            $returnStr .= '""';
                        }
                    }
                    if ($j < ($num_fields - 1)) {
                        $returnStr .= ',';
                    }
                }
                $returnStr .= ")" . EXPORT_SQL_DELIMITER . "\r\n";
            }
        }

        // output as SQL file

        /* allow for time */
        set_time_limit(0);

        // http://www.richnetapps.com/the-right-way-to-handle-file-downloads-in-php/

        /* declare headers */
        header("Content-Description: File Transfer");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . Config::dbSurvey() . EXPORT_FILE_SQL . '; modification-date="' . date('r', time()) . '";');
        header("Content-Type: application/sql");

        /* prevent caching (http://stackoverflow.com/questions/13640109/how-to-prevent-browser-cache-for-php-site) */
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        // http://stackoverflow.com/questions/15299325/x-download-options-noopen-equivalent
        header("X-Content-Type-Options: nosniff"); // http://stackoverflow.com/questions/21723436/firefox-downloads-text-plain-instead-of-showing-it

        /* clean buffer before outputting file */
        ob_end_clean();

        // echo output
        echo $returnStr;

        // stop
        exit;
    }

    function exportNubis() {
        global $db;
        $exporttables = Common::surveyExportTables();
        $returnStr = "";

        /* based off https://davidwalsh.name/backup-mysql-database-php */
        foreach ($exporttables as $export) {
            if (strtoupper($export) == strtoupper("_tracks")) {
                continue;
            }
            $query = 'select * from ' . Config::dbSurvey() . prepareDatabaseString($export) . ' where suid=' . prepareDatabaseString($this->suid);
            $result = $db->selectQuery($query);
            $num_fields = $db->getNumberOfFields($result);
            $fields = $db->getFields($result);
            $num_fields = sizeof($fields);            
            $fieldstr = ""; // `
            for ($i = 0; $i < $num_fields; $i++) {
                if ($fieldstr != "") {
                    $fieldstr .= ",";
                }
                $fi = $fields[$i];
                $fieldstr .= $fi->name;
            }
            // add content (table name and suid are added as placeholders)
            while ($row = $db->getRow($result)) {
                $returnStr .= $export . EXPORT_DELIMITER . $fieldstr . EXPORT_DELIMITER;
                for ($j = 0; $j < $num_fields; $j++) {
                    $fi = $fields[$j];
                    if (strtoupper($fi->name) == strtoupper(EXPORT_COLUMN_SUID)) {
                        $returnStr .= '"' . EXPORT_PLACEHOLDER_SUID . '"';
                    } else if (strtoupper($fi->name) == strtoupper(EXPORT_COLUMN_URID)) {
                        $returnStr .= '"' . EXPORT_PLACEHOLDER_URID . '"';
                    } else {
                        if (isset($row[$j])) {
                            $returnStr .= '"' . prepareExportString($row[$j]) . '"';
                        } else {
                            $returnStr .= prepareExportString('""');
                        }
                    }
                    if ($j < ($num_fields - 1)) {
                        $returnStr .= ',';
                    }
                }
                $returnStr .= "\r\n";
            }
        }

        // output as SQL file

        /* allow for time */
        set_time_limit(0);

        // http://www.richnetapps.com/the-right-way-to-handle-file-downloads-in-php/

        /* declare headers */
        header("Content-Description: File Transfer");
        header("Content-Type: application/force-download");
        header("Content-Type: application/download");
        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=" . Config::dbSurvey() . EXPORT_FILE_NUBIS . '; modification-date="' . date('r', time()) . '";');
        header("Content-Type: application/nubis");

        /* prevent caching (http://stackoverflow.com/questions/13640109/how-to-prevent-browser-cache-for-php-site) */
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        // http://stackoverflow.com/questions/15299325/x-download-options-noopen-equivalent
        header("X-Content-Type-Options: nosniff"); // http://stackoverflow.com/questions/21723436/firefox-downloads-text-plain-instead-of-showing-it

        /* clean buffer before outputting file */
        ob_end_clean();

        // echo output
        echo $returnStr;

        // stop
        exit;
    }

}