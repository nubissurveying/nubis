<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Data {

    function __construct() {
        
    }

    function getRespondentData($suid, $primkey) {
        global $db;
        $survey = new Survey($suid);
        $key = "answer as answer_dec";
        if ($survey->getDataEncryptionKey() != "") {
            $key = "aes_decrypt(answer, '" . $survey->getDataEncryptionKey() . "') as answer_dec";
        }
        $query = "select variablename, " . $key . ", dirty, language, mode, ts from " . Config::dbSurveyData() . "_data where suid=" . $suid . " and primkey='" . $primkey . "' order by ts asc, variablename asc";
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $arr[] = $row;
                }
            }
        }
        return $arr;
    }

    function getRespondentPrimKeys($suid, $completed = true, $orderBy = "ts") {
        global $db;
        $where = "";
        if ($complete) {
            $where = " and completed=" . prepareDatabaseString(INTERVIEW_COMPLETED) . " ";
        }
        $query = "select distinct primkey from " . Config::dbSurveyData() . "_data where suid=" . $suid . " " . $where . " order by " . prepareDatabaseString($orderBy);
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $arr[] = $row["primkey"];
                }
            }
        }
        return $arr;
    }

    function getScreendumps($suid, $id) {
        global $db, $survey;
        $decrypt = "screen as screen_dec";
        if ($survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(screen, '" . $survey->getDataEncryptionKey() . "') as screen_dec";
        }
        $query = "select $decrypt from " . Config::dbSurveyData() . "_screendumps where suid=" . prepareDatabaseString($suid) . " and primkey='" . prepareDatabaseString($id) . "' order by ts asc";
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $arr[] = gzuncompress($row["screen_dec"]);
                    //$arr[] = ($row["screen"]);
                }
            }
        }
        return $arr;
    }

    function getScreendump($suid, $id, $cnt) {
        global $db, $survey;
        $decrypt = "screen as screen_dec";
        if ($survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(screen, '" . $survey->getDataEncryptionKey() . "') as screen_dec";
        }
        $query = "select $decrypt from " . Config::dbSurveyData() . "_screendumps where suid=" . prepareDatabaseString($suid) . " and primkey='" . prepareDatabaseString($id) . "' order by scdid limit " . $cnt . ", 1";
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                $row = $db->getRow($res);
                return gzuncompress($row["screen_dec"]);
            }
        }
        return "";
    }

    function getNumberOfScreenDumps($suid, $id) {
        global $db;
        $query = "select screen from " . Config::dbSurveyData() . "_screendumps where suid=" . prepareDatabaseString($suid) . " and primkey='" . prepareDatabaseString($id) . "'";
        $res = $db->selectQuery($query);
        $arr = array();
        if ($res) {
            return $db->getNumberOfRows($res);
        }
        return 0;
    }

    function getAggregrateDataTimings($suid, $variable, $cutoff = 301) {
        $cnt = 0;
        $cnt1 = 0;
        $cnt2 = 0;
        $cnt3 = 0;
        $cnt4 = 0;
        $cnt5 = 0;
        $cnt6 = 0;
        $cnt7 = 0;
        $cnt8 = 0;
        global $db;
        $select = "select variable, sum(timespent) as total2 from " . Config::dbSurveyData() . "_consolidated_times where suid=" . $suid . " and timespent < $cutoff group by variable order by variable asc"; // , ts asc
        $res = $db->selectQuery($select);
        if ($res) {

            $total = 0;
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $total++;
                    if ($row["total2"] < 30) {
                        $cnt++;
                    } else if ($row["total2"] < 60) {
                        $cnt1++;
                    } else if ($row["total2"] < 90) {
                        $cnt2++;
                    } else if ($row["total2"] < 120) {
                        $cnt3++;
                    } else if ($row["total2"] < 150) {
                        $cnt4++;
                    } else if ($row["total2"] < 180) {
                        $cnt5++;
                    } else if ($row["total2"] < 210) {
                        $cnt6++;
                    } else if ($row["total2"] < 240) {
                        $cnt7++;
                    } else if ($row["total2"] > 270) {
                        $cnt8++;
                    }
                }
            }
        }

        return array($cnt, $cnt1, $cnt2, $cnt3, $cnt4, $cnt5, $cnt6, $cnt7, $cnt8);
    }

    function getAggregrateData($variable, $name, &$brackets) {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $answertype = $variable->getAnswerType();
        if (inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            return null;
        }

        global $survey, $db;
        $arr = array();
        $dkarray = array();
        $decrypt = "answer as data_dec";
        if ($survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(answer, '" . $survey->getDataEncryptionKey() . "') as data_dec";
        }
        //if ($variable->isArray()) {
        //    $query = "select $decrypt from " . Config::dbSurveyData() . "_data where suid=" . $survey->getSuid() . ' and variablename like "' . $name . '"' . " order by primkey";
        //} else {
        $query = "select $decrypt from " . Config::dbSurveyData() . "_data where suid=" . $survey->getSuid() . ' and variablename = "' . $name . '"' . " order by primkey";
        //}
        $res = $db->selectQuery($query);
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $ans = $row["data_dec"];
                    if (inArray($ans, array(ANSWER_DK, "", ANSWER_RF, ANSWER_NA))) {
                        $dkarray["'" . $ans . "'"]++;
                    } else {

                        if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
                            // set of enum/dropdown, then look at all options selected
                            if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                                $ans = explode(SEPARATOR_SETOFENUMERATED, $ans);
                                foreach ($ans as $a) {
                                    $arr[$a]++;
                                }
                            } else {
                                $arr[$ans]++;
                            }
                        } else {
                            $arr[] = $ans;
                        }
                    }
                }
            }
        }

        // add non-chosen options
        $answertype = $variable->getAnswerType();
        if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $options = $variable->getOptions();
            foreach ($options as $opt) {
                if (!isset($arr[$opt["code"]])) {
                    $arr[$opt["code"]] = 0;
                }
            }
        }
        // define brackets and recode
        else if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB))) {
            if (inArray($answertype, array(ANSWER_TYPE_SLIDER, ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB))) {
                $min = floor($variable->getMinimum());
                $max = ceil($variable->getMaximum());
            } else {
                $min = floor(min(array_keys($arr)));
                $max = ceil(max(array_keys($arr)));
            }

            $splt = (abs(min($arr)) + abs(max($arr))) / OUTPUT_AGGREGATE_NUMBEROFBRACKETS;
            $summation = array();
            $labels = array();
            $labels[0] = "  " . (string) (0) . "-" . (string) (min($arr)); // first label
            for ($i = 0; $i < count($arr); $i++) {
                for ($j = 0, $start = min($arr); $j < OUTPUT_AGGREGATE_NUMBEROFBRACKETS; $j++, $start += $splt) {
                    if ($arr[$i] >= $start && $arr[$i] < $start + $splt) {
                        $summation[$j + 1] = (isset($summation[$j + 1]) ? $summation[$j + 1] + 1 : 1);
                    }
                    $labels[$j + 1] = "  " . (string) ($start) . "-" . (string) ($start + $splt);
                }
            }
            $brackets = $labels;
            $arr = $summation;

            foreach ($labels as $k => $l) {
                if (!isset($arr[$k])) {
                    $arr[$k] = 0;
                }
            }
        }

        /* add any empty options */
        $a = array(ANSWER_DK, "", ANSWER_RF, ANSWER_NA);
        foreach ($a as $a1) {
            if (!isset($dkarray["'" . $a1 . "'"])) {
                $dkarray["'" . $a1 . "'"] = 0;
            }
        }

        // sort array from low to high        
        ksort($arr, SORT_NUMERIC);
        ksort($dkarray, SORT_NATURAL);

        // add dkarray if active
        $arr = array_merge($arr, $dkarray);

        // return result        
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        return $arr;
    }

    function generateTimings($suid) {

        set_time_limit(0); // generating may take a while
// create table
        global $db;
        $create = "create table if not exists " . Config::dbSurveyData() . "_consolidated_times  (
                suid int(11) NOT NULL DEFAULT '1',
                primkey varchar(150) NOT NULL,
                stateid int(11) DEFAULT NULL,  
		begintime varchar(50) NOT NULL,
                variable varchar(50) NOT NULL,
                timespent int(11) NOT NULL DEFAULT '0',
                language int(11) NOT NULL DEFAULT '1',
                mode int(11) NOT NULL DEFAULT '1',
                version int(11) NOT NULL DEFAULT '1',
                ts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (suid,primkey,begintime,variable)
              ) ENGINE=MyIsam  DEFAULT CHARSET=utf8;";
        $db->executeQuery($create);

        // update
        $query = "delete from " . Config::dbSurveyData() . "_consolidated_times where suid=" . $suid;
        $db->executeQuery($create);

        // old non-group by compliant: $query = "REPLACE INTO " . Config::dbSurveyData() . "_consolidated_times SELECT suid, primkey, begintime, stateid, variable, avg(timespent) as timespent, language, mode, version, ts FROM " . Config::dbSurveyData() . "_times where suid=" . $suid . " group by primkey, begintime order by primkey asc";
        $query = "REPLACE INTO " . Config::dbSurveyData() . "_consolidated_times SELECT min(suid) as suid, primkey, begintime, min(stateid) as stateid, min(variable) as variable, avg(timespent) as timespent, min(language) as language, min(mode) as mode, min(version) as version, min(ts) as ts FROM " . Config::dbSurveyData() . "_times where suid=" . $suid . " group by primkey, (CONVERT_TZ(begintime,'UTC','America/Vancouver')), rgid";
        $db->executeQuery($query);
    }

    function getTimings($suid, $cutoff = 301) {
        $cnt = 0;
        $cnt1 = 0;
        $cnt2 = 0;
        $cnt3 = 0;
        $cnt4 = 0;
        $cnt5 = 0;
        $cnt6 = 0;
        $cnt7 = 0;
        $cnt8 = 0;
        global $db;
        $select = "select primkey, sum(timespent)/60 as total2 from " . Config::dbSurveyData() . "_consolidated_times where suid=" . $suid . "  and  timespent < $cutoff group by primkey order by primkey asc";
        $res = $db->selectQuery($select);
        if ($res) {

            // get everyone completed
            $completed = array();
            if (Config::useDataRecords()) {
                $query1 = "select primkey from " . Config::dbSurveyData() . "_datarecords where suid=" . $suid . ' and completed=1';
            }
            else {
                $query1 = "select distinct primkey from " . Config::dbSurveyData() . "_data where suid=" . $suid . ' and completed=1';
            }
            if ($result = $db->selectQuery($query1)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row1 = $db->getRow($result)) {
                        $completed[] = $row1['primkey'];
                    }
                }
            }
            $total = 0;
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    if (in_array($row["primkey"], $completed)) {
                        $total++;
                        if ($row["total2"] < 6) {
                            $cnt++;
                        } else if ($row["total2"] < 11) {
                            $cnt1++;
                        } else if ($row["total2"] < 16) {
                            $cnt2++;
                        } else if ($row["total2"] < 21) {
                            $cnt3++;
                        } else if ($row["total2"] < 26) {
                            $cnt4++;
                        } else if ($row["total2"] < 31) {
                            $cnt5++;
                        } else if ($row["total2"] < 36) {
                            $cnt6++;
                        } else if ($row["total2"] < 41) {
                            $cnt7++;
                        } else if ($row["total2"] > 40) {
                            $cnt8++;
                        }
                    }
                }
            }
        }

        return array($cnt, $cnt1, $cnt2, $cnt3, $cnt4, $cnt5, $cnt6, $cnt7, $cnt8);
    }

    function getTimingsDataOverTime($suid, &$labels, $cutoff = 301) {
        $select = "select primkey, sum(timespent)/60 as total2, min(language) as language, min(ts) as ts from " . Config::dbSurveyData() . "_consolidated_times where suid=" . $suid . " and timespent < $cutoff group by primkey";
        global $db;
        $res = $db->selectQuery($select, $this->db);
        $dates = array();
        if ($res) {

            // get everyone completed
            $completed = array();
            $tses = array();
            if (Config::useDataRecords()) {
                $query1 = "select primkey, ts from " . Config::dbSurveyData() . "_datarecords where suid=" . $suid . ' and completed=1';
            }
            else {
                $query1 = "select primkey, ts from " . Config::dbSurveyData() . "_data where suid=" . $suid . ' and variablename="' . VARIABLE_PRIMKEY . '" and completed=1';
            }
            if ($result = $db->selectQuery($query1)) {
                if ($db->getNumberOfRows($result) > 0) {                    
                    while ($row1 = $db->getRow($result)) {
                        $completed[] = $row1['primkey'];
                        $tses[$row1["primkey"]] = $row1["ts"];
                    }
                }
            }
            $total = 0;
            $dates2 = array();
            $dates3 = array();
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    if (in_array($row["primkey"], $completed)) {
                        $ts = strtotime(date("F j, Y", strtotime($tses[$row["primkey"]])));
                        if (isset($dates2[$ts])) {
                            $dates3[$ts] = $dates3[$ts] + 1;
                            $dates2[$ts] = $dates2[$ts] + $row["total2"];
                        } else {
                            $dates2[$ts] = $row["total2"];
                            $dates3[$ts] = 1;
                        }
                    }
                }

                // calculate averages
                foreach ($dates2 as $k => $d) {
                    $r = round($d / $dates3[$k], 2);
                    $dates[$k] = $r;
                }
            }
        }

        $labels2 = array_keys($dates);        
        sort($labels2);
        $labels = array();
        foreach ($labels2 as $l) {
            $labels[] = date("F j, Y", $l) . ' (' . $dates3[$l] . ')';
        }
        
        return $dates;
    }

    function getTimingsDataPerRespondent($suid, $prim, &$labels, $cutoff = 301) {
        $select = "select avg(timespent)/60 as total2, min(language) as language, min(ts) as ts from " . Config::dbSurveyData() . "_consolidated_times where suid=" . $suid . " and primkey='" . $prim . "' and timespent < $cutoff group by stateid order by stateid asc";
        global $db;
        $res = $db->selectQuery($select, $this->db);
        $dates = array();
        $labels2 = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $ts = strtotime($row["ts"]);
                    $dates[] = $row["total2"];
                    $labels2[] = $ts;
                }
            }
        }
        sort($labels2);
        foreach ($labels2 as $l) {
            $labels[] = date("F j, Y H:i:s", $l);
        }
        return $dates;
    }

    function getAggregrateDataOld($variable) {
        global $survey, $db;
        $arr = array();
        $decrypt = "data as data_dec";
        if ($survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(data, '" . $survey->getDataEncryptionKey() . "') as data_dec";
        }

        if (Config::useDataRecords()) {
            $query = "select $decrypt from " . Config::dbSurveyData() . "_datarecords where suid=" . $survey->getSuid() . $extracompleted . " order by primkey";
        }
        else {
            $query = "select $decrypt from " . Config::dbSurveyData() . "_data where suid=" . $survey->getSuid() . " and variablename='" . VARIABLE_PRIMKEY . "' " . $extracompleted . " order by primkey";
        }
        $res = $db->selectQuery($query);
        $datanames = array();
        if ($res) {
            if ($db->getNumberOfRows($res) == 0) {
                return 'No records found';
            } else {
                /* go through records */
                while ($row = $db->getRow($res)) {
                    $record = new DataRecord();
                    $record->setAllData(unserialize(gzuncompress($row["data_dec"])));
                    $data = $record->getDataForVariable($variable->getName());
                    foreach ($data as $rec) {
                        $arr[$rec->getAnswer()]++;
                    }
                }
            }
        }
        return $arr;
    }

    function getPlatformData($suid) {
        $survey = new Survey($suid);
        $answer = "cast(answer as char) as answer_dec";
        if ($survey->getDataEncryptionKey() != "") {
            $answer = "cast(aes_decrypt(answer, '" . $survey->getDataEncryptionKey() . "') as char) as answer_dec";
        }
        $select = "select " . $answer . " from " . Config::dbSurveyData() . "_data where suid=" . $suid . " and variablename='" . VARIABLE_PLATFORM . "'";
        global $db;
        $res = $db->selectQuery($select);
        $data = array();
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $data[] = $row["answer_dec"];
                }
            }
        }
        return $data;
    }

    function getParaData($variable, $name, &$brackets) {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $answertype = $variable->getAnswerType();
        if (inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
            return null;
        }
        $this->processParaData();
        //return array();

        global $survey, $db;
        $arr = array();
        $key = $survey->getDataEncryptionKey();
        if ($key != "") {
            $query = "select variablename, sum(aes_decrypt(answer, '" . $key . "')) as total from " . Config::dbSurveyData() . "_processed_paradata where suid=" . $survey->getSuid() . " and variablename like '" . $name . "\_%' group by variablename order by variablename";
        } else {
            $query = "select variablename, sum(answer) as total from " . Config::dbSurveyData() . "_processed_paradata where suid=" . $survey->getSuid() . " and variablename like '" . $name . "\_%' group by variablename order by variablename";
        }
        $res = $db->selectQuery($query);
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $code = str_replace(strtoupper($name . "_"), "", strtoupper($row["variablename"]));
                    $arr[strtoupper($code)] = $row["total"];
                }
            }
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        return $arr;
    }

    function processParaData($name = "") {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;


        global $survey, $db;

        $query = "select max(pid) as pid from " . Config::dbSurveyData() . "_processed_paradata where suid=" . $survey->getSuid();
        $pid = 0;
        $res = $db->selectQuery($query);
        if ($res) {
            $row = $db->getRow($res);
            $pid = $row["pid"];
            if ($pid == "") {
                $pid = 0;
            }
        }

        $arr = array();
        $decrypt = "paradata as data_dec";
        $key = "";
        if ($survey->getDataEncryptionKey() != "") {
            $key = $survey->getDataEncryptionKey();
            $decrypt = "aes_decrypt(paradata, '" . $survey->getDataEncryptionKey() . "') as data_dec";
        }

        if ($name == "") {
            $query = "select *, $decrypt from " . Config::dbSurveyData() . "_paradata where pid > $pid and suid=" . $survey->getSuid() . ' order by primkey, pid asc';
        } else {
            $query = "select *, $decrypt from " . Config::dbSurveyData() . "_paradata where pid > $pid and suid=" . $survey->getSuid() . ' and (displayed = "' . $name . '" OR displayed like "%' . $name . '~%") order by primkey, pid asc';
        }

        $res = $db->selectQuery($query);
        $codes = array_values(Common::errorCodes());
        if ($res) {
            $oldprimkey = "";
            $arr = array();
            if ($db->getNumberOfRows($res) > 0) {
                $num = $db->getNumberOfRows($res);
                $cnt = 0;
                while ($row = $db->getRow($res)) {

                    // end of primkey, so store
                    if (($oldprimkey != "" && $row["primkey"] != $oldprimkey)) {

                        // k: varname
                        // a: array of error codes with number of times
                        foreach ($arr as $k => $a) {
                            foreach ($a as $error => $times) {
                                $query = "replace into " . Config::dbSurveyData() . "_processed_paradata (`pid`, `suid`, `primkey`, `rgid`, `variablename`, `answer`, `language`, `mode`, `version`, `ts`) values (";
                                if ($key != "") {
                                    $query .= $row["pid"] . "," . $row["suid"] . ",'" . $row["primkey"] . "'," . $row["rgid"] . ",'" . strtolower($k . "_" . $error) . "',aes_encrypt('" . $times . "','" . $key . "')," . $row["language"] . "," . $row["mode"] . "," . $row["version"] . ",'" . $row["ts"] . "'";
                                } else {
                                    $query .= $row["pid"] . "," . $row["suid"] . ",'" . $row["primkey"] . "'," . $row["rgid"] . ",'" . strtolower($k . "_" . $error) . "','" . $times . "'," . $row["language"] . "," . $row["mode"] . "," . $row["version"] . ",'" . $row["ts"] . "'";
                                }
                                $query .= ")";
                                $db->executeQuery($query);
                            }
                        }

                        // reset
                        $arr = array();                        
                    }                    

                    $oldprimkey = $row["primkey"];
                    $line = strtoupper($row["displayed"]);

                    // if displayed == variable OR displayed contains ~varname~ or displayed starts with varname~, process; otherwise skip
                    if ($name == "" || $line == strtoupper($name) || contains($line, "~" . $name . "~") || startsWith($line, $name . "~")) {

                        $line = $row["data_dec"];
                        $line = str_replace("FO=", "FO:", $line);
                        $line = str_replace("FI=", "FI:", $line);
                        $a = explode("||", $line);
                        $displayed = explode("~", $row["displayed"]);
                        $variables = array();
                        foreach ($displayed as $d) {
                            if (startsWith($d, ROUTING_IDENTIFY_SUBGROUP) == false && startsWith($d, ROUTING_IDENTIFY_ENDSUBGROUP) == false) {
                                $variables[] = $d;
                            }
                        }

                        foreach ($a as $k) {
                            $t = explode(":", $k);
                            $code = $t[0];

                            // error code
                            if (inArray($code, $codes)) {
                                $s = explode("=", $t[1]);
                                $varname = $s[0];
                                $number = str_replace("answer", "", str_replace("_name[]", "", $varname));

                                // find varname
                                if (isset($variables[$number - 1])) {
                                    $variable = $variables[$number - 1];
                                    if (isset($arr[strtoupper($variable)])) {
                                        $vararray = $arr[strtoupper($variable)];
                                    } else {
                                        $vararray = array();
                                    }
                                    if (isset($vararray[strtoupper($code)])) {
                                        $vararray[strtoupper($code)] = $vararray[strtoupper($code)] + 1;
                                    } else {
                                        $vararray[strtoupper($code)] = 1;
                                    }
                                    $arr[strtoupper($variable)] = $vararray;
                                }
                            } else if (inArray($code, array("FO", "FI"))) {
                                foreach ($variables as $variable) {
                                    if (isset($arr[strtoupper($variable)])) {
                                        $vararray = $arr[strtoupper($variable)];
                                    } else {
                                        $vararray = array();
                                    }
                                    if (isset($vararray[strtoupper($code)])) {
                                        $vararray[strtoupper($code)] = $vararray[strtoupper($code)] + 1;
                                    } else {
                                        $vararray[strtoupper($code)] = 1;
                                    }
                                    $arr[strtoupper($variable)] = $vararray;
                                }
                            }
                        }
                    }
                    $cnt++;
                    
                    // this was last one, so store
                    if ($cnt == $num) {

                        // k: varname
                        // a: array of error codes with number of times
                        foreach ($arr as $k => $a) {
                            foreach ($a as $error => $times) {
                                $query = "replace into " . Config::dbSurveyData() . "_processed_paradata (`pid`, `suid`, `primkey`, `rgid`, `variablename`, `answer`, `language`, `mode`, `version`, `ts`) values (";
                                if ($key != "") {
                                    $query .= $row["pid"] . "," . $row["suid"] . ",'" . $row["primkey"] . "'," . $row["rgid"] . ",'" . strtolower($k . "_" . $error) . "',aes_encrypt('" . $times . "','" . $key . "')," . $row["language"] . "," . $row["mode"] . "," . $row["version"] . ",'" . $row["ts"] . "'";
                                } else {
                                    $query .= $row["pid"] . "," . $row["suid"] . ",'" . $row["primkey"] . "'," . $row["rgid"] . ",'" . strtolower($k . "_" . $error) . "','" . $times . "'," . $row["language"] . "," . $row["mode"] . "," . $row["version"] . ",'" . $row["ts"] . "'";
                                }
                                $query .= ")";
                                $db->executeQuery($query);
                            }
                        }

                        // reset
                        $arr = array();                        
                    }
                }
            }
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

}

?>