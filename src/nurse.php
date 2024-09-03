<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Nurse {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {
        global $logActions;
        if (getFromSessionParams('page') != null) {
            $_SESSION['LASTPAGE'] = getFromSessionParams('page');
        }

        if (isVisionTestNurse(new User($_SESSION['URID']))) {
            return $this->mainPage();  //vision test: only return main page
        }
        $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('page'));
        if (startsWith(getFromSessionParams('page'), 'interviewer.sendreceive')) {
            $interviewer = new Interviewer($this->user);
            return $interviewer->getPage();
        } else {
            if (isset($_SESSION['LASTPAGE'])) {
                switch ($_SESSION['LASTPAGE']) {
                    case 'nurse.respondents.search': return $this->showSearchRes();
                        break;
                    case 'nurse.interviewer.respondent.info': return $this->showRespondentInfo(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.info': return $this->showRespondentInfo(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.consent': return $this->showRespondentConsent(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.consent.res': return $this->showRespondentConsentRes(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.labbarcode': return $this->showRespondentLabBarcode(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.labbarcode.res': return $this->showRespondentLabBarcodeRes(getFromSessionParams('primkey'));
                        break;


                    case 'nurse.respondent.barcode': return $this->showRespondentBarcode(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.barcode.res': return $this->showRespondentBarcodeRes(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.picture': return $this->showRespondentTakePicture(getFromSessionParams('primkey'));
                        break;


                    case 'nurse.respondent.smallbarcodes.print': return $this->showRespondentLabSmallBarcodes(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.trackingsheet.print': return $this->showRespondentPrintTrackingSheet(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.labbarcode.print': return $this->showRespondentReprintLabBarcode(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.uploadfiles': return $this->showRespondentUploadFiles(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.download': return $this->showRespondentDownloadFile(getFromSessionParams('id'));
                        break;
                    case 'nurse.respondent.fielddbs.received': return $this->showRespondentFieldDBSReceived(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.fielddbs.received.fromlab': return $this->showRespondentFieldDBSReceivedFromLab(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.blood.received.fromlab': return $this->showRespondentBloodReceivedFromLab(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.fielddbs.shiptolab': return $this->showRespondentFieldDBSShipToLab();
                        break;
                    case 'nurse.fielddbs.shiptolab.marked': return $this->showRespondentFieldDBSShipToLabMark();
                        break;

                    case 'nurse.backfromsms': return $this->showRespondentBackFromSms(getFromSessionParams('primkey'), getFromSessionParams('suid'));
                        break;
                    case 'nurse.surveycompleted': return $this->showSurveyCompleted(getFromSessionParams('primkey'), getFromSessionParams('suid'));
                        break;


                    case 'nurse.respondent.blood.storage': return $this->showRespondentBloodStorage(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.blood.storage.res': return $this->showRespondentBloodStorageRes(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.dbs.storage': return $this->showRespondentDBSStorage(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.dbs.storage.res': return $this->showRespondentDBSStorageRes(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.requestform': return $this->showRespondentLabRequest(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.requestform.res': return $this->showRespondentLabRequestRes(getFromSessionParams('primkey'));
                        break;


                    case 'nurse.labblood.overview': return $this->showLabBloodOverview();
                        break;
                    case 'nurse.labblood.overview.res': return $this->showLabBloodOverviewRes();
                        break;


                    case 'nurse.labdbs.overview': return $this->showLabDbsOverview();
                        break;
                    case 'nurse.labdbs.overview.res': return $this->showLabDbsOverviewRes();
                        break;


                    case 'nurse.respondent.fielddbsoverview.edit': return $this->ShowFieldDBSChangeDates(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.labblood.tolab': return $this->ShowRespondentBloodSendToLab(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.cd4results': return $this->ShowRespondentCD4(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.cd4results.res': return $this->ShowRespondentCD4Res(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.assigntofieldnurse': return $this->ShowRespondentFieldNurseAssign(getFromSessionParams('primkey'));
                        break;
                    case 'nurse.respondent.assigntofieldnurse.res': return $this->ShowRespondentFieldNurseAssignRes(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.respondent.fieldnurse.info': return $this->ShowRespondentFieldNurseInfo(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.sendreceive.upload': return $this->showSendReceiveUploadData();
                        break;

                    case 'nurse.followup': return $this->showFollowup();
                        break;
                    case 'nurse.followup.info': return $this->ShowFollowupInfo(getFromSessionParams('primkey'));
                        break;

                    case 'nurse.preferences': return $this->showPreferences();
                    case 'nurse.preferencesres': return $this->showPreferencesRes();    

                    default: return $this->mainPage();
                }
            } else {
                $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('nurse.home'));
                return $this->mainPage();
            }
        }
    }

    function mainPage($message = '') {
        $displayNurse = new DisplayNurse();
        return $displayNurse->showMain($message);
    }
    
    function showPreferencesRes() {
        //$this->user->setFilter(loadvar('filter'));
        //$this->user->setRegionFilter(loadvar('region'));
        $this->user->setTestMode(loadvar('testmode'));
        //$this->user->setCommunication(loadvar('communication'));
        //$this->user->setPuid(loadvar('puid'));
        $this->user->saveChanges();
        $display = new Display();
        return $this->mainPage($display->displaySuccess(Language::messagePreferencesSaved()));
    }

    function showPreferences() {
        $displayNurse = new DisplayNurse();
        return $displayNurse->showPreferences($this->user);
    }

    function showSearchRes() {
        $displayNurse = new DisplayNurse();
        if (trim(loadvar('search')) != '') {
            $respondents = new Respondents();
            $respondentsList = $respondents->getRespondentsSearch($this->user, loadvar('search'));
            $respondentsList = array_merge($respondentsList, $respondents->getRespondentsByBarcode($this->user, loadvar('search')));
            if (sizeof($respondentsList) == 1) { //just one found!
                foreach ($respondentsList as $respondent) {
                    return $this->showRespondentInfo($respondent->getPrimkey());
                }
            }
            return $displayNurse->showSearchRes($respondentsList);
        } else {
            $message = $displayNurse->displayError(Language::labelNurseEnterSearchTerm());
            return $this->mainPage($message);
        }
    }

    function showRespondentInfo($primkey, $message = '') {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();        
        if ($respondent->getPrimkey() != '') {
            return $displayNurse->showRespondentInfo($respondent, $message);
        }
        return $displayNurse->showMain($message);
    }

    function showRespondentConsent($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentConsent($respondent);
    }

    function showFieldDBSChangeDates($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        $lab = new Lab($primkey);


        $lab->setFieldDBSCollectedDate(loadvar('fielddbscollected'));
        $lab->setFieldDBSReceivedDate(loadvar('fielddbsreceived'));
        $lab->setFieldDBSShipmentDate(loadvar('fielddbsshipped'));

        $lab->setFieldDBSReceivedDateFromLab(loadvar('fielddbsshipmentreturneddate'));
        $lab->setFieldDBSClinicResultsIssued(loadvar('fielddbsclinicresultsissueddate'));
        $lab->setFieldDBSStatus(loadvar('fielddbsstatus'));

        $lab->saveChanges();

        $message = $displayNurse->displayInfo(Language::labelNurseDBSUpdated());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentConsentRes($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        $lab = new Lab($primkey);
        if (loadvar('refusal') == '1') { //refusal!
            $refusalreason = loadvar('reason');
            $lab->setRefusal(1);
            $lab->setRefusalReason($refusalreason);
            $lab->setRefusalDate(loadvar('refusaldate'));
            for ($i = 1; $i < 6; $i++) {
                $lab->setConsent($i, 0);
            }
        } else {
            $consentList = $_POST['consent'];
            $lab->setRefusal(0);
            $lab->resetConsent();
            foreach ($consentList as $key => $consent) {
                $lab->setConsent($key, $consent);
            }
        }
        if ($lab->getConsentUrid() == 0) {
            $lab->setConsentUrid(loadvar('consenturid'));
        }
        $lab->saveChanges();
        $message = $displayNurse->displayInfo(Language::labelNurseConsentUpdated());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentLabBarcode($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentLabBarcode($respondent);
    }

    function showRespondentLabBarcodeRes($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        $lab = new Lab($primkey);
        $scan1 = loadvar('scan1');
        $scan2 = loadvar('scan2');
        if ($scan1 != $scan2) {
            $message = $displayNurse->displayInfo(Language::labelNurseNoScanMatch());
            return $displayNurse->showRespondentLabBarcode($respondent, $message);
        } elseif ($scan1 == $lab->getBarcode()) {
            $message = $displayNurse->displayInfo(Language::labelNurseIdenticalCodes());
            return $displayNurse->showRespondentLabBarcode($respondent, $message);
        } else {
            $lab->setLabBarcode($scan1);
            if ($lab->getLabVisitTs() == '' || $lab->getLabVisitTs() == null || $lab->getLabVisitTs() == '0000-00-00 00:00:00') {
                $lab->setLabVisitTs(date('Y-m-d H:i:s'));
            }
            $lab->saveChanges();
            $message = $displayNurse->displayInfo(Language::labelNurseBarCodeUpdated());
            return $displayNurse->showRespondentInfo($respondent, $message);
        }
    }

    function showRespondentTakePicture($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentTakePicture($respondent);
    }

    function showRespondentBarcode($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentBarcode($respondent);
    }

    function showRespondentBarcodeRes($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        $lab = new Lab($primkey);
        $scan1 = loadvar('scan1');
        $scan2 = loadvar('scan2');
        if ($scan1 != $scan2) {
            $message = $displayNurse->displayInfo(Language::labelNurseNoScanMatch());
            return $displayNurse->showRespondentLabBarcode($respondent, $message);
        } elseif ($scan1 == $lab->getLabBarcode()) {
            $message = $displayNurse->displayInfo(Language::labelNurseIdenticalCodes());
            return $displayNurse->showRespondentLabBarcode($respondent, $message);
        } else {
            $lab->setBarcode($scan1);
            $lab->saveChanges();
            $message = $displayNurse->displayInfo(Language::labelNurseFieldBarCodeUpdated());
            return $displayNurse->showRespondentInfo($respondent, $message);
        }
    }

    function showRespondentLabSmallBarcodes($primkey) {
        $lab = new Lab($primkey);
        $labBarcode = $lab->getLabBarcode();
        ob_clean();
        header('Location: lab/barcode/phpqrcode/index.php?number=' . $labBarcode);

        exit;
    }

    function showRespondentReprintLabBarcode($primkey) {
        $lab = new Lab($primkey);

        ob_clean();
        echo '<html><head><style type="text/css">

@page  
{ 
    size: auto;   /* auto is the initial value */ 

    /* this affects the margin in the printer settings */ 
    margin: 19mm 0mm 0mm 0mm;  
} 
body  
{ 
    /* this affects the margin on the content before sending to printer */ 
    margin: 0px;  
} 
  P.breakhere {page-break-before: always}


</style></head><body>';
        echo '<img src=lab/barcode/barcode.php?scale=1&number=' . ($lab->getLabBarcode()) . '>';
        echo '</body></html>';


        exit;
    }

    function showRespondentUploadFiles($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);
        ob_clean();
        $_SESSION[CONFIGURATION_ENCRYPTION_COMMUNICATION_UPLOAD] = encryptC(Config::uploadAccessKey(), Config::smsComponentKey()); // set key for access
        require_once('lab/upload/index.php');
        echo uploadFile($respondent->getPrimkey(), $lab->getLabBarcode());
        exit;
    }

    function showRespondentBloodStorage($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentBloodStorageLocation($respondent);
    }

    function showRespondentDBSStorage($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentDBSStorageLocation($respondent);
    }

    function showRespondentBloodStorageRes($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);

        $lab->setLabBloodPosition(loadvar('stp'));
        $lab->setLabBloodLocation(loadvar('stb') . '~' . loadvar('str') . '~' . loadvar('sts') . '~' . loadvar('stf'));
        $lab->saveChanges();

        $displayNurse = new displayNurse();
        $message = $displayNurse->displayInfo(Language::labelNurseBloodLocationUpdated());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentDBSStorageRes($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);

        $lab->setLabDBSPosition(loadvar('stp'));
        $lab->setLabDBSLocation(loadvar('stb') . '~' . loadvar('str') . '~' . loadvar('sts') . '~' . loadvar('stf'));
        $lab->saveChanges();

        $displayNurse = new displayNurse();
        $message = $displayNurse->displayInfo(Language::labelNurseDBSLocationUpdated());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentDownloadFile($id) {
        global $db;
        $user = new User($_SESSION['URID']);
        //CHECK ON USER!!!!
        $query = 'select *, AES_DECRYPT(content, "' . Config::filePictureKey() . '") as content from ' . Config::dbSurveyData() . '_files where id="' . prepareDatabaseString($id) . '"';
        $result = $db->selectQuery($query);
        if ($result != null) {
            ob_clean();
            $row = $db->getRow($result);
            ob_clean();
            header('Content-type: image/png');
            print($row['content']);
            exit;
        }
        echo Language::labelNurseErrorFileDownload();
    }

    function showRespondentFieldDBSShipToLabMark() {
        global $db;
        $query = 'update ' . Config::dbSurveyData() . '_lab set fielddbsstatus = 2, fielddbsshipmentdate="' . prepareDatabaseString(date('Y-m-d')) . '" where fielddbsstatus = 1';
        $result = $db->selectQuery($query);

        $display = new Display();
        $message = $display->displayInfo(Language::labelNurseCardsSent());
        return $this->mainPage($message);
    }

    function showRespondentFieldDBSShipToLab() {
        /*        $displayNurse = new DisplayNurse();
          return $displayNurse->showFieldDBS(); */

        ob_clean();


        global $db;

        echo $this->parseTextTrackingSheet(file_get_contents('documentation/DBS To Lab/Header.html'), null, null);

        $returnStr = '';

        $query = 'select primkey from ' . Config::dbSurveyData() . '_lab where fielddbsstatus = 1';
        $result = $db->selectQuery($query);
        $i = 1;
        if ($result != null) {
            while ($row = $db->getRow($result)) {
                $lab = new Lab($row['primkey']);


                $barcode = 'lab/barcode/barcodegen/html/image.php?filetype=PNG&dpi=72&scale=1&rotation=0&font_family=Arial.ttf&font_size=11&text=' . $lab->getBarCode() . '&thickness=20&start=B&code=BCGcode128';

                $returnStr .= '<tr style="height:100px"><td align=center>' . $i . '</td><td align=center><img src=' . $barcode . '></td><td colspan=2 align=center>' . $lab->getFieldDBSCollectedDate() . '</td>';

                $returnStr .= '<td colspan=3></td><td colspan=5></td><td colspan=3></td></tr>';


                $i++;
            }
        }
        echo $returnStr;

        echo $this->parseTextTrackingSheet(file_get_contents('documentation/DBS To Lab/Footer.html'), null, null);


        exit;
    }

    function showRespondentFieldDBSReceived($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);


        $lab->setFieldDBSCollectedDate(loadvar('dbsdate'));
        $lab->setFieldDBSReceivedDate(date('Y-m-d'));
        $lab->setFieldDBSStatus(1);

        $lab->saveChanges();
        $displayNurse = new DisplayNurse();
        $message = $displayNurse->displayInfo(Language::labelNurseDBSCollectionDateAdded());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentFieldDBSReceivedFromLab($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);

        $lab->setFieldDBSReceivedDateFromLab(loadvar('dbsdate'));
        $lab->setFieldDBSStatus(3);
        $lab->saveChanges();

        $displayNurse = new DisplayNurse();
        $message = $displayNurse->displayInfo(Language::labelNurseDBSLabDateAdded());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentBloodReceivedFromLab($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);

        $lab->setLabBloodStatusReceivedDateFromLab(loadvar('dbsdate'));
        $lab->setLabBloodStatusStatus(3);
        $lab->saveChanges();

        $displayNurse = new DisplayNurse();
        $message = $displayNurse->displayInfo(Language::labelNurseBloodLabDateAdded());
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function showRespondentPrintTrackingSheet($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($respondent->getPrimkey());

        ob_clean();

        echo $this->parseTextTrackingSheet(file_get_contents('documentation/Tracking Sheet/Header.html'), $respondent, $lab);

        $labstations = Language::labStations();
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['1']['location']), $respondent, $lab);
        if ($lab->getConsent2() == 1 || $lab->getConsent3() == 1) {
            echo $this->parseTextTrackingSheet(file_get_contents($labstations['2']['location']), $respondent, $lab);
        } else {
            echo $this->parseTextTrackingSheet(file_get_contents($labstations['2']['nolocation']), $respondent, $lab);
        }
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['3']['location']), $respondent, $lab);
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['4']['location']), $respondent, $lab);

//consent 4/5: cognition
        if ($lab->getConsent4() == 1 && $lab->getConsent5() == 1) {
            echo $this->parseTextTrackingSheet(file_get_contents($labstations['5a']['location']), $respondent, $lab);
        } else {
            echo $this->parseTextTrackingSheet(file_get_contents($labstations['5a']['nolocation']), $respondent, $lab);
        }

        echo $this->parseTextTrackingSheet(file_get_contents($labstations['5b']['location']), $respondent, $lab);
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['6']['location']), $respondent, $lab);
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['7']['location']), $respondent, $lab);
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['8a']['location']), $respondent, $lab);
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['8b']['location']), $respondent, $lab);
        echo $this->parseTextTrackingSheet(file_get_contents($labstations['9']['location']), $respondent, $lab);


        echo '</body></html>';
        exit;
    }

    function parseTextTrackingSheet($text, $respondent, $lab) {

        $text = str_replace("%date%", date('Y-m-d H:i:s'), $text);

        if ($respondent != null) {
            $text = str_replace("%firstname%", $respondent->getFirstName() . ' ' . $respondent->getLastName(), $text);
        }
        if ($lab != null) {
            $labbarcode = $lab->getLabBarcode();
            $labbarcode = '<img src=lab/barcode/c128/html/image.php?filetype=PNG&dpi=72&scale=1&rotation=0&font_family=Arial.ttf&font_size=8&text=' . $labbarcode . '&thickness=20&start=B&code=BCGcode128>';
            $text = str_replace("%labbarcode%", $labbarcode, $text);
        }

        $text = str_replace("\A0", "", $text);

        return $text;
    }

    function showRespondentBackFromSms($primkey, $urid) {
        $respondent = new Respondent($primkey);      
        if ($respondent->getPrimkey() != '') {
            return $this->showRespondentInfo($primkey);
        }
        return $this->mainPage();
    }

    function showSurveyCompleted($primkey, $suid = 1) {
        $lab = new Lab($primkey);
        if ($suid == 3) { //survey
            $lab->setSurvey(2);
        }
        if ($suid == 4) { //data entry
            $lab->setMeasures(2);
        }
        if ($suid == 5) { //vision
            $lab->setVision(2);
        }
        if ($suid == 6) { //antro
            $lab->setAnthropometrics(2);
        }


        $lab->saveChanges();
        //end add contact
        $display = new Display();

        if (isFieldNurse(new User($_SESSION['URID']))) {
            return $this->ShowRespondentFieldNurseInfo($primkey, $display->displaySuccess(Language::labelNurseSurveyCompleted())); //field nurse
        } else {
            return $this->showRespondentInfo($primkey, $display->displaySuccess(Language::labelNurseSurveyCompleted()));
        }
    }

    function showRespondentLabRequest($primkey) {
        $respondent = new Respondent($primkey);

        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentLabRequest($respondent);
    }

    function showRespondentLabRequestRes($primkey) {
        $lab = new Lab($primkey);
        $storage = $_POST;
//       unset($storage['r']);
        unset($storage['r']);
        $lab->setRequestForm(json_encode($storage));

        $lab->saveChanges();
        //end add contact
        $display = new Display();
        return $this->showRespondentInfo($primkey, $display->displaySuccess(Language::labelNurseRequestFormCompleted()));
    }

    function showLabDbsOverview() {
        $displayNurse = new DisplayNurse();
        return $displayNurse->showLabDbsOverview();
    }

    function showLabDbsOverviewRes() {
        ob_clean();

        $box = loadvar('stb');
        $rack = loadvar('str');
        $shelf = loadvar('sts');
        $freezer = loadvar('stf');

        $atPosition = array();

        $lab = new Lab(null);
        $tests = $lab->getBloodTests();
        global $db;
        $query = 'select labdbsposition, labvisitts, aes_decrypt(labbarcode, "' . Config::labKey() . '") as labbarcode from ' . Config::dbSurveyData() . '_lab where labdbslocation = "' . prepareDatabaseString($box . '~' . $rack . '~' . $shelf . '~' . $freezer) . '"';
        $result = $db->selectQuery($query);
        if ($result != null) {
            while ($row = $db->getRow($result)) {
                $start = $row['labdbsposition'];
                // $senttolab = explode('~', $row['labbloodsenttolab']);
                // foreach($tests as $key=>$test){
                $atPosition[$start][0] = $row['labbarcode'];
                //        $atPosition[$start][1] = $row['labbarcode'] . ':' . sprintf("%0" . 2 . "d", $key);
                $atPosition[$start][2] = substr($row['labvisitts'], 0, 10); //date
                //      $atPosition[$start][3] = $test[1]; //ML
                //     $atPosition[$start][4] = $test[0]; //ML*/
                //  $atPosition[$start][5] = ''; //sent to lab
                //  if ($senttolab[$key] != ''){
                //    $atPosition[$start][5] = $senttolab[$key]; //'aaaa'; //sent to lab
                //  }
                // $start++;
                //}
            }
        }


        echo '<html><body>';
        echo '<table border=0 width=900px ><tr><td valign=top>';
        echo '<table>';
        echo '<tr><td width=550px><b>HAALSI Container Report DBS</b></td><td width=140px>DBS (50) Box</td><td align=right>Legend</td></tr>';
        echo '<tr><td><b>FREEZER ' . $freezer . '/SHELF ' . $shelf . '/RACK ' . $rack . '/BOX' . $box . '</td><td align=right colspan=2><nobr>Created on ' . date('Y-m-d H:i:s') . '</td></tr>';
        echo '</table>';
        echo '</td><td align=right width=120px>';
        echo '<table style="height:55px;" width=100% border=1 cellspacing=0 cellpadding=0><tr><td><table border=0 width=100%>';

        echo '<tr><td><font style="font-size:9px">barcode</font></td><td align=right><font style="font-size:9px">spec barcode</font></td></tr>';
        echo '<tr><td><font style="font-size:9px">harv date</font></td><td align=right><font style="font-size:9px">&nbsp;</font></td></tr>';
        echo '</table></td></tr></table>';

        echo '</td></tr>';
        echo '</table>';
        $i = 1;
        echo '<table width=900px border=1 cellspacing=5 cellpadding=0>';
        for ($row = 1; $row <= 50; $row++) {
            echo '<tr>';
            echo '<td width=10% style="height:55px">';
            echo '<table style="height:55px" width=100% border=0 cellspacing=0 cellpadding=0>';
            if ($atPosition[$i][5] != '') {
                echo '<tr><td align=center valign=center><font style="font-weight: bold; font-size:32px; color:red">X</font></td></tr><tr><td><font style="font-size:9px">' . $atPosition[$i][5] . '</font></td></tr>';
            } else {
                echo '<tr style="height:12px"><td><font style="font-size:9px">';
                if (isset($atPosition[$i])) {
                    echo $atPosition[$i][0];
                }
                echo '</td><td align=right><font style="font-size:9px">';
                if (isset($atPosition[$i])) {
                    echo $atPosition[$i][1];
                }
                echo '</font></td></tr>';
                echo '<tr style="height:12px"><td><font style="font-size:9px">';
                if (isset($atPosition[$i])) {
                    echo $atPosition[$i][4];
                }
                echo '</font></td><td align=right><font style="font-size:9px">&nbsp;</font></td></tr>';

                echo '<tr style="height:12px"><td><font style="font-size:9px">';
                if (isset($atPosition[$i])) {
                    echo $atPosition[$i][2];
                }
                echo '</font></td><td align=right><font style="font-size:9px">&nbsp;</font></td></tr>';
                echo '<tr style="height:12px"><td><font style="font-size:9px">';
                if (isset($atPosition[$i])) {
                    echo $atPosition[$i][3];
                }
                echo '</font></td><td align=right><font style="font-size:9px">' . sprintf("%0" . 3 . "d", $i) . '</font></td></tr>';
            }
            echo '</table>';
            echo '</td>';
            $i++;
            echo '</tr>';
        }
        echo '</table>';

        echo '</body></html>';
        exit;
    }

    function showLabBloodOverview() {
        $displayNurse = new DisplayNurse();
        return $displayNurse->showLabBloodOverview();
    }

    function showLabBloodOverviewRes() {
        ob_clean();

        $box = loadvar('stb');
        $rack = loadvar('str');
        $shelf = loadvar('sts');
        $freezer = loadvar('stf');

        $atPosition = array();

        $lab = new Lab(null);
        $tests = $lab->getBloodTests();
        global $db;
        $query = 'select labbloodposition, labbloodsenttolab, labbloodnotcollected, labvisitts, aes_decrypt(labbarcode, "' . Config::labKey() . '") as labbarcode from ' . Config::dbSurveyData() . '_lab where labbloodlocation = "' . prepareDatabaseString($box . '~' . $rack . '~' . $shelf . '~' . $freezer) . '"';
        $result = $db->selectQuery($query);
        if ($result != null) {
            while ($row = $db->getRow($result)) {
                $start = $row['labbloodposition'];
                $senttolab = explode('~', $row['labbloodsenttolab']);
                $notcollected = explode('~', $row['labbloodnotcollected']);
                foreach ($tests as $key => $test) {
                    $atPosition[$start][0] = $row['labbarcode'];
                    $atPosition[$start][1] = $row['labbarcode'] . ':' . sprintf("%0" . 2 . "d", $key);
                    $atPosition[$start][2] = substr($row['labvisitts'], 0, 10); //date
                    $atPosition[$start][3] = $test[1]; //ML
                    $atPosition[$start][4] = $test[0]; //ML
                    $atPosition[$start][5] = ''; //sent to lab
                    $atPosition[$start][6] = ''; //not collected
                    if ($senttolab[$key] != '') {
                        $atPosition[$start][5] = $senttolab[$key]; //'aaaa'; //sent to lab
                    }
                    if ($notcollected[$key] != '') {
                        $atPosition[$start][6] = $notcollected[$key]; //'aaaa'; //sent to lab
                    }
                    $start++;
                }
            }
        }


        echo '<html><body>';
        echo '<table border=0 width=900px ><tr><td valign=top>';
        echo '<table>';
        echo '<tr><td width=550px><b>HAALSI Container Report</b></td><td width=140px>10 X 10 Box</td><td align=right>Legend</td></tr>';
        echo '<tr><td><b>FREEZER ' . $freezer . '/SHELF ' . $shelf . '/RACK ' . $rack . '/BOX' . $box . '</td><td align=right colspan=2><nobr>Created on ' . date('Y-m-d H:i:s') . '</td></tr>';
        echo '</table>';
        echo '</td><td align=right width=120px>';
        echo '<table style="height:55px;" width=100% border=1 cellspacing=0 cellpadding=0><tr><td><table border=0 width=100%>';

        echo '<tr><td><font style="font-size:9px">barcode</font></td><td align=right><font style="font-size:9px">spec barcode</font></td></tr>';
        echo '<tr><td><font style="font-size:9px">type</font></td><td align=right><font style="font-size:9px">&nbsp;</font></td></tr>';
        echo '<tr><td><font style="font-size:9px">harv date</font></td><td align=right><font style="font-size:9px">&nbsp;</font></td></tr>';
        echo '<tr><td><font style="font-size:9px">volume</font></td><td align=right><font style="font-size:9px">coord</font></td></tr>';
        echo '</table></td></tr></table>';

        echo '</td></tr>';
        echo '</table>';
        $i = 1;
        echo '<table width=900px border=1 cellspacing=5 cellpadding=0>';
        for ($row = 1; $row <= 10; $row++) {
            echo '<tr>';
            for ($col = 1; $col <= 10; $col++) {
                echo '<td width=10% style="height:55px">';

                echo '<table style="height:55px" width=100% border=0 cellspacing=0 cellpadding=0>';

                if ($atPosition[$i][6] != '') { //not collected
                    echo '<tr><td align=center valign=center><font style="font-weight: bold; font-size:32px; color:blue">N</font></td></tr><tr><td><font style="font-size:9px">' . $atPosition[$i][5] . '</font></td></tr>';
                } elseif ($atPosition[$i][5] != '') {  //send to lap
                    echo '<tr><td align=center valign=center><font style="font-weight: bold; font-size:32px; color:red">X</font></td></tr><tr><td><font style="font-size:9px">' . $atPosition[$i][5] . '</font></td></tr>';
                } else {
                    echo '<tr style="height:12px"><td><font style="font-size:9px">';
                    if (isset($atPosition[$i])) {
                        echo $atPosition[$i][0];
                    }
                    echo '</td><td align=right><font style="font-size:9px">';
                    if (isset($atPosition[$i])) {
                        echo $atPosition[$i][1];
                    }
                    echo '</font></td></tr>';
                    echo '<tr style="height:12px"><td colspan=2><font style="font-size:9px">';
                    if (isset($atPosition[$i])) {
                        echo $atPosition[$i][4];
                    }
                    echo '</font></td></tr>';

                    echo '<tr style="height:12px"><td colspan=2><font style="font-size:9px"><nobr>';
                    if (isset($atPosition[$i])) {
                        echo $atPosition[$i][2];
                    }
                    echo '</font></td></tr>';
                    echo '<tr style="height:12px"><td><font style="font-size:9px">';
                    if (isset($atPosition[$i])) {
                        echo $atPosition[$i][3];
                    }
                    echo '</font></td><td align=right><font style="font-size:9px">' . sprintf("%0" . 3 . "d", $i) . '</font></td></tr>';
                }
                echo '</table>';

                echo '</td>';

                $i++;
            }
            echo '</tr>';
        }
        echo '</table>';

        echo '</body></html>';
        exit;
    }

    function ShowRespondentBloodSendToLab($primkey) {
        $respondent = new Respondent($primkey);
        $message = Language::labelNurseSelectVial();
        if (isset($_POST['assignid'])) {
            $lab = new Lab($primkey);
            $assignids = $_POST['assignid'];
            $timeslab = array_fill(0, 24, '');
            $notcollected = array_fill(0, 24, '');

            if ($lab->getLabBloodSentToLab() != '') {  //preload
                $timeslab = explode('~', $lab->getLabBloodSentToLab());
            }
            if ($lab->getLabBloodNotCollected() != '') {
                $notcollected = explode('~', $lab->getLabBloodNotCollected());
            }
            foreach ($assignids as $key => $assignid) {
                $timeslab[$key] = date('Y-m-d');
                $notcollected[$key] = date('Y-m-d');
            }

            if (isset($_POST['notcollected'])) { //not present!
                $message = Language::labelNurseMarkNotCollected();
                $lab->setLabBloodNotCollected(implode('~', $notcollected));
            } else { //send to lab
                $message = Language::labelNurseBloodSentLab();
                $lab->setLabBloodSentToLab(implode('~', $timeslab));
            }
            $lab->saveChanges();
        }



        $displayNurse = new DisplayNurse();
        $message = $displayNurse->displayInfo($message);
        return $displayNurse->showRespondentInfo($respondent, $message);
    }

    function ShowRespondentCD4($primkey) {
        $respondent = new Respondent($primkey);
//      $lab = new Lab($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentCD4($respondent);
    }

    function ShowRespondentCD4Res($primkey) {
        $respondent = new Respondent($primkey);
        $lab = new Lab($primkey);

        $lab->setCD4res(loadvar('cd4res'));
        $lab->setCD4date(loadvar('cd4date'));


        $lab->saveChanges();

        $display = new Display();
        return $this->showRespondentInfo($primkey, $display->displaySuccess(Language::labelNurseCD4Added()));
    }

    function ShowRespondentFieldNurseAssign($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentFieldNurseAssign($respondent);
    }

    function ShowRespondentFieldNurseAssignRes($primkey) {
        $respondent = new Respondent($primkey);
        $household = $respondent->getHousehold();

        $lab = new Lab($primkey);
        $selurid = loadvar('urid');
        $lab->setUrid($selurid);
        $lab->saveChanges();

        //set urid for lab
        //add to communication:   _lab, _household, _respondent
        $communication = new Communication();
        $communication->assignLab($household, $respondent, $lab, $selurid);

        $display = new Display();
        return $this->showRespondentInfo($primkey, $display->displaySuccess(Language::labelNurseFieldNurseAssigned()));
    }

    function ShowRespondentFieldNurseInfo($primkey, $message = '') {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showRespondentFieldNurseInfo($respondent, $message);
    }

    function showSendReceiveUploadData() {
        $displayNurse = new DisplayNurse();
        $communication = new Communication();

        if ($this->user->getLastData() != '' && $this->user->getLastData() != '0000-00-00 00:00:00') {
            $tables = array('data', 'datarecords', 'states', 'times', 'remarks', 'contacts', 'observations');
        } else {
            $tables = array('data', 'datarecords', 'times', 'remarks', 'contacts', 'observations');
        }
        $data = $communication->exportTables($tables, $this->user->getLastData(), 'primkey not like "999%"'); //no test data
//        $data = $communication->exportTables(array('data'), $this->user->getLastData(), 'primkey not like "999%"'); //no test data
//        $data = $communication->exportTables(array('remarks'), $this->user->getLastData(), 'primkey not like "999%"'); //no test data
        //update lab!
        $respondents = new Respondents();
        $respondents = $respondents->getRespondentsByUrid($_SESSION['URID']);
        foreach ($respondents as $respondent) {
            $data = 'UPDATE ' . Config::dbSurveyData() . '_lab set status = ' . prepareDatabaseString($respondent->getStatus()) . ' where primkey = \'' . prepareDatabaseString($respondent->getPrimkey()) . '\'' . ";\n";
        }

        if ($communication->sendToServerAsFile($data, $this->user->getUrid())) { //success sending data to server
            //update lastdate!
            $this->user->setLastData(date('Y-m-d H:i:s'));
            $this->user->saveChanges();
            $message = $displayNurse->displaySuccess(Language::labelDataUploaded());
        } else {
            $message = $displayNurse->displayError(Language::labelDataNotUploaded());
        }

        return $this->mainPage($message);
    }

    function showFollowup($message = '') {
        $displayNurse = new DisplayNurse();
        return $displayNurse->showFollowup($message);
    }

    function ShowFollowupInfo($primkey) {
        $respondent = new Respondent($primkey);
        $displayNurse = new DisplayNurse();
        return $displayNurse->showFollowupInfo($respondent);
    }

}

?>
