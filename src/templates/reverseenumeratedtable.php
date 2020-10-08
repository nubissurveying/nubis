<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class ReverseEnumeratedTableTemplate extends TableTemplate {

    private $first;
    private $role;

    function __construct($engine, $group) {

        parent::__construct($engine, $group);
    }

    function show($variables, $realvariables, $language) {

        $this->variables = $variables;

        $this->realvariables = $realvariables;

        $this->language = $language;

        $returnStr = "";
        if ($this->group->isTableMobile() == true) {
            $returnStr = $this->displayobject->displayTableSaw();
        }
        $returnStr .= $this->enumeratedTableReverse();

        return $returnStr;
    }

    function enumeratedTableReverse() {

        $pt = $this->group->getParentGroup()->getTemplate();
        $this->group->setEnumeratedFirst(1);

        /* add error checks */
        $this->addErrorChecks();

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

        if ($pt != $this->group->getTemplate()) {
            $returnStr .= '<div id="TGroup_' . implode("_", $this->realvariables) . '">';
        }

        /* start table */
        $width = $this->engine->replaceFills($this->group->getTableWidth());
        $style = "style='width: " . $width . "%; max-width: " . $width . "%'";
        $id = $this->group->getTableId();
        if (trim($id) == "") {
            $id = 'table_' . $this->group->getName() . mt_rand(0, 10000);
        }
        if ($pt != $this->group->getTemplate()) {
            $nolabels = "data-tablesaw-postappend";
            if ($this->group->isTableMobileLabels() == false) {
                $nolabels = "data-tablesaw-no-labels";
            }
            $returnStr .= '<table ' . $nolabels . ' data-tablesaw-mode="stack" id="' . $id . '" ' . $style . ' class="tablesaw tablesaw-stack table' . $this->striped . $this->bordered . $this->hovered . $this->condensed . ' uscic-table-enumerated">';
        }

        /* collect headers and options from first question */
        $headers = array();
        $orderedoptions = array();
        $variable = $this->variables[0];
        $var = $this->engine->getVariableDescriptive($variable);

        // get formatting
        $qf = $var->getQuestionFormatting();
        $questionalign = $var->getQuestionAlignment();

        /* get headers */
        for ($i = 0; $i < sizeof($this->variables); $i++) {
            $variable = $this->variables[$i];
            $var = $this->engine->getVariableDescriptive($variable);

            /* only display non-inline fields */
            if ($this->engine->isInlineField($variable) == false) {
                $headers[] = $this->engine->getFill($variable, $var, SETTING_QUESTION); //$this->displayobject->showQuestionText($variable, $var, "uscic-question-table-header");
            }
        }

        /* get ordered options */
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

        /* build table */
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

        $returnStr .= $this->showEnumeratedHeader($var, $headers);
        $returnStr .= "<tbody>";

        // add options in column style
        $counter = 1;
        $idarray = array();
        foreach ($orderedoptions as $option) {

            $currentcode = $option["code"];

            // start row
            $returnStr .= "<tr class='uscic-table-row-enumerated'>";

            // add option text
            $text = $this->showQuestionText($var, $option["code"], $option["label"], $questionalign, $qf, "uscic-question-table-row");
            $returnStr .= "<td class='uscic-table-row-question-cell-enumerated'>" . $text . "</td>";

            // go through all fields
            for ($i = 0; $i < sizeof($this->variables); $i++) {

                $variable = $this->variables[$i];

                /* only display non-inline fields */
                if ($this->engine->isInlineField($variable) == false) {

                    $var = $this->engine->getVariableDescriptive($variable);
                    $cnt = $this->displaynumbers[strtoupper($variable)];

                    /* answer element(s) */
                    if (inArray($var->getAnswerType(), array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED))) {

                        /* collect ids for inline checking */
                        if (isset($idarray[strtoupper($variable)])) {
                            $ids = $idarray[strtoupper($variable)];
                        } else {
                            $ids = array();
                        }

                        $varname = SESSION_PARAMS_ANSWER . $cnt;
                        $id = $this->engine->getFill($variable, $var, SETTING_ID);
                        if (trim($id) == "") {
                            $id = $varname;
                        }

                        $ids[] = $id . '_' . $option["code"];
                        $idarray[strtoupper($variable)] = $ids;


                        /* answer input element */
                        $previousdata = $this->engine->getAnswer($variable);

                        /* enumerated/set of enumerated */
                        $firsttime = false;
                        if ($counter == 1) {
                            $firsttime = true;
                        }
                        $returnStr .= $this->showEnumeratedOption($option, $cnt, $variable, $var, $firsttime, $previousdata);
                    } else {
                        $returnStr .= "<td class='uscic-table-row-cell'></td>";
                    }
                }
            }

            // end row
            $counter++;
            $returnStr .= "</tr>";
        }

        // add inline select code
        for ($i = 0; $i < sizeof($this->variables); $i++) {

            $variable = $this->variables[$i];
            $var = $this->engine->getVariableDescriptive($variable);

            /* only display non-inline fields */
            if ($this->engine->isInlineField($variable) == false) {
                $ids = array();
                if (isset($idarray[strtoupper($variable)])) {
                    $ids = $idarray[strtoupper($variable)];
                }

                $cnt = $this->displaynumbers[strtoupper($variable)];
                $varname = SESSION_PARAMS_ANSWER . $cnt;
                //echo $varname;
                //print_r($ids);
                $this->engine->getDisplayObject()->addInlineFieldChecks($varname, $variable, $var, $ids);
            }
        }

        $returnStr .= "</tbody></table></div>";

        if (Config::useAccessible()) {
            $returnStr .= "</fieldset>";
        }
        return $returnStr;
    }

    function showQuestiontext($var, $code, $text, $questionalign, $qf, $class = "uscic-question") {

        if (trim($text) == "") {
            return "";
        }

        $qa = "";
        switch ($questionalign) {
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
        $beginformat = "";
        $endformat = "";
        $questionformat = explode("~", $qf);
        if (inArray(FORMATTING_BOLD, $questionformat)) {
            $beginformat .= "<b>";
            $endformat .= "</b>";
        }
        if (inArray(FORMATTING_ITALIC, $questionformat)) {
            $beginformat .= "<i>";
            $endformat .= "</i>";
        }
        if (inArray(FORMATTING_UNDERLINED, $questionformat)) {
            $beginformat .= "<u>";
            $endformat .= "</u>";
        }

        $returnStr = '<div class="' . $class . ' ' . $qa . '"><span id="vsid_option' . $var->getVsid() . $code . '" uscic-target="vsid_' . $var->getVsid() . '" uscic-answercode="' . $code . '" uscic-texttype="' . SETTING_OPTIONS . '" class="' . $this->displayobject->inlineeditable . '">' . $this->displayobject->applyFormatting($text, $qf) . "</span></div>";
        return $returnStr;
    }

    function showEnumeratedHeader($var, $orderedoptions) {

        $returnStr = "<thead><tr class='uscic-table-row-header-enumerated'><th class='uscic-table-row-cell-header-enumerated'><nobr/></th>";

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
        $this->cellwidth = "width=" . round(($this->engine->replaceFills($this->group->getTableWidth()) - $this->engine->replaceFills($this->group->getQuestionColumnWidth())) / $noofcolumns) . "%";

        foreach ($orderedoptions as $option) {
            if (trim($option["label"] == "")) {
                continue;
            }
            $returnStr .= "<th class='uscic-table-row-cell-header-enumerated'><div class='" . $qa . "'><span id='vsid_option" . $var->getVsid() . $option["code"] . "' uscic-target='vsid_" . $var->getVsid() . "' uscic-texttype='" . SETTING_QUESTION . "' class='" . $this->displayobject->inlineeditable . "'>" . $this->displayobject->applyFormatting($option["label"], $this->group->getHeaderFormatting()) . "</span></div></th>";
        }

        $returnStr .= "</tr></thead>";

        return $returnStr;
    }

    function showEnumeratedOption($option, $number, $variable, $var, $first, $previousdata) {

        $returnStr = "";

        $varname = SESSION_PARAMS_ANSWER . $number;

        $id = $this->engine->getFill($variable, $var, SETTING_ID);
        if (trim($id) == "") {
            $id = $varname;
        }

        /* get id for inline field error checking */
        $ids = array();

        if (trim($option["label"] != "")) {
            $ids[] = $id . '_' . $option["code"];
        }

        if (inArray($var->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
            $varname .= "[]";
        }

        if ($var->getIfEmpty() != IF_EMPTY_ALLOW) {
            if (inArray($var->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED))) { // custom name for set of enumerated question, since we use a hidden field/textbox to track the real answer(s); we just use this custom name for referencing in the error checking
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

        switch ($var->getAnswerType()) {

            case ANSWER_TYPE_ENUMERATED: //enumerated

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

                                        <label for="' . $id . '_' . $option["code"] . '" class="uscic-table-enumerated-label">

                                            <div class="form-group uscic-table-row-cell-form-group">

                                                <input ' . $disabled . $this->role . ' class="uscic-radio-table" ' . $this->displayobject->getErrorTextString($varname) . ' ' . $inlinejavascript . ' type=radio id=' . $id . '_' . $option["code"] . ' name=' . $varname . ' value=' . $option["code"] . $selected . '>

                                            </div>

                                         </label>

                                        </div>
                                        <script type="text/javascript">$( document ).ready(function() { 
                                                    $("#cell' . $id . '_' . $option["code"] . '").click(function (e) {
                                                                          $("#' . $id . '_' . $option["code"] . '").prop("checked", true);
                                                                          $("#' . $id . '_' . $option["code"] . '").change();                                                                          
                                                                          });
                                                                         });</script>
                                        </td>

                                        ';
                } else {
                    //$returnStr .= '<td class="uscic-table-row-cell-enumerated"></td>';
                }

                /* done */
                break;

            case ANSWER_TYPE_SETOFENUMERATED: //set of enumerated

                $realvarname = $varname;

                /* we will have a text box entry OR a hidden field that tracks the entries, so that will be the real variable we look at in POST */
                $varname = $id . "_name[]";

                if ($first == true) {
                    $this->engine->getDisplayObject()->addSetOfEnumeratedChecks($varname, $variable, $var, ANSWER_TYPE_SETOFENUMERATED);
                }

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

                                        <label for="' . $id . '_' . $option["code"] . '" class="uscic-table-enumerated-label">

                                        <div class="form-group uscic-table-row-cell-form-group">

                                        <input  ' . $disabled  . $this->role . ' class="uscic-checkbox-table" ' . $this->displayobject->getErrorTextString($varname) . ' ' . $inlinejavascript . ' type=checkbox id=' . $id . '_' . $option["code"] . ' name=' . $varname . ' value=' . $option["code"] . $selected . '>

                                        </div>

                                        </label>

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
                                                                          $("#' . $id . '_' . $option["code"] . '")[0].onclick(); // trigger any onclick event handler    
                                                                          e.stopPropagation();  

                                                                          });
                                                                         });</script>';
                    /* add hidden field to track answers */
                    if ($first == true) {
                        $returnStr .= $this->engine->getDisplayObject()->addSetOfEnumeratedHidden($variable, $var, $realvarname, $varname, $id, $previousdata);
                    }
                    $returnStr .= '</td>';
                } else {
                    $returnStr .= '<td class="uscic-table-row-cell-setofenumerated">';

                    /* add hidden field to track answers */
                    if ($first == true) {
                        $returnStr .= $this->engine->getDisplayObject()->addSetOfEnumeratedHidden($variable, $var, $realvarname, $varname, $id, $previousdata);
                    }
                    $returnStr .= '</td>';
                }

                /* done */
                break;
        }

        return $returnStr;
    }

}

?>