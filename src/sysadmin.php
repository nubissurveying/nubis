<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class SysAdmin {

    function __construct() {
        
    }

    function getPage() {

        if (getFromSessionParams('page') != null) {
            if (loadvar('updatesessionpage') != 2) {
                $_SESSION['LASTPAGE'] = getFromSessionParams('page');
            }
            /* called via jquery .post to load into div */ else {

                switch (getFromSessionParams('page')) {
                    case "sysadmin.history": return $this->showHistory();
                        break;
                    case "sysadmin.search": return $this->showSearch();
                        break;
                    case "sysadmin.search.hide": return $this->showSearchHide();
                        break;
                    case "sysadmin.survey.ordervariable": return $this->showOrderVariable();
                        break;
                    case "sysadmin.survey.ordersection": return $this->showOrderSection();
                        break;
                    case "sysadmin.survey.ordersurvey": return $this->showOrderSurvey();
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
                case 'sysadmin.sms': return $this->showSms();
                    break;
                case 'sysadmin.sms.update': return $this->showMetaDataUpdate();
                    break;

                case 'sysadmin.sms.update.sql': return $this->showMetaDataSQLUpdate();
                    break;
                case 'sysadmin.sms.update.sql.res': return $this->showMetaDataSQLUpdateRes();
                    break;
                case 'sysadmin.sms.update.scripts': return $this->showScriptUpdate();
                    break;
                case 'sysadmin.sms.update.scripts.res': return $this->showScriptUpdateRes();
                    break;

                case 'sysadmin.sms.communication': return $this->showCommunicationTable();
                    break;
                case 'sysadmin.sms.communication.remove': return $this->showCommunicationRemove();
                    break;
                case 'sysadmin.sms.surveyassignment': return $this->showSurveyAssignment();
                    break;
                case 'sysadmin.sms.surveyassignment.res': return $this->showSurveyAssignmentRes();
                    break;

                case 'sysadmin.sms.sample': return $this->showSample();
                    break;
                case 'sysadmin.sms.sample.import': return $this->showImportSample();
                    break;
                case 'sysadmin.sms.sample.import.res': return $this->showImportSampleRes();
                    break;
                case 'sysadmin.sms.sample.download': return $this->showSampleDownload();
                    break;
                case 'sysadmin.sms.sample.assign': return $this->showAssignSample();
                    break;

                case "sysadmin.surveys": return $this->showSurveys();
                    break;
                case "sysadmin.survey": return $this->showSurvey();
                    break;
                case "sysadmin.survey.addsurvey": return $this->showEditSurvey(true);
                    break;
                case "sysadmin.survey.editsurvey": return $this->showEditSurvey();
                    break;
                case "sysadmin.survey.editsurveyres": return $this->showEditSurveyRes();
                    break;
                case "sysadmin.survey.copysurvey": return $this->showCopySurvey();
                    break;
                case "sysadmin.survey.copysurveyres": return $this->showCopySurveyRes();
                    break;
                case "sysadmin.survey.removesurvey": return $this->showRemoveSurvey();
                    break;
                case "sysadmin.survey.removesurveyres": return $this->showRemoveSurveyRes();
                    break;
                case "sysadmin.survey.variable": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.addvariable": return $this->showAddVariable();
                    break;
                case "sysadmin.survey.editvariable": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariablegeneral": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariablevalidation": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariableassistance": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariablelayout": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariablecheck": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariablefill": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariableoutput": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariableinteractive": return $this->showEditVariable();
                    break;
                case "sysadmin.survey.editvariablegeneralres": return $this->showEditVariableGeneralRes();
                    break;
                case "sysadmin.survey.editvariablevalidationres": return $this->showEditVariableValidationRes();
                    break;
                case "sysadmin.survey.editvariablelayoutres": return $this->showEditvariableLayoutRes();
                    break;
                case "sysadmin.survey.editvariableassistanceres": return $this->showEditVariableAssistanceRes();
                    break;
                case "sysadmin.survey.editvariableaccessres": return $this->showEditVariableAccessRes();
                    break;
                case "sysadmin.survey.editvariablefillres": return $this->showEditVariableFillRes();
                    break;
                case "sysadmin.survey.editvariablecheckres": return $this->showEditVariableCheckRes();
                    break;
                case "sysadmin.survey.editvariableoutputres": return $this->showEditVariableOutputRes();
                    break;
                case "sysadmin.survey.editvariableinteractiveres": return $this->showEditVariableInteractiveRes();
                    break;
                case "sysadmin.survey.editvariablenavigationres": return $this->showEditVariableNavigationRes();
                    break;
                case "sysadmin.survey.copyvariable": return $this->showCopyVariable();
                    break;
                case "sysadmin.survey.copyvariableres": return $this->showCopyVariableRes();
                    break;
                case "sysadmin.survey.removevariable": return $this->showRemoveVariable();
                    break;
                case "sysadmin.survey.removevariableres": return $this->showRemoveVariableRes();
                    break;
                case "sysadmin.survey.movevariable": return $this->showMoveVariable();
                    break;
                case "sysadmin.survey.movevariableres": return $this->showMoveVariableRes();
                    break;
                case "sysadmin.survey.refactorvariable": return $this->showRefactorVariable();
                    break;
                case "sysadmin.survey.refactorvariableres": return $this->showRefactorVariableRes();
                    break;

                case "sysadmin.survey.group": return $this->showEditGroup();
                    break;
                case "sysadmin.survey.addgroup": return $this->showAddGroup();
                    break;
                case "sysadmin.survey.editgroup": return $this->showEditGroup();
                    break;
                case "sysadmin.survey.editgroupgeneral": return $this->showEditGroup();
                    break;
                case "sysadmin.survey.editgroupvalidation": return $this->showEditGroup();
                    break;
                case "sysadmin.survey.editgrouplayout": return $this->showEditGroup();
                    break;
                case "sysadmin.survey.editgroupinteractive": return $this->showEditGroup();
                    break;
                case "sysadmin.survey.editgroupgeneralres": return $this->showEditGroupGeneralRes();
                    break;
                case "sysadmin.survey.editgroupvalidationres": return $this->showEditGroupValidationRes();
                    break;
                case "sysadmin.survey.editgrouplayoutres": return $this->showEditGroupLayoutRes();
                    break;
                case "sysadmin.survey.editgroupassistanceres": return $this->showEditGroupAssistanceRes();
                    break;
                case "sysadmin.survey.editgroupaccessres": return $this->showEditGroupAccessRes();
                    break;
                case "sysadmin.survey.editgroupnavigationres": return $this->showEditGroupNavigationRes();
                    break;
                case "sysadmin.survey.editgroupinteractiveres": return $this->showEditGroupInteractiveRes();
                    break;
                case "sysadmin.survey.editgroupoutputres": return $this->showEditGroupOutputRes();
                    break;
                case "sysadmin.survey.copygroup": return $this->showCopyGroup();
                    break;
                case "sysadmin.survey.copygroupres": return $this->showCopyGroupRes();
                    break;
                case "sysadmin.survey.removegroup": return $this->showRemoveGroup();
                    break;
                case "sysadmin.survey.removegroupres": return $this->showRemoveGroupRes();
                    break;
                case "sysadmin.survey.movegroup": return $this->showMoveGroup();
                    break;
                case "sysadmin.survey.movegroupres": return $this->showMoveGroupRes();
                    break;
                case "sysadmin.survey.refactorgroup": return $this->showRefactorGroup();
                    break;
                case "sysadmin.survey.refactorgroupres": return $this->showRefactorGroupRes();
                    break;

                case "sysadmin.survey.editrouting": return $this->showEditRouting();
                    break;
                case "sysadmin.survey.clickrouting": return $this->showClickRouting();
                    break;
                case "sysadmin.survey.section": return $this->showSection();
                    break;
                case "sysadmin.survey.addsection": return $this->showEditSection(true);
                    break;
                case "sysadmin.survey.editsection": return $this->showEditSection();
                    break;
                case "sysadmin.survey.editsectionres": return $this->showEditSectionRes();
                    break;
                case "sysadmin.survey.copysection": return $this->showCopySection();
                    break;
                case "sysadmin.survey.copysectionres": return $this->showcopySectionRes();
                    break;
                case "sysadmin.survey.removesection": return $this->showRemoveSection();
                    break;
                case "sysadmin.survey.removesectionres": return $this->showRemoveSectionRes();
                    break;
                case "sysadmin.survey.movesection": return $this->showMoveSection();
                    break;
                case "sysadmin.survey.movesectionres": return $this->showMoveSectionRes();
                    break;
                case "sysadmin.survey.refactorsection": return $this->showRefactorSection();
                    break;
                case "sysadmin.survey.refactorsectionres": return $this->showRefactorSectionRes();
                    break;

                case "sysadmin.survey.type": return $this->showEditType();
                    break;
                case "sysadmin.survey.addtype": return $this->showAddType();
                    break;
                case "sysadmin.survey.edittype": return $this->showEditType();
                    break;
                case "sysadmin.survey.edittypegeneral": return $this->showEditType();
                    break;
                case "sysadmin.survey.edittypevalidation": return $this->showEditType();
                    break;
                case "sysadmin.survey.edittypelayout": return $this->showEditType();
                    break;
                case "sysadmin.survey.edittypeoutput": return $this->showEditType();
                    break;
                case "sysadmin.survey.edittypeinteractive": return $this->showEditType();
                    break;
                case "sysadmin.survey.edittypegeneralres": return $this->showEditTypeGeneralRes();
                    break;
                case "sysadmin.survey.edittypevalidationres": return $this->showEditTypeValidationRes();
                    break;
                case "sysadmin.survey.edittypelayoutres": return $this->showEditTypeLayoutRes();
                    break;
                case "sysadmin.survey.edittypeoutputres": return $this->showEditTypeOutputRes();
                    break;
                case "sysadmin.survey.edittypeassistanceres": return $this->showEditTypeAssistanceRes();
                    break;
                case "sysadmin.survey.edittypeaccessres": return $this->showEditTypeAccessRes();
                    break;
                case "sysadmin.survey.edittypeinteractiveres": return $this->showEditTypeInteractiveRes();
                    break;
                case "sysadmin.survey.edittypenavigationres": return $this->showEditTypeNavigationRes();
                    break;
                case "sysadmin.survey.copytype": return $this->showCopyType();
                    break;
                case "sysadmin.survey.copytyperes": return $this->showCopyTypeRes();
                    break;
                case "sysadmin.survey.removetype": return $this->showRemoveType();
                    break;
                case "sysadmin.survey.removetyperes": return $this->showRemoveTypeRes();
                    break;
                case "sysadmin.survey.movetype": return $this->showMoveType();
                    break;
                case "sysadmin.survey.movetyperes": return $this->showMoveTypeRes();
                    break;
                case "sysadmin.survey.refactortype": return $this->showRefactorType();
                    break;
                case "sysadmin.survey.refactortyperes": return $this->showRefactorTypeRes();
                    break;

                case "sysadmin.survey.settings": return $this->showSettings();
                    break;
                case "sysadmin.survey.editsettingsaccess": return $this->showEditSettingsAccess();
                    break;
                case "sysadmin.survey.editsettingsaccessres": return $this->showEditSettingsAccessRes();
                    break;
                case "sysadmin.survey.editsettingsassistance": return $this->showEditSettingsAssistance();
                    break;
                case "sysadmin.survey.editsettingsassistanceres": return $this->showEditSettingsAssistanceRes();
                    break;
                case "sysadmin.survey.editsettingsvalidation": return $this->showEditSettingsValidation();
                    break;
                case "sysadmin.survey.editsettingsvalidationres": return $this->showEditSettingsValidationRes();
                    break;
                case "sysadmin.survey.editsettingsinteractive": return $this->showEditSettingsInteractive();
                    break;
                case "sysadmin.survey.editsettingsinteractiveres": return $this->showEditSettingsInteractiveRes();
                    break;
                case "sysadmin.survey.editsettingsdata": return $this->showEditSettingsData();
                    break;
                case "sysadmin.survey.editsettingsdatares": return $this->showEditSettingsDataRes();
                    break;
                case "sysadmin.survey.editsettingsgeneral": return $this->showEditSettingsGeneral();
                    break;
                case "sysadmin.survey.editsettingsgeneralres": return $this->showEditSettingsGeneralRes();
                    break;
                case "sysadmin.survey.editsettingsmode": return $this->showEditSettingsMode();
                    break;
                case "sysadmin.survey.editsettingsmoderes": return $this->showEditSettingsModeRes();
                    break;
                case "sysadmin.survey.editsettingslanguage": return $this->showEditSettingsLanguage();
                    break;
                case "sysadmin.survey.editsettingslanguageres": return $this->showEditSettingsLanguageRes();
                    break;
                case "sysadmin.survey.editsettingslayout": return $this->showEditSettingsLayout();
                    break;
                case "sysadmin.survey.editsettingslayoutres": return $this->showEditSettingsLayoutRes();
                    break;
                case "sysadmin.survey.editsettingsnavigation": return $this->showEditSettingsNavigation();
                    break;
                case "sysadmin.survey.editsettingsnavigationres": return $this->showEditSettingsNavigationRes();
                    break;

                case "sysadmin.output": return $this->showOutput();
                    break;
                case "sysadmin.output.data": return $this->showOutputData();
                    break;
                case "sysadmin.output.rawdata": return $this->showOutputRawData();
                    break;
                case "sysadmin.output.addondata": return $this->showOutputAddonData();
                    break;
                case "sysadmin.output.addondatares": return $this->showOutputAddonDataRes();
                    break;
                case "sysadmin.output.remarkdata": return $this->showOutputRemarkData();
                    break;
                case "sysadmin.output.remarkdatares": return $this->showOutputRemarkDataRes();
                    break;
                case "sysadmin.output.timings": return $this->showOutputTimingsData();
                    break;
                case "sysadmin.output.timingsres": return $this->showOutputTimingsDataRes();
                    break;

                case "sysadmin.output.datasingle": return $this->showOutputDataSingle();
                    break;
                case "sysadmin.output.datasingleres": return $this->showOutputDataSingleRes();
                    break;
                case "sysadmin.output.datamultiple": return $this->showOutputDataMultiple();
                    break;
                case "sysadmin.output.datamultipleres": return $this->showOutputDataMultipleRes();
                    break;
                case "sysadmin.output.documentation": return $this->showOutputDocumentation();
                    break;
                case "sysadmin.output.documentation.routing": return $this->showOutputRouting();
                    break;
                case "sysadmin.output.documentation.routing.dash": return $this->showOutputRoutingDash();
                    break;
                case "sysadmin.output.documentation.dictionary": return $this->showOutputDictionary();
                    break;
                case "sysadmin.output.documentation.translation.fills": return $this->showOutputTranslationFills();
                    break;
                case "sysadmin.output.documentation.translation.assistance": return $this->showOutputTranslationAssistance();
                    break;
                case "sysadmin.output.documentation.translation": return $this->showOutputTranslation();
                    break;
                case "sysadmin.output.screendumps": return $this->showScreenDumps();
                    break;
                case "sysadmin.output.screendumpsres": return $this->showScreenDumpsRes();
                    break;
                case "sysadmin.output.paradata": return $this->showOutputParadata();
                    break;
                case "sysadmin.output.paradatares": return $this->showOutputParadataRes();
                    break;
                case "sysadmin.output.statistics": return $this->showOutputStatistics();
                    break;
                case "sysadmin.output.statistics.response": return $this->showOutputStaticsResponse();
                    break;
                case "sysadmin.output.statistics.aggregates": return $this->showOutputStatisticsAggregates();
                    break;
                case "sysadmin.output.statistics.aggregates.section": return $this->showOutputStatisticsAggregatesSection();
                    break;
                case "sysadmin.output.statistics.aggregates.variable": return $this->showOutputStatisticsAggregatesVariable();
                    break;
                case "sysadmin.output.statistics.paradata": return $this->showOutputStatisticsParadata();
                    break;
                case "sysadmin.output.statistics.paradata.section": return $this->showOutputStatisticsParadataSection();
                    break;
                case "sysadmin.output.statistics.paradata.variable": return $this->showOutputStatisticsParadataVariable();
                    break;
                case "sysadmin.output.statistics.contacts.graphs": return $this->showOutputStaticsContactsGraphs();
                    break;
                case "sysadmin.output.statistics.timings.distribution": return $this->showOutputStatisticsTimings();
                    break;
                case "sysadmin.output.statistics.timings.overtime": return $this->showOutputStatisticsTimingsOverTime();
                    break;
                case "sysadmin.output.statistics.timings.respondent": return $this->showOutputStatisticsTimingsRespondent();
                    break;
                case "sysadmin.output.statistics.timings.respondentres": return $this->showOutputStatisticsTimingsRespondentRes();
                    break;
                case "sysadmin.output.statistics.platform": return $this->showOutputStatisticsPlatform();
                    break;
                case "sysadmin.tools": return $this->showTools();
                    break;
                case "sysadmin.tools.clean": return $this->showClean();
                    break;
                case "sysadmin.tools.cleanres": return $this->showCleanRes();
                    break;
                case "sysadmin.tools.export": return $this->showExport();
                    break;
                case "sysadmin.tools.exportres": return $this->showExportRes();
                    break;
                case "sysadmin.tools.import": return $this->showImport();
                    break;
                case "sysadmin.tools.importres": return $this->showImportRes();
                    break;
                case "sysadmin.tools.batcheditor": return $this->showBatchEditor();
                    break;
                case "sysadmin.tools.batcheditorres": return $this->showBatchEditorRes();
                    break;
                case "sysadmin.tools.check": return $this->showCheck();
                    break;
                case "sysadmin.tools.checkres": return $this->showCheckRes();
                    break;
                case "sysadmin.tools.compile": return $this->showCompile();
                    break;
                case "sysadmin.tools.compileres": return $this->showCompileRes();
                    break;
                case "sysadmin.tools.xicompile": return $this->showXiCompile();
                    break;
                case "sysadmin.tools.xicompileres": return $this->showXiCompileRes();
                    break;
                case "sysadmin.tools.test": return $this->showTest();
                    break;
                case "sysadmin.tools.issues": return $this->showIssues();
                    break;
                case "sysadmin.tools.flood": return $this->showFlood();
                    break;
                case "sysadmin.tools.floodres": return $this->showFloodRes();
                    break;
                case "sysadmin.preferences": return $this->showPreferences();
                    break;
                case "sysadmin.preferences.res": return $this->showPreferencesRes();
                    break;
                case "sysadmin.users": return $this->showUsers();
                    break;
                case "sysadmin.users.adduser": unset($_SESSION['LASTURID']);
                    return $this->showEditUser();
                    break;
                case "sysadmin.users.edituser": return $this->showEditUser();
                    break;
                case "sysadmin.users.edituserres": return $this->showEditUserRes();
                    break;
                case "sysadmin.users.edituseraccessres": return $this->showEditUserAccessRes();
                    break;
                case "sysadmin.users.copyuser": return $this->showCopyUser();
                    break;
                case "sysadmin.users.removeuser": return $this->showRemoveUser();
                    break;
                default: return $this->mainPage();
            }
        } else {
            return $this->mainPage();
        }
    }

    /* general */

    function showHeader() {
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr = $displaySysAdmin->showSysAdminHeader(Language::messageSMSTitle());
        return $returnStr;
    }

    function showFooter() {
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr = $this->showBottomBar();
        $returnStr .= $displaySysAdmin->showFooter();
        return $returnStr;
    }

    function mainPage() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showMain();
    }

    function showInfo() {
        
    }

    /* search */

    function showSearch() {
        $displaySearch = new DisplaySearch();
        return $displaySearch->showSearchSysadmin(loadvarAllowHTML("search"));
    }

    function showSearchHide() {
        $displaySearch = new DisplaySearch();
        return $displaySearch->hideSearch();
    }

    /* history */

    function showHistory() {
        $displaySysAdmin = new DisplaySysAdmin();
        $id = loadvar('id');
        $objecttype = loadvar('objecttype');
        $sub = loadvar('sub');

        return $displaySysAdmin->showHistory($id, $sub);
    }

    /* sms */

    function showSms($message = '') {
        $displaySms = new DisplaySms();
        return $displaySms->showMain($message);
    }

    function showMetaDataUpdate($message = '') {
        $displaySms = new DisplaySms();
        return $displaySms->showMetaDataUpdate($message);
    }

    function showMetaDataSQLUpdate() {
        $displaySms = new DisplaySms();
        return $displaySms->showMetaDataSQLUpdate();
    }

    function showMetaDataSQLUpdateRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.sms.update';

        $displaySms = new DisplaySms();
        $tables = array();
        $updateSql = '';
        if (loadvar('custom') == '1') {
            $updateSql = loadvar('sqlcode');
        }
        if (loadvar('metadata') == '1') { //meta data update SQL
            $tables[] = 'settings';
        }
        if (loadvar('users') == '1') { //update users
            $tables[] = 'users';
        }
        if (loadvar('psu') == '1') {
            $tables[] = 'psus';
        }
        if (sizeof($tables) > 0 || $updateSql != '') {
            $communication = new Communication();
            if ($updateSql != '') {
                $updateSql .= "\n";
            }
            if (sizeof($tables) > 0) {
                $updateSql .= $communication->exportTables($tables);
            }
            $users = new Users();
            $users = $users->getUsers();
            $selected = loadvar("iwers");
            if (loadvar("iwers") == "") {
                $message = $displaySms->displayInfo(Language::labelSMSLaptopSelectInterviewers());
                return $displaysSms->showMetaDataSQLUpdate($message);
            } else {
                foreach ($users as $user) {
                    if ($user->getUserType() == USER_INTERVIEWER) { //interviewer: get update ready!
                        //this should be per laptop (id on macaddress??), not interviewer
                        if (inArray($user->getUrid(), $selected) || inArray(-1, $selected)) {
                            echo 'ok';
                            //$communication->addSQLToUser($updateSql, $user->getUrid());
                        }
                    }
                }
                $message = $displaySms->displaySuccess(Language::labelSMSLaptopSQLUpdateReady());
            }
        } else {
            $message = $displaySms->displayInfo(Language::labelSMSLaptopSelectItems());
            return $displayssms->showMetaDataSQLUpdate($message);
        }
        return $this->showMetaDataUpdate($message);
    }

    function showScriptUpdate() {
        $displaySms = new DisplaySms();
        return $displaySms->showScriptUpdate();
    }

    function showScriptUpdateRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.sms.update';

        $communication = new Communication();

        $files = array();
        $communication->getScriptFiles($files, '/pathtofileshere/');
        $displaySms = new DisplaySms();
        if (true || sizeof($files) > 0) {

            $selected = loadvar("iwers");
            if (!is_array($selected)) {
                $selected = array($selected);
            }
            if (loadvar("iwers") == "") {
                $message = $displaySms->displayInfo(Language::labelSMSLaptopSelectInterviewers());
                return $displaySms->showScriptUpdate($message);
            } else {

                $users = new Users();
                $users = $users->getUsers();
                foreach ($files as $key => $file) {
                    //$message .= $key . ':' . $file . '<br/>';
                    foreach ($users as $user) {
                        if ($user->getUserType() == USER_INTERVIEWER) { //interviewer: get update ready!
                            if (inArray($user->getUrid(), $selected) || inArray(-1, $selected)) {
                                //this should be per laptop (id on macaddress??), not interviewer
                                //$communication->addScriptToUser($key, $file, $user->getUrid());
                            }
                        }
                    }
                    //$communication->addScriptToUser($key, $file, 14);
                }

                $message = $displaySms->displaySuccess(Language::labelSMSLaptopScriptUpdateReady());
            }
        } else {
            $message = $displaySms->displayInfo(Language::labelSMSLaptopScriptUpdateNoFiles(dbConfig::defaultFileLocation()));
            return $displaySms->showScriptUpdate($message);
        }
        return $this->showMetaDataUpdate($message);
    }

    function showCommunicationTable($message = '') {
        $displaySms = new DisplaySms();
        return $displaySms->showCommunicationTable($message);
    }

    function showCommunicationRemove() {
        $communication = new Communication();
        $communication->removeRecord(getFromSessionParams('hnid'));
        $display = new Display();
        return $this->showCommunicationTable($display->displayInfo('Communication line removed.'));
    }

    function showSurveyAssignment($message = "") {
        $displaySms = new DisplaySms();
        return $displaySms->showSurveyAssignment($message);
    }

    function showSurveyAssignmentRes() {
        $surveys = new Surveys();
        $surveys->setNurseLabSurvey(loadvar("nurselab"));
        $surveys->setNurseFollowUpSurvey(loadvar("nursefollowup"));
        $surveys->setNurseVisionSurvey(loadvar("nursevision"));
        $surveys->setNurseAntropometricsSurvey(loadvar("nurseantropometrics"));
        $surveys->setNurseDataSheetSurvey(loadvar("nursedatasheet"));
        $displaySms = new DisplaySms();
        $message = $displaySms->displaySuccess(Language::messageSurveyAssignmentUpdated());
        return $displaySms->showSurveyAssignment($message);
    }

    function showSample($message = "") {
        $displaySms = new DisplaySms();
        return $displaySms->showSample($message);
    }

    function showImportSample($message = "") {
        $displaySms = new DisplaySms();
        return $displaySms->showImportSample($message);
    }

    function showImportSampleRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], ".res"));
        $list = array();
        $display = new Display();
        if (loadvar('file') != '') {
            $list = file_get_contents(loadvar('file'), FILE_USE_INCLUDE_PATH);
            if (trim($list) == "") {                
                return $this->showImportSample($display->displayInfo(Language::messageEmptySampleFileSelected()));
            }
            $list = explode("\r\n", $list);
        } else {
            $display = new Display();
            return $this->showImportSample($display->displayInfo(Language::messageNoSampleFileSelected()));
        }
        $datatype = loadvar("paneltype");
        $headquery = "";
        if ($datatype == PANEL_HOUSEHOLD) { //household
            $headquery .= 'insert into ' . Config::dbSurvey() . '_households ';
            $headquery .= '(primkey, puid, name, address1, address2, city, zip, state, longitude, latitude, telephone1, telephone2, status, test) values ';
        } else {
            $headquery = 'insert into ' . Config::dbSurvey() . '_respondents ';
            $headquery .= '(primkey, hhid, puid, logincode, firstname, lastname, address1, address2, city, zip, state, longitude, latitude, telephone1, telephone2, email, sex, age, status, selected, present, test) values ';
        }

        global $db;
        $cnt = 0;
        foreach ($list as $row) {
            $cnt++;
            
            // ignore headers
            if ($cnt == 1) {
                continue;
            }
            if ($row != '') {
                $item = explode(",", $row);

                // fix sample display
                if ($datatype == PANEL_HOUSEHOLD) { //household
                    $query = $headquery . "(";
                    $query .= '"' . prepareDatabaseString($item[0]) . '",'; // primkey
                    $query .= '"' . prepareDatabaseString($item[1]) . '", '; // puid
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[2]) . '","' . Config::smsPersonalInfoKey() . '"), '; // name
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[3]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // address1
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[4]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // address2
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[5]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // city
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[6]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // zip
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[7]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // state                   
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[8]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // longitude
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[9]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // latitude
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[10]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // telephone1
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[11]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // telephone2
                    $query .= '"' . prepareDatabaseString($item[12]) . '", '; // status
                    $query .= '"' . prepareDatabaseString($item[13]) . '"'; // test
                    $query .= ")";
                    $db->executeQuery($query);
                } else {
                    $query = $headquery . "(";
                    $query .= '"' . prepareDatabaseString($item[0]) . '",'; // primkey
                    $query .= '"' . prepareDatabaseString($item[1]) . '", '; // hhid
                    $query .= '"' . prepareDatabaseString($item[2]) . '", '; // puid
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[3]) . '","' . Config::loginCodeKey() . '"), '; // login code
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[4]) . '","' . Config::smsPersonalInfoKey() . '"), '; // first name
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[5]) . '","' . Config::smsPersonalInfoKey() . '"), '; // last name
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[6]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // address1
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[7]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // address2
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[8]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // city
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[9]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // zip
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[10]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // state 
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[11]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // longitude
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[12]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // latitude
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[13]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // telephone1
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[14]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // telephone2
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[15]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // email
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[16]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // sex
                    $query .= 'aes_encrypt("' . prepareDatabaseString($item[17]) . '", "' . Config::smsPersonalInfoKey() . '"), '; // age                                        
                    $query .= '"' . prepareDatabaseString($item[18]) . '", '; // status
                    $query .= '"' . prepareDatabaseString($item[19]) . '", '; // selected
                    $query .= '"' . prepareDatabaseString($item[20]) . '", '; // present
                    $query .= '"' . prepareDatabaseString($item[21]) . '"'; // test
                    $query .= ")";
                    $db->executeQuery($query);
                }
            }
        }
        return $this->showSample($display->displayInfo(Language::messageSampleFileImported()));
    }

    function showAssignSample() {
        $assignids = loadvar('assignid');
        $selurid = loadvar('selurid');
        $message = "";
        if (is_array($assignids) && sizeof($assignids) > 0 && $selurid > 0) {
            
            if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
                $message = Language::messageSMSAssignHouseholds();
                foreach ($assignids as $id) { //sysadmin mode: change on server 'only'
                    $household = new Household($id);
                    $household->setUrid($selurid);
                    $household->saveChanges();
                }
            }
            else {
                $message = Language::messageSMSAssignRespondents();
                foreach ($assignids as $id) { //sysadmin mode: change on server 'only'
                    $respondent = new Respondent($id);
                    $respondent->setUrid($selurid);
                    $respondent->saveChanges();
                }
            }
        } else {
            if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
                $message = Language::messageSMSAssignHouseholdsNone();
            }
            else {
                $message = Language::messageSMSAssignRespondentsNone();
            }
        }

        $display = new Display();
        return $this->showSample($display->displayInfo($message));
    }

    function showSampleDownload() {
        global $db;
        $puid = loadvar('puid', 0);
        
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
            $households = new Households();
            $query = $households->getUnassignedAsQuery($puid, true);
        } else {
            $respondents = new Respondents();
            $query = $respondents->getUnassignedAsQuery($puid, true);
        }
        $filename = 'unassigned_sample_' . $puid . '_' . date('YmdHis');
        $result = $db->selectQuery($query);
        createCSV($result, $filename);
    }

    /* survey */

    function showSurveys() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showSurveys();
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

        return $displaySysAdmin->showSurvey();
    }

    function showOrderSurvey() {

        $suid = loadvar('id');
        if ($suid != "") {
            $fromPosition = loadvar('fromPosition');
            $toPosition = loadvar('toPosition');
            $direction = loadvar('direction');

            if (is_array(loadvar('fromPosition'))) {
                $arr = loadvar('fromPosition');
                $fromPosition = $arr[0];
            } else {
                $fromPosition = loadvar('fromPosition');
            }

            /* check if never ordered before and all positions are 1 (old nubis) */
            global $db;
            $q = "select position, count(*) as cnt from " . Config::dbSurvey() . "_surveys group by position";
            $res = $db->selectQuery($q);
            if ($res) {
                if ($db->getNumberOfRows($res) == 1) {
                    $row = $db->getRow($res);
                    if ($row["cnt"] > 1) {
                        $q = "SET @x = 0; UPDATE " . Config::dbSurvey() . "_surveys SET position = (@x:=@x+1) ORDER BY name asc";
                        $db->executeQueries($q);
                    }
                }
            }

            $toPosition = loadvar('toPosition');
            $direction = loadvar('direction');
            $aPosition = ($direction === "back") ? $toPosition + 1 : $toPosition - 1;
            if ($aPosition < 0) {
                $aPosition = 0;
            }
            $db->executeQuery("update " . Config::dbSurvey() . "_surveys set position = 0 where position=" . prepareDatabaseString($toPosition));
            $db->executeQuery("update " . Config::dbSurvey() . "_surveys set position = " . prepareDatabaseString($toPosition) . " where suid=" . prepareDatabaseString($suid));

            // backward direction: up
            if ($direction === "back") {
                $db->executeQuery("update " . Config::dbSurvey() . "_surveys set position = position + 1 WHERE (" . prepareDatabaseString($toPosition) . " <= position AND position <= " . prepareDatabaseString($fromPosition) . ") and suid != " . prepareDatabaseString($suid) . " and position != 0 ORDER BY position DESC");
            }
            // Forward Direction: down
            else if ($direction === "forward") {
                $db->executeQuery("update " . Config::dbSurvey() . "_surveys set position = position - 1 WHERE (" . prepareDatabaseString($fromPosition) . " <= position AND position <= " . prepareDatabaseString($toPosition) . ") and suid != " . prepareDatabaseString($suid) . " and position != 0 ORDER BY position ASC");
            }
            $db->executeQuery("update " . Config::dbSurvey() . "_surveys set position = " . prepareDatabaseString($aPosition) . " where position=0");
        }
    }

    function showEditSurvey($new = false) {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('suid') != "") {
            $_SESSION['SUID'] = getFromSessionParams('suid');
        }
        $_SESSION['EDITSURVEY'] = 1;
        if ($new) {
            return $displaySysAdmin->showEditSurvey(getFromSessionParams('suid'));
        } else {
            return $displaySysAdmin->showEditSurvey($_SESSION['SUID']);
        }
    }

    function showEditSurveyRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $suid = getFromSessionParams('suid');
        $content = "";
        if ($suid != '') { //edit
            $survey = new Survey($suid);
            $content = $displaySysAdmin->displaySuccess(Language::messageSurveyChanged(loadvar('name')));
        } else { //add survey!
            if (loadvar('name') != "") {
                $surveys = new Surveys();
                $newsuid = $surveys->getMaximumSuid() + 1;
                $_SESSION['SURVEY_MODE'] = MODE_CASI;
                $_SESSION['SURVEY_LANGUAGE'] = 1;
                $_SESSION['SUID'] = $newsuid;
                $survey = new Survey();
                $survey->setSuid($newsuid);
                $survey->setObjectName($newsuid);
                $survey->addVersion(Language::labelVersionCurrentName(), Language::labelVersionCurrentDescription());
                $survey->setDefaultMode(MODE_CASI); // self
                $survey->setAllowedModes(MODE_CASI);
                $survey->setChangeMode(MODE_CHANGE_NOTALLOWED);
                $survey->setReentryMode(MODE_REENTRY_NO);
                $survey->setBackMode(MODE_BACK_NO);
                $survey->setDefaultLanguage(1); // english
                $survey->setAccessType(LOGIN_ANONYMOUS);
                $survey->setName(loadvar('name'));
                $survey->setTitle(loadvar(SETTING_TITLE));
                $survey->setDescription(loadvar(SETTING_DESCRIPTION));

                /* add base section */
                $section = new Section();
                $section->setSuid($newsuid);
                $section->setSeid(1);
                $section->setName(SECTION_BASE);
                $section->setPosition(1);
                $section->save();

                /* add base questions */
                $var = new VariableDescriptive();
                $var->setVsid(1);
                $var->setName(VARIABLE_PRIMKEY);
                $var->setAnswerType(ANSWER_TYPE_STRING);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('PRIMARY KEY');
                $var->setQuestion('primary key');
                $var->setMaximumLength(ANSWER_PRIMKEY_LENGTH);
                $var->setTyd(-1);
                $var->setPosition(1);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(2);
                $var->setName(VARIABLE_BEGIN);
                $var->setAnswerType(ANSWER_TYPE_DATETIME);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('TIMESTAMP START');
                $var->setQuestion('timestamp start');
                $var->setTyd(-1);
                $var->setPosition(2);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(3);
                $var->setName(VARIABLE_END);
                $var->setAnswerType(ANSWER_TYPE_DATETIME);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('TIMESTAMP END');
                $var->setQuestion('timestamp end');
                $var->setTyd(-1);
                $var->setPosition(3);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(4);
                $var->setName(VARIABLE_VERSION);
                $var->setAnswerType(ANSWER_TYPE_INTEGER);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('VERSION INFO');
                $var->setQuestion('version info');
                $var->setTyd(-1);
                $var->setPosition(4);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(5);
                $var->setName(VARIABLE_MODE);
                $var->setAnswerType(ANSWER_TYPE_ENUMERATED);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('INTERVIEW MODE');
                $var->setOptionsText("1 (CAPI) Face-to-face\r\n2 (CATI) Telephone\r\n3 (CASI) Self-administered\r\n4 (CADI) Data entry");
                $var->setQuestion('interview mode');
                $var->setTyd(-1);
                $var->setPosition(5);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(6);
                $var->setName(VARIABLE_LANGUAGE);
                $var->setAnswerType(ANSWER_TYPE_INTEGER);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('INTERVIEW LANGUAGE');
                $var->setQuestion('interview language');
                $var->setTyd(-1);
                $var->setPosition(6);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(7);
                $var->setName(VARIABLE_TEMPLATE);
                $var->setAnswerType(ANSWER_TYPE_INTEGER);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('SURVEY TEMPLATE');
                $var->setQuestion('survey template');
                $var->setTyd(-1);
                $var->setPosition(7);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(8);
                $var->setName(VARIABLE_PLATFORM);
                $var->setAnswerType(ANSWER_TYPE_STRING);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('PLATFORM AND BROWSER INFORMATION');
                $var->setQuestion(Language::labelPlatform());
                $var->setTyd(-1);
                $var->setPosition(8);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(9);
                $var->setName(VARIABLE_INTRODUCTION);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('INTRODUCTION SCREEN');
                $var->setQuestion(Language::messageWelcome());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_YES);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(9);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(10);
                $var->setName(VARIABLE_THANKS);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('THANKS SCREEN');
                $var->setQuestion(Language::messageSurveyEnd());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(10);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(11);
                $var->setName(VARIABLE_COMPLETED);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('ALREADY COMPLETED SCREEN');
                $var->setQuestion(Language::messageSurveyCompleted());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(11);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(12);
                $var->setName(VARIABLE_LOCKED);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('PROCESSING SCREEN');
                $var->setQuestion(Language::messageSurveyProcessing());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(12);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(13);
                $var->setName(VARIABLE_DIRECT);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('DIRECT ACCESS ONLY SCREEN');
                $var->setQuestion(Language::errorDirectLogin());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(13);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(14);
                $var->setName(VARIABLE_IN_PROGRESS);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('IN PROGRESS SCREEN');
                $var->setQuestion(Language::errorInProgress());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(14);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(15);
                $var->setName(VARIABLE_LOGIN);
                $var->setAnswerType(ANSWER_TYPE_STRING);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('LOGIN SCREEN');
                $var->setQuestion(Language::labelLoginCode());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_YES);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(15);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(16);
                $var->setName(VARIABLE_CLOSED);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('CLOSED SCREEN');
                $var->setQuestion(Language::messageSurveyClosed());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(16);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(17);
                $var->setName(VARIABLE_EXECUTION_MODE);
                $var->setAnswerType(ANSWER_TYPE_ENUMERATED);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('EXECUTION MODE');
                $var->setQuestion(Language::labelExecutionMode());
                $var->setOptionsText("0 (NORMAL) Normal mode\r\n1 (TEST) Test mode");
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(17);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(18);
                $var->setName(VARIABLE_ACCESS);
                $var->setAnswerType(ANSWER_TYPE_NONE);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('NO ACCESS SCREEN');
                $var->setQuestion(Language::LabelSurveyNoAccess());
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowRemarkButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(18);
                $var->save();

                $var = new VariableDescriptive();
                $var->setVsid(19);
                $var->setName(VARIABLE_DEVICE);
                $var->setAnswerType(ANSWER_TYPE_ENUMERATED);
                $var->setSeid(1);
                $var->setSuid($newsuid);
                $var->setDescription('DEVICE');
                $var->setQuestion(Language::labelDevice());
                $var->setOptionsText("1 (PC) Desktop/laptop\r\n2 (TABLET) Tablet\r\n3 (PHONE) Phone");
                $var->setShowBackButton(BUTTON_NO);
                $var->setShowNextButton(BUTTON_NO);
                $var->setShowRFButton(BUTTON_NO);
                $var->setShowDKButton(BUTTON_NO);
                $var->setShowNAButton(BUTTON_NO);
                $var->setShowUpdateButton(BUTTON_NO);
                $var->setShowProgressBar(PROGRESSBAR_NO);
                $var->setHidden(HIDDEN_YES);
                $var->setTyd(-1);
                $var->setPosition(19);
                $var->save();

                /* update current user for access */
                $surv = new Survey($newsuid);
                $user = new User($_SESSION['URID']);
                $mods = explode("~", $surv->getAllowedModes());
                foreach ($mods as $m) {
                    $user->setLanguages($newsuid, $m, $surv->getAllowedLanguages($m));
                }
                $user->saveChanges();
                $content = $displaySysAdmin->displaySuccess(Language::messageSurveyAdded(loadvar('name')));
            }
        }
//ADD ALL SORTS OF CHECKS!!
        if ($suid != '' || loadvar('name') != "") {
            $survey->setName(loadvar('name'));
            $survey->setDescription(loadvar(SETTING_DESCRIPTION));
            $survey->setTitle(loadvar(SETTING_TITLE));
            $survey->setDefaultSurvey(loadvar(SETTING_DEFAULT_SURVEY));
            $survey->save();

            // default, then update setting and set all others to no
            $surveys = new Surveys();
            $surveys = $surveys->getSurveys();
            if (loadvar(SETTING_DEFAULT_SURVEY) == DEFAULT_SURVEY_YES) {
                if (sizeof($surveys) == 1) {
                    $survey->setDefaultSurvey(DEFAULT_SURVEY_YES);
                } else {
                    foreach ($surveys as $s) {
                        if ($s->getSuid() != $survey->getSuid()) {
                            $s->setDefaultSurvey(DEFAULT_SURVEY_NO);
                        } else {
                            $s->setDefaultSurvey(DEFAULT_SURVEY_YES);
                        }
                    }
                }
            } else {
                // if only one survey, make sure we set this one to default
                if (sizeof($surveys) == 1) {
                    $survey->setDefaultSurvey(DEFAULT_SURVEY_YES);
                }
            }
        }

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($suid != '') {
            return $displaySysAdmin->showEditSurvey($_SESSION['SUID'], $content);
        } else {
            return $displaySysAdmin->showSurveys($content);
        }
    }

    function showCopySurvey() {
        $_SESSION['LASTPAGE'] = 'sysadmin.survey';
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('suid') != "") {
            $_SESSION['SUID'] = getFromSessionParams('suid');
        }
        $suid = getFromSessionParams('suid');
        if ($suid != '') {
            $survey = new Survey($_SESSION['SUID']);
            $survey->copy();
            $_SESSION['SUID'] = $survey->getSuid();
            $user = new User($_SESSION['URID']);
            $modes = explode("~", $survey->getAllowedModes());
            foreach ($modes as $m) {
                $user->addMode($_SESSION['SUID'], $m, $survey->getAllowedLanguages(m));
            }
            $user->saveChanges();
            $displaySysAdmin = new DisplaySysAdmin();
            $content = $displaySysAdmin->displaySuccess(Language::messageSurveyCopied($survey->getName()));
            return $displaySysAdmin->showSurvey($content);
        } else {
            $content = $displaySysAdmin->displayError(Language::messageSurveyNotCopied($survey->getName()));
            return $displaySysAdmin->showSurveys($content);
        }
    }

    function showRemoveSurvey() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('suid') != "") {
            $_SESSION['SUID'] = getFromSessionParams('suid');
        }
        return $displaySysAdmin->showRemoveSurvey($_SESSION['SUID']);
    }

    function showRemoveSurveyRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.surveys';

        $displaySysAdmin = new DisplaySysAdmin();
        $suid = getFromSessionParams('suid');
        if ($suid != '') { //remove
            $survey = new Survey($suid);

            /* remove sections */
            $sections = $survey->getSections();
            foreach ($sections as $section) {
                $section->remove();

                /* remove variables */
                $variables = $survey->getVariableDescriptives($section->getSeid());
                foreach ($variables as $variable) {
                    $variable->remove();
                }
            }

            /* remove types */
            $types = $survey->getTypes();
            foreach ($types as $type) {
                $type->remove();
            }

            /* remove types */
            $groups = $survey->getGroups();
            foreach ($groups as $group) {
                $group->remove();
            }

            /* remove versions */
            $versions = $survey->getVersions();
            foreach ($versions as $version) {
                $version->remove();
            }

            /* remove survey */
            $survey->remove();

            /* update users */
            $users = new Users();
            $users = $users->getUsers();
            foreach ($users as $u) {
                $u->removeSurvey($suid);
                $u->saveChanges();
            }

            /* return result */
            return $displaySysAdmin->showSurveys($displaySysAdmin->displaySuccess(Language::messageSurveyRemoved($survey->getName())));
        } else {
            return $displaySysAdmin->showSurveys();
        }
    }

    /* routing */

    function showEditRouting() {

        $_SESSION['LASTPAGE'] = 'sysadmin.survey.section';

        //SAVE THE NEW ROUTING 
        $survey = new Survey($_SESSION['SUID']);
        $section = $survey->getSection($_SESSION['SEID']);
        $content = "";
        $displaySysAdmin = new DisplaySysAdmin();
        if (isset($_POST["routing"])) {
            $section->storeRouting(loadvarAllowHTML('routing'));

            /* compile */
            $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
            $messages = $compiler->generateEngine($_SESSION['SEID']);

            if (sizeof($messages) == 0) {
                $compiler->generateProgressBar($_SESSION['SEID']);
                $content = $displaySysAdmin->displaySuccess(Language::messageRoutingOk());
            } else {
                // 
                $m = '<a data-keyboard="false" data-toggle="modal" data-target="#errorsModal">Show errors</a>';
                $content = $displaySysAdmin->displayError(Language::messageRoutingNeedsFix() . " " . $m);
                $text = "";
                $user = new User($_SESSION['URID']);
                $codemir = $user->hasRoutingAutoIndentation();
                foreach ($messages as $rgid => $mm) {
                    if (is_array($mm)) {
                        foreach ($mm as $s) {
                            if (trim($s) != "") {
                                if ($codemir) {
                                    $text .= $displaySysAdmin->displayError("<a href='#' onclick='jumpToLine(" . $rgid . ");'>" . Language::errorRoutingLine() . " " . $rgid . "</a>: " . $s);
                                } else {
                                    $text .= $displaySysAdmin->displayError(Language::errorRoutingLine() . " " . $rgid . ": " . $s);
                                }
                            }
                        }
                    }
                }
                $content .= $displaySysAdmin->displayRoutingErrorModal($section, $text);
                //$content .= implode('<br/>', $messages);
                //$content .= '</div>';
            }
        }
        return $this->showSection($content);
    }

    function showClickRouting() {
        $action = loadvar("action");
        $survey = new Survey($_SESSION['SUID']);
        $var = $survey->getVariableDescriptiveByName($action);
        if ($var->getVsid() != "") {
            $_SESSION['LASTPAGE'] = 'sysadmin.survey.variable';
            return $this->showEditVariable($var->getVsid());
        }
        $section = $survey->getSectionByName($action);
        if ($section->getSeid() != "") {
            $_SESSION['LASTPAGE'] = 'sysadmin.survey.section';
            return $this->showSection("", $section->getSeid());
        }
        $group = $survey->getGroupByName($action);
        if ($group->getGid() != "") {
            $_SESSION['LASTPAGE'] = 'sysadmin.survey.group';
            return $this->showEditGroup($group->getGid());
        }

        /* nothing to click through */
        $displaySysAdmin = new DisplaySysAdmin();
        $content = $displaySysAdmin->displayWarning(Language::messageRoutingClickError($action));
        return $this->showSection($content);
    }

    /* section */

    function showSection($message = '', $seid = '') {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('seid') != '') {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        } else if ($seid != '') {
            $_SESSION['SEID'] = $seid;
        }
        if (loadvar("vrfiltermode_section") != '') {
            $_SESSION['VRFILTERMODE_SECTION'] = loadvar("vrfiltermode_section");
        }
        return $displaySysAdmin->showSection($_SESSION['SEID'], $message);
    }

    function showAddSection($content = "") {
        $displaySysAdmin = new DisplaySysAdmin();
        unset($_SESSION['SEID']);
        unset($_SESSION['VRFILTERMODE_SECTION']);
        return $displaySysAdmin->showEditSection("", $content);
    }

    function showOrderSection() {
        $seid = loadvar('id');
        if ($seid != "") {
            $fromPosition = loadvar('fromPosition');
            $toPosition = loadvar('toPosition');
            $direction = loadvar('direction');
            if (is_array(loadvar('fromPosition'))) {
                $arr = loadvar('fromPosition');
                $fromPosition = $arr[0];
            } else {
                $fromPosition = loadvar('fromPosition');
            }

            /* check if never ordered before and all positions are 1 (old nubis) */
            global $db;
            $q = "select position, count(*) as cnt from " . Config::dbSurvey() . "_sections where suid=" . $_SESSION['SUID'] . " group by position";
            $res = $db->selectQuery($q);
            if ($res) {
                if ($db->getNumberOfRows($res) == 1) {
                    $row = $db->getRow($res);
                    if ($row["cnt"] > 1) {
                        $q = "SET @x = 0; UPDATE " . Config::dbSurvey() . "_sections SET position = (@x:=@x+1) where suid=" . $_SESSION['SUID'] . " ORDER BY name asc";
                        $db->executeQueries($q);
                    }
                }
            }

            $toPosition = loadvar('toPosition');
            $direction = loadvar('direction');
            $aPosition = ($direction === "back") ? $toPosition + 1 : $toPosition - 1;
            $db->executeQuery("update " . Config::dbSurvey() . "_sections set position = 0 where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and position=" . prepareDatabaseString($toPosition));
            $db->executeQuery("update " . Config::dbSurvey() . "_sections set position = " . prepareDatabaseString($toPosition) . " where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and seid=" . prepareDatabaseString($seid));

            // backward direction: up
            if ($direction === "back") {
                $db->executeQuery("update " . Config::dbSurvey() . "_sections set position = position + 1 WHERE (" . prepareDatabaseString($toPosition) . " <= position AND position <= " . prepareDatabaseString($fromPosition) . ") and suid=" . prepareDatabaseString($_SESSION['SUID']) . " and seid !=  " . prepareDatabaseString($seid) . " and position != 0 ORDER BY position DESC");
            }
            // Forward Direction: down
            else if ($direction === "forward") {
                $db->executeQuery("update " . Config::dbSurvey() . "_sections set position = position - 1 WHERE ("  .prepareDatabaseString($fromPosition) . " <= position AND position <= " . prepareDatabaseString($toPosition) . ") and suid=" . prepareDatabaseString($_SESSION['SUID']) . " and seid != " . prepareDatabaseString($seid) . " and position != 0 ORDER BY position ASC");
            }
            $db->executeQuery("update " . Config::dbSurvey() . "_sections set position = " . prepareDatabaseString($aPosition) . " where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and position=0");
        }
    }

    function showEditSection($new = false) {
        if ($new) {
            unset($_SESSION['SEID']);
            unset($_SESSION['VRFILTERMODE_SECTION']);
        }
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        return $displaySysAdmin->showEditSection(getFromSessionParams('seid'));
    }

    function showEditSectionRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $seid = getFromSessionParams('seid');
        $content = "";
        if ($seid != '') { //edit
            $section = $survey->getSection($seid);
            $_SESSION['SEID'] = $seid;
            $content = $displaySysAdmin->displaySuccess(Language::messageSectionChanged(loadvar(SETTING_NAME)));
        } else { //add section!
            if (loadvar(SETTING_NAME) != "") {
                $section = new Section();
                $section->setSuid($_SESSION['SUID']);
                $content = $displaySysAdmin->displaySuccess(Language::messageSectionAdded(loadvar(SETTING_NAME)));
            }
        }

        $checker = new Checker($_SESSION['SUID']);
        if ($seid == '') {
            $checks = $checker->checkName(loadvar(SETTING_NAME));
            if (sizeof($checks) > 0) {
                $content = implode("<br/>", $checks);
                return $this->showAddSection($content);
            }
        }

//ADD ALL SORTS OF CHECKS!!
        if ($seid != '' || loadvar(SETTING_NAME) != "") {
            $section->setName(trim(loadvar(SETTING_NAME)));
            $section->setDescription(loadvar(SETTING_DESCRIPTION));
            $section->setHidden(loadvar(SETTING_HIDDEN));
            $section->setHeader(loadvarAllowHTML(SETTING_SECTION_HEADER));
            $section->setFooter(loadvarAllowHTML(SETTING_SECTION_FOOTER));
            $section->save();
            $checks = $checker->checkSection($section);
            if (sizeof($checks) > 0) {
                $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
            }
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateSections(array($section));
        $mess = $compiler->generateGetFillsSections(array($section));
        $mess = $compiler->generateInlineFieldsSections(array($section));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($seid != '') {
            return $displaySysAdmin->showEditSection($_SESSION['SEID'], $content);
        } else {
            return $displaySysAdmin->showSurvey($content);
        }
    }

    function showRefactorSection() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        return $displaySysAdmin->showRefactorSection($_SESSION['SEID']);
    }

    function showRefactorSectionRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $seid = getFromSessionParams('seid');
        if ($seid != '') { //refactor
            $_SESSION['SEID'] = $seid;
            $section = $survey->getSection($seid);
            $old = $section->getName();

            if ($old != loadvar(SETTING_NAME)) {
                $section->setName(loadvar(SETTING_NAME));
                $new = $section->getName();
                $section->save();
                $generate = array();
                $generate[] = $section;

                $sections = $survey->getSections();
                foreach ($sections as $sect) {
                    if ($sect->getSeid() != $seid) {
                        $routing = $sect->getRouting();

                        $excluded = array();
                        $newrouting = excludeText($routing, $excluded);
                        $newrouting = preg_replace("/\b" . $old . "\b/i", $new, $newrouting);
                        $newrouting = includeText($newrouting, $excluded);

                        if ($newrouting != $routing) {
                            $sect->storeRouting($newrouting);
                            $sect->save();
                            $generate[] = $sect;
                        }
                    }
                }

                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $messages[] = $compiler->generateSections($generate);
                foreach ($generate as $gen) {
                    $messages[] = $compiler->generateEngine($gen->getSeid());
                }
                return $displaySysAdmin->showEditSection($_SESSION['SEID'], $displaySysAdmin->displaySuccess(Language::messageSectionRenamed($old, $section->getName())));
            } else {
                return $displaySysAdmin->showRefactorSection($_SESSION['SEID'], $displaySysAdmin->displayWarning(Language::messageSectionNotRenamed()));
            }
        } else {
            return $displaySysAdmin->showSurvey($content);
        }
    }

    function showMoveSection() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        return $displaySysAdmin->showMoveSection($_SESSION['SEID']);
    }

    function showMoveSectionRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.section';

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $seid = getFromSessionParams('seid');
        if ($seid != '') { //move
            $section = $survey->getSection($seid);

            // determine survey
            $suid = $_SESSION['SUID'];
            if (isset($_POST['suid'])) {
                $suid = loadvar('suid');
            }

            /* actually moved */
            if ($suid != $_SESSION['SUID']) {
                $section->move($suid);

                /* compile old survey if no copy made */
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateSections(array($section), true);
                $oldvars = $survey->getVariableDescriptives($section->getSeid());
                $mess = $compiler->generateVariableDescriptives($oldvars, true);

                /* update survey in session */
                $_SESSION['SUID'] = $suid;
                $_SESSION['SEID'] = $section->getSeid();

                /* compile other survey */
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateSections(array($section));
                $mess = $compiler->generateGetFillsSections(array($section));
                $mess = $compiler->generateInlineFieldsSections(array($section));
                $newvars = $survey->getVariableDescriptives($section->getSeid());
                $mess = $compiler->generateVariableDescriptives($newvars);
                $mess = $compiler->generateSetFills($newvars);
                $mess = $compiler->generateGetFills($newvars);
                $mess = $compiler->generateInlineFields($newvars);

                // show section again
                return $displaySysAdmin->showSection($section->getSeid(), $displaySysAdmin->displaySuccess(Language::messageSectionMoved($section->getName())));
            } else {
                return $displaySysAdmin->showSurvey($displaySysAdmin->displayWarning(Language::messageSectionNotMoved($section->getName())));
            }
        } else {
            return $displaySysAdmin->showSurvey();
        }
    }

    function showCopySection() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        $surveys = new Surveys();
        //if ($surveys->getNumberOfSurveys() > 1) {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showCopySection($_SESSION['SEID']);
        //} else {
        //    return $this->showCopySectionRes();
        //}
    }

    function showCopySectionRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.section';

        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        $seid = getFromSessionParams('seid');
        if ($seid != '') {
            $survey = new Survey($_SESSION['SUID']);
            $section = $survey->getSection($seid);
            $suid = "";
            if (loadvar("suid") != "") {
                $suid = loadvar("suid");
            }
            $section->copy($suid, loadvar("includesuffix"));

            if ($suid == "" || $suid == $_SESSION['SUID']) {
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateSections(array($section));
            } else {
                $_SESSION['SUID'] = $suid;
                $survey = new Survey($_SESSION['SUID']);
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateSections(array($section));
                $mess = $compiler->generateGetFillsSections(array($section));
                $mess = $compiler->generateInlineFieldsSections(array($section));
                $newvars = $survey->getVariableDescriptives($section->getSeid());
                $mess = $compiler->generateVariableDescriptives($newvars);
                $mess = $compiler->generateSetFills($newvars);
                $mess = $compiler->generateGetFills($newvars);
                $mess = $compiler->generateInlineFields($newvars);
            }

            $_SESSION['SEID'] = $section->getSeid();
            $displaySysAdmin = new DisplaySysAdmin();
            $content = $displaySysAdmin->displaySuccess(Language::messageSectionCopied($section->getName()));
            return $displaySysAdmin->showSection($section->getSeid(), $content);
        } else {
            $content = $displaySysAdmin->displayError(Language::messageSectionNotCopied($section->getName()));
            return $displaySysAdmin->showSurvey($content);
        }
    }

    function showRemoveSection() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        return $displaySysAdmin->showRemoveSection($_SESSION['SEID']);
    }

    function showRemoveSectionRes() {

        $_SESSION['LASTPAGE'] = 'sysadmin.survey';
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $seid = getFromSessionParams('seid');
        if ($seid != '') { //edit
            $section = $survey->getSection($seid);
            $section->remove();

            $variables = $survey->getVariableDescriptives($seid);
            foreach ($variables as $variable) {
                $variable->remove();
            }

            /* compile */
            $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
            $mess = $compiler->generateVariableDescriptives($variables, true);
            $mess = $compiler->generateSections(array($section), true);

            return $displaySysAdmin->showSurvey($displaySysAdmin->displaySuccess(Language::messageSectionRemoved($section->getName())));
        } else {
            return $displaySysAdmin->showSurvey();
        }
    }

    /* variable */

    function showOrderVariable() {
        $vsid = loadvar('id');
        if ($vsid != "") {
            $fromPosition = loadvar('fromPosition');
            $toPosition = loadvar('toPosition');
            $direction = loadvar('direction');
            if (is_array(loadvar('fromPosition'))) {
                $arr = loadvar('fromPosition');
                $fromPosition = $arr[0];
            } else {
                $fromPosition = loadvar('fromPosition');
            }

            /* check if never ordered before and all positions are 1 (old nubis) */
            global $db;
            $q = "select position, count(*) as cnt from " . Config::dbSurvey() . "_variables where suid=" . $_SESSION['SUID'] . " and seid=" . $_SESSION['SEID'] . " group by position";
            $res = $db->selectQuery($q);
            if ($res) {
                if ($db->getNumberOfRows($res) == 1) {
                    $row = $db->getRow($res);
                    if ($row["cnt"] > 1) {
                        $q = "SET @x = 0; UPDATE " . Config::dbSurvey() . "_variables SET position = (@x:=@x+1) where suid=" . $_SESSION['SUID'] . " and seid=" . $_SESSION['SEID'] . " ORDER BY variablename asc";
                        $db->executeQueries($q);
                    }
                }
            }

            $toPosition = loadvar('toPosition');
            $direction = loadvar('direction');
            $aPosition = ($direction === "back") ? $toPosition + 1 : $toPosition - 1;
            $db->executeQuery("update " . Config::dbSurvey() . "_variables set position = 0 where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and position=" . prepareDatabaseString($toPosition));
            $db->executeQuery("update " . Config::dbSurvey() . "_variables set position = " . prepareDatabaseString($toPosition) . " where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and vsid=" . prepareDatabaseString($vsid));

            // backward direction: up
            if ($direction === "back") {
                $db->executeQuery("update " . Config::dbSurvey() . "_variables set position = position + 1 WHERE (" . prepareDatabaseString($toPosition) . " <= position AND position <= " . prepareDatabaseString($fromPosition) . ") and suid=" . prepareDatabaseString($_SESSION['SUID']) . " and vsid != " . prepareDatabaseString($vsid) . " and position != 0 ORDER BY position DESC");
            }
            // Forward Direction: down
            else if ($direction === "forward") {
                $db->executeQuery("update " . Config::dbSurvey() . "_variables set position = position - 1 WHERE (" . prepareDatabaseString($fromPosition) . " <= position AND position <= " . prepareDatabaseString($toPosition) . ") and vsid != " . prepareDatabaseString($vsid) . " and suid=" . prepareDatabaseString($_SESSION['SUID']) . " and position != 0 ORDER BY position ASC");
            }

            $db->executeQuery("update " . Config::dbSurvey() . "_variables set position = " . prepareDatabaseString($aPosition) . " where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and position=0");
        }
    }

    function showAddVariable($content = "") {
        $displaySysAdmin = new DisplaySysAdmin();
        unset($_SESSION['VSID']);
        unset($_SESSION['VRFILTERMODE_VARIABLE']);
        return $displaySysAdmin->showEditVariable('', $content);
    }

    function showCopyVariable() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }

        $surveys = new Surveys();
        if ($surveys->getNumberOfSurveys() > 1) {
            $displaySysAdmin = new DisplaySysAdmin();
            return $displaySysAdmin->showCopyVariable($_SESSION['VSID']);
        } else {
            $survey = new Survey($_SESSION['SUID']);
            //if (sizeof($survey->getSections()) > 1) {
            $displaySysAdmin = new DisplaySysAdmin();
            return $displaySysAdmin->showCopyVariable($_SESSION['VSID']);
            //} else {
            //    return $this->showCopyVariableRes();
            //}
        }
    }

    function showCopyVariableRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.variable';

        $displaySysAdmin = new DisplaySysAdmin();
        $vsid = getFromSessionParams('vsid');
        if ($vsid != '') {
            $survey = new Survey($_SESSION['SUID']);
            $var = $survey->getVariableDescriptive($vsid);
            $oldname = $var->getName();
            $suid = "";
            if (loadvar("suid") != "") {
                $suid = loadvar("suid");
            }
            $seid = "";
            if (loadvar("section") != "") {
                $seid = loadvar("section");
            }

            $number = loadvar('numberofcopies');
            if ($number > 0) {
                $newvars = array();
                for ($cnt = 0; $cnt < $number; $cnt++) {
                    if (loadvar("includesuffix") == 2) {
                        $var->copy($oldname . "_cl" . ($cnt + 1), $suid, $seid);
                    } else {
                        $var->copy($oldname . ($cnt + 1), $suid, $seid);
                    }
                    if ($cnt == 0) {
                        $_SESSION['VSID'] = $var->getVsid();
                    }

                    /* update section in session */
                    $_SESSION['SEID'] = loadvar("section");

                    // recompile
                    if ($suid == "" || $suid == $_SESSION['SUID']) {
                        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                        $mess = $compiler->generateVariableDescriptives(array($var));
                    }
                    // new survey, then compile the rest as well
                    else {
                        $_SESSION['SUID'] = $suid;
                        $survey = new Survey($_SESSION['SUID']);
                        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                        $mess = $compiler->generateVariableDescriptives(array($var));
                        $mess = $compiler->generateSetFills(array($var));
                        $mess = $compiler->generateGetFills(array($var));
                        $mess = $compiler->generateInlineFields(array($var));
                    }
                }

                $content = $displaySysAdmin->displaySuccess(Language::messageVariableCopied($oldname));
                return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
            } else {
                $content = $displaySysAdmin->displayError(Language::messageVariableNotCopied($var->getName()));
                return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
            }
        } else {
            $content = $displaySysAdmin->displayError(Language::messageVariableNotCopied($var->getName()));
            return $displaySysAdmin->showSection($content);
        }
    }

    function showEditVariable($vsid = "", $content = "") {
        $displaySysAdmin = new DisplaySysAdmin();
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
        } else if (inArray($answertype, array(ANSWER_TYPE_NONE)) && !inArray($_SESSION['VRFILTERMODE_VARIABLE'], array(0, 2, 5, 7, 8))) {
            $_SESSION['VRFILTERMODE_VARIABLE'] = 0;
        }

        /* update section id */
        $_SESSION['SEID'] = $variable->getSeid();
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableGeneralRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $_SESSION['VSID'] = $vsid;
        $content = "";
        if ($vsid != '') { //edit
            $variable = $survey->getVariableDescriptive($vsid);
            $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged(loadvar(SETTING_NAME)));
        } else { //add variable!
            if (loadvar(SETTING_NAME) != "") {
                $variable = new VariableDescriptive();
                $variable->setSuid($_SESSION['SUID']);
                $variable->setSeid($_SESSION['SEID']);
                $_SESSION['VSID'] = $variable->getVsid();
                $content = $displaySysAdmin->displaySuccess(Language::messageVariableAdded(loadvar(SETTING_NAME)));
            }
        }

        $checker = new Checker($_SESSION['SUID']);
        if ($vsid == '') {
            $checks = $checker->checkName(loadvar(SETTING_NAME));
            if (sizeof($checks) > 0) {
                $content = implode("<br/>", $checks);
                return $this->showAddVariable($content);
            }
        }

        /* ADD ALL SORTS OF CHECKS!! */
        if ($vsid != '' || loadvar(SETTING_NAME) != "") {
            $checks = $checker->checkName(loadvar(SETTING_NAME), $vsid);
            if (sizeof($checks) > 0) {
                $content = implode("<br/>", $checks);
                return $this->showEditVariable($vsid, $content);
            }

            $variable->setName(trim(loadvar(SETTING_NAME)));
            $variable->setDescription(loadvar(SETTING_DESCRIPTION));
            $variable->setQuestion(loadvarAllowHTML(SETTING_QUESTION));
            $variable->setAnswerType(loadvar(SETTING_ANSWERTYPE));

            // check custom answer type
            $tocall = str_replace('"', "'", trim(loadvar(SETTING_ANSWERTYPE_CUSTOM)));
            if ($tocall != "" && $tocall != "settingfollowtype") {
                $removed = array();
                $test = excludeText($tocall, $removed);
                if (stripos($test, '(') !== false) {
                    $parameters = rtrim(substr($test, stripos($test, '(') + 1), ')');
                    $parameters = preg_split("/[\s,]+/", $parameters);

                    foreach ($parameters as $p) {
                        $t = str_replace(INDICATOR_FILL, "", $p);
                        $t = str_replace(INDICATOR_FILL_NOVALUE, "", $t);
                        $vr = $survey->getVariableDescriptiveByName($t);
                        if ($vr->getVsid() != "") {
                            // variable reference ok
                        } else if (is_numeric($t)) {
                            // number ok
                        } /* else if (startsWith($t, '"') && endsWith($t, '"')) {
                          // quoted text ok
                          } else if (startsWith($t, "'") && endsWith($t, "'")) {
                          // quoted text ok
                          } */ else {
                            if (stripos($t, '(') !== false) {
                                $t = str_replace('(', '', $t);
                                $t = str_replace(')', '', $t);
                                $checks[] = "Parameter function call " . $t . " not allowed";
                            } else {
                                //$checks[] = $displaySysAdmin->displayError("Parameter '" . $t . "' must be a variable reference");
                            }
                        }
                    }
                    $tocheck = substr($test, 0, stripos($test, '('));
                } else {
                    $tocheck = $tocall;
                }

                // check against allowed custom answer functions
                if (inArray($tocheck, getAllowedCustomAnswerFunctions()) && !inArray($tocheck, getForbiddenCustomAnswerFunctions())) {
                    // ok
                    $variable->setAnswerTypeCustom($tocall);
                } else {
                    $checks[] = Language::messageCheckerFunctionNotAllowed($tocheck);
                }

                if (sizeof($checks) > 0) {
                    $content = $displaySysAdmin->displayError(implode("<br/>", $checks));
                    return $displaySysAdmin->showEditVariable($vsid, $content);
                }
            } else {

                // custom type specified, then must have custom answer function
                if (loadvar(SETTING_ANSWERTYPE) == ANSWER_TYPE_CUSTOM) {
                    if (loadvar(SETTING_TYPE) == -1) {
                        $checks[] = $displaySysAdmin->displayError("Custom answer function must be specified");

                        if (sizeof($checks) > 0) {
                            $content = implode("<br/>", $checks);
                            return $displaySysAdmin->showEditVariable($vsid, $content);
                        }
                    }
                }
                // no value, then set to empty
                else {
                    $variable->setAnswerTypeCustom($tocall);
                }
            }
            // end check custom answer type

            $user = new User($_SESSION['URID']);
            //if ($user->hasHTMLEditor()) {
            //    $variable->setOptionsText(str_ireplace("<br />", "\r\n" ,loadvarAllowHTML(SETTING_OPTIONS)));
            //}
            //else {
            $variable->setOptionsText(loadvarAllowHTML(SETTING_OPTIONS));
            //}
            $variable->setArray(loadvar(SETTING_ARRAY));
            $variable->setKeep(loadvar(SETTING_KEEP));
            $answertype = loadvar(SETTING_ANSWERTYPE);
            if (inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                $variable->setHidden(HIDDEN_YES);
            } else {
                //$variable->setHidden(loadvar(SETTING_HIDDEN));
            }
            if ($variable->getInputMask() == "") {
                switch ($answertype) {
                    case ANSWER_TYPE_INTEGER:
                        $variable->setInputMask(INPUTMASK_INTEGER);
                        break;
                    case ANSWER_TYPE_DOUBLE:
                        $variable->setInputMask(INPUTMASK_DOUBLE);
                        break;
                    case ANSWER_TYPE_RANGE:
                        $variable->setInputMask(INPUTMASK_INTEGER);
                        break;
                    default:
                        $variable->setInputMask(null);
                        break;
                }
            }
            $variable->setSection(loadvar(SETTING_SECTION));
            $variable->setTyd(loadvar(SETTING_TYPE));
            $variable->save();

            $checks = $checker->checkVariable($variable);
            if (sizeof($checks) > 0) {
                $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
            }
        }

        /* reload in case of type */
        if (loadvar(SETTING_NAME) != "" && $variable->getTyd() != -1) {
            $variable = $survey->getVariableDescriptive($vsid);
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($variable));
        $mess = $compiler->generateGetFills(array($variable));
        $mess = $compiler->generateInlineFields(array($variable));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($vsid != '') {
            return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
        } else {
            return $this->showSection($content);
        }
    }

    function showEditVariableOutputRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $_SESSION['VSID'] = $vsid;
        $var = $survey->getVariableDescriptive($vsid);
        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->setHidden(loadvar(SETTING_HIDDEN));
        $var->setHiddenPaperVersion(loadvar(SETTING_HIDDEN_PAPER_VERSION));
        $var->setHiddenRouting(loadvar(SETTING_HIDDEN_ROUTING));
        $var->setHiddenTranslation(loadvar(SETTING_HIDDEN_TRANSLATION));
        $var->setScreendumpStorage(loadvar(SETTING_SCREENDUMPS));
        $var->setParadata(loadvar(SETTING_PARADATA));
        $var->setDataKeep(loadvar(SETTING_DATA_KEEP));
        $var->setDataInputMask(loadvar(SETTING_DATA_INPUTMASK));
        $var->setDataSkipVariable(loadvar(SETTING_DATA_SKIP));
        $var->setDataSkipVariablePostFix(loadvar(SETTING_DATA_SKIP_POSTFIX));
        $var->setStoreLocation(loadvar(SETTING_DATA_STORE_LOCATION));
        $var->setStoreLocationExternal(loadvar(SETTING_DATA_STORE_LOCATION_EXTERNAL));

        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }
        if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED))) {
            $var->setOutputSetOfEnumeratedBinary(loadvar(SETTING_OUTPUT_SETOFENUMERATED));
        }
        if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_ENUMERATED))) {
            $var->setOutputOptionsText(loadvarAllowHTML(SETTING_OUTPUT_OPTIONS));
            $var->setOutputValueLabelWidth(loadvar(SETTING_OUTPUT_VALUELABEL_WIDTH));
        }

        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $compiler->generateVariableDescriptives(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($vsid != '') {
            return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
        } else {
            return $this->showSection($content);
        }
    }

    function showEditVariableInteractiveRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $_SESSION['VSID'] = $vsid;
        $var = $survey->getVariableDescriptive($vsid);
        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->setID(loadvar(SETTING_ID));
        $var->setInlineJavascript(loadvarAllowHTML(SETTING_JAVASCRIPT_WITHIN_ELEMENT));
        $var->setPageJavascript(loadvarAllowHTML(SETTING_JAVASCRIPT_WITHIN_PAGE));
        $var->setScripts(loadvarAllowHTML(SETTING_SCRIPTS));
        $var->setInlineStyle(loadvarAllowHTML(SETTING_STYLE_WITHIN_ELEMENT));
        $var->setPageStyle(loadvarAllowHTML(SETTING_STYLE_WITHIN_PAGE));

        $var->setOnBack(loadvar(SETTING_ON_BACK));
        $var->setOnNext(loadvar(SETTING_ON_NEXT));
        $var->setOnDK(loadvar(SETTING_ON_DK));
        $var->setOnRF(loadvar(SETTING_ON_RF));
        $var->setOnNA(loadvar(SETTING_ON_NA));
        $var->setOnUpdate(loadvar(SETTING_ON_UPDATE));
        $var->setOnLanguageChange(loadvar(SETTING_ON_LANGUAGE_CHANGE));
        $var->setOnModeChange(loadvar(SETTING_ON_MODE_CHANGE));
        $var->setOnVersionChange(loadvar(SETTING_ON_VERSION_CHANGE));

        $var->setClickBack(loadvar(SETTING_CLICK_BACK));
        $var->setClickNext(loadvar(SETTING_CLICK_NEXT));
        $var->setClickDK(loadvar(SETTING_CLICK_DK));
        $var->setClickRF(loadvar(SETTING_CLICK_RF));
        $var->setClickNA(loadvar(SETTING_CLICK_NA));
        $var->setClickUpdate(loadvar(SETTING_CLICK_UPDATE));
        $var->setClickLanguageChange(loadvar(SETTING_CLICK_LANGUAGE_CHANGE));
        $var->setClickModeChange(loadvar(SETTING_CLICK_MODE_CHANGE));
        $var->setClickVersionChange(loadvar(SETTING_CLICK_VERSION_CHANGE));
        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $compiler->generateVariableDescriptives(array($var));
        $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableValidationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;

        /* save validation settings based on answer type */
        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }
        if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_OPEN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB, ANSWER_TYPE_CALENDAR))) {

            switch ($answertype) {
                case ANSWER_TYPE_ENUMERATED:
                    $var->setInlineExactRequired(loadvar(SETTING_INLINE_EXACT_REQUIRED));
                    $var->setInlineExclusive(loadvar(SETTING_INLINE_EXCLUSIVE));
                    $var->setInlineInclusive(loadvar(SETTING_INLINE_INCLUSIVE));
                    $var->setInlineMinimumRequired(loadvar(SETTING_INLINE_MINIMUM_REQUIRED));
                    $var->setInlineMaximumRequired(loadvar(SETTING_INLINE_MAXIMUM_REQUIRED));
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $var->setInlineExactRequired(loadvar(SETTING_INLINE_EXACT_REQUIRED));
                    $var->setInlineExclusive(loadvar(SETTING_INLINE_EXCLUSIVE));
                    $var->setInlineInclusive(loadvar(SETTING_INLINE_INCLUSIVE));
                    $var->setInlineMinimumRequired(loadvar(SETTING_INLINE_MINIMUM_REQUIRED));
                    $var->setInlineMaximumRequired(loadvar(SETTING_INLINE_MAXIMUM_REQUIRED));
                /* fall through */
                case ANSWER_TYPE_MULTIDROPDOWN;
                    $var->setMinimumSelected(loadvar(SETTING_MINIMUM_SELECTED));
                    $var->setMaximumSelected(loadvar(SETTING_MAXIMUM_SELECTED));
                    $var->setExactSelected(loadvar(SETTING_EXACT_SELECTED));
                    $var->setInvalidSelected(loadvar(SETTING_INVALID_SELECTED));
                    $var->setInvalidSubSelected(loadvar(SETTING_INVALIDSUB_SELECTED));
                    break;
                case ANSWER_TYPE_RANK;
                    $var->setMinimumRanked(loadvar(SETTING_MINIMUM_RANKED));
                    $var->setMaximumRanked(loadvar(SETTING_MAXIMUM_RANKED));
                    $var->setExactRanked(loadvar(SETTING_EXACT_RANKED));
                    break;
                case ANSWER_TYPE_OPEN:
                /* fall through */
                case ANSWER_TYPE_STRING:
                    $var->setMinimumLength(loadvar(SETTING_MINIMUM_LENGTH));
                    $var->setMaximumLength(loadvar(SETTING_MAXIMUM_LENGTH));
                    $var->setMinimumWords(loadvar(SETTING_MINIMUM_WORDS));
                    $var->setMaximumWords(loadvar(SETTING_MAXIMUM_WORDS));
                    $var->setPattern(loadvar(SETTING_PATTERN));
                    break;
                case ANSWER_TYPE_RANGE:
                    $minimum = loadvar(SETTING_MINIMUM_RANGE);
                    $maximum = loadvar(SETTING_MAXIMUM_RANGE);
                    $others = loadvar(SETTING_OTHER_RANGE);
                    if (!(contains($minimum, ".") || contains($maximum, ".") || contains($others, "."))) {
                        if ($var->getInputMask() == "") {
                            $var->setInputMask(INPUTMASK_INTEGER);
                        }
                    } else {
                        if ($var->getInputMask() == "") {
                            $var->setInputMask(INPUTMASK_DOUBLE);
                        }
                    }
                    $var->setOtherValues($others);
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                    $var->setMinimum(loadvar(SETTING_MINIMUM_RANGE));
                    $var->setMaximum(loadvar(SETTING_MAXIMUM_RANGE));
                    break;
                case ANSWER_TYPE_KNOB:
                    $var->setMinimum(loadvar(SETTING_MINIMUM_RANGE));
                    $var->setMaximum(loadvar(SETTING_MAXIMUM_RANGE));
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $var->setMaximumDatesSelected(loadvar(SETTING_MAXIMUM_CALENDAR));
                    break;
                case ANSWER_TYPE_CUSTOM:
                    $minimum = loadvar(SETTING_MINIMUM_RANGE);
                    $maximum = loadvar(SETTING_MAXIMUM_RANGE);
                    $others = loadvar(SETTING_OTHER_RANGE);
                    if (!(contains($minimum, ".") || contains($maximum, ".") || contains($others, "."))) {
                        if ($var->getInputMask() == "") {
                            $var->setInputMask(INPUTMASK_INTEGER);
                        }
                    } else {
                        if ($var->getInputMask() == "") {
                            $var->setInputMask(INPUTMASK_DOUBLE);
                        }
                    }
                    $var->setMinimum(loadvar(SETTING_MINIMUM_RANGE));
                    $var->setMaximum(loadvar(SETTING_MAXIMUM_RANGE));
                    $var->setOtherValues($others);
                    break;
            }
        }

        if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $var->setComparisonEqualTo(loadvar(SETTING_COMPARISON_EQUAL_TO));
            $var->setComparisonNotEqualTo(loadvar(SETTING_COMPARISON_NOT_EQUAL_TO));
            $var->setComparisonGreaterEqualTo(loadvar(SETTING_COMPARISON_GREATER_EQUAL_TO));
            $var->setComparisonGreater(loadvar(SETTING_COMPARISON_GREATER));
            $var->setComparisonSmallerEqualTo(loadvar(SETTING_COMPARISON_SMALLER_EQUAL_TO));
            $var->setComparisonSmaller(loadvar(SETTING_COMPARISON_SMALLER));
        }
        /* string comparisons */ if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $var->setComparisonEqualTo(loadvar(SETTING_COMPARISON_EQUAL_TO));
            $var->setComparisonNotEqualTo(loadvar(SETTING_COMPARISON_NOT_EQUAL_TO));
            $var->setComparisonEqualToIgnoreCase(loadvar(SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE));
            $var->setComparisonNotEqualToIgnoreCase(loadvar(SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE));
        }

        if (inArray($answertype, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_DATE, ANSWER_TYPE_TIME, ANSWER_TYPE_DATETIME, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            $var->setInputMaskEnabled(loadvar(SETTING_INPUT_MASK_ENABLED));
            $var->setInputMask(loadvar(SETTING_INPUT_MASK));
            $var->setInputMaskCustom(loadvarAllowHTML(SETTING_INPUT_MASK_CUSTOM));
            $var->setInputMaskPlaceholder(loadvar(SETTING_INPUT_MASK_PLACEHOLDER));
            $var->setInputMaskCallback(loadvarAllowHTML(SETTING_INPUT_MASK_CALLBACK));
        }

        $var->setValidateAssignment(loadvar(SETTING_VALIDATE_ASSIGNMENT));
        $var->setIfEmpty(loadvar(SETTING_IFEMPTY));
        $var->setIfError(loadvar(SETTING_IFERROR));
        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableLayoutRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;

        $var->setPageHeader(loadvarAllowHTML(SETTING_PAGE_HEADER));
        $var->setPageFooter(loadvarAllowHTML(SETTING_PAGE_FOOTER));
        $var->setErrorPlacement(loadvar(SETTING_ERROR_PLACEMENT));
        $var->setPlaceholder(loadvarAllowHTML(SETTING_PLACEHOLDER));

        $var->setQuestionAlignment(loadvar(SETTING_QUESTION_ALIGNMENT));
        $ans = loadvar(SETTING_QUESTION_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $var->setQuestionFormatting(implode("~", $ans));
        $var->setAnswerAlignment(loadvar(SETTING_ANSWER_ALIGNMENT));
        $ans = loadvar(SETTING_ANSWER_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $var->setAnswerFormatting(implode("~", $ans));
        $var->setButtonAlignment(loadvar(SETTING_BUTTON_ALIGNMENT));
        $ans = loadvar(SETTING_BUTTON_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $var->setButtonFormatting(implode("~", $ans));

        $var->setShowBackButton(loadvar(SETTING_BACK_BUTTON));
        $var->setShowNextButton(loadvar(SETTING_NEXT_BUTTON));
        $var->setShowDKButton(loadvar(SETTING_DK_BUTTON));
        $var->setShowRFButton(loadvar(SETTING_RF_BUTTON));
        $var->setShowUpdateButton(loadvar(SETTING_UPDATE_BUTTON));
        $var->setShowNAButton(loadvar(SETTING_NA_BUTTON));
        $var->setShowRemarkButton(loadvar(SETTING_REMARK_BUTTON));
        $var->setShowRemarkSaveButton(loadvar(SETTING_REMARK_SAVE_BUTTON));
        $var->setShowCloseButton(loadvar(SETTING_CLOSE_BUTTON));

        $var->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $var->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $var->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $var->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $var->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $var->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $var->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $var->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));
        $var->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));

        $var->setShowProgressBar(loadvar(SETTING_PROGRESSBAR_SHOW));
        $var->setProgressBarFillColor(loadvar(SETTING_PROGRESSBAR_FILLED_COLOR));
        $var->setProgressBarWidth(loadvar(SETTING_PROGRESSBAR_WIDTH));
        $var->setProgressBarValue(loadvar(SETTING_PROGRESSBAR_VALUE));

        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $survey->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }

        if ($answertype == ANSWER_TYPE_TIME) {
            $var->setTimeFormat(loadvar(SETTING_TIME_FORMAT));
            //$var->setDateDefaultView(loadvar(SETTING_DATE_DEFAULT_VIEW));
        } else if ($answertype == ANSWER_TYPE_DATE) {
            $var->setDateFormat(loadvar(SETTING_DATE_FORMAT));
            $var->setDateDefaultView(loadvar(SETTING_DATE_DEFAULT_VIEW));
        } else if ($answertype == ANSWER_TYPE_DATETIME) {
            $var->setDateTimeFormat(loadvar(SETTING_DATETIME_FORMAT));
            $var->setDateDefaultView(loadvar(SETTING_DATE_DEFAULT_VIEW));
            $var->setDateTimeCollapse(loadvar(SETTING_DATETIME_COLLAPSE));
            $var->setDateTimeSideBySide(loadvar(SETTING_DATETIME_SIDE_BY_SIDE));
        } else if ($answertype == ANSWER_TYPE_SLIDER) {
            $var->setSliderOrientation(loadvar(SETTING_SLIDER_ORIENTATION));
            $var->setIncrement(loadvar(SETTING_SLIDER_INCREMENT));
            $var->setTooltip(loadvar(SETTING_SLIDER_TOOLTIP));
            $var->setTextbox(loadvar(SETTING_SLIDER_TEXTBOX));
            $var->setTextboxLabel(loadvar(SETTING_SLIDER_TEXTBOX_LABEL));
            $var->setTextboxPosttext(loadvar(SETTING_SLIDER_TEXTBOX_POSTTEXT));
            $var->setSliderLabels(loadvar(SETTING_SLIDER_LABELS));
            $var->setSliderLabelPlacement(loadvar(SETTING_SLIDER_LABEL_PLACEMENT));
            $var->setSliderPreSelection(loadvar(SETTING_SLIDER_PRESELECTION));
            $var->setSpinner(loadvar(SETTING_SPINNER));
            $var->setSpinnerType(loadvar(SETTING_SPINNER_TYPE));
            $var->setSpinnerUp(loadvar(SETTING_SPINNER_UP));
            $var->setSpinnerDown(loadvar(SETTING_SPINNER_DOWN));
            $var->setTextboxManual(loadvar(SETTING_TEXTBOX_MANUAL));
            $var->setSliderFormater(loadvar(SETTING_SLIDER_FORMATER));
        } else if ($answertype == ANSWER_TYPE_KNOB) {
            //$var->setKnobRotation(loadvar(SETTING_KNOB_ROTATION));
            $var->setIncrement(loadvar(SETTING_SLIDER_INCREMENT));
            $var->setTextbox(loadvar(SETTING_SLIDER_TEXTBOX));
            $var->setTextboxLabel(loadvar(SETTING_SLIDER_TEXTBOX_LABEL));
            $var->setTextboxPosttext(loadvar(SETTING_SLIDER_TEXTBOX_POSTTEXT));
            $var->setSpinner(loadvar(SETTING_SPINNER));
            $var->setSpinnerType(loadvar(SETTING_SPINNER_TYPE));
            $var->setSpinnerUp(loadvar(SETTING_SPINNER_UP));
            $var->setSpinnerDown(loadvar(SETTING_SPINNER_DOWN));
            $var->setTextboxManual(loadvar(SETTING_TEXTBOX_MANUAL));
        } else if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $var->setEnumeratedRandomizer(loadvar(SETTING_ENUMERATED_RANDOMIZER));
            $var->setComboboxOptGroup(loadvar(SETTING_DROPDOWN_OPTGROUP));
        } else if (inArray($answertype, array(ANSWER_TYPE_RANK))) {
            $var->setEnumeratedRandomizer(loadvar(SETTING_ENUMERATED_RANDOMIZER));
            //$var->setRankColumn(loadvar(SETTING_RANK_COLUMN));
            $var->setEnumeratedLabel(loadvar(SETTING_ENUMERATED_LABEL));
        } else if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            $var->setEnumeratedDisplay(loadvar(SETTING_ENUMERATED_ORIENTATION));
            $var->setEnumeratedBordered(loadvar(SETTING_ENUMERATED_BORDERED));
            $var->setEnumeratedSplit(loadvar(SETTING_ENUMERATED_SPLIT));
            $var->setEnumeratedTextbox(loadvar(SETTING_ENUMERATED_TEXTBOX));
            $var->setEnumeratedTextboxLabel(loadvar(SETTING_ENUMERATED_TEXTBOX_LABEL));
            $var->setEnumeratedTextboxPostText(loadvar(SETTING_ENUMERATED_TEXTBOX_POSTTEXT));
            $var->setEnumeratedLabel(loadvar(SETTING_ENUMERATED_LABEL));
            $var->setEnumeratedClickLabel(loadvar(SETTING_ENUMERATED_CLICK_LABEL));
            $var->setEnumeratedRandomizer(loadvar(SETTING_ENUMERATED_RANDOMIZER));
            $var->setEnumeratedColumns(loadvar(SETTING_ENUMERATED_COLUMNS));
            $var->setHeaderAlignment(loadvar(SETTING_HEADER_ALIGNMENT));
            
            if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED))) {
                $var->setSetOfEnumeratedRanking(loadvar(SETTING_SETOFENUMERATED_RANKING));
            }
            $ans = loadvar(SETTING_HEADER_FORMATTING);
            if (!is_array($ans)) {
                $ans = array($ans);
            }
            $var->setHeaderFormatting(implode("~", $ans));
            $var->setEnumeratedOrder(loadvar(SETTING_ENUMERATED_ORDER));
            $var->setEnumeratedCustom(loadvarAllowHTML(SETTING_ENUMERATED_CUSTOM));
            $var->setTableMobile(loadvar(SETTING_TABLE_MOBILE));
            $var->setTableMobileLabels(loadvar(SETTING_TABLE_MOBILE_LABELS));
        }

        if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
            $var->setSpinner(loadvar(SETTING_SPINNER));
            $var->setSpinnerType(loadvar(SETTING_SPINNER_TYPE));
            $var->setSpinnerUp(loadvar(SETTING_SPINNER_UP));
            $var->setSpinnerDown(loadvar(SETTING_SPINNER_DOWN));
            $var->setSpinnerIncrement(loadvar(SETTING_SPINNER_STEP));
            $var->setTextboxManual(loadvar(SETTING_TEXTBOX_MANUAL));
        }

        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));

        $var->setShowSectionHeader(loadvar(SETTING_SHOW_SECTION_HEADER));
        $var->setShowSectionFooter(loadvar(SETTING_SHOW_SECTION_FOOTER));

        if (Config::xiExtension()) {
            $var->setXiTemplate(loadvar(SETTING_GROUP_XI_TEMPLATE));
        }

        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableAccessRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;
        $var->setAccessReturnAfterCompletionAction(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION));
        $var->setAccessReturnAfterCompletionRedoPreload(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO));
        $var->setAccessReentryAction(loadvar(SETTING_ACCESS_REENTRY_ACTION));
        $var->setAccessReentryRedoPreload(loadvar(SETTING_ACCESS_REENTRY_PRELOAD_REDO));
        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableAssistanceRes() {
        $displaySysAdmin = new DisplaySysAdmin();
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
                $var->setErrorMessageEnumeratedEntered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED));
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $var->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
                $var->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
                $var->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
                $var->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
                $var->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
                $var->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));
                $var->setErrorMessageSetOfEnumeratedEntered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED));
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $var->setErrorMessageSelectMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_SELECT));
                $var->setErrorMessageSelectMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT));
                $var->setErrorMessageSelectExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_SELECT));
                $var->setErrorMessageSelectInvalidSubset(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT));
                $var->setErrorMessageSelectInvalidSet(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SELECT));
                break;
            case ANSWER_TYPE_RANK:
                $var->setErrorMessageRankMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_RANK));
                $var->setErrorMessageRankMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_RANK));
                $var->setErrorMessageRankExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_RANK));
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

        if (inArray($answertype, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $var->setErrorMessageComparisonEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO));
            $var->setErrorMessageComparisonNotEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO));
            $var->setErrorMessageComparisonGreaterEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO));
            $var->setErrorMessageComparisonGreater(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_GREATER));
            $var->setErrorMessageComparisonSmallerEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO));
            $var->setErrorMessageComparisonSmaller(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER));
        }
        /* string comparisons */ else if (inArray($answertype, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $var->setErrorMessageComparisonEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO));
            $var->setErrorMessageComparisonNotEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO));
            $var->setErrorMessageComparisonEqualToIgnoreCase(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE));
            $var->setErrorMessageComparisonNotEqualToIgnoreCase(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE));
        }

        $var->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableFillRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;
        $var->setFillText(loadvarAllowHTML(SETTING_FILLTEXT));
        $var->setFillCode(loadvarAllowHTML(SETTING_FILLCODE));
        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $messages = $compiler->generateSetFills(array($var));
        $mess = $compiler->generateGetFills(array($var));
        $mess = $compiler->generateInlineFields(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if (sizeof($messages) > 0) {

            $m = '<a data-keyboard="false" data-toggle="modal" data-target="#errorsModal">Show errors</a>';
            $content .= $displaySysAdmin->displayError(Language::messageFillRoutingNeedsFix() . " " . $m);
            $text = "";
            foreach ($messages as $rgid => $m) {
                foreach ($m as $s) {
                    if (trim($s) != "") {
                        $text .= $displaySysAdmin->displayError(Language::errorRoutingLine() . " " . $rgid . ": " . $s);
                    }
                }
            }
            $content .= $displaySysAdmin->displayRoutingErrorModal($var, $text);
            //$content .= implode('<br/>', $messages);
            //$content .= '</div>';
        }

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableCheckRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;
        $var->setCheckText(loadvarAllowHTML(SETTING_CHECKTEXT));
        $var->setCheckCode(loadvarAllowHTML(SETTING_CHECKCODE));
        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $messages = $compiler->generateChecks(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if (sizeof($messages) > 0) {

            $m = '<a data-keyboard="false" data-toggle="modal" data-target="#errorsModal">Show errors</a>';
            $content .= $displaySysAdmin->displayError(Language::messageCheckRoutingNeedsFix() . " " . $m);
            $text = "";
            foreach ($messages as $rgid => $m) {
                foreach ($m as $s) {
                    if (trim($s) != "") {
                        $text .= $displaySysAdmin->displayError(Language::errorRoutingLine() . " " . $rgid . ": " . $s);
                    }
                }
            }
            $content .= $displaySysAdmin->displayRoutingErrorModal($var, $text);
            //$content .= implode('<br/>', $messages);
            //$content .= '</div>';
        }

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showEditVariableNavigationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        $var = $survey->getVariableDescriptive($vsid);
        $_SESSION['VSID'] = $vsid;

        $content = $displaySysAdmin->displaySuccess(Language::messageVariableChanged($var->getName()));
        $var->setKeyboardBindingEnabled(loadvar(SETTING_KEYBOARD_BINDING_ENABLED));
        $var->setKeyboardBindingBack(loadvar(SETTING_KEYBOARD_BINDING_BACK));
        $var->setKeyboardBindingNext(loadvar(SETTING_KEYBOARD_BINDING_NEXT));
        $var->setKeyboardBindingDK(loadvar(SETTING_KEYBOARD_BINDING_DK));
        $var->setKeyboardBindingRF(loadvar(SETTING_KEYBOARD_BINDING_RF));
        $var->setKeyboardBindingNA(loadvar(SETTING_KEYBOARD_BINDING_NA));
        $var->setKeyboardBindingUpdate(loadvar(SETTING_KEYBOARD_BINDING_UPDATE));
        $var->setKeyboardBindingRemark(loadvar(SETTING_KEYBOARD_BINDING_REMARK));
        $var->setKeyboardBindingClose(loadvar(SETTING_KEYBOARD_BINDING_CLOSE));
        $var->setIndividualDKRFNA(loadvar(SETTING_DKRFNA));
        $var->setIndividualDKRFNAInline(loadvar(SETTING_DKRFNA_INLINE));
        $var->save();

        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkVariable($var);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateVariableDescriptives(array($var));
        $mess = $compiler->generateGetFills(array($var));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $content);
    }

    function showRemoveVariable() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        return $displaySysAdmin->showRemoveVariable($_SESSION['VSID']);
    }

    function showRemoveVariableRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.section';

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        if ($vsid != '') { //remove
            $variable = $survey->getVariableDescriptive($vsid);
            $variable->remove();

            /* compile */
            $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
            $mess = $compiler->generateVariableDescriptives(array($variable), true);
            $mess = $compiler->generateSetFills(array($variable), true);
            //$mess = $compiler->generateGetFills();
            return $displaySysAdmin->showSection($_SESSION['SEID'], $displaySysAdmin->displaySuccess(Language::messageVariableRemoved($variable->getName())));
        } else {
            return $displaySysAdmin->showSection($_SESSION['SEID']);
        }
    }

    function showRefactorVariable() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        return $displaySysAdmin->showRefactorVariable($_SESSION['VSID']);
    }

    function updateVariableValue($old, $new, $val) {

        // check fills
        $fills = getReferences($val, INDICATOR_FILL);
        $fills2 = getReferences($val, INDICATOR_FILL_NOVALUE);
        $fills3 = getReferences($val, INDICATOR_INLINEFIELD_ANSWER);
        $fills4 = getReferences($val, INDICATOR_INLINEFIELD_TEXT);

        usort($fills, "reversenat");
        foreach ($fills as $fill) {
            $newfill = preg_replace("/\b" . $old . "\b/i", $new, $fill);
            if ($newfill != $fill) {
                $pattern = "/\\" . INDICATOR_FILL . preparePattern($fill) . "/i";
                $val = preg_replace($pattern, INDICATOR_FILL . $newfill, $val);
            }
        }

        // check fills no values
        usort($fills2, "reversenat");
        foreach ($fills2 as $fill) {
            $newfill = preg_replace("/\b" . $old . "\b/i", $new, $fill);
            if ($newfill != $fill) {
                $pattern = "/\\" . INDICATOR_FILL_NOVALUE . preparePattern($fill) . "/i";
                $val = preg_replace($pattern, INDICATOR_FILL_NOVALUE . $newfill, $val);
            }
        }

        // check inline fields
        usort($fills3, "reversenat");
        foreach ($fills3 as $fill) {
            $newfill = preg_replace("/\b" . $old . "\b/i", $new, $fill);
            if ($newfill != $fill) {
                $pattern = "/\\" . INDICATOR_INLINEFIELD_ANSWER . preparePattern($fill) . "/i";
                $val = preg_replace($pattern, INDICATOR_INLINEFIELD_ANSWER . $newfill, $val);
            }
        }

        // check inline field texts
        usort($fills4, "reversenat");
        foreach ($fills4 as $fill) {
            $newfill = preg_replace("/\b" . $old . "\b/i", $new, $fill);
            if ($newfill != $fill) {
                $pattern = "/\\" . INDICATOR_INLINEFIELD_TEXT . preparePattern($fill) . "/i";
                $val = preg_replace($pattern, INDICATOR_INLINEFIELD_TEXT . $newfill, $val);
            }
        }

        // return result
        return $val;
    }

    function showRefactorVariableRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        if ($vsid != '') { //refactor
            $_SESSION['VSID'] = $vsid;
            $variable = $survey->getVariableDescriptive($vsid);
            $old = $variable->getName();
            if ($old != loadvar(SETTING_NAME)) {

                $variable->setName(loadvar(SETTING_NAME));
                $variable->save();
                $new = $variable->getName();
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $generatevars = array();
                $generatevars[] = $variable;

                // check types
                $types = $survey->getTypes();
                $generatetypes = array();
                foreach ($types as $type) {
                    $sets = $type->getSettingsArray();
                    $changed = false;
                    foreach ($sets as $s) {
                        $val = $s->getValue();
                        if (!is_numeric($val) && !inArray($val, array(SETTING_FOLLOW_GENERIC, ""))) {
                            if (strtolower($old) == strtolower($val)) {
                                $newval = $new;
                            } else {
                                $newval = $this->updateVariableValue($old, $new, $val);
                            }
                            if ($newval != $val) {
                                $s->setValue($newval);
                                $changed = true;
                            }
                        }
                    }

                    if ($changed == true) {
                        $type->save();
                        $generatetypes[] = $type;
                    }
                }

                $generatevarsgetfills = array();
                foreach ($generatetypes as $gt) {
                    $vars = $survey->getVariableDescriptivesOfType($gt->getTyd());
                    foreach ($vars as $v) {
                        if (isset($generatevarsgetfills[$v->getVsid()]) == false) {
                            $generatevarsgetfills[$v->getVsid()] = $v;
                        }
                    }
                }

                // check variables                
                $generatesetfills = array();
                $allvars = $survey->getVariableDescriptives();
                foreach ($allvars as $var) {
                    if ($var->getVsid() != $variable->getVsid()) {
                        $sets = $var->getSettingsArray();
                        $changed = false;
                        $fillcodechanged = false;
                        foreach ($sets as $s) {
                            $val = $s->getValue();
                            if (!is_numeric($val) && !inArray($val, array(SETTING_FOLLOW_GENERIC, SETTING_FOLLOW_TYPE, ""))) {
                                $excluded = array();
                                if ($s->getName() == SETTING_FILLCODE) {
                                    $newval = excludeText($val, $newval);
                                    $newval = preg_replace("/\b" . $old . "\b/i", $new, $newval);
                                    $newval = includeText($newval, $excluded);
                                } else {
                                    if (strtolower($old) == strtolower($val)) {
                                        $newval = $new;
                                    } else {
                                        $newval = $this->updateVariableValue($old, $new, $val);
                                    }
                                }

                                if ($newval != $val) {
                                    if ($s->getName() == SETTING_FILLCODE) {
                                        $fillcodechanged = true;
                                    }
                                    $s->setValue($newval);
                                    $changed = true;
                                }
                            }
                        }

                        if ($changed == true) {
                            $var->save();
                            if (isset($generatevarsgetfills[$var->getVsid()]) == false) {
                                $generatevarsgetfills[$var->getVsid()] = $var;
                            }
                            if ($fillcodechanged == true) {
                                $generatesetfills[] = $var;
                            }
                        }
                    }
                }

                // check groups
                $generategroups = array();
                $groups = $survey->getGroups();
                foreach ($groups as $gr) {

                    $sets = $gr->getSettingsArray();
                    $changed = false;
                    $fillcodechanged = false;
                    foreach ($sets as $s) {
                        $val = $s->getValue();
                        if (!is_numeric($val) && !inArray($val, array(SETTING_FOLLOW_GENERIC, ""))) {
                            $newval = $this->updateVariableValue($old, $new, $val);
                            if ($newval != $val) {
                                $s->setValue($newval);
                                $changed = true;
                            }
                        }
                    }

                    if ($changed == true) {
                        $gr->save();
                        $generategroups[] = $gr;
                    }
                }

                // check routing
                $generate = array();
                $sections = $survey->getSections();
                foreach ($sections as $sect) {
                    $routing = $sect->getRouting();

                    $excluded = array();
                    $newrouting = excludeText($routing, $excluded);
                    $newrouting = preg_replace("/\b" . $old . "\b/i", $new, $newrouting);
                    $newrouting = includeText($newrouting, $excluded);

                    if ($newrouting != $routing) {
                        $sect->storeRouting($newrouting);
                        $sect->save();
                        $generate[] = $sect;
                    }
                }


                // recompile everything
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $messages[] = $compiler->generateVariableDescriptives($generatevars);

                if (sizeof($generatetypes) > 0) {
                    $messages[] = $compiler->generateTypes($generatetypes);
                }

                if (sizeof($generate) > 0) {
                    $messages[] = $compiler->generateSections($generate);
                }

                if (sizeof($generatesetfills) > 0) {
                    $messages[] = $compiler->generateSetFills($generatesetfills);
                }

                if (sizeof($generatevarsgetfills) > 0) {
                    $messages[] = $compiler->generateGetFills($generatevarsgetfills);
                }

                if (sizeof($generategroups) > 0) {
                    $messages[] = $compiler->generateGetFillsGroups($generategroups);
                }
                foreach ($generate as $gen) {
                    $messages[] = $compiler->generateEngine($gen->getSeid());
                    $messages[] = $compiler->generateGetFillsRouting($gen->getSeid());
                }
                return $displaySysAdmin->showEditVariable($_SESSION['VSID'], $displaySysAdmin->displaySuccess(Language::messageVariableRenamed($old, $new)));
            } else {
                return $displaySysAdmin->showRefactorVariable($_SESSION['VSID'], $displaySysAdmin->displayWarning(Language::messageVariableNotRenamed()));
            }
        } else {
            return $displaySysAdmin->showSection($_SESSION['SEID']);
        }
    }

    function showMoveVariable() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        return $displaySysAdmin->showMoveVariable($_SESSION['VSID']);
    }

    function showMoveVariableRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $vsid = getFromSessionParams('vsid');
        if ($vsid != '') { //move
            $variable = $survey->getVariableDescriptive($vsid);

            // determine survey
            $suid = $_SESSION['SUID'];
            if (isset($_POST['suid'])) {
                $suid = loadvar('suid');
            }

            /* actually moved */
            if ($suid != $_SESSION['SUID'] || $_SESSION['SEID'] != loadvar("section")) {
                $variable->move($suid, loadvar('section'));

                /* if moved survey, then need to recompile old and new survey */
                if ($suid != $_SESSION['SUID']) {

                    /* compile old survey if no copy made */
                    $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                    $mess = $compiler->generateVariableDescriptives(array($variable), true);
                    $mess = $compiler->generateSetFills(array($variable), true);
                    //$mess = $compiler->generateGetFills(array($variable), true);
                    //$mess = $compiler->generateInlineFields(array($variable), true);

                    /* update survey in session */
                    $_SESSION['SUID'] = $suid;

                    /* compile other survey */
                    $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                    $mess = $compiler->generateVariableDescriptives(array($variable));
                    $mess = $compiler->generateSetFills(array($variable));
                    $mess = $compiler->generateGetFills(array($variable));
                    $mess = $compiler->generateInlineFields(array($variable));
                }
                /* moved section, then recompile variable itself (for section header/footer) */ else if ($_SESSION['SEID'] != loadvar("section")) {
                    $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                    $mess = $compiler->generateVariableDescriptives(array($variable));
                }

                /* update section in session */
                $_SESSION['SEID'] = loadvar("section");
                $_SESSION['VSID'] = $variable->getVsid();

                // show variable again
                return $displaySysAdmin->showEditVariable($variable->getVsid(), $displaySysAdmin->displaySuccess(Language::messageVariableMoved($variable->getName())));
            } else {
                return $displaySysAdmin->showSection($_SESSION['SEID'], $displaySysAdmin->displayWarning(Language::messageVariableNotMoved($variable->getName())));
            }
        } else {
            return $displaySysAdmin->showSection($_SESSION['SEID']);
        }
    }

    /* group */

    function showAddGroup($content = "") {
        $displaySysAdmin = new DisplaySysAdmin();
        unset($_SESSION['GID']);
        unset($_SESSION['VRFILTERMODE_GROUP']);
        return $displaySysAdmin->showEditGroup("", $content);
    }

    function showCopyGroup() {
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        }
        $surveys = new Surveys();
        //if ($surveys->getNumberOfSurveys() > 1) {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showCopyGroup($_SESSION['GID']);
        //} else {
        //   return $this->showCopyGroupRes();
        //}
    }

    function showCopyGroupRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.group';


        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        }
        $gid = getFromSessionParams('gid');
        if ($gid != '') {
            $survey = new Survey($_SESSION['SUID']);
            $group = $survey->getGroup($gid);
            $suid = "";
            if (loadvar("suid") != "") {
                $suid = loadvar("suid");
            }
            $group->copy($suid, loadvar("includesuffix"));

            $_SESSION['GID'] = $group->getGid();

            if ($suid == "" || $suid == $_SESSION['SUID']) {
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateGroups(array($group));
            } else {
                $_SESSION['SUID'] = $suid;
                $survey = new Survey($_SESSION['SUID']);
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateGroups(array($group));
                $mess = $compiler->generateGetFillsGroups(array($group));
                $mess = $compiler->generateInlineFieldsGroups(array($group));
            }

            $displaySysAdmin = new DisplaySysAdmin();
            $content = $displaySysAdmin->displaySuccess(Language::messageGroupCopied($group->getName()));
            return $displaySysAdmin->showEditGroup($group->getGid(), $content);
        } else {
            $content = $displaySysAdmin->displayError(Language::messageGroupNotCopied($group->getName()));
            return $displaySysAdmin->showSection($content);
        }
    }

    function showRefactorGroup() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        }
        return $displaySysAdmin->showRefactorGroup($_SESSION['GID']);
    }

    function showRefactorGroupRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        if ($gid != '') { //refactor
            $_SESSION['GID'] = $gid;
            $group = $survey->getGroup($gid);
            $old = $group->getName();
            if ($old != loadvar(SETTING_NAME)) {
                $group->setName(loadvar(SETTING_NAME));
                $group->save();
                $new = $group->getName();
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $messages[] = $compiler->generateGroups(array($group));

                $generate = array();
                $sections = $survey->getSections();
                foreach ($sections as $sect) {
                    $routing = $sect->getRouting();

                    $excluded = array();
                    $newrouting = excludeText($routing, $excluded);
                    $newrouting = preg_replace("/\b" . $old . "\b/i", $new, $newrouting);
                    $newrouting = includeText($newrouting, $excluded);

                    if ($newrouting != $routing) {
                        $sect->storeRouting($newrouting);
                        $sect->save();
                        $generate[] = $sect;
                    }
                }

                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $messages[] = $compiler->generateSections($generate);
                foreach ($generate as $gen) {
                    $messages[] = $compiler->generateEngine($gen->getSeid());
                }
                return $displaySysAdmin->showEditGroup($_SESSION['GID'], $displaySysAdmin->displaySuccess(Language::messageGroupRenamed($old, $new)));
            } else {
                return $displaySysAdmin->showRefactorGroup($_SESSION['GID'], $displaySysAdmin->displayWarning(Language::messageGroupNotRenamed()));
            }
        } else {
            return $displaySysAdmin->showSection($content);
        }
    }

    function showMoveGroup() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        }
        return $displaySysAdmin->showMoveGroup($_SESSION['GID']);
    }

    function showMoveGroupRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        if ($gid != '') { //move
            $group = $survey->getGroup($gid);

            // determine survey
            $suid = $_SESSION['SUID'];
            if (isset($_POST['suid'])) {
                $suid = loadvar('suid');
            }

            /* actually moved */
            if ($suid != $_SESSION['SUID']) {
                $group->move($suid);

                /* compile old survey if no copy made */
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateGroups(array($group), true);
                $mess = $compiler->generateGetFillsGroups(array($group), true);
                $mess = $compiler->generateInlineFieldsGroups(array($group), true);

                /* update survey in session */
                $_SESSION['SUID'] = $suid;
                $_SESSION['GID'] = $group->getGid();

                /* compile other survey */
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateGroups(array($group));
                $mess = $compiler->generateGetFillsGroups(array($group));
                $mess = $compiler->generateInlineFieldsGroups(array($group));

                // show group again
                return $displaySysAdmin->showEditGroup($group->getGid(), $displaySysAdmin->displaySuccess(Language::messageGroupMoved($group->getName())));
            } else {
                return $displaySysAdmin->showSection($_SESSION['SEID'], $displaySysAdmin->displayWarning(Language::messageGroupNotMoved($group->getName())));
            }
        } else {
            return $displaySysAdmin->showSection($_SESSION['SEID']);
        }
    }

    function showEditGroup($gid = '') {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        } else if ($gid != "") {
            $_SESSION['GID'] = $gid;
        }
        if (loadvar("vrfiltermode_group") != '') {
            $_SESSION['VRFILTERMODE_GROUP'] = loadvar("vrfiltermode_group");
        }
        return $displaySysAdmin->showEditGroup($_SESSION['GID']);
    }

    function showEditGroupGeneralRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $_SESSION['GID'] = $gid;
        $content = "";
        if ($gid != '') { //edit
            $group = $survey->getGroup($gid);
            $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged(loadvar(SETTING_GROUP_NAME)));
        } else { //add group!
            if (loadvar(SETTING_GROUP_NAME) != "") {
                $group = new Group();
                $group->setSuid($_SESSION['SUID']);
                $_SESSION['GID'] = $group->getGid();
                $content = $displaySysAdmin->displaySuccess(Language::messageGroupAdded(loadvar(SETTING_GROUP_NAME)));
            }
        }

        $checker = new Checker($_SESSION['SUID']);
        if ($gid == '') {
            $checks = $checker->checkName(loadvar(SETTING_GROUP_NAME));
            if (sizeof($checks) > 0) {
                $content = implode("<br/>", $checks);
                return $this->showAddGroup($content);
            }
        }

        /* ADD ALL SORTS OF CHECKS!! */
        if ($gid != '' || loadvar(SETTING_GROUP_NAME) != "") {
            $group->setName(trim(loadvar(SETTING_GROUP_NAME)));
            $group->setTemplate(loadvar(SETTING_GROUP_TEMPLATE));

            if (loadvar(SETTING_GROUP_TEMPLATE) == TABLE_TEMPLATE_CUSTOM) {
                $group->setCustomTemplate(loadvarAllowHTML(SETTING_GROUP_CUSTOM_TEMPLATE));
            } else {
                $group->setCustomTemplate("");
            }

            if (Config::xiExtension()) {
                $group->setXiTemplate(loadvar(SETTING_GROUP_XI_TEMPLATE));
            }

            $group->save();

            $checker = new Checker($_SESSION['SUID']);
            $checks = $checker->checkGroup($group);
            if (sizeof($checks) > 0) {
                $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
            }
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        if (loadvar(SETTING_GROUP_TEMPLATE) == TABLE_TEMPLATE_CUSTOM) {
            $mess = $compiler->generateGetFillsGroups(array($group));
            $mess = $compiler->generateInlineFieldsGroups(array($group));
        }

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($gid != '') {
            return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
        } else {
            return $this->showSection($content);
        }
    }

    function showEditGroupValidationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;

        $group->setIfError(loadvar(SETTING_IFERROR));
        $group->setExclusive(loadvar(SETTING_GROUP_EXCLUSIVE));
        $group->setInclusive(loadvar(SETTING_GROUP_INCLUSIVE));

        $group->setMinimumRequired(loadvar(SETTING_GROUP_MINIMUM_REQUIRED));
        $group->setMaximumRequired(loadvar(SETTING_GROUP_MAXIMUM_REQUIRED));
        $group->setExactRequired(loadvar(SETTING_GROUP_EXACT_REQUIRED));
        $group->setUniqueRequired(loadvar(SETTING_GROUP_UNIQUE_REQUIRED));
        $group->setSameRequired(loadvar(SETTING_GROUP_SAME_REQUIRED));

        $group->setInputMaskCallback(loadvarAllowHTML(SETTING_INPUT_MASK_CALLBACK));

        $group->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkGroup($group);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        $mess = $compiler->generateGetFillsGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showEditGroupNavigationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;

        $group->setKeyboardBindingEnabled(loadvar(SETTING_KEYBOARD_BINDING_ENABLED));
        $group->setKeyboardBindingBack(loadvar(SETTING_KEYBOARD_BINDING_BACK));
        $group->setKeyboardBindingNext(loadvar(SETTING_KEYBOARD_BINDING_NEXT));
        $group->setKeyboardBindingDK(loadvar(SETTING_KEYBOARD_BINDING_DK));
        $group->setKeyboardBindingRF(loadvar(SETTING_KEYBOARD_BINDING_RF));
        $group->setKeyboardBindingNA(loadvar(SETTING_KEYBOARD_BINDING_NA));
        $group->setKeyboardBindingUpdate(loadvar(SETTING_KEYBOARD_BINDING_UPDATE));
        $group->setKeyboardBindingRemark(loadvar(SETTING_KEYBOARD_BINDING_REMARK));
        $group->setKeyboardBindingClose(loadvar(SETTING_KEYBOARD_BINDING_CLOSE));
        $group->setIndividualDKRFNA(loadvar(SETTING_DKRFNA));
        $group->setIndividualDKRFNAInline(loadvar(SETTING_DKRFNA_INLINE));
        $group->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkGroup($group);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        $mess = $compiler->generateGetFillsGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showEditGroupInteractiveRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;
        /* $group->setInlineJavascript(loadvarAllowHTML(SETTING_JAVASCRIPT_WITHIN_ELEMENT));
          $group->setPageJavascript(loadvarAllowHTML(SETTING_JAVASCRIPT_WITHIN_PAGE));
          if (loadvarAllowHTML(SETTING_SCRIPTS) != "") {
          $group->setScripts(loadvarAllowHTML(SETTING_SCRIPTS));
          }
          $group->setInlineStyle(loadvarAllowHTML(SETTING_STYLE_WITHIN_ELEMENT));
          $group->setPageStyle(loadvarAllowHTML(SETTING_STYLE_WITHIN_PAGE)); */

        $group->setOnBack(loadvar(SETTING_ON_BACK));
        $group->setOnNext(loadvar(SETTING_ON_NEXT));
        $group->setOnDK(loadvar(SETTING_ON_DK));
        $group->setOnRF(loadvar(SETTING_ON_RF));
        $group->setOnNA(loadvar(SETTING_ON_NA));
        $group->setOnUpdate(loadvar(SETTING_ON_UPDATE));
        $group->setOnLanguageChange(loadvar(SETTING_ON_LANGUAGE_CHANGE));
        $group->setOnModeChange(loadvar(SETTING_ON_MODE_CHANGE));
        $group->setOnVersionChange(loadvar(SETTING_ON_VERSION_CHANGE));

        $group->setClickBack(loadvar(SETTING_CLICK_BACK));
        $group->setClickNext(loadvar(SETTING_CLICK_NEXT));
        $group->setClickDK(loadvar(SETTING_CLICK_DK));
        $group->setClickRF(loadvar(SETTING_CLICK_RF));
        $group->setClickNA(loadvar(SETTING_CLICK_NA));
        $group->setClickUpdate(loadvar(SETTING_CLICK_UPDATE));
        $group->setClickLanguageChange(loadvar(SETTING_CLICK_LANGUAGE_CHANGE));
        $group->setClickModeChange(loadvar(SETTING_CLICK_MODE_CHANGE));
        $group->setClickVersionChange(loadvar(SETTING_CLICK_VERSION_CHANGE));

        $group->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkGroup($group);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showEditGroupOutputRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;

        $group->setScreendumpStorage(loadvar(SETTING_SCREENDUMPS));
        $group->setParadata(loadvar(SETTING_PARADATA));
        $group->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showEditGroupLayoutRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;

        $group->setPageHeader(loadvarAllowHTML(SETTING_PAGE_HEADER));
        $group->setPageFooter(loadvarAllowHTML(SETTING_PAGE_FOOTER));
        $group->setErrorPlacement(loadvar(SETTING_ERROR_PLACEMENT));

        $group->setButtonAlignment(loadvar(SETTING_BUTTON_ALIGNMENT));
        $ans = loadvar(SETTING_BUTTON_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $group->setButtonFormatting(implode("~", $ans));

        if (inArray($group->getTemplate(), array_keys(Common::surveyTableTemplates()))) {
            $group->setTableID(loadvar(SETTING_GROUP_TABLE_ID));
            $group->setTableBordered(loadvar(SETTING_GROUP_TABLE_BORDERED));
            $group->setTableCondensed(loadvar(SETTING_GROUP_TABLE_CONDENSED));
            $group->setTableHovered(loadvar(SETTING_GROUP_TABLE_HOVERED));
            $group->setTableStriped(loadvar(SETTING_GROUP_TABLE_STRIPED));
            $group->setTableWidth(loadvar(SETTING_TABLE_WIDTH));
            $group->setTableMobile(loadvar(SETTING_TABLE_MOBILE));
            $group->setTableMobileLabels(loadvar(SETTING_TABLE_MOBILE_LABELS));
            $group->setQuestionColumnWidth(loadvar(SETTING_QUESTION_COLUMN_WIDTH));
            //}        
            //if ($group->getTemplate() == TABLE_TEMPLATE_ENUMERATED) {
            $group->setHeaderAlignment(loadvar(SETTING_HEADER_ALIGNMENT));
            $ans = loadvar(SETTING_HEADER_FORMATTING);
            if (!is_array($ans)) {
                $ans = array($ans);
            }
            $group->setHeaderFormatting(implode("~", $ans));
            $group->setHeaderFixed(loadvar(SETTING_HEADER_FIXED));
            $group->setHeaderScrollDisplay(loadvar(SETTING_HEADER_SCROLL_DISPLAY));
        }

        if (in_array($group->getTemplate(), array_keys(Common::surveyTableMultiColumnTables()))) {
            $group->setTableHeaders(loadvarAllowHTML(SETTING_TABLE_HEADERS));
        } else if (in_array($group->getTemplate(), array_keys(Common::surveyTableEnumTables()))) {
            $group->setFooterDisplay(loadvar(SETTING_FOOTER_DISPLAY));
        }


        $group->setShowBackButton(loadvar(SETTING_BACK_BUTTON));
        $group->setShowNextButton(loadvar(SETTING_NEXT_BUTTON));
        $group->setShowDKButton(loadvar(SETTING_DK_BUTTON));
        $group->setShowRFButton(loadvar(SETTING_RF_BUTTON));
        $group->setShowUpdateButton(loadvar(SETTING_UPDATE_BUTTON));
        $group->setShowNAButton(loadvar(SETTING_NA_BUTTON));
        $group->setShowRemarkButton(loadvar(SETTING_REMARK_BUTTON));
        $group->setShowRemarkSaveButton(loadvar(SETTING_REMARK_SAVE_BUTTON));
        $group->setShowCloseButton(loadvar(SETTING_CLOSE_BUTTON));

        $group->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $group->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $group->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $group->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $group->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $group->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $group->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $group->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));
        $group->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));

        $group->setShowProgressBar(loadvar(SETTING_PROGRESSBAR_SHOW));
        $group->setProgressBarFillColor(loadvar(SETTING_PROGRESSBAR_FILLED_COLOR));
        $group->setProgressBarWidth(loadvar(SETTING_PROGRESSBAR_WIDTH));
        $group->setProgressBarValue(loadvar(SETTING_PROGRESSBAR_VALUE));
        $group->setMultiColumnQuestiontext(loadvar(SETTING_MULTICOLUMN_QUESTIONTEXT));

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkGroup($group);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }
        $group->save();

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        $mess = $compiler->generateGetFillsGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showEditGroupAccessRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        $group = $survey->getGroup($gid);
        $_SESSION['GID'] = $gid;
        $group->setAccessReturnAfterCompletionAction(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION));
        $group->setAccessReentryRedoPreload(loadvar(SETTING_ACCESS_REENTRY_PRELOAD_REDO));
        $group->setAccessReturnAfterCompletionRedoPreload(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO));
        $group->setAccessReentryAction(loadvar(SETTING_ACCESS_REENTRY_ACTION));
        $group->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkGroup($group);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showEditGroupAssistanceRes() {
        $displaySysAdmin = new DisplaySysAdmin();
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
        $group->setErrorMessageUniqueRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED));
        $group->setErrorMessageSameRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_SAME_REQUIRED));
        $group->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageGroupChanged($group->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkGroup($group);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGroups(array($group));
        $mess = $compiler->generateGetFillsGroups(array($group));

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditGroup($_SESSION['GID'], $content);
    }

    function showRemoveGroup() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('gid') != "") {
            $_SESSION['GID'] = getFromSessionParams('gid');
        }
        return $displaySysAdmin->showRemoveGroup($_SESSION['GID']);
    }

    function showRemoveGroupRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.section';

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $gid = getFromSessionParams('gid');
        if ($gid != '') { //edit
            $group = $survey->getGroup($gid);
            $group->remove();

            /* compile */
            $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
            $mess = $compiler->generateGroups(array($group), true);
            //$mess = $compiler->generateGetFillsGroups();

            return $displaySysAdmin->showSection($_SESSION['SEID'], $displaySysAdmin->displaySuccess(Language::messageGroupRemoved($group->getName())));
        } else {
            return $displaySysAdmin->showSection($_SESSION['SEID']);
        }
    }

    /* settings */

    function showSettings() {
        $returnStr = '';
        $displaySysAdmin = new DisplaySysAdmin();
        $returnStr .= $displaySysAdmin->showSettings();
        return $returnStr;
    }

    function showEditSettingsAccess() {
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 0;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $_SESSION['EDITSURVEY'] = 1;
        return $displaySysAdmin->showEditSettingsAccess();
    }

    function showEditSettingsAccessRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        //$survey->setAccessReturn(loadvar(SETTING_ACCESS_RETURN));
        $survey->setAccessReturnAfterCompletionAction(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION));
        $survey->setAccessReturnAfterCompletionRedoPreload(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO));
        $survey->setAccessReentryAction(loadvar(SETTING_ACCESS_REENTRY_ACTION));
        $survey->setAccessReentryRedoPreload(loadvar(SETTING_ACCESS_REENTRY_PRELOAD_REDO));
        $survey->setAccessDatesFrom(loadvar(SETTING_ACCESS_DATES_FROM));
        $survey->setAccessDatesTo(loadvar(SETTING_ACCESS_DATES_TO));
        $survey->setAccessTimesFrom(loadvar(SETTING_ACCESS_TIMES_FROM));
        $survey->setAccessTimesTo(loadvar(SETTING_ACCESS_TIMES_TO));
        $survey->setAccessType(loadvar(SETTING_ACCESS_TYPE));
        $ans = loadvar(SETTING_ACCESS_DEVICE);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $survey->setAccessDevice(implode("~", $ans));
        $survey->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageAccessSettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsAccess($content);
    }

    function showEditSettingsAssistance() {
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SETTING'] = 1;
        return $displaySysAdmin->showEditSettingsAssistance();
    }

    function showEditSettingsAssistanceRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        $survey->setLoginError(loadvarAllowHTML(SETTING_LOGIN_ERROR));
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
        $survey->setErrorMessageSameRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_SAME_REQUIRED));

        $survey->setErrorMessageEnumeratedEntered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED));
        $survey->setErrorMessageSetOfEnumeratedEntered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED));

        $survey->setErrorMessageComparisonEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO));
        $survey->setErrorMessageComparisonNotEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO));
        $survey->setErrorMessageComparisonGreaterEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO));
        $survey->setErrorMessageComparisonGreater(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_GREATER));
        $survey->setErrorMessageComparisonSmallerEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO));
        $survey->setErrorMessageComparisonSmaller(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER));
        $survey->setErrorMessageComparisonEqualToIgnoreCase(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE));
        $survey->setErrorMessageComparisonNotEqualToIgnoreCase(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE));

        $survey->save();

        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGetFillsSurvey();
        $mess = $compiler->generateInlineFieldsSurvey();
        $content = $displaySysAdmin->displaySuccess(Language::messageAssistanceSettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsAssistance($content);
    }

    function showEditSettingsData() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 2;
        return $displaySysAdmin->showEditSettingsData();
    }

    function showEditSettingsDataRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        $survey->setHidden(loadvar(SETTING_HIDDEN));
        $survey->setHiddenPaperVersion(loadvar(SETTING_HIDDEN_PAPER_VERSION));
        $survey->setHiddenRouting(loadvar(SETTING_HIDDEN_ROUTING));
        $survey->setHiddenTranslation(loadvar(SETTING_HIDDEN_TRANSLATION));
        $survey->setDataEncryptionKey(loadvar(SETTING_DATA_ENCRYPTION_KEY));
        $survey->setDataInputMask(loadvar(SETTING_DATA_INPUTMASK));
        $survey->setScreendumpStorage(loadvar(SETTING_SCREENDUMPS));
        $survey->setParadata(loadvar(SETTING_PARADATA));
        $survey->setDataKeepOnly(loadvar(SETTING_DATA_KEEP_ONLY));
        $survey->setDataKeep(loadvar(SETTING_DATA_KEEP));
        $survey->setDataSkipVariable(loadvar(SETTING_DATA_SKIP));
        $survey->setDataSkipVariablePostFix(loadvar(SETTING_DATA_SKIP_POSTFIX));
        $survey->setOutputSetOfEnumeratedBinary(loadvar(SETTING_OUTPUT_SETOFENUMERATED));
        $survey->setOutputValueLabelWidth(loadvar(SETTING_OUTPUT_VALUELABEL_WIDTH));
        $survey->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageDataSettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));
        return $displaySysAdmin->showEditSettingsData($content);
    }

    function showEditSettingsValidation() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 5;
        return $displaySysAdmin->showEditSettingsValidation();
    }

    function showEditSettingsValidationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        $survey->setIfEmpty(loadvar(SETTING_IFEMPTY));
        $survey->setIfError(loadvar(SETTING_IFERROR));
        $survey->setInputMaskEnabled(loadvar(SETTING_INPUT_MASK_ENABLED));
        $survey->setInputMaskPlaceholder(loadvar(SETTING_INPUT_MASK_PLACEHOLDER));

        $survey->setExclusive(loadvar(SETTING_GROUP_EXCLUSIVE));
        $survey->setExclusive(loadvar(SETTING_GROUP_INCLUSIVE));
        $survey->setExactRequired(loadvar(SETTING_GROUP_EXACT_REQUIRED));
        $survey->setMinimumRequired(loadvar(SETTING_GROUP_MINIMUM_REQUIRED));
        $survey->setMaximumRequired(loadvar(SETTING_GROUP_MAXIMUM_REQUIRED));
        $survey->setUniqueRequired(loadvar(SETTING_GROUP_UNIQUE_REQUIRED));
        $survey->setSameRequired(loadvar(SETTING_GROUP_SAME_REQUIRED));
        $survey->setValidateAssignment(loadvar(SETTING_VALIDATE_ASSIGNMENT));
        $survey->setApplyChecks(loadvar(SETTING_APPLY_CHECKS));
        $survey->save();

        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGetFillsSurvey();
        $mess = $compiler->generateInlineFieldsSurvey();
        $content = $displaySysAdmin->displaySuccess(Language::messageValidationSettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsValidation($content);
    }

    function showEditSettingsInteractive() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 8;
        return $displaySysAdmin->showEditSettingsInteractive();
    }

    function showEditSettingsInteractiveRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        $survey->setScripts(loadvarAllowHTML(SETTING_SCRIPTS));
        $survey->setOnBack(loadvar(SETTING_ON_BACK));
        $survey->setOnNext(loadvar(SETTING_ON_NEXT));
        $survey->setOnDK(loadvar(SETTING_ON_DK));
        $survey->setOnRF(loadvar(SETTING_ON_RF));
        $survey->setOnNA(loadvar(SETTING_ON_NA));
        $survey->setOnUpdate(loadvar(SETTING_ON_UPDATE));
        $survey->setOnLanguageChange(loadvar(SETTING_ON_LANGUAGE_CHANGE));
        $survey->setOnModeChange(loadvar(SETTING_ON_MODE_CHANGE));
        $survey->setOnVersionChange(loadvar(SETTING_ON_VERSION_CHANGE));

        $survey->setClickBack(loadvar(SETTING_CLICK_BACK));
        $survey->setClickNext(loadvar(SETTING_CLICK_NEXT));
        $survey->setClickDK(loadvar(SETTING_CLICK_DK));
        $survey->setClickRF(loadvar(SETTING_CLICK_RF));
        $survey->setClickNA(loadvar(SETTING_CLICK_NA));
        $survey->setClickUpdate(loadvar(SETTING_CLICK_UPDATE));
        $survey->setClickLanguageChange(loadvar(SETTING_CLICK_LANGUAGE_CHANGE));
        $survey->setClickModeChange(loadvar(SETTING_CLICK_MODE_CHANGE));
        $survey->setClickVersionChange(loadvar(SETTING_CLICK_VERSION_CHANGE));

        $survey->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageInteractiveSettingsChanged());

        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGetFillsSurvey();
        $mess = $compiler->generateInlineFieldsSurvey();

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsInteractive($content);
    }

    function showEditSettingsMode() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 7;
        return $displaySysAdmin->showEditSettingsMode();
    }

    function showEditSettingsModeRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $_SESSION['EDITSURVEY'] = 1;
        $ans = loadvar(SETTING_ALLOWED_MODES);
        if (!is_array($ans)) {
            $ans = array($ans);
        }

        if (!inArray(loadvar(SETTING_DEFAULT_MODE), $ans)) {
            $content = $displaySysAdmin->displayError(Language::messageModeSettingsNotChanged());
        } else {
            $current = explode("~", $survey->getAllowedModes());
            $survey->setDefaultMode(loadvar(SETTING_DEFAULT_MODE));
            $survey->setChangeMode(loadvar(SETTING_CHANGE_MODE));
            $survey->setReentryMode(loadvar(SETTING_REENTRY_MODE));
            $survey->setBackMode(loadvar(SETTING_BACK_MODE));
            $survey->setAllowedModes(implode("~", $ans));
            $content = $displaySysAdmin->displaySuccess(Language::messageModeSettingsChanged());
            $users = new Users();
            $users = $users->getUsers();
            $update = loadvar("uridsel");
            foreach ($users as $u) {
                foreach ($current as $c) {
                    if (!inArray($c, $ans)) {
                        $u->removeMode($_SESSION['SUID'], $c);
                    }
                }
                foreach ($ans as $a) {
                    if (!inArray($a, $current)) {
                        if (inArray($u->getUrid(), $update) || inArray(-1, $update)) {
                            $u->addMode($_SESSION['SUID'], $a, $survey->getAllowedLanguages($a));
                        }
                    }
                }
                $u->saveChanges();
            }

            if (!inArray(getSurveyMode(), $ans)) {
                $_SESSION['SURVEY_MODE'] = $ans[0];
            }
        }

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsMode($content);
    }

    function showEditSettingsGeneral() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 6;
        return $displaySysAdmin->showEditSettingsGeneral();
    }

    function showEditSettingsGeneralRes() {
        $_SESSION['EDITSURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $survey->setTitle(loadvar(SETTING_TITLE));
        $survey->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageGeneralSettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsGeneral($content);
    }

    function showEditSettingsLanguage() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 3;
        return $displaySysAdmin->showEditSettingsLanguage();
    }

    function showEditSettingsLanguageRes() {
        $_SESSION['EDITSURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);

        $ans = loadvar(SETTING_ALLOWED_LANGUAGES);
        if (!is_array($ans)) {
            $ans = array($ans);
        }

        if (!inArray(loadvar(SETTING_DEFAULT_LANGUAGE), $ans)) {
            $content = $displaySysAdmin->displayError(Language::messageLanguageSettingsNotChanged());
        } else {
            $current = explode("~", $survey->getAllowedLanguages(getSurveyMode()));
            $survey->setDefaultLanguage(loadvar(SETTING_DEFAULT_LANGUAGE));
            $survey->setChangeLanguage(loadvar(SETTING_CHANGE_LANGUAGE));
            $survey->setReentryLanguage(loadvar(SETTING_REENTRY_LANGUAGE));
            $survey->setBackLanguage(loadvar(SETTING_BACK_LANGUAGE));
            $survey->setAllowedLanguages(implode("~", $ans));
            $users = new Users();
            $users = $users->getUsers();
            $update = loadvar("uridsel");
            foreach ($users as $u) {

                foreach ($current as $c) {
                    if (!inArray($c, $ans)) {
                        $u->removeLanguage($_SESSION['SUID'], getSurveyMode(), $c);
                    }
                }

                foreach ($ans as $a) {
                    if (!inArray($a, $current)) {
                        if (inArray($u->getUrid(), $update) || inArray(-1, $update)) {
                            $u->addLanguage($_SESSION['SUID'], getSurveyMode(), $a);
                        }
                    }
                }
                $u->saveChanges();
            }
            $content = $displaySysAdmin->displaySuccess(Language::messageLanguageSettingsChanged());

            if (!inArray(getSurveyLanguage(), $ans)) {
                $_SESSION['SURVEY_LANGUAGE'] = $ans[0];
            }
        }

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsLanguage($content);
    }

    function showEditSettingsNavigation() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 9;
        return $displaySysAdmin->showEditSettingsNavigation();
    }

    function showEditSettingsNavigationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['EDITSURVEY'] = 1;
        $survey = new Survey($_SESSION['SUID']);
        $survey->setKeyboardBindingEnabled(loadvar(SETTING_KEYBOARD_BINDING_ENABLED));
        $survey->setKeyboardBindingBack(loadvar(SETTING_KEYBOARD_BINDING_BACK));
        $survey->setKeyboardBindingNext(loadvar(SETTING_KEYBOARD_BINDING_NEXT));
        $survey->setKeyboardBindingDK(loadvar(SETTING_KEYBOARD_BINDING_DK));
        $survey->setKeyboardBindingRF(loadvar(SETTING_KEYBOARD_BINDING_RF));
        $survey->setKeyboardBindingNA(loadvar(SETTING_KEYBOARD_BINDING_NA));
        $survey->setKeyboardBindingUpdate(loadvar(SETTING_KEYBOARD_BINDING_UPDATE));
        $survey->setKeyboardBindingRemark(loadvar(SETTING_KEYBOARD_BINDING_REMARK));
        $survey->setKeyboardBindingClose(loadvar(SETTING_KEYBOARD_BINDING_CLOSE));
        $survey->setIndividualDKRFNA(loadvar(SETTING_DKRFNA));
        $survey->setIndividualDKRFNASingle(loadvar(SETTING_DKRFNA_SINGLE));
        $survey->setIndividualDKRFNAInline(loadvar(SETTING_DKRFNA_INLINE));

        $survey->setTimeout(loadvar(SETTING_TIMEOUT));
        $survey->setTimeoutLength(loadvar(SETTING_TIMEOUT_LENGTH));
        $survey->setTimeoutTitle(loadvar(SETTING_TIMEOUT_TITLE));
        $survey->setTimeoutAliveButton(loadvar(SETTING_TIMEOUT_ALIVE_BUTTON));
        $survey->setTimeoutLogoutButton(loadvar(SETTING_TIMEOUT_LOGOUT_BUTTON));
        $survey->setTimeoutLogoutURL(loadvar(SETTING_TIMEOUT_LOGOUT));
        $survey->setTimeoutRedirectURL(loadvar(SETTING_TIMEOUT_REDIRECT));

        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGetFillsSurvey();
        $mess = $compiler->generateInlineFieldsSurvey();

        $survey->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageNavigationSettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsNavigation($content);
    }

    function showEditSettingsLayout() {
        $_SESSION['EDITSURVEY'] = 1;
        $_SESSION['VRFILTERMODE_SURVEY'] = 1;
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['VRFILTERMODE_SETTING'] = 4;
        return $displaySysAdmin->showEditSettingsLayout();
    }

    function showEditSettingsLayoutRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $_SESSION['EDITSURVEY'] = 1;
        $survey = new Survey($_SESSION['SUID']);
        $survey->setPageHeader(loadvarAllowHTML(SETTING_PAGE_HEADER));
        $survey->setPageFooter(loadvarAllowHTML(SETTING_PAGE_FOOTER));
        $survey->setQuestionAlignment(loadvar(SETTING_QUESTION_ALIGNMENT));
        $ans = loadvar(SETTING_QUESTION_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $survey->setQuestionFormatting(implode("~", $ans));
        $survey->setAnswerAlignment(loadvar(SETTING_ANSWER_ALIGNMENT));
        $ans = loadvar(SETTING_ANSWER_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $survey->setAnswerFormatting(implode("~", $ans));
        $survey->setButtonAlignment(loadvar(SETTING_BUTTON_ALIGNMENT));
        $ans = loadvar(SETTING_BUTTON_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $survey->setButtonFormatting(implode("~", $ans));

        $survey->setShowBackButton(loadvar(SETTING_BACK_BUTTON));
        $survey->setShowNextButton(loadvar(SETTING_NEXT_BUTTON));
        $survey->setShowDKButton(loadvar(SETTING_DK_BUTTON));
        $survey->setShowRFButton(loadvar(SETTING_RF_BUTTON));
        $survey->setShowUpdateButton(loadvar(SETTING_UPDATE_BUTTON));
        $survey->setShowNAButton(loadvar(SETTING_NA_BUTTON));
        $survey->setShowRemarkButton(loadvar(SETTING_REMARK_BUTTON));
        $survey->setShowCloseButton(loadvar(SETTING_CLOSE_BUTTON));
        $survey->setShowRemarkSaveButton(loadvar(SETTING_REMARK_SAVE_BUTTON));

        $survey->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $survey->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $survey->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $survey->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $survey->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $survey->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $survey->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $survey->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));
        $survey->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));

        $survey->setShowProgressBar(loadvar(SETTING_PROGRESSBAR_SHOW));
        $survey->setProgressBarType(loadvar(SETTING_PROGRESSBAR_TYPE));
        $survey->setProgressBarFillColor(loadvar(SETTING_PROGRESSBAR_FILLED_COLOR));
        $survey->setProgressBarWidth(loadvar(SETTING_PROGRESSBAR_WIDTH));
        $survey->setTemplate(loadvar(SETTING_SURVEY_TEMPLATE));
        $survey->setErrorPlacement(loadvar(SETTING_ERROR_PLACEMENT));

        $survey->setHeaderAlignment(loadvar(SETTING_HEADER_ALIGNMENT));
        $ans = loadvar(SETTING_HEADER_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $survey->setTableWidth(loadvar(SETTING_TABLE_WIDTH));
        $survey->setQuestionColumnWidth(loadvar(SETTING_QUESTION_COLUMN_WIDTH));
        $survey->setHeaderFormatting(implode("~", $ans));
        $survey->setHeaderFixed(loadvar(SETTING_HEADER_FIXED));
        $survey->setHeaderScrollDisplay(loadvar(SETTING_HEADER_SCROLL_DISPLAY));
        $survey->setTableBordered(loadvar(SETTING_GROUP_TABLE_BORDERED));
        $survey->setTableCondensed(loadvar(SETTING_GROUP_TABLE_CONDENSED));
        $survey->setTableHovered(loadvar(SETTING_GROUP_TABLE_HOVERED));
        $survey->setTableStriped(loadvar(SETTING_GROUP_TABLE_STRIPED));
        $survey->setTableMobile(loadvar(SETTING_TABLE_MOBILE));
        $survey->setTableMobileLabels(loadvar(SETTING_TABLE_MOBILE_LABELS));

        $survey->setEnumeratedDisplay(loadvar(SETTING_ENUMERATED_ORIENTATION));
        $survey->setEnumeratedBordered(loadvar(SETTING_ENUMERATED_BORDERED));
        $survey->setEnumeratedSplit(loadvar(SETTING_ENUMERATED_SPLIT));
        $survey->setEnumeratedTextbox(loadvar(SETTING_ENUMERATED_TEXTBOX));
        $survey->setEnumeratedTextboxLabel(loadvar(SETTING_ENUMERATED_TEXTBOX_LABEL));
        $survey->setEnumeratedTextboxPosttext(loadvar(SETTING_ENUMERATED_TEXTBOX_POSTTEXT));
        $survey->setEnumeratedLabel(loadvar(SETTING_ENUMERATED_LABEL));
        $survey->setHeaderAlignment(loadvar(SETTING_HEADER_ALIGNMENT));
        $ans = loadvar(SETTING_HEADER_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $survey->setHeaderFormatting(implode("~", $ans));
        $survey->setEnumeratedOrder(loadvar(SETTING_ENUMERATED_ORDER));
        $survey->setSetOfEnumeratedRanking(loadvar(SETTING_SETOFENUMERATED_RANKING));
        
        $survey->setSliderOrientation(loadvar(SETTING_SLIDER_ORIENTATION));
        $survey->setIncrement(loadvar(SETTING_SLIDER_INCREMENT));
        $survey->setTooltip(loadvar(SETTING_SLIDER_TOOLTIP));
        $survey->setTextbox(loadvar(SETTING_SLIDER_TEXTBOX));
        $survey->setTextboxLabel(loadvar(SETTING_SLIDER_TEXTBOX_LABEL));
        $survey->setTextboxPosttext(loadvar(SETTING_SLIDER_TEXTBOX_POSTTEXT));
        $survey->setComboBoxNothingLabel(loadvar(SETTING_COMBOBOX_DEFAULT));
        $survey->setSliderLabelPlacement(loadvar(SETTING_SLIDER_LABEL_PLACEMENT));
        $survey->setSliderPreSelection(loadvar(SETTING_SLIDER_PRESELECTION));
        $survey->setShowSectionHeader(loadvar(SETTING_SHOW_SECTION_HEADER));
        $survey->setShowSectionFooter(loadvar(SETTING_SHOW_SECTION_FOOTER));

        $survey->setMultiColumnQuestiontext(loadvar(SETTING_MULTICOLUMN_QUESTIONTEXT));
        $survey->save();

        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateGetFillsSurvey();
        $mess = $compiler->generateInlineFieldsSurvey();
        $content = $displaySysAdmin->displaySuccess(Language::messageDisplaySettingsChanged());

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditSettingsLayout($content);
    }

    /* type */

    function showAddType($content = "") {
        $displaySysAdmin = new DisplaySysAdmin();
        unset($_SESSION['TYD']);
        return $displaySysAdmin->showEditType("", $content);
    }

    function showRefactorType() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('tyd') != "") {
            $_SESSION['TYD'] = getFromSessionParams('tyd');
        }
        return $displaySysAdmin->showRefactorType($_SESSION['TYD']);
    }

    function showRefactorTypeRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        if ($tyd != '') { //refactor
            $_SESSION['TYD'] = $tyd;
            $type = $survey->getType($tyd);
            $old = $type->getName();

            if ($old != loadvar(SETTING_NAME)) {
                $type->setName(loadvar(SETTING_NAME));
                $type->save();
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $messages[] = $compiler->generateTypes(array($type));
                return $displaySysAdmin->showEditType($_SESSION['TYD'], $displaySysAdmin->displaySuccess(Language::messageTypeRenamed($old, $type->getName())));
            } else {
                return $displaySysAdmin->showRefactorType($_SESSION['TYD'], $displaySysAdmin->displayWarning(Language::messageTypeNotRenamed()));
            }
        } else {
            return $displaySysAdmin->showSurvey($content);
        }
    }

    function showMoveType() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('tyd') != "") {
            $_SESSION['TYD'] = getFromSessionParams('tyd');
        }
        return $displaySysAdmin->showMoveType($_SESSION['TYD']);
    }

    function showMoveTypeRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        if ($tyd != '') { //move
            $type = $survey->getType($tyd);

            // determine survey
            $suid = $_SESSION['SUID'];
            if (isset($_POST['suid'])) {
                $suid = loadvar('suid');
            }

            /* actually moved */
            if ($suid != $_SESSION['SUID']) {
                $type->move($suid);

                /* compile old survey if no copy made */
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateTypes(array($type), true);
                $vars = $survey->getVariableDescriptivesOfType($tyd);
                $mess = $compiler->generateVariableDescriptives($vars);
                $mess = $compiler->generateGetFills($vars);

                /* update survey in session */
                $_SESSION['SUID'] = $suid;
                $_SESSION['TYD'] = $type->getTyd();

                /* compile other survey */
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateTypes(array($type));
                $vars = $survey->getVariableDescriptivesOfType($tyd);
                $mess = $compiler->generateVariableDescriptives($vars);
                $mess = $compiler->generateGetFills($vars);

                // show type again
                return $displaySysAdmin->showEditType($type->getTyd(), $displaySysAdmin->displaySuccess(Language::messageTypeMoved($type->getName())));
            } else {
                return $displaySysAdmin->showSurvey($displaySysAdmin->displayWarning(Language::messageTypeNotMoved($type->getName())));
            }
        } else {
            return $displaySysAdmin->showSurvey();
        }
    }

    function showEditType() {
        $displaySysAdmin = new DisplaySysAdmin();
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
        } else if (inArray($answertype, array(ANSWER_TYPE_NONE)) && !inArray($_SESSION['VRFILTERMODE_TYPE'], array(0, 2, 5, 8))) {
            $_SESSION['VRFILTERMODE_TYPE'] = 0;
        }
        return $displaySysAdmin->showEditType($_SESSION['TYD']);
    }

    function showEditTypeGeneralRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $content = "";
        if ($tyd != '') { //edit
            $type = $survey->getType($tyd);
            $_SESSION['TYD'] = $tyd;
            $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged(loadvar(SETTING_NAME)));
        } else { //add section!
            if (loadvar(SETTING_NAME) != "") {
                $type = new Type();
                $type->setSuid($_SESSION['SUID']);
                $_SESSION['TYD'] = $type->getTyd();
                $content = $displaySysAdmin->displaySuccess(Language::messageTypeAdded(loadvar(SETTING_NAME)));
            }
        }

        $checker = new Checker($_SESSION['SUID']);
        if ($tyd == '') {
            $checks = $checker->checkTypeName(loadvar(SETTING_NAME));
            if (sizeof($checks) > 0) {
                $content = implode("<br/>", $checks);
                return $this->showAddType($content);
            }
        }

        //ADD ALL SORTS OF CHECKS!!
        if ($tyd != '' || loadvar(SETTING_NAME) != "") {
            $type->setName(trim(loadvar(SETTING_NAME)));
            $type->setAnswerType(loadvar(SETTING_ANSWERTYPE));

            // check custom answer type
            $tocall = str_replace('"', "'", trim(loadvar(SETTING_ANSWERTYPE_CUSTOM)));
            if ($tocall != "") {
                $removed = array();
                $test = excludeText($tocall, $removed);
                if (stripos($test, '(') !== false) {
                    $parameters = rtrim(substr($test, stripos($test, '(') + 1), ')');
                    $parameters = preg_split("/[\s,]+/", $parameters);

                    foreach ($parameters as $p) {
                        $t = str_replace(INDICATOR_FILL, "", $p);
                        $t = str_replace(INDICATOR_FILL_NOVALUE, "", $t);
                        $vr = $survey->getVariableDescriptiveByName($t);
                        if ($vr->getVsid() != "") {
                            // variable reference ok
                        } else if (is_numeric($t)) {
                            // number ok
                        } /* else if (startsWith($t, '"') && endsWith($t, '"')) {
                          // quoted text ok
                          } else if (startsWith($t, "'") && endsWith($t, "'")) {
                          // quoted text ok
                          } */ else {
                            if (stripos($t, '(') !== false) {
                                $t = str_replace('(', '', $t);
                                $t = str_replace(')', '', $t);
                                $checks[] = "Parameter function call " . $t . " not allowed";
                            } else {
                                //$checks[] = $displaySysAdmin->displayError("Parameter '" . $t . "' must be a variable reference");
                            }
                        }
                    }
                    $tocheck = substr($test, 0, stripos($test, '('));
                } else {
                    $tocheck = $tocall;
                }

                // check against allowed custom answer functions
                if (inArray($tocheck, getAllowedCustomAnswerFunctions()) && !inArray($tocheck, getForbiddenCustomAnswerFunctions())) {
                    // ok
                    $type->setAnswerTypeCustom($tocall);
                } else {
                    $checks[] = Language::messageCheckerFunctionNotAllowed($tocheck);
                }

                if (sizeof($checks) > 0) {
                    $content = $displaySysAdmin->displayError(implode("<br/>", $checks));
                    return $displaySysAdmin->showEditType($tyd, $content);
                }
            }
            // end check custom answer type

            $type->setOptionsText(loadvarAllowHTML(SETTING_OPTIONS));
            $type->setArray(loadvar(SETTING_ARRAY));
            $type->setKeep(loadvar(SETTING_KEEP));

            $answertype = loadvar(SETTING_ANSWERTYPE);
            if (inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                $type->setHidden(HIDDEN_YES);
            } else {
                $type->setHidden(loadvar(SETTING_HIDDEN));
            }

            if ($type->getInputMask() == "") {
                switch ($answertype) {
                    case ANSWER_TYPE_INTEGER:
                        $type->setInputMask(INPUTMASK_INTEGER);
                        break;
                    case ANSWER_TYPE_DOUBLE:
                        $type->setInputMask(INPUTMASK_DOUBLE);
                        break;
                    case ANSWER_TYPE_RANGE:
                        $type->setInputMask(INPUTMASK_INTEGER);
                        break;
                    default:
                        $type->setInputMask(null);
                        break;
                }
            }

            $type->save();

            $checker = new Checker($_SESSION['SUID']);
            $checks = $checker->checkType($type);
            if (sizeof($checks) > 0) {
                $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
            }
        }


        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));

        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($_SESSION['TYD']);
        $mess = $compiler->generateVariableDescriptives($vars);
        $mess = $compiler->generateGetFills($vars);
        $mess = $compiler->generateInlineFields($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        if ($tyd != '') {
            return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
        } else {
            return $displaySysAdmin->showSurvey($content);
        }
    }

    function showEditTypeInteractiveRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;
        $type->setInlineJavascript(loadvarAllowHTML(SETTING_JAVASCRIPT_WITHIN_ELEMENT));
        $type->setPageJavascript(loadvarAllowHTML(SETTING_JAVASCRIPT_WITHIN_PAGE));
        if (loadvarAllowHTML(SETTING_SCRIPTS) != "") {
            $type->setScripts(loadvarAllowHTML(SETTING_SCRIPTS));
        }
        $type->setInlineStyle(loadvarAllowHTML(SETTING_STYLE_WITHIN_ELEMENT));
        $type->setPageStyle(loadvarAllowHTML(SETTING_STYLE_WITHIN_PAGE));

        $type->setOnBack(loadvar(SETTING_ON_BACK));
        $type->setOnNext(loadvar(SETTING_ON_NEXT));
        $type->setOnDK(loadvar(SETTING_ON_DK));
        $type->setOnRF(loadvar(SETTING_ON_RF));
        $type->setOnNA(loadvar(SETTING_ON_NA));
        $type->setOnUpdate(loadvar(SETTING_ON_UPDATE));
        $type->setOnLanguageChange(loadvar(SETTING_ON_LANGUAGE_CHANGE));
        $type->setOnModeChange(loadvar(SETTING_ON_MODE_CHANGE));
        $type->setOnVersionChange(loadvar(SETTING_ON_VERSION_CHANGE));

        $type->setClickBack(loadvar(SETTING_CLICK_BACK));
        $type->setClickNext(loadvar(SETTING_CLICK_NEXT));
        $type->setClickDK(loadvar(SETTING_CLICK_DK));
        $type->setClickRF(loadvar(SETTING_CLICK_RF));
        $type->setClickNA(loadvar(SETTING_CLICK_NA));
        $type->setClickUpdate(loadvar(SETTING_CLICK_UPDATE));
        $type->setClickLanguageChange(loadvar(SETTING_CLICK_LANGUAGE_CHANGE));
        $type->setClickModeChange(loadvar(SETTING_CLICK_MODE_CHANGE));
        $type->setClickVersionChange(loadvar(SETTING_CLICK_VERSION_CHANGE));

        $type->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

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
        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showEditTypeValidationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;

        /* save validation settings based on answer type */
        $t = $type->getAnswerType();
        if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_RANK, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_OPEN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB, ANSWER_TYPE_CALENDAR))) {
            switch ($t) {
                case ANSWER_TYPE_ENUMERATED:
                    $type->setInlineExactRequired(loadvar(SETTING_INLINE_EXACT_REQUIRED));
                    $type->setInlineExclusive(loadvar(SETTING_INLINE_EXCLUSIVE));
                    $type->setInlineInclusive(loadvar(SETTING_INLINE_INCLUSIVE));
                    $type->setInlineMinimumRequired(loadvar(SETTING_INLINE_MINIMUM_REQUIRED));
                    $type->setInlineMaximumRequired(loadvar(SETTING_INLINE_MAXIMUM_REQUIRED));
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $type->setInlineExactRequired(loadvar(SETTING_INLINE_EXACT_REQUIRED));
                    $type->setInlineExclusive(loadvar(SETTING_INLINE_EXCLUSIVE));
                    $type->setInlineInclusive(loadvar(SETTING_INLINE_INCLUSIVE));
                    $type->setInlineMinimumRequired(loadvar(SETTING_INLINE_MINIMUM_REQUIRED));
                    $type->setInlineMaximumRequired(loadvar(SETTING_INLINE_MAXIMUM_REQUIRED));
                /* fall through */
                case ANSWER_TYPE_MULTIDROPDOWN;
                    $type->setMinimumSelected(loadvar(SETTING_MINIMUM_SELECTED));
                    $type->setMaximumSelected(loadvar(SETTING_MAXIMUM_SELECTED));
                    $type->setExactSelected(loadvar(SETTING_EXACT_SELECTED));
                    $type->setInvalidSelected(loadvar(SETTING_INVALID_SELECTED));
                    $type->setInvalidSubSelected(loadvar(SETTING_INVALIDSUB_SELECTED));
                    break;
                case ANSWER_TYPE_RANK;
                    $type->setMinimumRanked(loadvar(SETTING_MINIMUM_RANKED));
                    $type->setMaximumRanked(loadvar(SETTING_MAXIMUM_RANKED));
                    $type->setExactRanked(loadvar(SETTING_EXACT_RANKED));
                    break;
                case ANSWER_TYPE_OPEN:
                /* fall through */
                case ANSWER_TYPE_STRING:
                    $type->setMinimumLength(loadvar(SETTING_MINIMUM_LENGTH));
                    $type->setMaximumLength(loadvar(SETTING_MAXIMUM_LENGTH));
                    $type->setMinimumWords(loadvar(SETTING_MINIMUM_WORDS));
                    $type->setMaximumWords(loadvar(SETTING_MAXIMUM_WORDS));
                    $type->setPattern(loadvar(SETTING_PATTERN));
                    break;
                case ANSWER_TYPE_RANGE:
                    $minimum = loadvar(SETTING_MINIMUM_RANGE);
                    $maximum = loadvar(SETTING_MAXIMUM_RANGE);
                    $others = loadvar(SETTING_OTHER_RANGE);
                    if (!(contains($minimum, ".") || contains($maximum, ".") || contains($others, "."))) {
                        if ($type->getInputMask() == "") {
                            $type->setInputMask(INPUTMASK_INTEGER);
                        }
                    } else {
                        if ($type->getInputMask() == "") {
                            $type->setInputMask(INPUTMASK_DOUBLE);
                        }
                    }
                    $type->setOtherValues($others);
                /* fall through */
                case ANSWER_TYPE_KNOB:
                    $type->setMinimum(loadvar(SETTING_MINIMUM_RANGE));
                    $type->setMaximum(loadvar(SETTING_MAXIMUM_RANGE));
                    break;
                case ANSWER_TYPE_SLIDER:
                    $type->setMinimum(loadvar(SETTING_MINIMUM_RANGE));
                    $type->setMaximum(loadvar(SETTING_MAXIMUM_RANGE));
                    break;
                case ANSWER_TYPE_CALENDAR:
                    $type->setMaximumDatesSelected(loadvar(SETTING_MAXIMUM_CALENDAR));
                    break;
                case ANSWER_TYPE_CUSTOM:
                    $minimum = loadvar(SETTING_MINIMUM_RANGE);
                    $maximum = loadvar(SETTING_MAXIMUM_RANGE);
                    $others = loadvar(SETTING_OTHER_RANGE);
                    if (!(contains($minimum, ".") || contains($maximum, ".") || contains($others, "."))) {
                        if ($var->getInputMask() == "") {
                            $var->setInputMask(INPUTMASK_INTEGER);
                        }
                    } else {
                        if ($var->getInputMask() == "") {
                            $var->setInputMask(INPUTMASK_DOUBLE);
                        }
                    }
                    $var->setMinimum(loadvar(SETTING_MINIMUM_RANGE));
                    $var->setMaximum(loadvar(SETTING_MAXIMUM_RANGE));
                    $var->setOtherValues($others);
                    break;
            }
        }

        if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $type->setComparisonEqualTo(loadvar(SETTING_COMPARISON_EQUAL_TO));
            $type->setComparisonNotEqualTo(loadvar(SETTING_COMPARISON_NOT_EQUAL_TO));
            $type->setComparisonGreaterEqualTo(loadvar(SETTING_COMPARISON_GREATER_EQUAL_TO));
            $type->setComparisonGreater(loadvar(SETTING_COMPARISON_GREATER));
            $type->setComparisonSmallerEqualTo(loadvar(SETTING_COMPARISON_SMALLER_EQUAL_TO));
            $type->setComparisonSmaller(loadvar(SETTING_COMPARISON_SMALLER));
        }
        /* string comparisons */ if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $type->setComparisonEqualTo(loadvar(SETTING_COMPARISON_EQUAL_TO));
            $type->setComparisonNotEqualTo(loadvar(SETTING_COMPARISON_NOT_EQUAL_TO));
            $type->setComparisonEqualToIgnoreCase(loadvar(SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE));
            $type->setComparisonNotEqualToIgnoreCase(loadvar(SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE));
        }

        if (inArray($t, array(ANSWER_TYPE_CUSTOM, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_STRING, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_DATE, ANSWER_TYPE_TIME, ANSWER_TYPE_DATETIME, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            $type->setInputMaskEnabled(loadvar(SETTING_INPUT_MASK_ENABLED));
            $type->setInputMask(loadvar(SETTING_INPUT_MASK));
            $type->setInputMaskPlaceholder(loadvar(SETTING_INPUT_MASK_PLACEHOLDER));
            $type->setInputMaskCustom(loadvarAllowHTML(SETTING_INPUT_MASK_CUSTOM));
            $type->setInputMaskCallback(loadvarAllowHTML(SETTING_INPUT_MASK_CALLBACK));
        }

        $type->setIfEmpty(loadvar(SETTING_IFEMPTY));
        $type->setIfError(loadvar(SETTING_IFERROR));
        $type->setValidateAssignment(loadvar(SETTING_VALIDATE_ASSIGNMENT));
        $type->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);
        $mess = $compiler->generateGetFills($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showEditTypeLayoutRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;

        $type->setPageHeader(loadvarAllowHTML(SETTING_PAGE_HEADER));
        $type->setPageFooter(loadvarAllowHTML(SETTING_PAGE_FOOTER));
        $type->setPlaceholder(loadvarAllowHTML(SETTING_PLACEHOLDER));

        $type->setQuestionAlignment(loadvar(SETTING_QUESTION_ALIGNMENT));
        $ans = loadvar(SETTING_QUESTION_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $type->setQuestionFormatting(implode("~", $ans));
        $type->setAnswerAlignment(loadvar(SETTING_ANSWER_ALIGNMENT));
        $ans = loadvar(SETTING_ANSWER_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $type->setAnswerFormatting(implode("~", $ans));
        $type->setButtonAlignment(loadvar(SETTING_BUTTON_ALIGNMENT));
        $ans = loadvar(SETTING_BUTTON_FORMATTING);
        if (!is_array($ans)) {
            $ans = array($ans);
        }
        $type->setButtonFormatting(implode("~", $ans));
        $type->setErrorPlacement(loadvar(SETTING_ERROR_PLACEMENT));

        $type->setShowBackButton(loadvar(SETTING_BACK_BUTTON));
        $type->setShowNextButton(loadvar(SETTING_NEXT_BUTTON));
        $type->setShowDKButton(loadvar(SETTING_DK_BUTTON));
        $type->setShowRFButton(loadvar(SETTING_RF_BUTTON));
        $type->setShowUpdateButton(loadvar(SETTING_UPDATE_BUTTON));
        $type->setShowNAButton(loadvar(SETTING_NA_BUTTON));
        $type->setShowRemarkButton(loadvar(SETTING_REMARK_BUTTON));
        $type->setShowCloseButton(loadvar(SETTING_CLOSE_BUTTON));
        $type->setShowRemarkSaveButton(loadvar(SETTING_REMARK_SAVE_BUTTON));

        $type->setLabelBackButton(loadvarAllowHTML(SETTING_BACK_BUTTON_LABEL));
        $type->setLabelNextButton(loadvarAllowHTML(SETTING_NEXT_BUTTON_LABEL));
        $type->setLabelDKButton(loadvarAllowHTML(SETTING_DK_BUTTON_LABEL));
        $type->setLabelRFButton(loadvarAllowHTML(SETTING_RF_BUTTON_LABEL));
        $type->setLabelUpdateButton(loadvarAllowHTML(SETTING_UPDATE_BUTTON_LABEL));
        $type->setLabelNAButton(loadvarAllowHTML(SETTING_NA_BUTTON_LABEL));
        $type->setLabelRemarkButton(loadvarAllowHTML(SETTING_REMARK_BUTTON_LABEL));
        $type->setLabelCloseButton(loadvarAllowHTML(SETTING_CLOSE_BUTTON_LABEL));
        $type->setLabelRemarkSaveButton(loadvarAllowHTML(SETTING_REMARK_SAVE_BUTTON_LABEL));

        $type->setShowProgressBar(loadvar(SETTING_PROGRESSBAR_SHOW));
        $type->setProgressBarFillColor(loadvar(SETTING_PROGRESSBAR_FILLED_COLOR));
        $type->setProgressBarWidth(loadvar(SETTING_PROGRESSBAR_WIDTH));

        $answertype = $type->getAnswerType();

        if ($answertype == ANSWER_TYPE_TIME) {
            $type->setTimeFormat(loadvar(SETTING_TIME_FORMAT));
            //$type->setDateDefaultView(loadvar(SETTING_DATE_DEFAULT_VIEW));
        } else if ($answertype == ANSWER_TYPE_DATE) {
            $type->setDateFormat(loadvar(SETTING_DATE_FORMAT));
            $type->setDateDefaultView(loadvar(SETTING_DATE_DEFAULT_VIEW));
        } else if ($answertype == ANSWER_TYPE_DATETIME) {
            $type->setDateTimeFormat(loadvar(SETTING_DATETIME_FORMAT));
            $type->setDateDefaultView(loadvar(SETTING_DATE_DEFAULT_VIEW));
            $type->setDateTimeCollapse(loadvar(SETTING_DATETIME_COLLAPSE));
            $type->setDateTimeSideBySide(loadvar(SETTING_DATETIME_SIDE_BY_SIDE));
        } else if ($answertype == ANSWER_TYPE_SLIDER) {
            $type->setSliderOrientation(loadvar(SETTING_SLIDER_ORIENTATION));
            $type->setIncrement(loadvar(SETTING_SLIDER_INCREMENT));
            $type->setTooltip(loadvar(SETTING_SLIDER_TOOLTIP));
            $type->setTextbox(loadvar(SETTING_SLIDER_TEXTBOX));
            $type->setTextboxLabel(loadvar(SETTING_SLIDER_TEXTBOX_LABEL));
            $type->setTextboxPosttext(loadvar(SETTING_SLIDER_TEXTBOX_POSTTEXT));
            $type->setSliderPreSelection(loadvar(SETTING_SLIDER_PRESELECTION));
            $type->setSliderLabels(loadvar(SETTING_SLIDER_LABELS));
            $type->setSliderLabelPlacement(loadvar(SETTING_SLIDER_LABEL_PLACEMENT));
            $type->setSpinner(loadvar(SETTING_SPINNER));
            $type->setSpinnerType(loadvar(SETTING_SPINNER_TYPE));
            $type->setSpinnerUp(loadvar(SETTING_SPINNER_UP));
            $type->setSpinnerDown(loadvar(SETTING_SPINNER_DOWN));
            $type->setTextboxManual(loadvar(SETTING_TEXTBOX_MANUAL));
            $type->setSliderFormater(loadvar(SETTING_SLIDER_FORMATER));
        } else if ($answertype == ANSWER_TYPE_KNOB) {
            //$type->setKnobRotation(loadvar(SETTING_KNOB_ROTATION));
            $type->setIncrement(loadvar(SETTING_SLIDER_INCREMENT));
            $type->setTextbox(loadvar(SETTING_SLIDER_TEXTBOX));
            $type->setTextboxLabel(loadvar(SETTING_SLIDER_TEXTBOX_LABEL));
            $type->setTextboxPosttext(loadvar(SETTING_SLIDER_TEXTBOX_POSTTEXT));
            $type->setSpinner(loadvar(SETTING_SPINNER));
            $type->setSpinnerType(loadvar(SETTING_SPINNER_TYPE));
            $type->setSpinnerUp(loadvar(SETTING_SPINNER_UP));
            $type->setSpinnerDown(loadvar(SETTING_SPINNER_DOWN));
            $type->setTextboxManual(loadvar(SETTING_TEXTBOX_MANUAL));
        } else if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN))) {
            $type->setEnumeratedRandomizer(loadvar(SETTING_ENUMERATED_RANDOMIZER));
            $type->setComboboxOptGroup(loadvar(SETTING_DROPDOWN_OPTGROUP));
        } else if (inArray($answertype, array(ANSWER_TYPE_RANK))) {
            $type->setEnumeratedRandomizer(loadvar(SETTING_ENUMERATED_RANDOMIZER));
            //$type->setRankColumn(loadvar(SETTING_RANK_COLUMN));
            $type->setEnumeratedLabel(loadvar(SETTING_ENUMERATED_LABEL));
        } else if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {
            $type->setEnumeratedDisplay(loadvar(SETTING_ENUMERATED_ORIENTATION));
            $type->setEnumeratedBordered(loadvar(SETTING_ENUMERATED_BORDERED));
            $type->setEnumeratedSplit(loadvar(SETTING_ENUMERATED_SPLIT));
            $type->setEnumeratedTextbox(loadvar(SETTING_ENUMERATED_TEXTBOX));
            $type->setEnumeratedTextboxLabel(loadvar(SETTING_ENUMERATED_TEXTBOX_LABEL));
            $type->setEnumeratedTextboxPostText(loadvar(SETTING_ENUMERATED_TEXTBOX_POSTTEXT));
            $type->setEnumeratedClickLabel(loadvar(SETTING_ENUMERATED_CLICK_LABEL));
            $type->setEnumeratedLabel(loadvar(SETTING_ENUMERATED_LABEL));
            $type->setEnumeratedColumns(loadvar(SETTING_ENUMERATED_COLUMNS));
            $type->setHeaderAlignment(loadvar(SETTING_HEADER_ALIGNMENT));
            $ans = loadvar(SETTING_HEADER_FORMATTING);
            
            if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED))) {
                $type->setSetOfEnumeratedRanking(loadvar(SETTING_SETOFENUMERATED_RANKING));
            }
            
            if (!is_array($ans)) {
                $ans = array($ans);
            }
            $type->setHeaderFormatting(implode("~", $ans));
            $type->setEnumeratedOrder(loadvar(SETTING_ENUMERATED_ORDER));
            $type->setEnumeratedCustom(loadvarAllowHTML(SETTING_ENUMERATED_CUSTOM));
            $type->setEnumeratedRandomizer(loadvar(SETTING_ENUMERATED_RANDOMIZER));
            $type->setTableMobile(loadvar(SETTING_TABLE_MOBILE));
            $type->setTableMobileLabels(loadvar(SETTING_TABLE_MOBILE_LABELS));
        }

        if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE))) {
            $type->setSpinner(loadvar(SETTING_SPINNER));
            $type->setSpinnerType(loadvar(SETTING_SPINNER_TYPE));
            $type->setSpinnerUp(loadvar(SETTING_SPINNER_UP));
            $type->setSpinnerDown(loadvar(SETTING_SPINNER_DOWN));
            $type->setSpinnerIncrement(loadvar(SETTING_SPINNER_STEP));
            $type->setTextboxManual(loadvar(SETTING_TEXTBOX_MANUAL));
        }
        $type->setShowSectionHeader(loadvar(SETTING_SHOW_SECTION_HEADER));
        $type->setShowSectionFooter(loadvar(SETTING_SHOW_SECTION_FOOTER));

        if (Config::xiExtension()) {
            $type->setXiTemplate(loadvar(SETTING_GROUP_XI_TEMPLATE));
        }
        $type->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

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
        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showEditTypeAccessRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;
        $type->setAccessReturnAfterCompletionAction(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION));
        $type->setAccessReturnAfterCompletionRedoPreload(loadvar(SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO));
        $type->setAccessReentryAction(loadvar(SETTING_ACCESS_REENTRY_ACTION));
        $type->setAccessReentryRedoPreload(loadvar(SETTING_ACCESS_REENTRY_PRELOAD_REDO));
        $type->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showEditTypeAssistanceRes() {
        $displaySysAdmin = new DisplaySysAdmin();
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
                $type->setErrorMessageEnumeratedEntered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED));
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $type->setErrorMessageInlineAnswered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_ANSWERED));
                $type->setErrorMessageInlineExactRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED));
                $type->setErrorMessageInlineExclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE));
                $type->setErrorMessageInlineInclusive(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE));
                $type->setErrorMessageInlineMinimumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED));
                $type->setErrorMessageInlineMaximumRequired(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED));
                $type->setErrorMessageSetOfEnumeratedEntered(loadvarAllowHTML(SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED));
            /* fall through */
            case ANSWER_TYPE_MULTIDROPDOWN:
                $type->setErrorMessageSelectMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_SELECT));
                $type->setErrorMessageSelectMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_SELECT));
                $type->setErrorMessageSelectExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_SELECT));
                $type->setErrorMessageSelectInvalidSubset(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT));
                $type->setErrorMessageSelectInvalidSet(loadvarAllowHTML(SETTING_ERROR_MESSAGE_INVALID_SELECT));
                break;
            case ANSWER_TYPE_RANK:
                $type->setErrorMessageRankMinimum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MINIMUM_RANK));
                $type->setErrorMessageRankMaximum(loadvarAllowHTML(SETTING_ERROR_MESSAGE_MAXIMUM_RANK));
                $type->setErrorMessageRankExact(loadvarAllowHTML(SETTING_ERROR_MESSAGE_EXACT_RANK));
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

        if (inArray($t, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER, ANSWER_TYPE_KNOB))) {
            $type->setErrorMessageComparisonEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO));
            $type->setErrorMessageComparisonNotEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO));
            $type->setErrorMessageComparisonGreaterEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO));
            $type->setErrorMessageComparisonGreater(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_GREATER));
            $type->setErrorMessageComparisonSmallerEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO));
            $type->setErrorMessageComparisonSmaller(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_SMALLER));
        }
        /* string comparisons */ else if (inArray($t, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN))) {
            $type->setErrorMessageComparisonEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO));
            $type->setErrorMessageComparisonNotEqualTo(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO));
            $type->setErrorMessageComparisonEqualToIgnoreCase(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE));
            $type->setErrorMessageComparisonNotEqualToIgnoreCase(loadvarAllowHTML(SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE));
        }

        $type->save();

        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

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
        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showEditTypeOutputRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;

        $type->setHidden(loadvar(SETTING_HIDDEN));
        $type->setHiddenPaperVersion(loadvar(SETTING_HIDDEN_PAPER_VERSION));
        $type->setHiddenRouting(loadvar(SETTING_HIDDEN_ROUTING));
        $type->setHiddenTranslation(loadvar(SETTING_HIDDEN_TRANSLATION));
        $type->setScreendumpStorage(loadvar(SETTING_SCREENDUMPS));
        $type->setParadata(loadvar(SETTING_PARADATA));
        $type->setDataKeep(loadvar(SETTING_DATA_KEEP));
        $type->setDataInputMask(loadvar(SETTING_DATA_INPUTMASK));
        $type->setDataSkipVariable(loadvar(SETTING_DATA_SKIP));
        $type->setDataSkipVariablePostFix(loadvar(SETTING_DATA_SKIP_POSTFIX));
        $type->setStoreLocation(loadvar(SETTING_DATA_STORE_LOCATION));
        $type->setStoreLocationExternal(loadvar(SETTING_DATA_STORE_LOCATION_EXTERNAL));

        $t = $type->getAnswerType();
        if (inArray($t, array(ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED))) {
            $type->setOutputSetOfEnumeratedBinary(loadvar(SETTING_OUTPUT_SETOFENUMERATED));
        }
        if (inArray($t, array(ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_ENUMERATED))) {
            $type->setOutputOptionsText(loadvarAllowHTML(SETTING_OUTPUT_OPTIONS));
            $type->setOutputValueLabelWidth(loadvar(SETTING_OUTPUT_VALUELABEL_WIDTH));
        }

        $type->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);
        //$mess = $compiler->generateGetFills($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showEditTypeNavigationRes() {
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        $type = $survey->getType($tyd);
        $_SESSION['TYD'] = $tyd;

        $type->setKeyboardBindingEnabled(loadvar(SETTING_KEYBOARD_BINDING_ENABLED));
        $type->setKeyboardBindingBack(loadvar(SETTING_KEYBOARD_BINDING_BACK));
        $type->setKeyboardBindingNext(loadvar(SETTING_KEYBOARD_BINDING_NEXT));
        $type->setKeyboardBindingDK(loadvar(SETTING_KEYBOARD_BINDING_DK));
        $type->setKeyboardBindingRF(loadvar(SETTING_KEYBOARD_BINDING_RF));
        $type->setKeyboardBindingNA(loadvar(SETTING_KEYBOARD_BINDING_NA));
        $type->setKeyboardBindingUpdate(loadvar(SETTING_KEYBOARD_BINDING_UPDATE));
        $type->setKeyboardBindingRemark(loadvar(SETTING_KEYBOARD_BINDING_REMARK));
        $type->setKeyboardBindingClose(loadvar(SETTING_KEYBOARD_BINDING_CLOSE));
        $type->setIndividualDKRFNA(loadvar(SETTING_DKRFNA));
        $type->setIndividualDKRFNAInline(loadvar(SETTING_DKRFNA_INLINE));

        $type->save();
        $content = $displaySysAdmin->displaySuccess(Language::messageTypeChanged($type->getName()));
        $checker = new Checker($_SESSION['SUID']);
        $checks = $checker->checkType($type);
        if (sizeof($checks) > 0) {
            $content .= $displaySysAdmin->displayError(implode("<br/>", $checks));
        }

        /* compile */
        $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
        $mess = $compiler->generateTypes(array($type));
        $vars = $survey->getVariableDescriptivesOfType($tyd);
        $mess = $compiler->generateVariableDescriptives($vars);

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        /* return result */
        return $displaySysAdmin->showEditType($_SESSION['TYD'], $content);
    }

    function showCopyType() {
        if (getFromSessionParams('tyd') != "") {
            $_SESSION['TYD'] = getFromSessionParams('tyd');
        }
        $surveys = new Surveys();
        //if ($surveys->getNumberOfSurveys() > 1) {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showCopyType($_SESSION['TYD']);
        //} else {
        //   return $this->showCopyTypeRes();
        //}
    }

    function showCopyTypeRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey.type';

        $tyd = getFromSessionParams('tyd');
        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $type = $survey->getType($tyd);
        if ($tyd != '') {
            $suid = "";
            if (loadvar("suid") != "") {
                $suid = loadvar("suid");
            }
            $type->copy($suid, loadvar("includesuffix"));
            $_SESSION['TYD'] = $type->getTyd();

            /* same survey */
            if ($suid == "" || $suid == $_SESSION['SUID']) {
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $mess = $compiler->generateTypes(array($type));
            }
            // new survey, then compile the rest
            else {
                $_SESSION['SUID'] = $suid;
                $survey = new Survey($_SESSION['SUID']);
                $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
                $vars = $survey->getVariableDescriptivesOfType($tyd);
                $mess = $compiler->generateVariableDescriptives($vars);
                $mess = $compiler->generateGetFills($vars);
                $mess = $compiler->generateInlineFields($vars);
            }

            $content = $displaySysAdmin->displaySuccess(Language::messageTypeCopied($type->getName()));
            return $displaySysAdmin->showEditType($type->getTyd(), $content);
        } else {
            $content = $displaySysAdmin->displayError(Language::messageTypeNotCopied($type->getName()));
            return $displaySysAdmin->showSurvey($content);
        }
    }

    function showRemoveType() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (getFromSessionParams('tyd') != "") {
            $_SESSION['TYD'] = getFromSessionParams('tyd');
        }
        return $displaySysAdmin->showRemoveType($_SESSION['TYD']);
    }

    function showRemoveTypeRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.survey';

        $displaySysAdmin = new DisplaySysAdmin();
        $survey = new Survey($_SESSION['SUID']);
        $tyd = getFromSessionParams('tyd');
        if ($tyd != '') { //edit
            $type = $survey->getType($tyd);
            $type->remove();

            /* compile */
            $compiler = new Compiler($_SESSION['SUID'], getSurveyVersion($survey));
            $mess = $compiler->generateTypes(array($type), true);
            $vars = $survey->getVariableDescriptivesOfType($tyd);
            $mess = $compiler->generateVariableDescriptives($vars);
            $mess = $compiler->generateGetFills($vars);

            return $displaySysAdmin->showSurvey($displaySysAdmin->displaySuccess(Language::messageTypeRemoved($type->getName())));
        } else {
            return $displaySysAdmin->showSurvey();
        }
    }

    /* output menu */

    function showOutput() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutput();
    }

    function showOutputData() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputData();
    }

    function showOutputRawData() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputRawData();
    }

    function showOutputAddonData() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputAddonData();
    }

    function showOutputAddonDataRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displayOutput = new DisplayOutput();
        $de = new DataExport(loadvar('survey'));
        $de->setProperty(DATA_OUTPUT_CLEAN, loadvar(DATA_OUTPUT_CLEAN));
        $de->setProperty(DATA_OUTPUT_HIDDEN, loadvar(DATA_OUTPUT_HIDDEN));
        $de->setProperty(DATA_OUTPUT_COMPLETED, loadvar(DATA_OUTPUT_COMPLETED));
        $de->setProperty(DATA_OUTPUT_FIELDNAME_CASE, loadvar(DATA_OUTPUT_FIELDNAME_CASE));
        if (loadvar(DATA_OUTPUT_FILENAME) != "") {
            $de->setProperty(DATA_OUTPUT_FILENAME, loadvar(DATA_OUTPUT_FILENAME));
        }
        $cookievars = "";
        if (isset($_COOKIE['uscicvariablecookie'])) {
            if (loadvar(DATA_OUTPUT_SUBDATA) == SUBDATA_YES) {
                $vars = explode("-", $_COOKIE['uscicvariablecookie']);
                $arr = array();
                foreach ($vars as $var) {
                    $varsplit = explode("~", $var);
                    if (loadvar('survey') == $varsplit[0]) { // only consider variables from survey we are downloading for
                        $survey = new Survey($varsplit[0]);
                        $v = $survey->getVariableDescriptive($varsplit[1]);
                        if ($v->getName() != "") {
                            $arr[] = strtoupper($v->getName());
                        }
                    }
                }
                if (sizeof($arr) > 0) {
                    $cookievars = implode("~", $arr);
                }
            }
        }
        $de->setProperty(DATA_OUTPUT_VARLIST, $cookievars);
        $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));
        $de->setProperty(DATA_OUTPUT_INCLUDE_VALUE_LABELS, loadvar(DATA_OUTPUT_INCLUDE_VALUE_LABELS));
        $de->setProperty(DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS, loadvar(DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS));
        $de->setProperty(DATA_OUTPUT_LANGUAGES, implode("~", loadvar(DATA_OUTPUT_LANGUAGES)));
        $de->setProperty(DATA_OUTPUT_MARK_EMPTY, loadvar(DATA_OUTPUT_MARK_EMPTY));
        $de->setProperty(DATA_OUTPUT_MODES, implode("~", loadvar(DATA_OUTPUT_MODES)));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, loadvar(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, loadvar(DATA_OUTPUT_PRIMARY_KEY_IN_DATA));
        $de->setProperty(DATA_OUTPUT_SURVEY, loadvar(DATA_OUTPUT_SURVEY));
        $de->setProperty(DATA_OUTPUT_TYPEDATA, loadvar(DATA_OUTPUT_TYPEDATA));
        $de->setProperty(DATA_OUTPUT_VARIABLES_WITHOUT_DATA, loadvar(DATA_OUTPUT_VARIABLES_WITHOUT_DATA));
        $de->setProperty(DATA_OUTPUT_KEEP_ONLY, loadvar(DATA_OUTPUT_KEEP_ONLY));
        $de->setProperty(DATA_OUTPUT_TYPE, loadvar(DATA_OUTPUT_TYPE));
        $de->setProperty(DATA_OUTPUT_ENCODING, "UTF-8");
        //$de->displayProperties();
        $de->generateAuxiliary();
        $de->download();
        //$de->displayProperties();
        //$de->writeCSVFile();
        //$de->displayLog();
        return $displayOutput->showOutputAddOnData();
    }

    function showOutputRemarkData() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputRemarkData();
    }

    function showOutputRemarkDataRes() {

        $de = new DataExport(loadvar('survey'));
        $de->setProperty(DATA_OUTPUT_CLEAN, loadvar(DATA_OUTPUT_CLEAN));
        $de->setProperty(DATA_OUTPUT_HIDDEN, loadvar(DATA_OUTPUT_HIDDEN));
        $de->setProperty(DATA_OUTPUT_COMPLETED, loadvar(DATA_OUTPUT_COMPLETED));
        if (loadvar(DATA_OUTPUT_FILENAME) != "") {
            $de->setProperty(DATA_OUTPUT_FILENAME, loadvar(DATA_OUTPUT_FILENAME));
        }
        $cookievars = "";
        if (isset($_COOKIE['uscicvariablecookie'])) {
            if (loadvar(DATA_OUTPUT_SUBDATA) == SUBDATA_YES) {
                $vars = explode("-", $_COOKIE['uscicvariablecookie']);
                $arr = array();
                foreach ($vars as $var) {
                    $varsplit = explode("~", $var);
                    if (loadvar('survey') == $varsplit[0]) { // only consider variables from survey we are downloading for
                        $survey = new Survey($varsplit[0]);
                        $v = $survey->getVariableDescriptive($varsplit[1]);
                        if ($v->getName() != "") {
                            $arr[] = strtoupper($v->getName());
                        }
                    }
                }
                if (sizeof($arr) > 0) {
                    $cookievars = implode("~", $arr);
                }
            }
        }
        $de->setProperty(DATA_OUTPUT_VARLIST, $cookievars);
        $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));

        $this->determineModeLanguage($de);
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, loadvar(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, loadvar(DATA_OUTPUT_PRIMARY_KEY_IN_DATA));
        $de->setProperty(DATA_OUTPUT_SURVEY, loadvar(DATA_OUTPUT_SURVEY));
        $de->setProperty(DATA_OUTPUT_TYPEDATA, loadvar(DATA_OUTPUT_TYPEDATA));
        $de->setProperty(DATA_OUTPUT_KEEP_ONLY, loadvar(DATA_OUTPUT_KEEP_ONLY));
        $de->setProperty(DATA_OUTPUT_TYPE, loadvar(DATA_OUTPUT_TYPE));
        $de->setProperty(DATA_OUTPUT_ENCODING, "UTF-8");
        //$de->displayProperties();
        $de->generateRemarks();
        $de->download();
    }

    function showOutputTimingsData() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputTimingsData();
    }

    function showOutputTimingsDataRes() {

        $de = new DataExport(loadvar('survey'));
        if (loadvar(DATA_OUTPUT_FILENAME) != "") {
            $de->setProperty(DATA_OUTPUT_FILENAME, loadvar(DATA_OUTPUT_FILENAME));
        }
        $cookievars = "";
        if (isset($_COOKIE['uscicvariablecookie'])) {
            if (loadvar(DATA_OUTPUT_SUBDATA) == SUBDATA_YES) {
                $vars = explode("-", $_COOKIE['uscicvariablecookie']);
                $arr = array();
                foreach ($vars as $var) {
                    $varsplit = explode("~", $var);
                    if (loadvar('survey') == $varsplit[0]) { // only consider variables from survey we are downloading for
                        $survey = new Survey($varsplit[0]);
                        $v = $survey->getVariableDescriptive($varsplit[1]);
                        if ($v->getName() != "") {
                            $arr[] = strtoupper($v->getName());
                        }
                    }
                }
                if (sizeof($arr) > 0) {
                    $cookievars = implode("~", $arr);
                }
            }
        }
        $de->setProperty(DATA_OUTPUT_VARLIST, $cookievars);
        $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));

        $this->determineModeLanguage($de);
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, loadvar(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, loadvar(DATA_OUTPUT_PRIMARY_KEY_IN_DATA));
        $de->setProperty(DATA_OUTPUT_SURVEY, loadvar(DATA_OUTPUT_SURVEY));
        $de->setProperty(DATA_OUTPUT_TYPEDATA, loadvar(DATA_OUTPUT_TYPEDATA));
        $de->setProperty(DATA_OUTPUT_TYPE, loadvar(DATA_OUTPUT_TYPE));
        $de->generateTimings();
        $de->download();
    }

    function showOutputParaData() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputParaData();
    }

    function showOutputParaDataRes() {

        $de = new DataExport(loadvar('survey'));
        if (loadvar(DATA_OUTPUT_FILENAME) != "") {
            $de->setProperty(DATA_OUTPUT_FILENAME, loadvar(DATA_OUTPUT_FILENAME));
        } else {
            $de->setProperty(DATA_OUTPUT_FILENAME, Config::dbSurveyData() . "_paradata");
        }
        $cookievars = "";
        if (isset($_COOKIE['uscicvariablecookie'])) {
            if (loadvar(DATA_OUTPUT_SUBDATA) == SUBDATA_YES) {
                $vars = explode("-", $_COOKIE['uscicvariablecookie']);
                $arr = array();
                foreach ($vars as $var) {
                    $varsplit = explode("~", $var);
                    if (loadvar('survey') == $varsplit[0]) { // only consider variables from survey we are downloading for
                        $survey = new Survey($varsplit[0]);
                        $v = $survey->getVariableDescriptive($varsplit[1]);
                        if ($v->getName() != "") {
                            $arr[] = strtoupper($v->getName());
                        }
                    }
                }
                if (sizeof($arr) > 0) {
                    $cookievars = implode("~", $arr);
                }
            }
        }
        $de->setProperty(DATA_OUTPUT_VARLIST, $cookievars);
        $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));

        $this->determineModeLanguage($de);
        $de->setProperty(DATA_OUTPUT_TYPE, DATA_OUTPUT_TYPE_DATA_TABLE);
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, loadvar(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, loadvar(DATA_OUTPUT_PRIMARY_KEY_IN_DATA));
        $de->setProperty(DATA_OUTPUT_SURVEY, loadvar(DATA_OUTPUT_SURVEY));
        $de->setProperty(DATA_OUTPUT_TYPEDATA, loadvar(DATA_OUTPUT_TYPEDATA));
        $de->setProperty(DATA_OUTPUT_FROM, loadvar(DATA_OUTPUT_FROM));
        $de->setProperty(DATA_OUTPUT_TO, loadvar(DATA_OUTPUT_TO));
        if (loadvar(DATA_OUTPUT_TYPEPARADATA) == PARADATA_RAW) {
            $de->setProperty(DATA_OUTPUT_FILETYPE, FILETYPE_CSV);
            $de->generateParadata();
        } else if (loadvar(DATA_OUTPUT_TYPEPARADATA) == PARADATA_PROCESSED) {
            $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));
            $de->generateProcessedParadata();
        } else {
            $de->setProperty(DATA_OUTPUT_FILETYPE, FILETYPE_CSV);
            $de->generateErrorParadata();
        }
        $de->download();
    }

    function showOutputDataSingle() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDataSingleSurvey();
    }

    function showOutputDataSingleRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displayOutput = new DisplayOutput();
        $de = new DataExport(loadvar('survey'));
        $de->setProperty(DATA_OUTPUT_CLEAN, loadvar(DATA_OUTPUT_CLEAN));
        $de->setProperty(DATA_OUTPUT_HIDDEN, loadvar(DATA_OUTPUT_HIDDEN));
        $de->setProperty(DATA_OUTPUT_COMPLETED, loadvar(DATA_OUTPUT_COMPLETED));
        $de->setProperty(DATA_OUTPUT_FIELDNAME_CASE, loadvar(DATA_OUTPUT_FIELDNAME_CASE));
        if (loadvar(DATA_OUTPUT_FILENAME) != "") {
            $de->setProperty(DATA_OUTPUT_FILENAME, loadvar(DATA_OUTPUT_FILENAME));
        }
        $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));
        $de->setProperty(DATA_OUTPUT_INCLUDE_VALUE_LABELS, loadvar(DATA_OUTPUT_INCLUDE_VALUE_LABELS));
        $de->setProperty(DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS, loadvar(DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS));
        $de->setProperty(DATA_OUTPUT_MARK_EMPTY, loadvar(DATA_OUTPUT_MARK_EMPTY));

        $this->determineModeLanguage($de);
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, loadvar(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, loadvar(DATA_OUTPUT_PRIMARY_KEY_IN_DATA));
        $de->setProperty(DATA_OUTPUT_SURVEY, loadvar(DATA_OUTPUT_SURVEY));
        $de->setProperty(DATA_OUTPUT_TYPEDATA, loadvar(DATA_OUTPUT_TYPEDATA));
        $cookievars = "";
        if (isset($_COOKIE['uscicvariablecookie'])) {
            if (loadvar(DATA_OUTPUT_SUBDATA) == SUBDATA_YES) {
                $vars = explode("-", $_COOKIE['uscicvariablecookie']);
                $arr = array();
                foreach ($vars as $var) {
                    $varsplit = explode("~", $var);
                    if (loadvar('survey') == $varsplit[0]) { // only consider variables from survey we are downloading for
                        $survey = new Survey($varsplit[0]);
                        $v = $survey->getVariableDescriptive($varsplit[1]);
                        if ($v->getName() != "") {
                            $arr[] = strtoupper($v->getName());
                        }
                    }
                }
                if (sizeof($arr) > 0) {
                    $cookievars = implode("~", $arr);
                }
            }
        }
        $de->setProperty(DATA_OUTPUT_VARLIST, $cookievars);
        $de->setProperty(DATA_OUTPUT_VARIABLES_WITHOUT_DATA, loadvar(DATA_OUTPUT_VARIABLES_WITHOUT_DATA));
        $de->setProperty(DATA_OUTPUT_KEEP_ONLY, loadvar(DATA_OUTPUT_KEEP_ONLY));
        $de->setProperty(DATA_OUTPUT_TYPE, loadvar(DATA_OUTPUT_TYPE));
        $de->setProperty(DATA_OUTPUT_ENCODING, "UTF-8");
        //$de->displayProperties();

        $de->generate();
        $de->download();
        //$de->displayProperties();
        //$de->writeCSVFile();
        //$de->displayLog();
        return $displayOutput->showOutputDataSingleSurvey();
    }

    function showOutputDataMultiple() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDataMultipleSurvey();
    }

    function showOutputDataMultipleRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDataMultipleSurveyRes();
    }

    function showScreendumps() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputScreenDumps();
    }

    function showScreendumpsRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputScreenDumpsRes();
    }

    function showOutputDocumentation() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDocumentation();
    }

    function showOutputRouting() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputRouting();
    }

    function showOutputRoutingDash() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputRoutingDash();
    }

    function showOutputTranslation() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputTranslation();
    }

    function showOutputTranslationFills() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputTranslationFills();
    }

    function showOutputTranslationAssistance() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputTranslationAssistance();
    }

    function showOutputDictionary() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDictionary();
    }

    function showOutputStatistics() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatistics();
    }

    function showOutputStatisticsAggregates() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsAggregates();
    }

    function showOutputStatisticsAggregatesSection() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsAggregatesSection($_SESSION['SEID']);
    }

    function showOutputStatisticsAggregatesVariable() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsAggregatesVariable($_SESSION['SEID'], $_SESSION['VSID']);
    }

    function showOutputStatisticsParadata() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsParadata();
    }

    function showOutputStatisticsParadataSection() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsParadataSection($_SESSION['SEID']);
    }

    function showOutputStatisticsParadataVariable() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsParadataVariable($_SESSION['SEID'], $_SESSION['VSID']);
    }

    function showOutputStaticsContactsGraphs() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsContactsGraphs(getFromSessionParams('seid'));
    }

    function showOutputStatisticsTimings() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsTimings();
    }

    function showOutputStatisticsTimingsOverTime() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsTimingsOverTime();
    }

    function showOutputStatisticsPlatform() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsPlatform();
    }

    function showOutputStatisticsTimingsRespondent() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsTimingsRespondent();
    }

    function showOutputStatisticsTimingsRespondentRes() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputStatisticsTimingsRespondentRes();
    }

    function showOutputStaticsResponse() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputResponse();
    }

    /* tools */

    function showTools() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showTools();
    }

    function showBatchEditor() {
        $displaySysAdmin = new DisplaySysAdmin();
        if (loadvar("vrfiltermode_batch") != '') {
            $_SESSION['VRFILTERMODE_BATCH'] = loadvar("vrfiltermode_batch");
        }
        return $displaySysAdmin->showBatchEditor();
    }

    function showBatchEditorRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $main = loadvar("vrfiltermode_batch");
        $_SESSION['VRFILTERMODE_BATCH'] = $main;
        $action = loadvar("batchaction");
        $selected = loadvar("selected");

        if ($selected == "") {
            switch ($main) {

                // variable action
                case 0:
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotSelected(Language::labelVariablesLower()));
                    break;
                // type action
                case 1:
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotSelected(Language::labelTypesLower()));
                    break;
                // group action
                case 2:
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotSelected(Language::labelGroupsLower()));
                    break;
                // section action
                case 3:
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotSelected(Language::labelSectionsLower()));
                    break;
            }
        } else {
            switch ($main) {

                // variable action
                case 0:
                    $variablecookievalue = $_COOKIE['uscicvariablecookie'];
                    $content = $this->handleVariableAction($selected, $action, $variablecookievalue);
                    break;
                // type action
                case 1:
                    $typecookievalue = $_COOKIE['uscictypecookie'];
                    $content = $this->handleTypeAction($selected, $action, $typecookievalue);
                    break;
                // group action
                case 2:
                    $groupcookievalue = $_COOKIE['uscicgroupcookie'];
                    $content = $this->handleGroupAction($selected, $action, $groupcookievalue);
                    break;
                // section action
                case 3:
                    $sectioncookievalue = $_COOKIE['uscicsectioncookie'];
                    $content = $this->handleSectionAction($selected, $action, $sectioncookievalue);
                    break;
            }
        }

        return $displaySysAdmin->showBatchEditor($content, $variablecookievalue, $typecookievalue, $groupcookievalue, $sectioncookievalue);
    }

    function handleVariableAction($selected, $action, &$variablecookievalue) {
        $surveys = array();
        $displaySysAdmin = new DisplaySysAdmin();
        switch ($action) {
            case 'edit':

                $settings = $this->getBatchEditorVariableProperties();
                $tosave = array();
                $tocompile = array();
                $changed = array();
                foreach ($settings as $set) {
                    if (loadvar($set . "_checkbox") == 1) {
                        $val = loadvarAllowHTML(($set));
                        if ($val != "") {
                            foreach ($selected as $sel) {
                                $s = explode("~", $sel);
                                if (isset($surveys[$s[0]])) {
                                    $survey = $surveys[$s[0]];
                                } else {
                                    $survey = new Survey($s[0]);
                                    $surveys[$s[0]] = $survey;
                                    $tocompile[] = $s[0];
                                }

                                if (isset($tosave[$sel])) {
                                    $var = $tosave[$sel];
                                } else {
                                    $var = $survey->getVariableDescriptive($s[1]);
                                    $tosave[$sel] = $var;
                                }

                                if (isset($changed[$s[0]])) {
                                    $arr = $changed[$s[0]];
                                    if (isset($arr[$sel]) == false) {
                                        $arr[$sel] = $var;
                                        $changed[$s[0]] = $arr;
                                    }
                                } else {
                                    $arr = array();
                                    $arr[$sel] = $var;
                                    $changed[$s[0]] = $arr;
                                }

                                $var->setSettingValue($set, $val);
                                if ($set == SETTING_INPUT_MASK && $val == INPUTMASK_CUSTOM) {
                                    $var->setSettingValue(SETTING_INPUT_MASK_CUSTOM, loadvarAllowHTML(SETTING_INPUT_MASK_CUSTOM));
                                } else if ($set == SETTING_ENUMERATED_TEXTBOX && $val == TEXTBOX_YES) {
                                    $var->setSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, loadvarAllowHTML(SETTING_ENUMERATED_TEXTBOX_LABEL));
                                }
                            }
                        }
                    }
                }

                /* save */
                foreach ($tosave as $to) {
                    $to->save();
                }

                /* compile */
                foreach ($tocompile as $comp) {
                    if (isset($surveys[$comp])) {
                        $survey = $surveys[$comp];
                    } else {
                        $survey = new Survey($comp);
                        $surveys[$comp] = $survey;
                    }

                    $compiler = new Compiler($comp, getSurveyVersion($surveys[$comp]));
                    $mess = $compiler->generateVariableDescriptives($changed[$comp]);
                    $mess = $compiler->generateGetFills($changed[$comp]);
                    $mess = $compiler->generateSetFills($changed[$comp]);
                    $mess = $compiler->generateInlineFields($changed[$comp]);
                }

                if ($changed) {
                    $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorEdited(Language::labelVariablesLower()));
                } else {
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotEdited());
                }
                break;

                break;
            case 'copy':

                // determine survey
                $suid = loadvar('suid');
                if ($suid == "") {
                    $suid = $_SESSION['SUID'];
                }
                $section = loadvar("copysection");
                $newvars = array();
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);
                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                    }
                    $var = $survey->getVariableDescriptive($s[1]);
                    $oldvar = $var;
                    $var->copy($var->getName() . "_cl", $suid, $section);
                    $newvars[] = $var;
                }

                /* compile new */
                $compiler = new Compiler($suid, getSurveyVersion($survey));
                $mess = $compiler->generateVariableDescriptives($newvars);
                $mess = $compiler->generateSetFills($newvars);
                $mess = $compiler->generateGetFills($newvars);
                $mess = $compiler->generateInlineFields($newvars);

                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorCopied(Language::labelVariablesLower()));
                break;
            case 'move':
                $tocompile = array();
                $moved = array();
                $newvars = array();

                // determine survey
                $suid = loadvar('suid');
                if ($suid == "") {
                    $suid = $_SESSION['SUID'];
                }
                $section = loadvar("movesection");
                $cookiearr = explode("-", $variablecookievalue);
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);
                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                        $tocompile[] = $s[0];
                    }
                    $var = $survey->getVariableDescriptive($s[1]);
                    $oldvar = $var;
                    $var->move($suid, $section);
                    if (isset($moved[$s[0]])) {
                        $arr = $moved[$s[0]];
                    } else {
                        $arr = array();
                    }
                    $arr[] = $oldvar;
                    $moved[$s[0]] = $arr;
                    $newvars[] = $var;

                    /* update cookie */
                    $ind = array_search($sel, $cookiearr);
                    $cookiearr[$ind] = $var->getSuid() . '~' . $var->getVsid();
                }

                /* update cookie */
                setcookie('uscicvariablecookie', implode("-", $cookiearr)); //implode("-", $arr));
                $variablecookievalue = implode("-", $cookiearr);

                /* compile old */
                foreach ($tocompile as $comp) {
                    if (isset($surveys[$comp])) {
                        $survey = $surveys[$comp];
                    } else {
                        $survey = new Survey($comp);
                        $surveys[$comp] = $survey;
                    }

                    /* we moved across survey for one or more variables in this survey */
                    if ($suid != $comp) {
                        $compiler = new Compiler($comp, getSurveyVersion($surveys[$comp]));
                        $mess = $compiler->generateVariableDescriptives($moved[$comp], true);
                        $mess = $compiler->generateSetFills($moved[$comp], true);
                    }
                }

                /* compile new */
                $compiler = new Compiler($suid, getSurveyVersion($survey));
                $mess = $compiler->generateVariableDescriptives($newvars);
                $mess = $compiler->generateSetFills($newvars);
                $mess = $compiler->generateGetFills($newvars);
                $mess = $compiler->generateInlineFields($newvars);

                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorMoved(Language::labelVariablesLower()));
                break;
            case 'remove':
                $removed = array();
                $cookiearr = explode("-", $variablecookievalue);
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);

                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                    }
                    $var = $survey->getVariableDescriptive($s[1]);
                    $var->remove();
                    $removed[] = $var;

                    /* update cookie */
                    $ind = array_search($sel, $cookiearr);
                    unset($cookiearr[$ind]);
                }

                /* update cookie */
                setcookie('uscicvariablecookie', implode("-", $cookiearr)); //implode("-", $arr));
                $variablecookievalue = implode("-", $cookiearr);

                /* compile */
                foreach ($surveys as $survey) {
                    $compiler = new Compiler($survey->getSuid(), getSurveyVersion($survey));
                    $mess = $compiler->generateVariableDescriptives($removed, true);
                    $mess = $compiler->generateSetFills($removed, true);
                    //$mess = $compiler->generateGetFills();
                }
                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorRemoved(Language::labelVariablesLower()));
                break;
            default:
                $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorUnrecognizedAction());
                break;
        }
        return $content;
    }

    function handleTypeAction($selected, $action, &$typecookievalue) {
        $surveys = array();
        $displaySysAdmin = new DisplaySysAdmin();
        switch ($action) {
            case 'edit':

                $settings = $this->getBatchEditorVariableProperties();
                $tosave = array();
                $tocompile = array();
                $changed = array();
                foreach ($settings as $set) {
                    if (loadvar($set . "_checkbox") == 1) {
                        $val = loadvarAllowHTML(($set));
                        if ($val != "") {
                            foreach ($selected as $sel) {
                                $s = explode("~", $sel);
                                if (isset($surveys[$s[0]])) {
                                    $survey = $surveys[$s[0]];
                                } else {
                                    $survey = new Survey($s[0]);
                                    $surveys[$s[0]] = $survey;
                                    $tocompile[] = $s[0];
                                }

                                if (isset($tosave[$sel])) {
                                    $type = $tosave[$sel];
                                } else {
                                    $type = $survey->getType($s[1]);
                                    $tosave[$sel] = $type;
                                }

                                if (isset($changed[$s[0]])) {
                                    $arr = $changed[$s[0]];
                                    if (isset($arr[$sel]) == false) {
                                        $arr[$sel] = $type;
                                        $changed[$s[0]] = $arr;
                                    }
                                } else {
                                    $arr = array();
                                    $arr[$sel] = $type;
                                    $changed[$s[0]] = $arr;
                                }

                                $type->setSettingValue($set, $val);
                                if ($set == SETTING_INPUT_MASK && $val == INPUTMASK_CUSTOM) {
                                    $type->setSettingValue(SETTING_INPUT_MASK_CUSTOM, loadvarAllowHTML(SETTING_INPUT_MASK_CUSTOM));
                                } else if ($set == SETTING_ENUMERATED_TEXTBOX && $val == TEXTBOX_YES) {
                                    $type->setSettingValue(SETTING_ENUMERATED_TEXTBOX_LABEL, loadvarAllowHTML(SETTING_ENUMERATED_TEXTBOX_LABEL));
                                }
                            }
                        }
                    }
                }

                /* save */
                foreach ($tosave as $to) {
                    $to->save();
                }

                /* compile */
                foreach ($tocompile as $comp) {
                    if (isset($surveys[$comp])) {
                        $survey = $surveys[$comp];
                    } else {
                        $survey = new Survey($comp);
                        $surveys[$comp] = $survey;
                    }

                    $compiler = new Compiler($comp, getSurveyVersion($surveys[$comp]));
                    $mess = $compiler->generateTypes($changed[$comp]);
                    $newtypes = $changed[$comp];
                    foreach ($newtypes as $newtype) {
                        $newvars = $survey->getVariableDescriptivesOfType($newtype->getTyd());
                        $mess = $compiler->generateVariableDescriptives($newvars);
                        $mess = $compiler->generateGetFills($newvars);
                        $mess = $compiler->generateInlineFields($newvars);
                    }
                }

                if ($changed) {
                    $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorEdited(Language::labelTypesLower()));
                } else {
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotEdited());
                }
                break;

                break;
            case 'copy':

                // determine survey
                $suid = loadvar('suid');
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);
                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                    }
                    $type = $survey->getType($s[1]);
                    $oldtype = $type;
                    $type->copy($suid);
                    $newtypes[] = $type;
                }

                /* compile new */
                $compiler = new Compiler($suid, getSurveyVersion($survey));
                $mess = $compiler->generateTypes($newtypes);
                foreach ($newtypes as $newtype) {
                    $newvars = $survey->getVariableDescriptivesOfType($newtype->getTyd());
                    $mess = $compiler->generateVariableDescriptives($newvars);
                    $mess = $compiler->generateGetFills($newvars);
                    $mess = $compiler->generateInlineFields($newvars);
                }

                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorCopied(Language::labelTypesLower()));
                break;
            case 'move':
                $tocompile = array();
                $moved = array();
                $newtypes = array();

                // determine survey
                $suid = loadvar('suid');
                $cookiearr = explode("-", $typecookievalue);
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);
                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                        $tocompile[] = $s[0];
                    }
                    $type = $survey->getType($s[1]);
                    $oldtype = $type;
                    $type->move($suid);
                    if (isset($moved[$s[0]])) {
                        $arr = $moved[$s[0]];
                    } else {
                        $arr = array();
                    }
                    $arr[] = $oldtype;
                    $moved[$s[0]] = $arr;
                    $newtypes[] = $type;

                    /* update cookie */
                    $ind = array_search($sel, $cookiearr);
                    $cookiearr[$ind] = $type->getSuid() . '~' . $type->getTyd();
                }

                /* update cookie */
                setcookie('uscictypecookie', implode("-", $cookiearr)); //implode("-", $arr));
                $typecookievalue = implode("-", $cookiearr);

                /* compile old */
                foreach ($tocompile as $comp) {
                    if (isset($surveys[$comp])) {
                        $survey = $surveys[$comp];
                    } else {
                        $survey = new Survey($comp);
                        $surveys[$comp] = $survey;
                    }

                    /* we moved across survey for one or more types in this survey */
                    if ($suid != $comp) {
                        $compiler = new Compiler($comp, getSurveyVersion($surveys[$comp]));
                        $mess = $compiler->generateTypes($moved[$comp], true);
                    }
                }

                /* compile new */
                $compiler = new Compiler($suid, getSurveyVersion($survey));
                $mess = $compiler->generateTypes($newtypes);
                $newtypes = $changed[$comp];
                foreach ($newtypes as $newtype) {
                    $newvars = $survey->getVariableDescriptivesOfType($newtype->getTyd());
                    $mess = $compiler->generateVariableDescriptives($newvars);
                    $mess = $compiler->generateGetFills($newvars);
                    $mess = $compiler->generateInlineFields($newvars);
                }

                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorMoved(Language::labelTypesLower()));
                break;
            case 'remove':

                $removed = array();
                $cookiearr = explode("-", $typecookievalue);
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);

                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                    }
                    $type = $survey->getType($s[1]);
                    $type->remove();
                    $removed[] = $type;

                    /* update cookie */
                    $ind = array_search($sel, $cookiearr);
                    unset($cookiearr[$ind]);
                }

                /* update cookie */
                setcookie('uscictypecookie', implode("-", $cookiearr)); //implode("-", $arr));
                $typecookievalue = implode("-", $cookiearr);

                /* compile */
                foreach ($surveys as $survey) {
                    $compiler = new Compiler($survey->getSuid(), getSurveyVersion($survey));
                    $mess = $compiler->generateTypes($removed, true);
                }
                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorRemoved(Language::labelTypesLower()));
                break;
            default:
                $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorUnrecognizedAction());
                break;
        }
        return $content;
    }

    function handleGroupAction($selected, $action, &$groupcookievalue) {
        $surveys = array();
        $displaySysAdmin = new DisplaySysAdmin();
        switch ($action) {
            case 'edit':

                $settings = $this->getBatchEditorGroupProperties();
                $tosave = array();
                $tocompile = array();
                $changed = array();
                foreach ($settings as $set) {
                    if (loadvar($set . "_checkbox") == 1) {
                        $val = loadvarAllowHTML(($set));
                        if ($val != "") {
                            foreach ($selected as $sel) {
                                $s = explode("~", $sel);
                                if (isset($surveys[$s[0]])) {
                                    $survey = $surveys[$s[0]];
                                } else {
                                    $survey = new Survey($s[0]);
                                    $surveys[$s[0]] = $survey;
                                    $tocompile[] = $s[0];
                                }

                                if (isset($tosave[$sel])) {
                                    $group = $tosave[$sel];
                                } else {
                                    $group = $survey->getGroup($s[1]);
                                    $tosave[$sel] = $group;
                                }

                                if (isset($changed[$s[0]])) {
                                    $arr = $changed[$s[0]];
                                    if (isset($arr[$sel]) == false) {
                                        $arr[$sel] = $group;
                                        $changed[$s[0]] = $arr;
                                    }
                                } else {
                                    $arr = array();
                                    $arr[$sel] = $group;
                                    $changed[$s[0]] = $arr;
                                }

                                $group->setSettingValue($set, $val);
                                if ($set == SETTING_GROUP_TEMPLATE && $val == TABLE_TEMPLATE_CUSTOM) {
                                    $group->setSettingValue(SETTING_GROUP_CUSTOM_TEMPLATE, loadvarAllowHTML(SETTING_GROUP_CUSTOM_TEMPLATE));
                                }
                            }
                        }
                    }
                }

                /* save */
                foreach ($tosave as $to) {
                    $to->save();
                }

                /* compile */
                foreach ($tocompile as $comp) {
                    if (isset($surveys[$comp])) {
                        $survey = $surveys[$comp];
                    } else {
                        $survey = new Survey($comp);
                        $surveys[$comp] = $survey;
                    }

                    $compiler = new Compiler($comp, getSurveyVersion($surveys[$comp]));
                    $mess = $compiler->generateGroups($changed[$comp]);
                    $mess = $compiler->generateGetFillsGroups($changed[$comp]);
                    $mess = $compiler->generateInlineFieldsGroups($changed[$comp]);
                }

                if ($changed) {
                    $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorEdited(Language::labelGroupsLower()));
                } else {
                    $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorNotEdited());
                }
                break;

                break;
            case 'copy':

                // determine survey
                $suid = loadvar('suid');
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);
                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                    }
                    $group = $survey->getGroup($s[1]);
                    $oldgroup = $group;
                    $group->copy($suid);
                    $newgroups[] = $group;
                }

                /* compile new */
                $compiler = new Compiler($suid, getSurveyVersion($survey));
                $mess = $compiler->generateGroups($newgroups);
                $mess = $compiler->generateGetFillsGroups($newgroups);
                $mess = $compiler->generateInlineFieldsGroups($newgroups);

                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorCopied(Language::labelGroupsLower()));
                break;
            case 'move':
                $tocompile = array();
                $moved = array();
                $newgroups = array();

                // determine survey
                $suid = loadvar('suid');
                $cookiearr = explode("-", $groupcookievalue);
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);
                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                        $tocompile[] = $s[0];
                    }
                    $group = $survey->getGroup($s[1]);
                    $oldgroup = $group;
                    $group->move($suid);
                    if (isset($moved[$s[0]])) {
                        $arr = $moved[$s[0]];
                    } else {
                        $arr = array();
                    }
                    $arr[] = $oldgroup;
                    $moved[$s[0]] = $arr;
                    $newgroups[] = $group;

                    /* update cookie */
                    $ind = array_search($sel, $cookiearr);
                    $cookiearr[$ind] = $group->getSuid() . '~' . $group->getGid();
                }

                /* update cookie */
                setcookie('uscicgroupcookie', implode("-", $cookiearr)); //implode("-", $arr));
                $groupcookievalue = implode("-", $cookiearr);

                /* compile old */
                foreach ($tocompile as $comp) {
                    if (isset($surveys[$comp])) {
                        $survey = $surveys[$comp];
                    } else {
                        $survey = new Survey($comp);
                        $surveys[$comp] = $survey;
                    }

                    /* we moved across survey for one or more groups in this survey */
                    if ($suid != $comp) {
                        $compiler = new Compiler($comp, getSurveyVersion($surveys[$comp]));
                        $mess = $compiler->generateGroups($moved[$comp], true);
                    }
                }

                /* compile new */
                $compiler = new Compiler($suid, getSurveyVersion($survey));
                $mess = $compiler->generateGroups($newgroups);
                $mess = $compiler->generateGetFillsGroups($newgroups);
                $mess = $compiler->generateInlineFieldsGroups($newgroups);

                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorMoved(Language::labelGroupsLower()));
                break;
            case 'remove':

                $removed = array();
                $cookiearr = explode("-", $groupcookievalue);
                foreach ($selected as $sel) {
                    $s = explode("~", $sel);

                    if (isset($surveys[$s[0]])) {
                        $survey = $surveys[$s[0]];
                    } else {
                        $survey = new Survey($s[0]);
                        $surveys[$s[0]] = $survey;
                    }
                    $group = $survey->getGroup($s[1]);
                    $group->remove();
                    $removed[] = $group;

                    /* update cookie */
                    $ind = array_search($sel, $cookiearr);
                    unset($cookiearr[$ind]);
                }

                /* update cookie */
                setcookie('uscicgroupcookie', implode("-", $cookiearr)); //implode("-", $arr));
                $groupcookievalue = implode("-", $cookiearr);

                /* compile */
                foreach ($surveys as $survey) {
                    $compiler = new Compiler($survey->getSuid(), getSurveyVersion($survey));
                    $mess = $compiler->generateGroups($removed, true);
                }
                $content = $displaySysAdmin->displaySuccess(Language::messageToolsBatchEditorRemoved(Language::labelGroupsLower()));
                break;
            default:
                $content = $displaySysAdmin->displayWarning(Language::messageToolsBatchEditorUnrecognizedAction());
                break;
        }
        return $content;
    }

    function getBatchEditorGroupProperties() {
        $settings = array(SETTING_DESCRIPTION,
            SETTING_ARRAY,
            SETTING_KEEP,
            SETTING_ACCESS_REENTRY_ACTION,
            SETTING_ACCESS_REENTRY_PRELOAD_REDO,
            SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION,
            SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO,
            SETTING_IFEMPTY,
            SETTING_IFERROR,
            SETTING_GROUP_EXCLUSIVE,
            SETTING_GROUP_INCLUSIVE,
            SETTING_GROUP_MAXIMUM_REQUIRED,
            SETTING_GROUP_MINIMUM_REQUIRED,
            SETTING_GROUP_EXACT_REQUIRED,
            SETTING_SURVEY_TEMPLATE,
            SETTING_PAGE_HEADER,
            SETTING_PAGE_FOOTER,
            SETTING_BUTTON_ALIGNMENT,
            SETTING_BUTTON_FORMATTING,
            SETTING_ERROR_PLACEMENT,
            SETTING_BACK_BUTTON,
            SETTING_BACK_BUTTON_LABEL,
            SETTING_NEXT_BUTTON,
            SETTING_NEXT_BUTTON_LABEL,
            SETTING_DK_BUTTON,
            SETTING_DK_BUTTON_LABEL,
            SETTING_RF_BUTTON,
            SETTING_RF_BUTTON_LABEL,
            SETTING_UPDATE_BUTTON,
            SETTING_UPDATE_BUTTON_LABEL,
            SETTING_NA_BUTTON,
            SETTING_NA_BUTTON_LABEL,
            SETTING_CLOSE_BUTTON,
            SETTING_CLOSE_BUTTON_LABEL,
            SETTING_REMARK_BUTTON,
            SETTING_REMARK_BUTTON_LABEL,
            SETTING_REMARK_SAVE_BUTTON,
            SETTING_REMARK_SAVE_BUTTON_LABEL,
            SETTING_PROGRESSBAR_SHOW,
            SETTING_PROGRESSBAR_FILLED_COLOR,
            SETTING_PROGRESSBAR_WIDTH,
            SETTING_HEADER_ALIGNMENT,
            SETTING_HEADER_FIXED,
            SETTING_HEADER_FORMATTING,
            SETTING_HEADER_SCROLL_DISPLAY,
            SETTING_GROUP_TABLE_BORDERED,
            SETTING_GROUP_TABLE_CONDENSED,
            SETTING_GROUP_TABLE_HOVERED,
            SETTING_GROUP_TABLE_STRIPED,
            SETTING_TABLE_WIDTH,
            SETTING_QUESTION_COLUMN_WIDTH,
            SETTING_ERROR_MESSAGE_EXCLUSIVE,
            SETTING_ERROR_MESSAGE_INCLUSIVE,
            SETTING_ERROR_MESSAGE_EXACT_REQUIRED,
            SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_SAME_REQUIRED,
            SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED,
            SETTING_KEYBOARD_BINDING_ENABLED,
            SETTING_KEYBOARD_BINDING_BACK,
            SETTING_KEYBOARD_BINDING_NEXT,
            SETTING_KEYBOARD_BINDING_DK,
            SETTING_KEYBOARD_BINDING_RF,
            SETTING_KEYBOARD_BINDING_NA,
            SETTING_KEYBOARD_BINDING_UPDATE,
            SETTING_KEYBOARD_BINDING_REMARK,
            SETTING_KEYBOARD_BINDING_CLOSE
        );
        return $settings;
    }

    function getBatchEditorVariableProperties() {
        $settings = array(SETTING_DESCRIPTION,
            SETTING_ANSWERTYPE,
            SETTING_OPTIONS,
            SETTING_ARRAY,
            SETTING_KEEP,
            SETTING_ACCESS_REENTRY_ACTION,
            SETTING_ACCESS_REENTRY_PRELOAD_REDO,
            SETTING_ACCESS_RETURN_AFTER_COMPLETION_ACTION,
            SETTING_ACCESS_RETURN_AFTER_COMPLETION_PRELOAD_REDO,
            SETTING_IFEMPTY,
            SETTING_IFERROR,
            SETTING_COMPARISON_EQUAL_TO,
            SETTING_COMPARISON_NOT_EQUAL_TO,
            SETTING_COMPARISON_GREATER_EQUAL_TO,
            SETTING_COMPARISON_GREATER,
            SETTING_COMPARISON_SMALLER_EQUAL_TO,
            SETTING_COMPARISON_SMALLER,
            SETTING_MAXIMUM_CALENDAR,
            SETTING_MINIMUM_LENGTH,
            SETTING_MINIMUM_LENGTH,
            SETTING_MINIMUM_WORDS,
            SETTING_MAXIMUM_WORDS,
            SETTING_PATTERN,
            SETTING_COMPARISON_EQUAL_TO_IGNORE_CASE,
            SETTING_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE,
            SETTING_MINIMUM_RANGE,
            SETTING_MAXIMUM_RANGE,
            SETTING_OTHER_RANGE,
            SETTING_MINIMUM_SELECTED,
            SETTING_MAXIMUM_SELECTED,
            SETTING_EXACT_SELECTED,
            SETTING_INLINE_EXCLUSIVE,
            SETTING_INLINE_EXCLUSIVE,
            SETTING_INLINE_MINIMUM_REQUIRED,
            SETTING_INLINE_MAXIMUM_REQUIRED,
            SETTING_INLINE_EXACT_REQUIRED,
            SETTING_INPUT_MASK_ENABLED,
            SETTING_INPUT_MASK,
            SETTING_INPUT_MASK_PLACEHOLDER,
            SETTING_SURVEY_TEMPLATE,
            SETTING_PAGE_HEADER,
            SETTING_PAGE_FOOTER,
            SETTING_QUESTION_ALIGNMENT,
            SETTING_QUESTION_FORMATTING,
            SETTING_ANSWER_ALIGNMENT,
            SETTING_ANSWER_FORMATTING,
            SETTING_BUTTON_ALIGNMENT,
            SETTING_BUTTON_FORMATTING,
            SETTING_ERROR_PLACEMENT,
            SETTING_BACK_BUTTON,
            SETTING_BACK_BUTTON_LABEL,
            SETTING_NEXT_BUTTON,
            SETTING_NEXT_BUTTON_LABEL,
            SETTING_DK_BUTTON,
            SETTING_DK_BUTTON_LABEL,
            SETTING_RF_BUTTON,
            SETTING_RF_BUTTON_LABEL,
            SETTING_UPDATE_BUTTON,
            SETTING_UPDATE_BUTTON_LABEL,
            SETTING_NA_BUTTON,
            SETTING_NA_BUTTON_LABEL,
            SETTING_CLOSE_BUTTON,
            SETTING_CLOSE_BUTTON_LABEL,
            SETTING_REMARK_BUTTON,
            SETTING_REMARK_BUTTON_LABEL,
            SETTING_REMARK_SAVE_BUTTON,
            SETTING_REMARK_SAVE_BUTTON_LABEL,
            SETTING_SHOW_SECTION_HEADER,
            SETTING_SHOW_SECTION_FOOTER,
            SETTING_PROGRESSBAR_SHOW,
            SETTING_PROGRESSBAR_FILLED_COLOR,
            SETTING_PROGRESSBAR_WIDTH,
            SETTING_ENUMERATED_ORIENTATION,
            SETTING_ENUMERATED_ORDER,
            SETTING_ENUMERATED_SPLIT,
            SETTING_ENUMERATED_RANDOMIZER,
            SETTING_ENUMERATED_TEXTBOX_LABEL,
            SETTING_ENUMERATED_TEXTBOX_POSTTEXT,
            SETTING_HEADER_ALIGNMENT,
            SETTING_HEADER_FORMATTING,
            SETTING_ENUMERATED_BORDERED,
            SETTING_ENUMERATED_TEXTBOX,
            SETTING_TIME_FORMAT,
            SETTING_DATE_FORMAT,
            SETTING_DATETIME_FORMAT,
            SETTING_DATE_DEFAULT_VIEW,
            SETTING_EMPTY_MESSAGE,
            SETTING_ERROR_MESSAGE_DOUBLE,
            SETTING_ERROR_MESSAGE_INTEGER,
            SETTING_ERROR_MESSAGE_RANGE,
            SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO,
            SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO,
            SETTING_ERROR_MESSAGE_COMPARISON_GREATER_EQUAL_TO,
            SETTING_ERROR_MESSAGE_COMPARISON_GREATER,
            SETTING_ERROR_MESSAGE_COMPARISON_SMALLER_EQUAL_TO,
            SETTING_ERROR_MESSAGE_COMPARISON_SMALLER,
            SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR,
            SETTING_ERROR_MESSAGE_COMPARISON_EQUAL_TO_IGNORE_CASE,
            SETTING_ERROR_MESSAGE_COMPARISON_NOT_EQUAL_TO_IGNORE_CASE,
            SETTING_ERROR_MESSAGE_PATTERN,
            SETTING_ERROR_MESSAGE_MINIMUM_LENGTH,
            SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH,
            SETTING_ERROR_MESSAGE_MINIMUM_WORDS,
            SETTING_ERROR_MESSAGE_MAXIMUM_WORDS,
            SETTING_ERROR_MESSAGE_ENUMERATED_ENTERED,
            SETTING_ERROR_MESSAGE_MINIMUM_SELECT,
            SETTING_ERROR_MESSAGE_MAXIMUM_SELECT,
            SETTING_ERROR_MESSAGE_EXACT_SELECT,
            SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT,
            SETTING_ERROR_MESSAGE_INVALID_SELECT,
            SETTING_ERROR_MESSAGE_SETOFENUMERATED_ENTERED,
            SETTING_ERROR_MESSAGE_INLINE_ANSWERED,
            SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE,
            SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE,
            SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED,
            SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED,
            SETTING_JAVASCRIPT_WITHIN_ELEMENT,
            SETTING_JAVASCRIPT_WITHIN_PAGE,
            SETTING_SCRIPTS,
            SETTING_STYLE_WITHIN_ELEMENT,
            SETTING_STYLE_WITHIN_PAGE,
            SETTING_HIDDEN,
            SETTING_HIDDEN_PAPER_VERSION,
            SETTING_HIDDEN_ROUTING,
            SETTING_HIDDEN_TRANSLATION,
            SETTING_DATA_INPUTMASK,
            SETTING_SCREENDUMPS,
            SETTING_PARADATA,
            SETTING_DATA_KEEP,
            SETTING_DATA_SKIP,
            SETTING_KEYBOARD_BINDING_ENABLED,
            SETTING_KEYBOARD_BINDING_BACK,
            SETTING_KEYBOARD_BINDING_NEXT,
            SETTING_KEYBOARD_BINDING_DK,
            SETTING_KEYBOARD_BINDING_RF,
            SETTING_KEYBOARD_BINDING_NA,
            SETTING_KEYBOARD_BINDING_UPDATE,
            SETTING_KEYBOARD_BINDING_REMARK,
            SETTING_KEYBOARD_BINDING_CLOSE,
            SETTING_SLIDER_ORIENTATION,
            SETTING_SLIDER_TOOLTIP,
            SETTING_SLIDER_TEXTBOX,
            SETTING_SLIDER_INCREMENT,
            SETTING_SLIDER_LABEL_PLACEMENT,
            SETTING_SLIDER_LABELS,
            SETTING_SLIDER_TEXTBOX_LABEL,
            SETTING_SLIDER_TEXTBOX_POSTTEXT
        );
        return $settings;
    }

    function showClean() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showClean();
    }

    function showCleanRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $clean = loadvar("clean");
        $from = loadvar("from");
        $to = loadvar("to");
        $datatypes = loadvar("datatype");
        global $db;
        $content = "";
        $displaySysAdmin = new DisplaySysAdmin();
        if ($clean != "" && $datatypes != "") {
            foreach ($clean as $cl) {
                $tsquery = "";
                if ($from != "") {
                    $tsquery .= " and ts > '" . prepareDatabaseString($from) . "'";
                }
                if ($to != "") {
                    $tsquery .= " and ts < '" . prepareDatabaseString($to) . "'";
                }

                $tables = array();
                if (inArray(SURVEY_EXECUTION_MODE_NORMAL, $datatypes)) {
                    $tables[] = Config::dbSurvey();
                }
                if (inArray(SURVEY_EXECUTION_MODE_TEST, $datatypes)) {
                    $tables[] = Config::dbSurvey() . "_test";
                }

                foreach ($tables as $table) {
                    $query = "delete from " . $table . "_actions where suid=" . prepareDatabaseString($cl) . " and systemtype=" . USCIC_SURVEY . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_data where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_datarecords where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_screendumps where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_logs where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_observations where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_states where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_times where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_paradata where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_loopdata where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_consolidated_times where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                    $query = "delete from " . $table . "_processed_paradata where suid=" . prepareDatabaseString($cl) . $tsquery;
                    $db->executeQuery($query);
                }
            }
            $content = $displaySysAdmin->displaySuccess(Language::messageToolsCleanOk());
        } else {
            if ($clean == "") {
                $content = $displaySysAdmin->displayWarning(Language::messageToolsCleanSelectSurvey());
            } else if ($datatypes == "") {
                $content = $displaySysAdmin->displayWarning(Language::messageToolsCleanSelectDataType());
            }
        }
        return $displaySysAdmin->showClean($content);
    }

    function showCheck() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showCheck();
    }

    function showCheckRes() {
        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $comp = loadvar("survey");
        $components = loadvar("components");
        if ($components == "") {
            return $displaySysAdmin->showCheck($displaySysAdmin->displayWarning(Language::messageToolsCompileSelectComponent()));
        }
        set_time_limit(0);
        $messages = array();
        $errors = false;
        $survey = new Survey($comp);
        $checker = new Checker($comp);
        $compiler = new Compiler($comp, getSurveyVersion($survey));
        $sectionmessages = array();
        $routingmessages = array();
        $variablemessages = array();
        $typemessages = array();
        $groupmessages = array();
        $surveymessages = array();
        if (inArray(SURVEY_COMPONENT_ROUTING, $components)) {
            $sections = $survey->getSections();
            foreach ($sections as $section) {
                $mess = $compiler->generateEngine($section->getSeid(), false);
                if (sizeof($mess) > 0) {
                    $routingmessages[$section->getName()] = $mess;
                    $errors = true;
                }
            }
        }
        if (inArray(SURVEY_COMPONENT_SECTION, $components)) {
            $sections = $survey->getSections();
            foreach ($sections as $section) {
                $mess = $checker->checkSection($section, true);
                if (sizeof($mess) > 0) {
                    $sectionmessages[$section->getName()] = $mess;
                    $errors = true;
                }
            }
        }
        if (inArray(SURVEY_COMPONENT_VARIABLE, $components)) {
            $vars = $survey->getVariableDescriptives();
            foreach ($vars as $var) {
                $mess = $checker->checkVariable($var, true);
                if (sizeof($mess) > 0) {
                    $variablemessages[$var->getName()] = $mess;
                    $errors = true;
                }

                $mess = $compiler->generateSetFills(array($var), false, false);
                if (sizeof($mess) > 0) {

                    if (isset($variablemessages[$var->getName()])) {
                        $variablemessages[$var->getName()] = array_merge($variablemessages[$var->getName()], $mess);
                    } else {
                        $variablemessages[$var->getName()] = $mess;
                    }
                    $errors = true;
                }
            }
        }
        if (inArray(SURVEY_COMPONENT_TYPE, $components)) {
            $types = $survey->getTypes();
            foreach ($types as $type) {
                $mess = $checker->checkType($type, true);
                if (sizeof($mess) > 0) {
                    $typemessages[$type->getName()] = $mess;
                    $errors = true;
                }
            }
        }
        if (inArray(SURVEY_COMPONENT_SETTING, $components)) {
            $mess = $checker->checkSurvey();
            if (sizeof($mess) > 0) {
                $surveymessages = $mess;
                $errors = true;
            }
        }
        if (inArray(SURVEY_COMPONENT_GROUP, $components)) {
            $groups = $survey->getGroups();
            foreach ($groups as $group) {
                $mess = $checker->checkGroup($group, true);
                if (sizeof($mess) > 0) {
                    $groupmessages[$group->getName()] = $mess;
                    $errors = true;
                }
            }
        }
        $content = "";
        $messages = array(Language::labelSections() => $sectionmessages, Language::labelVariables() => $variablemessages, Language::labelTypes() => $typemessages, Language::labelGroups() => $groupmessages, Language::labelSettings() => $surveymessages, Language::LabelRouting() => $routingmessages);
        if ($errors) {
            $m = '<a data-keyboard="false" data-toggle="modal" data-target="#errorsModal">Show error(s)</a>';
            $content .= $displaySysAdmin->displayError(Language::messageToolsCheckNotOk() . " " . $m);
        } else {
            $content .= $displaySysAdmin->displaySuccess(Language::messageToolsCheckOk());
        }
        $text = "";
        foreach ($messages as $k => $v) {

            if (sizeof($v) == 0) {
                //$text .= $displaySysAdmin->displaySuccess(Language::messageToolsCheckOk());
            } else {
                $text .= "<h3>" . $k . "</h3>";
                foreach ($v as $name => $m) {

                    foreach ($m as $object => $errors) {
                        if (is_array($errors)) {
                            foreach ($errors as $n) {
                                if (trim($n) != "") {
                                    $text .= $displaySysAdmin->displayError($name . ": " . $n);
                                }
                            }
                        } else {
                            if (trim($errors) != "") {
                                $text .= $displaySysAdmin->displayError($name . ": " . $errors);
                            }
                        }
                    }
                }
            }
        }
        $content .= $displaySysAdmin->displayRoutingErrorModal($survey, $text);
        return $displaySysAdmin->showCheck($content);
    }

    function showCompile() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showCompile();
    }

    function showCompileRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $compile = loadvar("compile");
        $components = loadvar("components");
        if ($compile == "") {
            return $displaySysAdmin->showCompile($displaySysAdmin->displayWarning(Language::messageToolsCompileSelectSurvey()));
        }
        if ($components == "") {
            return $displaySysAdmin->showCompile($displaySysAdmin->displayWarning(Language::messageToolsCompileSelectComponent()));
        }
        set_time_limit(0);
        $messages = array();
        global $survey;
        $currentsurvey = $survey;
        foreach ($compile as $comp) {
            $survey = new Survey($comp);
            $compiler = new Compiler($comp, getSurveyVersion($survey));
            if (inArray(SURVEY_COMPONENT_SECTION, $components)) {
                $sections = $survey->getSections();
                foreach ($sections as $section) {
                    $mess = $compiler->generateEngine($section->getSeid());
                    if (is_array($mess) && sizeof($mess) > 0) {
                        $messages[] = $mess;
                    }
                    $mess = $compiler->generateProgressBar($section->getSeid());
                    if (is_array($mess) && sizeof($mess) > 0) {
                        $messages[] = $mess;
                    }
                }
                $mess = $compiler->generateSections();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
            if (inArray(SURVEY_COMPONENT_VARIABLE, $components)) {
                $mess = $compiler->generateVariableDescriptives();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
            if (inArray(SURVEY_COMPONENT_TYPE, $components)) {

                $mess = $compiler->generateTypes();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
            if (inArray(SURVEY_COMPONENT_SETTING, $components)) {
                $mess = $compiler->generateSurveySettings();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
            if (inArray(SURVEY_COMPONENT_FILL, $components)) {
                $mess = $compiler->generateGetFills();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
                $mess = $compiler->generateSetFills();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
            if (inArray(SURVEY_COMPONENT_INLINEFIELDS, $components)) {
                $mess = $compiler->generateInlineFields();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
            if (inArray(SURVEY_COMPONENT_GROUP, $components)) {
                $mess = $compiler->generateGroups();
                if (is_array($mess) && sizeof($mess) > 0) {
                    $messages[] = $mess;
                }
            }
        }

        $survey = $currentsurvey;
        if (sizeof($messages) == 0) {
            $content = $displaySysAdmin->displaySuccess(Language::messageToolsCompileOk());
        } else {
            $content = $displaySysAdmin->displayError(Language::messageToolsCompileNotOk());
        }
        return $displaySysAdmin->showCompile($content);
    }

    function showXiCompile() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showXiCompile();
    }

    function showXiCompileRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displaySysAdmin = new DisplaySysAdmin();
        $compile = loadvar("compile");
        if ($compile == "") {
            return $displaySysAdmin->showXiCompile($displaySysAdmin->displayWarning(Language::messageToolsCompileSelectSurvey()));
        }
        return $displaySysAdmin->showXiCompileRes();
    }

    function showExport() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showExport();
    }

    function showExportRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));
        require_once("exporter.php");
        $exporter = new Exporter(loadvar(POST_PARAM_SUID));
        $exporter->export();
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showExport();
    }

    function showImport() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showImport();
    }

    function showImportRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        require_once("importer.php");
        $importer = new Importer();
        $result = $importer->import();
        $displaySysAdmin = new DisplaySysAdmin();
        if ($result == "") {
            $content = $displaySysAdmin->displaySuccess(Language::messageToolsImportOk());
        } else {
            $content = $displaySysAdmin->displayError(Language::messageToolsImportNotOk($result));
        }

        return $displaySysAdmin->showImport($content);
    }

    function showTest() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showTest();
    }

    function showIssues() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showIssues();
    }

    function showFlood() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showFlood();
    }

    function showFloodRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));
        $displaySysAdmin = new DisplaySysAdmin();
        $message = $displaySysAdmin->displaySuccess(Language::messageToolsFlooderDone(loadvar("number")));
        require_once("flooder.php");
        $flooder = new Flooder();
        $flooder->generateCases();
        return $displaySysAdmin->showFlood($message);
    }

    function showPreferences() {
        $displaySysAdmin = new DisplaySysAdmin();
        return $displaySysAdmin->showPreferences();
    }

    function showPreferencesRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], ".res"));

        $user = new User($_SESSION['URID']);
        $user->setNavigationInBreadCrumbs(loadvar('navigationinbreadcrumbs'));
        $user->setRoutingAutoIndentation(loadvar('routingautoindentation'));
        $user->setHTMLEditor(loadvar('htmleditor'));
        $user->setItemsInTable(loadvar('itemsintable'));
        $user->saveChanges();
        $displaySysAdmin = new DisplaySysAdmin();
        $content = $displaySysAdmin->displaySuccess(Language::messagePreferencesSaved());
        return $displaySysAdmin->showPreferences($content);
    }

    function showUsers() {
        $displayUsers = new displayUsers();
        return $displayUsers->showUsers();
    }

    function showEditUser() {
        if (getFromSessionParams('urid') != "") {
            $_SESSION['LASTURID'] = getFromSessionParams('urid');
        }
        $displayUsers = new DisplayUsers();
        return $displayUsers->showEditUser($_SESSION['LASTURID']);
    }

    function showEditUserRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.users.edituser';

        $displayUsers = new DisplayUsers();
        $urid = getFromSessionParams('urid');
        $content = "";
        if ($urid != '') { //edit
            $content = $displayUsers->displaySuccess(Language::messageUserChanged(loadvar('name')));
            $_SESSION['LASTURID'] = $urid;
        } else { //add user!
            if (loadvar('username') != "" && loadvar('name') != '') {

                //ADD NEW!!!
                $user = new User('', true);
                $urid = $user->getUrid();
                $_SESSION['LASTURID'] = $urid;
                $content = $displayUsers->displaySuccess(Language::messageUserAdded(loadvar('name')));
            }
        }

        //ADD ALL SORTS OF CHECKS!!
        if ($urid != '' && loadvar('name') != "" && loadvar('username') != '') {

            $user = new User($urid);
            $user->setName(loadvar('name'));
            $username = loadvar('username');
            require_once("users.php");
            $users = new Users();
            $existing = $users->getUsersByName($username);
            //$content = "";
            if (sizeof($existing) > 0) {
                $wrong = false;
                foreach ($existing as $e) {
                    if ($e->getUrid() != $urid) {
                        $wrong = true;
                        break;
                    }
                }
                if ($wrong == true) {
                    $content = $displayUsers->displayWarning(Language::messageUserDuplicateUsername());
                    $username .= "_duplicate";
                }
            }

            $user->setUsername($username);
            if (loadvar('pwd1') != '') {
                if (loadvar('pwd1') == loadvar('pwd2')) {
                    $user->setPassword(loadvar('pwd1'));
                } else {
                    $content .= $displayUsers->displayWarning(Language::messageUserNoMatch());
                }
            }
            $user->setSupervisor(loadvar('uridsel'));
            $user->setStatus(loadvar('status'));
            $user->setUserType(loadvar('usertype'));
            if (loadvar('usertype') == USER_SYSADMIN) {
                $user->setUserSubType(loadvar('usersubtype'));
            } else if (loadvar('usertype') == USER_NURSE) {
                $user->setUserSubType(loadvar('usersubtypenurse'));
            }
            $current = $user->getSurveysAccess();

            $allowedsurveys = loadvar(SETTING_USER_SURVEYS);

            // add access to all modes and languages if not specified in current access
            if (inArray(loadvar('usertype'), array(USER_RESEARCHER, USER_SYSADMIN, USER_TRANSLATOR, USER_TESTER))) {
                foreach ($allowedsurveys as $a) {
                    if (!inArray($a, $current)) {
                        $surv = new Survey($a);
                        $mods = explode("~", $surv->getAllowedModes());
                        foreach ($mods as $m) {
                            $user->setLanguages($a, $m, $surv->getAllowedLanguages($m));
                        }
                    }
                }
                foreach ($current as $c) {
                    if (!inArray($c, $allowedsurveys)) {
                        $user->removeSurvey($c);
                    }
                }
            }
            $user->saveChanges();

            // current survey not in allowed, then update to first survey for user
            if (inArray(loadvar('usertype'), array(USER_RESEARCHER, USER_SYSADMIN, USER_TRANSLATOR, USER_TESTER))) {
                if (!inArray($_SESSION['SUID'], $allowedsurveys)) {
                    $surveys = new Surveys();
                    $_SESSION['SUID'] = $surveys->getFirstSurvey();
                }
            }
        } else {
            $content = $displayUsers->displayWarning(Language::messageUserCorrectErrors());
        }
        return $displayUsers->showEditUser($_SESSION['LASTURID'], $content);
    }

    function showEditUserAccessRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = 'sysadmin.users.edituser';

        $displayUsers = new DisplayUsers();
        $urid = getFromSessionParams('urid');
        $_SESSION['LASTURID'] = $urid;

        // store access
        $user = new User($urid);
        $content = $displayUsers->displaySuccess(Language::messageUserChanged($user->getName()));
        $surv = loadvar(SMS_POST_SURVEY);
        $allmodes = Common::surveyModes();
        foreach ($allmodes as $k => $all) {
            if (loadvar(SETTING_USER_MODE . $k) == USER_MODE_YES) {
                $ans = loadvar(SETTING_USER_LANGUAGES . $k);
                if (!is_array($ans)) {
                    $ans = array($ans);
                }
                if (sizeof($ans) > 0) {
                    $user->setLanguages($surv, $k, implode("~", $ans));
                }
            } else {
                $user->removeMode($surv, $k);
            }
        }
        $user->saveChanges();
        return $displayUsers->showEditUser($_SESSION['LASTURID'], $content);
    }

    function showCopyUser() {
        $user_copyfrom = new User(getFromSessionParams('urid'));
        $user = new User('', true);
        $user->setName($user_copyfrom->getName());
        $user->setUsername($user_copyfrom->getUsername());
        $user->saveChanges();
        $displayUsers = new DisplayUsers();
        $_SESSION['LASTPAGE'] = "sysadmin.users.edituser";
        return $displayUsers->showEditUser($user->getUrid());
    }

    function showRemoveUser() {
        $content = '';
        $user = new User(getFromSessionParams('urid'));
        $displayUsers = new DisplayUsers();
        if ($user->getUrid() != $_SESSION['URID']) { //don't delete yourself!
            $user->delete();
            $content = $displayUsers->displaySuccess(Language::messageUserDeleted());
        } else {
            $content = $displayUsers->displayWarning(Language::messageUserNotDeleted());
        }
        $_SESSION['LASTPAGE'] = "sysadmin.users";
        return $displayUsers->showUsers($content);
    }

    function determineModeLanguage(&$de) {
        $user = new User($_SESSION['URID']);
        $modes = $user->getModes(loadvar('survey'));

        $mods = "";
        if (loadvar(DATA_OUTPUT_MODES) == "") {
            $mods = implode("~", $modes);
        } else {
            $ms = loadvar(DATA_OUTPUT_MODES);
            $ms1 = array();
            foreach ($ms as $m) {
                if (inArray($m, $modes)) {
                    $ms1[] = $m;
                }
            }
            $mods = implode("~", $ms1);
        }
        $de->setProperty(DATA_OUTPUT_MODES, $mods);

        $modes = explode("~", $mods);
        $langs = "";
        if (loadvar(DATA_OUTPUT_LANGUAGES) == "") {
            $langs = array();
            foreach ($modes as $m) {
                $langs = explode("~", $user->getLanguages(loadvar('survey'), $m));
            }
            $langs = implode("~", array_unique($langs));
        } else {
            $ls = loadvar(DATA_OUTPUT_LANGUAGES);
            $ls1 = array();
            foreach ($ls as $l) {
                foreach ($modes as $m) {
                    if (inArray($l, explode("~", $user->getLanguages(loadvar('survey'), $m)))) {
                        $ls1[] = $l;
                        break;
                    }
                }
            }
            $langs = implode("~", array_unique($ls1));
        }
        $de->setProperty(DATA_OUTPUT_LANGUAGES, $langs);
    }

}

?>