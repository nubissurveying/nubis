<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Flooder {

    function __construct() {
        
    }

    function generateCases() {
        $suid = loadvar("suid");
        if ($suid == "") {
            $suid = 1;
        }

        $seid = loadvar("seid");
        if ($seid == "") {
            $seid = 1;
        }
        $version = 1;
        setSurvey($suid);
        setSurveyLanguage(loadvar(POST_PARAM_LANGUAGE), true);
        setSurveyMode(loadvar(POST_PARAM_MODE), true);
        setSurveyVersion($version);

        /* generate cases */
        $number = loadvar("number");        
        for ($i = 0; $i < $number; $i++) {
            //set_time_limit(0);
            $primkey = generateRandomPrimkey();
            $this->generateCase($suid, $primkey, session_id(), $version, $seid); 
            //exit;
        }
    }

    function generateCase($suid, $primkey, $sesid, $version, $seid) {
        $_SESSION['SYSTEM_ENTRY'] = USCIC_SURVEY;
        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_SURVEY_RETRIEVAL;
        global $engine;
        $engine = loadEngine($suid, $primkey, $sesid, $version, $seid);
        $engine->setFlooding(true);
        $engine->getNextQuestion();
        //echo'done';
        // clean up
        setSessionParameter(SESSION_PARAM_RGID, null);
        setSessionParameter(SESSION_PARAM_GROUP, null);
        $_POST = array();

        $_SESSION['PARAMETER_RETRIEVAL'] = PARAMETER_ADMIN_RETRIEVAL;
        $_SESSION['SYSTEM_ENTRY'] = USCIC_SMS;
        return;
    }
}

?>