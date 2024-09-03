<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

require_once('instruction.php');

class PaperGenerator {

    private $level = 0;
    private $seid;
    private $nesting;
    private $statements;
    private $suid;
    private $cnt;
    private $type;
    private $groups;

    function __construct($suid, $version, $type = 0) {
        $this->suid = $suid;
        $this->version = $version;
        $this->survey = new Survey($this->suid);
        $this->statements = array();
        $this->type = $type; //0: normal, 1: dashes
    }

    function generate($seid, $nestingcounter = 1) {
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '128M');
        $this->seid = $seid;
        $q = "select * from " . Config::dbSurvey() . "_routing where suid=" . prepareDatabaseString($this->suid) . " and seid=" . prepareDatabaseString($this->seid) . " order by rgid asc";
        if ($rules = $db->selectQuery($q)) {

            if ($db->getNumberOfRows($rules) > 0) {

                /* set uscic-paperversion-nesting counter */
                $this->nesting = $nestingcounter;
                while ($row = $db->getRow($rules)) {
                    $this->instructions[$row["rgid"]] = new RoutingInstruction($row["suid"], $row["seid"], $row["rgid"], $row["rule"]);
                }

                /* process rules */
                for ($this->cnt = 1; $this->cnt <= sizeof($this->instructions); $this->cnt++) {
                    if (isset($this->instructions[$this->cnt])) {
                        $this->addRule($rootnode, $this->instructions[$this->cnt]);
                    }
                }
            }
        }

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
    }

    function getStatements() {
        return $this->statements;
    }

    function getString() {
        return implode("", $this->statements);
    }

    function addToStatements($string, $statementType = 0) {
        /*
          2: endif
          3: endloop
          4: endgroup
          1: if
          5: start group
          6: end question
          7: start loop
          10: else
          15: questiontext
         */
        $this->statementTypes[] = $statementType;
        if ($this->type == 0) {
            $this->statements[] = $string;
        } else {
            //remove else if end of if    
            if ($string == 'End of if') {
                //remove previous else if nothing in between
                //remove this and if if empty condition
            }
            $addtolist = true;
            $dashes = $this->getDashes($this->nesting);
            if ($statementType == 2) { //endif
                //check if previous is else
                if ($this->statementTypes[sizeof($this->statementTypes) - 2] == 10) { //previous is else!
                    array_pop($this->statements); //remove 2
                    array_pop($this->statements);
                    array_pop($this->statementTypes);
                }
                if ($this->statementTypes[sizeof($this->statementTypes) - 2] == 1) { //previous is else!
                    array_pop($this->statements); //remove 2
                    array_pop($this->statements);
                    array_pop($this->statementTypes);
                    $addtolist = false;
                }
            }
            if ($addtolist) {
                //http://stackoverflow.com/questions/5849130/replace-br-tag-from-a-string-in-php
                // $string = str_replace(" < ", " &lt; ", $string);
                // $string = str_replace(" > ", " &gt; ", $string);



                $string = preg_replace("/<br\W*?\/>/", "<br>", $string); //all <br/> to <br>

                $string = preg_replace("/<\/br>/", "<br>", $string); //all <br/> to <br>
                $string = preg_replace("/<br\W*?\>/", "<br/>\n" . $dashes, $string);  //all <br> to ||| <br/>
                //strip javascript  http://stackoverflow.com/questions/1886740/php-remove-javascript
                $string = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $string);
//            $string = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $string);
//            $string = preg_replace(' > ', '&lt;', $string);
//> <

                if (strip_tags($string, '<b><i><br/><br>') != '') { //don't add empty lines
                    //$string = str_replace('<1', '&lt;1', $string); //lasi dementia fix.. < 1 <1 is cut by strip_tags
                    //'<1' becomes '< 1'(note: somewhat application specific)  http://php.net/manual/es/function.strip-tags.php
                    $string = preg_replace(array('/<([0-9]+)/'), array('< $1'), $string);

//              $string = str_replace("/>s", "======", $string);

                    $string = $this->wordWrap(strip_tags($string, '<b><i><br/><br>'), $dashes, 100, $statementType);
                    $this->statements[] = $dashes . $string . '<br/>';
                }
                if ($statementType == 1 || $statementType == 2 || $statementType == 3 || $statementType == 4 || $statementType == 5 || $statementType == 6 || $statementType == 7 || $statementType == 10) { //if  //group //end question
                    if ($statementType == 1 || $statementType == 7 || $statementType == 5 || $statementType == 10) {
                        $dashes = $this->getDashes($this->nesting + 1);
                        $this->statements[] = $dashes . '<br>';
                    } else {
                        $this->statements[] = $dashes . '<br>';
                    }
                }
            }
        }
    }

    function wordWrap($string, $dashes, $characters = 100, $statementType = 0) {
        if ($statementType == 15) {
            //$string = wordwrap($string, $characters, '<br />' . $dashes . "\n");
            $string = $this->utf8_wordwrap($string, $characters, '<br />' . $dashes . "\n");
        }
        return $string;
    }

//http://stackoverflow.com/questions/3825226/multi-byte-safe-wordwrap-function-for-utf-8
    /**
     * wordwrap for utf8 encoded strings
     *
     * @param string $str
     * @param integer $len
     * @param string $what
     * @return string
     * @author Milian Wolff <mail@milianw.de>
     */
    function utf8_wordwrap($str, $width, $break, $cut = false) {
        if (!$cut) {
            $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){' . $width . ',}\b#U';
        } else {
            $regexp = '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){' . $width . '}#';
        }
        if (function_exists('mb_strlen')) {
            $str_len = mb_strlen($str, 'UTF-8');
        } else {
            $str_len = preg_match_all('/[\x00-\x7F\xC0-\xFD]/', $str, $var_empty);
        }
        $while_what = ceil($str_len / $width);
        $i = 1;
        $return = '';
        while ($i < $while_what) {
            preg_match($regexp, $str, $matches);
            $string = "";
            if (isset($matches[0])) {
                $string = $matches[0];
            }
            $return .= $string . $break;
            $str = substr($str, strlen($string));
            $i++;
        }
        return $return . $str;
    }

    function addFrontDashes($num) {
        $front = '';
        for ($i = 1; $i <= 1; $i++) {
            $front .= "<div class='uscic-paperversion-dashes'>";
        }
        return $front;
    }

    function addEndDashes($num) {
        $front = '';
        for ($i = 1; $i <= 1; $i++) {
            $front .= "</div>";
        }
        return $front;
    }

    function getDashes($num) {
        $dashes = '';
        for ($i = 1; $i < $num; $i++) {
            $dashes .= '| ';
        }
        return $dashes;
    }

    function addRule(&$node, $instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());
        if (contains($rule, "//")) {
            $rule = substr($rule, 0, strpos($rule, "//"));
            $instruction->setRule($rule);
        }

        /* empty line */
        if ($rule == "") {
            
        }
        // if condition 
        else if (startsWith($rule, ROUTING_IDENTIFY_IF)) {
            $this->addIf($node, $instruction);
            $this->nesting++;
        }

        // else if condition 
        else if (startsWith($rule, ROUTING_IDENTIFY_ELSEIF)) {
            $this->nesting--;
            $this->addIf($node, $instruction, true);
            $this->nesting++;
        }
        // else 
        else if (startsWith($rule, ROUTING_IDENTIFY_ELSE)) {
            $this->nesting--;
            $this->addElse($node, $instruction, true);
            $this->nesting++;
        }
        // for loop  
        else if (startsWith($rule, ROUTING_IDENTIFY_FOR)) {
            $this->addForLoop($node, $instruction);
            $this->nesting++;
        }
        // group  
        else if (startsWith($rule, ROUTING_IDENTIFY_GROUP)) {
            $this->addGroup($node, $instruction);
            $this->nesting++;
        }
        // sub group  
        else if (startsWith($rule, ROUTING_IDENTIFY_SUBGROUP)) {

            // ONLY if in group
            //if (sizeof($this->groups) > 0) {
            $this->addSubGroup($node, $instruction);
            //} else { // ignore the subgroup statement
            //    $this->cnt = $this->findEndSubGroup($rgid);
            //}
        }

        // move forward
        else if (startsWith($rule, ROUTING_MOVE_FORWARD)) {
            $this->addMoveForward($node, $instruction);
        }
        // move backward
        else if (startsWith($rule, ROUTING_MOVE_BACKWARD)) {
            $this->addMoveBackward($node, $instruction);
        }
        // assignment
        else if (contains($rule, ":=")) {
            $this->addAssignment($node, $instruction);
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
            $this->addToStatements($this->addEndDashes($this->nesting) . "<div class='uscic-paperversion-endif'>End of if</div></div>", 2);
            $this->nesting--;
        }
        // end do
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDDO)) {
            $this->addToStatements($this->addEndDashes($this->nesting) . "<div class='uscic-paperversion-enddo'>End of loop</div></div>", 3);
            $this->nesting--;
        }
        // end group
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDGROUP)) {
            $this->addToStatements($this->addEndDashes($this->nesting) . "<div class='uscic-paperversion-endgroup'>End of group of questions</div></div>", 4);
            $this->nesting--;
        }
        // end subgroup
        else if (startsWith($rule, ROUTING_IDENTIFY_ENDSUBGROUP)) {
            $this->addToStatements($this->addEndDashes($this->nesting) . "<div class='uscic-paperversion-endsubgroup'>End of subgroup of questions</div></div>");
        }
        // question
        else {

            /* check if this is a section */
            $mod = $rule;
            if (contains($rule, ".")) {
                $mod = substr($rule, strrpos($rule, ".") + 1);
            }

            $section = $this->survey->getSectionByName($mod);
            if ($section->getName() != "") {
                $gen = new PaperGenerator($this->suid, $this->version, $this->type);
                $gen->generate($section->getSeid(), $this->nesting);
                $this->statements = array_merge($this->statements, $gen->getStatements());
                return;
            }

            /* check if this is a question of type section */
            $var = $this->survey->getVariableDescriptiveByName(getBasicName($rule));
            if ($var->getAnswerType() == ANSWER_TYPE_SECTION) {
                $sectionid = $var->getSection();
                $section = $this->survey->getSection($sectionid);
                if ($section->getName() != "") {
                    $gen = new PaperGenerator($this->suid, $this->version, $this->type);
                    $gen->generate($section->getSeid(), $this->nesting);
                    $this->statements = array_merge($this->statements, $gen->getStatements());
                }
                /* we are done */
                return;
            }

            /* no section of question of type section, then see if it is a question */
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
            // check for EXIT
            else if (strtoupper($rule) == ROUTING_IDENTIFY_EXIT) {
                $this->addExit($node, $instruction);
            }
            // check for .FILL
            else if (endsWith($rule, ROUTING_IDENTIFY_FILL)) {
                $this->addSetFill($node, $instruction);
            } else {
                $this->addQuestion($node, $instruction);
            }
        }
    }

    function prepareText($text) {
        //$text = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $text);
        //$text = preg_replace('~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is', '', $text);
        $text = $this->replaceFills($text);
        $text = str_replace("<nobr>", "", $text);
        return $text;
        //return convertHTLMEntities($this->replaceFills($text));
    }

    function replaceFills($text) {
        $fills = getReferences($text, INDICATOR_FILL);
        if (sizeof($fills) > 0) {
            uksort($fills, "compareLength");
            foreach ($fills as $fill) {
                $fillref = str_replace("[", "\[", str_replace("]", "\]", $fill));
                
                $tt = $this->getFieldFromFill($fill);
                    if ($tt === null) {
                        $tt = "";
                    }
                $filltext = strtr($tt, array('\\' => '\\\\', '$' => '\$'));
                $pattern = "/\\" . INDICATOR_FILL . $fillref . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }
        }

        $fills = getReferences($text, INDICATOR_FILL_NOVALUE);
        if (sizeof($fills) > 0) {
            uksort($fills, "compareLength");
            foreach ($fills as $fill) {
                $fillref = str_replace("[", "\[", str_replace("]", "\]", $fill));
                $filltext = ""; //strtr($this->getFieldFromFill($fill), array('\\' => '\\\\', '$' => '\$'));
                $pattern = "/\\" . INDICATOR_FILL_NOVALUE . $fillref . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }
        }

        $fills = getReferences($text, INDICATOR_INLINEFIELD_TEXT);
        if (sizeof($fills) > 0) {
            uksort($fills, "compareLength");
            foreach ($fills as $fill) {
                $fillref = str_replace("[", "\[", str_replace("]", "\]", $fill));
                $filltext = ""; //strtr($this->getFieldFromFill($fill), array('\\' => '\\\\', '$' => '\$'));
                $pattern = "/\\" . INDICATOR_INLINEFIELD_TEXT . $fillref . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }
        }

        $fills = getReferences($text, INDICATOR_INLINEFIELD_ANSWER);
        if (sizeof($fills) > 0) {
            uksort($fills, "compareLength");
            foreach ($fills as $fill) {
                $fillref = str_replace("[", "\[", str_replace("]", "\]", $fill));
                $filltext = ""; //strtr($this->getFieldFromFill($fill), array('\\' => '\\\\', '$' => '\$'));
                $pattern = "/\\" . INDICATOR_INLINEFIELD_ANSWER . $fillref . "/i";
                $text = preg_replace($pattern, $filltext, $text);
            }
        }

        return $text;
    }

    function getFieldFromFill($fill) {
        $var = $this->survey->getVariableDescriptiveByName(getBasicName($fill));
        preg_match_all("/\[.*?\]/", $fill, $matches);
        $index = -1;
        if (sizeof($matches) > 0 && isset($matches[0])) {
            $index = "";
            if (isset($matches[0][0])) {
                trim($matches[0][0], '[]');
            }
            if ($var->getFillText() != '') {
                if (is_numeric($index)) {
                    return '[' . $this->prepareText($var->getFillTextByLine($index)) . ']';
                } else {
                    return '[' . implode('/', explode("\r\n", $var->getFillText())) . ']';
                }
            }
        }
        if ($index != -1) {
            return '[' . $var->getDescription() . '[' . $index . ']]';
        } else {
            return '[' . $var->getDescription() . ']';
        }
    }

    function addMoveForward(&$node, $instruction) {
        
    }

    function addMoveBackward(&$node, $instruction) {
        
    }

    function addElse(&$node, $instruction) {
        $this->addToStatements("</div>" . $this->addEndDashes($this->nesting));
        $this->addToStatements("<div class='uscic-paperversion-else uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-else-statement'>Else</div>" . $this->addFrontDashes($this->nesting), 10);
    }

    function addGroup(&$node, $instruction) {
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        $excluded = array();
        $rule = excludeText($rule, $excluded);

        $group = explode(".", $rule);
        $group = $this->survey->getGroupByName($group[1]);

        $this->addToStatements("<div class='uscic-paperversion-group uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-group-statement'>Group of questions presented on the same screen</div>" . $this->addFrontDashes($this->nesting), 5);
    }

    function addSubGroup(&$node, $instruction) {
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        $excluded = array();
        $rule = excludeText($rule, $excluded);

        $group = explode(".", $rule);
        $group = $this->survey->getGroupByName($group[1]);

        $this->addToStatements("<div class='uscic-paperversion-subgroup uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-subgroup-statement'>Subgroup of questions</div>" . $this->addFrontDashes($this->nesting));
    }

    function addQuestion(&$node, $instruction) {
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // check for .INLINE
        $inline = false;
        if (endsWith($rule, ROUTING_IDENTIFY_INLINE)) {
            $inline = true;
            $pos = strrpos($rule, ROUTING_IDENTIFY_INLINE);
            $rule = substr($rule, 0, $pos);
        }

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);
        $var = $this->survey->getVariableDescriptiveByName(getBasicName($rule));
        if ($var->getVsid() != "") {
            if ($var->isHiddenRouting() == false) {


                if (trim($var->getDescription()) != "") {
                    $this->addToStatements("<div class='uscic-paperversion-question uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-question-name'><b>" . $var->getName() . "</b> (" . $this->prepareText($var->getDescription()) . ")");
                } else {
                    $this->addToStatements("<div class='uscic-paperversion-question uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-question-name'>" . $var->getName() . "");
                }

                $this->addToStatements('</div>');

                if (trim($var->getQuestion()) != "") {
                    $this->addToStatements("<div class='uscic-paperversion-questiontext'>" . $this->prepareText($var->getQuestion()) . "</div>", 15);
                }

                $answertype = $var->getAnswerType();
                if ($answertype == SETTING_FOLLOW_TYPE) {
                    $type = $this->survey->getType($var->getTyd());
                    $answertype = $type->getAnswerType();
                }
                if (inArray($answertype, array(ANSWER_TYPE_ENUMERATED, ANSWER_TYPE_SETOFENUMERATED, ANSWER_TYPE_DROPDOWN, ANSWER_TYPE_MULTIDROPDOWN, ANSWER_TYPE_RANK))) {
                    $this->addToStatements("<div class='uscic-paperversion-question-categories'>" . $this->prepareText(str_replace("\r\n", "<br/>", $var->getOptionsText())) . "</div>");
                } elseif ($answertype == ANSWER_TYPE_STRING || $answertype == ANSWER_TYPE_OPEN) {
                    $this->addToStatements('STRING');
                } elseif ($answertype == ANSWER_TYPE_RANGE) {
                    $this->addToStatements('RANGE ' . $var->getMinimum() . '..' . $var->getMaximum());
                } elseif ($answertype == ANSWER_TYPE_KNOB) {
                    $this->addToStatements('KNOB ' . $var->getMinimum() . '..' . $var->getMaximum());
                }
                $this->addToStatements("</div>", 6);
            }
        }
    }

    function addInspect(&$node, $instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        $excluded = array();
        $rule = excludeText($rule, $excluded);

        if (endsWith($rule, ROUTING_IDENTIFY_INSPECT)) {
            $pos = strrpos($rule, ROUTING_IDENTIFY_INSPECT);
            $rule = substr($rule, 0, $pos);
        }

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);

        $this->addToStatements("<div class='uscic-paperversion-inspect uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-inspect-condition'>Value of question '" . $rule . "' asked as question</div></div>", 1);
    }

    function addSetFill(&$node, $instruction) {
        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        $excluded = array();
        $rule = excludeText($rule, $excluded);

        if (endsWith($rule, ROUTING_IDENTIFY_FILL)) {
            $pos = strrpos($rule, ROUTING_IDENTIFY_FILL);
            $rule = substr($rule, 0, $pos);
        }

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);

        $this->addToStatements("<div class='uscic-paperversion-fill uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-fill-condition'>Fill code of question '" . $rule . "' executed</div></div>", 1);
    }

    function addInspectSection(&$node, $instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        $excluded = array();
        $rule = excludeText($rule, $excluded);

        if (endsWith($rule, ROUTING_IDENTIFY_INSPECT_SECTION)) {
            $pos = strrpos($rule, ROUTING_IDENTIFY_INSPECT_SECTION);
            $rule = substr($rule, 0, $pos);
        }

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);
        $rule = includeText($rule, $excluded);

        if ($this->type == 2 || $this->type == 3) {
            $this->addToStatements("Value of question " . $this->prepareLatexString($rule) . " asked as section&#92;&#92;", 1);
            //$this->addToStatements("");
        } else {
            $this->addToStatements("<div class='uscic-paperversion-inspect uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-inspect-condition'>Value of question " . $rule . " asked as section</div></div>", 1);
        }
    }

    function addExit(&$node, $instruction) {
        $this->addToStatements("<div class='uscic-paperversion-exit uscic-paperversion-nesting" . $this->nesting . "'>Exit the survey</div>");
    }

    function addIf(&$node, $instruction, $else = false) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        /* get entire instruction */
        $pos = strrpos($rule, ROUTING_THEN);

        /* multi-line if */
        if ($pos < 0) {
            for ($cnt = ($this->cnt + 1); $cnt <= sizeof($this->instructions); $cnt++) {

                if (isset($this->instructions[$cnt])) {

                    $text = trim($this->instructions[$cnt]->getRule());



                    if (startsWith($text, "/*")) {

                        $this->skipComments($cnt, $cnt);
                    } else if (startsWith($text, "//")) {
                        
                    } else {



                        $pos = strrpos($text, ROUTING_THEN);

                        $rule .= " " . $text;

                        if ($pos > -1) {

                            $this->cnt = $cnt;

                            break;
                        }
                    }
                }
            }
        }

        if ($else == true) {
            $this->addToStatements("</div>" . $this->addEndDashes($this->nesting));
            $this->addToStatements("<div class='uscic-paperversion-if uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-if-condition'>" . $rule . "</div>" . $this->addFrontDashes($this->nesting), 1);
        } else {
            $this->addToStatements("<div class='uscic-paperversion-if uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-if-condition'>" . $rule . "</div>" . $this->addFrontDashes($this->nesting), 1);
        }
    }

    function addForLoop(&$node, $instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        // hide text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);


        // strip FOR
        $rule = substr($rule, stripos($rule, ROUTING_IDENTIFY_FOR) + strlen(ROUTING_IDENTIFY_FOR));

        // strip do
        $pos = strrpos(strtoupper($rule), ROUTING_IDENTIFY_DO);

        /* multi-line for */
        if ($pos < 0) {
            for ($cnt = ($this->cnt + 1); $cnt <= sizeof($this->instructions); $cnt++) {
                if (isset($this->instructions[$cnt])) {
                    $text = trim($this->instructions[$cnt]->getRule());
                    if (startsWith($text, "/*")) {
                        $this->skipComments($cnt, $cnt);
                    } else if (startsWith($text, "//")) {
                        
                    } else {
                        $pos = strrpos($text, ROUTING_IDENTIFY_DO);
                        $rule .= " " . $text;
                        if ($pos > -1) {
                            $this->cnt = $cnt;
                            break;
                        }
                    }
                }
            }
        }

        // strip do
        $rule = trim(substr($rule, 0, $pos));

        // determine min and max
        $bounds = splitString("/ TO /", strtoupper($rule));
        $counterplusstart = splitString("/:=/", $bounds[0]);

        $counterfield = includeText($counterplusstart[0], $excluded);
        $minimum = includeText($counterplusstart[1], $excluded);
        $maximum = includeText($bounds[1], $excluded);

        $this->addToStatements("<div class='uscic-paperversion-for uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-for-loop'>Loop from " . $minimum . " to " . $maximum . "</div>" . $this->addFrontDashes($this->nesting), 7);
    }

    function addAssignment(&$node, $instruction) {

        $rule = trim($instruction->getRule());
        $rgid = trim($instruction->getRgid());

        // hide text
        $excluded = array();
        $rule = excludeText($rule, $excluded);

        // hide module dot notations
        $rule = hideModuleNotations($rule, TEXT_MODULE_DOT);

        // split left and right hand
        $split = splitString("/:=/", $rule);
        $lefthand = includeText($split[0], $excluded);
        $righthand = includeText($split[1], $excluded);
        $this->addToStatements("<div class='uscic-paperversion-for uscic-paperversion-nesting" . $this->nesting . "'><div class='uscic-paperversion-assignment'>" . includeText($rule, $excluded) . "</div></div>", 7);
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

}

?>