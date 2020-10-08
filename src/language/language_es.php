<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Language extends LanguageBase {

    static function defaultDisplayOverviewAddressColumns() {
        return array('address1_dec' => 'Room', 'city_dec' => 'Location');
    }

    static function defaultDisplayInfoAddressColumns() {
        return array('address1_dec' => 'Room', 'city_dec' => 'Location');
    }

    static function defaultDisplayInfo2AddressColumns() {
        return array('telephone1_dec' => 'Telephone');
    }

    static function surveyChangeMode() {
        return 'Interview mode';
    }

    static function surveyChangeLanguage() {
        return 'Language';
    }

    /* SMS/SURVEY BUTTONS  */

    static function buttonNext() {
        return 'Next >>';
    }

    static function buttonBack() {
        return '<< Back';
    }

    static function buttonUpdate() {
        return 'Update';
    }

    static function buttonStart() {
        return 'Start';
    }

    static function buttonStartSurvey() {
        return 'Start Interview';
    }

    static function buttonDK() {
        return 'Don`t know';
    }

    static function buttonRF() {
        return 'Refuse';
    }

    static function buttonNA() {
        return 'Not applicable';
    }

    static function buttonRemark() {
        return 'Remark';
    }

    static function buttonRemarkSave() {
        return 'Save';
    }

    static function buttonClose() {
        return 'Cancel';
    }

    static function buttonJavascriptContinue() {
        return 'Next';
    }

    static function buttonClean() {
        return 'Clean';
    }

    static function buttonClear() {
        return 'Clear';
    }

    static function buttonCompile() {
        return 'Compile';
    }

    static function buttonExport() {
        return 'Export';
    }

    static function buttonImport() {
        return 'Import';
    }

    static function buttonTest() {
        return 'Test';
    }

    static function buttonFlood() {
        return 'Flood';
    }

    static function buttonCheck() {
        return 'Check';
    }

    static function buttonView() {
        return 'View';
    }

    static function buttonGenerate() {
        return 'Generate';
    }

    static function buttonTranslate() {
        return 'Translate';
    }

    static function buttonMove() {
        return 'Move';
    }

    static function buttonRefactor() {
        return 'Rename';
    }

    static function buttonDownload() {
        return 'Download';
    }

    static function buttonJump() {
        return 'Jump back';
    }

    /* SURVEY error checking */

    /*
     * 
     * required: "This field is required.",
      remote: "Please fix this field.",
      email: "Please enter a valid email address.",
      url: "Please enter a valid URL.",
      date: "Please enter a valid date.",
      dateISO: "Please enter a valid date (ISO).",
      number: "Please enter a valid number.",
      digits: "Please enter only digits.",
      creditcard: "Please enter a valid credit card number.",
      equalTo: "Please enter the same value again.",
      maxlength: $.validator.format("Please enter no more than {0} characters."),
      minlength: $.validator.format("Please enter at least {0} characters."),
      rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
      range: $.validator.format("Please enter a value between {0} and {1}."),
      max: $.validator.format("Please enter a value less than or equal to {0}."),
      min: $.validator.format("Please enter a value greater than or equal to {0}.")
     * 
     */

    static function errorCheckRequired() {
        return "Please provide an answer.";
    }

    static function errorCheckNotRequired() {
        return "Please do not provide an answer.";
    }

    static function errorCheckEmail() {
        return "Please provide a valid email address.";
    }

    static function errorCheckMaximumCalendar() {
        return 'todo here';
    }

    static function errorCheckInteger() {
        return "Please enter a whole number without any leading zeroes or decimal points.";
    }

    static function errorCheckDouble() {
        return "Please enter a number.";
    }

    static function errorCheckRange() {
        return "Please enter a number between " . PLACEHOLDER_MINIMUM . " and " . PLACEHOLDER_MAXIMUM . ".";
    }

    static function errorCheckPattern() {
        return "Please be sure that the pattern " . PLACEHOLDER_PATTERN . " is satisfied.";
    }

    static function errorCheckMinLength() {
        return "Please enter a minimum of " . PLACEHOLDER_MINIMUM_LENGTH . " characters.";
    }

    static function errorCheckMaxLength() {
        return "Please enter a maximum of " . PLACEHOLDER_MAXIMUM_LENGTH . " characters.";
    }

    static function errorCheckMinWords() {
        return "Please enter a minimum of " . PLACEHOLDER_MINIMUM_WORDS . " words.";
    }

    static function errorCheckMaxWords() {
        return "Please enter a maximum of " . PLACEHOLDER_MAXIMUM_WORDS . " words.";
    }

    static function errorCheckSelectMin() {
        return "Please check at least " . PLACEHOLDER_MINIMUM_SELECTED . " boxes.";
    }

    static function errorCheckSelectExact() {
        return "Please check exactly " . PLACEHOLDER_EXACT_SELECTED . " boxes.";
    }

    static function errorCheckSelectMax() {
        return "Please check at most " . PLACEHOLDER_MAXIMUM_SELECTED . " boxes.";
    }

    static function errorCheckSelectInvalidSubset() {
        return "Please do not select the combination of " . PLACEHOLDER_INVALIDSUBSET_SELECTED . " as part of your answer.";
    }

    static function errorCheckSelectInvalidSet() {
        return "Please do not select the combination of " . PLACEHOLDER_INVALIDSET_SELECTED . ".";
    }

    static function errorCheckExclusive() {
        return "Please provide an answer to one question.";
    }

    static function errorCheckInclusive() {
        return "Please provide an answer to all questions.";
    }
    
    static function errorCheckRankMin() {
        return "Please rank at least " . PLACEHOLDER_MINIMUM_RANKED . " options.";
    }

    static function errorCheckRankExact() {
        return "Please rank exactly " . PLACEHOLDER_EXACT_RANKED . " options.";
    }

    static function errorCheckRankMax() {
        return "Please rank at most " . PLACEHOLDER_MAXIMUM_RANKED . " options.";
    }

    static function errorCheckMinRequired() {
        return "Please provide an answer to at least " . PLACEHOLDER_MINIMUM_REQUIRED . " question(s).";
    }

    static function errorCheckMaxRequired() {
        return "Please provide an answer to at most " . PLACEHOLDER_MAXIMUM_REQUIRED . " question(s).";
    }

    static function errorCheckExactRequired() {
        return 'Please provide an answer to exactly ' . PLACEHOLDER_EXACT_REQUIRED . ' question(s).';
    }

    static function errorCheckUniqueRequired() {
        return 'Please provide an unique answer for each of the question(s).';
    }

    static function errorCheckSameRequired() {
        return 'Please provide the same answer for each of the question(s).';
    }

    static function errorCheckEnumeratedEntered() {
        return 'Please enter a valid answer code';
    }

    static function errorCheckSetOfEnumeratedEntered() {
        return 'Please enter only valid answer code(s)';
    }

    static function errorCheckComparisonEqualTo() {
        return 'Please enter an answer equal to';
    }

    static function errorCheckComparisonEqualToIgnoreCase() {
        return 'Please enter an answer equal to (ignoring case)';
    }

    static function errorCheckComparisonNotEqualTo() {
        return 'Please enter an answer not equal to';
    }

    static function errorCheckComparisonNotEqualToIgnoreCase() {
        return 'Please enter an answer not equal to (ignoring case)';
    }

    static function errorCheckComparisonGreaterEqualTo() {
        return 'Please enter an answer greater than or equal to';
    }

    static function errorCheckComparisonGreater() {
        return 'Please enter an answer greater than';
    }

    static function errorCheckComparisonSmallerEqualTo() {
        return 'Please enter an answer smaller than or equal to';
    }

    static function errorCheckComparisonSmaller() {
        return 'Please enter an answer smaller than';
    }

    static function errorRoutingLine() {
        return 'Line';
    }

    static function labelInlineEditExclusive() {
        return 'Only one question in selected option(s) may be answered';
    }

    static function labelInlineEditInclusive() {
        return 'All questions in selected option(s) must be answered';
    }

    static function labelInlineEditMinRequired() {
        return 'Minimum number of answered questions in selected option(s)';
    }

    static function labelInlineEditMaxRequired() {
        return 'Maximum number of answered questions in selected option(s)';
    }

    static function labelInlineEditExactRequired() {
        return 'Exact number of answered questions in selected option(s)';
    }

    static function errorCheckInlineAnswered() {
        return 'Please be sure to select the option(s) containing the filled out question(s).';
    }

    static function errorCheckInlineExclusive() {
        return "Please be sure to fill out only one question in the selected option(s).";
    }

    static function errorCheckInlineInclusive() {
        return "Please be sure to fill out all the questions in the selected option(s).";
    }

    static function errorCheckInlineMinRequired() {
        return "Please be sure to fill out at least " . PLACEHOLDER_INLINE_MINIMUM_REQUIRED . " question(s) in the selected option(s).";
    }

    static function errorCheckInlineMaxRequired() {
        return "Please be sure to fill out at most " . PLACEHOLDER_INLINE_MAXIMUM_REQUIRED . " question(s) in the selected option(s).";
    }

    static function errorCheckInlineExactRequired() {
        return "Please be sure to fill out exactly " . PLACEHOLDER_INLINE_EXACT_REQUIRED . " question(s) in the selected option(s).";
    }

    static function errorCheckInlineMaximumCalendar() {
        return "Please select at most " . PLACEHOLDER_MAXIMUM_CALENDAR . " dates.";
    }

    static function errorDirectLogin() {
        return "This survey is only accessible from an external page.";
    }

    static function errorLocked() {
        return "A problem occurred. Please contact the survey administrator.";
    }

    static function errorInProgress() {
        return "Your previous request is still being processed. Please wait a bit before attempting to refresh this page again in order to continue the survey. Please contact the survey administrator if the problem persists.";
    }

    static function errorCompleted() {
        return "We already received your responses, thank you!";
    }

    static function messageThanks() {
        return "This is the end of the interview, thank you!";
    }

    /* compiler errors */

    static function errorAssignmentInvalid() {
        return 'Invalid assignment statement';
    }

    static function errorCheckReturnInvalid() {
        return 'Invalid return error statement';
    }

    static function errorInspectInvalid() {
        return 'Invalid .INSPECT statement';
    }

    static function errorVariableInvalid() {
        return 'Invalid variable statement';
    }

    static function errorVariableNoArrayIndex($f) {
        return 'Variable ' . $f . ' is an array and requires an array index';
    }

    static function errorFILLInvalid() {
        return 'Invalid .FILL statement';
    }

    static function errorFILLCodeNoFill() {
        return 'Fill code can not contain .FILL statements';
    }

    static function errorFILLCodeOnlyAssignments() {
        return 'Fill code may only use IF statements, FOR loop statements and assignments';
    }

    static function errorSectionNotFound($f) {
        return 'Unable to find section ' . $f;
    }
    
    static function errorSectionInSection($f) {
        return 'Section ' . $f . ' cannot be called within itself';
    }

    static function errorSectionInVariableNotFound($f, $s) {
        return 'Referenced section ' . $s . ' in variable ' . $f . ' not found';
    }

    static function errorGroupMissingEndGroup() {
        return 'Missing ENDGROUP statement';
    }

    static function errorWhileMissingEndWhile() {
        return 'Missing ENDWHILE statement';
    }

    static function errorGroupMissingEndSubGroup() {
        return 'Missing ENDSUBGROUP statement';
    }

    static function errorTemplateNotFound($f) {
        return 'Unable to find group ' . $f;
    }

    static function errorForLoopMissingDo() {
        return 'Missing DO in FOR loop statement';
    }

    static function errorWhileMissingDo() {
        return 'Missing DO in WHILE statement';
    }

    static function errorForLoopMissingTo() {
        return 'Missing TO in FOR loop statement';
    }

    static function errorForLoopMissingAssignment() {
        return 'Missing counter assignment in FOR loop statement';
    }

    static function errorForLoopInvalid() {
        return 'Invalid FOR loop statement';
    }

    static function errorForLoopMissingEnddo() {
        return 'Missing ENDDO statement';
    }

    static function errorVariableNotFound($f) {
        return 'Unable to find variable ' . $f;
    }

    static function errorIfInvalid() {
        return 'Invalid IF statement';
    }

    static function errorElseIfInvalid() {
        return 'Invalid ELSEIF statement';
    }

    static function errorIfMissingThen() {
        return 'Missing THEN in IF statement';
    }

    static function errorIfMissingEndif() {
        return 'Missing ENDIF statement';
    }

    static function errorElseIfMissingThen() {
        return 'Missing THEN in ELSEIF statement';
    }

    static function errorNotArray($name) {
        return 'Variable ' . $name . ' is not an array';
    }

    static function errorArray($name) {
        return 'Variable ' . $name . ' is an array';
    }

    /* SURVEY/SMS messages */

    static function messageSurveyUnavailable() {
        return "Survey system not available!";
    }

    static function messageSurveyCompleted() {
        return 'We already received your responses. Thank you!';
    }

    static function messageSurveyProcessing() {
        return 'The system encountered an error and as a result your interview was locked. Please contact your survey administrator.';
    }

    static function messageSurveyEnd() {
        return 'This is the end of the interview.';
    }

    static function messageSurveyStart() {
        return 'This is the start of the interview.';
    }

    static function labelLoginCode() {
        return 'Please enter your login code:';
    }
    
    static function labelPlatform(){
        return 'Platform and browser information';
    }
    
    static function labelDevice() {
        return 'Device information';
    }
    
    static function labelExecutionMode() {
        return 'Execution mode';
    }
    
    static function LabelSurveyNoAccess() {
        return 'The device you are currently using is not allowed.';
    }

    static function messageWelcome() {
        return 'Welcome to this survey.<br/><br/><br/>';
    }

    static function messageCheckLoginCode() {
        return 'Please enter a valid login code.';
    }

    static function messageEnterPrimKey() {
        return 'Please enter your primary key!';
    }

    static function messageSurveyNotAccessible() {
        return 'Survey not accessible';
    }

    static function projectTitle() {
        return 'UAS Study';
    }

    static function messageSMSTitle() {
        return 'UAS SMS';
    }

    /* project title */

    static function messageTitle() {
        return 'UAS SMS';
    }

    static function messageSMSWelcome() {
        return 'Please enter your username and password to log in.';
    }

    static function messageCheckUsernamePassword() {
        return 'Please check your username and/or password';
    }

    static function messageEnterUsernamePassword() {
        return 'Please enter your username and/or password';
    }

    static function messagePreferencesSaved() {
        return 'Your preferences are saved';
    }

    /* SMS labels */

    static function labelHome() {
        return 'Home';
    }

    static function labelUsername() {
        return 'Username';
    }

    static function labelPassword() {
        return 'Password';
    }

    static function labelStartSurvey() {
        return 'Start interview';
    }

    static function labelAssignedSample() {
        return 'Assigned Sample';
    }

    static function labelUSB() {
        return 'USB';
    }

    static function labelInternet() {
        return 'Internet';
    }

    static function labelCommunication() {
        return 'Communication:';
    }

    static function labelExportAsSql() {
        return 'Export as SQL file';
    }

    static function labelWorkOnServer() {
        return 'Work on server';
    }

    static function labelInterviewers() {
        return 'Interviewers';
    }

    static function labelInfo() {
        return 'info';
    }

    static function labelInfoCap() {
        return 'Info';
    }

    static function labelAddContact() {
        return 'add contact';
    }

    static function labelAddContactCap() {
        return 'Add contact';
    }

    static function labelRemarks() {
        return 'Remarks';
    }

    static function labelContacts() {
        return 'Contacts';
    }

    static function labelHistory() {
        return 'History';
    }

    static function labelRevert() {
        return 'Revert';
    }

    static function labelRefactor() {
        return 'Refactor';
    }

    static function labelTracking() {
        return 'Tracking';
    }

    static function labelEdit() {
        return 'Edit';
    }

    static function labelTranslate() {
        return 'Translate';
    }

    static function labelCopy() {
        return 'Copy';
    }

    static function labelMove() {
        return 'Move';
    }

    static function labelRemove() {
        return 'Remove';
    }

    static function labelNone() {
        return 'none';
    }

    static function labelCompiledCode() {
        return 'Compiled code';
    }

    static function labelContactWith() {
        return 'Contact with:';
    }

    static function labelProxyName() {
        return 'Proxy name:';
    }

    static function labelPleaseSelect() {
        return 'Please select';
    }

    static function labelOutcome() {
        return 'Outcome:';
    }

    static function labelAppointment() {
        return 'Appointment:';
    }

    static function labelRemark() {
        return 'Remark:';
    }

    static function labelStatus() {
        return array('none', 'started', 'completed');
    }

    static function labelYes() {
        return 'yes';
    }

    static function labelYesCap() {
        return 'Yes';
    }

    static function labelNo() {
        return 'no';
    }

    static function labelNoCap() {
        return 'No';
    }

    static function labelNormalMode() {
        return 'Normal mode';
    }

    static function labelTestMode() {
        return 'Test mode';
    }

    /* USER EDIT LABELS */

    static function labelUserLanguageAllowed() {
        return 'Language(s)';
    }

    static function labelUserModeAllowed() {
        return 'Interview mode';
    }

    /* SMS SEARCH LABELS */

    static function labelSearch() {
        return 'Search';
    }

    static function labelSearched($term) {
        return 'Search results for  `' . $term . '`';
    }

    static function labelNoSearched($term) {
        return 'No results for  `' . $term . '`';
    }

    static function labelSearchName() {
        return 'Name';
    }

    static function labelSearchLine() {
        return 'Line';
    }

    static function labelSearchSetting() {
        return 'Found in';
    }

    static function labelSearchValue() {
        return 'Text';
    }

    static function labelSearchSurvey() {
        return 'Survey';
    }

    static function labelSearchRouting() {
        return 'Routing';
    }

    static function labelSearchSection() {
        return 'Section';
    }

    static function labelSearchVariables() {
        return 'Variables';
    }

    static function labelSearchTypes() {
        return 'Types';
    }

    static function labelSearchSections() {
        return 'Sections';
    }

    static function labelSearchGroups() {
        return 'Groups';
    }

    static function messageSearchNoResults() {
        return 'No results found';
    }

    static function messageSearchNoTerm() {
        return 'Please enter a search term';
    }

    /* SMS TOOLS LABELS */

    static function labelToolsCleanSurveys() {
        return 'Surveys';
    }

    static function labelOutput() {
        return 'Output';
    }

    static function labelNavigation() {
        return 'Navigation';
    }

    static function labelOutputData() {
        return 'Data';
    }

    static function labelOutputRawData() {
        return 'Raw data';
    }

    static function labelOutputRemarkData() {
        return 'Remarks';
    }

    static function labelOutputTimings() {
        return 'Timings';
    }

    static function labelOutputAuxiliaryData() {
        return 'Auxiliary data';
    }

    static function labelOutputMeta() {
        return 'Metadata';
    }

    static function labelOutputDataSingle() {
        return 'From a single survey';
    }

    static function labelOutputDataMultiple() {
        return 'Combined from multiple surveys';
    }

    static function labelOutputDataSource() {
        return 'Source';
    }

    static function labelOutputDataKeepOnly() {
        return 'Kept data only';
    }

    static function labelOutputDataFormat() {
        return 'Format';
    }

    static function labelOutputDataSurvey() {
        return 'Survey';
    }

    static function labelOutputDataLanguage() {
        return 'Language(s)';
    }

    static function labelOutputDataMode() {
        return 'Mode(s)';
    }

    static function labelOutputDataType() {
        return 'Type of data';
    }

    static function labelOutputDataClean() {
        return 'Clean data only';
    }

    static function labelOutputDataCompleted() {
        return 'Interviews';
    }

    static function labelOutputDataHidden() {
        return 'Exclude hidden variables';
    }

    static function labelOutputDataFileType() {
        return 'File type';
    }

    static function labelOutputDataFileName() {
        return 'Filename';
    }

    static function labelOutputDataFormatLanguage() {
        return 'Label language';
    }

    static function labelOutputDataFormatMode() {
        return 'Label mode';
    }

    static function labelOutputDataFileNameNoExtension() {
        return 'File extension automatically appended';
    }

    static function labelOutputDataPrimaryKey() {
        return 'Include primary key';
    }

    static function labelOutputDataNoData() {
        return 'Include variables without data';
    }

    static function labelOutputDataPrimaryKeyEncryption() {
        return 'Encrypt primary key with';
    }

    static function labelOutputDataPrimaryKeyEncryptionNo() {
        return 'Leave empty for no encryption';
    }

    static function labelOutputDataFieldname() {
        return 'Case of variable names';
    }

    static function labelOutputDataTable() {
        return 'Database table';
    }

    static function labelOutputDataValueLabel() {
        return 'Include value labels';
    }

    static function labelOutputDataValueLabelNumbers() {
        return 'Include numbers in value labels';
    }

    static function labelOutputDataValueLabelWidth() {
        return 'Value label display';
    }

    static function labelOutputDataMarkEmpty() {
        return 'Mark skipped answers';
    }

    static function labelOutputStatistics() {
        return 'Statistics';
    }

    static function labelOutputPaperVersion() {
        return 'Paper version';
    }

    static function labelOutputScreenDumps() {
        return 'Screendumps';
    }

    static function labelOutputTimingsRespondent() {
        return 'Times per screen per respondent';
    }

    static function labelOutputTranslation() {
        return 'Translation';
    }
    
    static function labelOutputTranslationAll() {
        return 'All translation';
    }
    
    static function labelOutputTranslationFills() {
        return 'Fill translations only';
    }
    
    static function labelOutputTranslationAssistance() {
        return 'Assistance message translations only';
    }

    static function labelOutputDocumentation() {
        return 'Documentation';
    }

    static function labelOutputDocumentationSurvey() {
        return 'Survey';
    }

    static function labelOutputDocumentationMode() {
        return 'Mode';
    }

    static function labelOutputDocumentationLanguage() {
        return 'Language';
    }

    static function labelOutputDocumentationDictionary() {
        return 'Dictionary';
    }

    static function labelOutputDocumentationRouting() {
        return 'Routing';
    }

    static function labelOutputScreenDumpsType() {
        return 'Format';
    }

    static function labelOutputScreenDumpsSurvey() {
        return 'Survey';
    }

    static function labelOutputScreenDumpsRespondent() {
        return 'Respondent';
    }

    static function labelOutputScreenDumpsRespondentFor() {
        return 'Screendumps for ';
    }

    static function labelToolsBatchEditorActions() {
        return 'Available actions';
    }

    static function labelToolsBatchEditorVariables() {
        return 'Available variables';
    }

    static function labelToolsBatchEditorGroups() {
        return 'Available groups';
    }

    static function labelToolsBatchEditorSections() {
        return 'Available sections';
    }

    static function labelToolsBatchEditorTypes() {
        return 'Available types';
    }

    static function labelStringOpen() {
        return 'Text';
    }

    static function labelSetOfEnumerated() {
        return 'Enumerated';
    }

    static function labelComparison() {
        return 'Comparison';
    }

    static function labelInputMask() {
        return 'Input mask';
    }

    static function labelGroup() {
        return 'Group';
    }

    static function labelInline() {
        return 'Inline enumerated';
    }

    static function labelRange() {
        return 'Range';
    }

    static function labelToolsCleanDataType() {
        return 'Type of data';
    }

    static function labelToolsCleanPeriod() {
        return 'Period';
    }

    static function labelToolsCleanFrom() {
        return 'From';
    }

    static function labelToolsCleanTo() {
        return 'To';
    }

    static function labelToolsCheckRouting() {
        return 'Routing';
    }

    static function labelToolsCompileSurveys() {
        return 'Surveys';
    }

    static function labelToolsCompileComponents() {
        return 'Components';
    }

    static function labelToolsCompileSections() {
        return 'Sections';
    }

    static function labelToolsCompileVariables() {
        return 'Variables';
    }

    static function labelToolsCompileInlineFields() {
        return 'Inline fields';
    }

    static function labelToolsCompileFills() {
        return 'Fills';
    }

    static function labelToolsCompileGroup() {
        return 'Groups';
    }

    static function labelToolsCompileTypes() {
        return 'Types';
    }

    static function labelToolsCompileSettings() {
        return 'Settings';
    }

    static function labelToolsExportSettings() {
        return 'Settings';
    }

    static function labelToolsExportTypeSerialize() {
        return 'For import via NubiS';
    }

    static function labelToolsExportTypeSQL() {
        return 'For import via e.g PHPMyAdmin';
    }

    static function labelToolsExportType() {
        return 'Type';
    }

    static function labelToolsImportSettings() {
        return 'Settings';
    }

    static function labelToolsImportType() {
        return 'Survey system';
    }

    static function labelToolsImportTypeMMIC() {
        return 'MMIC';
    }

    static function labelToolsImportTypeNubis() {
        return 'NubiS';
    }

    static function labelToolsImportTarget() {
        return 'Import type';
    }

    static function labelToolsImportTargetAdd() {
        return 'Add to current project';
    }

    static function labelToolsImportTargetReplace() {
        return 'Replace current project';
    }

    static function labelToolsImportDatabase() {
        return 'Database';
    }

    static function labelDatabaseServer() {
        return 'Server';
    }

    static function labelDatabaseName() {
        return 'Database';
    }

    static function labelDatabaseUsername() {
        return 'Username';
    }

    static function labelDatabasePassword() {
        return 'Password';
    }

    static function labelDatabaseTablename() {
        return 'Table name';
    }

    static function labelToolsImportDatabaseType() {
        return 'Type';
    }

    static function labelDatabaseTypeMySQL() {
        return 'MySQL';
    }

    static function labelDatabaseTypeOracle() {
        return 'Oracle';
    }

    /* SMS INTERVIEWER MESSAGES */

    static function messageSMSSurveyStart($respondent) {
        if ($respondent instanceof Respondent) {
            return 'This will start the interview for respondent ' . $respondent->getPrimkey() . '.';
        } else {
            return 'This will start the interview for household ' . $respondent->getPrimkey() . '.';
        }
    }

    static function messageSMSNoRemarksYet() {
        return 'There are no remarks yet.';
    }

    static function messageSMSNoContactsYet() {
        return 'There are no contacts yet.';
    }

    static function messageSMSNoHistoryYet() {
        return 'There is no history yet.';
    }

    static function messageContactAdded() {
        return 'Contact added.';
    }

    static function messageSelectContactOutcome() {
        return 'Please select an outcome of this contact.';
    }

    static function messageNoRespondentsFound() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'No respondents or households found. Please try again.';
        }
        return 'No respondents found. Please try again.';
    }

    static function messageRespondentChanged($respondent) {
        if ($respondent instanceof Respondent) {
            return "Respondent `" . $respondent->getPrimkey() . "` changed.";
        }
        return "Household `" . $respondent->getPrimkey() . "` changed.";
    }

    static function messageRespondentsFound() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'respondent(s) or household(s) found. Please select a respondent or household to start an interview.';
        }
        return 'respondent(s) found. Please select a respondent to start an interview.';
    }

    static function messageSelectRespondent() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'Please select a respondent or household to start an interview.';
        }
        return 'Please select a respondent to start an interview.';
    }

    static function messageRespondentsAssignedSupervisor($name) {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'Household(s)/Respondent(s) assigned to interviewer `' . $name . '`.';
        }
        return 'Respondent(s) assigned to interviewer `' . $name . '`.';
    }

    static function messageNoRespondentsAssigned() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'No households or respondents assigned to you. Please contact your supervisor or <b>check your filter settings</b>.';
        }
        return 'No respondents assigned to you. Please contact your supervisor or check your filter settings.';
    }

    static function messageNoRespondentsAssignedSupervisor() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'No respondents or households assigned to this interviewer.';
        }
        return 'No respondents assigned to this interviewer.';
    }

    static function messageTranslationStatusComplete() {
        return 'Translation complete';
    }

    static function messageTranslationStatusIncomplete() {
        return 'Translation incomplete';
    }

    static function messageAssistanceTextsChanged() {
        return 'Assistance texts changed.';
    }

    static function messageDisplayTextsChanged() {
        return 'Display texts changed.';
    }

    /* SMS buttons */

    static function buttonLogin() {
        return 'Login';
    }

    static function buttonAddContact() {
        return 'Add Contact';
    }

    /* SMS links */

    static function linkAppointments() {
        return 'Appointments';
    }

    static function linkCalendar() {
        return 'Calendar';
    }

    static function linkInterviews() {
        return 'Interviews';
    }

    static function linkBackToSMS() {
        return 'Back to the SMS';
    }

    static function linkReportProblem() {
        return 'Report problem';
    }

    static function linkJumpBack() {
        return 'Jump back';
    }

    static function jumpBack() {
        return 'Jump back';
    }

    static function noJumpBack() {
        return 'No options found';
    }

    static function jumpBackTo() {
        return 'Jump to:';
    }

    static function jumpScreen() {
        return 'Screen';
    }

    static function linkWatch() {
        return 'View data';
    }

    static function linkBackToNubiS() {
        return 'Exit testing';
    }

    static function divider() {
        return '&nbsp;&nbsp;|&nbsp;&nbsp;';
    }

    static function linkSendReceive() {
        return 'Send/Receive';
    }

    static function linkLogout() {
        return 'Logout';
    }

    static function linkSupervisorLogin() {
        return 'Supervisor login';
    }

    static function linkSupervisorLogout() {
        return 'Supervisor logout';
    }

    static function linkPreferences() {
        return 'Preferences';
    }

    static function linkUsers() {
        return 'Users';
    }

    static function linkOtherSurveys() {
        return 'Other Surveys';
    }

    static function linkResetTestCases() {
        return 'reset test cases';
    }

    /* SMS SYSADMIN */

    /* SMS SYSADMIN links */

    static function linkSms() {
        return 'SMS';
    }

    static function linkSurvey() {
        return 'Surveys';
    }

    static function linkOutput() {
        return 'Output';
    }

    static function linkData() {
        return "Data";
    }

    static function linkDocumentation() {
        return 'Documentation';
    }

    static function linkPaperVersion() {
        return "Paper version";
    }

    static function linkScreendumps() {
        return "Screen dumps";
    }

    static function linkStatistics() {
        return "Statistics";
    }

    static function linkTools() {
        return "Tools";
    }

    static function linkCleaner() {
        return "Cleaner";
    }

    static function linkBatchEditor() {
        return 'Batch editor';
    }

    static function linkChecker() {
        return "Checker";
    }

    static function linkCompiler() {
        return "Compiler";
    }

    static function linkTest() {
        return "Test";
    }

    static function linkReported() {
        return "Reported problems";
    }

    static function linkTester() {
        return "Tester";
    }

    static function linkFlood() {
        return "Flooder";
    }

    static function linkImport() {
        return "Importer";
    }

    static function linkExport() {
        return "Exporter";
    }

    static function linkInterviewers() {
        return 'Interviewers';
    }

    static function linkSample() {
        return 'Sample';
    }

    static function linkReports() {
        return 'Reports';
    }

    static function linkQuestions() {
        return 'Questions';
    }

    static function linkSettings() {
        return 'Settings';
    }

    static function linkTypes() {
        return 'Types';
    }

    static function linkEditTooltip() {
        return 'Edit';
    }

    static function linkRefactorTooltip() {
        return 'Refactor';
    }

    static function linkTagTooltip() {
        return 'Tag';
    }

    static function linkCopyTooltip() {
        return 'Copy';
    }

    static function linkMoveTooltip() {
        return 'Move';
    }

    static function linkRemoveTooltip() {
        return 'Remove';
    }

    /* SMS SYSADMIN labels */

    static function labelNavigationInBreadCrumbs() {
        return 'Show navigation options in breadcrumbs';
    }

    static function labelRoutingAutoIndentation() {
        return 'Use code formatting';
    }

    static function labelRouting() {
        return 'Routing';
    }

    static function labelRoutingCompiledCode() {
        return 'Compiled code for ';
    }

    static function labelRoutingHistory() {
        return 'History for ';
    }

    static function labelIfEmptyDefaultOrder() {
        return 'If empty, default order';
    }

    static function labelIfEmptyColumns() {
        return 'If empty, all options in a single column';
    }

    static function labelIfEmptyDefaultHeaders() {
        return 'If empty, no headers';
    }

    static function labelIfEmptyDefaultOrderPlaceholder() {
        return 'Question name';
    }

    static function labelIfEmptyColumnsPlaceholder() {
        return 'Number of options per column';
    }

    static function labelCustomFunctionCall() {
        return 'Please enter a custom function call';
    }

    static function labelGroups() {
        return 'Groups';
    }

    static function labelGeneral() {
        return 'General';
    }

    static function labelAccess() {
        return 'Access';
    }

    static function labelVerification() {
        return 'Validation';
    }

    static function labelInteractive() {
        return 'Interactive';
    }

    static function labelLayout() {
        return 'Display';
    }

    static function labelVariables() {
        return 'Variables';
    }

    static function labelVariablesLower() {
        return 'variables';
    }

    static function labelTypesLower() {
        return 'types';
    }

    static function labelGroupsLower() {
        return 'groups';
    }

    static function labelSectionsLower() {
        return 'sections';
    }

    static function labelAssistance() {
        return 'Assistance';
    }

    static function labelFill() {
        return 'Use as fill';
    }

    static function labelCheck() {
        return 'Apply check(s)';
    }

    static function labelText() {
        return 'Text';
    }

    static function labelSections() {
        return 'Sections';
    }

    static function labelTexts() {
        return 'Texts';
    }

    static function labelGroupEditTable() {
        return 'Table';
    }

    static function labelGroupEditTableID() {
        return 'Identifier';
    }

    static function labelGroupEditCondensed() {
        return 'Condensed';
    }

    static function labelGroupEditBordered() {
        return 'Bordered';
    }

    static function labelGroupEditStriped() {
        return 'Striped';
    }

    static function labelGroupEditHovered() {
        return 'Hovered';
    }

    static function labelGroupEditExclusive() {
        return 'Only one question may be answered';
    }

    static function labelGroupEditInclusive() {
        return 'All questions must be answered';
    }

    static function labelGroupEditMinRequired() {
        return 'Minimum number of answered questions';
    }

    static function labelGroupEditMaxRequired() {
        return 'Maximum number of answered questions';
    }

    static function labelGroupEditExactRequired() {
        return 'Exact number of answered questions';
    }

    static function labelGroupEditUnique() {
        return 'All questions must have unique answer';
    }

    static function labelGroupEditSame() {
        return 'All questions must have same answer';
    }

    static function labelGroupEditAssistanceExclusive() {
        return 'More than one answer given';
    }

    static function labelGroupEditAssistanceInclusive() {
        return 'Not all questions answered';
    }

    static function labelGroupEditAssistanceMinimumRequired() {
        return 'Not enough questions answered';
    }

    static function labelGroupEditAssistanceMaximumRequired() {
        return 'Too many questions answered';
    }

    static function labelGroupEditAssistanceExactRequired() {
        return 'Not exactly enough questions answered';
    }

    static function labelGroupEditAssistanceUniqueRequired() {
        return 'No unique answer for each question';
    }

    static function labelGroupEditAssistanceSameRequired() {
        return 'Not the same answer for each question';
    }

    static function labelTypes() {
        return 'Types';
    }

    static function labelTypeEditGeneral() {
        return 'General';
    }

    static function labelTypeEditAccess() {
        return 'Access';
    }

    static function labelTypeEditGeneralName() {
        return 'Name';
    }

    static function labelTypeEditGeneralTemplate() {
        return 'Template';
    }

    static function labelTypeEditGeneralStatus() {
        return 'Status';
    }

    static function labelTypeEditGeneralPosition() {
        return 'Position';
    }

    static function labelTypeEditGeneralVariableType() {
        return 'Type';
    }

    static function labelTypeEditGeneralAnswerType() {
        return 'Answer type';
    }

    static function labelTypeEditGeneralArray() {
        return 'Array';
    }

    static function labelTypeEditGeneralKeep() {
        return 'Keep';
    }

    static function labelTypeEditOutputScreendumps() {
        return 'Screen dumps';
    }

    static function labelTypeEditOutputParadata() {
        return 'Paradata';
    }

    static function labelTypeEditGeneralHidden() {
        return 'Data';
    }

    static function labelTypeEditGeneralHiddenPaperVersion() {
        return 'Paper version';
    }

    static function labelTypeEditGeneralHiddenRouting() {
        return 'Routing';
    }

    static function labelTypeEditGeneralHiddenTranslation() {
        return 'Translation';
    }

    static function labelTypeEditGeneralGroupName() {
        return 'Name';
    }

    static function labelTypeEditGeneralGroupTemplate() {
        return 'Template';
    }

    static function labelTypeEditGeneralVariableName() {
        return 'Name';
    }

    static function labelTypeEditGeneralDescription() {
        return 'Description';
    }

    static function labelTypeEditGeneralQuestion() {
        return 'Question';
    }

    static function labelTypeEditGeneralCategories() {
        return 'Options';
    }

    static function labelTypeEditGeneralSection() {
        return 'Section';
    }

    static function labelTypeEditValidationResponse() {
        return 'Response';
    }

    static function labelTypeEditRequired() {
        return 'Required';
    }

    static function labelTypeEditIfEmpty() {
        return 'If empty';
    }

    static function labelTypeEditIfError() {
        return 'If error';
    }

    static function labelTypeEditValidationGroup() {
        return 'Group';
    }

    static function labelTypeEditValidationCriteria() {
        return 'Verification';
    }

    static function labelTypeEditRangeMinimum() {
        return 'Minimum';
    }

    static function labelTypeEditRangeMaximum() {
        return 'Maximum';
    }

    static function labelTypeEditRangeOther() {
        return 'Other allowed values';
    }

    static function labelTypeEditTextMinimumSelected() {
        return 'Minimum number of options selected';
    }

    static function labelTypeEditTextMaximumSelected() {
        return 'Maximum number of options selected';
    }

    static function labelTypeEditTextExactSelected() {
        return 'Exact number of options selected';
    }

    static function labelTypeEditTextInvalidSubSet() {
        return 'Invalid sub set combinations';
    }

    static function labelTypeEditTextInvalidSet() {
        return 'Invalid set of combinations';
    }
    
    static function labelTypeEditTextMinimumRanked() {
        return 'Minimum number of options ranked';
    }

    static function labelTypeEditTextMaximumRanked() {
        return 'Maximum number of options ranked';
    }

    static function labelTypeEditTextExactRanked() {
        return 'Exact number of options ranked';
    }

    static function labelTypeEditValidationMasking() {
        return 'Input control';
    }

    static function labelTypeEditTextInputMaskEnable() {
        return 'Active';
    }

    static function labelTypeEditTextInputMask() {
        return 'Input mask';
    }

    static function labelTypeEditTextInputMaskCustom() {
        return 'Custom';
    }

    static function labelTypeEditTextInputMaskPlaceholder() {
        return 'Input placeholder';
    }

    static function labelTypeEditTextMinimumLength() {
        return 'Minimum character length';
    }

    static function labelTypeEditTextMaximumLength() {
        return 'Maximum character length';
    }

    static function labelTypeEditTextMinimumWords() {
        return 'Minimum number of words';
    }

    static function labelTypeEditTextMaximumWords() {
        return 'Maximum number of words';
    }

    static function labelTypeEditTextPattern() {
        return 'Pattern (regular expression)';
    }

    static function labelTypeEditCalendarMaximum() {
        return 'Maximum number of dates';
    }

    static function labelTypeEditValidationComparison() {
        return 'Comparison';
    }

    static function labelTypeEditComparisonEqualTo() {
        return 'Equal to';
    }

    static function labelTypeEditComparisonEqualToIgnoreCase() {
        return 'Equal to (ignore case)';
    }

    static function labelTypeEditComparisonNotEqualTo() {
        return 'Not equal to';
    }

    static function labelTypeEditComparisonNotEqualToIgnoreCase() {
        return 'Not equal to (ignore case)';
    }

    static function labelTypeEditComparisonGreaterThan() {
        return 'Greater than';
    }

    static function labelTypeEditComparisonGreaterOrEqualThan() {
        return 'Greater than or equal to';
    }

    static function labelTypeEditComparisonLessThan() {
        return 'Smaller than';
    }

    static function labelTypeEditComparisonLessOrEqualThan() {
        return 'Smaller than or equal to';
    }

    static function labelSectionEditHeader() {
        return 'Header';
    }

    static function labelSectionEditFooter() {
        return 'Footer';
    }

    static function labelSettingsModeDefault() {
        return 'Default mode';
    }

    static function labelSettingsModeAllowed() {
        return 'Available modes';
    }

    static function labelSettingsModeChange() {
        return 'Allow mode change';
    }

    static function labelSettingsModeReentry() {
        return 'Use last known mode on re-entry';
    }

    static function labelSettingsModeBack() {
        return 'Use last known mode when going back';
    }

    static function labelDataKeepOnly() {
        return 'Reset after completion if not keep answer';
    }

    static function labelDataKeep() {
        return 'Keep answer after completion';
    }

    static function labelSkipVariable() {
        return 'Add skip flag in data';
    }

    static function labelSkipVariablePostFix() {
        return 'Postfix';
    }

    static function labelDataVisibility() {
        return 'Visibility';
    }

    static function labelTypeEditLanguageGeneral() {
        return 'General';
    }

    static function labelTypeEditModeGeneral() {
        return 'General';
    }

    static function labelSettingsLanguageDefault() {
        return 'Default language';
    }

    static function labelSettingsLanguageAllowed() {
        return 'Available languages';
    }

    static function labelSettingsLanguageChange() {
        return 'Allow language change';
    }

    static function labelSettingsLanguageReentry() {
        return 'Use last known language on re-entry';
    }

    static function labelSettingsLanguageBack() {
        return 'Use last known language when going back';
    }

    static function labelEnumeratedTextBox() {
        return 'Or type in:';
    }

    static function labelEnumeratedTextBoxBefore() {
        return 'Text before box:';
    }
    
    static function labelEnumeratedTextBoxAfter() {
        return 'Text after box:';
    }

    static function labelSliderTextBox() {
        return 'Or type in:';
    }

    static function labelSliderTextBoxBefore() {
        return 'Text before box:';
    }

    static function labelSliderTextBoxAfter() {
        return 'Text after box:';
    }
    
    static function labelTypeEditLayoutDropdown() {
        return 'Dropdown';
    }
    
    static function labelTypeEditLayoutDropdownDefault() {
        return 'Text if nothing selected';
    }    

    static function labelTypeEditLayoutSliderLabels() {
        return 'Labels';
    }

    static function labelTypeEditLayoutSliderLabelPlacement() {
        return 'Label placement';
    }
    
    static function labelTypeEditLayoutKnob() {
        return 'Knob';
    }

    static function labelTypeEditLayoutSlider() {
        return 'Slider';
    }

    static function labelTypeEditLayoutTimePicker() {
        return 'Time picker';
    }

    static function labelTypeEditLayoutDatePicker() {
        return 'Date picker';
    }

    static function labelTypeEditLayoutDateTimePicker() {
        return 'Date/time picker';
    }

    static function labelTypeEditLayoutFormat() {
        return 'Format';
    }

    static function labelTypeEditLayoutTimeFormat() {
        return 'Time format';
    }

    static function labelTypeEditLayoutDateFormat() {
        return 'Date format';
    }

    static function labelTypeEditLayoutDateTimeFormat() {
        return 'Date/time format';
    }

    static function labelTypeEditLayoutEnumerated() {
        return 'Options';
    }

    static function labelTypeEditEnumeratedFormatting() {
        return 'Header formatting';
    }

    static function labelTypeEditEnumeratedSplit() {
        return 'Header';
    }

    static function labelTypeEditEnumeratedCustom() {
        return 'Custom';
    }

    static function labelTypeEditEnumeratedRandomizer() {
        return 'Options order';
    }

    static function labelTypeEditEnumeratedColumns() {
        return 'Options per column';
    }

    static function labelTypeEditLayoutOrientation() {
        return 'Orientation';
    }
    
    static function labelTypeEditLayoutRotation() {
        return 'Rotation';
    }

    static function labelTypeEditLayoutEnumeratedTemplate() {
        return 'Template';
    }

    static function labelTypeEditEnumeratedOrder() {
        return 'Option/label order';
    }

    static function labelTypeEditLayoutEnumeratedLabel() {
        return 'Label';
    }

    static function labelTypeEditLayoutEnumeratedColumns() {
        return 'Number of columns';
    }

    static function labelTypeEditLayoutTooltip() {
        return 'Tooltip';
    }

    static function labelTypeEditLayoutTextBox() {
        return 'Text box';
    }

    static function labelTypeRefactor() {
        return 'Rename to';
    }

    static function labelTypeEditLayoutStep() {
        return 'Increment';
    }

    static function labelTypeEditLayoutButtons() {
        return 'Buttons';
    }

    static function labelTypeEditLayoutOverall() {
        return 'Alignment and formatting';
    }

    static function labelTypeEditLayoutErrorPlacement() {
        return 'Placement';
    }

    static function labelTypeEditLayoutError() {
        return 'Error';
    }

    static function labelTypeEditQuestionAlignment() {
        return 'Question alignment';
    }

    static function labelTypeEditQuestionFormatting() {
        return 'Question formatting';
    }

    static function labelTypeEditAnswerAlignment() {
        return 'Answer alignment';
    }

    static function labelTypeEditAnswerFormatting() {
        return 'Answer formatting';
    }

    static function labelTypeEditHeaderAlignment() {
        return 'Header alignment';
    }

    static function labelTypeEditTableWidth() {
        return 'Table width (%)';
    }

    static function labelTypeEditTableHeaders() {
        return 'Table header(s)';
    }

    static function labelTypeEditQuestionColumnWidth() {
        return 'Question width (%)';
    }

    static function labelTypeEditHeaderFormatting() {
        return 'Header formatting';
    }

    static function labelTypeEditHeaderFixed() {
        return 'Table scroll';
    }

    static function labelTypeEditHeaderScrollDisplay() {
        return 'Scroll height (in px)';
    }

    static function labelTypeEditButtonAlignment() {
        return 'Button alignment';
    }

    static function labelTypeEditButtonFormatting() {
        return 'Button formatting';
    }

    static function labelTypeEditProgressbarAlignment() {
        return 'Alignment';
    }

    static function labelTypeEditLayoutProgressBar() {
        return 'Progress bar';
    }

    static function labelTypeEditLayoutProgressBarType() {
        return 'Type';
    }

    static function labelTypeEditLayoutProgressBarShow() {
        return 'Show';
    }

    static function labelTypeEditLayoutProgressBarFillColor() {
        return 'Color for progress';
    }

    static function labelTypeEditLayoutProgressBarRemainColor() {
        return 'Color for remaining portion';
    }

    static function labelTypeEditLayoutProgressBarWidth() {
        return 'Width';
    }

    static function labelTypeEditLayoutProgressBarValue() {
        return 'Set value';
    }

    static function labelTypeEditLayoutTemplate() {
        return 'Template';
    }

    static function labelTypeEditSectionHeader() {
        return 'Header';
    }

    static function labelTypeEditSectionFooter() {
        return 'Footer';
    }

    static function labelTypeEditLayoutSection() {
        return 'Section';
    }

    static function labelTypeEditButtonLabel() {
        return 'Label';
    }

    static function labelTypeEditBackButton() {
        return 'Back';
    }

    static function labelTypeEditNextButton() {
        return 'Next';
    }

    static function labelTypeEditDKButton() {
        return 'Don\'t know';
    }

    static function labelTypeEditRFButton() {
        return 'Refuse';
    }

    static function labelTypeEditNAButton() {
        return 'Not applicable';
    }

    static function labelTypeEditRemarkButton() {
        return 'Remark';
    }

    static function labelTypeEditRemarkSaveButton() {
        return 'Save remark';
    }

    static function labelTypeEditCloseButton() {
        return 'Close remark';
    }

    static function labelTypeEditUpdateButton() {
        return 'Update';
    }

    static function labelTypeEditAssistance() {
        return 'Assistance';
    }

    static function labelTypeEditAssistanceTexts() {
        return 'Hints';
    }

    static function labelTypeEditOutput() {
        return 'Output';
    }

    static function labelTypeEditAssistanceMessages() {
        return 'Messages';
    }

    static function labelTypeEditAssistanceHints() {
        return 'Hints';
    }

    static function labelTypeEditAssistanceEmptyMessage() {
        return 'No answer';
    }

    static function labelTypeEditAssistanceErrorMessageDouble() {
        return 'If not a real number';
    }

    static function labelTypeEditAssistanceErrorMessageInteger() {
        return 'If not an integer';
    }

    static function labelTypeEditAssistanceErrorMessagePattern() {
        return 'If pattern not satisfied';
    }

    static function labelTypeEditAssistanceErrorMessageMinLength() {
        return 'If not enough characters';
    }

    static function labelTypeEditAssistanceErrorMessageMaxLength() {
        return 'If too many characters';
    }

    static function labelTypeEditAssistanceErrorMessageMinWords() {
        return 'If not enough words';
    }

    static function labelTypeEditAssistanceErrorMessageMaxWords() {
        return 'If too many words';
    }

    static function labelTypeEditAssistanceErrorMessageMinSelect() {
        return 'If not enough options selected';
    }

    static function labelTypeEditAssistanceErrorMessageMaxSelect() {
        return 'If too many options selected';
    }

    static function labelTypeEditAssistanceErrorMessageExactSelect() {
        return 'If not exactly enough options selected';
    }
    
    static function labelTypeEditAssistanceErrorMessageMinRank() {
        return 'If not enough options ranked';
    }

    static function labelTypeEditAssistanceErrorMessageMaxRank() {
        return 'If too many options ranked';
    }

    static function labelTypeEditAssistanceErrorMessageExactRank() {
        return 'If not exactly enough options ranked';
    }

    static function labelTypeEditAssistanceErrorMessageInvalidSubSelect() {
        return 'If invalid subset of options selected';
    }

    static function labelTypeEditAssistanceErrorMessageInvalidSelect() {
        return 'If invalid set of options selected';
    }

    static function labelTypeEditAssistanceErrorMessageEnumeratedEntered() {
        return 'If invalid answer code entered';
    }

    static function labelTypeEditAssistanceErrorMessageSetOfEnumeratedEntered() {
        return 'If one or more invalid answer code(s) entered';
    }

    static function labelTypeEditAssistanceErrorMessageRange() {
        return 'If not in range';
    }

    static function labelTypeEditAssistanceErrorMessageMaxCalendar() {
        return 'If too many dates selected';
    }

    static function labelTypeEditAssistancePreText() {
        return 'Before input field';
    }

    static function labelTypeEditAssistancePostText() {
        return 'After input field';
    }

    static function labelTypeEditAssistanceHoverText() {
        return 'When hovered over input field';
    }

    static function labelTypeEditAssistanceErrorMessageInlineAnswered() {
        return 'If question in non-selected option answered';
    }

    static function labelTypeEditAssistanceErrorMessageInlineInclusive() {
        return 'If not all question(s) in selected option answered';
    }

    static function labelTypeEditAssistanceErrorMessageInlineExclusive() {
        return 'If more than one question in selected option answered';
    }

    static function labelTypeEditAssistanceErrorMessageInlineMinRequired() {
        return 'If not enough question(s) in selected option answered';
    }

    static function labelTypeEditAssistanceErrorMessageInlineMaxRequired() {
        return 'If too many question(s) in selected option answered';
    }

    static function labelTypeEditAssistanceErrorMessageInlineExactRequired() {
        return 'If not exact number of question(s) in selected option answered';
    }

    static function labelTypeEditAssistanceErrorMessageEqualTo() {
        return 'If not equal to';
    }

    static function labelTypeEditAssistanceErrorMessageEqualToIgnoreCase() {
        return 'If not equal to (ignore case)';
    }

    static function labelTypeEditAssistanceErrorMessageNotEqualTo() {
        return 'If equal to';
    }

    static function labelTypeEditAssistanceErrorMessageNotEqualToIgnoreCase() {
        return 'If equal to (ignore case)';
    }

    static function labelTypeEditAssistanceErrorMessageGreaterEqualTo() {
        return 'If not greater than or equal to';
    }

    static function labelTypeEditAssistanceErrorMessageGreater() {
        return 'If not greater than';
    }

    static function labelTypeEditAssistanceErrorMessageSmallerEqualTo() {
        return 'If not smaller than or equal to';
    }

    static function labelTypeEditAssistanceErrorMessageSmaller() {
        return 'If not smaller than';
    }

    static function labelTypeEditFillSettings() {
        return 'Details';
    }

    static function labelTypeEditFillText() {
        return 'Options';
    }

    static function labelTypeEditFillCode() {
        return 'Code';
    }

    static function labelTypeEditCheckCode() {
        return 'Code';
    }

    static function labelTypeEditCheckText() {
        return 'Errors';
    }

    static function labelTypeEditInteractiveTexts() {
        return 'Javascript';
    }

    static function labelTypeEditInteractiveExtraJavascript() {
        return 'External scripts';
    }

    static function labelTypeEditInteractiveID() {
        return 'Element ID';
    }

    static function labelTypeEditInteractiveInlineText() {
        return 'Inline';
    }

    static function labelTypeEditInteractivePageText() {
        return 'After page load';
    }

    static function labelTypeEditInteractiveStyle() {
        return 'Formatting';
    }

    static function labelTypeEditInteractiveInlineStyle() {
        return 'Inline style';
    }

    static function labelTypeEditInteractivePageStyle() {
        return 'Page style';
    }

    static function labelTypeCopySection() {
        return 'Copy to section';
    }

    static function labelTypeCopySurvey() {
        return 'Copy to survey';
    }

    static function labelTypeCopyNumber() {
        return 'Number of copies';
    }

    static function labelTypeCopySuffix() {
        return 'Append \'_cl\' to name';
    }

    static function labelTypeMoveSection() {
        return 'Move to section';
    }

    static function labelTypeMoveSurvey() {
        return 'Move to survey';
    }

    static function labelSettings() {
        return 'Settings';
    }

    static function labelSettingsLanguage() {
        return 'Language';
    }

    static function labelSettingsAccess() {
        return 'Access';
    }

    static function labelSettingsAssistance() {
        return 'Assistance';
    }

    static function labelSettingsAccessEntry() {
        return 'General';
    }

    static function labelSettingsAccessTemporal() {
        return 'Date/time';
    }

    static function labelSettingsAccessType() {
        return 'Login type';
    }

    static function labelSettingsAccessAfterCompletion() {
        return 'Re-entry after completion';
    }

    static function labelSettingsAccessAfterCompletionPreload() {
        return 'Re-do preload on re-entry after completion';
    }

    static function labelSettingsAccessReturn() {
        return 'Re-entry allowed';
    }

    static function labelSettingsAccessExit() {
        return 'Action after completion';
    }

    static function labelSettingsAccessDatesFrom() {
        return 'From';
    }

    static function labelSettingsAccessDatesTo() {
        return 'To';
    }

    static function labelSettingsAccessTimesFrom() {
        return 'Between';
    }

    static function labelSettingsAccessTimesTo() {
        return 'And';
    }

    static function labelSettingsData() {
        return 'Output';
    }

    static function labelSettingsTitle() {
        return 'Title';
    }

    static function labelSettingsPage() {
        return 'Overall';
    }

    static function labelSettingsHeader() {
        return 'Header';
    }

    static function labelSettingsFooter() {
        return 'Footer';
    }

    static function labelSettingsPlaceholder() {
        return 'Placeholder';
    }

    static function labelSettingsTable() {
        return 'Tables';
    }

    static function labelSettingsGeneral() {
        return 'General';
    }

    static function labelSettingsMode() {
        return 'Interview mode';
    }

    static function labelDataStorage() {
        return 'Storage';
    }

    static function labelDataOutput() {
        return 'Output';
    }

    static function labelDataFormat() {
        return 'Format';
    }

    static function labelTypeEditOutputCategories() {
        return 'Output categories';
    }

    static function labelDataEncryptionKey() {
        return 'Encryption key';
    }

    static function labelSettingsLayout() {
        return 'Display';
    }

    static function labelSettingsValidation() {
        return 'Validation';
    }

    static function labelSettingsNavigation() {
        return 'Navigation';
    }

    static function labelTypeEditNavigation() {
        return 'Navigation';
    }

    static function labelTypeEditKeyboardBindingEnabled() {
        return 'Keyboard control';
    }

    static function labelTypeEditAccessReentry() {
        return 'Return before completion';
    }

    static function labelTypeEditAccessReentryPreload() {
        return 'Re-do preload on return';
    }

    static function labelTypeEditKeyboardBindingBack() {
        return 'Back button';
    }

    static function labelTypeEditKeyboardBindingNext() {
        return 'Next button';
    }

    static function labelTypeEditKeyboardBindingDK() {
        return 'Don\'t know button';
    }

    static function labelTypeEditKeyboardBindingRF() {
        return 'Refuse button';
    }

    static function labelTypeEditKeyboardBindingNA() {
        return 'Not applicable button';
    }

    static function labelTypeEditKeyboardBindingUpdate() {
        return 'Update button';
    }

    static function labelTypeEditKeyboardBindingRemark() {
        return 'Remark button';
    }

    static function labelTypeEditOnSubmit() {
        return 'On form submit (PHP function)';
    }

    static function labelTypeEditOnClick() {
        return 'On click (embedded in JavaScript onclick statement)';
    }

    static function labelTypeEditOnBack() {
        return 'On back';
    }

    static function labelTypeEditOnNext() {
        return 'On next';
    }

    static function labelTypeEditOnDK() {
        return 'On DK';
    }

    static function labelTypeEditOnRF() {
        return 'On RF';
    }

    static function labelTypeEditOnNA() {
        return 'On NA';
    }

    static function labelTypeEditOnUpdate() {
        return 'On update';
    }

    static function labelTypeEditOnLanguageChange() {
        return 'On language change';
    }

    static function labelTypeEditOnModeChange() {
        return 'On mode change';
    }

    static function labelTypeEditOnVersionChange() {
        return 'On version change';
    }

    static function labelTypeEditKeyboardBindingClose() {
        return 'Close button';
    }
    
    static function labelTypeEditMultiColumnQuestion() {
        return 'Questiontext in first column';
    }

    static function labelTypeEditIndividualDKRFNA() {
        return 'Individual DK/RF/NA';
    }

    static function labelTypeEditIndividualDKRFNASingle() {
        return 'Individual DK/RF/NA if single question on screen';
    }

    static function labelTypeEditIndividualDKRFNAInline() {
        return 'Individual DK/RF/NA for inline questions';
    }

    static function labelSettingsInteractive() {
        return 'Interactive';
    }

    static function labelFilterSample() {
        return array('All', 'Completed', 'Resisting', 'Non sample', 'Incompletes', 'Validation');
    }

    static function labelDataTypeNormal() {
        return "Actual data";
    }

    static function labelDataTypeTest() {
        return "Test data";
    }

    static function labelTestSurvey() {
        return 'Survey';
    }

    static function labelTestLanguage() {
        return 'Language';
    }

    static function labelTestModeInput() {
        return 'Mode';
    }

    static function labelToolsTestSettings() {
        return 'Test parameters';
    }

    static function labelFloodSurvey() {
        return 'Survey';
    }

    static function labelToolsFloodSettings() {
        return 'Flood parameters';
    }

    static function labelFloodLanguage() {
        return 'Language';
    }

    static function labelFloodMode() {
        return 'Mode';
    }

    static function labelFloodNumber() {
        return 'Number of cases';
    }

    /* visibility */

    static function labelVisibilityVisible() {
        return "Visible";
    }

    static function labelVisibilityHidden() {
        return "Hidden";
    }

    /* enable/disable */

    static function labelEnabled() {
        return "Enabled";
    }

    static function labelDisabled() {
        return "Disabled";
    }

    /* user types */

    static function labelInterviewer() {
        return "Interviewer";
    }

    static function labelSupervisor() {
        return "Supervisor";
    }

    static function labelTranslator() {
        return "Translator";
    }

    static function labelNurse() {
        return "Nurse";
    }

    static function labelResearcher() {
        return "Reseacher";
    }

    static function labelSysadmin() {
        return "Sysadmin";
    }

    static function labelTester() {
        return "Tester";
    }

    /* answer types */

    static function labelAnswerTypeString() {
        return "String";
    }

    static function labelAnswerTypeOpen() {
        return "Open";
    }

    static function labelAnswerTypeEnumerated() {
        return "Radio buttons";
    }

    static function labelAnswerTypeDropdown() {
        return "Dropdown";
    }

    static function labelAnswerTypesetOfEnumerated() {
        return "Check boxes";
    }

    static function labelAnswerTypeMultiDropdown() {
        return "Multi-select dropdown";
    }

    static function labelAnswerTypeInteger() {
        return "Integer";
    }

    static function labelAnswerTypeDouble() {
        return "Real";
    }

    static function labelAnswerTypeRange() {
        return "Range";
    }

    static function labelAnswerTypeSlider() {
        return "Slider";
    }
    
    static function labelAnswerTypeKnob() {
        return "Knob";
    }
    
    static function labelAnswerTypeRank() {
        return "Rank";
    }

    static function labelAnswerTypeDate() {
        return "Date picker";
    }

    static function labelAnswerTypeTime() {
        return "Time picker";
    }

    static function labelAnswerTypeDateTime() {
        return "Date/time picker";
    }

    static function labelAnswerTypeCalendar() {
        return "Calendar";
    }

    static function labelAnswerTypeNone() {
        return "None";
    }

    static function labelAnswerTypeSection() {
        return "Section";
    }

    static function labelAnswerTypeCustom() {
        return 'Custom';
    }

    static function labelDropdownNothing() {
        return 'Select';
    }

    /* versions */

    static function labelVersionCurrentName() {
        return "Current";
    }

    static function labelVersionCurrentDescription() {
        return "Current version";
    }

    static function remarkTitle() {
        return 'Remark';
    }

    /* SMS SYSADMIN messages */

    static function messageNoSurveysAvailable() {
        return 'No surveys available.';
    }

    static function messageNoSurveysYet() {
        return 'No surveys yet. Please add a survey by clicking the link below.';
    }

    static function messageNoSectionsYet() {
        return 'No sections yet. Please add a section by clicking the link below.';
    }

    static function messageNoVariablesYet() {
        return 'No variables yet. Please add a variable by clicking the link below.';
    }

    static function messageNoGroupsYet() {
        return 'No groups yet. Please add a group by clicking the link below.';
    }

    static function messageNoTypesYet() {
        return 'No types yet. Please add a type by clicking the link below.';
    }

    static function messageNoUsersYet() {
        return 'No users yet. Please add a user by clicking the link below.';
    }

    static function messageRoutingOk() {
        return 'Routing saved.';
    }

    static function messageRoutingClickError($action) {
        return 'The selected text (' . $action . ') does not correspond to a known component of the survey.';
    }

    static function messageRoutingNeedsFix() {
        return 'There were one or more error(s) in the routing. Please check the following messages.';
    }

    static function messageFillRoutingNeedsFix() {
        return 'There were one or more error(s) in the fill code. Please check the following messages.';
    }

    static function messageCheckRoutingNeedsFix() {
        return 'There were one or more error(s) in the check code. Please check the following messages.';
    }

    static function messageToolsCleanOk() {
        return 'Cleaned.';
    }

    static function messageToolsCleanSelectSurvey() {
        return 'Please select at least one survey.';
    }

    static function messageToolsCleanSelectDataType() {
        return 'Please select at least one type of data.';
    }

    static function messageToolsCompileOk() {
        return 'Compiled successfully.';
    }

    static function messageToolsCompileNotOk() {
        return 'Compiled with error(s). Please use the checker to identify them.';
    }

    static function messageToolsCheckOk() {
        return 'No errors found.';
    }

    static function messageToolsCheckNotOk() {
        return 'Error(s) found: ';
    }

    static function labelErrorsIn() {
        return 'Error(s) in ';
    }
    
    static function labelAssignmentWarnings() {
        return 'Error(s) found in assigned values';
    }

    static function messageToolsCompileSelectSurvey() {
        return 'Please select at least one survey.';
    }

    static function messageToolsCompileSelectComponent() {
        return 'Please select at least one type of component.';
    }

    static function messageToolsImportDbFailure() {
        return 'Please check your database credentials.';
    }

    static function messageToolsImportOK() {
        return "Imported.";
    }

    static function messageToolsBatchEditorNotFound() {
        return 'Please tag one or more survey components.';
    }

    static function messageToolsBatchEditorNoVariablesFound() {
        return 'Please tag one or more variables.';
    }

    static function messageToolsBatchEditorNoTypesFound() {
        return 'Please tag one or more types.';
    }

    static function messageToolsBatchEditorNoGroupsFound() {
        return 'Please tag one or more groups.';
    }

    static function messageToolsBatchEditorNoSectionsFound() {
        return 'Please tag one or more sections.';
    }

    static function messageToolsBatchEditorNotSelected($text) {
        return 'Please select one or more ' . $text . ".";
    }

    static function messageToolsBatchEditorRemoved($text) {
        return 'Selected ' . $text . " were removed.";
    }

    static function messageToolsBatchEditorMoved($text) {
        return 'Selected ' . $text . " were moved.";
    }

    static function messageToolsBatchEditorCopied($text) {
        return 'Selected ' . $text . " were copied.";
    }

    static function messageToolsBatchEditorEdited($text) {
        return 'Selected ' . $text . " were edited.";
    }

    static function messageToolsBatchEditorNotEdited($text) {
        return 'Please select one or more properties to edit.';
    }

    static function messageToolsBatchEditorUnrecognizedAction() {
        return 'Unrecognized action';
    }

    // messageToolsCompileNoSurveysSelected

    static function messageAccessSettingsChanged() {
        return 'Access settings changed.';
    }

    static function messageAssistanceSettingsChanged() {
        return 'Assistance settings changed.';
    }

    static function messageDisplaySettingsChanged() {
        return 'Display settings changed.';
    }

    static function messageLanguageSettingsChanged() {
        return 'Language settings changed.';
    }

    static function messageLanguageSettingsNotChanged() {
        return 'Please ensure the selected default language is in the list of available languages.';
    }

    static function messageValidationSettingsChanged() {
        return 'Validation settings changed';
    }

    static function messageModeSettingsChanged() {
        return 'Interview mode settings changed';
    }

    static function messageInteractiveSettingsChanged() {
        return 'Interactive settings changed';
    }

    static function messageModeSettingsNotChanged() {
        return 'Please ensure the selected default mode is in the list of available interview modes.';
    }

    static function messageGeneralSettingsChanged() {
        return 'General settings changed';
    }

    static function messageDataSettingsChanged() {
        return 'Output settings changed';
    }

    static function messageNavigationSettingsChanged() {
        return 'Navigation settings changed';
    }

    static function messageSurveyChanged($name) {
        return 'Survey `' . $name . '` changed.';
    }

    static function messageSurveyCopied($name) {
        return 'Survey `' . $name . '` copied.';
    }

    static function messageSurveyNotCopied($name) {
        return 'Survey `' . $name . '` not copied.';
    }

    static function messageSurveyRemoved($name) {
        return 'Survey `' . $name . '` removed.';
    }

    static function messageSurveyAdded($name) {
        return 'Survey `' . $name . '` added.';
    }

    static function messageRemoveSurvey($name) {
        return "Are you sure you want to remove survey `" . $name . "` (and all of its sections, variables, and types)?";
    }

    static function messageSectionChanged($name) {
        return 'Section `' . $name . '` changed.';
    }

    static function messageSectionCopied($name) {
        return 'Section `' . $name . '` copied.';
    }

    static function messageSectionNotCopied($name) {
        return 'Section `' . $name . '` not copied.';
    }

    static function messageSectionRemoved($name) {
        return 'Section `' . $name . '` removed.';
    }

    static function messageSectionMoved($name) {
        return 'Section `' . $name . '` moved.';
    }

    static function messageSectionNotMoved($name) {
        return 'Section `' . $name . '` not moved.';
    }

    static function messageMoveSection($name) {
        return "Where do you want to move section `" . $name . "` to?";
    }

    static function messageCopySection($name) {
        return "Where do you want to copy section `" . $name . "` to?";
    }

    static function messageSectionAdded($name) {
        return 'Section `' . $name . '` added.';
    }

    static function messageSectionRenamed($old, $new) {
        return 'Section `' . $old . '` renamed to `' . $new . '`';
    }

    static function messageSectionNotRenamed() {
        return 'Please choose a name different from the current one.';
    }

    static function messageRefactorSection($name) {
        return "Are you sure you want to rename section `" . $name . "`?";
    }

    static function messageRemoveSection($name) {
        return "Are you sure you want to remove section `" . $name . "` (and all of its variables)?";
    }

    static function messageVariableChanged($name) {
        return 'Variable `' . $name . '` changed.';
    }

    static function messageVariableCopied($name) {
        return 'Variable `' . $name . '` copied.';
    }

    static function messageVariableNotCopied($name) {
        return 'Variable `' . $name . '` not copied.';
    }

    static function messageVariableMoved($name) {
        return 'Variable `' . $name . '` moved.';
    }

    static function messageVariableNotMoved($name) {
        return 'Variable `' . $name . '` not moved.';
    }

    static function messageVariableRemoved($name) {
        return 'Variable `' . $name . '` removed.';
    }

    static function messageVariableAdded($name) {
        return 'Variable `' . $name . '` added.';
    }

    static function messageMoveVariable($name) {
        return "Where do you want to move variable `" . $name . "` to?";
    }

    static function messageCopyVariable($name) {
        return "Where do you want to copy variable `" . $name . "` to?";
    }

    static function messageRemoveVariable($name) {
        return "Are you sure you want to remove variable `" . $name . "`?";
    }

    static function messageRefactorVariable($name) {
        return "Are you sure you want to rename variable `" . $name . "`?";
    }

    static function messageVariableRenamed($old, $new) {
        return 'Variable `' . $old . '` renamed to `' . $new . '`';
    }

    static function messageVariableNotRenamed() {
        return 'Please choose a name different from the current one.';
    }

    static function messageGroupCopied($name) {
        return 'Group `' . $name . '` copied.';
    }

    static function messageGroupNotCopied($name) {
        return 'Group `' . $name . '` not copied.';
    }

    static function messageGroupChanged($name) {
        return 'Group `' . $name . '` changed.';
    }

    static function messageMoveGroup($name) {
        return "Where do you want to move group `" . $name . "` to?";
    }

    static function messageCopyGroup($name) {
        return "Where do you want to copy group `" . $name . "` to?";
    }

    static function messageGroupNotRenamed() {
        return 'Please choose a name different from the current one.';
    }

    static function messageGroupRemoved($name) {
        return 'Group `' . $name . '` removed.';
    }

    static function messageGroupMoved($name) {
        return 'Group `' . $name . '` moved.';
    }

    static function messageGroupNotMoved($name) {
        return 'Group `' . $name . '` not moved.';
    }

    static function messageRefactorGroup($name) {
        return "Are you sure you want to rename group `" . $name . "`?";
    }

    static function messageGroupAdded($name) {
        return 'Group `' . $name . '` added.';
    }

    static function messageGroupRenamed($old, $new) {
        return 'Group `' . $old . '` renamed to `' . $new . '`';
    }

    static function messageRemoveGroup($name) {
        return "Are you sure you want to remove group `" . $name . "`?";
    }

    static function messageTypeChanged($name) {
        return 'Type `' . $name . '` changed.';
    }

    static function messageTypeCopied($name) {
        return 'Type `' . $name . '` copied.';
    }

    static function messageTypeNotCopied($name) {
        return 'Type `' . $name . '` not copied.';
    }

    static function messageTypeNotChanged($name) {
        return 'Type `' . $name . '` not changed.';
    }

    static function messageMoveType($name) {
        return "Where do you want to move type `" . $name . "` to?";
    }

    static function messageCopyType($name) {
        return "Where do you want to copy type `" . $name . "` to?";
    }

    static function messageRefactorType($name) {
        return "Are you sure you want to rename type `" . $name . "`?";
    }

    static function messageTypeNotRenamed() {
        return 'Please choose a name different from the current one.';
    }

    static function messageTypeRemoved($name) {
        return 'Type `' . $name . '` removed.';
    }

    static function messageTypeMoved($name) {
        return 'Type `' . $name . '` moved.';
    }

    static function messageTypeNotMoved($name) {
        return 'Type `' . $name . '` not moved.';
    }

    static function messageTypeRenamed($old, $new) {
        return 'Type `' . $old . '` renamed to `' . $new . '`';
    }

    static function messageTypeAdded($name) {
        return 'Type `' . $name . '` added.';
    }

    static function messageRemoveType($name) {
        return "Are you sure you want to remove type `" . $name . "`?";
    }

    static function messageUserChanged($name) {
        return 'User `' . $name . '` changed.';
    }

    static function messageUserRemoved($name) {
        return 'User `' . $name . '` removed.';
    }

    static function messageUserAdded($name) {
        return 'User `' . $name . '` added.';
    }

    static function messageRemoveUser($name) {
        return "Are you sure you want to remove user `" . $name . "`? Type `REMOVE` to continue.";
    }

    static function messageCopyUser($name) {
        return "Are you sure you want to copy user `" . $name . "`? Type `COPY` to continue.";
    }

    static function messageCheckerNoName() {
        return 'Please specify a name.';
    }

    static function messageCheckerInvalidName() {
        return 'Please use only letters, numbers or underscores for the name and start the name with a letter.';
    }

    static function messageCheckerVariableExists($name) {
        return 'A variable with the name `' . $name . '` already exists.';
    }

    static function messageCheckerVariableNotExists($name) {
        return 'A variable with the name `' . $name . '` does not exist yet. Please add it.';
    }

    static function messageCheckerVariableNotArray($name) {
        return 'Variable `' . $name . '` is not an array.';
    }

    static function messageCheckerVariableArray($name) {
        return 'Variable `' . $name . '` is an array and requires an array identifier.';
    }

    static function messageCheckerGroupExists($name) {
        return 'A group with the name `' . $name . '` already exists.';
    }

    static function messageCheckerTypeExists($name) {
        return 'A type with the name `' . $name . '` already exists.';
    }

    static function messageCheckerSectionExists($name) {
        return 'A section with the name `' . $name . '` already exists.';
    }

    static function messageCheckerVariableNumericOptionCodes($t) {
        return 'Answer option `' . $t . '` must start with a numerical code.';
    }

    static function messageCheckerVariableNoOptionCodes() {
        return 'No answer options specified.';
    }

    static function messageCheckerFunctionNotExists($f) {
        return 'Function  `' . $f . '` does not exist.';
    }

    /* options */

    static function optionsFollowGeneric() {
        return 'Follow survey';
    }

    static function optionsFollowType() {
        return 'Follow type';
    }

    static function optionTypeNone() {
        return 'No type';
    }

    static function optionsModeChangeProgrammaticAllowed() {
        return 'Programmatic only';
    }

    static function optionsModeChangeRespondentAllowed() {
        return 'Programmatic and by respondent';
    }

    static function optionsModeChangeNotAllowed() {
        return 'No';
    }

    static function optionsLanguageChangeProgrammaticAllowed() {
        return 'Programmatic only';
    }

    static function optionsLanguageChangeRespondentAllowed() {
        return 'Programmatic and by respondent';
    }

    static function optionsLanguageChangeNotAllowed() {
        return 'No';
    }

    static function optionsLanguageReentryYes() {
        return 'Yes';
    }

    static function optionsLanguageReentryNo() {
        return 'No';
    }

    static function optionsLanguageBackYes() {
        return 'Yes';
    }

    static function optionsLanguageBackNo() {
        return 'No';
    }

    static function optionsModeReentryYes() {
        return 'Yes';
    }

    static function optionsModeReentryNo() {
        return 'No';
    }

    static function optionsModeBackYes() {
        return 'Yes';
    }

    static function optionsModeBackNo() {
        return 'No';
    }

    static function optionsArrayYes() {
        return 'Yes';
    }

    static function optionsArrayNo() {
        return 'No';
    }

    static function optionsScreendumpsYes() {
        return 'Yes';
    }

    static function optionsScreendumpsNo() {
        return 'No';
    }

    static function optionsKeepYes() {
        return 'Yes';
    }

    static function optionsKeepNo() {
        return 'No';
    }

    static function optionsInputMaskNo() {
        return 'No';
    }

    static function optionsInputMaskYes() {
        return 'Yes';
    }

    static function optionsHiddenYes() {
        return 'Exclude';
    }

    static function optionsInputMaskNone() {
        return 'None';
    }

    static function optionsHiddenNo() {
        return 'Include';
    }

    static function optionsScreenDumpsTypeHTML() {
        return 'HTML Source';
    }

    static function optionsScreenDumpsTypeCarousel() {
        return 'Carousel';
    }

    static function optionsDataDataRecordTable() {
        return 'Data record table';
    }

    static function optionsDataDataTable() {
        return 'Data table';
    }

    static function optionsDataReal() {
        return 'Actual data';
    }

    static function optionsDataTest() {
        return 'Test data';
    }

    static function labelOutputDataVarlist() {
        return 'Only tagged variables';
    }

    static function optionsSubDataYes() {
        return 'Yes';
    }

    static function optionsSubDataNo() {
        return 'No';
    }

    static function optionsValueLabelWidthShort() {
        return 'Only in tabs';
    }

    static function optionsValueLabelWidthFull() {
        return 'In tabs and data editor';
    }

    static function optionsValueLabelNumbersYes() {
        return 'Yes';
    }

    static function optionsValueLabelNumbersNo() {
        return 'No';
    }

    static function optionsDataClean() {
        return 'Yes';
    }

    static function optionsDataDirty() {
        return 'No';
    }

    static function optionsDataNotHidden() {
        return 'Include';
    }

    static function optionsDataHidden() {
        return 'Exclude';
    }

    static function optionsDataCompleted() {
        return 'Completed interviews only';
    }

    static function optionsDataNotCompleted() {
        return 'All interviews';
    }

    static function optionsValueLabelsYes() {
        return 'Yes';
    }

    static function optionsValueLabelsNo() {
        return 'No';
    }

    static function optionsFieldnameLowerCase() {
        return 'Lowercase';
    }

    static function optionsFieldnameUpperCase() {
        return 'Uppercase';
    }

    static function optionsPrimaryKeyInDataYes() {
        return 'Yes';
    }

    static function optionsPrimaryKeyInDataNo() {
        return 'No';
    }

    static function optionsVariablesNoDataInDataYes() {
        return 'Yes';
    }

    static function optionsVariablesNoDataInDataNo() {
        return 'No';
    }

    static function optionsFileTypeStata() {
        return 'Stata';
    }

    static function optionsFileTypeCSV() {
        return 'CSV';
    }

    static function optionsMarkEmptyInVariable() {
        return 'In variable';
    }

    static function optionsMarkEmptyInSkipVariable() {
        return 'In skip variable';
    }

    static function optionsMarkEmptyNo() {
        return 'No';
    }

    static function optionsIfEmptyAllow() {
        return 'Allow to continue';
    }

    static function optionsIfEmptyNotAllow() {
        return 'Don\'t allow to continue';
    }

    static function optionsIfEmptyWarn() {
        return 'Display one-time warning';
    }

    static function optionsIfErrorAllow() {
        return 'Allow to continue';
    }

    static function optionsIfErrorNotAllow() {
        return 'Don\'t allow to continue';
    }

    static function optionsIfErrorWarn() {
        return 'Display one-time warning';
    }

    static function optionsProgressBarWhole() {
        return 'Overall';
    }

    static function optionsProgressBarSection() {
        return 'Per section';
    }

    static function optionsProgressBarNo() {
        return 'No';
    }

    static function optionsProgressBarPErcent() {
        return 'Percentage only';
    }

    static function optionsProgressBarBar() {
        return 'Bar only';
    }

    static function optionsProgressBarAll() {
        return 'Bar and percentage';
    }

    static function optionsButtonYes() {
        return 'Show';
    }

    static function optionsButtonNo() {
        return 'Hide';
    }

    static function optionsAccessTypeAnonymous() {
        return 'No login required';
    }

    static function optionsAccessTypeDirect() {
        return 'Via external web site only';
    }

    static function optionsAccessTypeLogincode() {
        return 'By login code';
    }

    static function optionsAccessReturnYes() {
        return 'Yes';
    }

    static function optionsAccessReturnNo() {
        return 'No';
    }

    static function optionsGroupYes() {
        return 'Yes';
    }

    static function optionsGroupNo() {
        return 'No';
    }
    
    static function optionsKnobRotationClockwise() {
        return 'Clockwise';
    }

    static function optionsKnobRotationAntiClockwise() {
        return 'Anti-clockwise';
    }

    static function optionsOrientationHorizontal() {
        return 'Horizontal';
    }

    static function optionsOrientationVertical() {
        return 'Vertical';
    }

    static function optionsOrientationCustom() {
        return 'Custom';
    }

    static function optionsEnumeratedOrderOptionFirst() {
        return 'Option, then label';
    }

    static function optionsEnumeratedOrderLabelFirst() {
        return 'Label, then option';
    }
    
    static function optionsRankColumnTwo() {
        return 'Two columns';
    }

    static function optionsRankColumnOne() {
        return 'One column';
    }

    static function optionsSliderOrientationHorizontal() {
        return 'Horizontal';
    }

    static function optionsSliderOrientationVertical() {
        return 'Vertical';
    }

    static function optionsSliderLabelsTop() {
        return 'Top';
    }

    static function optionsSliderLabelsBottom() {
        return 'Bottom';
    }

    static function optionsSliderTooltipYes() {
        return 'Yes';
    }

    static function optionsSliderTooltipNo() {
        return 'No';
    }

    static function optionsSliderTextboxYes() {
        return 'Yes';
    }

    static function optionsSliderTextboxNo() {
        return 'No';
    }

    static function optionsEnumeratedTextboxYes() {
        return 'Yes';
    }

    static function optionsEnumeratedTextboxNo() {
        return 'No';
    }

    static function optionsEnumeratedLabelOnly() {
        return 'Label only';
    }

    static function optionsEnumeratedInputOnly() {
        return 'Input only (no label)';
    }

    static function optionsEnumeratedLabelCode() {
        return 'Include code';
    }

    static function optionsEnumeratedLabelAll() {
        return 'Include code and value label';
    }

    static function optionsEnumeratedYes() {
        return 'Yes';
    }

    static function optionsEnumeratedNo() {
        return 'No';
    }

    static function optionsAlignmentLeft() {
        return 'Left';
    }

    static function optionsAlignmentRight() {
        return 'Right';
    }

    static function optionsAlignmentJustified() {
        return 'Justified';
    }

    static function optionsAlignmentCenter() {
        return 'Centered';
    }

    static function optionsFormattingBold() {
        return 'Bold';
    }

    static function optionsFormattingItalic() {
        return 'Italic';
    }

    static function optionsFormattingUnderlined() {
        return 'Underline';
    }
    
    static function optionsSpinnerHorizontal() {
        return 'Horizontal';
    }

    static function optionsSpinnerVertical() {
        return 'Vertical';
    }
    
    static function optionsSpinnerNo() {
        return 'No';
    }

    static function optionsSpinnerYes() {
        return 'Yes';
    }
    
    static function optionsManualNo() {
        return 'No';
    }

    static function optionsManualYes() {
        return 'Yes';
    }

    static function optionsHeaderFixedNo() {
        return 'No';
    }

    static function optionsHeaderFixedYes() {
        return 'Yes';
    }

    static function optionsErrorPlacementWithQuestion() {
        return 'With question';
    }

    static function optionsErrorPlacementAtTop() {
        return 'At top of page';
    }

    static function optionsErrorPlacementAtBottom() {
        return 'At bottom of page';
    }

    static function optionsAccessReentryActionSame() {
        return 'At last viewed screen of survey';
    }

    static function optionsAccessReentryActionSameRedo() {
        return 'At last viewed screen of survey redoing last action';
    }

    static function optionsAccessReentryActionNext() {
        return 'At screen after last viewed screen of survey';
    }

    static function optionsAccessReentryActionNotAllowed() {
        return 'No return allowed';
    }

    static function optionsAccessReentryActionFirst() {
        return 'At first screen of survey';
    }

    static function optionsAccessReentryActionStart() {
        return 'At beginning of survey';
    }

    static function optionsAccessReTurnAfterCompletionFromStart() {
        return 'At beginning of survey';
    }

    static function optionsAccessReTurnAfterCompletionNo() {
        return 'No re-entry allowed';
    }

    static function optionsAccessReTurnAfterCompletionFirst() {
        return 'At first screen of survey';
    }

    static function optionsAccessReTurnAfterCompletionLast() {
        return 'At last viewed screen of survey';
    }

    static function optionsAccessReTurnAfterCompletionLastRedo() {
        return 'At last viewed screen of survey redoing last action';
    }

    static function optionsPreloadRedoYes() {
        return 'Yes';
    }

    static function optionsPreloadRedoNo() {
        return 'No';
    }

    static function optionsDataInputMaskYes() {
        return 'Yes';
    }

    static function optionsDataInputMaskNo() {
        return 'No';
    }

    static function optionsDataSkipYes() {
        return 'Yes';
    }

    static function optionsDataSkipNo() {
        return 'No';
    }

    static function optionsDataKeepOnlyYes() {
        return 'Yes';
    }

    static function optionsDataKeepOnlyNo() {
        return 'No';
    }

    static function optionsDataKeepYes() {
        return 'Yes';
    }

    static function optionsDataKeepNo() {
        return 'No';
    }

    static function optionsKeyboardBindingYes() {
        return 'Yes';
    }

    static function optionsDataSetOfEnumeratedDefault() {
        return 'Answer code if answered';
    }

    static function optionsDataSetOfEnumeratedBinary() {
        return 'Binary yes/no';
    }

    static function optionsKeyboardBindingNo() {
        return 'No';
    }

    static function optionsIndividualDKRFNAYes() {
        return 'Yes';
    }

    static function optionsIndividualDKRFNANo() {
        return 'No';
    }

    static function optionsSectionHeaderYes() {
        return 'Yes';
    }

    static function optionsSectionHeaderNo() {
        return 'No';
    }

    static function optionsSectionFooterYes() {
        return 'Yes';
    }

    static function optionsSectionFooterNo() {
        return 'No';
    }

    static function optionsUserModeYes() {
        return 'Yes';
    }

    static function optionsUserModeNo() {
        return 'No';
    }

    //contact codes

    static function startInterviewCode() {
        return 100;
    }

    static function completedInterviewCode() {
        return 500;
    }

    static function optionsDispositionContactCode($respondent) {
        if ($respondent instanceof Respondent) {
            //code, proxy, name, explanation, resist, nonsample, display in dropdown 
            return array(
                '100' => array('0', 'Interview started', 'Interview started.', '0', '0', '0'),
                '101' => array('1', 'Contact no resistance', 'Contact was made. Respondent willing to participate.', '0', '0', '1'),
                '102' => array('1', 'Resistance', 'Respondent refused to participate.', '1', '0', '1'),
                '103' => array('0', 'Unable to contact', 'Tried to contact the respondent. A message was left.', '0', '0', '1'),
                '104' => array('0', 'Unable to locate', 'The respondent cannot be found at the given address.', '0', '1', '1'),
                '105' => array('1', 'Unable to participate', 'All respondents are physically or mentally unable/incompetent to participate.', '0', '0', '1'),
                '106' => array('1', 'All deceased', 'All respondents have deceased.', '0', '0', '1'),
                '107' => array('1', 'Language barriers', 'Respondent does not speak or read the target language well enough to complete the interview.', '0', '0', '1')
            );
        } else {

            return array(
                '100' => array('0', 'Interview started', 'Interview started.', '0', '0', '0'),
                '101' => array('1', 'Contact no resistance', 'Contact was made. Household willing to participate.', '0', '0', '1'),
                '102' => array('1', 'Resistance', 'All household members refused to participate.', '1', '0', '1'),
                '103' => array('0', 'Unable to contact', 'Tried to contact the household. A message was left.', '0', '0', '1'),
                '104' => array('0', 'Unable to locate', 'The household members cannot be found at the given address.', '0', '1', '1'),
                '105' => array('1', 'Unable to participate', 'All household members are physically or mentally unable/incompetent to participate.', '0', '0', '1'),
                '106' => array('1', 'All deceased', 'All household members have deceased.', '0', '0', '1'),
                '107' => array('1', 'Language barriers', 'No household member speaks or reads the target language well enough to complete the interview.', '0', '0', '1')
            );
        }
    }

    static function optionsFinalDispositionContactCode($respondent) {
        if ($respondent instanceof Respondent) {
            //code, proxy, name, explanation, resist, nonsample 
            return array(
                '500' => array('0', 'Interview completed', 'Interview completed', '0', '0', '0'),
                '502' => array('0', 'Final refusal', 'Respondent refused to participate.', '1', '0', '1'),
                '503' => array('0', 'Final non contact', 'Respondent could not be contacted.', '0', '0', '1'),
                '504' => array('0', 'Final non sample', 'Given address not a residential area.', '0', '1', '1')
            );
        } else {
            return array(
                '500' => array('0', 'Interview completed', 'Interview completed', '0', '0', '0'),
                '502' => array('0', 'Final refusal', 'All household members refused to participate.', '1', '0', '1'),
                '503' => array('0', 'Final non contact', 'All household members could not be contacted.', '0', '0', '1'),
                '504' => array('0', 'Final non sample', 'Given address not a residential area.', '0', '1', '1')
            );
        }
    }

    /* buttons */

    static function buttonSave() {
        return 'Save';
    }

    static function buttonAdd() {
        return 'Add';
    }

    static function buttonEdit() {
        return 'Edit';
    }

    static function buttonRemove() {
        return 'Remove';
    }

    static function buttonCopy() {
        return 'Copy';
    }

    /* headers */

    static function headerAddSurvey() {
        return "Add survey";
    }

    static function headerEditSurvey() {
        return "Edit survey";
    }

    static function headerCopySurvey() {
        return "Copy survey";
    }

    static function headerRemoveSurvey() {
        return "Remove survey";
    }

    static function headerAddSection() {
        return "Add section";
    }

    static function headerEditSection() {
        return "Edit section";
    }

    static function headerCopySection() {
        return "Copy section";
    }

    static function headerMoveSection() {
        return "Move section";
    }

    static function headerRemoveSection() {
        return "Remove section";
    }

    static function headerRefactorSection() {
        return "Rename section";
    }

    static function headerAddGroup() {
        return "Add group";
    }

    static function headerEditGroup() {
        return "Edit group";
    }

    static function headerMoveGroup() {
        return "Move group";
    }

    static function headerCopyGroup() {
        return "Copy group";
    }

    static function headerRemoveGroup() {
        return "Remove group";
    }

    static function headerRefactorGroup() {
        return "Rename group";
    }

    static function headerAddVariable() {
        return "Add variable";
    }

    static function headerEditVariable() {
        return "Edit variable";
    }

    static function headerCopyVariable() {
        return "Copy variable";
    }

    static function headerRemoveVariable() {
        return "Remove variable";
    }

    static function headerMoveVariable() {
        return "Move variable";
    }

    static function headerRefactorVariable() {
        return "Rename variable";
    }

    static function headerAddType() {
        return "Add type";
    }

    static function headerMoveType() {
        return "Move type";
    }

    static function headerEditType() {
        return "Edit type";
    }

    static function headerRefactorType() {
        return "Rename type";
    }

    static function headerEditTypeGeneral() {
        return 'Edit general';
    }

    static function headerEditTypeOutput() {
        return 'Edit output';
    }

    static function headerEditTypeInteractive() {
        return 'Edit interactive';
    }

    static function headerEditTypeVerification() {
        return 'Edit validation';
    }

    static function headerEditTypeLayout() {
        return 'Edit display';
    }

    static function headerEditTypeAssistance() {
        return 'Edit assistance';
    }

    static function headerEditTypeFill() {
        return 'Edit use as fill';
    }

    static function headerCopyType() {
        return "Copy type";
    }

    static function headerRemoveType() {
        return "Remove type";
    }

    static function headerEditSettingsGeneral() {
        return 'General';
    }

    static function headerEditSettingsMode() {
        return 'Interview mode';
    }

    static function headerEditSettingsAccess() {
        return 'Edit access settings';
    }

    static function headerEditSettingsAssistance() {
        return 'Edit assistance settings';
    }

    static function headerEditSettingsData() {
        return 'Edit output settings';
    }

    static function headerEditSettingsLanguage() {
        return 'Edit language settings';
    }

    static function headerEditSettingsLayout() {
        return 'Edit display settings';
    }

    static function headerEditSettingsValidation() {
        return 'Edit validation settings';
    }

    static function headerEditSettingsInteractive() {
        return 'Edit interactive settings';
    }

    static function headerEditSettingsNavigation() {
        return 'Edit navigation settings';
    }

    static function headerTranslateSettingsAssistance() {
        return 'Translate assistance texts';
    }

    static function headerTranslateSettingsLayout() {
        return 'Translate display texts';
    }

    static function headerTranslateType() {
        return 'Translate type';
    }

    static function headerTranslateGroup() {
        return 'Translate group';
    }

    static function headerTranslateVariable() {
        return 'Translate variable';
    }

    static function headerTranslateTypeGeneral() {
        return 'Translate general';
    }

    static function headerTranslateTypeAssistance() {
        return 'Translate assistance';
    }

    static function headerTranslateTypeLayout() {
        return 'Translate display';
    }

    static function headerSettings() {
        return 'Settings';
    }

    static function headerTexts() {
        return 'Texts';
    }

    static function headerSections() {
        return 'Sections';
    }

    static function headerSurveys() {
        return 'Surveys';
    }

    static function headerGroups() {
        return 'Groups';
    }

    static function headerTypes() {
        return 'Types';
    }

    static function headerOutput() {
        return 'Output';
    }

    static function headerOutputData() {
        return 'Data';
    }

    static function headerOutputAuxiliaryData() {
        return 'Auxiliary data';
    }

    static function headerOutputRemarkData() {
        return 'Remarks';
    }

    static function headerOutputTimingsData() {
        return 'Timings';
    }

    static function headerOutputRawData() {
        return 'Raw data';
    }

    static function headerOutputRawDataSingle() {
        return 'From a single survey';
    }

    static function headerOutputRawDataMultiple() {
        return 'Combined from multiple surveys';
    }

    static function headerOutputStatistics() {
        return 'Statistics';
    }

    static function headerOutputStatisticsPlatform() {
        return 'Platform information';
    }

    static function headerOutputStatisticsTimings() {
        return 'Timings distribution';
    }

    static function headerOutputStatisticsTimingsOverTime() {
        return 'Timings over time';
    }

    static function headerOutputStatisticsTimingsRespondent() {
        return 'Times per screen per respondent';
    }

    static function headerOutputStatisticsAggregate() {
        return 'Aggregate data';
    }

    static function headerOutputStatisticsResponse() {
        return 'Response';
    }

    static function headerOutputStatisticsContactGraphs() {
        return 'Contact graphs';
    }

    static function timingsBrackets() {
        return array("1-5 minutes", "5-10 minutes", "11-15 minutes", "16-20 minutes", "21-25 minutes", "26-30 minutes", "31-35 minutes", "36-40 minutes", "More than 40 minutes");
    }

    static function labelNumberOfRespondents() {
        return 'Number of respondents';
    }

    static function labelCompletedInterviews() {
        return 'completed interviews';
    }

    static function labelCompletedInterviewsUpper() {
        return 'Completed interviews';
    }

    static function labelNumberOfResponses() {
        return 'Responses';
    }

    static function labelTimeSpent() {
        return 'Time spent';
    }

    static function labelTimeSpentMinutes() {
        return 'minutes';
    }

    static function labelTimeSpentAverage() {
        return 'Average time spent';
    }

    static function labelTimeSpentTotal() {
        return 'Total time spent';
    }

    static function labelNubis() {
        return 'Nubis';
    }

    static function headerOutputDocumentation() {
        return 'Documentation';
    }

    static function headerOutputPaperVersion() {
        return 'Paper version';
    }

    static function headerOutputDictionary() {
        return 'Dictionary';
    }

    static function headerOutputTranslation() {
        return 'Translation';
    }

    static function headerOutputScreenDumps() {
        return 'Screen dumps';
    }

    static function labelTypeEditOutputInputMask() {
        return 'Store input mask';
    }

    static function labelTypeEditOutputSetOfEnumerated() {
        return 'Checkbox output';
    }

    static function labelTypeEditOutputValueLabelWidth() {
        return 'Value label display (STATA only)';
    }

    static function headerOutputScreenDumpsFor($primkey) {
        return $primkey;
    }

    static function headerTools() {
        return 'Tools';
    }

    static function headerToolsBatchEditor() {
        return 'Batch editor';
    }

    static function headerToolsCleaner() {
        return 'Cleaner';
    }

    static function headerToolsCompiler() {
        return 'Compiler';
    }

    static function headerToolsExporter() {
        return 'Exporter';
    }

    static function headerToolsImporter() {
        return 'Importer';
    }

    static function headerToolsTester() {
        return 'Tester';
    }

    static function headerToolsChecker() {
        return 'Checker';
    }

    static function headerToolsFlooder() {
        return 'Flooder';
    }

    static function headerPreferences() {
        return 'Preferences';
    }

    static function headerUsers() {
        return 'Users';
    }

    /* override base languages */

    static function getLanguagesArray() {
        $languages = array();
        $languages['en'] = array('name' => 'English', 'country' => 'US', 'value' => 1);
        $languages['es'] = array('name' => 'español', 'country' => 'ES', 'value' => 2);
        $languages['om'] = array('name' => 'Afaan Oromo', 'country' => 'ET', 'value' => 34);
        $languages['aa'] = array('name' => 'Afaraf', 'country' => 'ER', 'value' => 3);
        $languages['af'] = array('name' => 'Afrikaans', 'country' => 'SA', 'value' => 4);
        $languages['ak'] = array('name' => 'Akan', 'country' => 'GH', 'value' => 5);
        $languages['an'] = array('name' => 'aragonés', 'country' => 'ES', 'value' => 6);
        $languages['ig'] = array('name' => 'Asụsụ Igbo', 'country' => 'NG', 'value' => 7);
        $languages['gn'] = array('name' => 'Avañe\'ẽ', 'country' => 'PY', 'value' => 8);
        $languages['ae'] = array('name' => 'avesta', 'country' => 'IR', 'value' => 9);
        $languages['ay'] = array('name' => 'aymar aru', 'country' => 'BO', 'value' => 10);
        $languages['az'] = array('name' => 'azərbaycan dili', 'country' => 'AZ', 'value' => 11);
        $languages['id'] = array('name' => 'Bahasa Indonesia', 'country' => 'ID', 'value' => 12);
        $languages['ms'] = array('name' => 'bahasa Melayu', 'value' => 13); // 'country' => 'SG',
        $languages['bm'] = array('name' => 'bamanankan', 'country' => 'ML', 'value' => 14);
        $languages['jv'] = array('name' => 'basa Jawa', 'country' => 'ID', 'value' => 15);
        $languages['su'] = array('name' => 'Basa Sunda', 'country' => 'ID', 'value' => 16);
        $languages['bi'] = array('name' => 'Bislama', 'country' => 'VU', 'value' => 17);
        $languages['bs'] = array('name' => 'bosanski jezik', 'country' => 'BA', 'value' => 18);
        $languages['br'] = array('name' => 'brezhoneg', 'country' => 'FR', 'value' => 19);
        $languages['ca'] = array('name' => 'català', 'country' => 'ES', 'value' => 20);
        $languages['ch'] = array('name' => 'Chamoru', 'country' => 'US', 'value' => 21);
        $languages['ny'] = array('name' => 'chiCheŵa', 'country' => 'MW', 'value' => 22);
        $languages['sn'] = array('name' => 'chiShona', 'country' => 'MZ', 'value' => 23);
        $languages['co'] = array('name' => 'corsu', 'country' => 'FR', 'value' => 24);
        $languages['cy'] = array('name' => 'Cymraeg', 'country' => 'GB', 'value' => 25);
        $languages['da'] = array('name' => 'dansk', 'country' => 'DK', 'value' => 26);
        $languages['se'] = array('name' => 'Davvisámegiella', 'country' => 'NO', 'value' => 27);
        $languages['de'] = array('name' => 'Deutsch', 'country' => 'DE', 'value' => 28);
        $languages['nv'] = array('name' => 'Diné bizaad', 'country' => 'US', 'value' => 29);
        $languages['de'] = array('name' => 'Deutsch', 'country' => 'DE', 'value' => 30);
        $languages['et'] = array('name' => 'eesti', 'country' => 'EE', 'value' => 31);
        $languages['na'] = array('name' => 'Ekakairũ Naoero', 'country' => 'NR', 'value' => 32);
        $languages['uk'] = array('name' => 'English', 'country' => 'GB', 'value' => 33);
        //$languages['es'] = array('name' => 'Español', 'country' => 'ES', 'value' => 34);
        $languages['eo'] = array('name' => 'Esperanto', 'value' => 35); // 'country' => '',
        $languages['ee'] = array('name' => 'Eʋegbe', 'country' => 'TG', 'value' => 36);
        $languages['to'] = array('name' => 'faka Tonga', 'country' => 'TO', 'value' => 37);
        $languages['mg'] = array('name' => 'fiteny malagasy', 'country' => 'MG', 'value' => 38);
        $languages['fr'] = array('name' => 'français', 'country' => 'FR', 'value' => 39);
        $languages['fy'] = array('name' => 'Frysk', 'country' => 'NL', 'value' => 40);
        $languages['ff'] = array('name' => 'Fulfulde', 'country' => 'FR', 'value' => 41);
        $languages['fo'] = array('name' => 'føroyskt', 'country' => 'FO', 'value' => 42);
        $languages['gy'] = array('name' => 'Gaeilge', 'country' => 'IE', 'value' => 43);
        $languages['sm'] = array('name' => 'gagana fa\'a Samoa', 'country' => 'WS', 'value' => 44);
        $languages['gl'] = array('name' => 'galego', 'country' => 'ES', 'value' => 45);
        $languages['sq'] = array('name' => 'gjuha shqipe', 'value' => 46); // 'country' => ''
        $languages['gd'] = array('name' => 'Gàidhlig', 'country' => 'GB', 'value' => 47);
        $languages['ki'] = array('name' => 'Gĩkũyũ', 'country' => 'KE', 'value' => 48);
        $languages['ha'] = array('name' => 'Hausa', 'country' => 'EH', 'value' => 49);
        $languages['ho'] = array('name' => 'Hiri Motu', 'country' => 'PG', 'value' => 50);
        $languages['hr'] = array('name' => 'hrvatski jezik', 'country' => 'HR', 'value' => 51);
        $languages['io'] = array('name' => 'Ido', 'value' => 52); // 'country' => 'EH',
        $languages['rw'] = array('name' => 'Ikinyarwanda', 'country' => 'RW', 'value' => 53);
        $languages['rn'] = array('name' => 'Ikirundi', 'country' => 'BI', 'value' => 54);
        $languages['ia'] = array('name' => 'Interlingua', 'value' => 55); //'country' => 'EH',
        $languages['nd'] = array('name' => 'isiNrebele', 'country' => 'ZA', 'value' => 56);
        $languages['nr'] = array('name' => 'isiNdebele', 'country' => 'ZW', 'value' => 57);
        $languages['xh'] = array('name' => 'isiXhosa', 'country' => 'ZA', 'value' => 58);
        $languages['zu'] = array('name' => 'isiZulu', 'country' => 'ZA', 'value' => 59);
        $languages['it'] = array('name' => 'italiano', 'country' => 'IT', 'value' => 60);
        $languages['ik'] = array('name' => 'Iñupiaq', 'country' => 'US', 'value' => 61);
        $languages['pl'] = array('name' => 'polski', 'country' => 'PL', 'value' => 62);
        $languages['mh'] = array('name' => 'Kajin M̧ajeļ', 'country' => 'MH', 'value' => 63);
        $languages['kl'] = array('name' => 'kalaallisut', 'country' => 'GL', 'value' => 64);
        $languages['kr'] = array('name' => 'Kanuri', 'country' => 'CM', 'value' => 65);
        $languages['kw'] = array('name' => 'Kernewek', 'country' => 'GB', 'value' => 66);
        $languages['kg'] = array('name' => 'KiKongo', 'country' => 'CG', 'value' => 67);
        $languages['sw'] = array('name' => 'Kiswahili', 'country' => 'TZ', 'value' => 68);
        $languages['ht'] = array('name' => 'Kreyòl ayisyen', 'country' => 'HT', 'value' => 69);
        $languages['kj'] = array('name' => 'Kuanyama', 'country' => 'AO', 'value' => 71);
        $languages['ku'] = array('name' => 'Kurdî', 'value' => 72); // 'country' => 'TZ',
        $languages['la'] = array('name' => 'latine', 'value' => 73); // 'country' => 'TZ',
        $languages['lv'] = array('name' => 'latviešu valoda', 'country' => 'LV', 'value' => 74);
        $languages['lt'] = array('name' => 'lietuvių kalba', 'country' => 'LT', 'value' => 75);
        $languages['ro'] = array('name' => 'limba română', 'country' => 'RO', 'value' => 76);
        $languages['li'] = array('name' => 'Limburgs', 'country' => 'NL', 'value' => 77);
        $languages['ln'] = array('name' => 'Lingála', 'country' => 'CD', 'value' => 78);
        $languages['lg'] = array('name' => 'Luganda', 'country' => 'UG', 'value' => 79);
        $languages['lb'] = array('name' => 'Lëtzebuergesch', 'country' => 'UG', 'value' => 80);
        $languages['hu'] = array('name' => 'magyar', 'country' => 'UG', 'value' => 81);
        $languages['mt'] = array('name' => 'Malti', 'country' => 'MT', 'value' => 82);
        $languages['nl'] = array('name' => 'Nederlands', 'country' => 'NL', 'value' => 83);
        $languages['no'] = array('name' => 'Norsk', 'country' => 'NO', 'value' => 84);
        $languages['nb'] = array('name' => 'Norsk bokmål', 'country' => 'NO', 'value' => 85);
        $languages['nn'] = array('name' => 'Norsk nynorsk', 'country' => 'NO', 'value' => 86);
        $languages['uz'] = array('name' => 'O\'zbek', 'country' => 'UZ', 'value' => 87);
        $languages['oc'] = array('name' => 'occitan', 'country' => 'MC', 'value' => 88);
        $languages['ie'] = array('name' => 'Interlingue', 'value' => 89); // 'country' => 'UG',
        $languages['oc'] = array('name' => 'occitan', 'country' => 'MC', 'value' => 90);
        $languages['hz'] = array('name' => 'Otjiherero', 'country' => 'NA', 'value' => 91);
        $languages['ng'] = array('name' => 'Owambo', 'country' => 'MC', 'value' => 92);
        $languages['pt'] = array('name' => 'português', 'country' => 'AO', 'value' => 93);
        $languages['ty'] = array('name' => 'Reo Tahiti', 'country' => 'PF', 'value' => 94);
        $languages['rm'] = array('name' => 'rumantsch grischun', 'value' => 95); //
        $languages['qu'] = array('name' => 'Runa Simi', 'country' => 'PE', 'value' => 96);
        $languages['sc'] = array('name' => 'sardu', 'country' => 'IT', 'value' => 97);
        $languages['za'] = array('name' => 'Saɯ cueŋƅ', 'country' => 'CN', 'value' => 98);
        $languages['st'] = array('name' => 'Sesotho', 'country' => 'ZA', 'value' => 99);
        $languages['za'] = array('name' => 'Shangaan', 'country' => 'ZAF', 'value' => 100);
        return $languages;

        // add the rest, if not country, then don't show (country)
        /*

          'tn': 'Setswana',
          'ss': 'SiSwati',
          'sl': 'slovenski jezik',
          'sk': 'slovenÄ�ina',
          'so': 'Soomaaliga',
          'fi': 'suomi',
          'sv': 'Svenska',
          'mi': 'te reo MÄ�ori',
          'vi': 'Tiáº¿ng Viá»‡t',
          'lu': 'Tshiluba',
          've': 'Tshivená¸“a',
          'tw': 'Twi',
          'tk': 'TÃ¼rkmen',
          'tr': 'TÃ¼rkÃ§e',
          'ug': 'UyÆ£urqÉ™',
          'vo': 'VolapÃ¼k',
          'fj': 'vosa Vakaviti',
          'wa': 'walon',
          'tl': 'Wikang Tagalog',
          'wo': 'Wollof',
          'ts': 'Xitsonga',
          'yo': 'YorÃ¹bÃ¡',
          'sg': 'yÃ¢ngÃ¢ tÃ® sÃ¤ngÃ¶',
          'is': 'Ã�slenska',
          'cs': 'Ä�eÅ¡tina',
          'el': 'ÎµÎ»Î»Î·Î½Î¹ÎºÎ¬',
          'av': 'Ð°Ð²Ð°Ñ€ Ð¼Ð°Ñ†Ó€',
          'ab': 'Ð°Ò§Ñ�ÑƒÐ° Ð±Ñ‹Ð·ÑˆÓ™Ð°',
          'ba': 'Ð±Ð°ÑˆÒ¡Ð¾Ñ€Ñ‚ Ñ‚ÐµÐ»Ðµ',
          'be': 'Ð±ÐµÐ»Ð°Ñ€ÑƒÑ�ÐºÐ°Ñ� Ð¼Ð¾Ð²Ð°',
          'bg': 'Ð±ÑŠÐ»Ð³Ð°Ñ€Ñ�ÐºÐ¸ ÐµÐ·Ð¸Ðº',
          'os': 'Ð¸Ñ€Ð¾Ð½ Ã¦Ð²Ð·Ð°Ð³',
          'kv': 'ÐºÐ¾Ð¼Ð¸ ÐºÑ‹Ð²',
          'ky': 'ÐšÑ‹Ñ€Ð³Ñ‹Ð·Ñ‡Ð°',
          'mk': 'Ð¼Ð°ÐºÐµÐ´Ð¾Ð½Ñ�ÐºÐ¸ Ñ˜Ð°Ð·Ð¸Ðº',
          'mn': 'Ð¼Ð¾Ð½Ð³Ð¾Ð»',
          'ce': 'Ð½Ð¾Ñ…Ñ‡Ð¸Ð¹Ð½ Ð¼Ð¾Ñ‚Ñ‚',
          'ru': 'Ñ€ÑƒÑ�Ñ�ÐºÐ¸Ð¹ Ñ�Ð·Ñ‹Ðº',
          'sr': 'Ñ�Ñ€Ð¿Ñ�ÐºÐ¸ Ñ˜ÐµÐ·Ð¸Ðº',
          'tt': 'Ñ‚Ð°Ñ‚Ð°Ñ€ Ñ‚ÐµÐ»Ðµ',
          'tg': 'Ñ‚Ð¾Ò·Ð¸ÐºÓ£',
          'uk': 'ÑƒÐºÑ€Ð°Ñ—Ð½Ñ�ÑŒÐºÐ° Ð¼Ð¾Ð²Ð°',
          'cv': 'Ñ‡Ó‘Ð²Ð°Ñˆ Ñ‡Ó—Ð»Ñ…Ð¸',
          'cu': 'Ñ©Ð·Ñ‹ÐºÑŠ Ñ�Ð»Ð¾Ð²Ñ£Ð½ÑŒÑ�ÐºÑŠ',
          'kk': 'Ò›Ð°Ð·Ð°Ò› Ñ‚Ñ–Ð»Ñ–',
          'hy': 'Õ€Õ¡ÕµÕ¥Ö€Õ¥Õ¶',
          'yi': '×™×™Ö´×“×™×©',
          'he': '×¢×‘×¨×™×ª',
          'ur': 'Ø§Ø±Ø¯Ùˆ',
          'ar': 'Ø§Ù„Ø¹Ø±Ø¨ÙŠØ©',
          'fa': 'Ù�Ø§Ø±Ø³ÛŒ',
          'ps': 'Ù¾ÚšØªÙˆ',
          'ks': 'à¤•à¤¶à¥�à¤®à¥€à¤°à¥€',
          'ne': 'à¤¨à¥‡à¤ªà¤¾à¤²à¥€',
          'pi': 'à¤ªà¤¾à¤´à¤¿',
          'bh': 'à¤­à¥‹à¤œà¤ªà¥�à¤°à¥€',
          'mr': 'à¤®à¤°à¤¾à¤ à¥€',
          'sa': 'à¤¸à¤‚à¤¸à¥�à¤•à¥ƒà¤¤à¤®à¥�',
          'sd': 'à¤¸à¤¿à¤¨à¥�à¤§à¥€',
          'hi': 'à¤¹à¤¿à¤¨à¥�à¤¦à¥€',
          'as': 'à¦…à¦¸à¦®à§€à¦¯à¦¼à¦¾',
          'bn': 'à¦¬à¦¾à¦‚à¦²à¦¾',
          'pa': 'à¨ªà©°à¨œà¨¾à¨¬à©€',
          'gu': 'àª—à«�àªœàª°àª¾àª¤à«€',
          'or': 'à¬“à¬¡à¬¼à¬¿à¬†',
          'ta': 'à®¤à®®à®¿à®´à¯�',
          'te': 'à°¤à±†à°²à±�à°—à±�',
          'kn': 'à²•à²¨à³�à²¨à²¡',
          'ml': 'à´®à´²à´¯à´¾à´³à´‚',
          'si': 'à·ƒà·’à¶‚à·„à¶½',
          'th': 'à¹„à¸—à¸¢',
          'lo': 'àºžàº²àºªàº²àº¥àº²àº§',
          'bo': 'à½–à½¼à½‘à¼‹à½¡à½²à½‚',
          'dz': 'à½¢à¾«à½¼à½„à¼‹à½�',
          'my': 'á€—á€™á€¬á€…á€¬',
          'ka': 'áƒ¥áƒ�áƒ áƒ—áƒ£áƒšáƒ˜',
          'ti': 'á‰µáŒ�áˆ­áŠ›',
          'am': 'áŠ áˆ›áˆ­áŠ›',
          'iu': 'á�ƒá“„á’ƒá‘Žá‘�á‘¦',
          'oj': 'á�Šá“‚á”‘á“ˆá�¯á’§á�Žá“�',
          'cr': 'á“€á�¦á�ƒá”­á��á��á�£',
          'km': 'áž�áŸ’áž˜áŸ‚ážš',
          'zh': 'ä¸­æ–‡Â (ZhÅ�ngwÃ©n)',
          'ja': 'æ—¥æœ¬èªžÂ (ã�«ã�»ã‚“ã�”)',
          'ii': 'ê†ˆêŒ ê’¿ Nuosuhxop',
          'ko': 'í•œêµ­ì–´Â (éŸ“åœ‹èªž)'
          }; */
    }

    static function helpFollowSurvey() {
        return 'If empty, follows survey';
    }

    static function helpFollowType($type) {
        return 'If empty, follows `' . $type . '`, then survey';
    }

    static function helpFollowTypeOnly($type) {
        return 'If empty, follows `' . $type . '`';
    }

    static function helpProgressBarValue() {
        return '<i>If not set, it is automatically calculated</i>';
    }

    static function helpComparison() {
        return ' (' . SEPARATOR_COMPARISON . ' separated list of numbers and/or variable references such as *Q1)';
    }

    static function helpInvalidSet() {
        return ' (e.g. 1,2 ' . SEPARATOR_COMPARISON . ' 2,3-4)';
    }

    static function labelHouseholdMember() {
        return 'Household member';
    }

    static function labelProxy() {
        return 'Proxy';
    }

    /* keyboard bindings */

    static function keyboardBindingBack() {
        return 'Ctrl+B';
    }

    static function keyboardBindingNext() {
        return 'Ctrl+N';
    }

    static function keyboardBindingDK() {
        return 'Ctrl+D';
    }

    static function keyboardBindingRF() {
        return 'Ctrl+R';
    }

    static function keyboardBindingNA() {
        return 'Ctrl+A';
    }

    static function keyboardBindingUpdate() {
        return 'Ctrl+U';
    }

    static function keyboardBindingRemark() {
        return 'Ctrl+M';
    }

    static function keyboardBindingClose() {
        return 'Ctrl+C';
    }

    /* OTHER   */

    static function householdOrRespondentLabelCap($respondent) {
        if ($respondent instanceof Respondent) {
            return Language::respondentLabelCap();
        } else {
            return Language::householdLabelCap();
        }
    }

    static function householdLabelCap() {
        return 'Household';
    }

    static function respondentLabelCap() {
        return 'Respondent';
    }

    static function labelProxyCodeLabel($code) {
        return 'Proxy code for <b>' . $code . '</b>';
    }

    /* SMS nurse links */

    static function linkRespondents() {
        return 'Respondents';
    }

    static function messageNoRespondentsAssignedNurse() {
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) { //show household level
            return 'No households or respondents found. Please try again.';
        }
        return 'No respondents found. Please try again.';
    }

    static function labelDwelling() {
        return 'Dwelling';
    }

    static function labelVillage() {
        return 'Village';
    }

    static function labelTelephone() {
        return 'Telephone';
    }

    static function labelTestRespondents() {
        return array('',
            'jack',
            'jill'
        );
    }

    static function labelTestHousehold() {
        return 'respondent';
    }

    static function validationQuestions() {
        return array('BD035', 'BD003');
    }

    static function sessionExpiredMessage($timeout) {
        if ($timeout == 1) {
            return 'Your session will expire in ' . $timeout . ' minute.';
        } else if ($timeout < 1) {
            return 'Your session will expire in less than one minute.';
        }
        return 'Your session will expire in ' . $timeout . ' minutes.';
    }

    static function sessionExpiredKeepAliveButton() {
        return 'Continue survey';
    }

    static function sessionExpiredLogoutButton() {
        return 'Leave survey';
    }

    static function sessionExpiredTitle() {
        return 'Your session is about to expire!';
    }

    static function labHeader() {
        return 'documentation/Tracking Sheet/Header.html';
    }

    static function labStations() {
        return array(
            //index = array (station name, consent condition, trackingsheet html name, trackingsheet no visit html name, show in main nurse  

            '1' => array('name' => 'Station 1: Welcome', array(), 'location' => 'documentation/Tracking Sheet/Station1.html', 'nolocation' => '', 1),
            '2' => array('name' => 'Station 2: Blood and urine', array('OR', 2, 3), 'location' => 'documentation/Tracking Sheet/Station2.html', 'nolocation' => 'documentation/Tracking Sheet/Station2_novisit.html', 0),
            '3' => array('name' => 'Station 3: Refreshments', array(), 'location' => 'documentation/Tracking Sheet/Station3.html', 'nolocation' => '', 0),
            '4' => array('name' => 'Station 4: Ultrasound scan', array(), 'location' => 'documentation/Tracking Sheet/Station4.html', 'nolocation' => '', 0),
            '5a' => array('name' => 'Station 5a: Cognition', array('AND', 4, 5), 'location' => 'documentation/Tracking Sheet/Station5a.html', 'nolocation' => 'documentation/Tracking Sheet/Station5a_novisit.html', 0),
            '5b' => array('name' => 'Station 5b: Anthropometrics, Chair Stand, Vision, Spirometry', array(), 'location' => 'documentation/Tracking Sheet/Station5b.html', 'nolocation' => '', 1),
            '6' => array('name' => 'Station 6: Questionnaire', array(), 'location' => 'documentation/Tracking Sheet/Station6.html', 'nolocation' => '', 1),
            '7' => array('name' => 'Station 7: ECG', array(), 'location' => 'documentation/Tracking Sheet/Station7.html', 'nolocation' => '', 0),
            '8a' => array('name' => 'Station 8a: Ankle-brachial index (ABI)', array(), 'location' => 'documentation/Tracking Sheet/Station8a.html', 'nolocation' => '', 0),
            '8b' => array('name' => 'Station 8b: Peripheral Neuropathy', array(), 'location' => 'documentation/Tracking Sheet/Station8b.html', 'nolocation' => '', 0),
            '9' => array('name' => 'Station 9: Visit-Closeout', array(), 'location' => 'documentation/Tracking Sheet/Station9.html', 'nolocation' => '', 1)
        );
    }

    static function reportProblem() {
        return 'Report problem';
    }

    static function reportProblemConfirmation() {
        return 'Problem reported';
    }

    static function reportProblemCategory() {
        return 'Category';
    }

    static function reportProblemDescription() {
        return 'Description';
    }

    static function reportProblemCategories() {
        return array(
            1 => 'Question text',
            2 => 'Routing',
            3 => 'Display',
            4 => 'Translation',
            5 => 'Other'
        );
    }

    static function buttonReport() {
        return 'Report';
    }

    static function buttonCancel() {
        return 'Cancel';
    }

    static function consentTypes() {
        return array(1 => 'Participation in the Laboratory Study', 2 => 'Storage of Blood and/or Urine', 3 => 'Storage and Future Use of Blood Drops', 4 => 'Cognitive Assessment', 5 => 'Audio Recording of Cognitive Assessment', 6 => 'Awigen consent');
    }

    /*static function getAnswerTypes() {
        return array(ANSWER_TYPE_STRING => Language::labelAnswerTypeString(),
            ANSWER_TYPE_OPEN => Language::labelAnswerTypeOpen(),
            ANSWER_TYPE_ENUMERATED => Language::labelAnswerTypeEnumerated(),
            ANSWER_TYPE_DROPDOWN => Language::labelAnswerTypeDropDown(),
            ANSWER_TYPE_SETOFENUMERATED => Language::labelAnswerTypeSetOfEnumerated(),
            ANSWER_TYPE_MULTIDROPDOWN => Language::labelAnswerTypeMultiDropDown(),
            ANSWER_TYPE_INTEGER => Language::labelAnswerTypeInteger(),
            ANSWER_TYPE_DOUBLE => Language::labelAnswerTypeDouble(),
            ANSWER_TYPE_RANGE => Language::labelAnswerTypeRange(),
            ANSWER_TYPE_SLIDER => Language::labelAnswerTypeSlider(),
            ANSWER_TYPE_RANK => Language::labelAnswerTypeRank(),
            ANSWER_TYPE_DATE => Language::labelAnswerTypeDate(),
            ANSWER_TYPE_TIME => Language::labelAnswerTypeTime(),
            ANSWER_TYPE_DATETIME => Language::labelAnswerTypeDateTime(),
            ANSWER_TYPE_CALENDAR => Language::labelAnswerTypeCalendar(),
            ANSWER_TYPE_NONE => Language::labelAnswerTypeNone(),
            ANSWER_TYPE_SECTION => Language::labelAnswerTypeSection(),
            ANSWER_TYPE_CUSTOM => Language::labelAnswerTypeCustom());
    }*/

    static function messageNoData() {
        return 'No data found';
    }

    static function messageNoAggregateData() {
        return 'Data for a variable of this type cannot be shown in an aggregated manner.';
    }

    static function labelAggregateData() {
        return 'Aggregated data';
    }

    static function labelAggregateDetails() {
        return 'Details';
    }

    static function labelTableListNumber() {
        return 'Maximum number of items in table list';
    }

    static function helpFollowEmpty() {
        return 'If empty, show all';
    }

    static function labelHTMLEditor() {
        return 'Use HTML Editor';
    }

    static function labelWatchNoData() {
        return 'No data found';
    }

    static function labelWatchVariable() {
        return 'Variable name';
    }

    static function labelWatchValue() {
        return 'Value';
    }

    static function labelWatchLanguage() {
        return 'Language';
    }

    static function labelWatchMode() {
        return 'Interview mode';
    }

    static function labelWatchTime() {
        return 'Date/time';
    }

    static function labelNoProblemsReported() {
        return 'No reported problems found';
    }

    static function labelReportedBy() {
        return 'Reported by';
    }

    static function labelReportedOn() {
        return 'Reported on';
    }

    static function labelReportedCategory() {
        return 'Category';
    }

    static function labelReportedDescription() {
        return 'Description';
    }

    static function labelReportedPrimaryKey() {
        return 'Primary key';
    }

    static function labelReportedMode() {
        return 'Interview mode';
    }

    static function labelReportedLanguage() {
        return 'Language';
    }

    static function labelNurseHouseholdID() {
        return 'Household id';
    }

    static function labelNurseName() {
        return 'Name';
    }

    static function labelNurseDwellingID() {
        return 'Dwelling id';
    }

    static function labelNurseVillage() {
        return 'Village';
    }

    static function labelNurseScanBarcode() {
        return 'Scan barcode or search for respondents:';
    }

    static function labelNurseFieldDBS() {
        return 'Field DBS:';
    }

    static function labelNurseLabName() {
        return 'Agincourt Lab:';
    }

    static function labelNurseTestLab() {
        return 'Test lab:';
    }

    static function labelNurseSearchResults() {
        return 'Search results';
    }

    static function labelNurseButtonPrintTracking() {
        return 'Print tracking sheet';
    }

    static function labelNurseButtonScanLabBarcode() {
        return 'Scan lab barcode';
    }

    static function labelNurseButtonVisionTest() {
        return 'Start vision test';
    }

    static function labelNurseButtonAntropometrics() {
        return 'Enter Antropometrics';
    }

    static function labelNurseButtonStartLabSurvey() {
        return 'Start lab survey';
    }

    static function labelNurseErrorNoConsentBarcode() {
        return 'Respondent did not consent (yet) or no lab barcode assigned.';
    }

    static function labelNurseRespondentAssignedTo() {
        return 'Respondent assigned to: ';
    }

    static function labelNurseButtonAssignFieldNurse() {
        return 'Assign to field nurse';
    }

    static function labelNurseButtonUploadUSB() {
        return 'Upload USB data';
    }

    static function labelNurseButtonDataEnterSheet() {
        return 'Data enter sheet';
    }

    static function labelNurseDBSBlood() {
        return 'DBS/Blood';
    }

    static function labelNurseEnterCollectionDate() {
        return 'Please enter the collection date';
    }

    static function labelNurseButtonReceivedFieldDBS() {
        return 'Received field DBS';
    }

    static function labelNurseEnterReceivedDate() {
        return 'Please enter the date the result was received form the lab';
    }

    static function labelNurseButtonReceivedFieldDBSFromLab() {
        return 'Received field DBS from lab';
    }

    static function labelNurseBloodResultReceivedDate() {
        return 'Please enter the date the blood result was received form the lab';
    }

    static function labelNurseButtonReceivedBloodResultFromLab() {
        return 'Received blood result from lab';
    }

    static function labelNurseWarningReceivedBloodResultFromLab() {
        return 'Blood results received from lab';
    }

    static function labelNurseRespondentInfo() {
        return 'Respondent info';
    }

    static function labelNurseConsentInfo() {
        return 'Enter consent info';
    }

    static function labelNurseRespondentRefused() {
        return 'Respondent refused';
    }

    static function labelNurseRespondentRefusedParticipate() {
        return 'Respondent refuses to participate';
    }

    static function labelNurseRespondentRefusalReason() {
        return 'Refusal reason:';
    }

    static function labelNurseRespondentRefusalDate() {
        return 'Refusal date:';
    }

    static function labelNurseConsentFor() {
        return 'Consent forms signed for:';
    }

    static function labelNurseStaffConsent() {
        return 'Staff member conducting the consent:';
    }

    static function labelNurseOtherStaff() {
        return 'Other staff';
    }

    static function labelNurseAssignFiles() {
        return 'Assign/Files';
    }

    static function labelNursePrintForms() {
        return 'Print lab forms';
    }

    static function labelNurseButtonLabRequestForm() {
        return 'Laboratory request form';
    }

    static function labelNurseButtonSmallLabCodes() {
        return 'Print small lab barcodes';
    }

    static function labelNurseButtonReprintLabCodes() {
        return 'Re-print lab barcodes';
    }

    static function labelNurseButtonBloodStorage() {
        return 'Blood storage location';
    }

    static function labelNurseStorageLocation() {
        return 'Storage location';
    }

    static function labelNurseButtonStorageLocation() {
        return 'DBS storage location';
    }

    static function labelNurseShippingForms() {
        return 'Shipping forms';
    }

    static function labelNurseButtonShippingForms() {
        return 'Request/Shipping forms';
    }

    static function labelNurseButtonTube9() {
        return 'Send tube 9';
    }

    static function labelNurseButtonCD4Results() {
        return 'CD4 results';
    }

    static function labelNurseRespondent() {
        return 'Respondent';
    }

    static function labelNurseMoreInfo() {
        return 'More info: ';
    }

    static function labelNurseLabDBS() {
        return 'LAB DBS/Blood';
    }

    static function labelNurseFiles() {
        return 'Files';
    }

    static function labelNursePSU() {
        return 'PSU';
    }

    static function labelNurseSex() {
        return 'Sex';
    }

    static function labelNurseSexMale() {
        return 'Male';
    }

    static function labelNurseSexFemale() {
        return 'Female';
    }

    static function labelNurseAge() {
        return 'Age';
    }

    static function labelNurseAnon() {
        return 'Anon #';
    }

    static function labelNurseCD4Res() {
        return 'CD4 res';
    }

    static function labelNurseBarCode() {
        return 'Bar code';
    }

    static function labelNurseLabBarCode() {
        return 'Lab barcode';
    }

    static function labelNurseNoConsent() {
        return 'No consent given yet.';
    }

    static function labelNurseConsent() {
        return 'Consent given on ';
    }

    static function labelNurseConsentShort() {
        return 'Consent';
    }

    static function labelNurseStatus() {
        return 'Status';
    }

    static function labelNurseCollectedDate() {
        return 'Collected date';
    }

    static function labelNurseReceivedDate() {
        return 'Received date';
    }

    static function labelNurseShippedDate() {
        return 'Shipped date';
    }

    static function labelNurseResultsFromLab() {
        return 'Results from lab';
    }

    static function labelNurseResultsClinic() {
        return 'Results issued at clinic';
    }

    static function fieldDSBStatus() {
        return array(0 => 'No action yet', 1 => 'Received from field', 2 => 'Shipped to lab', 3 => 'Received from lab', 4 => 'Result given to Respondent');
    }

    static function labelNurseButtonChange() {
        return 'Change';
    }

    static function labelNurseDBSCardLocation() {
        return 'DBS card stored at:';
    }

    static function labelNursePosition() {
        return 'Position';
    }

    static function labelNurseViewBoxContent() {
        return 'View box content';
    }

    static function labelNurseBloodLocation() {
        return 'Blood stored at';
    }

    static function labelNurseBloodTestName() {
        return 'Name';
    }

    static function labelNurseBloodTestSize() {
        return 'Size';
    }

    static function labelNurseBloodTestPositionBox() {
        return 'Position in box';
    }

    static function labelNurseBloodTestFullBarCode() {
        return 'Full barcode';
    }

    static function labelNurseBloodTestAvailable() {
        return 'Available';
    }

    static function labelNurseBloodTestNotCollected() {
        return 'Not collected';
    }

    static function labelNurseBloodTestSentToLab() {
        return 'Sent to lab: ';
    }

    static function labelNurseBloodTestInFreezer() {
        return 'In freezer';
    }

    static function labelNurseBloodTestVialSelection() {
        return 'Select first 2 vials for each test';
    }

    static function labelNurseBloodTestMarkSelected() {
        return 'Mark selected as ';
    }

    static function labelNurseButtonShippedToLab() {
        return 'Shipped to the lab';
    }

    static function labelNurseOr() {
        return ' or ';
    }

    static function labelNurseButtonViewUpload() {
        return 'View/Upload files';
    }

    static function labelNurseButtonScanBarcode() {
        return 'Scan field barcode';
    }

    static function labelNurseButtonScanLabCode() {
        return 'Scan lab barcode';
    }

    static function labelNurseButtonUpdatePicture() {
        return 'Update picture';
    }

    static function labelNurseWarningNotEligible() {
        return 'Respondent is not eligible';
    }

    static function labelNurseLabBarCodeScan() {
        return 'Scan the lab barcode twice and then press "Save":';
    }

    static function labelNurseLabBarCodeScan1() {
        return ' Scan 1:';
    }

    static function labelNurseLabBarCodeScan2() {
        return ' Scan 2:';
    }

    static function labelNurseButtonSave() {
        return 'Save';
    }

    static function labelNurseFieldBarCodeScan() {
        return 'Scan or enter the barcode twice and then press "Save":';
    }

    static function labelNurseFieldBarCode() {
        return 'Field barcode:';
    }

    static function labelNurseTakePicture() {
        return 'Field barcode:';
    }

    static function labelNurseDrivers() {
        return array(1 => 'Peter', 2 => 'Peace', 3 => 'Ories', 4 => 'Solly', 5 => 'Sharron');
    }

    static function labelNurseDBSTToLab() {
        return 'DBS cards to ship to the lab:<br/>';
    }

    static function labelNurseBloodStorage() {
        return 'Blood storage';
    }

    static function labelNurseBloodStoredAt() {
        return 'Blood for this respondent is stored at:';
    }

    static function labelNurseBloodStoredAtStartNumber() {
        return 'Starting number in box:';
    }

    static function labelNurseBloodStoredAtBoxNumber() {
        return 'Box number::';
    }

    static function labelNurseBloodStoredAtRackNumber() {
        return 'Rack number:';
    }

    static function labelNurseBloodStoredAtShelveNumber() {
        return 'Shelve number:';
    }

    static function labelNurseBloodStoredAtFreezerNumber() {
        return 'Freezer number:';
    }

    static function labelNurseBloodStoredAtOrderNumber() {
        return 'Order number in  box:';
    }

    static function labelNurseDBSStoredAt() {
        return 'DBS for this respondent is stored at:';
    }

    static function labelNurseDBSStorage() {
        return 'DBS storage';
    }

    static function labelNurseLabRequestForm() {
        return 'Laboratory request form';
    }

    static function labelNurseLabRequestFormTitle() {
        return 'AWIGEn study Agincourt Laboratory Request Form (5 February 2015 Ver 1.0)';
    }

    static function labelNurseComments() {
        return 'COMMENTS:';
    }

    static function labelNurseCollectedBy() {
        return 'Specimen collected by:';
    }

    static function labelNurseReceivedBy() {
        return 'ReceivedBy:';
    }

    static function labelNurseLabDBSOverview() {
        return 'Laboratory dbs box overview';
    }

    static function labelNurseDBSBoxOverview() {
        return 'Dbs storage box overview';
    }

    static function labelNurseBoxAtLocation() {
        return 'Show box at this location:';
    }

    static function labelNurseButtonShow() {
        return 'Show';
    }

    static function labelNurseLabBloodOverview() {
        return 'Laboratory blood box overview';
    }

    static function labelNurseBloodBoxOverview() {
        return 'Blood storage box overview';
    }

    static function labelNurseCD4Results() {
        return 'CD4 test results';
    }

    static function labelNurseCD4ResultCode() {
        return 'Result code:';
    }

    static function labelNurseCD4ResultDate() {
        return 'Date:';
    }

    static function labelNurseAssignNurse() {
        return 'Assign field nurse';
    }

    static function labelNurseAssignNurseHomeVisit() {
        return 'Assign this respondent for a home visit to nurse: ';
    }

    static function labelNurseRespondentID() {
        return 'Respondent id';
    }

    static function labelDwellingID() {
        return 'Dwelling id';
    }

    static function labelNurseWarningNoCalls() {
        return 'No respondents to call.';
    }

    static function labelNurseFollowUp() {
        return 'Followup';
    }

    static function labelNurseFollowUpPhone1() {
        return 'Phone nr 1';
    }

    static function labelNurseFollowUpPhone2() {
        return 'Phone nr 2';
    }

    static function labelNurseFollowUpHouseholdHead() {
        return 'Household head';
    }

    static function labelNurseFollowUpSomeoneElse() {
        return 'Someone else';
    }

    static function labelNurseFollowUpDateTime() {
        return 'Date/time';
    }

    static function labelCommServerLocal() {
        return 'Local server';
    }

    static function labelCommServerOutside() {
        return 'Connect from outside';
    }

    static function labelInterviewerFilters() {
        return 'Filters';
    }

    static function labelInterviewerHouseholds() {
        return 'Households/Respondents:';
    }

    static function labelInterviewerFiltersHideCompleted() {
        return 'Hide completed';
    }

    static function labelInterviewerFiltersHideNone() {
        return 'None';
    }

    static function labelInterviewerFiltersHideCompletedAndFinal() {
        return 'Hide completed and Final contacts';
    }

    static function labelInterviewerFilterRegion() {
        return 'Region:';
    }

    static function labelInterviewerFilterRegionAll() {
        return 'All regions';
    }

    static function labelInterviewerFilterRegionOne() {
        return 'One region';
    }

    static function labelInterviewerWarningNoOtherSurveys() {
        return 'No other surveys available.';
    }

    static function nubisFooter() {
        return '&copy; <a href="https://github.com/nubissurveying/nubis">NubiS</a> -- <img src=images/' . SOFTWARE_LOGO . ' style="height:18px;">';
    }

    static function buttonSearch() {
        return 'Search';
    }

    static function labelInterviewerRespondent() {
        return 'Respondent';
    }

    static function labelInterviewerInternetCommunication() {
        return 'Internet communication';
    }

    static function labelInterviewerInternetUpdate() {
        return 'Update available on server.';
    }

    static function labelInterviewerInternetReceive() {
        return 'Receive data';
    }

    static function labelInterviewerInternetUpload() {
        return 'Upload data';
    }

    static function labelInterviewerNoInternet() {
        return 'No internet connection.';
    }

    static function labelInterviewerExport() {
        return 'Export';
    }

    static function labelInterviewerExportRetrieve() {
        return 'Retrieve data';
    }

    static function labelInterviewerImport() {
        return 'Import';
    }

    static function labelInterviewerImportData() {
        return 'Import data';
    }

    static function labelResearcherNoDocs() {
        return 'No documentation found.';
    }

    static function linkUnassigned() {
        return 'Unassigned sample';
    }

    static function labelResearcherOutputReports() {
        return 'Reports';
    }

    static function labelResearcherResponseOverview() {
        return 'Response overview';
    }

    static function labelResearcherButtonGo() {
        return 'Go';
    }

    static function labelResearcherWarningStata() {
        return 'Always run the STATA command "compress" before using data!';
    }

    static function labelResearcherDownloadSurveyData() {
        return 'Download survey data';
    }

    static function labelResearcherEncryption() {
        return 'Please enter an encryption key';
    }

    static function labelResearcherEncryptionKey() {
        return 'Encryption key';
    }

    static function labelResearcherDownloadData() {
        return 'Download data';
    }

    static function labelResearcherDownloadOtherData() {
        return 'Download other data';
    }

    static function labelResearcherDownloadHouseholds() {
        return 'Sample households (csv)';
    }

    static function labelResearcherDownloadRespondents() {
        return 'Sample respondents (csv)';
    }

    static function labelResearcherDownloadContacts() {
        return 'Contacts (csv)';
    }

    static function labelResearcherDownloadRemarks() {
        return 'Remarks (csv)';
    }

    static function labelResearcherResponseOverviewIndividual() {
        return 'Response overview individual survey (completed cases)';
    }

    static function labelResearcherResponseOverviewCover() {
        return 'Response overview coverscreen (completed cases)';
    }

    static function labelResearcherRespondents() {
        return 'Respondents';
    }

    static function labelResearcherHouseholds() {
        return 'Households';
    }

    static function labelResearcherDownloadCSV() {
        return 'Download (csv)';
    }

    static function labelResearcherDownloadGPS() {
        return 'Download GPS coordinates(csv)';
    }

    static function labelRespondentName() {
        return 'Name';
    }

    static function labelRespondentPSU() {
        return 'PSU';
    }

    static function labelRespondentHHMembers() {
        return 'HH members';
    }

    static function labelNurseMain() {
        return "Main Nurse";
    }

    static function labelNurseLab() {
        return "Lab Nurse";
    }

    static function labelNurseField() {
        return "Field Nurse";
    }

    static function labelNurseVision() {
        return "Vision Nurse";
    }

    static function messageUserNoMatch() {
        return 'Passwords don\'t match';
    }

    static function messageUserCorrectErrors() {
        return 'Correct errors';
    }
    
    static function messageUserDuplicateUsername() {
        return 'Username already in use';
    }

    static function messageUserDeleted() {
        return 'User deleted.';
    }

    static function messageUserNotDeleted() {
        return 'You can not delete the current user.';
    }

    static function labelSMSSample() {
        return 'Sample';
    }

    static function labelSMSCommunicationTable() {
        return 'Communication table';
    }

    static function labelSMSLaptopUpdate() {
        return 'Interviewer laptop update';
    }

    static function labelSMSLaptopUpdateMetaData() {
        return 'Update meta data (SQL)';
    }

    static function labelSMSLaptopUpdateScripts() {
        return 'Update scripts (PHP)';
    }

    static function labelSMSLaptopUpdateMetadataSurvey() {
        return ' Update all survey meta data';
    }

    static function labelSMSLaptopUpdateMetadataUsers() {
        return ' Update the users table';
    }

    static function labelSMSLaptopUpdateMetadataPSU() {
        return ' Update the psu table';
    }

    static function labelSMSLaptopUpdateMetadataCustom() {
        return ' Custom SQL code:';
    }

    static function labelSMSLaptopUpdateMetadataButton() {
        return 'Update interviewer laptops';
    }

    static function labelSMSLaptopScriptsMessage() {
        return 'Copy the scripts that need to be updated on the interviewer laptop to the pre-designated folder on the server.';
    }

    static function labelSMSFilterPSU() {
        return 'Filter on psu';
    }

    static function labelSMSFilterShow() {
        return 'Show';
    }

    static function labelSMSButtonAssign() {
        return 'Assign';
    }

    static function labelSMSWarningNoUnassignedHouseholds() {
        return 'No unassigned households';
    }

    static function labelSMSWarningNoUnassignedRespondents() {
        return 'No unassigned respondents';
    }

    static function labelSMSDownloadCSV() {
        return 'Download (csv)';
    }

    static function labelSMSDownloadGPS() {
        return 'Download GPS coordinates(csv)';
    }

    static function labelSMSInsertSample() {
        return 'Insert new sample';
    }

    static function labelSMSButtonInsertSample() {
        return 'Insert new sample';
    }

    static function labelSMSCommunicationTableHnid() {
        return 'Hnid';
    }

    static function labelSMSCommunicationTableTs() {
        return 'Date/Time';
    }

    static function labelSMSCommunicationTableDataType() {
        return 'Data type';
    }

    static function labelSMSCommunicationTableInsertTs() {
        return 'Inserted on';
    }

    static function labelSMSCommunicationTableReceived() {
        return 'Received';
    }

    static function labelSMSCommunicationTableReceivedTs() {
        return 'Received on';
    }

    static function labelSMSCommunicationTableDirection() {
        return 'Direction';
    }

    static function labelSMSCommunicationTableFileName() {
        return 'File name';
    }

    static function labelSMSCommunicationTableNoneFound() {
        return 'No communication found.';
    }

    static function labelResponseDataSubtitle() {
        return 'Started/completed';
    }

    static function labelResponseDataStarted() {
        return 'Started';
    }

    static function labelResponseDataCompleted() {
        return 'Completed';
    }

    static function labelResponseDataRespondents() {
        return ' respondents';
    }

    static function labelResponseDataContactsSub() {
        return 'appointment/answering machine/max rings/language barriers/refusal/disconnect';
    }

    static function labelResponseDataContacts() {
        return array('Appointment', 'Answering machine', 'Max rings', 'Refusal', 'Disconnected');
    }

    static function labelRespondentGPS() {
        return 'GPS';
    }

    static function labelRespondentStatus() {
        return 'Status';
    }

    static function labelRespondentContacts() {
        return '# of contacts';
    }

    static function labelRespondentWarningNoOneSelected() {
        return 'No one selected for the survey';
    }

    static function labelRespondentSex() {
        return 'Sex';
    }

    static function labelRespondentAge() {
        return 'Age';
    }

    static function labelRespondentIndividualSurvey() {
        return 'Individual survey';
    }

    static function labelRespondentNewAddress() {
        return 'New address for respondent';
    }

    static function labelRespondentAddressUnknown() {
        return 'Unknown';
    }

    static function labelRespondentFinalAssigned() {
        return 'A final code has been assigned to this household/respondent.';
    }

    static function labelRespondentFinancialR() {
        return 'Financial R';
    }

    static function labelRespondentSexMale() {
        return '(M)';
    }

    static function labelRespondentSexFemale() {
        return '(F)';
    }

    static function labelRespondentSexMaleFull() {
        return 'Male';
    }

    static function labelRespondentSexFemaleFull() {
        return 'Female';
    }

    static function labelRespondentYes() {
        return 'Yes';
    }

    static function labelRespondentNo() {
        return 'No';
    }

    static function labelRespondentRespondent() {
        return 'Respondent';
    }

    static function labelRespondentHousehold() {
        return 'Household';
    }

    static function labelRespondentContactsContact() {
        return 'Contact';
    }

    static function labelRespondentContactsInterviewer() {
        return 'Interviewer';
    }

    static function labelRespondentContactsDateTime() {
        return 'Date/Time';
    }

    static function labelRespondentContactsProxy() {
        return 'Proxy';
    }

    static function labelRespondentContactsRemark() {
        return 'Remark';
    }

    static function labelRespondentContactsAppointment() {
        return 'Appointment';
    }

    static function labelRespondentContactsAction() {
        return 'Action';
    }

    static function labelRespondentAddRemark() {
        return 'Add remark';
    }

    static function labelSuperVisorInterviewers() {
        return 'Interviewers';
    }

    static function labelSuperVisorInterviewersUrid() {
        return 'Urid';
    }

    static function labelSuperVisorInterviewersName() {
        return 'Name';
    }

    static function labelSuperVisorInterviewersUsername() {
        return 'Username';
    }

    static function labelSuperVisorInterviewersContacts() {
        return '# of contacts';
    }

    static function labelSuperVisorInterviewersCompleted() {
        return '# Completed (ind)';
    }

    static function labelSuperVisorInterviewersRefused() {
        return '# Refusal (ind)';
    }

    static function labelSuperVisorInterviewersLastUpload() {
        return 'Last upload';
    }

    static function labelSupervisorFilterPsu() {
        return 'Results filtered on psu';
    }

    static function labelSupervisorContactGraphs() {
        return 'Contact graphs';
    }

    static function labelSupervisorResponse() {
        return 'Response';
    }

    static function labelSupervisorSurveyInfo() {
        return 'Survey info';
    }

    static function labelSupervisorSetFilter() {
        return 'Set filter';
    }

    static function labelSupervisorGo() {
        return 'Go';
    }

    static function linkUnassignedSample() {
        return 'Unassigned sample';
    }

    static function labelSupervisorNoInterviewersAssigned() {
        return 'No interviewers assigned';
    }

    static function labelNurseEnterSearchTerm() {
        return 'Please enter a search term first.';
    }

    static function labelNurseDBSUpdated() {
        return 'Field DBS dates updated';
    }

    static function labelNurseConsentUpdated() {
        return 'Consent info updated';
    }

    static function labelNurseNoScanMatch() {
        return 'The two scans do not match. Please try again';
    }

    static function labelNurseIdenticalCodes() {
        return 'Lab barcode is identical to the fieldwork barcode. Please try again';
    }

    static function labelNurseBarCodeUpdated() {
        return 'Lab barcode updated';
    }

    static function labelNurseFieldBarCodeUpdated() {
        return 'Field barcode updated';
    }

    static function labelNurseBloodLocationUpdated() {
        return 'Blood location updated';
    }

    static function labelNurseDBSLocationUpdated() {
        return 'DBS card location updated';
    }

    static function labelNurseErrorFileDownload() {
        return 'Error while downloading file!';
    }

    static function labelNurseCardsSent() {
        return 'Cards set to sent to lab.';
    }

    static function labelNurseDBSCollectionDateAdded() {
        return 'DBS collection date added';
    }

    static function labelNurseDBSLabDateAdded() {
        return 'DBS received from lab date added';
    }

    static function labelNurseBloodLabDateAdded() {
        return 'Blood results received from lab date added';
    }

    static function labelNurseSurveyCompleted() {
        return 'Survey completed';
    }

    static function labelNurseRequestFormCompleted() {
        return 'Request form completed';
    }

    static function labelNurseSelectVial() {
        return 'Please selected a vial';
    }

    static function labelNurseMarkNotCollected() {
        return 'Marked as not collected';
    }

    static function labelNurseBloodSentLab() {
        return 'Blood sent to lab';
    }

    static function labelNurseCD4Added() {
        return 'CD4 test results added.';
    }

    static function labelNurseFieldNurseAssigned() {
        return 'Field nurse assigned.';
    }

    static function labelDataUploaded() {
        return 'Data uploaded to the server.';
    }

    static function labelDataNotUploaded() {
        return 'Could not send data to server.';
    }

    static function labelInterviewerBackFromSMS() {
        return 'Returned to SMS';
    }

    static function labelInterviewerSurveyCompleted() {
        return 'Survey completed';
    }

    static function labelInterviewerProxyCorrect() {
        return 'Proxy code is correct. Please start the interview.';
    }

    static function labelInterviewerProxyInCorrect() {
        return 'Proxy code is incorrect.';
    }

    static function labelInterviewerTestReset() {
        return 'Test cases reset.';
    }

    static function labelDataReceived() {
        return 'Data received from the server.';
    }

    static function labelDataNotReceived() {
        return 'Could not receive data from the server.';
    }

    static function labelSupervisorHHSurveyOutcome() {
        return 'Household survey outcome';
    }

    static function labelSupervisorHouseholds() {
        return 'Households';
    }

    static function labelSupervisorRespondentSurveyOutcome() {
        return 'Respondent survey outcome';
    }

    static function labelSupervisorRespondents() {
        return 'Respondents';
    }

    static function labelSupervisorSurveyInfoNames() {
        return array('Completed', 'Suspended', 'Non-Qualified');
    }

    static function labelSupervisorResponseGraphSubRespondents() {
        return 'Respondents contacted/completed/started';
    }

    static function labelSupervisorResponseGraphSubHouseholds() {
        return 'Households contacted/completed/started';
    }

    static function labelSupervisorResponseNames() {
        return array('Contacted', 'Started', 'Completed');
    }

    static function labelSupervisorContactSub() {
        return 'Contact outcome codes';
    }

    static function labelSupervisorFinalCodeAssigned() {
        return 'A final code has been assigned to this household/respondent.';
    }

    static function labelSupervisorAssignToInterviewer() {
        return 'Assign to different interviewer';
    }

    static function labelSupervisorAssignFinalStatus() {
        return 'Assign final contact status';
    }

    static function labelSupervisorCheckAnswers() {
        return 'Check answers';
    }

    static function labelSupervisorCheckRespondentAnswers() {
        return 'Check respondent answers';
    }

    static function labelSupervisorCheckRespondentQuestion() {
        return 'Question';
    }

    static function labelSupervisorCheckRespondentAnswer() {
        return 'Answer';
    }

    static function labelSupervisorCheckRespondentValidate() {
        return 'Validate interview';
    }

    static function labelSupervisorFilters() {
        return 'Filters';
    }

    static function labelSupervisorFilterRegion() {
        return 'Region:';
    }

    static function labelSupervisorFilterRegionAll() {
        return 'All regions';
    }

    static function labelSupervisorFilterRegionOne() {
        return 'One region';
    }

    static function labelSupervisorSurvey() {
        return 'Survey';
    }

    static function labelSupervisorUnassignedHouseholds() {
        return 'Assign households to interviewers';
    }

    static function labelSupervisorUnassignedRespondents() {
        return 'Assign respondents to interviewers';
    }

    static function labelSupervisorHouseholdReassigned() {
        return 'Household reassigned.';
    }

    static function labelSupervisorHouseholdNotReassigned() {
        return 'Household was already reassigned or reassigned to itself.';
    }

    static function labelSupervisorRespondentReassigned() {
        return 'Respondent reassigned.';
    }

    static function labelSupervisorRespondentNotReassigned() {
        return 'Respondent was already reassigned or reassigned to itself.';
    }

    static function labelSupervisorHouseholdAssigned() {
        return 'Households assigned.';
    }

    static function labelSupervisorRespondentAssigned() {
        return 'Respondents assigned.';
    }

    static function labelSupervisorHouseholdsNotAssigned() {
        return 'No households assigned. Please select one or more households and an interviewer to assign sample.';
    }

    static function labelSupervisorRespondentsNotAssigned() {
        return 'No respondents assigned. Please select one or more respondents and an interviewer to assign sample.';
    }

    static function labelNurseStartSurvey() {
        return 'Start the survey';
    }

    static function labelNurseShipToLab() {
        return 'Ship Field DBS cards to the lab';
    }

    static function labelNurseMarkShipped() {
        return 'Mark these DBS cards as "shipped"';
    }

    static function labelNurseToShip($cnt) {
        if ($cnt == 1) {
            return 'There is ' . $cnt . ' field DBS card to be send to the lab';
        } else {
            return 'There are ' . $cnt . ' field DBS cards to be send to the lab';
        }
    }

    static function labelNurseStartVision() {
        return 'Start the vision test';
    }

    static function labelNurseAntropometrics() {
        return 'Antropometrics';
    }

    static function buttonContinue() {
        return 'Continue';
    }

    static function labelUnsavedChanges() {
        return 'There are unsaved changes';
    }

    static function labelUnsavedChangesMessage() {
        return 'If you click \'Continue\' any changes you made will be lost. To stay on this page please click \'Cancel\'.';
    }

    static function labelTypeEditAssistanceLoginError() {
        return 'Incorrect login';
    }

    static function messageEnterPrimKeyDirectAccess() {
        return 'Something went wrong and it seems your survey session expired. Please start the survey again.';
    }

    static function labelSMSWarningNoSample() {
        return 'No sample information found';
    }

    static function labelTypeEditGeneralArrayInstance() {
        return 'Instance';
    }

    static function labelOutputDKBracket() {
        return "Don\'t know";
    }

    static function labelOutputRFBracket() {
        return "Refuse";
    }

    static function labelOutputNABracket() {
        return "Not applicable";
    }

    static function labelOutputEmptyBracket() {
        return "Skipped";
    }

    static function labelUnsavedChangesMessageConfirm() {
        return 'If you click \'Ok\' any changes you made will be lost. To stay on this page please click \'Cancel\'.';
    }

    static function optionsTimeoutYes() {
        return 'Yes';
    }

    static function optionsTimeoutNo() {
        return 'No';
    }

    static function labelTypeEditTimeout() {
        return 'Timeout enabled';
    }

    static function labelTypeEditTimeoutLength() {
        return 'Time in seconds before timeout';
    }

    static function labelTypeEditTimeoutLengthNone() {
        return '(if empty or zero, then ignored';
    }

    static function labelTypeEditTimeoutAliveButton() {
        return 'Keep alive button';
    }

    static function labelTypeEditTimeoutLogoutButton() {
        return 'End session button';
    }

    static function labelTypeEditTimeoutLogoutURL() {
        return 'Go to after user ends sessions';
    }

    static function labelTypeEditTimeoutRedirectURL() {
        return 'Go to after automatic end of session';
    }

    static function labelTypeEditTimeoutTitle() {
        return 'Dialog title';
    }

    static function messageSurveyClosed() {
        return "The survey is currently closed. Please try again later.";
    }

    static function editSurveyName() {
        return 'Name';
    }

    static function editSurveyDescription() {
        return 'Description';
    }

    static function editSurveyTitle() {
        return 'Title';
    }

    static function editSurveyDefault() {
        return 'Default survey';
    }

    static function optionsDefaultSurveyNo() {
        return 'No';
    }

    static function optionsDefaultSurveyYes() {
        return 'Yes';
    }

    static function defaultSurveyIndicator() {
        return ' (default survey)';
    }

    static function messageToolsFlooderDone($number) {
        if ($number > 1) {
            return 'Data for ' . $number . ' cases generated';
        } else {
            return 'Data for ' . $number . ' case generated';
        }
    }

    static function labelResearcherAggregates() {
        return 'Aggregate data';
    }

    static function labelResearcherContactGraphs() {
        return 'Contact graphs';
    }

    static function labelResearcherTimingsDistribution() {
        return 'Timings distribution';
    }

    static function labelResearcherTimingsOverTime() {
        return 'Timings over time';
    }

    static function labelResearcherTimingsPerScreen() {
        return 'Timings per screen per respondent';
    }

    static function labelResearcherPlatform() {
        return 'Platform information';
    }

    static function labelResearcherTimingsPer() {
        return 'Timings per screen';
    }

    static function headerReports() {
        return 'Reports';
    }

    static function headerReportsAggregates() {
        return 'Aggregate data';
    }

    static function headerReportsData() {
        return 'Data';
    }

    static function headerInstallTitle() {
        return 'NubiS Setup';
    }

    static function installWarning() {
        return 'Please ensure that the conf.php is writable by the web server before proceeding.';
    }

    static function installWarningDatabase() {
        return 'Could not connect to the database. Please verify your database settings.';
    }

    static function installWelcome() {
        return 'Welcome to the NubiS setup wizard. Please just use the following steps to install and configure NuBiS.';
    }

    static function installConfirmation() {
        return 'NubiS has been successfully installed. Please just click \'Next >>\' to proceed to the login screen to setup your first survey. Your initial login is sysadmin/sysadmin.';
    }

    static function installLabelWelcome() {
        return 'Welcome';
    }

    static function installLabelDatabase() {
        return 'Database';
    }

    static function installLabelLogging() {
        return 'Logging';
    }

    static function installLabelEncryption() {
        return 'Encryption';
    }

    static function installLabelSession() {
        return 'Session handling';
    }

    static function installLabelSample() {
        return 'Sample';
    }

    static function installLabelDatetime() {
        return 'Date/time';
    }

    static function installButtonNext() {
        return 'Next >>';
    }

    static function installButtonFinish() {
        return 'Finish';
    }

    static function installButtonBack() {
        return '<< Back';
    }

    static function installDatabaseWelcome() {
        return 'Please provide the database details in which you wish to install NubiS:';
    }

    static function installDatabaseWelcome2() {
        return 'At this point you can choose to fast complete the setup process by clicking \'Finish\' in which case NubiS will auto-configure itself with a standard configuration. Alternatively, you can click \'Next >>\' to configure NubiS\' various options.';
    }

    static function installLabelDatabaseServer() {
        return 'Server';
    }

    static function installLabelDatabaseName() {
        return 'Database name';
    }

    static function installLabelDatabaseUser() {
        return 'Username';
    }

    static function installLabelDatabasePassword() {
        return 'Password';
    }

    static function installLabelDatabaseSurvey() {
        return 'Table name prefix';
    }

    static function installDateTimeWelcome() {
        return 'Please select your date/time preferences:';
    }

    static function installLabelTimezone() {
        return 'Timezone';
    }

    static function installLabelTimeformat() {
        return 'US format';
    }

    static function installLabelTimeUseMinutes() {
        return 'Allow minutes usage';
    }

    static function installLabelTimeUseSeconds() {
        return 'Allow seconds usage';
    }
    
    static function installCommunicationYes() {
        return 'Yes';
    }

    static function installCommunicationNo() {
        return 'No';
    }

    static function installLabelYes() {
        return 'Yes';
    }

    static function installLabelNo() {
        return 'No';
    }

    static function installLabelSMS() {
        return 'SMS';
    }

    static function installLabelSurvey() {
        return 'Survey';
    }

    static function installEncryptionWelcome() {
        return 'NubiS uses a series of encryption keys to protect sensitive information. The ones below are auto-generated suggestions. If you wish to adjust one or more, just change them accordingly:';
    }

    static function installLabelEncryptionLoginCodes() {
        return 'Survey logins';
    }

    static function installLabelEncryptionDirect() {
        return 'Direct login';
    }

    static function installLabelEncryptionAdminLogin() {
        return 'Administrative logins';
    }

    static function installLabelEncryptionRespondent() {
        return 'Sample information';
    }

    static function installLabelEncryptionRemark() {
        return 'Remarks';
    }
    
    static function installLabelEncryptionCommunication() {
        return 'Communication';
    }

    static function installLabelEncryptionContactRemark() {
        return 'Contact remarks';
    }

    static function installLabelEncryptionContactName() {
        return 'Contact name';
    }

    static function installLabelEncryptionParameters() {
        return 'Submitted survey parameters';
    }

    static function installLabelEncryptionDirectLogin() {
        return 'Direct login';
    }

    static function installLabelEncryptionData() {
        return 'Main key';
    }

    static function installLabelEncryptionLab() {
        return 'Lab results';
    }

    static function installLabelEncryptionFilePicture() {
        return 'Files and pictures';
    }

    static function installLoggingWelcome() {
        return 'Please indicate which items you want NubiS to log (recommended is to log all):';
    }

    static function installLabelLoggingActions() {
        return 'Survey actions';
    }

    static function installLabelLoggingTimings() {
        return 'Survey times';
    }

    static function installLabelLoggingParadata() {
        return 'Survey paradata';
    }

    static function installLabelLoggingTabSwitch() {
        return 'Survey tab switches';
    }

    static function installLabelLoggingParadataMouseMovement() {
        return 'Mouse movement every # milliseconds';
    }

    static function installSessionWelcome() {
        return 'Please indicate if you wish to enable NubiS\' session timeout warning mechanism (applies to surveys only):';
    }

    static function installLabelSessionWarn() {
        return 'Warn for session timeout';
    }

    static function installLabelSessionDuration() {
        return 'Session duration (seconds)';
    }

    static function installLabelSessionLogout() {
        return 'After logout go to';
    }

    static function installLabelSessionRedirect() {
        return 'After redirect go to';
    }

    static function installLabelSessionPing() {
        return 'Refresh every # of milliseconds';
    }

    static function installSampleWelcome() {
        return 'Please specify the following for the sample you plan to survey:';
    }

    static function installLabelSampleType() {
        return 'Type';
    }

    static function installLabelSampleTracking() {
        return 'Tracking';
    }

    static function installLabelSampleInterviewAddress() {
        return 'Separate interview address';
    }

    static function installLabelHousehold() {
        return 'Households';
    }

    static function installLabelRespondent() {
        return 'Individual respondents';
    }

    static function installLabelSampleProxyContact() {
        return 'Allow proxy contact';
    }

    static function installLabelSampleProxyCodes() {
        return 'Use proxy codes';
    }
    
    static function installLabelAllowSampleCommunication() {
        return 'Allow communication';
    }

    static function installLabelSampleCommunication() {
        return 'Communication address for laptops';
    }

    static function installLabelSampleFileLocation() {
        return 'File location for script updates';
    }

    static function labelSMSLaptopAll() {
        return 'All interviewers';
    }

    static function labelSMSLaptopNoInterviewers() {
        return 'No interviewers found';
    }

    static function labelSMSLaptopSelectInterviewers() {
        return 'Please select one or more interviewers first.';
    }

    static function labelSMSLaptopSQLUpdateReady() {
        return 'Metadata update ready for interviewers.';
    }

    static function labelSMSLaptopSelectItems() {
        return 'Please select one or more items to update first.';
    }

    static function labelSMSLaptopScriptUpdateReady() {
        return 'Script update ready for interviewers.';
    }

    static function labelSMSLaptopScriptUpdateNoFiles($location) {
        return 'Please place one or more files in ' . $location . ' first.';
    }

    static function labelOutputParaData() {
        return 'Paradata';
    }

    static function headerOutputParadataData() {
        return 'Paradata';
    }

    static function timingsBracketsPerQuestion() {
        return array("Less than 30 seconds", "Less than 1 minute", "Less than 1.5 minutes", "Less than 2 minutes", "Less than 2.5 minutes", "Less than 3 minutes", "Less than 3.5 minutes", "Less than 4 minutes", "Less than 4.5 minutes");
    }

    static function labelAggregateTimings() {
        return 'Total time spent';
    }

    static function labelInterviews() {
        return 'interviews';
    }

    static function labelInterviewsUpper() {
        return 'Interviews';
    }

    static function labelShowRawData() {
        return 'Raw data';
    }

    static function labelShowTimingsData() {
        return 'Timings';
    }

    static function labelShowParadata() {
        return 'Paradata';
    }

    static function labelShowAuxData() {
        return 'Auxiliary data';
    }

    static function labelDataSingle() {
        return 'From a single survey';
    }

    static function labelDataMultiple() {
        return 'Combined from multiple surveys';
    }

    static function labelUserSurveyAllowed() {
        return 'Surveys';
    }

    static function labelUserGeneral() {
        return 'General';
    }

    static function labelUserAccess() {
        return 'Access';
    }

    static function labelSysadminMain() {
        return 'Allowed to manage users';
    }

    static function labelSysadminAdmin() {
        return 'Not allowed to manage users';
    }

    static function labelUserSurveyAccess() {
        return 'Survey';
    }

    static function labelUserAddUser() {
        return 'Add new user';
    }

    static function labelUserFilter() {
        return 'Filter on user type: ';
    }

    static function labelUserUserName() {
        return 'Username';
    }

    static function labelUserUserNameName() {
        return 'Name';
    }

    static function labelUserUserType() {
        return 'Type';
    }

    static function labelAll() {
        return 'All';
    }

    static function labelUserActive() {
        return 'Active';
    }

    static function labelUserUserSubType() {
        return 'Sub type';
    }

    static function labelUserSupervisor() {
        return 'Supervisor';
    }

    static function labelUserPassword() {
        return 'Password';
    }

    static function labelUserPassword2() {
        return 'Password (re-enter)';
    }

    static function labelSettingsLanguageAdd() {
        return 'If adding language, update users:';
    }

    static function labelSettingsModeAdd() {
        return 'If adding mode, update users:';
    }

    static function labelToolsExportHistoryYes() {
        return 'Yes';
    }

    static function labelToolsExportHistoryNo() {
        return 'No';
    }

    static function labelToolsExportHistory() {
        return 'Include history';
    }

    static function messageRemoveData() {
        return "Are you sure you want to remove all data for the selected survey(s)? Type `REMOVE` to continue.";
    }

    static function messageImportSurvey() {
        return "Are you sure you want to continue with the import? NOTE: If you are replacing the current survey(s), all survey components and data will be removed! Type `IMPORT` to continue.";
    }

    static function labelImportText() {
        return 'Export file content';
    }

    static function labelToolsImportFile() {
        return 'Import from';
    }

    static function labelToolsExportCreate() {
        return 'Include database scheme';
    }

    static function labelToolsExportCreateYes() {
        return 'Yes';
    }

    static function labelToolsExportCreateNo() {
        return 'No';
    }

    static function labelExportFile() {
        return 'Import from';
    }

    static function messageImportNoFile() {
        return 'no file selected.';
    }

    static function messageImportInvalidFile() {
        return 'invalid file selected.';
    }

    static function messageToolsImportNotOk($result) {
        return 'Import failed: ' . $result;
    }

    static function buttonBrowse() {
        return 'Browse';
    }

    static function labelTypeEditValidationAssignment() {
        return 'Assignment';
    }

    static function labelTypeEditChecks() {
        return 'Variable checks';
    }

    static function labelValidateAssignment() {
        return 'Validate value';
    }

    static function optionsValidateYes() {
        return 'Yes';
    }

    static function optionsValidateNo() {
        return 'No';
    }

    static function labelApplyChecks() {
        return 'Enable variable checks';
    }

    static function optionsApplyChecksYes() {
        return 'Yes';
    }

    static function optionsApplyChecksNo() {
        return 'No';
    }

    static function labelSurveysAddNew() {
        return 'add new survey';
    }

    static function labelSurveysAddNewCaps() {
        return 'Add new survey';
    }

    static function labelSectionsAddNew() {
        return 'add new section';
    }

    static function labelTypesAddNew() {
        return 'add new type';
    }

    static function labelGroupsAddNew() {
        return 'add new group';
    }

    static function labelVariablesAddNew() {
        return 'add new variable';
    }

    static function labelClean() {
        return 'Clean';
    }

    static function labelDirty() {
        return 'Dirty';
    }

    static function labelWatchClean() {
        return 'Clean/dirty';
    }

    static function linkCalculator() {
        return 'Calculator';
    }

    static function linkUpdate() {
        return 'Update data';
    }

    static function labelUpdateVariable() {
        return 'Variable';
    }

    static function labelUpdateCurrent() {
        return 'Current answer';
    }

    static function labelUpdateChangeTo() {
        return 'Change to';
    }

    static function labelUpdateChange() {
        return '';
    }

    static function labelUpdateQuestion() {
        return 'Question text';
    }

    static function labelTypeEditMobile() {
        return 'Auto-adjust on mobile';
    }

    static function labelTypeEditMobileLabels() {
        return 'Labels on mobile';
    }

    static function headerOutputStatisticsParadata() {
        return 'Paradata';
    }

    static function errorCodeLabels() {
        return array(
            "ER1" => "Empty warning",
            "ER2" => "Pattern not satisfied",
            "ER3" => "Empty warning",
            "ER4" => "Not left empty",
            "ER5" => "Not enough characters",
            "ER6" => "Too many characters",
            "ER7" => "Not enough or too many characters",
            "ER8" => "Minimum not met",
            "ER9" => "Maximum exceeded",
            "ER10" => "Out of range",
            "ER11" => "Out of range",
            "ER12" => "Invalid email",
            "ER13" => "Invalid URL",
            "ER14" => "Invalid date",
            "ER15" => "Invalid date",
            "ER16" => "Not a number",
            "ER17" => "Not an integer",
            "ER18" => "Not only digits",
            "ER19" => "Not equal to",
            "ER20" => "Equal to",
            "ER21" => "Not all alpha numerical",
            "ER22" => "Invalid zip code",
            "ER23" => "Not only letters",
            "ER24" => "Too many words",
            "ER25" => "Not enough words",
            "ER26" => "Not enough or too many words",
            "ER27" => "Not enough options selected",
            "ER28" => "Not exactly enough options selected",
            "ER29" => "Too many options selected",
            "ER30" => "Invalid combination selected",
            "ER31" => "Invalid combination selected",
            "ER32" => "Not enough options selected",
            "ER33" => "Not exactly enough options selected",
            "ER34" => "Too many options selected",
            "ER35" => "Invalid combination selected",
            "ER36" => "Invalid combination selected",
            "ER37" => "Too many inline fields answered",
            "ER38" => "Not all inline fields answered",
            "ER39" => "Not enough inline fields answered",
            "ER40" => "Too many inline fields answered",
            "ER41" => "Not exactly enough inline fields answered",
            "ER42" => "No inline fields answered",
            "ER43" => "Too many questions answered",
            "ER44" => "Not all questions answered",
            "ER45" => "Not enough questions answered",
            "ER46" => "Too many questions answered",
            "ER47" => "Not exactly enough questions answered",
            "ER48" => "Not all unique answers",
            "ER49" => "Not all same answers",
            "ER50" => "Not entered",
            "ER51" => "Not entered",
            "ER52" => "Not equal to",
            "ER53" => "Equal to",
            "ER54" => "Not greater or equal to",
            "ER55" => "Not greater than",
            "ER56" => "Not smaller or equal to",
            "ER57" => "Not smaller than",
            "ER58" => "Not equal to",
            "ER59" => "Equal to",
            "ER60" => "Not greater or equal to",
            "ER61" => "Not greater than",
            "ER62" => "Not smaller or equal to",
            "ER63" => "Not smaller than",
            "ER64" => "Not equal to",
            "ER65" => "Equal to",
            "ER66" => "Not equal to ignoring case",
            "ER67" => "Equal to ignoring case",
            "ER68" => "Not equal to date",
            "ER69" => "Equal to date",
            "ER70" => "Not greater or equal to date",
            "ER71" => "Not greater than date",
            "ER72" => "Not smaller or equal to date",
            "ER73" => "Not smaller than date",
            "ER74" => "Not equal to time",
            "ER75" => "Equal to time",
            "ER76" => "Not greater or equal to time",
            "ER77" => "Not greater than time",
            "ER78" => "Not smaller or equal to time",
            "ER79" => "Not smaller than time",
            "FO" => "Left screen",
            "FI" => "Returned to screen"
        );
    }

    static function labelErrors() {
        return 'Detected errors';
    }

    static function labelNumberOfTimes() {
        return 'Number of times';
    }

    static function labelResearcherParadata() {
        return 'Paradata';
    }

    static function headerReportsParadata() {
        return 'Paradata';
    }

    static function labelParadataDetails() {
        return 'Details';
    }

    static function optionsParadataRaw() {
        return 'Raw (CSV only)';
    }

    static function optionsParadataProcessed() {
        return 'Event counts';
    }
    
    static function optionsParadataError() {
        return 'Erroneous answers';
    }

    static function labelOutputDataTypeParadata() {
        return 'Type';
    }

    static function messageCheckerTypeNotExists($name) {
        return 'The type for variable `' . $name . '` does not exist.';
    }

    static function installLabelPerformance() {
        return 'Performance';
    }

    static function installPerformanceWelcome() {
        return 'NubiS can be tweaked for optimal performance on a particular platform by modifying the settings below. Please refer to the NubiS System Administrator manual for more information.';
    }

    static function installLabelPerformanceUseLocking() {
        return 'Use interview locking';
    }

    static function installLabelPerformanceUseTransactions() {
        return 'Use transactions for data storage';
    }

    static function installLabelPerformanceUsePreparedQueries() {
        return 'Use prepared statements for data storage';
    }

    static function installLabelPerformanceUseDataRecords() {
        return 'Use serialized data storage';
    }

    static function installLabelPerformanceUseState() {
        return 'Use data storage from state';
    }

    static function installLabelPerformanceUseMinify() {
        return 'Use script minification';
    }

    static function installLabelPerformanceUseSerialize() {
        return 'Use serialized survey components';
    }

    static function linkXiCompiler() {
        return 'Deploy to Xi';
    }

    static function headerToolsXiCompiler() {
        return 'Deploy to Xi';
    }

    static function buttonDeploy() {
        return 'Deploy';
    }

    static function labelToolsXiCompileCriteria() {
        return 'Settings';
    }

    static function labelToolsCompileModes() {
        return 'Interview mode';
    }

    static function linkRouting() {
        return 'Routing';
    }

    static function linkVariables() {
        return 'Variables';
    }
    
    static function labelTypeEditLayoutSpinner() {
        return 'Spinner';
    }
    
    static function labelTypeEditLayoutManual() {
        return 'Allow manual entry';
    }
    
    static function labelTypeEditGeneralSpinner() {
        return 'Show';
    }
    
    static function labelTypeEditGeneralSpinnerType() {
        return 'Type';
    }
    
    static function labelTypeEditGeneralSpinnerUp() {
        return 'Up icon';
    }
    
    static function labelTypeEditGeneralSpinnerDown() {
        return 'Down icon';
    }
    
    static function labelTypeEditGeneralSpinnerStep() {
        return 'Increment';
    }

    static function labelTypeEditGeneralGroupXiTemplate() {
        return 'Xi template';
    }

    static function labelTypeEditLayoutXi() {
        return 'Xi';
    }
        
    static function multiColumnQuestiontextYes() {
        return 'Yes';
    }
    
    static function multiColumnQuestiontextNo() {
        return 'No';
    }
    
    static function optionsAccessDevicePC() {
        return 'Desktop/laptop';
    }
    
    static function optionsTooltipYes() {
        return 'Yes';
    }
    
    static function optionsTooltipNo() {
        return 'No';
    }
    
    static function optionsAccessDeviceTablet() {
        return 'Tablet';
    }
    static function optionsAccessDevicePhone() {
        return 'Phone';
    }
    
    static function labelSettingsAccessDevice() {
        return 'Device(s)';
    }
    
    static function textAssignmentError($name, $value) {
        return 'Invalid value assigned to variable ' . $name . ": " . $value . "";
    }
}

?>