<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */
           
class MultiColumnTableTemplate extends TableTemplate {

    function __construct($engine, $group) {
        parent::__construct($engine, $group);
    }

    function multiColumn($breakpoint) {

        $returnStr = "";
        if ($this->group->isTableMobile() == true) {
            $returnStr .= $this->displayobject->displayTableSaw();
        }
        $pt = $this->group->getParentGroup()->getTemplate();
        if ($pt != $this->group->getTemplate()) {
            $returnStr .= '<div id="TGroup_' . implode("_", $this->realvariables) . '">';
        }

        /* add error checks */
        $this->addErrorChecks();

        /* start table */
        $id = $this->group->getTableId();
        if (trim($id) == "") {
            $id = 'table_' . $this->group->getName() . mt_rand(0, 10000);
        }

        // get header alignment for any headers and input boxes
        if ($this->group->getParentGroup() != null) {
            $align = $this->group->getParentGroup()->getHeaderAlignment();
        } else {
            $align = $this->group->getHeaderAlignment();
        }
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

        if ($pt != $this->group->getTemplate()) {
            $width = $this->engine->replaceFills($this->group->getTableWidth());
            $style = " style='width: " . $width . "%; max-width: " . $width . "%' ";
            $nolabels = "data-tablesaw-preappend";
            if ($this->group->isTableMobileLabels() == false) {
                $nolabels = "data-tablesaw-no-labels";
            }
            $returnStr .= '<table ' . $nolabels . ' data-tablesaw-mode="stack" id="' . $id . '"' . $style . ' class="tablesaw tablesaw-stack table' . $this->striped . $this->bordered . $this->hovered . $this->condensed . ' uscic-table-multicolumn">';

            /* build table */
            $returnStr .= '<thead>';

            $varhead = $this->engine->replaceFills($this->group->getTableHeaders());
            if ($varhead != "") {
                $vardesc = $this->engine->getVariableDescriptive($varhead);
                $this->headers = $vardesc->getOptions();
                foreach ($this->headers as $h) {
                    $returnStr .= "<th class='uscic-table-row-cell-header-multi-column'><div class='" . $qa . "'>" . $this->displayobject->applyFormatting($h["label"], $this->group->getHeaderFormatting()) . "</div></th>";
                }
            }
            $returnStr .= '</thead><tbody>';
        }

        $counter = 1;
        for ($i = 0; $i < sizeof($this->variables); $i++) {

            $variable = $this->variables[$i];

            if (startsWith($variable, ROUTING_IDENTIFY_SUBGROUP)) {

                //$returnStr .= "<tr class='uscic-table-row-twocolumn'><td class='uscic-table-row-cell-twocolumn' >" . $this->displayobject->showSubGroupQuestions($variable) . "</td></tr>";
                $returnStr .= $this->displayobject->showSubGroupQuestions($variable, $this->group);

                $i = $this->findEndSubGroup($this->variables, $i); // skip until the end of the sub group, and continue display from there
            } else {

                $var = $this->engine->getVariableDescriptive($variable);
                $varid = $var->getId();
                if ($varid == "") {
                    $varid = $i;
                }

                /* only display non-inline fields */
                if ($this->engine->isInlineField($variable) == false) {

                    /* question text */

                    // calculate cell width
                    if ($pt == $this->group->getTemplate()) {
                        $cellwidth = "style='width: " . $this->engine->replaceFills($this->group->getParentGroup()->getQuestionColumnWidth()) . "%;'";
                    } else {
                        $cellwidth = "style='width:" . $this->engine->replaceFills($this->group->getQuestionColumnWidth()) . "%;'";
                    }

                    // first column, so we add the question text
                    if ($counter == 1) {
                        if ($this->group->isMultiColumnQuestiontext() == true) {
                            $returnStr .= "<tr><td id='" . $id . "_questioncolumn_" . $varid . "' class='uscic-table-row-cell-question-multicolumn' " . $cellwidth . ">" . $this->displayobject->showQuestionText($variable, $var, "uscic-question-table-row") . "</td>";
                        } else {
                            $returnStr .= "<tr>";
                        }
                    }
                    $counter++;

                    $cnt = $this->displaynumbers[strtoupper($variable)];
                    if (!inArray($var->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {

                        // calculate cell width
                        if ($pt == $this->group->getTemplate()) {
                            $cellwidth = "style='width: " . round(($this->engine->replaceFills($this->group->getParentGroup()->getTableWidth()) - $this->engine->replaceFills($this->group->getParentGroup()->getQuestionColumnWidth())) / ($breakpoint - 1)) . "%;'";
                        } else {
                            $cellwidth = "style='width: " . round(($this->engine->replaceFills($this->group->getTableWidth()) - $this->engine->replaceFills($this->group->getQuestionColumnWidth())) / ($breakpoint - 1)) . "%;'";
                        }

                        /* answer input element */
                        $previousdata = $this->engine->getAnswer($variable);
                        $returnStr .= "<td id='" . $id . "_column_" . $varid . "' class='uscic-table-row-cell-multicolumn' " . $cellwidth . "><div class='" . $qa . "'>" . $this->displayobject->showAnswer($cnt, $variable, $var, $previousdata) . "</div></td>";
                    }

                    // end of row
                    if ($counter == $breakpoint) {
                        $counter = 1;
                        $returnStr .= "</tr>";
                    }
                }
            }
        }

        if ($pt != $this->group->getTemplate()) {
            $returnStr .= "</tbody></table></div>";
        }
        return $returnStr;
    }

}

?>