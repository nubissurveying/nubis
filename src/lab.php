<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Lab {

    var $lab;

    function __construct($primkey) {
        if ($primkey != null && $primkey != '') {
            global $db;
            $query = 'select *, aes_decrypt(barcode, "' . Config::labKey() . '") as barcode_dec, aes_decrypt(labbarcode, "' . Config::labKey() . '") as labbarcode_dec from ' . Config::dbSurveyData() . '_lab where primkey = "' . prepareDatabaseString($primkey) . '"';
            $result = $db->selectQuery($query);
            if ($result == null || $db->getNumberOfRows($result) == 0) { //not yet present: create?
                $queryNew = 'insert into ' . Config::dbSurveyData() . '_lab (primkey) values ("' . prepareDatabaseString($primkey) . '")';
                $db->executeQuery($queryNew);
                $result = $db->selectQuery($query);
            }
            $this->lab = $db->getRow($result);
        }
    }

    function fieldDBSStatus() {
        return Language::fieldDSBStatus();
    }

    function getBloodTests() {
        return array(
            1 => array('SR-AAA1A.1', '1ML'),
            2 => array('SR-AAA1A.2', '1ML'),
            3 => array('SR-AAA1A.3', '1ML'),
            4 => array('SR-AAA1A.4', '1ML'),
            5 => array('Serum', '1ML'),
            6 => array('Serum', '1ML'),
            7 => array('PK-AAA1A.1 Fasting Glucose', '1ML'),
            8 => array('PK-AAA1A.2 Fasting Glucose', '1ML'),
            9 => array('PK-AAA1A.1 2 Hours Glucose', '1ML'),
            10 => array('EDTA Whole Blood', '2ML'),
            11 => array('BC-AAA1A1.1', '.6ML'),
            12 => array('BC-AAA1A1.2', '.6ML'),
            13 => array('PE-AAA1A.1', '1ML'),
            14 => array('PE-AAA1A.2', '1ML'),
            15 => array('PE-AAA1A.3', '1ML'),
            16 => array('PE-AAA1A.4', '1ML'),
            17 => array('PE-AAA1A.5', '1ML'),
            18 => array('PE-AAA1A.6', '1ML'),
            19 => array('PE-AAA1A.7', '1ML'),
            20 => array('PE-AAA1A.8', '1ML'),
            21 => array('UR-AAA1.A.1', '2ML'),
            22 => array('UR-AAA1.A.2', '2ML'),
            23 => array('UR-AAA1.A.3', '2ML'),
            24 => array('UR-AAA1.A.4', '2ML'));

        /*     return array(
          1 => array('Serum', '1ML'),
          2 => array('Serum', '1ML'),
          3 => array('Serum', '1ML'),
          4 => array('Serum', '1ML'),
          5 => array('Glucose fasting', '1ML'),
          6 => array('Glucose fasting', '1ML'),
          7 => array('Glucose 2 Hrs', '1ML'),
          8 => array('Glucose 2 Hrs', '1ML'),
          9 => array('Plasma EDTA', '1ML'),
          10 => array('Plasma EDTA', '1ML'),
          11 => array('Plasma EDTA', '1ML'),
          12 => array('Plasma EDTA', '1ML'),
          13 => array('Plasma EDTA', '1ML'),
          14 => array('Plasma EDTA', '1ML'),
          15 => array('DNA EDTA', '.6ML'),
          16 => array('DNA EDTA', '.6ML'),
          17 => array('DNA EDTA', '.6ML'),
          18 => array('Whole blood', '1ML'),
          19 => array('HBA1c', '1ML'),
          20 => array('HBA1c', '1ML'),
          21 => array('Urine', '2ML'),
          22 => array('Urine', '2ML'),
          23 => array('Urine', '2ML'),
          24 => array('Urine', '2ML')); */
    }

    function getPrimkey() {
        return $this->lab['primkey'];
    }

    function getLabBarcode() {
        return $this->lab['labbarcode_dec'];
    }

    function setLabBarcode($labbarcode) {
        return $this->lab['labbarcode_dec'] = $labbarcode;
    }

    function getBarcode() {
        $barcode = $this->lab['barcode_dec'];
        if ($barcode != '') {
            return $this->lab['barcode_dec'];
        } else {
            //lookup barcode!!! in haalsi_data -> stored at BS021
            global $survey, $db;
            $query = 'select *, aes_decrypt(answer, "' . $survey->getDataEncryptionKey() . '") as answer from ' . Config::dbSurvey() . '_data where primkey = "' . prepareDatabaseString($this->getPrimkey()) . '" and variablename="bs021"';
            $result = $db->selectQuery($query);
            if ($result != null && $db->getNumberOfRows($result) > 0) {
                $row = $db->getRow($result);
                $barcode = $row['answer'];
                $this->lab['barcode_dec'] = $barcode;
                $this->saveChanges();
                return $barcode;
            } else {//no barcode....
                return '';
            }
        }
    }

    function setRefusal($refusal) {
        $this->lab['refusal'] = $refusal;
    }

    function getRefusal() {
        return $this->lab['refusal'];
    }

    function setRefusalReason($refusalreason) {
        $this->lab['refusalreason'] = $refusalreason;
    }

    function getRefusalReason() {
        return $this->lab['refusalreason'];
    }

    function setRefusalDate($refusaldate) {
        $this->lab['refusaldate'] = $refusaldate;
    }

    function getRefusalDate() {
        return $this->lab['refusaldate'];
    }

    function isRefusal() {
        return $this->getRefusal() == '1';
    }

    function setBarcode($barcode) {
        $this->lab['barcode_dec'] = $barcode;
    }

    function getConsent1() {
        return $this->lab['consent1'];
    }

    function setConsent1($consent1) {
        $this->lab['consent1'] = $consent1;
    }

    function getConsent2() {
        return $this->lab['consent2'];
    }

    function setConsent2($consent2) {
        $this->lab['consent2'] = $consent2;
    }

    function getConsent3() {
        return $this->lab['consent3'];
    }

    function setConsent3($consent3) {
        $this->lab['consent3'] = $consent3;
    }

    function getConsent4() {
        return $this->lab['consent4'];
    }

    function setConsent4($consent4) {
        $this->lab['consent4'] = $consent4;
    }

    function getConsent5() {
        return $this->lab['consent5'];
    }

    function setConsent5($consent5) {
        $this->lab['consent5'] = $consent5;
    }

    function getSurvey() {
        return $this->lab['survey'];
    }

    function setSurvey($survey) {
        $this->lab['survey'] = $survey;
    }

    function getMeasures() {
        return $this->lab['measures'];
    }

    function setMeasures($measures) {
        $this->lab['measures'] = $measures;
    }

    function getVision() {
        return $this->lab['vision'];
    }

    function setVision($vision) {
        $this->lab['vision'] = $vision;
    }

    function getAnthropometrics() {
        return $this->lab['anthropometrics'];
    }

    function setAnthropometrics($vision) {
        $this->lab['anthropometrics'] = $anthropometrics;
    }

    function resetConsent() {
        for ($i = 1; $i < 8; $i++) {
            $this->lab['consent' . $i] = 0;
        }
    }

    function setConsent($index, $value) {
        $this->lab['consent' . $index] = $value;
    }

    function getConsent($index) {
        return $this->lab['consent' . $index];
    }

    function getConsentUrid() {
        return $this->lab['consenturid'];
    }

    function getUrid() {
        return $this->lab['urid'];
    }

    function setUrid($urid) {
        $this->lab['urid'] = $urid;
    }

    function setConsentUrid($consenturid) {
        $this->lab['consenturid'] = $consenturid;
        $this->lab['consentts'] = date('Y-m-d h:i:s');
    }

    function getConsentTs() {
        return $this->lab['consentts'];
    }

    function setFieldDBSShipmentDate($fielddbsshipmentdate) {
        $this->lab['fielddbsshipmentdate'] = $fielddbsshipmentdate;
    }

    function getFieldDBSShipmentDate() {
        return $this->lab['fielddbsshipmentdate'];
    }

    function setFieldDBSReceivedDate($fielddbsreceiveddate) {
        $this->lab['fielddbsreceiveddate'] = $fielddbsreceiveddate;
    }

    function getFieldDBSReceivedDate() {
        return $this->lab['fielddbsreceiveddate'];
    }

    function setFieldDBSReceivedDateFromLab($fielddbsshipmentreturneddate) {
        $this->lab['fielddbsshipmentreturneddate'] = $fielddbsshipmentreturneddate;
    }

    function getFieldDBSReceivedDateFromLab() {
        return $this->lab['fielddbsshipmentreturneddate'];
    }

    function setFieldDBSClinicResultsIssued($fielddbsclinicresultsissueddate) {
        $this->lab['fielddbsclinicresultsissueddate'] = $fielddbsclinicresultsissueddate;
    }

    function getFieldDBSClinicResultsIssued() {
        return $this->lab['fielddbsclinicresultsissueddate'];
    }

    function getFieldDBSStatus() {
        return $this->lab['fielddbsstatus'];
    }

    function setFieldDBSStatus($fielddbsstatus) {
        $this->lab['fielddbsstatus'] = $fielddbsstatus;
    }

    function setFieldDBSCollectedDate($fielddbscollecteddate) {
        $this->lab['fielddbscollecteddate'] = $fielddbscollecteddate;
    }

    function getFieldDBSCollectedDate() {
        return $this->lab['fielddbscollecteddate'];
    }

    function displayFieldDBSStatus() {
        $status = $this->fieldDBSStatus();
        return $status[$this->getFieldDBSStatus()];
    }

    function getPreload($startArray = array()) { //imported into survey from sms/lab
        $preload = $startArray;

        $RgetsStation2 = 0;
        if ($this->getConsent2() == 1 || $this->getConsent3() == 1) {
            $RgetsStation2 = 1;
        }
        $preload['RgetsStation2'] = $RgetsStation2;

        $RgetsStation5a = 0;
        if ($this->getConsent4() == 1 && $this->getConsent5() == 1) {
            $RgetsStation5a = 1;
        }
        $preload['RgetsStation5a'] = $RgetsStation5a;
        return $preload;
    }

    function getLabDBSLocation() {
        return $this->lab['labdbslocation'];
    }

    function setLabDBSLocation($labdbslocation) {
        return $this->lab['labdbslocation'] = $labdbslocation;
    }

    function getLabBloodLocation() {
        return $this->lab['labbloodlocation'];
    }

    function setLabBloodLocation($labbloodlocation) {
        return $this->lab['labbloodlocation'] = $labbloodlocation;
    }

    function getLabBloodLocationByIndex($index) {
        $pos = explode('~', $this->getLabBloodLocation());
        return $pos[$index];
    }

    function getLabDBSLocationByIndex($index) {
        $pos = explode('~', $this->getLabDBSLocation());
        return $pos[$index];
    }

    function getLabDBSPosition() {
        return $this->lab['labdbsposition'];
    }

    function setLabDBSPosition($labdbsposition) {
        return $this->lab['labdbsposition'] = $labdbsposition;
    }

    function getLabBloodPosition() {
        return $this->lab['labbloodposition'];
    }

    function getLabDbsLocationAsArray() {
        return explode('~', $this->getLabDbsLocation());
    }

    function getLabBloodLocationAsArray() {
        return explode('~', $this->getLabBloodLocation());
    }

    function setLabBloodPosition($labbloodposition) {
        return $this->lab['labbloodposition'] = $labbloodposition;
    }

    function displayPosition($location) {
        $pop = explode('~', $location);
        if (sizeof($pop) > 0) {
            $returnStr = '';
            $returnStr .= 'Box: ' . $pop[0];
            $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $returnStr .= 'Rack: ' . $pop[1];
            $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $returnStr .= 'Shelve: ' . $pop[2];
            $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
            $returnStr .= 'Freezer: ' . $pop[3];
            return $returnStr;
        }
        return '';
    }

    function getLabVisitTs() {
        return $this->lab['labvisitts'];
    }

    function setLabVisitTs($labvisitts) {
        $this->lab['labvisitts'] = $labvisitts;
    }

    function getLabBloodSentToLab() {
        return $this->lab['labbloodsenttolab'];
    }

    function setLabBloodSentToLab($labbloodsenttolab) {
        $this->lab['labbloodsenttolab'] = $labbloodsenttolab;
    }

    function getLabBloodSentToLabByIndex($index) {
        $pos = explode('~', $this->getLabBloodSentToLab());
        return $pos[$index];
    }

    function setLabBloodNotCollected($labbloodnotcollected) {
        $this->lab['labbloodnotcollected'] = $labbloodnotcollected;
    }

    function getLabBloodNotCollected() {
        return $this->lab['labbloodnotcollected'];
    }

    function getLabBloodNotCollectedByIndex($index) {
        $pos = explode('~', $this->getLabBloodNotCollected());
        return $pos[$index];
    }

    function setRequestForm($requestform) {
        $this->lab['requestform'] = $requestform;
    }

    function getRequestForm() {
        return $this->lab['requestform'];
    }

    function getHIVFinalAnon() {
        return $this->lab['fielddbshivfinalanon'];
    }

    function getCD4res() {
        return $this->lab['cd4res'];
    }

    function setCD4res($cd4res) {
        $this->lab['cd4res'] = $cd4res;
    }

    function getCD4date() {
        return $this->lab['cd4date'];
    }

    function setCD4date($cd4date) {
        $this->lab['cd4date'] = $cd4date;
    }

    function getLabBloodStatus() {
        return $this->lab['labbloodstatus'];
    }

    function setLabBloodStatus($labbloodstatus) {
        $this->lab['labbloodstatus'] = $labbloodstatus;
    }

    function setLabBloodShipmentDate($labbloodshipmentdate) {
        $this->lab['labbloodshipmentdate'] = $labbloodshipmentdate;
    }

    function getLabBloodShipmentDate() {
        return $this->lab['labbloodshipmentdate'];
    }

    function setLabBloodReceivedDateFromLab($labbloodshipmentreturneddate) {
        $this->lab['labbloodshipmentreturneddate'] = $labbloodshipmentreturneddate;
    }

    function getLabBloodReceivedDateFromLab() {
        return $this->lab['labbloodshipmentreturneddate'];
    }

    function saveChanges() {
        global $db;
        $query = 'UPDATE ' . Config::dbSurveyData() . '_lab SET ';
        $query .= 'barcode = aes_encrypt("' . prepareDatabaseString($this->getBarcode()) . '", "' . Config::labKey() . '"), ';
        $query .= 'labbarcode = aes_encrypt("' . prepareDatabaseString($this->getLabBarcode()) . '", "' . Config::labKey() . '"), ';
        $query .= 'consent1 = "' . prepareDatabaseString($this->getConsent1()) . '",';
        $query .= 'consent2 = "' . prepareDatabaseString($this->getConsent2()) . '", ';
        $query .= 'consent3 = "' . prepareDatabaseString($this->getConsent3()) . '", ';
        $query .= 'consent4 = "' . prepareDatabaseString($this->getConsent4()) . '", ';
        $query .= 'consent5 = "' . prepareDatabaseString($this->getConsent5()) . '",  ';

        $query .= 'refusal = "' . prepareDatabaseString($this->getRefusal()) . '",  ';
        $query .= 'refusalreason = "' . prepareDatabaseString($this->getRefusalReason()) . '",  ';
        $query .= 'refusaldate = "' . prepareDatabaseString($this->getRefusalDate()) . '",  ';

        $query .= 'cd4res = "' . prepareDatabaseString($this->getCD4res()) . '", ';
        $query .= 'cd4date = "' . prepareDatabaseString($this->getCD4date()) . '", ';


        $query .= 'survey = "' . prepareDatabaseString($this->getSurvey()) . '", ';
        $query .= 'measures = "' . prepareDatabaseString($this->getMeasures()) . '", ';
        $query .= 'vision = "' . prepareDatabaseString($this->getVision()) . '", ';
        $query .= 'anthropometrics = "' . prepareDatabaseString($this->getAnthropometrics()) . '", ';

        $query .= 'requestform = "' . prepareDatabaseString($this->getRequestForm()) . '", ';


        $query .= 'urid = "' . prepareDatabaseString($this->getUrid()) . '", ';

        $query .= 'labvisitts = "' . prepareDatabaseString($this->getLabVisitTs()) . '", ';



        $query .= 'fielddbsshipmentdate = "' . prepareDatabaseString($this->getFieldDBSShipmentDate()) . '", ';
        $query .= 'fielddbsreceiveddate = "' . prepareDatabaseString($this->getFieldDBSReceivedDate()) . '", ';
        $query .= 'fielddbscollecteddate = "' . prepareDatabaseString($this->getFieldDBSCollectedDate()) . '", ';
        $query .= 'fielddbsshipmentreturneddate = "' . prepareDatabaseString($this->getFieldDBSReceivedDateFromLab()) . '", ';
        $query .= 'fielddbsclinicresultsissueddate = "' . prepareDatabaseString($this->getFieldDBSClinicResultsIssued()) . '", ';

        $query .= 'fielddbsstatus =  "' . prepareDatabaseString($this->getFieldDBSStatus()) . '", ';

        $query .= 'labdbslocation =  "' . prepareDatabaseString($this->getLabDBSLocation()) . '", ';
        $query .= 'labdbsposition =  "' . prepareDatabaseString($this->getLabDBSPosition()) . '", ';

        $query .= 'labbloodstatus =  "' . prepareDatabaseString($this->getLabBloodStatus()) . '", ';
        $query .= 'labbloodshipmentdate =  "' . prepareDatabaseString($this->getLabBloodShipmentDate()) . '", ';
        $query .= 'labbloodshipmentreturneddate =  "' . prepareDatabaseString($this->getLabBloodReceivedDateFromLab()) . '", ';


        $query .= 'labbloodlocation =  "' . prepareDatabaseString($this->getLabBloodLocation()) . '", ';
        $query .= 'labbloodposition =  "' . prepareDatabaseString($this->getLabBloodPosition()) . '", ';
        $query .= 'labbloodsenttolab = "' . prepareDatabaseString($this->getLabBloodSentToLab()) . '", ';
        $query .= 'labbloodnotcollected = "' . prepareDatabaseString($this->getLabBloodNotCollected()) . '", ';


        $query .= 'consenturid = "' . prepareDatabaseString($this->getConsentUrid()) . '", ';
        $query .= 'consentts = "' . prepareDatabaseString($this->getConsentTs()) . '" ';

        $query .= 'WHERE primkey = "' . prepareDatabaseString($this->getPrimkey()) . '"';

        $db->executeQuery($query);
    }

}

?>