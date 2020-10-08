<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Translator {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {

        if (getFromSessionParams('page') != null) {
            if (loadvar('updatesessionpage') != 2) {
                $_SESSION['LASTPAGE'] = getFromSessionParams('page');
            }
            /* called via jquery .post to load into div */ else {

                switch (getFromSessionParams('page')) {
                    case "translator.history": return $this->showHistory();
                        break;
                    case "translator.search": return $this->showSearch();
                        break;
                    case "translator.search.hide": return $this->showSearchHide();
                        break;
                }

                /* stop */
                return;
            }
        };

        if (isset($_SESSION['LASTPAGE'])) {
            if (loadvar("ignoreres") == 1) {
                if (endsWith($_SESSION['LASTPAGE'], "res")) {
                    $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strlen($_SESSION['LASTPAGE']) - strlen("res")); // avoid form resubmit
                }
            }
            switch ($_SESSION['LASTPAGE']) {
                case "translator.home": return $this->mainPage();
                    break;
                case 'translator.surveys': return $this->showSurveys();
                    break;
                case 'translator.survey': return $this->showSurvey();
                    break;

                case 'translator.survey.section': return $this->showSection();
                    break;

                case 'translator.survey.settings': return $this->showSettings();
                    break;
                case 'translator.survey.translatesettingsassistance': return $this->showTranslateSettingsAssistance();
                    break;
                case 'translator.survey.translatesettingsassistanceres': return $this->showTranslateSettingsAssistanceRes();
                    break;
                case 'translator.survey.translatesettingslayout': return $this->showTranslateSettingsLayout();
                    break;
                case 'translator.survey.translatesettingslayoutres': return $this->showTranslateSettingsLayoutRes();
                    break;

                case 'translator.survey.groups': return $this->showGroups();
                    break;
                case "translator.survey.group": return $this->showTranslateGroup();
                    break;
                case "translator.survey.translategroup": return $this->showTranslateGroup();
                    break;
                case "translator.survey.translategrouplayout": return $this->showTranslateGroup();
                    break;
                case "translator.survey.translategrouplayoutres": return $this->showTranslateGroupLayoutRes();
                    break;
                case "translator.survey.translategroupassistance": return $this->showTranslateGroup();
                    break;
                case "translator.survey.translategroupassistanceres": return $this->showTranslateGroupAssistanceRes();
                    break;

                case 'translator.survey.variables': return $this->showVariables();
                    break;
                case 'translator.survey.translatevariable': return $this->showTranslateVariable();
                    break;
                case 'translator.survey.translatevariablegeneral': return $this->showTranslateVariable();
                    break;
                case 'translator.survey.translatevariablegeneralres': return $this->showTranslateVariableGeneralRes();
                    break;
                case 'translator.survey.translatevariableassistance': return $this->showTranslateVariable();
                    break;
                case 'translator.survey.translatevariableassistanceres': return $this->showTranslateVariableAssistanceRes();
                    break;
                case 'translator.survey.translatevariablelayout': return $this->showTranslateVariable();
                    break;
                case 'translator.survey.translatevariablelayoutres': return $this->showTranslateVariableLayoutRes();
                    break;
                case 'translator.survey.translatevariablefill': return $this->showTranslateVariable();
                    break;
                case 'translator.survey.translatevariablefillres': return $this->showTranslateVariableFillRes();
                    break;

                case 'translator.survey.types': return $this->showTypes();
                    break;
                case 'translator.survey.translatetype': return $this->showTranslateType();
                    break;
                case 'translator.survey.translatetypegeneral': return $this->showTranslateType();
                    break;
                case 'translator.survey.translatetypegeneralres': return $this->showTranslateTypeGeneralRes();
                    break;
                case 'translator.survey.translatetypeassistance': return $this->showTranslateType();
                    break;
                case 'translator.survey.translatetypeassistanceres': return $this->showTranslateTypeAssistanceRes();
                    break;
                case 'translator.survey.translatetypelayout': return $this->showTranslateType();
                    break;
                case 'translator.survey.translatetypelayoutres': return $this->showTranslateTypeLayoutRes();
                    break;

                case "translator.output": return $this->showOutput();
                    break;
                case "translator.output.documentation": return $this->showOutputDocumentation();
                    break;
                case "translator.output.documentation.translation": return $this->showOutputTranslation();
                    break;
                case "translator.tools": return $this->showTools();
                    break;
                case "translator.tools.test": return $this->showTest();
                    break;

                case "translator.preferences": return $this->showPreferences();
                    break;
                case "translator.preferences.res": return $this->showPreferencesRes();
                    break;

                case "translator.search": return $this->showSearch();
                    break;
                case "translator.search.hide": return $this->showSearchHide();
                    break;

                default: return $this->mainPage();
            }
        } else {
            return $this->mainPage();
        }
    }

    function mainPage() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showMain();
    }

    function showSurveys() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showSurveys();
    }

    function showSurvey() {
        if (getFromSessionParams('suid') != '') {
            $_SESSION['SUID'] = getFromSessionParams('suid');
        }
        if (loadvar("vrfiltermode_survey") != '') {
            $_SESSION['VRFILTERMODE_SURVEY'] = loadvar("vrfiltermode_survey");
        }
        $displaySysAdmin = new DisplaySysAdmin();
        if (!isset($_SESSION['SEID'])) {
            $_SESSION['SEID'] = 1;
        } //default root module
        if (!isset($_SESSION['VRFILTERMODE_SECTION'])) {
            $_SESSION['VRFILTERMODE_SECTION'] = 0;
        } //default show variables
        if (!isset($_SESSION['VRFILTERMODE_SURVEY'])) {
            $_SESSION['VRFILTERMODE_SURVEY'] = 0;
        } //default show variables

        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showSurvey();
    }

    /* SECTION FUNCTIONS */

    function showSection($message = '', $seid = '') {
        $displayTranslator = new DisplayTranslator();
        if (getFromSessionParams('seid') != '') {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        } else if ($seid != '') {
            $_SESSION['SEID'] = $seid;
        }
        if (loadvar("vrfiltermode_section") != '') {
            $_SESSION['VRFILTERMODE_SECTION'] = loadvar("vrfiltermode_section");
        }
        return $displayTranslator->showSection($_SESSION['SEID'], $message);
    }

    /* SETTINGS FUNCTIONS */

    function showSettings() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showSettings();
    }

    function showTranslateSettingsAssistance() {
        $displayTranslator = new DisplayTranslator();
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SETTING'] = 1;
        return $displayTranslator->showTranslateSettingsAssistance();
    }

    function showTranslateSettingsAssistanceRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        $survey->setEmptyMessage(loadvarAllowHTML(SETTING_EMPTY_MESSAGE));
        $survey->setErrorMessageMinimumLength(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH));
        $survey->setErrorMessageMaximumLength(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH));
        $survey->setErrorMessageMinimumWords(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_WORDS));
        $survey->setErrorMessageMaximumWords(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS));
        $survey->setErrorMessagePattern(loadvarAllowHTML(SETTING_ERROR_MESSAGE_PATTERN));
        $survey->setErrorMessageDouble(loadvarAllowHTML(SETTING_ERROR_MESSAGE_DOUBLE));
        $survey->setErrorMessageInteger(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INTEGER));
        $survey->setErrorMessageSelectMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_SELECT));
        $survey->setErrorMessageSelectMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT));
        $survey->setErrorMessageSelectExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_SELECT));
        $survey->setErrorMessageSelectInvalidSubset(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT));
        $survey->setErrorMessageSelectInvalidSet(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SELECT));
        $survey->setErrorMessageRange(loadvarAllowHTML(SETTING_ERROR_MESSAGE_RANGE));
        $survey->setErrorMessageMaximumCalendar(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR));

        $survey->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
        $survey->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
        $survey->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
        $survey->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
        $survey->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
        $survey->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));

        $survey->setErrorMessageExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_REQUIRED));
        $survey->setErrorMessageMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED));
        $survey->setErrorMessageMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED));
        $survey->setErrorMessageExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXCLUSIVE));
        $survey->setErrorMessageInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INCLUSIVE));
        $survey->setErrorMessageUniqueRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED));

        $survey->save();
        $content = $displayTranslator->displaySuccess(Language::messageAssistanceTextsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displayTranslator->showTranslateSettingsAssistance($content);
    }

    function showTranslateSettingsLayout() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displayTranslator = new DisplayTranslator();
        $_SESSION['VRFILTERMODE_SETTING'] = 4;
        return $displayTranslator->showTranslateSettingsLayout();
    }

    function showTranslateSettingsLayoutRes() {
        $displayTranslator = new DisplayTranslator();
        $_SESSION['EDITSURVEY'] = 1;
        $survey = new Survey($_SESSION['SUID']);

        $survey->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $survey->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $survey->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $survey->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $survey->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $survey->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $survey->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $survey->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));
        $survey->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));
        $survey->setEnumeratedTextboxLabel(loadvar(SETTING_ENUMERATED_TEXTBOX_LABEL));

        $survey->save();
        $content = $displayTranslator->displaySuccess(Language::messageDisplayTextsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displayTranslator->showTranslateSettingsLayout($content);
    }

    /* VARIABLE FUNCTIONS */

    function showTranslateVariable($vsid = "") {
        $displayTranslator = new DisplayTranslator();
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        } else if ($vsid != "") {
            $_SESSION['VSID'] = $vsid;
        }
        if (loadvar("vrfiltermode_variable") != '') {
            $_SESSION['VRFILTERMODE_VARIABLE'] = loadvar("vrfiltermode_variable");
        }

        $survey = new Survey($_SESSION['SUID']);
        $variable = $survey->getVariableDescriptive($_SESSION['VSID']);
        $answertype = $variable->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($variable->getTyd());
            $answertype = $type->getAnswerType();
        }
        if (inArray($answertype, array(ANSWER_TYPE_SECTION))) {
            $_SESSION['VRFILTERMODE_VARIABLE'] = 0;
        } else if (inArray($answertype, array(ANSWER_TYPE_NONE)) && !inArray($_SESSION['VRFILTERMODE_VARIABLE'], array(0, 2, 5))) {
            $_SESSION['VRFILTERMODE_VARIABLE'] = 0;
        }

        /* update section id */
        $_SESSION['SEID'] = $variable->getSeid();
        return $displayTranslator->showTranslateVariable($_SESSION['VSID']);
    }

    function showTranslateVariableGeneralRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $_SESSION['VSID'] = $vsid;
        $content = "";
        $variable = $survey->getVariableDescriptive($vsid);
        $content = $displayTranslator->displaySuccess(Language::messageVariableChanged($variable->getName()));

        //$variable->setDescription(loadvar(SETTING_DESCRIPTION));
        $variable->setQuestion(loadvarAllowHTML(SETTING_QUESTION));
        $variable->setOptionsText(loadvarAllowHTML(SETTING_OPTIONS));
        $variable->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($variable));
        $mess = $compiler->generateGetFills(array($variable));
        $mess = $compiler->generateInlineFields(array($variable));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($vsid != '') {
            return $displayTranslator->showTranslateVariable($_SESSION['VSID'], $content);
        } else {
            return $this->showSection($content);
        }
    }

    function showTranslateVariableLayoutRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;

        $var->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $var->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $var->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $var->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $var->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $var->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $var->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $var->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));
        $var->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));
        $content = $displayTranslator->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateVariable($_SESSION['VSID'], $content);
    }

    function showTranslateVariableAssistanceRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;

        $var->setPreText(loadvarAllowHTML(SETTING_PRETEXT));
        $var->setPostText(loadvarAllowHTML(SETTING_POSTTEXT));
        $var->setHoverText(loadvarAllowHTML(SETTING_HOVERTEXT));
        $var->setEmptyMessage(loadvarAllowHTML(SETTING_EMPTY_MESSAGE));

        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }

        switch ($answertype) {
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $var->setErrorMessageMinimumLength(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH));
                $var->setErrorMessageMaximumLength(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH));
                $var->setErrorMessageMinimumWords(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_WORDS));
                $var->setErrorMessageMaximumWords(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS));
                $var->setErrorMessagePattern(loadvarAllowHTML(SETTING_ERROR_MESSAGE_PATTERN));
                break;
            case ANSWER_TYPE_DOUBLE:
                $var->setErrorMessageDouble(loadvarAllowHTML(SETTING_ERROR_MESSAGE_DOUBLE));
                break;
            case ANSWER_TYPE_INTEGER:
                $var->setErrorMessageInteger(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INTEGER));
                break;
            case ANSWER_TYPE_ENUMERATED:
                $var->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
                $var->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
                $var->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
                $var->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
                $var->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
                $var->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $var->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
                $var->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
                $var->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
                $var->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
                $var->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
                $var->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $var->setErrorMessageSelectMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_SELECT));
                $var->setErrorMessageSelectMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT));
                $var->setErrorMessageSelectExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_SELECT));
                $var->setErrorMessageSelectInvalidSubset(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT));
                $var->setErrorMessageSelectInvalidSet(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SELECT));
                break;
            case ANSWER_TYPE_RANGE:
            /* fall through */
            case ANSWER_TYPE_KNOB:
            /* fall through */
            case ANSWER_TYPE_SLIDER:
                $var->setErrorMessageRange(loadvarAllowHTML(SETTING_ERROR_MESSAGE_RANGE));
                break;
            case ANSWER_TYPE_CALENDAR:
                $var->setErrorMessageMaximumCalendar(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR));
                break;
        }

        $content = $displayTranslator->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateVariable($_SESSION['VSID'], $content);
    }

    function showTranslateVariableFillRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;
        $var->setFillText(loadvarAllowHTML(SETTING_FILLTEXT));
        $content = $displayTranslator->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateVariable($_SESSION['VSID'], $content);
    }

    /* TYPE FUNCTIONS */

    function showTypes() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showTypes();
    }

    function showTypesTranslate() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showTypesTranslate();
    }

    function showTranslateType() {
        $displayTranslator = new DisplayTranslator();
        if (getFromSessionParams('tyd') != "") {
            $_SESSION['TYD'] = getFromSessionParams('tyd');
        }
        if (loadvar("vrfiltermode_type") != '') {
            $_SESSION['VRFILTERMODE_TYPE'] = loadvar("vrfiltermode_type");
        }

        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($_SESSION['TYD']);
        $answertype = $type->getAnswerType();
        if (inArray($answertype, array(ANSWER_TYPE_SECTION))) {
            $_SESSION['VRFILTERMODE_TYPE'] = 0;
        } else if (inArray($answertype, array(ANSWER_TYPE_NONE)) && !inArray($_SESSION['VRFILTERMODE_TYPE'], array(0, 2, 5))) {
            $_SESSION['VRFILTERMODE_TYPE'] = 0;
        }
        return $displayTranslator->showTranslateType($_SESSION['TYD']);
    }

    function showTranslateTypeGeneralRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $content = "";
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;
        $content = $displayTranslator->displaySuccess(Language::messageTypeChanged($type->getName()));
        $type->setOptionsText(loadvarAllowHTML(SETTING_OPTIONS));
        $type->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));

        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);
        $mess = $compiler->generateGetFills($vars);
        $mess = $compiler->generateInlineFields($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($tyd != '') {
            return $displayTranslator->showTranslateType($_SESSION['TYD'], $content);
        } else {
            return $displayTranslator->showSurvey($content);
        }
    }

    function showTranslateTypeLayoutRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;

        $type->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $type->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $type->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $type->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $type->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $type->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $type->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $type->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));
        $type->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));
        $content = $displayTranslator->displaySuccess(Language::messageTypeChanged($type->getName()));
        $type->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);
        $mess = $compiler->generateGetFills($vars);
        $mess = $compiler->generateInlineFields($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateType($_SESSION['TYD'], $content);
    }

    function showTranslateTypeAssistanceRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;

        $type->setPreText(loadvarAllowHTML(SETTING_PRETEXT));
        $type->setPostText(loadvarAllowHTML(SETTING_POSTTEXT));
        $type->setHoverText(loadvarAllowHTML(SETTING_HOVERTEXT));
        if (loadvar(SETTING_EMPTY_MESSAGE) != "") {
            $type->setEmptyMessage(loadvarAllowHTML(SETTING_EMPTY_MESSAGE));
        }

        $t = $type->getAnswerType();
        switch ($t) {
            case ANSWER_TYPE_STRING:
            /* fall through */
            case ANSWER_TYPE_OPEN:
                $type->setErrorMessageMinimumLength(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_LENGTH));
                $type->setErrorMessageMaximumLength(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH));
                $type->setErrorMessageMinimumWords(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_WORDS));
                $type->setErrorMessageMaximumWords(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_WORDS));
                $type->setErrorMessagePattern(loadvarAllowHTML(SETTING_ERROR_MESSAGE_PATTERN));
                break;
            case ANSWER_TYPE_DOUBLE:
                $type->setErrorMessageDouble(loadvarAllowHTML(SETTING_ERROR_MESSAGE_DOUBLE));
                break;
            case ANSWER_TYPE_INTEGER:
                $type->setErrorMessageInteger(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INTEGER));
                break;
            case ANSWER_TYPE_ENUMERATED:
                $type->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
                $type->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
                $type->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
                $type->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
                $type->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
                $type->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $type->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
                $type->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
                $type->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
                $type->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
                $type->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
                $type->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $type->setErrorMessageSelectMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_SELECT));
                $type->setErrorMessageSelectMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT));
                $type->setErrorMessageSelectExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_SELECT));
                $type->setErrorMessageSelectInvalidSubset(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT));
                $type->setErrorMessageSelectInvalidSet(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SELECT));
                break;
            case ANSWER_TYPE_RANGE:
            /* fall through */
            case ANSWER_TYPE_KNOB:
            /* fall through */
            case ANSWER_TYPE_SLIDER:
                $type->setErrorMessageRange(loadvarAllowHTML(SETTING_ERROR_MESSAGE_RANGE));
                break;
            case ANSWER_TYPE_CALENDAR:
                $type->setErrorMessageMaximumCalendar(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR));
                break;
        }


        $content = $displayTranslator->displaySuccess(Language::messageTypeChanged($type->getName()));
        $type->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);
        $mess = $compiler->generateGetFills($vars);
        $mess = $compiler->generateInlineFields($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateType($_SESSION['TYD'], $content);
    }

    /* GROUP FUNCTIONS */

    function showTranslateGroup($gid = '') {
        $displayTranslator = new DisplayTranslator();
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        } else if ($gid != "") {
            $_SESSION['GID'] = $gid;
        }
        if (loadvar("vrfiltermode_group") != '') {
            $_SESSION['VRFILTERMODE_GROUP'] = loadvar("vrfiltermode_group");
        }
        return $displayTranslator->showTranslateGroup($_SESSION['GID']);
    }

    function showTranslateGroupLayoutRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;

        $group->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $group->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $group->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $group->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $group->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $group->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $group->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $group->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));
        $group->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));

        $content = $displayTranslator->displaySuccess(Language::messageGroupChanged($group->getName()));
        $group->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        $mess = $compiler->generateGetFillsGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateGroup($_SESSION['GID'], $content);
    }

    function showTranslateGroupAssistanceRes() {
        $displayTranslator = new DisplayTranslator();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;

        $group->setEmptyMessage(loadvarAllowHTML(SETTING_EMPTY_MESSAGE));
        $group->setErrorMessageExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_REQUIRED));
        $group->setErrorMessageMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED));
        $group->setErrorMessageMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED));
        $group->setErrorMessageExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXCLUSIVE));
        $group->setErrorMessageInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INCLUSIVE));
        $group->SetErrorMessageUniqueRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED));

        $content = $displayTranslator->displaySuccess(Language::messageGroupChanged($group->getName()));
        $group->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        $mess = $compiler->generateGetFillsGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displayTranslator->showTranslateGroup($_SESSION['GID'], $content);
    }

    /* OUTPUT FUNCTIONS */

    function showOutput() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutput();
    }

    function showOutputDocumentation() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDocumentation();
    }

    function showOutputTranslation() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputTranslation();
    }

    /* TOOLS FUNCTIONS */

    function showTools() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showTools();
    }

    function showTest() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showTest();
    }

    /* PREFERENCES FUNCTIONS */

    function showPreferences() {
        $displayTranslator = new DisplayTranslator();
        return $displayTranslator->showPreferences();
    }

    function showPreferencesRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], ".res"));

        $user = new User($_SESSION['URID']);
        $user->setNavigationInBreadCrumbs(loadvar('navigationinbreadcrumbs'));
        $user->saveChanges();
        $displayTranslator = new DisplayTranslator();
        $content = $displayTranslator->displaySuccess(Language::messagePreferencesSaved());
        return $displayTranslator->showPreferences($content);
    }

    /* search */

    function showSearch() {
        $displaySearch = new DisplaySearch();
        return $displaySearch->showSearchTranslator(loadvarAllowHTML("search"));
    }

    function showSearchHide() {
        $displaySearch = new DisplaySearch();
        return $displaySearch->hideSearch();
    }

}

?>