<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class SMS {

    var $urid;
    var $user;
    var $phpid;

    function __construct($urid, $phpid) {
        $this->urid = $urid;
        $this->phpid = $phpid;
        $this->user = new User($urid);
    }

    function getPage() {
        //DISPLAY PAGE DEPENDING ON ADMINTYPE
        if ($this->user->isActive()) {
            switch ($this->user->getUserType()) {
                case USER_INTERVIEWER: return $this->getInterviewerMain();
                    break;
                case USER_SUPERVISOR: return $this->getSupervisorMain();
                    break;
                case USER_TRANSLATOR: return $this->getTranslatorMain();
                    break;
                case USER_RESEARCHER: return $this->getResearcherMain();
                    break;
                case USER_SYSADMIN: return $this->getSysAdminMain();
                    break;
                case USER_NURSE: return $this->getNurseMain();
                    break;
                case USER_TESTER: return $this->getTesterMain();
                    break;
            }
        } else {
            return $this->user->getName() . ' has no access to the SMS.';
        }
        return "SMS not accessible";
    }

    function getSysAdminMain() {

        // check for pushed in sysadmin key
        if (Config::smsSysadminKey() != "") {
            
            if (loadvar("sk") == Config::smsSysadminKey()) {
                $_SESSION['SYSTEM_KEY'] = Config::smsSysadminKey();
            }

            if (isset($_SESSION['SYSTEM_KEY']) && $_SESSION['SYSTEM_KEY'] == Config::smsSysadminKey()) {
                // we have the correct key
            } else {
                $displayLogin = new DisplayLoginSMS();
                return $displayLogin->showSysadminKey();
            }
        }
        
        $sysAdmin = new SysAdmin();
        return $sysAdmin->getPage();
    }

    function getResearcherMain() {
        $researcher = new Researcher($this->user);
        return $researcher->getPage();
    }

    function getTranslatorMain() {
        $translator = new Translator();
        return $translator->getPage();
    }

    function getInterviewerMain() {
        $interviewer = new Interviewer($this->user);
        return $interviewer->getPage();
    }

    function getNurseMain() {
        $nurse = new Nurse($this->user);
        return $nurse->getPage();
    }

    function getSupervisorMain() {
        $supervisor = new Supervisor($this->user);
        return $supervisor->getPage();
    }

    function getTesterMain() {
        $tester = new Tester($this->user);
        return $tester->getPage();
    }

}

?>