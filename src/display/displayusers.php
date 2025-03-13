<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayUsers extends DisplaySysAdmin {

    public function __construct() {
        parent::__construct();
    }

    function showUsers($message = "") {
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . Language::headerUsers() . '</li>';
        $returnStr .= '</ol>';
        $returnStr .= $message;
        $returnStr .= '<div id=usersdiv>';

        $returnStr .= '</div>';
        $usertype = loadvar('usertype', USER_INTERVIEWER);
        $users = new Users();
        if ($usertype == "-1") {
            $returnStr .= $this->showUsersList($users->getUsers());
        } else {
            $returnStr .= $this->showUsersList($users->getUsersByType($usertype));
        }
        $returnStr .= '<a href="' . setSessionParams(array('page' => 'sysadmin.users.adduser')) . '">' . Language::labelUserAddUser() . '</a>';

        $returnStr .= '</p></div>    </div>'; //container and wrap
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function showUsersList($users) {

        $returnStr = '';
        $returnStr .= "<form id=refreshform method=post>";
        $returnStr .= '<input type=hidden name=page value="sysadmin.users">';
        $returnStr .= '<input type=hidden name="usertype" id="usertype_hidden">';
        $returnStr .= "</form>";
        $usertypes = array(-1 => Language::labelAll(), USER_INTERVIEWER => Language::labelInterviewer(), USER_NURSE => Language::labelNurse(), USER_SUPERVISOR => Language::labelSupervisor(), USER_TRANSLATOR => Language::labelTranslator(), USER_RESEARCHER => Language::labelResearcher(), USER_SYSADMIN => Language::labelSysadmin(), USER_TESTER => Language::labelTester());
        $usertype = loadvar('usertype', USER_INTERVIEWER);
        $returnStr .= Language::labelUserFilter() . $this->displaySelectFromArray($usertypes, $usertype, 'usertype');
        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                                                $("#usertype").change(function (e) {
                                                    $("#usertype_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
        $returnStr .= "</script>";
        
        if (sizeof($users) > 0) {

            $returnStr .= $this->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#usertable').dataTable(
                                {
                                    \"iDisplayLength\": " . sizeof($users) . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    paginate: false,
                                    colVis: {
                                        activate: \"mouseover\",
                                        exclude: [ 0 ]
                                    }
                                }    
                             );                                         
                       });</script>
                        "; //
            $returnStr .= $this->displayPopoverScript();
            $returnStr .= '<br/><br/><table id="usertable" class="table table-striped table-bordered pre-scrollable table-condensed table-hover">';
            $returnStr .= '<thead><tr><th></td><th>' . Language::labelUserUserName() . '</th><th>' . Language::labelUserUserNameName() . '</th><th>' . Language::labelUserUserType() . '</th></tr></thead>';
            $returnStr .= '<tbody>';
            $usertypes = array(USER_INTERVIEWER => Language::labelInterviewer(), USER_NURSE => Language::labelNurse(), USER_SUPERVISOR => Language::labelSupervisor(), USER_TRANSLATOR => Language::labelTranslator(), USER_RESEARCHER => Language::labelResearcher(), USER_SYSADMIN => Language::labelSysadmin(), USER_TESTER => Language::labelTester());
            foreach ($users as $user) {
                $returnStr .= '<tr><td>';
                $content = '<a id="' . $user->getUrid() . '_edit" title="' . Language::linkEditTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.users.edituser', 'urid' => $user->getUrid())) . '"><span class="glyphicon glyphicon-edit"></span></a>';
                $content .= '&nbsp;&nbsp;<a id="' . $user->getUrid() . '_copy" title="' . Language::linkCopyTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.users.copyuser', 'urid' => $user->getUrid())) . '" ' . confirmAction(language::messageCopyUser($user->getName()), 'COPY') . '><span class="glyphicon glyphicon-copyright-mark"></span></a>';
                $content .= '&nbsp;&nbsp;<a id="' . $user->getUrid() . '_remove" title="' . Language::linkRemoveTooltip() . '" href="' . setSessionParams(array('page' => 'sysadmin.users.removeuser', 'urid' => $user->getUrid())) . '" ' . confirmAction(language::messageRemoveUser($user->getName()), 'REMOVE') . '><span class="glyphicon glyphicon-remove"></span></a>';
                $returnStr .= '<a rel="popover" id="' . $user->getUrid() . '_popover" data-placement="right" data-html="true" data-toggle="popover" data-trigger="hover" href="' . setSessionParams(array('page' => 'sysadmin.users.edituser', 'urid' => $user->getUrid())) . '"><span class="glyphicon glyphicon-hand-right"></span></a>';
                $returnStr .= '<td>' . $user->getUsername() . '</td><td>' . $user->getName() . '</td>';
                $returnStr .= '<td>' . $usertypes[$user->getUserType()] . '</td></tr>';
                $returnStr .= $this->displayPopover("#" . $user->getUrid() . '_popover', $content);
            }
            $returnStr .= '</tbody>';
            $returnStr .= '</table><br/><br/>';
        } else {
            $returnStr .= "<br/><br/>" . $this->displayWarning(Language::messageNoUsersYet());
        }
        return $returnStr;
    }

    function showEditUser($urid, $message = "") {
        
        $user = new User($urid);
        $returnStr = $this->showSysAdminHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= $this->showNavBar();
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<ol class="breadcrumb">';
        $returnStr .= '<li>' . setSessionParamsHref(array('page' => 'sysadmin.users'), Language::headerUsers()) . '</li>';
        if ($user->getUsername() == '') {
            $returnStr .= '<li>' . Language::labelUserAddUser() . '</li>';
        } else {
            $returnStr .= '<li>' . Language::labelEdit() . ' ' . $user->getUsername() . '</li>';
        }
        $returnStr .= '</ol>';
        $returnStr .= $message;
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<form id="editform" method="post">';

        $returnStr .= '<span class="label label-default">' . Language::labelUserGeneral() . '</span>';
        $returnStr .= '<div class="well">';
        $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.users.edituserres', 'urid' => $urid));


        $returnStr .= '<div class="row">';
        $returnStr .= '<div class="col-md-6">';

        $returnStr .= '<table>';
        $returnStr .= '<tr><td>' . Language::labelUserUserName() . '</td><td><input type="text" class="form-control" name="username" value="' . convertHTLMEntities($user->getUsername(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td>' . Language::labelUserUserNameName() . '</td><td><input type="text" class="form-control" name="name" value="' . convertHTLMEntities($user->getName(), ENT_QUOTES) . '"></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelUserActive() . '</td><td>';
        $returnStr .= $this->showDropDown(array(VARIABLE_ENABLED => Language::labelEnabled(), VARIABLE_DISABLED => Language::labelDisabled()), $user->getStatus(), 'status');
        $returnStr .= '</td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelUserUserType() . '</td><td>';
        $returnStr .= $this->showDropDown(array(USER_INTERVIEWER => Language::labelInterviewer(), USER_NURSE => Language::labelNurse(), USER_SUPERVISOR => Language::labelSupervisor(), USER_TRANSLATOR => Language::labelTranslator(), USER_RESEARCHER => Language::labelResearcher(), USER_SYSADMIN => Language::labelSysadmin(), USER_TESTER => Language::labelTester()), $user->getUserType(), 'usertype', 'usertype');
        $returnStr .= '</td></tr>';

        $returnStr .= "<script type='text/javascript'>";
        $returnStr .= '$( document ).ready(function() {
                            $("#usertype").change(function (e) {
                                handleType(this.value);
                            });
                            
                            handleType($("#usertype").val());    
                        });
                                                
function handleType(value) {

                                                    if (value == ' . USER_NURSE . ') {
                                                        $("#subtype").show(); 
                                                        $("#subtype2").hide();                                                         
                                                    }   
                                                    else if (value == ' . USER_SYSADMIN . ') {
                                                        $("#subtype2").show(); 
                                                        $("#subtype").hide(); 
                                                    }
                                                    else {
                                                        $("#subtype").hide();                                                       
                                                        $("#subtype2").hide();
                                                    }
                                                    
                                                    if (value == ' . USER_INTERVIEWER . ' || value == ' . USER_CATIINTERVIEWER . ' || value == ' . USER_NURSE . ' || value == ' . USER_SUPERVISOR . ') {
                                                        $("#super").show();
                                                        $("#accessdiv").hide();
                                                        $("#surveyaccess").hide();
                                                    }
                                                    else {
                                                        $("#super").hide();
                                                        $("#accessdiv").show();
                                                        $("#surveyaccess").show();
                                                    }
                                                }  
                                                          

                        
                                                
                        ';
        $returnStr .= "</script>";

        if (inArray($user->getUserType(), array(USER_NURSE))) {
            $returnStr .= '<tr id=subtype><td align=top>' . Language::labelUserUserSubType() . '</td><td>';
            $returnStr .= $this->showDropDown(array(USER_NURSE_MAIN => Language::labelNurseMain(), USER_NURSE_LAB => Language::labelNurseLab(), USER_NURSE_FIELD => Language::labelNurseField(), USER_NURSE_VISION => Language::labelNurseVision()), $user->getUserSubType(), 'usersubtypenurse');
            $returnStr .= '</td></tr>';
        } else if (inArray($user->getUserType(), array(USER_SYSADMIN))) {
            $returnStr .= '<tr id=subtype2><td align=top>' . Language::labelUserUserSubType() . '</td><td>';
            $returnStr .= $this->showDropDown(array(USER_SYSADMIN_MAIN => Language::labelSysadminMain(), USER_SYSADMIN => Language::labelSysadminAdmin()), $user->getUserSubType(), 'usersubtype');
            $returnStr .= '</td></tr>';
        } else {
            $returnStr .= '<tr id=subtype style="display: none;"><td align=top>' . Language::labelUserUserSubType() . '</td><td>';
            $returnStr .= $this->showDropDown(array(USER_NURSE_MAIN => Language::labelNurseMain(), USER_NURSE_LAB => Language::labelNurseLab(), USER_NURSE_FIELD => Language::labelNurseField(), USER_NURSE_VISION => Language::labelNurseVision()), $user->getUserSubType(), 'usersubtypenurse');
            $returnStr .= '</td></tr>';
            $returnStr .= '<tr id=subtype2 style="display: none;"><td align=top>' . Language::labelUserUserSubType() . '</td><td>';
            $returnStr .= $this->showDropDown(array(USER_SYSADMIN_MAIN => Language::labelSysadminMain(), USER_SYSADMIN => Language::labelSysadminAdmin()), $user->getUserSubType(), 'usersubtype');
            $returnStr .= '</td></tr>';
        }

        if (inArray($user->getUserType(), array(USER_INTERVIEWER, USER_CATIINTERVIEWER, USER_NURSE, USER_SUPERVISOR))) {
            $returnStr .= '<tr id=super><td>' . Language::labelUserSupervisor() . '</td><td>';
            $users = new Users();
            $users = $users->getUsersByType(USER_SUPERVISOR);
            $exclude = "";
            if ($user->getUserType() == USER_SUPERVISOR) {
                $exclude = $user->getUrid();
            }
            $returnStr .= $this->displayUsers($users, $user->getSupervisor(), 'uridsel', true, $exclude);
            $returnStr .= '</td></tr>';
        }
        $extra = '';
        if (inArray($user->getUserType(), array(USER_NURSE, USER_INTERVIEWER, USER_SUPERVISOR, USER_CATIINTERVIEWER))) {
            $extra = "style='display: none;'";
        }
        
        // show all surveys to manage which allowed access to if sysadmin and managing other users
        $all = false;
        if ($user->getUserType() == USER_SYSADMIN && $user->getUserSubType() == 1) {
            $all = true;
        }                
        $returnStr .= '<tr id="surveyaccess"' . $extra . '><td>' . Language::labelUserSurveyAllowed() . '</td><td>' . $this->displaySurveys(SETTING_USER_SURVEYS . "[]", SETTING_USER_SURVEYS, implode("~", $user->getSurveysAccess()), '', "multiple", "", $all) . '</td></tr>';

        $returnStr .= '</table></div>';
        $returnStr .= '<div class="col-md-6">';
        $returnStr .= '<table>';
        $returnStr .= '<tr><td align=top>' . Language::labelUserPassword() . '</td><td><input type="text" class="form-control" name="pwd1"></td></tr>';
        $returnStr .= '<tr><td align=top>' . Language::labelUserPassword2() . '</td><td><input type="text" class="form-control" name="pwd2"></td></tr>';
        $returnStr .= '</table></div></div>';
        if ($urid != "") {
            $returnStr .= '<br/><input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
        } else {
            $returnStr .= '<br/><input type="submit" class="btn btn-default" value="' . Language::buttonAdd() . '"/>';
        }
        $returnStr .= '</div></form>';
        $suid = $_SESSION['SUID'];

        /* available surveys */
        if ($urid != "") {
            $extra = '';
            if (inArray($user->getUserType(), array(USER_NURSE, USER_INTERVIEWER, USER_SUPERVISOR, USER_CATIINTERVIEWER))) {
                $extra = "style='display: none;'";
            }
            $returnStr .= "<div " . $extra . " id='accessdiv'>";

            $returnStr .= "<form id=refreshform method=post>";
            $returnStr .= '<input type=hidden name=page value="sysadmin.users.edituser">';
            $returnStr .= '<input type=hidden name="' . SMS_POST_SURVEY . '" id="' . SMS_POST_SURVEY . '_hidden" value="' . getSurvey() . '">';
            $returnStr .= "</form>";

            $returnStr .= '<form id="editform1" method="post">';

            $returnStr .= setSessionParamsPost(array('page' => 'sysadmin.users.edituseraccessres', 'urid' => $urid));

            $returnStr .= '<span class="label label-default">' . Language::labelUserAccess() . '</span>';
            $returnStr .= '<div class="well">';
            $returnStr .= "<table>";
            $allsurveys = $user->getSurveysAccess();
            if (!inArray($suid, $allsurveys)) {
                $suid = $allsurveys[0];
            }
            $survey = new Survey($suid);
            $u = $_SESSION['URID'];
            $_SESSION['URID'] = $urid; // pretend to be edited user for a moment to get surveys to display
            $returnStr .= '<tr><td>' . Language::labelUserSurveyAccess() . '</td><td>' . $this->displaySurveys(SMS_POST_SURVEY, SMS_POST_SURVEY, $suid, '', "") . '</td></tr>';
            $_SESSION['URID'] = $u;



            $returnStr .= "<script type='text/javascript'>";
            $returnStr .= '$( document ).ready(function() {
                                                $("#' . SMS_POST_SURVEY . '").change(function (e) {
                                                    $("#' . SMS_POST_SURVEY . '_hidden").val(this.value);                                                     
                                                    $("#refreshform").submit();
                                                });
                                                })';
            $returnStr .= "</script>";

            /* available modes */
            $modes = Common::surveyModes();
            $allowedmodes = explode("~", $survey->getAllowedModes());
            $usermodes = $user->getModes($suid); 
            foreach ($allowedmodes as $mode) {
                $returnStr .= "<tr class='modesrow'><td>" . $modes[$mode] . "</td><td>";
                $returnStr .= $this->displayUserMode(SETTING_USER_MODE . $mode, inArray($mode, $usermodes));
                $userlanguages = $user->getLanguages($suid, $mode);
                $returnStr .= "<td>" . Language::labelUserLanguageAllowed() . "</td>";
                $returnStr .= "<td>" . $this->displayLanguagesAdmin(SETTING_USER_LANGUAGES . $mode, SETTING_USER_LANGUAGES . $mode, $userlanguages, true, false, false, "multiple", $survey->getAllowedLanguages($mode)) . "</td>";
                $returnStr .= "</tr>";
            }

            $returnStr .= '</table>';
            $returnStr .= '<br/><input type="submit" class="btn btn-default" value="' . Language::buttonEdit() . '"/>';
            $returnStr .= '</div></form></div>';
        }

        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter(false);
        return $returnStr;
    }

    function displayUserMode($name, $selected = false) {
        $returnStr = "<select class='selectpicker show-tick' name=" . $name . ">";
        if ($selected) {
            $selected[USER_MODE_YES] = "SELECTED";
            $selected[USER_MODE_NO] = "";
        } else {
            $selected[USER_MODE_NO] = "SELECTED";
            $selected[USER_MODE_YES] = "";
        }
        $returnStr .= "<option " . $selected[USER_MODE_YES] . " value=" . USER_MODE_YES . ">" . Language::optionsUserModeYes() . "</option>";
        $returnStr .= "<option " . $selected[USER_MODE_NO] . " value=" . USER_MODE_NO . ">" . Language::optionsUserModeNo() . "</option>";
        $returnStr .= "</select>";
        return $returnStr;
    }

}

?>