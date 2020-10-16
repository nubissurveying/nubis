<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once("../constants.php");
require_once("../functions.php");
require_once("../dbConfig.php");
require_once("../config.php");
require_once("../globals.php");
require_once("../user.php");

if (loadvar('r') != '') {
    getSessionParamsPost(loadvar('r'));
}

// include language
$_SESSION['SYSTEM_ENTRY'] = USCIC_SMS;
$l = getSMSLanguage();
if (file_exists("language/language" . getSMSLanguagePostFix($l) . ".php")) {
    require_once('language_' . getSMSLanguagePostFix($l) . '.php');
} else {
    require_once('language_en.php'); // fall back on english language  file
}
$_SESSION['SYSTEM_ENTRY'] = USCIC_SURVEY; // switch back to survey

class ReportIssue {

    function __construct() {
        
    }        

    function report() {
        $returnStr = $this->showHeader(Language::messageSMSTitle());        
        $returnStr .= "<form method='post'>";
        $params = getSessionParams();
        $params['testpage'] = 'reportRes';
        $returnStr .= setSessionParamsPost($params);

        $returnStr .= '<div id="wrap">';
        $returnStr .= '<div class="container"><p>';

        $returnStr .= $this->reportSub();

        $returnStr .= '                </div></div>';
        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showFooter(false);
        echo $returnStr;
    }
    
    function reportSub($message = '') {
        $returnStr = $this->displayComboBox();
        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::reportProblem() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';
        $returnStr .= $message;
        global $survey;
        $issues = $survey->getReportedIssues();

        $returnStr .= '<div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#newissue" aria-controls="home" role="tab" data-toggle="tab">New problem</a></li>
    <li role="presentation"><a href="#reportedissues" aria-controls="home" role="tab" data-toggle="tab">Reported problems (' . sizeof($issues) . ')</a></li>
  </ul>
<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="newissue">';

        $returnStr .= "<br/><table>";
        $returnStr .= "<tr><td>" . Language::reportProblemCategory() . "</td><td>";
        $returnStr .= "<select class='selectpicker show-tick' name='reportcategory'>";
        $options = Language::reportProblemCategories();
        foreach ($options as $k => $option) {
            $returnStr .= "<option value=" . $k . ">" . $option . "</option>";
        }
        $returnStr .= "</select></td></tr>";
        $returnStr .= "<tr><td valign=top>" . Language::reportProblemDescription() . "</td><td>";
        $returnStr .= "<textarea name='reportcomment' class='form-control' style='min-width: 400px;' rows=6></textarea>";
        $returnStr .= "</td></tr>";
        $returnStr .= '</table><br/>';
        $returnStr .= '<button type="submit" class="btn btn-success" style="min-width:100px">' . Language::buttonReport() . '</button>';
        $returnStr .= '<button onclick="window.close();" type="cancel" class="btn btn-default" style="min-width:100px">' . Language::buttonClose() . '</button>';
        $returnStr .= "</form>";

        $returnStr .= '</div>
    <div role="tabpanel" class="tab-pane" id="reportedissues">';

        if (sizeof($issues) == 0) {
            $returnStr .= "<br/>" . '<div class="alert alert-warning">' . Language::labelNoProblemsReported() . '</div>';
        } else {

            $returnStr .= $this->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#issuetable').dataTable(
                                {
                                    \"iDisplayLength\": " . sizeof($issues) . ",
                                    dom: 'C<\"clear\">lfrtip',
                                    searching: false,
                                    paging: false
                                    }    
                                );
                                         
                       });</script>

                        "; //

            $returnStr .= "<br/><table id='issuetable' class='table table-bordered table-striped'><thead>";
            $returnStr .= "<th>Reported by</th><th>" . Language::labelReportedOn() . "</th><th>" . Language::labelReportedCategory() . "</th><th>" . Language::labelReportedDescription() . "</th><th>" . Language::labelReportedMode() . "</th><th>" . Language::labelReportedLanguage() . "</th>";
            $returnStr .= "</thead><tbody>";
            $modes = Common::surveyModes();
            $languages = Language::getLanguagesArray();
            $cats = Language::reportProblemCategories();
            foreach ($issues as $is) {
                $us = new User($is['urid']);
                $returnStr .= "<tr>";
                $returnStr .= "<td>" . $us->getUsername() . "</td>";
                $returnStr .= "<td>" . $is["ts"] . "</td>";
                $returnStr .= "<td>" . $cats[$is["category"]] . "</td>";
                $returnStr .= "<td>" . $is["comment"] . "</td>";
                $returnStr .= "<td>" . $modes[$is["mode"]] . "</td>";
                $returnStr .= "<td>" . $languages[str_replace("_","",getSurveyLanguagePostFix($is["language"]))]['name'] . "</td>";
                $returnStr .= "</tr>";
            }
            $returnStr .= "</tbody></table>";
        }

        $returnStr .= '</div>
  </div>
</div>';
        return $returnStr;
    }

    function reportRes() {

        global $db;
        $query = "insert into " . Config::dbSurvey() . "_issues (urid,suid,primkey,mainseid,seid,rgid,displayed,category,comment,status,language,mode,version) values (";
        $query .= $db->escapeString(getFromSessionParams('reporturid')) . ",";
        $query .= $db->escapeString(getFromSessionParams('reportsuid')) . ",";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportprimkey')) . "',";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportmainseid')) . "',";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportseid')) . "',";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportrgid')) . "',";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportdisplayed')) . "',";
        $query .= "'" . $db->escapeString(loadvar('reportcategory')) . "',";
        $query .= "'" . $db->escapeString(loadvar('reportcomment')) . "',";
        $query .= ISSUE_REPORTED . ", ";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportlanguage')) . "',";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportmode')) . "',";
        $query .= "'" . $db->escapeString(getFromSessionParams('reportversion')) . "'";
        $query .= ")";
        $db->executeQuery($query);

        $returnStr = $this->showHeader(Language::messageSMSTitle());
        $returnStr .= '<div id="wrap">';
        $returnStr .= '<div class="container"><p>';
        $returnStr .= $this->reportSub('<div class="alert alert-success">' . Language::reportProblemConfirmation() . '</div>'); //'<button onclick="window.close();" type="cancel" class="btn btn-default" style="min-width:100px">' . Language::buttonClose() . '</button>';
        $returnStr .= '                </div></div>';
        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showFooter();
        echo $returnStr;
    }

    function showHeader($title, $style = '') {

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

        if (determinedJavascriptEnabled() == false) {
            $returnStr .= '<noscript><meta http-equiv="refresh" content="0; URL=' . getURL() . '/nojavascript.php"></noscript>';
        }
        $returnStr .= '
    <!-- Bootstrap core CSS -->
		<link rel="stylesheet" type="text/css" href="../bootstrap/dist/css/bootstrap.min.css">

    <!-- Custom scripts and styles for this template -->';

        $returnStr .= '<script type="text/javascript" charset="utf-8" language="javascript" src="../bootstrap/assets/js/jquery.js"></script>';
        $returnStr .= '
    ' . $style . '

<script type="text/javascript">
    if(typeof window.history.pushState == \'function\') {
        window.history.pushState({}, "Hide", "index.php");
    }    
</script>
      
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../bootstrap/assets/js/html5shiv.js"></script>
      <script src="../bootstrap/assets/js/respond.min.js"></script>
    <![endif]-->
    <script src="../js/hover-dropdown.js"></script>
    <script type="text/javascript" src="../js/tooltip.js"></script>
    <script type="text/javascript" src="../js/popover.js"></script>    
    <script type="text/javascript" src="../js/modal.js"></script>';
        $returnStr .= '</head>
                    <body>
                    ';

        return $returnStr;
    }
    
    
    function displayDataTablesScripts($extensions = array()) {

        $returnStr = "";        
        if (!isRegisteredScript("../js/datatables/datatables.js")) {
            registerScript('../js/datatables/datatables.js');
            $returnStr .= getScript("../js/datatables/datatables.js");
        }
        
        if (!isRegisteredScript("../js/datatables/extensions/date_sorting.js")) {
            registerScript('../js/datatables/extensions/date_sorting.js');
            $returnStr .= getScript("../js/datatables/extensions/date_sorting.js");
        }
        
        if (!isRegisteredScript("../js/datetimepicker/moment-min.js")) {
            registerScript('../js/datetimepicker/moment-min.js');
            $returnStr .= getScript("../js/datetimepicker/moment-min.js");
        }
        
        if (!isRegisteredScript("../js/datatables/datatables.css")) {
            registerScript('../js/datatables/datatables.css');
            $returnStr .= getCSS("../js/datatables/datatables.css");
        }
        foreach ($extensions as $ext) {
            if (!isRegisteredScript("../js/datatables/extensions/' . $ext . '.js")) {
                registerScript('../js/datatables/extensions/' . $ext . '.js');
                $returnStr .= getScript("../js/datatables/extensions/' . $ext . '.js");
            }
            if (strtoupper($ext) != strtoupper('rowreorder')) { // reorder has no associated css
                if (!isRegisteredScript("../js/datatables/extensions/' . $ext . '.css")) {
                    registerScript('../js/datatables/extensions/' . $ext . '.css');
                    $returnStr .= getCSS("../js/datatables/extensions/' . $ext . '.css");
                }
            } else {
                if (!isRegisteredScript("../js/jqueryui/sortable.js")) {
                    registerScript('../js/jqueryui/sortable.js');
                    $returnStr .= getScript("../js/jqueryui/sortable.js");
                }
            }
        }

        /* https://datatables.net/forums/discussion/10437/fixedheader-column-headers-not-changing-on-window-resize/p1 */
        /* resize of header on window resize/empty/error */
        $returnStr .= '<script type="text/javascript">            
                        function resizeDataTables() {
                        $(\'div.dataTables_scrollBody table.dataTable\').each( function( index ) {
                        $(this).dataTable().fnAdjustColumnSizing();
                        });
                        }

                        $(window).on(\'resize\', function () {
                        resizeDataTables();
                        } );
                        </script>';
        return $returnStr;
    }

    function displayComboBox() {
        $str = '<script src="../js/bootstrap-select/bootstrap-select-min.js"></script>';
        $str .= '<link href="../css/bootstrap-select.css" type="text/css" rel="stylesheet">';
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

    function showFooter($fastLoad = true) {

        $returnStr = '
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->';
        if ($fastLoad) {
            $returnStr .= '<script src="../bootstrap/assets/js/jquery.js"></script>';
        }
        $returnStr .= '<script src="../bootstrap/dist/js/bootstrap.min.js"></script>';
        return $returnStr;
    }

}


?>