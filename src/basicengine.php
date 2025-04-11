<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class BasicEngine extends NubisObject {

    var $primkey;
    var $phpid;
    var $version;
    var $state;
    var $variabledescriptives;
    var $functions;
    var $types;
    var $groups;
    var $sections;
    var $sectionids;
    var $endofsurvey; // indicates whether we are at the end of the survey
    var $prefix; // prefix for variables if e.g. mod1[1].Q1
    var $parentprefix;
    var $seid; // section identifier
    var $mainseid; // main section identifier (the section we started the interview with)
    var $parentseid; // parent section identifier (empty if this is the top engine)    
    var $previousstateid;
    var $survey;
    var $getfillclasses;
    var $setfillclasses;
    var $checkclasses;
    var $setfills; // keeps track of name/rgid pairings for any .FILL calls
    var $inlinefieldclasses;
    var $inlinefields;
    var $progressbar;
    var $currentloopcount;
    var $firsttime;
    var $startatbegin; // indicates whether to force loop to start at beginning (only true if coming directly from end of nested loop)
    var $datarecord;
    var $previousrgid;
    var $previouslooprgid;
    var $previousloopaction;
    var $previousloopstring;
    var $previouswhilergid;
    var $previouswhileaction;
    var $display;
    var $firsttimeloopset;
    var $redofills;
    var $forward; // indicates if we are moving forward (next, dk, rf, or na clicked)
    var $updateaction; // indicates whether we are doing update/change language/change mode/change version
    var $dk; // holds any variables with dk answers
    var $rf; // holds any variables with rf answers
    var $na; // holds any variables with na answers
    var $currentaction; // indicates action taken (back, next, and so on)
    var $locked; // interview status: unlocked/locked
    var $firsttimelock; // indicates whether interview status was saved before
    var $processedfills;
    var $justassigned;
    private $flooding;
    private $stop;
    var $firstform;
    private $externalonly;
    private $reset; 
    var $reloadscreen;
    var $parentrgid; 

    function __construct($suid, $primkey, $phpid, $version, $seid, $doState = true, $doContext = true) {

        $this->primkey = $primkey;
        $this->phpid = $phpid;
        $this->setSuid($suid);
        if (isset($_SESSION['URID']) && $_SESSION['URID'] != '') {
            require_once("user.php"); // needed here
            require_once("users.php"); // needed here
            require_once("contact.php"); // needed here
            require_once("contacts.php"); // needed here
            $user = new User($_SESSION['URID']);
            if (isTestmode() && inArray($user->getUserType(), array(USER_SYSADMIN, USER_TRANSLATOR, USER_TESTER))) {
                $this->display = new DisplayQuestionTest($this->primkey, $this);
            } else if ($user->getUserType() == USER_NURSE) {
                $this->display = new DisplayQuestionNurse($this->primkey, $this);
            } else {
                $this->display = new DisplayQuestionSms($this->primkey, $this);
            }
        } else {
            $this->display = new DisplayQuestion($this->primkey, $this);
        }

        global $survey;
        $this->survey = $survey;
        $this->version = $version;

        /* get context */
        if ($doContext == true) {
            $this->loadContext();
        }

        /* data record */
        $this->datarecord = new DataRecord($suid, $primkey);
        $this->datarecord->loadRecord();

        /* do state */
        if ($doState == true) {

            /* get main section and current section */
            $this->mainseid = getSurveyMainSection($suid, $primkey);
            $this->seid = getSurveySection($suid, $primkey);

            /* check for existing state */
            $this->state = new State($this->primkey, $this->survey->getSuid());
            if ($this->loadLastState() == true) {
                $this->previousrgid = $this->getRgid();
                $this->previousloopaction = $this->getForLoopLastAction();
                $this->previousloopstring = $this->getLoopString();
                $this->previouslooprgid = $this->getLoopRgid();
                $this->previouswhilergid = $this->getWhileRgid();
                $this->previouswhileaction = $this->getWhileLastAction();
            }
            // no state found, then create new state
            else {

                $this->state->setSuid($suid);
                $this->setMainSeid($this->mainseid);
                $this->setSeid($this->seid);
                $this->setPrefix("");
                $this->setParentPrefix("");
                $this->setParentSeid(0);
                $this->setParentRgid(0);

                /* set loop string, loop rgid and loop left off to empty by default */
                $this->setLoopString("");
                $this->setLoopRgid("");
                $this->setForLoopLastAction("");
                $this->setWhileRgid("");
                $this->setWhileLastAction("");
            }
        } else {
            $this->seid = $seid; // set for loadSetFillClasses
        }

        $this->flooding = false;
        $this->stop = false;
        $this->startatbegin = false;
        $this->redofills = false;
        $this->forward = false;
        $this->firstform = false;
        $this->updateaction = false;
        $this->reloadscreen = false;
        $this->reset = array();

        $this->dk = array();
        $this->rf = array();
        $this->na = array();
        $this->processedfills = array();
        $this->justassigned = array();
        $this->currentaction = ACTION_DYNAMIC;
        $this->externalonly = array();
    }

    /* CONTEXT FUNCTIONS */

    function setFirstForm($first = false) {
        $this->firstform = $first;
    }

    function setRedoFills($redofills) {
        $this->redofills = $redofills;
    }

    function getPrimaryKey() {
        return $this->primkey;
    }

    function loadContext() {
        global $db;
        if (Config::useUnserialize()) {
            $q = "select * from " . Config::dbSurvey() . "_context where suid=" . prepareDatabaseString($this->getSuid()) . " and version=" . prepareDatabaseString($this->version);
        } else {
            $q = "select getfills, inlinefields, setfills, checks from " . Config::dbSurvey() . "_context where suid=" . prepareDatabaseString($this->getSuid()) . " and version=" . prepareDatabaseString($this->version);
        }
        $r = $db->selectQuery($q);
        $this->variabledescriptives = array();
        $this->types = array();
        $this->groups = array();
        $this->sections = array();
        if ($row = $db->getRow($r)) {
            if (Config::useUnserialize()) {
                if ($row["variables"] != "") {
                    $this->variabledescriptives = unserialize(gzuncompress($row["variables"]));
                }
                if ($row["types"] != "") {
                    $this->types = unserialize(gzuncompress($row["types"]));
                }
                if ($row["groups"] != "") {
                    $this->groups = unserialize(gzuncompress($row["groups"]));
                }
                if ($row["sections"] != "") {
                    $this->sections = unserialize(gzuncompress($row["sections"]));
                    foreach ($this->sections as $s) {
                        $this->sectionids[$s->getSeid()] = $s;
                    }
                }
            }
            if ($row["getfills"] != "") {
                $this->getfillclasses = unserialize(gzuncompress($row["getfills"]));
            }
            if ($row["inlinefields"] != "") {
                $this->inlinefieldclasses = unserialize(gzuncompress($row["inlinefields"]));
            }
            if ($row["setfills"] != "") {
                $this->setfillclasses = unserialize(gzuncompress($row["setfills"]));
            }
            if (isset($row["checks"]) && $row["checks"] != "") {
                $this->checkclasses = unserialize(gzuncompress($row["checks"]));
            }
        }
    }

    function clearContext() {
        $this->variabledescriptives = null;
        $this->functions = null;
        $this->types = null;
        $this->groups = null;
        $this->sections = null;
        $this->getfillclasses = null;
        $this->inlinefieldclasses = null;
        $this->setfillclasses = null;
        $this->checkclasses = null;
        unset($this->variabledescriptives);
        unset($this->functions);
        unset($this->types);
        unset($this->groups);
        unset($this->sections);
        unset($this->getfillclasses);
        unset($this->inlinefieldclasses);
        unset($this->setfillclasses);
        unset($this->checkclasses);
    }

    /* CHECKS FUNCTION */

    function applyChecks() {
        if ($this->survey->isApplyChecks() == false) {
            return;
        }
    }

    /* INLINE FIELD FUNCTIONS */

    function loadInlineFieldClass($fillclass, $fillclasscode) {

        /* check if we loaded it before */
        try {
            $fillcl = new ReflectionClass($fillclass);
            if ($fillcl) {
                //return $engineclass->newInstance($suid, $primkey, $phpid, $version, $mainseid, $seid, $prefix, $parentseid, $parentprefix);
                return $fillcl->newInstance($this);
            }
        } catch (Exception $e) {
            
        }

        ob_start();
        eval($fillclasscode);
        $contents = ob_get_clean();
        if ($contents == "") {
            try {
                $fillcl = new ReflectionClass($fillclass);
                if ($fillcl) {
                    return $fillcl->newInstance($this);
                }
            } catch (Exception $e) {
                
            }
        }
    }

    function addInlineField($variable) {

        /* no need to add inline fields if we hit update/change language, since
         * we still have those from the state, but it just overwrites earlier entries
         * so it is ok to just proceed here
         */
        /* if ($this->updateaction == true) {

          } */
        $this->state->addInlineField($this->getInlineField($variable));
    }

    function isInlineField($variable) {
        return $this->state->isInlineField($variable);
    }

    function setInlineFields($array) {
        $this->state->setInlineFields($array);
    }

    function getInlineFields() {
        return $this->state->getInlineFields();
    }

    function replaceInlineFields($text, $enumid = "", $enumtype = "", $enumvalue = "", $customtemplate = false) {
        $displaynumbers = $this->getDisplayNumbers();
        $temp = $this->getTemplate();

        /* replace inline question texts */
        $cnt = 0;
        while (strpos($text, INDICATOR_INLINEFIELD_TEXT) !== false) {
            $fields = getReferences($text, INDICATOR_INLINEFIELD_TEXT);

            // sort inline fields by longest keys
            //uksort($fields, "compareLength");
            usort($fields, "reversenat");

            foreach ($fields as $field) {

                if ($field == "") {
                    $fieldref = $fill;
                    $replacetext = DUMMY_INDICATOR_INLINEFIELD_TEXT;
                } else {

                    $realfield = $this->getInlineField($field); // update in case of brackets
                    $fieldref = $field; //str_replace("[", "\[", str_replace("]", "\]", $field));
                    // only if in group we add inline fields
                    if ($temp != "") {

                        //$cnt = $displaynumbers[strtoupper($realfield)];
                        // we have a cnt, then this is a field being displayed!
                        //if ($cnt != "") {
                        // NO RESTRICTION ON SHOWING TEXT HERE FOR WHETHER QUESTION IS INLINE FIELD (WE ONLY DO THAT FOR INPUT BOXES)
                        $replacetext = $this->display->showQuestionText($realfield, $this->getVariableDescriptive($realfield), "uscic-question-inline");
                        //}
                    } else {
                        $replacetext = "";
                    }
                }

                $pattern = "/\\" . INDICATOR_INLINEFIELD_TEXT . preparePattern($fieldref) . "/i";
                $text = preg_replace($pattern, $replacetext, $text);
            }
            $cnt++;

            /* stop after 999 times */
            if ($cnt > 999) {
                break;
            }
        }

        $pattern = "/\\DUMMY_INDICATOR_INLINEFIELD_TEXT/i";
        $text = preg_replace($pattern, INDICATOR_INLINEFIELD_TEXT, $text);

        /* replace answer fields */
        $cnt = 0;
        while (strpos($text, INDICATOR_INLINEFIELD_ANSWER) !== false) {

            $fields = getReferences($text, INDICATOR_INLINEFIELD_ANSWER);

            // sort inline fields by longest keys
            //uksort($fields, "compareLength");
            usort($fields, "reversenat");

            foreach ($fields as $field) {

                if ($field == "") {
                    $fieldref = $fill;
                    $replacetext = DUMMY_INDICATOR_INLINEFIELD_ANSWER;
                } else {
                    $realfield = $this->getInlineField($field); // update in case of brackets
                    $fieldref = $field; //str_replace("[", "\[", str_replace("]", "\]", $field));
                    // only if in group we add inline fields
                    if ($temp != "") {
                        $tt = $this->getAnswer($realfield);
                        if ($tt === null) {
                            $tt = "";
                        }
                        $previousdata = strtr($tt, array('\\' => '\\\\', '$' => '\$'));
                        $variable = $this->getVariableDescriptive($field);
                        $cnt = "";
                        if (isset($displaynumbers[strtoupper($realfield)])) {
                            $cnt = $displaynumbers[strtoupper($realfield)];
                        }

                        // we have a cnt, then this is a field being displayed!
                        if ($cnt != "") {

                            /* if radio/set of enumerated, then add error check if inline field filled out and option not checked */
                            $varname = SESSION_PARAMS_ANSWER . $cnt;
                            $id = $this->getFill($realfield, $variable, SETTING_ID);
                            if (trim($id) == "") {
                                $id = $varname;
                            }
                            $cm = $this->display->getCustomTemplateMode();
                            $this->display->setCustomTemplate($customtemplate);                                
                            if (trim($enumid) != "") {
                                $legend = "legend_" . str_replace("]", "", str_replace("[", "", $enumid));                                
                                $replacetext = $this->display->showAnswer($cnt, $realfield, $variable, $previousdata, true, $enumid . "_" . $enumvalue, $legend);                                
                            } else {
                                $replacetext = $this->display->showAnswer($cnt, $realfield, $variable, $previousdata, true);
                            }
                            $this->display->setCustomTemplate($cm);

                            if (inArray($variable->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                                $varname .= "[]";
                            }

                            /* add firefox double click script for inline text fields */
                            $replacetext .= $this->display->displayAutoFocusScript($id);

                            /* if radio/set of enumerated, then add auto-select script and error check */
                            if ($enumid != "") {
                                $replacetext .= $this->display->displayAutoSelectScript($id, $varname, $enumid, $enumtype, $enumvalue, $variable->getAnswerType());
                            }
                        }
                        // field not found, then replace with empty
                        else {
                            $replacetext = "";
                        }
                    } else {
                        $replacetext = "";
                    }
                }

                $pattern = "/\\" . INDICATOR_INLINEFIELD_ANSWER . preparePattern($fieldref) . "/i";
                $text = preg_replace($pattern, $replacetext, $text);
            }
            $cnt++;

            /* stop after 999 times */
            if ($cnt > 999) {
                break;
            }
        }

        $pattern = "/\\DUMMY_INDICATOR_INLINEFIELD_ANSWER/i";
        $text = preg_replace($pattern, INDICATOR_INLINEFIELD_ANSWER, $text);

        return $text;
    }

    function updateInlineFields($text) {
        $cnt = 0;
        if (strpos($text, INDICATOR_INLINEFIELD_ANSWER) !== false) {
            $fields = getReferences($text, INDICATOR_INLINEFIELD_ANSWER);

            // sort inline fields by longest keys
            usort($fields, "reversenat");

            foreach ($fields as $field) {
                if ($field == "") {
                    continue;
                }
                $realfield = INDICATOR_INLINEFIELD_ANSWER . $this->getInlineField($field); // update in case of brackets
                $fieldref = $field; //str_replace("[", "\[", str_replace("]", "\]", $field));
                $pattern = "/\\" . INDICATOR_INLINEFIELD_ANSWER . preparePattern($fieldref) . "/i";
                $text = preg_replace($pattern, $realfield, $text);
            }
        }

        $cnt = 0;
        if (strpos($text, INDICATOR_INLINEFIELD_TEXT) !== false) {
            $fields = getReferences($text, INDICATOR_INLINEFIELD_TEXT);

            // sort inline fields by longest keys
            usort($fields, "reversenat");

            foreach ($fields as $field) {
                if ($field == "") {
                    continue;
                }
                $realfield = INDICATOR_INLINEFIELD_TEXT . $this->getInlineField($field); // update in case of brackets
                $fieldref = $field; //str_replace("[", "\[", str_replace("]", "\]", $field));
                $pattern = "/\\" . INDICATOR_INLINEFIELD_TEXT . preparePattern($fieldref) . "/i";
                $text = preg_replace($pattern, $realfield, $text);
            }
        }

        return $text;
    }

    function getInlineField($variable) {
        if ($this->inlinefieldclasses) {
            $classextension = prepareClassExtension($variable);
            if (isset($this->inlinefieldclasses['"' . strtoupper($variable) . '"'])) {
                $class = $this->loadInlineFieldClass(CLASS_INLINEFIELD . "_" . $classextension, $this->inlinefieldclasses['"' . strtoupper($variable) . '"']);
                if ($class) {
                    $result = $class->getInlineField(strtoupper($variable));
                    if ($result != "") {
                        return $result;
                    }
                }
            }
        }
        return $variable;
    }

    function getInlineFieldText($variable) {

        $value = $this->state->getInlineFieldText($variable);
        if ($value != "") {
            return $value;
        }
        return $this->getInlineField($variable);
    }

    function getInlineFieldValue($variable) {
        return $this->getAnswer($variable);
    }

    /* GET FILL FUNCTIONS */

    function getProcessedFills() {
        return $this->processedfills;
    }

    function setProcessedFills($pr) {
        $this->processedfills = $pr;
    }

    function getFillValue($variable) {
        // always redo fills
        //if ($this->redofills == true) {
        //if (isset($this->setfillclasses)) {
        //   if (isset($this->setfillclasses[strtoupper(getBasicName($variable))])) {
        //$this->setFillValue($this->setfillclasses[strtoupper(getBasicName($variable))]);
        if (!inArray(getBasicName($variable), $this->processedfills) && $this->wasAssigned(getBasicName($variable)) == false) { // only do each fill once!
            $this->setFillValue(getBasicName($variable));
            $this->processedfills[] = getBasicName($variable);
        }
        //  }
        //}
        //}

        if ($this->getfillclasses) {

            $classextension = prepareClassExtension($variable);
            if (isset($this->getfillclasses['"' . strtoupper($variable) . '"'])) {
                $getfillclass = $this->loadGetFillClass(CLASS_GETFILL . "_" . $classextension, $this->getfillclasses['"' . strtoupper($variable) . '"']);
                if ($getfillclass) {

                    if (Config::filling() == FILL_NO_SPACE_INSERT) {
                        return $getfillclass->getFillValue();
                    } else if (Config::filling() == FILL_SPACE_INSERT_BEFORE) {
                        return " " . $getfillclass->getFillValue();
                    } else if (Config::filling() == FILL_SPACE_INSERT_AFTER) {
                        return $getfillclass->getFillValue() . " ";
                    } else if (Config::filling() == FILL_SPACE_INSERT_AROUND) {
                        return " " . $getfillclass->getFillValue() . " ";
                    }
                }
            }
        }
        return "";
    }

    function loadGetFillClass($fillclass, $fillclasscode) {

        /* check if we loaded it before */
        try {
            $fillcl = new ReflectionClass($fillclass);
            if ($fillcl) {
                //return $engineclass->newInstance($suid, $primkey, $phpid, $version, $mainseid, $seid, $prefix, $parentseid, $parentprefix);
                return $fillcl->newInstance($this);
            }
        } catch (Exception $e) {
            
        }

        ob_start();
        eval($fillclasscode);
        $contents = ob_get_clean();

        if ($contents == "") {
            try {
                $fillcl = new ReflectionClass($fillclass);
                if ($fillcl) {
                    return $fillcl->newInstance($this);
                }
            } catch (Exception $e) {
                
            }
        }
    }

    /* SET FILL FUNCTIONS */

    //function loadSetFillClasses() {
    //  $this->setfillclasses = loadSetFillClasses($this->getSuid(), $this->seid, $this->version);
    //}

    function loadSetFillClass($fillclass, $fillclasscode) {

        /* check if we loaded it before */
        try {
            $fillcl = new ReflectionClass($fillclass);
            if ($fillcl) {
                return $fillcl->newInstance($this);
            }
        } catch (Exception $e) {
            
        }

        ob_start();
        eval($fillclasscode);
        $contents = ob_get_clean();
        if ($contents == "") {
            try {
                $fillcl = new ReflectionClass($fillclass);
                if ($fillcl) {
                    return $fillcl->newInstance($this);
                }
            } catch (Exception $e) {
                
            }
        }
    }

    function setFillValue($variable) {
        $variable = trim($variable);
        if ($this->setfillclasses && isset($this->setfillclasses[strtoupper($variable) . getSurveyLanguage() . getSurveyMode()])) {// PHP 8 ISSUE
            $setfillclass = $this->loadSetFillClass(CLASS_SETFILL . "_" . $variable, $this->setfillclasses[strtoupper($variable) . getSurveyLanguage() . getSurveyMode()]);
            if ($setfillclass) {
                // execute fill code
                $setfillclass->doAction($setfillclass->getFirstAction());
            }
        }
    }

    function setFillTexts($array) {
        $this->state->setFillTexts($array);
    }

    /* OBJECT RETRIEVAL FUNCTIONS */

    function getVariableDescriptive($variable) {
        $variable = getBasicName($variable);
        if (isset($this->variabledescriptives[strtoupper($variable)])) {
            return $this->variabledescriptives[strtoupper($variable)];
        }

        /* something went wrong, so we get it from the db */
        $this->variabledescriptives[strtoupper($variable)] = $this->survey->getVariableDescriptiveByName($variable); //new VariableDescriptive($variable);
        return $this->variabledescriptives[strtoupper($variable)];
    }

    function getVariableDescriptives() {
        return $this->variabledescriptives;
    }

    function getType($type) {
        if (isset($this->types[strtoupper($type)])) {
            return $this->types[strtoupper($type)];
        }
        /* something went wrong, so we get it from the db */
        $this->types[strtoupper($type)] = $this->survey->getTypeByName($type);
        return $this->types[strtoupper($type)];
    }

    function getTypeById($tyd) {
        if (isset($this->types)) {
            foreach ($this->types as $type) {
                if ($type->getTyd() == $tyd) {
                    return $type;
                }
            }
        }
        /* something went wrong, so we get it from the db */
        $type = $this->survey->getType($type);
        $this->types[strtoupper($type->getName())] = $type;
        return $type;
    }

    function getTypes() {
        return $this->types;
    }

    // called in a inspectsection construction
    function getSectionIdentifier($name) {
        $prefix = $name;
        if (contains($name, ".")) {
            $prefix = substr($name, 0, strripos($name, ".") + 1); // strip anything before a . in the name
        }

        $section = $this->getSection(str_replace(".", "", $prefix));
        if ($section->getName() != "") {
            return $section->getSeid();
        }
        return "";
    }

    function getSection($section) {

        if (is_numeric($section)) {
            if (isset($this->sectionids[$section])) {
                $section = $this->sectionids[$section];
                return $section;
            } else {
                $s = $this->survey->getSection($section);
                $this->sections[strtoupper($s->getName())] = $s;
                $this->sectionids[$section] = $s;
                return $s;
            }
        }

        if (isset($this->sections[strtoupper($section)])) {
            return $this->sections[strtoupper($section)];
        }

        /* something went wrong, so we get it from the db */
        $this->sections[strtoupper($section)] = $this->survey->getSectionByName($section);
        return $this->sections[strtoupper($section)];
    }

    function getSections() {
        return $this->sections;
    }

    function getGroup($group) {
        if (isset($this->groups[strtoupper($group)])) {
            return $this->groups[strtoupper($group)];
        }
        /* something went wrong, so we get it from the db */
        $this->groups[strtoupper($group)] = $this->survey->getGroupByName($group);
        return $this->groups[strtoupper($group)];
    }

    function getGroups() {
        return $this->groups;
    }

    /* DATA RECORD FUNCTIONS */

    function getDataRecord() {
        return $this->datarecord;
    }

    /* REMARK FUNCTIONS */

    function updateRemarkStatus($dirty = DATA_DIRTY) {
        global $db;
        $query = "update " . Config::dbSurveyData() . "_observations set dirty=" . prepareDatabaseString($dirty) . " where suid=" . prepareDatabaseString($this->getSuid()) . " and primkey='" . prepareDatabaseString($this->getPrimaryKey()) . "' and displayed='" . prepareDatabaseString(getFromSessionParams(SESSION_PARAM_VARIABLES)) . "'"; // stateid=" . $this->getStateID(); // . " and displayed='" . $this->getDisplayed() . "'";
        $db->executeQuery($query);
    }

    function loadRemark() {
        global $db, $survey;
        $key = $survey->getDataEncryptionKey();
        $extra = "remark";
        if ($key != "") {
            $extra = "aes_decrypt(remark, '" . prepareDatabaseString($key) . "') as remark";
        }
        //$stateid = $this->getStateID();
        //if ($this->getForward() == true) {
        //    $stateid++; // moving forward, so the current state id is the one we left; if there is any remark, then it will be under the next state
        //}
        $query = "select " . $extra . " from " . Config::dbSurveyData() . "_observations where suid=" . prepareDatabaseString($this->getSuid()) . " and primkey='" . prepareDatabaseString($this->getPrimaryKey()) . "' and displayed='" . prepareDatabaseString($this->getDisplayed()) . "'"; // and stateid=" . $stateid; // . " and displayed='" . $this->getDisplayed() . "'";
        if ($res = $db->selectQuery($query)) {
            if ($db->getNumberOfRows($res) > 0) {
                $row = $db->getRow($res);
                return $row["remark"];
            }
        }
        return "";
    }

    /* STATE FUNCTIONS */

    function isMainSection() {
        return ($this->getParentSeid() == 0);
    }

    function isFirstState() {
        global $db;
        $cnt = 0;
        // no check here for seid, since we want to know if we are at the beginning of the survey as a whole (displayed != "", so we ignore states of section calls)!
        $result = $db->selectQuery('select count(*) as cnt from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid()) . ' and displayed != "" group by primkey');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $cnt = $row['cnt'];
        }
        return ($cnt <= 1);
    }

    function removeAllStatesExceptFirst() {
        global $db;
        $result = $db->selectQuery('select stateid from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid()) . ' and displayed != "" order by stateid asc limit 0,1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $first = $row['stateid'];
            $q = "delete from " . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and stateid > ' . prepareDatabaseString($first);
            $db->executeQuery($q);
        }
    }

    function removeAllStates() {
        global $db;
        $q = "delete from " . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid());
        $db->executeQuery($q);
    }

    function reinitializeState() {
        $this->state = null;
        unset($this->state);
        $this->state = new State($this->primkey, $this->survey->getSuid());
        $this->state->setSuid($this->survey->getSuid());
        $this->setMainSeid($this->mainseid);
        $this->setSeid(0);
        $this->setPrefix("");
        $this->setParentPrefix("");
        $this->setParentSeid(0);

        /* set loop string, loop rgid and loop left off to empty by default */
        $this->setLoopString("");
        $this->setLoopRgid("");
        $this->setForLoopLastAction("");
        $this->setWhileRgid("");
        $this->setWhileLastAction("");
    }

    function alreadyStarted() {
        global $db;
        $result = $db->selectQuery('select * from ' . Config::dbSurveyData() . '_data where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" limit 0,1');
        if ($result) {
            if ($db->getNumberOfRows($result) == 0) {
                return false; // not started yet
            }
            return true; // started
        }
        return true; // assume already started, since we don't know, and if we did there is a problem with the states table
    }

    function loadLastState() {
        global $db;
        $result = $db->selectQuery('select stateid, mainseid, seid, prefix from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid()) . ' order by stateid desc limit 0,1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $result = $this->state->loadState($row['stateid'], $row["mainseid"], $row["seid"], $row["prefix"]);
            if ($result) {
                $this->setMainSeid($this->state->getMainSeid());
                $this->setSeid($this->state->getSeid());
                $this->setPrefix($this->state->getPrefix());
                $this->setParentPrefix($this->state->getParentPrefix());
                $this->setParentSeid($this->state->getParentSeid());
                $this->setParentRgid($this->state->getParentRgid());
                return true;
            }
        }
        return false;
    }

    function loadLastSectionState() {
        global $db;
        $result = $db->selectQuery('select stateid, mainseid, seid, prefix from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid()) . ' and seid=' . prepareDatabaseString($this->seid) . ' and prefix="' . prepareDatabaseString($this->prefix) . '" and displayed != "" order by stateid desc limit 0,1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $loopstring = $this->getLoopString(); // preserve from last state!
            $looprgid = $this->getLoopRgid();
            $loopaction = $this->getForLoopLastAction();
            $result = $this->state->loadState($row['stateid'], $row["mainseid"], $row["seid"], $row["prefix"]);
            if ($result) {
                $this->setLoopString($loopstring);
                $this->setLoopRgid($looprgid);
                $this->setForLoopLastAction($loopaction);
                $this->setWhileRgid($looprgid);
                $this->setWhileLastAction($loopaction);
                $this->setMainSeid($this->state->getMainSeid());
                $this->setSeid($this->state->getSeid());
                $this->setPrefix($this->state->getPrefix());
                $this->setParentPrefix($this->state->getParentPrefix());
                $this->setParentSeid($this->state->getParentSeid());
                $this->setParentRgid($this->state->getParentRgid());
                return true;
            }
        }
        return false;
    }

    function loadPreviousSectionEntryState() {
        global $db;
        $parentprefix = $this->parentprefix;
        $prefix = $this->prefix;
        if ($prefix == "") {
            $parentprefix = "";
        }
        else {            
            if ($parentprefix != "") {
                $parentprefix .= ".";
            }
        }
        $result = $db->selectQuery('select stateid, mainseid, seid, prefix from ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid()) . ' and seid=' . prepareDatabaseString($this->seid) . ' and prefix="' . prepareDatabaseString($prefix) . '" and parentprefix="' . prepareDatabaseString($parentprefix) . '" and displayed="" order by stateid desc limit 0,1');
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $state = new State($this->primkey, $this->survey->getSuid());
            $result = $state->loadState($row['stateid'], $row["mainseid"], $row["seid"], $row["prefix"]);
            if ($result) {
                return $state;
            }
        }
        return null;
    }

    function deleteLastState() {
        $this->state->deleteLastState();
    }

    function getState() {
        return $this->state;
    }

    function getStateId() {
        return $this->state->getStateId();
    }

    function saveState($new = true) {
        if ($new) {
            $this->state->setStateId($this->state->getStateId() + 1);
        }
        return $this->state->saveState();
    }

    function getDisplayed() {
        return $this->state->getDisplayed();
    }

    function getRgid() {
        return $this->state->getRgid();
    }

    function getSeid() {
        return $this->state->getSeid();
    }

    function getMainSeid() {
        return $this->mainseid;
        return $this->state->getMainSeid();
    }

    function getParentSeid() {
        return $this->state->getParentSeid();
    }

    function getParentPrefix() {
        return $this->state->getParentPrefix();
    }

    function getParentRgid() {
        return $this->state->getParentRgid();
    }

    function getTemplate() {
        return $this->state->getTemplate();
    }

    function getProgress($type = PROGRESSBAR_WHOLE) {
        $progressbar = loadProgressBar($this->getSuid(), $this->getMainSeid(), $this->version);
        if ($progressbar) {
            if ($type == PROGRESSBAR_WHOLE) {
                $current = $progressbar->getScreenNumber($this->getSeid(), $this->getParentRgid(), $this->getRgid(), $this->getLoopString());
                $total = $progressbar->getNumberOfScreens();
            } else {
                $progressbar = new Progressbar($this->getSuid(), $this->getMainSeid());
                $current = $progressbar->getSectionProgress($this->getSuid(), $this->getMainSeid(), $this->getSeid(), $this->getRgid(), $this->getLoopString(), $this->getLoopRgid());
                $total = $progressbar->getSectionTotal($this->getSuid(), $this->getSeid());
            }
            return ($current / $total);
        }
        return "";
    }

    function getPrefix() {
        return $this->state->getPrefix();
    }

    function setPrefix($prefix) {
        $this->prefix = $prefix;
        $this->state->setPrefix($prefix);
    }

    function setDisplayed($variables) {
        $this->state->setDisplayed($variables);
    }

    function setRgid($rgid) {
        $this->state->setRgid($rgid);
    }

    function setSeid($seid) {
        $this->seid = $seid;
        $this->state->setSeid($seid);
    }

    function setMainSeid($seid) {
        $this->mainseid = $seid;
        $this->state->setMainSeid($seid);
    }

    function setParentSeid($seid) {
        $this->parentseid = $seid;
        $this->state->setParentSeid($seid);
    }

    function setParentRgid($rgid) {
        $this->parentrgid = $rgid;
        $this->state->setParentRgid($rgid);
    }

    function setParentPrefix($prefix) {
        $this->parentprefix = $prefix; 
        $this->state->setParentPrefix($prefix);
    }

    function setTemplate($template) {
        $this->state->setTemplate($template);
    }

    function setState($state) {
        $this->state = $state;
    }

    // check assignment values
    function checkAnswer($variablename, $answer) {

        if ($this->survey->isValidateAssignment() == true) {
            if (!is_array($answer)) {
                $answer = trim($answer);
            } else { // array, then approve
                return VALID_ASSIGNMENT;
            }
            $var = $this->getVariableDescriptive($variablename);
            $ans = $var->getAnswerType();
            switch ($ans) {
                case ANSWER_TYPE_OPEN:
                /* fall through */
                case ANSWER_TYPE_STRING:
                    $min = $this->getFill($variablename, $var, SETTING_MINIMUM_LENGTH);
                    $max = $this->getFill($variablename, $var, SETTING_MAXIMUM_LENGTH);
                    $len = strlen($answer);
                    if ($min > 0 && $len < $min) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    if ($max > 0 && $len > $max) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }

                    $min = $this->getFill($variablename, $var, SETTING_MINIMUM_WORDS);
                    $max = $this->getFill($variablename, $var, SETTING_MAXIMUM_WORDS);
                    $count = str_word_count($answer);
                    if ($min > 0 && $count < $min) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    if ($max > 0 && $count > $max) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }

                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_INTEGER:
                    if ($answer != "0" && !is_integer($answer)) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_DOUBLE:
                    if ($answer != "0" && !is_numeric($answer)) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_DROPDOWN:
                /* fall through */
                case ANSWER_TYPE_ENUMERATED:
                    if ($answer != "0" && !is_integer($answer)) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    $options = $this->getFill($variablename, $var, SETTING_OPTIONS);
                    foreach ($options as $c) {
                        if ($c["code"] == $answer) {
                            return VALID_ASSIGNMENT;
                        }
                    }
                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_MULTIDROPDOWN:
                /* fall through */
                case ANSWER_TYPE_SETOFENUMERATED:
                    $answers = explode(SEPARATOR_SETOFENUMERATED, $answer);
                    $options = $this->getFill($variablename, $var, SETTING_OPTIONS);
                    foreach ($answers as $a) {
                        if (!is_integer($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                        $found = false;
                        foreach ($options as $c) {
                            if ($c["code"] == $a) {
                                $found = true;
                                break;
                            }
                        }
                        if ($found == false) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_SLIDER:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */
                case ANSWER_TYPE_RANGE:
                    $min = $this->getFill($variablename, $var, SETTING_MINIMUM_RANGE);
                    $max = $this->getFill($variablename, $var, SETTING_MAXIMUM_RANGE);
                    $others = $this->getFill($variablename, $var, SETTING_OTHER_RANGE);
                    $others = explode(",", $others);

                    // real range
                    if (contains($min, ".") || contains($max, ".")) {
                        if (!is_numeric($answer)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                        if (($answer < $min || $answer > $max) && !inArray($answer, $others)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                        return $this->checkAnswerComparison($variablename, $var, $answer);
                    }
                    // integer range
                    else {
                        if (!is_integer($answer)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                        if (($answer < $min || $answer > $max) && !inArray($answer, $others)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                        return $this->checkAnswerComparison($variablename, $var, $answer);
                    }
                /* date/time/datetime */
                case ANSWER_TYPE_DATETIME:
                /* fall through */
                case ANSWER_TYPE_DATE:
                    if (strtotime($answer) == false) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_TIME:
                    if (strtotime(date("Y-m-d") . " " . $answer) == false) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                    return $this->checkAnswerComparison($variablename, $var, $answer);
                case ANSWER_TYPE_NONE:
                /* fall through */
                case ANSWER_TYPE_SECTION:
                    return INVALID_ASSIGNMENT;
                case ANSWER_TYPE_RANK:
                    $t = explode(SEPARATOR_SETOFENUMERATED, $answer);
                    $options = $this->getFill($variablename, $var, SETTING_OPTIONS);
                    foreach ($t as $a) {
                        if (!is_integer($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                        $found = false;
                        foreach ($options as $c) {
                            if ($c["code"] == $a) {
                                $found = true;
                                break;
                            }
                        }
                        if ($found == false) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
            }
        }
        return VALID_ASSIGNMENT;
    }

    function checkAnswerComparison($variablename, $var, $answer) {

        /* error checks numeric comparison */
        $at = $var->getAnswerType();

        if (inArray($at, array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
            $answerstring = implode(SEPARATOR_SETOFENUMERATED, sort(explode(SEPARATOR_SETOFENUMERATED, $answer)));
            $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
            if ($eq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($arr as $a) {
                    $arrstring = implode(SEPARATOR_SETOFENUMERATED, sort(explode(SEPARATOR_SETOFENUMERATED, $a)));
                    if ($arrstring != $answerstring) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                }
            }
            $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
            if ($neq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($arr as $a) {
                    $arrstring = implode(SEPARATOR_SETOFENUMERATED, sort(explode(SEPARATOR_SETOFENUMERATED, $a)));
                    if ($arrstring == $answerstring) {
                        $this->display->addAssignmentWarning($variablename, $answer);
                        return INVALID_ASSIGNMENT;
                    }
                }
            }
            $answers = explode(SEPARATOR_SETOFENUMERATED, $answer);
            $geq = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
            if ($geq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $geq);
                foreach ($arr as $a) {
                    foreach ($answers as $b) {
                        if ($b < $a) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $gr = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER));
            if ($gr != "") {
                $arr = explode(SEPARATOR_COMPARISON, $gr);
                foreach ($arr as $a) {
                    foreach ($answers as $b) {
                        if ($b <= $a) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $seq = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
            if ($seq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $seq);
                foreach ($arr as $a) {
                    foreach ($answers as $b) {
                        if ($b > $a) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $sm = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
            if ($sm != "") {
                $arr = explode(SEPARATOR_COMPARISON, $sm);
                foreach ($arr as $a) {
                    foreach ($answers as $b) {
                        if ($b >= $a) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }

            return $this->checkAnswerSetOfEnumeratedChecks($variablename, $var, $answer);
        } else if (inArray($at, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SLIDER))) {
            $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
            if ($eq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($arr as $a) {
                    if (is_numeric(trim($a))) {
                        if (trim($a) != $answer) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
            if ($neq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($arr as $a) {
                    if (is_numeric(trim($a))) {
                        if (trim($a) == $answer) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $geq = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
            if ($geq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $geq);
                foreach ($arr as $a) {
                    if (is_numeric(trim($a))) {
                        if ($answer < trim($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $gr = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER));
            if ($gr != "") {
                $arr = explode(SEPARATOR_COMPARISON, $gr);
                foreach ($arr as $a) {
                    if (is_numeric(trim($a))) {
                        if ($answer <= trim($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $seq = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
            if ($seq != "") {
                $arr = explode(SEPARATOR_COMPARISON, seq);
                foreach ($arr as $a) {
                    if (is_numeric(trim($a))) {
                        if ($answer > trim($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $sm = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
            if ($sm != "") {
                $arr = explode(SEPARATOR_COMPARISON, $sm);
                foreach ($arr as $a) {
                    if (is_numeric(trim($a))) {
                        if ($answer >= trim($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
        }
        // string comparison
        else if (inArray($at, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
            if ($eq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($answer != trim($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
            if ($neq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($answer == trim($a)) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE));
            if ($eq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if (strtoupper($answer) != strtotupper(trim($a))) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE));
            if ($neq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if (strtoupper($answer) == strtoupper(trim($a))) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
        }
        // error checking date/time
        else if (inArray($at, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME))) {
            $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
            if ($eq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $eq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($at == ANSWER_TYPE_TIME) {
                            $a = date("Y-m-d") . " " . $a;
                        }
                        $t = strtotime($a);
                        if (strtotime($answer) != $t) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
            if ($neq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $neq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($at == ANSWER_TYPE_TIME) {
                            $a = date("Y-m-d") . " " . $a;
                        }
                        $t = strtotime($a);
                        if (strtotime($answer) == $t) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $geq = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
            if ($geq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $geq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($at == ANSWER_TYPE_TIME) {
                            $a = date("Y-m-d") . " " . $a;
                        }
                        $t = strtotime($a);
                        if (strtotime($answer) < $t) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }

            $gr = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER));
            if ($gr != "") {
                $arr = explode(SEPARATOR_COMPARISON, $gr);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($at == ANSWER_TYPE_TIME) {
                            $a = date("Y-m-d") . " " . $a;
                        }
                        $t = strtotime($a);
                        if (strtotime($answer) <= $t) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $seq = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
            if ($seq != "") {
                $arr = explode(SEPARATOR_COMPARISON, $seq);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($at == ANSWER_TYPE_TIME) {
                            $a = date("Y-m-d") . " " . $a;
                        }
                        $t = strtotime($a);
                        if (strtotime($answer) > $t) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
            $sm = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
            if ($sm != "") {
                $arr = explode(SEPARATOR_COMPARISON, $sm);
                foreach ($arr as $a) {
                    $variable = str_replace(" ", "", $a);
                    $vartemp = $this->getVariableDescriptive($variable);
                    if ($vartemp->getVsid() == "") { // if not a variable, then we assume it is a literal text
                        if ($at == ANSWER_TYPE_TIME) {
                            $a = date("Y-m-d") . " " . $a;
                        }
                        $t = strtotime($a);
                        if (strtotime($answer) >= $t) {
                            $this->display->addAssignmentWarning($variablename, $answer);
                            return INVALID_ASSIGNMENT;
                        }
                    }
                }
            }
        }

        return VALID_ASSIGNMENT;
    }

    function checkAnswerSetOfEnumeratedChecks($name, $variable, $var) {
        $min = $this->getFill($variable, $var, SETTING_MINIMUM_SELECTED);
        if ($min != "") {
            if (sizeof(explode(SEPARATOR_SETOFENUMERATED, $answer)) < $min) {
                return INVALID_ASSIGNMENT;
            }
        }
        $max = $this->getFill($variable, $var, SETTING_MAXIMUM_SELECTED);
        if ($max != "") {
            if (sizeof(explode(SEPARATOR_SETOFENUMERATED, $answer)) > $max) {
                return INVALID_ASSIGNMENT;
            }
        }
        $exact = $this->getFill($variable, $var, SETTING_EXACT_SELECTED);
        if ($exact != "") {
            if (sizeof(explode(SEPARATOR_SETOFENUMERATED, $answer)) != $exact) {
                return INVALID_ASSIGNMENT;
            }
        }
        $invalidsub = $this->getFill($variable, $var, SETTING_INVALIDSUB_SELECTED);
        if ($invalidsub != "") {
            $indices = explode(SEPARATOR_SETOFENUMERATED, $answer);
            $invalids = explode(SEPARATOR_COMPARISON, $invalidsub);
            foreach ($invalids as $s) {
                $invalid = explode(",", $s);
                $selected = array();
                $invalidselected = array();
                for ($cnt = 0; $cnt < sizeof($invalid); $cnt++) {
                    $inv = $invalid[$cnt];
                    if (contains($inv, "-")) {
                        $t = explode("-", $inv);
                        $all = true;
                        for ($cnt1 = $t[0]; $cnt1 <= $t[1]; $cnt1++) {
                            if (!inArray($cnt1, $indices)) {
                                $all = false;
                                break;
                            }
                            $invalidselected[] = $cnt1;
                        }
                        if ($all == true) {
                            $selected[$cnt] = 0;
                        } else {
                            $selected[$cnt] = -1;
                        }
                    } else {
                        $invalidselected[] = $inv;
                        $key = array_search($inv, $indices);
                        if (!$key) {
                            $selected[$cnt] = -1; // returns -1 if not found
                        } else {
                            $selected[$cnt] = $key;
                        }
                    }
                }

                // no -1, then all found
                if (!inArray(-1, $selected)) {
                    return INVALID_ASSIGNMENT;
                }
            }
        }
        $invalid = $this->getFill($variable, $var, SETTING_INVALID_SELECTED);
        if ($invalid != "") {
            $indices = explode(SEPARATOR_SETOFENUMERATED, $answer);
            $invalids = explode(SEPARATOR_COMPARISON, $invalidsub);
            foreach ($invalids as $s) {
                $invalid = explode(",", $s);
                $selected = array();
                $invalidselected = array();
                for ($cnt = 0; $cnt < sizeof($invalid); $cnt++) {
                    $inv = $invalid[$cnt];
                    if (contains($inv, "-")) {
                        $t = explode("-", $inv);
                        $all = true;
                        for ($cnt1 = $t[0]; $cnt1 <= $t[1]; $cnt1++) {
                            if (!inArray($cnt1, $indices)) {
                                $all = false;
                                break;
                            }
                            $invalidselected[] = $cnt1;
                        }
                        if ($all == true) {
                            $selected[$cnt] = 0;
                        } else {
                            $selected[$cnt] = -1;
                        }
                    } else {
                        $invalidselected[] = $inv;
                        $key = array_search($inv, $indices);
                        if (!$key) {
                            $selected[$cnt] = -1; // returns -1 if not found
                        } else {
                            $selected[$cnt] = $key;
                        }
                    }
                }

                // no -1, then all found
                if (!inArray(-1, $selected)) {

                    // if size of selected indices is the same as the total number selected, then false;
                    // otherwise we selected more than the invalid set and we thus allow it
                    if (sizeof($indices) == sizeof($invalidselected)) {
                        return INVALID_ASSIGNMENT;
                    }
                }
            }
        }
    }

    // server based validation, inactive right now
    function validateAnswer($variablename, $answer) {
        return true;
        /* $var = $this->getVariable($variablename);

          // empty check
          if ($var->isRequireAnswer() == false && $answer == "") {
          return false;
          }

          // DK/RF: allow
          if (inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
          return true;
          }

          // error check
          switch ($var->getAnswerType()) {
          case ANSWER_TYPE_STRING: //string
          return true;
          //TODO: ADD LENGTH/REG EXP CHECK
          case ANSWER_TYPE_ENUMERATED: //enumerated
          return true;
          case ANSWER_TYPE_SETOFENUMERATED: //set of enumerated
          return true;
          case ANSWER_TYPE_INTEGER: //integer
          return is_integer($answer);
          case ANSWER_TYPE_REAL: //real
          return is_numeric($answer);
          case ANSWER_TYPE_RANGE: //range
          return in_array($answer, $var->getRange());
          case ANSWER_TYPE_OPEN: //open
          return true;
          //TODO: REG EXP CHECK/LENGTH
          } */
    }

    function prefixVariableName($variablename) {
        if (Config::prefixing() == PREFIXING_FULL) {
            return $this->prefixVariableNameFull($variablename);
        } else if (Config::prefixing() == PREFIXING_FULL_IF_BRACKET) {
            return $this->prefixVariableNameFullIfBracket($variablename);
        }
        return $this->prefixVariableNameBracket($variablename);
    }

    function prefixVariableNameBracket($variablename) {
        $vardesc = $this->getVariableDescriptive($variablename);
        if ($vardesc->getSeid() == $this->getSeid()) {
            $full = $this->getParentPrefix() . $this->getPrefix();
            $prefixes = explode(".", $full);
            $check = "";
            foreach ($prefixes as $pr) {
                if (contains($pr, "[")) {
                    if ($check != "") {
                        $check .= ".";
                    }
                    $check .= $pr;
                }
            }
            if ($check != "") {
                $check .= ".";
            }

            if (!startsWith($variablename, $check)) {
                $variablename = $check . $variablename;
            }
        }
        return $variablename;
    }

    function prefixVariableNameFullIfBracket($variablename) {
        $vardesc = $this->getVariableDescriptive($variablename);
        if ($vardesc->getSeid() == $this->getSeid()) {
            $full = $this->getParentPrefix() . $this->getPrefix();
            if (contains($full, "[")) {
                if (!startsWith($variablename, $this->getParentPrefix() . $this->getPrefix())) {
                    $variablename = $this->getParentPrefix() . $this->getPrefix() . $variablename;
                }
            }
        }
        return $variablename;
    }

    function prefixVariableNameFull($variablename) {
        $vardesc = $this->getVariableDescriptive($variablename);
        if ($vardesc->getSeid() == $this->getSeid()) {
            $full = $this->getParentPrefix() . $this->getPrefix();
            if (!startsWith($variablename, $this->getParentPrefix() . $this->getPrefix())) {
                $variablename = $this->getParentPrefix() . $this->getPrefix() . $variablename;
            }
        }
        return $variablename;
    }

    function setAnswer($variablename, $answer, $dirty = DATA_CLEAN) {

        /* check for external function */
        $vardesc = $this->getVariableDescriptive($variablename);
        if ($vardesc->getStoreLocation() == STORE_LOCATION_BOTH || $vardesc->getStoreLocation() == STORE_LOCATION_EXTERNAL) {
            $tocall = $vardesc->getStoreLocationExternal();
            if (function_exists($tocall) && stripos($tocall, '(') !== false) { // don't allow parameters
                if (inArray($tocall, getAllowedExternalStorageFunctions()) && !inArray($tocall, getForbiddenExternalStorageFunctions())) {
                    try {
                        $f = new ReflectionFunction($tocall);
                        $f->invoke(STORE_EXTERNAL_SET, $this->getPrimaryKey(), $variablename, $answer, $dirty);
                    } catch (Exception $e) {
                        
                    }
                }
            }

            // external only (ignore for core variables)
            if ($vardesc->getStoreLocation() == STORE_LOCATION_EXTERNAL && !inArray($variablename, Common::surveyCoreVariables())) {
                return;
            }
        }

        if ($this->validateAnswer($variablename, $answer)) {
            $vardesc = $this->getVariableDescriptive($variablename);
            $variablename1 = $this->prefixVariableName($variablename);
            $this->addLogs($variablename1, $answer, $dirty);

            return $this->state->setData($variablename1, $answer, $dirty);
        }

        return false;
    }

    function getAnswer($variablename) {

        /* check for external function (ignore for core variables) */
        $vardesc = $this->getVariableDescriptive($variablename);
        if ($vardesc->getStoreLocation() == STORE_LOCATION_EXTERNAL && !inArray($variablename, Common::surveyCoreVariables())) {
            $tocall = $vardesc->getStoreLocationExternal();
            if (function_exists($tocall) && stripos($tocall, '(') !== false) { // don't allow parameters
                if (inArray($tocall, getAllowedExternalStorageFunctions()) && !inArray($tocall, getForbiddenExternalStorageFunctions())) {
                    try {
                        $f = new ReflectionFunction($tocall);
                        return $f->invoke(STORE_EXTERNAL_GET, $this->getPrimaryKey(), $variablename);
                    } catch (Exception $e) {
                        
                    }
                }
            }
            return null; // external failed
        }

        // stored internal or both, go through internal
        $variablename = $this->prefixVariableName($variablename);
        $ans = $this->state->getData($variablename);
        return $ans;
    }

    function getDirty($variablename) {
        $variablename = $this->prefixVariableName($variablename);
        return $this->state->getDirty($variablename);
    }

    function processAnswer($variablename) {
        $ans = $this->getAnswer($variablename);
        if ($ans == null || $ans == "") {
            return;
        }
        if (strtoupper($ans) == ANSWER_DK) {
            $this->dk[] = strtoupper($variablename);
        } else if (strtoupper($ans) == ANSWER_RF) {
            $this->rf[] = strtoupper($variablename);
        } else if (strtoupper($ans) == ANSWER_NA) {
            $this->na[] = strtoupper($variablename);
        }
    }

    function getDKAnswers() {
        return $this->dk;
    }

    function isDKAnswer($variable) {
        if (inArray(strtoupper($variable), $this->getDKAnswers())) {
            return true;
        }
        return false;
    }

    function getRFAnswers() {
        return $this->rf;
    }

    function isRFAnswer($variable) {
        if (inArray(strtoupper($variable), $this->getRFAnswers())) {
            return true;
        }
        return false;
    }

    function getNAAnswers() {
        return $this->na;
    }

    function isNAAnswer($variable) {
        if (inArray(strtoupper($variable), $this->getNAAnswers())) {
            return true;
        }
        return false;
    }

    function getVariable($variable) {
        return $this->state->getVariable($variable);
    }

    function removeAssignmentsAfterRgid($rgid) {
        $this->state->removeAssignmentsAfterRgid($rgid);
    }

    function addAssignment($variablename, $oldvalue, $rgid) {

        $vardesc = $this->getVariableDescriptive($variablename);
        if ($vardesc->isKeep()) {
            return; // skip if keep is set to yes
        }

        // external storage only, don't store in assigments
        if ($vardesc->getStoreLocation() == STORE_LOCATION_EXTERNAL) {
            return;
        }
        /* if ($vardesc->getSeid() == $this->getSeid()) {
          $full = $this->getParentPrefix() . $this->getPrefix();
          if (contains($full, "[")) {
          $variablename = $this->getParentPrefix() . $this->getPrefix() . $variablename;
          }
          } */
        $variablename = $this->prefixVariableName($variablename);

        /* no need to add assignments we already have if we hit update/change language, since
         * we still have those from the state
         */
        if ($this->updateaction == true) {
            /* check here */
        }
        if (!inArray(getBasicName($variablename), $this->justassigned)) {
            $this->justassigned[] = getBasicName($variablename);
        }
        $this->state->addAssignment($variablename, $oldvalue, $rgid);
    }

    function getAssignment($variablename, $oldvalue, $rgid) {
        $vardesc = $this->getVariableDescriptive($variablename);
        /* if ($vardesc->getSeid() == $this->getSeid()) {
          $variablename = $this->getParentPrefix() . $this->getPrefix() . $variablename;
          } */
        $variablename = $this->prefixVariableName($variablename);
        return $this->state->getAssignment($variablename, $oldvalue, $rgid);
    }

    function wasAssigned($variablename) {
        $variablename = $this->prefixVariableName($variablename);
        if (!inArray($variablename, $this->justassigned)) {
            return false;
        }
        return true;
    }

    function undoAssignments($cleanvariables) {
        $this->state->undoAssignments($cleanvariables);
    }

    function setAssignments($array) {
        $this->state->setAssignments($array);
    }

    function getCleanVariables() {
        global $db;
        $result = $db->selectQuery('SET SESSION group_concat_max_len = 1000000;'); // increase default length of 1024!
        $result = $db->selectQuery('
SELECT GROUP_CONCAT( displayed SEPARATOR \'~\' ) as totaldisplayed, GROUP_CONCAT( assigned SEPARATOR \'~\' ) as totalassigned
FROM ' . Config::dbSurveyData() . '_states where suid=' . prepareDatabaseString($this->getSuid()) . ' and primkey = "' . prepareDatabaseString($this->primkey) . '" and mainseid=' . prepareDatabaseString($this->getMainSeid()));

        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            return array_unique(array_merge(explode("~", $row["totaldisplayed"]), explode("~", $row["totalassigned"])));
        }
        return array(); // something went wrong, so we assume we have no clean variables from previous state(s)
    }

    function getDisplayObject() {
        return $this->display;
    }

    /* FLOODING FUNCTIONS */

    function getFlooding() {
        return $this->flooding;
    }

    function setFlooding($flooding) {
        $this->flooding = $flooding;
    }

    /* ROUTING FUNCTIONS */

    function getForward() {
        return $this->forward;
    }

    function setForward($forward) {
        $this->forward = $forward;
    }

    /* LOOP FUNCTIONS */

    function getLoopString() {
        return $this->state->getLoopString();
    }

    function setLoopString($string) {
        $this->state->setLoopString($string);
    }

    function isFirstTimeLoop($looprgid, $outerloopcounters) {

        /* last loop action comes from previous state in database (NOT in-memory now in our current loop)
         * (if it comes from our current loop, then we don't want to reset the counter)
         */
        if ($this->getPreviousForLoopLastAction() != "") {

            /* clear so we don't keep repeating this as we are going through the loop */
            $this->setPreviousForLoopLastAction("");

            /* rgid of current loop matches that of the previous state, then we are going
             * back into this loop, so we don't want to reset
             */
            if ($looprgid == $this->getPreviousLoopRgid()) {
                $this->reset[$looprgid] = false; // prevent reset as we exit the loop and call isFirstTimeLoop again
                return false;
            }
            /* rgid of current loop comes after that of the previous state, 
             * then we are going into a new loop 
             */ else if ($looprgid > $this->getPreviousLoopRgid()) {
                return true;
            } else if ($looprgid < $this->getPreviousLoopRgid()) {
                return false;
            }
        }
        // no last loop action from db, so we are doing in-memory stuff
        else {

            /* no reset prevention set yet, then we did not do anything before */
            if (!isset($this->reset[$looprgid])) {
                return true;
            } else {
                if ($this->reset[$looprgid] != false) {
                    return true;
                }
            }
        }

        /* don't reset */
        return false;
    }

    function doForLoop($min, $max, $counterfield, $loopactions, $looprgid, $nextrgid, $outerloopcounters, $outerlooprgids, $normalfor = 1, $exitfor = 1) {

        // no loop actions OR minimum is greater than maximum Or no minimum OR no maximum, then skip
        if ($loopactions == "" || ($normalfor == 1 && $min > $max) || ($normalfor == 2 && $min < $max) || $max == "" || $min == "") {
            $this->doAction($nextrgid);
            return;
        }

        // actions and no exitfor, so we continue
        $current = $this->getAnswer($counterfield);
        $loopactions = explode("~", $loopactions);

        // determine loop string from before
        $loopstring = "";
        $oc = explode("~", $outerloopcounters);
        foreach ($oc as $o) {
            $val = $this->getAnswer($o);
            $loopstring .= $val;
        }
        $this->setLoopRgid($looprgid);

        // first time entering the loop
        if (($current < $min || $this->isFirstTimeLoop($looprgid, $outerlooprgids)) && $exitfor != 2) {
            //$this->firsttimelockloopset[$looprgid] = true;
            // store loop data
            global $db;
            $query = "replace into " . Config::dbSurveyData() . "_loopdata (suid, primkey, mainseid, seid, looprgid, loopmin, loopmax, loopcounter,looptype, loopactions) values (";
            $query .= prepareDatabaseString($this->getSuid()) . ", '" . prepareDatabaseString($this->getPrimaryKey()) . "', " . prepareDatabaseString($this->getMainSeid()) . "," . prepareDatabaseString($this->getSeid()) . ", " . prepareDatabaseString($looprgid) . "," . prepareDatabaseString($min) . "," . prepareDatabaseString($max) . ",'" . prepareDatabaseString($counterfield) . "'," . prepareDatabaseString($normalfor) . ",'" . prepareDatabaseString(implode("~", $loopactions)) . "')";
            $db->executeQuery($query);

            $this->reset[$looprgid] = false;
            $this->startLoop($current, $min, $loopstring, $counterfield, $loopactions[0], $looprgid);
        }
        // maximum has been reached/exitfor --> end of for loop
        else if ($current > $max || $exitfor == 2) {

            // nested loop we are exiting
            if ($outerloopcounters != "") {

                // update outer counter IF LAST ACTION
                if ($nextrgid < $looprgid) { // this doesn't work if nested loop is in e.g. an if inside the outer loop, since we return to the loop and then increment
                    // check if nested loop is the last action in the outer loop
                    // if yes, then do counter increment
                    // get loop actions of outer loop
                    $outeractions = explode("~", $this->getLoopActions($nextrgid));

                    // we have loop actions for this rgid, so we are going back to the outer loop
                    if (sizeof($outeractions) > 0) {

                        $last = $outeractions[sizeof($outeractions)];

                        // nested loop is last action
                        //if (true || $last == $looprgid) {
                        $countertoincrement = end($oc);
                        $now = $this->getAnswer($countertoincrement);
                        $this->addAssignment($countertoincrement, $now, $nextrgid);
                        $this->setAnswer($countertoincrement, $now + 1);
                        //$outerrgids = explode("~", $outerlooprgids);
                        //foreach ($outerrgids as $r) {
                        //   $this->reset[$r] = true;
                        //}
                        //}
                    }
                    // don't reset outer counter
                    $this->reset[$nextrgid] = false;
                }

                // allow reset of inner loop (for completely in memory looping (assignments) where we are exiting a nested loop to which we will come back again)!
                unset($this->reset[$looprgid]);

                // reset inner counter
                $current = "";
                $this->addAssignment($counterfield, $current, $looprgid);
                $this->setAnswer($counterfield, "");

                $loopstring = "";
                foreach ($oc as $o) {
                    $val = $this->getAnswer($o);
                    $loopstring .= $val;
                }

                // remove last action from most inner loop
                $this->removeForLoopLastAction();

                // remove last action from outer loop if we are returning to loop before this loop
                if ($nextrgid < $looprgid) {
                    $this->removeForLoopLastAction();
                }

                // reset loop action for outer loop IF NOT last loop action
                if ($this->reset[$nextrgid] == true) {
                    $this->resetForLoopLastAction(); // TODO: TEST THIS WITH THREE NESTED LOOPS
                }
            }
            // no more loops
            else {
                // set max to max
                $current = $this->getAnswer($counterfield);
                $this->addAssignment($counterfield, $current, $looprgid);
                $this->setAnswer($counterfield, $current - 1);
                $this->setForLoopLastAction("");
                $this->setLoopRgid("");
            }

            // loop string becomes the outer string
            $this->setLoopString($loopstring);

            /* do next action */
            $this->doAction($nextrgid);
        } else { /* still inside loop */

            // get last for loop action we did FOR THIS LOOP
            if ($outerloopcounters != "") {
                $position = sizeof($oc);
            }
            // no outer loops, then position is first one
            else {
                $position = 0;
            }
            $last = $this->getForLoopLastActionCurrentLoop($position);

            // no action found, then assume it is the first action
            if ($last == -1) {
                $index = 0;
                $next = 0;
            } else {

                // last for loop action we did == last action in for loop,
                // then we completed a loop
                if ($last == end($loopactions)) {
                    $this->completeLoop($counterfield, $current, $looprgid, $loopstring);
                    return;
                }
                // last for loop action was not the last action in for loop,
                // so we find the next action (if any)
                else {
                    $index = array_search($last, $loopactions);
                    $next = $index + 1;
                }
            }

            // action(s) left
            if (isset($loopactions[$next])) {
                $this->setLoopString($loopstring . $current); // update loop string for progress bar

                /* do next action inside loop */
                $this->doAction($loopactions[$next]);

                /* stop */
                return;
            }
            // no action(s) left/found, then completed loop
            else {
                $this->completeLoop($counterfield, $current, $looprgid, $loopstring);
            }
        }
    }

    function startLoop($current, $min, $loopstring, $counterfield, $action, $looprgid) {
        $current = $min;
        $this->setLoopString($loopstring . $current); // set loop string for progress bar
        $this->addAssignment($counterfield, "", $looprgid);
        $this->setAnswer($counterfield, $current);
        $this->doAction($action);
    }

    function completeLoop($counterfield, $current, $looprgid, $loopstring) {
        $this->addAssignment($counterfield, $current, $looprgid);
        $current++;
        $this->setAnswer($counterfield, $current);
        $this->setLoopString($loopstring . $current); // update loop string for progress bar
        $this->resetForLoopLastAction();
        $this->reset[$looprgid] = false; // prevent reset for if we are re-entering
        $this->doAction($looprgid);
    }

    function getForLoopLastAction() {
        return $this->state->getForLoopLastAction();
    }

    function getLoopRgid() {
        return $this->state->getLoopRgid();
    }

    function setLoopRgid($rgid) {
        $this->state->setLoopRgid($rgid);
    }

    function getLoopActions($looprgid) {
        global $db;
        $query = "select loopactions from " . Config::dbSurveyData() . "_loopdata where suid=" . prepareDatabaseString($this->getSuid()) . " and primkey='" . prepareDatabaseString($this->getPrimaryKey()) . "' and looprgid=" . prepareDatabaseString($looprgid);
        $res = $db->selectQuery($query);
        if ($res && $db->getNumberOfRows($res) > 0) {
            $row = $db->getRow($res);
            return $row["loopactions"];
        }

        // failed somehow
        return null;
    }

    function getForLoopLastActionCurrentLoop($position) {
        $current = trim($this->getForLoopLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getForLoopLastAction());
            if (isset($arr[$position])) {
                return $arr[$position];
            }
        }
        return -1;
    }

    function addForLoopLastAction($rgid, $position) {
        $current = trim($this->getForLoopLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getForLoopLastAction());
            $new = array();
            foreach ($arr as $a) {
                if ($a != -1) {
                    $new[] = $a;
                }
            }

            //if (!inArray($rgid, $new)) {
            $new[$position] = $rgid;
            if ($position > 0) {
                for ($i = 0; $i < $position; $i++) {
                    if (!isset($new[$i])) {
                        $new[$i] = $rgid;
                    }
                }
            }

            $this->setForLoopLastAction(implode("~", $new));
            //}
        } else {
            if ($position == 0) {
                $this->setForLoopLastAction($rgid);
            } else {

                /* add and make sure that we fill anything before in case this is a nested loop
                 * for which no previous action took place
                 */
                $new[$position] = $rgid;
                if ($position > 0) {
                    for ($i = 0; $i < $position; $i++) {
                        if (!isset($new[$i])) {
                            $new[$i] = $rgid;
                        }
                    }
                }
                $this->setForLoopLastAction(implode("~", $new));
            }
        }
    }

    function removeForLoopLastAction() {
        $current = trim($this->getForLoopLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getForLoopLastAction());
            array_pop($arr);
            $this->setForLoopLastAction(implode("~", $arr));
        } else {
            $this->setForLoopLastAction("");
        }
    }

    function resetForLoopLastAction() {
        $current = trim($this->getForLoopLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getForLoopLastAction());
            $arr[sizeof($arr) - 1] = -1;
            $this->setForLoopLastAction(implode("~", $arr));
        } else {
            $this->setForLoopLastAction(""); // no loops left
        }
    }

    function setForLoopLastAction($lastrgid) {
        $this->state->setForLoopLastAction($lastrgid);
    }

    /* WHILE FUNCTIONS */

    function doWhileLoop($whileactions, $condition, $whilergid, $nextrgid, $outerwhilergids, $exitwhile = 1) {
        if (!$condition || $whileactions == "") { //condition not met: get out of loop || no while actions: get out of loop
            $this->doAction($nextrgid);
            return;
        }
        if ($exitwhile == 2) { //exitwhile encountered: get out of loop
            $this->doAction($nextrgid);
            return;
        }

        $whileactions = explode("~", $whileactions);

        if ($outerwhilergids != "") {
            $position = sizeof(explode("~", $outerwhilergids));
        }
        // no outer whiles, then position is first one
        else {
            $position = 0;
        }

        $last = $this->getForLoopLastActionCurrentLoop($position);
        $this->setWhileRgid($whilergid);
        if ($last == -1) {
            $index = 0;
            $next = 0;
        } else {

            // last while action we did == last action in while,
            // then we completed a loop
            if ($last == end($whileactions)) {
                $this->completeWhile($whilergid);
                return;
            }
            // last for loop action was not the last action in for loop,
            // so we find the next action (if any)
            else {
                $index = array_search($last, $whileactions);
                $next = $index + 1;
            }
        }

        // action(s) left
        if (isset($whileactions[$next])) {

            /* do next action inside loop */
            $this->doAction($whileactions[$next]);

            /* stop */
            return;
        }
        // no action(s) left/found, then completed while
        else {
            $this->completeWhile($whilergid);
        }
    }

    function completeWhile($whilergid) {
        $this->resetWhileLastAction();
        $this->doAction($whilergid);
    }

    function doWhileLoopGroup($groupactions) {
        $grouplooparray = array();
        $groupactions = explode("~", $groupactions);
        foreach ($groupactions as $ga) {
            $action = $this->doAction($ga);
            if ($action != "") {
                $grouplooparray[] = $action;
            }
        }

        // return result
        return implode("~", $grouplooparray);
    }

    function getWhileLastAction() {
        return $this->state->getWhileLastAction();
    }

    function getWhileRgid() {
        return $this->state->getWhileRgid();
    }

    function setWhileRgid($rgid) {
        $this->state->setWhileRgid($rgid);
    }

    function getWhileString() {
        return $this->state->getWhileString();
    }

    function setWhileString($string) {
        $this->state->setWhileString($string);
    }

    function getWhileLastActionCurrentLoop($position) {
        $current = trim($this->getWhileLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getWhileLastAction());
            if (isset($arr[$position])) {
                return $arr[$position];
            }
        }
        return -1;
    }

    function addWhileLastAction($rgid, $position) {
        $current = trim($this->getWhileLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getWhileLastAction());
            $new = array();
            foreach ($arr as $a) {
                if ($a != -1) {
                    $new[] = $a;
                }
            }

            //if (!inArray($rgid, $new)) {
            $new[$position] = $rgid;
            if ($position > 0) {
                for ($i = 0; $i < $position; $i++) {
                    if (!isset($new[$i])) {
                        $new[$i] = $rgid;
                    }
                }
            }

            $this->setWhileLastAction(implode("~", $new));
            //}
        } else {
            if ($position == 0) {
                $this->setWhileLastAction($rgid);
            } else {

                /* add and make sure that we fill anything before in case this is a nested loop
                 * for which no previous action took place
                 */
                $new[$position] = $rgid;
                if ($position > 0) {
                    for ($i = 0; $i < $position; $i++) {
                        if (!isset($new[$i])) {
                            $new[$i] = $rgid;
                        }
                    }
                }
                $this->setWhileLastAction(implode("~", $new));
            }
        }
    }

    function removeWhileLastAction() {
        $current = trim($this->getWhileLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getWhileLastAction());
            array_pop($arr);
            $this->setWhileLastAction(implode("~", $arr));
        } else {
            $this->setWhileLastAction("");
        }
    }

    function resetWhileLastAction() {
        $current = trim($this->getWhileLastAction());
        if ($current != "") {
            $arr = explode("~", $this->getWhileLastAction());
            $arr[sizeof($arr) - 1] = -1;
            $this->setWhileLastAction(implode("~", $arr));
        } else {
            $this->setWhileLastAction(""); // no loops left
        }
    }

    function setWhileLastAction($lastrgid) {
        $this->state->setWhileLastAction($lastrgid);
    }

    function doForLoopGroup($min, $max, $counterfield, $groupactions, $looprgid, $nextrgid, $normalfor = 1) {
        //$this->currentloopcount = 0;
        if ($min == "" || $max == "") {
            return "";
        }
        $current = $this->getAnswer($counterfield);
        $groupactions = explode("~", $groupactions);
        $grouplooparray = array();

        // normal for (1 to 5)
        if ($normalfor == 1) {
            if ($min > $max) {
                return "";
            }
            for ($tempcount = $min; $tempcount <= $max; $tempcount++) {
                $this->addAssignment($counterfield, $tempcount, $looprgid);
                $this->setAnswer($counterfield, $tempcount);
                foreach ($groupactions as $ga) {
                    $action = $this->doAction($ga);
                    if ($action != "") {
                        $grouplooparray[] = $action;
                    }
                }
            }
        }
        // reverse for (5 to 1)
        else {
            if ($min < $max) {
                return "";
            }
            for ($tempcount = $min; $tempcount >= $max; $tempcount--) {
                $this->addAssignment($counterfield, $tempcount, $looprgid);
                $this->setAnswer($counterfield, $tempcount);
                foreach ($groupactions as $ga) {
                    $action = $this->doAction($ga);
                    if ($action != "") {
                        $grouplooparray[] = $action;
                    }
                }
            }
        }

        // return result
        return implode("~", $grouplooparray);
    }

    function doGroup($actions, $rgid, $template, $nextrgid) {
        $array = array();
        $actions = explode("~", $actions);
        foreach ($actions as $action) {
            $action = $this->doAction($action);
            if ($action != "") {
                $array[] = $action;
            }
        }

        // no actions
        if (sizeof($array) == 0) {
            $this->doAction($nextrgid);
            return;
        } else {
            $this->showQuestion(implode("~", $array), $rgid, $template);
        }
    }

    function doSubGroup($actions, $template) {
        $array = array();
        $actions = explode("~", $actions);
        foreach ($actions as $action) {
            $action = $this->doAction($action);
            if ($action != "") {
                $array[] = $action;
            }
        }

        /* construct string with entire subgroup statement including a number identifying the group */
        $number = $this->addSubDisplay(implode("~", $array), $template);
        $result = ROUTING_IDENTIFY_SUBGROUP . "_" . $number . "." . $template . "~" . implode("~", $array) . "~" . ROUTING_IDENTIFY_ENDSUBGROUP;
        return $result;
    }

    function doSection($prefix, $rgid, $seid, $mainrestart = false) {
        $this->setRgid($rgid);
        $this->setDisplayed("");
        $this->setTemplate("");
        //$this->setParentPrefix($this->getParentPrefix() . $this->getPrefix());         
        //$this->setPrefix($prefix); 

        /* save data record */
        $this->getDataRecord()->saveRecord();

        // add state entry, so we can use it later to move on once we return to this module
        if ($mainrestart == false) {
            $this->saveState(true);
        }

        /* clear */
        $this->datarecord = null;
        unset($this->datarecord);
        $this->clearContext();
        $stateid = $this->state->getStateId();

        /* start section */
        global $engine;
        $engine = loadEngine($this->getSuid(), $this->primkey, $this->phpid, $this->version, $seid, false, true);

        /* set state as current state */
        $engine->setState($this->state);
        $parentprefix = $this->getParentPrefix() . $this->getPrefix(); // get this before we override state properties below

        /* update state properties */
        $engine->setSeid($seid);
        $engine->setMainSeid($this->getMainSeid());
        $engine->setPrefix($prefix);
        $engine->setParentSeid($this->seid);
        $engine->setParentRgid($rgid);
        $engine->setParentPrefix($parentprefix); // everything we have so far
        $engine->setForward($this->getForward());
        $engine->setFlooding($this->getFlooding());
        $engine->startSection();
        if ($this->getFlooding()) {
            $this->stop = true;
            return;
        }
        doExit();
    }

    function addSubDisplay($variables, $template) {
        return $this->state->addSubDisplay($variables, $template);
    }

    function getSubDisplays() {
        return $this->state->getSubDisplays();
    }

    function setSubDisplays($subdisplays) {
        $this->state->setSubDisplays($subdisplays);
    }

    function getDisplayCounter() {
        return $this->state->getDisplayCounter();
    }

    function setDisplayCounter($dc) {
        $this->state->setDisplayCounter($dc);
    }

    function determineDisplayNumbers($realvariables) {
        $dn = array();
        $real = explode("~", $realvariables);
        foreach ($real as $variable) {
            $variable = str_replace(" ", "", $variable);
            $cnt = $this->getDisplayCounter();
            $dn[strtoupper($variable)] = $cnt;
        }
        $this->state->setDisplayNumbers($dn);
    }

    function getDisplayNumbers() {
        return $this->state->getDisplayNumbers();
    }

    function isLocked() {
        if (Config::useLocking() == false) {
            return false;
        }

        if (isset($this->locked)) {
            return $this->locked;
        }
        $this->locked = false;
        $this->firsttimelock = true;
        global $db;
        $query = "select status from " . Config::dbSurveyData() . "_interviewstatus where suid = " . prepareDatabaseString($this->getSuid()) . " and primkey='" . prepareDatabaseString($this->getPrimaryKey()) . "'";
        $res = $db->selectQuery($query);
        if ($res) {

            // no entry yet, so first time, so currently unlocked
            if ($db->getNumberOfRows($res) == 0) {
                $this->locked = false;
                $this->firsttimelock = true;
            } else {
                $this->firsttimelock = false;
                $row = $db->getRow($res);
                if ($row["status"] == INTERVIEW_LOCKED) {
                    $this->locked = true;
                } else {
                    $this->locked = false;
                }
            }
        }
        // the query to the db failed, so locked???
        else {
            $this->firsttimelock = true;
            $this->locked = false;
        }

        // return result
        return $this->locked;
    }

    function lock() {
        if (Config::useLocking() == false) {
            return;
        }
        global $db;
        if ($this->firsttimelock == true) {
            $this->firsttimelock = false;
            $query = "insert into " . Config::dbSurveyData() . "_interviewstatus (suid, primkey, mainseid, status) values(" . prepareDatabaseString($this->getSuid()) . ",'" . prepareDatabaseString($this->getPrimaryKey()) . "', " . prepareDatabaseString($this->getMainSeid()) . "," . prepareDatabaseString(INTERVIEW_LOCKED) . ")";
        } else {
            $query = "update " . Config::dbSurveyData() . "_interviewstatus set status=" . prepareDatabaseString(INTERVIEW_LOCKED) . " WHERE suid = " . prepareDatabaseString($this->getSuid()) . " and primkey = '" . prepareDatabaseString($this->getPrimaryKey()) . "' and mainseid=" . prepareDatabaseString($this->getMainSeid()) . " LIMIT 1";
        }
        $db->executeQuery($query);
    }

    function unlock() {
        $_SESSION['REQUEST_IN_PROGRESS'] = null;
        unset($_SESSION['REQUEST_IN_PROGRESS']);
        if (Config::useLocking() == false) {
            return;
        }
        global $db;
        if ($this->firsttimelock == true) {
            $this->firsttimelock = false;
            $query = "insert into " . Config::dbSurveyData() . "_interviewstatus (suid, primkey, mainseid, status) values(" . prepareDatabaseString($this->getSuid()) . ",'" . prepareDatabaseString($this->getPrimaryKey()) . "', " . prepareDatabaseString($this->getMainSeid()) . "," . prepareDatabaseString(INTERVIEW_UNLOCKED) . ")";
        } else {
            $query = "update " . Config::dbSurveyData() . "_interviewstatus set status=" . prepareDatabaseString(INTERVIEW_UNLOCKED) . " WHERE suid = " . prepareDatabaseString($this->getSuid()) . " and primkey = '" . prepareDatabaseString($this->getPrimaryKey()) . "' and mainseid=" . prepareDatabaseString($this->getMainSeid()) . " LIMIT 1";
        }

        $db->executeQuery($query);
    }

    function startSection() {

        // returning to the survey/section
        if ($this->loadLastSectionState() == true) {

            /* show last questions */
            $this->showQuestion($this->getDisplayed(), $this->getRgid(), $this->getTemplate());

            /* stop */
            return;
        }
        // starting with the survey/section
        else {

            /* do first action */
            $this->doAction($this->getFirstAction());

            /* save data record */
            $this->getDataRecord()->saveRecord();

            /* we finished everything and are showing a question if 
             * all went well so this is the moment to save the state
             */
            $this->saveState(true);

            if ($this->getFlooding()) {
                if ($this->stop != true) {
                    $this->doFakeSubmit($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                    $this->getNextQuestion();
                }
                $this->stop = true;
                return;
            }

            /* stop */
            return;
        }
    }

    function endSection() {

        global $db;
        /* load last section state */
        $previoussectionstate = $this->loadPreviousSectionEntryState();

        if ($previoussectionstate != null) {
            $fromrgid = $previoussectionstate->getRgid();
            $seid = $previoussectionstate->getSeid();
            $mainseid = $previoussectionstate->getMainSeid();
            $parentseid = $previoussectionstate->getParentSeid();
            $parentrgid = $previoussectionstate->getParentRgid();
            $prefix = $previoussectionstate->getPrefix();
            $parentprefix = $previoussectionstate->getParentprefix();

            /* find out where to go after section call */
            $torgid = 0;
            $result = $db->selectQuery('select torgid from ' . Config::dbSurvey() . '_next where suid=' . prepareDatabaseString($this->getSuid()) . ' and seid=' . prepareDatabaseString($this->seid) . ' and fromrgid = ' . prepareDatabaseString($fromrgid));

            // no entry, then this is the end
            if ($db->getNumberOfRows($result) == 0) {
                $torgid = '0';
            }
            // entry, so get where need to go
            else {
                $row = $db->getRow($result);
                $torgid = $row["torgid"];
            }

            /* update section info from previous section state in this section */
            $this->setSeid($seid);
            $this->setMainSeid($mainseid);
            $this->setParentSeid($parentseid);
            $this->setPrefix($prefix);
            $this->setParentPrefix($parentprefix);
            $this->setParentRgid($parentrgid);

            /* check if we are going back to a loop */
            $query = "select primkey from " . Config::dbSurveyData() . "_loopdata where suid=" . prepareDatabaseString($this->getSuid()) . " and primkey='" . prepareDatabaseString($this->getPrimaryKey()) . "' and mainseid=" . prepareDatabaseString($mainseid) . " and seid=" . prepareDatabaseString($seid) . " and looprgid=" . prepareDatabaseString($torgid);
            $result = $db->selectQuery($query);
            if ($db->getNumberOfRows($result) > 0) {
                $this->reset[$torgid] = false; // so we don't reset the loop counter when going back to the section loop
            }

            /* do action */
            $this->doAction($torgid);

            /* NOTE: we only get below if all the actions are assignments, i.e. no questions are asked 
             * OR we are doing data flooding so we never show a screen and keep on going
             */

            if ($this->getFlooding()) {
                if ($this->stop != true) {
                    $this->getDataRecord()->saveRecord();
                    $this->saveState(true);
                    $this->doFakeSubmit($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                    $this->getNextQuestion();
                }
            } else {
                /* save data record */
                $this->getDataRecord()->saveRecord();

                /* we finished everything and are showing a question if 
                 * all went well so this is the moment to save the state
                 */
                $this->saveState(true);
            }

            if ($this->getFlooding()) {
                $this->stop = true;
                return;
            }
            doExit();
        }
    }

    function isOldFormSubmit() {

        /* if data flooder, then we skip this */
        if ($this->getFlooding() == true) {
            return false;
        }

        $currentlast = $this->getLastSurveyAction();

        // no entries yet or something went wrong, then we are starting survey
        if ($currentlast <= 0) {
            //return true;
        }

        // get last action in form (is empty if we just started)
        $lastform = getFromSessionParams(SESSION_PARAM_LASTACTION);
        if ($lastform != "") {
            if ($lastform != $currentlast) { // submitted action is not the last action in the _actions table, then this is an old form!
                return true;
            }
        }
        // lastform can be empty with F5 resubmit, leading to multiple scripts running at the same time
        else {
            if ($this->firstform == false) {
                return true;
            }
        }

        // we are fine
        return false;
    }

    // indicates if the action is a reload of the screen
    function getReloadScreen() {
        return $this->reloadscreen;
    }

    /*
     * 
     */

    function getNextQuestion() {
        global $db;

        /* get language */
        $language = getSurveyLanguage();

        // include language
        if (file_exists("language/language" . getSurveyLanguagePostFix($language) . ".php")) {
            require_once('language' . getSurveyLanguagePostFix($language) . '.php'); // language  
        } else {
            require_once('language_en.php'); // fall back on english language  file
        }

        // check if session request already in progress
        if ((isset($_SESSION['PREVIOUS_REQUEST_IN_PROGRESS']) && $_SESSION['PREVIOUS_REQUEST_IN_PROGRESS'] == 1)) {
            if (Config::useTransactions() == true) {
                doCommit();
            }
            echo $this->display->showInProgressSurvey();
            doExit();
        }
        // check if database locked
        else if ($this->isLocked()) {
            if (Config::useTransactions() == true) {
                doCommit();
            }
            echo $this->display->showLockedSurvey();
            doExit();
        }

        // lock (we unlock in showQuestion OR doEnd if end of survey
        $this->lock();

        // we are starting/returning to the survey/section OR submit of old form
        $oldform = $this->isOldFormSubmit();
        if (getFromSessionParams(SESSION_PARAM_RGID) == '' || $oldform) {

            // returning to the survey
            if ($this->getDisplayed() != "") {

                // set indicator
                $this->reloadscreen = true;

                // completed interview
                if ($this->getDataRecord()->isCompleted()) {

                    /* see what to do on reentry after completion
                     * based on settings of last displayed variable/group
                     */
                    $reentry = AFTER_COMPLETION_NO_REENTRY;
                    $reentry_preload = PRELOAD_REDO_NO;
                    $groupname = $this->getTemplate();
                    $rgid = $this->getRgid();
                    $variablenames = $this->getDisplayed();
                    if ($groupname != "") {
                        $group = $this->getGroup($groupname);
                        $reentry = $group->getAccessReturnAfterCompletionAction();
                        $reentry_preload = $group->getAccessReturnAfterCompletionRedoPreload();
                    } else {
                        $variables = explode("~", $variablenames);
                        $realvariables = explode("~", $this->display->getRealVariables($variables));
                        if (sizeof($realvariables) > 0) {
                            $var = $this->getVariableDescriptive($realvariables[0]);
                            $reentry = $var->getAccessReturnAfterCompletionAction();
                            $reentry_preload = $var->getAccessReturnAfterCompletionRedoPreload();
                        }
                    }

                    if ($reentry == AFTER_COMPLETION_NO_REENTRY) {
                        $this->unlock();
                        if (Config::useTransactions() == true) {
                            doCommit();
                        }
                        echo $this->display->showCompletedSurvey();
                        doExit();
                    }
                    // allow re-entry
                    else {

                        // set current action to reentry
                        $this->currentaction = ACTION_SURVEY_REENTRY;

                        /* update language, mode and version */
                        $this->setAnswer(VARIABLE_LANGUAGE, $language);
                        $this->setAnswer(VARIABLE_VERSION, getSurveyVersion());
                        $this->setAnswer(VARIABLE_MODE, getSurveyMode());
                        $this->setAnswer(VARIABLE_PLATFORM, $_SERVER['HTTP_USER_AGENT']);
                        $this->setAnswer(VARIABLE_EXECUTION_MODE, getSurveyExecutionMode());
                        $this->setAnswer(VARIABLE_TEMPLATE, getSurveyTemplate());
                        $this->setAnswer(VARIABLE_END, null);

                        /* set interview data as incompleted */
                        $this->getDataRecord()->setToIncomplete();

                        // redoing preloads
                        if ($reentry_preload == PRELOAD_REDO_YES) {
                            $pd = loadvarSurvey('pd');
                            if ($pd != '') {
                                getSessionParamsPost(loadvarSurvey('pd'), 'PD');
                                foreach ($_SESSION['PD'] as $field => $answer) {
                                    $this->setAnswer($field, $answer);
                                }
                            }
                        }

                        // where are we entering
                        $where = $reentry; //$this->survey->getAccessReturnAfterCompletionAction();
                        // show first question(s) of survey
                        if ($where == AFTER_COMPLETION_FIRST_SCREEN) {

                            /* save data record */
                            $this->getDataRecord()->saveRecord();

                            // get data of current state, which is the last one (with updated preloads if we re-did the preloads)
                            $data = $this->state->getAllData();

                            // remove all states except first one with displayed != "" and anything before that state
                            $this->removeAllStatesExceptFirst();

                            // load the first state
                            $this->loadLastState();

                            // set data from last state to first state
                            $this->state->setAllData($data);
                            //unset($data);
                            // save updated state
                            $this->saveState(false);

                            /* if (language different from state AND not using last known language) OR (mode different from state AND not using last known language) OR (version different from state), then wipe fill texts */
                            if (($this->state->getLanguage() != getSurveyLanguage() && $this->survey->getReentryLanguage(getSurveyMode()) == LANGUAGE_REENTRY_NO) || ($this->state->getMode() != getSurveyMode() && $this->survey->getReentryMode() == MODE_REENTRY_NO) || $this->state->getVersion() != getSurveyVersion()) {
                                $this->setFillTexts(array());

                                /* indicate to redo any fills */
                                $this->setRedoFills(true);
                            }

                            // show question(s)
                            $groupname = $this->getTemplate();
                            $rgid = $this->getRgid();
                            $variablenames = $this->getDisplayed();
                            $this->showQuestion($variablenames, $rgid, $groupname);
                            doExit();
                        }
                        // start from the beginning (includes doing any assignments again)
                        else if ($where == AFTER_COMPLETION_FROM_START) {

                            // get data of current state, which is the last one (with updated preloads if we re-did the preloads)
                            $data = $this->state->getAllData();

                            // remove all states
                            $this->removeAllStates();

                            // initialize new state
                            $this->reinitializeState();

                            // set data
                            $this->state->setAllData($data);

                            // start main section
                            $this->doSection("", 0, $this->getMainSeid(), true);

                            /* stop */
                            doExit();
                        }
                        // show last question(s) of survey
                        else if (inArray($where, array(AFTER_COMPLETION_LAST_SCREEN, AFTER_COMPLETION_LAST_SCREEN_REDO))) {

                            /* if (language different from state AND not using last known language) OR (mode different from state AND not using last known language) OR (version different from state), then wipe fill texts */
                            if (($this->state->getLanguage() != getSurveyLanguage() && $this->survey->getReentryLanguage(getSurveyMode()) == LANGUAGE_REENTRY_NO) || ($this->state->getMode() != getSurveyMode() && $this->survey->getReentryMode() == MODE_REENTRY_NO) || $this->state->getVersion() != getSurveyVersion()) {
                                $this->setFillTexts(array());

                                /* indicate to redo any fills */
                                $this->setRedoFills(true);
                            }

                            $groupname = $this->getTemplate();
                            $rgid = $this->getRgid();
                            $variablenames = $this->getDisplayed();

                            // not redoing anything
                            if ($where == AFTER_COMPLETION_LAST_SCREEN) {
                                $this->showQuestion($variablenames, $rgid, $groupname);
                                doExit();
                            }
                            // redo last action (e.g. to perform assignment or re-evaluate if condition
                            else {

                                // in group, then we redo
                                if ($groupname != "") {

                                    /* indicate update action */
                                    $this->updateaction = true;

                                    /* clear inline fields and sub displays */
                                    $this->setInlineFields(array());
                                    $this->setSubDisplays(array());

                                    /* remove assignments within action */
                                    $this->removeAssignmentsAfterRgid($rgid);

                                    /* re-do action */
                                    $this->doAction($rgid);

                                    /* save data record */
                                    $this->getDataRecord()->saveRecord();

                                    /* we finished everything and are showing a question if all went well 
                                     * so this is the moment to update the state
                                     */
                                    $this->saveState(false);

                                    /* stop */
                                    return;
                                } else {
                                    $this->showQuestion($variablenames, $rgid, $groupname);
                                }
                            }
                            doExit();
                        }
                    }
                }
                // reentry non-completed interview
                else {
                    /* see what to do on reentry
                     * based on settings of last 
                     * displayed variable/group
                     */
                    $action = REENTRY_SAME_SCREEN;
                    $reentry_preload = PRELOAD_REDO_NO;
                    $groupname = $this->getTemplate();
                    $rgid = $this->getRgid();
                    $variablenames = $this->getDisplayed();
                    if ($groupname != "") {
                        $group = $this->getGroup($groupname);
                        $action = $group->getAccessReentryAction();
                        $reentry_preload = $group->getAccessReentryRedoPreload();
                    } else {
                        $variables = explode("~", $variablenames);
                        $realvariables = explode("~", $this->display->getRealVariables($variables));
                        if (sizeof($realvariables) > 0) {
                            $var = $this->getVariableDescriptive($realvariables[0]);
                            $action = $var->getAccessReentryAction();
                            $reentry_preload = $var->getAccessReentryRedoPreload();
                        }
                    }

                    /* no re-entry allowed */
                    if ($action == REENTRY_NO_REENTRY) {
                        $this->unlock();
                        if (Config::useTransactions() == true) {
                            doCommit();
                        }
                        echo $this->display->showCompletedSurvey();
                        doExit();
                    } else {

                        // set current action to reentry
                        $this->currentaction = ACTION_SURVEY_RETURN;

                        /* update language, mode and version */
                        $this->setAnswer(VARIABLE_LANGUAGE, $language);
                        $this->setAnswer(VARIABLE_VERSION, getSurveyVersion());
                        $this->setAnswer(VARIABLE_MODE, getSurveyMode());
                        $this->setAnswer(VARIABLE_PLATFORM, $_SERVER['HTTP_USER_AGENT']);
                        $this->setAnswer(VARIABLE_EXECUTION_MODE, getSurveyExecutionMode());
                        $this->setAnswer(VARIABLE_TEMPLATE, getSurveyTemplate());

                        // redoing preloads
                        if ($reentry_preload == PRELOAD_REDO_YES) {
                            $pd = loadvarSurvey('pd');
                            if ($pd != '') {
                                getSessionParamsPost(loadvarSurvey('pd'), 'PD');
                                foreach ($_SESSION['PD'] as $field => $answer) {
                                    $this->setAnswer($field, $answer);
                                }
                            }
                        }


                        // show first question(s) of survey
                        if ($action == REENTRY_FIRST_SCREEN) {

                            /* save data record */
                            $this->getDataRecord()->saveRecord();

                            // get data of current state, which is the last one (with updated preloads if we re-did the preloads)
                            $data = $this->state->getAllData();

                            // remove all states except first one with displayed != "" and anything before that state
                            $this->removeAllStatesExceptFirst();

                            // load the first state
                            $this->loadLastState();

                            // set data from last state to first state
                            $this->state->setAllData($data);
                            //unset($data);
                            // save updated state
                            $this->saveState(false);

                            /* if (language different from state AND not using last known language) OR (mode different from state AND not using last known language) OR (version different from state), then wipe fill texts */
                            if (($this->state->getLanguage() != getSurveyLanguage() && $this->survey->getReentryLanguage(getSurveyMode()) == LANGUAGE_REENTRY_NO) || ($this->state->getMode() != getSurveyMode() && $this->survey->getReentryMode() == MODE_REENTRY_NO) || $this->state->getVersion() != getSurveyVersion()) {
                                $this->setFillTexts(array());

                                /* indicate to redo any fills */
                                $this->setRedoFills(true);
                            }

                            // show question(s)
                            $groupname = $this->getTemplate();
                            $rgid = $this->getRgid();
                            $variablenames = $this->getDisplayed();
                            $this->showQuestion($variablenames, $rgid, $groupname);
                            doExit();
                        }
                        // start from the beginning (includes doing any assignments again)
                        else if ($action == REENTRY_FROM_START) {

                            // get data of current state, which is the last one (with updated preloads if we re-did the preloads)
                            $data = $this->state->getAllData();

                            // remove all states
                            $this->removeAllStates();

                            // initialize new state
                            $this->reinitializeState();

                            // set data
                            $this->state->setAllData($data);

                            // start main section
                            $this->doSection("", 0, $this->getMainSeid(), true);

                            /* stop */
                            doExit();
                        }
                        // show last question(s)
                        else if (inArray($action, array(REENTRY_SAME_SCREEN, REENTRY_SAME_SCREEN_REDO_ACTION))) {

                            /* if (language different from state AND not using last known language) OR (mode different from state AND not using last known language) OR (version different from state), then wipe fill texts */
                            if (($this->state->getLanguage() != getSurveyLanguage() && $this->survey->getReentryLanguage(getSurveyMode()) == LANGUAGE_REENTRY_NO) || ($this->state->getMode() != getSurveyMode() && $this->survey->getReentryMode() == MODE_REENTRY_NO) || $this->state->getVersion() != getSurveyVersion()) {
                                $this->setFillTexts(array());

                                /* indicate to redo any fills */
                                $this->setRedoFills(true);
                            }

                            // not redoing anything
                            if ($action == REENTRY_SAME_SCREEN) {
                                $this->showQuestion($variablenames, $rgid, $groupname);
                                doExit();
                            }
                            // redo last action (e.g. to perform assignment or re-evaluate if condition
                            else {

                                // in group, then we redo
                                if ($groupname != "") {

                                    /* indicate update action */
                                    $this->updateaction = true;

                                    /* clear inline fields and sub displays */
                                    $this->setInlineFields(array());
                                    $this->setSubDisplays(array());

                                    /* clear any assignments part of the action */
                                    $this->removeAssignmentsAfterRgid($rgid);

                                    /* re-do action */
                                    $this->doAction($rgid);

                                    /* save data record */
                                    $this->getDataRecord()->saveRecord();

                                    /* we finished everything and are showing a question if all went well 
                                     * so this is the moment to update the state
                                     */
                                    $this->saveState(false);

                                    /* stop */
                                    return;
                                } else {
                                    $this->showQuestion($variablenames, $rgid, $groupname);
                                    doExit();
                                }
                            }
                        }
                        // show question(s) after the last question(s)
                        else {
                            $torgid = 0;
                            $result = $db->selectQuery('select torgid from ' . Config::dbSurvey() . '_next where suid=' . prepareDatabaseString($this->getSuid()) . ' and seid=' . prepareDatabaseString($this->seid) . ' and fromrgid = ' . prepareDatabaseString($rgid));
                            if ($row = $db->getRow($result)) {
                                $torgid = $row["torgid"];
                            }

                            /* indicate we are going forward */
                            $this->setForward(true);

                            /* log action */
                            $this->currentaction = ACTION_EXIT_NEXT;
                            $this->logAction($rgid, ACTION_EXIT_NEXT);

                            // action to do
                            if ($torgid > 0) {

                                /* reset any assignments */
                                $this->setAssignments(array());

                                /* reset inline fields */
                                $this->setInlineFields(array());

                                /* reset sub displays */
                                $this->setSubDisplays(array());

                                /* reset fill texts */
                                $this->setFillTexts(array());

                                /* do action */
                                $this->doAction($torgid);

                                /* we finished everything and are showing a question if 
                                 * all went well so this is the moment to save the state
                                 */
                                if ($this->endofsurvey == false) {

                                    /* save data record */
                                    $this->getDataRecord()->saveRecord();
                                    $this->saveState();
                                }
                            }
                            // we are at the end after having a last action on the base module
                            else {

                                /* do end */
                                $this->doEnd(true);
                            }
                        }
                    }

                    /* stop */
                    doExit();
                }
            }
            // starting with the survey
            else {

                // if already data, then something is wrong, because on re-entry the state table should have as last entry a "displayed" value!
                if ($this->alreadyStarted()) {
                    if (Config::useTransactions() == true) {
                        doCommit();
                    }
                    echo $this->display->showLockedSurvey();
                    doExit();
                }
                $this->currentaction = ACTION_SURVEY_ENTRY;

                /* store basic fields */
                $this->setAnswer(VARIABLE_PRIMKEY, $this->primkey);
                $this->setAnswer(VARIABLE_BEGIN, date("Y-m-d H:i:s", time()));
                $this->setAnswer(VARIABLE_LANGUAGE, $language);
                $this->setAnswer(VARIABLE_VERSION, getSurveyVersion());
                $this->setAnswer(VARIABLE_MODE, getSurveyMode());
                $this->setAnswer(VARIABLE_PLATFORM, $_SERVER['HTTP_USER_AGENT']);
                $this->setAnswer(VARIABLE_EXECUTION_MODE, getSurveyExecutionMode());
                $this->setAnswer(VARIABLE_TEMPLATE, getSurveyTemplate());

                /* preload */
                $pd = loadvarSurvey('pd');
                if ($pd != '') {
                    getSessionParamsPost(loadvarSurvey('pd'), 'PD');
                    foreach ($_SESSION['PD'] as $field => $answer) {
                        $this->setAnswer($field, $answer);
                    }
                }

                /* save data record */
                $this->getDataRecord()->saveRecord();

                /* do first action */
                $this->doAction($this->getFirstAction());

                /* we finished everything and are showing a question if 
                 * went well so this is the moment to save the state
                 */
                $this->saveState();

                if ($this->getFlooding()) {
                    if ($this->stop != true) {
                        $this->doFakeSubmit($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                        $this->getNextQuestion();
                    }
                }

                /* stop */
                $this->stop = true;
                return;
            }
        }
        // we are in the survey
        else {

            /* get the rgid */
            $lastrgid = getFromSessionParams(SESSION_PARAM_RGID);

            /* check if rgid matches the one from the state AND no posted navigation
             * if not, then this is a browser resubmit
             */
            if ($lastrgid != $this->getPreviousRgid() && !isset($_POST['navigation'])) {

                /* show last question(s) and stop */
                $this->showQuestion($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                doExit();
            }

            /* handle timings */
            $this->addTimings($lastrgid, $this->getStateId());

            /* get query display object for button labels */
            $vars = splitString("/~/", getFromSessionParams(SESSION_PARAM_VARIABLES));

            /* check for external storage only variables */
            $this->externalonly = array();
            $cnt = 0;
            foreach ($vars as $t) {

                $cnt++;

                // if core variable, then always store internal
                if (inArray($t, Common::surveyCoreVariables())) {
                    continue;
                }
                $vr = $this->getVariableDescriptive($t);
                if ($vr->getVsid() != "") {
                    if ($vr->getStoreLocation() == STORE_LOCATION_EXTERNAL) {
                        $this->externalonly[getBasicName($t)] = "answer" . $cnt;
                    }
                }
                unset($vr); // clean up
            }

            $dkrfnacheck = false;
            $queryobject = null;
            $screendumps = false;
            $paradata = false;
            if (sizeof($vars) == 1) {
                $var = $this->getVariableDescriptive($vars[0]);
                $queryobject = $var;
                $backlabel = $var->getLabelBackButton();
                $updatelabel = $var->getLabelUpdateButton();
                $nextlabel = $var->getLabelNextButton();
                $dklabel = $var->getLabelDKButton();
                $rflabel = $var->getLabelRFButton();
                $nalabel = $var->getLabelNAButton();
                $remarks = $var->getShowRemarkButton();
                $dkrfnacheck = $var->isIndividualDKRFNA();
                $screendumps = $var->isScreendumpStorage();
                $paradata = $var->isParadata();
            }
            // group
            else {
                $group = $this->getGroup(getFromSessionParams(SESSION_PARAM_GROUP));
                $queryobject = $group;
                $backlabel = $group->getLabelBackButton();
                $updatelabel = $group->getLabelUpdateButton();
                $nextlabel = $group->getLabelNextButton();
                $dklabel = $group->getLabelDKButton();
                $rflabel = $group->getLabelRFButton();
                $nalabel = $group->getLabelNAButton();
                $remarks = $group->getShowRemarkButton();
                $dkrfnacheck = $group->isIndividualDKRFNA();
                $screendumps = $group->isScreendumpStorage();
                $paradata = $group->isParadata();
            }

            /* handle screenshot (ignore if external storage only variables */
            if ($screendumps == true && sizeof($this->externalonly) == 0) {
                $this->addScreenshot();
            }

            /* handle paradata */
            if ($paradata == true) {
                $this->addParadata($lastrgid);
            }

            /* handle action */

            // back
            if (isset($_POST['navigation']) && $_POST['navigation'] == $backlabel) {

                $this->currentaction = ACTION_EXIT_BACK;

                /* update remark status from clean to dirty */
                if ($remarks == BUTTON_YES && loadvarSurvey(POST_PARAM_REMARK_INDICATOR) == 1) {
                    $this->updateRemarkStatus(DATA_DIRTY);
                }

                $this->doBackState($lastrgid, $dkrfnacheck);
                $cnt = 0;
                $currentseid = $this->getSeid();

                // this was a section call, so we need to go back one more state
                while ($this->getDisplayed() == "") {
                    $this->setSeid($this->getParentSeid());
                    $this->setPrefix($this->getParentPrefix());
                    $this->doBackState($this->getRgid(), $dkrfnacheck, false); // dont save answers again!                   
                    $cnt++;
                    if ($cnt > 100) {
                        break;
                    }
                }

                /* if (language different from state AND update) OR (mode different from state AND update) OR (version different from state), then wipe fill texts */
                $redo = false;
                $langback = $this->survey->getBackLanguage(getSurveyMode());
                $modeback = $this->survey->getBackMode();
                $statlang = $this->state->getLanguage();
                $statmode = $this->state->getMode();
                $statver = $this->state->getVersion();
                if (($statlang != getSurveyLanguage() && $langback == LANGUAGE_BACK_YES) || ($statmode != getSurveyMode() && $modeback == MODE_BACK_YES) || $statver != getSurveyVersion()) {
                    $this->setFillTexts(array());

                    /* indicate to redo any fills */
                    $this->setRedoFills(true);
                    $redo = true;
                }

                /* if language different, but keeping from state, then update language */
                if (($statlang != getSurveyLanguage() && $langback != LANGUAGE_BACK_YES)) {
                    setSurveyLanguage($statlang);
                }

                if (($statmode != getSurveyMode() && $modeback != MODE_BACK_YES)) {
                    setSurveyMode($statmode);
                }
                if ($statver != getSurveyVersion()) {
                    setSurveyVersion($statver);
                }

                /* check for on submit function */
                $onsubmit = $queryobject->getOnBack();
                $tocall = $this->replaceFills($onsubmit);
                $parametersout = array();
                $removed = array();
                $test = excludeText($tocall, $removed);
                if (stripos($test, '(') !== false) {
                    $parameters = rtrim(substr($tocall, stripos($test, '(') + 1), ')');
                    $parameters = preg_split("/[\s,]+/", $parameters);
                    foreach ($parameters as $p) {
                        $removed = array();
                        $pt = excludeText($p, $removed);
                        if (stripos($pt, '(') === false) { // no function calls as parameters
                            $parametersout[] = (string) $p;
                        }
                    }

                    $tocall = substr($tocall, 0, stripos($tocall, '('));
                }

                if (function_exists($tocall)) {
                    if (inArray($tocall, getAllowedOnChangeFunctions()) && !inArray($tocall, getForbiddenOnChangeFunctions())) {
                        try {
                            $f = new ReflectionFunction($tocall);
                            $returnStr .= $f->invoke($parametersout);
                        } catch (Exception $e) {
                            
                        }
                    }
                }

                /* no need to reset inline fields array in state --> they are based on the routing
                 * if we went back after a language change, then any routing related change resulting from
                 * that are not effectuated until after going forward again.
                 */

                /* show previous question(s) from the stored state */
                if ($this->getRgid() != "") {

                    // no language/mode/version change, so no need to redo anything
                    if ($redo == false) {
                        $this->showQuestion($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                    }
                    // we need to redo in case of fills since we changed language/mode/version
                    else {

                        /* we have a rgid */
                        if ($this->getRgid() > 0) {
                            //$this->updateaction = true;  
                            // we are going back to different section, so we need to load another engine
                            if ($currentseid != $this->getSeid()) {
                                global $engine;
                                $engine = loadEngine($this->getSuid(), $this->primkey, $this->phpid, $this->version, $this->getSeid(), false, true);

                                /* set state as current state */
                                $engine->setState($this->state);

                                /* update state properties */
                                $engine->setSeid($this->getSeid());
                                $engine->setMainSeid($this->getMainSeid());
                                $engine->setPrefix($this->getPrefix());
                                $engine->setParentSeid($this->getParentSeid());
                                $engine->setParentRgid($this->getParentRgid());
                                $engine->setParentPrefix($this->getParentPrefix());
                                $engine->setForward($this->getForward());
                                $engine->setFlooding($this->getFlooding());

                                // do the action in the correct engine
                                $engine->doAction($this->getRgid());

                                // stop
                                return;
                            }
                            // we are still in the same section, so we can redo the action using the current engine
                            else {

                                $this->doAction($this->getRgid());
                                /* we finished everything and are showing a question if all went well 
                                 * so this is the moment to update the state
                                 */
                                $this->saveState(false);
                            }
                        } else {
                            $this->showQuestion($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                        }
                    }
                } else {
                    // this should not happen
                    $this->showQuestion(VARIABLE_INTRODUCTION, "");
                }

                /* save data record */
                $this->getDataRecord()->saveRecord();

                /* stop */
                return;
            }
            // update OR language change OR mode change or programmatic update
            else if (isset($_POST['navigation']) && ($_POST['navigation'] == $updatelabel || $_POST['navigation'] == NAVIGATION_LANGUAGE_CHANGE || $_POST['navigation'] == NAVIGATION_MODE_CHANGE || $_POST['navigation'] == PROGRAMMATIC_UPDATE)) {

                $torgid = getFromSessionParams(SESSION_PARAM_RGID);

                /* log action */
                if ($_POST['navigation'] == $updatelabel) {
                    $this->currentaction = ACTION_EXIT_UPDATE;
                    $this->logAction($lastrgid, ACTION_EXIT_UPDATE);
                } else if ($_POST['navigation'] == NAVIGATION_LANGUAGE_CHANGE) {
                    $this->currentaction = ACTION_EXIT_LANGUAGE_CHANGE;
                    $this->logAction($lastrgid, ACTION_EXIT_LANGUAGE_CHANGE);
                    $this->setAnswer(VARIABLE_LANGUAGE, getSurveyLanguage());
                } else if ($_POST['navigation'] == NAVIGATION_MODE_CHANGE) {
                    $this->currentaction = ACTION_EXIT_MODE_CHANGE;
                    $this->logAction($lastrgid, ACTION_EXIT_MODE_CHANGE);
                    $this->setAnswer(VARIABLE_MODE, getSurveyMode());
                } else if ($_POST['navigation'] == NAVIGATION_VERSION_CHANGE) {
                    $this->currentaction = ACTION_EXIT_VERSION_CHANGE;
                    $this->logAction($lastrgid, ACTION_EXIT_VERSION_CHANGE);
                    $this->setAnswer(VARIABLE_VERSION, getSurveyVersion());
                } else if ($_POST['navigation'] == PROGRAMMATIC_UPDATE) {
                    $this->currentaction = ACTION_EXIT_PROGRAMMATIC_UPDATE;
                    $this->logAction($lastrgid, ACTION_EXIT_PROGRAMMATIC_UPDATE);
                }

                /* store answers in db and previous state */
                $cnt = 1;
                foreach ($vars as $var) {
                    $vd = $this->getVariableDescriptive($var);
                    if ($vd->getAnswerType() == ANSWER_TYPE_SETOFENUMERATED || $vd->getAnswerType() == ANSWER_TYPE_MULTIDROPDOWN) {
                        $answer = "";
                        if ($dkrfnacheck == true) { /* dk/rf/na */
                            $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt . "_dkrfna");
                            if (!inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                            }
                        } else {
                            $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                        }

                        if (is_array($answer)) {
                            $answer = implode(SEPARATOR_SETOFENUMERATED, $answer);
                        }
                        $this->setAnswer($var, $answer, DATA_DIRTY);
                    } else {
                        if ($vd->getAnswerType() != ANSWER_TYPE_NONE) {
                            $answer = "";
                            if ($dkrfnacheck == true) { /* dk/rf/na */
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt . "_dkrfna");
                                if (!inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                                    $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                                }
                            } else {
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                            }
                            $this->setAnswer($var, $answer, DATA_DIRTY);
                        }
                    }
                    $cnt++;
                }

                /* if update button OR language OR mode OR version now different from state, then wipe fill texts */
                if ($this->currentaction == ACTION_EXIT_UPDATE || ($_POST['navigation'] == NAVIGATION_LANGUAGE_CHANGE && $this->state->getLanguage() != getSurveyLanguage()) || ($_POST['navigation'] == NAVIGATION_MODE_CHANGE && $this->state->getMode() != getSurveyMode()) || ($_POST['navigation'] == NAVIGATION_VERSION_CHANGE && $this->state->getVersion() != getSurveyVersion())) {
                    $this->setFillTexts(array());

                    /* indicate to redo any fills */
                    $this->setRedoFills(true);
                }

                /* indicate update action */
                $this->updateaction = true;

                /* clear inline fields and sub displays */
                $this->setInlineFields(array());
                $this->setSubDisplays(array());

                /* update remark status to clean */
                if ($remarks == BUTTON_YES && loadvarSurvey(POST_PARAM_REMARK_INDICATOR) == 1) {
                    $this->updateRemarkStatus(DATA_DIRTY);
                }

                /* check for on submit function */
                $onsubmit = "";
                if ($_POST['navigation'] == $updatelabel) {
                    $onsubmit = $queryobject->getOnUpdate();
                } else if ($_POST['navigation'] == NAVIGATION_LANGUAGE_CHANGE) {
                    $onsubmit = $queryobject->getOnLanguageChange();
                } else if ($_POST['navigation'] == NAVIGATION_MODE_CHANGE) {
                    $onsubmit = $queryobject->getOnModeChange();
                } else if ($_POST['navigation'] == NAVIGATION_VERSION_CHANGE) {
                    $onsubmit = $queryobject->getOnVersionChange();
                }

                $tocall = $this->replaceFills($onsubmit);
                $parametersout = array();
                $removed = array();
                $test = excludeText($tocall, $removed);
                if (stripos($test, '(') !== false) {
                    $parameters = rtrim(substr($tocall, stripos($test, '(') + 1), ')');
                    $parameters = preg_split("/[\s,]+/", $parameters);
                    foreach ($parameters as $p) {
                        $removed = array();
                        $pt = excludeText($p, $removed);
                        if (stripos($pt, '(') === false) { // no function calls as parameters
                            $parametersout[] = (string) $p;
                        }
                    }

                    $tocall = substr($tocall, 0, stripos($tocall, '('));
                }

                if (function_exists($tocall)) {
                    if (inArray($tocall, getAllowedOnChangeFunctions()) && !inArray($tocall, getForbiddenOnChangeFunctions())) {
                        try {
                            $f = new ReflectionFunction($tocall);
                            $returnStr .= $f->invoke($parametersout);
                        } catch (Exception $e) {
                            
                        }
                    }
                }

                /* re-do action */
                $this->doAction($this->getRgid());

                /* save data record */
                $this->getDataRecord()->saveRecord();

                /* we finished everything and are showing a question if all went well 
                 * so this is the moment to update the state
                 */
                $this->saveState(false);

                /* stop */
                return;
            }
            // next/dk/rf/na
            else if (isset($_POST['navigation']) && inArray($_POST['navigation'], array($nextlabel, $dklabel, $rflabel, $nalabel))) {
                $torgid = 0;
                $result = $db->selectQuery('select torgid from ' . Config::dbSurvey() . '_next where suid=' . prepareDatabaseString($this->getSuid()) . ' and seid=' . prepareDatabaseString($this->seid) . ' and fromrgid = ' . prepareDatabaseString($lastrgid));
                if ($row = $db->getRow($result)) {
                    $torgid = $row["torgid"];
                }

                /* indicate we are going forward */
                $this->setForward(true);

                /* log action */
                if ($_POST['navigation'] == $nextlabel) {
                    $this->currentaction = ACTION_EXIT_NEXT;
                    $this->logAction($lastrgid, ACTION_EXIT_NEXT);
                } else if ($_POST['navigation'] == $dklabel) {
                    $this->currentaction = ACTION_EXIT_DK;
                    $this->logAction($lastrgid, ACTION_EXIT_DK);
                } else if ($_POST['navigation'] == $rflabel) {
                    $this->currentaction = ACTION_EXIT_RF;
                    $this->logAction($lastrgid, ACTION_EXIT_RF);
                } else if ($_POST['navigation'] == $nalabel) {
                    $this->currentaction = ACTION_EXIT_NA;
                    $this->logAction($lastrgid, ACTION_EXIT_NA);
                }

                /* store answers in db and previous state */
                $cnt = 1;

                foreach ($vars as $var) {

                    // next button
                    if ($_POST['navigation'] == $nextlabel) {
                        $vd = $this->getVariableDescriptive($var);
                        if ($vd->getAnswerType() == ANSWER_TYPE_SETOFENUMERATED || $vd->getAnswerType() == ANSWER_TYPE_MULTIDROPDOWN) {

                            $answer = "";
                            if ($dkrfnacheck == true) { /* dk/rf/na */
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt . "_dkrfna");
                                if (!inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                                    $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                                }
                            } else {
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                            }

                            if (is_array($answer)) {
                                $answer = implode(SEPARATOR_SETOFENUMERATED, $answer);
                            }
                            $this->setAnswer($var, $answer, DATA_CLEAN);
                        } else {
                            if ($vd->getAnswerType() != ANSWER_TYPE_NONE) {
                                $answer = "";
                                if ($dkrfnacheck == true) { /* dk/rf/na */
                                    $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt . "_dkrfna");
                                    if (!inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                                        $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                                    }
                                } else {
                                    $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                                }
                                $this->setAnswer($var, $answer, DATA_CLEAN);
                            }
                        }
                    }
                    // dk button
                    else if ($_POST['navigation'] == $dklabel) {
                        $vd = $this->getVariableDescriptive($var);
                        if ($vd->getAnswerType() != ANSWER_TYPE_NONE) {
                            $this->setAnswer($var, ANSWER_DK, DATA_CLEAN);
                        }
                    }
                    // rf button
                    else if ($_POST['navigation'] == $rflabel) {
                        $vd = $this->getVariableDescriptive($var);
                        if ($vd->getAnswerType() != ANSWER_TYPE_NONE) {
                            $this->setAnswer($var, ANSWER_RF, DATA_CLEAN);
                        }
                    }
                    // na button
                    else if ($_POST['navigation'] == $nalabel) {
                        $vd = $this->getVariableDescriptive($var);
                        if ($vd->getAnswerType() != ANSWER_TYPE_NONE) {
                            $this->setAnswer($var, ANSWER_NA, DATA_CLEAN);
                        }
                    }
                    $cnt++;
                }

                /* update remark status to clean */
                if ($remarks == BUTTON_YES && loadvarSurvey(POST_PARAM_REMARK_INDICATOR) == 1) {
                    $this->updateRemarkStatus(DATA_CLEAN);
                }

                $onsubmit = "";
                if ($_POST['navigation'] == $nextlabel) {
                    $onsubmit = $queryobject->getOnNext();
                } else if ($_POST['navigation'] == $dklabel) {
                    $onsubmit = $queryobject->getOnDK();
                } else if ($_POST['navigation'] == $rflabel) {
                    $onsubmit = $queryobject->getOnRF();
                } else if ($_POST['navigation'] == $nalabel) {
                    $onsubmit = $queryobject->getOnNA();
                }

                $tocall = $this->replaceFills($onsubmit);
                $parametersout = array();
                $removed = array();
                $test = excludeText($tocall, $removed);
                if (stripos($test, '(') !== false) {
                    $parameters = rtrim(substr($tocall, stripos($test, '(') + 1), ')');
                    $parameters = preg_split("/[\s,]+/", $parameters);
                    foreach ($parameters as $p) {
                        $removed = array();
                        $pt = excludeText($p, $removed);
                        if (stripos($pt, '(') === false) { // no function calls as parameters
                            $parametersout[] = (string) $p;
                        }
                    }

                    $tocall = substr($tocall, 0, stripos($tocall, '('));
                }

                if (function_exists($tocall)) {
                    if (inArray($tocall, getAllowedOnChangeFunctions()) && !inArray($tocall, getForbiddenOnChangeFunctions())) {
                        try {
                            $f = new ReflectionFunction($tocall);
                            $returnStr .= $f->invoke($parametersout);
                        } catch (Exception $e) {
                            
                        }
                    }
                }

                // action to do
                if ($torgid > 0) {

                    /* reset any assignments */
                    $this->setAssignments(array());

                    /* reset inline fields */
                    $this->setInlineFields(array());

                    /* reset sub displays */
                    $this->setSubDisplays(array());

                    /* reset fill texts */
                    $this->setFillTexts(array());

                    /* do action */
                    $this->doAction($torgid);

                    /* we finished everything and are showing a question if 
                     * went well so this is the moment to save the state
                     */
                    if ($this->endofsurvey == false) {

                        if ($this->getFlooding()) {
                            if ($this->stop != true) {
                                $this->getDataRecord()->saveRecord();
                                $this->saveState();
                            }
                        } else {
                            /* save data record */
                            $this->getDataRecord()->saveRecord();
                            $this->saveState();
                        }
                    }
                }
                // we are at the end of a section after having a last action
                else {

                    /* not end of survey, then clear any assignments and so on */
                    if ($this->isMainSection() == false) {
                        /* reset any assignments */
                        $this->setAssignments(array());

                        /* reset inline fields */
                        $this->setInlineFields(array());

                        /* reset sub displays */
                        $this->setSubDisplays(array());

                        /* reset fill texts */
                        $this->setFillTexts(array());
                    }

                    /* do end */
                    $this->doEnd(true);
                }

                /* stop */
                if ($this->getFlooding()) {
                    if ($this->stop != true) {
                        $this->doFakeSubmit($this->getDisplayed(), $this->getRgid(), $this->getTemplate());
                        $this->getNextQuestion();
                    }
                    $this->stop = true;
                    return;
                }
                doExit();
            }
        }
    }

    function doBackState($lastrgid, $dkrfnacheck, $save = true) {

        /* we load the last state */
        $this->loadLastState();

        $currentprefix = $this->getPrefix();
        $currentparentprefix = $this->getParentPrefix();
        $currentseid = $this->getSeid();

        /* delete last screenshot NOT ANYMORE, WE KEEP ALL SCREENSHOTS */
        //$this->deleteLastScreenshot();

        /* delete last state from db */
        $this->deleteLastState();

        /* determine which ones are the 'clean' variables 
         * (i.e. still in the state somewhere as displayed or assigned)
         */
        $cleanvariables = $this->getCleanVariables();

        /* undo any assignments in the db that were the result
         * of the last time we went forward
         */

        $this->undoAssignments($cleanvariables);

        /* get all data from the last state */
        $data = $this->state->getAllData();

        /* we load the last state before the last state we just deleted */
        $this->loadLastState();

        /* set all data from deleted state to the previous one */
        $this->state->setAllData($data);

        // save answers: only first time, if we go back across section calls then any answers will have been stored
        // in the state that we had updated before
        if ($save) {
            $vars = splitString("/~/", getFromSessionParams(SESSION_PARAM_VARIABLES));
            $cnt = 1;

            // Check if one or more current answers are DK/RF/NA. If so, then if current values are empty, we keep DK/RF/NA.
            // If we have a non-empty answer OR current answer is not DK/RF/NA, then we store all answers
            $update = false;
            foreach ($vars as $var) {
                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                if ((!is_array($answer) && trim($answer) != "") || (is_array($answer) && trim($answer[0]) != "")) {
                    $update = true;
                    break;
                } else {
                    $current = $this->getAnswer($var);
                    if (!inArray($current, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                        $update = true;
                        break;
                    }
                }
            }

            /* if individual dk/rf/na, then we always update on going back (if not, then on going back we preserve dk/rf/na if empty answer) */
            if ($dkrfnacheck == true) {
                $update = true;
            }

            // use prefix from last state, not previous, so we store answers under the right name!
            $newprefix = $this->getPrefix();
            $newparentprefix = $this->getParentPrefix();
            $newseid = $this->getSeid();
            $this->setPrefix($currentprefix);
            $this->setParentPrefix($currentparentprefix);
            $this->setSeid($currentseid);

            /* store answers in db and previous state */
            if ($update == true) {

                $defaultcleanvariables = getDefaultCleanVariables();
                $cnt = 1;
                foreach ($vars as $var) {

                    $vd = $this->getVariableDescriptive($var);
                    if ($vd->getAnswerType() == ANSWER_TYPE_SETOFENUMERATED || $vd->getAnswerType() == ANSWER_TYPE_MULTIDROPDOWN) {
                        $answer = "";
                        if ($dkrfnacheck == true) { /* dk/rf/na */
                            $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt . "_dkrfna");
                            if (!inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                            }
                        } else {
                            $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                        }

                        if (is_array($answer)) {
                            $answer = implode(SEPARATOR_SETOFENUMERATED, $answer);
                        }

                        if (inArray($var, $cleanvariables) || inArray($var, $defaultcleanvariables)) {
                            $dirty = DATA_CLEAN;
                        } else {
                            $dirty = DATA_DIRTY;
                        }

                        $this->setAnswer($var, $answer, $dirty);
                    } else {
                        if ($vd->getAnswerType() != ANSWER_TYPE_NONE) {

                            $answer = "";
                            if ($dkrfnacheck == true) { /* dk/rf/na */
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt . "_dkrfna");
                                if (!inArray($answer, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
                                    $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                                }
                            } else {
                                $answer = loadvarSurvey(SESSION_PARAMS_ANSWER . $cnt);
                            }

                            $dirty = DATA_DIRTY;
                            if (inArray($var, $cleanvariables) || inArray($var, $defaultcleanvariables)) {
                                $dirty = DATA_CLEAN;
                            }
                            $this->setAnswer($var, $answer, $dirty);
                        }
                    }
                    $cnt++;
                }
            }

            // restore to new state now that we stored the answer(s)
            $this->setPrefix($newprefix);
            $this->setParentPrefix($newparentprefix);
            $this->setSeid($newseid);
        }

        /* log action */
        $this->logAction($lastrgid, ACTION_EXIT_BACK);

        /* update previous state 
         * (for any answers given on the current screen)
         */
        $this->saveState(false);
    }

    function doExit() {
        $this->endofsurvey = true;
        $this->setAnswer(VARIABLE_END, date("Y-m-d H:i:s", time()));
        if (Config::useTransactions() == true) {
            doCommit();
        }

        /* save data record */
        $this->getDataRecord()->saveRecord();

        /* save last state */
        $this->saveState(false);

        /* set interview data as completed */
        $this->getDataRecord()->setToComplete();

        /* unlock */
        $this->unlock();

        if ($this->getFlooding()) {
            $this->stop = true;
            return;
        }

        /* show end and exit */
        echo $this->display->showEndSurvey();
        doExit();
    }

    function doEnd($savestate = false) {

        /* if already state of other sections before this one, then this is not the first section 
         * if it is, then do the end part
         */
        if ($this->isMainSection() == true) {
            $this->endofsurvey = true;
            $this->setAnswer(VARIABLE_END, date("Y-m-d H:i:s", time()));

            /* check if we need to reset non-keep variable answers */
            if ($this->survey->getDataKeepOnly() == DATA_KEEP_ONLY_YES) {
                $vars = $this->state->getVariableNames();
                $this->currentaction = ACTION_SURVEY_END;
                foreach ($vars as $var) {
                    $vd = $this->getVariableDescriptive(getBasicName($var));
                    if ($vd->getDataKeep() == DATA_KEEP_NO) {
                        $this->setAnswer($var, null);
                    }
                }
            }

            if (Config::useTransactions() == true) {
                doCommit();
            }

            /* save data record */
            $this->getDataRecord()->saveRecord();

            /* $savestate == true if we are reaching doEnd by going next on a last action in the base module 
             * and there are no more actions left in the _next table.
             */
            if ($savestate == true) {
                $this->saveState(false);
            }
            // we came here by calling doEnd in the compiled code after not finding a next action/if unmet and skipped to end/
            // and so on
            else {

                /* get any last things we did from the current state */
                $assign = $this->state->getAssignments();
                $data = $this->state->getAllData();

                /* load the last state we had, update it with any actions we did and save it */
                if ($this->loadLastState()) {
                    $this->state->setAllData($data);
                    $this->state->setAssignments($assign);
                    $this->saveState(false);
                }
            }

            /* set interview data as completed */
            $this->getDataRecord()->setToComplete();

            /* unlock */
            $this->unlock();

            if ($this->getFlooding()) {
                $this->stop = true;
                return;
            }

            /* show end and exit */
            echo $this->display->showEndSurvey();
            doExit();
        } else {

            /* get current state */
            $seid = $this->getParentSeid();
            $mainseid = $this->getMainSeid();
            $assign = $this->state->getAssignments();
            $data = $this->state->getAllData();
            $prefix = "";
            $this->getDataRecord()->saveRecord();
            $this->datarecord = null;
            unset($this->datarecord);
            $this->clearContext();

            /* get engine */
            global $engine;
            $engine = loadEngine($this->getSuid(), $this->primkey, $this->phpid, $this->version, $seid, false, true);

            /* transfer current state with updated details for section we are going back to */
            $engine->setState($this->state);
            $engine->setSeid($seid);
            $engine->setMainSeid($mainseid);

            // remove last "." if present
            $prefix = $this->getParentPrefix();
            if (endsWith($prefix, ".")) {
                $prefix = substr($prefix, 0, strlen($prefix) - 1);
            }

            // complicated prefix (secA.subsec.)
            if (contains($prefix, ".")) {
                $pos = strrpos($prefix, ".");
                $parentprefix = substr($prefix, 0, $pos);
                $prefix = substr($prefix, $pos + 1, strlen($prefix)) . ".";

                $engine->setParentPrefix($parentprefix);
                $engine->setPrefix($prefix);
            }
            // no complicated prefix (secA.subsec.)
            else {
                $pre = "";
                if ($prefix != "") {
                    $pre = $prefix . ".";
                }
                $engine->setPrefix($pre);
            }

            $engine->setForward($this->getForward());
            $engine->setFlooding($this->getFlooding());

            // transfer loop details for if we move in-memory back to loop (to prevent reset)
            $engine->setPreviousForLoopLastAction($this->getPreviousForLoopLastAction());
            $engine->setPreviousLoopRgid($this->getPreviousLoopRgid());

            /* go into parent section again */
            $engine->endSection();
            if ($this->getFlooding()) {
                $this->stop = true;
                return;
            }
            doExit();
        }
    }

    function getPreviousRgid() {
        return $this->previousrgid;
    }

    function getPreviousLoopRgid() {
        return $this->previouslooprgid;
    }

    function setPreviousLoopRgid($rgid) {
        $this->previouslooprgid = $rgid;
    }

    function getPreviousForLoopLastAction() {
        return $this->previousloopaction;
    }

    function setPreviousForLoopLastAction($t) {
        $this->previousloopaction = $t;
    }

    function getPreviousLoopString() {
        return $this->previousloopstring;
    }

    function getPreviousWhileRgid() {
        return $this->previouswhilergid;
    }

    function getPreviousWhileLastAction() {
        return $this->previouswhileaction;
    }

    function setPreviousWhileLastAction($t) {
        $this->previouswhileaction = $t;
    }

    /* LOG FUNCTIONS */

    function addScreenshot() {

        if ($this->getFlooding()) {
            return;
        }

        // don't store if external only
        $realvariablenames = $this->display->getRealVariables(explode("~", $this->getDisplayed()));
        foreach ($realvariablenames as $t) {

            // if core variable, then always store internal
            if (inArray($t, Common::surveyCoreVariables())) {
                continue;
            }
            $vr = $this->getVariableDescriptive($t);
            if ($vr->getVsid() != "") {
                if ($vr->getStoreLocation() == STORE_LOCATION_EXTERNAL) {
                    return;
                }
            }
        }

        global $survey;
        $localdb = null;
        if (Config::useTransactions() == true) {
            global $transdb;
            $localdb = $transdb;
        } else {
            global $db;
            $localdb = $db;
        }

        $l = getSurveyLanguage();
        $m = getSurveyMode();
        $v = getSurveyVersion();
        $key = $survey->getDataEncryptionKey();

        $stateid = $this->getStateId();

        $screen = gzcompress(urldecode(loadvar(POST_PARAM_SCREENSHOT)), 9);
        if ($stateid == "") {
            $stateid = 1;
        }

        $primkey = $this->getPrimaryKey();
        $bp = new BindParam();
        $suid = $this->getSuid();
        $scid = null;

        $bp->add(MYSQL_BINDING_INTEGER, $scid);
        $bp->add(MYSQL_BINDING_INTEGER, $suid);
        $bp->add(MYSQL_BINDING_STRING, $primkey);
        $bp->add(MYSQL_BINDING_INTEGER, $stateid);
        $bp->add(MYSQL_BINDING_STRING, $screen);
        $bp->add(MYSQL_BINDING_INTEGER, $m);
        $bp->add(MYSQL_BINDING_INTEGER, $l);
        $bp->add(MYSQL_BINDING_INTEGER, $v);

        if ($key == "") {
            $query = "insert into " . Config::dbSurveyData() . "_screendumps(scdid, suid, primkey, stateid, screen, mode, language, version) values (?,?,?,?,?,?,?,?)";
        } else {
            $query = "insert into " . Config::dbSurveyData() . "_screendumps(scdid, suid, primkey, stateid, screen, mode, language, version) values (?,?,?,?,aes_encrypt(?, '" . $key . "'),?,?,?)";
        }

        $localdb->executeBoundQuery($query, $bp->get());
        return "";
    }

    function addParadata($lastrgid) {

        if ($this->getFlooding()) {
            return;
        }

        $localdb = null;
        if (Config::useTransactions() == true) {
            global $transdb;
            $localdb = $transdb;
        } else {
            global $db;
            $localdb = $db;
        }
        $pardata = loadvar(POST_PARAM_PARADATA);
        $display = array();
        $vars = splitString("/~/", getFromSessionParams(SESSION_PARAM_VARIABLES));
        foreach ($vars as $variablename) {
            $variablename = $this->prefixVariableName($variablename);
            $display[] = $variablename;
        }
        $displayed = implode("~", $display);
        $stateid = $this->getStateId();
        $primkey = $this->getPrimaryKey();
        $suid = $this->getSuid();
        $l = getSurveyLanguage();
        $m = getSurveyMode();
        $v = getSurveyVersion();
        $pid = null;

        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_INTEGER, $pid);
        $bp->add(MYSQL_BINDING_INTEGER, $suid);
        $bp->add(MYSQL_BINDING_STRING, $primkey);
        $bp->add(MYSQL_BINDING_INTEGER, $stateid);
        $bp->add(MYSQL_BINDING_INTEGER, $lastrgid);
        $bp->add(MYSQL_BINDING_STRING, $displayed);
        $bp->add(MYSQL_BINDING_STRING, $pardata);
        $bp->add(MYSQL_BINDING_INTEGER, $m);
        $bp->add(MYSQL_BINDING_INTEGER, $l);
        $bp->add(MYSQL_BINDING_INTEGER, $v);
        global $survey;
        $key = $survey->getDataEncryptionKey();
        if ($key == "") {
            $query = "insert into " . Config::dbSurveyData() . "_paradata(pid, suid, primkey, stateid, rgid, displayed, paradata, mode, language, version) values (?,?,?,?,?,?,?,?,?,?)";
        } else {
            $query = "insert into " . Config::dbSurveyData() . "_paradata(pid, suid, primkey, stateid, rgid, displayed, paradata, mode, language, version) values (?,?,?,?,?,?,aes_encrypt(?, '" . $key . "'),?,?,?)";
        }

        $localdb->executeBoundQuery($query, $bp->get());
    }

    function addTimings($lastrgid, $laststateid) {

        if (Config::logSurveyTimings() == false) {
            return;
        }
        $localdb = null;
        if (Config::useTransactions() == true) {
            global $transdb;
            $localdb = $transdb;
        } else {
            global $db;
            $localdb = $db;
        }
        $vars = splitString("/~/", getFromSessionParams(SESSION_PARAM_VARIABLES));
        $beginwithload = date("Y-m-d H:i:s", getFromSessionParams(SESSION_PARAM_TIMESTAMP)); // session timestamp set during processing        
        $beginonscreen = date("Y-m-d H:i:s", loadvar("plts")); // document ready time
        $end = time();
        $lang = getSurveyLanguage();
        $mode = getSurveyMode();
        $version = getSurveyVersion();
        $time = time();
        $docend = loadvar("plets");
        if ($docend == "") {
            $docend = $time; // if no document end set, then use the time from processing (would happen if some code submits the page without setting the 'plets' variable
        }

        foreach ($vars as $var) {
            $var = $this->prefixVariableName($var);
            $query = "insert into " . Config::dbSurveyData() . '_times (suid, primkey, stateid, rgid, variable, begintime, begintime2, endtime, endtime2, timespent, timespent2, language, mode, version) values (';
            $query .= prepareDatabaseString($this->getSuid()) . ",";
            $query .= "'" . prepareDatabaseString($this->primkey) . "',";
            $query .= "'" . prepareDatabaseString($laststateid) . "',";
            $query .= "'" . prepareDatabaseString($lastrgid) . "',";
            $query .= "'" . prepareDatabaseString($var) . "',";
            $query .= "'" . prepareDatabaseString($beginwithload) . "',";
            $query .= "'" . prepareDatabaseString($beginonscreen) . "',";
            $query .= "'" . prepareDatabaseString(date("Y-m-d H:i:s", $end)) . "',";
            $query .= "'" . prepareDatabaseString(date("Y-m-d H:i:s", $docend)) . "',";

            $query .= prepareDatabaseString(($time - getFromSessionParams(SESSION_PARAM_TIMESTAMP))) . ","; // difference between outputting of page and storing of timing entry
            $query .= prepareDatabaseString(($docend - loadvar("plts"))) . ","; // difference between page ready and action (button clicked, language changed)            
            $query .= prepareDatabaseString($lang) . ",";
            $query .= prepareDatabaseString($mode) . ",";
            $query .= prepareDatabaseString($version) . ")";
            $localdb->executeQuery($query);
        }
    }

    function addLogs($variable, $answer, $di) {
        if (Config::logSurveyActions() == false) {
            return;
        }

        // no log if external storage only (ignore for core variables)
        $vardesc = $this->getVariableDescriptive($variable);
        if ($vardesc->getStoreLocation() == STORE_LOCATION_EXTERNAL && !inArray($variable, Common::surveyCoreVariables())) {
            return;
        }

        $localdb = null;
        if (Config::useTransactions() == true) {
            global $transdb;
            $localdb = $transdb;
        } else {
            global $db;
            $localdb = $db;
        }
        $ans = $answer;
        if ($ans == "") {
            $ans = null;
        } else if (is_array($ans)) {
            $ans = gzcompress(serialize($ans));
        }

        $prim = $this->getPrimaryKey();
        $var = $variable;
        $dirty = $di;
        $action = $this->currentaction;
        $suid = $this->getSuid();
        $version = getSurveyVersion();
        $language = getSurveyLanguage();
        $mode = getSurveyMode();

        if (Config::prepareDataQueries() == false) {
            global $survey;
            $key = $survey->getDataEncryptionKey();
            if (is_array($ans)) {
                $ans = gzcompress(serialize($ans));
            }

            $answer = '"' . prepareDatabaseString($ans) . '"';
            if ($key != "") {
                $answer = "aes_encrypt('" . prepareDatabaseString($ans) . "', '" . prepareDatabaseString($key) . "')";
            }
            $localdb->executeQuery('INSERT INTO ' . Config::dbSurveyData() . '_logs (suid, primkey, variablename, answer, dirty, action, version, language, mode) VALUES (' . prepareDatabaseString($suid) . ',"' . prepareDatabaseString($prim) . '","' . prepareDatabaseString($var) . '",' . prepareDatabaseString($answer) . ',' . prepareDatabaseString($dirty) . ',' . prepareDatabaseString($action) . ',' . prepareDatabaseString($version) . ',' . prepareDatabaseString($language) . ',' . prepareDatabaseString($mode) . ')');
        } else {

            $bp = new BindParam();
            $bp->add(MYSQL_BINDING_STRING, $suid);
            $bp->add(MYSQL_BINDING_STRING, $prim);
            $bp->add(MYSQL_BINDING_STRING, $var);
            $bp->add(MYSQL_BINDING_STRING, $ans);
            $bp->add(MYSQL_BINDING_INTEGER, $dirty);
            $bp->add(MYSQL_BINDING_INTEGER, $action);
            $bp->add(MYSQL_BINDING_INTEGER, $version);
            $bp->add(MYSQL_BINDING_INTEGER, $language);
            $bp->add(MYSQL_BINDING_INTEGER, $mode);
            $answer = "?";
            global $survey;
            $key = $survey->getDataEncryptionKey();
            if ($key != "") {
                $answer = "aes_encrypt(?, '" . $key . "')";
            }
            $localdb->executeBoundQuery('INSERT INTO ' . Config::dbSurveyData() . '_logs (suid, primkey, variablename, answer, dirty, action, version, language, mode) VALUES (?,?,?,' . $answer . ',?,?,?,?,?)', $bp->get());
        }
    }

    function getLastSurveyAction() {
        global $logActions;
        return $logActions->getLastSurveyAction($this->phpid, $this->getPrimaryKey());
    }

    function logAction($lastrgid, $actiontype) {
        global $logActions;
        $logActions->addSurveyAction($this->primkey, '', $lastrgid, USCIC_SURVEY, $actiontype, $this->getExternalOnly());
    }

    function getExternalOnly() {
        return $this->externalonly;
    }

    /* DISPLAY FUNCTIONS */

    function showQuestion($variablename, $rgid, $template = "") {

        /* log entry (single entry for all variable(s) 
         * in case of a group statement)
         */
        $this->logAction($rgid, ACTION_ENTRY);

        /* update state */
        $this->setDisplayed($variablename);
        $this->setRgid($rgid);
        $this->setTemplate($template);

        /* update state for sub display info */
        $this->setSubDisplays($this->getSubDisplays());

        /* unlock */
        $this->unlock();

        /* DATA FLOODER, then no need to build the screen */
        if ($this->getFlooding() == true) {
            return;
        }

        //ob_flush();
        //flush();
        header("X-XSS-Protection: 0"); // for chrome xx protection feature
        echo $this->display->showQuestion($variablename, $rgid, $template);

        // using transactions, then commit now after we started outputting
        if (Config::useTransactions() == true) {
            doCommit();
        }
    }

    function deleteLastScreenshot() {
        global $db;
        // TODO: UPDATE SO WE KEEP IT, BUT INDICATE IT IS DIRTY DATA NOW
        //$db->executeQuery('delete ' . Config::dbSurveyData() . '_screendumps where suid=' . $this->getSuid() . ' and primkey = "' . $this->primkey . '" order by scdid desc limit 1');
    }

    /* FILL FUNCTIONS */

    // used to keep track of fill text in group statements
    function addFillValue($variable) {

        $language = getSurveyLanguage();
        $var = $this->getVariableDescriptive($variable);
        $options = $var->getOptions();
        if (is_array($options)) {
            for ($i = 0; $i < sizeof($options); $i++) {
                $option = &$options[$i];
                $option["label"] = $this->replaceFills($option["label"], true);
            }
        }
        $t = $var->getAnswerType();
        $emptywarning = $this->replaceFills($var->getEmptyMessage(), true);
        $inlineansweredwarning = $this->replaceFills($var->getErrorMessageInlineAnswered());
        $inlinejavascript = $this->replaceFills($var->getInlineJavascript(), true);
        $pagejavascript = $this->replaceFills($var->getPageJavascript(), true);
        $scripts = $this->replaceFills($var->getScripts(), true);
        $id = $this->replaceFills($var->getID(), true);
        $inlinestyle = $this->replaceFills($var->getInlineStyle(), true);
        $pagestyle = $this->replaceFills($var->getPageStyle(), true);
        $filltext = $this->replaceFills($var->getFillText(), true);
        $checktext = $this->replaceFills($var->getCheckText(), true);
        $placeholder = $this->replaceFills($var->getPlaceholder(), true);
        $pageheader = $this->replaceFills($var->getPageHeader(), true);
        $pagefooter = $this->replaceFills($var->getPageFooter(), true);
        $extra = array();

        if (!inArray($t, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
            $eq = $this->replaceFills($var->getComparisonEqualTo(), true);
            $neq = $this->replaceFills($var->getComparisonNotEqualTo(), true);
            $geq = $this->replaceFills($var->getComparisonGreaterEqualTo(), true);
            $ge = $this->replaceFills($var->getComparisonGreater(), true);
            $seq = $this->replaceFills($var->getComparisonSmallerEqualTo(), true);
            $se = $this->replaceFills($var->getComparisonSmaller(), true);
            $ereq = "";
            $erneq = "";
            $ergeq = "";
            $erge = "";
            $erseq = "";
            $erse = "";
            if ($eq != "") {
                $ereq = $this->replaceFills($var->getErrorMessageComparisonEqualTo(), true);
            }
            if ($neq != "") {
                $erneq = $this->replaceFills($var->getErrorMessageComparisonNotEqualTo(), true);
            }
            if ($geq != "") {
                $ergeq = $this->replaceFills($var->getErrorMessageComparisonGreaterEqualTo(), true);
            }
            if ($ge != "") {
                $erge = $this->replaceFills($var->getErrorMessageComparisonGreater(), true);
            }
            if ($seq != "") {
                $erseq = $this->replaceFills($var->getErrorMessageComparisonSmallerEqualTo(), true);
            }
            if ($se != "") {
                $erse = $this->replaceFills($var->getErrorMessageComparisonSmaller(), true);
            }

            $one = array(SETTING_COMPARISON_EQUAL_TO => $eq, SETTING_COMPARISON_NOT_EQUAL_TO => $neq, SETTING_COMPARISON_GREATER_EQUAL_TO => $geq, SETTING_COMPARISON_GREATER => $ge, SETTING_COMPARISON_SMALLER_EQUAL_TO => $seq, SETTING_COMPARISON_SMALLER => $se);
            $two = array(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO => $ereq, SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO => $erneq, SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO => $ergeq, SETTING_ERROR_MESSAGE_COMPARISON_GREATER => $erge, SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO => $erseq, SETTING_ERROR_MESSAGE_COMPARISON_SMALLER => $erse);
            $extra = array_merge($one, $two);
        }
        /// string comparisons
        else if (inArray($t, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $eq = $this->replaceFills($var->getComparisonEqualTo(), true);
            $neq = $this->replaceFills($var->getComparisonNotEqualTo(), true);
            $eqign = $this->replaceFills($var->getComparisonEqualToIgnoreCase(), true);
            $neqign = $this->replaceFills($var->getComparisonNotEqualToIgnoreCase(), true);

            $ereq = "";
            $erneq = "";
            $ereqign = "";
            $erneqign = "";
            if ($eq != "") {
                $ereq = $this->replaceFills($var->getErrorMessageComparisonEqualTo(), true);
            }
            if ($neq != "") {
                $erneq = $this->replaceFills($var->getErrorMessageComparisonNotEqualTo(), true);
            }
            if ($eqign != "") {
                $ereqign = $this->replaceFills($var->getErrorMessageComparisonEqualToIgnoreCase(), true);
            }
            if ($neqign != "") {
                $erneqign = $this->replaceFills($var->getErrorMessageComparisonNotEqualToIgnoreCase(), true);
            }

            $one = array(SETTING_COMPARISON_EQUAL_TO => $eq, SETTING_COMPARISON_NOT_EQUAL_TO => $neq, SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE => $eqign, SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE => $neqign);
            $two = array(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO => $ereq, SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO => $erneq, SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE => $ereqign, SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE => $erneqign);
            $extra = array_merge($one, $two);
        }

        switch ($t) {
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $inputmask = $this->replaceFills($var->getInputMask(), true);
                $inputmaskcustom = $this->replaceFills($var->getInputMaskCustom(), true);
                $inputmaskplaceholder = $this->replaceFills($var->getInputMaskPlaceholder(), true);
                $minlength = $this->replaceFills($var->getMinimumLength(), true);
                $maxlength = $this->replaceFills($var->getMaximumLength(), true);
                $minwords = $this->replaceFills($var->getMinimumWords(), true);
                $maxwords = $this->replaceFills($var->getMaximumWords(), true);
                $pattern = $this->replaceFills($var->getPattern(), true);
                $minwarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM_LENGTH => $minlength), $this->replaceFills($var->getErrorMessageMinimumLength(), true));
                $maxwarning = replacePlaceHolders(array(PLACEHOLDER_MAXIMUM_LENGTH => $maxlength), $this->replaceFills($var->getErrorMessageMaximumLength(), true));
                $minwordswarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM_WORDS => $minwords), $this->replaceFills($var->getErrorMessageMinimumWords(), true));
                $maxwordswarning = replacePlaceHolders(array(PLACEHOLDER_MAXIMUM_WORDS => $maxwords), $this->replaceFills($var->getErrorMessageMaximumWords(), true));
                $patternwarning = replacePlaceHolders(array(PLACEHOLDER_PATTERN => $pattern), $this->replaceFills($var->getErrorMessagePattern(), true));
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_INPUT_MASK_CUSTOM => $inputmaskcustom, SETTING_INPUT_MASK => $inputmask, SETTING_INPUT_MASK_PLACEHOLDER => $inputmaskplaceholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_PRETEXT => $this->replaceFills($var->getPreText(), true), SETTING_POSTTEXT => $this->replaceFills($var->getPostText(), true), SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_MINIMUM_LENGTH => $minlength, SETTING_MAXIMUM_LENGTH => $maxlength, SETTING_MINIMUM_WORDS => $minwords, SETTING_MAXIMUM_WORDS => $maxwords, SETTING_PATTERN => $pattern, SETTING_ERROR_MESSAGE_MINIMUM_LENGTH => $minwarning, SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH => $maxwarning, SETTING_ERROR_MESSAGE_MINIMUM_WORDS => $minwordswarning, SETTING_ERROR_MESSAGE_MAXIMUM_WORDS => $maxwordswarning, SETTING_ERROR_MESSAGE_PATTERN => $patternwarning, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_RANGE:
                $inputmask = $this->replaceFills($var->getInputMask(), true);
                $inputmaskcustom = $this->replaceFills($var->getInputMaskCustom(), true);
                $inputmaskplaceholder = $this->replaceFills($var->getInputMaskPlaceholder(), true);
                $minimumrange = $this->replaceFills($var->getMinimum(), true);
                $maximumrange = $this->replaceFills($var->getMaximum(), true);
                $otherrange = $this->replaceFills($var->getOtherValues(), true);
                $rangewarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM => $minimumrange, PLACEHOLDER_MAXIMUM => $maximumrange, PLACEHOLDER_OTHERVALUES => $otherrange), $this->replaceFills($var->getErrorMessageRange(), true));
                $arr = array(SETTING_OTHER_RANGE => $otherrange, SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_INPUT_MASK_CUSTOM => $inputmaskcustom, SETTING_INPUT_MASK => $inputmask, SETTING_INPUT_MASK_PLACEHOLDER => $inputmaskplaceholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_PRETEXT => $this->replaceFills($var->getPreText(), true), SETTING_POSTTEXT => $this->replaceFills($var->getPostText(), true), SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_MINIMUM_RANGE => $minimumrange, SETTING_MAXIMUM_RANGE => $maximumrange, SETTING_ERROR_MESSAGE_RANGE => $rangewarning, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_DOUBLE:
                $inputmask = $this->replaceFills($var->getInputMask(), true);
                $inputmaskcustom = $this->replaceFills($var->getInputMaskCustom(), true);
                $inputmaskplaceholder = $this->replaceFills($var->getInputMaskPlaceholder(), true);
                $doublewarning = $this->replaceFills($var->getErrorMessageDouble(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_INPUT_MASK_CUSTOM => $inputmaskcustom, SETTING_INPUT_MASK => $inputmask, SETTING_INPUT_MASK_PLACEHOLDER => $inputmaskplaceholder, SETTING_ERROR_MESSAGE_DOUBLE => $doublewarning, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_PRETEXT => $this->replaceFills($var->getPreText(), true), SETTING_POSTTEXT => $this->replaceFills($var->getPostText(), true), SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_INTEGER:
                $inputmask = $this->replaceFills($var->getInputMask(), true);
                $inputmaskcustom = $this->replaceFills($var->getInputMaskCustom(), true);
                $inputmaskplaceholder = $this->replaceFills($var->getInputMaskPlaceholder(), true);
                $intwarning = $this->replaceFills($var->getErrorMessageInteger(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_INPUT_MASK_CUSTOM => $inputmaskcustom, SETTING_INPUT_MASK => $inputmask, SETTING_INPUT_MASK_PLACEHOLDER => $inputmaskplaceholder, SETTING_ERROR_MESSAGE_INTEGER => $intwarning, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_PRETEXT => $this->replaceFills($var->getPreText(), true), SETTING_POSTTEXT => $this->replaceFills($var->getPostText(), true), SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_KNOB:
                $textlabel = $this->replaceFills($var->getTextBoxLabel(), true);
                $minimumrange = $this->replaceFills($var->getMinimum(), true);
                $maximumrange = $this->replaceFills($var->getMaximum(), true);
                $rangewarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM => $minimumrange, PLACEHOLDER_MAXIMUM => $maximumrange), $this->replaceFills($var->getErrorMessageRange(), true));
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_SLIDER_TEXTBOX_LABEL => $textlabel, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_MINIMUM_RANGE => $minimumrange, SETTING_MAXIMUM_RANGE => $maximumrange, SETTING_ERROR_MESSAGE_RANGE => $rangewarning, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_SLIDER:
                $textlabel = $this->replaceFills($var->getTextBoxLabel(), true);
                $minimumrange = $this->replaceFills($var->getMinimum(), true);
                $maximumrange = $this->replaceFills($var->getMaximum(), true);
                $rangewarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM => $minimumrange, PLACEHOLDER_MAXIMUM => $maximumrange), $this->replaceFills($var->getErrorMessageRange(), true));
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_SLIDER_TEXTBOX_LABEL => $textlabel, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_MINIMUM_RANGE => $minimumrange, SETTING_MAXIMUM_RANGE => $maximumrange, SETTING_ERROR_MESSAGE_RANGE => $rangewarning, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_ENUMERATED:
                $textlabel = $this->replaceFills($var->getEnumeratedTextBoxLabel(), true);
                $postlabel = $this->replaceFills($var->getEnumeratedTextBoxPostText(), true);

                $exactinline = $this->replaceFills($var->getInlineExactRequired(), true);
                $exactinlinewarning = "";
                if ($exactinline != "") {
                    $exactinlinewarning = replacePlaceHolders(array(PLACEHOLDER_INLINE_EXACT_REQUIRED => $exactinline), $this->replaceFills($var->getErrorMessageInlineExactRequired(), true));
                }

                $mininline = $this->replaceFills($var->getInlineMinimumRequired(), true);
                $mininlinewarning = "";
                if ($mininline != "") {
                    $mininlinewarning = replacePlaceHolders(array(PLACEHOLDER_INLINE_MINIMUM_REQUIRED => $mininline), $this->replaceFills($var->getErrorMessageInlineMinimumRequired(), true));
                }

                $maxinline = $this->replaceFills($var->getInlineMaximumRequired(), true);
                $maxinlinewarning = "";
                if ($maxinline != "") {
                    $maxinlinewarning = replacePlaceHolders(array(PLACEHOLDER_INLINE_MAXIMUM_REQUIRED => $maxinline), $this->replaceFills($var->getErrorMessageInlineMaximumRequired(), true));
                }

                $exclusiveinlinewarning = $this->replaceFills($var->getErrorMessageInlineExclusive(), true);
                $inclusiveinlinewarning = $this->replaceFills($var->getErrorMessageInlineInclusive(), true);

                $custom = $this->replaceFills($var->getEnumeratedCustom(), true);
                $random = $this->replaceFills($var->getEnumeratedRandomizer(), true);
                $enteredwarning = $this->replaceFills($var->getErrorMessageEnumeratedEntered(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED => $enteredwarning, SETTING_ENUMERATED_RANDOMIZER => $random, SETTING_ENUMERATED_CUSTOM => $custom, SETTING_ENUMERATED_TEXTBOX_LABEL => $textlabel, SETTING_ENUMERATED_TEXTBOX_POSTTEXT => $postlabel, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED => $exactinlinewarning, SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED => $maxinlinewarning, SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED => $mininlinewarning, SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE => $exclusiveinlinewarning, SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE => $inclusiveinlinewarning, SETTING_INLINE_MINIMUM_REQUIRED => $mininline, SETTING_INLINE_MAXIMUM_REQUIRED => $maxinline, SETTING_INLINE_EXACT_REQUIRED => $exactinline, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_OPTIONS => $options, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_DROPDOWN:
                $random = $this->replaceFills($var->getEnumeratedRandomizer(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_ENUMERATED_RANDOMIZER => $random, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_PRETEXT => $this->replaceFills($var->getPreText(), true), SETTING_POSTTEXT => $this->replaceFills($var->getPostText(), true), SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_OPTIONS => $options, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $textlabel = $this->replaceFills($var->getEnumeratedTextBoxLabel(), true);
                $postlabel = $this->replaceFills($var->getEnumeratedTextBoxPostText(), true);
                $minselect = $this->replaceFills($var->getMinimumSelected(), true);
                $maxselect = $this->replaceFills($var->getMaximumSelected(), true);
                $exactselect = $this->replaceFills($var->getExactSelected(), true);
                $invalid = $this->replaceFills($var->getInvalidSelected(), true);
                $invalidsub = $this->replaceFills($var->getInvalidSubSelected(), true);

                $minwarning = "";
                $maxwarning = "";
                $exactwarning = "";
                $invalidsubwarning = "";
                $invalidwarning = "";
                if ($minselect != "") {
                    $minwarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM_SELECTED => $minselect), $this->replaceFills($var->getErrorMessageSelectMinimum(), true));
                }
                if ($maxselect != "") {
                    $maxwarning = replacePlaceHolders(array(PLACEHOLDER_MAXIMUM_SELECTED => $maxselect), $this->replaceFills($var->getErrorMessageSelectMaximum(), true));
                }
                if ($exactselect != "") {
                    $exactwarning = replacePlaceHolders(array(PLACEHOLDER_EXACT_SELECTED => $exactselect), $this->replaceFills($var->getErrorMessageSelectExact(), true));
                }
                if ($invalidsub != "") {
                    $invalidsubwarning = replacePlaceHolders(array(PLACEHOLDER_INVALIDSUBSET_SELECTED => getInvalidSubsetString($var, $invalidsub)), $this->replaceFills($var->getErrorMessageSelectInvalidSubset(), true));
                }
                if ($invalid != "") {
                    $invalidwarning = replacePlaceHolders(array(PLACEHOLDER_INVALIDSET_SELECTED => getInvalidSetString($var, $invalid)), $this->replaceFills($var->getErrorMessageSelectInvalidSet(), true));
                }

                $enteredwarning = $this->replaceFills($var->getErrorMessageSetOfEnumeratedEntered(), true);


                $exactinline = $this->replaceFills($var->getInlineExactRequired(), true);
                $exactinlinewarning = "";
                if ($exactinline != "") {
                    $exactinlinewarning = replacePlaceHolders(array(PLACEHOLDER_INLINE_EXACT_REQUIRED => $exactinline), $this->replaceFills($var->getErrorMessageInlineExactRequired(), true));
                }

                $mininline = $this->replaceFills($var->getInlineMinimumRequired(), true);
                $mininlinewarning = "";
                if ($mininline != "") {
                    $mininlinewarning = replacePlaceHolders(array(PLACEHOLDER_INLINE_MINIMUM_REQUIRED => $mininline), $this->replaceFills($var->getErrorMessageInlineMinimumRequired(), true));
                }

                $maxinline = $this->replaceFills($var->getInlineMaximumRequired(), true);
                $maxinlinewarning = "";
                if ($maxinline != "") {
                    $maxinlinewarning = replacePlaceHolders(array(PLACEHOLDER_INLINE_MAXIMUM_REQUIRED => $maxinline), $this->replaceFills($var->getErrorMessageInlineMaximumRequired(), true));
                }

                $exclusiveinlinewarning = $this->replaceFills($var->getErrorMessageInlineExclusive(), true);
                $inclusiveinlinewarning = $this->replaceFills($var->getErrorMessageInlineInclusive(), true);

                $custom = $this->replaceFills($var->getEnumeratedCustom(), true);
                $random = $this->replaceFills($var->getEnumeratedRandomizer(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED => $enteredwarning, SETTING_ENUMERATED_RANDOMIZER => $random, SETTING_ENUMERATED_CUSTOM => $custom, SETTING_ENUMERATED_TEXTBOX_LABEL => $textlabel, SETTING_ENUMERATED_TEXTBOX_POSTTEXT => $postlabel, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED => $exactinlinewarning, SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED => $maxinlinewarning, SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED => $mininlinewarning, SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE => $exclusiveinlinewarning, SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE => $inclusiveinlinewarning, SETTING_INLINE_MINIMUM_REQUIRED => $mininline, SETTING_INLINE_MAXIMUM_REQUIRED => $maxinline, SETTING_INLINE_EXACT_REQUIRED => $exactinline, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_OPTIONS => $options, SETTING_MINIMUM_SELECTED => $minselect, SETTING_EXACT_SELECTED => $exactselect, SETTING_MAXIMUM_SELECTED => $maxselect, SETTING_INVALID_SELECTED => $invalid, SETTING_INVALIDSUB_SELECTED => $invalidsub, SETTING_ERROR_MESSAGE_MINIMUM_SELECT => $minwarning, SETTING_ERROR_MESSAGE_MAXIMUM_SELECT => $maxwarning, SETTING_ERROR_MESSAGE_EXACT_SELECT => $exactwarning, SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT => $invalidsubwarning, SETTING_ERROR_MESSAGE_INVALID_SELECT => $invalidwarning, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_MULTIDROPDOWN:
                $minselect = $this->replaceFills($var->getMinimumSelected(), true);
                $maxselect = $this->replaceFills($var->getMaximumSelected(), true);
                $exactselect = $this->replaceFills($var->getExactSelected(), true);
                $invalid = $this->replaceFills($var->getInvalidSelected(), true);
                $invalidsub = $this->replaceFills($var->getInvalidSubSelected(), true);

                $minwarning = "";
                $maxwarning = "";
                $exactwarning = "";
                $invalidsubwarning = "";
                $invalidwarning = "";
                if ($minselect != "") {
                    $minwarning = replacePlaceHolders(array(PLACEHOLDER_MINIMUM_SELECTED => $minselect), $this->replaceFills($var->getErrorMessageSelectMinimum(), true));
                }
                if ($maxselect != "") {
                    $maxwarning = replacePlaceHolders(array(PLACEHOLDER_MAXIMUM_SELECTED => $maxselect), $this->replaceFills($var->getErrorMessageSelectMaximum(), true));
                }
                if ($exactselect != "") {
                    $exactwarning = replacePlaceHolders(array(PLACEHOLDER_EXACT_SELECTED => $exactselect), $this->replaceFills($var->getErrorMessageSelectExact(), true));
                }
                if ($invalidsub != "") {
                    $invalidsubwarning = replacePlaceHolders(array(PLACEHOLDER_INVALIDSUBSET_SELECTED => getInvalidSubsetString($var, $invalidsub)), $this->replaceFills($var->getErrorMessageSelectInvalidSubset(), true));
                }
                if ($invalid != "") {
                    $invalidwarning = replacePlaceHolders(array(PLACEHOLDER_INVALIDSET_SELECTED => getInvalidSetString($var, $invalid)), $this->replaceFills($var->getErrorMessageSelectInvalidSet(), true));
                }

                $random = $this->replaceFills($var->getEnumeratedRandomizer(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_ENUMERATED_RANDOMIZER => $random, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_PRETEXT => $this->replaceFills($var->getPreText(), true), SETTING_POSTTEXT => $this->replaceFills($var->getPostText(), true), SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_OPTIONS => $options, SETTING_MINIMUM_SELECTED => $minselect, SETTING_EXACT_SELECTED => $exactselect, SETTING_MAXIMUM_SELECTED => $maxselect, SETTING_INVALID_SELECTED => $invalid, SETTING_INVALIDSUB_SELECTED => $invalidsub, SETTING_ERROR_MESSAGE_MINIMUM_SELECT => $minwarning, SETTING_ERROR_MESSAGE_MAXIMUM_SELECT => $maxwarning, SETTING_ERROR_MESSAGE_EXACT_SELECT => $exactwarning, SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT => $invalidsubwarning, SETTING_ERROR_MESSAGE_INVALID_SELECT => $invalidwarning, SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_CALENDAR:
                $dateswarning = replacePlaceHolders(array(PLACEHOLDER_MAXIMUM_CALENDAR => $this->replaceFills($var->getMaximumDatesSelected(), true)), $this->replaceFills($var->getErrorMessageMaximumCalendar(), true));
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR => $dateswarning, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_MAXIMUM_CALENDAR => $this->replaceFills($var->getMaximumDatesSelected(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR => $dateswarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_CUSTOM:
                $custom = $this->replaceFills($var->getAnswerTypeCustom(), true);
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_ANSWERTYPE_CUSTOM => $custom, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_MAXIMUM_CALENDAR => $this->replaceFills($var->getMaximumDatesSelected(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR => $dateswarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_SLIDER:
                $textlabel = $this->replaceFills($var->getTextBoxLabel(), true);
                $postlabel = $this->replaceFills($var->getTextBoxPostText(), true);
                $arr = array(SETTING_SLIDER_TEXTBOX_LABEL => $textlabel, SETTING_SLIDER_TEXTBOX_POSTTEXT => $postlabel, SETTING_SLIDER_LABELS => $this->replaceFills($var->getSliderLabels(), true), SETTING_SLIDER_INCREMENT => $this->replaceFills($var->getIncrement(), true), SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_DATE:
                $arr = array(SETTING_DATE_DEFAULT_VIEW => $this->replaceFills($var->getDateDefaultView(), true), SETTING_DATE_FORMAT => $this->replaceFills($var->getDateFormat(), true), SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_DATETIME:
                $arr = array(SETTING_DATE_DEFAULT_VIEW => $this->replaceFills($var->getDateDefaultView(), true), SETTING_DATETIME_FORMAT => $this->replaceFills($var->getDateTimeFormat(), true), SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            case ANSWER_TYPE_TIME:
                $arr = array(SETTING_DATE_DEFAULT_VIEW => $this->replaceFills($var->getDateDefaultView(), true), SETTING_TIME_FORMAT => $this->replaceFills($var->getTimeFormat(), true), SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
            default:
                $arr = array(SETTING_PAGE_FOOTER => $pagefooter, SETTING_PAGE_HEADER => $pageheader, SETTING_PLACEHOLDER => $placeholder, SETTING_ERROR_MESSAGE_INLINE_ANSWERED => $inlineansweredwarning, SETTING_FILLTEXT => $filltext, SETTING_QUESTION => $this->replaceFills($var->getQuestion(), true), SETTING_EMPTY_MESSAGE => $emptywarning, SETTING_JAVASCRIPT_WITHIN_ELEMENT => $inlinejavascript, SETTING_JAVASCRIPT_WITHIN_PAGE => $pagejavascript, SETTING_SCRIPTS => $scripts, SETTING_ID => $id, SETTING_STYLE_WITHIN_ELEMENT => $inlinestyle, SETTING_STYLE_WITHIN_PAGE => $pagestyle, SETTING_HOVERTEXT => $this->replaceFills($var->getHoverText(), true), SETTING_CHECKTEXT => $checktext);
                break;
        }

        // add
        $this->state->addFillText($variable, array_merge($arr, $extra));
    }

    function getFill($variable, $vardescriptive, $texttype = "question") {
        $array = $this->state->getFillText($variable);

        //use text array if text array (if group statement)
        if ($array != null && sizeof($array) > 0) {
            switch ($texttype) {
                case SETTING_OPTIONS:
                    $options = $array[$texttype];
                    $id = "";
                    if (inArray($vardescriptive->getAnswerType(), array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_RANK))) {
                        $id = $this->getFill($variable, $vardescriptive, SETTING_ID);
                        if ($id == "") {
                            $d = $this->getDisplayNumbers();
                            $number = $d[strtoupper($variable)];
                            $id = SESSION_PARAMS_ANSWER . $number;
                        }
                    }
                    for ($i = 0; $i < sizeof($options); $i++) {
                        $option = &$options[$i];
                        $option["label"] = $this->replaceInlineFields($option["label"], $id, $vardescriptive->getAnswerType(), $option["code"]);
                    }
                    return $options;
                default:

                    if (isset($array[$texttype])) {
                        return $this->replaceInlineFields($array[$texttype]);
                    }
                    return "";
            }
        }

        // not in group, so we get it now
        $text = "";
        switch ($texttype) {
            case SETTING_QUESTION:
                $text = $this->replaceFills($vardescriptive->getQuestion());
                break;
            case SETTING_OPTIONS:
                $options = $vardescriptive->getOptions();
                $id = "";
                if (inArray($vardescriptive->getAnswerType(), array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_RANK))) {
                    $id = $this->getFill($variable, $vardescriptive, SETTING_ID);
                    if ($id == "") {
                        $d = $this->getDisplayNumbers();
                        $number = $d[strtoupper($variable)];
                        $id = SESSION_PARAMS_ANSWER . $number;
                    }
                }
                for ($i = 0; $i < sizeof($options); $i++) {
                    $option = &$options[$i];
                    $option["label"] = $this->replaceInlineFields($this->replaceFills($option["label"]), $id, $vardescriptive->getAnswerType(), $option["code"]);
                }
                return $options;
            case SETTING_MINIMUM_RANGE:
                $text = $this->replaceFills($vardescriptive->getMinimum());
                break;
            case SETTING_MAXIMUM_RANGE:
                $text = $this->replaceFills($vardescriptive->getMaximum());
                break;
            case SETTING_OTHER_RANGE:
                $text = $this->replaceFills($vardescriptive->getOtherValues());
                break;
            case SETTING_MINIMUM_LENGTH:
                $text = $this->replaceFills($vardescriptive->getMinimumLength());
                break;
            case SETTING_PLACEHOLDER:
                $text = $this->replaceFills($vardescriptive->getPlaceholder());
                break;
            case SETTING_INPUT_MASK:
                $text = $this->replaceFills($vardescriptive->getInputMask());
                break;
            case SETTING_INPUT_MASK_CUSTOM:
                $text = $this->replaceFills($vardescriptive->getInputMaskCustom());
                break;
            case SETTING_INPUT_MASK_PLACEHOLDER:
                $text = $this->replaceFills($vardescriptive->getInputMaskPlaceholder());
                break;
            case SETTING_MAXIMUM_LENGTH:
                $text = $this->replaceFills($vardescriptive->getMaximumLength());
                break;
            case SETTING_MAXIMUM_CALENDAR:
                $text = $this->replaceFills($vardescriptive->getMaximumDatesSelected());
                break;
            case SETTING_MINIMUM_WORDS:
                $text = $this->replaceFills($vardescriptive->getMinimumWords());
                break;
            case SETTING_MAXIMUM_WORDS:
                $text = $this->replaceFills($vardescriptive->getMaximumWords());
                break;
            case SETTING_PATTERN:
                $text = $this->replaceFills($vardescriptive->getPattern());
                break;
            case SETTING_MINIMUM_RANKED:
                $text = $this->replaceFills($vardescriptive->getMinimumRanked());
                break;
            case SETTING_EXACT_RANKED:
                $text = $this->replaceFills($vardescriptive->getExactRanked());
                break;
            case SETTING_MAXIMUM_RANKED:
                $text = $this->replaceFills($vardescriptive->getMaximumRanked());
                break;
            case SETTING_MINIMUM_SELECTED:
                $text = $this->replaceFills($vardescriptive->getMinimumSelected());
                break;
            case SETTING_EXACT_SELECTED:
                $text = $this->replaceFills($vardescriptive->getExactSelected());
                break;
            case SETTING_MAXIMUM_SELECTED:
                $text = $this->replaceFills($vardescriptive->getMaximumSelected());
                break;
            case SETTING_INVALID_SELECTED:
                $text = $this->replaceFills($vardescriptive->getInvalidSelected());
                break;
            case SETTING_INVALIDSUB_SELECTED:
                $text = $this->replaceFills($vardescriptive->getInvalidSubSelected());
                break;
            case SETTING_INLINE_MINIMUM_REQUIRED:
                $text = $this->replaceFills($vardescriptive->getInlineMinimumRequired());
                break;
            case SETTING_INLINE_MAXIMUM_REQUIRED:
                $text = $this->replaceFills($vardescriptive->getInlineMaximumRequired());
                break;
            case SETTING_INLINE_EXACT_REQUIRED:
                $text = $this->replaceFills($vardescriptive->getInlineExactRequired());
                break;
            case SETTING_EMPTY_MESSAGE:
                $text = $this->replaceFills($vardescriptive->getEmptyMessage());
                break;
            case SETTING_ERROR_MESSAGE_MINIMUM_LENGTH:
                $text = $this->replaceFills($vardescriptive->getErrorMessageMinimumLength());
                break;
            case SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH:
                $text = $this->replaceFills($vardescriptive->getErrorMessageMaximumLength());
                break;
            case SETTING_ERROR_MESSAGE_MINIMUM_WORDS:
                $text = $this->replaceFills($vardescriptive->getErrorMessageMinimumWords());
                break;
            case SETTING_ERROR_MESSAGE_MAXIMUM_WORDS:
                $text = $this->replaceFills($vardescriptive->getErrorMessageMaximumWords());
                break;
            case SETTING_ERROR_MESSAGE_PATTERN:
                $text = $this->replaceFills($vardescriptive->getErrorMessagePattern());
                break;
            case SETTING_ERROR_MESSAGE_RANGE:
                $text = $this->replaceFills($vardescriptive->getErrorMessageRange());
                break;
            case SETTING_ERROR_MESSAGE_INTEGER:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInteger());
                break;
            case SETTING_ERROR_MESSAGE_DOUBLE:
                $text = $this->replaceFills($vardescriptive->getErrorMessageDouble());
                break;
            case SETTING_ERROR_MESSAGE_MINIMUM_RANK:
                $text = $this->replaceFills($vardescriptive->getErrorMessageRankMinimum());
                break;
            case SETTING_ERROR_MESSAGE_MAXIMUM_RANK:
                $text = $this->replaceFills($vardescriptive->getErrorMessageRankMaximum());
                break;
            case SETTING_ERROR_MESSAGE_EXACT_RANK:
                $text = $this->replaceFills($vardescriptive->getErrorMessageRankExact());
                break;
            case SETTING_ERROR_MESSAGE_MINIMUM_SELECT:
                $text = $this->replaceFills($vardescriptive->getErrorMessageSelectMinimum());
                break;
            case SETTING_ERROR_MESSAGE_MAXIMUM_SELECT:
                $text = $this->replaceFills($vardescriptive->getErrorMessageSelectMaximum());
                break;
            case SETTING_ERROR_MESSAGE_EXACT_SELECT:
                $text = $this->replaceFills($vardescriptive->getErrorMessageSelectExact());
                break;
            case SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT:
                $text = $this->replaceFills($vardescriptive->getErrorMessageSelectInvalidSubset());
                break;
            case SETTING_ERROR_MESSAGE_INVALID_SELECT:
                $text = $this->replaceFills($vardescriptive->getErrorMessageSelectInvalidSet());
                break;
            case SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInlineExclusive());
                break;
            case SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInlineInclusive());
                break;
            case SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInlineMinimumRequired());
                break;
            case SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInlineMaximumRequired());
                break;
            case SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInlineExactRequired());
                break;
            case SETTING_ERROR_MESSAGE_INLINE_ANSWERED:
                $text = $this->replaceFills($vardescriptive->getErrorMessageInlineAnswered());
                break;
            case SETTING_PRETEXT:
                $text = $this->replaceFills($vardescriptive->getPretext());
                break;
            case SETTING_POSTTEXT:
                $text = $this->replaceFills($vardescriptive->getPosttext());
                break;
            case SETTING_HOVERTEXT:
                $text = $this->replaceFills($vardescriptive->getHovertext());
                break;
            case SETTING_JAVASCRIPT_WITHIN_ELEMENT:
                $text = $this->replaceFills($vardescriptive->getInlineJavascript());
                break;
            case SETTING_JAVASCRIPT_WITHIN_PAGE:
                $text = $this->replaceFills($vardescriptive->getPageJavascript());
                break;
            case SETTING_SCRIPTS:
                $text = $this->replaceFills($vardescriptive->getScripts());
                break;
            case SETTING_ID:
                $text = $this->replaceFills($vardescriptive->getId());
                break;
            case SETTING_STYLE_WITHIN_ELEMENT:
                $text = $this->replaceFills($vardescriptive->getInlineStyle());
                break;
            case SETTING_STYLE_WITHIN_PAGE:
                $text = $this->replaceFills($vardescriptive->getPageStyle());
                break;
            case SETTING_FILLTEXT:
                $text = $this->replaceFills($vardescriptive->getFillText());
                break;
            case SETTING_CHECKTEXT:
                $text = $this->replaceFills($vardescriptive->getCheckText());
                break;
            case SETTING_SLIDER_TEXTBOX_LABEL:
                $text = $this->replaceFills($vardescriptive->getTextBoxLabel());
                break;
            case SETTING_SLIDER_TEXTBOX_POSTTEXT:
                $text = $this->replaceFills($vardescriptive->getTextBoxPostText());
                break;
            case SETTING_ENUMERATED_CUSTOM:
                $text = $this->replaceFills($vardescriptive->getEnumeratedCustom());
                break;
            case SETTING_ENUMERATED_TEXTBOX_LABEL:
                $text = $this->replaceFills($vardescriptive->getEnumeratedTextBoxLabel());
                break;
            case SETTING_ENUMERATED_TEXTBOX_POSTTEXT:
                $text = $this->replaceFills($vardescriptive->getEnumeratedTextBoxPostText());
                break;
            case SETTING_ENUMERATED_RANDOMIZER:
                $text = $this->replaceFills($vardescriptive->getEnumeratedRandomizer());
                break;
            case SETTING_DROPDOWN_OPTGROUP:
                $text = $this->replaceFills($vardescriptive->getComboboxOptGroup());
                break;
            case SETTING_ANSWERTYPE_CUSTOM:
                $text = $this->replaceFills($vardescriptive->getAnswerTypeCustom());
                break;
            case SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED:
                $text = $this->replaceFills($vardescriptive->getErrorMessageEnumeratedEntered());
                break;
            case SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED:
                $text = $this->replaceFills($vardescriptive->getErrorMessageSetOfEnumeratedEntered());
                break;
            case SETTING_PAGE_HEADER:
                $text = $this->replaceFills($vardescriptive->getPageHeader());
                break;
            case SETTING_PAGE_FOOTER:
                $text = $this->replaceFills($vardescriptive->getPageFooter());
                break;
            case SETTING_COMPARISON_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getComparisonEqualTo());
                break;
            case SETTING_COMPARISON_NOT_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getComparisonNotEqualTo());
                break;
            case SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE:
                $text = $this->replaceFills($vardescriptive->getComparisonEqualToIgnoreCase());
                break;
            case SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE:
                $text = $this->replaceFills($vardescriptive->getComparisonNotEqualToIgnoreCase());
                break;
            case SETTING_COMPARISON_GREATER_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getComparisonGreaterEqualTo());
                break;
            case SETTING_COMPARISON_GREATER:
                $text = $this->replaceFills($vardescriptive->getComparisonGreater());
                break;
            case SETTING_COMPARISON_SMALLER_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getComparisonSmallerEqualTo());
                break;
            case SETTING_COMPARISON_SMALLER:
                $text = $this->replaceFills($vardescriptive->getComparisonSmaller());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonEqualTo());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonNotEqualTo());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonEqualToIgnoreCase());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonNotEqualToIgnoreCase());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonGreaterEqualTo());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_GREATER:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonGreater());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonSmallerEqualTo());
                break;
            case SETTING_ERROR_MESSAGE_COMPARISON_SMALLER:
                $text = $this->replaceFills($vardescriptive->getErrorMessageComparisonSmaller());
                break;
            case SETTING_SLIDER_LABELS:
                $text = $this->replaceFills($vardescriptive->getSliderLabels());
                break;
            case SETTING_SLIDER_INCREMENT:
                $text = $this->replaceFills($vardescriptive->getIncrement());
                break;
            case SETTING_DATE_FORMAT:
                $text = $this->replaceFills($vardescriptive->getDateFormat());
                break;
            case SETTING_TIME_FORMAT:
                $text = $this->replaceFills($vardescriptive->getTimeFormat());
                break;
            case SETTING_DATETIME_FORMAT:
                $text = $this->replaceFills($vardescriptive->getDateTimeFormat());
                break;
            case SETTING_DATE_DEFAULT_VIEW:
                $text = $this->replaceFills($vardescriptive->getDateDefaultView());
                break;
        }
        return $this->replaceInlineFields($text);
    }

    function replaceFills($text, $updateinlinefields = false) {
        $cnt = 0;
        if (trim($text) == "") {
            return $text;
        }

        while (strpos($text, INDICATOR_FILL_NOVALUE) !== false) {
            $fills = getReferences($text, INDICATOR_FILL_NOVALUE);

            // sort fills by longest keys
            //uksort($fills, "compareLength");
            usort($fills, "reversenat");
            foreach ($fills as $fill) {
                if ($fill == "") {
                    $fillref = $fill;
                    $filltext = DUMMY_INDICATOR_FILL_NOVALUE;
                } else {
                    $fillref = $fill;

                    $tt = $this->getFillValue(INDICATOR_FILL_NOVALUE . $fill); 
                    if ($tt === null) {
                        $tt = "";
                    }
                    $filltext = strtr($tt, array('\\' => '\\\\', '$' => '\$'));
                }
                $pattern = "/\\" . INDICATOR_FILL_NOVALUE . preparePattern($fillref) . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }

            $cnt++;

            /* stop after 999 times */
            if ($cnt > 999) {
                break;
            }
        }

        $pattern = "/\\DUMMY_INDICATOR_FILL_NOVALUE/i";
        $text = preg_replace($pattern, INDICATOR_FILL_NOVALUE, $text);

        $cnt = 0;
        while (strpos($text, INDICATOR_FILL) !== false) {
            $fills = getReferences($text, INDICATOR_FILL);

            // sort fills by longest keys
            //uksort($fills, "compareLength");
            usort($fills, "reversenat");

            foreach ($fills as $fill) {
                if ($fill == "") {
                    $fillref = $fill;
                    $filltext = DUMMY_INDICATOR_FILL;
                } else {
                    $fillref = $fill;
                    $tt = $this->getDisplayValue($fill, $this->getFillValue($fill)); 
                    if ($tt === null) {
                        $tt = "";
                    }
                    $filltext = strtr($tt, array('\\' => '\\\\', '$' => '\$'));
                }
                $pattern = "/\\" . INDICATOR_FILL . preparePattern($fillref) . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }

            $cnt++;

            /* stop after 999 times */
            if ($cnt > 999) {
                break;
            }
        }

        $pattern = "/\\DUMMY_INDICATOR_FILL/i";
        $text = preg_replace($pattern, INDICATOR_FILL_NOVALUE, $text);


        if ($updateinlinefields) {
            $text = $this->updateInlineFields($text);
        }
        return $text;
    }

    function getDisplayValue($variable, $value) {
        $var = $this->getVariableDescriptive($variable);
        if ($var) {
            $type = $var->getAnswerType();
            switch ($type) {
                case ANSWER_TYPE_OPEN:
                    return $value;
                    break;
                case ANSWER_TYPE_STRING:
                    return $value;
                    break;
                case ANSWER_TYPE_DROPDOWN:
                /* fall through */
                case ANSWER_TYPE_ENUMERATED:
                    return $var->getOptionLabel($value);
                    break;
                case ANSWER_TYPE_MULTIDROPDOWN:
                /* fall through */
                case ANSWER_TYPE_RANK:
                /* fall through */
                case ANSWER_TYPE_SETOFENUMERATED:
                    return $var->getSetOfEnumeratedOptionLabel($value);
                    break;
                case ANSWER_TYPE_INTEGER:
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                /* fall through */
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */
                case ANSWER_TYPE_DOUBLE:
                    return $value;
                    break;
                default:
                    return $value;
            }
        }
        return "";
    }

    /* DATA FLOODER */

    function doFakeSubmit($variables, $rgid, $template) {

        // clear any previous post variables
        $_POST = array();

        // nothing, then we reached the end and break
        if ($variables == "" || $this->stop == true) {
            return;
        }

        // determine variables and display numbers
        $variables = explode("~", $variables);

        // determine next label to set in session
        $nextlabel = "";
        $queryobject = null;
        if (sizeof($variables) == 1) {
            $var = $this->getVariableDescriptive($variables[0]);
            $queryobject = $var;
            $nextlabel = $var->getLabelNextButton();
        } else {
            $group = $this->getGroup($template);
            $queryobject = $group;
            $nextlabel = $group->getLabelNextButton();
        }

        // set information for next call
        setSessionParameter(SESSION_PARAM_RGID, $rgid);
        setSessionParameter(SESSION_PARAM_GROUP, $template);
        $_POST['navigation'] = $nextlabel;
        $realvariables = explode("~", $this->getDisplayObject()->getRealVariables($variables));
        setSessionParameter(SESSION_PARAM_VARIABLES, implode("~", $realvariables));
        setSessionParameter(SESSION_PARAM_TIMESTAMP, date("Y-m-d H:i:s"));

        $this->setDisplayCounter(0); // reset
        $this->determineDisplayNumbers(implode("~", $realvariables));
        $displaynumbers = $this->getDisplayNumbers();

        // generate answer(s)
        foreach ($realvariables as $rl) {
            $var = $this->getVariableDescriptive($rl);
            if (!inArray($var->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                $number = $displaynumbers[strtoupper($rl)];
                $id = SESSION_PARAMS_ANSWER . $number;
                $_POST[$id] = $this->generateAnswer($var, $queryobject);
            }
        }

        $_POST['r'] = 'dummy'; // add this so session parameters are used
    }

    function generateAnswer($var, $queryobject) {
        $variable = $var->getName();
        if ($variable == VARIABLE_LANGUAGE) {
            if (getSurveyLanguageAllowChange() == LANGUAGE_CHANGE_PROGRAMMATIC_ALLOWED) {
                $allowed = explode("~", $this->survey->getAllowedLanguages(getSurveyMode()));
                if (sizeof($allowed) > 1) {
                    $rand = mt_rand(0, sizeof($allowed));
                    return $allowed[$rand];
                }
            }
            return getSurveyLanguage();
        }
        if ($variable == VARIABLE_MODE) {
            if (getSurveyModeAllowChange() == MODE_CHANGE_PROGRAMMATIC_ALLOWED) {
                $allowed = explode("~", $this->survey->getAllowedModes());
                if (sizeof($allowed) > 1) {
                    $rand = mt_rand(0, sizeof($allowed));
                    return $allowed[$rand];
                }
            }
            return getSurveyMode();
        }
        if ($variable == VARIABLE_VERSION) {
            return getSurveyVersion();
        }

        // check for error options
        $noanswer = array();
        if ($var->getIfEmpty() != IF_EMPTY_NOTALLOW) {
            $noanswer[] = "";
        }
        $allowerror = $var->getIfError() != IF_ERROR_NOTALLOW;

        // check for dk/rf/na        
        if ($queryobject->getShowDKButton() == BUTTON_YES) {
            $noanswer[] = ANSWER_DK;
        }
        if ($queryobject->getShowRFButton() == BUTTON_YES) {
            $noanswer[] = ANSWER_RF;
        }
        if ($queryobject->getShowNAButton() == BUTTON_YES) {
            $noanswer[] = ANSWER_NA;
        }

        // no answer is an option, then do a random probe
        if (sizeof($noanswer) > 0) {

            // 1/50 chance for an empty answer
            if (mt_rand(1, 50) == 25) {
                return $noanswer[mt_rand(0, sizeof($noanswer))]; // return a no answer option
            }
        }

        // we return a non-empty answer
        $answertype = $var->getAnswerType();
        switch ($answertype) {
            case ANSWER_TYPE_INTEGER:
                return mt_rand(0, PHP_INT_MAX);
            case ANSWER_TYPE_DOUBLE:
                return mt_rand(0, PHP_INT_MAX);
            case ANSWER_TYPE_RANGE:
                $min = $this->getFill($variable, $var, SETTING_MINIMUM_RANGE);
                if ($min == "") {
                    $min = 0;
                }
                $max = $this->getFill($variable, $var, SETTING_MAXIMUM_RANGE);
                if ($max == "") {
                    $max = PHP_INT_MAX;
                }
                $others = explode(",", $this->getFill($variable, $var, SETTING_OTHER_RANGE));
                $r = mt_rand(1, 50);
                if (sizeof($others) > 0 && $r == 50) {
                    $opt = array();
                    foreach ($others as $o) {
                        if (is_numeric($o)) {
                            $opt[] = $o;
                        }
                    }
                    if (sizeof($opt) > 0) {
                        return $opt[mt_rand(0, sizeof($opt))];
                    } else {
                        return mt_rand($min, $max);
                    }
                } else {
                    return mt_rand($min, $max);
                }
            case ANSWER_TYPE_KNOB:
                $min = $this->getFill($variable, $var, SETTING_MINIMUM_RANGE);
                if ($min == "") {
                    $min = 0;
                }
                $max = $this->getFill($variable, $var, SETTING_MAXIMUM_RANGE);
                if ($max == "") {
                    $max = PHP_INT_MAX;
                }
                return mt_rand($min, $max);
            case ANSWER_TYPE_SLIDER:
                $min = $this->getFill($variable, $var, SETTING_MINIMUM_RANGE);
                if ($min == "") {
                    $min = 0;
                }
                $max = $this->getFill($variable, $var, SETTING_MAXIMUM_RANGE);
                if ($max == "") {
                    $max = PHP_INT_MAX;
                }
                return mt_rand($min, $max);
            case ANSWER_TYPE_STRING:
                $min = $this->getFill($variable, $var, SETTING_MINIMUM_LENGTH);
                if ($min == "") {
                    $min = 5;
                }
                $max = $this->getFill($variable, $var, SETTING_MAXIMUM_LENGTH);
                if ($max == "") {
                    $max = PHP_INT_MAX;
                }
                return $this->generateRandomText($min);
            case ANSWER_TYPE_OPEN:
                $min = $this->getFill($variable, $var, SETTING_MINIMUM_LENGTH);
                if ($min == "") {
                    $min = 5;
                }
                $max = $this->getFill($variable, $var, SETTING_MAXIMUM_LENGTH);
                if ($max == "") {
                    $max = PHP_INT_MAX;
                }
                return $this->generateRandomText($min);
            case ANSWER_TYPE_DROPDOWN;
            /* fall through */
            case ANSWER_TYPE_ENUMERATED:
                return $this->generateRandomEnumerated($variable, $var);
            case ANSWER_TYPE_MULTIDROPDOWN;
            /* fall through */
            case ANSWER_TYPE_SETOFENUMERATED:
                return $this->generateRandomSetOfEnumerated($variable, $var);
            case ANSWER_TYPE_DATE:
                return date("Y-m-d", $this->generateRandomDateTime($variable, $var));
            case ANSWER_TYPE_TIME:
                return $this->generateRandomDateTime($variable, $var);
            case ANSWER_TYPE_DATETIME:
                return date("Y-m-d H:i:s", $this->generateRandomDateTime($variable, $var));
            case ANSWER_TYPE_CUSTOM:
                return "1";
            case ANSWER_TYPE_SECTION;
            /* fall through */
            case ANSWER_TYPE_NONE:
                return ""; // should not happen, but ok
            default:
                return "";
        }
    }

    function generateRandomText($length = 8) {
        $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
        $count = mb_strlen($chars);
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }
        return $result;
    }

    function generateRandomSetOfEnumerated($variable, $var) {
        $options = $var->getOptions();
        $codes = array();
        foreach ($options as $opt) {
            $codes[] = $opt["code"];
        }

        // equal to
        $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
        if ($eq != "") {
            $values = explode(SEPARATOR_SETOFENUMERATED, $eq);
            $res = array();
            foreach ($values as $v) {
                if (is_numeric($v)) {
                    $res[] = $v;
                }
            }
            return implode(SEPARATOR_SETOFENUMERATED, $res);
        }

        // not equal to, then exclude those codes
        $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
        if ($neq != "") {
            $values = explode(SEPARATOR_SETOFENUMERATED, $neq);
            foreach ($values as $v) {
                if (is_numeric($v)) {
                    if (inArray($v, $codes)) {
                        unset($codes[array_search($v, $codes)]);
                    }
                }
            }
        }

        $ge = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER));
        $codes2 = array();
        if ($ge != "") {
            $values = explode(SEPARATOR_SETOFENUMERATED, $ge);
            foreach ($codes as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code <= $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes2[] = $code;
                }
            }
        }

        $geq = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
        $codes3 = array();
        if ($geq != "") {
            $values = explode(SEPARATOR_SETOFENUMERATED, $geq);
            foreach ($codes2 as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code < $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes3[] = $code;
                }
            }
        }

        $se = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
        $codes4 = array();
        if ($se != "") {
            $values = explode(SEPARATOR_SETOFENUMERATED, $se);
            foreach ($codes3 as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code >= $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes4[] = $code;
                }
            }
        }

        $seq = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
        $codes5 = array();
        if ($seq != "") {
            $values = explode(SEPARATOR_SETOFENUMERATED, $seq);
            foreach ($codes4 as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code > $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes5[] = $code;
                }
            }
        }

        $exclusive = array();
        $invalidsub = $this->engine->getFill($variable, $var, SETTING_INVALIDSUB_SELECTED);
        if ($invalidsub != "") {
            $this->getInvalidCombinations($invalidsub, $exclusive);
        }
        $invalid = $this->engine->getFill($variable, $var, SETTING_INVALID_SELECTED);
        if ($invalid != "") {
            $this->getInvalidCombinations($invalid, $exclusive);
        }

        $res = array();
        foreach ($codes5 as $c) {
            $random = mt_rand(1, sizeof($codes5));
            if ($random == 1) {
                $add = true;

                // check for invalid combinations
                foreach ($exclusive as $arr) {
                    $first = $arr[0];
                    $second = $arr[1];

                    // get all numbers in first part
                    $firstnumbers = array();
                    if (contains($first, "-")) {
                        $firstfirst = explode("-", $first);
                        for ($j = $firstfirst[0]; $j <= $firstfirst[1]; $j++) {
                            $firstnumbers[] = $j;
                        }
                    } else {
                        if (is_numeric($first)) {
                            $firstnumbers[] = $first;
                        }
                    }

                    // get all numbers in second part
                    $secondnumbers = array();
                    if (contains($second, "-")) {
                        $secondsecond = explode("-", $second);
                        for ($j = $secondsecond[0]; $j <= $secondsecond[1]; $j++) {
                            $secondnumbers[] = $j;
                        }
                    } else {
                        if (is_numeric($second)) {
                            $secondnumbers[] = $second;
                        }
                    }

                    // add code to temp array and check for invalid combination
                    $restemp = $res;
                    $restemp[] = $c;
                    $allfirst = true;
                    foreach ($firstnumbers as $f) {
                        if (!inArray($f, $restemp)) {
                            $allfirst = false;
                            break;
                        }
                    }

                    // found all numbers in the first group
                    if ($allfirst == true) {
                        $allsecond = true;
                        foreach ($secondnumbers as $f) {
                            if (!inArray($f, $restemp)) {
                                $allsecond = false;
                                break;
                            }
                        }

                        // found all numbers in the second group, so don't add to avoid invalid
                        if ($allsecond == true) {
                            $add = false;
                        }
                    }

                    // we found an invalid combination, then stop
                    if ($add == false) {
                        break;
                    }
                }
                if ($add == true) {
                    $res[] = $c;
                }
            }
        }

        return implode(SEPARATOR_SETOFENUMERATED, $res);
    }

    function getInvalidCombinations($invalid, &$exclusive) {
        $subs = explode(";", $invalid);
        foreach ($subs as $sub) {
            $expl = explode(",", $sub);
            $exclusive[] = $expl;
        }
    }

    function generateRandomEnumerated($variable, $var) {
        $options = $var->getOptions();
        $codes = array();
        foreach ($options as $opt) {
            $codes[] = $opt["code"];
        }

        // equal to
        $eq = trim($this->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
        if ($eq != "") {
            $values = explode("-", $eq);
            foreach ($values as $v) {
                if (is_numeric($v)) {
                    return $v;
                }
            }
        }

        // not equal to, then exclude those codes
        $neq = trim($this->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
        if ($neq != "") {
            $values = explode("-", $neq);
            foreach ($values as $v) {
                if (is_numeric($v)) {
                    if (inArray($v, $codes)) {
                        unset($codes[array_search($v, $codes)]);
                    }
                }
            }
        }

        $ge = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER));
        $codes2 = array();
        if ($ge != "") {
            $values = explode("-", $ge);
            foreach ($codes as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code <= $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes2[] = $code;
                }
            }
        }

        $geq = trim($this->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
        $codes3 = array();
        if ($geq != "") {
            $values = explode("-", $geq);
            foreach ($codes2 as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code < $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes3[] = $code;
                }
            }
        }

        $se = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
        $codes4 = array();
        if ($se != "") {
            $values = explode("-", $se);
            foreach ($codes3 as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code >= $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes4[] = $code;
                }
            }
        }

        $seq = trim($this->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
        $codes5 = array();
        if ($seq != "") {
            $values = explode("-", $seq);
            foreach ($codes4 as $code) {
                $keep = true;
                foreach ($values as $v) {
                    if (is_numeric($v) && $code > $v) {
                        $keep = false;
                        break;
                    }
                }
                if ($keep) {
                    $codes5[] = $code;
                }
            }
        }

        $random = mt_rand(0, sizeof($codes5));
        return $codes5[$random];
    }

    function generateRandomDateTime($variable, $var) {
        $type = $var->getAnswerType();
        $eq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_EQUAL_TO));
        if ($eq != "") {
            $values = explode("-", $eq);
            foreach ($values as $v) {
                if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
                    if (strtotime($v)) {
                        return strtotime($v);
                    }
                } else {
                    if (strtotime("2015-11-01 " + $v)) {
                        return $v;
                    }
                }
            }
        }
        $min = 0;
        $max = time();
        $exclude = array();
        $neq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_NOT_EQUAL_TO));
        if ($neq != "") {
            $values = explode("-", $neq);
            foreach ($values as $v) {
                if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
                    if (strtotime($v)) {
                        $exclude[] = strtotime($v);
                    }
                } else {
                    if (strtotime("2015-11-01 " + $v)) {
                        $exclude[] = $this->getTimeSeconds($v);
                    }
                }
            }
        }
        $geq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_GREATER_EQUAL_TO));
        if ($geq != "") {
            $values = explode("-", $geq);
            foreach ($values as $v) {
                if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
                    if (strtotime($v)) {
                        if ($min < strtotime($v)) {
                            $min = strtotime($v);
                        }
                    }
                } else {
                    if (strtotime("2015-11-01 " + $v)) {
                        if ($min < $this->getTimeSeconds($v)) {
                            $min = $this->getTimeSeconds($v);
                        }
                    }
                }
            }
        }

        $gr = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_GREATER));
        if ($gr != "") {
            $values = explode("-", $gr);
            foreach ($values as $v) {
                if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
                    if (strtotime($v)) {
                        if ($min < (strtotime($v) + 1)) {
                            $min = strtotime($v) + 1;
                        }
                    }
                } else {
                    if (strtotime("2015-11-01 " + $v)) {
                        if ($min < ($this->getTimeSeconds($v) + 1)) {
                            $min = $this->getTimeSeconds($v) + 1;
                        }
                    }
                }
            }
        }
        $seq = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_SMALLER_EQUAL_TO));
        if ($seq != "") {
            $values = explode("-", $seq);
            foreach ($values as $v) {
                if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
                    if (strtotime($v)) {
                        if ($max > strtotime($v)) {
                            $max = strtotime($v);
                        }
                    }
                } else {
                    if (strtotime("2015-11-01 " + $v)) {
                        if ($max > $this->getTimeSeconds($v)) {
                            $max = $this->getTimeSeconds($v);
                        }
                    }
                }
            }
        }
        $sm = trim($this->engine->getFill($variable, $var, SETTING_COMPARISON_SMALLER));
        if ($sm != "") {
            $values = explode("-", $sm);
            foreach ($values as $v) {
                if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
                    if (strtotime($v)) {
                        if ($max > (strtotime($v) - 1)) {
                            $max = strtotime($v) - 1;
                        }
                    }
                } else {
                    if (strtotime("2015-11-01 " + $v)) {
                        if ($max > $this->getTimeSeconds($v)) {
                            $max = $this->getTimeSeconds($v) - 1;
                        }
                    }
                }
            }
        }

        // no acceptable answer possible
        if ($min > $max) {
            return "";
        }
        $ans = "";
        while (true) {
            $ans = mt_rand($min, $max);
            if (!inArray($ans, $exclude)) {
                break;
            }
        }

        // return generated answer
        if (inArray($type, array(ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME))) {
            return $ans;
        } else {
            return date("H:i:s", strtotime("2015-11-01") + $ans);
        }
    }

    function getTimeSeconds($str_time) {
        // http://stackoverflow.com/questions/4834202/convert-hhmmss-to-seconds-only
        $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
        sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
        return ($hours * 3600 + $minutes * 60 + $seconds);
    }

}

?>