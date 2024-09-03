<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Type extends Component {

    private $type;
    private $options;
    private $range;
    private $minimum;
    private $maximum;

    function __construct($nameOrRow = "") {
        $this->setObjectType(OBJECT_TYPE);
        if (is_array($nameOrRow)) {
            $this->type = $nameOrRow;
            $this->setSuid($this->type["suid"]);
            $this->setObjectName($this->getTyd());

            /* add settings */
            $this->addSettings($this->getSettings());
        }
    }

    function getTyd() {
        if (isset($this->type['tyd'])) {
            return $this->type['tyd'];
        }
        return "";
    }

    function setTyd($tyd) {
        $this->type["tyd"] = $tyd;
    }

    function getName() {
        if (isset($this->type["name"])) {
	        return $this->type["name"];
	 }
	 return "";

    }

    function setName($name) {
        $this->type["name"] = $name;
        $this->setSettingValue(SETTING_NAME, $name);
    }

    function getDescription($default = true) {
        return $this->getSettingValue(SETTING_DESCRIPTION, $default);
    }

    function setDescription($description) {
        $this->setSettingValue(SETTING_DESCRIPTION, $description);
    }

    function getAnswerType($default = true) {
        return $this->getSettingValue(SETTING_ANSWERTYPE, $default);
    }

    function setAnswerType($text) {
        $this->setSettingValue(SETTING_ANSWERTYPE, $text);
    }

    function getAnswerTypeCustom($default = true) {
        return $this->getSettingValue(SETTING_ANSWERTYPE_CUSTOM, $default);
    }

    function setAnswerTypeCustom($text) {
        $this->setSettingValue(SETTING_ANSWERTYPE_CUSTOM, $text);
    }

    function getKeep($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_KEEP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ARRAY, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        return $this->getArray() == ARRAY_ANSWER_YES;
    }

    function getSection($default = true) {
        return $this->getSettingValue(SETTING_SECTION, $default);
    }

    function setSection($text) {
        $this->setSettingValue(SETTING_SECTION, $text);
    }

    /* validation functions */

    function getInlineExclusive($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INLINE_EXCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INLINE_INCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INLINE_MINIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INLINE_MAXIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INLINE_EXACT_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_IFEMPTY, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getIfError($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_IFERROR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    /* output functions */

    function getHidden($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getHiddenPaperVersion($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN_PAPER_VERSION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getHiddenRouting($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN_ROUTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getHiddenTranslation($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_HIDDEN_TRANSLATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getScreendumpStorage($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SCREENDUMPS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getParadata($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PARADATA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getDataKeep($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_KEEP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getDataSkipVariable($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_SKIP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getDataSkipVariablePostFix($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_SKIP_POSTFIX, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getStoreLocation($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATA_STORE_LOCATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOutputOptionsText($default = true) {
        return $this->getSettingValue(SETTING_OUTPUT_OPTIONS, $default);
    }

    function setOutputOptionsText($options) {
        $this->setSettingValue(SETTING_OUTPUT_OPTIONS, $options);
    }

    function getOutputValueLabelWidth($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_OUTPUT_VALUELABEL_WIDTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    /* interactive functions */

    function getScripts($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SCRIPTS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnNext($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_NEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnBack($default = true) {
        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_BACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnDK($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_DK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnRF($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_RF, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnNA($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_NA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnUpdate($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_UPDATE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnLanguageChange($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_LANGUAGE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnModeChange($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_MODE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getOnVersionChange($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ON_VERSION_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    
    
    function getClickNext($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_NEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickBack($default = true) {
        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_BACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickDK($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_DK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickRF($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_RF, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickNA($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_NA, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickUpdate($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_UPDATE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickLanguageChange($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_LANGUAGE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickModeChange($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_MODE_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getClickVersionChange($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLICK_VERSION_CHANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPageJavascript();
    }

    function setPageJavascript($text) {
        $this->setSettingValue(SETTING_JAVASCRIPT_WITHIN_PAGE, $text);
    }

    function getInlineJavascript($default = true) {
        return $this->getSettingValue(SETTING_JAVASCRIPT_WITHIN_ELEMENT, $default);
    }

    function setInlineJavascript($text) {
        $this->setSettingValue(SETTING_JAVASCRIPT_WITHIN_ELEMENT, $text);
    }

    function getInlineStyle($default = true) {
        return $this->getSettingValue(SETTING_STYLE_WITHIN_ELEMENT, $default);
    }

    function setInlineStyle($text) {
        $this->setSettingValue(SETTING_STYLE_WITHIN_ELEMENT, $text);
    }

    function getPageStyle($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_STYLE_WITHIN_PAGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPageStyle();
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
        return $this->getSettingValue(SETTING_PRETEXT, $default);
    }

    function setPreText($text) {
        $this->setSettingValue(SETTING_PRETEXT, $text);
    }

    function getPostText($default = true) {
        return $this->getSettingValue(SETTING_POSTTEXT, $default);
    }

    function setPostText($text) {
        $this->setSettingValue(SETTING_POSTTEXT, $text);
    }

    function getHoverText($default = true) {
        return $this->getSettingValue(SETTING_HOVERTEXT, $default);
    }

    function setHoverText($text) {
        $this->setSettingValue(SETTING_HOVERTEXT, $text);
    }

    function getEmptyMessage($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_EMPTY_MESSAGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INTEGER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_DOUBLE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_PATTERN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_WORDSs, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INVALID_SELECT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INLINE_ANSWERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSetOFEnumeratedEntered($default);
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

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageComparisonSmaller($default);
    }

    function setErrorMessageComparisonSmaller($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER, $value);
    }

    /* options functions */

    function getSetOfEnumeratedRanking($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SETOFENUMERATED_RANKING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSetOfEnumeratedRanking($default);
    }

    function setSetOfEnumeratedRanking($value) {
        $this->setSettingValue(SETTING_SETOFENUMERATED_RANKING, $value);
    }
    
    function getEnumeratedDisplay($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_ORIENTATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_CUSTOM, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getEnumeratedColumns($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_COLUMNS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function setEnumeratedColumns($value) {
        $this->setSettingValue(SETTING_ENUMERATED_COLUMNS, $value);
    }

    function getEnumeratedRandomizer($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_RANDOMIZER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function setEnumeratedRandomizer($value) {
        $this->setSettingValue(SETTING_ENUMERATED_RANDOMIZER, $value);
    }

    function getEnumeratedOrder($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_ORDER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_SPLIT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedSplit($default);
    }

    function setEnumeratedSplit($value) {
        $this->setSettingValue(SETTING_ENUMERATED_SPLIT, $value);
    }

    function getEnumeratedBordered($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_BORDERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedBordered($default);
    }

    function setEnumeratedBordered($value) {
        $this->setSettingValue(SETTING_ENUMERATED_BORDERED, $value);
    }
    
    function getComboboxOptGroup($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_DROPDOWN_OPTGROUP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        return $this->getSettingValue(SETTING_OPTIONS, $default);
    }

    function setOptionsText($options) {
        $this->setSettingValue(SETTING_OPTIONS, $options);
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
            $remainder = splitString("/(\(\w+\))/", $t[1], PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            if (sizeof($remainder) == 2) {
                $acronym = trim(str_replace(")", "", str_replace("(", "", $remainder[0])));
                $label = trim($remainder[1]);
            } else {
                $acronym = "";
                $label = trim($remainder[0]);
            }
            $this->options[] = array("code" => $code, "label" => $label, "acronym" => $acronym);
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
        $values = explode("~", $value);
        //sort($values);
        $labels = array();
        foreach ($values as $v) {
            $labels[] = $this->getOptionLabel($v);
        }

        return implode(", ", $labels);
    }

    function getOptions() {
        $this->setOptions();
        return $this->options;
    }
    
    function clearOptions() {
        $this->options = null;
        unset($this->options);
    }

    function getOptionCodeByAcronym($acronym) {
        $this->setOptions();
        foreach ($this->options as $option) {
            if (strtoupper($option["acronym"]) == strtoupper($acronym)) {
                return $option["code"];
            }
        }
    }

    function getEnumeratedTextbox($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedTextbox($default);
    }

    function setEnumeratedTextbox($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX, $value);
    }

    function getEnumeratedTextboxLabel($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedTextboxLabel($default);
    }

    function setEnumeratedTextboxLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, $value);
    }
    
    function getEnumeratedTextboxPostText($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        return "";
    }

    function setEnumeratedTextboxPostText($value) {
        $this->setSettingValue(SETTING_ENUMERATED_TEXTBOX_POSTTEXT, $value);
    }

    function getEnumeratedLabel($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ENUMERATED_CLICK_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getEnumeratedClickLabel($default);
    }

    function setEnumeratedClickLabel($value) {
        $this->setSettingValue(SETTING_ENUMERATED_CLICK_LABEL, $value);
    }
    
    
    function getTableMobile($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_TABLE_MOBILE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* group level setting */
        $val = $this->getSettingValue(SETTING_TABLE_MOBILE_LABELS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableMobileLabels($default);
    }
    
    function isTableMobileLabels() {
        if ($this->getTableMobileLabels() == GROUP_YES) {
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

        /* no survey level setting */
        return "";
    }

    function setComparisonSmaller($value) {
        $this->setSettingValue(SETTING_COMPARISON_SMALLER, $value);
    }

    /* range functions */

    function getMinimum($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_OTHER_RANGE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumSelected($default);
    }

    function getExactSelected($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_EXACT_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getExactSelected($default);
    }

    function getMaximumSelected($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximumSelected($default);
    }

    function getInvalidSelected($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INVALID_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInvalidSelected($default);
    }

    function getInvalidSubSelected($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INVALIDSUB_SELECTED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_RANK_COLUMN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_RANKED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumRanked($default);
    }

    function getExactRanked($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_EXACT_RANKED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getExactRanked($default);
    }

    function getMaximumRanked($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_RANKED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_CALENDAR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PATTERN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getPattern($default);
    }

    function getMinimumLength($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        if ($this->getAnswerType() == ANSWER_TYPE_OPEN) {
            return $survey->getMinimumOpenLength($default);
        }
        return $survey->getMinimumLength($default);
    }

    function getMaximumLength($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_LENGTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        if ($this->getAnswerType() == ANSWER_TYPE_OPEN) {
            return $survey->getMaximumOpenLength($default);
        }
        return $survey->getMaximumLength($default);
    }

    function getMinimumWords($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MINIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumWords($default);
    }

    function getMaximumWords($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_MAXIMUM_WORDS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function getInputMaskCustom($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_CUSTOM, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function getInputMaskEnabled($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_ENABLED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getInputMaskCallback($default = true) {
        return $this->getSettingValue(SETTING_INPUT_MASK_CALLBACK, $default);
    }

    function setInputMaskCallback($text) {
        $this->setSettingValue(SETTING_INPUT_MASK_CALLBACK, $text);
    }

    function getInputMaskPlaceholder($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_PLACEHOLDER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInputMaskPlaceholder($default);
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

    /* access functions */

    function getAccessReturnAfterCompletionAction($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_REENTRY_ACTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ACCESS_REENTRY_PRELOAD_REDO, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    /* date time picker functions */

    function getDateFormat($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_DATE_FORMAT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    /* overall display functions */

    function getPlaceholder($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_PLACEHOLDER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PAGE_HEADER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PAGE_FOOTER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_QUESTION_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_QUESTION_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ANSWER_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ANSWER_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_BUTTON_ALIGNMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_BUTTON_FORMATTING, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    /* button functions */

    function getShowBackButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_BACK_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowBackButton($default);
    }

    function getShowNextButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_NEXT_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowNextButton($default);
    }

    function getShowDKButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_DK_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowDKButton($default);
    }

    function getShowRFButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_RF_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowRFButton($default);
    }

    function getShowUpdateButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_UPDATE_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowUpdateButton($default);
    }

    function getShowNAButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_NA_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowNAButton($default);
    }

    function getShowRemarkButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_REMARK_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowRemarkButton($default);
    }

    function getShowRemarkSaveButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_REMARK_SAVE_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowRemarkSaveButton($default);
    }

    function getShowCloseButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLOSE_BUTTON, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_BACK_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelBackButton($default);
    }

    function getLabelNextButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_NEXT_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelNextButton($default);
    }

    function getLabelDKButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_DK_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelDKButton($default);
    }

    function getLabelRFButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_RF_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelRFButton($default);
    }

    function getLabelUpdateButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_UPDATE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelUpdateButton($default);
    }

    function getLabelNAButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_NA_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelNAButton($default);
    }

    function getLabelRemarkButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_REMARK_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelRemarkButton($default);
    }

    function getLabelRemarkSaveButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_REMARK_SAVE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelRemarkSaveButton($default);
    }

    function getLabelCloseButton($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_CLOSE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getXiTemplate($default = true) {
        return $this->getSettingValue(SETTING_GROUP_XI_TEMPLATE, $default);
    }

    function setXiTemplate($text) {
        $this->setSettingValue(SETTING_GROUP_XI_TEMPLATE, $text);
    }
    
    /* spinner functions */
    function getSpinner($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SPINNER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSpinner($default);
    }
    
    function isSpinner() {
        return $this->getSpinner() == SPINNER_YES;
    }

    function setSpinner($value) {
        $this->setSettingValue(SETTING_SPINNER, $value);
    }
    
    function getSpinnerType($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_TYPE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getSpinnerDown($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_DOWN, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getSpinnerUp($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_UP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
    
    function getSpinnerIncrement($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SPINNER_STEP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_TEXTBOX_MANUAL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextboxManual($default);
    }    

    function setTextboxManual($value) {
        $this->setSettingValue(SETTING_TEXTBOX_MANUAL, $value);
    }

    /* knob functions */
    function getKnobRotation($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_KNOB_ROTATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_PRESELECTION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSliderPreSelection($default);
    }

    function setSliderPreSelection($value) {
        $this->setSettingValue(SETTING_SLIDER_PRESELECTION, $value);
    }

    function getSliderFormater($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_FORMATER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSliderFormater($default);
    }

    function setSliderFormater($value) {
        $this->setSettingValue(SETTING_SLIDER_FORMATER, $value);
    }
    
    function getTooltip($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TOOLTIP, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getSliderLabelPlacement($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_LABEL_PLACEMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_LABELS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        return '';
    }

    function setSliderLabels($value) {
        $this->setSettingValue(SETTING_SLIDER_LABELS, $value);
    }

    function getSliderOrientation($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_ORIENTATION, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    function getTextbox($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TEXTBOX, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextbox($default);
    }

    function setTextbox($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX, $value);
    }

    function getTextboxLabel($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextboxLabel($default);
    }

    function setTextboxLabel($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $value);
    }
    
    function getTextboxPostText($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTextboxPostText($default);
    }

    function setTextboxPostText($value) {
        $this->setSettingValue(SETTING_SLIDER_TEXTBOX_LABEL, $value);
    }

    function getIncrement($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_SLIDER_INCREMENT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_SHOW, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_FILLED_COLOR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_REMAIN_COLOR, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_PROGRESSBAR_WIDTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

    /* keyboard binding functions */

    function getKeyboardBindingEnabled($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_KEYBOARD_BINDING_ENABLED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowSectionHeader($default);
    }

    function setShowSectionHeader($text) {
        $this->setSettingValue(SETTING_SHOW_SECTION_HEADER, $text);
    }

    function getShowSectionFooter($default = true) {

        /* variable level setting */
        $val = $this->getSettingValue(SETTING_SHOW_SECTION_FOOTER, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getShowSectionFooter($default);
    }

    function setShowSectionFooter($text) {
        $this->setSettingValue(SETTING_SHOW_SECTION_FOOTER, $text);
    }
    
    function getValidateAssignment($default = true) {
        if ($this->getSettingValue(SETTING_VALIDATE_ASSIGNMENT, $default) != "") {
            return $this->getSettingValue(SETTING_VALIDATE_ASSIGNMENT, $default);
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_RANK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_RANK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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

        /* type level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_RANK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
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
        return true;
    }

    function isTranslatedGeneral() {
        $arr = array(
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
    
    function isUsed() {
        global $survey;
        $types = $survey->getVariableDescriptivesOfType($this->getTyd());
        if (sizeof($types) > 0) {
            return true;
        }
        return false;
    }

    /* generic functions */

    function remove() {
        global $db;
        if (!isset($this->type['tyd'])) {
            return;
        }

        $query = "delete from " . Config::dbSurvey() . "_types where suid = " . prepareDatabaseString($this->getSuid()) . " and tyd = " . prepareDatabaseString($this->getTyd());
        $db->executeQuery($query);
        $query = "delete from " . Config::dbSurvey() . "_settings where suid = " . prepareDatabaseString($this->getSuid()) . " and object = " . prepareDatabaseString($this->getObjectName()) . " and objecttype = " . prepareDatabaseString($this->getObjectType());
        $db->executeQuery($query);
    }

    function move($suid) {

        global $db;
        if (!isset($this->type['tyd'])) {
            return;
        }

        $query = "select max(tyd) as max from " . Config::dbSurvey() . "_types";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $tyd = $row["max"] + 1;

        $this->remove();

        $this->setObjectName($tyd);
        $this->setTyd($tyd);
        $this->setSuid($suid);
        $this->save();
    }

    function copy($newsuid = "", $suffix = 2) {
        global $db;
        $query = "select max(tyd) as max from " . Config::dbSurvey() . "_types";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $tyd = $row["max"] + 1;
        $this->setObjectName($tyd);
        $this->setTyd($tyd);
        if ($suffix == 2) {
            $this->setName($this->getName() . "_cl");
        }
        if ($newsuid != "") {
            $this->setSuid($newsuid);
        }
        $this->save();
    }

    function save() {
        global $db;
        if (!isset($this->type['tyd'])) {
            $query = "select max(tyd) as max from " . Config::dbSurvey() . "_types";
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $tyd = $row["max"] + 1;
            $this->setObjectName($tyd);
            $this->setTyd($tyd);
        }

        $query = "replace into " . Config::dbSurvey() . "_types (suid, tyd, name) values(";
        $query .= prepareDatabaseString($this->getSuid()) . ",";
        $query .= prepareDatabaseString($this->getTyd()) . ",";
        $query .= "'" . prepareDatabaseString($this->getName()) . "'";
        $query .= ")";

        $db->executeQuery($query);

        /* save settings */
        $settings = $this->getSettingsArray();
        foreach ($settings as $key => $setting) {
            $setting->setObject($this->getTyd());
            $setting->setSuid($this->getSuid());
            $setting->save();
        }
    }

}

?>