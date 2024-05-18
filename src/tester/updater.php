<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Updater {

    private $engine;

    function __construct() {
        $primkey = getFromSessionParams('watchprimkey');
        $seid = getSurveySection(getFromSessionParams('watchsuid'), $primkey);
        $this->engine = loadEngine(getFromSessionParams('watchsuid'), $primkey, "", getSurveyVersion(), $seid);
    }

    function update() {
        $returnStr = $this->showHeader(Language::messageSMSTitle());
        $returnStr .= $this->displayComboBox();
        $returnStr .= '<div id="wrap">';
        $returnStr .= '<div class="container"><p>';

        $returnStr .= '<div class="panel panel-default">
                <div class="panel-heading">';
        $returnStr .= '<h4>' . Language::linkUpdate() . '</h4>';
        $returnStr .= '                </div>
                <div class="panel-body">';

        require_once('data.php');
        require_once('display.php');

        $data = new Data();
        $values = $data->getRespondentData(getFromSessionParams('watchsuid'), getFromSessionParams('watchprimkey'));

        $returnStr .= '</div>
    <div role="panel" class="panel">';

        if (sizeof($values) == 0) {
            $returnStr .= "<br/>" . '<div class="alert alert-warning">' . Language::labelWatchNoData() . '</div>';
        } else {

            $display = $this->engine->getDisplayObject();
            $returnStr .= $display->displayDataTablesScripts(array("colvis", "rowreorder"));
            $returnStr .= "<script type='text/javascript'>
                        $(document).ready(function(){
                            $('#issuetable').dataTable(
                                {
                                    \"iDisplayLength\": 4,
                                    dom: 'C<\"clear\">lfrtip',
                                    searching: false,
                                    paging: true
                                    }    
                                );
                                         
                       });</script>

                        "; //

            $returnStr .= "<br/><table id='issuetable' class='table table-bordered table-striped'><thead>";
            $returnStr .= "<th>" . Language::labelUpdateVariable() . "</th><th>" . Language::labelUpdateQuestion() . "</th><th>" . Language::labelUpdateCurrent() . "</th><th>" . Language::labelUpdateChangeTo() . "</th></tr>";
            $returnStr .= "</thead><tbody>";
            $modes = Common::surveyModes();
            $languages = Language::getLanguagesArray();
            $sesid = session_id();
            require_once('object.php');
            require_once('component.php');
            require_once('setting.php');
            require_once('type.php');
            require_once('variabledescriptive.php');
            global $survey;
            $survey = new Survey(getFromSessionParams('watchsuid'));
            $cnt = 1;
            $params = getSessionParams();
            $params[SESSION_PARAM_SURVEY] = getFromSessionParams('watchsuid'); // add for getSurvey
            $params['testpage'] = 'updateRes';
            $paramstring = setSessionParamsPost($params);
            foreach ($values as $is) {
                $var = $survey->getVariableDescriptiveByName($is["variablename"]);
                $returnStr .= "<tr>";
                $returnStr .= "<td>" . $is["variablename"] . "</td>";
                $returnStr .= "<td>" . $var->getQuestion() . "</td>";
                $returnStr .= "<td>" . $this->getDisplayValue($var, $is["answer_dec"]) . "</td>";
                $returnStr .= "<td>" . $this->displayInput($paramstring, $is["variablename"], $var, $is["answer_dec"], $cnt) . "</td>";
                $returnStr .= "</tr>";
                $cnt++;
            }
            $returnStr .= "</tbody></table>";
        }

        $returnStr .= '</div>
  </div>
</div>';

        $returnStr .= '                </div></div>';
        $returnStr .= '</p></div>    </div>'; //container and wrap

        $returnStr .= $this->showFooter(false);
        echo $returnStr;
    }

    function displayInput($paramstring, $variable, $var, $previousdata, $cnt) {
        if ($var->getVsid() == "") {
            return "";
        }
        $display = $this->engine->getDisplayObject();
        $returnStr .= "<form method=post id='form" . $cnt . "' name='form" . $cnt . "'>";
        $returnStr .= $paramstring;        
        $returnStr .= $display->showAnswer($cnt, $variable, $var, $previousdata, false, "");        
        $returnStr .= "<input type=hidden name='cnt' value='" . $cnt . "'>";
        $returnStr .= "<input type=hidden name='updatevariable" . $cnt . "' value='" . $variable . "'>";
        $returnStr .= "<button onclick=\" $('#form" . $cnt . "').submit();\" type=button class='btn btn-default' >" . Language::buttonUpdate() . "</button>";
        $returnStr .= "</form>";
        return $returnStr;
    }

    function getDisplayValue($var, $value) {

        if ($var) {
            $type = $var->getAnswerType();
            switch ($type) {
                case ANSWER_TYPE_OPEN:
                    return $value;
                    break;
                case ANSWER_TYPE_STRING:
                    return $value;
                    break;
                case ANSWER_TYPE_DROPDOWN:
                /* fall through */
                case ANSWER_TYPE_ENUMERATED:
                    return $var->getOptionLabel($value);
                    break;
                case ANSWER_TYPE_MULTIDROPDOWN:
                /* fall through */
                case ANSWER_TYPE_RANK:
                /* fall through */    
                case ANSWER_TYPE_SETOFENUMERATED:
                    return $var->getSetOfEnumeratedOptionLabel($value);
                    break;
                case ANSWER_TYPE_INTEGER:
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                /* fall through */    
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */    
                case ANSWER_TYPE_DOUBLE:
                    return $value;
                    break;
                default:
                    return $value;
            }
        }
        return "";
    }

    function updateRes() {
        global $survey, $engine;
        $engine = $this->engine;
        $survey = new Survey(getFromSessionParams('watchsuid'));
        $var = $survey->getVariableDescriptiveByName($is["variablename"]);
        $cnt = loadvar("cnt");
        $r = $this->engine->getAnswer(loadvar("updatevariable" . $cnt));
        $this->engine->setAnswer(loadvar("updatevariable" . $cnt), loadvar("answer" . $cnt), $this->engine->getDirty(loadvar("updatevariable" . $cnt)));
        $this->engine->getState()->saveState();
        $this->update();
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