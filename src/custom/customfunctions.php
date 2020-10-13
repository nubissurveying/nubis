<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

//FULL QUESTION GOES THROUG THIS SURVEY BEFORE OUTPUT TO THE SCREEN
function lastParse($str) {
    $patterns = array('/((?i)\[IWER:(.*?)])/s');
    $replace = array('<br/><div class="alert alert-info" role="alert"><b>\2</b></div>');
    $str = preg_replace($patterns, $replace, $str);
    
    $patterns = array('/((?i)@I@\/IWER:(.*?)@I)/s');
    $replace = array('<br/><div class="alert alert-info" role="alert"><b>\2</b></div>');
    $str = preg_replace($patterns, $replace, $str);
    return $str;
}

// allows to never have certain variables set to dirty on going back
function getDefaultCleanVariables() {
    return array(
    );
}

function calcAge($year, $month, $day) {
    if ($year == '') {
        return null;
    }
    if ($month == '') {
        $month = 6;
    }
    if ($day == '') {
        $day = 15;
    }
    $age = floor((strtotime(date('Y-m-d')) - strtotime($year . '-' . $month . '-' . $day)) / 31556926);
    return $age;
}

function displayInOtherLanguage() {
    global $engine;
    $display = $engine->getDisplayObject();
    $old = getSurveyLanguage();
    setSessionParameter(SESSION_PARAM_LANGUAGE, 1);
    setSessionParameter(SESSION_PARAM_NEWLANGUAGE, null);
    $str = "<div id='uscic-otherlanguageview' class='uscic-wrap'><div class='container'>";
    $str .= '<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a data-toggle="tab" role="tab" href="#english">English</a></li>
  <li role="presentation" class=""><a href="#">Dutch</a></li>
</ul>';
    $vars = $engine->getDisplayed();
    $display->setLastParse(false);
    $display->setShowHeader(false);
    $display->setShowFooter(false);
    $engine->setRedoFills(true);

    $str .= "<div class='tab-content'><div id='english' class='tab-pane active'><div class='outershield'><div class='shield'></div>";
    $str .= $display->showQuestion($vars, $engine->getRgid(), $engine->getTemplate());
    $str .= "</div></div></div>";

    $str .= "</div></div>";
    $str .= "<script type='text/javascript'>";
    $str .= "$(document).ready(function() {      
            var bottom = $('#uscic-mainpanel').position().top+$('#uscic-mainpanel').outerHeight(true);
            $('#uscic-otherlanguageview').css('position', 'absolute');
            $('#uscic-otherlanguageview').css('left', $('#uscic-mainpanel').position().left);
            $('#uscic-otherlanguageview').css('width', $('#uscic-mainpanel').width());            
            $('#uscic-otherlanguageview').css('top', bottom+50);
            });";
    $str .= "</script>";
    setSessionParameter(SESSION_PARAM_LANGUAGE, $old);

    // do this here since we don't call lastparse
    $patterns = array('/((?i)\[IWER:(.*?)])/s');
    $replace = array('<br/><div class="alert alert-info" role="alert"><b>\2</b></div>');
    $str = preg_replace($patterns, $replace, $str);
    return $str;
}

/* ALWAYS TO BE PRESENT */

// function to get and set answers in an external location
function handleExternalStorageExample($type, $primkey, $variable, $answer = "", $dirty = "") {
    if ($type == STORE_EXTERNAL_GET) {
        // code to retrieve
    }
    else {
        // code to store
    }
}

function bottomDKRFNA() {
//return "";
    $str = "";
    $str .= "<hr><div style=''><center><h3>Individual DK/RF</h3>";
    global $engine, $survey;
    $str .= "<br/><br/><table><tr><td>DK: </td>";
    $str .= '<td><select multiple name="dk_list"' . ' id="dk_list" class="selectpicker show-tick">';
    $answernumbers = $engine->getDisplayNumbers();
    $display = $engine->getDisplayObject();
    $displayed = explode("~", $display->getRealVariables(explode("~", $engine->getDisplayed())));
    if (sizeof($displayed) == 1) {
        return "";
    }
    $cnt = 0;
    foreach ($displayed as $a) {
        if (!$engine->isInlineField($a)) {
            $cnt++;
        }
    }
    if ($cnt == 1) {
        return "";
    }

    foreach ($displayed as $a) {
        if ($engine->isInlineField($a)) {
            continue;
        }
        $var = $engine->getVariableDescriptive($a);
        $id = "" . $answernumbers[strtoupper($a)];
        $answertype = $var->getAnswerType();
        if ($answertype == ANSWER_TYPE_NONE) {
            continue;
        }
        if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED))) {
            $id .= "_name[]";
        } else if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN))) {
            $id .= "[]";
        }
        $ans = $engine->getAnswer($a);
        $selected = "";
        if (strtoupper($ans) == "DK") {
            $selected = "selected";
        }
        $str .= "<option " . $selected . " value='" . $id . "'>" . $a . " (" . substr($var->getQuestion(), 0, 40) . ")</option>";
    }
    $str .= "</select></td></tr>";
    $str .= '<tr><td>RF: </td><td><select multiple name="rf_list"' . ' id="rf_list" class="selectpicker show-tick">';
    $answernumbers = $engine->getDisplayNumbers();
    $display = $engine->getDisplayObject();
    $displayed = explode("~", $display->getRealVariables(explode("~", $engine->getDisplayed())));
    $scripts = array();
    foreach ($displayed as $a) {
        if ($engine->isInlineField($a)) {
            continue;
        }
        $var = $engine->getVariableDescriptive($a);
        $id = "" . $answernumbers[strtoupper($a)];
        $answertype = $var->getAnswerType();
        if ($answertype == ANSWER_TYPE_NONE) {
            continue;
        }

        if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED))) {
            $id .= "_name[]";
        } else if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN))) {
            $id .= "[]";
        }
        $ans = $engine->getAnswer($a);
        $selected = "";
        if (strtoupper($ans) == "RF") {
            $selected = "selected";
        }
        $str .= "<option " . $selected . " value='" . $id . "'>" . $a . " (" . substr($var->getQuestion(), 0, 40) . ")</option>";
    }
    $str .= "</select></td>";
    $str .= "</tr></table></center><br/><br/>";

    foreach ($displayed as $a) {
        $var = $engine->getVariableDescriptive($a);
        $ans = $engine->getAnswer($a);
        if (!inArray($ans, array(ANSWER_DK, ANSWER_RF, ANSWER_NA))) {
            $ans = "";
        } else {
            $scripts[] = "<script type='text/javascript'>
                            $(document).ready(function() {
                                $('[name=\"answer" . $answernumbers[strtoupper($a)] . "\"]').addClass(\"dkrfna\");
                            });    

                         </script>";
        }
        $id = "dkrf_answer" . $answernumbers[strtoupper($a)];
        $id2 = $id;
        $answertype = $var->getAnswerType();
        if ($answertype == ANSWER_TYPE_NONE) {
            continue;
        }

        if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED))) {
            $id .= "_name[]";
            $id2 .= "";
        } else if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN))) {
            $id .= "[]";
            $id2 .= "";
        }
        $str .= "<input type=hidden name='" . $id2 . "' id='" . $id2 . "' value='" . $ans . "'/ >";
    }

    // add select scripts
    $str .= implode("", $scripts);

    // add deselect dk/rf
    $str .= "<script type='text/javascript'>";
    foreach ($displayed as $a) {
        $var = $engine->getVariableDescriptive($a);
        $id = "dkrf_answer" . $answernumbers[strtoupper($a)];
        $id2 = $id;
        $answertype = $var->getAnswerType();
        if ($answertype == ANSWER_TYPE_NONE) {
            continue;
        }

        if (inArray($answertype, array(ANSWER_TYPE_SETOFENUMERATED))) {
            $id .= "_name[]";
            $id2 .= "";
        } else if (inArray($answertype, array(ANSWER_TYPE_MULTIDROPDOWN))) {
            $id .= "[]";
            $id2 .= "";
        }
        $str .= "$('[name=\"answer" . str_replace("dkrf_answer", "", $id) . "\"]').change(function() {
                    if ($('[name=\"answer" . str_replace("dkrf_answer", "", $id) . "\"]').attr(\"nochange\") == 1) {
			return;	
		    }
                    $('#" . $id2 . "').val('');
                    $('#dk_list option[value=\"" . str_replace("dkrf_answer", "", $id) . "\"]').prop('selected', false);                        
                    $('#rf_list option[value=\"" . str_replace("dkrf_answer", "", $id) . "\"]').prop('selected', false);                        
                    $('#dk_list').selectpicker('refresh');
                    $('#rf_list').selectpicker('refresh');
                    $('[name=\"answer" . str_replace("dkrf_answer", "", str_replace("[", '\[', str_replace("]", '\]', $id))) . "\"]').removeClass(\"dkrfna\");
                });";
        if ($answertype == ANSWER_TYPE_SLIDER) {
            $str .= "$('[id=\"answer" . str_replace("dkrf_answer", "", $id) . "_textbox\"]').keyup(function() {
                    $('#" . $id2 . "').val('');
                    $('#dk_list option[value=\"" . str_replace("dkrf_answer", "", $id) . "\"]').prop('selected', false);                        
                    $('#rf_list option[value=\"" . str_replace("dkrf_answer", "", $id) . "\"]').prop('selected', false);                        
                    $('#dk_list').selectpicker('refresh');
                    $('#rf_list').selectpicker('refresh');
                    $('[name=\"answer" . str_replace("dkrf_answer", "", str_replace("[", '\[', str_replace("]", '\]', $id))) . "\"]').removeClass(\"dkrfna\");
                });";
        }
    }
    $str .= "</script>";

    // add dk/rf handling    
    $str .= "<script type='text/javascript'>";
    $str .= "$('#dk_list').change(function() {
                var array = ($(this).val() + '').split(',');
                $('#dk_list option').each(function() {
                    var val2 = $(this).val();  
                    var val3 = val2.replace('[','').replace(']','');
                    val2 = val2.replace('[','\[').replace(']','\]');
                    var type = $('[name=\"answer' + val2 + '\"]').attr('type');
                    if (type == 'checkbox') {
                        val3 = val3.replace('_name','');
                    }
                    if (jQuery.inArray(val2, array) != -1) {                                                                
                        $('#dkrf_answer' + val3).val('DK');
                        $('[name=\"answer' + val2 + '\"]').addClass(\"dkrfna\");
                        $('#rf_list option[value=\"' + val2 + '\"]').prop('selected', false);                        
                        if (type == 'text') {
                            if ($('[name=\"answer' + val2 + '\"]').hasClass('bootstrapslider')) {
                                var x = $('[name=\"answer' + val2 + '\"]').slider();
                                x.slider('setValue', '');
                                $('[id=\"answer' + val2 + '_textbox\"]').val('');
                            }
                            else {
                                $('[name=\"answer' + val2 + '\"]').val('');    
                            }
                        }
                        else if (type == 'radio') {
                            if ($('[name=\"answer' + val2 + '\"]').hasClass('selectpicker')) {
                                $('[name=\"answer' + val2 + '\"]').attr(\"nochange\",\"1\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker(\"val\",\"\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker('refresh');
				$('[name=\"answer' + val2 + '\"]').removeAttr(\"nochange\");

                            }
                            else {
                                $('[name=\"answer' + val2 + '\"]').prop('checked', false);
                            }
                        }  
                        else if (type == 'checkbox') {
                            $('[name=\"answer' + val2 + '\"]').removeAttr('checked');
                        }
                        else if ($('[name=\"answer' + val2 + '\"]').is('select')) {
                            $('[name=\"answer' + val2 + '\"]').attr(\"nochange\",\"1\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker(\"val\",\"\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker('refresh');
				$('[name=\"answer' + val2 + '\"]').removeAttr(\"nochange\");

                        }
                    }   
                    else {
                        if ($('#dkrf_answer' + val3).val() == 'DK') {
                            $('[name=\"answer' + val2 + '\"]').removeClass(\"dkrfna\");
                            $('#dkrf_answer' + val3).val('');                        
                        }    
                    }
                });
                $('#rf_list').selectpicker('refresh');
            });";
    $str .= "$('#rf_list').change(function() {
                var array = ($(this).val() + '').split(',');
                $('#rf_list option').each(function() {
                    var val2 = $(this).val();  
                    var val3 = val2.replace('[','').replace(']','');
                    val2 = val2.replace('[','\[').replace(']','\]');
                    var type = $('[name=\"answer' + val2 + '\"]').attr('type');
                    if (type == 'checkbox') {
                        val3 = val3.replace('_name','');
                    }
                    if (jQuery.inArray(val2, array) != -1) {                                                                
                        $('#dkrf_answer' + val3).val('RF');
                        $('[name=\"answer' + val2 + '\"]').addClass(\"dkrfna\");
                        $('#dk_list option[value=\"' + val2 + '\"]').prop('selected', false);                        
                        if (type == 'text') {
                            if ($('[name=\"answer' + val2 + '\"]').hasClass('bootstrapslider')) {
                                var x = $('[name=\"answer' + val2 + '\"]').slider();
                                x.slider('setValue', '');
                                $('[id=\"answer' + val2 + '_textbox\"]').val('');
                            }
                            else {
                                $('[name=\"answer' + val2 + '\"]').val('');    
                            }
                        }
                        else if (type == 'radio') {
                            if ($('[name=\"answer' + val2 + '\"]').hasClass('selectpicker')) {
                                $('[name=\"answer' + val2 + '\"]').attr(\"nochange\",\"1\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker(\"val\",\"\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker('refresh');
				$('[name=\"answer' + val2 + '\"]').removeAttr(\"nochange\");

                            }
                            else {
                                $('[name=\"answer' + val2 + '\"]').prop('checked', false);
                            }
                        }  
                        else if (type == 'checkbox') {
                            $('[name=\"answer' + val2 + '\"]').removeAttr('checked');
                        }
                        else if ($('[name=\"answer' + val2 + '\"]').is('select')) {
                            $('[name=\"answer' + val2 + '\"]').attr(\"nochange\",\"1\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker(\"val\",\"\");
                            $('[name=\"answer' + val2 + '\"]').selectpicker('refresh');
				$('[name=\"answer' + val2 + '\"]').removeAttr(\"nochange\");

                        }
                    }   
                    else {
                        if ($('#dkrf_answer' + val3).val() == 'RF') {
                            $('[name=\"answer' + val2 + '\"]').removeClass(\"dkrfna\");
                            $('#dkrf_answer' + val3).val('');                        
                        }    
                    }
                });
                $('#dk_list').selectpicker('refresh');
            });";
    $str .= "</script></div>";
    return $str;
}

/* blaise functions */
function str($str) {
    return $str;
}

function substring($str, $start, $end) {
    return substr($str, $start, $end);
}

function getYearFromDateTime($date) {
    $ex = explode("-", $date);
    return trim($ex[0]);
}

?>