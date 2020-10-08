<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class dbConfig {
    
    static private $setup;
    static $loaded;
    
    static function load($l = "conf.php") {
        DbConfig::$loaded = 1;
        try {
            if (file_exists($l)) {
                require_once($l);
                DbConfig::$loaded = 2;
                if (isset($configuration) && is_array($configuration)) {
                    DbConfig::$setup = $configuration;
                    DbConfig::$loaded = 3;
                }                
            }
        }
        catch (Exception $e) {

        }
        return DbConfig::$loaded;
    }
    
    static function getProperty($type, $name) {
        if (!isset(DbConfig::$setup[$type])) {
            return '';
        }
        $sub = DbConfig::$setup[$type];
        if (!isset($sub[$name])) {
            return '';
        }
        return $sub[$name];
    }

    static function dbName() {        
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_NAME);
    }

    static function dbSurvey() {
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_SURVEY);
    }

    static function dbUser() {
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_USER);
    }

    static function dbPassword() {
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_PASSWORD);
    }

    static function dbServer() {
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_SERVER);
    }
    
    static function dbPort() {
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_PORT);
    }

    static function dbType() {
        return DbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_TYPE);
    }

    static function defaultStartup() {
        return DbConfig::getProperty(CONFIGURATION_GENERAL, CONFIGURATION_GENERAL_STARTUP);
    }

    static function defaultDevice() {
        return DbConfig::getProperty(CONFIGURATION_GENERAL, CONFIGURATION_GENERAL_DEVICE);
    }
        
    static function defaultPanel() {
        return DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_PANEL);
    }
    
    static function defaultTracking() {
        return DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_TRACKING);
    }

    static function defaultCommunicationServer() {
        return DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_COMMUNICATION);
    }
    
    static function defaultFileLocation() {
        return DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_FILELOCATION);
    }

    static function defaultSeparateInterviewAddress() {
        return (DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_INTERVIEWADDRESS)) == 1; // 1=YES
    }

    static function defaultProxyCode() {
        return (DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_PROXYCODE)) == 1; // 1=YES
    }

    static function defaultAllowProxyContact() {
        return (DbConfig::getProperty(CONFIGURATION_SAMPLE, CONFIGURATION_SAMPLE_PROXYCONTACT)) == 1; // 1=YES
    }
}

?>