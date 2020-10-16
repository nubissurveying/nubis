<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Importer {

    private $importdb;
    private $db;
    private $sourcetable;
    private $targettable;
    private $addtosuid;
    private $suid;
    private $syid;
    private $languages;

    function __construct() {

        global $db;

        $this->db = $db;

        $this->sourcetable = loadvar("databaseTablename");

        $this->targettable = Config::dbSurvey();
    }

    function import() {
        $importtype = loadvar(SETTING_IMPORT_TYPE);
        switch ($importtype) {
            case IMPORT_TYPE_NUBIS:
                return $this->importNubis();
            case IMPORT_TYPE_MMIC:
                return $this->importMMIC();
            case IMPORT_TYPE_BLAISE:
                return $this->importBlaise();
        }
    }

    function importBlaise() {
        $arr = $_FILES[SETTING_IMPORT_TEXT];
        if (sizeof($arr) == 0) {
            return Language::messageImportNoFile();
        }
        $name = $arr["name"];
        if (!endsWith($name, EXPORT_FILE_BLAISE_INC) && !endsWith($name, EXPORT_FILE_BLAISE_BLA)) {
            return Language::messageImportInvalidFile();
        }
        $str = file_get_contents($arr["tmp_name"]);
        if ($str == "") {
            return Language::messageImportInvalidBlaiseFile();
        }

        $urid = $_SESSION['URID'];
        $user = new User($urid);
        if (loadvar(SETTING_IMPORT_AS) == IMPORT_TARGET_ADD) {
            $this->addtosuid = loadvar("targetsurvey");
        }
        // replace
        else if (loadvar(SETTING_IMPORT_AS) == IMPORT_TARGET_REPLACE) {

            $this->addtosuid = 1;

            /* delete existing content */
            $tables = Common::surveyTables();
            foreach ($tables as $table) {
                $query = "delete from " . Config::dbSurvey() . $table;
                $this->db->executeQuery($query);
            }

            /* delete existing data */
            $tables = Common::surveyDataTables();
            foreach ($tables as $table) {
                if ($table == "_actions") {
                    $query = "delete from " . Config::dbSurvey() . $table . " where suid != ''";
                } else {
                    $query = "delete from " . Config::dbSurvey() . $table;
                }
                $this->db->executeQuery($query);
            }

            /* delete test data */
            $tables = Common::surveyTestDataTables();
            foreach ($tables as $table) {
                if ($table == "_actions") {
                    $query = "delete from " . Config::dbSurvey() . $table . " where suid != ''";
                } else {
                    $query = "delete from " . Config::dbSurvey() . $table;
                }
                $this->db->executeQuery($query);
            }
        }

        // prepare
        set_time_limit(0);
        $survey = new Survey($this->addtosuid);

        // import blaise variable file (.inc)
        if (endsWith($name, EXPORT_FILE_BLAISE_INC)) {
            $result = $this->importBlaiseInc($survey, $str);
            if ($result != "") {
                $survey->remove();
                return $result;
            }
        }
        // import blaise routing file (.bla)
        else if (endsWith($name, EXPORT_FILE_BLAISE_BLA)) {
            //return "Currently not supported";
            $result = $this->importBlaiseInc($survey, $str);
            if ($result != "") {
                $survey->remove();
                return $result;
            }
        }
        return "";
        // compile        
        $compiler = new Compiler($this->addtosuid, getSurveyVersion($survey));

        // sections
        $sections = $survey->getSections();
        foreach ($sections as $section) {
            $mess = $compiler->generateEngine($section->getSeid());
        }

        $mess = $compiler->generateSections();
        $mess = $compiler->generateVariableDescriptives();
        $mess = $compiler->generateTypes();
        $mess = $compiler->generateGetFills();
        $mess = $compiler->generateSetFills();
        $mess = $compiler->generateInlineFields();
        $mess = $compiler->generateGroups();

        $user = new User($_SESSION['URID']);
        $mods = explode("~", $survey->getAllowedModes());
        foreach ($mods as $m) {
            $user->setLanguages($this->addtosuid, $m, $survey->getAllowedLanguages($m));
        }
        $user->saveChanges();

        // return result
        return "";
    }

    function lookupType($survey, $name) {
        $type = $survey->getTypeByName($name);
        if ($type->getTyd() != "") {
            return $type->getTyd();
        }
        return "";
    }

    function addType($survey, &$tid, $name, $options) {
        if (trim($name) == "") {
            return;
        }
        // disable for now
        //return;

        $testtype = $survey->getTypeByName($name);
        if ($testtype->getTyd() != "") {
            $type = $testtype;
        } else {
            $type = new Type();
            $type->setSuid($this->suid);
            $type->setTyd($tid);
            $type->setName($name);
            $tid++;
        }

        // array
        $isarray = false;
        if (startsWith($options, "ARRAY ")) {
            $isarray = true;
            $type->setArray(ARRAY_ANSWER_YES);
            $options = explode(" OF ", $options);
            $options = trim($options[1]);
        }

        // range
        if ($isarray == false && contains($options, "..")) {
            $r = explode("..", $options);
            $min = trim($r[0]);
            $max = trim($r[1]);
            $type->setAnswerType(ANSWER_TYPE_RANGE);
            $type->setMinimum($min);
            $type->setMaximum($max);
            
        } else if (startsWith($options, "(")) {
            $type->setAnswerType(ANSWER_TYPE_ENUMERATED); // override on variable level
            $options = substr($options, 1); // remove first (
            $options = substr($options, 0, strlen($options) - 1); // remove last (
            $options = explode("\n", $options);
            $optout = array();
            
            foreach ($options as $opt) {
                $firstpos = strpos($opt, '"');
                $lastpos = strrpos($opt, '"');
                $opt = substr($opt, $firstpos + 1, $lastpos);
                $lastquote = strrpos($opt, '"');
                $opt = substr($opt, 0, $lastquote);
                $dot = strpos($opt, ".");
                $optiontext = substr($opt, 0, $dot) . substr($opt, $dot + 1, strlen($opt));
                $optout[] = $optiontext;
            }
            
            $type->setOptionsText(implode("\n", $optout));
        } else if (startsWith($options, "STRING")) {
            
            $type->setAnswerType(ANSWER_TYPE_STRING);
        } else if (startsWith($options, "INTEGER")) {
            
            $type->setAnswerType(ANSWER_TYPE_INTEGER);
        } else if (startsWith($options, "REAL")) {
            
            $type->setAnswerType(ANSWER_TYPE_DOUBLE);
        } else {
            // type that refers itself to another type, not supported in nubis, ignore
            return;
        }

        // save type
        $type->save();
    }

    function addVariable($survey, &$vsid, $name, $text, $label, $type, $options, $sectionidentifier, $array, $position, $issectionvar = false) {

        if (trim($name) == "") {
            return;
        }
        // disable for now
        //return;

        // strip any (EXTRANAME) statements
        if (contains($name, "(")) {
            $rr = explode("(", $name);
            $name = trim($rr[0]);
        }

        $testvar = $survey->getVariableDescriptiveByName($name);
        if ($testvar->getVsid() != "") {
            $vd = $testvar;
        } else {
            $vd = new VariableDescriptive();
            $vd->setSuid($this->suid);
            $vd->setVsid($vsid);
            $vsid++;
            $vd->setName($name);
        }
        $vd->setPosition($position);
        $text = trim($text);
        $text = substr($text, 1);
        $text = substr($text, 0, strlen($text) - 1);
        $vd->setQuestion(trim($text));
        $vd->setDescription(trim($label));
        $vd->setSeid($sectionidentifier);

        if ($array == "yes") {
            $vd->setArray(ARRAY_ANSWER_YES);
        }

        // check for type    
        $setofenum = false;
        if (startsWith($type, "SET OF")) {
            $temptype = trim(str_ireplace("SET OF", "", $type));
            if ($temptype != "") {
                $rr = explode("SET OF", trim($type));
                if (sizeof($rr) > 1) {
                    $type = trim($rr[1]);
                    $setofenum = true;
                }
            }
        }
        $tyd = $this->lookupType($survey, $type);
        if ($tyd == "") {

            // section
            if ($issectionvar == true) {
                $sct = $survey->getSectionByName($type);
                if ($sct->getSeid() != "") {
                    $vd->setAnswerType(ANSWER_TYPE_SECTION);
                    $vd->setHidden(HIDDEN_YES);
                    if ($sct->getSeid() != "") {
                        $vd->setSection($sct->getSeid());
                    }
                } else {
                    
                }
            }
            // range
            else if (contains($options, "..")) {
                $r = explode("..", $options);
                $min = trim($r[0]);
                $max = trim($r[1]);
                $vd->setAnswerType(ANSWER_TYPE_RANGE);
                $vd->setMinimum($min);
                $vd->setMaximum($max);
            }
            // range
            else if (contains($type, "..")) {
                $r = explode("..", $type);
                $min = trim($r[0]);
                $max = trim($r[1]);
                $vd->setAnswerType(ANSWER_TYPE_RANGE);
                $vd->setMinimum($min);
                $vd->setMaximum($max);
            } else if (startsWith($type, "SET OF")) {
                $vd->setAnswerType(ANSWER_TYPE_SETOFENUMERATED);
                $options = substr($options, 1); // remove first (
                $options = substr($options, 0, strlen($options) - 1); // remove last (
                $options = explode("\n", $options);
                $optout = array();
                $cnt = 0;
                foreach ($options as $opt) {

                    $second = substr(trim($opt), 1, 1);
                    if (startsWith(trim($opt), "a") && is_numeric($second)) {
                        $firstpos = strpos($opt, '"');
                        if (endsWith($opt, '",') || endsWith($opt, '"')) {
                            $lastpos = strrpos($opt, '"');
                        } else {
                            $lastpos = strlen($opt);
                        }

                        $opt = substr($opt, $firstpos + 1, $lastpos);

                        if (endsWith($opt, '",') || endsWith($opt, '")')) {
                            $lastquote = strrpos($opt, '",');
                        } else if (endsWith($opt, '")')) {
                            $lastquote = strrpos($opt, '")');
                        } else {
                            $lastquote = strlen($opt);
                        }

                        $opt = substr($opt, 0, $lastquote);
                        if (endsWith($opt, '"')) {
                            $opt = substr($opt, 0, strlen($opt) - 1);
                        }

                        $dot = strpos($opt, ".");
                        $optiontext = substr($opt, 0, $dot) . substr($opt, $dot + 1, strlen($opt));
                        $optout[$cnt] = $optiontext;
                        $cnt++;
                    } else {
                        if (endsWith($opt, '",')) {
                            $opt = str_replace('",', "", $opt);
                        } else if (endsWith($opt, '"')) {
                            $opt = str_replace('"', "", $opt);
                        }

                        $previous = $optout[$cnt - 1];
                        $optout[$cnt - 1] = $previous . " " . $opt;
                    }
                }

                $vd->setOptionsText(implode("\n", $optout));
            } else if (startsWith($type, "(")) {
                $vd->setAnswerType(ANSWER_TYPE_ENUMERATED);
                $options = $type . "\n" . $options;
                $options = substr($options, 1); // remove first (
                $options = substr($options, 0, strlen($options) - 1); // remove last (
                $options = explode("\n", $options);
                $optout = array();
                $cnt = 0;
                foreach ($options as $opt) {

                    $second = substr(trim($opt), 1, 1);
                    if (startsWith(trim($opt), "a") && is_numeric($second)) {
                        $firstpos = strpos($opt, '"');
                        if (endsWith($opt, '",') || endsWith($opt, '"')) {
                            $lastpos = strrpos($opt, '"');
                        } else {
                            $lastpos = strlen($opt);
                        }

                        $opt = substr($opt, $firstpos + 1, $lastpos);

                        if (endsWith($opt, '",') || endsWith($opt, '")')) {
                            $lastquote = strrpos($opt, '",');
                        } else if (endsWith($opt, '")')) {
                            $lastquote = strrpos($opt, '")');
                        } else {
                            $lastquote = strlen($opt);
                        }

                        $opt = substr($opt, 0, $lastquote);
                        if (endsWith($opt, '"')) {
                            $opt = substr($opt, 0, strlen($opt) - 1);
                        }

                        $dot = strpos($opt, ".");
                        $optiontext = substr($opt, 0, $dot) . substr($opt, $dot + 1, strlen($opt));
                        $optout[$cnt] = $optiontext;
                        $cnt++;
                    } else {
                        if (endsWith($opt, '",')) {
                            $opt = str_replace('",', "", $opt);
                        } else if (endsWith($opt, '"')) {
                            $opt = str_replace('"', "", $opt);
                        }
                        
                        $previous = $optout[$cnt - 1];
                        $optout[$cnt - 1] = $previous . " " . $opt;
                    }
                }
                $vd->setOptionsText(implode("\n", $optout));
            } else if (startsWith($type, "STRING")) {
                $vd->setAnswerType(ANSWER_TYPE_STRING);
            } else if (startsWith($type, "INTEGER")) {
                $vd->setAnswerType(ANSWER_TYPE_INTEGER);
            } else if (startsWith($type, "REAL")) {
                $vd->setAnswerType(ANSWER_TYPE_DOUBLE);
            } else if (startsWith($type, "TIMETYPE")) {
                $vd->setAnswerType(ANSWER_TYPE_TIME);
            } else if (startsWith($type, "DATETYPE")) {
                $vd->setAnswerType(ANSWER_TYPE_DATE);
            } else {
                $vd->setAnswerType(ANSWER_TYPE_STRING); // default to string if unknown
            }
        } else {

            if ($setofenum == false) {
                $vd->setAnswerType(SETTING_FOLLOW_TYPE);
            } else {
                $vd->setAnswerType(ANSWER_TYPE_SETOFENUMERATED);
            }
            $vd->setTyd($tyd);
        }

        // save variable
        $vd->save();
    }

    function importBlaiseInc($survey, $str) {

        // all sections
        $subblocks = array();

        // temp replace of ENDBLOCK
        $str = str_replace("ENDBLOCK ", "ENDSECTION ", $str);

        $temp = explode("BLOCK ", $str);
        $query = "select max(seid) as max, max(position) as maxposition from " . Config::dbSurvey() . "_sections";
        $r = $this->db->selectQuery($query);
        $row = $this->db->getRow($r);
        $seid = $row["max"] + 1;
        $order = $row["maxposition"] + 1;
        $pid = 0;
        $sectionnames = array();
        $descriptions = array();
        $this->suid = $this->addtosuid;

        $survey->setSuid($this->suid);

        // only look if we have a file with at least one BLOCK statement
        if (contains($str, "BLOCK")) {
            foreach ($temp as $v) {
                $all = explode("\n", $v);
                $al = trim($all[0]);
                if ($al == "" || startsWith($al, "TYPE")) {
                    continue;
                }
                $sectionnames[$seid] = $all[0];
                $sub = explode("{", $all[0]);
                $name = trim($sub[0]);
                $description = trim(str_replace("}", "", $sub[1]));
                $testsection = $survey->getSectionByName($name);
                if ($testsection->getSeid() == "") {

                    $query = "replace into " . $this->targettable . "_sections (suid, seid, name, position, pid) values (";
                    $query .= prepareDatabaseString($this->addtosuid) . ",";
                    $query .= prepareDatabaseString($seid) . ",";
                    $query .= "'" . prepareDatabaseString($name) . "',";
                    $query .= prepareDatabaseString($order) . ",";
                    $query .= prepareDatabaseString($pid) . ")";
                    $this->db->executeQuery($query);

                    /* add rest as settings */
                    $this->addSetting($seid, OBJECT_SECTION, SETTING_DESCRIPTION, $description);
                    $this->addSetting($seid, OBJECT_SECTION, SETTING_HIDDEN, HIDDEN_NO);

                    // increase counters
                    $seid++;
                    $order++;
                } else {
                    $seid = $testsection->getSeid();
                    $testsection->setDescription($description);
                    $testsection->save();
                }

                if ($description == "") {
                    $descriptions[$seid] = $name;
                } else {
                    $descriptions[$seid] = $description;
                }
            }
        }

        echo "<br/><br/><br/>";

        $all = explode(PHP_EOL, $str);
        $fields = array();
        $auxfields = array();
        $locals = array();
        $types = array();

        // chop up file in different blocks
        for ($i = 0; $i < sizeof($all); $i++) {
            $a = trim($all[$i]);

            // FIELDS start
            if (startsWith($a, "FIELDS")) {

                // find where it ends
                $ending = $this->findEnd($all, $i);
                if ($all[$ending] != "" && $this->isKeyword($all[$ending])) {
                    $subarray = array_slice($all, $i, ($ending - $i + 1));
                } else {
                    $subarray = array_slice($all, $i, ($ending - $i));
                }
                $fields[] = $subarray;
            }
            // AUXFIELDS start
            elseif (startsWith($a, "AUXFIELDS")) {

                // find where it ends
                $ending = $this->findEnd($all, $i);
                if ($all[$ending] != "" && $this->isKeyword($all[$ending])) {
                    $subarray = array_slice($all, $i, ($ending - $i + 1));
                } else {
                    $subarray = array_slice($all, $i, ($ending - $i));
                }
                $auxfields[] = $subarray;
            }
            // LOCALS start
            elseif (startsWith($a, "LOCALS")) {

                // find where it ends
                $ending = $this->findEnd($all, $i);
                
                if ($all[$ending] != "" && $this->isKeyword($all[$ending])) {
                    $subarray = array_slice($all, $i, ($ending - $i + 1));
                } else {
                    $subarray = array_slice($all, $i, ($ending - $i));
                }
                
                $locals[] = $subarray;
            }
            // TYPES start
            elseif (startsWith($a, "TYPE")) {

                // find where it ends
                $ending = $this->findEnd($all, $i);
                if ($all[$ending] != "" && $this->isKeyword($all[$ending])) {
                    $subarray = array_slice($all, $i, ($ending - $i + 1));
                } else {
                    $subarray = array_slice($all, $i, ($ending - $i));
                }
                $types[] = $subarray;
            }
            // PROCEDURE start
            elseif (startsWith($a, "PROCEDURE")) {
                /* ignore */

                // find where it ends
                $ending = $this->findEndProcedure($all, $i); // ENDPROCEDURE
                $subarray = array_slice($all, $i, ($ending - $i + 1));
                $procedures[] = $subarray;
            }
        }

        // get max vsid
        $query = "select max(vsid) as max from " . Config::dbSurvey() . "_variables";
        $r = $this->db->selectQuery($query);
        $row = $this->db->getRow($r);
        $maxvsid = $row["max"];
        $currentvsid = $maxvsid + 1;

        // process procedures
        $sname = array_values($sectionnames);
        $sname = $sname[0];
        $sname = explode(" ", $sname);
        $sname = $sname[0];
        $testsection = $survey->getSectionByName($sname);
        $fillseid = $testsection->getSeid();

        // process each variable
        $position = "";
        $q = "select max(position) as maxposition from " . Config::dbSurvey() . "_variables where suid=" . $this->suid . " and seid=" . $fillseid;
        $rst = $this->db->selectQuery($q);
        if ($rst) {
            $rtw = $this->db->getRow($rst);
            $position = $rtw["maxposition"] + 1;
        }

        if ($position == "") {
            $position = 1;
        }


        for ($j = 0; $j < sizeof($procedures); $j++) {
            $subarray = $procedures[$j];
            $name = $subarray[0];
            $name = trim(str_ireplace("PROCEDURE ", "", $name));
            $variable = str_ireplace("Txt_", "", $name);
            $code = array();
            $rulefound = false;
            for ($m = 1; $m <= sizeof($subarray); $m++) {
                $line = trim($subarray[$m]);
                if (startsWith($line, "RULES")) {
                    $rulefound = true;
                } else {
                    if ($rulefound) {
                        $words = explode(" ", $line);
                        $lineout = "";
                        foreach ($words as $w) {
                            if (startsWith($w, "pe")) {
                                
                                $lineout[] = substr($w, strlen("pe"), strlen($w));
                            } elseif (startsWith($w, "pi")) {
                                $lineout[] = substr($w, strlen("pi"), strlen($w));
                            } else {
                                $lineout[] = $w;
                            }
                        }
                        $res = implode(" ", $lineout);
                        $res = str_replace("{", "//", $res);
                        $res = str_replace("}", "", $res);
                        //$res = str_replace("= a", "= ", $res);
                        $res = $this->stripNonAscii($res);
                        $code[] = $res;
                        //break;
                    }
                }
            }

            $var = $survey->getVariableDescriptiveByName($variable);
            if ($var->getVsid() != "") {
                
                $var->setFillCode(implode("\n", $code));
                $var->setSeid($fillseid); // add to fills section;
                $var->setPosition($position);
                $var->save();
                $position++;
            } else {
                
                $var = new VariableDescriptive();
                $var->setSuid($this->suid);
                $var->setSeid($fillseid); // add to fills section;
                $var->setVsid($currentvsid);
                $currentvsid++;
                $var->setName($variable);
                $var->setFillCode(implode("\n", $code));
                $var->setPosition($position);
                $var->save();
                $position++;
            }

            echo $variable . "<br/>";
            echo implode("\n", $code);
            echo "<hr>";
            //break;
        }
        return;

        // types
        $query = "select max(tyd) as max from " . Config::dbSurvey() . "_types";
        $r = $this->db->selectQuery($query);
        $row = $this->db->getRow($r);
        $maxtyd = $row["max"];
        $currenttyd = $maxtyd + 1;
        for ($j = 0; $j < sizeof($types); $j++) {
            $fieldarray = $types[$j];

            $name = "";
            $options = "";

            // go through fields section
            $linecnt = 0;
            $firstfound = false;

            // get type details
            for ($k = 1; $k <= sizeof($fieldarray); $k++) {

                $t = trim($fieldarray[$k]);
                
                // end of field definition
                if ($firstfound == true && $t == "") {

                    if ($name != "") {
                        $this->addType($survey, $currenttyd, $name, $options);
                    }

                    // reset 
                    $name = "";
                    $type = "";
                    $options = "";
                    $linecnt = 0;
                    continue; // skip to next
                } elseif ($t == "") {
                    continue;
                }

                $firstfound = true;
                if ($linecnt == 0) {
                    $namedesc = trim($fieldarray[$k]);
                    $name = explode("=", $namedesc);
                    $name = trim($name[0]);
                } else {
                    if ($options != "") {
                        $options .= "\n";
                    }
                    $options .= trim($fieldarray[$k]);
                }

                $linecnt++;

                if ($k == sizeof($fieldarray)) {

                    if ($name != "") {
                        $this->addType($survey, $currenttyd, $name, $options);
                    }

                    // reset 
                    $name = "";
                    $type = "";
                    $options = "";
                    $linecnt = 0;
                }
            }
        }

        // all fields
        $query = "select max(vsid) as max from " . Config::dbSurvey() . "_variables";
        $r = $this->db->selectQuery($query);
        $row = $this->db->getRow($r);
        $maxvsid = $row["max"];
        $currentvsid = $maxvsid + 1;

        for ($j = 0; $j < sizeof($fields); $j++) {

            $fieldarray = $fields[$j];
            $name = "";
            $label = "";
            $text = "";
            $type = "";
            $options = "";
            $array = "no";

            // go through fields section
            $linecnt = 0;
            $foundlabel = false;
            $afterlabel = false;
            $sectionidentifier = str_ireplace("FIELDS", "", $fieldarray[0]);
            $sectionidentifier = str_replace("{", "", trim($sectionidentifier));
            $sectionidentifier = str_replace("}", "", $sectionidentifier);
            $firstfound = false;

            // process each variable
            $position = "";
            if (array_search($sectionidentifier, $descriptions)) {
                $seid = array_search($sectionidentifier, $descriptions);
                $q = "select max(position) as maxposition from " . Config::dbSurvey() . "_variables where suid=" . $this->suid . " and seid=" . $seid;
                $rst = $this->db->selectQuery($q);
                if ($rst) {
                    $rtw = $this->db->getRow($rst);
                    $position = $rtw["maxposition"] + 1;
                }
            }
            if ($position == "") {
                $position = 1;
            }

            for ($k = 1; $k <= sizeof($fieldarray); $k++) {

                $t = trim($fieldarray[$k]);
                
                // end of field definition
                if ($firstfound == true && $t == "") {

                    if (array_search($sectionidentifier, $descriptions)) {
                        $seid = array_search($sectionidentifier, $descriptions);
                    } else {
                        $keys = array_keys($descriptions);
                        $seid = $keys[0];
                    }
                    
                    $this->addVariable($survey, $currentvsid, $name, $text, $label, $type, $options, $seid, $array, $position);
                    $position++;

                    // reset 
                    $name = "";
                    $label = "";
                    $text = "";
                    $type = "";
                    $options = "";
                    $linecnt = 0;
                    $foundlabel = false;
                    $afterlabel = false;
                    $array = "no";
                    $seid = "";
                    continue; // skip to next
                } elseif ($t == "") {
                    continue;
                }

                // comment, then ignore
                //if (startsWith(trim($fieldarray[$k]), "{")) {
                //   continue;
                //}

                $firstfound = true;

                if ($linecnt == 0) {
                    $namedesc = trim($fieldarray[$k]);

                    // single line definition
                    if (contains($namedesc, ":")) {                        
                        $nametemp = split(":", $namedesc);
                        $name = trim($nametemp[0]);
                        $issectionvar = false;
                        if (contains($nametemp[1], "ARRAY ")) {
                            $array = "yes";
                            $arr = explode(" OF ", trim($nametemp[1]));
                            $type = trim($arr[1]);
                            $issectionvar = true;
                        } else {
                            $type = trim($nametemp[1]);
                        }

                        if (contains($name, ' /')) {
                            $tt = split(' /', $name);
                            $name = trim($tt[0]);
                            $label = str_replace(' /', "", $tt[1]);
                            $label = trim(str_replace('"', "", $label));
                        }

                        if (array_search($sectionidentifier, $descriptions)) {
                            $seid = array_search($sectionidentifier, $descriptions);
                        } else {
                            $keys = array_keys($descriptions);
                            $seid = $keys[0];
                        }

                        $this->addVariable($survey, $currentvsid, $name, $text, $label, $type, $options, $seid, $array, $position, $issectionvar);
                        $position++;

                        // reset 
                        $name = "";
                        $label = "";
                        $text = "";
                        $type = "";
                        $options = "";
                        $linecnt = 0;
                        $foundlabel = false;
                        $afterlabel = false;
                        $array = "no";
                        $seid = "";
                        continue;
                    } else {
                        $name = split(" ", $namedesc);
                        $name = trim($name[0]);
                    }
                }
                // start of label
                elseif (startsWith($t, "/")) {
                    $foundlabel = true;
                    $label = str_replace("/", "", trim($fieldarray[$k]));
                    $label = str_replace('"', "", $label);
                    $label = str_replace(':', "", $label);
                }
                // we found the label already, so next one is answer type
                elseif ($foundlabel == true) {
                    $type = trim($fieldarray[$k]); // SET OF/STRING/INTEGER
                    $foundlabel = false;
                    $afterlabel = true;
                }
                // label not found yet, then question text
                elseif ($foundlabel == false && $afterlabel == false) {
                    if ($text != "") {
                        $text .= " ";
                    }
                    $text .= trim($fieldarray[$k]);
                } else {
                    if ($options != "") {
                        $options .= "\n";
                    }
                    $options .= trim($fieldarray[$k]);
                }

                $linecnt++;

                if ($k == sizeof($fieldarray)) {

                    if (array_search($sectionidentifier, $descriptions)) {
                        $seid = array_search($sectionidentifier, $descriptions);
                    } else {
                        $keys = array_keys($descriptions);
                        $seid = $keys[0];
                    }

                    $this->addVariable($survey, $currentvsid, $name, $text, $label, $type, $options, $seid, $array, $position);
                    $position++;

                    // reset 
                    $name = "";
                    $label = "";
                    $text = "";
                    $type = "";
                    $options = "";
                    $linecnt = 0;
                    $foundlabel = false;
                    $afterlabel = false;
                    $array = "no";
                    $seid = "";
                    //continue; // skip to next
                }
            }
            if ($j == 0) {
                //break;
            }
        }

        // add auxfields
        // add locals        
        // return
        return "";
    }

    function isKeyword($a) {
        // FIELDS start
        if (startsWith($a, "FIELDS")) {
            return true;
        }
        // AUXFIELDS start
        elseif (startsWith($a, "AUXFIELDS")) {
            return true;
        }
        // LOCALS start
        elseif (startsWith($a, "LOCALS")) {
            return true;
        }
        // RULES start
        elseif (startsWith($a, "RULES")) {
            return true;
        }
        // BLOCK start
        elseif (startsWith($a, "BLOCK")) {
            return true;
        }
        // ENDBLOCK start
        elseif (startsWith($a, "ENDBLOCK")) {
            return true;
        }
        // ENDSECTION start
        elseif (startsWith($a, "ENDSECTION")) {
            return true;
        }
        // PROCEDURE start
        elseif (startsWith($a, "PROCEDURE")) {
            return true;
        }
        // TYPE start
        elseif (startsWith($a, "TYPE")) {
            return true;
        }
        // PARAMETERS start
        elseif (startsWith($a, "PARAMETERS")) {
            return true;
        }
    }
    
    function stripWordQuotes($str) {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        return str_replace($this->chrmap, "", $str);
    }
    
    function stripNonAscii($str) {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        $str = $this->stripWordQuotes($str);

        // http://stackoverflow.com/questions/1176904/php-how-to-remove-all-non-printable-characters-in-a-string
        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str);
    }

    function findEnd($all, $position) {

        for ($i = ($position + 1); $i < sizeof($all); $i++) {
            $a = $all[$i];

            // FIELDS start
            if (startsWith($a, "FIELDS")) {
                return $i - 1;
            }
            // AUXFIELDS start
            elseif (startsWith($a, "AUXFIELDS")) {
                return $i - 1;
            }
            // LOCALS start
            elseif (startsWith($a, "LOCALS")) {
                return $i - 1;
            }
            // RULES start
            elseif (startsWith($a, "RULES")) {
                return $i - 1;
            }
            // BLOCK start
            elseif (startsWith($a, "BLOCK")) {
                return $i - 1;
            }
            // ENDBLOCK start
            elseif (startsWith($a, "ENDBLOCK")) {
                return $i - 1;
            }
            // ENDSECTION start
            elseif (startsWith($a, "ENDSECTION")) {
                return $i - 1;
            }
            // PROCEDURE start
            elseif (startsWith($a, "PROCEDURE")) {
                return $i - 1;
            }
            // TYPE start
            elseif (startsWith($a, "TYPE")) {
                return $i - 1;
            }
            // PARAMETERS start
            elseif (startsWith($a, "PARAMETERS")) {
                return $i - 1;
            }
            // COMMENT start
            elseif (startsWith($a, "{") && endsWith($a, "}")) {
                return $i - 1;
            }
        }

        return sizeof($all);
    }

    function findEndProcedure($all, $position) {

        for ($i = ($position + 1); $i < sizeof($all); $i++) {
            $a = $all[$i];

            if (startsWith($a, "ENDPROCEDURE")) {
                return $i - 1;
            }
        }

        return sizeof($all);
    }

    /* not used currently */

    function addBaseSection($survey, $name, $title, $description) {

        $newsuid = $this->addtosuid;
        $survey->setSuid($newsuid);
        $survey->setObjectName($newsuid);
        $survey->addVersion(Language::labelVersionCurrentName(), Language::labelVersionCurrentDescription());
        $survey->setDefaultMode(MODE_CASI); // self
        $survey->setDefaultLanguage(1); // english
        $survey->setAccessType(LOGIN_ANONYMOUS);
        $survey->setName($name);
        $survey->setTitle($title);
        $survey->setDescription($description);
        $survey->save();

        /* add base section */
        $section = new Section();
        $section->setSuid($newsuid);
        $section->setSeid(1);
        $section->setName(SECTION_BASE);
        $section->setPosition(1);
        $section->save();

        /* add base questions */
        $var = new VariableDescriptive();
        $var->setVsid(1);
        $var->setName(VARIABLE_PRIMKEY);
        $var->setAnswerType(ANSWER_TYPE_STRING);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('PRIMARY KEY');
        $var->setQuestion('primary key');
        $var->setMaximumLength(ANSWER_PRIMKEY_LENGTH);
        $var->setTyd(-1);
        $var->setPosition(1);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(2);
        $var->setName(VARIABLE_BEGIN);
        $var->setAnswerType(ANSWER_TYPE_DATETIME);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('TIMESTAMP START');
        $var->setQuestion('timestamp start');
        $var->setTyd(-1);
        $var->setPosition(2);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(3);
        $var->setName(VARIABLE_END);
        $var->setAnswerType(ANSWER_TYPE_DATETIME);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('TIMESTAMP END');
        $var->setQuestion('timestamp end');
        $var->setTyd(-1);
        $var->setPosition(3);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(4);
        $var->setName(VARIABLE_VERSION);
        $var->setAnswerType(ANSWER_TYPE_INTEGER);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('VERSION INFO');
        $var->setQuestion('version info');
        $var->setTyd(-1);
        $var->setPosition(4);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(5);
        $var->setName(VARIABLE_MODE);
        $var->setAnswerType(ANSWER_TYPE_ENUMERATED);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('INTERVIEW MODE');
        $var->setOptionsText("1 (CAPI) Face-to-face\r\n2 (CATI) Telephone\r\n3 (CASI) Self-administered\r\n4 (CADI) Data entry");
        $var->setQuestion('interview mode');
        $var->setTyd(-1);
        $var->setPosition(5);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(6);
        $var->setName(VARIABLE_LANGUAGE);
        $var->setAnswerType(ANSWER_TYPE_INTEGER);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('INTERVIEW LANGUAGE');
        $var->setQuestion('interview language');
        $var->setTyd(-1);
        $var->setPosition(6);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(7);
        $var->setName(VARIABLE_TEMPLATE);
        $var->setAnswerType(ANSWER_TYPE_INTEGER);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('SURVEY TEMPLATE');
        $var->setQuestion('survey template');
        $var->setTyd(-1);
        $var->setPosition(7);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(8);
        $var->setName(VARIABLE_PLATFORM);
        $var->setAnswerType(ANSWER_TYPE_STRING);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('PLATFORM AND BROWSER INFORMATION');
        $var->setQuestion(Language::labelPlatform());
        $var->setTyd(-1);
        $var->setPosition(8);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(9);
        $var->setName(VARIABLE_INTRODUCTION);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('INTRODUCTION SCREEN');
        $var->setQuestion(Language::messageWelcome());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_YES);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(9);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(10);
        $var->setName(VARIABLE_THANKS);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('THANKS SCREEN');
        $var->setQuestion(Language::messageSurveyEnd());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(10);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(11);
        $var->setName(VARIABLE_COMPLETED);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('ALREADY COMPLETED SCREEN');
        $var->setQuestion(Language::messageSurveyCompleted());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(11);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(12);
        $var->setName(VARIABLE_LOCKED);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('PROCESSING SCREEN');
        $var->setQuestion(Language::messageSurveyProcessing());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(12);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(13);
        $var->setName(VARIABLE_DIRECT);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('DIRECT ACCESS ONLY SCREEN');
        $var->setQuestion(Language::errorDirectLogin());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(13);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(14);
        $var->setName(VARIABLE_IN_PROGRESS);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('IN PROGRESS SCREEN');
        $var->setQuestion(Language::errorInProgress());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(14);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(15);
        $var->setName(VARIABLE_LOGIN);
        $var->setAnswerType(ANSWER_TYPE_STRING);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('LOGIN SCREEN');
        $var->setQuestion(Language::labelLoginCode());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_YES);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(15);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(16);
        $var->setName(VARIABLE_CLOSED);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('CLOSED SCREEN');
        $var->setQuestion(Language::messageSurveyClosed());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(16);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(17);
        $var->setName(VARIABLE_EXECUTION_MODE);
        $var->setAnswerType(ANSWER_TYPE_ENUMERATED);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('EXECUTION MODE');
        $var->setQuestion(Language::labelExecutionMode());
        $var->setOptionsText("0 (NORMAL) Normal mode\r\n1 (TEST) Test mode");
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(17);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(18);
        $var->setName(VARIABLE_ACCESS);
        $var->setAnswerType(ANSWER_TYPE_NONE);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('NO ACCESS SCREEN');
        $var->setQuestion(Language::LabelSurveyNoAccess());
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowRemarkButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(18);
        $var->save();

        $var = new VariableDescriptive();
        $var->setVsid(19);
        $var->setName(VARIABLE_DEVICE);
        $var->setAnswerType(ANSWER_TYPE_ENUMERATED);
        $var->setSeid(1);
        $var->setSuid($newsuid);
        $var->setDescription('DEVICE');
        $var->setQuestion(Language::labelDevice());
        $var->setOptionsText("1 (PC) Desktop/laptop\r\n2 (TABLET) Tablet\r\n3 (PHONE) Phone");
        $var->setShowBackButton(BUTTON_NO);
        $var->setShowNextButton(BUTTON_NO);
        $var->setShowRFButton(BUTTON_NO);
        $var->setShowDKButton(BUTTON_NO);
        $var->setShowNAButton(BUTTON_NO);
        $var->setShowUpdateButton(BUTTON_NO);
        $var->setShowProgressBar(PROGRESSBAR_NO);
        $var->setHidden(HIDDEN_YES);
        $var->setTyd(-1);
        $var->setPosition(19);
        $var->save();

        /* update current user for access */
        $surv = new Survey($newsuid);
        $user = new User($_SESSION['URID']);
        $mods = explode("~", $surv->getAllowedModes());
        foreach ($mods as $m) {
            $user->setLanguages($newsuid, $m, $surv->getAllowedLanguages($m));
        }
        $user->saveChanges();
    }

    function importBlaiseBla($str) {
        
    }

    function importNubis() {
        $arr = $_FILES[SETTING_IMPORT_TEXT];
        if (sizeof($arr) == 0) {
            return Language::messageImportNoFile();
        }
        $name = $arr["name"];
        if (!endsWith($name, EXPORT_FILE_NUBIS)) {
            return Language::messageImportInvalidFile();
        }
        $str = file_get_contents($arr["tmp_name"]);
        if ($str == "") {
            return Language::messageImportInvalidFile();
        }

        $urid = $_SESSION['URID'];
        $user = new User($urid);
        if (loadvar(SETTING_IMPORT_AS) == IMPORT_TARGET_ADD) {
            $surveys = new Surveys();
            $this->addtosuid = $surveys->getMaximumSuid() + 1;
        }
        // replace
        else if (loadvar(SETTING_IMPORT_AS) == IMPORT_TARGET_REPLACE) {

            $this->addtosuid = 1;

            /* delete existing content */
            $tables = Common::surveyTables();
            foreach ($tables as $table) {
                $query = "delete from " . Config::dbSurvey() . $table;
                $this->db->executeQuery($query);
            }

            /* delete existing data */
            $tables = Common::surveyDataTables();
            foreach ($tables as $table) {
                if ($table == "_actions") {
                    $query = "delete from " . Config::dbSurvey() . $table . " where suid != ''";
                } else {
                    $query = "delete from " . Config::dbSurvey() . $table;
                }
                $this->db->executeQuery($query);
            }

            /* delete test data */
            $tables = Common::surveyTestDataTables();
            foreach ($tables as $table) {
                if ($table == "_actions") {
                    $query = "delete from " . Config::dbSurvey() . $table . " where suid != ''";
                } else {
                    $query = "delete from " . Config::dbSurvey() . $table;
                }
                $this->db->executeQuery($query);
            }
        }

        // add suid and urid
        $str = str_ireplace(EXPORT_PLACEHOLDER_URID, $urid, $str);
        $str = str_ireplace(EXPORT_PLACEHOLDER_SUID, $this->addtosuid, $str);
        $queries = explode("\n", $str);
        $tables = Common::surveyExportTables();
        foreach ($queries as $q) {

            $q = explode(EXPORT_DELIMITER, trim($q));
            if (sizeof($q) != 3) {
                continue;
            }
            if (!inArray($q[0], $tables)) {
                continue;
            }
            $fields = sizeof(explode(",", $q[1]));
            $f = "";
            for ($i = 0; $i < $fields; $i++) {
                if ($f != "") {
                    $f .= ",";
                }
                $f .= "?";
            }
            $query = IMPORT_STATEMENT_INSERT . ' ' . Config::dbSurvey() . $q[0] . " (" . $q[1] . ") " . IMPORT_STATEMENT_INSERT_VALUES . " (" . $f . ")";
            $bp = new BindParam();

            $fields2 = sizeof(explode(",", $q[2]));
            if ($fields != $fields2) {
                continue; // mismatch column count and value count
            }
            $it = explode(",", $q[2]);
            for ($i = 0; $i < $fields2; $i++) {
                $val = & prepareImportString($it[$i]);
                $bp->add(MYSQL_BINDING_STRING, $val);
            }
            $this->db->executeBoundQuery($query, $bp->get());
        }

        // prepare
        set_time_limit(0);

        // compile
        $survey = new Survey($this->addtosuid);
        $compiler = new Compiler($this->addtosuid, getSurveyVersion($survey));

        // sections
        $sections = $survey->getSections();
        foreach ($sections as $section) {
            $mess = $compiler->generateEngine($section->getSeid());
        }

        $mess = $compiler->generateSections();
        $mess = $compiler->generateVariableDescriptives();
        $mess = $compiler->generateTypes();
        $mess = $compiler->generateGetFills();
        $mess = $compiler->generateSetFills();
        $mess = $compiler->generateInlineFields();
        $mess = $compiler->generateGroups();

        $user = new User($_SESSION['URID']);
        $mods = explode("~", $survey->getAllowedModes());
        foreach ($mods as $m) {
            $user->setLanguages($this->addtosuid, $m, $survey->getAllowedLanguages($m));
        }
        $user->saveChanges();

        // return result
        return "";
    }

    function importMMIC() {

        set_time_limit(0);
        $this->importdb = new Database();
        $server = loadvar(SETTING_IMPORT_SERVER);
        if ($server == "") {
            $server = "localhost";
        }
        if ($this->importdb->connect($server, loadvar(SETTING_IMPORT_DATABASE), loadvar(SETTING_IMPORT_USER), loadvar(SETTING_IMPORT_PASSWORD)) == false) {
            $display = new Display();
            return $display->displayError(Language::messageToolsImportDbFailure());
        }
        $this->sourcetable = loadvar(SETTING_IMPORT_TABLE);

        // add
        if (loadvar(SETTING_IMPORT_AS) == IMPORT_TARGET_ADD) {
            $surveys = new Surveys();
            $this->addtosuid = $surveys->getMaximumSuid();
        }
        // replace 
        else if (loadvar(SETTING_IMPORT_AS) == IMPORT_TARGET_REPLACE) {
            $this->addtosuid = 0;

            /* delete existing content */
            $tables = Common::surveyTables();
            foreach ($tables as $table) {
                $query = "delete from " . Config::dbSurvey() . $table;
                $this->db->executeQuery($query);
            }

            /* delete existing data */
            $tables = Common::surveyDataTables();
            foreach ($tables as $table) {
                if ($table == "_actions") {
                    $query = "delete from " . Config::dbSurvey() . $table . " where suid != ''";
                } else {
                    $query = "delete from " . Config::dbSurvey() . $table;
                }
                $this->db->executeQuery($query);
            }

            /* delete test data */
            $tables = Common::surveyTestDataTables();
            foreach ($tables as $table) {
                if ($table == "_actions") {
                    $query = "delete from " . Config::dbSurvey() . $table . " where suid != ''";
                } else {
                    $query = "delete from " . Config::dbSurvey() . $table;
                }
                $this->db->executeQuery($query);
            }
        }

        /* convert */
        $this->convertSurveys();

        // return result
        return "";
    }

    function convertSurveys() {

        $query = "select * from " . $this->sourcetable . "_surveys order by syid";
        if (!$res = $this->importdb->selectQuery($query)) {
            $query = "select * from " . $this->sourcetable . "_survey order by syid";
            $res = $this->importdb->selectQuery($query);
        }

        if ($res) {
            if ($this->importdb->getNumberOfRows($res) > 0) {
                $user = new User($_SESSION['URID']);
                while ($row = $this->importdb->getRow($res)) {
                    $this->suid = $row["syid"] + $this->addtosuid;
                    $this->syid = $row["syid"];
                    $this->convertSurveySettings($row);

                    // get languages
                    $survey = new Survey($this->suid);
                    $this->languages = explode("~", $survey->getAllowedLanguages(MODE_CASI));

                    $this->convertSections();
                    $this->convertVariables();
                    $this->convertTemplates();
                    $this->convertTypes();
                    $this->convertRouting();

                    // if first survey in project, then set as default survey
                    $surveys = new Surveys();
                    $surveys = $surveys->getSurveys();
                    if (sizeof($surveys) == 1) {
                        $survey->setDefaultSurvey(DEFAULT_SURVEY_YES);
                    }

                    // update allowed modes
                    $survey->setAllowedModes(MODE_CASI);

                    // update allowed languages
                    $survey->setAllowedLanguages(implode("~", $this->languages));

                    // update access of user doing the import
                    $mods = explode("~", $survey->getAllowedModes());
                    foreach ($mods as $m) {
                        $user->setLanguages($this->suid, $m, $survey->getAllowedLanguages($m));
                    }
                    $user->saveChanges();
                }
            }
        }
    }

    function convertSurveySettings($row) {

        $query = "replace into " . Config::dbSurvey() . "_surveys (suid, name, description) values (";

        $query .= prepareDatabaseString($this->suid) . ",";

        $query .= "'" . prepareDatabaseString($row["header"]) . "',";

        $query .= "'')";

        $this->db->executeQuery($query);



        $query = "replace into " . Config::dbSurvey() . "_versions (suid, vnid, name, description) values (";

        $query .= prepareDatabaseString($this->suid) . ",";

        $query .= prepareDatabaseString(1) . ",";

        $query .= "'Current',";

        $query .= "'Current version')";

        $this->db->executeQuery($query);



        /* add default survey */

        $setting = new Setting();

        $setting->setSuid($this->suid);

        $setting->setObject(USCIC_SURVEY);

        $setting->setObjectType(OBJECT_SURVEY);

        $setting->setName(SETTING_DEFAULT_SURVEY);

        $setting->setValue($this->suid);

        $setting->setMode(MODE_CASI); // dummy

        $setting->setLanguage(1); // dummy

        $setting->save();



        /* add default mode */

        $setting = new Setting();

        $setting->setSuid($this->suid);

        $setting->setObject(USCIC_SURVEY);

        $setting->setObjectType(OBJECT_SURVEY);

        $setting->setName(SETTING_DEFAULT_MODE);

        $setting->setMode(MODE_CASI);

        $setting->setLanguage(1); // dummy

        $setting->setValue(MODE_CASI);

        $setting->save();



        /* add default language */

        $setting = new Setting();

        $setting->setSuid($this->suid);

        $setting->setObject(USCIC_SURVEY);

        $setting->setObjectType(OBJECT_SURVEY);

        $setting->setName(SETTING_DEFAULT_LANGUAGE);

        $setting->setMode(MODE_CASI);

        $setting->setLanguage(1); // dummy

        $setting->setValue(1); // english

        $setting->save();
    }

    function convertSections() {

        $query = "select meid as seid, name as name, parentmeid as pid, description as description, visible as hidden, qorder from " . $this->sourcetable . "_module where syid=" . $this->syid . " order by meid";

        if ($res = $this->importdb->selectQuery($query)) {

            if ($this->importdb->getNumberOfRows($res) > 0) {

                while ($row = $this->importdb->getRow($res)) {

                    $query = "replace into " . $this->targettable . "_sections (suid, seid, name, position, pid) values (";

                    $query .= prepareDatabaseString($this->suid) . ",";

                    $query .= prepareDatabaseString($row["seid"]) . ",";

                    $query .= "'" . prepareDatabaseString($row["name"]) . "',";

                    $query .= prepareDatabaseString($row["qorder"]) . ",";

                    $query .= prepareDatabaseString($row["pid"]) . ")";

                    $this->db->executeQuery($query);



                    /* add rest as settings */

                    $this->addSetting($row["seid"], OBJECT_SECTION, SETTING_DESCRIPTION, $row["description"]);

                    $this->addSetting($row["seid"], OBJECT_SECTION, SETTING_HIDDEN, $row["hidden"]);
                }
            }
        }
    }

    function convertTemplates() {

        $query = "select * from " . $this->sourcetable . "_template where syid=" . $this->syid . " order by teid";

        if ($res = $this->importdb->selectQuery($query)) {

            if ($this->importdb->getNumberOfRows($res) > 0) {

                while ($row = $this->importdb->getRow($res)) {

                    $query = "replace into " . $this->targettable . "_groups (suid, gid, name) values (";
                    $query .= prepareDatabaseString($this->suid) . ",";
                    $query .= prepareDatabaseString($row["teid"]) . ",";
                    $query .= "'" . prepareDatabaseString($row["name"]) . "')";
                    $this->db->executeQuery($query);

                    $this->addSetting($row["teid"], OBJECT_GROUP, SETTING_GROUP_TEMPLATE, TABLE_TEMPLATE_CUSTOM);
                    $content = $row["description"];
                    for ($i = 1; $i < 101; $i++) {
                        $content = str_ireplace("\$Questiontext" . $i . "\$", INDICATOR_CUSTOMTEMPLATE . INDICATOR_CUSTOMTEMPLATEQUESTION . $i . INDICATOR_CUSTOMTEMPLATE, $content);
                        $content = str_ireplace("\$Answer" . $i . "\$", INDICATOR_CUSTOMTEMPLATE . INDICATOR_CUSTOMTEMPLATEANSWER . $i . INDICATOR_CUSTOMTEMPLATE, $content);
                    }
                    $this->addSetting($row["teid"], OBJECT_GROUP, SETTING_GROUP_CUSTOM_TEMPLATE, $content);

                    /* convert translations */
                    $q = "select * from " . $this->sourcetable . "_translation where syid=" . $this->syid . " and id=" . $row["teid"] . " order by source";
                    if ($r = $this->importdb->selectQuery($q)) {
                        if ($this->importdb->getNumberOfRows($r) > 0) {
                            while ($rowtrans = $this->importdb->getRow($r)) {
                                $language = $rowtrans["language"];
                                $source = $rowtrans["source"];
                                switch ($source) {
                                    case "tmpeid":
                                        $content = $rowtrans["translation"];
                                        for ($i = 1; $i < 101; $i++) {
                                            $content = str_ireplace("\$Questiontext" . $i . "\$", INDICATOR_CUSTOMTEMPLATE . INDICATOR_CUSTOMTEMPLATEQUESTION . $i . INDICATOR_CUSTOMTEMPLATE, $content);
                                            $content = str_ireplace("\$Answer" . $i . "\$", INDICATOR_CUSTOMTEMPLATE . INDICATOR_CUSTOMTEMPLATEANSWER . $i . INDICATOR_CUSTOMTEMPLATE, $content);
                                        }
                                        $this->addSetting($row["teid"], OBJECT_GROUP, SETTING_GROUP_CUSTOM_TEMPLATE, $content, $language);
                                        if (!inArray($language, $this->languages)) {
                                            $this->languages[] = $language;
                                        }
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    function convertVariables() {

        $query = "select qnid as id, fullvariablename as variablename, questiontext as question, questiontype as answertype, description, answer as options, keep, arraysize, meid as seid, emptyallowed as requireanswer, visible as hidden, mmicparsetext as settings, fills, qorder from " . $this->sourcetable . "_question where syid=" . $this->syid . " order by qnid";

        if ($res = $this->importdb->selectQuery($query)) {

            if ($this->importdb->getNumberOfRows($res) > 0) {

                while ($row = $this->importdb->getRow($res)) {

                    $query = "replace into " . $this->targettable . "_variables (suid, vsid, seid, variablename, position) values (";
                    $query .= prepareDatabaseString($this->suid) . ",";
                    $query .= prepareDatabaseString($row["id"]) . ",";
                    $query .= prepareDatabaseString($row["seid"]) . ",";
                    $query .= "'" . prepareDatabaseString($row["variablename"]) . "',";
                    $query .= prepareDatabaseString($row["qorder"]) . ")";
                    $this->db->executeQuery($query);


                    /* add rest as settings */
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_DESCRIPTION, $row["description"]);
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_QUESTION, $row["question"]);
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_ANSWERTYPE, $this->convertAnswerType($row["answertype"], $row["settings"]));
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_OPTIONS, $row["options"]);
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_REQUIREANSWER, $row["requireanswer"]);
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_KEEP, $row["keep"]);
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_ARRAY, $this->isArray($row["arraysize"]));
                    $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_HIDDEN, $row["hidden"]);


                    if (trim($row["fills"]) != "") {
                        $t = $row["fills"];
                        for ($i = 0; $i < 20; $i++) {
                            $t = str_ireplace("\$Fill" . $i . "\$", VARIABLE_VALUE_FILL . $i, $t);
                        }
                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_FILLCODE, $t);

                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_FILLTEXT, $row["question"]);
                    }

                    /* add settings */
                    $this->convertSettings($row);

                    /* convert translations */
                    $q = "select * from " . $this->sourcetable . "_translation where syid=" . $this->syid . " and id=" . $row["id"] . " order by source";
                    if ($r = $this->importdb->selectQuery($q)) {
                        if ($this->importdb->getNumberOfRows($r) > 0) {
                            while ($rowtrans = $this->importdb->getRow($r)) {
                                $language = $rowtrans["language"];
                                $source = $rowtrans["source"];
                                switch ($source) {
                                    case "dnid":
                                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_DESCRIPTION, $rowtrans["translation"], $language);
                                        break;
                                    case "qnid":
                                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_QUESTION, $rowtrans["translation"], $language);
                                        break;
                                    case "csid":
                                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_OPTIONS, $rowtrans["translation"], $language);
                                        break;
                                    case "hhid":
                                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_PRETEXT, $rowtrans["translation"], $language);
                                        break;
                                    case "rfbuid":
                                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_RF_BUTTON_LABEL, $rowtrans["translation"], $language);
                                        break;
                                    case "dkbuid":
                                        $this->addSetting($row["id"], OBJECT_VARIABLEDESCRIPTIVE, SETTING_DK_BUTTON_LABEL, $rowtrans["translation"], $language);
                                        break;
                                }

                                if (!inArray($language, $this->languages)) {
                                    $this->languages[] = $language;
                                }
                            }
                        }
                    }
                }

                // update names
                $updates = array(
                    "update " . $this->targettable . "_variables set variablename='" . VARIABLE_BEGIN . "' where suid = " . $this->suid . " and variablename='tsstart'",
                    "update " . $this->targettable . "_variables set variablename='" . VARIABLE_END . "' where suid = " . $this->suid . " and variablename='tsend'",
                    "update " . $this->targettable . "_variables set variablename='" . VARIABLE_THANKS . "' where suid = " . $this->suid . " and variablename='thanks1'",
                    "update " . $this->targettable . "_variables set variablename='" . VARIABLE_COMPLETED . "' where suid = " . $this->suid . " and variablename='completed1'"
                );

                foreach ($updates as $update) {
                    $this->db->executeQuery($update);
                }


                // delete not needed

                $deletes = array("intro1", "welcome1", "return1", "direct1", "finished1", "closed1", "timeout1", "browserinfo", "illegal1", "eligible1");

                foreach ($deletes as $delete) {

                    $query = "delete from " . $this->targettable . "_variables where suid = " . $this->suid . " and variablename='" . prepareDatabaseString($delete) . "'";

                    $this->db->executeQuery($query);
                }
            }
        }
    }

    function addSetting($object, $objecttype, $settingname, $value, $language = 1, $echo = false) {

        $query = "replace into " . $this->targettable . "_settings (suid, object, objecttype, name, value, language, mode) values (";

        $query .= prepareDatabaseString($this->suid) . ",";

        $query .= prepareDatabaseString($object) . ",";

        $query .= prepareDatabaseString($objecttype) . ",";

        $query .= "'" . prepareDatabaseString($settingname) . "',";

        $query .= "'" . prepareDatabaseString($value, false) . "',"; // allow for html/scripts

        $query .= "'" . prepareDatabaseString($language) . "',";

        $query .= "" . MODE_CASI . ")"; // interview mode: always assume web
        if ($echo) {
            echo $query;
        }
        $this->db->executeQuery($query);
    }

    function convertSettings($row, $objecttype = OBJECT_VARIABLEDESCRIPTIVE) {

        $in = $row["answertype"];

        /* process layout settings */
        $settings = explode("\r\n", $row["settings"]);

        foreach ($settings as $setting) {

            if (startsWith($setting, "MMICJavascript")) {
                $v = trim(str_ireplace("MMICJavascript(", "", $setting));
                $v = substr($v, 0, strlen($v) - 1);
                $this->addSetting($row["id"], $objecttype, SETTING_JAVASCRIPT_WITHIN_ELEMENT, $v, 1);
            } else if (startsWith($setting, "MMICExtraJavascript")) {
                $v = trim(str_ireplace("MMICExtraJavascript(", "", $setting));
                $v = substr($v, 0, strlen($v) - 1);

                /* strip script tags */
                $pos = strpos($v, ">");
                $v = substr($v, $pos + 1);
                $pos = strrpos($v, "<");
                $v = substr($v, 0, $pos);

                /* add */
                $this->addSetting($row["id"], $objecttype, SETTING_JAVASCRIPT_WITHIN_PAGE, $v, 1);
            } else if (startsWith($setting, "MMICNoBack")) {
                $this->addSetting($row["id"], $objecttype, SETTING_BACK_BUTTON, BUTTON_NO, 1);
            } else if (startsWith($setting, "MMICNoNext")) {
                $this->addSetting($row["id"], $objecttype, SETTING_NEXT_BUTTON, BUTTON_NO, 1);
            } else if (startsWith($setting, "MMICShowDKButton")) {
                $v = str_replace(")", "", str_replace("MMICShowDKButton(", "", $setting));
                if (strtoupper($v) == "ON") {
                    $this->addSetting($row["id"], $objecttype, SETTING_DK_BUTTON, BUTTON_YES, 1);
                }
            } else if (startsWith($setting, "MMICShowRFButton")) {
                $v = str_replace(")", "", str_replace("MMICShowRFButton(", "", $setting));
                if (strtoupper($v) == "ON") {
                    $this->addSetting($row["id"], $objecttype, SETTING_RF_BUTTON, BUTTON_YES, 1);
                }
            } else if (startsWith($setting, "MMICShowUpdateButton")) {

                $v = str_replace(")", "", str_replace("MMICShowUpdateButton(", "", $setting));

                if (strtoupper($v) == "ON") {

                    $this->addSetting($row["id"], $objecttype, SETTING_UPDATE_BUTTON, BUTTON_YES, 1);
                }
            } else if (startsWith($setting, "MMICHint")) {

                $v = str_replace(")", "", str_replace("MMICHint(", "", $setting));

                $va = explode('$Inputtype$', $v);



                /* first one: can be before or after */

                if (isset($va[0])) {



                    /* before */

                    if (strpos($v, trim($va[0])) < strpos($v, '$Inputtype$')) {

                        $this->addSetting($row["id"], $objecttype, SETTING_PRETEXT, trim($va[0]), 1);
                    }

                    /* after */ else {

                        $this->addSetting($row["id"], $objecttype, SETTING_POSTTEXT, trim($va[0]), 1);
                    }
                }

                /* second one, so must be after */

                if (isset($va[1])) {

                    $this->addSetting($row["id"], $objecttype, SETTING_POSTTEXT, trim($va[1]), 1);
                }
            }
        }



        switch ($in) {

            case 1://string

                /* add setting for max length */

                if ($row["options"] != "") {

                    $this->addSetting($row["id"], $objecttype, SETTING_MAXIMUM_LENGTH, $row["options"], 1);
                }

                break;

            case 2://integer

                break;

            case 3://range



                /* add settings for min and max */

                $r = explode("..", $row["options"]);

                $this->addSetting($row["id"], $objecttype, SETTING_MINIMUM_RANGE, $r[0], 1);

                $this->addSetting($row["id"], $objecttype, SETTING_MAXIMUM_RANGE, $r[1], 1);

            case 4://enumerated

                break;

            case 5://set of enumerated



                /* check and add minimum/maximum selected settings */

                $settings = explode("\r\n", $row["settings"]);

                foreach ($setting as $setting) {

                    if (startsWith($setting, "MMICMinimumSetSize")) {

                        $min = str_replace(")", "", str_replace("MMICMinimumSetSize(", "", $setting));

                        $this->addSetting($row["id"], $objecttype, SETTING_MINIMUM_SELECTED, $min, 1);
                    } else if (startsWith($setting, "MMICMaximumSetSize")) {

                        $max = str_replace(")", "", str_replace("MMICMaximumSetSize(", "", $setting));

                        $this->addSetting($row["id"], $objecttype, SETTING_MAXIMUM_SELECTED, $max, 1);
                    } else if (startsWith($setting, "MMICInvalidSubSets")) {

                        $subs = str_replace(")", "", str_replace("MMICInvalidSubSets(", "", $setting));

                        $subs = str_replace("[", "", $subs);

                        $subs = str_replace("]", "", $subs);

                        $subs = str_replace(",", ";", $subs);

                        $subs = str_replace("~", ",", $subs);

                        $this->addSetting($row["id"], SETTING_INVALIDSUB_SELECTED, $subs, 1);
                    } else if (startsWith($setting, "MMICInvalidSets")) {

                        $subs = str_replace(")", "", str_replace("MMICInvalidSets(", "", $setting));

                        $subs = str_replace("[", "", $subs);

                        $subs = str_replace("]", "", $subs);

                        $subs = str_replace(",", ";", $subs);

                        $subs = str_replace("~", ",", $subs);

                        $this->addSetting($row["id"], $objecttype, SETTING_INVALID_SELECTED, $subs, 1);
                    }
                }



            case 6://open

                /* add setting for max length */

                if ($row["options"] != "") {

                    $this->addSetting($row["id"], $objecttype, SETTING_MAXIMUM_LENGTH, $row["options"], 1);
                }

                break;

            case 7://real

                break;

            case 8://no input

                break;

            case 9://module

                break;

            case 10://datetype

                break;

            case 11://timetype

                break;
        }



        /* button settings */

        $settings = explode("\r\n", $row["settings"]);

        foreach ($setting as $setting) {

            if (startsWith($setting, "MMICShowDKButton")) {

                $on = str_replace(")", "", str_replace("MMICShowDKButton(", "", $setting));

                if ($on == "on") {

                    $this->addSetting($row["id"], $objecttype, SETTING_DK_BUTTON, BUTTON_YES, 1);
                } else if ($on == "off") {

                    $this->addSetting($row["id"], $objecttype, SETTING_DK_BUTTON, BUTTON_NO, 1);
                }
            } else if (startsWith($setting, "MMICShowRFButton")) {

                $on = str_replace(")", "", str_replace("MMICShowRFButton(", "", $setting));

                if ($on == "on") {

                    $this->addSetting($row["id"], $objecttype, SETTING_RF_BUTTON, BUTTON_YES, 1);
                } else if ($on == "off") {

                    $this->addSetting($row["id"], $objecttype, SETTING_RF_BUTTON, BUTTON_NO, 1);
                }
            } else if (startsWith($setting, "MMICShowUpdateButton")) {

                $on = str_replace(")", "", str_replace("MMICShowUpdateButton(", "", $setting));

                if ($on == "on") {

                    $this->addSetting($row["id"], $objecttype, SETTING_UPDATE_BUTTON, BUTTON_YES, 1);
                } else if ($on == "off") {

                    $this->addSetting($row["id"], $objecttype, SETTING_UPDATE_BUTTON, BUTTON_NO, 1);
                }
            } else if (startsWith($setting, "MMICNoBack")) {

                $this->addSetting($row["id"], $objecttype, SETTING_BACK_BUTTON, BUTTON_NO, 1);
            } else if (startsWith($setting, "MMICNoNext")) {

                $this->addSetting($row["id"], $objecttype, SETTING_NEXT_BUTTON, BUTTON_NO, 1);
            }
        }
    }

    function convertAnswerType($in, $settings) {

        switch ($in) {

            case 1://string                   

                return ANSWER_TYPE_STRING;

            case 2://integer

                return ANSWER_TYPE_INTEGER;

            case 3://range                

                return ANSWER_TYPE_RANGE;

            case 4://enumerated

                if (contains($settings, "MMICComboBox")) {

                    return ANSWER_TYPE_DROPDOWN;
                }

                return ANSWER_TYPE_ENUMERATED;

            case 5://set of enumerated                

                if (contains($settings, "MMICList")) {

                    return ANSWER_TYPE_MULTIDROPDOWN;
                }

                return ANSWER_TYPE_SETOFENUMERATED;

            case 6://open                

                return ANSWER_TYPE_OPEN;

            case 7://real

                return ANSWER_TYPE_DOUBLE;

            case 8://no input

                return ANSWER_TYPE_NONE;

            case 9://module

                return ANSWER_TYPE_SECTION;

            case 10://datetype

                return ANSWER_TYPE_DATE;

            case 11://timetype

                return ANSWER_TYPE_TIME;
        }
    }

    function isArray($size) {

        if ($size > 0) {

            return 1;
        }

        return 0;
    }

    function convertRouting() {

        $query = "select meid as seid, rules from " . $this->sourcetable . "_module where syid=" . $this->syid . " order by meid";

        if ($res = $this->importdb->selectQuery($query)) {

            if ($this->importdb->getNumberOfRows($res) > 0) {

                global $db;

                while ($row = $this->importdb->getRow($res)) {

                    $rules = explode("\r\n", $row["rules"]);

                    $cnt = 1;

                    foreach ($rules as $rule) {

                        $query = "replace into " . $this->targettable . "_routing (suid, seid, rgid, rule) values (";

                        $query .= prepareDatabaseString($this->suid) . ",";

                        $query .= prepareDatabaseString($row["seid"]) . ",";

                        $query .= prepareDatabaseString($cnt) . ",";

                        $query .= "'" . prepareDatabaseString($rule) . "')";

                        $this->db->executeQuery($query);

                        $cnt++;
                    }
                }



                $query = "select * from " . $this->targettable . "_routing where suid=" . $this->suid . " and trim(rule) like 'begincombine%' order by rgid asc";

                if ($res = $this->db->selectQuery($query)) {
                    $survey = new Survey($this->suid);
                    if ($this->db->getNumberOfRows($res) > 0) {

                        while ($row = $this->db->getRow($res)) {
                            $rule = trim($row["rule"]);
                            if (contains($rule, "(")) {
                                $line = trim(str_replace(")", "", substr($rule, strpos($rule, "(") + 1)));
                            }
                            if ($line == "") {
                                $line = "shortcombinegroup";
                            }

                            $query = "update " . $this->targettable . "_routing set rule='group." . $line . "' where suid=" . $this->suid . " and seid=" . $row["seid"] . " and rgid=" . $row["rgid"];

                            $this->db->executeQuery($query);

                            /* add group */
                            if ($line != "") {
                                $exgr = $survey->getGroupByName($line);
                                if ($exgr->getGid() == "") {
                                    $group = new Group();
                                    $group->setSuid($this->suid);
                                    $group->setName($line);
                                    $group->save();
                                }
                            }
                        }
                    }
                }



                $query = "select * from " . $this->targettable . "_routing where suid=" . $this->suid . " and rule like 'jumpback(%' order by rgid asc";
                if ($res = $this->db->selectQuery($query)) {
                    if ($this->db->getNumberOfRows($res) > 0) {

                        while ($row = $db->getRow($res)) {
                            $line = str_replace(")", "", substr($row["rule"], strpos($row["rule"], "(") + 1));
                            $query = "update " . $this->targettable . "_routing set rule='moveBackward." . $line . "' where suid=" . $this->suid . " and seid=" . $row["seid"] . " and rgid=" . $row["rgid"];
                            $this->db->executeQuery($query);
                        }
                    }
                }


                $query = "select * from " . $this->targettable . "_routing where suid=" . $this->suid . " and rule like 'jump(%' order by rgid asc";
                if ($res = $this->db->selectQuery($query)) {
                    if ($this->db->getNumberOfRows($res) > 0) {

                        while ($row = $this->importdb->getRow($res)) {
                            $line = str_replace(")", "", substr($row["rule"], strpos($row["rule"], "(") + 1));
                            $query = "update " . $this->targettable . "_routing set rule='moveForward." . $line . "' where suid=" . $this->suid . " and seid=" . $row["seid"] . " and rgid=" . $row["rgid"];

                            $this->db->executeQuery($query);
                        }
                    }
                }



                $query = "update " . $this->targettable . "_routing set rule='endgroup' where suid=" . $this->suid . " and trim(rule)='endcombine'";

                $this->db->executeQuery($query);
            }
        }
    }

    function convertTypes() {

        $query = "select teid as id, name as name, questiontype as answertype, answer as options from " . $this->sourcetable . "_type where syid=" . $this->syid . " order by teid";

        if ($res = $this->importdb->selectQuery($query)) {

            if ($this->importdb->getNumberOfRows($res) > 0) {

                while ($row = $this->importdb->getRow($res)) {

                    $query = "replace into " . $this->targettable . "_types (suid, tyd, name) values (";
                    $query .= prepareDatabaseString($this->suid) . ",";
                    $query .= prepareDatabaseString($row["id"]) . ",";
                    $query .= "'" . prepareDatabaseString($row["name"]) . "')";
                    $this->db->executeQuery($query);

                    /* add rest as settings */
                    $this->addSetting($row["id"], OBJECT_TYPE, SETTING_ANSWERTYPE, $this->convertAnswerType($row["answertype"], $row["settings"]));
                    $this->addSetting($row["id"], OBJECT_TYPE, SETTING_OPTIONS, $row["options"]);

                    /* add usage in variables */
                    $query = "select * from " . $this->targettable . "_settings where suid=" . $this->suid . " and name='" . SETTING_OPTIONS . "' and objecttype=" . OBJECT_VARIABLEDESCRIPTIVE . " and value='" . $row["name"] . "'";

                    $res1 = $this->db->selectQuery($query);

                    if ($res1) {

                        if ($this->db->getNumberOfRows($res1) > 0) {
                            while ($row1 = $this->db->getRow($res1)) {
                                $q = "update " . $this->targettable . "_variables set tyd=" . $row["id"] . " where suid=" . $this->suid . " and vsid=" . $row1["object"];
                                $this->db->executeQuery($q);

                                // remove options in settings for variable, so it does not override the type's options
                                $q = "delete from " . $this->targettable . "_settings where suid=" . $this->suid . " and object=" . $row1["object"] . " and name='" . SETTING_OPTIONS . "' and objecttype=" . OBJECT_VARIABLEDESCRIPTIVE;
                                $this->db->executeQuery($q);
                            }
                        }
                    }

                    /* add settings */
                    $this->convertSettings($row, OBJECT_TYPE);

                    /* convert translations */
                    $q = "select source, language, cast(translation as char) as translation from " . $this->sourcetable . "_translation where syid=" . $this->syid . " and id=" . $row["id"] . " order by source";
                    if ($r = $this->importdb->selectQuery($q)) {
                        if ($this->importdb->getNumberOfRows($r) > 0) {
                            while ($rowtrans = $this->importdb->getRow($r)) {
                                $language = $rowtrans["language"];
                                $source = $rowtrans["source"];
                                switch ($source) {
                                    case "teid":
                                        $this->addSetting($row["id"], OBJECT_TYPE, SETTING_OPTIONS, $rowtrans["translation"], $language);
                                        break;
                                }

                                if (!inArray($language, $this->languages)) {
                                    $this->languages[] = $language;
                                }
                            }
                        }
                    }
                }

                // update answer types
                $updates = array(
                    "update " . $this->targettable . "_settings set value=" . $this->convertAnswerType(3) . " where suid=" . $this->suid . " and objecttype=" . OBJECT_TYPE . " and name='" . SETTING_ANSWERTYPE . "' and value=3",
                    "update " . $this->targettable . "_settings set value=" . $this->convertAnswerType(4) . " where suid=" . $this->suid . " and objecttype=" . OBJECT_TYPE . " and name='" . SETTING_ANSWERTYPE . "' and value=4",
                    "update " . $this->targettable . "_settings set value=" . $this->convertAnswerType(5) . " where suid=" . $this->suid . " and objecttype=" . OBJECT_TYPE . " and name='" . SETTING_ANSWERTYPE . "' and value=5",
                    "update " . $this->targettable . "_settings set value=" . $this->convertAnswerType(6) . " where suid=" . $this->suid . " and objecttype=" . OBJECT_TYPE . " and name='" . SETTING_ANSWERTYPE . "' and value=6"
                );

                foreach ($updates as $update) {
                    $this->db->executeQuery($update);
                }
            }
        }
    }

}
?>