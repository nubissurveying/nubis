<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DataExport {

    private $db;
    private $suid;
    private $properties;
    private $variablenames = array();
    private $lookup = array();
    private $valuelabels = array();
    private $labels = array();
    private $datatypes = array();
    private $answerwidth = array();
    private $variablestosplit = array();
    private $withsuffix = array();
    private $realsuffix = array();
    private $log = array();
    private $recordcount;
    private $variablenumber;
    private $csvhandle;
    private $statahandle;
    private $variabletypes;
    private $variables;
    private $formats;
    private $littleendian;
    private $shortempty;
    private $shorterror;
    private $shortdk;
    private $shortrf;
    private $shortna;
    private $shortmarkempty;
    private $doubleempty;
    private $doublerror;
    private $doubledk;
    private $doublerf;
    private $doublena;
    private $doublemarkempty;
    private $floatempty;
    private $floaterror;
    private $floatdk;
    private $floatrf;
    private $floatna;
    private $floatmarkempty;
    private $downloadlocation;
    private $survey;
    private $variabledescriptives;
    private $primkeylength;
    private $arrayfields;
    private $skipvariables;
    private $descriptives; // retrieved variable descriptive objects
    private $asked;
    private $currentrecord;
    private $minprimkeylength;
    private $maxprimkeylength;
    private $languages;
    private $modes;
    private $versions;
    private $encoding;
    private $maxwidths;
    private $chrmap;
    private $setofenumeratedvariables;

    function __construct($suid) {
        global $db, $survey;
        $this->db = $db;
        $this->suid = prepareDatabaseString($suid);
        $this->survey = new Survey($this->suid);
        $survey = $this->survey;
        $this->variabledescriptives = array();

        $this->setProperty(DATA_OUTPUT_MAINTABLE, Config::dbSurvey());
        $this->setProperty(DATA_OUTPUT_MAINDATATABLE, Config::dbSurveyData());


        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME, $this->getProperty(DATA_OUTPUT_MAINTABLE));

        /* set defaults */
        $this->setProperty(DATA_OUTPUT_FILETYPE, FILETYPE_STATA);
        $this->setProperty(DATA_OUTPUT_SURVEY, "");
        $this->setProperty(DATA_OUTPUT_MODES, "");
        $this->setProperty(DATA_OUTPUT_LANGUAGES, "");
        $this->setProperty(DATA_OUTPUT_VERSIONS, "");
        $this->setProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA, PRIMARYKEY_YES);
        $this->setProperty(DATA_OUTPUT_HIDDEN, DATA_NOTHIDDEN);
        $this->setProperty(DATA_OUTPUT_CLEAN, DATA_DIRTY);
        $this->setProperty(DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS, VALUELABELNUMBERS_YES);
        $this->setProperty(DATA_OUTPUT_FIELDNAME_CASE, FIELDNAME_LOWERCASE);
        $this->setProperty(DATA_OUTPUT_INCLUDE_VALUE_LABELS, VALUELABEL_YES);
        $this->setProperty(DATA_OUTPUT_VARIABLES_WITHOUT_DATA, VARIABLES_WITHOUT_DATA_YES);
        $this->setProperty(DATA_OUTPUT_VALUELABEL_PREFIX, $this->getProperty(DATA_OUTPUT_MAINTABLE) . "_vl");
        $this->setProperty(DATA_OUTPUT_VALUELABEL_WIDTH, VALUELABEL_WIDTH_FULL);
        $this->setProperty(DATA_OUTPUT_ENCODING, "UTF-8");
        $this->setProperty(DATA_OUTPUT_COMPLETED, INTERVIEW_NOTCOMPLETED);
        $this->setProperty(DATA_OUTPUT_MARK_EMPTY, MARKEMPTY_IN_VARIABLE);
        $this->setProperty(DATA_OUTPUT_KEEP_ONLY, DATA_KEEP_NO);
        $this->setProperty(DATA_OUTPUT_CLEAN, DATA_CLEAN);
        $this->setProperty(DATA_OUTPUT_TYPEDATA, DATA_REAL);
        $this->setProperty(DATA_OUTPUT_VARLIST, "");
        $this->setProperty(DATA_OUTPUT_TYPE, DATA_OUTPUT_TYPE_DATA_TABLE);
        $this->setProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION, "");
        $this->setProperty(DATA_OUTPUT_FROM, "");
        $this->setProperty(DATA_OUTPUT_TO, "");
        $this->minprimkeylength = Config::getMinimumPrimaryKeyLength();
        $this->maxprimkeylength = Config::getMaximumPrimaryKeyLength();

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        $this->chrmap = array(
            // Windows codepage 1252
            "\xC2\x82", // U+0082⇒U+201A single low-9 quotation mark
            "\xC2\x84", // U+0084⇒U+201E double low-9 quotation mark
            "\xC2\x8B", // U+008B⇒U+2039 single left-pointing angle quotation mark
            "\xC2\x91", // U+0091⇒U+2018 left single quotation mark
            "\xC2\x92", // U+0092⇒U+2019 right single quotation mark
            "\xC2\x93", // U+0093⇒U+201C left double quotation mark
            "\xC2\x94", // U+0094⇒U+201D right double quotation mark
            "\xC2\x9B", // U+009B⇒U+203A single right-pointing angle quotation mark
            // Regular Unicode     // U+0022 quotation mark (")
            // U+0027 apostrophe     (')
            "\xC2\xAB", // U+00AB left-pointing double angle quotation mark
            "\xC2\xBB", // U+00BB right-pointing double angle quotation mark
            "\xE2\x80\x98", // U+2018 left single quotation mark
            "\xE2\x80\x99", // U+2019 right single quotation mark
            "\xE2\x80\x9A", // U+201A single low-9 quotation mark
            "\xE2\x80\x9B", // U+201B single high-reversed-9 quotation mark
            "\xE2\x80\x9C", // U+201C left double quotation mark
            "\xE2\x80\x9D", // U+201D right double quotation mark
            "\xE2\x80\x9E", // U+201E double low-9 quotation mark
            "\xE2\x80\x9F", // U+201F double high-reversed-9 quotation mark
            "\xE2\x80\xB9", // U+2039 single left-pointing angle quotation mark
            "\xE2\x80\xBA", // U+203A single right-pointing angle quotation mark
        );
    }    

    function setProperty($property, $value) {
        $this->properties[$property] = $value;

        // update data tables to be used: real versus test data
        if ($property == DATA_OUTPUT_TYPEDATA) {
            if ($value == DATA_TEST) {
                $this->setProperty(DATA_OUTPUT_MAINDATATABLE, Config::dbSurveyData() . "_test");
            } else {
                $this->setProperty(DATA_OUTPUT_MAINDATATABLE, Config::dbSurveyData());
            }
        }
    }

    function getProperty($property) {
        if (isset($this->properties[$property])) {
            if (is_array($this->properties[$property])) {
                return $this->properties[$property];
            }
            return prepareDatabaseString($this->properties[$property]);
        }
    }

    function displayProperties() {
        foreach ($this->properties as $n => $v) {
            if (is_array($v)) {
                echo "<b>" . $n . "</b>: " . implode(" ", $v) . "<br/>";
            } else {
                echo "<b>" . $n . "</b>: " . $v . "<br/>";
            }
        }
    }

    function getSection($seid) {
        return $this->survey->getSection($seid);
    }

    function getType($tyd) {
        return $this->survey->getType($tyd);
    }

    function getVariableDescriptive($name) {
        if (isset($this->variabledescriptives[strtoupper($name)])) {
            return $this->variabledescriptives[strtoupper($name)];
        }
        $this->variabledescriptives[strtoupper($name)] = $this->survey->getVariableDescriptiveByName($name);
        return $this->variabledescriptives[strtoupper($name)];
    }

    /* AUXILIARY DATA FILE */

    function startCSVAuxiliaryFile() {

        $this->csvhandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }
        $this->csvhandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV), "w");
        if (!$this->csvhandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV);

        $separator = ",";
        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) == PRIMARYKEY_NO) {
            $header = "variable, language, mode, version, timestamp";
        } else {
            $header = "primkey, variable, language, mode, version, timestamp";
        }

        /* write headers */
        if ($this->csvhandle) {
            fwrite($this->csvhandle, $header . "\n");
        }
    }

    function generateAuxiliary() {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);
        ini_set('memory_limit', Config::dataExportMemoryLimit());

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . "_auxiliary.csv");

        /* set arrays */
        if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);
        
        $this->arrayfields = array();
        $this->skipvariables = array();
        $this->setofenumeratedbinary = array();
        $this->descriptives = array();
        $this->withsuffix = array();
        
        $extracompleted = '';

        /* get records to consider if only completed */
        if ($this->getProperty(DATA_OUTPUT_COMPLETED) == INTERVIEW_COMPLETED) {
            $extracompleted = " and completed=" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_COMPLETED));
        }
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extracompleted .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extracompleted .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }

        $datanames = array();
        $extra = "";
        if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATARECORD_TABLE) {
            $query = "select distinct datanames from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_datarecords where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra;
            $res = $this->db->selectQuery($query);
            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {
                    /* go through records */
                    while ($row = $this->db->getRow($res)) {
                        $datanames = array_unique(array_merge($datanames, explode("~", gzuncompress($row["datanames"]))));
                        $row = null;
                        unset($row);
                    }
                }
            }
        } else {
            $query = "select distinct variablename from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra;
            $res = $this->db->selectQuery($query);
            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {
                    /* go through records */
                    while ($row = $this->db->getRow($res)) {
                        $datanames[] = $row["variablename"];
                        $row = null;
                        unset($row);
                    }
                    $datanames = array_unique($datanames);
                }
            }
        }

        // check for filter
        $filter = array();
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", $this->getProperty(DATA_OUTPUT_VARLIST));
        }

        /* collect data names */
        $vars = array();
        foreach ($datanames as $d) {
            $vd = $this->getVariableDescriptive(getBasicName($d));
            if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it           
                // hidden variable
                if ($this->getProperty(DATA_OUTPUT_HIDDEN) == HIDDEN_YES && $vd->isHidden()) {
                    continue;
                } else if (sizeof($filter) > 0 && !inArray($vd->getName(), $filter)) {
                    continue;
                }

                // create index to track
                $section = $this->getSection($vd->getSeid());
                $key = $vd->getSuid() . $vd->getSeid() . $vd->getVsid();
                if (isset($vars[$key])) {
                    $arrtemp = $vars[$key];
                    $arr = $arrtemp["vars"];
                } else {
                    $arr = array();
                }
                
                $arr[] = strtoupper($d); // this needs to work to ensure we are getting the right array and putting it in $vars array                
                $vars[$key] = array("order" => $section->getPosition() . $vd->getSeid() . $vd->getPosition() . $vd->getVsid(), "vars" => $arr);
            }
        }

        $datanames = null;
        unset($datanames);
        $varnames = array();

        /* sort data names by section position, section seid, variable position, variable name */
        uasort($vars, 'dataexportSort');

        // get variable names
        foreach ($vars as $key => $subvars) {
            $subvars = $subvars["vars"];
            ksort($subvars, SORT_STRING); // sort by variable name
            foreach ($subvars as $d) {
                $varnames[] = $d;
            }
        }

        /* start writing files */
        $outputtype = strtolower($this->getProperty(DATA_OUTPUT_FILETYPE));
        $this->startCSVAuxiliaryFile();

        // no variables
        if (sizeof($varnames) == 0) {
            return 'No records found';
        }

        $this->separator = ",";

        /* go through all entries */

        // datarecords based
        if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATARECORD_TABLE) {
            $decrypt = "data as data_dec";
            if ($this->survey->getDataEncryptionKey() != "") {
                $decrypt = "aes_decrypt(data, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as data_dec";
            }
            $query = "select *, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_datarecords where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . " " . $extracompleted . " order by primkey";
            $res = $this->db->selectQuery($query);
            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {
                    /* go through records */
                    while ($row = $this->db->getRow($res)) {
                        $record = new DataRecord();
                        $record->setAllData(unserialize(gzuncompress($row["data_dec"])));

                        for ($i = 0; $i < sizeof($varnames); $i++) {
                            $variable = $record->getData($varnames[$i]);
                            $line = "";
                            if ($variable) {

                                /* no match on language, mode and version, then treat as never gotten */
                                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($variable->getLanguage(), $this->languages)))) {//
                                    continue;
                                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($variable->getMode(), $this->modes)))) {//
                                    continue;
                                }

                                if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                                        $prim = encryptC($row["primkey"], $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
                                    } else {
                                        $prim = $row["primkey"];
                                    }
                                    $line .= $prim . $this->separator;
                                }
                                // language, mode, version, timestamp
                                $line .= strtolower($varnames[$i]) . $this->separator . $variable->getLanguage() . $this->separator . $variable->getMode() . $this->separator . $variable->getVersion() . $this->separator . $variable->getTs();
                                fwrite($this->csvhandle, $line . "\n");
                                $line = null;
                                unset($line);
                            }
                        }
                        $record = null;
                        $row = null;
                        unset($record);
                        unset($row);
                    }
                }
            }
        }
        // data based
        else {
            $query = "select distinct primkey from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . " " . $extracompleted . " order by primkey";
            $res = $this->db->selectQuery($query);
            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {

                    while ($row = $this->db->getRow($res)) {

                        $query2 = "select * from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($row["primkey"]) . "'";
                        $currentrecord = array();
                        $res2 = $this->db->selectQuery($query2);
                        if ($res2) {
                            while ($row2 = $this->db->getRow($res2)) {

                                /* no match on language, mode and version, then treat as never gotten */
                                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($row2["language"], $this->languages)))) {//
                                    continue;
                                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($row2["mode"], $this->modes)))) {//
                                    continue;
                                }

                                $currentrecord[strtoupper($row2["variablename"])] = array("name" => $row2["variablename"], "language" => $row2["language"], "mode" => $row2["mode"], "version" => $row2["version"], "ts" => $row2["ts"]);
                                $row2 = null;
                                unset($row2);
                            }

                            // loop through variables
                            for ($i = 0; $i < sizeof($varnames); $i++) {
                                $line = "";
                                if (isset($currentrecord[strtoupper($varnames[$i])])) {
                                    $info = $currentrecord[strtoupper($varnames[$i])];

                                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                                        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                                            $prim = encryptC($row["primkey"], $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
                                        } else {
                                            $prim = $row["primkey"];
                                        }
                                        $line .= $prim . $this->separator;
                                    }
                                    $line .= strtolower($varnames[$i]) . $this->separator . $info["language"] . $this->separator . $info["mode"] . $this->separator . $info["version"] . $this->separator . $info["ts"];
                                    fwrite($this->csvhandle, $line . "\n");
                                    $info = null;
                                    unset($info);
                                    $line = null;
                                    unset($line);
                                }
                            }
                            $currentrecord = null;
                            unset($currentrecord);
                        }

                        $row = null;
                        unset($row);
                    }
                }
            }
        }

        /* finish */
        $this->finishCSVFile();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* REMARK FILES */

    /* WRITE CSV FILE */

    function startCSVRemarkFile() {

        $this->csvhandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }
        $this->csvhandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV), "w");
        if (!$this->csvhandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV);

        $separator = ",";
        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) == PRIMARYKEY_NO) {
            $header = "variables, remark, language, mode, version, timestamp";
        } else {
            $header = "primkey, variables, remark, language, mode, version, timestamp";
        }

        /* write headers */
        if ($this->csvhandle) {
            fwrite($this->csvhandle, $header . "\n");
        }
    }

    function generateRemarks() {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);

        /* set arrays */
        if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . "_remarks.csv");

        // declare stuff
        $primkeys = array();
        $vars = array();

        // check for filter
        $filter = array();
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", $this->getProperty(DATA_OUTPUT_VARLIST));
        }

        // get all variables and figure out which ones we can include remarks for
        $this->variabledescriptives = $this->survey->getVariableDescriptives();
        foreach ($this->variabledescriptives as $vd) {

            // hidden variable
            if ($this->getProperty(DATA_OUTPUT_HIDDEN) == DATA_HIDDEN && $vd->isHidden()) {
                continue;
            } else if (sizeof($filter) > 0 && !inArray($vd->getName(), $filter)) {
                continue;
            }
            // kept variables only
            else if ($this->getProperty(DATA_OUTPUT_KEEP_ONLY) == DATA_KEEP_YES) {
                if ($vd->getDataKeep() != DATA_KEEP_YES) {
                    continue;
                }
            }
            $vars[] = strtoupper($vd->getName());
        }

        /* get records to consider if only completed */
        $extracompleted = "";
        $extra = "";
        if ($this->getProperty(DATA_OUTPUT_COMPLETED) == INTERVIEW_COMPLETED) {
            $extracompleted = " and completed=" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_COMPLETED));
        }
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extracompleted .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
            $extra .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extracompleted .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
            $extra .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }
        $primkeys = array();
        if ($extracompleted != "") {
            if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATARECORD_TABLE) {
                $query = "select distinct primkey from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_datarecords where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted;
                $res = $this->db->selectQuery($query);
            } else {
                $query = "select distinct primkey from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted;
                $res = $this->db->selectQuery($query);
            }

            // collect
            if ($db->getNumberOfRows($res) > 0) {
                while ($row = $db->getRow($res)) {
                    $primkeys[] = $row["primkey"];
                    $row = null;
                    unset($row);
                }
            }
            $res = null;
            unset($res);
        }


        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);

        /* start writing files */
        $outputtype = strtolower($this->getProperty(DATA_OUTPUT_FILETYPE));
        $this->startCSVRemarkFile();
        $this->separator = ",";

        /* go through all remarks */
        $decrypt = "remark as remark_dec";
        if ($this->survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(remark, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as remark_dec";
        }
        $query = "select *, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_observations where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extra . " order by primkey";
        $res = $this->db->selectQuery($query);

        if ($res) {
            if ($this->db->getNumberOfRows($res) == 0) {
                return 'No remarks found';
            } else {
                /* go through records */
                while ($row = $this->db->getRow($res)) {

                    // exclude based on primkey
                    if (sizeof($primkeys) > 0 && !inArray($row["primkey"], $primkeys)) {
                        continue;
                    }

                    // exclude based on hidden variable
                    $displayed = $row["displayed"];
                    $arr = explode("~", $displayed);
                    foreach ($arr as $a) {
                        if (!inArray(strtoupper(getBasicName($a)), $vars)) {
                            continue;
                        }
                    }

                    /* no match on language, mode and version, then treat as never gotten */
                    if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($row["language"], $this->languages)))) {//
                        continue;
                    } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($row["mode"], $this->modes)))) {//
                        continue;
                    }

                    $remark = $row["remark_dec"];
                    $line = "";
                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                            
                        } else {
                            $prim = $row["primkey"];
                        }
                        $line .= $prim . $this->separator;
                    }
                    $line .= getValueForCsv($displayed) . $this->separator . getValueForCsv($remark) . $this->separator . $row["language"] . $this->separator . $row["mode"] . $this->separator . $row["version"] . $this->separator . getValueForCsv($row["ts"]);
                    fwrite($this->csvhandle, $line . "\n");
                    $line = null;
                    unset($line);
                    $row = null;
                    unset($row);
                }
            }
        }

        /* finish */
        $this->finishCSVFile();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* TIMING FILES */

    /* WRITE CSV FILE */

    function generateTimings() {

        /* set arrays */
        if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . "_timings.csv");
        $this->separator = ",";

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);
        // create table
        $create = "create table if not exists " . prepareDatabaseString($this->getProperty(DATA_OUTPUT_MAINDATATABLE)) . "_consolidated_times  (
                suid int(11) NOT NULL DEFAULT '1',
                primkey varchar(150) NOT NULL,
                begintime varchar(50) NOT NULL,
                stateid int(11) DEFAULT NULL, 
                variable varchar(50) NOT NULL,
                timespent int(11) NOT NULL DEFAULT '0',
                language int(11) NOT NULL DEFAULT '1',
                mode int(11) NOT NULL DEFAULT '1',
                version int(11) NOT NULL DEFAULT '1',
                ts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (suid,primkey,begintime,variable)
              ) ENGINE=MyIsam  DEFAULT CHARSET=utf8;";
        $this->db->executeQuery($create);

        $query = "delete from table " . prepareDatabaseString($this->getProperty(DATA_OUTPUT_MAINDATATABLE)) . "_consolidated_times where suid=" . prepareDatabaseString($this->suid);
        $this->db->executeQuery($query);
        $query = "REPLACE INTO " . prepareDatabaseString($this->getProperty(DATA_OUTPUT_MAINDATATABLE)) . "_consolidated_times SELECT min(suid) as suid, primkey, begintime, min(stateid) as stateid, min(variable) as variable, avg(timespent) as timespent, min(language) as language, min(mode) as mode, min(version) as version, min(ts) as ts FROM " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_times where suid=" . prepareDatabaseString($this->suid) . " group by primkey, begintime order by primkey asc";
        $this->db->executeQuery($query);

        // check for filter
        $filter = array();
        $extra = '';
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", prepareDatabaseString($this->getProperty(DATA_OUTPUT_VARLIST)));
            $extra = " AND (variable='" . implode("' OR variable='", $filter) . "')";
        }
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extra .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extra .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }

        $cutoff = Config::getTimingCutoff(); //DATA_TIMINGS_CUTOFF; // more than 5 minutes we ignore in calculating total interview time	
        $data = '';
        $select = "select primkey, variable, timespent, language, mode from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_consolidated_times where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) < " . prepareDatabaseString($this->maxprimkeylength) . $extra . " order by primkey asc, ts asc";
        $res = $this->db->selectQuery($select);
        if ($this->db->getNumberOfRows($res) > 0) {

            while ($row = $this->db->getRow($res)) {

                /* no match on language, mode and version, then treat as never gotten */
                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($row["language"], $this->languages)))) {//
                    continue;
                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($row["mode"], $this->modes)))) {//
                    continue;
                } else if (sizeof($filter) > 0 && !inArray(getBasicName($row["variable"]), $filter)) {
                    continue;
                } /* else {
                  $vd = $this->getVariableDescriptive(getBasicName($row["variable"]));
                  if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it
                  // hidden variable
                  if ($this->getProperty(DATA_OUTPUT_HIDDEN) == HIDDEN_YES && $vd->isHidden()) {
                  continue;
                  }
                  }
                  } */

                $line = '';
                if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                        $line .= getValueForCsv(encryptC($row["primkey"], $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION)));
                    } else {
                        $line .= getValueForCsv($row["primkey"]);
                    }
                }
                $line .= getValueForCsv($row["variable"]);
                $line .= getValueForCsv($row["timespent"]);
                $line .= getValueForCsv($row["language"]);
                $line .= getValueForCsv($row["mode"]);

                if (trim($line) != "") {
                    $data .= trim($line) . "\n";
                }
            }
        }

        $data3 = "";
        $extralanguagemode = "";
        if (sizeof($this->languages) > 0) {
            $extralanguagemode .= " AND (language=" . implode(" OR language=", $this->languages) . ")";
        }
        if (sizeof($this->modes) > 0) {
            $extralanguagemode .= " AND (mode=" . implode(" OR mode=", $this->modes) . ")";
        }
        $select = "select variable, count(*) as cnt, avg(timespent) as average from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_consolidated_times where suid=" . prepareDatabaseString($this->suid) . " and timespent < " . prepareDatabaseString($cutoff) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) < " . prepareDatabaseString($this->maxprimkeylength) . prepareDatabaseString($extralanguagemode) . prepareDatabaseString($extra) . " group by variable order by variable asc";
        $res = $this->db->selectQuery($select);
        if ($this->db->getNumberOfRows($res) > 0) {
            while ($row = $this->db->getRow($res)) {
                $line = getValueForCsv($row["variable"]);
                $line .= getValueForCsv($row["cnt"]);
                $line .= getValueForCsv($row["average"]);
                $line .= getValueForCsv($row["average"] / 60);
                if (trim($line) != "") {
                    $data3 .= trim($line) . "\n";
                }
            }
        }

        $data2 = "";
        $select = "select primkey, sum(timespent) as total, sum(timespent)/60 as total2, avg(timespent) as average, count(*) as cnt, min(language) as language, min(mode) as mode from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_consolidated_times where suid=" . prepareDatabaseString($this->suid) . "  and  timespent < " . prepareDatabaseString($cutoff) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) < " . prepareDatabaseString($this->maxprimkeylength) . prepareDatabaseString($extra) . " group by primkey order by primkey asc";
        $res = $this->db->selectQuery($select);
        if ($this->db->getNumberOfRows($res) > 0) {
            while ($row = $this->db->getRow($res)) {

                /* no match on language, mode and version, then treat as never gotten */
                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($row["language"], $this->languages)))) {//
                    continue;
                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($row["mode"], $this->modes)))) {//
                    continue;
                }

                $line = '';
                if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                        $line .= getValueForCsv(encryptC($row["primkey"], $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION)));
                    } else {
                        $line .= getValueForCsv($row["primkey"]);
                    }
                }
                $line .= getValueForCsv($row["total"]);
                $line .= getValueForCsv($row["total2"]);
                $line .= getValueForCsv($row["cnt"]);
                $line .= getValueForCsv($row["average"]);
                $line .= getValueForCsv($row["language"]);
                $line .= getValueForCsv($row["mode"]);

                if (trim($line) != "") {
                    $data2 .= trim($line) . "\n";
                }
            }
        }

        $this->csvhandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }
        $this->csvhandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV), "w");
        if (!$this->csvhandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV);

        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
            $header = 'primkey' . $this->separator . 'variable' . $this->separator . 'timespent' . $this->separator . 'language' . $this->separator . 'mode';
            $header2 = 'primkey' . $this->separator . 'total time spent (in seconds)' . $this->separator . 'total time spent (in minutes)' . $this->separator . 'number of screens' . $this->separator . 'average time spent per screen (in seconds)' . $this->separator . 'language' . $this->separator . 'mode';
        } else {
            $header = 'variable' . $this->separator . 'timespent' . $this->separator . 'language' . $this->separator . 'mode';
            $header2 = 'total time spent (in seconds)' . $this->separator . 'total time spent (in minutes)' . $this->separator . 'number of screens' . $this->separator . 'average time spent per screen (in seconds)' . $this->separator . 'language' . $this->separator . 'mode';
        }
        $header3 = 'variable, number of times on screen, average time spent (in seconds), average time spent (in minutes)';
        $data = str_replace("\r", "", $data);
        if ($data == "") {
            $data = "\n(0) Records Found!\n";
        } else {
            $data2 = str_replace("\r", "", $data2);
        }

        // write file
        fwrite($this->csvhandle, "$header2\n$data2\n\n\n$header3\n$data3\n\n\n$header\n$data");

        /* finish */
        $this->finishCSVFile();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* PARADATA FILES */

    function processErrorParaData($name = "") {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $query = "select max(pid) as pid from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_error_paradata where suid=" . prepareDatabaseString($this->survey->getSuid());
        $pid = 0;
        $res = $this->db->selectQuery($query);
        if ($res) {
            $row = $this->db->getRow($res);
            $pid = $row["pid"];
            if ($pid == "") {
                $pid = 0;
            }
        }

        $arr = array();
        $decrypt = "paradata as data_dec";
        $key = "";
        if ($this->survey->getDataEncryptionKey() != "") {
            $key = $this->survey->getDataEncryptionKey();
            $decrypt = "aes_decrypt(paradata, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as data_dec";
        }

        if ($name == "") {
            $query = "select *, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_paradata where pid > " . prepareDatabaseString($pid) . " and suid=" . prepareDatabaseString($this->survey->getSuid()) . ' order by primkey, pid asc';
        } else {
            $query = "select *, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_paradata where pid > " . prepareDatabaseString($pid) . " and suid=" . prepareDatabaseString($this->survey->getSuid()) . ' and (displayed = "' . prepareDatabaseString($name) . '" OR displayed like "%' . prepareDatabaseString($name) . '~%") order by primkey, pid asc';
        }
        $res = $this->db->selectQuery($query);
        $codes = array_values(Common::errorCodes());
        if ($res) {
            $oldprimkey = "";
            $arr = array();
            if ($this->db->getNumberOfRows($res) > 0) {
                $num = $this->db->getNumberOfRows($res);
                $cnt = 0;
                while ($row = $this->db->getRow($res)) {

                    $line = strtoupper($row["displayed"]);

                    if ($name == "" || $line == strtoupper($name) || contains($line, "~" . $name . "~") || startsWith($line, $name . "~")) {

                        $line = $row["data_dec"];
                        $line = str_replace("FO=", "FO:", $line);
                        $line = str_replace("FI=", "FI:", $line);
                        $a = explode("||", $line);
                        $displayed = explode("~", $row["displayed"]);
                        $variables = array();
                        foreach ($displayed as $d) {
                            if (startsWith($d, ROUTING_IDENTIFY_SUBGROUP) == false && startsWith($d, ROUTING_IDENTIFY_ENDSUBGROUP) == false) {
                                $variables[] = $d;
                            }
                        }

                        foreach ($a as $k) {
                            $t = explode(":", $k);
                            $code = $t[0];

                            // only if error code with a recorded answer
                            if (inArray($code, $codes) && sizeof($t) == 3) {
                                $s = explode("=", $t[2]);
                                $answer = $s[0];
                                if (trim($answer) == "") {
                                    continue;
                                }
                                $varname = $t[1];
                                $number = str_replace("answer", "", str_replace("_name[]", "", $varname));

                                // find varname
                                if (isset($variables[$number - 1])) {
                                    $variable = $variables[$number - 1];

                                    $query = "insert into " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_error_paradata (`pid`, `suid`, `primkey`, `code`, `rgid`, `variablename`, `answer`, `language`, `mode`, `version`, `ts`) values (";
                                    if ($key != "") {
                                        $query .= prepareDatabaseString($row["pid"]) . "," . prepareDatabaseString($row["suid"]) . ",'" . prepareDatabaseString($row["primkey"]) . "','" . prepareDatabaseString($code) . "'," . prepareDatabaseString($row["rgid"]) . ",'" . prepareDatabaseString(strtolower($variable)) . "',aes_encrypt('" . prepareDatabaseString($answer) . "','" . prepareDatabaseSprepareDatabaseString(tring($key) . "')," . $row["language"]) . "," . prepareDatabaseString($row["mode"]) . "," . prepareDatabaseString($row["version"]) . ",'" . prepareDatabaseString($row["ts"]) . "'";
                                    } else {
                                        $query .= prepareDatabaseString($row["pid"]) . "," . prepareDatabaseString($row["suid"]) . ",'" . prepareDatabaseString($row["primkey"]) . "','" . prepareDatabaseString($code) . "'," . prepareDatabaseString($row["rgid"]) . ",'" . prepareDatabaseString(strtolower($variable)) . "','" . prepareDatabaseString($answer) . "'," . prepareDatabaseString($row["language"]) . "," . prepareDatabaseString($row["mode"]) . "," .prepareDatabaseString( $row["version"]) . ",'" . prepareDatabaseString($row["ts"]) . "'";
                                    }
                                    $query .= ")";                                    
                                    $this->db->executeQuery($query);
                                }
                            }
                        }
                    }
                }
            }
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function processParaData($name = "") {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $query = "select max(pid) as pid from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_processed_paradata where suid=" . prepareDatabaseString($this->survey->getSuid());
        $pid = 0;
        $res = $this->db->selectQuery($query);
        if ($res) {
            $row = $this->db->getRow($res);
            $pid = $row["pid"];
            if ($pid == "") {
                $pid = 0;
            }
        }

        $arr = array();
        $decrypt = "paradata as data_dec";
        $key = "";
        if ($this->survey->getDataEncryptionKey() != "") {
            $key = $this->survey->getDataEncryptionKey();
            $decrypt = "aes_decrypt(paradata, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as data_dec";
        }

        if ($name == "") {
            $query = "select *, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_paradata where pid > " . prepareDatabaseString($pid) . " and suid=" . prepareDatabaseString($this->survey->getSuid()) . ' order by primkey, pid asc';
        } else {
            $query = "select *, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_paradata where pid > " . prepareDatabaseString($pid) . " and suid=" . prepareDatabaseString($this->survey->getSuid()) . ' and (displayed = "' . prepareDatabaseString($name) . '" OR displayed like "%' . prepareDatabaseString($name) . '~%") order by primkey, pid asc';
        }
        $res = $this->db->selectQuery($query);
        $codes = array_values(Common::errorCodes());
        if ($res) {
            $oldprimkey = "";
            $arr = array();
            if ($this->db->getNumberOfRows($res) > 0) {
                $num = $this->db->getNumberOfRows($res);
                $cnt = 0;
                while ($row = $this->db->getRow($res)) {

                    // end of primkey, so store
                    if ($oldprimkey != "" && $row["primkey"] != $oldprimkey) {

                        // k: varname
                        // a: array of error codes with number of times
                        foreach ($arr as $k => $a) {
                            foreach ($a as $error => $times) {
                                $query = "replace into " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_processed_paradata (`pid`, `suid`, `primkey`, `rgid`, `variablename`, `answer`, `language`, `mode`, `version`, `ts`) values (";
                                if ($key != "") {
                                    $query .= prepareDatabaseString($row["pid"]) . "," . prepareDatabaseString($row["suid"]) . ",'" . prepareDatabaseString($row["primkey"]) . "'," . prepareDatabaseString($row["rgid"]) . ",'" . prepareDatabaseString(strtolower($k . "_" . $error)) . "',aes_encrypt('" . prepareDatabaseString($times) . "','" . prepareDatabaseString($key) . "')," . prepareDatabaseString($row["language"]) . "," . prepareDatabaseString($row["mode"]) . "," . prepareDatabaseString($row["version"]) . ",'" . prepareDatabaseString($row["ts"]) . "'";
                                } else {
                                    $query .= prepareDatabaseString($row["pid"]) . "," . prepareDatabaseString($row["suid"]) . ",'" . prepareDatabaseString($row["primkey"]) . "'," . prepareDatabaseString($row["rgid"]) . ",'" . prepareDatabaseString(strtolower($k . "_" . $error)) . "','" . prepareDatabaseString($times) . "'," . prepareDatabaseString($row["language"]) . "," . prepareDatabaseString($row["mode"]) . "," . prepareDatabaseString($row["version"]) . ",'" . prepareDatabaseString($row["ts"]) . "'";
                                }
                                $query .= ")";
                                $this->db->executeQuery($query);
                            }
                        }

                        // reset
                        $arr = array();
                    }
                    $oldprimkey = $row["primkey"];

                    $line = strtoupper($row["displayed"]);

                    // if displayed == variable OR displayed contains ~varname~ or displayed starts with varname~, process; otherwise skip
                    if ($name == "" || $line == strtoupper($name) || contains($line, "~" . $name . "~") || startsWith($line, $name . "~")) {

                        $line = $row["data_dec"];
                        $line = str_replace("FO=", "FO:", $line);
                        $line = str_replace("FI=", "FI:", $line);
                        $a = explode("||", $line);
                        $displayed = explode("~", $row["displayed"]);
                        $variables = array();
                        foreach ($displayed as $d) {
                            if (startsWith($d, ROUTING_IDENTIFY_SUBGROUP) == false && startsWith($d, ROUTING_IDENTIFY_ENDSUBGROUP) == false) {
                                $variables[] = $d;
                            }
                        }

                        foreach ($a as $k) {
                            $t = explode(":", $k);
                            $code = $t[0];

                            // error code
                            if (inArray($code, $codes)) {
                                if (sizeof($t) == 2) { // no wrong answer
                                    $s = explode("=", $t[1]);
                                } else { // wrong answer
                                    $s = explode("=", $t[2]);
                                }
                                $varname = $s[0];
                                $number = str_replace("answer", "", str_replace("_name[]", "", $varname));

                                // find varname
                                if (isset($variables[$number - 1])) {
                                    $variable = $variables[$number - 1];
                                    if (isset($arr[strtoupper($variable)])) {
                                        $vararray = $arr[strtoupper($variable)];
                                    } else {
                                        $vararray = array();
                                    }
                                    if (isset($vararray[strtoupper($code)])) {
                                        $vararray[strtoupper($code)] = $vararray[strtoupper($code)] + 1;
                                    } else {
                                        $vararray[strtoupper($code)] = 1;
                                    }
                                    $arr[strtoupper($variable)] = $vararray;
                                }
                            } else if (inArray($code, array("FO", "FI"))) {
                                foreach ($variables as $variable) {
                                    if (isset($arr[strtoupper($variable)])) {
                                        $vararray = $arr[strtoupper($variable)];
                                    } else {
                                        $vararray = array();
                                    }
                                    if (isset($vararray[strtoupper($code)])) {
                                        $vararray[strtoupper($code)] = $vararray[strtoupper($code)] + 1;
                                    } else {
                                        $vararray[strtoupper($code)] = 1;
                                    }
                                    $arr[strtoupper($variable)] = $vararray;
                                }
                            }
                        }
                    }

                    $cnt++;

                    // this was last one, so store
                    if ($cnt == $num) {

                        // k: varname
                        // a: array of error codes with number of times
                        foreach ($arr as $k => $a) {
                            foreach ($a as $error => $times) {
                                $query = "replace into " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_processed_paradata (`pid`, `suid`, `primkey`, `rgid`, `variablename`, `answer`, `language`, `mode`, `version`, `ts`) values (";
                                if ($key != "") {
                                    $query .= prepareDatabaseString($row["pid"]) . "," . prepareDatabaseString($row["suid"]) . ",'" . prepareDatabaseString($row["primkey"]) . "'," . prepareDatabaseString($row["rgid"]) . ",'" . prepareDatabaseString(strtolower($k . "_" . $error)) . "',aes_encrypt('" . prepareDatabaseString($times) . "','" . prepareDatabaseString($key) . "')," . prepareDatabaseString($row["language"]) . "," . prepareDatabaseString($row["mode"]) . "," . prepareDatabaseString($row["version"]) . ",'" . prepareDatabaseString($row["ts"]) . "'";
                                } else {
                                    $query .= prepareDatabaseString($row["pid"]) . "," . prepareDatabaseString($row["suid"]) . ",'" . prepareDatabaseString($row["primkey"]) . "'," . prepareDatabaseString($row["rgid"]) . ",'" . prepareDatabaseString(strtolower($k . "_" . $error)) . "','" . prepareDatabaseString($times) . "'," . prepareDatabaseString($row["language"]) . "," . prepareDatabaseString($row["mode"]) . "," . prepareDatabaseString($row["version"]) . ",'" . prepareDatabaseString($row["ts"]) . "'";
                                }
                                $query .= ")";
                                $this->db->executeQuery($query);
                            }
                        }

                        // reset
                        $arr = array();
                    }
                }
            }
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* WRITE PROCESSED STATA FILE */

    function generateProcessedParadata() {

        $this->processParadata();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);
        ini_set('memory_limit', Config::dataExportMemoryLimit());

        /* set arrays */
        if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_STATA, $this->getProperty(DATA_OUTPUT_FILENAME) . ".dta");
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . ".csv");

        $extracompleted = "";
        if ($this->getProperty(DATA_OUTPUT_COMPLETED) == INTERVIEW_COMPLETED) {
            $extracompleted = " and completed=" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_COMPLETED));
        }

        // find any data names in the data   
        $extra = "";
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extra .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extra .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }
        $datanames = array();
        $this->maxwidths = array();

        $decrypt = ", MAX( LENGTH(answer)) as max";
        if ($this->survey->getDataEncryptionKey() != "") {
            $decrypt = ", MAX( LENGTH( cast(aes_decrypt(answer, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as char))) AS max";
        }

        $query = "select variablename" . $decrypt . " from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_processed_paradata where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extra . " group by variablename";
        $res = $this->db->selectQuery($query);
        if ($res) {
            if ($this->db->getNumberOfRows($res) == 0) {
                return 'No records found';
            } else {
                /* go through records */
                while ($row = $this->db->getRow($res)) {
                    $realname = trim(substr($row["variablename"], 0, strrpos($row["variablename"], "_")));
                    $vd = $this->getVariableDescriptive(getBasicName($realname));
                    if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it           
                        // hidden variable
                        if ($this->getProperty(DATA_OUTPUT_HIDDEN) == HIDDEN_YES && $vd->isHidden()) {
                            continue;
                        }
                    }
                    $datanames[] = $row["variablename"];
                    $this->maxwidths[strtoupper($row["variablename"])] = $row["max"];
                    $row = null;
                    unset($row);
                }
            }
            $res = null;
            unset($res);
        }

        // check for filter
        $filter = array();
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", $this->getProperty(DATA_OUTPUT_VARLIST));
        }

        // sort data names by name
        sort($datanames);


        /* check for primkey variable presence */
        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
            array_unshift($datanames, VARIABLE_PRIMKEY);
        }

        $this->arrayfields = array();
        $this->skipvariables = array();
        $this->setofenumeratedbinary = array();
        $this->descriptives = array();
        $this->withsuffix = array();
        
        /* retrieve variable information */
        foreach ($datanames as $d) {
            $this->processParadataVariable($d);
        }

        /* set number of variables */
        $this->variablenumber = sizeof($this->variablenames);

        /* get number of records */
        $query = "select distinct primkey from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_processed_paradata where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extra;
        $res = $this->db->selectQuery($query);
        $this->recordcount = $this->db->getNumberOfRows($res);

        /* start writing files */
        $outputtype = strtolower($this->getProperty(DATA_OUTPUT_FILETYPE));
        $this->asked = sprintf('%.0F', "8.988465625461158E307");
        if ($outputtype == FILETYPE_CSV) {
            $this->startCSVFile();
            $this->separator = ",";
        } else {
            $this->startStataFile();
            
            $this->littleendian = $this->isLittleEndian();

            // http://www.stata.com/help.cgi?dta_113
            $this->shortempty = 32741;
            $this->shorterror = 32763;
            $this->shortdk = 32745;
            $this->shortna = 32755;
            $this->shortrf = 32759;
            $this->shortmarkempty = 32746;
            $this->doubleempty = sprintf('%.0F', "8.98846567431158E307"); // 2^1013            
            $this->doubledk = sprintf('%.0F', "8.99724347282165E307");
            $this->doublerf = sprintf('%.0F', "9.027965767606894E307");
            $this->doublemarkempty = sprintf('%.0F', "8.98846567431158E307") * 1.001220703125000;
            $this->doublena = sprintf('%.0F', "8.98846567431158E307") * 1.003417968750000000;
            $this->doubleerror = sprintf('%.0F', "8.98846567431158E307") * 1.005371093750000000;

            // floats not used right now
            $this->floatempty = sprintf('%.0F', "1.7014118E38F");
            $this->floatdk = sprintf('%.0F', "1.7030734E38F");
            $this->floatna = sprintf('%.0F', "1.7030734E38F"); // TODO
            $this->floatrf = sprintf('%.0F', "1.7088887E38F");
            $this->floatmarkempty = sprintf('%.0F', "1.7030734E38F");
        }

        // get languages, modes, versions
        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);
        $this->encoding = $this->getProperty(DATA_OUTPUT_ENCODING);

        /* go through all records */
        if ($res) {
            if ($this->db->getNumberOfRows($res) == 0) {
                return 'No records found';
            } else {

                $decrypt = "answer as data_dec";
                if ($this->survey->getDataEncryptionKey() != "") {
                    $decrypt = "aes_decrypt(answer, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as data_dec";
                }

                /* go through records */
                while ($row = $this->db->getRow($res)) {

                    $query = "select primkey, variablename, $decrypt, language, mode, version from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_processed_paradata where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($row["primkey"]) . "'";
                    $this->currentrecord = array();
                    $res2 = $this->db->selectQuery($query);
                    if ($res2) {
                        if ($this->db->getNumberOfRows($res2)) {
                            $tempcnt = 1;
                            while ($row2 = $this->db->getRow($res2)) {
                                $this->currentrecord[strtoupper($row2["variablename"])] = array("name" => $row2["variablename"], "answer" => $row2["data_dec"], "language" => $row2["language"], "mode" => $row2["mode"], "version" => $row2["version"]);
                                if ($tempcnt == 1) {
                                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                                        $this->currentrecord[strtoupper(VARIABLE_PRIMKEY)] = array("name" => VARIABLE_PRIMKEY, "answer" => $row["primkey"], "language" => $row2["language"], "mode" => $row2["mode"], "version" => $row2["version"]);
                                        $tempcnt++;
                                    }
                                }
                                $row2 = null;
                                unset($row2);
                            }

                            if (sizeof($this->currentrecord) > 0) {
                                if ($outputtype == FILETYPE_CSV) {
                                    $this->addCSVRecord($row["primkey"]);
                                } else {
                                    $this->addStataRecord($row["primkey"]);
                                    $cnt++;
                                }
                            }
                            $this->currentrecord = null;
                            unset($this->currentrecord);
                        }
                    }
                    $query = null;
                    unset($query);
                    $res2 = null;
                    unset($res2);
                    $row = null;
                    unset($row);
                }
            }
        }

        /* finish */
        if ($outputtype == FILETYPE_CSV) {
            $this->finishCSVFile();
        } else {
            $this->addValueLabels();
            $this->finishStataFile();
        }

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function processParadataVariable($variablename) {

        // remove any spacing
        $variablename = trim($variablename);

        // already processed
        if (inArray(strtoupper($variablename), $this->lookup)) {
            return;
        }

        // no primary key in data
        if (strtoupper($variablename) == strtoupper(VARIABLE_PRIMKEY) && $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) == PRIMARYKEY_NO) {
            return;
        }

        // get answer type
        if (strtoupper($variablename) == strtoupper(VARIABLE_PRIMKEY)) {
            $answertype = ANSWER_TYPE_STRING;
            $label = "PRIMARY KEY";
        } else {
            $ops = Language::errorCodeLabels();
            $answertype = ANSWER_TYPE_INTEGER;
            $pos = strrpos($variablename, "_");
            $code = substr($variablename, $pos + 1);
            $label = strtoupper($variablename . ' - ' . $ops[strtoupper($code)]);
        }

        $suid = $this->suid;

        /* check for lowercase */
        if ($this->getProperty(DATA_OUTPUT_FIELDNAME_CASE) == FIELDNAME_LOWERCASE) {
            $variablename = strtolower($variablename);
        } else {
            $variablename = strtoupper($variablename);
        }

        /* process */
        $width = 1;
        switch ($answertype) {

            /* string (prim_key) */
            case ANSWER_TYPE_STRING:
                $datatype = STATA_TYPE_STRING;
                $width = 20;
                break;

            /* integer */
            case ANSWER_TYPE_INTEGER:
                $datatype = STATA_TYPE_SHORT;
                $width = strlen(ANSWER_RANGE_MAXIMUM);
                break;
        }

        $valueLabel = "";
        $this->variablenames[] = $variablename;
        $this->lookup[] = strtoupper($variablename);
        $this->valuelabels[] = "";
        $this->labels[] = $label;
        $this->datatypes[] = $datatype;
        $labelset = false;

        if (isset($this->maxwidths[strtoupper($variablename)]) && $labelset == false) {
            $max = $this->maxwidths[strtoupper($variablename)];
            if (is_numeric($max)) {
                if ($max < $width) {
                    if ($datatype != STATA_TYPE_DOUBLE) {
                        $width = $max;
                    } else {
                        $width = $max * 2; // these we need to add some extra, so Stata editor displays them fine
                    }
                }
            }
        }

        if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_DOUBLE)) && $width < 4) {
            $width = 4;
        }

        $this->answerwidth[] = $width;
    }

    /* WRITE RAW CSV FILE */

    function generateParadata() {

        /* set arrays */
        if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);
        
        $this->arrayfields = array();
        $this->skipvariables = array();
        $this->setofenumeratedbinary = array();
        $this->descriptives = array();
        $this->withsuffix = array();

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . "_paradata.csv");
        $this->separator = ",";

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);

        // check for filter
        $filter = array();
        $extra = '';
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", prepareDatabaseString($this->getProperty(DATA_OUTPUT_VARLIST)));
            $extra = " AND (variable='" . implode("' OR variable='", $filter) . "')";
        }
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extra .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extra .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }

        $decrypt = "paradata as paradata_dec";
        if ($this->survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(paradata, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as paradata_dec";
        }
        $data = '';
        $select = "select primkey, displayed, language, mode, $decrypt, ts from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_paradata where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) < " . prepareDatabaseString($this->maxprimkeylength) . $extra . " order by primkey asc, pid asc";
        $res = $this->db->selectQuery($select);
        if ($this->db->getNumberOfRows($res) > 0) {

            while ($row = $this->db->getRow($res)) {

                /* no match on language, mode and version, then treat as never gotten */
                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($row["language"], $this->languages)))) {//
                    continue;
                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($row["mode"], $this->modes)))) {//
                    continue;
                } else if (sizeof($filter) > 0) {
                    $include = true;
                    $displayed = explode("~", $row["displayed"]);
                    foreach ($displayed as $d) {
                        if (!inArray(getBasicName($d), $filter)) {
                            $include = false;
                        }
                    }
                    if ($include == false) {
                        continue;
                    }
                } else {
                    $include = true;
                    $displayed = explode("~", $row["displayed"]);
                    foreach ($displayed as $d) {
                        $vd = $this->getVariableDescriptive(getBasicName($d));
                        if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it           
                            // hidden variable
                            if ($this->getProperty(DATA_OUTPUT_HIDDEN) == HIDDEN_YES && $vd->isHidden()) {
                                $include = false;
                            }
                        }
                    }
                    if ($include == false) {
                        continue;
                    }
                }

                $line = '';
                if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                        $line .= getValueForCsv(encryptC($row["primkey"], $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION)));
                    } else {
                        $line .= getValueForCsv($row["primkey"]);
                    }
                }
                $line .= getValueForCsv($row["displayed"]);
                $line .= getValueForCsv($row["paradata_dec"]);
                $line .= getValueForCsv($row["language"]);
                $line .= getValueForCsv($row["mode"]);
                $line .= getValueForCsv($row["ts"]);

                if (trim($line) != "") {
                    $data .= trim($line) . "\n";
                }
            }
        }

        $this->csvhandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }
        $this->csvhandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV), "w");
        if (!$this->csvhandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV);

        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
            $header = 'primkey' . $this->separator . 'variable(s)' . $this->separator . 'paradata' . $this->separator . 'language' . $this->separator . 'mode' . $this->separator . 'date/time';
        } else {
            $header = 'variable(s)' . $this->separator . 'paradata' . $this->separator . 'language' . $this->separator . 'mode' . $this->separator . 'date/time';
        }
        $data = str_replace("\r", "", $data);
        if ($data == "") {
            $data = "\n(0) Records Found!\n";
        }

        // write file
        fwrite($this->csvhandle, "$header\n$data");

        /* finish */
        $this->finishCSVFile();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* ERROR ANSWERS */

    function generateErrorParadata() {

        $this->processErrorParadata();

        /* set arrays */
        if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }
        if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);
        
        $this->arrayfields = array();
        $this->skipvariables = array();
        $this->setofenumeratedbinary = array();
        $this->descriptives = array();
        $this->withsuffix = array();

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . "_error_paradata.csv");
        $this->separator = ",";

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);

        // check for filter
        $filter = array();
        $extra = '';
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", prepareDatabaseString($this->getProperty(DATA_OUTPUT_VARLIST)));
            $extra = " AND (variable='" . implode("' OR variable='", $filter) . "')";
        }
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extra .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extra .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }

        $decrypt = "answer as answer_dec";
        if ($this->survey->getDataEncryptionKey() != "") {
            $decrypt = "aes_decrypt(answer, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as answer_dec";
        }
        $data = '';
        $select = "select primkey, code, variablename, language, mode, $decrypt, ts from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_error_paradata where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) < " . prepareDatabaseString($this->maxprimkeylength) . $extra . " order by primkey asc, variablename asc";
        $res = $this->db->selectQuery($select);
        if ($this->db->getNumberOfRows($res) > 0) {

            $labels = Language::errorCodeLabels();
            while ($row = $this->db->getRow($res)) {
                
                /* no match on language, mode and version, then treat as never gotten */
                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($row["language"], $this->languages)))) {//
                    continue;
                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($row["mode"], $this->modes)))) {//
                    continue;
                } else if (sizeof($filter) > 0) {
                    $include = true;
                    $variablename = $row["variablename"];
                    if (!inArray(getBasicName($variablename), $filter)) {
                        continue;
                    }
                } else {
                    $include = true;
                    $variablename = $row["variablename"];
                    $vd = $this->getVariableDescriptive(getBasicName($variablename));
                    if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it           
                        // hidden variable
                        if ($this->getProperty(DATA_OUTPUT_HIDDEN) == HIDDEN_YES && $vd->isHidden()) {
                            $include = false;
                        }
                    }

                    if ($include == false) {
                        continue;
                    }
                }

                $line = '';
                if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
                    if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                        $line .= getValueForCsv(encryptC($row["primkey"], $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION)));
                    } else {
                        $line .= getValueForCsv($row["primkey"]);
                    }
                }
                $cd = "";
                $code = $row["code"];
                if (isset($labels[$code])) {
                    $cd = $labels[$code];
                }
                $line .= getValueForCsv($cd);
                $line .= getValueForCsv($row["variablename"]);
                $line .= getValueForCsv($row["answer_dec"]);
                $line .= getValueForCsv($row["language"]);
                $line .= getValueForCsv($row["mode"]);
                $line .= getValueForCsv($row["ts"]);

                if (trim($line) != "") {
                    $data .= trim($line) . "\n";
                }
            }
        }

        $this->csvhandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }
        $this->csvhandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV), "w");
        if (!$this->csvhandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV);

        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
            $header = 'primkey' . $this->separator . 'code' . $this->separator . 'variable' . $this->separator . 'answer' . $this->separator . 'language' . $this->separator . 'mode' . $this->separator . 'date/time';
        } else {
            $header = 'code' . $this->separator . 'variable(s)' . $this->separator . 'answer' . $this->separator . 'language' . $this->separator . 'mode' . $this->separator . 'date/time';
        }
        $data = str_replace("\r", "", $data);
        if ($data == "") {
            $data = "\n(0) Records Found!\n";
        }

        // write file
        fwrite($this->csvhandle, "$header\n$data");

        /* finish */
        $this->finishCSVFile();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* RAW DATA FILES */

    function processVariable($variablename, $var) {

        // remove any spacing
        $variablename = trim($variablename);

        // already processed
        if (inArray(strtoupper($variablename), $this->lookup)) {
            return;
        }

        // no primary key in data
        if (strtoupper($variablename) == strtoupper(VARIABLE_PRIMKEY) && $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) == PRIMARYKEY_NO) {
            return;
        }

        // only kept data
        if ($this->getProperty(DATA_OUTPUT_KEEP_ONLY) == DATA_KEEP_YES) {

            // variable not kept, then exclude
            if ($var->getDataKeep() != DATA_KEEP_YES) {
                return;
            }
        }

        // get answer type
        $answertype = $var->getAnswerType();
        if ($answertype == SETTING_FOLLOW_TYPE) {
            $type = $this->getType($var->getTyd());
            $answertype = $type->getAnswerType();
        }

        $label = $var->getDescription();
        $suid = $var->getSuid();
        $valueLabel = "";

        /* check for lowercase */
        if ($this->getProperty(DATA_OUTPUT_FIELDNAME_CASE) == FIELDNAME_LOWERCASE) {
            $variablename = strtolower($variablename);
        } else {
            $variablename = strtoupper($variablename);
        }

        /* process */
        $width = 1;
        $soem = 0;
        $labelset = false;

        /* array */
        if (inArray(strtoupper($variablename), $this->arrayfields) && contains($variablename, "[") == false) {
            return; // don't add generic array method
            $datatype = STATA_TYPE_STRING;
            $max = $var->getMaximumLength();
            if (!is_numeric($max)) {
                $max = 50;
            } else if ($max > 244) {
                $max = 243;
            }
            $width = $max;
        } else {
            switch ($answertype) {

                /* type 'section' */
                case ANSWER_TYPE_SECTION:
                    return;

                /* no input, treat as string */
                case ANSWER_TYPE_NONE:
                    $datatype = STATA_TYPE_STRING;
                    $width = 1;
                    break;

                /* datetype, treat as string */
                case ANSWER_TYPE_DATE:
                    $datatype = STATA_TYPE_STRING;
                    $width = 10; // '2012-10-12'
                    break;
                /* time type, treat as string */
                case ANSWER_TYPE_TIME:
                    $datatype = STATA_TYPE_STRING;
                    $width = 8; // '12:55:34'
                    break;
                /* datatime type, treat as string */
                case ANSWER_TYPE_DATETIME:
                    $datatype = STATA_TYPE_STRING;
                    $width = 19; // '2012-10-12 12:55:34'
                    break;

                /* string, falls to open for max width check */
                case ANSWER_TYPE_STRING:
                    $max = $var->getMaximumLength();
                    if (!is_numeric($max)) {
                        $max = 20;
                    } else if ($max > 244) {
                        $max = 243;
                    }
                    $datatype = STATA_TYPE_STRING;
                    $width = $max;
                    break;
                /* open */
                case ANSWER_TYPE_OPEN:
                    $max = $var->getMaximumLength();
                    if (!is_numeric($max) || $max > 244) {
                        $max = 243;
                    }
                    $datatype = STATA_TYPE_STRING;
                    $width = $max;
                    break;

                /* set of enumerated */
                case ANSWER_TYPE_SETOFENUMERATED:
                /* fall through */

                /* rank */

                /* multi select dropdown */
                case ANSWER_TYPE_MULTIDROPDOWN:
                    $this->setofenumeratedvariables[$variablename] = 1;
                    if ($var->getOutputOptionsText() != "") {
                        $valueLabel = $var->getOutputOptionsText();
                        $options = $var->getOutputOptions();
                    } else {
                        $valueLabel = $var->getOptionsText();
                        $options = $var->getOptions();
                    }
                    $optioncodes = array();
                    $optionlabels = array();
                    $soem = 0;
                    if (is_array($options)) {
                        foreach ($options as $option) {
                            $code = $option["code"];
                            $optioncodes[] = $code;
                            $optionlabels[] = $this->prepareLabel(trim(strip_tags($option["label"])));
                            if ($code > $soem) {
                                $soem = $code;
                            }
                        }
                    }
                    $width = strlen($soem);
                    /* data type is set below */
                    break;

                /* enumerated */
                case ANSWER_TYPE_ENUMERATED:
                /* fall through */

                /* single select dropdown */
                case ANSWER_TYPE_DROPDOWN:
                    $datatype = STATA_TYPE_SHORT;
                    if ($var->getOutputOptionsText() != "") {
                        $valueLabel = $var->getOutputOptionsText();
                        $options = $var->getOutputOptions();
                    } else {
                        $valueLabel = $var->getOptionsText();
                        $options = $var->getOptions();
                    }
                    
                    $maxcode = "";
                    $valwidth = $this->getProperty(DATA_OUTPUT_VALUELABEL_WIDTH);
                    if ($var->getOutputValueLabelWidth() != "") {
                        $valwidth = $var->getOutputValueLabelWidth();
                    }
                    if ($valwidth == VALUELABEL_WIDTH_SHORT) {
                        $width = strlen($var->getMaximumOptionCode());
                    } else {
                        $so = 0;
                        if (is_array($options)) {
                            foreach ($options as $option) {
                                $code = $option["code"];
                                if ($maxcode == "" || $code > $maxcode) {
                                    $maxcode = $code;
                                }
                                $lab = $this->prepareLabel(trim(strip_tags($option["label"])));
                                $sn = strlen($code) + strlen($lab) + 1; //(one space)
                                if ($sn > $so) {
                                    $so = $sn;
                                }
                            }
                            $labelset = true;
                        }
                        $width = $so; //$soem;
                    }
                    
                    // check if maximum code is bigger than max short --> 32767
                    if ($maxcode == "") { // not found max code yet
                        foreach ($options as $option) {
                            $code = $option["code"];
                            if ($maxcode == "" || $code > $maxcode) {
                                $maxcode = $code;
                            }                            
                        }
                    }
                    
                    // code too high, use double instead
                    if ($maxcode != "" && $maxcode > 32767) {
                        $datatype = STATA_TYPE_DOUBLE;
                    }

                    break;

                /* range */
                case ANSWER_TYPE_RANGE:
                /* fall through */

                /* knob */
                case ANSWER_TYPE_KNOB:
                /* fall through */

                /* slider */
                case ANSWER_TYPE_SLIDER:
                    $min = $var->getMinimum();
                    $max = $var->getMaximum();

                    /* maximum is fill reference */
                    if (!is_numeric($max)) {
                        $max = ANSWER_RANGE_MAXIMUM;
                    }
                    /* minimum is fill reference */
                    if (!is_numeric($min)) {
                        $min = ANSWER_RANGE_MINIMUM;
                    }
                    
                    /* only for range */
                    if (contains($min, ".") || contains($max, ".")) {
                        /* treat as double */
                        $datatype = STATA_TYPE_DOUBLE;
                    } else {
                        
                        // out of range for short
                        if ($min < -32768 || $max > 32768) {
                            $datatype = STATA_TYPE_DOUBLE;
                        }
                        /* in range for short */
                        else {
                            $datatype = STATA_TYPE_SHORT;
                        }
                    }
                    
                    $width = strlen($max);
                    if (strlen($min) > $width) {
                        $width = strlen($min);
                    }
                /* double */
                case ANSWER_TYPE_DOUBLE:
                    $datatype = STATA_TYPE_DOUBLE;
                    $width = strlen(ANSWER_RANGE_MAXIMUM);
                    break;

                /* integer */
                case ANSWER_TYPE_INTEGER:
                    $datatype = STATA_TYPE_SHORT; // DOUBLE SOMETIMES FOR NEWER PLATFORMS
                    $width = strlen(ANSWER_RANGE_MAXIMUM);
                    break;
                case ANSWER_TYPE_RANK:
                /* fall through */
                /* custom and not set */
                default:
                    $datatype = STATA_TYPE_STRING;
                    $max = $var->getMaximumLength();
                    if (!is_numeric($max) || $max == "" || $max == 0) {
                        $max = 20;
                    }
                    else if ($max > 244) {
                        $max = 243;
                    }
                    $width = $max;
                    break;
            }
        }


        /* check width for Stata */
        if ($width < 2 && $this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) { // && inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_CALENDAR))) {
            $width = 2;
        }

        /* set of enumerated/multi-select dropdown */
        if ($soem > 0) {

            $codes = $optioncodes; //explode(SEPARATOR_SETOFENUMERATED, $optioncodes);
            $vls = $optionlabels; //explode(SEPARATOR_SETOFENUMERATED, $optionlabels);

            if (!isset($this->variablestosplit[strtoupper($variablename)])) {

                $valwidth = $this->getProperty(DATA_OUTPUT_VALUELABEL_WIDTH);
                if ($var->getOutputValueLabelWidth() != "") {
                    $valwidth = $var->getOutputValueLabelWidth();
                }

                /* remember options for set of enum questions, so we can add empty indicators later on when preparing data */
                $this->variablestosplit[strtoupper($variablename)] = $optioncodes;
                $str = 0;
                for ($i = 0; $i < sizeof($codes); $i++) {
                    $code = $codes[$i];
                    $vl = $this->prepareLabel($vls[$i]);
                    $this->withsuffix[strtoupper($variablename . "s" . $code)] = "s" . $code;
                    $this->realsuffix[strtoupper($variablename . "s" . $code)] = "_" . $code . "_";
                    $this->variablenames[] = $variablename . "s" . $code;
                    $this->lookup[] = strtoupper($variablename . "s" . $code);

                    if ($var->getOutputSetOfEnumeratedBinary() == SETOFENUMERATED_DEFAULT) {
                        $this->labels[] = $label;
                        $this->valuelabels[] = trim(strip_tags($valueLabel));
                        if ($valwidth == VALUELABEL_WIDTH_SHORT) {
                            $width = strlen($code);
                        } else {
                            $width = strlen($code) + strlen($vl) + 1; //(one space)
                        }
                    } else {
                        $this->labels[] = $vl; // use value label of option as the variable description
                        $this->setofenumeratedbinary[strtoupper($variablename . "s" . $code)] = $var->getOutputSetOfEnumeratedBinary();
                        $this->valuelabels[] = "0 No\r\n1 Yes\r\n";
                        if ($valwidth == VALUELABEL_WIDTH_SHORT) {
                            $width = 1;
                        } else {
                            $width = 5;
                        }
                    }

                    $this->datatypes[] = STATA_TYPE_SHORT;
                    if ($width < 2 && $this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) { // && inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_CALENDAR))) {
                        $width = 2;
                    }

                    $this->answerwidth[] = $width;

                    if ($str != 0) {
                        $str = $str + 1;
                    }
                    $str = $str + strlen($code);

                    /* add skip variable */
                    if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_SKIP_VARIABLE) {
                        if ($var->isDataSkipVariable() == true) {
                            $this->skipvariables[strtoupper($variablename . "s" . $code)] = $variablename . "s" . $code . $var->getDataSkipVariablePostFix();
                            $this->variablenames[] = $variablename . "s" . $code . $var->getDataSkipVariablePostFix();
                            $this->lookup[] = strtoupper($variablename . "s" . $code . $var->getDataSkipVariablePostFix());
                            $this->valuelabels[] = "1 Skip";
                            $this->labels[] = "Skip variable for option " . $code . " of " . $variablename;
                            $this->datatypes[] = STATA_TYPE_SHORT;
                            $this->answerwidth[] = 1;
                        }
                    }
                }

                $this->variablenames[] = $variablename;
                $this->lookup[] = strtoupper($variablename);
                $this->valuelabels[] = trim(strip_tags($valueLabel));
                $this->labels[] = $label;
                $this->datatypes[] = STATA_TYPE_STRING;

                if (isset($this->maxwidths[strtoupper($variablename)])) {
                    $max = $this->maxwidths[strtoupper($variablename)];
                    if (is_numeric($max)) {
                        if ($max < $str) {
                            $str = $max;
                        }
                    }
                }

                $this->answerwidth[] = $str;
            }
        }
        // all other answer types
        else {
            $this->variablenames[] = $variablename;
            $this->lookup[] = strtoupper($variablename);
            $this->valuelabels[] = trim(strip_tags($valueLabel));
            $this->labels[] = $label;
            $this->datatypes[] = $datatype;

            if (isset($this->maxwidths[strtoupper($variablename)]) && $labelset == false) {
                $max = $this->maxwidths[strtoupper($variablename)];
                if (is_numeric($max)) {
                    if ($max < $width) {
                        if ($datatype != STATA_TYPE_DOUBLE) {
                            $width = $max;
                        } else {
                            $width = $max * 2; // these we need to add some extra, so Stata editor displays them fine
                        }
                    }
                }
            }

            if ($width < 2 && $this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) { // && inArray($answertype, array(ANSWER_TYPE_NONE, ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_CALENDAR))) {
                $width = 2;
            }
            if (inArray($answertype, array(ANSWER_TYPE_INTEGER, ANSWER_TYPE_DOUBLE)) && $width < 4) {
                $width = 4;
            }

            $this->answerwidth[] = $width;

            /* add skip variable */
            if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_SKIP_VARIABLE) {
                if ($var->isDataSkipVariable() == DATA_SKIP_YES) {
                    $this->skipvariables[strtoupper($variablename)] = $variablename . $var->getDataSkipVariablePostFix();
                    $this->variablenames[] = $variablename . $var->getDataSkipVariablePostFix();
                    $this->lookup[] = strtoupper($variablename . $var->getDataSkipVariablePostFix());
                    $this->valuelabels[] = "1 Skip";
                    $this->labels[] = "Skip variable for " . $variablename;
                    $this->datatypes[] = STATA_TYPE_SHORT;
                    $this->answerwidth[] = 1;
                }
            }
        }
    }

    function generate() {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        set_time_limit(0);
        ini_set('memory_limit', Config::dataExportMemoryLimit());

        /* set arrays */
        if (is_array($this->getProperty(DATA_OUTPUT_MODES))) {
            $this->setProperty(DATA_OUTPUT_MODES, $this->getProperty(DATA_OUTPUT_MODES));
        } else if (trim($this->getProperty(DATA_OUTPUT_MODES)) != "") {
            $this->setProperty(DATA_OUTPUT_MODES, explode("~", $this->getProperty(DATA_OUTPUT_MODES)));
        } else {
            $this->setProperty(DATA_OUTPUT_MODES, array());
        }

        if (is_array($this->getProperty(DATA_OUTPUT_LANGUAGES))) {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, $this->getProperty(DATA_OUTPUT_LANGUAGES));
        } else if (trim($this->getProperty(DATA_OUTPUT_LANGUAGES)) != "") {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, explode("~", $this->getProperty(DATA_OUTPUT_LANGUAGES)));
        } else {
            $this->setProperty(DATA_OUTPUT_LANGUAGES, array());
        }

        if (is_array($this->getProperty(DATA_OUTPUT_VERSIONS))) {
            $this->setProperty(DATA_OUTPUT_VERSIONS, $this->getProperty(DATA_OUTPUT_VERSIONS));
        } else if (trim($this->getProperty(DATA_OUTPUT_VERSIONS)) != "") {
            $this->setProperty(DATA_OUTPUT_VERSIONS, explode("~", $this->getProperty(DATA_OUTPUT_VERSIONS)));
        } else {
            $this->setProperty(DATA_OUTPUT_VERSIONS, array());
        }

        /* set file names */
        $this->setProperty(DATA_OUTPUT_FILENAME_STATA, $this->getProperty(DATA_OUTPUT_FILENAME) . ".dta");
        $this->setProperty(DATA_OUTPUT_FILENAME_CSV, $this->getProperty(DATA_OUTPUT_FILENAME) . ".csv");

        $extracompleted = "";
        if ($this->getProperty(DATA_OUTPUT_COMPLETED) == INTERVIEW_COMPLETED) {
            $extracompleted = " and completed=" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_COMPLETED));
        }

        // find any data names in the data   
        $extra = "";
        if ($this->getProperty(DATA_OUTPUT_FROM) != "") {
            $extra .= " and ts > '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_FROM)) . "'";
        }
        if ($this->getProperty(DATA_OUTPUT_TO) != "") {
            $extra .= " and ts < '" . prepareDatabaseString($this->getProperty(DATA_OUTPUT_TO)) . "'";
        }
        $datanames = array();
        $this->maxwidths = array();

        if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATARECORD_TABLE) {
            $query = "select distinct datanames from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_datarecords where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra;
            $res = $this->db->selectQuery($query);
            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {
                    /* go through records */
                    while ($row = $this->db->getRow($res)) {
                        $datanames = array_unique(array_merge($datanames, explode("~", gzuncompress($row["datanames"]))));
                        $row = null;
                        unset($row);
                    }
                }
                $res = null;
                unset($res);

                // get max width
                $decrypt = "answer";
                if ($this->survey->getDataEncryptionKey() != "") {
                    $decrypt = "cast(aes_decrypt(answer, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as char)";
                }
                $query = "SELECT variablename, MAX( LENGTH( " . $decrypt . " )) AS max FROM " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data WHERE suid = " . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extra . $extracompleted . " GROUP BY variablename";
                $res = $this->db->selectQuery($query);
                if ($res) {
                    if ($this->db->getNumberOfRows($res) == 0) {
                        
                    } else {
                        /* go through records */
                        while ($row = $this->db->getRow($res)) {
                            $this->maxwidths[strtoupper($row["variablename"])] = $row["max"];
                            $row = null;
                            unset($row);
                        }
                    }
                    $res = null;
                    unset($res);
                }
            }
        } else {
            $decrypt = ", MAX( LENGTH(answer)) as max";
            if ($this->survey->getDataEncryptionKey() != "") {
                $decrypt = ", MAX( LENGTH( cast(aes_decrypt(answer, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as char))) AS max";
            }
            $query = "select variablename" . $decrypt . " from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra . " group by variablename";
            $res = $this->db->selectQuery($query);
            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {
                    /* go through records */
                    while ($row = $this->db->getRow($res)) {
                        $datanames[] = $row["variablename"];
                        $this->maxwidths[strtoupper($row["variablename"])] = $row["max"];
                        $row = null;
                        unset($row);
                    }
                }
                $res = null;
                unset($res);
            }
        }

        // check for filter
        $filter = array();
        if ($this->getProperty(DATA_OUTPUT_VARLIST) != "") {
            $filter = explode("~", $this->getProperty(DATA_OUTPUT_VARLIST));
        }

        /* collect info to sort */
        $vars = array();  
        $this->arrayfields = array();
        $this->skipvariables = array();
        $this->setofenumeratedbinary = array();
        $this->descriptives = array();
        $this->withsuffix = array();
        
        foreach ($datanames as $d) {
            $vd = $this->getVariableDescriptive(getBasicName($d));
            if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it           
                // hidden variable
                if ($this->getProperty(DATA_OUTPUT_HIDDEN) == HIDDEN_YES && $vd->isHidden()) {
                    continue;
                } else if (sizeof($filter) > 0 && !inArray($vd->getName(), $filter)) {
                    continue;
                }

                // array and not a specific instance, then store for later so we know to process it differently
                if ($vd->isArray() && contains($d, "[") == false) {
                    $this->arrayfields[strtoupper($d)] = strtoupper($d);
                }

                // create index to track
                $section = $this->getSection($vd->getSeid());
                $key = $vd->getSuid() . $vd->getSeid() . $vd->getVsid();
                if (isset($vars[$key])) {
                    $arrtemp = $vars[$key];
                    $arr = $arrtemp["vars"];
                } else {
                    $arr = array();
                }
                $arr[] = strtoupper($d); // this needs to work to ensure we are getting the right array and putting it in $vars array
                $vars[$key] = array("order" => $section->getPosition() . $vd->getSeid() . $vd->getPosition() . $vd->getVsid(), "vars" => $arr);

                $vd = null;
                unset($vd);
            }
        }

        $datanames = null;
        unset($datanames);

        /* collect variables not found in data */
        if ($this->getProperty(DATA_OUTPUT_VARIABLES_WITHOUT_DATA) == VARIABLES_WITHOUT_DATA_YES) {
            $this->variabledescriptives = $this->survey->getVariableDescriptives();
            foreach ($this->variabledescriptives as $vd) {

                // hidden variable
                if ($this->getProperty(DATA_OUTPUT_HIDDEN) == DATA_HIDDEN && $vd->isHidden()) {
                    continue;
                } else if (inArray($vd->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                    continue;
                } else if (sizeof($filter) > 0 && !inArray($vd->getName(), $filter)) {
                    continue;
                }

                // array, then store for later so we know to process it differently
                if ($vd->isArray()) {
                    $this->arrayfields[strtoupper($vd->getName())] = strtoupper($vd->getName());
                }

                $section = $this->getSection($vd->getSeid());
                $key = $vd->getSuid() . $vd->getSeid() . $vd->getVsid();
                if (isset($vars[$key])) {
                    $arrtemp = $vars[$key];
                    $arr = $arrtemp["vars"];
                } else {
                    $arr = array();
                }
                $arr[] = strtoupper($vd->getName());
                $vars[$key] = array("order" => $section->getPosition() . $vd->getSeid() . $vd->getPosition() . $vd->getVsid(), "vars" => $arr);

                // no width set, so first time, then no data at all so we set width to 2
                if (!isset($this->maxwidths[strtoupper($vd->getName())])) {
                    $this->maxwidths[strtoupper($vd->getName())] = 2;
                }
            }
        }
        else {
            foreach ($datanames as $d) {
                $vd = $this->getVariableDescriptive(getBasicName($d));
                if ($vd->getVsid() != "") { // if info not found, then ignore since we don't know how to handle it  
                    // no width set, so first time, then no data at all so we set width to 2
                    if (!isset($this->maxwidths[strtoupper($vd->getName())])) {
                        $this->maxwidths[strtoupper($vd->getName())] = 2;
                    }
                }
            }
        }


        /* sort data names by section position, section seid, variable position, variable name */
        uasort($vars, 'dataexportSort');

        /* retrieve variable information */
        foreach ($vars as $key => $subvars) {
            
            $subvars = $subvars["vars"];
            sort($subvars, SORT_STRING); // sort by variable name
            foreach ($subvars as $d) {
                $this->processVariable($d, $this->getVariableDescriptive(getBasicName($d)));
            }
            $vars[$key] = null;
        }

        $vars = null;
        unset($vars);
        $this->descriptives = null;
        unset($this->descriptives);

        /* check for primkey variable presence */
        if ($this->getProperty(DATA_OUTPUT_PRIMARY_KEY_IN_DATA) != PRIMARYKEY_NO) {
            /* TODO */
        }

        /* set number of variables */
        $this->variablenumber = sizeof($this->variablenames);

        /* get number of records */
        if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATARECORD_TABLE) {
            $query = "select count(*) as cnt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_datarecords where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra;
            $res = $this->db->selectQuery($query);
            $row = $this->db->getRow($res);
            $this->recordcount = $row["cnt"];
        } else {
            $query = "select primkey from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and variablename='prim_key' and answer is not null and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra;
            $res = $this->db->selectQuery($query);
            $this->recordcount = $this->db->getNumberOfRows($res);
        }
;
        /* start writing files */
        $outputtype = strtolower($this->getProperty(DATA_OUTPUT_FILETYPE));
        $this->asked = sprintf('%.0F', "8.988465625461158E307");
        if ($outputtype == FILETYPE_CSV) {
            $this->startCSVFile();
            $this->separator = ",";
        } else if ($outputtype == FILETYPE_STATA) {
            $this->startStataFile();
            $this->littleendian = $this->isLittleEndian();

            // http://www.stata.com/help.cgi?dta_113
            $this->shortempty = 32741;
            $this->shorterror = 32763;
            $this->shortdk = 32745;
            $this->shortna = 32755;
            $this->shortrf = 32759;
            $this->shortmarkempty = 32746;

            $this->doubleempty = sprintf('%.0F', "8.98846567431158E307"); // 2^1013            
            $this->doubledk = sprintf('%.0F', "8.99724347282165E307");
            $this->doublerf = sprintf('%.0F', "9.027965767606894E307");
            $this->doublemarkempty = sprintf('%.0F', "8.98846567431158E307") * 1.001220703125000;
            $this->doublena = sprintf('%.0F', "8.98846567431158E307") * 1.003417968750000000;
            $this->doubleerror = sprintf('%.0F', "8.98846567431158E307") * 1.005371093750000000;

            // floats not used right now
            $this->floatempty = sprintf('%.0F', "1.7014118E38F");
            $this->floatdk = sprintf('%.0F', "1.7030734E38F");
            $this->floatna = sprintf('%.0F', "1.7030734E38F"); // TODO
            $this->floatrf = sprintf('%.0F', "1.7088887E38F");
            $this->floatmarkempty = sprintf('%.0F', "1.7030734E38F");
        }

        // get languages, modes, versions
        $this->languages = $this->getProperty(DATA_OUTPUT_LANGUAGES);
        $this->modes = $this->getProperty(DATA_OUTPUT_MODES);
        $this->versions = $this->getProperty(DATA_OUTPUT_VERSIONS);
        $this->encoding = $this->getProperty(DATA_OUTPUT_ENCODING);

        /* go through all records */
        if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATARECORD_TABLE) {
            $decrypt = "data as data_dec";
            if ($this->survey->getDataEncryptionKey() != "") {
                $decrypt = "aes_decrypt(data, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as data_dec";
            }
            $query = "select primkey, $decrypt from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_datarecords where suid=" . prepareDatabaseString($this->suid) . " and length(primkey) >= " . prepareDatabaseString($this->minprimkeylength) . " and length(primkey) <= " . prepareDatabaseString($this->maxprimkeylength) . $extracompleted . $extra . " order by primkey";
            $res = $this->db->selectQuery($query);

            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {
                    /* go through records */
                    while ($row = $this->db->getRow($res)) {
                        $record = new DataRecord();
                        $record->setAllData(unserialize(gzuncompress($row["data_dec"])));
                        if ($outputtype == FILETYPE_CSV) {
                            $this->addCSVRecord($row["primkey"], $record);
                        } else if ($outputtype == FILETYPE_STATA) {
                            $this->addStataRecord($row["primkey"], $record);
                        }
                        $record = null;
                        $row = null;
                        unset($record);
                        unset($row);
                    }
                }
            }
        } else {
            // we already got all distinct primkeys before when we determined the number of records

            if ($res) {
                if ($this->db->getNumberOfRows($res) == 0) {
                    return 'No records found';
                } else {

                    $decrypt = "answer as data_dec";
                    if ($this->survey->getDataEncryptionKey() != "") {
                        $decrypt = "aes_decrypt(answer, '" . prepareDatabaseString($this->survey->getDataEncryptionKey()) . "') as data_dec";
                    }

                    /* go through records */
                    while ($row = $this->db->getRow($res)) {

                        $query = "select primkey, variablename, $decrypt, language, mode, version, dirty from " . $this->getProperty(DATA_OUTPUT_MAINDATATABLE) . "_data where suid=" . prepareDatabaseString($this->suid) . " and primkey='" . prepareDatabaseString($row["primkey"]) . "'";
                        $this->currentrecord = array();
                        $res2 = $this->db->selectQuery($query);
                        if ($res2) {
                            while ($row2 = $this->db->getRow($res2)) {
                                $this->currentrecord[strtoupper($row2["variablename"])] = array("name" => $row2["variablename"], "dirty" => $row2["dirty"], "answer" => $row2["data_dec"], "language" => $row2["language"], "mode" => $row2["mode"], "version" => $row2["version"]);
                                $row2 = null;
                                unset($row2);
                            }

                            if (sizeof($this->currentrecord) > 0) {
                                if ($outputtype == FILETYPE_CSV) {
                                    $this->addCSVRecord($row["primkey"]);
                                } else if ($outputtype == FILETYPE_STATA) {                                    
                                    $this->addStataRecord($row["primkey"]);
                                }
                            }
                            $this->currentrecord = null;
                            unset($this->currentrecord);
                        }
                        $query = null;
                        unset($query);
                        $res2 = null;
                        unset($res2);
                        $row = null;
                        unset($row);
                    }
                }
            }
        }

        /* finish */
        if ($outputtype == FILETYPE_CSV) {
            $this->finishCSVFile();
        } else if ($outputtype == FILETYPE_STATA) {
            $this->addValueLabels();
            $this->finishStataFile();
        }

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* WRITE DATA FILES */

    /* WRITE STATA FILE */

    function prepareName($rn) {
        $rn = str_replace("[", "_", $rn);
        $rn = str_replace("]", "_", $rn);
        $rn = str_replace(",", "_", $rn);
        $rn = trim(str_replace(".", "", $rn));
        return $this->stripNonAscii($rn);
    }

    function stripWordQuotes($str) {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        return str_replace($this->chrmap, "", $str);
    }

    function stripNonAscii($str) {

        // https://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
        $str = $this->stripWordQuotes($str);

        // http://stackoverflow.com/questions/1176904/php-how-to-remove-all-non-printable-characters-in-a-string
        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str);
    }

    function prepareLabel($label, $replace = array("'", "^", "~")) {
        foreach ($replace as $r) {
            $label = str_replace($r, "", $label);
        }

        // can't have any characters with accents in STATA labels
        return $this->stripNonAscii($label);
    }

    function startStataFile() {

        $this->statahandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }

        $this->statahandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_STATA), "w");
        if (!$this->statahandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_STATA);
        $encoding = $this->getProperty(DATA_OUTPUT_ENCODING);
        $this->variabletypes = array();
        $this->variables = array();
        $this->formats = array();

        /* http://www.stata.com/help.cgi?dta_113 */

        /* determine length and format of variables */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            $variablename = $this->variablenames[$i];
            $variablename = $this->prepareName($variablename);
            $this->variables[$i] = $variablename;
            $maximum = $this->answerwidth[$i];
            switch ($this->datatypes[$i]) {
                case STATA_TYPE_STRING:
                    if ($maximum > 244) {
                        $maximum = 244;
                    }
                    $this->variabletypes[$i] = $maximum;
                    $this->formats[$i] = "%" . $maximum . "s";
                    break;
                case STATA_TYPE_SHORT:
                    $this->variabletypes[$i] = STATA_DATAFORMAT_SHORT;
                    $this->formats[$i] = "%" . $maximum . ".0f";
                    break;
                case STATA_TYPE_INTEGER:
                    $this->variabletypes[$i] = STATA_DATAFORMAT_SHORT;
                    $this->formats[$i] = "%" . $maximum . ".0f";
                    break;
                case STATA_TYPE_DOUBLE:
                    $this->variabletypes[$i] = STATA_DATAFORMAT_DOUBLE;
                    $this->formats[$i] = "%" . $maximum . ".0g";
                    break;
            }
        }

        /* START OF FILE */

        /* header */
        $this->recordbytes = null;
        $this->writeByte($this->recordbytes, 113);
        $this->writeByte($this->recordbytes, 1);
        $this->writeByte($this->recordbytes, 1);
        $this->writeByte($this->recordbytes, 0);
        $this->writeShort($this->recordbytes, $this->variablenumber);
        $this->writeInt($this->recordbytes, $this->recordcount);
        for ($i = 0; $i < 81; $i++) {
            $this->writeByte($this->recordbytes, 0);
        }
        for ($i = 0; $i < 18; $i++) {
            $this->writeByte($this->recordbytes, 0);
        }

        /* type list */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            $this->writeByte($this->recordbytes, $this->variabletypes[$i]);
        }

        $trunced = array();
        $untrunced = array();

        /* variable list */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            $variablename = $this->stripNonAscii($this->variables[$i]);

            /* check for lowercase/uppercase! */
            if ($this->getProperty(DATA_OUTPUT_FIELDNAME_CASE) == FIELDNAME_LOWERCASE) {
                $variablename = strtolower($variablename);
            }

            /* write variable name */
            $this->writeString($this->recordbytes, $variablename, 33, $encoding);
        }

        /* write empty */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            $this->writeShort($this->recordbytes, 0);
        }
        $this->writeShort($this->recordbytes, 0);

        /* variable format list */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            $this->writeString($this->recordbytes, $this->formats[$i], 12, $encoding);
        }

        /* value label list */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            if (isset($this->valuelabels[$i])) {

                // don't add definition for value labels for summary string set of enumerated 
                $tvar = $this->variables[$i];
                if (isset($this->setofenumeratedvariables[$tvar]) || $this->valuelabels[$i] == "" || $this->getProperty(DATA_OUTPUT_INCLUDE_VALUE_LABELS) == VALUELABEL_NO) {
                    for ($v = 0; $v < 33; $v++) {
                        $this->writeByte($this->recordbytes, 0);
                    }
                } else {
                    
                    $labelname = $this->getProperty(DATA_OUTPUT_VALUELABEL_PREFIX);                    
                    $this->writeString($this->recordbytes, $labelname . $i, 33, $encoding);
                }
            }
        }

        /* label list */
        for ($i = 0; $i < $this->variablenumber; $i++) {
            $this->writeString($this->recordbytes, $this->prepareLabel($this->labels[$i]), 81, $encoding);
        }

        /* no long names, so the characteristics section is empty */
        if (sizeof($trunced) == 0) {
            for ($i = 0; $i < 5; $i++) {
                $this->writeByte($this->recordbytes, 0);
            }
        }
        // trunced names: add as characteristics
        else {
            $actualname = "actual";
            for ($i = 0; $i < $this->variablenumber; $i++) {
                if (isset($untrunced[$i])) {

                    /* get the actual name */
                    $rn = $untrunced[$i];

                    /* get trunced name */
                    $trunc = $trunced[strtoupper($n)];
                    if ($this->getProperty(DATA_OUTPUT_FIELDNAME_CASE) == FIELDNAME_LOWERCASE) {
                        $trunc = strtolower($trunc);
                    }

                    /* make original name in same format as the other ones */
                    $rn = $this->prepareName($rn);

                    /* open */
                    $this->writeByte($this->recordbytes, 1);

                    /* length (66 for the variable name and characteristic name, then the content plus 1; where plus 1 is for the finishing zero) */
                    $this->writeInt($this->recordbytes, 66 + strlen($rn) + 1);

                    /* shortened variable name */
                    $this->writeString($this->recordbytes, $trunc, 33, $encoding);

                    /* characteristic name */
                    $this->writeString($this->recordbytes, $actualname, 33, $encoding);

                    /* original name */
                    $this->writeString($this->recordbytes, $n, strlen($rn) + 1, $encoding);
                }
            }
            /* close */
            for ($i = 0; $i < 5; $i++) {
                $this->writeByte($this->recordbytes, 0);
            }
        }
        fwrite($this->statahandle, $this->recordbytes);
    }

    function getValueLabel($option) {
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
                    $pos = strpos($labeltext, $remainder[0]);
                    $label = trim(substr($labeltext, $pos + strlen($remainder[0])));
                } else {
                    $label = $labeltext;
                }
            }
        } else {
            $label = $labeltext;
        }

        if ($this->getProperty(DATA_OUTPUT_INCLUDE_VALUE_LABEL_NUMBERS) == VALUELABELNUMBERS_YES) {
            return $code . " " . $label;
        } else {
            return $label;
        }
    }

    function addValueLabels() {
        $encoding = $this->getProperty(DATA_OUTPUT_ENCODING);
        if ($this->getProperty(DATA_OUTPUT_INCLUDE_VALUE_LABELS) == VALUELABEL_YES) {
            $this->recordbytes = null;
            for ($i = 0; $i < $this->variablenumber; $i++) {
                if (isset($this->valuelabels[$i]) && $this->valuelabels[$i] != "") {

                    $tvar = $this->variablenames[$i];

                    // don't add definition for value labels for summary string set of enumerated variable
                    if (isset($this->setofenumeratedvariables[$tvar])) {
                        continue; // no value labels for set of enumerated string summation variables
                    }

                    // get raw labels and sort by answer code ascending
                    $rawlabels = explode("\r\n", trim($this->valuelabels[$i]));
                    $labels = array();
                    for ($j = 0; $j < sizeof($rawlabels); $j++) {
                        $labelarray = splitString("/ /", $rawlabels[$j]);
                        $labels[$labelarray[0]] = $rawlabels[$j];
                    }
                    ksort($labels);
                    $labels = array_values($labels); // strip out keys used for sorting

                    $length = 0;
                    for ($j = 0; $j < sizeof($labels); $j++) {
                        $label = $this->getValueLabel($labels[$j]);
                        $length = $length + strlen($this->prepareLabel($label)) + 1;
                    }

                    // write the length of the value labels
                    $this->writeInt($this->recordbytes, $length + 2 * 4 + 2 * (sizeof($labels) * 4));

                    // write the value label name
                    $labelname = $this->getProperty(DATA_OUTPUT_VALUELABEL_PREFIX);
                    $this->writeString($this->recordbytes, $labelname . $i, 33, $encoding);
                    for ($j = 0; $j < 3; $j++) {
                        $this->writeByte($this->recordbytes, 0);
                    }

                    // write the number of value labels
                    $this->writeInt($this->recordbytes, sizeof($labels));

                    // write the value label text length
                    $this->writeInt($this->recordbytes, $length);

                    // write the length of each value label
                    $position = 0;
                    for ($j = 0; $j < sizeof($labels); $j++) {
                        $this->writeInt($this->recordbytes, $position);
                        $label = $this->getValueLabel($labels[$j]);

                        // update position
                        $position = $position + strlen($this->prepareLabel($label)) + 1;
                    }

                    // write the codes of value labels
                    for ($j = 0; $j < sizeof($labels); $j++) {
                        $labelarray = splitString("/ /", $labels[$j]);
                        $val = $labelarray[0];
                        $this->writeInt($this->recordbytes, $val);
                    }

                    // write value labels themselves
                    for ($j = 0; $j < sizeof($labels); $j++) {
                        $label = $this->getValueLabel($labels[$j]);
                        $this->writeString($this->recordbytes, $this->prepareLabel($label), strlen($this->prepareLabel($label)) + 1, $encoding);
                    }
                }
            }

            // write value labels
            fwrite($this->statahandle, $this->recordbytes);
        }
    }

    function isShort(&$value) {
        $test = $value;
        if (startsWith($test, "-")) {
            $test = substr($test, 1);
        }
        $test = str_replace(",", "", $test); // replace any commas from an input mask
        if (is_numeric($test)) {
            return true;
        }
        return false;
    }

    function isDouble($value) {
        $test = $value;
        if (startsWith($test, "-")) {
            $test = substr($test, 1);
        }
        $test = str_replace(",", "", $test); // replace any commas from an input mask
        if (is_numeric($test)) {
            return true;
        }
        return false;
    }

    function addStataRecord($primkey, $record = null) {

        $this->shortenedNames = array();
        $skipped = array();
        $this->recordbytes = null;
        
        for ($i = 0; $i < $this->variablenumber; $i++) {
            if (!isset($this->variablenames[$i])) {
                continue;
            }
            $fieldname = $this->variablenames[$i];
            if ($fieldname == "") {
                continue;
            }
            $variableobject = null;
            $value = null;

            // skip variable
            if (inArray(strtoupper($fieldname), $this->skipvariables)) {
                $value = $skipped[strtoupper($fieldname)];
            }           
            // set of enumerated/multi dropdown
            else if (isset($this->withsuffix[strtoupper($fieldname)])) {                
                $binary = $this->setofenumeratedbinary[strtoupper($fieldname)];
                $last = strrpos($fieldname, $this->withsuffix[strtoupper($fieldname)]);
                $num = str_replace("s", "", $this->withsuffix[strtoupper($fieldname)]);
                $fieldname = substr(($fieldname), 0, $last);
                $value = $this->getValue($primkey, $record, $fieldname);
                if ($value != null && !inArray($value, array("", ANSWER_DK, ANSWER_RF, ANSWER_NA, $this->asked))) {

                    $arr = array();
                    if (contains($value, SEPARATOR_SETOFENUMERATED)) {
                        $arr = explode(SEPARATOR_SETOFENUMERATED, $value);
                    } else if (contains($value, SEPARATOR_SETOFENUMERATED_OLD)) {
                        $arr = explode(SEPARATOR_SETOFENUMERATED_OLD, $value);
                    } else {
                        $arr[] = $value;
                    }
                    if (inArray($num, $arr)) {
                        $value = $num;
                        if ($binary == SETOFENUMERATED_BINARY) {
                            $value = 1;
                        }
                    } else {
                        $value = ""; // set to "", so it appears as just empty rather than skipped (since it was not skipped, just not selected)
                        if ($binary == SETOFENUMERATED_BINARY) {
                            $value = "0";
                        }
                    }
                }
            } else {                
                $value = $this->getValue($primkey, $record, $fieldname);
            }

            // we have a value, then check for serialized array answer
            if ($value != null) {

                // variable is an instance of an array variable
                if (inArray(strtoupper(getBasicName($fieldname)), $this->arrayfields)) {

                    // this is a compressed string!
                    $v = gzuncompress($value);
                    if ($v !== false) {

                        // this is a serialized string!
                        if (unserialize($v) !== false) {
                            $v1 = unserialize(gzuncompress($value));

                            // the unserialized is an array or object, then output empty string so it appears as empty (not skipped)
                            if (is_array($v1) || is_object($v1)) {
                                $value = "";
                            }
                        }
                    }
                }
            }

            // primary key encryption
            if ($fieldname == VARIABLE_PRIMKEY && $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                $value = encryptC($value, $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
            }


            // check if it is a setofenum/multi-dropdown
            if (isset($this->variabledescriptives[strtoupper($fieldname)])) {
                $vardes = $this->variabledescriptives[strtoupper($fieldname)];
            } else {
                $vardes = $this->getVariableDescriptive($fieldname);
            }
            $setofenum = false;
            if (inArray($vardes->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                $setofenum = true;
            }

            // write variable
            $type = $this->variabletypes[$i];
            $width = $this->answerwidth[$i];
            if ($type == STATA_DATAFORMAT_SHORT) {
                $shortobj = $this->shortempty;
                if ($value == ANSWER_RF) {
                    $shortobj = $this->shortrf;
                } else if ($value == ANSWER_DK) {
                    $shortobj = $this->shortdk;
                } else if ($value == ANSWER_NA) {
                    $shortobj = $this->shortna;
                } else if (!inArray($value, array("", null, $this->asked))) {
                    $value = str_replace(",", "", $value); // replace any commas from an input mask
                    //if ($this->isShort($value)) {
                    $shortobj = $value;
                    //} else {
                    //    $shortobj = $this->shorterror;
                    //}
                } else if ($value == $this->asked) {
                    if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) {
                        $shortobj = $this->shortmarkempty;
                    } else if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_SKIP_VARIABLE) {
                        if (isset($this->skipvariables[strtoupper($this->variablenames[$i])])) {
                            $skipped[strtoupper($this->skipvariables[strtoupper($this->variablenames[$i])])] = 1;
                        }
                    }
                }


                $this->writeShort($this->recordbytes, $shortobj);
                $shortobj = null;
                unset($shortobj);
            } else if ($type == STATA_DATAFORMAT_DOUBLE) {

                $doubleobj = $this->doubleempty;
                if ($value == ANSWER_RF) {
                    $doubleobj = $this->doublerf;
                } else if ($value == ANSWER_DK) {
                    $doubleobj = $this->doubledk;
                } else if ($value == ANSWER_NA) {
                    $doubleobj = $this->doublena;
                } else if (!inArray($value, array("", null, $this->asked))) {
                    $value = str_replace(",", "", $value); // replace any commas from an input mask
                    //if ($this->isDouble($value)) {
                    $doubleobj = $value;
                    //} else {
                    //    $doubleobj = $this->doubleerror;
                    //}
                } else if ($value == $this->asked) {
                    if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) {
                        $doubleobj = $this->doublemarkempty;
                    } else if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_SKIP_VARIABLE) {
                        if (isset($this->skipvariables[strtoupper($this->variablenames[$i])])) {
                            $skipped[strtoupper($this->skipvariables[strtoupper($this->variablenames[$i])])] = 1;
                        }
                    }
                }
                $this->writeDouble($this->recordbytes, $doubleobj);
                $doubleobj = null;
                unset($doubleobj);
            } else {
                $stringobj = "";
                if ($setofenum == true) {
                    $stringobj = ".";
                }
                if ($value == ANSWER_RF) {
                    $stringobj = ".r";
                } else if ($value == ANSWER_DK) {
                    $stringobj = ".d";
                } else if ($value == ANSWER_NA) {
                    $stringobj = ".n";
                } else if (!inArray($value, array("", null, $this->asked))) {
                    $stringobj = $value;
                    $stringobj = str_replace("\"", "\"\"", $stringobj);
                    $stringobj = str_replace("\r", "", $stringobj);
                    $stringobj = str_replace("\n", "", $stringobj);
                    $stringobj = str_replace("\r\n", "", $stringobj);
                    $stringobj = $this->stripWordQuotes($stringobj);
                    //if (strlen($stringobj) > 244) {
                    //   $stringobj = substr($stringobj, 0, 243);
                    //}
                } else if ($value == $this->asked) {
                    if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) {
                        $stringobj = ".e";
                    } else if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_SKIP_VARIABLE) {
                        if (isset($this->skipvariables[strtoupper($this->variablenames[$i])])) {
                            $skipped[strtoupper($this->skipvariables[strtoupper($this->variablenames[$i])])] = 1;
                        }
                    }
                }

                $this->writeString($this->recordbytes, $stringobj, $width, $this->encoding);
                $stringobj = null;
                unset($stringobj);
            }

            $variableobject = null;
            unset($variableobject);
            $value = null;
            unset($value);
        }

        // write record
        fwrite($this->statahandle, $this->recordbytes);
        $this->recordbytes = null;
        unset($this->recordbytes);
    }

    function finishStataFile() {
        if ($this->statahandle) {
            fclose($this->statahandle);
        }
    }

    function streamwrite($bytes) {
        $this->recordbytes .= $bytes;
        //fwrite($this->statahandle, $bytes);
    }

    function writeByte($handle, $byte) {
        $this->streamwrite(pack("c", $byte));
    }

    function writeShort($handle, $byte) {
        $this->streamwrite(pack("n", $byte)); // n= big endian order; v = little endian order
    }

    function writeInt($handle, $byte) {
        $this->streamwrite(pack("N", $byte)); // using long here, using 'i' for integer does not work!
    }

    /* NOT USED AT THE MOMENT */

    function writeFloat($handle, $byte) {
        $this->streamwrite(pack("f", $byte)); // f
    }

    /* END NOT USED AT THE MOMENT */

    function isLittleEndian() {
        $testint = 0x00FF;
        $p = pack('S', $testint);
        return $testint === current(unpack('v', $p));
    }
    
    function writeDouble($handle, $byte) {
        if ($this->littleendian) {
            $bytes = strrev(pack("d", $byte));
        } else {
            $bytes = pack("d", $byte);
        }
        
        //fwrite($handle, $bytes); // d
        $this->streamwrite($bytes); // d
        $bytelen = mb_strlen($bytes, 'ISO-8859-1'); // necessary otherwise stata file is corrupted
        for ($b = 0; $b < (8 - $bytelen); $b++) { // pad to 8 if necessary
            $this->writeByte($handle, 0);
        }
    }

    function writeString($handle, $string, $length, $encoding) {        
        $string = mb_convert_encoding($string, $encoding, "UTF-8");
        $len = mb_strlen($string, "ISO-8859-1"); // necessary otherwise stata file is corrupted
        if ($len > $length) {
            $string = mb_substr($string, 0, $length); // cut off if too long            
        }
        
        $stop = $length - $len;
        //fwrite($handle, $string);
        $this->streamwrite($string);
        for ($b = 0; $b < $stop; $b++) {
            $this->writeByte($handle, 0);
        }
        return;

        /* BELOW DOES NOT WORK WITH UNPACKING; STRING GETS WRITTEN BUT STATA
         * DOES NOT RECOGNIZE IT
         */
        /* $asciiBytes = null;
          if ($string != null) {
          $asciiBytes = unpack('c*', $string);
          // TODO: asciiBytes = string.getBytes(encoding);
          }
          for ($b = 0; $b < ($length); $b++) {
          if ($asciiBytes != null && $b < (sizeof($asciiBytes)+1)) {
          $this->writeByte($handle, $asciiBytes[$b]);
          } else {
          $this->writeByte($handle, 0);
          }
          } */
    }

    /* WRITE CSV FILE */

    function startCSVFile() {

        $this->csvhandle = null;
        $outdir = sys_get_temp_dir();
        if (!endsWith($outdir, DIRECTORY_SEPARATOR)) {
            $outdir .= DIRECTORY_SEPARATOR;
        }
        $this->csvhandle = fopen($outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV), "w");
        if (!$this->csvhandle) {
            /* show error */
            return;
        }
        $this->downloadlocation = $outdir . $this->getProperty(DATA_OUTPUT_FILENAME_CSV);

        $separator = ",";
        $variables = "";

        for ($i = 0; $i < $this->variablenumber; $i++) {
            $variable = $this->prepareName($this->variablenames[$i]);
            if (trim($variables) != "") {
                $variables .= $separator;
            }
            $variables .= $variable;
        }

        // force opening in utf8
        if ($this->csvhandle) {
            fwrite($this->csvhandle, "\xEF\xBB\xBF");
        }

        /* write headers */
        if ($this->csvhandle) {
            fwrite($this->csvhandle, $variables . "\n");
        }
    }

    function getDataTableValue($prim, $field) {

        if (!array_key_exists(strtoupper($field), $this->currentrecord)) {
            return null;
        }

        if (isset($this->currentrecord[strtoupper($field)])) {

            $arr = $this->currentrecord[strtoupper($field)];

            /* no match on language, mode and version, then treat as never asked */
            if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($arr["language"], $this->languages)))) {//
                $value = null;
            } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($arr["mode"], $this->modes)))) {//
                $value = null;
            }
            // match
            else {
                $value = $arr["answer"];
                if ($value == null) {
                    $value = $this->asked;
                }
            }

            // check for clean/dirty data
            if ($this->getProperty(DATA_OUTPUT_CLEAN) == DATA_CLEAN) {
                if ($arr["dirty"] == DATA_DIRTY) {
                    $value = null; // treat as never asked
                }
            }
        }
        // never asked
        else {
            $value = null;
        }

        return $value;
    }

    function getValue($primkey, $record, $fieldname) {

        $value = null; // assume never asked
        
        // from _data table
        if ($this->getProperty(DATA_OUTPUT_TYPE) == DATA_OUTPUT_TYPE_DATA_TABLE) {
            $value = $this->getDataTableValue($primkey, $fieldname);
        }
        // from _datarecords table
        else {
            $variableobject = $record->getData($fieldname);
            if ($variableobject) {

                /* no match on language, mode and version, then treat as never asked */
                if (!(sizeof($this->languages) == 0 || (sizeof($this->languages) > 0 && inArray($variableobject->getLanguage(), $this->languages)))) {//
                    $value = null;
                } else if (!(sizeof($this->modes) == 0 || (sizeof($this->modes) > 0 && inArray($variableobject->getMode(), $this->modes)))) {//
                    $value = null;
                }
                // match
                else {
                    $value = $variableobject->getAnswer($primkey);
                    if ($value == null) {
                        $value = $this->asked;
                    }
                }

                // check for clean/dirty data
                if ($this->getProperty(DATA_OUTPUT_CLEAN) == DATA_CLEAN) {
                    if ($variableobject->isDirty()) {
                        $value = null; // treat as never asked
                    }
                }
            }
            // never asked
            else {
                $value = null;
            }
        }

        // return value
        return $value;
    }

    function addCSVRecord($primkey, $record = null) {

        $line = "";
        $skipped = array();
        for ($i = 0; $i < $this->variablenumber; $i++) {
            if (!isset($this->variablenames[$i])) {
                continue;
            }
            $fieldname = $this->variablenames[$i];
            if ($fieldname == "") {
                continue;
            }
            $variableobject = null;
            $value = null;

            // skip variable
            if (inArray(strtoupper($fieldname), $this->skipvariables)) {
                $value = $skipped[strtoupper($fieldname)];
            }
            // set of enumerated/multi dropdown
            else if (isset($this->withsuffix[strtoupper($fieldname)])) {
                $binary = $this->setofenumeratedbinary[strtoupper($fieldname)];
                $last = strrpos($fieldname, $this->withsuffix[strtoupper($fieldname)]);
                $num = str_replace("s", "", $this->withsuffix[strtoupper($fieldname)]);
                $fieldname = substr(($fieldname), 0, $last);
                $value = $this->getValue($primkey, $record, $fieldname);

                if ($value != null && !inArray($value, array("", ANSWER_DK, ANSWER_RF, ANSWER_NA, $this->asked))) {
                    $arr = array();
                    if (contains($value, SEPARATOR_SETOFENUMERATED)) {
                        $arr = explode(SEPARATOR_SETOFENUMERATED, $value);
                    } else if (contains($value, SEPARATOR_SETOFENUMERATED_OLD)) {
                        $arr = explode(SEPARATOR_SETOFENUMERATED_OLD, $value);
                    } else {
                        $arr[] = $value;
                    }
                    if (inArray($num, $arr)) {
                        $value = $num;
                        if ($binary == SETOFENUMERATED_BINARY) {
                            $value = 1;
                        }
                    } else {
                        $value = ""; // set to "", so it appears as just empty rather than skipped (since it was not skipped, just not selected)
                        if ($binary == SETOFENUMERATED_BINARY) {
                            $value = "0";
                        }
                    }
                }
            } else {
                $value = $this->getValue($primkey, $record, $fieldname);
            }

            // we have a value, then check for serialized array answer
            if ($value != null) {

                // variable is an instance of an array variable
                if (inArray(strtoupper(getBasicName($fieldname)), $this->arrayfields)) {

                    // this is a compressed string!
                    $v = gzuncompress($value);
                    if ($v !== false) {

                        // this is a serialized string!
                        if (unserialize($v) !== false) {
                            $v1 = unserialize(gzuncompress($value));

                            // the unserialized is an array or object, then output empty string so it appears as empty (not skipped)
                            if (is_array($v1) || is_object($v1)) {
                                $value = "";
                            }
                        }
                    }
                }
            }

            // primary key encryption
            if ($fieldname == VARIABLE_PRIMKEY && $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION) != "") {
                $value = encryptC($value, $this->getProperty(DATA_OUTPUT_PRIMARY_KEY_ENCRYPTION));
            }

            // write variable            
            $stringobj = "";
            if ($value == ANSWER_RF) {
                $stringobj = ".r";
            } else if ($value == ANSWER_DK) {
                $stringobj = ".d";
            } else if ($value == ANSWER_NA) {
                $stringobj = ".n";
            } else if (!inArray($value, array("", null, $this->asked))) {
                $stringobj = $value;
            } // empty value!
            else if ($value == $this->asked) {
                if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_VARIABLE) {
                    $stringobj = ".e";
                } else if ($this->getProperty(DATA_OUTPUT_MARK_EMPTY) == MARKEMPTY_IN_SKIP_VARIABLE) {
                    if (isset($this->skipvariables[strtoupper($this->variablenames[$i])])) {
                        $skipped[strtoupper($this->skipvariables[strtoupper($this->variablenames[$i])])] = 1;
                    }
                }
            }

            /* add separator */
            if ($line != "" || ($i > 0 && ($i + 1 < $this->recordcount))) {
                $line .= $this->separator;
            }

            $stringobj = str_replace("\"", "\"\"", $stringobj);
            $stringobj = str_replace("\r", "", $stringobj);
            $stringobj = str_replace("\n", "", $stringobj);
            $stringobj = str_replace("\r\n", "", $stringobj);
            $stringobj = $this->stripWordQuotes($stringobj);
            $line .= '"' . $stringobj . '"';
            $stringobj = null;
            unset($stringobj);
        }

        fwrite($this->csvhandle, $line . "\n");
        $line = null;
        unset($line);
        //exit;
    }

    function finishCSVFile() {
        if ($this->csvhandle) {
            fclose($this->csvhandle);
        }
    }

    /* DOWNLOAD FUNCTIONS */

    function download() {

        $file = pathinfo($this->downloadlocation);

        /* check */
        if (isset($file['extension']) && inArray($file['extension'], array(DATA_OUTPUT_FILEEXTENSION_STATA, DATA_OUTPUT_FILEEXTENSION_CSV))) {

            /* allow for time */
            set_time_limit(0);

            $lastmodified = filemtime($this->downloadlocation); // http://www.php.net/filemtime
            $filesize = intval(sprintf("%u", filesize($this->downloadlocation))); // http://stackoverflow.com/questions/5501427/php-filesize-mb-kb-conversion
            // http://www.richnetapps.com/the-right-way-to-handle-file-downloads-in-php/

            /* declare headers */
            header("Content-Description: File Transfer");
            header("Content-Type: application/force-download");
            header("Content-Type: application/download");
            header('Content-Type: application/octet-stream');
            header("Content-Length: " . $filesize);
            header("Content-Disposition: attachment; filename=" . $file['filename'] . "." . $file['extension'] . '; modification-date="' . date('r', $lastmodified) . '";');

            if ($file['extension'] == DATA_OUTPUT_FILEEXTENSION_STATA) {
                header("Content-Type: application/dta");
            } else if ($file['extension'] == DATA_OUTPUT_FILEEXTENSION_CSV) {
                header("Content-Type: application/ms-excel");
            }

            /* prevent caching (http://stackoverflow.com/questions/13640109/how-to-prevent-browser-cache-for-php-site) */
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            // http://stackoverflow.com/questions/15299325/x-download-options-noopen-equivalent
            header("X-Content-Type-Options: nosniff"); // http://stackoverflow.com/questions/21723436/firefox-downloads-text-plain-instead-of-showing-it


            /* clean buffer before outputting file */
            ob_end_clean();

            // output file
            $chunksize = 1 * (1024 * 1024);
            $handle = fopen($this->downloadlocation, 'rb');
            $buffer = '';
            while (!feof($handle)) {
                print(@fread($handle, $chunksize));
                //ob_flush();
                flush();
            }
            fclose($handle);
            //ob_end_clean();
            // remove temporary file
            unlink($this->downloadlocation);

            // stop
            exit;
        }
    }

    /* LOG */

    function addToLog($entry) {
        $this->log[] = (sizeof($this->log) + 1) . "---" . date("Y-m-d h:i:s", time()) . "---" . $entry;
    }

    function displayLog() {
        foreach ($this->log as $n => $v) {
            echo $v . "<br/>";
        }
    }

}

?>