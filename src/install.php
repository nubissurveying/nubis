<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once("language/languagebase.php");
require_once("language/language_en.php");

class Install {

    function __construct($page = '') {
        echo $this->mainPage($page);
        doExit();
    }

    function mainPage($page = "", $message = "") {
        $returnStr = $this->showHeader(Language::headerInstallTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= "<div class='container'>";
        if (is_writable('conf.php') == false) {
            $returnStr .= '<div class="alert alert-danger">' . Language::installWarning() . '</div>';
        } else {
            $returnStr .= $message;
            $returnStr .= $this->getContent($page);
        }
        $returnStr .= "</div></div>";
        $returnStr .= "</div>";
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function getContent($page, $message = '') {
        switch ($page) {
            case "setupRes":
                return $this->showSetupRes();
                break;
            case "setup":
                return $this->showSetup($message);
                break;
            case "finish":
                return $this->finish($message);
                break;
            default:
                return $this->showSetup();
        }
    }

    function showSetup($message = '') {
        $returnStr = $message;
        $returnStr .= $this->displayComboBox();
        $zones = $this->getTimezones();
        $returnStr .= '<form method="post"><div style="margin-top: 20px;">
                        <input type="hidden" name="p" value="setupRes" />
                        <input type="hidden" name="se" value="2" />
  <!-- Nav tabs -->
  <ul id="myTabs" class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">' . Language::installLabelWelcome() . '</a></li>
    <li role="presentation"><a href="#database" aria-controls="database" role="tab" data-toggle="tab">' . Language::installLabelDatabase() . '</a></li>
    <li role="presentation"><a href="#datetime" aria-controls="datetime" role="tab" data-toggle="tab">' . Language::installLabelDateTime() . '</a></li>
    <li role="presentation"><a href="#encryption" aria-controls="encryption" role="tab" data-toggle="tab">' . Language::installLabelEncryption() . '</a></li>
    <li role="presentation"><a href="#logging" aria-controls="logging" role="tab" data-toggle="tab">' . Language::installLabelLogging() . '</a></li>
    <li role="presentation"><a href="#session" aria-controls="session" role="tab" data-toggle="tab">' . Language::installLabelSession() . '</a></li>    
    <li role="presentation"><a href="#performance" aria-controls="sample" role="tab" data-toggle="tab">' . Language::installLabelPerformance() . '</a></li>    
    <li role="presentation"><a href="#sample" aria-controls="sample" role="tab" data-toggle="tab">' . Language::installLabelSample() . '</a></li>    
  </ul>

  <!-- Tab panes -->
  <div class="tab-content" style="margin-top: 20px;">
    <div role="tabpanel" class="tab-pane active" id="home">' . Language::installWelcome() . '
    <br/><br/>    
    <button onclick="$(\'#myTabs li:eq(1) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>
    </div>
    <div role="tabpanel" class="tab-pane" id="database">
        ' . Language::installDatabaseWelcome() . '
        <br/><br/>
        <table style="width: 400px; max-width: 400px;" class="table table-striped table-bordered">
        <tr>
            <td>' . Language::installLabelDatabaseServer() . '</td><td><input value="' . DATABASE_LOCALHOST . '" type="text" class="form-control" name="databaseserver" /></td>
        </tr> 
        <tr>
            <td>' . Language::installLabelDatabasePort() . '</td><td><input value="' . DATABASE_MYSQL_PORT . '" type="text" class="form-control" name="databaseport" /></td>
        </tr> 
        <tr>
            <td>' . Language::installLabelDatabaseName() . '</td><td><input type="text" class="form-control" name="databasename" /></td>
        </tr>                        
        <tr>
            <td>' . Language::installLabelDatabaseUser() . '</td><td><input type="text" class="form-control" name="databaseuser" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelDatabasePassword() . '</td><td><input type="password" class="form-control" name="databasepassword" /></td>
        </tr>        
        <tr>
            <td>' . Language::installLabelDatabaseSurvey() . '</td><td><input type="text" class="form-control" name="databasetablename" /></td>
        </tr>   
        <tr>
            <td style="min-width: 140px; width: 140px">' . Language::installLabelTimezone() . '</td><td>
                <select class="selectpicker show-tick" name="timezone" />
                ';
        $current = date_default_timezone_get();
        if (!$current) {
            $current = ini_get('date.timezone');
        }
        foreach ($zones as $zone) {
            foreach ($zone as $k => $v) {
                $selected = "";
                if (strtolower($current) == strtolower($k)) {
                    $selected = "SELECTED";
                }
                $returnStr .= '<option ' . $selected . ' value="' . $k . '">' . $k . '</option>';
            }
        }
        $keys = array();
        $keys[1] = $this->generateKey();
        $keys[2] = $this->generateKey();
        $keys[3] = $this->generateKey();
        $keys[4] = $this->generateKey();
        $keys[5] = $this->generateKey();
        $keys[6] = $this->generateKey();
        $keys[7] = $this->generateKey();
        $keys[8] = $this->generateKey();
        $keys[9] = $this->generateKey();
        $keys[10] = $this->generateKey();
        $keys[11] = $this->generateKey();
        $keys[12] = $this->generateKey();
        $keys[13] = $this->generateKey();
        $keys[14] = $this->generateKey();
        $keys[15] = $this->generateKey();
        $keys[16] = $this->generateKey();
        $keys[17] = $this->generateKey();
        $keys[18] = $this->generateKey();
        $keys[19] = $this->generateKey();

        $returnStr .= '
                </select>
            </td>    
        </tr>  
        </table>        
        ' . Language::installDatabaseWelcome2() . '
        <br/>    
        <br/>    
        <button onclick="$(\'#myTabs li:eq(0) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button onclick="$(\'#myTabs li:eq(2) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>
        <button type="submit" class="btn btn-primary">' . Language::installButtonFinish() . '</button>
    </div>
    <div role="tabpanel" class="tab-pane" id="datetime">
    ' . Language::installDateTimeWelcome() . '
        <br/><br/>        
        <table style="width: 400px; max-width: 400px;" class="table table-striped table-bordered">
        <tr><th><nobr/></th><th align=middle>' . Language::installLabelSMS() . '</th><th align=middle>' . Language::installLabelSurvey() . '</th></tr>                    
        <tr>
            <td style="min-width: 140px; width: 140px">' . Language::installLabelTimeformat() . '
            </td>
            <td>                
                <select class="selectpicker show-tick" name="timeformatsms" />
                <option value="true">' . Language::installLabelYes() . '</option>
                <option value="false">' . Language::installLabelNo() . '</option>
                </select>
            </td>
            <td>
                <select class="selectpicker show-tick" name="timeformatsurvey" />
                <option value="true">' . Language::installLabelYes() . '</option>
                <option value="false">' . Language::installLabelNo() . '</option>
                </select>                
            </td>
        </tr>
        <tr>
            <td>' . Language::installLabelTimeUseMinutes() . '</td>
            <td>                
                <select class="selectpicker show-tick" name="timeminutessms" />
                <option value="true">' . Language::installLabelYes() . '</option>
                <option value="false">' . Language::installLabelNo() . '</option>
                </select>
            </td>
            <td>
                <select class="selectpicker show-tick" name="timeminutessurvey" />
                <option value="true">' . Language::installLabelYes() . '</option>
                <option value="false">' . Language::installLabelNo() . '</option>
                </select>                
            </td>
        </tr>  
        <tr>
            <td>' . Language::installLabelTimeUseSeconds() . '</td>
            <td>                
                <select class="selectpicker show-tick" name="timesecondssms" />
                <option value="true">' . Language::installLabelYes() . '</option>
                <option value="false">' . Language::installLabelNo() . '</option>
                </select>
            </td>
            <td>
                <select class="selectpicker show-tick" name="timesecondssurvey" />
                <option value="true">' . Language::installLabelYes() . '</option>
                <option value="false">' . Language::installLabelNo() . '</option>
                </select>                
            </td>
        </tr>  
        </table>        
        <br/>   
        <button onclick="$(\'#myTabs li:eq(1) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button onclick="$(\'#myTabs li:eq(3) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>
    </div>
            
    <div role="tabpanel" class="tab-pane" id="encryption">
    ' . Language::installEncryptionWelcome() . '
        <br/><br/>
        <table style="width: 550px; max-width: 550px;" class="table table-striped table-bordered">
        <tr>
            <td style="width: 250px; max-width: 250px;">' . Language::installLabelEncryptionData() . '</td><td><input value="' . $keys[1] . '" type="text" class="form-control" name="encryptiondata" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionAdminLogin() . '</td><td><input value="' . $keys[2] . '"  type="text" class="form-control" name="encryptionadmin" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionLoginCodes() . '</td><td><input value="' . $keys[3] . '"  type="text" class="form-control" name="encryptionlogincodes" /></td>
        </tr>                                
        <tr>
            <td>' . Language::installLabelEncryptionDirect() . '</td><td><input value="' . $keys[4] . '"  type="text" class="form-control" name="encryptiondirect" /></td>
        </tr>                                
        <tr>
            <td>' . Language::installLabelEncryptionRespondent() . '</td><td><input value="' . $keys[5] . '"  type="text" class="form-control" name="encryptionrespondent" /></td>
        </tr>                                
        <tr>
            <td>' . Language::installLabelEncryptionContactName() . '</td><td><input value="' . $keys[6] . '"  type="text" class="form-control" name="encryptioncontactnames" /></td>                
        </tr>                                
        <tr>
            <td>' . Language::installLabelEncryptionContactRemark() . '</td><td><input value="' . $keys[7] . '"  type="text" class="form-control" name="encryptioncontactremarks" /></td>
        </tr>                                
        <tr>
            <td>' . Language::installLabelEncryptionLab() . '</td><td><input value="' . $keys[8] . '"  type="text" class="form-control" name="encryptionlab" /></td>
        </tr>        
        <tr>
            <td>' . Language::installLabelEncryptionFilePicture() . '</td><td><input value="' . $keys[9] . '"  type="text" class="form-control" name="encryptionfile" /></td>
        </tr> 
        <tr>
            <td>' . Language::installLabelEncryptionParameters() . '</td><td><input value="' . $keys[10] . '"  type="text" class="form-control" name="encryptionparameters" /></td>
        </tr> 
        <tr>
            <td>' . Language::installLabelEncryptionRemark() . '</td><td><input value="' . $keys[11] . '"  type="text" class="form-control" name="encryptionremarks" /></td>
        </tr>                                
        <tr>
            <td>' . Language::installLabelEncryptionCommunicationContent() . '</td><td><input value="' . $keys[12] . '"  type="text" class="form-control" name="encryptioncommunicationcontent" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionCommunicationContent() . '</td><td><input value="' . $keys[13] . '"  type="text" class="form-control" name="encryptioncommunicationcomponent" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionCommunicationAccess() . '</td><td><input value="' . $keys[14] . '"  type="text" class="form-control" name="encryptioncommunicationaccess" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionTester() . '</td><td><input value="' . $keys[15] . '"  type="text" class="form-control" name="encryptiontester" /></td>
        </tr> 
        <tr>
            <td>' . Language::installLabelEncryptionPicture() . '</td><td><input value="' . $keys[16] . '"  type="text" class="form-control" name="encryptionpicture" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionCalendar() . '</td><td><input value="' . $keys[17] . '"  type="text" class="form-control" name="encryptioncalendar" /></td>
        </tr> 
        <tr>
            <td>' . Language::installLabelEncryptionUploadAccess() . '</td><td><input value="' . $keys[18] . '"  type="text" class="form-control" name="encryptionuploadaccess" /></td>
        </tr>
        <tr>
            <td>' . Language::installLabelEncryptionUploadAjax() . '</td><td><input value="' . $keys[19] . '"  type="text" class="form-control" name="encryptionajaxaccess" /></td>
        </tr>
        </table>
        <br/>    
        <button onclick="$(\'#myTabs li:eq(2) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button onclick="$(\'#myTabs li:eq(4) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>    
    </div>
    
    <div role="tabpanel" class="tab-pane" id="logging">
    ' . Language::installLoggingWelcome() . '
        <br/><br/>
        <table style="width: 400px; max-width: 400px;" class="table table-striped table-bordered">
        <tr>
        <td>' . Language::installLabelLoggingActions() . '</td>
        <td>
            <select class="selectpicker show-tick" name="loggingactions" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option value="2">' . Language::installLabelNo() . '</option>
            </select>                
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelLoggingTimings() . '</td>
        <td>
            <select class="selectpicker show-tick" name="loggingtimings" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option value="2">' . Language::installLabelNo() . '</option>
            </select>                
            </td>
        </tr>        
        <td>' . Language::installLabelLoggingParadata() . '</td>
        <td>            
            <select id="paradata" class="selectpicker show-tick" name="loggingparadata" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option value="2">' . Language::installLabelNo() . '</option>
            </select>  
            <script type="text/javascript">
                $("#paradata").change(function() {
                    if ($(this).val() == 2) {
                        $("#tabswitch").hide();
                        $("#mouse").hide();
                    }
                    else {
                        $("#tabswitch").show();
                        $("#mouse").show();
                    }    
                });
            </script>
            </td>
        </tr>
        <tr id="tabswitch">
        <td>' . Language::installLabelLoggingTabSwitch() . '</td>
        <td>
            <select class="selectpicker show-tick" name="loggingtabswitch" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option value="2">' . Language::installLabelNo() . '</option>
            </select>                
            </td>
        </tr>
        <tr>
        <tr id="mouse">
        <td>' . Language::installLabelLoggingParadataMouseMovement() . '</td>
        <td>
            <input value="10000" type="text" class="form-control" name="loggingmouse" />
        </td>
        </tr>
        </table>
        <br/>
        <button onclick="$(\'#myTabs li:eq(3) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button onclick="$(\'#myTabs li:eq(5) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>    
    </div>
        
    <div role="tabpanel" class="tab-pane" id="session">
    ' . Language::installSessionWelcome() . '
        <br/><br/>
        <table style="width: 400px; max-width: 400px;" class="table table-striped table-bordered">
        <tr>
        <td>' . Language::installLabelSessionWarn() . '</td>
        <td>
            <select id="sessionwarn" class="selectpicker show-tick" name="sessionwarn" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>
            </select>  
            <script type="text/javascript">
                $("#sessionwarn").change(function() {
                    if ($(this).val() == 2) {
                        $("#sessionduration").hide();
                        $("#sessionlogout").hide();
                        $("#sessionredirect").hide();
                        $("#sessionping").hide();
                    }
                    else {
                        $("#sessionduration").show();
                        $("#sessionlogout").show();
                        $("#sessionredirect").show();
                        $("#sessionping").show();
                    }    
                });
            </script>
            </td>
        </tr>
        <tr style="display: none" id="sessionduration">
        <td>' . Language::installLabelSessionDuration() . '</td>
        <td>
            <input value="1800"  type="text" class="form-control" name="sessionduration" />                
            </td>
        </tr>        
        <tr style="display: none" id="sessionlogout">
        <td>' . Language::installLabelSessionLogout() . '</td>
        <td>
            <input type="text" class="form-control" name="sessionlogout" />                
            </td>
        </tr>
        <tr style="display: none" id="sessionredirect">
        <td>' . Language::installLabelSessionRedirect() . '</td>
        <td>
            <input type="text" class="form-control" name="sessionredirect" />                
            </td>
        </tr>
        <tr style="display: none" id="sessionping">
        <td>' . Language::installLabelSessionPing() . '</td>
        <td>
            <input value="5000" type="text" class="form-control" name="sessionping" />                
            </td>
        </tr>
        </table> 
        <br/>
        <button onclick="$(\'#myTabs li:eq(4) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button onclick="$(\'#myTabs li:eq(6) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>    
    </div>

    <div role="tabpanel" class="tab-pane" id="performance">
    ' . Language::installPerformanceWelcome() . '
        <br/><br/>
        <table style="width: 400px; max-width: 400px;" class="table table-striped table-bordered">
        <tr>
        <td>' . Language::installLabelPerformanceUseSerialize() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performanceserialize" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceUseLocking() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performancelocking" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceUseDataRecords() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performancerecords" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceUsePreparedQueries() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performancequeries" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceUseState() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performancestate" />
            <option selected value="1">' . Language::installLabelYes() . '</option>
            <option value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceUseTransactions() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performancetransaction" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceUseMinify() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performanceminify" />
            <option selected value="1">' . Language::installLabelYes() . '</option>
            <option value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelPerformanceAccessible() . '</td>
        <td>
            <select class="selectpicker show-tick" name="performanceaccessible" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>            
            </td>
        </tr>
        </table> 
        <br/>
        <button onclick="$(\'#myTabs li:eq(5) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button onclick="$(\'#myTabs li:eq(7) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonNext() . '</button>    
    </div>

    <div role="tabpanel" class="tab-pane" id="sample">
    ' . Language::installSampleWelcome() . '
        <br/><br/>
        <table style="width: 400px; max-width: 400px;" class="table table-striped table-bordered">
        <tr>
        <td>' . Language::installLabelSampleType() . '</td>
        <td>
            <select class="selectpicker show-tick" name="sampletype" />
            <option value="1">' . Language::installLabelHousehold() . '</option>
            <option value="2">' . Language::installLabelRespondent() . '</option>
            </select>              
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelSampleTracking() . '</td>
        <td>
            <select class="selectpicker show-tick" name="sampletracking" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>
            </select>              
            </td>
        </tr>        
        <tr>
        <td>' . Language::installLabelSampleProxyContact() . '</td>
        <td>
            <select class="selectpicker show-tick" name="sampleproxycontact" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>
            </select>              
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelSampleProxyCodes() . '</td>
        <td>
            <select class="selectpicker show-tick" name="sampleproxycode" />
            <option value="1">' . Language::installLabelYes() . '</option>
            <option selected value="2">' . Language::installLabelNo() . '</option>
            </select>              
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelAllowSampleCommunication() . '</td>
        <td>
            <select class="selectpicker show-tick" name="allowsamplecommunication" />
            <option value="1">' . Language::installCommunicationYes() . '</option>
            <option selected value="2">' . Language::installCommunicationNo() . '</option>
            </select>                        
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelAllowUpload() . '</td>
        <td>
            <select class="selectpicker show-tick" name="allowupload" />
            <option value="1">' . Language::installUploadYes() . '</option>
            <option selected value="2">' . Language::installUploadNo() . '</option>
            </select>                        
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelSampleCommunication() . '</td>
        <td>
            <input type="text" class="form-control" name="samplecommunication" />                         
            </td>
        </tr>
        <tr>
        <td>' . Language::installLabelSampleFileLocation() . '</td>
        <td>
            <input type="text" class="form-control" name="samplefilelocation" />                         
            </td>
        </tr>
        </table> 
        <br/>
        <button onclick="$(\'#myTabs li:eq(6) a\').tab(\'show\'); return false;" class="btn btn-default">' . Language::installButtonBack() . '</button>
        <button type="submit" class="btn btn-primary">' . Language::installButtonFinish() . '</button>
    </div>
  </div>  
</div></form>';
        return $returnStr;
    }

    function showSetupRes() {

        /* write conf.php file */
        if (is_writable('conf.php') == false) {
            $message = '<div class="alert alert-danger">' . Language::installWarning() . '</div>';
            return $this->getContent("setup", $message);
        } else {

            /* test db */
            
            $db = new Database();
            if ($db->connect(loadvar("databaseserver"), loadvar("databasename"), loadvar("databaseuser"), loadvar("databasepassword")) == false) { //no connection with DB.. Errormessage! 
                $message = '<div class="alert alert-danger">' . Language::installWarningDatabase() . '</div>';
                return $this->getContent("setup", $message);
            } else {
                $syskey = $this->generateSysadminKey();
                $file = fopen("conf.php", "w");
            $str = '<?php

$configuration = array(
    CONFIGURATION_DATABASE => array(
        CONFIGURATION_DATABASE_SERVER => "' . loadvar("databaseserver") . '",
        CONFIGURATION_DATABASE_PORT => "' . loadvar("databaseport") . '",            
        CONFIGURATION_DATABASE_NAME => "' . loadvar("databasename") . '",
        CONFIGURATION_DATABASE_TYPE => "1",
        CONFIGURATION_DATABASE_USER => "' . loadvar("databaseuser") . '",
        CONFIGURATION_DATABASE_PASSWORD => "' . loadvar("databasepassword") . '",
        CONFIGURATION_DATABASE_SURVEY => "' . loadvar("databasetablename") . '"
    ),
    CONFIGURATION_GENERAL => array(
        CONFIGURATION_GENERAL_STARTUP => "1",
        CONFIGURATION_GENERAL_DEVICE => "1"
    ),
    CONFIGURATION_SAMPLE => array(
        CONFIGURATION_SAMPLE_PANEL => "' . loadvar("sampletype") . '",
        CONFIGURATION_SAMPLE_TRACKING => "' . loadvar("sampletracking") . '",
        CONFIGURATION_SAMPLE_PROXYCODE => "' . loadvar("sampleproxycode") . '",
        CONFIGURATION_SAMPLE_PROXYCONTACT => "' . loadvar("sampleproxycontact") . '",
        CONFIGURATION_SAMPLE_ALLOW_COMMUNICATION => "' . loadvar("allowsamplecommunication") . '",
        CONFIGURATION_SAMPLE_ALLOW_UPLOAD => "' . loadvar("allowupload") . '",
        CONFIGURATION_SAMPLE_COMMUNICATION => "' . loadvar("samplecommunication") . '",
        CONFIGURATION_SAMPLE_FILELOCATION => "' . loadvar("samplefilelocation") . '"
    ),
    CONFIGURATION_ENCRYPTION => array(
        CONFIGURATION_ENCRYPTION_DATA => "' . loadvar("encryptiondata") . '",
        CONFIGURATION_ENCRYPTION_LOGINCODES => "' . loadvar("encryptionlogincodes") . '",
        CONFIGURATION_ENCRYPTION_ADMIN => "' . loadvar("encryptionadmin") . '",
        CONFIGURATION_ENCRYPTION_PERSONAL => "' . loadvar("encryptionrespondent") . '",
        CONFIGURATION_ENCRYPTION_REMARK => "' . loadvar("encryptionremarks") . '",
        CONFIGURATION_ENCRYPTION_CONTACTREMARK => "' . loadvar("encryptioncontactremarks") . '",
        CONFIGURATION_ENCRYPTION_CONTACTNAME => "' . loadvar("encryptioncontactnames") . '",
        CONFIGURATION_ENCRYPTION_ACTION_PARAMS => "' . loadvar("encryptionparameters") . '",
        CONFIGURATION_ENCRYPTION_DIRECT => "' . loadvar("encryptiondirect") . '",
        CONFIGURATION_ENCRYPTION_LAB => "' . loadvar("encryptionlab") . '",
        CONFIGURATION_ENCRYPTION_COMMUNICATION_CONTENT => "' . loadvar("encryptioncommunicationcontent") . '",    
        CONFIGURATION_ENCRYPTION_COMMUNICATION_COMPONENT => "' . loadvar("encryptioncommunicationcomponent") . '",    
        CONFIGURATION_ENCRYPTION_COMMUNICATION_ACCESS => "' . loadvar("encryptioncommunicationaccess") . '",    
        CONFIGURATION_ENCRYPTION_TESTER => "' . loadvar("encryptiontester") . '",    
        CONFIGURATION_ENCRYPTION_PICTURE => "' . loadvar("encryptionpicture") . '",    
        CONFIGURATION_ENCRYPTION_CALENDAR => "' . loadvar("encryptioncalendar") . '",    
        CONFIGURATION_ENCRYPTION_FILE => "' . loadvar("encryptionfile") . '",
        CONFIGURATION_ENCRYPTION_COMMUNICATION_UPLOAD => "' . loadvar("encryptionuploadaccess") . '",    
        CONFIGURATION_ENCRYPTION_COMMUNICATION_AJAX => "' . loadvar("encryptionajaxaccess") . '",    
        CONFIGURATION_ENCRYPTION_SYSADMIN => "' . $syskey . '"
    ),
    CONFIGURATION_DATETIME => array(
        CONFIGURATION_DATETIME_TIMEZONE => "' . loadvar("timezone") . '",
        CONFIGURATION_DATETIME_USFORMAT_SMS => "' . loadvar('timeformatsms') . '",
        CONFIGURATION_DATETIME_USFORMAT_SURVEY => "' . loadvar('timeformatsurvey') . '",
        CONFIGURATION_DATETIME_MINUTES_SMS => "' . loadvar('timeminutessms') . '",
        CONFIGURATION_DATETIME_MINUTES_SURVEY => "' . loadvar('timeminutessurvey') . '",
        CONFIGURATION_DATETIME_SECONDS_SMS => "' . loadvar('timesecondssms') . '",
        CONFIGURATION_DATETIME_SECONDS_SURVEY => "' . loadvar('timesecondssurvey') . '"
    ),
    CONFIGURATION_LOGGING => array(
        CONFIGURATION_LOGGING_TIMINGS => "' . loadvar('loggingtimings') . '",
        CONFIGURATION_LOGGING_PARAMS => "' . loadvar('loggingactions') . '",
        CONFIGURATION_LOGGING_ACTIONS => "' . loadvar('loggingactions') . '",
        CONFIGURATION_LOGGING_PARADATA => "' . loadvar('loggingparadata') . '",
        CONFIGURATION_LOGGING_TABSWITCH => "' . loadvar('loggingtabswitch') . '",
        CONFIGURATION_LOGGING_MOUSE => "' . loadvar('loggingmouse') . '"
    ),
    CONFIGURATION_SESSION => array(
        CONFIGURATION_SESSION_WARN => "' . loadvar('sessionwarn') . '",
        CONFIGURATION_SESSION_TIMEOUT => "' . loadvar('sessionduration') . '",
        CONFIGURATION_SESSION_LOGOUT => "' . loadvar('sessionlogout') . '",
        CONFIGURATION_SESSION_REDIRECT => "' . loadvar('sessionredirect') . '",
        CONFIGURATION_SESSION_PING => "' . loadvar('sessionping') . '"
    ),
    CONFIGURATION_PERFORMANCE => array(
        CONFIGURATION_PERFORMANCE_DATA_FROM_STATE => "' . loadvar('performancestate') . '",
        CONFIGURATION_PERFORMANCE_PREPARE_QUERIES => "' . loadvar('performancequeries') . '",
        CONFIGURATION_PERFORMANCE_UNSERIALIZE => "' . loadvar('performanceserialize') . '",
        CONFIGURATION_PERFORMANCE_USE_DATARECORDS => "' . loadvar('performancerecords') . '",
        CONFIGURATION_PERFORMANCE_USE_DYNAMIC_MINIFY => "' . loadvar('performanceminify') . '",
        CONFIGURATION_PERFORMANCE_USE_LOCKING => "' . loadvar('performancelocking') . '",
        CONFIGURATION_PERFORMANCE_USE_ACCESSIBLE => "' . loadvar('performanceaccessible') . '",
        CONFIGURATION_PERFORMANCE_USE_TRANSACTIONS => "' . loadvar('performancetransaction') . '"
    )
);
?>';
            fwrite($file, $str);
            fclose($file);

                // create tables
                $fr = file_get_contents(dirname(__FILE__) . "/admin/sql/createtables.sql");
                $str = str_replace("survey1", prepareDatabaseString(loadvar("databasetablename")), $fr);
                $db->executeQueries($str);

                $query = "REPLACE INTO `" . prepareDatabaseString(loadvar("databasetablename")) . "_users` (`urid`, `status`, `name`, `username`, `password`, `usertype`, usersubtype, `sup`, `filter`, `regionfilter`, `testmode`, `communication`, `settings`, `access`, `lastdata`, `ts`) VALUES
                (1, 1, 'Sysadmin', 'sysadmin', aes_encrypt('sysadmin','" . prepareDatabaseString(loadvar("encryptionadmin")) . "'), 4, 1, NULL, 1, 0, 0, 2, 0x613a313a7b733a31303a226e6176696e6272656164223b733a313a2231223b7d, NULL, NULL, '2014-04-12 00:20:49');";
                $db->executeQuery($query);
                return $this->getContent("finish", $syskey);
            }
        }
    }

    function finish($syskey) {
        $returnStr = '<br/><div class="alert alert-success">' . Language::installConfirmation($syskey) . '</div><br/><br/>';
        $returnStr .= "<form method=post>"; 
        $returnStr .= "<input type=hidden name=" . POST_PARAM_FULLRESET . " value='1' />";
        $returnStr .= "<input type=hidden name=" . POST_PARAM_SE . " value='" . USCIC_SMS . "' />";
        $returnStr .= '<button type="submit" class="btn btn-primary">' . Language::installButtonNext() . '</button>';
        $returnStr .= "</form>";
        return $returnStr;
    }

    function showHeader($title, $style = '', $fastload = false) {
        /* FOR NO CACHING
         * <meta http-equiv="cache-control" content="max-age=0" />
          <meta http-equiv="cache-control" content="no-cache" />
          <meta http-equiv="expires" content="0" />
          <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
          <meta http-equiv="pragma" content="no-cache" />
         */
        $returnStr = ' 
<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <link rel="shortcut icon" href="images/favicon.ico">

    <title>' . $title . '</title>';

        $returnStr .= '
    <!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="bootstrap/dist/css/bootstrap.min.css">

    <!-- Custom scripts and styles for this template -->';

        if ($fastload == false) {
            $returnStr .= '<script type="text/javascript" charset="utf-8" language="javascript" src="bootstrap/assets/js/jquery.js"></script>';
        }
        $returnStr .= '<link href="css/uscicadmin.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet"> 
                  ';
        $returnStr .= '
    ' . $style . '

<script type="text/javascript">
    if(typeof window.history.pushState == \'function\') {
        window.history.pushState({}, "Hide", "index.php");
    }    
</script>
      
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="bootstrap/assets/js/html5shiv.js"></script>
      <script src="bootstrap/assets/js/respond.min.js"></script>
    <![endif]-->
    
    <script src="js/hover-dropdown.js"></script>
    <script type="text/javascript" src="js/tooltip.js"></script>
    <script type="text/javascript" src="js/popover.js"></script>    
    <script type="text/javascript" src="js/modal.js"></script>
    ';

        $returnStr .= '</head>
                    <body>
                    ';
        return $returnStr;
    }

    function showFooter($fastLoad = true, $extra = '') {
        $returnStr = '
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->';
        if ($fastLoad) {
            $returnStr .= '<script src="bootstrap/assets/js/jquery.js"></script>';
        }
        $returnStr .= '<script src="bootstrap/dist/js/bootstrap.min.js"></script>';
        $returnStr .= $extra;
        $returnStr .= '</body></html>';
        return $returnStr;
    }

    function showBottomBar() {
        return '</div>
    <div id="footer">
      <div class="container">
        <p class="text-muted credit">' . Language::nubisFooter() . '</p>
      </div>
    </div>
';
    }

    public function showNavBar() {
        $returnStr = '
      <!-- Fixed navbar -->
      <div id="mainnavbar" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand">' . Language::headerInstallTitle() . '</span>
          </div>
          
        </div>
      </div>
';
        $returnStr .= "<div id='content'>";

        return $returnStr;
    }

    function displayComboBox() {
        $str = '';
        if (!isRegisteredScript("js/bootstrap-select/bootstrap-select-min.js")) {
            registerScript('js/bootstrap-select/bootstrap-select-min.js');
            $str .= getScript("js/bootstrap-select/bootstrap-select-min.js");
        }
        if (!isRegisteredScript("css/bootstrap-select.css")) {
            registerScript('css/bootstrap-select.css');
            $str .= getCSS("css/bootstrap-select.css");
        }
        $str .= '<script type="text/javascript">
                    $(document).ready(function(){
                    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
                      $(".selectpicker").selectpicker({
                            mobile: true,
                            noneSelectedText : \'' . Language::labelDropdownNothing() . '\'}
                        );                      
                      }
                    else {
                      $(".selectpicker").selectpicker({
                            noneSelectedText : \'' . Language::labelDropdownNothing() . '\'}
                        );
                    }
                  });
                  </script>';
        return $str;
    }

    function getTimezones() {
        $zones = timezone_identifiers_list();
        foreach ($zones as $zone) {
            $zone = explode('/', $zone); // 0 => Continent, 1 => City
            // Only use "friendly" continent names
            if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific') {
                if (isset($zone[1]) != '') {
                    $locations[$zone[0]][$zone[0] . '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')
                }
            }
        }
        return $locations;
    }

    function generateKey($length = 32) {
        $str = "";
        $chars = "abcdefghijklmnopqrstuvwxyz123456*&#!*1234567890";
        for ($i = 0; $i < $length; $i++) {
            $x = mt_rand(0, strlen($chars) - 1);
            $str .= $chars[$x];
        }
        return $str;
    }
    
    function generateSysadminKey($length = 16) {
        $str = "";
        $chars = "abcdefghijklmnopqrstuvwxyz1234567890";
        for ($i = 0; $i < $length; $i++) {
            $x = mt_rand(0, strlen($chars) - 1);
            $str .= $chars[$x];
        }
        return $str;
    }

}

?>