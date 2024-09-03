<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Login {

    var $phpid;

    function __construct($phpid) {
        $this->phpid = $phpid;
    }

    function getSMSLoginScreen($message = '') {
        $displaySMS = new DisplayLoginSMS();
        return $displaySMS->showLogin($message);
    }

    function getClosedScreen() {
        $displayLogin = new DisplayLogin();
        return $displayLogin->showSurveyClosed();
    }

    function getLoginScreen($message = '') {
        $returnStr = '';
        global $survey;
        if (isTestMode()) {
            $displayLogin = new DisplayLogin();
            $returnStr .= $displayLogin->showLoginDirect(loadvarSurvey('primkey'), $message);
        } else {
            if ($survey->getAccessType() == LOGIN_ANONYMOUS) {
                $displayLogin = new DisplayLogin();
                //TODO MAKE SURE generateRandomPrimkey doesn't exist yet!!                  
                $returnStr .= $displayLogin->showLoginAnonymous(generateRandomPrimkey(8));
            } elseif ($survey->getAccessType() == LOGIN_DIRECT) {
                $displayLogin = new DisplayLogin();
                $returnStr .= $displayLogin->showLoginDirect(loadvarSurvey('primkey'), $message);
            } elseif ($survey->getAccessType() == LOGIN_LOGINCODE) {
                $displayLogin = new DisplayLogin();
                $returnStr .= $displayLogin->showLoginCode($message);
            }
        }
        return $returnStr;
    }

    function checkSMSAccess() {
        global $db;
        $username = loadvar('username');
        $password = loadvar('password');
        $result = $db->selectQuery('select urid from ' . Config::dbSurvey() . '_users where username=\'' . prepareDatabaseString($username) . '\' and status=1 and aes_decrypt(password, \'' . Config::smsPasswordKey() . '\') = \'' . prepareDatabaseString($password) . '\'');
        //$row = $db->getRow($result);
        if ($db->getNumberOfRows($result) > 0) {
            $row = $db->getRow($result);
            $_SESSION['URID'] = $row['urid'];            
            return true;
        }

        $logactions = new LogActions();
        $logactions->addAction('', '', "loginwrong", USCIC_SMS, '1', false);
        return false;
    }

    function checkAccess() {
        global $db, $survey;
        switch ($survey->getAccessType()) {
            case LOGIN_ANONYMOUS:
                return true;
                break;

            case LOGIN_DIRECT:
                return true;
                break;

            case LOGIN_LOGINCODE:
                $logincode = loadvarSurvey('primkey');
                $result = $db->selectQuery('select primkey from ' . Config::dbSurvey() . '_respondents where aes_decrypt(logincode, \'' . Config::loginCodeKey() . '\') = \'' . prepareDatabaseString($logincode) . '\'');
                if ($db->getNumberOfRows($result) > 0) {
                    $row = $db->getRow($result);
                    $_SESSION['PRIMKEY'] = $row['primkey'];
                    return true;
                }

                $logactions = new LogActions();
                $logactions->addAction('', '', "loginwrong", USCIC_SURVEY);
                break;
        }
        return false;
    }

}

?>