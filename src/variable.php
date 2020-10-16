<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Variable {

    private $variablename;
    private $completevariablename;
    private $variabledescriptive;
    private $enumerated;
    private $suid;
    private $language;
    private $mode;
    private $version;
    private $ts;
    private $dirty;
    private $answertype;
    private $array;
    var $variable;

    function __construct($variablenameOrRow = "") {
        if (is_array($variablenameOrRow)) {
            $this->variable = $variablenameOrRow;
        } elseif ($variablenameOrRow != "") {
            $this->setVariable($variablenameOrRow);
        }
    }

    function setVariable($variablename) {
        global $db, $engine, $language;
        $this->completevariablename = $variablename;
        $this->variablename = getBasicName($this->completevariablename);
        $variabledescriptive = $engine->getVariableDescriptive($this->variablename);
        $this->array = $variabledescriptive->isArray();
        $this->answertype = $variabledescriptive->getAnswerType();
        $this->language = $language;
        if ($variabledescriptive == null) {
            return false;
        }
        return true;
    }

    function getAnswer($primkey) {

        if (is_array($this->variable) && array_key_exists("answer", $this->variable)) {
            return $this->variable["answer"];
        } else {

            // not an array
            if ($this->array == false) {

                // set of enumerated variable
                if ($this->answertype == ANSWER_TYPE_SETOFENUMERATED || $this->answertype == ANSWER_TYPE_MULTIDROPDOWN) {

                    // specific set of enumerated option
                    $matches = array();
                    preg_match("/(_[0-9]+_\b){1}/", $this->completevariablename, $matches);
                    if (sizeof($matches) > 0) {

                        // get indicated option
                        $bracketvalue = str_replace("_", "", $matches[0]);

                        // get values of set of enum question as a whole
                        global $engine;
                        $values = explode(SEPARATOR_SETOFENUMERATED, $engine->getAnswer(preg_replace("/(_[0-9]+_\b)/", "", $this->completevariablename)));

                        // selected
                        if (in_array($bracketvalue, $values)) {
                            $this->variable["answer"] = $bracketvalue;
                        } else {
                            $this->variable["answer"] = null;
                        }
                    }
                    // not a specific set of enumerated option
                    else {
                        $this->variable["answer"] = $this->retrieveAnswer($primkey, $this->completevariablename);
                    }
                }
                // not set of enumerated variable
                else {
                    $this->variable["answer"] = $this->retrieveAnswer($primkey, $this->completevariablename);
                }
            }

            // an array
            else {

                // generic variable
                if (endsWith($this->completevariablename, "]") == false) {
                    $ans = $this->retrieveAnswer($primkey, $this->completevariablename);
                    if ($ans == "") {
                        $this->variable["answer"] = null;
                    } else {
                        $t = unserialize(gzuncompress($this->retrieveAnswer($primkey, $this->completevariablename)));
                        if (is_array($t) && sizeof($t) == 0) {
                            $t = null;
                        }
                        $this->variable["answer"] = $t;
                    }
                }

                // specific array instance, e.g. Q1[1,1,1]
                else {

                    // set of enumerated variable: Q1_1_[1,1,1]
                    if ($this->answertype == ANSWER_TYPE_SETOFENUMERATED || $this->answertype == ANSWER_TYPE_MULTIDROPDOWN) {

                        // specific set of enumerated option
                        $matches = array();
                        preg_match("/(_[0-9]+_\b){1}/", $this->completevariablename, $matches);
                        if (sizeof($matches) > 0) {

                            // get indicated option
                            $bracketvalue = str_replace("_", "", $matches[0]);

                            // get values of set of enum question as a whole
                            global $engine;
                            $values = explode(SEPARATOR_SETOFENUMERATED, $engine->getAnswer(preg_replace("/(_[0-9]+_\b)/", "", $this->completevariablename)));

                            // selected
                            if (in_array($bracketvalue, $values)) {
                                $this->variable["answer"] = $bracketvalue;
                            } else {
                                $this->variable["answer"] = null;
                            }
                        }

                        // not a specific set of enumerated option
                        else {

                            $this->variable["answer"] = $this->retrieveAnswer($primkey, $this->completevariablename);
                        }
                    }
                    // not a set of enumerated question
                    else {

                        $ans = $this->retrieveAnswer($primkey, $this->completevariablename);

                        // try to see if it is an array itself
                        $data = @gzuncompress($ans);
                        if ($data !== false) {

                            $t = unserialize($data);
                            if (is_array($t) && sizeof($t) == 0) {
                                $t = null;
                            }
                            $this->variable["answer"] = $t;
                        } else {

                            $this->variable["answer"] = $ans;
                        }
                    }
                }
            }
        }

        // return result
        return $this->variable["answer"];
    }

    function retrieveDirty($primkey, $dataname) {
        global $db;
        $query = "select dirty from " . Config::dbSurveyData() . "_data where suid=" . prepareDatabaseString(getSurvey()) . " and primkey='" . prepareDatabaseString($primkey) . "' and variablename='" . prepareDatabaseString($dataname) . "'";

        if ($res = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($res) > 0) {
                $row = $db->getRow($res);
                return $row["dirty"];
            }
        }
        return '';
    }

    private function retrieveAnswer($primkey, $dataname) {
        global $db, $survey;
        $key = $survey->getDataEncryptionKey();
        $answer = "answer";
        if ($key != "") {
            $answer = "aes_decrypt(answer, '" . $key . "') as answer";
        }
        $query = "select $answer, dirty from " . Config::dbSurveyData() . "_data where suid=" . prepareDatabaseString(getSurvey()) . " and primkey='" . prepareDatabaseString($primkey) . "' and variablename='" . prepareDatabaseString($dataname) . "'";

        if ($res = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($res) > 0) {
                $row = $db->getRow($res);
                $this->setDirty($row["dirty"]);
                return $row["answer"];
            }
        }
        return null;
    }

    function setAnswer($primkey, $answer) {

        global $engine;

        // not an array
        if ($this->array == false) {

            // set of enumerated variable
            if ($this->answertype == ANSWER_TYPE_SETOFENUMERATED || $this->answertype == ANSWER_TYPE_MULTIDROPDOWN) {

                // specific set of enumerated option
                $matches = array();
                preg_match("/(_[0-9]+_\b){1}/", $this->completevariablename, $matches);
                if (sizeof($matches) > 0) {

                    // get indicated option
                    $bracketvalue = str_replace("_", "", $matches[0]);

                    // get current values
                    global $engine;
                    $real = preg_replace("/(_[0-9]+_\b)/", "", $this->completevariablename);
                    $values = explode(SEPARATOR_SETOFENUMERATED, $engine->getAnswer($real));

                    // set to empty
                    if ($answer == null) {
                        if (inArray($bracketvalue, $values)) {
                            $values[array_search($bracketvalue, $values)] = null; //
                            unset($values[array_search($bracketvalue, $values)]); //
                        }
                        $this->variable["answer"] = null;   // we don't call storeAnswer for _1_, so set the in-memory value here                     
                    }

                    // set to response
                    else {

                        if (strtoupper($answer) == ANSWER_RESPONSE) {
                            if (inArray($bracketvalue, $values)) {
                                $values[array_search($bracketvalue, $values)] = $bracketvalue;
                            } else {
                                $values[] = $bracketvalue;
                            }
                            $this->variable["answer"] = $bracketvalue; // we don't call storeAnswer for _1_, so set the in-memory value here
                        }
                    }

                    $final = array();
                    foreach ($values as $k => $v) {
                        if ($v != "") {
                            $final[$k] = $v;
                        }
                    }

                    sort($final); // sort ascending

                    return $engine->setAnswer($real, implode(SEPARATOR_SETOFENUMERATED, $final), $this->getDirty());
                }

                // no specific set of enumerated option
                else {
                    return $this->storeAnswer($primkey, $engine->prefixVariableName($this->completevariablename), $answer);
                }
            }

            // not set of enumerated variable
            else {
                return $this->storeAnswer($primkey, $engine->prefixVariableName($this->completevariablename), $answer);
            }
        }

        // an array
        else {

            // not a specific instance (can't have set of enum indicators)
            if (endsWith($this->completevariablename, "]") == false) {

                // get current array
                $currentarray = $engine->getAnswer($this->completevariablename);

                // answer is not an array, then make it one
                if (!is_array($answer)) {

                    // we have something!
                    if ($answer != "") {
                        $answer = array($answer);
                    }
                    // nothing!
                    else {
                        $answer = array();
                    }
                }

                // flatten array (http://stackoverflow.com/questions/9546181/flatten-multidimensional-array-concatenating-keys)
                $answer = flatten($answer);

                // store each individual value
                $bool = true;
                if (sizeof($answer) > 0) {
                    foreach ($answer as $key => $value) {
                        if (!$engine->setAnswer($this->completevariablename . "[" . $key . "]", $value, $this->getDirty())) {
                            $bool = false;
                        }
                    }
                }

                // reset any values no longer present
                if (is_array($currentarray)) {
                    foreach ($currentarray as $ck => $cv) {
                        if (isset($answer[$ck]) == false) {
                            if (!$engine->setAnswer($this->completevariablename . "[" . $ck . "]", null, DATA_DIRTY)) {
                                $bool = false;
                            }
                        }
                    }
                }
                
                // store complete array answer if value(s), don't strip any tags!
                if (sizeof($answer) > 0) {
                    $this->storeAnswer($primkey, $engine->prefixVariableName($this->completevariablename), gzcompress(serialize($answer)), false);
                }
                // no values, then store as empty
                else {
                    $this->storeAnswer($primkey, $engine->prefixVariableName($this->completevariablename), "");
                }
                return true;
            }
            // specific instance
            else {

                // TODO: strip the last [] --> see getBasicName in functions.php for code
                $varname = trim(substr($this->completevariablename, 0, strrpos($this->completevariablename, "["))); // TODO fix this to always get the correct [ after the last .
                $index = substr($this->completevariablename, strrpos($this->completevariablename, "[") + 1);
                $index = trim(substr($index, 0, strlen($index) - 1));

                // get entire answer
                $ans = $engine->getAnswer($varname);
                if ($ans == "" || is_null($ans)) {
                    $arr = array();
                } else {
                    $arr = $ans; //unserialize(gzuncompress($ans));
                }

                // set of enumerated variable
                if ($this->answertype == ANSWER_TYPE_SETOFENUMERATED || $this->answertype == ANSWER_TYPE_MULTIDROPDOWN) {

                    // specific set of enumerated option: e.g. Q1_1_[1,2]
                    $matches = array();
                    preg_match("/(_[0-9]+_\b){1}/", $varname, $matches);
                    if (sizeof($matches) > 0) {

                        // get indicated option
                        $bracketvalue = str_replace("_", "", $matches[0]);

                        // get current values
                        global $engine;
                        $real = preg_replace("/(_[0-9]+_\b)/", "", $varname);
                        $values = explode(SEPARATOR_SETOFENUMERATED, $engine->getAnswer($real));

                        // set to empty
                        if ($answer == null) {
                            if (in_array($bracketvalue, $values)) {
                                $values[array_search($bracketvalue, $values)] = null; //
                                unset($values[array_search($bracketvalue, $values)]);
                            }
                        }
                        // set to response
                        else {
                            if (strtoupper($answer) == ANSWER_RESPONSE) {
                                if (inArray($bracketvalue, $values)) {
                                    $values[array_search($bracketvalue, $values)] = $bracketvalue;
                                } else {
                                    $values[] = $bracketvalue;
                                }
                            }
                        }

                        $final = array();
                        foreach ($values as $k => $v) {
                            if ($v != "") {
                                $final[$k] = $v;
                            }
                        }

                        sort($final); // sort ascending
                        $answer = implode(SEPARATOR_SETOFENUMERATED, $final);
                    }

                    // no specific set of enum option, then $answer is what we need to store
                    else {
                        /* do nothing */
                    }
                }

                // not set of enum question, then $answer is what we need to store
                else {
                    /* do nothing */
                }

                // update array
                $arr[$index] = $answer;

                // flatten array
                $arr = flatten($arr); // flatten array

                // store updated array first, so the last call sets the in-memory answer properly
                //$engine->setAnswer($varname, gzcompress(serialize($arr)));
                // store complete array answer, don't strip any tags!
                $this->storeAnswer($primkey, $engine->prefixVariableName($varname), gzcompress(serialize($arr)), false);

                // array answer, then add individual entries
                if (is_array($answer)) {
                    if (sizeof($answer) > 0) {
                        $temparray[$index] = $answer;
                        $temparray = flatten($temparray); // flatten array
                        foreach ($temparray as $key => $value) {
                            if (!$engine->setAnswer($varname . "[" . $key . "]", $value, $this->getDirty())) {
                                $bool = false;
                            }
                        }
                    }
                }

                // store the separate value under the specified name (e.g. Q1[1,1])
                // answer itself is an array
                if (is_array($answer)) {

                    // store array answer, don't strip any tags!
                    $this->storeAnswer($primkey, $engine->prefixVariableName($this->completevariablename), gzcompress(serialize($answer)), false);
                }
                // answer is not an array
                else {
                    $this->storeAnswer($primkey, $engine->prefixVariableName($this->completevariablename), $answer);
                }

                // return result
                return true;
            }
        }
    }

    private function storeAnswer($primkey, $variable, $answer, $striptags = true) {
        global $engine;
        $localdb = null;
        if (Config::useTransactions() == true) {
            global $transdb;
            $localdb = $transdb;
        } else {
            global $db;
            $localdb = $db;
        }
        $dirty = $this->getDirty();
        $prim = $primkey;
        $var = $variable; //$engine->prefixVariableName($variable);
        $ans = $answer;
        if (!is_array($ans) && $ans == "" && strlen($ans) == 0) { // preserve '0' as answer
            $ans = null;
        }
        $version = getSurveyVersion();
        $language = getSurveyLanguage();
        $mode = getSurveyMode();
        $suid = getSurvey();

        // set session language/mode here if changed through routing!
        if ($ans !== null) {
            if (strtoupper($variable) == strtoupper(VARIABLE_LANGUAGE)) {
                $_SESSION['PARAMS'][SESSION_PARAM_LANGUAGE] = $ans;
            } else if (strtoupper($variable) == strtoupper(VARIABLE_MODE)) {
                $_SESSION['PARAMS'][SESSION_PARAM_MODE] = $ans;
            } else if (strtoupper($variable) == strtoupper(VARIABLE_VERSION)) {
                $_SESSION['PARAMS'][SESSION_PARAM_VERSION] = $ans;
            } else if (strtoupper($variable) == strtoupper(VARIABLE_TEMPLATE)) {
                $_SESSION['PARAMS'][SESSION_PARAM_TEMPLATE] = $ans;
            }
        }

        /* set attributes for data record processing in export */
        $this->suid = $suid;
        $this->primkey = $prim;
        $this->language = $language;
        $this->mode = $mode;
        $this->version = $version;
        $this->ts = date("Y-m-d h:i:s", time());

        if (Config::prepareDataQueries() == false) {
            global $survey;
            $key = $survey->getDataEncryptionKey();
            if ($ans === null && $ans !== 0) {
                $answer = 'null';
            } else {
                $answer = '"' . prepareDatabaseString($ans, $striptags) . '"';
                if ($key != "") {
                    $answer = "aes_encrypt('" . prepareDatabaseString($ans, $striptags) . "', '" . $key . "')";
                }
            }
            $queryparams = 'suid, primkey, variablename, answer, dirty, version, language, mode';
            $queryvalues = prepareDatabaseString($suid);
            $queryvalues .= ",'" . prepareDatabaseString($prim) . "'";
            $queryvalues .= ",'" . prepareDatabaseString($var) . "'";
            $queryvalues .= "," . $answer;
            $queryvalues .= "," . prepareDatabaseString($dirty);
            $queryvalues .= "," . prepareDatabaseString($version);
            $queryvalues .= "," . prepareDatabaseString($language);
            $queryvalues .= "," . prepareDatabaseString($mode);
            $query = 'REPLACE INTO ' . Config::dbSurveyData() . '_data (' . $queryparams . ') VALUES (' . $queryvalues . ')';
            if ($localdb->executeQuery($query)) {
                $this->variable["answer"] = $ans;
                return true;
            }
            return false;
        } else {
            $bp = new BindParam();

            $bp->add(MYSQL_BINDING_STRING, $suid);
            $bp->add(MYSQL_BINDING_STRING, $prim);
            $bp->add(MYSQL_BINDING_STRING, $var);
            $bp->add(MYSQL_BINDING_STRING, $ans);
            $bp->add(MYSQL_BINDING_INTEGER, $dirty);
            $bp->add(MYSQL_BINDING_INTEGER, $version);
            $bp->add(MYSQL_BINDING_INTEGER, $language);
            $bp->add(MYSQL_BINDING_INTEGER, $mode);

            global $survey;
            $key = $survey->getDataEncryptionKey();
            $answer = "?";
            if ($key != "") {
                $answer = "aes_encrypt(?, '" . $key . "')";
            }

            $queryparams = 'suid, primkey, variablename, answer, dirty, version, language, mode';
            $queryvalues = '?,?,?,' . $answer . ',?,?,?,?';
            $query = 'REPLACE INTO ' . Config::dbSurveyData() . '_data (' . $queryparams . ') VALUES (' . $queryvalues . ')';
            if ($localdb->executeBoundQuery($query, $bp->get())) {
                $this->variable["answer"] = $ans;
                return true;
            }
            return false;
        }
    }

    function getDirty() {
        return $this->dirty;
    }

    function isDirty() {
        return $this->dirty == DATA_DIRTY;
    }

    function setDirty($dirty) {
        $this->dirty = $dirty;
    }

    function getSuid() {
        return $this->suid;
    }

    function getVariableDescriptiveName() {
        return $this->variablename;
    }

    function setVariableDescriptiveName($v) {
        $this->variablename = $v;
    }

    function getDataName() {
        return $this->completevariablename;
    }

    function setDataName($dn) {
        $this->completevariablename = $dn;
    }

    function getLanguage() {
        return $this->language;
    }

    function getMode() {
        return $this->mode;
    }

    function getTs() {
        return $this->ts;
    }

    function getVersion() {
        return $this->version;
    }

    function getVariableDescriptive() {
        return $this->variabledescriptive;
    }

    /*
      function setVariableDescriptive($var) {
      $this->variabledescriptive = $var;
      } */

    function getVsid() {
        return $this->variable['vsid'];
    }

}

?>