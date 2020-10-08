<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.
  
  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class EnumeratedTableTemplate extends TableTemplate {

    private $first;
    private $dkrfna;
    private $role;
    private $returnStrAdd;

    function __construct($engine, $group) {

        parent::__construct($engine, $group);
    }
    
    function show($variables, $realvariables, $language) {

        $this->variables = $variables;

        $this->realvariables = $realvariables;
        $this->returnStrAdd = "";
        $this->language = $language;
        $returnStr = "";
        if ($this->group->isTableMobile() == true) {
            $returnStr = $this->displayobject->displayTableSaw();
        }
        $returnStr .= $this->enumeratedTable();
        return $returnStr;
    }

    function enumeratedTable() {

        $pt = $this->group->getParentGroup()->getTemplate();
        $current = $this->group->getTemplate();        
        $this->group->setEnumeratedFirst(1);

        /* add error checks */
        $this->addErrorChecks();

        /* start table */
        $width = $this->engine->replaceFills($this->group->getTableWidth());
        //$style = "style='width: " . $width . "%; max-width: " . $width . "%'";
        $id = $this->group->getTableId();
        if (trim($id) == "") {
            $id = 'table_' . $this->group->getName() . mt_rand(0, 10000);
        }
        
        $returnStr = "";
        
        // accessibility
        $legend = "";
        $this->role = "";
        if (Config::useAccessible()) {
            $legend = 'legend_' . $id;
            $this->role = 'role="radio" aria-labelledby="legend_' . $id . '" ';
            $returnStr .= "<fieldset class='nubis-accessible-fieldset' role='group'>";
            $returnStr .= "<legend id='legend_" . $id . "' class='nubis-accessible-legend'>Options</legend>";
        }
        
        if ($pt != $current) {
            $returnStr .= '<div id="TGroup_' . implode("_", $this->realvariables) . '">';
        }
        
        //$id = "example";
        if ($pt != $current) {
            $nolabels = "data-tablesaw-postappend";
            if ($this->group->isTableMobileLabels() == false) {
                $nolabels = "data-tablesaw-no-labels";
            }
            $returnStr .= '<table ' . $nolabels . ' data-tablesaw-mode="stack" id="' . $id . '" class="tablesaw tablesaw-stack table' . $this->striped . $this->bordered . $this->hovered . $this->condensed . ' uscic-table-enumerated">';
        }

        /* check for dk/rf/na */
        for ($i = 0; $i < sizeof($this->variables); $i++) {
            $variable = $this->variables[$i];
            $var = $this->engine->getVariableDescriptive($variable);
            if ($var->getShowDKButton() == BUTTON_YES) {
                $this->dkrfna = true;
                break;
            } else if ($var->getShowRFButton() == BUTTON_YES) {
                $this->dkrfna = true;
                break;
            } else if ($var->getShowNAButton() == BUTTON_YES) {
                $this->dkrfna = true;
                break;
            }
        }

        /* build table */
        $headername = "";
        $headervar = null;
        for ($i = 0; $i < sizeof($this->variables); $i++) {

            $variable = $this->variables[$i];
            if (startsWith($variable, ROUTING_IDENTIFY_SUBGROUP)) {
                $returnStr .= $this->displayobject->showSubGroupQuestions($variable, $this->group);
                $i = $this->findEndSubGroup($this->variables, $i); // skip until the end of the sub group, and continue display from there
            } else {

                $var = $this->engine->getVariableDescriptive($variable);

                // use first question we find for the table header (if subgroup, then add if not added yet in enclosing group)
                if ($pt != $current || $this->group->getParentGroup()->isEnumeratedFirst() == true) {

                    if ($this->group->isEnumeratedFirst() == true) {

                        if ($this->group->isHeaderFixed()) {
                            $this->displayobject->useDataTables();
                            $width = 0;
                            if ($this->group->isTableMobile() == true) {
                                $width = 639; // below 640 tablesaw kicks in
                            }

                            $returnStr .= '<script type="text/javascript">
                                
                                        function handleDisplay() {
                                            var height = $(window).height();
                                            var width = $(window).width();
                                            var tableheight = $(\'#' . $id . '\').height();
                                            if (width > ' . $width . ' && height < tableheight) {
                                            $(\'#' . $id . '\').dataTable( {
                                                "destroy": true,
                                                "sScrollY":       height,
                                                "scrollCollapse": true,
                                                "paging":         false,
                                                "ordering":       false,
                                                "info":     false,
                                                "filter":   false
                                            } );

                                            }
                                        }
                                        
                                        $(document).ready(function() {
                                            handleDisplay();
                                        });
                                        
                                        $(window).resize(function() {
                                            if ($.fn.DataTable.isDataTable(\'#' . id . '\')) {
                                                $(\'#' . $id . '\').dataTable().fnDestroy();
                                            }
                                            handleDisplay();
                                        });
                                    
                                    </script>';
                        }
                        
                        $headername = $variable;
                        $headervar = $var;
                        $returnStr .= $this->showEnumeratedHeader($variable, $var);
                        $returnStr .= "<tbody>";
                    }
                }

                /* only display non-inline fields */
                if ($this->engine->isInlineField($variable) == false) {
                    $returnStr .= "<tr class='uscic-table-row-enumerated'>";

                    /* question text */
                    $text = $this->displayobject->showQuestionText($variable, $var, "uscic-question-table-row");
                    $returnStr .= "<td class='uscic-table-row-question-cell-enumerated'>" . $text . "</td>";

                    /* answer element(s) */
                    $cnt = $this->displaynumbers[strtoupper($variable)];
                    if (inArray($var->getAnswerType(), array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {

                        /* answer input element */
                        $previousdata = $this->engine->getAnswer($variable);

                        /* enumerated/set of enumerated */
                        $returnStr .= $this->showEnumerated($cnt, $variable, $var, $previousdata);
                    } else {
                        $returnStr .= "<td class='uscic-table-row-cell'></td>";
                    }

                    $returnStr .= "</tr>";
                }

                /* update 'first' indicator */
                $this->group->setEnumeratedFirst(2);
            }
        }


        if ($pt != $current) {
            $returnStr .= "</tbody>";
        }
        
        // footer below
        if ($this->group->isFooterDisplay() && $headervar != null) {
            $returnStr .= "<tfooter>";
            $returnStr .= $this->showEnumeratedHeader($headername, $headervar);
            $returnStr .= "</tfooter>";           
        }
        $returnStr .= "</table></div>";
        
        $returnStr .= $this->returnStrAdd;
        
        if (Config::useAccessible()) {
            $returnStr .= "</fieldset>";
        }

        return $returnStr;
    }

    function showEnumeratedHeader($variable, $var) {

        $options = $this->engine->getFill($variable, $var, SETTING_OPTIONS);

        $orderedoptions = $options;
        $order = $this->engine->getFill($variable, $var, SETTING_ENUMERATED_RANDOMIZER);
        if ($order != "") {
            //$arr = explode(",", $this->engine->getAnswer($order));
            $arr = $this->engine->getAnswer($order);
            //print_r($arr);
            if (is_array($arr) && sizeof($arr) > 0) {
                $orderedoptions = array();
                foreach ($arr as $a) {
                    foreach ($options as $option) {
                        if ($option["code"] == $a) {
                            $orderedoptions[] = $option;
                            break;
                        }
                    }
                }
            }
        }

        /* column for dk/rf/na */
        if ($this->dkrfna == true) {
            $orderedoptions[] = "";
        }

        $returnStr = "<thead><tr class='uscic-table-row-header-enumerated'><th class='uscic-table-row-cell-header-enumerated'>&nbsp;</th>";

        $align = $this->group->getHeaderAlignment();
        $qa = "";
        switch ($align) {
            case ALIGN_LEFT:
                $qa = "text-left";
                break;
            case ALIGN_RIGHT:
                $qa = "text-right";
                break;
            case ALIGN_JUSTIFIED:
                $qa = "text-justify";
                break;
            case ALIGN_CENTER:
                $qa = "text-center";
                break;
            default:
                break;
        }

        /* calculate cell width */
        $noofcolumns = sizeof($orderedoptions);
        $this->cellwidth = "style='width: " . round(($this->engine->replaceFills($this->group->getTableWidth()) - $this->engine->replaceFills($this->group->getQuestionColumnWidth())) / $noofcolumns) . "%;'";

        foreach ($orderedoptions as $option) {
            if (trim($option["label"] == "")) {
                continue;
            }
            $returnStr .= "<th class='uscic-table-row-cell-header-enumerated'><div class='" . $qa . "'><span id='vsid_option" . $var->getVsid() . $option["code"] . "' uscic-target='vsid_" . $var->getVsid() . "' uscic-answercode='" . $option["code"] . "' uscic-texttype='" . SETTING_OPTIONS . "' class='" . $this->displayobject->inlineeditable . "'>" . $this->displayobject->applyFormatting($option["label"], $this->group->getHeaderFormatting()) . "</span></div></th>";
        }

        $returnStr .= "</tr></thead>";

        return $returnStr;
    }

    function showEnumerated($number, $variable, $var, $previousdata) {

        $returnStr = "";

        $varname = SESSION_PARAMS_ANSWER . $number;

        $id = $this->engine->getFill($variable, $var, SETTING_ID);
        if (trim($id) == "") {
            $id = $varname;
        }

        /* get ids for inline field error checking */
        $ids = array();
        $options = $this->engine->getFill($variable, $var, SETTING_OPTIONS);
        $orderedoptions = $options;
        $order = $this->engine->getFill($variable, $var, SETTING_ENUMERATED_RANDOMIZER);
        if ($order != "") {
            //$arr = explode(",", $this->engine->getAnswer($order));
            $arr = $this->engine->getAnswer($order);
            //print_r($arr);
            if (is_array($arr) && sizeof($arr) > 0) {
                $orderedoptions = array();
                foreach ($arr as $a) {
                    foreach ($options as $option) {
                        if ($option["code"] == $a) {
                            $orderedoptions[] = $option;
                            break;
                        }
                    }
                }
            }
        }
        foreach ($orderedoptions as $option) {
            if (trim($option["label"] != "")) {
                $ids[] = $id . '_' . $option["code"];
            }
        }

        $at = $var->getAnswerType();
        if (inArray($at, array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
            $varname .= "[]";
        }

        if ($var->getIfEmpty() != IF_EMPTY_ALLOW) {
            if (inArray($at, array(ANSWER_TYPE_SETOFENUMERATED))) { // custom name for set of enumerated question, since we use a hidden field/textbox to track the real answer(s); we just use this custom name for referencing in the error checking
                $this->displayobject->addErrorCheck(SESSION_PARAMS_ANSWER . $number . "_name[]", $variable, new ErrorCheck(ERROR_CHECK_REQUIRED, "true"), $this->engine->getFill($variable, $var, SETTING_EMPTY_MESSAGE));
            } else {
                $this->displayobject->addErrorCheck($varname, $variable, new ErrorCheck(ERROR_CHECK_REQUIRED, "true"), $this->engine->getFill($variable, $var, SETTING_EMPTY_MESSAGE));
            }
        }



        $inlinejavascript = $this->engine->getFill($variable, $var, SETTING_JAVASCRIPT_WITHIN_ELEMENT);

        $align = $this->group->getHeaderAlignment();
        $qa = "";
        switch ($align) {
            case ALIGN_LEFT:
                $qa = "text-left";
                break;
            case ALIGN_RIGHT:
                $qa = "text-right";
                break;
            case ALIGN_JUSTIFIED:
                $qa = "text-justify";
                break;
            case ALIGN_CENTER:
                $qa = "text-center";
                break;
            default:
                break;
        }

        switch ($at) {

            case ANSWER_TYPE_ENUMERATED: //enumerated

                $this->displayobject->addComparisonChecks($var, $variable, $varname);
                $dkrfna = $this->displayobject->addDKRFNAButton($varname, $var, $variable);
                $dkrfnaclass = "";
                if ($dkrfna != "") {
                    if ($this->engine->isDKAnswer($variable)) {
                        $dkrfnaclass = "dkrfna";
                    } else if ($this->engine->isRFAnswer($variable)) {
                        $dkrfnaclass = "dkrfna";
                    } else if ($this->engine->isNAAnswer($variable)) {
                        $dkrfnaclass = "dkrfna";
                    }
                }

                $this->engine->getDisplayObject()->addInlineFieldChecks($varname, $variable, $var, $ids);
                foreach ($orderedoptions as $option) {

                    if (trim($option["label"]) != "") {
                        $selected = ' aria-checked="false"';

                        if ($option["code"] == $previousdata) {

                            $selected = ' CHECKED aria-checked="true"';
                        }
                        
                        $disabled = '';
                        $disclass = '';
                        if ($this->engine->getDisplayObject()->isEnumeratedActive($variable, $var, $option["code"]) == false) {
                            $disabled = ' disabled ';
                            $disclass = ' disabled ';
                        }

                        $returnStr .= '<td id="cell' . $id . '_' . $option["code"] . '" class="uscic-table-row-cell-enumerated' . $disclass . '" ' . $this->cellwidth . '><div class="' . $qa . '">

                                        <label style="display: none;" for="' . $id . '_' . $option["code"] . '" class="uscic-table-enumerated-label">.</label>

                                            <div class="form-group uscic-table-row-cell-form-group">

                                                <input ' . $disabled . $this->role . ' class="uscic-radio-table ' . $dkrfnaclass . '" ' . $this->displayobject->getErrorTextString($varname) . ' ' . $inlinejavascript . ' type=radio id="' . $id . '_' . $option["code"] . '" name="' . $varname . '" value="' . $option["code"] . '" ' . $selected . '>

                                            </div>

                                         

                                        </div>';
                        $returnStr .= $this->displayobject->displayRadioButtonScript($var, $id . '_' . $option["code"], true);
                        $returnStr .= '
                                        </td>

                                        ';
                    } else {
                        //$returnStr .= '<td class="uscic-table-row-cell-enumerated"></td>';
                    }
                }

                // dk/rf/na                
                if ($dkrfna != '') {
                    $returnStr .= '<td class="uscic-table-row-cell-enumerated-dkrfna">' . $dkrfna . "</td>";
                } else if ($this->dkrfna == true) {
                    $returnStr .= '<td class="uscic-table-row-cell-enumerated"></td>';
                }

                /* done */
                break;

            case ANSWER_TYPE_SETOFENUMERATED: //set of enumerated

                $realvarname = $varname;
                $this->role = str_replace("radio", "checkbox", $this->role);
                
                /* we will have a text box entry OR a hidden field that tracks the entries, so that will be the real variable we look at in POST */
                $varname = $id . "_name[]";

                $this->engine->getDisplayObject()->addSetOfEnumeratedChecks($varname, $variable, $var, ANSWER_TYPE_SETOFENUMERATED);
                $this->engine->getDisplayObject()->addInlineFieldChecks($varname, $variable, $var, $ids);

                $dkrfna = $this->displayobject->addDKRFNAButton(substr($realvarname, 0, strlen($realvarname) - 2), $var, $variable, false, '', $id);
                $dkrfnaclass = "";
                if ($dkrfna != "") {
                    if ($this->engine->isDKAnswer($variable)) {
                        $dkrfnaclass = "dkrfna";
                    } else if ($this->engine->isRFAnswer($variable)) {
                        $dkrfnaclass = "dkrfna";
                    } else if ($this->engine->isNAAnswer($variable)) {
                        $dkrfnaclass = "dkrfna";
                    }
                }

                $options = $this->engine->getFill($variable, $var, SETTING_OPTIONS);
                foreach ($orderedoptions as $option) {

                    if (trim($option["label"]) != "") {
                        
                        $selected = ' aria-checked="false"';

                        if (inArray($option["code"], explode(SEPARATOR_SETOFENUMERATED, $previousdata))) {

                            $selected = ' CHECKED aria-checked="true"';
                        }
                        
                        $disabled = '';
                        $disclass = '';
                        if ($this->engine->getDisplayObject()->isEnumeratedActive($variable, $var, $option["code"]) == false) {
                            $disabled = ' disabled ';
                            $disclass = ' disabled ';
                        }

                        $returnStr .= '<td id="cell' . $id . '_' . $option["code"] . '" class="uscic-table-row-cell-setofenumerated' . $disclass . '" ' . $this->cellwidth . '><div class="' . $qa . '">

                                        <label style="display: none;" for="' . $id . '_' . $option["code"] . '" class="uscic-table-enumerated-label">.</label>

                                        <div class="form-group uscic-table-row-cell-form-group">

                                        <input ' . $disabled . $this->role . ' class="uscic-checkbox-table ' . $dkrfnaclass . '" ' . $this->displayobject->getErrorTextString($varname) . ' ' . $inlinejavascript . ' type="checkbox" id="' . $id . '_' . $option["code"] . '" name="' . $varname . '" value="' . $option["code"] . '" ' . $selected . '>

                                        </div>

                                        

                                   </div>';
                        $returnStr .= '<script type="text/javascript">$( document ).ready(function() {

                                                $("#' . $id . '_' . $option["code"] . '").click(function (e) {     
                                                    e.stopPropagation();    
                                                });

                                                    $("#cell' . $id . '_' . $option["code"] . '").click(function (e) {  
                                                        e.preventDefault();
                                                                          if ($("#' . $id . '_' . $option["code"] . '").prop("checked")) {
                                                                              $("#' . $id . '_' . $option["code"] . '").prop("checked", false);
                                                                          }      
                                                                          else {

                                                                            $("#' . $id . '_' . $option["code"] . '").prop("checked", true);
                                                                          }      
                                                                          $("#' . $id . '_' . $option["code"] . '").change();
                                                                          if ($("#' . $id . '_' . $option["code"] . '")[0]) {    
                                                                            $("#' . $id . '_' . $option["code"] . '")[0].onclick(); // trigger any onclick event handler    
                                                                          }      
                                                                          e.stopPropagation();  

                                                                          });
                                                                         });</script>
                                    </td>';
                    } else {
                        $returnStr .= '<td class="uscic-table-row-cell-setofenumerated"></td>';
                    }
                }

                // dk/rf/na                
                if ($dkrfna != '') {
                    $returnStr .= '<td class="uscic-table-row-cell-enumerated-dkrfna">' . $dkrfna . "</td>";
                } else if ($this->dkrfna == true) {
                    $returnStr .= '<td class="uscic-table-row-cell-enumerated"></td>';
                }

                /* add hidden field to track answers */
                $this->returnStrAdd .= $this->displayobject->addSetOfEnumeratedHidden($variable, $var, $realvarname, $varname, $id, $previousdata);

                /* add unchecking code */
                if ($var->isInputMaskEnabled()) {
                    $this->returnStrAdd .= $this->displayobject->displayCheckBoxUnchecking($id, $var->getInvalidSubSelected());
                }

                /* done */
                break;
        }

        return $returnStr;
    }

}

?>