<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Researcher {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {
        if (loadvar('updatesessionpage') != 2) {
            if (getFromSessionParams('page') != "") {
                $_SESSION['LASTPAGE'] = getFromSessionParams('page');
            }
        }
        if (isset($_SESSION['LASTPAGE'])) {
            switch ($_SESSION['LASTPAGE']) {

                case 'researcher.data': return $this->showData();
                    break;
                case 'researcher.data.rawdata': return $this->ShowRawData();
                    break;
                case 'researcher.data.timings': return $this->ShowTimingsData();
                    break;
                case 'researcher.data.timingsres': return $this->showTimingsDataRes();
                    break;
                case 'researcher.data.paradata': return $this->ShowParaData();
                    break;
                case 'researcher.data.paradatares': return $this->showParaDataRes();
                    break;
                case 'researcher.data.auxiliarydata': return $this->ShowAuxData();
                    break;
                case 'researcher.data.auxiliarydatares': return $this->ShowAuxDataRes();
                    break;
                case 'researcher.datasingle': return $this->showDataSingle();
                    break;
                case 'researcher.datasingleres': return $this->showDataSingleRes();
                    break;
                //case 'researcher.datamultiple': return $this->showDataMultiple(); break;
                //case 'researcher.datamultipleres': return $this->showDataMultipleRes(); break;

                case 'researcher.data.other': return $this->showOtherData();
                    break;

                case 'researcher.reports': return $this->showReports();
                    break;
                case 'researcher.reports.responseoverview': return $this->showReportsResponse();
                    break;
                case 'researcher.reports.aggregates': return $this->showReportsAggregates();
                    break;
                case "researcher.reports.aggregates.section": return $this->showReportsAggregatesSection();
                    break;
                case "researcher.reports.aggregates.variable": return $this->showReportsAggregatesVariable();
                    break;

                case 'researcher.reports.paradata': return $this->showReportsParadata();
                    break;
                case "researcher.reports.paradata.section": return $this->showReportsParadataSection();
                    break;
                case "researcher.reports.paradata.variable": return $this->showReportsParadataVariable();
                    break;

                case "researcher.reports.contact.graphs": return $this->showReportsContactGraphs();
                    break;
                case "researcher.reports.timings.distribution": return $this->showReportsTimingsDistribution();
                    break;
                case "researcher.reports.timings.overtime": return $this->showReportsTimingsOverTime();
                    break;
                case "researcher.reports.timings.perscreen": return $this->showReportsTimingsPerScreen();
                    break;
                case "researcher.reports.timings.perscreen.res": return $this->showReportsTimingsPerScreenRes();
                    break;
                case "researcher.reports.platform": return $this->showReportsPlatform();
                    break;

                case 'researcher.documentation': return $this->showDocumentation();
                    break;

                case 'sysadmin.output.datasingleres': return $this->downloadData();
                    break;

                case 'researcher.sample': return $this->showSample();
                    break;
                case 'researcher.sample.assign': return $this->showAssignSample();
                    break;
                case 'researcher.sample.download': return $this->showSampleDownload();
                    break;
                case 'researcher.sample.download.gps': return $this->showSampleDownloadGPS();
                    break;

                case "researcher.output.documentation": return $this->showOutputDocumentation();
                    break;
                case "researcher.output.documentation.routing": return $this->showOutputRouting();
                    break;
                case "researcher.output.documentation.routing.dash": return $this->showOutputRoutingDash();
                    break;
                case "researcher.output.documentation.dictionary": return $this->showOutputDictionary();
                    break;
                case "researcher.output.documentation.translation": return $this->showOutputTranslation();
                    break;

                default: return $this->mainPage();
            }
        } else {
            return $this->mainPage();
        }
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

    function showOutputDictionary() {
        $displayOutput = new DisplayOutput();
        return $displayOutput->showOutputDictionary();
    }

    function mainPage() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showMain();
    }

    function showData() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showData();
    }

    function showDataSingle() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showDataSingleSurvey();
    }

    function showDataSingleRes() {

        /* update last page */
        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

        $displayResearcher = new DisplayResearcher();
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

        $this->determineModeLanguage($de);
        $de->setProperty(DATA_OUTPUT_MARK_EMPTY, loadvar(DATA_OUTPUT_MARK_EMPTY));
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
        $de->setProperty(DATA_OUTPUT_FROM, loadvar(DATA_OUTPUT_FROM));
        $de->setProperty(DATA_OUTPUT_TO, loadvar(DATA_OUTPUT_TO));
        //$de->displayProperties();
        $de->generate();
        $de->download();

        //$de->displayProperties();
        //$de->writeCSVFile();
        //$de->displayLog();
        return $displayResearcher->showRawData();
    }

    /*
      function showDataMultiple() {
      $displayResearcher = new DisplayResearcher();
      return $displayResearcher->showDataMultipleSurvey();
      }
     */

//    function showDataMultipleRes() {

    /* update last page */
    /*        $_SESSION['LASTPAGE'] = substr($_SESSION['LASTPAGE'], 0, strripos($_SESSION['LASTPAGE'], "res"));

      $displayResearcher = new DisplayResearcher();
      return $displayResearcher->showDataMultipleSurveyRes();
      }
     */
    function showRawData() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showRawData();
    }

    function showTimingsData() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showTimingsData();
    }

    function showTimingsDataRes() {

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

        $this->determineModeLanguage($de);

        //echo $mods . '----' . $langs;        
        //exit;
        $de->setProperty(DATA_OUTPUT_VARLIST, $cookievars);
        $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));
        // $de->setProperty(DATA_OUTPUT_LANGUAGES, implode("~", loadvar(DATA_OUTPUT_LANGUAGES)));
        //$de->setProperty(DATA_OUTPUT_MODES, loadvar(DATA_OUTPUT_MODES));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, loadvar(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
        $de->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, loadvar(DATA_OUTPUT_PRIMARY_KEY_IN_DATA));
        $de->setProperty(DATA_OUTPUT_SURVEY, loadvar(DATA_OUTPUT_SURVEY));
        $de->setProperty(DATA_OUTPUT_TYPEDATA, loadvar(DATA_OUTPUT_TYPEDATA));
        $de->setProperty(DATA_OUTPUT_TYPE, loadvar(DATA_OUTPUT_TYPE));
        $de->setProperty(DATA_OUTPUT_FROM, loadvar(DATA_OUTPUT_FROM));
        $de->setProperty(DATA_OUTPUT_TO, loadvar(DATA_OUTPUT_TO));
        $de->generateTimings();
        $de->download();
    }

    function showParaData() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showParadata();
    }

    function showParaDataRes() {

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
        $de->setProperty(DATA_OUTPUT_FROM, loadvar(DATA_OUTPUT_FROM));
        $de->setProperty(DATA_OUTPUT_TO, loadvar(DATA_OUTPUT_TO));
        if (loadvar(DATA_OUTPUT_TYPEPARADATA) == PARADATA_RAW) {
            $de->setProperty(DATA_OUTPUT_FILETYPE, FILETYPE_CSV);
            $de->generateParadata();
        } else {
            $de->setProperty(DATA_OUTPUT_FILETYPE, loadvar(DATA_OUTPUT_FILETYPE));
            $de->generateProcessedParadata();
        }
        $de->download();
    }

    function showAuxData() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showAuxData();
    }

    function showAuxDataRes() {

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
        $de->setProperty(DATA_OUTPUT_FROM, loadvar(DATA_OUTPUT_FROM));
        $de->setProperty(DATA_OUTPUT_TO, loadvar(DATA_OUTPUT_TO));
        //$de->displayProperties();
        $de->generateAuxiliary();
        $de->download();
        //$de->displayProperties();
        //$de->writeCSVFile();
        //$de->displayLog();
        return $displayOutput->showOutputAddOnData();
    }

    function showOtherData() {
        global $db;
        $type = getFromSessionParams('type');
        if ($type != '') {
            $filename = '_' . date('YmdHis');
            $query = '';
            switch ($type) {
                case 1:
                    $filename = 'households' . $filename;
                    $query = 'select primkey,urid,puid,status,ts from ' . dbConfig::dbSurvey() . '_households where test = 0 order by primkey';

                    break;

                case 2:
                    $filename = 'respondents' . $filename;
                    $query = 'select primkey,hhid,urid,status,selected,present,hhhead,finr,famr,permanent,validation,ts from ' . dbConfig::dbSurvey() . '_respondents where test = 0 order by primkey';
                    break;

                case 3:
                    $filename = 'contacts' . $filename;
                    $query = 'select primkey,code,contactts,proxy,urid, aes_decrypt(remark, "' . Config::smsContactRemarkKey() . '") as remark, ts from ' . dbConfig::dbSurvey() . '_contacts where primkey not like "999%"';
                    break;

                case 4:
                    $filename = 'remarks' . $filename;
                    $query = 'select primkey,urid, aes_decrypt(remark, "' . Config::smsRemarkKey() . '") as remark, ts from ' . dbConfig::dbSurvey() . '_remarks where primkey not like "999%"';
                    break;
            }
            if ($query != '') {
                $result = $db->selectQuery($query);
                createCSV($result, $filename);
            }
        }
    }

    function showReports() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReports();
    }

    function showReportsResponse() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsResponse();
    }

    function showReportsAggregates() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsAggregates();
    }

    function showReportsAggregatesSection() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        $displayResearcher = new displayResearcher();
        return $displayResearcher->showReportsAggregatesSection($_SESSION['SEID']);
    }

    function showReportsAggregatesVariable() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        $displayResearcher = new displayResearcher();
        return $displayResearcher->showReportsAggregatesVariable($_SESSION['SEID'], $_SESSION['VSID']);
    }

    function showReportsParadata() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsParadata();
    }

    function showReportsParadataSection() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        $displayResearcher = new displayResearcher();
        return $displayResearcher->showReportsParadataSection($_SESSION['SEID']);
    }

    function showReportsParadataVariable() {
        if (getFromSessionParams('seid') != "") {
            $_SESSION['SEID'] = getFromSessionParams('seid');
        }
        if (getFromSessionParams('vsid') != "") {
            $_SESSION['VSID'] = getFromSessionParams('vsid');
        }
        $displayResearcher = new displayResearcher();
        return $displayResearcher->showReportsParadataVariable($_SESSION['SEID'], $_SESSION['VSID']);
    }

    function showReportsContactGraphs() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsContactGraphs(getFromSessionParams('seid'));
    }

    function showReportsTimingsDistribution() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsTimingsDistribution();
    }

    function showReportsTimingsOverTime() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsTimingsOverTime();
    }

    function showReportsTimingsPerScreen() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsTimingsPerScreen();
    }

    function showReportsTimingsPerScreenRes() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsTimingsPerScreenRes();
    }

    function showReportsPlatform() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showReportsPlatform();
    }

    function showDocumentation() {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showDocumenation();
    }

    function showSample($message = '') {
        $displayResearcher = new DisplayResearcher();
        return $displayResearcher->showSample($message);
    }

    function downloadData() {
        $SysAdmin = new SysAdmin(); //download data directly
        $SysAdmin->getPage();
    }

    function showAssignSample() {
        $SysAdmin = new SysAdmin();
        $message = $SysAdmin->assignSample(loadvar('assignid'), loadvar('selurid'));
        $display = new Display();
        return $this->showSample($display->displayInfo($message));
    }

    function showSampleDownload() {
        $SysAdmin = new SysAdmin(); //download unassigned sample data directly
        $SysAdmin->showSampleDownload();
    }

    function showSampleDownloadGPS() {
        $SysAdmin = new SysAdmin(); //download unassigned sample data directly
        $SysAdmin->showSampleDownloadGPS();
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