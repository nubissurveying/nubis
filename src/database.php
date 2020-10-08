<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Database {

    var $db = null;
    private $type;

    public function __construct() {
        
        $this->type = dbConfig::getProperty(CONFIGURATION_DATABASE, CONFIGURATION_DATABASE_TYPE);
        if ($this->db == null) {

            //$this->db = @mysqli_connect(Config::dbServer(), Config::dbUser(), Config::dbPassword());  //default mysql
            switch ($this->type) {
                case DB_SQLITE:
                    //$this->db = new SQLite3('db.sqlite');
                    //$this->db->createFunction('aes_encrypt', 'aes_encrypt');
                    //$this->db->createFunction('aes_decrypt', 'aes_decrypt');
                    break;
                default:
                    $port = DATABASE_MYSQL_PORT;
                    if (Config::dbPort() != "") {
                        $port = Config::dbPort();
                    }
                    $this->db = @mysqli_connect(null, Config::dbUser(), Config::dbPassword(), null, $port);  //default mysql
                    if ($this->db != null) {
                        if (mysqli_select_db($this->db, Config::dbName())) {
                            @mysqli_query($this->db, 'SET CHARACTER SET utf8;');
                            @mysqli_query($this->db, 'SET collation_connection = \'utf8_general_ci\';');
                        } else {
                            $this->db = null;
                        }
                    }
                    break;
            }
        }
    }

    function connect($dbServer, $dbName, $username, $password) {
        switch ($this->type) {
            case DB_SQLITE:
                //$this->db = new SQLite3('db.sqlite');
                //$this->db->createFunction('aes_encrypt', 'encryptC');
                //$this->db->createFunction('aes_decrypt', 'decryptC');
                return true;
            default:
                $this->db = @mysqli_connect($dbServer, $username, $password);  //default mysql                    
                if ($this->db != null) {
                    if (mysqli_select_db($this->db, $dbName)) {
                        @mysqli_query($this->db, 'SET CHARACTER SET utf8;');
                        @mysqli_query($this->db, 'SET collation_connection = \'utf8_general_ci\';');
                        return true;
                    }
                }
        }
        return false;
    }

    function disconnect() {
        switch ($this->type) {
            case DB_SQLITE:
                $this->db = null;
                break;
            default:
                if ($this->db) {
                    @mysqli_close($this->db);
                }
                $this->db = null;
                break;
        }
    }

    function getDb() {
        return $this->db;
    }

    function selectQuery($query) {
        switch ($this->type) {
            case DB_SQLITE:
                return $this->db->query($query);
            default:
                return mysqli_query($this->db, $query);
        }
    }

    function executeQuery($query) {
        switch ($this->type) {
            case DB_SQLITE:
                $this->db->exec($query);
            default:
                return mysqli_query($this->db, $query);
        }
    }

    function executeQueries($queries) {
        $queries = explode(";", $queries);
        foreach ($queries as $query) {
            $this->executeQuery($query);
        }
    }

    function getLastInsertedId() {
        switch ($this->type) {
            case DB_SQLITE:
                return $this->db->lastInsertRowID();
            default:
                return @mysqli_insert_id($this->db);
        }
    }

    function getNumberOfRows($result) {
        switch ($this->type) {
            case DB_SQLITE:
                return 100;
            default:
                if ($result) {
                    return @mysqli_num_rows($result);
                }
        }
    }

    function getNumberOfFields($result) {
        switch ($this->type) {
            case DB_SQLITE:
                return $result->numColumns();
            default:
                if ($result) {    
                    return @mysqli_num_fields($result);
                }
        }
    }

    function getFields($result) {
        if ($result) {
            return @mysqli_fetch_fields($result);
        }
    }

    function getRow($result, $type = MYSQLI_BOTH) {
        switch ($this->type) {
            case DB_SQLITE:
                if ($result) {
                    return $result->fetchArray();
                }
            default:
                if ($result) {    
                    return @mysqli_fetch_array($result, $type);
                }
        }
    }

    function escapeString($string) {
        switch ($this->type) {
            case DB_SQLITE:
                return $this->db->escapeString(str_replace('"', '""', $string)); // sqlite does not escape "
            default:
                return mysqli_escape_string($this->db, $string);
        }
    }

    function beginTransaction() {
        switch ($this->type) {
            case DB_SQLITE:
                $this->selectQuery("BEGIN TRANSACTION");
                break;
            default:
                $this->selectQuery("BEGIN");
                break;
        }
    }

    function commitTransaction() {
        switch ($this->type) {
            case DB_SQLITE:
                $this->selectQuery("END TRANSACTION");
                break;
            default:
                $this->selectQuery("COMMIT");
        }
    }

    function executeBoundQuery($query, $parameters) {
        // http://mattbango.com/notebook/code/prepared-statements-in-php-and-mysqli/
        /* Bind parameters: s - string, b - blob, i - int, etc */
        $stmt = $this->db->prepare($query);
        if ($stmt) {

            if (call_user_func_array(array($stmt, 'bind_param'), $this->refValues($parameters))) {
                if ($stmt->execute()) {
                    $stmt->close();
                    return true;
                } else {
                    $stmt->close();
                    return false;
                }
            } else {
                $stmt->close();
            }
        }
    }

    // http://www.php.net/manual/en/mysqli-stmt.bind-param.php
    function refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value) {
                $refs[$key] = &$arr[$key];
            }
            return $refs;
        }
        return $arr;
    }

}

// http://www.php.net/manual/en/mysqli-stmt.bind-param.php
class BindParam {

    private $values = array(), $types = '';

    public function add($type, &$value) {
        $this->values[] = $value;
        $this->types .= $type;
    }

    public function get() {
        return array_merge(array($this->types), $this->values);
    }

}

?>