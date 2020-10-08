<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */
require_once("common.php");

class Config {

    static private $table;

    
    /* DATABASE */
    
    static function dbSurvey() {
        if (self::$table != "") {
            return self::$table;
        }
        return dbConfig::dbSurvey();
    }

    static function dbSurveySet($table) {
        self::$table = $table;
    }

    static function dbName() {
        return dbConfig::dbName();
    }

    static function dbUser() {
        return dbConfig::dbUser();
    }

    static function dbPassword() {
        return dbConfig::dbPassword();
    }

    static function dbServer() {
        return dbConfig::dbServer();
    }
    
    static function dbPort() {
        return dbConfig::dbPort();
    }

    static function dbType() {
        return dbConfig::dbType();
    }

    static function dbSurveyData() {
        if (isset($_SESSION[SURVEY_EXECUTION_MODE])) {
            switch ($_SESSION[SURVEY_EXECUTION_MODE]) {
                case SURVEY_EXECUTION_MODE_NORMAL:
                    return Config::dbSurvey();
                case SURVEY_EXECUTION_MODE_TEST:
                    return Config::dbSurvey() . "_test";
                default:
                    return Config::dbSurvey();
            }
        }

        // not set, then assume normal mode
        return Config::dbSurvey();
    }

    static function defaultStartup() {
        return dbConfig::defaultStartup(); //default survey mode!
    }

    /* SAMPLE */
    
    static function allowCommunication() {
        return (DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_ALLOW_COMMUNICATION)) == 1; // 1=YES    
    }
    
    
    /* ENCRYPTION */
    
    static function loginCodeKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_LOGINCODES);
    }

    static function smsPasswordKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_ADMIN);
    }

    static function smsPersonalInfoKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_PERSONAL);
    }

    static function smsRemarkKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_REMARK);
    }
    
    static function smsCommunicationKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_COMMUNICATION);
    }

    static function smsContactRemarkKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_CONTACTREMARK);
    }

    static function smsContactNameKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_CONTACTNAME);
    }

    static function logActionParamsKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_ACTION_PARAMS);
    }

    static function dataEncryptionKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_DATA);
    }

    static function directLoginKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_DIRECT);
    }

    static function labKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_LAB);
    }

    static function filePictureKey() {
        return dbConfig::getProperty(CONFIGURATION_ENCRYPTION, CONFIGURATION_ENCRYPTION_FILE);
    }

    
    /* DATE/TIME */
    static function timezone() {        
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_TIMEZONE);
    }

    static function usFormatSMS() {
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_USFORMAT_SMS);        
    }

    static function usFormatSurvey() {
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_USFORMAT_SURVEY);        
    }

    static function secondsSMS() {
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_SECONDS_SMS);
    }

    static function secondsSurvey() {
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_SECONDS_SURVEY);
    }

    static function minutesSMS() {
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_MINUTES_SMS);
    }

    static function minutesSurvey() {
        return dbConfig::getProperty(CONFIGURATION_DATETIME, CONFIGURATION_DATETIME_MINUTES_SURVEY);
    }

    /* SESSION HANDLING */

    static function warnTimeout() {
        return dbConfig::getProperty(CONFIGURATION_SESSION, CONFIGURATION_SESSION_WARN);
    }

    static function sessionTimeout() {
        return dbConfig::getProperty(CONFIGURATION_SESSION, CONFIGURATION_SESSION_TIMEOUT);
    }

    static function sessionLogoutURL() {
        return dbConfig::getProperty(CONFIGURATION_SESSION, CONFIGURATION_SESSION_LOGOUT);
    }

    static function sessionRedirectURL() {
        return dbConfig::getProperty(CONFIGURATION_SESSION, CONFIGURATION_SESSION_REDIRECT);
    }
    
    static function sessionExpiredPingInterval() {
        return dbConfig::getProperty(CONFIGURATION_SESSION, CONFIGURATION_SESSION_PING);
    }
    
    static function sessionAliveURL() {
        return 'ajax/index.php?p=keepalive';
    }

    static function sessionExpiredWarnPoint() {
        return 6 / 10; // warn after 60% of time has passed
    }
    
    
    /* VALIDATION */
    
    static function getMinimumPrimaryKeyLength() {
        return 0;
    }
    
    static function getMaximumPrimaryKeyLength() {
        return 20;
    }
    
    static function getTimingCutoff() {
        return 301;
    }
    
    /*  LOGGING */
    static function logParams() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_PARAMS)) == 1; // 1=YES    
    }

    static function logSurveyActions() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_ACTIONS)) == 1; // 1=YES    
    }

    static function logSurveyTimings() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_TIMINGS)) == 1; // 1=YES    
    }
    
    // log tab switch or not
    static function logTabSwitch() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_TABSWITCH)) == 1; // 1=YES    
    }

    // log paradata
    static function logParadata() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_PARADATA)) == 1; // 1=YES    
    }
    
    static function logParadataMouseMovement() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_MOUSE)) > 0; // 1=YES    
    }

    static function logParadataMouseMovementInterval() {
        return (DbConfig::getProperty(CONFIGURATION_LOGGING, CONFIGURATION_LOGGING_MOUSE)); // 1=YES    
    }        
    
    static function graphStartDate() {
        return strtotime("2014-10-13");
    }

    /* INTERACTION */
    static function allowRadioButtonUnselect() {
        return true;
    }

    static function individualDKRFNA() {
        return INDIVIDUAL_DKRFNA_YES;
    }

    static function individualDKRFNASingle() {
        return INDIVIDUAL_DKRFNA_NO;
    }

    static function individualDKRFNAInline() {
        return INDIVIDUAL_DKRFNA_NO;
    }
    
    /* MEMORY LIMITS */
    static function dataExportMemoryLimit() {
        return '512M';
    }
    
    static function compilerMemoryLimit() {
        return '512M';
    }
    
    /* PERFORMANCE */
    
    static function prepareDataQueries() { // determines if data and log entries are inserted using prepared statements
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_PREPARE_QUERIES)) == 1; // 1=YES    
    }

    static function useUnserialize() {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_UNSERIALIZE)) == 1; // 1=YES    
    }
    
    static function retrieveDataFromState() {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_DATA_FROM_STATE)) == 1; // 1=YES    
    }
    
    static function useDataRecords() {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_USE_DATARECORDS)) == 1; // 1=YES    
    }
        
    static function useLocking() {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_USE_LOCKING)) == 1; // 1=YES    
    }
    
    static function useDynamicMinify() {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_USE_DYNAMIC_MINIFY)) == 1; // 1=YES    
    }
    
    static function useTransactions() {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_USE_TRANSACTIONS)) == 1; // 1=YES    
    }
    
    function useAccessible () {
        return (DbConfig::getProperty(CONFIGURATION_PERFORMANCE, CONFIGURATION_PERFORMANCE_USE_ACCESSIBLE)) == 1; // 1=YES    
    }
    
    static function checkComponents() {
        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
            return true;
        }
        return false;
    }
    
    /* NUBIS SMS integration */

    static function smsUsage() {
        if ($_SESSION['SYSTEM_ENTRY'] == USCIC_SMS) {
            return true;
        }
        return false; // change to false to not include PHP files related to SMS survey extensions when launching survey
    }
    
    /* OTHER */
    static function xiExtension() {
        return true;
    }
    
    /* PREFIXING BEHAVIOR */
    function prefixing() {
        return PREFIXING_BRACKET_ONLY;
    }
    
    /* FILL BEHAVIOR */
    function filling() {
        return FILL_SPACE_INSERT_BEFORE;
    }
    
}

?>