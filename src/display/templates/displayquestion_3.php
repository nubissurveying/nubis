<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayQuestion extends DisplayQuestionBasic {

    function redirect($page) {
        global $survey;
        $returnStr = $this->showHeader($survey->getTitle(), '<link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">');
        $returnStr .= '<form method="post" action="../index.php">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_PRIMKEY . '" value="' . addslashes(encryptC($this->primkey, Config::directLoginKey())) . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_SUID . '" value="' . getSurvey() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_LANGUAGE . '" value="' . getSurveyLanguage() . '">';
        $returnStr .= '<input type=hidden name="' . POST_PARAM_MODE . '" value="' . getSurveyMode() . '">';
        $returnStr .= setSessionParamsPost(array('page' => $page));
        $returnStr .= '</form>';
        $returnStr .= '<script>';
        $returnStr .= '$(document).ready(function(){ $("form:first").submit(); }); ';
        $returnStr .= '</script></body><html>';
        return $returnStr;
    }

    function showEndSurvey() {
        echo $this->redirect("survey.return.end");
        doExit();
    }

    function showCompletedSurvey() {
        echo $this->redirect("survey.return.alreadycompleted");
        doExit();
    }

}

?>