<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class VariableDescriptive extends Component {

    private $variabledescriptive;
    private $options;
    private $outputoptions;
    private $maximumoptioncode;
    private $type;
    private $iferrorgroup;
    private $fillines;
    private $checklines;

    function __construct($variablenameOrRow = "") {

        $this->setObjectType(OBJECT_VARIABLEDESCRIPTIVE);
        if (is_array($variablenameOrRow)) {
            $this->variabledescriptive = $variablenameOrRow;

            $this->setObjectName($this->getVsid());

            $this->setSuid($this->variabledescriptive["suid"]);

            /* read in type */
            if ($this->hasType()) {
                global $survey;
                $this->type = $survey->getType($this->getTyd());
            }

            /* add settings */
            $this->addSettings($this->getSettings());
        }
        $this->options = null;
        $this->outputoptions = null;
        $this->iferrorgroup = $this->getIfError();
    }

    function getVsid() {

        return $this->variabledescriptive['vsid'];
    }

    function setVsid($vsid) {

        $this->variabledescriptive['vsid'] = $vsid;
    }

    function getName() {

        return $this->variabledescriptive["variablename"];
    }

    function setName($name) {
        $this->variabledescriptive["variablename"] = $name;
        $this->setSettingValue(SETTING_NAME, $name);
    }

    function getSeid() {

        return $this->variabledescriptive["seid"];
    }

    function setSeid($seid) {
        $this->variabledescriptive["seid"] = $seid;
    }

    function getPosition() {
        return $this->variabledescriptive["position"];
    }

    function setPosition($position) {
        $this->variabledescriptive["position"] = $position;
    }

    function getDescription($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DESCRIPTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* no survey or type level setting */
        return "";
    }

    function setDescription($description) {
        $this->setSettingValue(SETTING_DESCRIPTION, $description);
    }

    function getQuestion($default = true) {
        return $this->getSettingValue(SETTING_QUESTION, $default);
    }

    function setQuestion($text) {
        $this->setSettingValue(SETTING_QUESTION, $text);
    }

    function getAnswerType($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ANSWERTYPE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAnswerType($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setAnswerType($text) {
        $this->setSettingValue(SETTING_ANSWERTYPE, $text);
    }

    function getAnswerTypeCustom($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ANSWERTYPE_CUSTOM, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAnswerTypeCustom($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setAnswerTypeCustom($text) {
        $this->setSettingValue(SETTING_ANSWERTYPE_CUSTOM, $text);
    }

    /* general functions */

    function getKeep($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEEP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeep($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */
        if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }

        global $survey;
        return $survey->getKeep($default);
    }

    function setKeep($text) {
        $this->setSettingValue(SETTING_KEEP, $text);
    }

    function isKeep() {
        return $this->getKeep() == KEEP_ANSWER_YES;
    }

    function getArray($default = true) {

        /* variable level setting */       
        $val = $this->getSettingValue(SETTING_ARRAY, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getArray($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getArray($default);
    }

    function setArray($text) {
        $this->setSettingValue(SETTING_ARRAY, $text);
    }

    function isArray() {

        $arr = $this->getArray();
        if ($arr == ARRAY_ANSWER_YES) {
            return true;
        }
        return false;
    }

    function getSection($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SECTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSection($default);
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function setSection($text) {
        $this->setSettingValue(SETTING_SECTION, $text);
    }

    /* output functions */

    function getHidden($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHidden($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHidden($default);
    }

    function setHidden($text) {

        $this->setSettingValue(SETTING_HIDDEN, $text);
    }

    function isHidden() {
        if ($this->getHidden() == HIDDEN_YES) {
            return true;
        }
        return false;
    }

    function getHiddenPaperVersion($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN_PAPER_VERSION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHiddenPaperVersion($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHiddenPaperVersion($default);
    }

    function setHiddenPaperVersion($text) {
        $this->setSettingValue(SETTING_HIDDEN_PAPER_VERSION, $text);
    }

    function isHiddenPaperVersion() {
        if ($this->getHiddenPaperVersion() == HIDDEN_YES) {
            return true;
        }
        return false;
    }

    function getHiddenRouting($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN_ROUTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHiddenRouting($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHiddenRouting($default);
    }

    function setHiddenRouting($text) {
        $this->setSettingValue(SETTING_HIDDEN_ROUTING, $text);
    }

    function isHiddenRouting() {
        if ($this->getHiddenRouting() == HIDDEN_YES) {
            return true;
        }
        return false;
    }

    function getHiddenTranslation($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN_TRANSLATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHiddenTranslation($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHiddenTranslation($default);
    }

    function setHiddenTranslation($text) {
        $this->setSettingValue(SETTING_HIDDEN_TRANSLATION, $text);
    }

    function isHiddenTranslation() {
        if ($this->getHiddenRouting() == HIDDEN_YES) {
            return true;
        }
        return false;
    }

    function getScreendumpStorage($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SCREENDUMPS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getScreendumpStorage($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getScreendumpStorage($default);
    }

    function setScreendumpStorage($text) {
        $this->setSettingValue(SETTING_SCREENDUMPS, $text);
    }

    function isScreendumpStorage() {
        if ($this->getScreendumpStorage() == SCREENDUMPS_YES) {
            return true;
        }
        return false;
    }

    function getParadata($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PARADATA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getParadata($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }        
        global $survey;
        return $survey->getParadata($default);
    }

    function setParadata($text) {
        $this->setSettingValue(SETTING_PARADATA, $text);
    }

    function isParadata() {
        if ($this->getParadata() == PARADATA_YES) {
            return true;
        }
        return false;
    }

    function getDataKeep($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_KEEP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDataKeep($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDataKeep($default);
    }

    function setDataKeep($text) {
        $this->setSettingValue(SETTING_DATA_KEEP, $text);
    }

    function isDataKeep() {
        if ($this->getDataKeep() == DATA_KEEP_YES) {
            return true;
        }
        return false;
    }

    function getDataSkipVariable($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_SKIP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDataSkipVariable($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDataSkipVariable($default);
    }

    function setDataSkipVariable($text) {
        $this->setSettingValue(SETTING_DATA_SKIP, $text);
    }

    function isDataSkipVariable() {
        if ($this->getDataSkipVariable() == DATA_SKIP_YES) {
            return true;
        }
        return false;
    }

    function getDataSkipVariablePostFix($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_SKIP_POSTFIX, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDataSkipVariable($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDataSkipVariablePostFix($default);
    }

    function setDataSkipVariablePostFix($text) {
        $this->setSettingValue(SETTING_DATA_SKIP_POSTFIX, $text);
    }

    function getDataInputMask($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_INPUTMASK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDataInputMask($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDataInputMask($default);
    }

    function setDataInputMask($text) {
        $this->setSettingValue(SETTING_DATA_INPUTMASK, $text);
    }

    function isDataInputMask() {
        if ($this->getDataInputMask() == DATA_INPUTMASK_YES) {
            return true;
        }
        return false;
    }
    
    function getStoreLocation($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_STORE_LOCATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getStoreLocation($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;        
        return $survey->getStoreLocation($default);
    }

    function setStoreLocation($text) {
        $this->setSettingValue(SETTING_DATA_STORE_LOCATION, $text);
    }
    
    function getStoreLocationExternal($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_STORE_LOCATION_EXTERNAL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getStoreLocationExternal($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getStoreLocationExternal($default);
    }

    function setStoreLocationExternal($text) {
        $this->setSettingValue(SETTING_DATA_STORE_LOCATION_EXTERNAL, $text);
    }

    function getOutputOptions() {
        $this->setOutputOptions();
        return $this->outputoptions;
    }

    private function setOutputOptions() {

        if ($this->outputoptions != null) {
            return $this->outputoptions;
        }

        $options = explode("\r\n", $this->getOutputOptionsText());
        foreach ($options as $option) {
            if (trim($option) == "") {
                continue;
            }

            $t = splitString("/ /", $option, PREG_SPLIT_NO_EMPTY, 2);
            $code = trim($t[0]);
            $labeltext = trim($t[1]);

            /* acronym */
            if (startsWith($labeltext, "(")) {
                $remainder = splitString("/(\(\w+\))/", $labeltext, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY, 2);
                if (sizeof($remainder) >= 2) {
                    if (contains($remainder[0], "(")) {
                        $acronym = trim(str_replace(")", "", str_replace("(", "", $remainder[0])));
                        $pos = strpos($labeltext, $remainder[0]);
                        $label = trim(substr($labeltext, $pos + strlen($remainder[0])));
                    } else {
                        $acronym = "";
                        $label = $labeltext;
                    }
                }
            } else {
                $acronym = "";
                $label = $labeltext;
            }

            $this->outputoptions[] = array("code" => $code, "label" => $label, "acronym" => $acronym);
            if ($this->maximumoptioncode == "" || $code > $this->maximumoptioncode) {
                $this->maximumoptioncode = $code;
            }
        }
    }

    function getOutputOptionsText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_OUTPUT_OPTIONS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOutputOptionsText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setOutputOptionsText($options) {
        $this->setSettingValue(SETTING_OUTPUT_OPTIONS, $options);
    }

    function getOutputEncrypted() {
        return $this->getSettingValue(SETTING_OUTPUT_ENCRYPTED);
    }

    function setOutputEncrypted($text) {
        $this->setSettingValue(SETTING_OUTPUT_ENCRYPTED, $text);
    }

    function isOutputEncrypted() {
        if ($this->getOutputEncrypted() == OUTPUT_ENCRYPTED_YES) {
            return true;
        }
        return false;
    }

    function getOutputValueLabelWidth($default = true) {
        /* variable level setting */
        $val = $this->getSettingValue(SETTING_OUTPUT_VALUELABEL_WIDTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOutputValueLabelWidth($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOutputValueLabelWidth($default);
    }

    function setOutputValueLabelWidth($text) {
        $this->setSettingValue(SETTING_OUTPUT_VALUELABEL_WIDTH, $text);
    }

    function getOutputSetOfEnumeratedBinary($default = true) {
        /* variable level setting */
        $val = $this->getSettingValue(SETTING_OUTPUT_SETOFENUMERATED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOutputSetOfEnumeratedBinary($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOutputSetOfEnumeratedBinary($default);
    }

    function setOutputSetOfEnumeratedBinary($text) {
        $this->setSettingValue(SETTING_OUTPUT_SETOFENUMERATED, $text);
    }

    /* validation functions */

    function getInlineExclusive($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INLINE_EXCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineExclusive($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInlineExclusive($default);
    }

    function isInlineExclusive($default = true) {
        return $this->getInlineExclusive($default) == INLINE_YES;
    }

    function setInlineExclusive($text) {

        $this->setSettingValue(SETTING_INLINE_EXCLUSIVE, $text);
    }

    function getInlineInclusive($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INLINE_INCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineInclusive($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInlineInclusive($default);
    }

    function isInlineInclusive($default = true) {
        return $this->getInlineInclusive($default) == INLINE_YES;
    }

    function setInlineInclusive($text) {

        $this->setSettingValue(SETTING_INLINE_INCLUSIVE, $text);
    }

    function getInlineMinimumRequired($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INLINE_MINIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineMinimumRequired($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInlineMinimumRequired($default);
    }

    function setInlineMinimumRequired($text) {
        $this->setSettingValue(SETTING_INLINE_MINIMUM_REQUIRED, $text);
    }

    function getInlineMaximumRequired($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INLINE_MAXIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineMaximumRequired($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInlineMaximumRequired($default);
    }

    function setInlineMaximumRequired($text) {

        $this->setSettingValue(SETTING_INLINE_MAXIMUM_REQUIRED, $text);
    }

    function getInlineExactRequired($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INLINE_EXACT_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineExactRequired($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInlineExactRequired($default);
    }

    function setInlineExactRequired($text) {

        $this->setSettingValue(SETTING_INLINE_EXACT_REQUIRED, $text);
    }

    function getIfEmpty($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_IFEMPTY, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getIfEmpty($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getIfEmpty($default);
    }

    function setIfEmpty($text) {
        $this->setSettingValue(SETTING_IFEMPTY, $text);
    }
    
    function getIfErrorGroup() {
        return $this->iferrorgroup;
    }
    
    function setIfErrorGroup($text) {
        $this->iferrorgroup = $text;
    }

    function getIfError($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_IFERROR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getIfError($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getIfError($default);
    }

    function setIfError($text) {
        $this->setSettingValue(SETTING_IFERROR, $text);
    }

    /* interactive functions */

    function getID($default = true) {
        return $this->getSettingValue(SETTING_ID, $default);
    }

    function setID($text) {
        $this->setSettingValue(SETTING_ID, $text);
    }

    function getScripts($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SCRIPTS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getScripts($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getScripts($default);
    }

    function setScripts($value) {
        $this->setSettingValue(SETTING_SCRIPTS, $value);
    }

    function getOnNext() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_NEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnNext($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnNext($default);
    }

    function setOnNext($value) {
        $this->setSettingValue(SETTING_ON_NEXT, $value);
    }

    function getOnBack() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_BACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnBack($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnBack($default);
    }

    function setOnBack($value) {
        $this->setSettingValue(SETTING_ON_BACK, $value);
    }

    function getOnDK() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_DK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnDK($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnDK($default);
    }

    function setOnDK($value) {
        $this->setSettingValue(SETTING_ON_DK, $value);
    }

    function getOnRF() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_RF, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnRF($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnRF($default);
    }

    function setOnRF($value) {
        $this->setSettingValue(SETTING_ON_RF, $value);
    }

    function getOnNA() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_NA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnNA($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnNA($default);
    }

    function setOnNA($value) {
        $this->setSettingValue(SETTING_ON_NA, $value);
    }

    function getOnUpdate() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_UPDATE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnUpdate($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnUpdate($default);
    }

    function setOnUpdate($value) {
        $this->setSettingValue(SETTING_ON_UPDATE, $value);
    }

    function getOnLanguageChange() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_LANGUAGE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnLanguageChange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnLanguageChange($default);
    }

    function setOnLanguageChange($value) {
        $this->setSettingValue(SETTING_ON_LANGUAGE_CHANGE, $value);
    }

    function getOnModeChange() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_MODE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnModeChange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnModeChange($default);
    }

    function setOnModeChange($value) {
        $this->setSettingValue(SETTING_ON_MODE_CHANGE, $value);
    }

    function getOnVersionChange() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ON_VERSION_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOnVersionChange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOnVersionChange($default);
    }

    function setOnVersionChange($value) {
        $this->setSettingValue(SETTING_ON_VERSION_CHANGE, $value);
    }
    
    
    function getClickNext() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_NEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getclickNext($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickNext($default);
    }

    function setClickNext($value) {
        $this->setSettingValue(SETTING_CLICK_NEXT, $value);
    }

    function getClickBack() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_BACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickBack($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickBack($default);
    }

    function setClickBack($value) {
        $this->setSettingValue(SETTING_CLICK_BACK, $value);
    }

    function getClickDK() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_DK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickDK($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickDK($default);
    }

    function setClickDK($value) {
        $this->setSettingValue(SETTING_CLICK_DK, $value);
    }

    function getClickRF() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_RF, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickRF($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickRF($default);
    }

    function setClickRF($value) {
        $this->setSettingValue(SETTING_CLICK_RF, $value);
    }

    function getClickNA() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_NA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickNA($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickNA($default);
    }

    function setClickNA($value) {
        $this->setSettingValue(SETTING_CLICK_NA, $value);
    }

    function getClickUpdate() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_UPDATE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickUpdate($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickUpdate($default);
    }

    function setClickUpdate($value) {
        $this->setSettingValue(SETTING_CLICK_UPDATE, $value);
    }

    function getClickLanguageChange() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_LANGUAGE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickLanguageChange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickLanguageChange($default);
    }

    function setClickLanguageChange($value) {
        $this->setSettingValue(SETTING_CLICK_LANGUAGE_CHANGE, $value);
    }

    function getClickModeChange() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_MODE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickModeChange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickModeChange($default);
    }

    function setClickModeChange($value) {
        $this->setSettingValue(SETTING_CLICK_MODE_CHANGE, $value);
    }

    function getClickVersionChange() {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLICK_VERSION_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getClickVersionChange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getClickVersionChange($default);
    }

    function setClickVersionChange($value) {
        $this->setSettingValue(SETTING_CLICK_VERSION_CHANGE, $value);
    }

    function getPageJavascript($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPageJavascript($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPageJavascript($default);
    }

    function setPageJavascript($text) {
        $this->setSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $text);
    }

    function getInlineJavascript($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_JAVASCRIPT_WITHIN_ELEMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineJavascript($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setInlineJavascript($text) {
        $this->setSettingValue(SETTING_JAVASCRIPT_WITHIN_ELEMENT, $text);
    }

    function getInlineStyle($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_STYLE_WITHIN_ELEMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInlineStyle($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setInlineStyle($text) {
        $this->setSettingValue(SETTING_STYLE_WITHIN_ELEMENT, $text);
    }

    function getPageStyle($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_STYLE_WITHIN_PAGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPageStyle($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPageStyle($default);
    }

    function setPageStyle($text) {
        $this->setSettingValue(SETTING_STYLE_WITHIN_PAGE, $text);
    }

    /* assistance functions */

    function getErrorPlacement($default = true) {

        $val = $this->getSettingValue(SETTING_ERROR_PLACEMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorPlacement($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorPlacement($default);
    }

    function setErrorPlacement($value) {
        $this->setSettingValue(SETTING_ERROR_PLACEMENT, $value);
    }

    function getPreText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PRETEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPreText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setPreText($text) {

        $this->setSettingValue(SETTING_PRETEXT, $text);
    }

    function getPostText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_POSTTEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPostText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setPostText($text) {

        $this->setSettingValue(SETTING_POSTTEXT, $text);
    }

    function getHoverText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HOVERTEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHoverText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setHoverText($text) {

        $this->setSettingValue(SETTING_HOVERTEXT, $text);
    }

    function getEmptyMessage($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_EMPTY_MESSAGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEmptyMessage($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEmptyMessage($default);
    }

    function setEmptyMessage($text) {

        $this->setSettingValue(SETTING_EMPTY_MESSAGE, $text);
    }

    function getErrorMessageInteger($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INTEGER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInteger($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInteger($default);
    }

    function setErrorMessageInteger($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INTEGER, $text);
    }

    function getErrorMessageDouble($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_DOUBLE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageDouble($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageDouble($default);
    }

    function setErrorMessageDouble($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_DOUBLE, $text);
    }

    function getErrorMessagePattern($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_PATTERN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessagePattern($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessagePattern($default);
    }

    function setErrorMessagePattern($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_PATTERN, $text);
    }

    function getErrorMessageRange($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageRange($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageRange($default);
    }

    function setErrorMessageRange($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_RANGE, $text);
    }

    function getErrorMessageMaximumCalendar($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageMaximumCalendar($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMaximumCalendar($default);
    }

    function setErrorMessageMaximumCalendar($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR, $text);
    }

    function getErrorMessageMinimumLength($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageMinimumLength($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMinimumLength($default);
    }

    function setErrorMessageMinimumLength($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH, $text);
    }

    function getErrorMessageMaximumLength($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageMaximumLength($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMaximumLength($default);
    }

    function setErrorMessageMaximumLength($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH, $text);
    }

    function getErrorMessageMinimumWords($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageMinimumWords($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMinimumWords($default);
    }

    function setErrorMessageMinimumWords($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS, $text);
    }

    function getErrorMessageMaximumWords($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageMaximumWords($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMaximumWords($default);
    }

    function setErrorMessageMaximumWords($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS, $text);
    }

    function getErrorMessageSelectMinimum($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageSelectMinimum($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSelectMinimum($default);
    }

    function setErrorMessageSelectMinimum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT, $text);
    }

    function getErrorMessageSelectMaximum($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageSelectMaximum($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSelectMaximum($default);
    }

    function setErrorMessageSelectMaximum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT, $text);
    }

    function getErrorMessageSelectExact($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageSelectExact($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSelectExact($default);
    }

    function setErrorMessageSelectExact($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXACT_SELECT, $text);
    }

    function getErrorMessageSelectInvalidSubset($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageSelectInvalidSubset($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSelectInvalidSubset($default);
    }

    function setErrorMessageSelectInvalidSubset($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT, $text);
    }

    function getErrorMessageSelectInvalidSet($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageSelectInvalidSet($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSelectInvalidSet($default);
    }

    function setErrorMessageSelectInvalidSet($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INVALID_SELECT, $text);
    }

    function getErrorMessageInlineExclusive($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInlineExclusive($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInlineExclusive($default);
    }

    function setErrorMessageInlineExclusive($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE, $value);
    }

    function getErrorMessageInlineInclusive($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInlineInclusive($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInlineInclusive($default);
    }

    function setErrorMessageInlineInclusive($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE, $value);
    }

    function getErrorMessageInlineMinimumRequired($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInlineMinimumRequired($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInlineMinimumRequired($default);
    }

    function setErrorMessageInlineMinimumRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED, $text);
    }

    function getErrorMessageInlineMaximumRequired($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInlineMaximumRequired($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInlineMaximumRequired($default);
    }

    function setErrorMessageInlineMaximumRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED, $text);
    }

    function getErrorMessageInlineExactRequired($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInlineExactRequired($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInlineExactRequired($default);
    }

    function setErrorMessageInlineExactRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED, $text);
    }

    function getErrorMessageInlineAnswered($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageInlineAnswered($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInlineAnswered($default);
    }

    function setErrorMessageInlineAnswered($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED, $text);
    }

    function getErrorMessageEnumeratedEntered($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageEnumeratedEntered($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageEnumeratedEntered($default);
    }

    function setErrorMessageEnumeratedEntered($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED, $text);
    }

    function getErrorMessageSetOfEnumeratedEntered($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageSetOFEnumeratedEntered($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSetOfEnumeratedEntered($default);
    }

    function setErrorMessageSetOfEnumeratedEntered($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED, $text);
    }

    function getErrorMessageComparisonEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonEqualTo($default);
    }

    function setErrorMessageComparisonEqualTo($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO, $value);
    }

    function getErrorMessageComparisonNotEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonNotEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonNotEqualTo($default);
    }

    function setErrorMessageComparisonNotEqualTo($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO, $value);
    }

    function getErrorMessageComparisonEqualToIgnoreCase($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonEqualToIgnoreCase($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonEqualToIgnoreCase($default);
    }

    function setErrorMessageComparisonEqualToIgnoreCase($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE, $value);
    }

    function getErrorMessageComparisonNotEqualToIgnoreCase($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonNotEqualToIgnoreCase($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonNotEqualToIgnoreCase($default);
    }

    function setErrorMessageComparisonNotEqualToIgnoreCase($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $value);
    }

    function getErrorMessageComparisonGreaterEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonGreaterEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonGreaterEqualTo($default);
    }

    function setErrorMessageComparisonGreaterEqualTo($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO, $value);
    }

    function getErrorMessageComparisonGreater($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonGreater($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonGreater($default);
    }

    function setErrorMessageComparisonGreater($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_GREATER, $value);
    }

    function getErrorMessageComparisonSmallerEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonSmallerEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonSmallerEqualTo($default);
    }

    function setErrorMessageComparisonSmallerEqualTo($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO, $value);
    }

    function getErrorMessageComparisonSmaller($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageComparisonSmaller($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonSmaller($default);
    }

    function setErrorMessageComparisonSmaller($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER, $value);
    }

    /* type functions */

    function getTyd() {
        return $this->variabledescriptive["tyd"];
    }

    function setTyd($tyd) {
        $this->variabledescriptive["tyd"] = $tyd;
    }

    function hasType() {
        if ($this->getTyd() > 0) {
            return true;
        }
        return false;
    }

    /* options functions */

    function getEnumeratedDisplay($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_ORIENTATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedDisplay($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedDisplay($default);
    }

    function setEnumeratedDisplay($value) {
        $this->setSettingValue(SETTING_ENUMERATED_ORIENTATION, $value);
    }

    function getEnumeratedCustom($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_CUSTOM, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedCustom($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedCustom($default);
    }

    function setEnumeratedCustom($value) {
        $this->setSettingValue(SETTING_ENUMERATED_CUSTOM, $value);
    }
    
    function getEnumeratedcolumns($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_COLUMNS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedColumns($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setEnumeratedColumns($value) {
        $this->setSettingValue(SETTING_ENUMERATED_COLUMNS, $value);
    }

    function getEnumeratedRandomizer($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_RANDOMIZER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedRandomizer($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setEnumeratedRandomizer($value) {
        $this->setSettingValue(SETTING_ENUMERATED_RANDOMIZER, $value);
    }

    function getEnumeratedOrder($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_ORDER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedOrder($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedOrder($default);
    }

    function setEnumeratedOrder($value) {
        $this->setSettingValue(SETTING_ENUMERATED_ORDER, $value);
    }

    function getEnumeratedSplit($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_SPLIT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedSplit($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedSplit($default);
    }

    function isEnumeratedSplit() {
        if ($this->getEnumeratedSplit() == ENUMERATED_YES) {
            return true;
        }
        return false;
    }

    function setEnumeratedSplit($value) {
        $this->setSettingValue(SETTING_ENUMERATED_SPLIT, $value);
    }

    function getEnumeratedBordered($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_BORDERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedBordered($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedBordered($default);
    }

    function isEnumeratedBordered() {
        if ($this->getEnumeratedBordered() == ENUMERATED_YES) {
            return true;
        }
        return false;
    }

    function setEnumeratedBordered($value) {
        $this->setSettingValue(SETTING_ENUMERATED_BORDERED, $value);
    }
    
    function getComboboxOptGroup($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DROPDOWN_OPTGROUP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComboboxOptGroup($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        return "";
    }

    function setComboboxOptGroup($value) {
        $this->setSettingValue(SETTING_DROPDOWN_OPTGROUP, $value);
    }
    
    function getHeaderAlignment($default = true) {

        $val = $this->getSettingValue(SETTING_HEADER_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHeaderAlignment($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHeaderAlignment($default);
    }

    function setHeaderAlignment($value) {
        $this->setSettingValue(SETTING_HEADER_ALIGNMENT, $value);
    }

    function getHeaderFormatting($default = true) {

        $val = $this->getSettingValue(SETTING_HEADER_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getHeaderFormatting($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHeaderFormatting($default);
    }

    function setHeaderFormatting($value) {
        $this->setSettingValue(SETTING_HEADER_FORMATTING, $value);
    }

    function getOptionsText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_OPTIONS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOptionsText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setOptionsText($options) {
        $this->setSettingValue(SETTING_OPTIONS, $options);
    }

    function getMaximumOptionCode() {
        return $this->maximumoptioncode;
    }

    private function setOptions() {

        if ($this->options != null) {
            return $this->options;
        }

        $options = explode("\r\n", $this->getOptionsText());
        foreach ($options as $option) {
            if (trim($option) == "") {
                continue;
            }

            $t = splitString("/ /", $option, PREG_SPLIT_NO_EMPTY, 2);
            $code = trim($t[0]);            
            if (isset($t[1])) {
                $labeltext = trim($t[1]);
            } else {
                $labeltext = '';
            }

            /* acronym */
            if (startsWith($labeltext, "(")) {
                $remainder = splitString("/(\(\w+\))/", $labeltext, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY, 2);
                if (sizeof($remainder) >= 2) {
                    if (contains($remainder[0], "(")) {
                        $acronym = trim(str_replace(")", "", str_replace("(", "", $remainder[0])));
                        $pos = strpos($labeltext, $remainder[0]);
                        $label = trim(substr($labeltext, $pos + strlen($remainder[0])));
                    } else {
                        $acronym = "";
                        $label = $labeltext;
                    }
                }
            } else {
                $acronym = "";
                $label = $labeltext;
            }

            $this->options[] = array("code" => $code, "label" => $label, "acronym" => $acronym);
            if ($this->maximumoptioncode == "" || $code > $this->maximumoptioncode) {
                $this->maximumoptioncode = $code;
            }
        }
    }

    function getOptionLabel($code) {
        $this->setOptions();
        foreach ($this->options as $option) {
            if ($option["code"] == $code) {
                return $option["label"];
            }
        }
    }

    function getSetOfEnumeratedOptionLabel($value) {

        $this->setOptions();
        $values = explode(SEPARATOR_SETOFENUMERATED, $value);
        //sort($values);

        $labels = array();
        foreach ($values as $v) {
            if (trim($v) != "") {
                $label = trim($this->getOptionLabel($v));
                if ($label != "") {
                    $labels[] = $label;
                }
            }
        }
        if (sizeof($labels) == 1) {
            return $labels[0];
        }
        return implode(", ", $labels);
    }
    
    function clearOptions() {
        $this->options = null;
        unset($this->options);
    }

    function getOptions() {
        $this->setOptions();
        return $this->options;
    }

    function getOptionCodeByAcronym($acronym) {
        $this->setOptions();
        foreach ($this->options as $option) {
            if (strtoupper($option["acronym"]) == strtoupper($acronym)) {
                return $option["code"];
            }
        }
        return "";
    }

    function getEnumeratedTextBox($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedTextBox($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedTextBox($default);
    }

    function isEnumeratedTextBox($default = true) {
        if ($this->getEnumeratedTextBox() == TEXTBOX_YES) {
            return true;
        }
        return false;
    }

    function setEnumeratedTextBox($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX, $value);
    }

    function getEnumeratedTextBoxLabel($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedTextBoxLabel($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedTextBoxLabel($default);
    }

    function setEnumeratedTextBoxLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, $value);
    }

    function getEnumeratedTextBoxPostText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedTextBoxPostText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedTextBoxPostText($default);
    }

    function setEnumeratedTextBoxPostText($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT, $value);
    }
    
    function getEnumeratedLabel($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedLabel($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedLabel($default);
    }

    function setEnumeratedLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_LABEL, $value);
    }
    
    function getEnumeratedClickLabel($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_CLICK_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getEnumeratedClickLabel($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedClickLabel($default);
    }
    
    function isEnumeratedClickLabel() {
        if ($this->getEnumeratedClickLabel() == CLICK_LABEL_YES) {
            return true;
        }
        return false;
    }

    function setEnumeratedClickLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_CLICK_LABEL, $value);
    }
    
    function getTableMobile($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_TABLE_MOBILE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTableMobile($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableMobile($default);
    }
    
    function isTableMobile() {
        if ($this->getTableMobile() == GROUP_YES) {
            return true;
        }
        return false;
    }

    function setTableMobile($text) {
        $this->setSettingValue(SETTING_TABLE_MOBILE, $text);
    }  
    
    function getTableMobileLabels($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_TABLE_MOBILE_LABELS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTableMobileLabels($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableMobileLabels($default);
    }
    
    function isTableMobileLabels() {
        if ($this->getTableMobileLabels() == MOBILE_LABEL_YES) {
            return true;
        }
        return false;
    }

    function setTableMobileLabels($text) {
        $this->setSettingValue(SETTING_TABLE_MOBILE_LABELS, $text);
    }

    /* comparison functions */

    function getComparisonEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonEqualTo($value) {
        $this->setSettingValue(SETTING_COMPARISON_EQUAL_TO, $value);
    }

    function getComparisonNotEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_NOT_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonNotEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonNotEqualTo($value) {
        $this->setSettingValue(SETTING_COMPARISON_NOT_EQUAL_TO, $value);
    }

    function getComparisonEqualToIgnoreCase($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonEqualToIgnoreCase($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonEqualToIgnoreCase($value) {
        $this->setSettingValue(SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE, $value);
    }

    function getComparisonNotEqualToIgnoreCase($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonNotEqualToIgnoreCase($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonNotEqualToIgnoreCase($value) {
        $this->setSettingValue(SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE, $value);
    }

    function getComparisonGreaterEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_GREATER_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonGreaterEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonGreaterEqualTo($value) {
        $this->setSettingValue(SETTING_COMPARISON_GREATER_EQUAL_TO, $value);
    }

    function getComparisonGreater($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_GREATER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonGreater($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonGreater($value) {
        $this->setSettingValue(SETTING_COMPARISON_GREATER, $value);
    }

    function getComparisonSmallerEqualTo($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_SMALLER_EQUAL_TO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonSmallerEqualTo($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonSmallerEqualTo($value) {
        $this->setSettingValue(SETTING_COMPARISON_SMALLER_EQUAL_TO, $value);
    }

    function getComparisonSmaller($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_COMPARISON_SMALLER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getComparisonSmaller($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setComparisonSmaller($value) {
        $this->setSettingValue(SETTING_COMPARISON_SMALLER, $value);
    }

    /* range functions */

    function getMinimum($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMinimum($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimum($default);
    }

    function setMinimum($value) {
        $this->setSettingValue(SETTING_MINIMUM_RANGE, $value);
    }

    function getMaximum($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMaximum($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximum($default);
    }

    function setMaximum($value) {
        $this->setSettingValue(SETTING_MAXIMUM_RANGE, $value);
    }

    function getOtherValues($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_OTHER_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getOtherValues($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getOtherValues($default);
    }

    function setOtherValues($value) {
        $this->setSettingValue(SETTING_OTHER_RANGE, $value);
    }

    /* set of enumerated functions */

    function getMinimumSelected($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMinimumSelected($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumSelected($default);
    }

    function getExactSelected($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_EXACT_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getExactSelected($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getExactSelected($default);
    }

    function getMaximumSelected($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMaximumSelected($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximumSelected($default);
    }

    function getInvalidSelected($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INVALID_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInvalidSelected($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInvalidSelected();
    }

    function getInvalidSubSelected($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INVALIDSUB_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInvalidSubSelected($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInvalidSubSelected($default);
    }

    function setMinimumSelected($value) {
        $this->setSettingValue(SETTING_MINIMUM_SELECTED, $value);
    }

    function setExactSelected($value) {
        $this->setSettingValue(SETTING_EXACT_SELECTED, $value);
    }

    function setMaximumSelected($value) {
        $this->setSettingValue(SETTING_MAXIMUM_SELECTED, $value);
    }

    function setInvalidSelected($value) {
        $this->setSettingValue(SETTING_INVALID_SELECTED, $value);
    }

    function setInvalidSubSelected($value) {
        $this->setSettingValue(SETTING_INVALIDSUB_SELECTED, $value);
    }
    
    
    /* ranker functions */
    
    function getRankColumn($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_RANK_COLUMN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getRankColumn($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getRankColumn($default);
    }

    function setRankColumn($value) {
        $this->setSettingValue(SETTING_RANK_COLUMN, $value);
    }

    function getMinimumRanked($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_RANKED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMinimumRanked($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumRanked($default);
    }

    function getExactRanked($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_EXACT_RANKED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getExactRanked($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getExactRanked($default);
    }

    function getMaximumRanked($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_RANKED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMaximumRanked($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximumRanked($default);
    }
    
    function setMinimumRanked($value) {
        $this->setSettingValue(SETTING_MINIMUM_RANKED, $value);
    }

    function setExactRanked($value) {
        $this->setSettingValue(SETTING_EXACT_RANKED, $value);
    }

    function setMaximumRanked($value) {
        $this->setSettingValue(SETTING_MAXIMUM_RANKED, $value);
    }

    /* date functions */

    function getMaximumDatesSelected($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_CALENDAR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMaximumDatesSelected($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximumDatesSelected($default);
    }

    function setMaximumDatesSelected($value) {
        $this->setSettingValue(SETTING_MAXIMUM_CALENDAR, $value);
    }

    /* string and open functions */

    function getPattern($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PATTERN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPattern($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPattern($default);
    }

    function getMinimumLength($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMinimumLength($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        if ($this->getAnswerType() == ANSWER_TYPE_OPEN) {
            return $survey->getMinimumOpenLength($default);
        } else {
            return $survey->getMinimumLength($default);
        }
    }

    function getMaximumLength($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMaximumLength($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        if ($this->getAnswerType() == ANSWER_TYPE_OPEN) {
            return $survey->getMaximumOpenLength($default);
        } else {
            return $survey->getMaximumLength($default);
        }
    }

    function getMinimumWords($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMinimumWords($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumWords($default);
    }

    function getMaximumWords($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getMaximumWords($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximumWords($default);
    }

    function setPattern($value) {
        $this->setSettingValue(SETTING_PATTERN, $value);
    }

    function setInputMask($value) {
        $this->setSettingValue(SETTING_INPUT_MASK, $value);
    }

    function setInputMaskCustom($value) {
        $this->setSettingValue(SETTING_INPUT_MASK_CUSTOM, $value);
    }

    function setInputMaskEnabled($value) {
        $this->setSettingValue(SETTING_INPUT_MASK_ENABLED, $value);
    }

    function setInputMaskPlaceholder($value) {
        $this->setSettingValue(SETTING_INPUT_MASK_PLACEHOLDER, $value);
    }

    function setMinimumLength($value) {
        $this->setSettingValue(SETTING_MINIMUM_LENGTH, $value);
    }

    function setMaximumLength($value) {
        $this->setSettingValue(SETTING_MAXIMUM_LENGTH, $value);
    }

    function setMinimumWords($value) {
        $this->setSettingValue(SETTING_MINIMUM_WORDS, $value);
    }

    function setMaximumWords($value) {
        $this->setSettingValue(SETTING_MAXIMUM_WORDS, $value);
    }

    /* input mask functions */

    function getInputMask($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInputMask($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInputMask($default);
    }

    function getInputMaskCustom($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_CUSTOM, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInputMaskCustom($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function getInputMaskEnabled($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_ENABLED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInputMaskEnabled($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInputMaskEnabled($default);
    }

    function isInputMaskEnabled() {
        $arr = $this->getInputMaskEnabled();
        if ($arr == INPUT_MASK_YES) {
            return true;
        }
        return false;
    }

    function getInputMaskPlaceholder($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_PLACEHOLDER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInputMaskPlaceholder($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInputMaskPlaceholder($default);
    }
    
    function getInputMaskCallback($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_CALLBACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getInputMaskCallback($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* no survey level setting */
        return "";
    }

    function setInputMaskCallback($text) {
        $this->setSettingValue(SETTING_INPUT_MASK_CALLBACK, $text);
    }

    /* datetime picker functions */

    function getDateFormat($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATE_FORMAT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDateFormat($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDateFormat($default);
    }

    function setDateFormat($value) {
        $this->setSettingValue(SETTING_DATE_FORMAT, $value);
    }

    function getTimeFormat($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_TIME_FORMAT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTimeFormat($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTimeFormat($default);
    }

    function setTimeFormat($value) {
        $this->setSettingValue(SETTING_TIME_FORMAT, $value);
    }

    function getDateTimeFormat($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATETIME_FORMAT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDateTimeFormat($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDateTimeFormat($default);
    }

    function setDateTimeFormat($value) {
        $this->setSettingValue(SETTING_DATETIME_FORMAT, $value);
    }
    
    function getDateDefaultView($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATE_DEFAULT_VIEW, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDateDefaultView($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDateDefaultView($default);
    }

    function setDateDefaultView($value) {
        $this->setSettingValue(SETTING_DATE_DEFAULT_VIEW, $value);
    }
    
    function getDateTimeCollapse($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATETIME_COLLAPSE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDateTimeCollapse($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDateTimeCollapse($default);
    }

    function setDateTimeCollapse($value) {
        $this->setSettingValue(SETTING_DATETIME_COLLAPSE, $value);
    }
    
    function getDateTimeSideBySide($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATETIME_SIDE_BY_SIDE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getDateTimeSideBySide($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getDateTimeSideBySide($default);
    }

    function setDateTimeSideBySide($value) {
        $this->setSettingValue(SETTING_DATETIME_SIDE_BY_SIDE, $value);
    }
    

    /* access functions */

    function getAccessReturnAfterCompletionAction($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAccessReturnAfterCompletionAction($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getAccessReturnAfterCompletionAction($default);
    }

    function setAccessReturnAfterCompletionAction($value) {
        $this->setSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $value);
    }

    function getAccessReturnAfterCompletionRedoPreload($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAccessReturnAfterCompletionRedoPreload($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getAccessReturnAfterCompletionRedoPreload($default);
    }

    function setAccessReturnAfterCompletionRedoPreload($value) {
        $this->setSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $value);
    }

    function getAccessReentryAction($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_REENTRY_ACTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAccessReentryAction($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getAccessReentryAction($default);
    }

    function setAccessReentryAction($value) {
        $this->setSettingValue(SETTING_ACCESS_REENTRY_ACTION, $value);
    }

    function getAccessReentryRedoPreload($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAccessReentryRedoPreload($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getAccessReentryRedoPreload($default);
    }

    function setAccessReentryRedoPreload($value) {
        $this->setSettingValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $value);
    }

    /* overall display functions */

    function getPlaceholder($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PLACEHOLDER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPlaceholder($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPlaceholder($default);
    }

    function setPlaceholder($value) {
        $this->setSettingValue(SETTING_PLACEHOLDER, $value);
    }

    function getPageHeader($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PAGE_HEADER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPageHeader($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPageHeader($default);
    }

    function setPageHeader($value) {
        $this->setSettingValue(SETTING_PAGE_HEADER, $value);
    }

    function getPageFooter($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PAGE_FOOTER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getPageFooter($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPageFooter($default);
    }

    function setPageFooter($value) {
        $this->setSettingValue(SETTING_PAGE_FOOTER, $value);
    }

    function getQuestionAlignment($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_QUESTION_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getQuestionAlignment($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getQuestionAlignment($default);
    }

    function setQuestionAlignment($value) {
        $this->setSettingValue(SETTING_QUESTION_ALIGNMENT, $value);
    }

    function getQuestionFormatting($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_QUESTION_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getQuestionFormatting($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getQuestionFormatting($default);
    }

    function setQuestionFormatting($value) {
        $this->setSettingValue(SETTING_QUESTION_FORMATTING, $value);
    }

    function getAnswerAlignment($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ANSWER_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAnswerAlignment($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getAnswerAlignment($default);
    }

    function setAnswerAlignment($value) {
        $this->setSettingValue(SETTING_ANSWER_ALIGNMENT, $value);
    }

    function getAnswerFormatting($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ANSWER_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getAnswerFormatting($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getAnswerFormatting($default);
    }

    function setAnswerFormatting($value) {
        $this->setSettingValue(SETTING_ANSWER_FORMATTING, $value);
    }

    function getButtonAlignment($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_BUTTON_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getButtonAlignment($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getButtonAlignment($default);
    }

    function setButtonAlignment($value) {
        $this->setSettingValue(SETTING_BUTTON_ALIGNMENT, $value);
    }

    function getButtonFormatting($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_BUTTON_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getButtonFormatting($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getButtonFormatting($default);
    }

    function setButtonFormatting($value) {
        $this->setSettingValue(SETTING_BUTTON_FORMATTING, $value);
    }
    
    function getXiTemplate($default = true) {
        
        /* variable level setting */
        $val = $this->getSettingValue(SETTING_GROUP_XI_TEMPLATE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getXiTemplate($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }
        return "";
    }

    function setXiTemplate($text) {
        $this->setSettingValue(SETTING_GROUP_XI_TEMPLATE, $text);
    }

    /* button functions */

    function getShowBackButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_BACK_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowBackButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowBackButton($default);
    }

    function getShowNextButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_NEXT_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowNextButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowNextButton($default);
    }

    function getShowDKButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DK_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowDKButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowDKButton($default);
    }

    function getShowRFButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_RF_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowRFButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowRFButton($default);
    }

    function getShowUpdateButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_UPDATE_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowUpdateButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowUpdateButton($default);
    }

    function getShowNAButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_NA_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowNAButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowNAButton($default);
    }

    function getShowRemarkButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_REMARK_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowRemarkButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowRemarkButton($default);
    }

    function getShowRemarkSaveButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_REMARK_SAVE_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowRemarkSaveButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowRemarkSaveButton($default);
    }

    function getShowCloseButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLOSE_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowCloseButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowCloseButton($default);
    }

    function setShowBackButton($value) {

        $this->setSettingValue(SETTING_BACK_BUTTON, $value);
    }

    function setShowNextButton($value) {
        $this->setSettingValue(SETTING_NEXT_BUTTON, $value);
    }

    function setShowDKButton($value) {
        $this->setSettingValue(SETTING_DK_BUTTON, $value);
    }

    function setShowRFButton($value) {
        $this->setSettingValue(SETTING_RF_BUTTON, $value);
    }

    function setShowUpdateButton($value) {
        $this->setSettingValue(SETTING_UPDATE_BUTTON, $value);
    }

    function setShowNAButton($value) {
        $this->setSettingValue(SETTING_NA_BUTTON, $value);
    }

    function setShowRemarkButton($value) {
        $this->setSettingValue(SETTING_REMARK_BUTTON, $value);
    }

    function setShowRemarkSaveButton($value) {
        $this->setSettingValue(SETTING_REMARK_SAVE_BUTTON, $value);
    }

    function setShowCloseButton($value) {
        $this->setSettingValue(SETTING_CLOSE_BUTTON, $value);
    }

    function getLabelBackButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_BACK_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelBackButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelBackButton($default);
    }

    function getLabelNextButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_NEXT_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelNextButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelNextButton($default);
    }

    function getLabelDKButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DK_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelDKButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelDKButton($default);
    }

    function getLabelRFButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_RF_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelRFButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelRFButton($default);
    }

    function getLabelUpdateButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_UPDATE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelUpdateButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelUpdateButton($default);
    }

    function getLabelNAButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_NA_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelNAButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelNAButton($default);
    }

    function getLabelRemarkButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_REMARK_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelRemarkButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelRemarkButton($default);
    }

    function getLabelRemarkSaveButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_REMARK_SAVE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelRemarkSaveButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelRemarkSaveButton($default);
    }

    function getLabelCloseButton($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_CLOSE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getLabelCloseButton($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelCloseButton($default);
    }

    function setLabelBackButton($value) {
        $this->setSettingValue(SETTING_BACK_BUTTON_LABEL, $value);
    }

    function setLabelNextButton($value) {
        $this->setSettingValue(SETTING_NEXT_BUTTON_LABEL, $value);
    }

    function setLabelDKButton($value) {
        $this->setSettingValue(SETTING_DK_BUTTON_LABEL, $value);
    }

    function setLabelRFButton($value) {
        $this->setSettingValue(SETTING_RF_BUTTON_LABEL, $value);
    }

    function setLabelUpdateButton($value) {
        $this->setSettingValue(SETTING_UPDATE_BUTTON_LABEL, $value);
    }

    function setLabelNAButton($value) {
        $this->setSettingValue(SETTING_NA_BUTTON_LABEL, $value);
    }

    function setLabelRemarkButton($value) {
        $this->setSettingValue(SETTING_REMARK_BUTTON_LABEL, $value);
    }

    function setLabelRemarkSaveButton($value) {
        $this->setSettingValue(SETTING_REMARK_SAVE_BUTTON_LABEL, $value);
    }

    function setLabelCloseButton($value) {
        $this->setSettingValue(SETTING_CLOSE_BUTTON_LABEL, $value);
    }
    
    /* spinner functions */    
    function getSpinner($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SPINNER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSpinner($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSpinner($default);
    }
    
    function isSpinner() {
        if ($this->getSpinner() == SPINNER_YES) {
            return true;
        }
        return false;
    }

    function setSpinner($value) {
        $this->setSettingValue(SETTING_SPINNER, $value);
    }
    
    function getSpinnerType($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_TYPE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSpinnerType($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSpinnerType($default);
    }

    function setSpinnerType($value) {
        $this->setSettingValue(SETTING_SPINNER_TYPE, $value);
    }
    
    function getSpinnerUp($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_UP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSpinnerUp($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSpinnerUp($default);
    }

    function setSpinnerUp($value) {
        $this->setSettingValue(SETTING_SPINNER_UP, $value);
    }
    
    function getSpinnerDown($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_DOWN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSpinnerDown($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSpinnerDown($default);
    }

    function setSpinnerDown($value) {
        $this->setSettingValue(SETTING_SPINNER_DOWN, $value);
    }
    
    function getSpinnerIncrement($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_STEP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSpinnerIncrement($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSpinnerIncrement($default);
    }

    function setSpinnerIncrement($value) {
        $this->setSettingValue(SETTING_SPINNER_STEP, $value);
    }
    
    function getTextboxManual($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_TEXTBOX_MANUAL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTextboxManual($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextboxManual($default);
    }
    
    function isTextboxManual() {
        if ($this->getTextboxManual() == MANUAL_YES) {
            return true;
        }
        return false;
    }

    function setTextboxManual($value) {
        $this->setSettingValue(SETTING_TEXTBOX_MANUAL, $value);
    }
    
    /* knob functions */    
    function getKnobRotation($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KNOB_ROTATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKnobRotation($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKnobRotation($default);
    }

    function setKnobRotation($value) {
        $this->setSettingValue(SETTING_KNOB_ROTATION, $value);
    }

    /* slider functions */
    function getSliderPreSelection($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_PRESELECTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSliderPreSelection($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ 
        if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSliderPreSelection($default);
    }

    function setSliderPreSelection($value) {
        $this->setSettingValue(SETTING_SLIDER_PRESELECTION, $value);
    }
    
    function getSliderFormater($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_FORMATER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSliderFormater($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ 
        if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSliderFormater($default);
    }

    function setSliderFormater($value) {
        $this->setSettingValue(SETTING_SLIDER_FORMATER, $value);
    }
    
    function getTooltip($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TOOLTIP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTooltip($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getToolTip($default);
    }

    function setTooltip($value) {
        $this->setSettingValue(SETTING_SLIDER_TOOLTIP, $value);
    }

    function getSliderOrientation($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_ORIENTATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSliderOrientation($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSliderOrientation($default);
    }

    function setSliderOrientation($value) {
        $this->setSettingValue(SETTING_SLIDER_ORIENTATION, $value);
    }

    function getTextBox($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TEXTBOX, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTextBox($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextBox($default);
    }

    function isTextBox($default = true) {
        if ($this->getTextBox() == TEXTBOX_YES) {
            return true;
        }
        return false;
    }

    function setTextBox($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX, $value);
    }

    function getTextBoxLabel($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTextBoxLabel($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextBoxLabel($default);
    }

    function setTextBoxLabel($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $value);
    }
    
    function getTextBoxPostText($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TEXTBOX_POSTTEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getTextBoxPostText($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextboxPostText($default);
    }

    function setTextBoxPostText($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX_POSTTEXT, $value);
    }

    function getSliderLabelPlacement($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_LABEL_PLACEMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSliderLabelPlacement($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSliderLabelPlacement($default);
    }

    function setSliderLabelPlacement($value) {
        $this->setSettingValue(SETTING_SLIDER_LABEL_PLACEMENT, $value);
    }

    function getSliderLabels($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_LABELS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getSliderLabels($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        return '';
    }

    function setSliderLabels($value) {
        $this->setSettingValue(SETTING_SLIDER_LABELS, $value);
    }

    function getIncrement($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_INCREMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getIncrement($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getIncrement($default);
    }

    function setIncrement($value) {
        $this->setSettingValue(SETTING_SLIDER_INCREMENT, $value);
    }

    /* progressbar functions */

    function getShowProgressBar($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_SHOW, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowProgressBar($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowProgressBar($default);
    }

    function setShowProgressBar($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_SHOW, $value);
    }

    function getProgressBarFillColor($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_FILLED_COLOR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getProgressBarFillColor($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getProgressBarFillColor($default);
    }

    function setProgressBarFillColor($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_FILLED_COLOR, $value);
    }

    function getProgressBarRemainColor($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_REMAIN_COLOR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getProgressBarRemainColor($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getProgressBarRemainColor($default);
    }

    function setProgressBarRemainColor($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_REMAIN_COLOR, $value);
    }

    function getProgressBarWidth($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_WIDTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getProgressBarWidth($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getProgressBarWidth($default);
    }

    function setProgressBarWidth($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_WIDTH, $value);
    }

    function getProgressBarValue($default = true) {
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_VALUE, $default);
        if ($val != "") {
            return $val;
        }
        return "";
    }

    function setProgressBarValue($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_VALUE, $value);
    }

    /* fill functions */

    function getFillText() {
        return $this->getSettingValue(SETTING_FILLTEXT);
    }

    function getFillTextByLine($line) {
        $this->fillines = explode("\r\n", $this->getFillText());
        if (isset($this->fillines[$line - 1])) {
            return $this->fillines[$line - 1];
        }

        return "";
    }

    function setFillText($text) {
        $this->setSettingValue(SETTING_FILLTEXT, $text);
    }

    function getFillCode($default = true) {
        return $this->getSettingValue(SETTING_FILLCODE, $default);
    }

    function setFillCode($text) {
        $this->setSettingValue(SETTING_FILLCODE, $text);
    }
    
    /* check functions */
    
    function getCheckText() {
        return $this->getSettingValue(SETTING_CHECKTEXT);
    }

    function getCheckTextByLine($line) {
        $this->checklines = explode("\r\n", $this->getCheckText());
        if (isset($this->checklines[$line - 1])) {
            return $this->checklines[$line - 1];
        }

        return "";
    }

    function setCheckText($text) {
        $this->setSettingValue(SETTING_CHECKTEXT, $text);
    }
    
    function getCheckCode($default = true) {
        return $this->getSettingValue(SETTING_CHECKCODE, $default);
    }

    function setCheckCode($text) {
        $this->setSettingValue(SETTING_CHECKCODE, $text);
    }

    /* keyboard binding functions isKeyboardBinding */

    function getKeyboardBindingEnabled($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_ENABLED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingEnabled($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingEnabled($default);
    }

    function isKeyboardBindingEnabled() {
        $arr = $this->getKeyboardBindingEnabled();
        if ($arr == KEYBOARD_BINDING_YES) {
            return true;
        }
        return false;
    }

    function setKeyboardBindingEnabled($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_ENABLED, $value);
    }

    function getKeyboardBindingBack($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_BACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingBack($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingBack($default);
    }

    function getKeyboardBindingNext($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_NEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingNext($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingNext($default);
    }

    function getKeyboardBindingDK($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_DK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingDK($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingDK($default);
    }

    function getKeyboardBindingRF($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_RF, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingRF($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingRF($default);
    }

    function getKeyboardBindingNA($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_NA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingNA($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingNA($default);
    }

    function getKeyboardBindingUpdate($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_UPDATE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingUpdate($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingUpdate($default);
    }

    function getKeyboardBindingRemark($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_REMARK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingRemark($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingRemark($default);
    }

    function getKeyboardBindingClose($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_CLOSE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getKeyboardBindingClose($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getKeyboardBindingClose($default);
    }

    function setKeyboardBindingBack($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_BACK, $value);
    }

    function setKeyboardBindingNext($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_NEXT, $value);
    }

    function setKeyboardBindingDK($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_DK, $value);
    }

    function setKeyboardBindingRF($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_RF, $value);
    }

    function setKeyboardBindingNA($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_NA, $value);
    }

    function setKeyboardBindingUpdate($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_UPDATE, $value);
    }

    function setKeyboardBindingRemark($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_REMARK, $value);
    }

    function setKeyboardBindingClose($value) {
        $this->setSettingValue(SETTING_KEYBOARD_BINDING_CLOSE, $value);
    }

    function getIndividualDKRFNA($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DKRFNA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getIndividualDKRFNA($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getIndividualDKRFNA($default);
    }

    function isIndividualDKRFNA() {
        $arr = $this->getIndividualDKRFNA();
        if ($arr == INDIVIDUAL_DKRFNA_YES) {
            return true;
        }
        return false;
    }

    function setIndividualDKRFNA($value) {
        $this->setSettingValue(SETTING_DKRFNA, $value);
    }

    function getIndividualDKRFNAInline($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DKRFNA_INLINE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getIndividualDKRFNAInline($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getIndividualDKRFNAInline($default);
    }

    function isIndividualDKRFNAInline() {
        $arr = $this->getIndividualDKRFNAInline();
        if ($arr == INDIVIDUAL_DKRFNA_YES) {
            return true;
        }
        return false;
    }

    function setIndividualDKRFNAInline($value) {
        $this->setSettingValue(SETTING_DKRFNA_INLINE, $value);
    }

    /* section header/footer functions */

    function getShowSectionHeader($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SHOW_SECTION_HEADER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowSectionHeader($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowSectionHeader($default);
    }

    function isShowSectionHeader() {
        $arr = $this->getShowSectionHeader();
        if ($arr == SECTIONHEADER_YES) {
            return true;
        }
        return false;
    }

    function setShowSectionHeader($text) {
        $this->setSettingValue(SETTING_SHOW_SECTION_HEADER, $text);
    }

    function getShowSectionFooter($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SHOW_SECTION_FOOTER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getShowSectionFooter($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowSectionFooter($default);
    }

    function isShowSectionFooter() {
        $arr = $this->getShowSectionFooter();
        if ($arr == SECTIONFOOTER_YES) {
            return true;
        }
        return false;
    }

    function setShowSectionFooter($text) {
        $this->setSettingValue(SETTING_SHOW_SECTION_FOOTER, $text);
    }

    /* translation functions */

    function isTranslated() {
        if ($this->isTranslatedGeneral() == false) {
            return false;
        }
        if ($this->isTranslatedLayout() == false) {
            return false;
        }
        if ($this->isTranslatedAssistance() == false) {
            return false;
        }
        if ($this->isTranslatedFill() == false) {
            return false;
        }
        return true;
    }

    function isTranslatedGeneral() {
        $arr = array(
            SETTING_QUESTION,
            SETTING_OPTIONS
        );

        global $survey;
        $mode = getSurveyMode();
        $default = $survey->getDefaultLanguage($mode);
        foreach ($arr as $a) {
            $s = $this->getSetting($a, false);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedFill() {
        $arr = array(
            SETTING_FILLTEXT
        );

        global $survey;
        $mode = getSurveyMode();
        $default = $survey->getDefaultLanguage($mode);
        foreach ($arr as $a) {
            $s = $this->getSetting($a, false);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedLayout() {
        $arr = array(
            SETTING_BACK_BUTTON_LABEL,
            SETTING_NEXT_BUTTON_LABEL,
            SETTING_DK_BUTTON_LABEL,
            SETTING_RF_BUTTON_LABEL,
            SETTING_UPDATE_BUTTON_LABEL,
            SETTING_NA_BUTTON_LABEL,
            SETTING_REMARK_BUTTON_LABEL,
            SETTING_CLOSE_BUTTON_LABEL
        );

        global $survey;
        $mode = getSurveyMode();
        $default = $survey->getDefaultLanguage($mode);
        foreach ($arr as $a) {
            $s = $this->getSetting($a, false);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }

    function isTranslatedAssistance() {
        $arr = array(
            SETTING_EMPTY_MESSAGE,
            SETTING_ERROR_MESSAGE_MINIMUM_LENGTH,
            SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH,
            SETTING_ERROR_MESSAGE_MINIMUM_WORDS,
            SETTING_ERROR_MESSAGE_MAXIMUM_WORDS,
            SETTING_ERROR_MESSAGE_PATTERN,
            SETTING_ERROR_MESSAGE_DOUBLE,
            SETTING_ERROR_MESSAGE_INTEGER,
            SETTING_ERROR_MESSAGE_MINIMUM_SELECT,
            SETTING_ERROR_MESSAGE_MAXIMUM_SELECT,
            SETTING_ERROR_MESSAGE_EXACT_SELECT,
            SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT,
            SETTING_ERROR_MESSAGE_INVALID_SELECT,
            SETTING_ERROR_MESSAGE_RANGE,
            SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR,
            SETTING_ERROR_MESSAGE_INLINE_ANSWERED,
            SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED,
            SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE,
            SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE,
            SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_EXACT_REQUIRED,
            SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_EXCLUSIVE,
            SETTING_ERROR_MESSAGE_INCLUSIVE,
            SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED
        );

        global $survey;
        $mode = getSurveyMode();
        $default = $survey->getDefaultLanguage($mode);
        foreach ($arr as $a) {
            $s = $this->getSetting($a, false);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }
    
    function getValidateAssignment($default = true) {
        if ($this->getSettingValue(SETTING_VALIDATE_ASSIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_VALIDATE_ASSIGNMENT, $default);
        }
        
        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getValidateAssignment($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ 
        if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getValidateAssignment($default);
    }
    
    function isValidateAssignment() {
        if ($this->getValidateAssignment() == VALIDATE_ASSIGNMENT_YES) {
            return true;
        }
        return false;
    }
    
    function setValidateAssignment($value) {
        $this->setSettingValue(SETTING_VALIDATE_ASSIGNMENT, $value);
    }

    
    function getErrorMessageRankMinimum($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageRankMinimum($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageRankMinimum($default);
    }

    function setErrorMessageRankMinimum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK, $text);
    }

    function getErrorMessageRankMaximum($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageRankMaximum($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageRankMaximum($default);
    }

    function setErrorMessageRankMaximum($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK, $text);
    }

    function getErrorMessageRankExact($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_RANK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
            return $val;
        }

        /* type level setting */
        if ($val != SETTING_FOLLOW_GENERIC && $this->hasType()) {
            if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
                return SETTING_FOLLOW_TYPE;
            }
            $val = $this->type->getErrorMessageRankExact($default);
            if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
                return $val;
            }
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageRankExact($default);
    }

    function setErrorMessageRankExact($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXACT_RANK, $text);
    }
    
    /* generic functions */

    function move($suid, $seid, $copy = false) {

        global $db;
        if (!isset($this->variabledescriptive['vsid'])) {
            return;
        }

        $query = "select max(vsid) as max from " . Config::dbSurvey() . "_variables";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $vsid = $row["max"] + 1;
        $oldsuid = $this->getSuid();
        $this->remove();

        /* set position */
        $query = "select max(position) as max from " . Config::dbSurvey() . "_variables where suid=" . $this->getSuid() . " and seid=" . $this->getSeid();
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $pos = $row["max"] + 1;
        $this->setPosition($pos);

        $this->setObjectName($vsid);
        $this->setVsid($vsid);
        $this->setSuid($suid);
        $this->setSeid($seid);        
        
        // check for type
        if ($this->hasType()) {
            if ($oldsuid != $suid) {        
                $this->type->copy($suid);
                $this->setTyd($this->type->getTyd());
            }
        }
        
        $this->save();
    }

    function remove() {

        global $db;
        if (!isset($this->variabledescriptive['vsid'])) {
            return;
        }

        $query = "delete from " . Config::dbSurvey() . "_variables where suid = " . prepareDatabaseString($this->getSuid()) . " and vsid = " . prepareDatabaseString($this->getVsid());
        $db->executeQuery($query);

        $query = "delete from " . Config::dbSurvey() . "_settings where suid = " . prepareDatabaseString($this->getSuid()) . " and object = " . prepareDatabaseString($this->getObjectName()) . " and objecttype = " . prepareDatabaseString($this->getObjectType());
        $db->executeQuery($query);
    }

    function copy($newname, $newsuid = "", $newseid = "", $types = true) {
        global $db;
        $query = "select max(vsid) as max from " . Config::dbSurvey() . "_variables";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $vsid = $row["max"] + 1;
        $oldsuid = $this->getSuid();
        $this->setObjectName($vsid);
        $this->setVsid($vsid);
        $this->setName($newname);
        if ($newsuid != "") {
            $this->setSuid($newsuid);
        }
        if ($newseid != "") {
            $this->setSeid($newseid);
        }

        /* set position */
        $query = "select max(position) as max from " . Config::dbSurvey() . "_variables where suid=" . $this->getSuid() . " and seid=" . $this->getSeid();
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $pos = $row["max"] + 1;
        $this->setPosition($pos);
        
        // check for type
        if ($types == true && $this->hasType()) {
            if ($newsuid != "" && $oldsuid != $newsuid) {        
                $this->type->copy($newsuid);
                $this->setTyd($this->type->getTyd());
            }
        }
        
        $this->save();
    }

    function save() {

        global $db;
        if (!isset($this->variabledescriptive['vsid'])) {
            $query = "select max(vsid) as max from " . Config::dbSurvey() . "_variables";
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $vsid = $row["max"] + 1;
            $this->setObjectName($vsid);
            $this->setVsid($vsid);

            /* set position */
            $query = "select max(position) as max from " . Config::dbSurvey() . "_variables where suid=" . $this->getSuid() . " and seid=" . $this->getSeid();
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $pos = $row["max"] + 1;
            $this->setPosition($pos);
        }

        $query = "replace into " . Config::dbSurvey() . "_variables (suid, vsid, seid, variablename, position, tyd) values(";
        $query .= prepareDatabaseString($this->getSuid()) . ",";
        $query .= prepareDatabaseString($this->getVsid()) . ",";
        $query .= prepareDatabaseString($this->getSeid()) . ",";
        $query .= "'" . prepareDatabaseString($this->getName()) . "',";
        $order = $this->getPosition();
        if ($order == "") {
            $order = 1;
        }
        $query .= prepareDatabaseString($order) . ",";
        $tyd = $this->getTyd();
        if ($tyd == "") {
            $tyd = -1;
        }
        $query .= prepareDatabaseString($tyd) . "";

        $query .= ")";
        $db->executeQuery($query);

        /* save settings */
        $settings = $this->getSettingsArray();
        foreach ($settings as $key => $setting) {
            $setting->setObject($this->getVsid());
            $setting->setSuid($this->getSuid());
            $setting->save();
        }
    }

}

?>