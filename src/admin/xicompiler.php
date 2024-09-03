<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once("instruction.php");
require_once("phpparser_bootstrap.php");

ini_set('memory_limit', Config::compilerMemoryLimit());
ini_set('xdebug.max_nesting_level', 4000);
ini_set("error_reporting", "ALL");

define('FUNCTION_XI_GET_ANSWER', "value");
define('FUNCTION_XI_ASK', "ask");
define('FUNCTION_XI_GET_FILL_TEXT_BY_LINE', "getLine");
define('FILL_MARKER', '~ereiu43765~');

/* NOTES:
 * 
 * If using .FILL in Nubis for Xi, then line options are copied over to the question text
 * Response/empty/dk/rf: adjustment not needed since xi uses same dk/rf codes and interpretation
 * 
 */

/* TODO:
 * 
 * SET EMPTY/DK/RF: what are the xi equivalents
 * group inline fields: something to be done with 'setOtherOption(1, OTHER_OPTION_ONSELECTED, $A013_4_year[$A_counter->value()])'
 * add isempty/isdk/isrf functions to nubis so they can be used for Xi
 */

class XiCompiler {

    //private $modes;
    private $languages;
    private $factory;
    private $printer;
    private $currentlanguage;
    private $currentmode;
    private $types_output;
    private $questions_output;
    private $fillclass;
    private $currentrgid;
    private $messages;
    private $instructions;
    private $cnt;
    private $nestinglevel;
    private $nestingarray;
    private $lastvar; // last encountered variable (used to get value acronym codes)
    private $groupstatements; // statements for an encountered group
    private $group; // indicates whether we are in a group statement
    private $screencounter; // question screen counter
    private $suid;
    private $seid;
    private $version;
    private $survey;
    private $looptimes; // keeps track of how often a question screen can appear (typically 1, but can be more if within loop)
    private $lasttimesloop; // keeps track of number of loops of last for loop
    private $loops; // keeps track of rgid's of loops
    private $groups; // keeps track of rgid's of groups
    private $groupsend; // keeps track of end rgid's of groups   
    private $groupactions; // keeps track of actions of groups
    private $progressbarloops; // keeps track of rgid's of loops for progress bar processing
    private $ifs; // keeps track of rgid's of ifs
    private $lastloopactions; // keeps track of last action of a loop
    private $loopactions; // keeps track of actions of loops
    private $loopcounters; // keeps track of names of loop counters
    private $loopnextrgids; // keeps track of loop next rgids for exitfor
    private $setfills;
    private $ifreset;
    private $elseifreset;
    private $elsereset;
    private $whiles; // keeps track of rgid's of whiles
    private $lastwhileactions; // keeps track of last action of a while
    private $whileactions; // keeps track of actions of whiles
    private $whilenextrgids; // keeps track of while next rgids for exitwhile
    private $routing_output;
    private $lastgroup;

    function __construct($suid, $mode, $version) {
        $this->suid = $suid;
        $this->version = $version;
        $this->survey = new Survey($this->suid);
        $this->modes = explode("~", $this->survey->getAllowedModes());
        foreach ($this->modes as $m) {
            $this->languages[$m] = explode("~", $this->survey->getAllowedLanguages($m));
        }
        $this->currentmode = $mode;
        $this->currentlanguage = $_SESSION['SURVEY_LANGUAGE'];
        $this->languages = explode("~", $this->survey->getAllowedLanguages($this->currentmode));
        $this->types_output = array();
        $this->questions_output = array();
        $this->routing_output = array();
        $this->fillclass = false;
        $this->messages = array();
        $this->factory = new PHPParser_BuilderFactory();
        $this->printer = new PHPParser_PrettyPrinter_Default();        
    }

    function getRoutingOutput($seid) {
        if (isset($this->routing_output[$seid])) {
            return $this->routing_output[$seid];
        }
        return array();
    }

    function getTypesOutput() {
        return $this->types_output;
    }

    function getVariableDescriptivesOutput() {
        return $this->questions_output;
    }

    function generateSurvey() {
        $array = array();

        // add groups
        $array[] = $this->generateGroups();

        // add types
        $array[] = $this->generateTypes();

        // add variables
        $array[] = $this->generateVariableDescriptives();
    }

    function generateGroups() {
        
    }

    function generateVariableDescriptives() {
        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $vars = $this->survey->getVariableDescriptives();
        foreach ($vars as $var) {
            $this->generateVariableDescriptive($var);
        }
        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateVariableDescriptive($var) {
        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $str = "";
        $cnt = 0;
        $answertype = $var->getAnswerType();
        $anstext = "";
        
        $description = "\r\narray(\r\n";
        $texts = array();
        foreach ($this->languages as $l) {
            $_SESSION['SURVEY_LANGUAGE'] = $l;
            $texts[] = $l . " => '" . $this->handleFills($var->getDescription()) . "'";
        }

        $description .= implode(",\r\n", $texts);
        $description .= "\r\n)";
        
        $questiontext = "\r\narray(\r\n";
        $texts = array();
        foreach ($this->languages as $l) {
            $_SESSION['SURVEY_LANGUAGE'] = $l;
            $texts[] = $l . " => '" . $this->handleFills($var->getQuestion()) . "'";
        }

        $questiontext .= implode(",\r\n", $texts);
        $questiontext .= "\r\n)";
        $type = "";

        // existing type
        if ($var->getTyd() > 0) {
            $t = $this->survey->getType($var->getTyd());
            $type = "\$" . $t->getName();
        }
        // make type
        else {
            $type = "T" . $var->getName();
            $old = $var->getName();
            $var->setName($type);
            $this->generateType($type, $var); // we pretend the variable is a type here (they share the same functions)
            $var->setName($old);
            $type = "\$" . $type;
        }
        $template = $var->getXiTemplate();
        if ($template == "") {
            $template = "TQuestionTemplate";
        }
        
        $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
        
        /*
        switch ($answertype) {
            case ANSWER_TYPE_NONE:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_STRING:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_OPEN:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_INTEGER:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_RANGE:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_ENUMERATED:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_DROPDOWN:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
            default:
                $str .= "\$" . $var->getName() . " = new Question('" . $var->getName() . "', " . $description . ", " . $questiontext . ", " . $type . ", $" . $template . ");\r\n";
                break;
        }*/

        // set options if had type before (just to so we override if it is different)
        if ($var->getTyd() > 0) {
            if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
                if ($var->getShowDKButton() == BUTTON_YES) {
                    $str .= "\$" . $var->getName() . "->setShowDKButton(true);\r\n";
                }
                if ($var->getShowRFButton() == BUTTON_YES) {
                    $str .= "\$" . $var->getName() . "->setShowRFButton(true);\r\n";
                }

                $onempty = $var->getIfEmpty();
                if ($onempty == IF_EMPTY_WARN) {
                    $str .= "\$" . $var->getName() . "->setValidationOption(VALIDATION_OPTION_REQUEST_ONE_TIME);\r\n";
                } else if ($onempty == IF_EMPTY_ALLOW) {
                    $str .= "\$" . $var->getName() . "->setValidationOption(VALIDATION_OPTION_ALLOW_CONTINUE);\r\n";
                }

                $pretexts = array();
                foreach ($this->languages as $l) {
                    $_SESSION['SURVEY_LANGUAGE'] = $l;
                    $pretext = $this->handleFills($var->getPreText());
                    if ($pretext != "") {
                        $pretexts[] = $l . " => " . $pretext;
                    }
                }

                if (sizeof($pretexts) > 0) {
                    $str .= "\$" . $var->getName() . "->setHintTexts(NULL, array(" . implode(",", $pretexts) . "));\r\n";
                }

                // TODO: input width/height would have to come from the inline style setting?
                //$str .= "\$" . $type->getName() . "->setInputWidth(" . $width . ");";
                //$str .= "\$" . $type->getName() . "->setInputHeight(" . $width . ");";
            }
        }

        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        $this->questions_output[] = $str;
    }

    function generateTypes() {

        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        $types = $this->survey->getTypes();
        foreach ($types as $type) {
            $this->types_output[] = $this->generateType($type->getName(), $type);
        }

        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateType($name, $type) {

        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $str = "";
        $answertype = $type->getAnswerType();
        $anstext = "";
        $template = $type->getXiTemplate();
        if ($template == "") {
            $template = "TTextTemplate";
        }
        
        switch ($answertype) {
            case ANSWER_TYPE_NONE:
                $anstext = "QUESTION_TYPE_NONE";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_STRING:
                $anstext = "QUESTION_TYPE_STRING";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_OPEN:
                $anstext = "QUESTION_TYPE_OPEN";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_RANGE:
                $anstext = "QUESTION_TYPE_RANGE";
                $min = $this->handleFills($type->getMinimum());
                $max = $this->handleFills($type->getMaximum());
                $range = "array(" . $min . "," . $max . ")";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", " . $range . ", $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_INTEGER:
                $anstext = "QUESTION_TYPE_INTEGER";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_DOUBLE:
                $anstext = "QUESTION_TYPE_FLOAT";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_DATE:
                $anstext = "QUESTION_TYPE_DATE";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_TIME:
                $anstext = "QUESTION_TYPE_TIME";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_DATETIME:
                $anstext = "QUESTION_TYPE_DATETIME";
                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", '', $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_ENUMERATED:
                $anstext = "QUESTION_TYPE_ENUMERATED";
                $template = "TEnumeratedTemplate";
                $out = array();
                foreach ($this->languages as $l) {
                    $_SESSION['SURVEY_LANGUAGE'] = $l;
                    $optiontext = "\r\narray(\r\n";
                    $type->clearOptions();
                    $options = $type->getOptions();
                    for ($i = 0; $i < sizeof($options); $i++) {
                        $option = $options[$i];
                        $optiontext .= $option["code"] . " => '" . $this->handleFills($option["label"]) . "'";
                        if (($i + 1) < sizeof($options)) {
                            $optiontext .= ", ";
                        }
                        $optiontext .= "\r\n";
                    }
                    $optiontext .= ")";
                    $out[] = $l . " => " . $optiontext;
                }

                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", array(\r\n" . implode(",\r\n", $out) . "), $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_SETOFENUMERATED:
                $anstext = "QUESTION_TYPE_SETOF";
                $template = "TEnumeratedTemplate";
                $out = array();
                foreach ($this->languages as $l) {
                    $_SESSION['SURVEY_LANGUAGE'] = $l;
                    $optiontext = "\r\narray(\r\n";
                    $type->clearOptions();
                    $options = $type->getOptions();
                    for ($i = 0; $i < sizeof($options); $i++) {
                        $option = $options[$i];
                        $optiontext .= $option["code"] . " => '" . $this->handleFills($option["label"]) . "'";
                        if (($i + 1) < sizeof($options)) {
                            $optiontext .= ",";
                        }
                        $optiontext .= "\r\n";
                    }
                    $optiontext .= ")";
                    $out[] = $l . " => " . $optiontext;
                }

                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", array(\r\n" . implode(",\r\n", $out) . "), $" . $template . ");\r\n";
                break;
            case ANSWER_TYPE_DROPDOWN:
                $anstext = "QUESTION_TYPE_SELECT";
                $template = "TSelectTemplate";
                $out = array();
                foreach ($this->languages as $l) {
                    $_SESSION['SURVEY_LANGUAGE'] = $l;
                    $optiontext = "\r\narray(\r\n";
                    $type->clearOptions();
                    $options = $type->getOptions();
                    for ($i = 0; $i < sizeof($options); $i++) {
                        $option = $options[$i];
                        $optiontext .= $option["code"] . " => '" . $this->handleFills($option["label"]) . "'";
                        if (($i + 1) < sizeof($options)) {
                            $optiontext .= ",";
                        }
                        $optiontext .= "\r\n";
                    }
                    $optiontext .= ")";
                    $out[] = $l . " => " . $optiontext;
                }

                $str .= "\$" . $type->getName() . " = new Type('" . $type->getName() . "', " . $anstext . ", array(\r\n" . implode(", \r\n", $out) . "), $" . $template . ");\r\n";
                break;
        }

        // add generic
        if (!inArray($answertype, array(ANSWER_TYPE_NONE))) {
            if ($type->getShowDKButton() == BUTTON_YES) {
                $str .= "\$" . $type->getName() . "->setShowDKButton(true);\r\n";
            }
            if ($type->getShowRFButton() == BUTTON_YES) {
                $str .= "\$" . $type->getName() . "->setShowRFButton(true);\r\n";
            }

            $onempty = $type->getIfEmpty();
            if ($onempty == IF_EMPTY_WARN) {
                $str .= "\$" . $type->getName() . "->setValidationOption(VALIDATION_OPTION_REQUEST_ONE_TIME);\r\n";
            } else if ($onempty == IF_EMPTY_ALLOW) {
                $str .= "\$" . $type->getName() . "->setValidationOption(VALIDATION_OPTION_ALLOW_CONTINUE);\r\n";
            }

            $pretexts = array();
            foreach ($this->languages as $l) {
                $_SESSION['SURVEY_LANGUAGE'] = $l;
                $pretext = $type->getPreText();
                if ($pretext != "") {
                    $pretexts[] = $l . " => '" . $this->handleFills($pretext) . "'";
                }
            }

            if (sizeof($pretexts) > 0) {
                $str .= "\$" . $type->getName() . "->setHintTexts(NULL, array(" . implode(",", $pretexts) . "));\r\n";
            }

            // TODO: input width/height would have to come from the inline style setting?
            //$str .= "\$" . $type->getName() . "->setInputWidth(" . $width . ");";
            //$str .= "\$" . $type->getName() . "->setInputHeight(" . $width . ");";
        }

        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;

       $this->types_output[] = $str;       
    }

    function handleFills($text) {
        $cnt = 0;
        while (strpos($text, INDICATOR_FILL) !== false) {
            $fills = getReferences($text, INDICATOR_FILL);

            // sort fills by longest keys
            usort($fills, "reversenat");
            foreach ($fills as $fill) {
                $fillref = $fill; // str_replace("[", "\[", str_replace("]", "\]", $fill));
                $filltext = strtr(FILL_MARKER . $this->handleFill($fill) . FILL_MARKER, array('\\' => '\\\\', '$' => '\$'));
                $pattern = "/\\" . INDICATOR_FILL . preparePattern($fillref) . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }
            $cnt++;

            /* stop after 999 times */
            if ($cnt > 999) {
                break;
            }
        }
        $text = str_replace("'", "\'", $text);
        return str_replace(FILL_MARKER, "^", $text);
    }

    function handleFill($text) {

        $excluded = array();
        $rule = excludeText($text, $excluded);
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);


        $classextension = prepareClassExtension($text);

        try {
            $stmts = $parser->parse("<?php " . $rule . " ?>");
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node                        
            $this->updateVariables($stmt);

            // fake method call for Q1[1,1] reference
            $cleanup = false;
            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmt = $stmt->value;
                $cleanup = true;
            } else {
                if (isset($stmt->value->name)) {
                    $stmt = $stmt->value->name;
                } else {
                    $stmt = $stmt->value;
                }
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorFillInvalid());
            return;
        }

        /* get statements */
        $stmts = array($stmt);

        /* generate code */
        $fillcall = $this->printer->prettyPrint($stmts);

        // clean up
        if ($cleanup) {
            $fillcall = str_replace(" . (", "", $fillcall);
            $fillcall = str_replace(" . ])", "]", $fillcall);
        }
        /* return result */

        // strip ending ;
        return substr($fillcall, 0, strlen($fillcall) - 1);
    }

    function updateVariables(&$node) {

        $subs = $node->getSubNodeNames();
        if (sizeof($subs) == 0) {
            return;
        }

        // child nodes
        for ($i = 0; $i < sizeof($subs); $i++) {

            $nm = $subs[$i];
            $subnode = $node->$nm;

            // name node: this could be a variable
            if ($subnode instanceof PHPParser_Node_Name) {

                $name = $subnode->getFirst();

                // restore any brackets!
                $name = str_replace(TEXT_BRACKET_LEFT, "[", $name);
                $name = str_replace(TEXT_BRACKET_RIGHT, "]", $name);

                // restore any dot notations!
                $name = str_replace(TEXT_MODULE_DOT, ".", $name);

                $var = $this->survey->getVariableDescriptiveByName(getBasicName($name)); // new VariableDescriptive();  
                if (strtoupper($name) == VARIABLE_VALUE_NULL) {
                    $stmt = new PHPParser_Node_Scalar_String(VARIABLE_VALUE_NULL);
                    $node->$nm = $stmt;
                } else if (strtoupper($name) == VARIABLE_VALUE_DK) {
                    $stmt = new PHPParser_Node_Scalar_String(VARIABLE_VALUE_DK);
                    $node->$nm = $stmt;
                } else if (strtoupper($name) == VARIABLE_VALUE_RF) {
                    $stmt = new PHPParser_Node_Scalar_String(VARIABLE_VALUE_RF);
                    $node->$nm = $stmt;
                } else if (strtoupper($name) == VARIABLE_VALUE_NA) {
                    $stmt = new PHPParser_Node_Scalar_String(VARIABLE_VALUE_NA);
                    $node->$nm = $stmt;
                } else if (strtoupper($name) == VARIABLE_VALUE_RESPONSE) {
                    $stmt = new PHPParser_Node_Scalar_String(VARIABLE_VALUE_RESPONSE);
                    $node->$nm = $stmt;
                } else if (strtoupper($name) == VARIABLE_VALUE_EMPTY) {
                    $stmt = new PHPParser_Node_Scalar_String(VARIABLE_VALUE_EMPTY);
                    $node->$nm = $stmt;
                } else if (strtoupper($name) == VARIABLE_VALUE_INARRAY) {

                    /* do nothing */
                    
                } else if ($var->getVsid() != "") {

                    $answertype = $var->getAnswerType();
                    if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_RANK))) {
                        $this->lastvar = $var;
                    }

                    $args = array();
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable($name), new PHPParser_Node_Name(array(FUNCTION_XI_GET_ANSWER)), $args);
                    $node->$nm = $stmt;
                } else if ($this->fillclass == true && startsWith($name, VARIABLE_VALUE_FILL)) {

                    $line = trim(str_ireplace(VARIABLE_VALUE_FILL, "", $name));
                    if ($line != "") {
                        $args = array();
                        if (!is_numeric($line)) {
                            $parser = new PHPParser_Parser(new PHPParser_Lexer);                            
                            try {
                                $stmtsleft = $parser->parse("<?php " . $line . "?>");
                                $temp = new PHPParser_Node_Arg($stmtsleft[0]); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
                                $this->updateVariables($temp);
                                if ($temp->value instanceof PHPParser_Node_Expr_MethodCall) {
                                    $args[] = $temp->value->args[0];
                                }

                                // a non-bracketed field
                                else {

                                    /* not a constant, which happens if the counter field does not exist */
                                    if (isset($temp->value->name)) {
                                        $args[] = $temp->value->name;
                                    }
                                }
                            } catch (Exception $e) {
                                $this->addErrorMessage(Language::errorAssignmentInvalid());
                                return;
                            }
                        } else {
                            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($line));
                        }
                        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable($this->currentfillvariable), new PHPParser_Node_Name(array(FUNCTION_XI_GET_FILL_TEXT_BY_LINE)), $args);
                        $node->$nm = $stmt;
                    }
                } else {

                    if ($this->lastvar != null) {
                        $vc = $this->lastvar->getOptionCodeByAcronym($name);
                        if ($vc > 0) {
                            $stmt = new PHPParser_Node_Scalar_String($vc);
                            $node->$nm = $stmt;
                        } else {

                            /* fill that is not present (or something else), then return the
                             * text representation of whatever was inputted
                             */
                            if (!inArray($name, array("true", "false"))) {
                                $this->messages[] = $this->addErrorMessage(Language::errorVariableNotFound($name));
                            }
                            $stmt = new PHPParser_Node_Scalar_String($name);
                            $node->$nm = $stmt;
                        }
                    } else {

                        /* fill that is not present (or something else), then return the
                         * text representation of whatever was inputted
                         */
                        if (!inArray($name, array("true", "false"))) {
                            $this->messages[] = $this->addErrorMessage(Language::errorVariableNotFound($name));
                        }
                        $stmt = new PHPParser_Node_Scalar_String($name);
                        $node->$nm = $stmt;
                    }
                }
            }

            // function call: there could be variables in there
            else if ($subnode instanceof PHPParser_Node_Expr_FuncCall) {

                /* check if function name is actually a question; if yes, then
                 * this is actually a bracket Q1[cnt], which we are processing
                 * as Q1(cnt) so the PHPParser doesn't break
                 */

                /* get function name */
                $namenode = $subnode->name;
                $name = $namenode->getFirst();
                $name = str_replace(TEXT_MODULE_DOT, ".", $name);

                // real function call
                if (function_exists($name)) {
                    if (!inArray($name, getAllowedRoutingFunctions()) || inArray($name, getForbiddenRoutingFunctions())) {
                        $this->addErrorMessage(Language::messageCheckerFunctionNotAllowed($name));
                        $name = "INVALID";
                        $stmt = new PHPParser_Node_Scalar_String($name);
                        $node->$nm = $stmt;
                    } else {

                        $args = $subnode->args;
                        for ($j = 0; $j < sizeof($args); $j++) {
                            $this->updateVariables($args[$j]);
                        }
                    }
                } else {

                    // not a real function call, but a bracket statement
                    $var = $this->survey->getVariableDescriptiveByName(getBasicName($name)); // new VariableDescriptive();

                    if ($var->getVsid() != "") {

                        // array statement, so question should be an array    
                        if ($var->isArray() == false) {
                            $this->addErrorMessage(Language::errorNotArray(strtolower(getBasicName($name))));
                        }

                        /* go through 'function' arguments 
                         * and change them to "[" . $this-getValue("field") . "]"
                         */
                        $answertype = $var->getAnswerType();
                        if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_RANK))) {
                            $this->lastvar = $var;
                        }
                        $args = array();
                        $stmt = new PHPParser_Node_Expr_MethodCall($this->handleBracketExpression($subnode, $name), new PHPParser_Node_Name(array(FUNCTION_XI_GET_ANSWER)), $args);
                        $node->$nm = $stmt; // $this->handleBracketExpression($subnode, $name)
                    } else {

                        /* fill that is not present (or something else), then return the
                         * text representation of whatever was inputted
                         */
                        $stmt = new PHPParser_Node_Scalar_String($name);
                        $node->$nm = $stmt;

                        if (!inArray($name, array("true", "false"))) {
                            $this->messages[] = $this->addErrorMessage(Language::errorVariableNotFound($name));
                        }
                    }
                }
            } else if ($subnode instanceof PHPParser_Node_Expr_Array) {
                $items = $subnode->items;
                for ($j = 0; $j < sizeof($items); $j++) {
                    $this->updateVariables($items[$j]);
                }
            } else {

                // check children if real node itself (not an array part)
                if ($subnode instanceof PHPParser_NodeAbstract) {
                    $this->updateVariables($subnode);
                }
            }
        }
    }

    function handleBracketExpression($subnode, $name) {

        // get arguments of q1[cnt+cnt1-getTest("1)] --> 'function call': q1(cnt+cnt1-getTest("1))
        $args = $subnode->args;

        // construct left hand side
        $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Variable($name . "["), new PHPParser_Node_Scalar_String("dummy"));

        // construct right hand side
        $bracketnode->right = new PHPParser_Node_Expr_Concat($this->getBrackets($args), new PHPParser_Node_Scalar_DNumber("]")); // use DNumber to avoid getting single quots around the ]
        // return result
        return $bracketnode;
    }

    function getBrackets($args) {

        $bracketnode = null;
        $oldbracketnode = null;
        for ($j = 0; $j < sizeof($args); $j++) {
            $argnode = $args[$j];
            $this->updateVariables($argnode);

            // get value node inside argnode
            $valuenode = $argnode->value;

            // not last argument, then concatenate (Q1[1,1,1])
            if (($j + 1) <= sizeof($args)) {

                // first time
                if ($bracketnode == null) {

                    if (sizeof($args) > 1) {

                        // preserve quotes for associate array references
                        if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                            $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String(""), $valuenode), new PHPParser_Node_Scalar_String("")), new PHPParser_Node_Scalar_String(","));
                        } else {
                            $bracketnode = new PHPParser_Node_Expr_Concat($valuenode, new PHPParser_Node_Scalar_String(","));
                        }
                    } else {

                        // preserve quotes for associate array references
                        if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                            $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String(""), $valuenode), new PHPParser_Node_Scalar_String(""));
                        } else {
                            $bracketnode = $valuenode;
                        }
                    }
                }

                // second or more arguments
                else {

                    // last one
                    if (($j + 1) == sizeof($args)) {

                        // preserve quotes for associate array references
                        if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String(""), $valuenode), new PHPParser_Node_Scalar_String("")));
                        } else {
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, $valuenode);
                        }
                    } else {

                        // preserve quotes for associate array references
                        if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String(""), $valuenode), new PHPParser_Node_Scalar_String("")), new PHPParser_Node_Scalar_String("")));
                        } else {
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, new PHPParser_Node_Expr_Concat($valuenode, new PHPParser_Node_Scalar_String(",")));
                        }
                    }
                }
            } else {

                // only one argument            
                if ($bracketnode == null) {

                    // preserve quotes for associate array references
                    if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                        $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String(""), $valuenode), new PHPParser_Node_Scalar_String(""));
                    } else {
                        $bracketnode = $valuenode;
                    }
                } else {
                    //$bracketnode->right = $valuenode;
                }
            }
        }

        return $bracketnode;
    }

    function addErrorMessage($message, $rgid = "") {
        $arr = array();
        $r = $this->currentrgid;
        if ($rgid != "") {
            $r = $rgid;
        }
        if (isset($this->messages[$r])) {
            $arr = $this->messages[$r];
        }
        if (!inArray($message, $arr)) {
            $arr[] = $message;
        }
        $this->messages[$r] = $arr;
    }

    function generateFill($seid, $var) {
        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $this->fillclass = true;
        $this->instructions = array();

        /* set screen counter */
        $this->screencounter = 0;
        $this->ifreset = array();
        $this->elseifreset = array();
        $this->elsereset = array();

        /* add rules */
        $this->looptimes = 1;
        $this->lasttimesloop = array();
        $this->lastloopactions = array();
        $this->loops = array();
        $this->whiles = array();
        $this->lastwhileactions = array();
        $this->groups = array();
        $this->groupsend = array();
        $this->groupactions = array();
        $this->messages = array();
        $this->seid = $seid;
        $code = $var->getFillCode();
        $fillrules = explode("\r\n", $code);
        $cnt = 1;

        foreach ($fillrules as $fillrule) {
            $this->instructions[$cnt] = new RoutingInstruction($var->getSuid(), $var->getSeid(), $cnt, rtrim($fillrule));
            $cnt++;
        }
        
        $this->currentfillvariable = $var->getName();

        /* process rules */
        for ($this->cnt = 1; $this->cnt <= sizeof($this->instructions); $this->cnt++) {
            if (isset($this->instructions[$this->cnt])) {
                $this->addRule($this->instructions[$this->cnt]);
            }
        }
        //$_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateRouting($seid) {

        $this->instructions = array();

        /* set screen counter */
        $this->screencounter = 0;
        $this->ifreset = array();
        $this->elseifreset = array();
        $this->elsereset = array();

        /* not a fill class we are generating */
        $this->fillclass = false;
        $this->setfills = array();

        /* add rules */
        $this->looptimes = 1;
        $this->lasttimesloop = array();
        $this->lastloopactions = array();
        $this->loops = array();
        $this->whiles = array();
        $this->lastwhileactions = array();
        $this->groups = array();
        $this->groupsend = array();
        $this->groupactions = array();
        $this->messages = array();
        $this->seid = $seid;
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_routing where suid=" . prepareDatabaseString($this->suid) . " and seid=" . prepareDatabaseString($this->seid) . " order by rgid asc";
        if ($rules = $db->selectQuery($q)) {

            if ($db->getNumberOfRows($rules) > 0) {
                while ($row = $db->getRow($rules)) {
                    $this->instructions[$row["rgid"]] = new RoutingInstruction($this->suid, $this->seid, $row["rgid"], $row["rule"]);
                }

                /* process rules */
                for ($this->cnt = 1; $this->cnt <= sizeof($this->instructions); $this->cnt++) {
                    if (isset($this->instructions[$this->cnt])) {
                        $this->addRule($this->instructions[$this->cnt]);
                    }
                }
            }
        }
    }

    function addRule($instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        $this->currentrgid = $rgid;

        if (contains($rule, "//")) {
            $rule = substr($rule, 0, stripos($rule, "//"));
            $instruction->setRule($rule);
        }

        /* empty line */
        if ($rule == "") {
            
        }

        // if condition 
        else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
            $this->addIf($instruction);
        }
        // else if condition 
        else if (startsWith($rule, ROUTING_IDENTIFY_ELSEIF)) {
            $this->addIf($instruction);
        }
        // else 
        else if (startsWith($rule, ROUTING_IDENTIFY_ELSE)) {
            $this->addElse($instruction);
        }
        // for loop  
        else if (startsWith($rule, ROUTING_IDENTIFY_FOR) || startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {
            $this->addForLoop($instruction);
        }
        // while loop  
        else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {
            $this->addWhileLoop($instruction);
        }
        // group  
        else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {
            $this->addGroup($instruction);
        }
        // sub group  
        else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {
            // do nothing, not supported in xi
        }
        // move forward
        else if (startsWith($rule, ROUTING_MOVE_FORWARD)) {
            $this->addMoveForward($instruction);
        }
        // move backward
        else if (startsWith($rule, ROUTING_MOVE_BACKWARD)) {
            $this->addMoveBackward($instruction);
        }
        // assignment
        else if (contains($rule, ":=")) {
            $this->addAssignment($instruction);
        }

        // multi line comment
        else if (startsWith($rule, "/*")) {
            $this->skipComments($this->cnt, $this->cnt);
        }
        // single line comment
        else if (startsWith($rule, "//")) {

            /* do nothing */
        }

        // end if
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDIF)) {
            $this->routing_output[$this->seid][] = "}\r\n";
        }
        // end while
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {
            $this->routing_output[$this->seid][] = "}\r\n";
        }
        // end do
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {
            $this->routing_output[$this->seid][] = "}\r\n";
        }
        // end group
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP)) {
            if ($this->fillclass == true) {
                return;
            }
            $this->group = false;
            $this->routing_output[$this->seid][] = "\$" . $this->lastgroup . "->ask();\r\n";
            $this->lastgroup = "";
        }
        // end subgroup
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP)) {
            // do nothing, not supported in xi
        }

        // question
        else {

            /* process rule */
            $rule = trim($instruction->getRule());
            $excluded = array();
            $rule = excludeText($rule, $excluded);

            // check for .KEEP
            if (endsWith($rule, ROUTING_IDENTIFY_KEEP)) {
                /* do nothing */
            }
            // check for .INSPECT
            else if (endsWith($rule, ROUTING_IDENTIFY_INSPECT)) {
                // do nothing, not supported in xi
            }
            // check for EXITFOR
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXITFOR) {
                $this->routing_output[$this->seid][] = "break;\r\n";                
            }
            // check for EXITWHILE
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXITWHILE) {
                $this->routing_output[$this->seid][] = "break;\r\n";
            }
            // check for EXIT
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXIT) {
                $this->routing_output[$this->seid][] = "exit;\r\n";
            }
            // check for .FILL
            else if (endsWith($rule, ROUTING_IDENTIFY_FILL)) {

                if ($this->fillclass == false) {
                    $this->addSetFill($instruction);
                } else {
                    $this->addErrorMessage(Language::errorFillCodeNoFill());
                }
            } else {

                // only allowed in main routing (not in fill code)
                if ($this->fillclass == false) {

                    /* check if this is a section */
                    $mod = $rule;

                    /* complex section statement */
                    if (contains($mod, ".") && endsWith($mod, ROUTING_IDENTIFY_INLINE) == false) {
                        while (contains($mod, ".")) {
                            $tofind = substr($mod, 0, stripos($mod, "."));
                            $section = $this->survey->getSectionByName($tofind);
                            if ($section->getName() == "") {
                                $this->addErrorMessage(Language::errorSectionNotFound($tofind));
                            }
                            $mod = substr($mod, stripos($mod, ".") + 1);
                        }
                    }

                    /* check if it is a section */
                    $section = $this->survey->getSectionByName($mod);
                    if ($section->getName() != "") {
                        $this->addSection($section->getSeid(), false);
                    } else {

                        /* check if this is a question of type section */
                        $var = $this->survey->getVariableDescriptiveByName(getBasicName($rule));
                        if ($var->getAnswerType() == ANSWER_TYPE_SECTION) {
                            $sectionid = $var->getSection();
                            $section = $this->survey->getSection($sectionid);
                            if ($section->getName() != "") {
                                $this->addSection($section->getSeid(), true);
                            } else {
                                $this->addErrorMessage(Language::errorSectionInVariableNotFound($rule));
                            }

                            // check for array
                            if ($var->isArray()) {
                                if (!contains($rule, "[")) {
                                    $this->addErrorMessage(Language::errorVariableNoArrayIndex($rule));
                                }
                            }
                            return;
                        }

                        $this->addQuestion($instruction);
                    }
                }
                // in fill code, then none of this is allowed
                else {
                    $this->addErrorMessage(Language::errorFillCodeOnlyAssignments());
                }
            }
        }
    }
    
    function addSetFill($instruction) {
        
        $_SESSION['SURVEY_LANGUAGE'] = $this->survey->getDefaultLanguage($this->mode); // use main language
        $excluded = array();
        $text = trim(str_ireplace(ROUTING_IDENTIFY_FILL, "", $instruction->getRule()));
        $rule = excludeText($text, $excluded);
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        
        $newxi = new XiCompiler($this->suid, $this->currentmode, $this->version);
        $var = $this->survey->getVariableDescriptiveByName($rule);
        $newxi->generateFill($this->seid, $var);
        
        // copy over fill options to question text
        foreach ($this->languages as $l) {
            $_SESSION['SURVEY_LANGUAGE'] = $l;
            $this->routing_output[$this->seid][] = "\$" . $var->getName() . "->setQuestionText('" . str_replace("\r\n", "\n", $this->handleFills($var->getFillText())) . "'," . $l . ");\r\n";
        }        
        
        $_SESSION['SURVEY_LANGUAGE'] = $this->survey->getDefaultLanguage($this->mode); // use main language
        $this->routing_output[$this->seid][] = implode("", $newxi->getRoutingOutput($this->seid));        
    }
    
    function addSection($seid, $questionsection = false) {
        
        $_SESSION['SURVEY_LANGUAGE'] = $this->survey->getDefaultLanguage($this->mode); // use main language                
        $newxi = new XiCompiler($this->suid, $this->currentmode, $this->version);
        $newxi->generateRouting($seid);        
        $this->routing_output[$this->seid][] = implode("", $newxi->getRoutingOutput($seid));        
    }

    function addQuestion($instruction) {


        // questions only allowed in main routing (not in fill code)
        if ($this->fillclass == true) {
            return;
        }

        $rule = trim($instruction->getRule());

        $rgid = trim($instruction->getRgid());
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // check for .INLINE
        $inline = false;
        if (endsWith($rule, ROUTING_IDENTIFY_INLINE)) {
            $inline = true;
            $pos = strripos($rule, ROUTING_IDENTIFY_INLINE);
            $rule = substr($rule, 0, $pos);
        }
        
        // check for array
        $var = $this->survey->getVariableDescriptiveByName(getBasicName($rule)); // new VariableDescriptive(); 
        if ($var->isArray()) {
            if (!contains($rule, "[")) {
                $this->addErrorMessage(Language::errorVariableNoArrayIndex($rule));
            }
        }
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {

            $stmtstemp = $parser->parse("<?php " . $rule . " ?>");
            // only one statement (no ; allowed in assignment right hand side)
            
            $stmttemp = new PHPParser_Node_Arg($stmtstemp[0]); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmttemp);
            $cleanup = false;
            
            if ($stmttemp->value instanceof PHPParser_Node_Expr_MethodCall) {
                $st = new PHPParser_Node_Expr_MethodCall($stmttemp->value->var, FUNCTION_XI_ASK);
                $cleanup = true;
            } else if ($stmttemp->value instanceof PHPParser_Node_Expr_Concat) {
                $st = new PHPParser_Node_Expr_MethodCall($stmttemp->value, FUNCTION_XI_ASK);
            } else {
                $rule = showModuleNotations($rule, TEXT_MODULE_DOT);
                $st = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable($rule), FUNCTION_XI_ASK); /* no brackets */
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorVariableInvalid());
            return;
        }

        $fillcall = $this->printer->prettyPrint(array($st));

        // clean up
        if ($cleanup) {
            $fillcall = str_replace(" . (", "", $fillcall);
            $fillcall = str_replace(" . ])", "]", $fillcall);
        }

        if ($this->group == false) {
            $this->routing_output[$this->seid][] = $fillcall . "\r\n";
        } else {
            $this->routing_output[$this->seid][] = "\$" . $this->lastgroup . "->addQuestion(" . str_replace("->ask();", "", $fillcall) . ");\r\n";
        }
    }

    function addIf($instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        $rgidafter = $rgid;

        $ifstmt = $this->analyzeIf($rule);
        if (!$ifstmt) {
            return;
        }

        if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
            $ifnode = new PHPParser_Node_Stmt_If($ifstmt);
        } else {
            $ifnode = new PHPParser_Node_Stmt_ElseIf($ifstmt);
        }
        $fillcall = $this->printer->prettyPrint(array($ifnode));

        // clean up
        //if ($cleanup) {
            $fillcall = str_replace(" . (", "", $fillcall);
            $fillcall = str_replace(" . ])", "]", $fillcall);
        //}
        $fillcall = str_replace("\n", "", trim(substr($fillcall, 0, strlen($fillcall) - 1))) . "\r\n"; // remove closing "}"
        // elseif
        if (!startsWith($rule, ROUTING_IDENTIFY_IF)) {
            $fillcall = "}\r\n" . $fillcall;
        }
        $this->routing_output[$this->seid][] = $fillcall;
    }

    function analyzeIf($rule, $print = false) {
        /* multi-line if */

        if (endsWith(strtoupper($rule), ROUTING_THEN) == false) {

            $found = false;
            for ($cnt = ($this->cnt + 1); $cnt <= sizeof($this->instructions); $cnt++) {
                if (isset($this->instructions[$cnt])) {
                    $text = trim($this->instructions[$cnt]->getRule());
                    if (startsWith($text, "/*")) {
                        $this->skipComments($cnt, $cnt);
                    } else if (startsWith($text, "//")) {
                        
                    } else {

                        $rule .= " " . $text;
                        if (endsWith(strtoupper($rule), ROUTING_THEN) == true) {

                            $this->cnt = $cnt;
                            $rgidafter = $this->instructions[$cnt]->getRgid();
                            $found = true;
                            break;
                        }
                    }
                }
            }

            if ($found == false) {
                if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
                    $this->addErrorMessage(Language::errorIfMissingThen());
                } else {
                    $this->addErrorMessage(Language::errorElseIfMissingThen());
                }
                return;
            }
        }

        // exclude text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        /* strip (else)if and then */
        $iftype = "";
        if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
            $pos = stripos($rule, ROUTING_IF);
            $rule = trim(substr($rule, $pos + strlen(ROUTING_IF)));
            $pos = strripos($rule, ROUTING_THEN);
            if ($pos < 1) {
                $this->addErrorMessage(Language::errorIfMissingThen()); // TODO: ADD METHOD!
                return;
            }
            $rule = trim(substr($rule, 0, strlen($rule) - strlen(ROUTING_THEN)));
            $iftype = ROUTING_IDENTIFY_IF;
        } else {
            $pos = stripos($rule, ROUTING_ELSEIF);
            $rule = trim(substr($rule, $pos + strlen(ROUTING_ELSEIF)));
            $pos = strripos($rule, ROUTING_THEN);
            if ($pos < 1) {
                $this->addErrorMessage(Language::errorElseIfMissingThen()); // TODO: ADD METHOD!
                return;
            }

            $rule = trim(substr($rule, 0, strlen($rule) - strlen(ROUTING_THEN)));
            $iftype = ROUTING_IDENTIFY_ELSEIF;
        }

        // prepare string for parsing     
        $or = $rule;
        $rule = str_ireplace(LOGICAL_OR, " || ", $rule);
        $rule = str_ireplace(LOGICAL_AND, " && ", $rule);
        $rule = str_replace("!=", "<>", $rule);
        $rule = str_replace("<=", "<<", $rule);
        $rule = str_replace(">=", ">>", $rule);
        $rule = str_replace("=", "==", $rule);
        $rule = str_replace("<<", "<=", $rule);
        $rule = str_replace(">>", ">=", $rule);
        $rule = str_replace("<>", "!=", $rule);

        $rule = $this->prepare(array('/' . PATTERN_WORDBREAK . '(' . PATTERN_CASE_INSENSITIVE . LOGICAL_NOT . ')' . PATTERN_WORDBREAK . '/'), array('!'), $rule);
        $rule = $this->prepare(array('/' . PATTERN_BREAKSTART . PATTERN_NOTEQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_RESPONSE . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_EQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_RESPONSE . ')' . PATTERN_WORDBREAK . '/'), array('((\1 == ' . ANSWER_RF . LOGICAL_OR . ' \1 == ' . ANSWER_DK . LOGICAL_OR . ' \1 == ' . ANSWER_NA . LOGICAL_OR . ' ' . FUNCTION_IS_NULL . '(\1)) ' . LOGICAL_AND . ' \1 != "0")', '((\1 != ' . ANSWER_RF . LOGICAL_AND . ' \1 != ' . ANSWER_DK . LOGICAL_AND . ' \1 != ' . ANSWER_NA . LOGICAL_AND . ' !' . FUNCTION_IS_NULL . '(\1)) ' . LOGICAL_OR . ' \1 == "0")'), $rule);
        $rule = $this->prepare(array('/' . PATTERN_BREAKSTART . PATTERN_EQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_RF . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_NOTEQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_RF . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_EQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_DK . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_NOTEQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_DK . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_EQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_NA . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_NOTEQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_NA . ')' . PATTERN_WORDBREAK . '/'), array('((\1 == ' . ANSWER_RF . ') ' . LOGICAL_AND . ' \1 != "0")', '((\1 != ' . ANSWER_RF . ') ' . LOGICAL_OR . ' \1 == "0")', '((\1 == ' . ANSWER_DK . ') ' . LOGICAL_AND . ' \1 != "0")', '((\1 != ' . ANSWER_DK . ') ' . LOGICAL_OR . ' \1 == "0")', '((\1 == ' . ANSWER_NA . ') ' . LOGICAL_AND . ' \1 != "0")', '((\1 != ' . ANSWER_NA . ') ' . LOGICAL_OR . ' \1 == "0")'), $rule);
        $rule = $this->prepare(array('/' . PATTERN_BREAKSTART . PATTERN_EQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_EMPTY . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_NOTEQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_EMPTY . ')' . PATTERN_WORDBREAK . '/'), array(FUNCTION_IS_NULL . '(\1) == ' . LOGICAL_TRUE, '!(' . FUNCTION_IS_NULL . '(\1) == ' . LOGICAL_TRUE . ')'), $rule);
        $rule = $this->prepare(array('/' . PATTERN_BREAKSTART . PATTERN_EQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_NONRESPONSE . ')' . PATTERN_WORDBREAK . '/', '/' . PATTERN_BREAKSTART . PATTERN_NOTEQUALTO . PATTERN_BREAKEND . '(' . PATTERN_CASE_INSENSITIVE . ANSWER_NONRESPONSE . ')' . PATTERN_WORDBREAK . '/'), array('((\1 == ' . ANSWER_DK . LOGICAL_OR . ' \1 == ' . ANSWER_RF . LOGICAL_OR . ' \1 == ' . ANSWER_NA . LOGICAL_OR . ' ' . FUNCTION_IS_NULL . '(\1)) ' . LOGICAL_AND . ' \1 != "0")', '((\1 != ' . ANSWER_DK . LOGICAL_AND . ' \1 != ' . ANSWER_RF . LOGICAL_AND . ' \1 != ' . ANSWER_NA . LOGICAL_AND . ' !' . FUNCTION_IS_NULL . '(\1)) ' . LOGICAL_OR . ' \1 == "0")'), $rule);

        /* handle 'variable in [1,2]' */
        $find = array();
        $locate = '/' . PATTERN_ALPHANUMERIC . PATTERN_CASE_INSENSITIVE . LOGICAL_IN . '\[' . PATTERN_ALPHANUMERIC . '\]/i'; // /i for case insensitive
        if (preg_match_all($locate, $rule, $find, PREG_SET_ORDER)) {
            foreach ($find as $found) {
                $process = splitString("/,/", $found[2]);
                $resultarray = array();
                foreach ($process as $p) {
                    $parray = splitString("/\.\./", $p);
                    if (sizeof($parray) > 1) {
                        $result = "";
                        for ($i = $parray[0]; $i <= $parray[1]; $i++) {
                            $result .= $i;
                            if (($i + 1) <= $parray[1]) {
                                $result .= ",";
                            }
                        }
                        $resultarray[] = $result;
                    } else {
                        $resultarray[] = trim($parray[0]);
                    }
                }
                $rule = $this->prepare(array($locate), array(FUNCTION_IN_ARRAY . "(" . $found[1] . ", array(" . implode(",", $resultarray) . "), 1)"), $rule, 1);
            }
        }

        // handle 1 in variable (set of enumerated reference)
        $find = array();
        $locate = '/' . PATTERN_ALPHANUMERIC . PATTERN_CASE_INSENSITIVE . LOGICAL_IN . PATTERN_ALPHANUMERIC . '/i'; // /i for case insensitive
        if (preg_match_all($locate, $rule, $find, PREG_SET_ORDER)) {
            foreach ($find as $found) {
                $rule = $this->prepare(array($locate), array(FUNCTION_IN_ARRAY . "(" . $found[1] . ", explode('" . SEPARATOR_SETOFENUMERATED . "'," . $found[2] . "), 1)"), $rule, 1);
            }
        }

        // replace [ and ] with ( and ), so the parser doesn't break
        // (we deal with these cases in the updateVariables function)
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);

        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        try {

            $parsestmts = $parser->parse("<?php " . $rule . " ?>");
            $ifstmt = $parsestmts[0]; // only one statement (no ; allowed in assignment right hand side)

            // complex expression, then wrap in fake argument object
            if ($ifstmt instanceof PHPParser_Node_Expr) { //$ifstmt instanceof PHPParser_Node_Expr_FuncCall || 
                $ifstmt = new PHPParser_Node_Arg($ifstmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
                $this->updateVariables($ifstmt);
                $ifstmt = $ifstmt->value;
            } else {
                $this->updateVariables($ifstmt);
            }

            if ($print == true) {
                if (sizeof($this->groups) == 0 && $this->fillclass != true) {
                    $this->printer = new PHPParser_PrettyPrinter_Default();
                    return $this->printer->prettyPrint(array($ifstmt));
                } else {
                    return $ifstmt; // we want the whole statement for a do while group statement
                }
            }
            return $ifstmt;
        } catch (PHPParser_Error $e) {

            if ($iftype == ROUTING_IDENTIFY_ELSEIF) {
                $this->addErrorMessage(Language::errorElseIfInvalid());
            } else {
                $this->addErrorMessage(Language::errorIfInvalid());
            }
            if ($print) {
                return '';
            }
            return null;
        }
    }

    function prepare($lookup, $new, $string, $limit = -1) {
        return preg_replace($lookup, $new, $string, $limit);
    }

    function addElse($instruction) {
        $this->routing_output[$this->seid][] = "}\r\nelse {\r\n";
    }

    function addAssignment($instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        // hide quoted text
        $excluded = array();
        $tempparts = splitString("/:=/", $rule, PREG_SPLIT_NO_EMPTY, 2);
        $checkvar = trim($tempparts[0]);
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        /* split into two */
        $parts = splitString("/:=/", $rule, PREG_SPLIT_NO_EMPTY, 2);
        if (sizeof($parts) != 2) {
            $this->addErrorMessage(Language::errorAssignmentInvalid());
            return;
        }

        /* create assignment */
        $assignfunctionnode = $this->factory->method($function);
        $assignfunctionnode->makePrivate();
        $lefthand = trim(includeText($parts[0], $excluded));
        $righthand = trim(includeText($parts[1], $excluded));

        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {

            /* left hand */
            $stmtsleft = $parser->parse("<?php " . $lefthand . "?>");
            $cleanup = false;

            // only one statement (no ; allowed in assignment right hand side)
            $stmtleft = new PHPParser_Node_Arg($stmtsleft[0]); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmtleft);

            if ($stmtleft->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmtleft = $stmtleft->value->var;
            }
            // a non-bracketed field
            else {

                /* not a constant, which happens if the counter field does not exist */
                if ($stmtleft->value->name instanceof PHPParser_Node_Expr_MethodCall) {
                    $stmtleft = $stmtleft->value->name->var;
                } else {
                    $stmtleft = new PHPParser_Node_Expr_Variable($lefthand);
                }
            }

            /* right hand EMPTY --> keyword in PHP */
            if (strtoupper($righthand) == VARIABLE_VALUE_EMPTY) {
                $righthand = "'" . strtoupper($righthand) . "'";
            }
            $stmts = $parser->parse("<?php " . $righthand . "?>");

            // only one statement (no ; allowed in assignment right hand side)
            $stmt = new PHPParser_Node_Arg($stmts[0]);
            $this->updateVariables($stmt);
            $stmtright = $stmt;
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorAssignmentInvalid());
            return;
        }

        // create assignment node
        $n = new PHPParser_Node_Expr_MethodCall($stmtleft, FUNCTION_XI_GET_ANSWER, array($stmtright));
        $fillcall = $this->printer->prettyPrint(array($n));

        // clean up
        $fillcall = str_replace(" . (", "", $fillcall);
        $fillcall = str_replace(" . ])", "]", $fillcall);
        $fillcall = str_replace("\n", "", trim(substr($fillcall, 0, strlen($fillcall) - 1))) . ";\r\n"; // remove closing "}"

        $this->routing_output[$this->seid][] = $fillcall;
    }

    function addForLoop($instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        $rgidafter = $rgid;

        // hide text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

        // strip FOR
        $reversefor = 1;
        if (startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {
            $rule = substr($rule, stripos($rule, ROUTING_IDENTIFY_FORREVERSE) + strlen(ROUTING_IDENTIFY_FORREVERSE));
            $reversefor = 2;
        } else {
            $rule = substr($rule, stripos($rule, ROUTING_IDENTIFY_FOR) + strlen(ROUTING_IDENTIFY_FOR));
        }

        /* multi-line for */

        //if ($pos < 0) {
        if (endsWith(strtoupper($rule), ROUTING_IDENTIFY_DO) == false) {
            for ($cnt = ($this->cnt + 1); $cnt <= sizeof($this->instructions); $cnt++) {
                if (isset($this->instructions[$cnt])) {
                    $text = trim($this->instructions[$cnt]->getRule());
                    if (startsWith($text, "/*")) {
                        $this->skipComments($cnt, $cnt);
                    } else if (startsWith($text, "//")) {
                        
                    } else {

                        $rule .= " " . $text;
                        if (endsWith(strtoupper($rule), ROUTING_IDENTIFY_DO) == true) {
                            $this->cnt = $cnt;
                            $rgidafter = $this->instructions[$cnt]->getRgid();
                            break;
                        }
                    }
                }
            }
        }

        $pos = strripos(strtoupper($rule), ROUTING_IDENTIFY_DO);
        if ($pos < 1) {
            $this->addErrorMessage(Language::errorForLoopMissingDo());
            return;
        }

        // strip do
        $rule = trim(substr($rule, 0, $pos));

        if (!contains(strtoupper($rule), " TO ")) {
            $this->addErrorMessage(Language::errorForLoopMissingTo());
            return;
        }

        if (!contains(strtoupper($rule), ":=")) {
            $this->addErrorMessage(Language::errorForLoopMissingAssignment());
            return;
        }


        // determine min and max
        $bounds = preg_split("/ to /i", $rule);
        $counterplusstart = splitString("/:=/", $bounds[0]);
        $counterfield = includeText($counterplusstart[0], $excluded);
        $minimum = includeText($counterplusstart[1], $excluded);
        $maximum = includeText($bounds[1], $excluded);

        // check for array
        if (!is_numeric($minimum)) {
            $var = $this->survey->getVariableDescriptiveByName(getBasicName($minimum)); // new VariableDescriptive(); 
            if ($var->isArray()) {
                if (!contains($minimum, "[")) {
                    $this->addErrorMessage(Language::errorVariableNoArrayIndex(strtolower(getBasicName($minimum))));
                }
            }
        }
        if (!is_numeric($maximum)) {
            $var = $this->survey->getVariableDescriptiveByName(getBasicName($maximum)); // new VariableDescriptive(); 
            if ($var->isArray()) {
                if (!contains($maximum, "[")) {
                    $this->addErrorMessage(Language::errorVariableNoArrayIndex(strtolower(getBasicName($maximum))));
                }
            }
        }

        // replace [ and ] with ( and ), so the parser doesn't break
        // (we deal with these cases in the updateVariables function)
        $counterfield = str_replace("[", TEXT_BRACKET_LEFT, $counterfield);
        $counterfield = str_replace("]", TEXT_BRACKET_RIGHT, $counterfield);
        $minimum = str_replace("[", TEXT_BRACKET_LEFT, $minimum);
        $minimum = str_replace("]", TEXT_BRACKET_RIGHT, $minimum);
        $maximum = str_replace("[", TEXT_BRACKET_LEFT, $maximum);
        $maximum = str_replace("]", TEXT_BRACKET_RIGHT, $maximum);


        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {

            $stmts = $parser->parse("<?php " . $minimum . "?>");

            // only one statement (no ; allowed in loop minimum)
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);
            $min = $stmt;

            $stmts = $parser->parse("<?php " . $maximum . "?>");
            // only one statement (no ; allowed in loop maximum)
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);
            $max = $stmt;

            $stmts = $parser->parse("<?php " . $counterfield . "?>");

            // only one statement (no ; allowed in loop maximum)
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);

            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $counter = $stmt->value->var;
            } else {

                /* not a constant, which happens if the counter field does not exist */
                if ($stmt->value->name instanceof PHPParser_Node_Expr_MethodCall) {
                    $counter = $stmt->value->name->var;
                } else {
                    $counter = new PHPParser_Node_Expr_Variable($counterfield);
                }
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorForLoopInvalid());
            return;
        }

        // create for loop
        $temp = new PHPParser_Node_Expr_MethodCall($counter, FUNCTION_XI_GET_ANSWER, array($min));
        $temp1 = new PHPParser_Node_Expr_MethodCall($counter, FUNCTION_XI_GET_ANSWER, array());
        $temp2 = new PHPParser_Node_Expr_SmallerOrEqual($temp1, $max->value);
        $temp4 = new PHPParser_Node_Expr_Plus($temp1, new PHPParser_Node_Scalar_LNumber(1));
        $temp3 = new PHPParser_Node_Expr_MethodCall($counter, FUNCTION_XI_GET_ANSWER, array($temp4));
        $fillcall = "for (" . $this->printer->prettyPrint(array($temp)) . " " . $this->printer->prettyPrint(array($temp2)) . " " . $this->printer->prettyPrint(array($temp3));

        // clean up
        $fillcall = str_replace(" . (", "", $fillcall);
        $fillcall = str_replace(" . ])", "]", $fillcall);
        $fillcall = str_replace("\n", "", trim(substr($fillcall, 0, strlen($fillcall) - 1))) . ") {\r\n"; // remove closing "}"

        $this->routing_output[$this->seid][] = $fillcall;
    }

    function addGroup($instruction) {

        // $TA012_1ToA012_3Group = new QuestionGroup('TA012_1ToA012_3Group', array($A012_1, $A012_2, $A012_3), $TGroupTemplate_NoWrap);
        if ($this->fillclass == true) {
            return;
        }

        $this->group = true;

        $rule = trim($instruction->getRule());

        $rgid = trim($instruction->getRgid());
        $this->actions[] = $rgid;

        $group = explode(".", $rule);
        if (sizeof($group) < 2 || trim($group[1]) == "") {
            $this->addErrorMessage(Language::errorGroupTemplateNotFound());
            return;
        }

        $this->lastgroup = $group[1];
        $this->routing_output[$this->seid][] = "\$" . $group[1] . " = new QuestionGroup('" . $group[1] . "', array(), \$TGroupTemplate_NoWrap);\r\n";
    }

}


?>