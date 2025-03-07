<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DefaultTemplate {

    protected $engine;
    protected $variables;
    protected $realvariables;
    protected $language;
    protected $inclusive;
    protected $exclusive;
    protected $minrequired;
    protected $maxrequired;
    protected $exactrequired;
    protected $uniquerequired;
    protected $samerequired;
    protected $displaynumbers;
    protected $displayobject;
    protected $group;

    function __construct($engine, $group) {

        $this->engine = $engine;

        $this->group = $group;

        $this->exclusive = false;

        $this->inclusive = false;

        $this->minrequired = "";

        $this->maxrequired = "";

        $this->exactrequired = "";

        $this->uniquerequired = false;
        
        $this->samerequired = false;
        
        $this->parseOptions();

        $this->displaynumbers = array();

        $this->displayobject = $this->engine->getDisplayObject();

        $this->displaynumbers = $this->engine->getDisplayNumbers();
    }

    function parseOptions() {
        if ($this->group->getExclusive() == GROUP_YES) {
            $this->exclusive = true;
        }

        if ($this->group->getInclusive() == GROUP_YES) {
            $this->inclusive = true;
        }

        if ($this->group->getUniqueRequired() == GROUP_YES) {
            $this->uniquerequired = true;
        }
        
        if ($this->group->getSameRequired() == GROUP_YES) {
            $this->samerequired = true;
        }

        $this->minrequired = $this->engine->replaceFills($this->group->getMinimumRequired());
        $this->maxrequired = $this->engine->replaceFills($this->group->getMaximumRequired());
        $this->exactrequired = $this->engine->replaceFills($this->group->getExactRequired());
    }

    function show($variables, $realvariables, $language) {

        $returnStr = "";// "<div class='uscic-question-box' id='" . implode("_", $realvariables) . "'>"; // add div around this question/subgroup for styling purposes
        $this->variables = $variables;
        $this->realvariables = $realvariables;

        /* add error checks */
        $this->addErrorChecks();

        /* go through all variable statements (including any subgroup statements) */
        for ($i = 0; $i < sizeof($this->variables); $i++) {
            $variable = $this->variables[$i];
            
            if (startsWith($variable, ROUTING_IDENTIFY_SUBGROUP)) {
                //$returnStr .= '<br/><br/>';
                $returnStr .= $this->displayobject->showSubGroupQuestions($variable);
                $i = $this->findEndSubGroup($this->variables, $i); // skip until the end of the sub group, and continue display from there
            } else {
                $var = $this->engine->getVariableDescriptive($variable);

                /* only display non-inline fields */
                if ($this->engine->isInlineField($variable) == false) {

                    /* question text */
                    $class = "uscic-question";
                    if ($i == 0) {
                        $class = "uscic-question-first";
                    }
                    $returnStr .= $this->displayobject->showQuestionText($variable, $var, $class);
                    $cnt = $this->displaynumbers[strtoupper($variable)];

                    /* answer input element */
                    if (!inArray($var->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                        $previousdata = $this->engine->getAnswer($variable);
                        $returnStr .= $this->displayobject->showAnswer($cnt, $variable, $var, $previousdata);
                    }
                }
            }
        }

        return $returnStr . "</div>";
    }

    function addErrorChecks() {
        
        // if no group, then no need to do below
        if ($this->group->getGid() == "") {
            return;
        }
        
        // add error checks
        global $survey;
        foreach ($this->realvariables as $variable) {

            $name = SESSION_PARAMS_ANSWER . $this->displaynumbers[strtoupper($variable)];
            $var = $this->engine->getVariableDescriptive($variable);
            
            // add to real element (ignore none, section)
            if (!inArray($var->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {

                if ($var->getAnswerType() == ANSWER_TYPE_SETOFENUMERATED) {
                    $name .= "_name[]";
                }
                else if ($var->getAnswerType() == ANSWER_TYPE_MULTIDROPDOWN) {
                    $name .= "[]";
                }
                $id = $var->getId();
                if (trim($id) == "") {
                    $id = $name;
                }
                if ($this->exclusive == true) {
                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_EXCLUSIVE, $this->getAnswerList()), $this->engine->replaceFills($this->group->getErrorMessageExclusive()));
                }

                if ($this->inclusive == true) {
                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_INCLUSIVE, $this->getAnswerList()), $this->engine->replaceFills($this->group->getErrorMessageInclusive()));
                }

                if ($this->minrequired > 0) {

                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_MINREQUIRED, '"' . $this->minrequired . "-" . $this->getAnswerList() . '"'), replacePlaceHolders(array(PLACEHOLDER_MINIMUM_REQUIRED => $this->minrequired), $this->engine->replaceFills($this->group->getErrorMessageMinimumRequired())));
                }

                if ($this->maxrequired > 0) {

                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_MAXREQUIRED, '"' . $this->maxrequired . "-" . $this->getAnswerList() . '"'), replacePlaceHolders(array(PLACEHOLDER_MAXIMUM_REQUIRED => $this->maxrequired), $this->engine->replaceFills($this->group->getErrorMessageMaximumRequired())));
                }

                if ($this->exactrequired > 0) {

                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_EXACTREQUIRED, '"' . $this->exactrequired . "-" . $this->getAnswerList() . '"'), replacePlaceHolders(array(PLACEHOLDER_EXACT_REQUIRED => $this->exactrequired), $this->engine->replaceFills($this->group->getErrorMessageExactRequired())));
                }

                if ($this->uniquerequired == true) {
                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_UNIQUEREQUIRED, $this->getAnswerList()), $this->engine->replaceFills($this->group->getErrorMessageUniqueRequired()));
                }
                
                if ($this->samerequired == true) {
                    $this->displayobject->addErrorCheck($name, $variable, new ErrorCheck(ERROR_CHECK_SAMEREQUIRED, $this->getAnswerList()), $this->engine->replaceFills($this->group->getErrorMessageSameRequired()));
                }
                $var->setIfErrorGroup($this->group->getIfError());
                break; // only add to first variable in the group
            }
        }
    }

    function getAnswerList($exclude = "") {

        $answeridlist = array();

        foreach ($this->realvariables as $variable) {
            $variable = str_replace(" ", "", $variable);
            $var = $this->engine->getVariableDescriptive($variable);
            if (strtoupper($exclude) != strtoupper($variable) && !inArray($var->getAnswerType(), array(ANSWER_TYPE_NONE, ANSWER_TYPE_SECTION))) {
                $name = SESSION_PARAMS_ANSWER . $this->displaynumbers[strtoupper($variable)];
                if (inArray($var->getAnswerType(), array(ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_MULTIDROPDOWN))) {
                    $name .= "name[]";
                }
                $answeridlist[] = $name;
            }
        }

        return "['" . implode("','", $answeridlist) . "']";
    }

    function findEndSubGroup($variables, $start) {

        $level = 1;

        for ($cnt = ($start + 1); $cnt < sizeof($variables); $cnt++) {

            $variable = $variables[$cnt];
            if (startsWith($variable, ROUTING_IDENTIFY_SUBGROUP)) {

                /* nested subgroup */

                $level++;
            } else if (startsWith($variable, ROUTING_IDENTIFY_ENDSUBGROUP)) {

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            }
        }

        return sizeof($variables);
    }

}

?>