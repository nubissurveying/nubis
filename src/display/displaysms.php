<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplaySms extends DisplaySysAdmin {

    function showMain($message = '') {
//        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), 'SMS'), 'label' => 'SMS');
        $headers[] = array('link' => '', 'label' => Language::linkSms());

        $returnStr = $this->showSmsHeader($headers);

        $returnStr .= $message;

        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.sample')) . '" class="list-group-item">' . Language::labelSMSSample() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.communication')) . '" class="list-group-item">' . Language::labelSMSCommunicationTable() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.update')) . '" class="list-group-item">' . Language::labelSMSLaptopUpdate() . '</a>';

        $returnStr .= '</div>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showMetaDataUpdate($message = '') {
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()), 'label' => Language::linkSms());
        $headers[] = array('link' => '', 'label' => Language::labelSMSLaptopUpdate());

        $returnStr = $this->showSmsHeader($headers);
        //CONTENT
        $returnStr .= $message;
        $returnStr .= '<div class="list-group">';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.update.sql')) . '" class="list-group-item">' . Language::labelSMSLaptopUpdateMetaData() . '</a>';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.update.scripts')) . '" class="list-group-item">' . Language::labelSMSLaptopUpdateScripts() . '</a>';
        $returnStr .= '</div>';
        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showMetaDataSQLUpdate($message = '') {

        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), 'SMS'), 'label' => Language::linkSMS());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms.update'), Language::labelSMSLaptopUpdate()), 'label' => Language::labelSMSLaptopUpdate());
        $headers[] = array('link' => '', 'label' => 'Update meta data (SQL)');


        $returnStr = $this->showSmsHeader($headers);
        $returnStr .= $message;
        //CONTENT
        //sysadmin.sms.update.sql.res

        $users = new Users();
        $users = $users->getUsersByType(USER_INTERVIEWER);
        if (sizeof($users) > 0) {

            $returnStr .= $this->displayComboBox();
            $returnStr .= '<form method="post">';
            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.sms.update.sql.res'));
            $returnStr .= '<table>';
            $returnStr .= '<tr><td><label><input name="metadata" type="checkbox" value="1">' . Language::labelSMSLaptopUpdateMetadataSurvey() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name="users" type="checkbox" value="1"> ' . Language::labelSMSLaptopUpdateMetadataUsers() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name="psu" type="checkbox" value="1"> ' . Language::labelSMSLaptopUpdateMetadataPSU() . '</label></td></tr>';
            $returnStr .= '<tr><td><label><input name="custom" type="checkbox" value="1">' . Language::labelSMSLaptopUpdateMetadataCustom() . '</label><br/>';
            $returnStr .= '<textarea name="sqlcode" rows=3 cols=60 class="form-control"></textarea>';
            $returnStr .= '</td></tr>';
            $returnStr .= '</table><br/>';
            $returnStr .= "<select multiple name='iwers[]' class='selectpicker show-tick'>";
            $returnStr .= "<option value='-1'>" . Language::labelSMSLaptopAll() . "</option>";
            foreach ($users as $user) {
                if ($user->getUserType() == USER_INTERVIEWER) {
                    $returnStr .= "<option value='" . $user->getUrid() . "'>" . $user->getName() . "</option>";
                }
            }
            $returnStr .= "</select>";
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::labelSMSLaptopUpdateMetadataButton() . '"/>';

            $returnStr .= '</form>';
        } else {
            $returnStr .= $this->displayInfo(Language::labelSMSLaptopNoInterviewers());
        }
        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showScriptUpdate($message = '') {
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()), 'label' => Language::linkSms());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms.update'), Language::labelSMSLaptopUpdate()), 'label' => Language::labelSMSLaptopUpdate());
        $headers[] = array('link' => '', 'label' => Language::labelSMSLaptopUpdateScripts());

        $returnStr = $this->showSmsHeader($headers);
        $returnStr .= $this->displayComboBox();
        $returnStr .= $message;
        //CONTENT
        //sysadmin.sms.update.sql.res
        $returnStr .= '<form method="post">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.sms.update.scripts.res'));
        $users = new Users();
        $users = $users->getUsersByType(USER_INTERVIEWER);
        if (sizeof($users) > 0) {
            $returnStr .= $this->displayInfo(Language::labelSMSLaptopScriptsMessage());
            $returnStr .= '<br/><br/>';
            $returnStr .= "<select multiple name='iwers[]' class='selectpicker show-tick'>";
            $returnStr .= "<option value='-1'>" . Language::labelSMSLaptopAll() . "</option>";
            foreach ($users as $user) {
                if ($user->getUserType() == USER_INTERVIEWER) {
                    $returnStr .= "<option value='" . $user->getUrid() . "'>" . $user->getName() . "</option>";
                }
            }
            $returnStr .= "</select>";
            $returnStr .= '<input type="submit" class="btn btn-default" value="' . Language::labelSMSLaptopUpdateMetadataButton() . '"/>';

            $returnStr .= '</form>';
        } else {
            $returnStr .= $this->displayInfo(Language::labelSMSLaptopNoInterviewers());
        }
        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showAvailableUnassignedHouseholds() {

        $refpage = 'sysadmin.sms.sample';
        $currentUser = new User($_SESSION['URID']);
        if ($currentUser->getUserType() == USER_SUPERVISOR) {
            $refpage = 'supervisor.unassignedsample';
        } elseif ($currentUser->getUserType() == USER_RESEARCHER) {
            $refpage = 'researcher.sample';
        }

        $returnStr = '';
        //select psu        
        $puid = loadvar('puid', 0);
        $returnStr .= $this->showActionBar(Language::labelSMSFilterPSU(), $this->displayPsus($puid, true), Language::labelSMSFilterShow(), setSessionParamsPost(array('page' => $refpage)));
        if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
            $households = new Households();
            $unassignedRespondentOrHouseholds = $households->getUnassigned($puid);
        } else {
            $respondents = new Respondents();
            $unassignedRespondentOrHouseholds = $respondents->getUnassigned($puid);
        }
        if (sizeof($unassignedRespondentOrHouseholds) > 0) {



            $returnStr .= '<form method="post">';
            $returnStr .= setSessionParamsPost(array('page' => $refpage . '.assign'));
            $returnStr .= '<input type=hidden name=puid value="' . $puid . '">';

            $returnStr .= '<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
	    <thead>
	      <tr>
		<th><label><input type="checkbox" id="checkAll"/> &nbsp;&nbsp;id</label></th>
		<th>Name</th>';

            //echo 'here';
            $columns = $this->defaultDisplayOverviewAddressColumns();
            foreach ($columns as $column) {
                $returnStr .= '<th>' . $column . '</th>';
            }
            $returnStr .= '    </tr>
	    </thead>
	    <tbody>';
            foreach ($unassignedRespondentOrHouseholds as $respondentOrHousehold) {
                $returnStr .= '<tr><td>';
                $returnStr .= '<label><input type=checkbox name="assignid[]" value="' . $respondentOrHousehold->getPrimkey() . '">&nbsp;&nbsp;';
                $returnStr .= $respondentOrHousehold->getPrimkey() . '</label></td>';
                $returnStr .= '<td>' . $respondentOrHousehold->getName() . '</td>';
                foreach ($columns as $key => $column) {
                    $returnStr .= '<td>' . $respondentOrHousehold->getDataByField($key) . '</td>';
                }
                $returnStr .= '</tr>';
            }
            $returnStr .= '</table>';
            $returnStr .= '<script>
$("#checkAll").change(function () {
    $("input:checkbox").prop("checked", $(this).prop("checked"));
});

</script>';
            $returnStr .= '<nav class="navbar navbar-default" role="navigation">';
            $returnStr .= '<div class="container-fluid"><div class="navbar-header">';
            $returnStr .= '<table><tr><td valign=top><img src="images/arrow_ltr.png"></td><td><a class="navbar-brand">assign selected to:</a></td></tr></table>';
            $returnStr .= '</div><div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">';
            $returnStr .= '<div class="navbar-form navbar-left">';
            $returnStr .= '<div class="form-group">';
            if ($currentUser->getUserType() == USER_SUPERVISOR) {
                $returnStr .= $this->displayInterviewerSelect(0, true);
            } else {
                $returnStr .= $this->displaySupervisorSelect();
            }
            $returnStr .= '</div>';
            $returnStr .= '<button type="submit" class="btn btn-default">' . Language::labelSMSButtonAssign() . '</button>';
            $returnStr .= '</div></form></div></div></nav>';
        } else {
            if ($refpage == 'sysadmin.sms.sample') {
                $returnStr .= $this->displayWarning(Language::labelSMSWarningNoSample());
            } else {
                if (dbConfig::defaultPanel() == PANEL_HOUSEHOLD) {
                    $returnStr .= $this->displayWarning(Language::labelSMSWarningNoUnassignedHouseholds());
                } else {
                    $returnStr .= $this->displayWarning(Language::labelSMSWarningNoUnassignedRespondents());
                }
            }
        }
        return $returnStr;
    }

    function showSample($message = '') {
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()), 'label' => Language::linkSms());
        $headers[] = array('link' => '', 'label' => Language::labelSMSSample());

        $returnStr = $this->showSmsHeader($headers);
        //CONTENT

        $returnStr .= $message;

        $returnStr .= $this->showAvailableUnassignedHouseholds();

        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.sample.download')) . '&puid= ' . loadvar('puid', 0) . '">' . Language::labelSMSDownloadCSV() . '</a>';
        $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.sample.download.gps')) . '&puid= ' . loadvar('puid', 0) . '">' . Language::labelSMSDownloadGPS() . '</a>';
        $returnStr .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
        $returnStr .= '<a href="index.php?r=' . setSessionsParamString(array('page' => 'sysadmin.sms.sample.insert')) . '">' . Language::labelSMSInsertSample() . '</a>';



        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        //echo $returnStr;
        return $returnStr;
    }

    function showInsertSample() {
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()), 'label' => Language::linkSms());
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms.sample'), Language::labelSMSSample()), 'label' => Language::labelSMSSample());
        $headers[] = array('link' => '', 'label' => Language::labelSMSInsertSample());

        $returnStr = $this->showSmsHeader($headers);
        //CONTENT

        $returnStr .= '<form method="post" enctype="multipart/form-data">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.sms.sample.insert.res'));
        $returnStr .= '<table>';
        $returnStr .= '<tr><td>Sample type</td><td>';


        $paneltype = loadvar('paneltype', 1);

        $returnStr .= $this->displayPanelTypeFilter($paneltype);
        $returnStr .= '<tr><td>Upload file</td><td>';
        $returnStr .= '<input type="file" name="file" size="50" class="form-control" />';
        $returnStr .= '</td></tr>';
        $returnStr .= '</table>';

        $returnStr .= '<button type="submit" class="btn btn-default">' . Language::labelSMSButtonInsertSample() . '</button>';

        $returnStr .= '</form>';

        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        //echo $returnStr;
        return $returnStr;
    }

    function showCommunicationTable($message = '') {
        $headers[] = array('link' => setSessionParamsHref(array('page' => 'sysadmin.sms'), Language::linkSms()), 'label' => Language::linkSms());
        $headers[] = array('link' => '', 'label' => Language::labelSMSCommunicationTable());

        $returnStr = $this->showSmsHeader($headers);
        //CONTENT

        $returnStr .= $message;

        $urid = loadvar('selurid', 0);
        $returnStr .= $this->displayInterviewerDropDown('sysadmin.sms.communication', $urid);

        $communication = new Communication();
        if ($urid > 0) {
            $list = $communication->getAllUserCommunication($urid); //getAllUserQueries($urid);
            $hnidTexts = array();
            if (sizeof($list) > 0) {
                $returnStr .= '<br/><table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">';
                $returnStr .= '<tr><th>' . Language::labelSMSCommunicationTableHnid() . '</th><th>' . Language::labelSMSCommunicationTableTs() . '</th><th>' . Language::labelSMSCommunicationTableDataType() . '</th><th>' . Language::labelSMSCommunicationTableInsertTs() . '</th><th>' . Language::labelSMSCommunicationTableReceived() . '</th><th>' . Language::labelSMSCommunicationTableReceivedTs() . '</th><th>' . Language::labelSMSCommunicationTableDirection() . '</th><th>' . Language::labelSMSCommunicationTableFileName() . '</th></tr>';

                foreach ($list as $item) {
                    $returnStr .= '<tr><td>';
                    $hnidTexts[$item['hnid']] = cutOffString('SQL:<br/>' . $communication->decryptAndUncompress($item['sqlcode']), 800);
                    $returnStr .= '<a title="' . Language::linkEditTooltip() . '" onclick="$(\'#hnid' . $item['hnid'] . '\').modal(\'show\');"><span class="glyphicon glyphicon-eye-open"></span></a>';
                    $returnStr .= '&nbsp;&nbsp;<a title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.sms.communication.remove', 'hnid' => $item['hnid'])) . '&selurid=' . $urid . '"><span class="glyphicon glyphicon-remove"></span></a>';
                    $returnStr .= '&nbsp;&nbsp;' . $item['hnid'] . '</td>';
                    $returnStr .= '<td>' . $item['ts'] . '</td>';
                    $returnStr .= '<td>' . $item['datatype'] . '</td>';
                    $returnStr .= '<td>' . $item['insertts'] . '</td>';
                    $returnStr .= '<td>' . $item['received'] . '</td>';
                    $returnStr .= '<td>' . $item['receivedts'] . '</td>';
                    $returnStr .= '<td>' . $item['direction'] . '</td>';
                    $returnStr .= '<td>' . $item['filename'] . '</td>';
                    $returnStr .= '</tr>';
                }
                $returnStr .= '</table>';
                //modal forms
                foreach ($hnidTexts as $key => $text) {
                    $returnStr .= $this->showModalForm('hnid' . $key, $text);
                }
            } else {
                $returnStr .= "<br/><br/>" . $this->displayInfo(Language::labelSMSCommunicationTableNoneFound());
            }
        }

//        $returnStr .= '<input type="submit" class="btn btn-default" value="Update all interviewer laptops"/>';
//        $returnStr .= '</form>';

        $returnStr .= '</form>';
        //END CONTENT
        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showSmsHeader($actions, $navbar = true, $extra = '') {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle(), $extra);
        $returnStr .= '<div id="wrap">';
        if ($navbar) {
            $returnStr .= $this->showNavBar();
        }
        $returnStr .= '<div class="container">';
        if ($navbar) {
            $returnStr .= '<ol class="breadcrumb">';
            for ($i = 0; $i < sizeof($actions); $i++) {
                $action = $actions[$i];
                if ($action['link'] == '') {
                    $returnStr .= '<li class="active">' . $action['label'] . '</li>';
                } else {
                    $returnStr .= '<li>' . $action['link'] . '</li>';
                }
            }

            $returnStr .= '</ol>';
        }
//        $returnStr .= '<div class="row row-offcanvas row-offcanvas-right">';
//        $returnStr .= '<div id=sectiondiv class="col-xs-12 col-sm-9">';
//        $returnStr .= $message;
        return $returnStr;
    }

}

?>