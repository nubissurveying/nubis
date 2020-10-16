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

class Compiler {

    private $factory;
    private $printer;
    private $doaction;
    private $doaction_cases;
    private $actions;
    private $isinlinefield;
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
    private $fillclass; // indicates whether we are generating a section engine or set fill class
    private $checkclass; // indicates whether we are generating a check class
    private $currentrgid;
    private $messages;
    private $ifreset;
    private $elseifreset;
    private $elsereset;
    private $whiles; // keeps track of rgid's of whiles
    private $lastwhileactions; // keeps track of last action of a while
    private $whileactions; // keeps track of actions of whiles
    private $whilenextrgids; // keeps track of while next rgids for exitwhile
    private $ifrgidafter;
    private $sectionname;
    private $extranode;

    function __construct($suid, $version) {
        $this->suid = $suid;
        $this->version = $version;
        $this->survey = new Survey($this->suid);
        $this->extranode = null;
    }

    /* FILL FUNCTIONS */

    function addSetFill($function, &$node, $instruction) {

        $this->setfills[] = $instruction;



        /* get details */

        $rgid = $instruction->getRgid();
        //$rule = trim(str_ireplace(".FILL", "", $instruction->getRule()));

        /* add call to show/return question */
        $excluded = array();
        $text = trim(str_ireplace(ROUTING_IDENTIFY_FILL, "", $instruction->getRule()));
        $rule = excludeText($text, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);

        /* create fill function */
        $fillfunctionnode = $this->factory->method($function);
        $fillfunctionnode->makePrivate();

        /* parse to find any errors */
        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {

            $stmts = $parser->parse("<?php " . $rule . " ?>");
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);

            // fake method call for Q1[1,1] reference
            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value);
            } else {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value->name);
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorFILLInvalid());
            return;
        }

        $rule = str_replace(TEXT_BRACKET_LEFT, "[", $rule);
        $rule = str_replace(TEXT_BRACKET_RIGHT, "]", $rule);

        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(getBasicName($rule)));
        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_SET_FILL_VALUE)), $args);
        $fillfunctionnode->addStmt($stmt);



        /* get next (real: question or complex statement) rgid for after the fill execution */
        $nextrgid = $this->findNextStatementAfterQuestion($rgid);

        /* not in for or while loop */
        if (sizeof($this->loops) == 0 && sizeof($this->whiles) == 0) {

            // not in group OR in group but not group action, then add link to next action
            if (sizeof($this->groups) == 0 || (sizeof($this->groups) > 0 && !inArray($nextrgid, end($this->groupactions)))) {
                $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                $fillfunctionnode->addStmt($stmt);
            }
        } else {

            // loop action
            if (inArray($rgid, $this->loopactions[end($this->loops)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
                $fillfunctionnode->addStmt($stmt);
            }
            // while action
            else if (inArray($rgid, $this->whileactions[end($this->whiles)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
                $fillfunctionnode->addStmt($stmt);
            }

            // check for end of for loop
            $enddo = -1;
            if (sizeof($this->loops) > 0) {
                $enddo = $this->findEndDo(end($this->loops));
            }
            $endwhile = -1;
            if (sizeof($this->whiles) > 0) {
                $endwhile = $this->findEndWhile(end($this->whiles));
            }
            if ($enddo != -1 && $endwhile != -1) {
                if ($endwhile < $enddo) {
                    if ($nextrgid > $endwhile) {
                        $nextrgid = end($this->whiles);
                    }
                } else {
                    if ($nextrgid > $enddo) {
                        $nextrgid = end($this->loops);
                    }
                }
            } else if ($enddo != -1) {
                if ($nextrgid > $enddo) {
                    $nextrgid = end($this->loops);
                }
            } else if ($endwhile != -1) {
                if ($nextrgid > $endwhile) {
                    $nextrgid = end($this->whiles);
                }
            }

            // last loop action, then link back to beginning of loop
            if ($this->lastloopactions[end($this->loops)] == $rgid) {

                // not a group, then link to beginning of loop!
                if (sizeof($this->groups) == 0) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($this->loops[sizeof($this->loops) - 1]));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                    $fillfunctionnode->addStmt($stmt);
                }
            }
            // last while action, then link back to beginning of while
            else if ($this->lastwhileaction[end($this->whiles)] == $rgid) {

                // not a group, then link to beginning of while!
                if (sizeof($this->groups) == 0) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($this->whiles[sizeof($this->whiles) - 1]));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                    $fillfunctionnode->addStmt($stmt);
                }
            }
            // not last loop or while action, then link to action IF not itself a loop or while action
            else {

                /* in group, then link */
                if (sizeof($this->groups) > 0) {

                    // don't link if the next statement is the loop OR the last loop action OR OR the last while action OR it is a group action
                    if ($nextrgid != end($this->whiles) && $nextrgid != end($this->loops) && $nextrgid < $this->groupsend[end($this->groups)] && !inArray($nextrgid, end($this->loopactions)) && !inArray($nextrgid, end($this->whileactions)) && !inArray($nextrgid, end($this->groupactions))) {
                        $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                        $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction));
                        $fillfunctionnode->addStmt($stmt);
                    }
                }
                // not in a group 
                else {

                    // don't link if the next statement is the loop OR the last loop action OR it is a group action
                    //if ($nextrgid != end($this->loops)) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                    $fillfunctionnode->addStmt($stmt);
                    //}
                }
            }
        }

        /* add fill function node */
        $node->addStmt($fillfunctionnode);
    }

    function loadSetFills() {
        global $db;
        $q = "select setfills from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            if ($row["setfills"] != "") {
                return unserialize(gzuncompress($row["setfills"]));
            }
        }
        return array();
    }

    function generateSetFills($variables = array(), $remove = false, $compile = true) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* create factory, printer and root node */

        $this->factory = new PHPParser_BuilderFactory();

        $this->printer = new PHPParser_PrettyPrinter_Default();
        $this->messages = array();
        //$setfillclasses = array();

        $this->fillclass = true;

        /* load any existing set fill pairings from context */
        $setfillclasses = $this->loadSetFills();

        /* go through all variable(s) if none provided */
        global $db;
        if (sizeof($variables) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_variables where suid=" . $this->suid . " order by vsid asc";
            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $variables[] = new VariableDescriptive($row);
                    }
                }
            }
        }

        foreach ($variables as $var) {
            if ($remove == false) {
                $code = $var->getFillCode();
                if (trim($code) != "") {

                    $this->currentfillvariable = $var->getName();
                    //$rule = trim(str_ireplace(".FILL", "", $s->getRule()));            
                    //$rgid = $s->getRgid();

                    /* store pairing */
                    //$setfillarray[strtoupper(getBasicName($rule))] = $rgid;

                    $rootnode = $this->factory->class(CLASS_SETFILL . "_" . $this->currentfillvariable)->extend(CLASS_BASICFILL);

                    /* preset trackers */

                    $this->looptimes = 1;

                    $this->lasttimesloop = array();

                    $this->lastloopactions = array();

                    $this->loops = array();
                    $this->groups = array();
                    $this->groupsend = array();
                    $this->groupactions = array();
                    $this->instructions = array();
                    $this->whiles = array();
                    $this->lastwhileactions = array();

                    $this->doaction_cases = array();
                    $this->actions = array();
                    $stmts = array();

                    //$var = $this->survey->getVariableDescriptiveByName(getBasicName($rule));


                    $fillrules = explode("\r\n", $code);
                    $cnt = 1;

                    foreach ($fillrules as $fillrule) {

                        $this->instructions[$cnt] = new RoutingInstruction($var->getSuid(), $var->getSeid(), $cnt, rtrim($fillrule));

                        $cnt++;
                    }


                    /* process setfillvalue cases */
                    for ($this->cnt = 1; $this->cnt <= sizeof($this->instructions); $this->cnt++) {

                        if (isset($this->instructions[$this->cnt])) {
                            $this->addRule($rootnode, $this->instructions[$this->cnt]);
                        }
                    }

                    /* add end */

                    $stmts[] = new PHPParser_Node_Stmt_Break();

                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(null, $stmts);



                    /* add doAction function */

                    $this->doaction = $this->factory->method(FUNCTION_DO_ACTION);

                    $this->doaction->makePublic();

                    $param = new PHPParser_Builder_Param('rgid');

                    $param->setDefault("");

                    $this->doaction->addParam($param);

                    $doactioncond = new PHPParser_Node_Expr_Variable("rgid");

                    $this->doaction->addStmt(new PHPParser_Node_Stmt_Switch($doactioncond, $this->doaction_cases));

                    $rootnode->addStmt($this->doaction);


                    /* add getFirstAction function */
                    $firstaction = $this->factory->method(FUNCTION_GET_FIRST_ACTION);
                    $firstaction->makePublic();
                    $firstaction->addStmt(new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_LNumber($this->actions[0])));
                    $rootnode->addStmt($firstaction);

                    /* get statements */

                    $stmts = array($rootnode->getNode());



                    /* generate code for set fill class */

                    //$setfillclasses[$rgid] = $this->printer->prettyPrint($stmts);
                    $setfillclasses[strtoupper($this->currentfillvariable) . getSurveyLanguage() . getSurveyMode()] = $this->printer->prettyPrint($stmts);

                } else {

                    // no fill code, then remove
                    if (isset($setfillclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())])) {
                        unset($setfillclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())]);
                    }
                }
            } else {
                if (isset($setfillclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())])) {
                    unset($setfillclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())]);
                }
            }
        }

        if ($compile == true) {

            /* check for first time */
            $this->addContext();

            /* store in db */
            global $db;
            $bp = new BindParam();
            $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($setfillclasses), 9));
            $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
            $bp->add(MYSQL_BINDING_INTEGER, $this->version);
            $query = "update " . Config::dbSurvey() . "_context set setfills = ? where suid=? and version = ? ";
            $db->executeBoundQuery($query, $bp->get());
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        return $this->messages;
    }

    function loadGetFillClasses() {
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            if ($row["getfills"] != "") {
                return unserialize(gzuncompress($row["getfills"]));
            }
        }
        return array();
    }

    function updateGetFills($text = array(), $compile = true) {

        if (sizeof($text) == 0) {
            return;
        }
        /* keep track */
        $getfillclasses = $this->loadGetFillClasses();
        $fills = getReferences(implode(" ", $text), INDICATOR_FILL);

        /* go through fills */
        foreach ($fills as $fill) {

            /* not processed before */
            if ($fill != "") {
                $getfillclasses['"' . strtoupper($fill) . '"'] = $this->addFill($fill);
            }
        }

        if ($compile == true) {
            /* check for first time */
            $this->addContext();

            /* store in db */
            global $db;
            $bp = new BindParam();
            $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($getfillclasses), 9));
            $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
            $bp->add(MYSQL_BINDING_INTEGER, $this->version);
            $query = "update " . Config::dbSurvey() . "_context set getfills = ? where suid=? and version = ? ";
            $db->executeBoundQuery($query, $bp->get());
        }
    }

    function updateGetFillsNoValues($text = array()) {

        if (sizeof($text) == 0) {
            return;
        }
        /* keep track */
        $getfillclasses = $this->loadGetFillClasses();
        $fills = getReferences(implode(" ", $text), INDICATOR_FILL_NOVALUE);

        /* go through fills */
        foreach ($fills as $fill) {

            /* not processed before */
            if ($fill != "") {
                $getfillclasses['"' . strtoupper(INDICATOR_FILL_NOVALUE . $fill) . '"'] = $this->addFillNoValue($fill);
            }
        }

        /* check for first time */
        $this->addContext();

        /* store in db */
        global $db;
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($getfillclasses), 9));
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
        $query = "update " . Config::dbSurvey() . "_context set getfills = ? where suid=? and version = ? ";
        $db->executeBoundQuery($query, $bp->get());
    }

    function generateGetFillsGroups($groups = array()) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* go through all group(s) if none provided */
        global $db;
        if (sizeof($groups) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_groups where suid=" . $this->suid . " order by gid asc";

            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $groups[] = new Group($row);
                    }
                }
            }
        }

        /* gather all texts */
        $text = array();
        foreach ($groups as $group) {
            $text[] = $group->getTableID();
            $text[] = $group->getTableWidth();
            $text[] = $group->getQuestionColumnWidth();
            $text[] = $group->getErrorMessageExclusive();
            $text[] = $group->getErrorMessageInclusive();
            $text[] = $group->getErrorMessageMinimumRequired();
            $text[] = $group->getErrorMessageExactRequired();
            $text[] = $group->getErrorMessageMaximumRequired();
            $text[] = $group->getErrorMessageUniqueRequired();
            $text[] = $group->getPageHeader();
            $text[] = $group->getPageFooter();
            $text[] = $group->getExactRequired();
            $text[] = $group->getMaximumRequired();
            $text[] = $group->getMinimumRequired();
            $text[] = $group->getScripts();
            $text[] = $group->getLabelBackButton();
            $text[] = $group->getLabelNextButton();
            $text[] = $group->getLabelDKButton();
            $text[] = $group->getLabelRFButton();
            $text[] = $group->getLabelNAButton();
            $text[] = $group->getLabelUpdateButton();
            $text[] = $group->getLabelRemarkButton();
            $text[] = $group->getLabelRemarkSaveButton();
            $text[] = $group->getLabelCloseButton();
            $text[] = $group->getCustomTemplate();
            $text[] = $group->getProgressBarWidth();
            $text[] = $group->getProgressBarValue();

            $text[] = $group->getKeyboardBindingBack();
            $text[] = $group->getKeyboardBindingNext();
            $text[] = $group->getKeyboardBindingUpdate();
            $text[] = $group->getKeyboardBindingClose();
            $text[] = $group->getKeyboardBindingDK();
            $text[] = $group->getKeyboardBindingRF();
            $text[] = $group->getKeyboardBindingNA();
            $text[] = $group->getKeyboardBindingRemark();

            $text[] = $group->getOnBack();
            $text[] = $group->getOnNext();
            $text[] = $group->getOnDK();
            $text[] = $group->getOnRF();
            $text[] = $group->getOnNA();
            $text[] = $group->getOnUpdate();
            $text[] = $group->getOnLanguageChange();
            $text[] = $group->getOnModeChange();
            $text[] = $group->getOnVersionChange();

            $text[] = $group->getClickBack();
            $text[] = $group->getClickNext();
            $text[] = $group->getClickDK();
            $text[] = $group->getClickRF();
            $text[] = $group->getClickNA();
            $text[] = $group->getClickUpdate();
            $text[] = $group->getClickLanguageChange();
            $text[] = $group->getClickModeChange();
            $text[] = $group->getClickVersionChange();
        }

        /* update get fills */
        $this->updateGetFills($text);
        $this->updateGetFillsNoValues($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateGetFillsSections($sections = array()) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* go through all group(s) if none provided */
        global $db;
        if (sizeof($sections) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_sections where suid=" . $this->suid . " order by seid asc";

            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $sections[] = new Section($row);
                    }
                }
            }
        }

        /* gather all texts */
        $text = array();
        foreach ($sections as $section) {
            $text[] = $section->getHeader();
            $text[] = $section->getFooter();
        }

        /* update get fills */
        $this->updateGetFills($text);
        $this->updateGetFillsNoValues($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateGetFillsRouting($seid, $compile = true) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* go through all group(s) if none provided */
        global $db;
        $text = array();
        $q = "select rule from " . Config::dbSurvey() . "_routing where suid=" . $this->suid . " and seid=" . $seid . " and rule like '%group.^%' order by rgid asc";
        if ($result = $db->selectQuery($q)) {
            if ($db->getNumberOfRows($result) > 0) {
                while ($row = $db->getRow($result)) {
                    $text[] = $row["rule"];
                }

                /* update get fills */
                $this->updateGetFills($text, $compile);
            }
        }

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateGetFills($variables = array()) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        /* keep track */
        //$getfillclasses = $this->loadGetFillClasses();
        //$fills = array();

        /* go through all variable(s) if none provided */
        global $db;
        if (sizeof($variables) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_variables where suid=" . $this->suid . " order by vsid asc";

            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $variables[] = new VariableDescriptive($row);
                    }
                }
            }
        }

        /* gather all texts */
        $text = array();
        foreach ($variables as $var) {

            $t = $var->getAnswerType();

            // general
            $text[] = $var->getQuestion();
            $text[] = $var->getEmptyMessage();
            $text[] = $var->getMinimum();
            $text[] = $var->getMaximum();
            $text[] = $var->getOtherValues();
            $text[] = $var->getMinimumLength();
            $text[] = $var->getMaximumLength();
            $text[] = $var->getMinimumWords();
            $text[] = $var->getMaximumWords();
            $text[] = $var->getMinimumSelected();
            $text[] = $var->getMaximumSelected();
            $text[] = $var->getExactSelected();
            $text[] = $var->getMaximumDatesSelected();
            $text[] = $var->getFillText();
            $text[] = $var->getPageHeader();
            $text[] = $var->getPageFooter();
            $text[] = $var->getLabelBackButton();
            $text[] = $var->getLabelNextButton();
            $text[] = $var->getLabelDKButton();
            $text[] = $var->getLabelRFButton();
            $text[] = $var->getLabelNAButton();
            $text[] = $var->getLabelUpdateButton();
            $text[] = $var->getLabelRemarkButton();
            $text[] = $var->getLabelRemarkSaveButton();
            $text[] = $var->getLabelCloseButton();
            $text[] = $var->getPreText();
            $text[] = $var->getPostText();
            $text[] = $var->getInlineStyle();
            $text[] = $var->getPageStyle();
            $text[] = $var->getPageJavascript();
            $text[] = $var->getInlineJavascript();
            $text[] = $var->getScripts();
            $text[] = $var->getProgressBarWidth();
            $text[] = $var->getProgressBarValue();
            $text[] = $var->getPlaceholder();

            $text[] = $var->getOnBack();
            $text[] = $var->getOnNext();
            $text[] = $var->getOnDK();
            $text[] = $var->getOnRF();
            $text[] = $var->getOnNA();
            $text[] = $var->getOnUpdate();
            $text[] = $var->getOnLanguageChange();
            $text[] = $var->getOnModeChange();
            $text[] = $var->getOnVersionChange();

            $text[] = $var->getClickBack();
            $text[] = $var->getClickNext();
            $text[] = $var->getClickDK();
            $text[] = $var->getClickRF();
            $text[] = $var->getClickNA();
            $text[] = $var->getClickUpdate();
            $text[] = $var->getClickLanguageChange();
            $text[] = $var->getClickModeChange();
            $text[] = $var->getClickVersionChange();

            if (inArray($t, array(ANSWER_TYPE_STRING, ANSWER_TYPE_OPEN, ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER))) {
                $text[] = $var->getComparisonEqualTo();
                $text[] = $var->getComparisonNotEqualTo();
                $text[] = $var->getComparisonGreaterEqualTo();
                $text[] = $var->getComparisonGreater();
                $text[] = $var->getComparisonSmallerEqualTo();
                $text[] = $var->getComparisonSmaller();
                $text[] = $var->getErrorMessageComparisonEqualTo();
                $text[] = $var->getErrorMessageComparisonNotEqualTo();
                $text[] = $var->getErrorMessageComparisonEqualToIgnoreCase();
                $text[] = $var->getErrorMessageComparisonNotEqualToIgnoreCase();
                $text[] = $var->getErrorMessageComparisonGreaterEqualTo();
                $text[] = $var->getErrorMessageComparisonGreater();
                $text[] = $var->getErrorMessageComparisonSmallerEqualTo();
                $text[] = $var->getErrorMessageComparisonSmaller();
            } else if ($t == ANSWER_TYPE_CUSTOM) {
                $text[] = $var->getAnswerTypeCustom();
            }

            $text[] = $var->getKeyboardBindingBack();
            $text[] = $var->getKeyboardBindingNext();
            $text[] = $var->getKeyboardBindingUpdate();
            $text[] = $var->getKeyboardBindingClose();
            $text[] = $var->getKeyboardBindingDK();
            $text[] = $var->getKeyboardBindingRF();
            $text[] = $var->getKeyboardBindingNA();
            $text[] = $var->getKeyboardBindingRemark();

            switch ($t) {
                case ANSWER_TYPE_DOUBLE:
                    $text[] = $var->getErrorMessageDouble();
                    break;
                case ANSWER_TYPE_INTEGER:
                    $text[] = $var->getErrorMessageInteger();
                    break;
                case ANSWER_TYPE_STRING;
                /* fall through */

                case ANSWER_TYPE_OPEN;
                    $text[] = $var->getErrorMessagePattern();
                    $text[] = $var->getErrorMessageMinimumLength();
                    $text[] = $var->getErrorMessageMaximumLength();
                    $text[] = $var->getErrorMessageMinimumWords();
                    $text[] = $var->getErrorMessageMaximumWords();
                    break;
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                    $text[] = $var->getErrorMessageRange();
                    $text[] = $var->getIncrement();
                    $text[] = $var->getSliderLabels();
                    break;
                case ANSWER_TYPE_ENUMERATED:
                    $text[] = $var->getEnumeratedCustom();
                //$text[] = $var->getEnumeratedLabel();
                /* fall through */

                case ANSWER_TYPE_DROPDOWN:
                    $text[] = $var->getEnumeratedRandomizer();
                    $text[] = $var->getOptionsText();
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                    $text[] = $var->getEnumeratedCustom();
                //$text[] = $var->getEnumeratedLabel();
                /* fall through */

                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getOptionsText();
                    $text[] = $var->getEnumeratedRandomizer();
                    $text[] = $var->getErrorMessageSelectMinimum();
                    $text[] = $var->getErrorMessageSelectMaximum();
                    $text[] = $var->getErrorMessageSelectExact();
                    $text[] = $var->getErrorMessageSelectInvalidSubset();
                    $text[] = $var->getErrorMessageSelectInvalidSet();
                    break;

                case ANSWER_TYPE_CALENDAR:
                    $text[] = $var->getErrorMessageMaximumCalendar();
                    break;
                case ANSWER_TYPE_DATE:
                    $text[] = $var->getDateFormat();
                    $text[] = $var->getDateDefaultView();
                    break;
                case ANSWER_TYPE_TIME:
                    $text[] = $var->getTimeFormat();
                    $text[] = $var->getDateDefaultView();
                    break;
                case ANSWER_TYPE_DATETIME:
                    $text[] = $var->getDateTimeFormat();
                    $text[] = $var->getDateDefaultView();
                    break;
            }
        }

        $this->updateGetFills($text);
        $this->updateGetFillsNoValues($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateGetFillsSurvey() {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* gather all texts */
        $text = array();

        $text[] = $this->survey->getLabelBackButton();
        $text[] = $this->survey->getLabelNextButton();
        $text[] = $this->survey->getLabelDKButton();
        $text[] = $this->survey->getLabelRFButton();
        $text[] = $this->survey->getLabelNAButton();
        $text[] = $this->survey->getLabelUpdateButton();
        $text[] = $this->survey->getLabelRemarkButton();
        $text[] = $this->survey->getLabelRemarkSaveButton();
        $text[] = $this->survey->getLabelCloseButton();
        $text[] = $this->survey->getTableWidth();
        $text[] = $this->survey->getQuestionColumnWidth();

        $text[] = $this->survey->getEmptyMessage();
        $text[] = $this->survey->getErrorMessageExclusive();
        $text[] = $this->survey->getErrorMessageInclusive();
        $text[] = $this->survey->getErrorMessageMinimumRequired();
        $text[] = $this->survey->getErrorMessageExactRequired();
        $text[] = $this->survey->getErrorMessageMaximumRequired();
        $text[] = $this->survey->getErrorMessageUniqueRequired();
        $text[] = $this->survey->getErrorMessageComparisonEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonNotEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonEqualToIgnoreCase();
        $text[] = $this->survey->getErrorMessageComparisonNotEqualToIgnoreCase();
        $text[] = $this->survey->getErrorMessageComparisonGreaterEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonGreater();
        $text[] = $this->survey->getErrorMessageComparisonSmallerEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonSmaller();

        $text[] = $this->survey->getOnBack();
        $text[] = $this->survey->getOnNext();
        $text[] = $this->survey->getOnDK();
        $text[] = $this->survey->getOnRF();
        $text[] = $this->survey->getOnNA();
        $text[] = $this->survey->getOnUpdate();
        $text[] = $this->survey->getOnLanguageChange();
        $text[] = $this->survey->getOnModeChange();
        $text[] = $this->survey->getOnVersionChange();

        $text[] = $this->survey->getClickBack();
        $text[] = $this->survey->getClickNext();
        $text[] = $this->survey->getClickDK();
        $text[] = $this->survey->getClickRF();
        $text[] = $this->survey->getClickNA();
        $text[] = $this->survey->getClickUpdate();
        $text[] = $this->survey->getClickLanguageChange();
        $text[] = $this->survey->getClickModeChange();
        $text[] = $this->survey->getClickVersionChange();

        $text[] = $this->survey->getKeyboardBindingBack();
        $text[] = $this->survey->getKeyboardBindingNext();
        $text[] = $this->survey->getKeyboardBindingUpdate();
        $text[] = $this->survey->getKeyboardBindingClose();
        $text[] = $this->survey->getKeyboardBindingDK();
        $text[] = $this->survey->getKeyboardBindingRF();
        $text[] = $this->survey->getKeyboardBindingNA();
        $text[] = $this->survey->getKeyboardBindingRemark();

        $text[] = $this->survey->getErrorMessageDouble();
        $text[] = $this->survey->getErrorMessageInteger();
        $text[] = $this->survey->getErrorMessagePattern();
        $text[] = $this->survey->getErrorMessageMinimumLength();
        $text[] = $this->survey->getErrorMessageMaximumLength();
        $text[] = $this->survey->getErrorMessageMinimumWords();
        $text[] = $this->survey->getErrorMessageMaximumWords();
        $text[] = $this->survey->getErrorMessageRange();
        $text[] = $this->survey->getIncrement();

        $text[] = $this->survey->getErrorMessageSelectMinimum();
        $text[] = $this->survey->getErrorMessageSelectMaximum();
        $text[] = $this->survey->getErrorMessageSelectExact();
        $text[] = $this->survey->getErrorMessageSelectInvalidSubset();
        $text[] = $this->survey->getErrorMessageSelectInvalidSet();

        $text[] = $this->survey->getErrorMessageMaximumCalendar();
        $text[] = $this->survey->getErrorMessageInlineAnswered();
        $text[] = $this->survey->getErrorMessageInlineExactRequired();
        $text[] = $this->survey->getErrorMessageInlineExclusive();
        $text[] = $this->survey->getErrorMessageInlineInclusive();
        $text[] = $this->survey->getErrorMessageInlineMaximumRequired();
        $text[] = $this->survey->getErrorMessageInlineMinimumRequired();
        $text[] = $this->survey->getErrorMessageSetOfEnumeratedEntered();

        $text[] = $this->survey->getDateFormat();
        $text[] = $this->survey->getTimeFormat();
        $text[] = $this->survey->getDateTimeFormat();
        $text[] = $this->survey->getPageHeader();
        $text[] = $this->survey->getPageFooter();
        $text[] = $this->survey->getProgressBarWidth();
        $text[] = $this->survey->getProgressBarFillColor();
        $text[] = $this->survey->getProgressBarRemainColor();
        $text[] = $this->survey->getScripts();
        $text[] = $this->survey->getPlaceholder();

        $text[] = $this->survey->getTimeoutLength();
        $text[] = $this->survey->getTimeoutTitle();
        $text[] = $this->survey->getTimeoutAliveButton();
        $text[] = $this->survey->getTimeoutLogoutButton();
        $text[] = $this->survey->getTimeoutLogoutURL();
        $text[] = $this->survey->getTimeoutRedirectURL();

        $this->updateGetFills($text);
        $this->updateGetFillsNoValues($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateInlineFieldsSurvey() {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* gather all texts */
        $text = array();

        $text[] = $this->survey->getLabelBackButton();
        $text[] = $this->survey->getLabelNextButton();
        $text[] = $this->survey->getLabelDKButton();
        $text[] = $this->survey->getLabelRFButton();
        $text[] = $this->survey->getLabelNAButton();
        $text[] = $this->survey->getLabelUpdateButton();
        $text[] = $this->survey->getLabelRemarkButton();
        $text[] = $this->survey->getLabelRemarkSaveButton();
        $text[] = $this->survey->getLabelCloseButton();
        $text[] = $this->survey->getTableWidth();
        $text[] = $this->survey->getQuestionColumnWidth();

        $text[] = $this->survey->getEmptyMessage();
        $text[] = $this->survey->getErrorMessageExclusive();
        $text[] = $this->survey->getErrorMessageInclusive();
        $text[] = $this->survey->getErrorMessageMinimumRequired();
        $text[] = $this->survey->getErrorMessageExactRequired();
        $text[] = $this->survey->getErrorMessageMaximumRequired();
        $text[] = $this->survey->getErrorMessageUniqueRequired();
        $text[] = $this->survey->getErrorMessageComparisonEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonNotEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonEqualToIgnoreCase();
        $text[] = $this->survey->getErrorMessageComparisonNotEqualToIgnoreCase();
        $text[] = $this->survey->getErrorMessageComparisonGreaterEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonGreater();
        $text[] = $this->survey->getErrorMessageComparisonSmallerEqualTo();
        $text[] = $this->survey->getErrorMessageComparisonSmaller();

        $text[] = $this->survey->getOnBack();
        $text[] = $this->survey->getOnNext();
        $text[] = $this->survey->getOnDK();
        $text[] = $this->survey->getOnRF();
        $text[] = $this->survey->getOnNA();
        $text[] = $this->survey->getOnUpdate();
        $text[] = $this->survey->getOnLanguageChange();
        $text[] = $this->survey->getOnModeChange();
        $text[] = $this->survey->getOnVersionChange();

        $text[] = $this->survey->getKeyboardBindingBack();
        $text[] = $this->survey->getKeyboardBindingNext();
        $text[] = $this->survey->getKeyboardBindingUpdate();
        $text[] = $this->survey->getKeyboardBindingClose();
        $text[] = $this->survey->getKeyboardBindingDK();
        $text[] = $this->survey->getKeyboardBindingRF();
        $text[] = $this->survey->getKeyboardBindingNA();
        $text[] = $this->survey->getKeyboardBindingRemark();

        $text[] = $this->survey->getErrorMessageDouble();
        $text[] = $this->survey->getErrorMessageInteger();
        $text[] = $this->survey->getErrorMessagePattern();
        $text[] = $this->survey->getErrorMessageMinimumLength();
        $text[] = $this->survey->getErrorMessageMaximumLength();
        $text[] = $this->survey->getErrorMessageMinimumWords();
        $text[] = $this->survey->getErrorMessageMaximumWords();
        $text[] = $this->survey->getErrorMessageRange();
        $text[] = $this->survey->getIncrement();
        $text[] = $this->survey->getProgressBarWidth();

        $text[] = $this->survey->getErrorMessageSelectMinimum();
        $text[] = $this->survey->getErrorMessageSelectMaximum();
        $text[] = $this->survey->getErrorMessageSelectExact();
        $text[] = $this->survey->getErrorMessageSelectInvalidSubset();
        $text[] = $this->survey->getErrorMessageSelectInvalidSet();

        $text[] = $this->survey->getErrorMessageMaximumCalendar();
        $text[] = $this->survey->getErrorMessageInlineAnswered();
        $text[] = $this->survey->getErrorMessageInlineExactRequired();
        $text[] = $this->survey->getErrorMessageInlineExclusive();
        $text[] = $this->survey->getErrorMessageInlineInclusive();
        $text[] = $this->survey->getErrorMessageInlineMaximumRequired();
        $text[] = $this->survey->getErrorMessageInlineMinimumRequired();
        $text[] = $this->survey->getErrorMessageSetOfEnumeratedEntered();

        $text[] = $this->survey->getDateFormat();
        $text[] = $this->survey->getTimeFormat();
        $text[] = $this->survey->getDateTimeFormat();
        $text[] = $this->survey->getPageHeader();
        $text[] = $this->survey->getPageFooter();
        $text[] = $this->survey->getProgressBarFillColor();
        $text[] = $this->survey->getProgressBarRemainColor();
        $text[] = $this->survey->getScripts();
        $text[] = $this->survey->getPlaceholder();

        $text[] = $this->survey->getTimeoutLength();
        $text[] = $this->survey->getTimeoutTitle();
        $text[] = $this->survey->getTimeoutAliveButton();
        $text[] = $this->survey->getTimeoutLogoutButton();
        $text[] = $this->survey->getTimeoutLogoutURL();
        $text[] = $this->survey->getTimeoutRedirectURL();

        /* update inline fields */
        $this->updateInlineFields($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function loadInlineFieldClasses() {
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            if ($row["inlinefields"] != "") {
                return unserialize(gzuncompress($row["inlinefields"]));
            }
        }
        return array();
    }

    function updateInlineFields($text = array()) {

        if (sizeof($text) == 0) {
            return;
        }
        /* keep track */
        $classes = $this->loadInlineFieldClasses();
        $fills = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_ANSWER);
        $fills2 = getReferences(implode(" ", $text), INDICATOR_INLINEFIELD_TEXT);
        $fills3 = array_unique(array_merge($fills, $fills2));

        /* go through fills */
        foreach ($fills3 as $fill) {
            
            /* not processed before */
            if ($fill != "") {
                $classes['"' . strtoupper($fill) . '"'] = $this->addInlineField($fill);
            }
        }

        /* check for first time */
        $this->addContext();

        /* store in db */
        global $db;
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($classes), 9));
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
        $query = "update " . Config::dbSurvey() . "_context set inlinefields = ? where suid=? and version = ? ";
        $db->executeBoundQuery($query, $bp->get());
    }

    function generateInlineFields($variables = array()) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* go through all variable(s) if none provided */
        global $db;
        if (sizeof($variables) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_variables where suid=" . $this->suid . " order by vsid asc";
            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $variables[] = new VariableDescriptive($row);
                    }
                }
            }
        }

        /* gather all texts */
        foreach ($variables as $var) {
            $t = $var->getAnswerType();

            // general
            $text[] = $var->getQuestion();
            $text[] = $var->getEmptyMessage();
            $text[] = $var->getFillText();
            $text[] = $var->getPreText();
            $text[] = $var->getPostText();
            switch ($t) {
                case ANSWER_TYPE_DOUBLE:
                    $text[] = $var->getErrorMessageDouble();
                    break;
                case ANSWER_TYPE_INTEGER:
                    $text[] = $var->getErrorMessageInteger();
                    break;
                case ANSWER_TYPE_STRING;
                /* fall through */

                case ANSWER_TYPE_OPEN;
                    $text[] = $var->getErrorMessagePattern();
                    $text[] = $var->getErrorMessageMinimumLength();
                    $text[] = $var->getErrorMessageMaximumLength();
                    $text[] = $var->getErrorMessageMinimumWords();
                    $text[] = $var->getErrorMessageMaximumWords();
                    break;
                case ANSWER_TYPE_RANGE:
                /* fall through */
                case ANSWER_TYPE_KNOB:
                /* fall through */
                case ANSWER_TYPE_SLIDER:
                    $text[] = $var->getErrorMessageRange();
                    break;
                case ANSWER_TYPE_ENUMERATED:
                /* fall through */

                case ANSWER_TYPE_DROPDOWN:
                    $text[] = $var->getOptionsText();
                    break;
                case ANSWER_TYPE_SETOFENUMERATED:
                /* fall through */

                case ANSWER_TYPE_MULTIDROPDOWN:
                    $text[] = $var->getOptionsText();
                    $text[] = $var->getErrorMessageSelectMinimum();
                    $text[] = $var->getErrorMessageSelectMaximum();
                    $text[] = $var->getErrorMessageSelectExact();
                    $text[] = $var->getErrorMessageSelectInvalidSubset();
                    $text[] = $var->getErrorMessageSelectInvalidSet();
                    break;

                case ANSWER_TYPE_CALENDAR:
                    $text[] = $var->getErrorMessageMaximumCalendar();
                    break;
            }

            if (inArray($t, array(ANSWER_TYPE_RANGE, ANSWER_TYPE_KNOB, ANSWER_TYPE_DOUBLE, ANSWER_TYPE_INTEGER, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_DATE, ANSWER_TYPE_DATETIME, ANSWER_TYPE_TIME, ANSWER_TYPE_SLIDER))) {
                $text[] = $var->getComparisonEqualTo();
                $text[] = $var->getComparisonNotEqualTo();
                $text[] = $var->getComparisonGreaterEqualTo();
                $text[] = $var->getComparisonGreater();
                $text[] = $var->getComparisonSmallerEqualTo();
                $text[] = $var->getComparisonSmaller();
                $text[] = $var->getErrorMessageComparisonEqualTo();
                $text[] = $var->getErrorMessageComparisonNotEqualTo();
                $text[] = $var->getErrorMessageComparisonGreaterEqualTo();
                $text[] = $var->getErrorMessageComparisonGreater();
                $text[] = $var->getErrorMessageComparisonSmallerEqualTo();
                $text[] = $var->getErrorMessageComparisonSmaller();
            }
        }

        /* update inline fields */
        $this->updateInlineFields($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateInlineFieldsGroups($groups = array()) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* go through all group(s) if none provided */
        global $db;
        if (sizeof($groups) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_groups where suid=" . $this->suid . " order by gid asc";

            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $groups[] = new Group($row);
                    }
                }
            }
        }

        /* gather all texts */
        $text = array();
        foreach ($groups as $group) {
            $text[] = $group->getErrorMessageExclusive();
            $text[] = $group->getErrorMessageInclusive();
            $text[] = $group->getErrorMessageMinimumRequired();
            $text[] = $group->getErrorMessageExactRequired();
            $text[] = $group->getErrorMessageMaximumRequired();
            $text[] = $group->getErrorMessageUniqueRequired();
            $text[] = $group->getPageHeader();
            $text[] = $group->getPageFooter();
            $text[] = $group->getExactRequired();
            $text[] = $group->getMaximumRequired();
            $text[] = $group->getMinimumRequired();
            $text[] = $group->getUniqueRequired();
            $text[] = $group->getScripts();
            $text[] = $group->getLabelBackButton();
            $text[] = $group->getLabelNextButton();
            $text[] = $group->getLabelDKButton();
            $text[] = $group->getLabelRFButton();
            $text[] = $group->getLabelNAButton();
            $text[] = $group->getLabelUpdateButton();
            $text[] = $group->getLabelRemarkButton();
            $text[] = $group->getLabelRemarkSaveButton();
            $text[] = $group->getLabelCloseButton();
            $text[] = $group->getCustomTemplate();
        }

        /* update inline fields */
        $this->updateInlineFields($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateInlineFieldsSections($sections = array()) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* go through all group(s) if none provided */
        global $db;
        if (sizeof($sections) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_sections where suid=" . $this->suid . " order by seid asc";

            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $sections[] = new Section($row);
                    }
                }
            }
        }

        /* gather all texts */
        $text = array();
        foreach ($sections as $section) {
            $text[] = $section->getHeader();
            $text[] = $section->getFooter();
        }

        /* update inline fields */
        $this->updateInlineFields($text);

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    /* MAIN ROUTING FUNCTIONS */

    function generateEngine($seid, $compile = true) {
        set_time_limit(0);
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        global $db;
        $this->seid = $seid;
        $q = "select * from " . Config::dbSurvey() . "_routing where suid=" . $this->suid . " and seid=" . $this->seid . " order by rgid asc";
        if ($rules = $db->selectQuery($q)) {

            if ($db->getNumberOfRows($rules) > 0) {

                if ($compile == true) {
                    $query = "replace into " . Config::dbSurvey() . "_engines (suid, version, seid) values (" . $this->suid . "," . $this->version . ", " . $this->seid . ")";
                    $db->executeQuery($query);
                }

                // get section name
                $this->sectionname = "";
                $q = "select name from " . Config::dbSurvey() . "_sections where suid=" . $this->suid . " and seid=" . $this->seid . "";
                if ($res = $db->selectQuery($q)) {
                    if ($db->getNumberOfRows($res) > 0) {
                        $row = $db->getRow($res);
                        $this->sectionname = $row["name"];
                    }
                }

                /* create factory, printer and root node */
                $this->factory = new PHPParser_BuilderFactory();
                $this->printer = new PHPParser_PrettyPrinter_Default();
                $rootnode = $this->factory->class(CLASS_ENGINE . $this->seid)->extend(CLASS_BASICENGINE);


                /* keep track of doAction cases */
                $this->doaction_cases = array();
                $this->actions = array();


                /* store all instructions */
                $this->instructions = array();


                /* set screen counter */
                $this->screencounter = 0;
                $this->ifreset = array();
                $this->elseifreset = array();
                $this->elsereset = array();

                /* not a fill class we are generating */
                $this->fillclass = false;
                $this->setfills = array();

                /* clear */
                if ($compile == true) {
                    $q = "delete from " . Config::dbSurvey() . "_next where suid=" . $this->suid . " and seid=" . $this->seid;
                    $db->executeQuery($q);
                    $q = "delete from " . Config::dbSurvey() . "_screens where suid=" . $this->suid . " and seid=" . $this->seid;
                    $db->executeQuery($q);
                }

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

                while ($row = $db->getRow($rules)) {
                    $this->instructions[$row["rgid"]] = new RoutingInstruction($this->suid, $this->seid, $row["rgid"], $row["rule"]);
                }

                // set default survey language and mode for routing
                $cm = getSurveyMode();
                $cl = getSurveyLanguage();
                $mode = $this->survey->getDefaultMode();
                $_SESSION['SURVEY_MODE'] = $mode;
                $_SESSION['SURVEY_LANGUAGE'] = $this->survey->getDefaultLanguage($mode);

                /* process rules */
                for ($this->cnt = 1; $this->cnt <= sizeof($this->instructions); $this->cnt++) {
                    if (isset($this->instructions[$this->cnt])) {
                        $this->addRule($rootnode, $this->instructions[$this->cnt]);
                    }
                }

                // compiler (if we do this through the checker no need to do the below)
                if ($compile == true) {

                    /* add end action */
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_END)), array());
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(null, $stmts);


                    /* add doAction function */
                    $this->doaction = $this->factory->method(FUNCTION_DO_ACTION);
                    $this->doaction->makeProtected();
                    $param = new PHPParser_Builder_Param('rgid');
                    $param->setDefault("");
                    $this->doaction->addParam($param);
                    $doactioncond = new PHPParser_Node_Expr_Variable("rgid");
                    $this->doaction->addStmt(new PHPParser_Node_Stmt_Switch($doactioncond, $this->doaction_cases));
                    $rootnode->addStmt($this->doaction);

                    /* add getFirstAction function */
                    $firstaction = $this->factory->method(FUNCTION_GET_FIRST_ACTION);
                    $firstaction->makePublic();
                    $first = "";
                    if (isset($this->actions[0])) {
                        $first = $this->actions[0];
                        $firstaction->addStmt(new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_LNumber($first)));
                    }

                    $rootnode->addStmt($firstaction);


                    /* get statements */
                    $stmts = array($rootnode->getNode());


                    /* generate code */
                    if ($compile == true) {
                        $engine = $this->printer->prettyPrint($stmts);
                        $engine = str_replace(" == 0", " == '0'", $engine);

                        /* store in db */
                        $bp = new BindParam();
                        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($engine), 9));
                        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($this->instructions), 9));
                        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
                        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
                        $bp->add(MYSQL_BINDING_INTEGER, $this->seid);
                        $query = "update " . Config::dbSurvey() . "_engines set engine = ?, instructions = ? where suid=? and version = ? and seid=?";
                        $db->executeBoundQuery($query, $bp->get());
                    }
                }

                /* group fill names */
                $this->generateGetFillsRouting($seid, $compile);

                $_SESSION['SURVEY_MODE'] = $cm;
                $_SESSION['SURVEY_LANGUAGE'] = $cl;
            }
        }

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        return $this->messages;
    }

    function loadChecks() {
        global $db;
        $q = "select checks from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            if ($row["checks"] != "") {
                return unserialize(gzuncompress($row["checks"]));
            }
        }
        return array();
    }

    function generateChecks($variables = array(), $remove = false, $compile = true) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* create factory, printer and root node */

        $this->factory = new PHPParser_BuilderFactory();

        $this->printer = new PHPParser_PrettyPrinter_Default();
        $this->messages = array();
        //$setfillclasses = array();

        $this->fillclass = true;
        $this->checkclass = true;

        /* load any existing checks from context */
        $checkclasses = $this->loadChecks();

        /* go through all variable(s) if none provided */
        global $db;
        if (sizeof($variables) == 0) {
            $q = "select * from " . Config::dbSurvey() . "_variables where suid=" . $this->suid . " order by vsid asc";
            if ($result = $db->selectQuery($q)) {
                if ($db->getNumberOfRows($result) > 0) {
                    while ($row = $db->getRow($result)) {
                        $variables[] = new VariableDescriptive($row);
                    }
                }
            }
        }

        foreach ($variables as $var) {
            if ($remove == false) {
                $code = $var->getCheckCode();
                if (trim($code) != "") {

                    $this->currentfillvariable = $var->getName();
                    //$rule = trim(str_ireplace(".FILL", "", $s->getRule()));            
                    //$rgid = $s->getRgid();

                    /* store pairing */
                    //$setfillarray[strtoupper(getBasicName($rule))] = $rgid;

                    $rootnode = $this->factory->class(CLASS_CHECK . "_" . $this->currentfillvariable)->extend(CLASS_BASICCHECK);

                    /* preset trackers */

                    $this->looptimes = 1;

                    $this->lasttimesloop = array();

                    $this->lastloopactions = array();

                    $this->loops = array();
                    $this->groups = array();
                    $this->groupsend = array();
                    $this->groupactions = array();
                    $this->instructions = array();
                    $this->whiles = array();
                    $this->lastwhileactions = array();

                    $this->doaction_cases = array();
                    $this->actions = array();
                    $stmts = array();

                    //$var = $this->survey->getVariableDescriptiveByName(getBasicName($rule));


                    $checkrules = explode("\r\n", $code);
                    $cnt = 1;

                    foreach ($checkrules as $checkrule) {

                        $this->instructions[$cnt] = new RoutingInstruction($var->getSuid(), $var->getSeid(), $cnt, rtrim($checkrule));

                        $cnt++;
                    }

                    /* process setfillvalue cases */
                    for ($this->cnt = 1; $this->cnt <= sizeof($this->instructions); $this->cnt++) {

                        if (isset($this->instructions[$this->cnt])) {
                            $this->addRule($rootnode, $this->instructions[$this->cnt]);
                        }
                    }

                    /* add end */
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(null, $stmts);



                    /* add doAction function */

                    $this->doaction = $this->factory->method(FUNCTION_DO_ACTION);

                    $this->doaction->makePublic();

                    $param = new PHPParser_Builder_Param('rgid');

                    $param->setDefault("");

                    $this->doaction->addParam($param);

                    $doactioncond = new PHPParser_Node_Expr_Variable("rgid");

                    $this->doaction->addStmt(new PHPParser_Node_Stmt_Switch($doactioncond, $this->doaction_cases));

                    $rootnode->addStmt($this->doaction);


                    /* add getFirstAction function */
                    $firstaction = $this->factory->method(FUNCTION_GET_FIRST_ACTION);
                    $firstaction->makePublic();
                    $firstaction->addStmt(new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_LNumber($this->actions[0])));
                    $rootnode->addStmt($firstaction);

                    /* get statements */

                    $stmts = array($rootnode->getNode());



                    /* generate code for check class */

                    //$setfillclasses[$rgid] = $this->printer->prettyPrint($stmts);
                    $checkclasses[strtoupper($this->currentfillvariable) . getSurveyLanguage() . getSurveyMode()] = $this->printer->prettyPrint($stmts);

                } else {

                    // no check code, then remove
                    if (isset($checkclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())])) {
                        unset($checkclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())]);
                    }
                }
            } else {
                if (isset($checkclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())])) {
                    unset($checkclasses[strtoupper($var->getName() . getSurveyLanguage() . getSurveyMode())]);
                }
            }
        }

        if ($compile == true) {

            /* check for first time */
            $this->addContext();

            /* store in db */
            global $db;
            $bp = new BindParam();
            $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($checkclasses), 9));
            $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
            $bp->add(MYSQL_BINDING_INTEGER, $this->version);
            $query = "update " . Config::dbSurvey() . "_context set checks = ? where suid=? and version = ? ";
            $db->executeBoundQuery($query, $bp->get());
        }
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        return $this->messages;
    }

    function addRule(&$node, $instruction) {



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

            $action = FUNCTION_DO_IF . $rgid;

            $this->addIf($action, $node, $instruction);



            // not in group

            if (sizeof($this->groups) == 0) {

                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());

                $stmts[] = new PHPParser_Node_Stmt_Break();
            }

            // in group
            else {

                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
            }

            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;
        }

        // else if condition 
        else if (startsWith($rule, ROUTING_IDENTIFY_ELSEIF)) {

            $action = FUNCTION_DO_ELSEIF . $rgid;

            $this->addIf($action, $node, $instruction);



            // not in group

            if (sizeof($this->groups) == 0) {

                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());

                $stmts[] = new PHPParser_Node_Stmt_Break();
            }

            // in group
            else {

                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
            }

            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;
        }

        // else 
        else if (startsWith($rule, ROUTING_IDENTIFY_ELSE)) {

            $action = FUNCTION_DO_ELSE . $rgid;

            $this->addElse($action, $node, $instruction);



            // not in group

            if (sizeof($this->groups) == 0) {

                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());

                $stmts[] = new PHPParser_Node_Stmt_Break();
            }

            // in group
            else {

                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
            }

            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;
        }
        // for loop  
        else if (startsWith($rule, ROUTING_IDENTIFY_FOR) || startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {

            if ($this->checkclass == false) {
                $action = FUNCTION_DO_LOOP . $rgid;
                $this->addForLoop($action, $node, $instruction);

                // not in group
                if (sizeof($this->groups) == 0) {
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                }
                // in group
                else {
                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                }
                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
        }
        // while loop  
        else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {

            if ($this->checkclass == false) {
                $action = FUNCTION_DO_WHILE . $rgid;
                $this->addWhileLoop($action, $node, $instruction);

                // not in group
                if (sizeof($this->groups) == 0) {
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                }
                // in group
                else {
                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                }
                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
        }
        // group  
        else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {

            // only allowed in main routing (not in fill code)
            if ($this->fillclass == false) {
                $action = FUNCTION_DO_GROUP . $rgid;
                $this->addGroup($action, $node, $instruction);
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());
                $stmts[] = new PHPParser_Node_Stmt_Break();
                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
        }
        // sub group  
        else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

            // ONLY if in group
            if (sizeof($this->groups) > 0) {
                $action = FUNCTION_DO_SUBGROUP . $rgid;
                $this->addSubGroup($action, $node, $instruction);
                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
                //$this->cnt = $this->findEndSubGroup($rgid);
            } else { // ignore the subgroup statement
                $this->cnt = $this->findEndSubGroup($rgid);
                if ($this->cnt == "") {
                    return;
                }
            }
        }

        // move forward
        else if (startsWith($rule, ROUTING_MOVE_FORWARD)) {



            // only allowed in main routing (not in fill code)

            if ($this->fillclass == false) {

                $action = FUNCTION_DO_MOVE_FORWARD . $rgid;

                $this->addMoveForward($action, $node, $instruction);



                // not in group

                if (sizeof($this->groups) == 0) {

                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());

                    $stmts[] = new PHPParser_Node_Stmt_Break();
                }

                // in group
                else {

                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                }

                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
        }

        // move backward
        else if (startsWith($rule, ROUTING_MOVE_BACKWARD)) {



            // only allowed in main routing (not in fill code)

            if ($this->fillclass == false) {

                $action = FUNCTION_DO_MOVE_BACKWARD . $rgid;

                $this->addMoveBackward($action, $node, $instruction);



                // not in group

                if (sizeof($this->groups) == 0) {

                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());

                    $stmts[] = new PHPParser_Node_Stmt_Break();
                }

                // in group
                else {

                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                }

                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
        }

        // assignment
        else if (contains($rule, ":=")) {

            if ($this->checkclass == false) {
                $action = FUNCTION_DO_ASSIGNMENT . $rgid;
                $this->addAssignment($action, $node, $instruction);

                // not in group
                if (sizeof($this->groups) == 0) {
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                }

                // in group
                else {
                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                }

                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
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
            
        }
        // end while
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {

            if (sizeof($this->groups) == 0) { // should this always happen??
                array_pop($this->whiles);
                array_pop($this->lastwhileactions);
                array_pop($this->whileactions);
                array_pop($this->whilenextrgids);
            } else {
                array_pop($this->whiles);
                array_pop($this->lastwhileactions);
                array_pop($this->whileactions);
                array_pop($this->whilenextrgids);
            }
        }

        // end do
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {

            if (sizeof($this->groups) == 0) { // should this always happen??
                $divide = array_pop($this->lasttimesloop);
                array_pop($this->loops);
                array_pop($this->lastloopactions);
                array_pop($this->loopactions);
                array_pop($this->loopcounters);
                array_pop($this->loopnextrgids);

                if ($divide) {
                    $this->looptimes = $this->looptimes / $divide;
                } else {

                    // something went wrong (we don't have loop count stored),
                    // so we assume a routing error and reset looptimes to 1
                    $this->looptimes = 1;
                }
            } else {
                array_pop($this->loops);
                array_pop($this->lastloopactions);
                array_pop($this->loopactions);
                array_pop($this->loopnextrgids);
            }
        }
        // end group
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP)) {
            
        }
        // end subgroup
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP)) {
            if (sizeof($this->groups) > 0) {
                array_pop($this->groups);
                array_pop($this->groupsend);
                array_pop($this->groupactions);
            }
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
                $this->addInspect($node, $instruction);
            }
            // check for .INSPECTSECTION
            else if (endsWith($rule, ROUTING_IDENTIFY_INSPECT_SECTION)) {
                $this->addInspectSection($node, $instruction);
            }
            // check for EXITFOR
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXITFOR) {

                /* only add if in loop, otherwise ignore */
                if (sizeof($this->loopnextrgids) > 0) {
                    $args = $this->loopnextrgids[sizeof($this->loopnextrgids) - 1];
                    $looprgid = $this->loops[sizeof($this->loops) - 1];
                    $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(2)); // signal exitfor
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP)), $args);
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                    $this->actions[] = $rgid;
                }
            }
            // check for EXITWHILE
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXITWHILE) {

                /* only add if in while, otherwise ignore */
                if (sizeof($this->whilenextrgids) > 0) {
                    $args = $this->whilenextrgids[sizeof($this->whilenextrgids) - 1];
                    $looprgid = $this->whiles[sizeof($this->whiles) - 1];
                    $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(2)); // signal exitwhile
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE)), $args);
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                    $this->actions[] = $rgid;
                }
            }
            // check for EXIT
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXIT) {
                $args = array();
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_EXIT)), $args);
                $stmts[] = new PHPParser_Node_Stmt_Break();
                $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                $this->actions[] = $rgid;
            }
            // check for .FILL
            else if (endsWith($rule, ROUTING_IDENTIFY_FILL)) {

                if ($this->fillclass == false) {
                    $action = FUNCTION_DO_FILL . $rgid;
                    $this->addSetFill($action, $node, $instruction);

                    // not in group
                    if (sizeof($this->groups) == 0) {
                        $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());
                        $stmts[] = new PHPParser_Node_Stmt_Break();
                    }

                    // in group
                    else {
                        $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array()));
                    }

                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                    $this->actions[] = $rgid;
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
                        if (strtolower($this->sectionname) == strtolower($section->getName())) {
                            $this->addErrorMessage(Language::errorSectionInSection($tofind));
                        } else {
                            $this->addSection($rule . ".", $rgid, $section->getSeid(), false);
                        }
                    } else {

                        /* check if this is a question of type section */
                        $var = $this->survey->getVariableDescriptiveByName(getBasicName($rule));
                        if ($var->getAnswerType() == ANSWER_TYPE_SECTION) {
                            $sectionid = $var->getSection();
                            $section = $this->survey->getSection($sectionid);
                            if ($section->getName() != "") {
                                if (strtolower($this->sectionname) == strtolower($section->getName())) {
                                    $this->addErrorMessage(Language::errorSectionInSection($tofind));
                                } else {
                                    $this->addSection($rule . ".", $rgid, $section->getSeid(), true);
                                }
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

                        $this->addQuestion($node, $instruction);
                    }
                }
                // check code
                else if ($this->checkclass == true) {
                    $action = FUNCTION_DO_CHECK_RETURN . $rgid;
                    $this->addCheckReturn($action, $node, $instruction);
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($action)), array());
                    $stmts[] = new PHPParser_Node_Stmt_Break();
                    $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
                    $this->actions[] = $rgid;
                }
                // in fill code, then none of this is allowed
                else {
                    $this->addErrorMessage(Language::errorFillCodeOnlyAssignments());
                }
            }
        }
    }

    function addSection($rule, $rgid, $seid, $typequestion = false) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }

        $nextrgid = $this->findNextStatementAfterQuestion($rgid);


        // not in group                    

        if (sizeof($this->groups) == 0) {

            $prefix = $rule;

            if (contains($rule, ".")) {

                $prefix = substr($rule, 0, strripos($rule, ".") + 1);
            }



            // section call

            if ($typequestion == false) {

                $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($prefix));
            }

            // question of type section
            else {

                // hide module dot notations
                $prefix = substr($prefix, 0, strlen($prefix) - 1); // strip dot first!
                // hide module dot notations
                $prefix = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $prefix);
                $prefix = hideModuleNotations($prefix, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

                /* replace brackets */
                $prefix = str_replace("[", TEXT_BRACKET_LEFT, $prefix);
                $prefix = str_replace("]", TEXT_BRACKET_RIGHT, $prefix);

                //$prefix = hideModuleNotations($prefix, TEXT_MODULE_DOT);
                // hide brackets before parsing
                //$prefix = str_replace("[", TEXT_BRACKET_LEFT, $prefix);
                //$prefix = str_replace("]", TEXT_BRACKET_RIGHT, $prefix);
                // loop action
                if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
                }
                // while action
                else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
                }

                // parse prefix
                $parser = new PHPParser_Parser(new PHPParser_Lexer);
                try {
                    $stmts1 = $parser->parse("<?php " . $prefix . "?>");
                    $stmtleft = new PHPParser_Node_Arg($stmts1[0]); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
                    $this->updateVariables($stmtleft);
                    if ($stmtleft->value instanceof PHPParser_Node_Expr_MethodCall) {
                        $args[] = new PHPParser_Node_Expr_Concat($stmtleft->value->args[0], new PHPParser_Node_Scalar_String("."));
                    }

                    // a non-bracketed field
                    else {
                        $args[] = new PHPParser_Node_Expr_Concat($stmtleft->value->name->args[0]->value, new PHPParser_Node_Scalar_String("."));
                    }
                } catch (PHPParser_Error $e) {
                    return;
                }
            }

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($seid));
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_SECTION)), $args);
            $stmts[] = new PHPParser_Node_Stmt_Break();
            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;
        }

        // in group --> cannot have section call inside group statement!
        else {

            /* we ignore everything */
        }



        // store where we go next from here after the section call                    

        $this->addNext($rgid, $nextrgid);



        /* add question screen entry */

        $this->addQuestionScreen($rgid, $seid);
    }

    function addMoveForward($function, &$node, $instruction) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }
        $rule = trim($instruction->getRule());

        $rgid = trim($instruction->getRgid());
        $this->actions[] = $rgid;


        /* $excluded = array();

          $rule = excludeText($rule, $excluded);



          $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);

          $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);



          // hide module dot notations

          $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

          $rule = includeText($rule, $excluded);

         */



        // split off moveForward. part

        $split = explode(".", $rule);

        $query = "select rgid from " . Config::dbSurvey() . "_routing where suid=" . $this->suid . " and rgid > " . $rgid . " and rule='" . $split[1] . "' order by rgid desc";

        $targetrgid = "";

        global $db;

        if ($result = $db->selectQuery($query)) {

            if ($db->getNumberOfRows($result) > 0) {

                $row = $db->getRow($result);

                $targetrgid = $row["rgid"];
            }
        }

        // nothing found then just go to the next statement

        if ($targetrgid == "") {

            $targetrgid = $this->findNextStatementAfterQuestion($rgid);
        }



        // not in a group AND valid target
        if (sizeof($this->groups) == 0 && is_numeric($targetrgid) && $targetrgid > 0) {



            // add group function        

            $movefunctionnode = $this->factory->method($function);

            $movefunctionnode->makePrivate();



            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($targetrgid));

            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args);

            $movefunctionnode->addStmts($stmts);



            /* add doAction */

            $node->addStmt($movefunctionnode);
        }

        // in a group: SHOULD NOT HAPPEN!
        else {

            /* ignore */
        }
    }

    function addMoveBackward($function, &$node, $instruction) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }
        $rule = trim($instruction->getRule());

        $rgid = trim($instruction->getRgid());
        $this->actions[] = $rgid;


        /* $excluded = array();

          $rule = excludeText($rule, $excluded);



          $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);

          $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);



          // hide module dot notations

          $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

          $rule = includeText($rule, $excluded);

         */



        // split off moveBackward. part

        $split = explode(".", $rule);

        $query = "select rgid from " . Config::dbSurvey() . "_routing where suid=" . $this->suid . " and rgid < " . $rgid . " and rule='" . $split[1] . "' order by rgid desc";

        $targetrgid = null;

        global $db;

        if ($result = $db->selectQuery($query)) {

            if ($db->getNumberOfRows($result) > 0) {

                $row = $db->getRow($result);

                $targetrgid = $row["rgid"];
            }
        }



        // nothing found then just go to the next statement

        if ($targetrgid == "") {

            $targetrgid = $this->findNextStatementAfterQuestion($rgid);
        }





        // not in a group AND valid target

        if (sizeof($this->groups) == 0 && is_numeric($targetrgid) && $targetrgid > 0) {



            // add group function        

            $movefunctionnode = $this->factory->method($function);

            $movefunctionnode->makePrivate();

            // IF IN A LOOP AND GOING BACK TO BEFORE THE LOOP, THEN:
            // before going backward clear all loop and while information so we don't keep it and think we are still in a loop
            /* if (sizeof($this->loops) > 0 && $targetrgid < $this->loops[sizeof($this->loops)]) {
              $tempargs = array();
              $tempargs[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(0));
              $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array("setLoopRgid")), $tempargs);

              $tempargs = array();
              $tempargs[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(""));
              $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array("setLoopString")), $tempargs);

              $tempargs = array();
              $tempargs[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(""));
              $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array("setForLoopLastAction")), $tempargs);
              } */

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($targetrgid));
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args);

            $movefunctionnode->addStmts($stmts);


            /* add doAction */

            $node->addStmt($movefunctionnode);
        }

        // in a group: SHOULD NOT HAPPEN!
        else {

            /* ignore */
        }
    }

    function addGroup($function, &$node, $instruction) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }

        $this->groupstatements = array();

        $this->group = true;

        $rule = trim($instruction->getRule());

        $rgid = trim($instruction->getRgid());
        $this->actions[] = $rgid;
        $endrgid = $this->findEndGroup($rgid);

        if ($endrgid == "") {
            return;
        }

        $group = explode(".", $rule);
        if (sizeof($group) < 2 || trim($group[1]) == "") {
            $this->addErrorMessage(Language::errorGroupTemplateNotFound());
            return;
        }

        $this->groups[] = $rgid;
        $this->groupsend[] = $endrgid;
        $groupactions = $this->findStatementsInGroup($rgid);
        $this->realgroups[] = $this->groups[sizeof($this->groups) - 1];
        $this->groupactions[$this->groups[sizeof($this->groups) - 1]] = $groupactions;

        // add group function        

        $groupfunctionnode = $this->factory->method($function);

        $groupfunctionnode->makePrivate();



        // go through group rules
        $currentcount = $this->cnt; // remember where we are with group
        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            if (isset($this->instructions[$cnt])) {

                if ($this->instructions[$cnt]->getRgid() == $endrgid) {

                    break;
                }
                $this->cnt = $cnt; // update count so we don't have problems with multi-line statements
                $this->addRule($node, $this->instructions[$cnt]);
                $cnt = $this->cnt; // update cnt here in case of multi-line statements
            }
        }
        $this->cnt = $currentcount; // set back to where we were to continue with group
        // determine group
        $groupnode = new PHPParser_Node_Scalar_String(trim($group[1]));


        // loop action
        if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
            $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
            $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
            $groupfunctionnode->addStmt($stmt);
        }
        // while action
        else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
            $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
            $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
            $groupfunctionnode->addStmt($stmt);
        }

        // add next
        $nextrgid = $this->findNextStatementAfterQuestion($endrgid);
        $this->addNext($rgid, $nextrgid);


        // add group actions statement
        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $groupactions)));
        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
        $args[] = new PHPParser_Node_Arg($groupnode);
        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_GROUP)), $args);
        $groupfunctionnode->addStmt($stmt);


        /* add group doAction */

        $node->addStmt($groupfunctionnode);



        // update overall counter

        $this->cnt = $endrgid;
        $this->actions[] = $rgid;

        /* store question screen */
        $this->addQuestionScreen($rgid);

        // false again
        $this->group = false;

        array_pop($this->groups);
        array_pop($this->realgroups);
        array_pop($this->groupsend);
        array_pop($this->groupactions);

        // add group to db if not exists yet  
        if (substr(trim($group[1]), 0, 1) != INDICATOR_FILL) {
            $gr = $this->survey->getGroupByName(trim($group[1]));
            if (trim($group[1]) != "" && $gr->getName() == "") {
                $gr = new Group();
                $gr->setSuid($this->suid);
                $gr->setName(trim($group[1]));
                $gr->setType(GROUP_MAIN);
                $gr->save();
            }
        }
    }

    function findStatementsInGroup($rgid) {

        $level = 1;

        $loopactions = array();

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_FOR) || startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndDo($cnt); /* skip to the end */
                if ($cnt == "") {
                    //    $this->addErrorMessage(Language::errorForLoopMissingEnddo());
                    return;
                }
            } else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndWhile($cnt); /* skip to the end */
                if ($cnt == "") {
                    //    $this->addErrorMessage(Language::errorForLoopMissingEnddo());
                    return;
                }
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP)) {

                $level--;

                if ($level > 0) {/* end of a group */

                    //    $level--;
                } else {/* end of the group, so return whatever comes after the endgroup */

                    break;
                }
            }

            // if statement, then we add it and then skip to end 
            else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndIf($cnt, ROUTING_IDENTIFY_IF);
            }

            // sub group statement, then we add it and skip to the end
            else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndSubGroup($cnt);
                if ($cnt == "") {
                    return;
                }
            }

            // if it is not an elseif, else, endif, endgroup or endsubgroup statement we add; i.e. assignment or question
            else if (!(endsWith($rule, ROUTING_IDENTIFY_KEEP) || startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE) || startsWith($rule, ROUTING_IDENTIFY_ENDIF) || startsWith($rule, ROUTING_IDENTIFY_ENDDO) || startsWith($rule, ROUTING_IDENTIFY_ENDGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP))) {

                $loopactions[] = $cnt;
            }
        }

        return $loopactions;
    }

    function findStatementsInSubGroup($rgid) {

        $level = 1;

        $loopactions = array();

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_FOR) || startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndDo($cnt); /* skip to the end */
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP)) {

                $level--;

                if ($level > 0) {/* end of a sub group */

                    //    $level--;
                } else {/* end of the sub group, so return whatever comes after the endsubgroup */

                    break;
                }
            }

            // if statement, then we add it and then skip to end 
            else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndIf($cnt, ROUTING_IDENTIFY_IF);
            }

            // group statement: not allowed in subgroup, so ignore it
            else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {

                $cnt = $this->findEndGroup($cnt);
            }

            // sub group statement, then we add it and skip to the end
            else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

                $loopactions[] = $cnt;

                $cnt = $this->findEndSubGroup($cnt);
                if ($cnt == "") {
                    return;
                }
            }

            // if it is not an elseif, else, endif, endgroup or endsubgroup statement we add; i.e. assignment or question
            else if (!(endsWith($rule, ROUTING_IDENTIFY_KEEP) || startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE) || startsWith($rule, ROUTING_IDENTIFY_ENDIF) || startsWith($rule, ROUTING_IDENTIFY_ENDDO) || startsWith($rule, ROUTING_IDENTIFY_ENDGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP))) {

                $loopactions[] = $cnt;
            }
        }

        return $loopactions;
    }

    function addSubGroup($function, &$node, $instruction) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }

        $rule = trim($instruction->getRule());

        $rgid = trim($instruction->getRgid());

        $endrgid = $this->findEndSubGroup($rgid);

        if ($endrgid == "") {
            //$this->addErrorMessage(Language::errorGroupMissingEndSubGroup());
            return;
        }

        $group = explode(".", $rule);
        if (sizeof($group) < 2 || trim($group[1]) == "") {
            $this->addErrorMessage(Language::errorGroupTemplateNotFound());
            return;
        }

        $this->groups[] = $rgid;
        $this->groupsend[] = $endrgid;
        $groupactions = $this->findStatementsInSubGroup($rgid);

        /* add these sub group actions to the top group, so we know they are part of the group structure! */
        $current = $this->groupactions[end($this->realgroups)];
        $this->groupactions[end($this->realgroups)] = array_merge($current, $groupactions);

        $this->groupactions[$this->groups[sizeof($this->groups) - 1]] = $groupactions;


        // add sub group function        

        $subgroupfunctionnode = $this->factory->method($function);

        $subgroupfunctionnode->makePrivate();


        // determine group
        $groupnode = new PHPParser_Node_Scalar_String(trim($group[1]));


        // get group actions and add statement

        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $groupactions)));

        $args[] = new PHPParser_Node_Arg($groupnode);

        $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_SUBGROUP)), $args));

        $subgroupfunctionnode->addStmt($stmt);



        /* add subgroup doAction */

        $node->addStmt($subgroupfunctionnode);

        // add group to db if not exists yet      
        if (substr(trim($group[1]), 0, 1) != INDICATOR_FILL) {
            $gr = $this->survey->getGroupByName(trim($group[1]));
            if ($gr->getName() == "") {
                $gr = new Group();
                $gr->setSuid($this->suid);
                $gr->setName(trim($group[1]));
                $gr->setType(GROUP_SUB);
                $gr->save();
            }
        }
        // update overall counter
        $this->cnt = $rgid; // back to beginning of subgroup so we add any subquestions!
    }

    function addElse($function, &$node, $instruction) {

        $rule = strtoupper(trim($instruction->getRule()));

        $rgid = trim($instruction->getRgid());



        /* create else function */

        $elsefunctionnode = $this->factory->method($function);

        $elsefunctionnode->makePrivate();



        /* find endif */

        $endifrgid = $this->findEndIf($rgid);

        $beginifrgid = $this->findIf($rgid);

        //if (isset($this->elseifreset[$beginifrgid]) > 0) {
        //    unset($this->elseifreset[$beginifrgid]);
        //    $this->elseifreset = array_filter($this->elseifreset);
        //}

        $this->elseifreset[$beginifrgid] = $beginifrgid;


        /* get next (real: question or complex statement) rgid and do that, since else is always true */
        $nextrgid = $this->findNextStatement($rgid);

        /* next real action is before end of endif */
        if ($nextrgid < $endifrgid) {
            
        }
        // next statement is after the endif, then we need to ignore loop, else and so on 
        else {
            $nextrgid = $this->findNextStatementAfterQuestion($rgid);
        }


        if (sizeof($this->loops) > 0 || sizeof($this->whiles) > 0) {
            if (sizeof($this->loops) > 0) {
                $enddo = $this->findEndDo(end($this->loops));
                $endpoint = end($this->loops);
            } else {
                $enddo = -1;
            }
            if (sizeof($this->whiles) > 0) {
                $endwhile = $this->findEndWhile(end($this->whiles));
            } else {
                $endwhile = -1;
            }
            $end = "";
            if ($enddo != -1) {
                $end = $enddo;
            }
            if ($endwhile != -1 && ($endwhile < $end || $end == "")) { // end while is before end loop, then this is the first one we cross!
                $end = $endwhile;
                $endpoint = end($this->whiles);
            }

            if ($nextrgid > $end) {
                $nextrgid = $endpoint; //;
            } else if ($nextrgid == 0) {
                $nextrgid = $endpoint;
            }
        }

        // last loop action
        if ($this->lastloopactions[end($this->loops)] == $nextrgid) {
            $argsfalse[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($this->loops[sizeof($this->loops) - 1]));
        }
        // last while action
        else if ($this->lastwhileactions[end($this->whiles)] == $nextrgid) {
            $argsfalse[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($this->whiles[sizeof($this->whiles) - 1]));
        }
        // link to next action
        else {
            $argsfalse[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
        }

        $stmtsfalse = array();


        // not in group OR fill class, then link always to action (whether it is another action or back to beginning of loop)
        if (sizeof($this->groups) == 0 && $this->fillclass != true) {
            $stmtsfalse[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsfalse);
        } else {

            // fill class
            if ($this->fillclass) {

                // don't link back for fill class or next statement that is itself a loop statement
                if ((sizeof($this->loops) > 0 && $nextrgid == end($this->loops)) || inArray($nextrgid, end($this->loopactions))) {
                    $nextrgid = 0;
                }
                // don't link back for fill class or next statement that is itself a while statement
                else if ((sizeof($this->whiles) > 0 && $nextrgid == end($this->whiles)) || inArray($nextrgid, end($this->whileactions))) {
                    $nextrgid = 0;
                }

                if ($nextrgid > 0) {
                    $stmtsfalse[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsfalse));
                } else {
                    // no action needed
                }
            }
            // group!
            else {
                // don't link back for group to loop begin OR next statement that is itself a loop statement OR next statement that itself is a while statement
                //if ((sizeof($this->loops) > 0 && $nextfalsergid == end($this->loops)) || inArray($nextfalsergid, end($this->groupactions))) { // OLD ONE
                if ((sizeof($this->loops) > 0 && $nextrgid == end($this->loops)) || inArray($nextrgid, end($this->loopactions))) {
                    $nextrgid = 0;
                } else if ((sizeof($this->whiles) > 0 && $nextrgid == end($this->whiles)) || inArray($nextrgid, end($this->whileactions))) {
                    $nextrgid = 0;
                } else if ((sizeof($this->groups) > 0 && $nextrgid == end($this->groupsend)) || inArray($nextrgid, end($this->groupactions))) {
                    $nextrgid = 0;
                }
                if ($nextrgid > 0 && $nextrgid < end($this->groupsend)) {
                    $stmtsfalse[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsfalse));
                } else {
                    $stmtsfalse[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
                }
            }
        }

        /* add */
        /* $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));

          if (sizeof($this->groups) == 0) {
          $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args);
          } else {
          // next found AND not beyond end of group
          if ($nextrgid > 0 && $nextrgid < end($this->groupsend)) {
          $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args));
          } else {
          $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
          }
          $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args));
          } */

        /* add else doAction */
        $elsefunctionnode->addStmts($stmtsfalse);
        $node->addStmt($elsefunctionnode);
    }

    function prepare($lookup, $new, $string, $limit = -1) {
        return preg_replace($lookup, $new, $string, $limit);
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
                            $this->ifrgidafter = $this->instructions[$cnt]->getRgid();
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

            $beginifrgid = $this->findIf($rgid);

            // previous elseif, remove
            if (isset($this->elseifreset[$beginifrgid]) > 0) {
                unset($this->elseifreset[$beginifrgid]);
                $this->elseifreset = array_filter($this->elseifreset);
            }
            $this->elseifreset[$beginifrgid] = $beginifrgid;
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

        //$rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

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

        $rule = showModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT
        // hide module dot notations for parsing
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // replace [ and ] with ( and ), so the parser doesn't break
        // (we deal with these cases in the updateVariables function)
        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
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

    function addIf($function, &$node, $instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        $this->ifrgidafter = $rgid;

        $ifstmt = $this->analyzeIf($rule);
        if (!$ifstmt) {
            return;
        }

        $iftype = "";
        if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
            $iftype = ROUTING_IDENTIFY_IF;
        } else {
            $iftype = ROUTING_IDENTIFY_ELSEIF;
        }

        /* get next rgid for when if statement is false */

        $nextfalsergid = $this->findNextAfterIf($rgid, $iftype);

        /* create if function */

        $iffunctionnode = $this->factory->method($function);

        $iffunctionnode->makePrivate();



        /* if: true statement */

        /* get next (real: question or complex statement) rgid for when the if statement is true */
        $nexttruergid = $this->findNextStatement($this->ifrgidafter, true); // TODO: WE SAY TO IGNORE ELSE HERE, IS THAT CORRECT? I THINK SO, SINCE IF CANNOT BE FOLLOWED IF TRUE BY AN ELSE/ELSEIF

        /* if loop, check that next after if is true is not beyond the loop */
        if (sizeof($this->loops) > 0 || sizeof($this->whiles) > 0) {
            if (sizeof($this->loops) > 0) {
                $enddo = $this->findEndDo(end($this->loops));
                $endpoint = end($this->loops);
            } else {
                $enddo = -1;
            }
            if (sizeof($this->whiles) > 0) {
                $endwhile = $this->findEndWhile(end($this->whiles));
            } else {
                $endwhile = -1;
            }
            $end = "";
            if ($enddo != -1) {
                $end = $enddo;
            }
            if ($endwhile != -1 && ($endwhile < $end || $end == "")) { // end while is before end loop, then this is the first one we cross!
                $end = $endwhile;
                $endpoint = end($this->whiles);
            }
            if ($nexttruergid > $end) {
                $nexttruergid = $endpoint;
            } else if ($nexttruergid == 0) {
                $nexttruergid = $endpoint;
            }

            // don't link back for group to loop/while begin
            if (sizeof($this->groups) > 0 && $nexttruergid == $endpoint) {
                $nexttruergid = 0;
            }
            // don't link back for fill to loop/while begin
            else if ($this->fillclass == true && $nexttruergid == $endpoint) {
                $nexttruergid = 0;
            }
        }

        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nexttruergid));
        $stmts = array();

        // fill class
        if ($this->fillclass == true) {
            if ($nexttruergid > 0) {
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args);
            }
        }
        // no group
        else if (sizeof($this->groups) == 0) {
            $this->actions[] = $rgid;
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args);
        }
        // in group
        else {

            // next found AND not beyond group
            if ($nexttruergid > 0 && $nexttruergid < end($this->groupsend)) {
                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $args));
            } else {
                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
            }
        }

        $ifnode = new PHPParser_Node_Stmt_If($ifstmt, array('stmts' => $stmts));



        // not in group AND loop AND (not nested if OR nested if inside a nested loop), then set where we left off

        if ($iftype == ROUTING_IDENTIFY_IF && sizeof($this->groups) == 0) {

            // loop action
            if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                $setstatement = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
                $iffunctionnode->addStmt($setstatement);
            }
            // while action
            else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                $setstatement = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
                $iffunctionnode->addStmt($setstatement);
            }
        }

        $iffunctionnode->addStmt($ifnode);

        /* else: false statement */
        $copynext = $nextfalsergid;
        if (sizeof($this->loops) > 0 || sizeof($this->whiles) > 0) {

            if (sizeof($this->loops) > 0) {
                $enddo = $this->findEndDo(end($this->loops));
                $endpoint = end($this->loops);
            } else {
                $enddo = -1;
            }
            if (sizeof($this->whiles) > 0) {
                $endwhile = $this->findEndWhile(end($this->whiles));
            } else {
                $endwhile = -1;
            }
            $end = "";
            if ($enddo != -1) {
                $end = $enddo;
            }
            if ($endwhile != -1 && ($endwhile < $end || $end == "")) { // end while is before end loop, then this is the first one we cross!
                $end = $endwhile;
                $endpoint = end($this->whiles);
            }

            if ($nextfalsergid > $end) {
                $nextfalsergid = $endpoint;
            } else if ($nextfalsergid == 0) {
                $nextfalsergid = $endpoint;
            }
        }


        $endif = $this->findEndIf($rgid);

        // endif is BEFORE nextfalsergid, then check for last loop action
        if ($endif < $nextfalsergid) {

            // last loop action --> this if can be the last, but we still need to do an else, so we rely instead on whether the next false rgid is beyond the end of the loop
            // if that is the case, we deal with it below to link back to the beginning of the loop
            //if ($this->lastloopaction[end($this->loops)] == $nextfalsergid) {
            //    $argsfalse[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($this->loops[sizeof($this->loops) - 1]));
            //}
            // link to next action
            //else {
            $argsfalse[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextfalsergid));
            //}
        }
        // nextfalsergid is BEFORE endif, so then this must be an elseif or else
        else {
            $argsfalse[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextfalsergid));
        }

        $stmtsfalse = array();


        // not in group OR fill class, then link always to action (whether it is another action or back to beginning of loop)
        if (sizeof($this->groups) == 0 && $this->fillclass != true) {
            $stmtsfalse[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsfalse);
        } else {

            if ($this->fillclass) {

                // don't link back for fill class or next statement that is itself a loop statement
                if ((sizeof($this->loops) > 0 && $nextfalsergid == end($this->loops)) || inArray($nextfalsergid, end($this->loopactions))) {
                    $nextfalsergid = 0;
                } else if ((sizeof($this->whiles) > 0 && $nextfalsergid == end($this->whiles)) || inArray($nextfalsergid, end($this->whileactions))) {
                    $nextfalsergid = 0;
                }

                if ($nextfalsergid > 0) {
                    $stmtsfalse[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsfalse));
                } else {
                    // no action needed
                }
            } else {
                // don't link back for group to loop begin OR next statement that is itself a loop statement OR next statement that is itself a while statement
                //if ((sizeof($this->loops) > 0 && $nextfalsergid == end($this->loops)) || inArray($nextfalsergid, end($this->groupactions))) { // OLD ONE
                if ((sizeof($this->loops) > 0 && $nextfalsergid == end($this->loops)) || inArray($nextfalsergid, end($this->loopactions))) {
                    $nextfalsergid = 0;
                } else if ((sizeof($this->whiles) > 0 && $nextfalsergid == end($this->whiles)) || inArray($nextfalsergid, end($this->whileactions))) {
                    $nextfalsergid = 0;
                } else if ((sizeof($this->groups) > 0 && $nextfalsergid == end($this->groupsend)) || inArray($nextfalsergid, end($this->groupactions))) {
                    $nextfalsergid = 0;
                }
                if ($nextfalsergid > 0 && $nextfalsergid < end($this->groupsend)) {
                    $stmtsfalse[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsfalse));
                } else {
                    $stmtsfalse[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
                }
            }
        }

        $elsenode = new PHPParser_Node_Stmt_Else($stmtsfalse);
        $iffunctionnode->addStmt($elsenode);

        //}

        /* add if function */
        $node->addStmt($iffunctionnode);
    }

    function addAssignment($function, &$node, $instruction) {

        if ($this->checkclass == true) {
            return;
        }
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        // hide quoted text
        $excluded = array();
        $tempparts = splitString("/:=/", $rule, PREG_SPLIT_NO_EMPTY, 2);
        $checkvar = trim($tempparts[0]);
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        /* split into two */
        $parts = splitString("/:=/", $rule, PREG_SPLIT_NO_EMPTY, 2);
        if (sizeof($parts) != 2) {
            $this->addErrorMessage(Language::errorAssignmentInvalid());
            return;
        }

        /* get next (real: question or complex statement) rgid for after the assignment */
        $nextrgid = $this->findNextStatementAfterQuestion($rgid);

        /* create assignment */
        $assignfunctionnode = $this->factory->method($function);
        $assignfunctionnode->makePrivate();
        //$lefthand = showModuleNotationsPreserve(trim(includeText($parts[0], $excluded)), TEXT_MODULE_DOT);
        $lefthand = trim(includeText($parts[0], $excluded));
        $righthand = trim(includeText($parts[1], $excluded));
        $parser = new PHPParser_Parser(new PHPParser_Lexer);



        try {

            /* left hand */
            $stmtsleft = $parser->parse("<?php " . $lefthand . "?>");
            $st = $stmtsleft[0];
            $stmtleft = new PHPParser_Node_Arg($st); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node                        
            $this->updateVariables($stmtleft);
            //
            if ($stmtleft->value instanceof PHPParser_Node_Expr_MethodCall) {
                $args[] = $stmtleft->value->args[0];
            }
            // a non-bracketed field
            else {

                /* not a constant, which happens if the counter field does not exist */
                if ($stmtleft->value->name instanceof PHPParser_Node_Expr_MethodCall) {
                    $args[] = $stmtleft->value->name->args[0]->value;
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
            $args[] = $stmt;

            /* check if we need to add code for undoing assignment if going back */
            $name = $parts[0];
            $name = str_replace(TEXT_BRACKET_LEFT, "[", $name);
            $name = str_replace(TEXT_BRACKET_RIGHT, "]", $name);
            $variable = $this->survey->getVariableDescriptiveByName(getBasicName($name)); // new VariableDescriptive();
            // add check assignment statement if we are doing checking
            if ($variable->isValidateAssignment() == true) {
                $stmtvar = new PHPParser_Node_Scalar_String($checkvar);
                //$ifstmt = new PHPParser_Node_Expr_Equal(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_CHECK_ANSWER)), array($stmtvar, $stmt)), new PHPParser_Node_Scalar_LNumber(2));
                //$ifnode = new PHPParser_Node_Stmt_If($ifstmt, array('stmts' => array(new PHPParser_Node_Stmt_Return())));
                //$ifnode = new PHPParser_Node_Stmt_If($ifstmt, array('stmts' => array(new PHPParser_Node_Stmt_Return())));
                $assignfunctionnode->addStmt(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_CHECK_ANSWER)), array($stmtvar, $stmt)));
            }

            if ($variable->getVsid() != "") {
                $keep = $variable->isKeep();
                if ($keep == false) {

                    // check routing for .KEEP
                    global $db;
                    $q = "select * from " . Config::dbSurvey() . "_routing where suid=" . $this->suid . " and rule='" . trim(getBasicName($name)) . trim(ROUTING_KEEP) . "'";
                    if ($res = $db->selectQuery($q)) {
                        if ($db->getNumberOfRows($res) > 0) {
                            $keep = true;
                        }
                    }
                }
                if ($keep == false) {
                    $oldvaluenode = new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp"), $stmtleft->value);
                    $assignfunctionnode->addStmt($oldvaluenode);
                    if ($stmtleft->value instanceof PHPParser_Node_Expr_MethodCall) {
                        $undonode = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_ADD_ASSIGNMENT)), array($stmtleft->value->args[0], new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_LNumber($rgid)));
                        $assignfunctionnode->addStmt($undonode);
                    }
                    // a non-bracketed field
                    else {

                        /* not a constant, which happens if the counter field does not exist */
                        //if (!$stmtleft->value instanceof PHPParser_Node_Expr_ConstFetch) {
                        if ($stmtleft->value->name instanceof PHPParser_Node_Expr_MethodCall) {
                            $undonode = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_ADD_ASSIGNMENT)), array($stmtleft->value->name->args[0]->value, new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_LNumber($rgid)));
                            $assignfunctionnode->addStmt($undonode);
                        }
                    }
                }
            }
            $assignnode = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_SET_ANSWER)), $args);
            $assignfunctionnode->addStmt($assignnode);
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorAssignmentInvalid());
            return;
        }

        if (sizeof($this->groups) == 0) {
            $this->actions[] = $rgid;
        }

        /* action to follow (not in a loop/while!) */
        if (sizeof($this->loops) == 0 && sizeof($this->whiles) == 0) {

            if (sizeof($this->groups) == 0) {
                $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                $assignfunctionnode->addStmt($stmt);
            } else {

                if ((sizeof($this->groups) > 0 && $nextrgid == end($this->groupsend)) || inArray($nextrgid, end($this->groupactions)) || $nextrgid > end($this->groupsend)) {
                    $nextrgid = 0;
                }

                //if (!inArray($nextrgid, end($this->groupactions))) {
                if ($nextrgid > 0) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                    $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction));
                    $assignfunctionnode->addStmt($stmt);
                }
            }
        } else {

            // loop action and not fill class or group, then we keep track of where we are
            if ($this->fillclass != true && sizeof($this->groups) == 0) {
                if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
                    $assignfunctionnode->addStmt($stmt);
                } else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
                    $assignfunctionnode->addStmt($stmt);
                }
            }

            if (sizeof($this->loops) > 0) {
                $enddo = $this->findEndDo(end($this->loops));
                $endpoint = end($this->loops);
                $last = $this->lastloopactions[end($this->loops)];
                $number = $this->loops[sizeof($this->loops) - 1];
                $acts = end($this->loopactions);
            } else {
                $enddo = -1;
            }
            if (sizeof($this->whiles) > 0) {
                $endwhile = $this->findEndWhile(end($this->whiles));
            } else {
                $endwhile = -1;
            }
            $end = "";
            if ($enddo != -1) {
                $end = $enddo;
            }
            if ($endwhile != -1 && ($endwhile < $end || $end == "")) { // end while is before end loop, then this is the first one we cross!
                $end = $endwhile;
                $endpoint = end($this->whiles);
                $last = $this->lastwhileactions[end($this->whiles)];
                $number = $this->whiles[sizeof($this->whiles) - 1];
                $acts = end($this->whileactions);
            }

            if ($nextrgid > $end) {
                $nextrgid = $endpoint;
            }

            // last loop action, then link back to beginning of loop            
            if ($last == $rgid) {

                // not a group OR FILL code for loop, then link to beginning of loop!
                if (sizeof($this->groups) == 0 && $this->fillclass != true) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($number));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                    $assignfunctionnode->addStmt($stmt);
                }
            }
            // not last loop action, then link to action IF not itself a loop action
            else {

                /* in group, then link */
                if (sizeof($this->groups) > 0) {

                    // don't link if the next statement is the loop OR the last loop action OR it is a group action
                    if ($nextrgid != $endpoint && $nextrgid < $this->groupsend[end($this->groups)] && !inArray($nextrgid, $acts) && !inArray($nextrgid, end($this->groupactions))) {
                        $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                        $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction));
                        $assignfunctionnode->addStmt($stmt);
                    }
                }
                // not in a group 
                else {

                    // not in fill class, then link
                    if ($this->fillclass != true) {
                        $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                        $assignfunctionnode->addStmt($stmt);
                    } else {
                        // in fill class
                        // don't link if the next statement is the loop OR the last loop action
                        if ($nextrgid != $endpoint && !inArray($nextrgid, $acts)) {
                            $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                            $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction);
                            $assignfunctionnode->addStmt($stmt);
                        }
                    }
                }
            }
        }

        /* add assign function */
        $node->addStmt($assignfunctionnode);
    }

    function addCheckReturn($function, &$node, $instruction) {

        if ($this->checkclass == false) {
            return;
        }
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        // hide quoted text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

        /* replace brackets */
        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        if (!startsWith($rule, FUNCTION_CHECK_ERROR_RETURN)) {
            $this->addErrorMessage(Language::errorCheckReturnInvalid());
        }
        $rule = trim(str_ireplace(FUNCTION_CHECK_ERROR_RETURN, "", $rule));
        $rule = includeText($rule, $excluded);

        /* create check return */
        $checkfunctionnode = $this->factory->method($function);
        $checkfunctionnode->makePrivate();

        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {

            $level = ERROR_HARD;
            if (startsWith($rule, VARIABLE_VALUE_SOFT_ERROR)) {
                $level = ERROR_SOFT;
                $rule = trim(str_ireplace(VARIABLE_VALUE_SOFT_ERROR, "", $rule));
            } else if (startsWith($rule, VARIABLE_VALUE_HARD_ERROR)) {
                $rule = trim(str_ireplace(VARIABLE_VALUE_HARD_ERROR, "", $rule));
            } else {
                $this->addErrorMessage(Language::errorCheckReturnInvalid());
                return;
            }

            // numeric error line
            if (is_numeric($rule)) {
                $stmt = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rule));
                $args[] = $stmt;
            } else {
                $stmts = $parser->parse("<?php " . $rule . "?>");

                // only one statement
                $stmt = new PHPParser_Node_Arg($stmts[0]);
                $this->updateVariables($stmt);
                $args[] = $stmt;
            }

            // add set error level
            $stmtvar = new PHPParser_Node_Scalar_LNumber($level);
            $setnode = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_SET_CHECK_LEVEL)), array($stmtvar));
            $checkfunctionnode->addStmt($setnode);

            // add return error
            $returnnode = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_GET_ERROR_TEXT_BY_LINE)), $args));
            $checkfunctionnode->addStmt($returnnode);
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorCheckReturnInvalid());
            return;
        }

        /* add check function */
        $node->addStmt($checkfunctionnode);
    }

    function addInspect($node, $instruction) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }

        /* get details */
        $rgid = $instruction->getRgid();
        $function = FUNCTION_DO_INSPECT . $rgid;

        /* create inspect function */
        $inspectfunctionnode = $this->factory->method($function);
        $inspectfunctionnode->makePrivate();

        /* add call to show/return question */
        $excluded = array();
        $text = str_ireplace(ROUTING_IDENTIFY_INSPECT, "", $instruction->getRule());
        $rule = excludeText($text, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {
            $stmts = $parser->parse("<?php " . $rule . " ?>");
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);

            // fake method call for Q1[1,1] reference
            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value);
            } else {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value->name);
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorInspectInvalid());
            return;
        }

        $inspectfunctionnode->addStmt($stmt);

        /* add question inspect node */
        $node->addStmt($inspectfunctionnode);


        // not in a group
        if (sizeof($this->groups) == 0) {

            $stmts = array();
            // store where we go next from here
            $nextrgid = $this->findNextStatementAfterQuestion($rgid);
            $this->addNext($rgid, $nextrgid);

            // loop action
            if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
            } else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
            }

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array()));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_SHOW_QUESTION)), $args);
            $stmts[] = new PHPParser_Node_Stmt_Break();
            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;

            // add question screen 
            $this->addQuestionScreen($rgid);
        }

        // in a group
        else {

            /* add fill value statement */
            $stmts = array();
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array()));
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_ADD_FILL_VALUE)), $args);

            /* if this is a loop action, then loop statement will link to the next action, so no need to specify anything */
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            if ((sizeof($this->loops) == 0 || !inArray($rgid, $this->loopactions[end($this->loops)])) && (sizeof($this->whiles) == 0 || !inArray($rgid, $this->whileactions[end($this->whiles)]))) {
                $groupendrgid = end($this->groupsend);
                $nextrgid = $this->findNextStatementAfterQuestionInGroup($rgid, $groupendrgid);

                /* we have an action that is not itself a group action */
                if ($nextrgid > 0 && $nextrgid < $groupendrgid && !inArray($nextrgid, $this->groupactions[end($this->groups)]) && !inArray($nextrgid, $this->loopactions[end($this->loops)]) && !inArray($nextrgid, $this->whileactions[end($this->whiles)])) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                    $stmt = new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction));

                    $stmtthis = new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp1"), new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array()));

                    $cond = new PHPParser_Node_Expr_NotEqual(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_String(""));
                    $stmt1 = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Variable("temp1"), new PHPParser_Node_Scalar_String("~")), new PHPParser_Node_Expr_Variable("temp"));
                    $ifstmt = new PHPParser_Node_Stmt_If($cond, array('stmts' => array(new PHPParser_Node_Stmt_Return($stmt1))));
                    //$ifstmt = new PHPParser_Node_Stmt_If($cond, array('stmts' => array()));

                    $stmts[] = $stmt;
                    $stmts[] = $stmtthis;
                    $stmts[] = $ifstmt;
                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_Variable("temp1"));
                } else {
                    $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array()));
                }
            } else {
                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array()));
            }

            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
        }
    }

    function addInspectSection($node, $instruction) {

        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
            return;
        }

        $nextrgid = $this->findNextStatementAfterQuestion($rgid);

        // not in group                    
        if (sizeof($this->groups) == 0) {

            /* get details */
            $rgid = $instruction->getRgid();
            $function = FUNCTION_DO_INSPECT_SECTION . $rgid;

            /* create inspect function */
            $inspectfunctionnode = $this->factory->method($function);
            $inspectfunctionnode->makePrivate();

            /* add call to show/return question */
            $excluded = array();
            $text = str_ireplace(ROUTING_IDENTIFY_INSPECT_SECTION, "", $instruction->getRule());
            $rule = excludeText($text, $excluded);

            // hide module dot notations
            $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
            $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

            /* replace brackets */
            $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
            $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

            //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
            //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
            // hide module dot notations
            //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
            $rule = includeText($rule, $excluded);
            /* $section = $this->survey->getSectionByName($rule);
              if ($section->getName() != "") {
              $seid = $section->getSeid();
              }
              else {
              $this->addErrorMessage(Language::errorSectionInVariableNotFound($rule));
              return;
              } */

            $parser = new PHPParser_Parser(new PHPParser_Lexer);
            try {
                $stmts = $parser->parse("<?php " . $rule . " ?>");
                $stmt = $stmts[0];
                $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
                $this->updateVariables($stmt);

                // fake method call for Q1[1,1] reference
                if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                    $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_Concat($stmt->value, new PHPParser_Node_Scalar_String(".")));
                } else {
                    $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_Concat($stmt->value->name, new PHPParser_Node_Scalar_String(".")));
                }
            } catch (PHPParser_Error $e) {
                $this->addErrorMessage(Language::errorInspectInvalid());
                return;
            }

            $inspectfunctionnode->addStmt($stmt);

            /* add question inspect node */
            $node->addStmt($inspectfunctionnode);

            $stmts = array();
            // store where we go next from here
            // loop action
            if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
            } else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
            }

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array()));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));

            // seid needs to be called as $this->getInspectSection($this->getAnswer("SECTIONNAMEHERE"));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array("getSectionIdentifier")), array(new PHPParser_Node_Arg(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array($function)), array())))));

            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_SECTION)), $args);
            $stmts[] = new PHPParser_Node_Stmt_Break();
            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;
        } else {
            /* ignore everything */
        }

        $this->addNext($rgid, $nextrgid);
        // $this->addQuestionScreen($rgid, $seid); TODO: how would we get the seid here? we don't know it in advance, leave out for now
    }

    function addQuestion(&$node, $instruction) {


        // questions only allowed in main routing (not in fill code)
        if ($this->fillclass == true) {
            return;
        }
        if ($this->checkclass == true) {
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

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        try {

            $stmtstemp = $parser->parse("<?php " . $rule . " ?>");
            // only one statement (no ; allowed in assignment right hand side)
            $stmttemp = new PHPParser_Node_Arg($stmtstemp[0]); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmttemp);

            if ($stmttemp->value instanceof PHPParser_Node_Expr_MethodCall) {
                $args[] = $stmttemp->value->args[0];
            } else if ($stmttemp->value instanceof PHPParser_Node_Expr_Concat) {
                $args[] = $stmttemp->value;
            } else {
                $rule = showModuleNotations($rule, TEXT_MODULE_DOT);
                $args[] = new PHPParser_Node_Scalar_String($rule); /* no brackets */
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorVariableInvalid());
            return;
        }


        // not in a group
        if (sizeof($this->groups) == 0) {

            // store where we go next from here
            $nextrgid = $this->findNextStatementAfterQuestion($rgid);
            $this->addNext($rgid, $nextrgid);

            // loop action
            if (sizeof($this->loops) > 0 && inArray($rgid, $this->loopactions[end($this->loops)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
            } else if (sizeof($this->whiles) > 0 && inArray($rgid, $this->whileactions[end($this->whiles)])) {
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
            }

            /* inline (should never happen since always a group statement.
             * if it does happen, then ignore it so we show the question anyway */

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_SHOW_QUESTION)), $args);
            $stmts[] = new PHPParser_Node_Stmt_Break();
            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
            $this->actions[] = $rgid;

            // add question screen 
            $this->addQuestionScreen($rgid);
        }

        // in a group
        else {

            /* inline, then add as inline field */
            if ($inline == true) {
                $r = trim($instruction->getRule());
                $pos = strripos($r, ROUTING_IDENTIFY_INLINE);
                $r = substr($r, 0, $pos);

                /* add generic */
                $arg = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($r));
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_ADD_INLINE_FIELD)), array($arg));

                /* add specific if brackets */
                if (contains($r, "[")) {
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_ADD_INLINE_FIELD)), $args);
                }
            }

            /* add fill value statement */
            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_ADD_FILL_VALUE)), $args);

            /* if this is a loop action, then loop statement will link to the next action, so no need to specify anything */
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            if ((sizeof($this->loops) == 0 || !inArray($rgid, $this->loopactions[end($this->loops)])) && (sizeof($this->whiles) == 0 || !inArray($rgid, $this->whileactions[end($this->whiles)]))) {
                $groupendrgid = end($this->groupsend);
                $nextrgid = $this->findNextStatementAfterQuestionInGroup($rgid, $groupendrgid);

                /* we have an action that is not itself a group action */

                // next statement is redo loop, then don't
                if ($nextrgid == end($this->loops)) {
                    $stmts[] = new PHPParser_Node_Stmt_Return($args[0]);
                } else if ($nextrgid > 0 && $nextrgid < $groupendrgid && !inArray($nextrgid, $this->groupactions[end($this->groups)]) && !inArray($nextrgid, $this->loopactions[end($this->loops)]) && !inArray($nextrgid, $this->whileactions[end($this->whiles)])) {
                    $argsaction[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
                    $stmt = new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_ACTION)), $argsaction));

                    $cond = new PHPParser_Node_Expr_NotEqual(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_String(""));
                    $stmt1 = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat($args[0], new PHPParser_Node_Scalar_String("~")), new PHPParser_Node_Expr_Variable("temp"));
                    $ifstmt = new PHPParser_Node_Stmt_If($cond, array('stmts' => array(new PHPParser_Node_Stmt_Return($stmt1))));
                    //$stmts[] = new PHPParser_Node_Stmt_Return($stmt1);

                    $stmts[] = $stmt;
                    $stmts[] = $ifstmt;
                    $stmts[] = new PHPParser_Node_Stmt_Return($args[0]);
                } else {
                    $stmts[] = new PHPParser_Node_Stmt_Return($args[0]);
                }
            } else {
                $stmts[] = new PHPParser_Node_Stmt_Return($args[0]);
            }

            $this->doaction_cases[] = new PHPParser_Node_Stmt_Case(new PHPParser_Node_Scalar_LNumber($rgid), $stmts);
        }
    }

    function addQuestionScreen($rgid, $section = -1) {

        $this->screencounter++;

        global $db;

        $looprgid = -1;
        $looptimes = -1;
        $whilergid = -1;
        if (sizeof($this->loops) > 0) { // nested loop, then store any previous loop times
            $looptimes = implode("~", $this->lasttimesloop); //end($this->loops);
            $looprgid = implode("~", $this->loops); //end($this->loops);
        }
        if (sizeof($this->whiles) > 0) { // nested loop, then store any previous loop times
            $whilergid = implode("~", $this->whiles); //end($this->loops);
        }
        $ifrgid = 0;
        if (sizeof($this->elseifreset) > 0) {
            $ifrgid = end($this->elseifreset);
            array_pop($this->elseifreset);
        }
        if (sizeof($this->groups) > 0) {
            $ifrgid = 0;
        }

        $query = "replace into " . Config::dbSurvey() . "_screens (suid, seid, rgid, ifrgid, number, section, looptimes, outerlooptimes, outerlooprgids, outerwhilergids, dummy) values(" . prepareDatabaseString($this->suid) . ", " . prepareDatabaseString($this->seid) . ", '" . prepareDatabaseString($rgid) . "', '" . prepareDatabaseString($ifrgid) . "', '" . prepareDatabaseString($this->screencounter) . "', " . prepareDatabaseString($section) . ", " . prepareDatabaseString($this->looptimes) . ", '" . prepareDatabaseString($looptimes) . "','" . prepareDatabaseString($looprgid) . "','" . prepareDatabaseString($whilergid) . "', 0);";

        $db->executeQuery($query);
    }


    function addNext($from, $to) {

        global $db;

        if ($to == 0) {

            $to = "0"; // explicit zero since myisam doesn't like '' on newer mysql
        }

        $query = "replace into " . Config::dbSurvey() . "_next (suid, seid, fromrgid, torgid) values(" . prepareDatabaseString($this->suid) . ", " . prepareDatabaseString($this->seid) . ", '" . prepareDatabaseString($from) . "', '" . prepareDatabaseString($to) . "')";

        $db->executeQuery($query);
    }

    function addForLoop($function, &$node, $instruction) {

        if ($this->checkclass == true) {
            return;
        }
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        $rgidafter = $rgid;

        // hide text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        // TODO: handle brackets
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

                        //$pos = strripos($text, ROUTING_IDENTIFY_DO);
                        $rule .= " " . $text;

                        //if ($pos > -1) {
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
        $bounds = splitString("/ TO /", strtoupper($rule));
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



        /* create do function */

        $forfunctionnode = $this->factory->method($function);

        $forfunctionnode->makePrivate();



        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        try {



            $stmts = $parser->parse("<?php " . $minimum . "?>");

            // only one statement (no ; allowed in loop minimum)

            $stmt = $stmts[0];

            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node

            $this->updateVariables($stmt);
            $args[] = $stmt;


            $stmts = $parser->parse("<?php " . $maximum . "?>");

            // only one statement (no ; allowed in loop maximum)

            $stmt = $stmts[0];

            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node

            $this->updateVariables($stmt);

            $args[] = $stmt;
            $stmts = $parser->parse("<?php " . $counterfield . "?>");

            // only one statement (no ; allowed in loop maximum)

            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node

            $this->updateVariables($stmt);

            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $args[] = $stmt->value->args[0];
            } else {

                /* not a constant, which happens if the counter field does not exist */
                if ($stmt->value->name instanceof PHPParser_Node_Expr_MethodCall) {
                    $args[] = $stmt->value->name->args[0]->value;
                }
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorForLoopInvalid());
            return;
        }

        $enddo = $this->findEndDo($rgid);
        //if ($enddo == "") {
        //    $this->addErrorMessage(Language::errorForLoopMissingEnddo());
        //    return;
        //}
        // not in group
        $outerlooprgids = implode("~", $this->loops);
        $this->loops[] = $rgid;
        if (sizeof($this->groups) == 0 && $this->fillclass != true) {
            $this->actions[] = $rgid;
            $timesloop = $maximum;
            if (!is_numeric($timesloop)) {
                $timesloop = LOOP_MAXIMUM_IF_UNDEFINED;
            }
            $this->looptimes = $this->looptimes * $timesloop;
            $this->lasttimesloop[] = $timesloop;
            $outerloopcounters = implode("~", $this->loopcounters);
            $this->loopcounters[] = trim($counterfield);
            $this->progressbarloops[] = $rgid;

            // if nested loop, then size is greater than 1
            if (sizeof($this->loops) > 1) {

                $nextrgid = $this->findNextAfterForLoop($rgidafter, true);
                $enddo = $this->findEndDo($this->loops[sizeof($this->loops) - 2]);
                if ($nextrgid > $enddo) {
                    $nextrgid = $this->loops[sizeof($this->loops) - 2];
                } else if ($nextrgid == 0) {
                    $nextrgid = $this->loops[sizeof($this->loops) - 2];
                }

                // in basicengine: should return true if sizeof last loop action is smaller than loopcounter size?
                // i think now if we have a nested loop without any questions, but just assignments that it does not work
            }
            // not a nested loop
            else {
                $nextrgid = $this->findNextAfterForLoop($rgidafter, false);
            }

            // set last loop action if nested loop and loop action
            $stmts = array();
            if (sizeof($this->loops) > 1) {
                $arr = $this->loopactions[$this->loops[sizeof($this->loops) - 2]];
                if (inArray($rgid, $arr)) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 2));
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
                }
            }
            if (sizeof($this->whiles) > 0) {
                $arr = $this->whileactions[$this->whiles[sizeof($this->whiles) - 1]];
                if (inArray($rgid, $arr)) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 1));
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
                }
            }

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $this->findStatementsInLoop($rgid))));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($outerloopcounters));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($outerlooprgids));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($reversefor));

            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP)), $args);
            $this->loopnextrgids[] = $args;

            // add dummy entry if outer loop or while
            if (sizeof($this->loops) == 1) {
                global $db;
                $this->screencounter++;
                $query = "replace into " . Config::dbSurvey() . "_screens (suid, seid, rgid, ifrgid, number, section, looptimes, outerlooptimes, outerlooprgids, outerwhilergids, dummy) values(" . prepareDatabaseString($this->suid) . ", " . prepareDatabaseString($this->seid) . ", '" . prepareDatabaseString($rgid) . "', '" . prepareDatabaseString($ifrgid) . "', '" . prepareDatabaseString($this->screencounter) . "', -1, -1, '-1','-1','-1', 1);";
                $db->executeQuery($query);
            }
        }

        // in group OR fill class
        else {

            $nextrgid = $this->findNextAfterForLoop($rgid); // always go to next
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $this->findStatementsInLoop($rgid))));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($reversefor));
            //$args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String("")); // dont think we need this
            $stmts = array();

            // not fill class
            if ($this->fillclass != true) {
                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_GROUP)), $args));
            } else {
                $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP)), $args);
            }
            $this->loopnextrgids[] = $args;
        }
        $forfunctionnode->addStmts($stmts);

        /* add for loop function */
        $node->addStmt($forfunctionnode);
    }

    function findStatementsInLoop($rgid) {

        $level = 1;
        $loopactions = array();
        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {
            $rule = trim($this->instructions[$cnt]->getRule());
            if (startsWith($rule, "/*")) {
                $this->skipComments($cnt, $cnt);
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_FOR)) {

                /* nested for loop */
                $loopactions[] = $cnt; // . LOOP_MARKER;
                $cnt = $this->findEndDo($cnt); /* skip to the end */
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {
                $level--;
                if ($level > 0) {/* end of a for loop */
                    //    $level--;
                } else {/* end of the if, so return whatever comes after the endif */
                    break;
                }
            }

            // if statement, then we add it and then skip to end 
            else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
                $loopactions[] = $cnt;
                $cnt = $this->findEndIf($cnt);
            }
            // while statement, then we add it and then skip to end 
            else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {
                $loopactions[] = $cnt;
                $cnt = $this->findEndWhile($cnt);
            }
            // group statement we add 
            else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {
                $loopactions[] = $cnt;
                $cnt = $this->findEndGroup($cnt);
            }
            // sub group statement we add/ignore 
            else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

                // treat as loop action if overall group is not a loop action itself
                if (inArray(end($this->groups), $loopactions) == false) {
                    $loopactions[] = $cnt;
                }
                $cnt = $this->findEndSubGroup($cnt);
                if ($cnt == "") {
                    return;
                }
            }
            // if it is not an elseif, else, endif, or endcombine statement we add; i.e. assignment or question
            else if (!(startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE) || startsWith($rule, ROUTING_IDENTIFY_ENDIF) || startsWith($rule, ROUTING_IDENTIFY_ENDGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP))) {

                if ($this->fillclass == false) {
                    // check for .KEEP
                    if (!endsWith($rule, ROUTING_IDENTIFY_KEEP)) {
                        $loopactions[] = $cnt;
                    }
                } else {

                    // hide quoted text
                    $excluded = array();
                    $rule = excludeText($rule, $excluded);

                    // hide module dot notations
                    $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

                    // only allow assignments (no questions OR .KEEP)
                    if (contains($rule, ":=")) {
                        $loopactions[] = $cnt;
                    }
                }
            }
        }

        $this->loopactions[$rgid] = $loopactions;
        $this->lastloopactions[end($this->loops)] = end($loopactions);

        return $loopactions;
    }

    function findStatementsInWhile($rgid) {

        $level = 1;
        $whileactions = array();
        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());
            if (startsWith($rule, "/*")) {
                $this->skipComments($cnt, $cnt);
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {

                /* nested while loop */
                $whileactions[] = $cnt; // . LOOP_MARKER;
                $cnt = $this->findEndWhile($cnt); /* skip to the end */
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {
                $level--;
                if ($level > 0) {/* end of a for loop */
                    //    $level--;
                } else {/* end of the while, so return whatever comes after the endwhile */
                    break;
                }
            }

            // if statement, then we add it and then skip to end 
            else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
                $whileactions[] = $cnt;
                $cnt = $this->findEndIf($cnt);
            }
            // for statement, then we add it and then skip to end 
            else if (startsWith($rule, ROUTING_IDENTIFY_FOR)) {
                $whileactions[] = $cnt;
                $cnt = $this->findEndDo($cnt);
            }

            // group statement we add 
            else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {
                $whileactions[] = $cnt;
                $cnt = $this->findEndGroup($cnt);
            }
            // sub group statement we add/ignore 
            else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

                // treat as loop action if overall group is not a loop action itself
                if (inArray(end($this->groups), $loopactions) == false) {
                    $whileactions[] = $cnt;
                }
                $cnt = $this->findEndSubGroup($cnt);
                if ($cnt == "") {
                    return;
                }
            }

            // if it is not an elseif, else, endif, or endcombine statement we add; i.e. assignment or question
            else if (!(startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE) || startsWith($rule, ROUTING_IDENTIFY_ENDIF) || startsWith($rule, ROUTING_IDENTIFY_ENDGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP))) {

                if ($this->fillclass == false) {
                    // check for .KEEP
                    if (!endsWith($rule, ROUTING_IDENTIFY_KEEP)) {
                        $whileactions[] = $cnt;
                    }
                } else {

                    // hide quoted text
                    $excluded = array();
                    $rule = excludeText($rule, $excluded);

                    // hide module dot notations
                    $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

                    // only allow assignments (no questions OR .KEEP)
                    if (contains($rule, ":=")) {
                        $whileactions[] = $cnt;
                    }
                }
            }
        }

        $this->whileactions[$rgid] = $whileactions;
        $this->lastwhileaction[end($this->whiles)] = end($whileactions);

        return $whileactions;
    }

    function addWhileLoop($function, &$node, $instruction) {

        if ($this->checkclass == true) {
            return;
        }
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        $rgidafter = $rgid;

        // hide text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        // TODO: bracket handling
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

        // strip WHILE
        $rule = substr($rule, stripos($rule, ROUTING_IDENTIFY_WHILE) + strlen(ROUTING_IDENTIFY_WHILE));

        /* multi-line while */

        //if ($pos < 0) {
        if (endsWith(strtoupper($rule), ROUTING_IDENTIFY_DO) == false) {
            for ($cnt = ($this->cnt + 1); $cnt <= sizeof($this->instructions); $cnt++) {
                if (isset($this->instructions[$cnt])) {
                    $text = trim($this->instructions[$cnt]->getRule());
                    if (startsWith($text, "/*")) {
                        $this->skipComments($cnt, $cnt);
                    } else if (startsWith($text, "//")) {
                        
                    } else {

                        //$pos = strripos($text, ROUTING_IDENTIFY_DO);
                        $rule .= " " . $text;

                        //if ($pos > -1) {
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
            $this->addErrorMessage(Language::errorWhileMissingDo());
            return;
        }

        // strip do
        $rule = trim(substr($rule, 0, $pos));
        $condition = $this->analyzeIf(ROUTING_IDENTIFY_IF . $rule . ' ' . ROUTING_THEN, true);

        /* create do function */
        $whilefunctionnode = $this->factory->method($function);
        $whilefunctionnode->makePrivate();
        $parser = new PHPParser_Parser(new PHPParser_Lexer);
        $enddo = $this->findEndWhile($rgid);

        $outerwhilergids = implode("~", $this->whiles);
        $this->whiles[] = $rgid;
        $nextrgid = $this->findNextAfterWhile($rgidafter, false);
        // set last loop action if nested loop and loop action
        $stmts = array();

        if (sizeof($this->groups) == 0 && $this->fillclass != true) {
            $this->actions[] = $rgid;

            // if nested while, then size is greater than 1
            if (sizeof($this->whiles) > 1) {

                $nextrgid = $this->findNextAfterWhile($rgidafter, true);
                $enddo = $this->findEndWhile($this->whiles[sizeof($this->whiles) - 2]);
                if ($nextrgid > $enddo) {
                    $nextrgid = $this->whiles[sizeof($this->whiles) - 2];
                } else if ($nextrgid == 0) {
                    $nextrgid = $this->whiles[sizeof($this->whiles) - 2];
                }

                // in basicengine: should return true if sizeof last loop action is smaller than loopcounter size?
                // i think now if we have a nested loop without any questions, but just assignments that it does not work
            }
            // not a nested loop
            else {
                $nextrgid = $this->findNextAfterWhile($rgidafter, false);
            }

            // set last while action if nested while and while action
            $stmts = array();
            if (sizeof($this->whiles) > 1) {
                $arr = $this->whileactions[$this->whiles[sizeof($this->whiles) - 2]];
                if (inArray($rgid, $arr)) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->whiles) - 2));
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_LEFTOFF)), $s);
                }
            }
            if (sizeof($this->loops) > 0) {
                $arr = $this->loopactions[$this->loops[sizeof($this->loops) - 1]];
                if (inArray($rgid, $arr)) {
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
                    $s[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(sizeof($this->loops) - 1));
                    $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_LOOP_LEFTOFF)), $s);
                }
            }

            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $this->findStatementsInWhile($rgid))));
            $condition = rtrim($condition, ';');
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($condition));  //there is no boolean?
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($outerwhilergids));

            $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE)), $args);
            $this->whilenextrgids[] = $args;
        }

        // in group OR fill class
        else {

            $nextrgid = $this->findNextAfterWhile($rgid); // always go to next            
            $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $this->findStatementsInWhile($rgid))));
            //$args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($condition));  //there is no boolean?
            //$args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
            //$args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
            //$args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String("")); // dont think we need this
            $stmts = array();

            // not fill class
            if ($this->fillclass != true) {
                $sub = array();
                $subnode1 = array(new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_GROUP)), $args)));
                $subnode2 = array(new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_String("~")), new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE_GROUP)), $args))));
                $else = new PHPParser_Node_Stmt_Else($subnode2);
                $sub[] = new PHPParser_Node_Stmt_If(new PHPParser_Node_Expr_Equal(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_String), array('stmts' => $subnode1, 'else' => $else));
                $stmts[] = new PHPParser_Node_Expr_Assign(new PHPParser_Node_Expr_Variable("temp"), new PHPParser_Node_Scalar_String(""));
                $stmts[] = new PHPParser_Node_Stmt_While($condition, $sub);
                $stmts[] = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Expr_Variable("temp"));
            } else {
                $sub = array();
                $sub[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE)), $args);
                $stmts[] = new PHPParser_Node_Stmt_While($condition, $sub);
            }
            $this->whilenextrgids[] = $args;
        }

        /* $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String(implode("~", $this->findStatementsInWhile($rgid))));
          $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($condition));  //there is no boolean?
          $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($rgid));
          $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($nextrgid));
          //$args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($outerloopcounters));
          $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($outerlooprgids));
          $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber(1));
          $stmts[] = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_DO_WHILE)), $args);
          $this->whilenextrgids[] = $args;
         */
        $whilefunctionnode->addStmts($stmts);
        /* add while function */
        $node->addStmt($whilefunctionnode);
    }

    function findNextAfterForLoop($rgid, $nestedloop = false) {

        $level = 1;
        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {
            $rule = trim($this->instructions[$cnt]->getRule());
            if (startsWith($rule, "/*")) {
                $this->skipComments($cnt, $cnt);
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_FOR)) {
                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {
                $level--;
                if ($level > 0) {/* end of a for loop */
                    //    $level--;
                } else {/* end of the do */

                    // not nested, so return whatever comes after the enddo
                    if ($nestedloop == false) {
                        return $this->findNextStatement($cnt, true);
                    }

                    // nested loop, then see if we have a statement BEFORE the outer enddo
                    else {
                        $endouterdo = $this->findEndDo($cnt, $this->instructions);
                        $next = $this->findNextStatement($cnt, true);

                        // next statement before end of outer loop
                        if ($next < $endouterdo) {
                            return $next;
                        }

                        // not next statement before end of outer loop, then back to outer loop statement
                        else {
                            return $this->loops[sizeof($this->loops) - 2];
                        }
                    }
                }
            }
        }

        return 0;
    }

    function findNextAfterWhile($rgid, $nestedloop = false) {

        $level = 1;

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {
                $this->skipComments($cnt, $cnt);
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {
                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {
                $level--;
                if ($level > 0) {/* end of a for loop */
                    //    $level--;
                } else {/* end of the do */

                    // not nested, so return whatever comes after the enddo
                    if ($nestedloop == false) {
                        return $this->findNextStatement($cnt, true);
                    }

                    // nested loop, then see if we have a statement BEFORE the outer endwhile
                    else {
                        $endouterdo = $this->findEndWhile($cnt, $this->instructions);
                        $next = $this->findNextStatement($cnt, true);

                        // next statement before end of outer loop
                        if ($next < $endouterdo) {
                            return $next;
                        }
                        // not next statement before end of outer while, then back to outer while statement
                        else {
                            return $this->whiles[sizeof($this->whiles) - 2];
                        }
                    }
                }
            }
        }

        return 0;
    }

    function findNextStatement($rgid, $ignoreelse = false) {

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            if (isset($this->instructions[$cnt])) {

                $rule = trim($this->instructions[$cnt]->getRule());

                if (startsWith($rule, "/*")) {

                    $this->skipComments($cnt, $cnt);

                } else if (startsWith($rule, "//")) {
                    
                } else if ($rule == "") {
                    
                } else if ((startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE)) && $ignoreelse == true) {

                    $cnt = $this->findEndIf($cnt); // skip until end
                } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {
                    
                } else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {
                    
                } else if (startsWith($rule, ROUTING_IDENTIFY_ENDIF)) {
                    
                } else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP)) {
                    
                } else if (startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP)) {
                    
                } else {

                    // not a fill class, then allow more

                    if ($this->fillclass == false) {

                        /* ignore. KEEP */
                        if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false) {
                            return $cnt;
                        }
                    } else {

                        // no groups
                        if (startsWith($rule, ROUTING_IDENTIFY_GROUP) || startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {
                            
                        } else {

                            // hide quoted text
                            $excluded = array();
                            $rule = excludeText($rule, $excluded);

                            // hide module dot notations
                            $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

                            // only allow if/for/assignments (no questions OR .KEEP)
                            if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false && contains($rule, " ")) {
                                return $cnt;
                            }
                        }
                    }
                }
            }
        }

        return 0;
    }

    function findEndIf($rgid) {

        $level = 1;

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {

                /* nested if inside an if/elseif */

                $level++;
            } else if (startsWith($rule, ROUTING_ENDIF)) {

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            }
        }

        $this->addErrorMessage(Language::errorIfMissingEndif(), $rgid);
    }

    function findIf($rgid) {

        $level = 1;

        for ($cnt = ($rgid - 1); $cnt > 0; $cnt--) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "*/")) {

                $this->skipReverseComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {

                /* nested for inside a for */

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDIF)) {

                $level++;
            }
        }
    }

    function findFor($rgid) {

        $level = 1;

        for ($cnt = ($rgid - 1); $cnt > 0; $cnt--) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_FOR) || startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {

                /* nested for inside a for */

                $level--;
                if ($level == 0) {
                    return $cnt;
                }
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {

                $level++;
            }
        }
    }

    function findEndDo($rgid, $temp = null) {

        $level = 1;

        if ($temp == null) {

            $temp = $this->instructions;
        }

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_FOR) || startsWith($rule, ROUTING_IDENTIFY_FORREVERSE)) {

                /* nested for inside a for */

                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            }
        }

        $this->addErrorMessage(Language::errorForLoopMissingEnddo(), $rgid);
    }

    function findWhile($rgid) {

        $level = 1;

        for ($cnt = ($rgid - 1); $cnt > 0; $cnt--) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {

                /* nested while inside a while */

                $level--;
                if ($level == 0) {
                    return $cnt;
                }
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {

                $level++;
            }
        }
    }

    function findEndWhile($rgid, $temp = null) {

        $level = 1;

        if ($temp == null) {

            $temp = $this->instructions;
        }

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);
                
            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_WHILE)) {

                /* nested for inside a for */

                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            }
        }

        $this->addErrorMessage(Language::errorWhileMissingEndWhile(), $rgid);
    }

    function findEndGroup($rgid) {

        $level = 1;

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {

                /* nested combine (should never happen) */

                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP)) {

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            }
        }

        $this->addErrorMessage(Language::errorGroupMissingEndGroup(), $rgid);
    }

    function findEndSubGroup($rgid) {

        $level = 1;

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

                /* nested subgroup */

                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP)) {

                $level--;

                if ($level == 0) {

                    return $cnt;
                }
            }
        }

        $this->addErrorMessage(Language::errorGroupMissingEndSubGroup(), $rgid);
    }

    function findNextAfterIf($rgid, $iftype) {

        $level = 1;

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {

                /* nested if inside an if/elseif */

                $level++;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ELSEIF)) {

                if ($level == 1) { /* not a nested elseif */

                    return $cnt;
                } else { /* nested elseif */
                }
            } else if (startsWith($rule, ROUTING_ENDIF)) {

                $level--;

                if ($level > 0) {/* end of a nested if */
                } else {/* end of the if, so return whatever comes after the endif */

                    return $this->findNextStatement($cnt, true); // todo: do we need to call this ignore an else/elseif?
                }
            } else if (startsWith($rule, ROUTING_ELSE)) {

                // if/elseif statement, so this else could be the next one

                if ($level == 1) { /* not a nested else */

                    return $cnt;
                } else { /* nested else */
                }
            } else {

                if ($level == 0) {



                    // not a fill class, then allow more

                    if ($this->fillclass == false) {

                        /* ignore. KEEP */
                        if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false) {
                            return $cnt;
                        }
                    } else {



                        // no groups

                        if (startsWith($rule, ROUTING_IDENTIFY_GROUP) || startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {
                            
                        } else {

                            // hide quoted text

                            $excluded = array();

                            $rule = excludeText($rule, $excluded);



                            // hide module dot notations
                            $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);



                            // only allow if/for/assignments (no questions OR .KEEP)
                            if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false && contains($rule, " ")) {
                                return $cnt;
                            }
                        }
                    }
                }
            }
        }
    }

    function findNextStatementAfterQuestion($rgid) {

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE)) {

                $cnt = $this->findEndIf($cnt);
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {// we were inside a loop, then go back to start of loop (we exit only inside the loop code, not here)
                $cnt = $this->findFor($cnt) - 1;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDWHILE)) {// we were inside a while, then go back to start of while (we exit only inside the while code, not here)
                $cnt = $this->findWhile($cnt) - 1;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDIF)) {

                /* we were inside a complex statement and are going out now, so find the next statement on the level above */
            } else {



                // not a fill class, then allow more

                if ($this->fillclass == false) {

                    /* ignore. KEEP */
                    if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false) {
                        return $cnt;
                    }
                } else {



                    // no groups

                    if (startsWith($rule, ROUTING_IDENTIFY_GROUP) || startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {
                        
                    } else {

                        // hide quoted text

                        $excluded = array();

                        $rule = excludeText($rule, $excluded);



                        // hide module dot notations

                        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);



                        // only allow if/for/assignments (no questions OR .KEEP)
                        if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false && contains($rule, " ")) {
                            return $cnt;
                        }
                    }
                }
            }
        }

        return 0;
    }

    function findNextStatementAfterQuestionInGroup($rgid, $groupend) {

        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {
            if ($cnt == $groupend) {
                break;
            }
            $rule = trim($this->instructions[$cnt]->getRule());

            if (startsWith($rule, "/*")) {

                $this->skipComments($cnt, $cnt);

            } else if (startsWith($rule, "//")) {
                
            } else if ($rule == "") {
                
            } else if (startsWith($rule, ROUTING_IDENTIFY_ELSEIF) || startsWith($rule, ROUTING_IDENTIFY_ELSE)) {

                $cnt = $this->findEndIf($cnt);
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {// we were inside a loop, then go back to start of loop (we exit only inside the loop code, not here)
                $cnt = $this->findFor($cnt) - 1;
            } else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP) || startsWith($rule, ROUTING_IDENTIFY_ENDIF)) {

                /* we were inside a complex statement and are going out now, so find the next statement on the level above */
            } else {



                // groups cannot occur in a fill class

                if ($this->fillclass == false) {

                    /* ignore. KEEP */
                    if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false) {
                        return $cnt;
                    }
                } else {



                    // no groups

                    if (startsWith($rule, ROUTING_IDENTIFY_GROUP) || startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {
                        
                    } else {

                        // hide quoted text

                        $excluded = array();

                        $rule = excludeText($rule, $excluded);



                        // hide module dot notations

                        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);



                        // only allow if/for/assignments (no questions OR .KEEP)
                        if (endsWith($rule, ROUTING_IDENTIFY_KEEP) == false && contains($rule, " ")) {
                            return $cnt;
                        }
                    }
                }
            }
        }

        return 0;
    }

    function skipComments($rgid, &$updatergid) {



        // ends on same line!        

        if (isset($this->instructions[$rgid])) {

            $rule = trim($this->instructions[$rgid]->getRule());

            if (endsWith($rule, "*/")) {

                if ($updatergid != null) {

                    $updatergid = $this->instructions[$rgid]->getRgid();

                    return;
                }
            }
        }



        for ($cnt = ($rgid + 1); $cnt <= sizeof($this->instructions); $cnt++) {

            if (isset($this->instructions[$cnt])) {

                $rule = trim($this->instructions[$cnt]->getRule());

                if (startsWith($rule, "*/")) {

                    if ($updatergid != null) {

                        $updatergid = $this->instructions[$cnt]->getRgid();
                    }

                    break;
                }
            }
        }
    }

    function skipReverseComments($rgid, &$updatergid) {



        // ends on same line!        

        if (isset($this->instructions[$rgid])) {

            $rule = trim($this->instructions[$rgid]->getRule());

            if (startsWith($rule, "/*")) {

                if ($updatergid != null) {

                    $updatergid = $this->instructions[$rgid]->getRgid();

                    return;
                }
            }
        }


        for ($cnt = ($rgid - 1); $cnt >= 0; $cnt--) {

            if (isset($this->instructions[$cnt])) {

                $rule = trim($this->instructions[$cnt]->getRule());

                if (startsWith($rule, "/*")) {

                    if ($updatergid != null) {

                        $updatergid = $this->instructions[$cnt]->getRgid();
                    }

                    break;
                }
            }
        }
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
                $name = str_replace(TEXT_BRACKET_RIGHT_MODULE, "].", $name);
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

                    //} else if ($var->setVariableDescriptive(getBasicName($name))) {
                } else if ($var->getVsid() != "") {
                    
                    // check anything in name before any .
                    $nms = explode(".", $name);
                    for ($i = 0; $i < sizeof($nms) - 1; $i++) {

                        $var = $this->survey->getVariableDescriptiveByName(getBasicName($nms[$i]));
                        if ($var->isArray() == false && contains("[", $nms[$i])) {
                            $this->addErrorMessage(Language::errorNotArray(strtolower(getBasicName($nms[$i]))));
                        }
                        else if ($var->isArray() == true && !contains("[", $nms[$i])) {
                            $this->addErrorMessage(Language::errorVariableNoArrayIndex(strtolower(getBasicName($nms[$i]))));
                        }
                    }
                    
                    $answertype = $var->getAnswerType();

                    if (inArray($answertype, array(ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_RANK))) {
                        $this->lastvar = $var;
                    }
                    
                    // an array, but not an array statement
                    //if ($var->isArray() == true) {
                    //    $this->addErrorMessage(Language::errorArray(getBasicName($name)));
                    //}
                    $args = array();
                    $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($name));
                    $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_GET_ANSWER)), $args);
                    $node->$nm = $stmt;
                } else if ($this->fillclass == true && startsWith($name, VARIABLE_VALUE_FILL)) {

                    $line = trim(str_ireplace(VARIABLE_VALUE_FILL, "", $name));
                    if ($line != "") {

                        $args = array();
                        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($this->currentfillvariable));

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
                                    if ($temp->value->name instanceof PHPParser_Node_Expr_MethodCall) {
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
                        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_GET_FILL_TEXT_BY_LINE)), $args);
                        $node->$nm = $stmt;
                    }
                } else if ($this->checkclass && (startsWith($name, VARIABLE_VALUE_SOFT_ERROR) || startsWith($name, VARIABLE_VALUE_HARD_ERROR))) {
                    $type = ERROR_HARD;

                    if (startsWith($name, VARIABLE_VALUE_SOFT_ERROR)) {
                        $line = trim(str_ireplace(VARIABLE_VALUE_SOFT_ERROR, "", $name));
                    } else {
                        $type = ERROR_SOFT;
                        $line = trim(str_ireplace(VARIABLE_VALUE_HARD_ERROR, "", $name));
                    }
                    if ($line != "") {

                        $args = array();
                        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_String($this->currentfillvariable));

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
                                    if ($temp->value->name instanceof PHPParser_Node_Expr_MethodCall) {
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

                        $args[] = new PHPParser_Node_Arg(new PHPParser_Node_Scalar_LNumber($type));
                        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_GET_ERROR_TEXT_BY_LINE)), $args);
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
            // bit and call: things like secA[cnt].Q1
            else if ($subnode instanceof PHPParser_Node_Expr_BitwiseAnd) {
                
                $left = $subnode->left;
                $right = $subnode->right;
                
                // this applies to assignments like sec1[cnt].Q1
                if ($right instanceof PHPParser_Node_Expr_ConstFetch) {
                    $this->extranode = new PHPParser_Node_Scalar_String($right->name->parts[0]);

                    $subnode = new PHPParser_Node_Arg($left);
                    $this->updateVariables($subnode);
                    $node->$nm = $subnode->value;
                }
                // this applies to assignments like sec1[cnt].Q1[cnt]
                else if ($right instanceof PHPParser_Node_Expr_FuncCall) {
                                        
                    $n = getBasicName($right->name->parts[0]);
                    $tv = $this->survey->getVariableDescriptiveByName($n); // new VariableDescriptive();  
                    if ($tv->isArray() == false) {
                        $this->addErrorMessage(Language::errorNotArray(strtolower($n)));                        
                    }
                    $node1 = new PHPParser_Node_Scalar_String($right->name->parts[0]);
                    $args = $right->args;
                    $concat = new PHPParser_Node_Expr_Concat($node1, new PHPParser_Node_Scalar_String("["));                    
                    $concat = new PHPParser_Node_Expr_Concat($concat, $this->getBrackets($args));
                    $concat = new PHPParser_Node_Expr_Concat($concat, new PHPParser_Node_Scalar_String("]"));
                    $this->extranode = $concat;

                    $subnode = new PHPParser_Node_Arg($left);
                    $this->updateVariables($subnode);
                    $node->$nm = $subnode->value;
                }
                // deal with things like sec1[cnt].Q1 == 2 and similar things
                else {
                    
                    $rightleft = $right->left;
                    if ($rightleft instanceof PHPParser_Node_Expr_ConstFetch) {
                        $this->extranode = new PHPParser_Node_Scalar_String($right->left->name->parts[0]);
                    } else if ($rightleft instanceof PHPParser_Node_Expr_FuncCall) {

                        // TODO: add check for if variable is array
                        $node1 = new PHPParser_Node_Scalar_String($rightleft->name->parts[0]);
                        $args = $rightleft->args;
                        $concat = new PHPParser_Node_Expr_Concat($node1, new PHPParser_Node_Scalar_String("["));                        
                        $concat = new PHPParser_Node_Expr_Concat($concat, $this->getBrackets($args));                        
                        $concat = new PHPParser_Node_Expr_Concat($concat, new PHPParser_Node_Scalar_String("]"));
                        $this->extranode = $concat;
                    }

                    $tt = new PHPParser_Node_Arg($left);
                    $this->updateVariables($tt);

                    $subnode->left = $tt;
                    $subnode->right = $right->right;
                    $type = $right->getType();
                    $class = "";
                    if (class_exists("PHPParser_Node_" . $type)) {
                        $class = "PHPParser_Node_" . $type;
                        $sr = new PHPParser_Node_Arg($subnode->right);                        
                        $this->updateVariables($sr);
                        $newnode = new $class($subnode->left->value, $sr->value);
                        $node->$nm = $newnode;                        
                    } else {
                        $this->addErrorMessage(Language::errorInvalidExpression());
                        $node->$nm = $subnode; // used class doesn't exist, so we ignore it for now
                    }
                }

                $this->extranode = null;
                //return;
            }
            // concat/minus/plus/mod/div handling (need custom handling to support [cnt] in the right hand of the expression)
            else if ($subnode instanceof PHPParser_Node_Expr_Concat || $subnode instanceof PHPParser_Node_Expr_Minus || $subnode instanceof PHPParser_Node_Expr_Plus || $subnode instanceof PHPParser_Node_Expr_Mod || $subnode instanceof PHPParser_Node_Expr_Div) {

                $left = new PHPParser_Node_Arg($subnode->left);
                $right = new PHPParser_Node_Arg($subnode->right);
                $this->updateVariables($left); 
                $this->updateVariables($right);
                $subnode->left = $left->value;
                $subnode->right = $right->value;
                $node-$nm = $subnode;
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
                    $args = $subnode->args;
                    for ($j = 0; $j < sizeof($args); $j++) {                          
                        $this->updateVariables($args[$j]);
                    }
                } else {

                    // check anything in name before any .
                    $nms = explode(".", $name);
                    for ($i = 0; $i < sizeof($nms) - 1; $i++) {
                        $var = $this->survey->getVariableDescriptiveByName(getBasicName($nms[$i]));
                        if ($var->isArray() == false && contains("[", $nms[$i])) {
                            $this->addErrorMessage(Language::errorNotArray(strtolower(getBasicName($nms[$i]))));
                        }
                        else if ($var->isArray() == true && !contains("[", $nms[$i])) {
                            $this->addErrorMessage(Language::errorVariableNoArrayIndex(strtolower(getBasicName($nms[$i]))));
                        }
                    }
                    
                    $var = $this->survey->getVariableDescriptiveByName(getBasicName($name)); // new VariableDescriptive();
                    //if ($var->setVariableDescriptive(getBasicName($name))) {

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
                        $funcnode = $this->handleBracketExpression($subnode, $name);                        
                        if ($this->extranode != null) {
                            
                            //$args[] = $funcnode;
                            //$args[] = $this->extranode;
                            // construct left hand side
                            $brack = new PHPParser_Node_Expr_Concat($funcnode, new PHPParser_Node_Scalar_String("."));

                            // construct right hand side
                            $brack = new PHPParser_Node_Expr_Concat($brack, $this->extranode);

                            $args[] = $brack;
                        } else {
                            $args[] = $funcnode;
                        }

                        $stmt = new PHPParser_Node_Expr_MethodCall(new PHPParser_Node_Expr_Variable(VARIABLE_THIS), new PHPParser_Node_Name(array(FUNCTION_GET_ANSWER)), $args);
                        $node->$nm = $stmt;
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

    function handleBracketExpression($subnode, $name) {

        // get arguments of q1[cnt+cnt1-getTest("1)] --> 'function call': q1(cnt+cnt1-getTest("1))
        $args = $subnode->args;

        // construct left hand side
        $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String($name . "["), new PHPParser_Node_Scalar_String("dummy"));

        // construct right hand side
        $bracketnode->right = new PHPParser_Node_Expr_Concat($this->getBrackets($args), new PHPParser_Node_Scalar_String("]"));

        // return result        
        return $bracketnode;
    }

    function addInlineField($text) {

        $excluded = array();
        $rule = excludeText($text, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        $this->factory = new PHPParser_BuilderFactory();
        $this->printer = new PHPParser_PrettyPrinter_Default();
        $classextension = prepareClassExtension($text);

        $rootnode = $this->factory->class(CLASS_INLINEFIELD . "_" . $classextension)->extend(CLASS_BASICINLINEFIELD);
        $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
        try {
            $stmts = $parser->parse("<?php " . $rule . " ?>");
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);

            // fake method call for Q1[1,1] reference            
            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmt_name = new PHPParser_Node_Stmt_Return($stmt->value->args[0]);
                //$stmt = new PHPParser_Node_Stmt_Return($stmt->value);
            } else {
                $stmt_name = new PHPParser_Node_Stmt_Return($stmt->value->name->args[0]->value);
                //$stmt = new PHPParser_Node_Stmt_Return($stmt->value->name);
            }
        } catch (PHPParser_Error $e) {
            return;
        }

        /* add getInlineField function */
        $getinlinefield = $this->factory->method(FUNCTION_GET_INLINE_FIELD);
        $getinlinefield->makePublic();
        $getinlinefield->addStmt($stmt_name);
        $rootnode->addStmt($getinlinefield);

        /* get statements */
        $stmts = array($rootnode->getNode());

        /* generate code */
        $class = $this->printer->prettyPrint($stmts);

        /* return result */
        return $class;
    }

    function addFill($text) {

        $excluded = array();
        $rule = excludeText($text, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        $this->factory = new PHPParser_BuilderFactory();
        $this->printer = new PHPParser_PrettyPrinter_Default();
        $classextension = prepareClassExtension($text);

        $rootnode = $this->factory->class(CLASS_GETFILL . "_" . $classextension)->extend(CLASS_BASICFILL);
        $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
        try {
            $stmts = $parser->parse("<?php " . $rule . " ?>");
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);

            // fake method call for Q1[1,1] reference
            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value);
            } else {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value->name);
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorFillInvalid());
            return;
        }

        /* add getFillValue function */
        $getfillvalue = $this->factory->method(FUNCTION_GET_FILL_VALUE);
        $getfillvalue->makePublic();
        $getfillvalue->addStmt($stmt);
        $rootnode->addStmt($getfillvalue);

        /* get statements */
        $stmts = array($rootnode->getNode());

        /* generate code */
        $fillclass = $this->printer->prettyPrint($stmts);

        /* return result */
        return $fillclass;
    }

    function addFillNoValue($text) {

        $excluded = array();
        $rule = excludeText($text, $excluded);

        // hide module dot notations
        $rule = str_replace("].", TEXT_BRACKET_RIGHT_MODULE, $rule);
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT); // TEXT_MODULE_DOT

        /* replace brackets */
        $rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        $rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);

        //$rule = str_replace("[", TEXT_BRACKET_LEFT, $rule);
        //$rule = str_replace("]", TEXT_BRACKET_RIGHT, $rule);
        // hide module dot notations
        //$rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        $this->factory = new PHPParser_BuilderFactory();
        $this->printer = new PHPParser_PrettyPrinter_Default();
        $classextension = prepareClassExtension($text);

        $rootnode = $this->factory->class(CLASS_GETFILL . "_" . $classextension)->extend(CLASS_BASICFILL);
        $stmt = new PHPParser_Node_Stmt_Return(new PHPParser_Node_Scalar_String(""));
        try {

            $stmts = $parser->parse("<?php " . $rule . " ?>");
            $stmt = $stmts[0];
            $stmt = new PHPParser_Node_Arg($stmt); // encapsulate in fake Argument object, since updateVariables looks only at children of entered node
            $this->updateVariables($stmt);

            // fake method call for Q1[1,1] reference
            if ($stmt->value instanceof PHPParser_Node_Expr_MethodCall) {
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value);
            } else {
                //$stmt = new PHPParser_Node_Stmt_Return($stmt->value->name->args[0]->value);                
                $stmt = new PHPParser_Node_Stmt_Return($stmt->value->name);
            }
        } catch (PHPParser_Error $e) {
            $this->addErrorMessage(Language::errorFillInvalid());
            return;
        }

        /* add getFillValue function */
        $getfillvalue = $this->factory->method(FUNCTION_GET_FILL_VALUE);
        $getfillvalue->makePublic();
        $getfillvalue->addStmt($stmt);
        $rootnode->addStmt($getfillvalue);

        /* get statements */
        $stmts = array($rootnode->getNode());

        /* generate code */
        $fillclass = $this->printer->prettyPrint($stmts);

        /* return result */
        return $fillclass;
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
                            $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String("'"), $valuenode), new PHPParser_Node_Scalar_String("'")), new PHPParser_Node_Scalar_String(","));
                        } else {
                            $bracketnode = new PHPParser_Node_Expr_Concat($valuenode, new PHPParser_Node_Scalar_String(","));
                        }
                    } else {

                        // preserve quotes for associate array references
                        if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                            $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String("'"), $valuenode), new PHPParser_Node_Scalar_String("'"));
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
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String("'"), $valuenode), new PHPParser_Node_Scalar_String("'")));
                        } else {
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, $valuenode);
                        }
                    } else {

                        // preserve quotes for associate array references
                        if ($valuenode instanceof PHPParser_Node_Scalar_String) {
                            $bracketnode = new PHPParser_Node_Expr_Concat($bracketnode, new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String("'"), $valuenode), new PHPParser_Node_Scalar_String("'")), new PHPParser_Node_Scalar_String(",")));
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
                        $bracketnode = new PHPParser_Node_Expr_Concat(new PHPParser_Node_Expr_Concat(new PHPParser_Node_Scalar_String("'"), $valuenode), new PHPParser_Node_Scalar_String("'"));
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

    /* CONTEXT FUNCTIONS */

    function addContext() {

        global $db;

        $query = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;

        $result = $db->selectQuery($query);

        if ($db->getNumberOfRows($result) == 0) {

            $query = "replace into " . Config::dbSurvey() . "_context (suid, version) values (" . $this->suid . "," . $this->version . ")";

            $db->executeQuery($query);
        }
    }

    function generateContext() {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        $this->addContext();

        $this->generateVariableDescriptives();

        $this->generateSurveySettings();

        $this->generateTypes();

        $this->generateGroups();
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function loadVariableDescriptives() {
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            return unserialize(gzuncompress($row["variables"]));
        }
        return array();
    }

    function generateVariableDescriptives($vars = array(), $remove = false) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* keep track */
        $current = $this->loadVariableDescriptives();

        /* get variables */
        global $survey;
        $survey = $this->survey;
        if (sizeof($vars) == 0) {
            $vars = $this->survey->getVariableDescriptives();
        }

        if ($remove == false) {
            foreach ($vars as $var) {
                $current[strtoupper($var->getName())] = $var;
            }
        } else {
            foreach ($vars as $var) {
                unset($current[strtoupper($var->getName())]);
            }
        }

        /* check for first time */
        $this->addContext();

        /* store in db */
        global $db;
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($current), 9));

        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
        $query = "update " . Config::dbSurvey() . "_context set variables = ? where suid = ? and version = ?";
        $db->executeBoundQuery($query, $bp->get());

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function loadSections() {
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            return unserialize(gzuncompress($row["sections"]));
        }
        return array();
    }

    function generateSections($sections = array(), $remove = false) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* keep track */
        $current = $this->loadSections();

        /* get sections */
        if (sizeof($sections) == 0) {
            global $survey;
            $survey = $this->survey;
            $sections = $this->survey->getSections();
        }

        foreach ($sections as $section) {
            if ($remove == false) {
                $current[strtoupper($section->getName())] = $section;
            } else {
                unset($current[strtoupper($section->getName())]);
            }
        }

        /* check for first time */
        $this->addContext();

        /* store in db */
        global $db;
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($current), 9));
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
        $query = "update " . Config::dbSurvey() . "_context set sections = ? where suid= ? and version = ?";
        $db->executeBoundQuery($query, $bp->get());

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function loadTypes() {
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            return unserialize(gzuncompress($row["types"]));
        }
        return array();
    }

    function generateTypes($types = array(), $remove = false) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* keep track */
        $current = $this->loadTypes();

        /* get types */
        if (sizeof($types) == 0) {
            $types = $this->survey->getTypes();
        }

        foreach ($types as $type) {
            if ($remove == false) {
                $current[strtoupper($type->getName())] = $type;
            } else {
                unset($current[strtoupper($type->getName())]);
            }
        }

        /* check for first time */
        $this->addContext();

        /* store in db */
        global $db;
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($current), 9));
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
        $query = "update " . Config::dbSurvey() . "_context set types = ? where suid= ? and version = ?";
        $db->executeBoundQuery($query, $bp->get());

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function loadGroups() {
        global $db;
        $q = "select * from " . Config::dbSurvey() . "_context where suid=" . $this->suid . " and version=" . $this->version;
        $r = $db->selectQuery($q);
        if ($row = $db->getRow($r)) {
            return unserialize(gzuncompress($row["groups"]));
        }
        return array();
    }

    function generateGroups($groups = array(), $remove = false) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;

        /* keep track */
        $current = $this->loadGroups();

        /* get groups */
        if (sizeof($groups) == 0) {
            $groups = $this->survey->getGroups();
        }

        if ($remove == false) {
            foreach ($groups as $group) {
                $current[strtoupper($group->getName())] = $group;
            }
        } else {
            foreach ($groups as $group) {
                unset($current[strtoupper($group->getName())]);
            }
        }

        /* check for first time */
        $this->addContext();

        /* store in db */
        global $db;
        $bp = new BindParam();
        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($current), 9));
        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);
        $bp->add(MYSQL_BINDING_INTEGER, $this->version);
        $query = "update " . Config::dbSurvey() . "_context set groups = ? where suid= ? and version = ?";
        $db->executeBoundQuery($query, $bp->get());

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateSurveySettings() {

        return;

        global $db;

        $settings = array();

        $settingsarray = $this->survey->getSettings();

        foreach ($settingsarray as $setting) {

            $settings[strtoupper($setting->getName() . $setting->getMode() . $setting->getLanguage())] = $setting;
        }



        /* check for first time */

        $this->addContext();



        /* store in db */

        $bp = new BindParam();

        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($settings), 9));

        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);

        $bp->add(MYSQL_BINDING_INTEGER, $this->version);

        $query = "update " . Config::dbSurvey() . "_context set settings = ? where suid = ? and version = ?";

        $db->executeBoundQuery($query, $bp->get());
    }

    function generateProgressBar($seid) {

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        global $db;



        // check for any missing for loop statements prior to a nested for loop
        //$q1 = "select * from " . Config::dbSurvey() . "_screens where suid=" . $this->suid . " and seid=" . $seid . " and locate('~', outerlooptimes) != 0";

        $q1 = "select * from " . Config::dbSurvey() . "_screens where suid=" . $this->suid . " and seid=" . $seid;

        $toprocess = array();

        $res1 = $db->selectQuery($q1);

        if ($res1) {

            if ($db->getNumberOfRows($res1) > 0) {

                $previous = "";

                while ($row1 = $db->getRow($res1)) {

                    if (contains($row1["outerlooptimes"], "~")) {

                        if ($previous == "") {

                            $toprocess[] = $row1;

                            $previous = $row1["outerlooptimes"];

                        } else {

                            /* ignore anything following until we exited any nested loops */
                        }
                    } else {

                        $previous = "";
                    }
                }
            }
        }



        foreach ($toprocess as $t) {

            $outerlooptimes = explode("~", $t["outerlooptimes"]);

            $outerlooprgids = explode("~", $t["outerlooprgids"]);

            array_pop($outerlooptimes);

            array_pop($outerlooprgids);

            $outerlooptimes = array_reverse($outerlooptimes);

            $outerlooprgids = array_reverse($outerlooprgids);

            $lookbefore = $t["number"];

            $dummy = sizeof($outerlooptimes);

            for ($i = 0; $i < sizeof($outerlooptimes); $i++) {

                $o = $outerlooptimes[$i];

                $needwork = true;

                // how far can we look back? (not farther than end of any previous loops)

                $maxback = "";

                $q2 = "select * from " . Config::dbSurvey() . "_screens where suid=" . $this->suid . " and seid=" . $seid . " and number < " . $lookbefore . " and outerlooptimes=-1 order by number desc";

                $res2 = $db->selectQuery($q2);

                if ($db->getNumberOfRows($res2) > 0) {

                    $row2 = $db->getRow($res2);

                    $maxback = $row2["number"];
                } else {

                    $maxback = 0;
                }

                // any entries that are with the right loop count

                $q2 = "select * from " . Config::dbSurvey() . "_screens where suid=" . $this->suid . " and seid=" . $seid . " and number > " . $maxback . " and number < " . $lookbefore . " and looptimes=" . $o . " order by number desc";

                $res2 = $db->selectQuery($q2);

                if ($res2) {

                    if ($db->getNumberOfRows($res2) > 0) {

                        $needwork = false;

                        $row3 = $db->getRow($res2);

                        $lookbefore = $row3["number"];
                    }
                }



                if ($needwork) {

                    $loopstring = "";

                    $out = array();

                    $temp = array_reverse($outerlooptimes);

                    $looptimes = 1;

                    for ($j = 0; $j < sizeof($temp) - $i; $j++) {

                        $out[] = $temp[$j];

                        $looptimes = $looptimes * $temp[$j];
                    }

                    $loopstring = implode("~", $out);

                    $query = "replace into " . Config::dbSurvey() . "_screens (suid, seid, rgid, number, section, looptimes, outerlooptimes, outerlooprgids, dummy) values(" . prepareDatabaseString($row2["suid"]) . ", " . prepareDatabaseString($row2["seid"]) . ", '" . prepareDatabaseString($outerlooprgids[$i]) . "', '" . prepareDatabaseString($t["number"]) . "', " . prepareDatabaseString($row2["section"]) . ", " . prepareDatabaseString($looptimes) . ", '" . prepareDatabaseString($loopstring) . "', '', " . $dummy . ")";

                    $db->executeQuery($query);

                    $dummy--;
                }
            }
        }


        // delete existing
        $query = "delete from " . Config::dbSurvey() . "_progressbars where suid=" . $this->suid . " and seid=" . $seid;
        $db->executeQuery($query);

        $progressbar = new Progressbar($this->suid, $seid);

        $this->generateProgressBarSection($progressbar, $seid, $seid, 0, "", "", 0);

        $progressbar->save();



        /* store compiled in db */

        $bp = new BindParam();

        $bp->add(MYSQL_BINDING_STRING, gzcompress(serialize($progressbar), 9));

        $bp->add(MYSQL_BINDING_INTEGER, $this->suid);

        $bp->add(MYSQL_BINDING_INTEGER, $seid);

        $bp->add(MYSQL_BINDING_INTEGER, $this->version);

        $query = "update " . Config::dbSurvey() . "_engines set progressbar = ? where suid = ? and seid = ? and version = ?";

        $db->executeBoundQuery($query, $bp->get());

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function generateProgressbarSection($progressbar, $mainseid, $sectionseid, $sectionrgid, $outerlooptimes = "", $loopstring = "", $loopcount = "") {

        global $db;

        $q1 = "select * from " . Config::dbSurvey() . "_screens where suid=" . $this->suid . " and seid=" . $sectionseid . " order by number asc, rgid asc, dummy asc";

        $res1 = $db->selectQuery($q1);

        if ($res1) {

            if ($db->getNumberOfRows($res1) > 0) {

                while ($row1 = $db->getRow($res1)) {

                    // no section or loop stuff

                    if ($row1["section"] == -1 && $row1["outerlooptimes"] == -1 && $row1["dummy"] == 0) {

                        $progressbar->addEntry($sectionseid, $sectionrgid, $row1["rgid"], $loopstring, $row1["ifrgid"], $loopcount);
                    }

                    // loop
                    else if ($row1["outerlooptimes"] != -1) {
                        $actions = array(); // TODO: WILL THIS WORK WITH NESTED LOOPS???
                        $actions[] = array("ifrgid" => $row1["ifrgid"], "rgid" => $row1["rgid"], "looptimes" => $row1["looptimes"], "outerlooptimes" => $row1["outerlooptimes"], "section" => $row1["section"], "dummy" => $row1["dummy"]);



                        /* find actions until end of loop */

                        while ($row2 = $db->getRow($res1)) {

                            if ($row2["outerlooptimes"] == -1) {

                                $toadd = $row2["rgid"];
                                break;
                            }

                            $actions[] = array("ifrgid" => $row2["ifrgid"], "rgid" => $row2["rgid"], "looptimes" => $row2["looptimes"], "outerlooptimes" => $row2["outerlooptimes"], "section" => $row2["section"], "dummy" => $row2["dummy"]);
                        }

                        $this->generateProgressBarLoop($progressbar, $sectionseid, $sectionrgid, $row1["rgid"], $row1["looptimes"], $row1["looptimes"], $row1["outerlooptimes"], $actions, $loopstring);

                        // add the action right after end of loop
                        // next one is a question screen
                        if ($row2["section"] == -1 && $row2["outerlooptimes"] == -1 && $row2["dummy"] == 0) {
                            $progressbar->addEntry($sectionseid, $sectionrgid, $toadd, $loopstring, $row2["ifrgid"]);
                        }
                        // next one is in a loop itself
                        else if ($row2["outerlooptimes"] != -1) {
                            $actions = array(); // TODO: WILL THIS WORK WITH NESTED LOOPS???
                            $actions[] = array("ifrgid" => $row2["ifrgid"], "rgid" => $row2["rgid"], "looptimes" => $row2["looptimes"], "outerlooptimes" => $row2["outerlooptimes"], "section" => $row2["section"], "dummy" => $row2["dummy"]);

                            /* find actions until end of loop */
                            while ($row3 = $db->getRow($res1)) {
                                if ($row3["outerlooptimes"] == -1) {
                                    $toadd = $row3["rgid"];
                                    break;
                                }
                                $actions[] = array("ifrgid" => $row3["ifrgid"], "rgid" => $row3["rgid"], "looptimes" => $row3["looptimes"], "outerlooptimes" => $row3["outerlooptimes"], "section" => $row3["section"], "dummy" => $row3["dummy"]);
                            }

                            //$this->generateProgressBarLoop($progressbar, $sectionseid, $sectionrgid, $row2["rgid"], $row2["looptimes"], $row2["looptimes"], $row2["outerlooptimes"], $actions, $loopstring);
                        } else if ($row2["section"] > -1 && $row2["dummy"] == 0) {
                            $this->generateProgressbarSection($progressbar, $sectionseid, $row2["section"], $row2["rgid"], $outerlooptimes, $loopstring);
                        }
                    }

                    // section
                    else if ($row1["section"] > -1 && $row1["dummy"] == 0) {
                        $this->generateProgressbarSection($progressbar, $sectionseid, $row1["section"], $row1["rgid"], $outerlooptimes, $loopstring);
                    }
                }
            }
        }
    }

    function generateProgressBarLoop($progressbar, $mainseid, $mainrgid, $rgid, $loopstoadd, $looptimes, $outerlooptimes, $actions, $loopstring = "") {

        for ($m = 0; $m < $loopstoadd; $m++) {

            for ($j = 0; $j < sizeof($actions); $j++) {

                $action = $actions[$j];



                /* nested loop statement */

                //if ($action["looptimes"] > $looptimes) {

                if (strlen($action["outerlooptimes"]) > strlen($outerlooptimes)) {

                    $nestedactions = array();

                    $nestedactions[] = array("ifrgid" => $action["ifrgid"], "rgid" => $action["rgid"], "looptimes" => $action["looptimes"], "outerlooptimes" => $action["outerlooptimes"], "section" => $action["section"], "dummy" => $action["dummy"]);



                    /* find actions until end of loop */

                    for ($k = $j + 1; $k < sizeof($actions); $k++) {

                        $subaction = $actions[$k];

                        //if ($subaction["looptimes"] == $looptimes) {

                        if (strlen($subaction["outerlooptimes"]) == strlen($outerlooptimes)) {

                            break;
                        }

                        $nestedactions[] = array("ifrgid" => $subaction["ifrgid"], "rgid" => $subaction["rgid"], "looptimes" => $subaction["looptimes"], "outerlooptimes" => $subaction["outerlooptimes"], "section" => $subaction["section"], "dummy" => $subaction["dummy"]);
                    }

                    $this->generateProgressBarLoop($progressbar, $mainseid, $mainrgid, $action["rgid"], $action["looptimes"] / $loopstoadd, $action["looptimes"], $action["outerlooptimes"], $nestedactions, $loopstring . ($m + 1));

                    // jump to end one (equals the entry after the nested loop)
                    $j = $k - 1;
                    
                } else {



                    // question

                    if ($action["section"] == -1 && $action["dummy"] == 0) {
                        $progressbar->addEntry($mainseid, $mainrgid, $action["rgid"], $loopstring . ($m + 1), $action["ifrgid"], ($m * 1), $rgid);
                    }

                    // section call
                    else if ($action["section"] > -1 && $action["dummy"] == 0) {
                        $this->generateProgressbarSection($progressbar, $mainseid, $action["section"], $action["rgid"], $outerlooptimes, $loopstring . ($m + 1));
                    }
                }
            }
        }
    }

}

?>