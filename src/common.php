<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Common {    
    
    static function surveyModes() {
        return array(MODE_CAPI => MODE_LABEL_CAPI, MODE_CASI => MODE_LABEL_CASI, MODE_CATI => MODE_LABEL_CATI, MODE_CADI => MODE_LABEL_CADI);
    }

    static function surveyTemplates() {
        return array(TABLE_TEMPLATE_DEFAULT => "One after another", TABLE_TEMPLATE_SIMPLE => "Row by row in table", TABLE_TEMPLATE_SINGLEROW => "Single row in table", TABLE_TEMPLATE_SINGLECOLUMN => "Single column in table", TABLE_TEMPLATE_TWOCOLUMN => "Two columns in table", TABLE_TEMPLATE_THREECOLUMN => 'Three column table', TABLE_TEMPLATE_FOURCOLUMN => 'Four column table', TABLE_TEMPLATE_FIVECOLUMN => 'Five column table', TABLE_TEMPLATE_SIXCOLUMN => 'Six column table', TABLE_TEMPLATE_SEVENCOLUMN => 'Seven column table', TABLE_TEMPLATE_EIGHTCOLUMN => 'Eight column table', TABLE_TEMPLATE_NINECOLUMN => 'Nine column table', TABLE_TEMPLATE_ENUMERATED => "Enumerated table rows", TABLE_TEMPLATE_CUSTOM => "Custom", TABLE_TEMPLATE_ENUMERATED_REVERSE => "Reverse enumerated table rows");
    }

    static function surveyTableTemplates() {
        return array(TABLE_TEMPLATE_SIMPLE => "Row by row in table", TABLE_TEMPLATE_SINGLEROW => "Single row in table", TABLE_TEMPLATE_SINGLECOLUMN => "Single column in table", TABLE_TEMPLATE_TWOCOLUMN => "Two columns in table", TABLE_TEMPLATE_THREECOLUMN => 'Three column table', TABLE_TEMPLATE_FOURCOLUMN => 'Four column table', TABLE_TEMPLATE_FIVECOLUMN => 'Five column table', TABLE_TEMPLATE_SIXCOLUMN => 'Six column table', TABLE_TEMPLATE_SEVENCOLUMN => 'Seven column table', TABLE_TEMPLATE_EIGHTCOLUMN => 'Eight column table', TABLE_TEMPLATE_NINECOLUMN => 'Nine column table', TABLE_TEMPLATE_ENUMERATED => "Enumerated table rows", TABLE_TEMPLATE_ENUMERATED_REVERSE => "Reverse enumerated table rows");
    }

    static function surveyTableEnumTables() {
        return array(TABLE_TEMPLATE_ENUMERATED => "Enumerated table rows", TABLE_TEMPLATE_ENUMERATED_REVERSE => "Reverse enumerated table rows");
    }

    static function surveyTableMultiColumnTables() {
        return array(TABLE_TEMPLATE_TWOCOLUMN => "Two column table", TABLE_TEMPLATE_THREECOLUMN => "Three column table", TABLE_TEMPLATE_FOURCOLUMN => 'Four column table', TABLE_TEMPLATE_FIVECOLUMN => 'Five column table', TABLE_TEMPLATE_SIXCOLUMN => 'Six column table', TABLE_TEMPLATE_SEVENCOLUMN => 'Seven column table', TABLE_TEMPLATE_EIGHTCOLUMN => 'Eight column table', TABLE_TEMPLATE_NINECOLUMN => 'Nine column table');
    }

    static function surveyInputMasks() {
        return array(INPUTMASK_INTEGER => "Integer", INPUTMASK_DOUBLE => "Real", INPUTMASK_USPHONE => "US Phone", INPUTMASK_USCURRENCY => "US currency", INPUTMASK_EUROCURRENCY => "Euro currency", INPUTMASK_EMAILSHORT => "Email", INPUTMASK_MEDICARE => "Medicare Number", INPUTMASK_SOCIAL => "Social Security Number", INPUTMASK_CUSTOM => "Custom");
    }

    static function surveyTables() {
        return array("_sections", "_variables", "_context", "_engines", "_next", "_routing", "_screens", "_settings", "_surveys", "_groups", "_tracks", "_progressbars", "_types", "_versions");
    }

    static function surveyDataTables() {
        return array("_actions", "_data", "_screendumps", "_states", "_times", "_paradata", "_logs", "_loopdata", "_consolidated_times", "_processed_paradata");
    }

    static function surveyTestDataTables() {
        return array("_test_actions", "_test_data", "_test_screendumps", "_test_states", "_test_times", "_test_loopdata", "_test_consolidated_times", "_test_processed_paradata");
    }
    
    static function surveyExportTables() {
        return array("_sections", "_variables", "_routing", "_settings", "_surveys", "_groups", "_tracks", "_types", "_versions");
    }
    
    static function allTables() {
        return array("_actions", "_communication", "_context","_consolidated_times","_data", "_datarecords","_engines","_files","_groups","_households","_interviewstatus","_issues","_lab","_logs","_loopdata","_next","_observations","_paradata","_processed_paradata","_pictures","_progressbars","_psus","_remarks","_respondents","_routing","_screendumps","_screens","_sections","_settings","_states","_surveys","_test_actions","_test_data","_test_datarecords","_test_files","_test_interviewstatus","_test_lab","_test_logs","_test_loopdata","_test_observations","_test_paradata","_test_processed_paradata", "_test_pictures","_test_screendumps","_test_states","_test_consolidated_times","_test_times","_times","_tracks","_types","_users","_variables","_versions");
    }

    static function surveyCoreVariables() {
        return array(VARIABLE_ACCESS, VARIABLE_BEGIN, VARIABLE_CLOSED, VARIABLE_COMPLETED, VARIABLE_DEVICE, VARIABLE_DIRECT, VARIABLE_END, VARIABLE_EXECUTION_MODE, VARIABLE_IN_PROGRESS, VARIABLE_INTRODUCTION, VARIABLE_LANGUAGE, VARIABLE_LOCKED, VARIABLE_LOGIN, VARIABLE_MODE, VARIABLE_PLATFORM, VARIABLE_PRIMKEY, VARIABLE_TEMPLATE, VARIABLE_THANKS, VARIABLE_VERSION);
    }

    static function surveyCoreSections() {
        return array(SECTION_BASE);
    }
        
    static function surveyOverallTemplates() {
        return array(0 => "Basic", 1 => "UAS", 2 => "MTeens", 3 => "Minimal");
    }
    
    static function errorCodes() {
        return array(
            ERROR_CHECK_REQUIRED => "ER1",
            ERROR_CHECK_PATTERN => "ER2",
            ERROR_CHECK_EMPTY => "ER3",
            ERROR_CHECK_NOTEMPTY => "ER4",
            ERROR_CHECK_MINLENGTH => "ER5",
            ERROR_CHECK_MAXLENGTH => "ER6",
            ERROR_CHECK_RANGELENGTH => "ER7",
            ERROR_CHECK_MIN => "ER8",
            ERROR_CHECK_MAX => "ER9",
            ERROR_CHECK_RANGE => "ER10",
            ERROR_CHECK_RANGE_CUSTOM => "ER11",
            ERROR_CHECK_EMAIL => "ER12",
            ERROR_CHECK_URL => "ER13",
            ERROR_CHECK_DATE => "ER14",
            ERROR_CHECK_DATEISO => "ER15",
            ERROR_CHECK_NUMBER => "ER16",
            ERROR_CHECK_INTEGER => "ER17",
            ERROR_CHECK_DIGITS => "ER18",
            ERROR_CHECK_EQUALTO => "ER19",
            ERROR_CHECK_NOTEQUALTO => "ER20",
            ERROR_CHECK_ALPHANUMERIC => "ER21",
            ERROR_CHECK_ZIPCODEUS => "ER22",
            ERROR_CHECK_LETTERSONLY => "ER23",
            ERROR_CHECK_MAXWORDS => "ER24",
            ERROR_CHECK_MINWORDS => "ER25",
            ERROR_CHECK_RANGEWORDS => "ER26",
            ERROR_CHECK_MINSELECTED => "ER27",
            ERROR_CHECK_EXACTSELECTED => "ER28",
            ERROR_CHECK_MAXSELECTED => "ER29",
            ERROR_CHECK_INVALIDSUBSELECTED => "ER30",
            ERROR_CHECK_INVALIDSELECTED => "ER31",
            ERROR_CHECK_MINSELECTEDDROPDOWN => "ER32",
            ERROR_CHECK_EXACTSELECTEDDROPDOWN => "ER33",
            ERROR_CHECK_MAXSELECTEDDROPDOWN => "ER34",
            ERROR_CHECK_INVALIDSUBSELECTEDDROPDOWN => "ER35",
            ERROR_CHECK_INVALIDSELECTEDDROPDOWN => "ER36",
            ERROR_CHECK_INLINE_EXCLUSIVE => "ER37",
            ERROR_CHECK_INLINE_INCLUSIVE => "ER38",
            ERROR_CHECK_INLINE_MINREQUIRED => "ER39",
            ERROR_CHECK_INLINE_MAXREQUIRED => "ER40",
            ERROR_CHECK_INLINE_EXACTREQUIRED => "ER41",
            ERROR_CHECK_INLINE_ANSWERED => "ER42",
            ERROR_CHECK_EXCLUSIVE => "ER43",
            ERROR_CHECK_INCLUSIVE => "ER44",
            ERROR_CHECK_MINREQUIRED => "ER45",
            ERROR_CHECK_MAXREQUIRED => "ER46",
            ERROR_CHECK_EXACTREQUIRED => "ER47",
            ERROR_CHECK_UNIQUEREQUIRED => "ER48",
            ERROR_CHECK_SAMEREQUIRED => "ER49",
            ERROR_CHECK_ENUMERATED_ENTERED => "ER50",
            ERROR_CHECK_SETOFENUMERATED_ENTERED => "ER51",
            ERROR_CHECK_COMPARISON_EQUAL_TO => "ER52",
            ERROR_CHECK_COMPARISON_NOT_EQUAL_TO => "ER53",
            ERROR_CHECK_COMPARISON_GREATER_EQUAL_TO => "ER54",
            ERROR_CHECK_COMPARISON_GREATER => "ER55",
            ERROR_CHECK_COMPARISON_SMALLER_EQUAL_TO => "ER56",
            ERROR_CHECK_COMPARISON_SMALLER => "ER57",
            ERROR_CHECK_SETOFENUM_COMPARISON_EQUAL_TO => "ER58",
            ERROR_CHECK_SETOFENUM_COMPARISON_NOT_EQUAL_TO => "ER59",
            ERROR_CHECK_SETOFENUM_COMPARISON_GREATER_EQUAL_TO => "ER60",
            ERROR_CHECK_SETOFENUM_COMPARISON_GREATER => "ER61",
            ERROR_CHECK_SETOFENUM_COMPARISON_SMALLER_EQUAL_TO => "ER62",
            ERROR_CHECK_SETOFENUM_COMPARISON_SMALLER => "ER63",
            ERROR_CHECK_COMPARISON_EQUAL_TO_STRING => "ER64",
            ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_STRING => "ER65",
            ERROR_CHECK_COMPARISON_EQUAL_TO_STRING_IGNORE_CASE => "ER66",
            ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_STRING_IGNORE_CASE => "ER67",
            ERROR_CHECK_COMPARISON_EQUAL_TO_DATETIME => "ER68",
            ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_DATETIME => "ER69",
            ERROR_CHECK_COMPARISON_GREATER_EQUAL_TO_DATETIME => "ER70",
            ERROR_CHECK_COMPARISON_GREATER_DATETIME => "ER71",
            ERROR_CHECK_COMPARISON_SMALLER_EQUAL_TO_DATETIME => "ER72",
            ERROR_CHECK_COMPARISON_SMALLER_DATETIME => "ER73",
            ERROR_CHECK_COMPARISON_EQUAL_TO_TIME => "ER74",
            ERROR_CHECK_COMPARISON_NOT_EQUAL_TO_TIME => "ER75",
            ERROR_CHECK_COMPARISON_GREATER_EQUAL_TO_TIME => "ER76",
            ERROR_CHECK_COMPARISON_GREATER_TIME => "ER77",
            ERROR_CHECK_COMPARISON_SMALLER_EQUAL_TO_TIME => "ER78",
            ERROR_CHECK_COMPARISON_SMALLER_TIME => "ER79",
            ERROR_CHECK_MINRANKED => "ER80",
            ERROR_CHECK_MAXRANKED => "ER81",
            ERROR_CHECK_EXACTRANKED => "ER82",
        );
    }    
    
    static function getAnswerTypes() {
        return array(ANSWER_TYPE_STRING => Language::labelAnswerTypeString(),
            ANSWER_TYPE_OPEN => Language::labelAnswerTypeOpen(),
            ANSWER_TYPE_ENUMERATED => Language::labelAnswerTypeEnumerated(),
            ANSWER_TYPE_DROPDOWN => Language::labelAnswerTypeDropDown(),
            ANSWER_TYPE_SETOFENUMERATED => Language::labelAnswerTypeSetOfEnumerated(),
            ANSWER_TYPE_MULTIDROPDOWN => Language::labelAnswerTypeMultiDropDown(),
            ANSWER_TYPE_INTEGER => Language::labelAnswerTypeInteger(),
            ANSWER_TYPE_DOUBLE => Language::labelAnswerTypeDouble(),
            ANSWER_TYPE_RANGE => Language::labelAnswerTypeRange(),
            ANSWER_TYPE_KNOB => Language::labelAnswerTypeKnob(),
            ANSWER_TYPE_SLIDER => Language::labelAnswerTypeSlider(),
            ANSWER_TYPE_RANK => Language::labelAnswerTypeRank(),
            ANSWER_TYPE_DATE => Language::labelAnswerTypeDate(),
            ANSWER_TYPE_TIME => Language::labelAnswerTypeTime(),
            ANSWER_TYPE_DATETIME => Language::labelAnswerTypeDateTime(),
            ANSWER_TYPE_CALENDAR => Language::labelAnswerTypeCalendar(),
            ANSWER_TYPE_NONE => Language::labelAnswerTypeNone(),
            ANSWER_TYPE_SECTION => Language::labelAnswerTypeSection(),
            ANSWER_TYPE_CUSTOM => Language::labelAnswerTypeCustom());
    }
    
}

?>