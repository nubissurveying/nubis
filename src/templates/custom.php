<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */ 

class CustomTemplate extends TableTemplate {

    
    function __construct($engine, $group) {

        parent::__construct($engine, $group);
    }

    function show($variables, $realvariables, $language) {

        $this->variables = $variables;

        $this->realvariables = $realvariables;

        $this->language = $language;

        $returnStr = $this->custom();

        return $returnStr;
    }

    function custom() {
        
        /* add error checks */
        $this->addErrorChecks($this->realvariables);
        $template = $this->group->getCustomTemplate();
        $template = $this->engine->replaceFills($template);
        
        $pt = $this->group->getParentGroup()->getTemplate();
        if ($pt != $this->group->getTemplate()) {
            $returnStr = '<div id="TGroup_' . implode("_",$this->realvariables) . '">';
        }
        
        /* insert any references based on template ordering placeholders */
        for ($i = 1; $i <= sizeof($this->realvariables); $i++) {
            $variable = $this->realvariables[$i-1];
            $template = str_ireplace(INDICATOR_CUSTOMTEMPLATE . INDICATOR_CUSTOMTEMPLATEQUESTION . $i . INDICATOR_CUSTOMTEMPLATE, INDICATOR_INLINEFIELD_TEXT . $variable, $template);
            $template = str_ireplace(INDICATOR_CUSTOMTEMPLATE . INDICATOR_CUSTOMTEMPLATEANSWER . $i . INDICATOR_CUSTOMTEMPLATE, INDICATOR_INLINEFIELD_ANSWER . $variable, $template);
        }
        
        /* build custom display */
        $returnStr = $this->engine->replaceInlineFields($this->engine->replaceFills($template));        
        if ($pt != $this->group->getTemplate()) {
            $returnStr .= '</div>';
        }
        
        return $returnStr;
    }
}

?>