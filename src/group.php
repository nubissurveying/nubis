<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Group extends Component {

    private $group;
    private $runtimeproperties;

    function __construct($nameOrRow = "") {
        $this->setObjectType(OBJECT_GROUP);
        if (is_array($nameOrRow)) {
            $this->group = $nameOrRow;
            $this->setSuid($this->group["suid"]);
            $this->setObjectName($this->getGid());

            /* add settings */
            $this->addSettings($this->getSettings());
        }
        $this->runtimeproperties = array();
    }

    function getGid() {
        return $this->group["gid"];
    }

    function setGid($gid) {
        $this->group["gid"] = $gid;
    }

    function getName() {
        return $this->group["name"];
    }

    function setName($name) {
        $this->group["name"] = $name;
        $this->setSettingValue(SETTING_GROUP_NAME, $name);
    }

    function getType($default = true) {
        return $this->getSettingValue(SETTING_GROUP_TYPE, $default);
    }

    function setType($text) {
        $this->setSettingValue(SETTING_GROUP_TYPE, $text);
    }

    function getParentGroup() {
        return $this->group["parent"];
    }

    function setParentGroup($parent) {
        $this->group["parent"] = $parent;
    }

    function getEnumeratedFirst() {
        return $this->group["enumeratedfirst"];
    }

    function isEnumeratedFirst() {
        if ($this->getEnumeratedFirst() == 1) {
            return true;
        }
        return false;
    }

    function setEnumeratedFirst($first) {
        $this->group["enumeratedfirst"] = $first;
    }

    /* interactive functions */

    function getScripts($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_SCRIPTS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getScripts();
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

    function getOnBack() {
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

    function getOnUpdate() {

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

    function getClickBack() {
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

    function getClickUpdate() {

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
    
    /* overall display functions */

    function getTemplate($default = true) {
        return $this->getSettingValue(SETTING_GROUP_TEMPLATE, $default);
    }

    function setTemplate($text) {
        $this->setSettingValue(SETTING_GROUP_TEMPLATE, $text);
    }

    function getCustomTemplate($default = true) {
        return $this->getSettingValue(SETTING_GROUP_CUSTOM_TEMPLATE, $default);
    }

    function setCustomTemplate($text) {
        $this->setSettingValue(SETTING_GROUP_CUSTOM_TEMPLATE, $text);
    }
    
    function getXiTemplate($default = true) {
        return $this->getSettingValue(SETTING_GROUP_XI_TEMPLATE, $default);
    }

    function setXiTemplate($text) {
        $this->setSettingValue(SETTING_GROUP_XI_TEMPLATE, $text);
    }

    /* table display */

    function getTableID($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_TABLE_ID, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function setTableID($text) {
        $this->setSettingValue(SETTING_GROUP_TABLE_ID, $text);
    }

    function getTableStriped($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_TABLE_STRIPED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableStriped($default);
    }

    function setTableStriped($text) {
        $this->setSettingValue(SETTING_GROUP_TABLE_STRIPED, $text);
    }

    function getTableBordered($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_TABLE_BORDERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableBordered($default);
    }

    function setTableBordered($text) {
        $this->setSettingValue(SETTING_GROUP_TABLE_BORDERED, $text);
    }

    function getTableCondensed($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_TABLE_CONDENSED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableCondensed($default);
    }

    function setTableCondensed($text) {
        $this->setSettingValue(SETTING_GROUP_TABLE_CONDENSED, $text);
    }

    function getTableHovered($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_TABLE_HOVERED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableHovered($default);
    }

    function setTableHovered($text) {
        $this->setSettingValue(SETTING_GROUP_TABLE_HOVERED, $text);
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
        $val = $this->getSettingValue(SETTING_GROUP_TABLE_MOBILE_LABELS, $default);
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
        if ($this->getTableMobileLabels() == MOBILE_LABEL_YES) {
            return true;
        }
        return false;
    }

    function setTableMobileLabels($text) {
        $this->setSettingValue(SETTING_GROUP_TABLE_MOBILE_LABELS, $text);
    }  

    /* validation */

    function getExclusive($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_EXCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getExclusive($default);
    }

    function isExclusive($default = true) {
        return $this->getSettingValue(SETTING_GROUP_EXCLUSIVE, $default) == GROUP_YES;
    }

    function setExclusive($text) {
        $this->setSettingValue(SETTING_GROUP_EXCLUSIVE, $text);
    }

    function getInclusive($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_INCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getInclusive($default);
    }

    function isInclusive($default = true) {
        return $this->getSettingValue(SETTING_GROUP_INCLUSIVE, $default) == GROUP_YES;
    }

    function setInclusive($text) {

        $this->setSettingValue(SETTING_GROUP_INCLUSIVE, $text);
    }

    function getMinimumRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_MINIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMinimumRequired($default);
    }

    function setMinimumRequired($text) {
        $this->setSettingValue(SETTING_GROUP_MINIMUM_REQUIRED, $text);
    }

    function getMaximumRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_MAXIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMaximumRequired($default);
    }

    function setMaximumRequired($text) {
        $this->setSettingValue(SETTING_GROUP_MAXIMUM_REQUIRED, $text);
    }

    function getExactRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_EXACT_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getExactRequired($default);
    }

    function setExactRequired($text) {
        $this->setSettingValue(SETTING_GROUP_EXACT_REQUIRED, $text);
    }

    function getUniqueRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_UNIQUE_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getUniqueRequired($default);
    }

    function isUniqueRequired($default = true) {
        return $this->getUniqueRequired == GROUP_YES;
    }

    function setUniqueRequired($text) {
        $this->setSettingValue(SETTING_GROUP_UNIQUE_REQUIRED, $text);
    }

    function getSameRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_GROUP_SAME_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getSameRequired($default);
    }

    function isSameRequired($default = true) {
        return $this->getSameRequired() == GROUP_YES;
    }

    function setSameRequired($text) {
        $this->setSettingValue(SETTING_GROUP_SAME_REQUIRED, $text);
    }

    function getIfError($default = true) {

        /* group level setting */
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

    /* overall display functions */

    function getPageHeader($default = true) {

        /* group level setting */
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

        /* group level setting */
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

    function getHeaderAlignment($default = true) {

        /* group level setting */
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
    
    function getFooterDisplay($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_FOOTER_DISPLAY, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getFooterDisplay($default);
    }
    
    function isFooterDisplay() {
        if ($this->getFooterDisplay() == ENUM_FOOTER_YES) {
            return true;
        }
        return false;
    }

    function setFooterDisplay($value) {
        $this->setSettingValue(SETTING_FOOTER_DISPLAY, $value);
    }

    function getHeaderFormatting($default = true) {

        /* group level setting */
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

    function getHeaderFixed($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_HEADER_FIXED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHeaderFixed($default);
    }

    function isHeaderFixed() {
        if ($this->getHeaderFixed() == TABLE_YES) {
            return true;
        }
        return false;
    }

    function setHeaderFixed($value) {
        $this->setSettingValue(SETTING_HEADER_FIXED, $value);
    }

    function getHeaderScrollDisplay($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_HEADER_SCROLL_DISPLAY, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getHeaderScrollDisplay($default);
    }

    function setHeaderScrollDisplay($value) {
        $this->setSettingValue(SETTING_HEADER_SCROLL_DISPLAY, $value);
    }

    function getQuestionColumnWidth($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_QUESTION_COLUMN_WIDTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getQuestionColumnWidth($default);
    }

    function setQuestionColumnWidth($value) {
        $this->setSettingValue(SETTING_QUESTION_COLUMN_WIDTH, $value);
    }

    function getTableWidth($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_TABLE_WIDTH, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getTableWidth($default);
    }

    function setTableWidth($value) {
        $this->setSettingValue(SETTING_TABLE_WIDTH, $value);
    }

    function getTableHeaders($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_TABLE_HEADERS, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* no survey level setting */
        return "";
    }

    function setTableHeaders($value) {
        $this->setSettingValue(SETTING_TABLE_HEADERS, $value);
    }

    function getButtonAlignment($default = true) {

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
        $val = $this->getSettingValue(SETTING_CLOSE_BUTTON_LABEL, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getLabelcloseButton($default);
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

    function getEmptyMessage($default = true) {

        /* group level setting */
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

    function getErrorMessageExclusive($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_EXCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInclusive($default);
    }

    function setErrorMessageExclusive($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXCLUSIVE, $value);
    }

    function getErrorMessageInclusive($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_INCLUSIVE, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageInclusive($default);
    }

    function setErrorMessageInclusive($value) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_INCLUSIVE, $value);
    }

    function getErrorMessageMinimumRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMinimumRequired($default);
    }

    function setErrorMessageMinimumRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED, $text);
    }

    function getErrorMessageMaximumRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageMaximumRequired($default);
    }

    function setErrorMessageMaximumRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED, $text);
    }

    function getErrorMessageExactRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_EXACT_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageExactRequired($default);
    }

    function setErrorMessageExactRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_EXACT_REQUIRED, $text);
    }

    function getErrorMessageUniqueRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageUniqueRequired($default);
    }

    function setErrorMessageUniqueRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED, $text);
    }

    function getErrorMessageSameRequired($default = true) {

        /* group level setting */
        $val = $this->getSettingValue(SETTING_ERROR_MESSAGE_SAME_REQUIRED, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getErrorMessageSameRequired($default);
    }

    function setErrorMessageSameRequired($text) {
        $this->setSettingValue(SETTING_ERROR_MESSAGE_SAME_REQUIRED, $text);
    }

    /* progressbar functions */

    function getShowProgressBar($default = true) {

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

        /* group level setting */
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

    function getProgressBarValue($default = true) {

        if ($this->getSettingValue(SETTING_PROGRESSBAR_VALUE, $default) != "") {
            return $this->getSettingValue(SETTING_PROGRESSBAR_VALUE, $default);
        }
        return "";
    }

    function setProgressBarValue($value) {
        $this->setSettingValue(SETTING_PROGRESSBAR_VALUE, $value);
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
        if ($this->getIndividualDKRFNA() == INDIVIDUAL_DKRFNA_YES) {
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
        if ($this->getIndividualDKRFNAInline() == INDIVIDUAL_DKRFNA_YES) {
            return true;
        }
        return false;
    }

    function setIndividualDKRFNAInline($value) {
        $this->setSettingValue(SETTING_DKRFNA_INLINE, $value);
    }
    
    /* output functions */
    function getScreendumpStorage($default = true) {
        /* type level setting */
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

    function setScreendumpStorage($value) {
        $this->setSettingValue(SETTING_SCREENDUMPS, $value);
    }
    
    function isScreendumpStorage() {
        if ($this->getScreendumpStorage() == SCREENDUMPS_YES) {
            return true;
        }
        return false;
    }
    
    function getParadata($default = true) {
        /* type level setting */
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

    function setParadata($value) {
        $this->setSettingValue(SETTING_PARADATA, $value);
    }
    
    function isParadata() {
        if ($this->getParadata() == PARADATA_YES) {
            return true;
        }
        return false;
    }

    function getMultiColumnQuestiontext($default = true) {
        /* type level setting */
        $val = $this->getSettingValue(SETTING_MULTICOLUMN_QUESTIONTEXT, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        global $survey;
        return $survey->getMultiColumnQuestiontext($default);
    }
    
    function isMultiColumnQuestiontext() {
        if ($this->getMultiColumnQuestiontext() == MULTI_QUESTION_YES) {
            return true;
        }
        return false;
    }

    function setMultiColumnQuestiontext($value) {
        $this->setSettingValue(SETTING_MULTICOLUMN_QUESTIONTEXT, $value);
    }
    
    /* input masking */
    function getInputMaskCallback($default = true) {

        /* type level setting */
        $val = $this->getSettingValue(SETTING_INPUT_MASK_CALLBACK, $default);
        if (!inArray($val, array("", SETTING_FOLLOW_GENERIC))) {
            return $val;
        }

        /* survey level setting */ if ($_SESSION['PARAMETER_RETRIEVAL'] == PARAMETER_ADMIN_RETRIEVAL) {
            return SETTING_FOLLOW_GENERIC;
        }
        return "";
    }

    function setInputMaskCallback($value) {
        $this->setSettingValue(SETTING_INPUT_MASK_CALLBACK, $value);
    }
    
    /* translator functions */

    function isTranslated() {
        if ($this->isTranslatedLayout() == false) {
            return false;
        }
        if ($this->isTranslatedAssistance() == false) {
            return false;
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
            $s = $this->getSetting($a);
            //if ($s->getValue() != "") {
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
            //}
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
            $s = $this->getSetting($a);
            $s1 = $this->getSettingModeLanguage($a, $mode, $default, $this->getObjectType());
            if (($s->getValue() == "" && $s1->getValue() != "") || ($s1->getTimestamp() > $s->getTimestamp())) {
                return false;
            }
        }
        return true;
    }

    /* generic functions */

    function remove() {

        global $db;
        if (!isset($this->group['gid'])) {
            return;
        }
        $query = "delete from " . Config::dbSurvey() . "_groups where suid = " . prepareDatabaseString($this->getSuid()) . " and gid = " . prepareDatabaseString($this->getGid());
        $db->executeQuery($query);
        $query = "delete from " . Config::dbSurvey() . "_settings where suid = " . prepareDatabaseString($this->getSuid()) . " and object = " . prepareDatabaseString($this->getObjectName()) . " and objecttype = " . prepareDatabaseString($this->getObjectType());
        $db->executeQuery($query);
    }

    function move($suid) {

        global $db;
        if (!isset($this->group['gid'])) {
            return;
        }
        $query = "select max(gid) as max from " . Config::dbSurvey() . "_groups";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $gid = $row["max"] + 1;

        $this->remove();

        $this->setObjectName($gid);
        $this->setGid($gid);
        $this->setSuid($suid);
        $this->save();
    }

    function copy($newsuid = "", $suffix = 2) {
        global $db;
        $query = "select max(gid) as max from " . Config::dbSurvey() . "_groups";
        $r = $db->selectQuery($query);
        $row = $db->getRow($r);
        $gid = $row["max"] + 1;
        $this->setObjectName($gid);
        $this->setGid($gid);
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
        if (!isset($this->group['gid'])) {
            $query = "select max(gid) as max from " . Config::dbSurvey() . "_groups";
            $r = $db->selectQuery($query);
            $row = $db->getRow($r);
            $gid = $row["max"] + 1;
            $this->setObjectName($gid);
            $this->setGid($gid);
        }

        $query = "replace into " . Config::dbSurvey() . "_groups (suid, gid, name) values(";
        $query .= prepareDatabaseString($this->getSuid()) . ",";
        $query .= prepareDatabaseString($this->getGid()) . ",";
        $query .= "'" . prepareDatabaseString($this->getName()) . "'";
        $query .= ")";
        //echo $query;
        $db->executeQuery($query);

        /* save settings */
        $settings = $this->getSettingsArray();
        foreach ($settings as $key => $setting) {
            $setting->setObject($this->getGid());
            $setting->setSuid($this->getSuid());
            $setting->save();
        }
    }

    /* function determineRuntimeProperties($lang, $mode, $methods) {

      $changed = $this->getChanged();
      if (sizeof($changed) == 0) {
      $changed = array_keys($methods);
      }

      foreach ($changed as $ch) {
      if (isset($methods[$ch])) {
      $reflectionMethod = new ReflectionMethod($this, $methods[$ch]);
      $this->runtimeproperties[strtoupper($ch) . $lang . $mode] = $reflectionMethod->invoke();
      }
      }

      return;
      } */
}

?>