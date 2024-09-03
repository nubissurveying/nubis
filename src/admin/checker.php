<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Checker {

    private $suid;
    private $survey;
    private $display;

    function __construct($suid) {
        $this->suid = $suid;
        $this->survey = new Survey($this->suid);
        $this->display = new Display();
    }

    function checkName($name, $vsid = "") {
        $messages = array();
        if (trim($name) == "") {
            $messages[] = $this->display->displayError(Language::messageCheckerNoName());
            return $messages;
        }

        $first = substr($name, 0, 1);
        if (ctype_alnum(str_replace("_", "", $name)) == false || ctype_alpha($first) == false) {
            $messages[] = $this->display->displayError(Language::messageCheckerInvalidName());
            return $messages;
        }

        $matches = array();
        preg_match("/(_[0-9]+_\b){1}/", $name, $matches);
        if (sizeof($matches) > 0) {
            $messages[] = $this->display->displayError(Language::messageCheckerInvalidNameEnding());
            return $messages;
        }

        $var = $this->survey->getVariableDescriptiveByName($name);
        if ($var->getVsid() != "" && $var->getVsid() != $vsid) {
            $messages[] = $this->display->displayError(Language::messageCheckerVariableExists($name));
            return $messages;
        }

        $group = $this->survey->getGroupByName($name);
        if ($group->getGid() != "") {
            $messages[] = $this->display->displayError(Language::messageCheckerGroupExists($name));
            return $messages;
        }

        $section = $this->survey->getSectionByName($name);
        if ($section->getSeid() != "") {
            $messages[] = $this->display->displayError(Language::messageCheckerSectionExists($name));
        }

        return $messages;
    }

    function checkTypeName($name) {
        $messages = array();

        if (trim($name) == "") {
            $messages[] = $this->display->displayError(Language::messageCheckerNoName());
            return $messages;
        }

        $first = substr($name, 0, 1);
        if (ctype_alnum(str_replace("_", "", $name)) == false || ctype_alpha($first) == false) {
            $messages[] = $this->display->displayError(Language::messageCheckerInvalidName());
            return $messages;
        }

        $type = $this->survey->getTypeByName($name);
        if ($type->getTyd() != "") {
            $messages[] = $this->display->displayError(Language::messageCheckerTypeExists($name));
            return $messages;
        }
        return $messages;
    }

    function checkSection($section, $all = false) {
        $text = array();
        $text[] = $section->getHeader();
        $text[] = $section->getFooter();

        // check for references
        $fills = getReferences(implode(" ", $text), INDICATOR_FILL);
        $fills1 = getReferences(implode(" ", $text), INDICATOR_FILL_NOVALUE);
        $fills2 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_ANSWER);
        $fills3 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_TEXT);
        $fills = array_unique(array_merge($fills, $fills1, $fills2, $fills3));
        $messages = $this->checkReferences($fills);
        return $messages;
    }

    function checkVariable($var, $all = false) {

        // get answer type
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $t = $var->getAnswerType();
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        $text = array();
        
        if (!isset($_SESSION['VRFILTERMODE_VARIABLE'])) {
            $_SESSION['VRFILTERMODE_VARIABLE'] = 0;
        }
        // general        
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0 || $all == true) {
            $text[] = $var->getQuestion();

            switch ($t) {
                case ANSWER_TYPE_ENUMERATED:
                case ANSWER_TYPE_DROPDOWN:
                case ANSWER_TYPE_SETOFENUMERATED:
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getOptionsText();
                    break;
                case ANSWER_TYPE_CUSTOM:
                    $text[] = $var->getAnswerTypeCustom();
                    break;
            }
        }
        
        // verification
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 1 || $all == true) {
            switch ($t) {
                case ANSWER_TYPE_RANGE:
                    $text[] = $var->getMinimum();
                    $text[] = $var->getMaximum();
                    $text[] = $var->getOtherValues();
                    break;
                case ANSWER_TYPE_SLIDER:
                    $text[] = $var->getMinimum();
                    $text[] = $var->getMaximum();
                    break;
                case ANSWER_TYPE_KNOB:
                    $text[] = $var->getMinimum();
                    $text[] = $var->getMaximum();
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getMinimumSelected();
                    $text[] = $var->getMaximumSelected();
                    $text[] = $var->getExactSelected();
                    break;
                case ANSWER_TYPE_STRING:
                case ANSWER_TYPE_OPEN:
                    $text[] = $var->getMinimumLength();
                    $text[] = $var->getMaximumLength();
                    $text[] = $var->getMinimumWords();
                    $text[] = $var->getMaximumWords();
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $text[] = $var->getMaximumDatesSelected();
                    break;
            }

            if (inArray($t, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER))) {
                $text[] = $var->getComparisonEqualTo();
                $text[] = $var->getComparisonNotEqualTo();
                $text[] = $var->getComparisonGreaterEqualTo();
                $text[] = $var->getComparisonGreater();
                $text[] = $var->getComparisonSmallerEqualTo();
                $text[] = $var->getComparisonSmaller();
            }
        }
        
        // display
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 2 || $all == true) {
            $text[] = $var->getPageHeader();
            $text[] = $var->getPageFooter();
            $text[] = $var->getLabelBackButton();
            $text[] = $var->getLabelNextButton();
            $text[] = $var->getLabelDKButton();
            $text[] = $var->getLabelRFButton();
            $text[] = $var->getLabelNAButton();
            $text[] = $var->getLabelUpdateButton();
            $text[] = $var->getLabelRemarkButton();
            $text[] = $var->getLabelRemarkSaveButton();
            $text[] = $var->getLabelCloseButton();

            switch ($t) {

                case ANSWER_TYPE_ENUMERATED:
                case ANSWER_TYPE_DROPDOWN:
                case ANSWER_TYPE_SETOFENUMERATED:
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getEnumeratedCustom();
                    $text[] = $var->getEnumeratedRandomizer();
                    break;
            }
        }
        
        // assistance
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 3 || $all == true) {
            $text[] = $var->getEmptyMessage();
            $text[] = $var->getPreText();
            $text[] = $var->getPostText();

            if (inArray($t, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER))) {
                $text[] = $var->getErrorMessageComparisonEqualTo();
                $text[] = $var->getErrorMessageComparisonNotEqualTo();
                $text[] = $var->getErrorMessageComparisonGreaterEqualTo();
                $text[] = $var->getErrorMessageComparisonGreater();
                $text[] = $var->getErrorMessageComparisonSmallerEqualTo();
                $text[] = $var->getErrorMessageComparisonSmaller();
            } else if (inArray($t, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
                $text[] = $var->getErrorMessageComparisonEqualToIgnoreCase();
                $text[] = $var->getErrorMessageComparisonNotEqualToIgnoreCase();
            }

            switch ($t) {
                case ANSWER_TYPE_DOUBLE:
                    $text[] = $var->getErrorMessageDouble();
                    break;
                case ANSWER_TYPE_INTEGER:
                    $text[] = $var->getErrorMessageInteger();
                    break;
                case ANSWER_TYPE_STRING;
                /* fall through */

                case ANSWER_TYPE_OPEN;
                    $text[] = $var->getErrorMessagePattern();
                    $text[] = $var->getErrorMessageMinimumLength();
                    $text[] = $var->getErrorMessageMaximumLength();
                    $text[] = $var->getErrorMessageMinimumWords();
                    $text[] = $var->getErrorMessageMaximumWords();
                    break;
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                    $text[] = $var->getErrorMessageRange();
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                /* fall through */

                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getErrorMessageSelectMinimum();
                    $text[] = $var->getErrorMessageSelectMaximum();
                    $text[] = $var->getErrorMessageSelectExact();
                    $text[] = $var->getErrorMessageSelectInvalidSubset();
                    $text[] = $var->getErrorMessageSelectInvalidSet();
                    break;

                case ANSWER_TYPE_CALENDAR:
                    $text[] = $var->getErrorMessageMaximumCalendar();
                    break;
            }
        }
        
        // fill
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 4 || $all == true) {
            $text[] = $var->getFillText();
            $text[] = $var->getFillCode();
        }
        
        // interactive
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 6 || $all == true) {
            $text[] = $var->getInlineStyle();
            $text[] = $var->getPageStyle();
            $text[] = $var->getPageJavascript();
            $text[] = $var->getInlineJavascript();
            $text[] = $var->getScripts();

            $text[] = $var->getOnBack();
            $text[] = $var->getOnNext();
            $text[] = $var->getOnDK();
            $text[] = $var->getOnRF();
            $text[] = $var->getOnNA();
            $text[] = $var->getOnUpdate();
            $text[] = $var->getOnLanguageChange();
            $text[] = $var->getOnModeChange();
            $text[] = $var->getOnVersionChange();
        }
        
        // navigation
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 7 || $all == true) {
            $text[] = $var->getKeyboardBindingBack();
            $text[] = $var->getKeyboardBindingNext();
            $text[] = $var->getKeyboardBindingUpdate();
            $text[] = $var->getKeyboardBindingClose();
            $text[] = $var->getKeyboardBindingDK();
            $text[] = $var->getKeyboardBindingRF();
            $text[] = $var->getKeyboardBindingNA();
            $text[] = $var->getKeyboardBindingRemark();
        }

        // check for references
        $fills = getReferences(implode(" ", $text), INDICATOR_FILL);        
        $fills1 = getReferences(implode(" ", $text), INDICATOR_FILL_NOVALUE);
        $fills2 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_ANSWER);
        $fills3 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_TEXT);
        $fills = array_unique(array_merge($fills, $fills1, $fills2, $fills3));
        $messages = $this->checkReferences($fills);

        // options text format check
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0 || $all == true) {
            if (inArray($t, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK))) {
                if ($var->getOptionsText() == "") {
                    $messages[] = Language::messageCheckerVariableNoOptionCodes();
                } else if ($var->getOptionsText() != SETTING_FOLLOW_TYPE) {
                    $options = explode("\r\n", $var->getOptionsText());
                    foreach ($options as $option) {
                        $t = splitString("/ /", $option, PREG_SPLIT_NO_EMPTY, 2);
                        $code = trim($t[0]);

                        if (!is_numeric($code)) {
                            $messages[] = Language::messageCheckerVariableNumericOptionCodes($option);
                            break;
                        }
                        $labeltext = trim($t[1]);
                    }
                }
            }
        }
        // function reference check
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 6 || $all == true) {
            $functions = array();
            $functions[] = $var->getOnBack();
            $functions[] = $var->getOnNext();
            $functions[] = $var->getOnDK();
            $functions[] = $var->getOnRF();
            $functions[] = $var->getOnNA();
            $functions[] = $var->getOnUpdate();
            $functions[] = $var->getOnLanguageChange();
            $functions[] = $var->getOnModeChange();
            $functions[] = $var->getOnVersionChange();
            foreach ($functions as $f) {
                if (stripos($f, '(') !== false) {
                    $f = substr($f, 0, stripos($f, '('));
                }
                if (!inArray(trim($f), array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
                    if (function_exists($f) == false) {
                        $messages[] = Language::messageCheckerFunctionNotExists($f);
                    } else {
                        if (!inArray($f, getAllowedOnChangeFunctions()) || inArray($f, getForbiddenOnChangeFunctions())) {
                            $messages[] = Language::messageCheckerFunctionNotAllowed($f);
                        }
                    }
                }
            }
        }

        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0 || $all == true) {
            if ($t == ANSWER_TYPE_CUSTOM) {
                $functions = array();
                $functions[] = $var->getAnswerTypeCustom();
                foreach ($functions as $f) {
                    if (stripos($f, '(') !== false) {
                        $f = substr($f, 0, stripos($f, '('));
                    }
                    if (!inArray(trim($f), array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
                        if (function_exists($f) == false) {
                            $messages[] = Language::messageCheckerFunctionNotExists($f);
                        } else {
                            if (!inArray($f, getAllowedCustomAnswerFunctions()) || inArray($f, getForbiddenCustomAnswerFunctions())) {
                                $messages[] = Language::messageCheckerFunctionNotAllowed($f);
                            }
                        }
                    }
                }
            }
        }

        if ($var->hasType()) {
            $tempsurv = new Survey($var->getSuid());
            $temptype = $tempsurv->getType($var->getTyd());
            if ($temptype->getTyd() == "") {
                $messages[] = Language::messageCheckerTypeNotExists($var->getName());
            }
        }
        return $messages;
    }

    function checkType($var, $all = false) {

        // get answer type
        $t = $var->getAnswerType();
        if (!isset($_SESSION['VRFILTERMODE_TYPE'])) {
            $_SESSION['VRFILTERMODE_TYPE'] = 0;
        }
        
        // general   
        $text = array();
        if ($_SESSION['VRFILTERMODE_TYPE'] == 0 || $all == true) {
            switch ($t) {
                case ANSWER_TYPE_ENUMERATED:
                case ANSWER_TYPE_DROPDOWN:
                case ANSWER_TYPE_SETOFENUMERATED:
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getOptionsText();
                    break;
                case ANSWER_TYPE_CUSTOM:
                    $text[] = $var->getAnswerTypeCustom();
                    break;
            }
        }
        // verification
        if ($_SESSION['VRFILTERMODE_TYPE'] == 1 || $all == true) {
            switch ($t) {
                case ANSWER_TYPE_RANGE:
                    $text[] = $var->getMinimum();
                    $text[] = $var->getMaximum();
                    $text[] = $var->getOtherValues();
                    break;
                case ANSWER_TYPE_KNOB:
                    $text[] = $var->getMinimum();
                    $text[] = $var->getMaximum();
                    break;
                case ANSWER_TYPE_SLIDER:
                    $text[] = $var->getMinimum();
                    $text[] = $var->getMaximum();
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getMinimumSelected();
                    $text[] = $var->getMaximumSelected();
                    $text[] = $var->getExactSelected();
                    break;
                case ANSWER_TYPE_STRING:
                case ANSWER_TYPE_OPEN:
                    $text[] = $var->getMinimumLength();
                    $text[] = $var->getMaximumLength();
                    $text[] = $var->getMinimumWords();
                    $text[] = $var->getMaximumWords();
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $text[] = $var->getMaximumDatesSelected();
                    break;
            }

            if (inArray($t, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER))) {
                $text[] = $var->getComparisonEqualTo();
                $text[] = $var->getComparisonNotEqualTo();
                $text[] = $var->getComparisonGreaterEqualTo();
                $text[] = $var->getComparisonGreater();
                $text[] = $var->getComparisonSmallerEqualTo();
                $text[] = $var->getComparisonSmaller();
            }
        }
        // display
        if ($_SESSION['VRFILTERMODE_TYPE'] == 2 || $all == true) {
            $text[] = $var->getPageHeader();
            $text[] = $var->getPageFooter();
            $text[] = $var->getLabelBackButton();
            $text[] = $var->getLabelNextButton();
            $text[] = $var->getLabelDKButton();
            $text[] = $var->getLabelRFButton();
            $text[] = $var->getLabelNAButton();
            $text[] = $var->getLabelUpdateButton();
            $text[] = $var->getLabelRemarkButton();
            $text[] = $var->getLabelRemarkSaveButton();
            $text[] = $var->getLabelCloseButton();

            switch ($t) {

                case ANSWER_TYPE_ENUMERATED:
                case ANSWER_TYPE_DROPDOWN:
                case ANSWER_TYPE_SETOFENUMERATED:
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getEnumeratedCustom();
                    $text[] = $var->getEnumeratedRandomizer();
                    break;
            }
        }
        // assistance
        if ($_SESSION['VRFILTERMODE_TYPE'] == 3 || $all == true) {
            $text[] = $var->getEmptyMessage();
            $text[] = $var->getPreText();
            $text[] = $var->getPostText();

            if (inArray($t, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER))) {
                $text[] = $var->getErrorMessageComparisonEqualTo();
                $text[] = $var->getErrorMessageComparisonNotEqualTo();
                $text[] = $var->getErrorMessageComparisonGreaterEqualTo();
                $text[] = $var->getErrorMessageComparisonGreater();
                $text[] = $var->getErrorMessageComparisonSmallerEqualTo();
                $text[] = $var->getErrorMessageComparisonSmaller();
            } else if (inArray($t, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
                $text[] = $var->getErrorMessageComparisonEqualToIgnoreCase();
                $text[] = $var->getErrorMessageComparisonNotEqualToIgnoreCase();
            }

            switch ($t) {
                case ANSWER_TYPE_DOUBLE:
                    $text[] = $var->getErrorMessageDouble();
                    break;
                case ANSWER_TYPE_INTEGER:
                    $text[] = $var->getErrorMessageInteger();
                    break;
                case ANSWER_TYPE_STRING;
                /* fall through */

                case ANSWER_TYPE_OPEN;
                    $text[] = $var->getErrorMessagePattern();
                    $text[] = $var->getErrorMessageMinimumLength();
                    $text[] = $var->getErrorMessageMaximumLength();
                    $text[] = $var->getErrorMessageMinimumWords();
                    $text[] = $var->getErrorMessageMaximumWords();
                    break;
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                    $text[] = $var->getErrorMessageRange();
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                /* fall through */

                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getErrorMessageSelectMinimum();
                    $text[] = $var->getErrorMessageSelectMaximum();
                    $text[] = $var->getErrorMessageSelectExact();
                    $text[] = $var->getErrorMessageSelectInvalidSubset();
                    $text[] = $var->getErrorMessageSelectInvalidSet();
                    break;

                case ANSWER_TYPE_CALENDAR:
                    $text[] = $var->getErrorMessageMaximumCalendar();
                    break;
            }
        }
        // interactive
        if ($_SESSION['VRFILTERMODE_TYPE'] == 6 || $all == true) {
            $text[] = $var->getInlineStyle();
            $text[] = $var->getPageStyle();
            $text[] = $var->getPageJavascript();
            $text[] = $var->getInlineJavascript();
            $text[] = $var->getScripts();
        }

        // check for references
        $fills = getReferences(implode(" ", $text), INDICATOR_FILL);
        $fills1 = getReferences(implode(" ", $text), INDICATOR_FILL_NOVALUE);
        $fills2 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_ANSWER);
        $fills3 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_TEXT);
        $fills = array_unique(array_merge($fills, $fills1, $fills2, $fills3));
        $messages = $this->checkReferences($fills);

        // options text format check
        if ($_SESSION['VRFILTERMODE_TYPE'] == 0 || $all == true) {
            if (inArray($t, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                $options = explode("\r\n", $var->getOptionsText());
                foreach ($options as $option) {
                    $t = splitString("/ /", $option, PREG_SPLIT_NO_EMPTY, 2);
                    $code = trim($t[0]);

                    if (!is_numeric($code)) {
                        $messages[] = Language::messageCheckerVariableNumericOptionCodes($option);
                        break;
                    }

                    $labeltext = trim($t[1]);
                }
            }
        }
        
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 0 || $all == true) {
            if ($t == ANSWER_TYPE_CUSTOM) {
                $functions = array();
                $functions[] = $var->getAnswerTypeCustom();
                foreach ($functions as $f) {
                    if (stripos($f, '(') !== false) {
                        $f = substr($f, 0, stripos($f, '('));
                    }
                    if (!inArray(trim($f), array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
                        if (function_exists($f) == false) {
                            $messages[] = Language::messageCheckerFunctionNotExists($f);
                        } else {
                            if (!inArray($f, getAllowedCustomAnswerFunctions()) || inArray($f, getForbiddenCustomAnswerFunctions())) {
                                $messages[] = Language::messageCheckerFunctionNotAllowed($f);
                            }
                        }
                    }
                }
            }
        }
        
        // function reference check
        if ($_SESSION['VRFILTERMODE_VARIABLE'] == 6 || $all == true) {
            $functions = array();
            $functions[] = $var->getOnBack();
            $functions[] = $var->getOnNext();
            $functions[] = $var->getOnDK();
            $functions[] = $var->getOnRF();
            $functions[] = $var->getOnNA();
            $functions[] = $var->getOnUpdate();
            $functions[] = $var->getOnLanguageChange();
            $functions[] = $var->getOnModeChange();
            $functions[] = $var->getOnVersionChange();
            foreach ($functions as $f) {
                if (!inArray(trim($f), array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
                    if (function_exists($f) == false) {
                        $messages[] = Language::messageCheckerFunctionNotExists($f);
                    }
                    else {
                        if (!inArray($f, getAllowedOnChangeFunctions()) || inArray($f, getForbiddenOnChangeFunctions())) {
                            $messages[] = Language::messageCheckerFunctionNotAllowed($f);
                        }
                    }
                }
            }
        }

        return $messages;
    }

    function checkGroup($group, $all = false) {

        // general  
        $text = array();
        if (!isset($_SESSION['VRFILTERMODE_GROUP'])) {
            $_SESSION['VRFILTERMODE_GROUP'] = 0;
        }
        
        if ($_SESSION['VRFILTERMODE_GROUP'] == 0 || $all == true) {
            if ($group->getTemplate() == TABLE_TEMPLATE_CUSTOM) {
                $text[] = $group->getCustomTemplate();
            }
        }
        // verification
        if ($_SESSION['VRFILTERMODE_GROUP'] == 1 || $all == true) {
            $text[] = $group->getMinimumRequired();
            $text[] = $group->getMaximumRequired();
            $text[] = $group->getExactRequired();
        }
        // display
        if ($_SESSION['VRFILTERMODE_GROUP'] == 2 || $all == true) {
            $text[] = $group->getPageHeader();
            $text[] = $group->getPageFooter();
            $text[] = $group->getLabelBackButton();
            $text[] = $group->getLabelNextButton();
            $text[] = $group->getLabelDKButton();
            $text[] = $group->getLabelRFButton();
            $text[] = $group->getLabelNAButton();
            $text[] = $group->getLabelUpdateButton();
            $text[] = $group->getLabelRemarkButton();
            $text[] = $group->getLabelRemarkSaveButton();
            $text[] = $group->getLabelCloseButton();
            if (inArray($group->getTemplate(), array_keys(Common::surveyTableTemplates()))) {
                $text[] = $group->getTableID();
                $text[] = $group->getTableWidth();
                $text[] = $group->getQuestionColumnWidth();
            }
        }
        // assistance
        if ($_SESSION['VRFILTERMODE_GROUP'] == 3 || $all == true) {
            $text[] = $group->getErrorMessageExclusive();
            $text[] = $group->getErrorMessageInclusive();
            $text[] = $group->getErrorMessageMinimumRequired();
            $text[] = $group->getErrorMessageExactRequired();
            $text[] = $group->getErrorMessageMaximumRequired();
            $text[] = $group->getErrorMessageUniqueRequired();
        }
        // navigation
        if ($_SESSION['VRFILTERMODE_GROUP'] == 4 || $all == true) {
            $text[] = $group->getKeyboardBindingBack();
            $text[] = $group->getKeyboardBindingNext();
            $text[] = $group->getKeyboardBindingUpdate();
            $text[] = $group->getKeyboardBindingClose();
            $text[] = $group->getKeyboardBindingDK();
            $text[] = $group->getKeyboardBindingRF();
            $text[] = $group->getKeyboardBindingNA();
            $text[] = $group->getKeyboardBindingRemark();
        }

        // check for references
        $fills = getReferences(implode(" ", $text), INDICATOR_FILL);
        $fills1 = getReferences(implode(" ", $text), INDICATOR_FILL_NOVALUE);
        $fills2 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_ANSWER);
        $fills3 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_TEXT);
        $fills = array_unique(array_merge($fills, $fills1, $fills2, $fills3));
        $messages = $this->checkReferences($fills);

        // interactive
        if ($_SESSION['VRFILTERMODE_GROUP'] == 6 || $all == true) {
            $functions[] = $group->getOnBack();
            $functions[] = $group->getOnNext();
            $functions[] = $group->getOnDK();
            $functions[] = $group->getOnRF();
            $functions[] = $group->getOnNA();
            $functions[] = $group->getOnUpdate();
            $functions[] = $group->getOnLanguageChange();
            $functions[] = $group->getOnModeChange();
            $functions[] = $group->getOnVersionChange();
            foreach ($functions as $f) {
                if (!inArray(trim($f), array("", SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE))) {
                    if (function_exists($f) == false) {
                        $messages[] = Language::messageCheckerFunctionNotExists($f);
                    }
                    else {
                        if (!inArray($f, getAllowedOnChangeFunctions()) || inArray($f, getForbiddenOnChangeFunctions())) {
                            $messages[] = Language::messageCheckerFunctionNotAllowed($f);
                        }
                    }
                }
            }
        }

        return $messages;
    }

    function checkSurvey() {
        $survey = new Survey($this->suid);
        $text = array();
        $text[] = $var->getPageHeader();
    }

    function checkReferences($refs) {
        $messages = array();
        foreach ($refs as $r) {
            if (trim($r) != "") {
                if (contains($r, "[")) {
                    $this->checkArray($r, $messages);
                    $r = str_replace("[", "||", $r);
                    $r = str_replace("]", "||", $r);
                    $r = str_replace(".", "||", $r);
                    $r = str_replace(",", "||", $r);
                    $r = str_replace(" ", "", $r);

                    // replace any operators
                    $r = str_replace("-", "||", $r);
                    $r = str_replace("+", "||", $r);
                    $r = str_replace("*", "||", $r);
                    $r = str_replace("/", "||", $r);
                    $explode = explode("||", $r);
                    foreach ($explode as $e) {
                        if (trim($e) != "") {
                            if (!is_numeric($e)) {

                                // check for _1_ for set of enum
                                $v = $this->survey->getVariableDescriptiveByName(getBasicName($e));
                                if (inArray($v->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                                    $e = preg_replace("/(_[0-9]+_(\b|\[)){1}/", "", $e);
                                }

                                // check for associative key
                                $e = str_replace('"', "'", $e);
                                if (startsWith($e, "'") && endswith($e, "'")) {
                                    
                                } else {
                                    if ($v->getVsid() == "") {
                                        $sec = $this->survey->getSectionByName($e);
                                        if ($sec->getSeid() == "") {
                                            $messages[] = Language::messageCheckerVariableNotExists($e);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    if (!is_numeric($r)) {
                        $v = $this->survey->getVariableDescriptiveByName(getBasicName($r));
                        if (inArray($v->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                            $r = preg_replace("/(_[0-9]+_(\b|\[)){1}/", "", $r);
                        }
                        if ($v->getVsid() == "") {
                            $sec = $this->survey->getSectionByName($e);
                            if ($sec->getSeid() == "") {
                                $messages[] = Language::messageCheckerVariableNotExists($r);
                            }
                        } else {
                            $this->checkArray($r, $messages);
                        }
                    }
                }
            }
        }

        // return result
        return $messages;
    }

    function checkArray($str, &$messages) {

        // http://stackoverflow.com/questions/2938137/is-there-way-to-keep-delimiter-while-using-php-explode-or-other-similar-function
        $parts = preg_split('/([\[\., \]])/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($i = 0; $i < sizeof($parts); $i++) {
            $s = $parts[$i];

            // not a delimiter
            if (!inArray($s, array("[", "]", ".", " ", ","))) {
                // see if this is a variable 
                $v = $this->survey->getVariableDescriptiveByName(getBasicName($s));
                if ($v->getVsid() != "") {
                    if (isset($parts[$i + 1]) && $parts[$i + 1] == "[") { // bracket reference
                        if ($v->isArray() == false) {
                            $messages[] = Language::messageCheckerVariableNotArray($s);
                        }
                    } else {
                        if ($v->isArray() == true) {
                            $messages[] = Language::messageCheckerVariableArray($s);
                        }
                    }
                }
            }
        }
    }

}

?>