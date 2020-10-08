<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Tester {

    var $user;

    function __construct($user) {
        $this->user = $user;
    }

    function getPage() {
        global $logActions;
        if (getFromSessionParams('page') != null) {
            $_SESSION['LASTPAGE'] = getFromSessionParams('page');
            $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('page'));
        }
        if (isset($_SESSION['LASTPAGE'])) {
            switch ($_SESSION['LASTPAGE']) {
                case 'tester.tools.test': return $this->showTest();
                    break;
                case 'tester.tools.reported': return $this->showReported();
                    break;
                default: return $this->mainPage();
            }
        } else {
            $logActions->addAction(getFromSessionParams('primkey'), $this->user->getUrid(), getFromSessionParams('tester.home'));
            return $this->mainPage();
        }
    }

    function mainPage() {
        $displayTester = new DisplayTester();
        return $displayTester->showMain();
    }

    function showTest() {
        $displayTester = new DisplayTester();
        return $displayTester->showTest();
    }

    function showReported() {
        $displayTester = new DisplayTester();
        return $displayTester->showReported();
    }

}

?>