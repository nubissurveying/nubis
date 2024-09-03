<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class State extends NubisObject {

    private $stateid;
    private $mainseid;
    private $seid;
    private $parentseid;
    private $parentprefix;
    private $parentrgid;
    private $primkey;
    private $displayed;
    private $subdisplays;
    private $rgid;
    private $data;
    private $assignments;
    private $types;
    private $template;
    private $filltexts;
    private $displaycounter;
    private $displaynumbers;
    private $suid;
    private $survey;
    private $loopstring;
    private $leftoff;
    private $looprgid;
    private $whilergid;
    private $whileleftoff;
    private $inlinefields;
    private $language;
    private $mode;
    private $version;
    private $prefix;
    private $variables;

    function __construct($prim, $suid) {
        $this->primkey = $prim;
        $this->data = array();
        $this->assignments = array();
        $this->filltexts = array();
        $this->subdisplays = array();
        $this->displaycounter = 0;
        $this->displaynumbers = array();
        $this->suid = $suid;
        $this->loopstring = "";
        $this->leftoff = 0;
        $this->whileleftoff = 0;
        $this->inlinefields = array();
        $this->variables = array();
    }

    function getLoopString() {

        return $this->loopstring;
    }

    function setLoopString($cnt) {

        $this->loopstring = $cnt;
    }

    function getLoopRgid() {

        return $this->looprgid;
    }

    function setLoopRgid($looprgid) {

        $this->looprgid = $looprgid;
    }

    function getForLoopLastAction() {

        return $this->leftoff;
    }

    function setForLoopLastAction($leftoff) {

        $this->leftoff = $leftoff;
    }

    function getWhileRgid() {

        return $this->whilergid;
    }

    function setWhileRgid($looprgid) {

        $this->whilergid = $looprgid;
    }

    function getWhileLastAction() {

        return $this->whileleftoff;
    }

    function setWhileLastAction($leftoff) {

        $this->whileleftoff = $leftoff;
    }

    function getDisplayCounter() {

        $this->displaycounter++;

        return $this->displaycounter;
    }

    function setDisplayCounter($dc) {
        $this->displaycounter = $dc;
    }

    function getDisplayNumbers() {

        return $this->displaynumbers;
    }

    function getDisplayed() {

        return $this->displayed;
    }

    function setDisplayNumbers($dn) {

        $this->displaynumbers = $dn;
    }

    function setDisplayed($displayed) {

        $this->displayed = $displayed;
    }

    function addInlineField($variable) {
        $this->inlinefields[strtoupper($variable)] = $variable;
    }

    function isInlineField($variable) {
        return inArray($variable, $this->inlinefields);
    }

    function getInlineFields() {
        return $this->inlinefields;
    }

    function setInlineFields($array) {
        $this->inlinefields = $array;
    }

    function addSubDisplay($variables, $template) {

        $this->subdisplays[] = array("variables" => $variables, "template" => $template);

        return (sizeof($this->subdisplays) - 1);
    }

    function getSubDisplays() {

        return $this->subdisplays;
    }

    function setSubDisplays($subdisplays) {

        $this->subdisplays = $subdisplays;
    }

    function getTemplate() {

        return $this->template;
    }

    function setTemplate($template) {

        $this->template = $template;
    }

    function getStateId() {

        return $this->stateid;
    }

    function setStateId($stid) {

        $this->stateid = $stid;
    }

    function getMainSeid() {

        return $this->mainseid;
    }

    function setMainSeid($seid) {

        $this->mainseid = $seid;
    }

    function getSuid() {
        return $this->suid;
    }

    function setSuid($suid) {
        $this->suid = $suid;
    }

    function getSeid() {

        return $this->seid;
    }

    function setSeid($seid) {

        $this->seid = $seid;
    }

    function getParentSeid() {

        return $this->parentseid;
    }

    function setParentSeid($seid) {

        $this->parentseid = $seid;
    }

    function getParentRgid() {
        return $this->parentrgid;
    }

    function setParentRgid($seid) {
        $this->parentrgid = $seid;
    }

    function getParentPrefix() {

        return $this->parentprefix;
    }

    function setParentPrefix($prefix) {

        $this->parentprefix = $prefix;
    }

    function getRgid() {

        return $this->rgid;
    }

    function setRgid($rgid) {

        $this->rgid = $rgid;
    }

    function getPrefix() {

        return $this->prefix;
    }

    function setPrefix($prefix) {

        $this->prefix = $prefix;
    }

    function loadState($stid, $mainseid, $seid, $prefix) {

        global $db, $survey;
        $key = $survey->getDataEncryptionKey();
        $data = "data as data_dec";
        $assignments = "assignments as assignments_dec";
        $fills = "fills as fills_dec";
        if ($key != "") {
            $data = "aes_decrypt(data, '" . $key . "') as data_dec";
            $assignments = "aes_decrypt(assignments, '" . $key . "') as assignments_dec";
            $fills = "aes_decrypt(fills, '" . $key . "') as fills_dec";
        }
        if (Config::retrieveDataFromState() == false) {
            $q = "select suid, stateid, mainseid, seid, parentseid, parentrgid, parentprefix, prefix, primkey, rgid, displayed, template, loopstring, looplastaction, looprgid, whilergid, whilelastaction, subdisplays, inlinefields, language, mode, version, $assignments, $fills from " . Config::dbSurveyData() . "_states where suid=" . prepareDatabaseString($this->suid) . "  and mainseid=" . prepareDatabaseString($mainseid) . " and seid=" . prepareDatabaseString($seid) . " and prefix='" . prepareDatabaseString($prefix) . "' and primkey='" . prepareDatabaseString($this->primkey) . "' and stateid=" . prepareDatabaseString($stid);
        } else {
            $q = "select suid, stateid, mainseid, seid, parentseid, parentrgid, parentprefix, prefix, primkey, rgid, displayed, template, loopstring, looplastaction, looprgid, whilergid, whilelastaction, subdisplays, inlinefields, language, mode, version, $data, $assignments, $fills from " . Config::dbSurveyData() . "_states where suid=" . prepareDatabaseString($this->suid) . "  and mainseid=" . prepareDatabaseString($mainseid) . " and seid=" . prepareDatabaseString($seid) . " and prefix='" . prepareDatabaseString($prefix) . "' and primkey='" . prepareDatabaseString($this->primkey) . "' and stateid=" . prepareDatabaseString($stid);
        }
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            $this->setSuid($row["suid"]);
            $this->stateid = $row["stateid"];
            $this->mainseid = $row["mainseid"];
            $this->seid = $row["seid"];
            $this->parentseid = $row["parentseid"];
            $this->parentrgid = $row["parentrgid"];
            $this->parentprefix = $row["parentprefix"];
            $this->prefix = $row["prefix"];
            $this->primkey = $row["primkey"];
            $this->rgid = $row["rgid"];
            $this->displayed = $row["displayed"];
            $this->template = $row["template"];
            $this->loopstring = $row["loopstring"];
            $this->leftoff = $row["looplastaction"];
            $this->looprgid = $row["looprgid"];
            $this->whileleftoff = $row["whilelastaction"];
            $this->whilergid = $row["whilergid"];
            if (Config::retrieveDataFromState()) {
                $this->loadData($row["data_dec"]);
            }
            $this->loadAssignments($row["assignments_dec"]);
            $this->loadFillTexts($row["fills_dec"]);
            $this->loadSubDisplays($row["subdisplays"]);
            $this->loadInlineFields($row["inlinefields"]);
            $this->language = $row["language"];
            $this->mode = $row["mode"];
            $this->version = $row["version"];
            return true;
        }

        /* no state found */
        return false;
    }

    function loadInlineFields($inlinefields) {
        if ($inlinefields != "") {
            $this->inlinefields = unserialize(gzuncompress($inlinefields));
        }
    }

    function loadSubDisplays($subdisplays) {

        if ($subdisplays != "") {

            $this->subdisplays = unserialize(gzuncompress($subdisplays));
        }
    }

    function loadFillTexts($fills) {

        if ($fills != "") {

            $this->filltexts = unserialize(gzuncompress($fills));
        }
    }

    function loadAssignments($assignments) {

        if ($assignments != "") {

            $this->assignments = unserialize(gzuncompress($assignments));
        }
    }

    function loadData($data) {

        if ($data != "") {

            $this->data = unserialize(gzuncompress($data));
        }
    }

    function saveState() {

        global $db, $survey;

        $key = $survey->getDataEncryptionKey();

        $data = "?";

        $assignments = "?";

        $fills = "?";

        if ($key != "") {

            $data = "aes_encrypt(?, '" . $key . "')";

            $assignments = "aes_encrypt(?, '" . $key . "')";

            $fills = "aes_encrypt(?, '" . $key . "')";
        }



        $query = "replace into " . Config::dbSurveyData() . "_states (suid, mainseid, seid, parentseid, parentrgid, prefix, parentprefix, stateid, primkey, rgid, displayed, looprgid, loopstring, looplastaction, whilergid, whilelastaction, template, assigned, data, assignments, fills, subdisplays, inlinefields, language, mode, version) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,$data,$assignments,$fills,?,?,?,?,?)";
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->mainseid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->seid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->parentseid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->parentrgid);
        $bp->add(MYSQL_BINDING_STRING, $this->prefix);
        $bp->add(MYSQL_BINDING_STRING, $this->parentprefix);
        $bp->add(MYSQL_BINDING_INTEGER, $this->stateid);
        $bp->add(MYSQL_BINDING_STRING, $this->primkey);
        $bp->add(MYSQL_BINDING_INTEGER, $this->rgid);
        $bp->add(MYSQL_BINDING_STRING, $this->displayed);
        $bp->add(MYSQL_BINDING_INTEGER, $this->looprgid);
        $bp->add(MYSQL_BINDING_STRING, $this->loopstring);
        if ($this->leftoff == null) {
            $this->leftoff = "";
        }
        $bp->add(MYSQL_BINDING_STRING, $this->leftoff);
        $bp->add(MYSQL_BINDING_INTEGER, $this->whilergid);
        if ($this->whileleftoff == null) {
            $this->whileleftoff = "";
        }
        $bp->add(MYSQL_BINDING_STRING, $this->whileleftoff);

        $bp->add(MYSQL_BINDING_STRING, $this->template);

        $assigned = implode("~", $this->getAssigned());

        $bp->add(MYSQL_BINDING_STRING, $assigned);

        $data = gzcompress($this->saveData());

        $assignments = gzcompress($this->saveAssignments());

        $fills = gzcompress($this->saveFillText());

        $subdisplays = gzcompress($this->saveSubDisplays());

        $inlinefields = gzcompress($this->saveInlineFields());

        $bp->add(MYSQL_BINDING_STRING, $data);

        $bp->add(MYSQL_BINDING_STRING, $assignments);

        $bp->add(MYSQL_BINDING_STRING, $fills);

        $bp->add(MYSQL_BINDING_STRING, $subdisplays);

        $bp->add(MYSQL_BINDING_STRING, $inlinefields);

        $l = getSurveyLanguage();
        $m = getSurveyMode();
        $v = getSurveyVersion();

        $bp->add(MYSQL_BINDING_INTEGER, $l);
        $bp->add(MYSQL_BINDING_INTEGER, $m);
        $bp->add(MYSQL_BINDING_INTEGER, $v);
        $db->executeBoundQuery($query, $bp->get());
    }

    function saveData() {

        return serialize($this->data);
    }

    function saveAssignments() {

        return serialize($this->assignments);
    }

    function saveFillText() {

        return serialize($this->filltexts);
    }

    function saveInlineFields() {
        return serialize($this->inlinefields);
    }

    function saveSubDisplays() {

        return serialize($this->subdisplays);
    }

    function getVariableNames() {
        return array_keys($this->data);
    }

    function getVariable($variablename) {

        if (isset($this->variables[strtoupper($variablename)])) {

            return $this->variables[strtoupper($variablename)];
        }



        $variable = new Variable();

        if ($variable->setVariable($variablename)) {

            $this->variables[strtoupper($variablename)] = $variable;

            return $this->variables[strtoupper($variablename)];
        }

        return new Variable();
    }

    function setVariable($variablename, $variable) {

        $this->variables[strtoupper($variablename)] = $variable;

        $this->data[strtoupper($variablename)] = $variable->getAnswer();
    }

    function getAllData() {

        return $this->data;
    }

    function setAllData($data) {

        $this->data = $data;
    }

    function getDirty($variablename) {
        global $engine;
        $variable = new Variable();
        $dirty = $variable->retrieveDirty($engine->getPrimaryKey(), $variablename);
        if ($dirty != "") {
            return $dirty;
        }
        return DATA_CLEAN; // something went wrong, assume clean
    }

    function getData($variablename) {

        /* in memory */
        if (array_key_exists(strtoupper($variablename), $this->data)) {
            return $this->data[strtoupper($variablename)];
        }

        /* retrieve from database */
        $variable = new Variable();
        if ($variable->setVariable($variablename)) {
            $this->variables[strtoupper($variablename)] = $variable;
            $answer = $variable->getAnswer($this->primkey);
            global $engine;
            $var = $engine->getVariableDescriptive($variablename);
            if ($var->isArray() && is_array($answer) && sizeof($answer) == 0) {
                $answer = null;
            }
            $this->data[strtoupper($variablename)] = $answer;
            return $this->data[strtoupper($variablename)];
        }

        return null;
    }

    function setData($variablename, $answer, $clean = 1) {

        /* variable in memory */
        if (isset($this->variables[strtoupper($variablename)])) {
            $variable = $this->variables[strtoupper($variablename)];
        } else {

            $variable = new Variable();
            if (!$variable->setVariable($variablename)) {
                $variable = null;
            }
        }

        /* we have variable */
        if ($variable != null) {
            $variable->setDirty($clean);

            /* check for EMPTY */
            if ($answer !== null && ((!is_array($answer) && strtoupper($answer) == VARIABLE_VALUE_EMPTY) || (is_array($answer) && sizeof($answer) == 0) || (!is_array($answer) && trim($answer) == "" && $answer !== 0))) {
                $answer = null;
            }

            if ($variable->setAnswer($this->primkey, $answer)) {

                /* update in-memory */
                $this->variables[strtoupper($variablename)] = $variable;
                $this->data[strtoupper($variablename)] = $answer;

                /* update state memory if array update 
                 * (this does not happen in variable.php via $engine->setAnswer()
                 * in order to avoid an infinite loop)
                 */
                global $engine;
                $var = $engine->getVariableDescriptive($variablename);
                if ($var->isArray() && contains($variablename, "[")) {

                    $arr = $this->getData(getBasicName($variablename));
                    $index = str_replace("]", "", substr($variablename, strrpos($variablename, "[") + 1));

                    // update array
                    $arr[$index] = $answer;

                    // flatten array
                    $arr = flatten($arr); // flatten array
                    $this->data[strtoupper(getBasicName($variablename))] = $arr;
                }
                // set of enum/multi-dropdown and answer is for whole set of enum (so not something like q1_1_ := response), then update in-memory option selections
                else if (($var->getAnswerType() == ANSWER_TYPE_SETOFENUMERATED || $var->getAnswerType() == ANSWER_TYPE_MULTIDROPDOWN) && !contains($variablename, "_")) {
                    $options = $var->getOptions();
                    $values = array();
                    if ($answer != "") {
                        $values = explode(SEPARATOR_SETOFENUMERATED, $answer);
                    }
                    foreach ($options as $o) {
                        $code = $o["code"];
                        if (isset($this->data[strtoupper($variablename . "_" . $code . "_")])) {
                            if (!inArray($code, $values)) {
                                $this->data[strtoupper($variablename . "_" . $code . "_")] = null;
                            }
                        } else {
                            if (inArray($code, $values)) {
                                $this->data[strtoupper($variablename . "_" . $code . "_")] = $code;
                            }
                        }
                    }
                }

                /* update data record */
                $engine->getDataRecord()->setData($variable);
                return true;
            }
        }

        return false;
    }

    function addAssignment($variablename, $oldvalue, $rgid) {
        $this->assignments[] = array("variable" => $variablename, "value" => $oldvalue, "rgid" => $rgid);
    }

    function removeAssignmentsAfterRgid($rgid) {
        foreach ($this->assignments as $k => $assign) {
            if ($assign["rgid"] >= $rgid) {
                unset($this->assignments[$k]);
            }
        }
    }

    function getAssignment($variablename, $oldvalue, $rgid) {

        foreach ($this->assignments as $assign) {

            if (strtoupper($assign["variable"]) == strtoupper($variablename) && $assign["value"] == $oldvalue && $assign["rgid"] == $rgid) {

                return $assign;
            }
        }

        return null;
    }

    function getAssignments() {

        return $this->assignments;
    }

    function setAssignments($arr) {

        $this->assignments = $arr;
    }

    function getAssigned() {

        $array = array();

        foreach ($this->assignments as $assign) {

            $array[] = $assign["variable"];
        }

        return array_unique($array);
    }

    function undoAssignments($cleanvariables) {



        /* collapse assignment array to only keep 

         * one assignment for each variable

         * (specifically the oldest one)

         */

        $unique = array();
        $originalname = array();
        foreach ($this->assignments as $as) {
            // this ensures we only keep the oldest one
            if (!isset($unique[strtoupper($as["variable"])])) {
                $unique[strtoupper($as["variable"])] = $as["value"];
                $originalname[strtoupper($as["variable"])] = $as["variable"];
            }
        }



        /* undo assignments */
        foreach ($unique as $variable => $value) {
            if (inArray($variable, $cleanvariables)) {
                $dirty = DATA_CLEAN;
            } else {
                $dirty = DATA_DIRTY;
            }
            $original = $originalname[$variable];
            $this->setData($original, $value, $dirty);
        }

        return;
    }

    function addFillText($variable, $array) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                if (sizeof($v) == 0) {
                    unset($array[$k]);
                }
            } else if (trim($v) == "") {
                unset($array[$k]);
            }
        }
        $this->filltexts[strtoupper($variable)] = $array;
    }

    function getFillTexts() {
        return $this->filltexts;
    }

    function setFillTexts($filltexts) {
        $this->filltexts = $filltexts;
    }

    function getFillText($variable) {

        if (isset($this->filltexts[strtoupper($variable)])) {

            return $this->filltexts[strtoupper($variable)];
        }

        return null;
    }

    function getLanguage() {
        return $this->language;
    }

    function setLanguage($l) {
        $this->language = $l;
    }

    function getMode() {
        return $this->mode;
    }

    function setMode($l) {
        $this->mode = $l;
    }

    function getVersion() {
        return $this->version;
    }

    function setVersion($l) {
        $this->version = $l;
    }

    function deleteState() {
        global $db;
        $db->executeQuery('delete from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->suid) . ' and mainseid=' . prepareDatabaseString($this->mainseid) . ' and seid=' . prepareDatabaseString($this->seid) . ' and prefix="' . prepareDatabaseString($this->prefix) . '" and primkey = "' . prepareDatabaseString($this->primkey) . '" and stateid=' . prepareDatabaseString($this->stateid));
    }

    function deleteLastState() {
        global $db;
        $db->executeQuery('delete from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->suid) . ' and mainseid=' . prepareDatabaseString($this->mainseid) . ' and seid=' . prepareDatabaseString($this->seid) . ' and prefix="' . prepareDatabaseString($this->prefix) . '" and primkey = "' . prepareDatabaseString($this->primkey) . '" order by stateid desc limit 1');
    }

}

?>