<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplaySearch extends DisplaySysAdmin {

    public function __construct() {
        parent::__construct();
    }

    function hideSearch() {
        unset($_SESSION['SEARCH']);
        unset($_SESSION['SEARCHTERM']);
    }

    function showSearchSysadmin($searchparameters) {
        $returnStr = '<a id="closelink" class="close pull-right">&times;</a>';
        $returnStr .= "<script type='text/javascript'>
                        $ ('#closelink').click(function(event) {
                                $.sidr('close', 'optionssidebar');
                                $.get('" . setSessionParams(array("page" => "sysadmin.search.hide")) . "&updatesessionpage=2" . "',{},function(response){});
                            });
                        ";
        $returnStr .= "</script>";
        if (trim($searchparameters) == "") {
            $returnStr .= $this->displayWarning(Language::messageSearchNoTerm());
        } else {
            global $db, $survey;
            $query = "select name, object, objecttype from " . Config::dbSurvey() . "_settings where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and CONVERT(value using utf8) COLLATE utf8_general_ci like '%" . prepareDatabaseString($searchparameters) . "%' group by objecttype, object, name order by objecttype, object, name";
            $res = $db->selectQuery($query);
            $query1 = "select * from " . Config::dbSurvey() . "_routing where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and CONVERT(rule using utf8) COLLATE utf8_general_ci like '%" . prepareDatabaseString($searchparameters) . "%' order by seid asc, rgid asc";
            $res1 = $db->selectQuery($query1);
            $query2 = "select tyd from " . Config::dbSurvey() . "_types where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and CONVERT(name using utf8) COLLATE utf8_general_ci like '%" . prepareDatabaseString($searchparameters) . "%' order by tyd asc";
            $res2 = $db->selectQuery($query2);
            if ($res || $res1 || $res2) {
                if ($db->getNumberOfRows($res) == 0 && $db->getNumberOfRows($res1) == 0 && $db->getNumberOfRows($res2) == 0) {
                    $returnStr .= $this->displayWarning(Language::labelNoSearched($searchparameters));
                } else {

                    /*
                     * 
                     */

                    $returnStr .= $this->displayCookieScripts();
                    $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#search a').bind('click',function(event){
                                  event.preventDefault();
                                  var url=this.href + \"&" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "\";
                                  $.get(url,{},function(response){ 
                                     $('#content').html($(response).children().first())
                              })	
                           })
                          });
                        ";
                    $returnStr .= "</script>";
                    $_SESSION['SEARCH'] = SEARCH_OPEN_YES;
                    $_SESSION['SEARCHTERM'] = $searchparameters;
                    $returnStr .= $this->displaySuccess(Language::labelSearched($searchparameters));
                    $var_results = array();
                    $type_results = array();
                    $survey_results = array();
                    $group_results = array();
                    $section_results = array();

                    if ($db->getNumberOfRows($res) > 0) {
                        while ($row = $db->getRow($res)) {


                            /* process */
                            switch ($row["objecttype"]) {
                                case OBJECT_VARIABLEDESCRIPTIVE:
                                    $variable = $survey->getVariableDescriptive($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscicvariablecookie'])) {
                                        $cookievalue = $_COOKIE['uscicvariablecookie'];
                                        if (inArray($variable->getSuid() . "~" . $variable->getVsid(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $var_results[$row["name"] . $row["object"] . $row["objecttype"]] = "<tr>
                                            <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicvariablecookie\',\'' . $variable->getSuid() . "~" . $variable->getVsid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "sysadmin.survey.editvariable", "suid" => $_SESSION['SUID'], "vsid" => $row["object"])) . "'>" . $variable->getName() . "</a></td>                                                      
                                                          </tr>";
                                    break;
                                case OBJECT_TYPE:
                                    $type = $survey->getType($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscictypeecookie'])) {
                                        $cookievalue = $_COOKIE['uscictypecookie'];
                                        if (inArray($type->getSuid() . "~" . $type->getTyd(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $type_results[] = "<tr>
                                        <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscictypecookie\',\'' . $type->getSuid() . "~" . $type->getTyd() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "sysadmin.survey.edittype", "suid" => $_SESSION['SUID'], "tyd" => $row["object"])) . "'>" . $type->getName() . "</a></td>

                                                          </tr>";
                                    break;
                                case OBJECT_SECTION:
                                    $section = $survey->getSection($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscicsectioncookie'])) {
                                        $cookievalue = $_COOKIE['uscicsectioncookie'];
                                        if (inArray($section->getSuid() . "~" . $section->getSeid(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $section_results[] = "<tr>
                                        <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicsectioncookie\',\'' . $section->getSuid() . "~" . $section->getSeid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "sysadmin.survey.editsection", "suid" => $_SESSION['SUID'], "seid" => $row["object"])) . "'>" . $section->getName() . "</a></td>

                                                          </tr>";
                                    break;
                                case OBJECT_GROUP:
                                    $group = $survey->getGroup($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscicgroupcookie'])) {
                                        $cookievalue = $_COOKIE['uscicgroupcookie'];
                                        if (inArray($group->getSuid() . "~" . $group->getGid(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $group_results[] = "<tr>
                                        <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicgroupcookie\',\'' . $group->getSuid() . "~" . $group->getGid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "sysadmin.survey.editgroup", "suid" => $_SESSION['SUID'], "gid" => $row["object"])) . "'>" . $group->getName() . "</a></td>

                                                          </tr>";
                                    break;
                                case OBJECT_SURVEY:
                                    $survey_results[] = "<tr>
                                                            <td>" . $survey->getName() . "</td>                                                      
                                                          </tr>";
                                    break;
                            }
                        }
                    }

                    // if types found check if used by variables
                    if ($db->getNumberOfRows($res2) > 0) {
                        while ($rtw = $db->getRow($res2)) {

                            $querytype = "select vsid as object, variablename as name from " . Config::dbSurvey() . "_variables where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and tyd='" . prepareDatabaseString($rtw["tyd"]) . "' order by vsid";
                            $restype = $db->selectQuery($querytype);
                            if ($restype) {
                                if ($db->getNumberOfRows($restype) > 0) {
                                    while ($rtype = $db->getRow($restype)) {

                                        $variable = $survey->getVariableDescriptive($rtype["object"]);
                                        $tagclass = ""; //'class="btn btn-default"';
                                        if (isset($_COOKIE['uscicvariablecookie'])) {
                                            $cookievalue = $_COOKIE['uscicvariablecookie'];
                                            if (inArray($variable->getSuid() . "~" . $variable->getVsid(), explode("-", $cookievalue))) {
                                                $tagclass = 'class="uscic-cookie-tag-active"';
                                            }
                                        }
                                        $var_results[$rtype["name"] . $rtype["object"] . OBJECT_VARIABLEDESCRIPTIVE] = "<tr>
                                            <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicvariablecookie\',\'' . $variable->getSuid() . "~" . $variable->getVsid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                                "<td><a class='searchlink' href='" . setSessionParams(array("page" => "sysadmin.survey.editvariable", "suid" => $_SESSION['SUID'], "vsid" => $rtype["object"])) . "'>" . $variable->getName() . "</a></td>                                                      
                                                          </tr>";
                                    }
                                }
                            }
                        }
                    }

                    $var_results = array_unique($var_results);
                    $type_results = array_unique($type_results);
                    $survey_results = array_unique($survey_results);
                    $group_results = array_unique($group_results);

                    $var_footer = "";
                    $type_footer = "";
                    $section_footer = "";
                    $group_footer = "";
                    $survey_footer = "";

                    $var_header = '<div id="collapseVariables" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($var_results) > 0) {
                        $var_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th></th><th align=middle>' . Language::labelSearchName() . '</th>' .
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $var_footer .= "</tbody></table></div></div>";
                    } else {
                        $var_footer .= "</div></div>";
                    }

                    $type_header = '<div id="collapseTypes" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($type_results) > 0) {
                        $type_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $type_footer .= "</tbody></table></div></div>";
                    } else {
                        $type_footer .= "</div></div>";
                    }

                    $survey_header = '<div id="collapseSurvey" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($survey_results) > 0) {
                        $survey_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $survey_footer .= "</tbody></table></div></div>";
                    } else {
                        $survey_footer .= "</div></div>";
                    }

                    $group_header = '<div id="collapseGroups" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($group_results) > 0) {
                        $group_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $group_footer .= "</tbody></table></div></div>";
                    } else {
                        $group_footer .= "</div></div>";
                    }

                    $section_header = '<div id="collapseSections" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($section_results) > 0) {
                        $section_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $section_footer .= "</tbody></table></div></div>";
                    } else {
                        $section_footer .= "</div></div>";
                    }

                    /* search in routing */
                    $routing_results = array();
                    $routing_header .= '<div id="collapseRouting" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if ($db->getNumberOfRows($res1) > 0) {
                        while ($row = $db->getRow($res1)) {
                            $section = $survey->getSection($row["seid"]);
                            $routing_results[] = "<tr>
                                                    <td><a class='searchlink' href='" . setSessionParams(array("page" => "sysadmin.survey.section", "suid" => $_SESSION['SUID'], "seid" => $row["seid"], "routingline" => $row["rgid"])) . "'>" . $section->getName() . " at " . Language::labelSearchLine() . " " . $row["rgid"] . "</a></td>
                                                  </tr>";
                        }
                    }

                    if (sizeof($routing_results) > 0) {
                        $routing_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchSection() . ' at ' . Language::labelSearchLine() . '</th> 
                                        </thead>
                                        <tbody data-link="row" class="rowlink">';
                        $routing_footer .= "</tbody></table></div></div>";
                    } else {
                        $routing_footer .= "</div></div>";
                    }

                    if (sizeof($var_results) > 0) {
                        $varstring = $var_header . implode("", $var_results) . $var_footer;
                    } else {
                        $varstring = $var_header . $this->displayWarning(Language::messageSearchNoResults()) . $var_footer;
                    }
                    if (sizeof($type_results) > 0) {
                        $typestring = $type_header . implode("", $type_results) . $type_footer;
                    } else {
                        $typestring = $type_header . $this->displayWarning(Language::messageSearchNoResults()) . $type_footer;
                    }
                    if (sizeof($group_results) > 0) {
                        $groupstring = $group_header . implode("", $group_results) . $group_footer;
                    } else {
                        $groupstring = $group_header . $this->displayWarning(Language::messageSearchNoResults()) . $group_footer;
                    }
                    if (sizeof($section_results) > 0) {
                        $sectionstring = $section_header . implode("", $section_results) . $section_footer;
                    } else {
                        $sectionstring = $section_header . $this->displayWarning(Language::messageSearchNoResults()) . $section_footer;
                    }
                    if (sizeof($survey_results) > 0) {
                        $surveystring = $survey_header . implode("", $survey_results) . $survey_footer;
                    } else {
                        $surveystring = $survey_header . $this->displayWarning(Language::messageSearchNoResults()) . $survey_footer;
                    }
                    if (sizeof($routing_results) > 0) {
                        $routingstring = $routing_header . implode("", $routing_results) . $routing_footer;
                    } else {
                        $routingstring = $routing_header . $this->displayWarning(Language::messageSearchNoResults()) . $routing_footer;
                    }

                    $returnStr .= '<div id="search">
                                    <div class="panel-group" id="accordion">
                                       <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseVariables">
                                                ' . Language::labelSearchVariables() . '(' . sizeof($var_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $varstring . '</div>

                                       <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseSurvey">
                                                ' . Language::labelSearchSurvey() . '(' . sizeof($survey_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $surveystring . '</div>
                                              
                                        <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseRouting">
                                                ' . Language::labelSearchRouting() . '(' . sizeof($routing_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $routingstring . '</div>  

                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseTypes">
                                                ' . Language::labelSearchTypes() . '(' . sizeof($type_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $typestring . '</div>

                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseGroups">
                                                ' . Language::labelSearchGroups() . '(' . sizeof($group_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $groupstring . '</div>

                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseSections">
                                                ' . Language::labelSearchSections() . '(' . sizeof($section_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $sectionstring . '</div>';

                    /* accordion end div */
                    $returnStr .= '</div>';

                    /* end search div */
                    $returnStr .= "</div>";
                }
            } else {
                $returnStr .= $this->displayWarning(Language::messageSearchNoResults());
            }
        }
        return $returnStr;
    }

    function showSearchTranslator($searchparameters) {
        $returnStr = '<a id="closelink" class="close pull-right">&times;</a>';
        $returnStr .= "<script type='text/javascript'>
                        $ ('#closelink').click(function(event) {
                                $.sidr('close', 'optionssidebar');
                                $.get('" . setSessionParams(array("page" => "translator.search.hide")) . "&updatesessionpage=2" . "',{},function(response){});
                            });
                        ";
        $returnStr .= "</script>";
        if (trim($searchparameters) == "") {
            $returnStr .= $this->displayWarning(Language::messageSearchNoTerm());
        } else {
            global $db, $survey;
            $query = "select name, object, objecttype from " . Config::dbSurvey() . "_settings where suid=" . prepareDatabaseString($_SESSION['SUID']) . " and CONVERT(value using utf8) COLLATE utf8_general_ci like '%" . prepareDatabaseString($searchparameters) . "%' group by objecttype, object, name order by objecttype, object, name";
            $res = $db->selectQuery($query);
            //$query1 = "select * from " . Config::dbSurvey() . "_routing where suid=" . $_SESSION['SUID'] . " and CONVERT(rule using utf8) COLLATE utf8_general_ci like '%" . prepareDatabaseString($searchparameters) . "%' order by seid asc, rgid asc";
            //$res1 = $db->selectQuery($query1);
            if ($res /* || $res1 */) {
                if ($db->getNumberOfRows($res) == 0 /* && $db->getNumberOfRows($res1) == 0 */) {
                    $returnStr .= $this->displayWarning(Language::labelNoSearched($searchparameters));
                } else {

                    /*
                     * 
                     */

                    $returnStr .= $this->displayCookieScripts();
                    $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#search a').bind('click',function(event){
                                  event.preventDefault();
                                  var url=this.href + \"&" . POST_PARAM_AJAX_LOAD . "=" . AJAX_LOAD . "\";
                                  $.get(url,{},function(response){ 
                                     $('#content').html($(response).children().first())
                              })	
                           })
                          });
                        ";
                    $returnStr .= "</script>";
                    $_SESSION['SEARCH'] = SEARCH_OPEN_YES;
                    $_SESSION['SEARCHTERM'] = $searchparameters;
                    $returnStr .= $this->displaySuccess(Language::labelSearched($searchparameters));
                    $var_results = array();
                    $type_results = array();
                    $survey_results = array();
                    $group_results = array();
                    $section_results = array();

                    // TODO: HOW TO GROUP HERE: VARIABLE YES, SURVEY NO, SHOW NUMBER OF PLACES FOUND IN CASE OF MULTIPLE LOCATIONS? OR SHOW ALL ENTRIES? 
                    if ($db->getNumberOfRows($res) > 0) {
                        while ($row = $db->getRow($res)) {


                            /* process */
                            switch ($row["objecttype"]) {
                                case OBJECT_VARIABLEDESCRIPTIVE:
                                    $variable = $survey->getVariableDescriptive($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscicvariablecookie'])) {
                                        $cookievalue = $_COOKIE['uscicvariablecookie'];
                                        if (inArray($variable->getSuid() . "~" . $variable->getVsid(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $var_results[$row["name"] . $row["object"] . $row["objecttype"]] = "<tr>
                                            <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicvariablecookie\',\'' . $variable->getSuid() . "~" . $variable->getVsid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "translator.survey.translatevariable", "suid" => $_SESSION['SUID'], "vsid" => $row["object"])) . "'>" . $variable->getName() . "</a></td>                                                      
                                                          </tr>";
                                    break;
                                case OBJECT_TYPE:
                                    $type = $survey->getType($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscictypeecookie'])) {
                                        $cookievalue = $_COOKIE['uscictypecookie'];
                                        if (inArray($type->getSuid() . "~" . $type->getTyd(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $type_results[] = "<tr>
                                        <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscictypecookie\',\'' . $type->getSuid() . "~" . $type->getTyd() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "translator.survey.translatetype", "suid" => $_SESSION['SUID'], "tyd" => $row["object"])) . "'>" . $type->getName() . "</a></td>

                                                          </tr>";
                                    break;
                                case OBJECT_SECTION:
                                    $section = $survey->getSection($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscicsectioncookie'])) {
                                        $cookievalue = $_COOKIE['uscicsectioncookie'];
                                        if (inArray($section->getSuid() . "~" . $section->getSeid(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $section_results[] = "<tr>
                                        <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicsectioncookie\',\'' . $section->getSuid() . "~" . $section->getSeid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "translator.survey.section", "suid" => $_SESSION['SUID'], "seid" => $row["object"])) . "'>" . $section->getName() . "</a></td>

                                                          </tr>";
                                    break;
                                case OBJECT_GROUP:
                                    $group = $survey->getGroup($row["object"]);
                                    $tagclass = ""; //'class="btn btn-default"';
                                    if (isset($_COOKIE['uscicgroupcookie'])) {
                                        $cookievalue = $_COOKIE['uscicgroupcookie'];
                                        if (inArray($group->getSuid() . "~" . $group->getGid(), explode("-", $cookievalue))) {
                                            $tagclass = 'class="uscic-cookie-tag-active"';
                                        }
                                    }
                                    $group_results[] = "<tr>
                                        <td><a " . $tagclass . ' onclick="var res = updateCookie(\'uscicgroupcookie\',\'' . $group->getSuid() . "~" . $group->getGid() . '\'); if (res == 1) { $(this).addClass(\'uscic-cookie-tag-active\'); } else { $(this).removeClass(\'uscic-cookie-tag-active\'); } return false;" title="' . Language::linkTagTooltip() . '" href="" role="button"><span class="glyphicon glyphicon-tag"></span></a></td>' .
                                            "<td><a class='searchlink' href='" . setSessionParams(array("page" => "translator.survey.translategroup", "suid" => $_SESSION['SUID'], "gid" => $row["object"])) . "'>" . $group->getName() . "</a></td>

                                                          </tr>";
                                    break;
                                case OBJECT_SURVEY:
                                    $survey_results[] = "<tr>
                                                            <td>" . $survey->getName() . "</td>                                                      
                                                          </tr>";
                                    break;
                            }
                        }
                    }

                    $var_header .= '<div id="collapseVariables" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($var_results) > 0) {
                        $var_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th></th><th align=middle>' . Language::labelSearchName() . '</th>' .
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $var_footer .= "</tbody></table></div></div>";
                    } else {
                        $var_footer .= "</div></div>";
                    }

                    $type_header .= '<div id="collapseTypes" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($type_results) > 0) {
                        $type_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $type_footer .= "</tbody></table></div></div>";
                    } else {
                        $type_footer .= "</div></div>";
                    }

                    $survey_header .= '<div id="collapseSurvey" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($survey_results) > 0) {
                        $survey_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $survey_footer .= "</tbody></table></div></div>";
                    } else {
                        $survey_footer .= "</div></div>";
                    }

                    $group_header .= '<div id="collapseGroups" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($group_results) > 0) {
                        $group_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $group_footer .= "</tbody></table></div></div>";
                    } else {
                        $group_footer .= "</div></div>";
                    }

                    $section_header .= '<div id="collapseSections" class="panel-collapse collapse">
                                            <div class="panel-body">';
                    if (sizeof($section_results) > 0) {
                        $section_header .= '<table class="table table-striped table-bordered">
                                        <thead>
                                        <th align=middle>' . Language::labelSearchName() . '</th>' .
                                //<th align=middle>' . Language::labelSearchSetting() . '</th>
                                '</thead>
                                        <tbody data-link="row" class="rowlink">';
                        $section_footer .= "</tbody></table></div></div>";
                    } else {
                        $section_footer .= "</div></div>";
                    }

                    if (sizeof($var_results) > 0) {
                        $varstring = $var_header . implode("", $var_results) . $var_footer;
                    } else {
                        $varstring = $var_header . $this->displayWarning(Language::messageSearchNoResults()) . $var_footer;
                    }
                    if (sizeof($type_results) > 0) {
                        $typestring = $type_header . implode("", $type_results) . $type_footer;
                    } else {
                        $typestring = $type_header . $this->displayWarning(Language::messageSearchNoResults()) . $type_footer;
                    }
                    if (sizeof($group_results) > 0) {
                        $groupstring = $group_header . implode("", $group_results) . $group_footer;
                    } else {
                        $groupstring = $group_header . $this->displayWarning(Language::messageSearchNoResults()) . $group_footer;
                    }
                    if (sizeof($section_results) > 0) {
                        $sectionstring = $section_header . implode("", $section_results) . $section_footer;
                    } else {
                        $sectionstring = $section_header . $this->displayWarning(Language::messageSearchNoResults()) . $section_footer;
                    }
                    if (sizeof($survey_results) > 0) {
                        $surveystring = $survey_header . implode("", $survey_results) . $survey_footer;
                    } else {
                        $surveystring = $survey_header . $this->displayWarning(Language::messageSearchNoResults()) . $survey_footer;
                    }

                    $returnStr .= '<div id="search">
                                    <div class="panel-group" id="accordion">
                                       <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseVariables">
                                                ' . Language::labelSearchVariables() . '(' . sizeof($var_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $varstring . '</div>

                                       <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseSurvey">
                                                ' . Language::labelSearchSurvey() . '(' . sizeof($survey_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $surveystring . '</div>
                                              
                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseTypes">
                                                ' . Language::labelSearchTypes() . '(' . sizeof($type_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $typestring . '</div>

                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseGroups">
                                                ' . Language::labelSearchGroups() . '(' . sizeof($group_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $groupstring . '</div>

                                      <div class="panel panel-default">
                                          <div class="panel-heading">
                                            <h4 class="panel-title">
                                              <a data-toggle="collapse" data-target="#collapseSections">
                                                ' . Language::labelSearchSections() . '(' . sizeof($section_results) . ')
                                              </a>
                                            </h4>
                                          </div> ' . $sectionstring . '</div>';

                    /* accordion end div */
                    $returnStr .= '</div>';

                    /* end search div */
                    $returnStr .= "</div>";
                }
            } else {
                $returnStr .= $this->displayWarning(Language::messageSearchNoResults());
            }
        }
        return $returnStr;
    }

    function convertSettingName($setting) {
        define('SETTING_NAME', 'name');
        define('SETTING_DESCRIPTION', 'description');
        define('SETTING_ROUTING', 'routing');
        define('SETTING_QUESTION', 'question');

        define('SETTING_OPTIONS', 'options');
        define('SETTING_ENUMERATED_CUSTOM', 'enumeratedcustom');
        define('SETTING_DATA_ENCRYPTION_KEY', 'dataencryptionkey');
        define('SETTING_MINIMUM_RANGE', 'minimum');
        define('SETTING_MAXIMUM_RANGE', 'maximum');

        define('SETTING_INPUT_MASK', 'inputmask');
        define('SETTING_INPUT_MASK_CUSTOM', 'inputmaskcustom');
        define('SETTING_INPUT_MASK_PLACEHOLDER', 'inputmaskplaceholder');
        define('SETTING_MINIMUM_LENGTH', 'minimumlength');
        define('SETTING_MAXIMUM_LENGTH', 'maximumlength');
        define('SETTING_MINIMUM_OPEN_LENGTH', 'minimumlength');
        define('SETTING_MAXIMUM_OPEN_LENGTH', 'maximumlength');
        define('SETTING_MINIMUM_WORDS', 'minimumwords');
        define('SETTING_MAXIMUM_WORDS', 'maximumwords');
        define('SETTING_MINIMUM_SELECTED', 'minimumselected');
        define('SETTING_EXACT_SELECTED', 'exactselected');
        define('SETTING_MAXIMUM_SELECTED', 'maximumselected');
        define('SETTING_INVALIDSUB_SELECTED', 'invalidsubselected');
        define('SETTING_INVALID_SELECTED', 'invalidselected');
        define('SETTING_MINIMUM_REQUIRED', 'minimumrequired');
        define('SETTING_MAXIMUM_REQUIRED', 'maximumrequired');
        define('SETTING_MINIMUM_CALENDAR', 'minimumcalendar');
        define('SETTING_MAXIMUM_CALENDAR', 'maximumcalendar');
        define('SETTING_PATTERN', 'pattern');
        define('SETTING_BACK_BUTTON_LABEL', 'backbuttonlabel');
        define('SETTING_NEXT_BUTTON_LABEL', 'nextbuttonlabel');
        define('SETTING_DK_BUTTON_LABEL', 'dkbuttonlabel');
        define('SETTING_RF_BUTTON_LABEL', 'rfbuttonlabel');
        define('SETTING_UPDATE_BUTTON_LABEL', 'updatebuttonlabel');
        define('SETTING_NA_BUTTON_LABEL', 'nabuttonlabel');

        define('SETTING_PROGRESSBAR_FILLED_COLOR', 'progressbarfilledcolor');

        define('SETTING_PROGRESSBAR_REMAIN_COLOR', 'progressbarremaincolor');

        define('SETTING_PROGRESSBAR_VALUE', 'progressbarvalue');

        define('SETTING_PROGRESSBAR_WIDTH', 'progressbarwidth');

        define('SETTING_FILLTEXT', 'filltext');

        define('SETTING_FILLCODE', 'fillcode');



        define('SETTING_SLIDER_INCREMENT', 'sliderincrement');
        define('SETTING_SLIDER_TEXTBOX_LABEL', 'slidertextboxlabel');
        define('SETTING_SLIDER_TEXTBOX_POSTTEXT', 'slidertextboxposttext');


        define('SETTING_EMPTY_MESSAGE', 'emptymessage');
        define('SETTING_ERROR_MESSAGE_RANGE', 'errormessagerange');
        define('SETTING_ERROR_MESSAGE_INTEGER', 'errormessageinteger');

        define('SETTING_ERROR_MESSAGE_DOUBLE', 'errormessagedouble');

        define('SETTING_ERROR_MESSAGE_PATTERN', 'errormessagepattern');

        define('SETTING_ERROR_MESSAGE_MINIMUM_LENGTH', 'errormessageminlength');

        define('SETTING_ERROR_MESSAGE_MAXIMUM_LENGTH', 'errormessagemaxlength');

        define('SETTING_ERROR_MESSAGE_MINIMUM_WORDS', 'errormessageminwords');

        define('SETTING_ERROR_MESSAGE_MAXIMUM_WORDS', 'errormessagemaxwords');

        define('SETTING_ERROR_MESSAGE_MINIMUM_SELECT', 'errormessageminselect');

        define('SETTING_ERROR_MESSAGE_MAXIMUM_SELECT', 'errormessagemaxselect');

        define('SETTING_ERROR_MESSAGE_EXACT_SELECT', 'errormessageexactselect');

        define('SETTING_ERROR_MESSAGE_INVALID_SUB_SELECT', 'errormessageinvalidsubselect');

        define('SETTING_ERROR_MESSAGE_INVALID_SELECT', 'errormessageinvalidselect');

        define('SETTING_ERROR_MESSAGE_MINIMUM_REQUIRED', 'errorminimumrequired');
        define('SETTING_ERROR_MESSAGE_MAXIMUM_REQUIRED', 'errormaximumrequired');
        define('SETTING_ERROR_MESSAGE_MAXIMUM_CALENDAR', 'errormessagemaximumcalendar');
        define('SETTING_ERROR_MESSAGE_EXACT_REQUIRED', 'errorexactrequired');
        define('SETTING_ERROR_MESSAGE_INCLUSIVE', 'errorinclusive');
        define('SETTING_ERROR_MESSAGE_EXCLUSIVE', 'errorexclusive');
        define('SETTING_ERROR_MESSAGE_UNIQUE_REQUIRED', 'uniquerequired');

        define('SETTING_ERROR_MESSAGE_INLINE_MINIMUM_REQUIRED', 'errorinlineminimumrequired');
        define('SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_REQUIRED', 'errorinlinemaximumrequired');
        define('SETTING_ERROR_MESSAGE_INLINE_MAXIMUM_CALENDAR', 'errorinlinemessagemaximumcalendar');
        define('SETTING_ERROR_MESSAGE_INLINE_EXACT_REQUIRED', 'errorinlineexactrequired');
        define('SETTING_ERROR_MESSAGE_INLINE_INCLUSIVE', 'errorinlineinclusive');
        define('SETTING_ERROR_MESSAGE_INLINE_EXCLUSIVE', 'errorinlineexclusive');
        define('SETTING_ERROR_MESSAGE_INLINE_ANSWERED', 'errorinlineanswered');

        define('SETTING_ID', 'id');
        define('SETTING_JAVASCRIPT_WITHIN_ELEMENT', 'elementjavascript');
        define('SETTING_JAVASCRIPT_WITHIN_PAGE', 'pagejavascript');
        define('SETTING_STYLE_WITHIN_ELEMENT', 'elementstyle');
        define('SETTING_STYLE_WITHIN_PAGE', 'pagestyle');

        define('SETTING_PLACEHOLDER', 'placeholder');
        define('SETTING_PRETEXT', 'pretext');

        define('SETTING_POSTTEXT', 'posttext');

        define('SETTING_HOVERTEXT', 'hovertext');

        define('SETTING_SURVEY_TEMPLATE', 'surveytemplate');
        define('SETTING_GROUP_NAME', 'groupname');

        define('SETTING_GROUP_MINIMUM_REQUIRED', "minimumrequired");
        define('SETTING_GROUP_MAXIMUM_REQUIRED', "maximumrequired");
        define('SETTING_GROUP_EXACT_REQUIRED', "exactrequired");
        define('SETTING_GROUP_UNIQUE_REQUIRED', "uniquerequired");

        define('SETTING_GROUP_TABLE_ID', 'tableid');
        define('SETTING_ENUMERATED_TEXTBOX_LABEL', 'enumeratedtextboxlabel');
        define('SETTING_ENUMERATED_TEXTBOX_POSTTEXT', 'eneumeratedtextboxposttext');

        define('SETTING_INLINE_MINIMUM_REQUIRED', "inlineminimumrequired");
        define('SETTING_INLINE_MAXIMUM_REQUIRED', "inlinemaximumrequired");
        define('SETTING_INLINE_EXACT_REQUIRED', "inlineexactrequired");
    }

}

?>