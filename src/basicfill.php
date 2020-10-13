<?php

/* 
------------------------------------------------------------------------
Copyright (C) 2014 Bart Orriens, Albert Weerman

This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
------------------------------------------------------------------------
*/

class BasicFill {
    
    private $engine;
    
    function __construct($engine) {
        $this->engine = $engine;
    }

    function checkAnswer($variable, $answer) {
        return $this->engine->checkAnswer($variable, $answer);
    }
   
    function getAnswer($variable) {
        return $this->engine->getAnswer($variable);
    }

    function setAnswer($variable, $value) {
        return $this->engine->setAnswer($variable, $value);
    }

    function addAssignment($variablename, $oldvalue, $rgid) {
        $this->engine->addAssignment($variablename, $oldvalue, $rgid);
    }

    
    function addForLoopLastAction($rgid, $position) {
        //$this->engine->addForLoopLastAction($rgid, $position);
        // NOTE: THIS IS NOT NEEDED IN IN-MEMORY FOR LOOP AS WE KNOW EXACTLY WHERE WE ARE;
        // MOREOVER, DOING THIS INSIDE A NORMAL LOOP WOULD CAUSE THAT LOOP TO MALFUNCTION
    }

    
    function getFillTextByLine($variable, $line) {
        $var = $this->engine->getVariableDescriptive($variable);        
        $text = $this->engine->getFill($variable, $var, SETTING_FILLTEXT); //$var->getFillTextByLine($line);                
        $fillines = explode("\r\n", $text);
        if (isset($fillines[$line - 1])) {
            return $fillines[$line - 1];
        }

        return "";
    }
    
    function doForLoop($min, $max, $counterfield, $loopactions, $looprgid, $nextrgid, $normalfor = 1) {
        $current = $this->getAnswer($counterfield);
        $loopactions = explode("~", $loopactions);
        
        // normal loop (1 to 5)
        if ($normalfor == 1) {
            for ($tempcount = $min; $tempcount <= $max; $tempcount++) {
                $this->addAssignment($counterfield, $tempcount, $looprgid);
                $this->setAnswer($counterfield, $tempcount);
                foreach ($loopactions as $ga) {
                    $action = $this->doAction($ga);    
                    if ($action == ROUTING_IDENTIFY_EXITFOR) {
                        break;
                    }
                }
            }
        }
        // reverse loop (5 to 1)
        else {
            for ($tempcount = $min; $tempcount >= $max; $tempcount--) {
                $this->addAssignment($counterfield, $tempcount, $looprgid);
                $this->setAnswer($counterfield, $tempcount);
                foreach ($loopactions as $ga) {
                    $action = $this->doAction($ga);    
                    if ($action == ROUTING_IDENTIFY_EXITFOR) {
                        break;
                    }
                }
            }
        }
        $this->doAction($nextrgid);
    }
    
    function doWhileLoop($groupactions) {
        $groupactions = explode("~", $groupactions);
        foreach ($groupactions as $ga) {
            $action = $this->doAction($ga);            
        }
    }
}



?>