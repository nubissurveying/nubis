<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */
 
class SimpleTableTemplate extends TableTemplate {

    function __construct($engine, $group) {
        parent::__construct($engine, $group);
    }

    function show($variables, $realvariables, $language) {

        $this->variables = $variables;

        $this->realvariables = $realvariables;

        $this->language = $language;

        $returnStr = $this->simpleTable();

        return $returnStr;
    }

    function simpleTable() {

        $pt = $this->group->getParentGroup()->getTemplate();
        if ($pt != $this->group->getTemplate()) {
            $returnStr = '<div id="TGroup_' . implode("_",$this->realvariables) . '">';
        }


        /* add error checks */

        $this->addErrorChecks();

        $cellwidth = "width=" . round((100 - $this->engine->replaceFills($this->group->getQuestionColumnWidth())) / 2) . "%";

        /* start table */
        $id = $this->group->getTableId();
        if (trim($id) == "") {
            $id = 'table_' . $this->group->getName() . mt_rand(0,10000);
        }
        if ($pt != $this->group->getTemplate()) {
            $returnStr .= '<table id="' . $id . '" class="table' . $this->striped . $this->bordered . $this->hovered . $this->condensed . ' uscic-table-simpletable">';

            /* build table */
            $returnStr .= '<thead></thead><tbody>';
        }

        for ($i = 0; $i < sizeof($this->variables); $i++) {

            $variable = $this->variables[$i];

            if (startsWith($variable, ROUTING_IDENTIFY_SUBGROUP)) {

                $returnStr .= $this->displayobject->showSubGroupQuestions($variable, $this->group);

                $i = $this->findEndSubGroup($this->variables, $i); // skip until the end of the sub group, and continue display from there
            } else {

                $var = $this->engine->getVariableDescriptive($variable);


                /* only display non-inline fields */
                if ($this->engine->isInlineField($variable) == false) {

                    /* question text */
                    $returnStr .= "<tr><td class='uscic-table-row-question-cell-simpletable' " . $cellwidth . ">" . $this->displayobject->showQuestionText($variable, $var, "uscic-question-table-row") . "</td>";
                    $cnt = $this->displaynumbers[strtoupper($variable)];
                    if (!inArray($var->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {

                        /* answer input element */
                        $previousdata = $this->engine->getAnswer($variable);
                        $returnStr .= "<td class='uscic-table-row-cell-simpletable' >" . $this->displayobject->showAnswer($cnt, $variable, $var, $previousdata) . "</td>";
                    } else {
                        $returnStr .= "<td class='uscic-table-row-cell-simpletable' ></td>";
                    }
                    $returnStr .= "</tr>";
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