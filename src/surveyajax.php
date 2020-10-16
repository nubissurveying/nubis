<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class SurveyAjax {

    private $params;
    private $survey;

    function __construct($params) {
        $this->params = $params;
        $this->survey = new Survey($this->getParam(POST_PARAM_SUID));
    }

    function getParam($param) {
        if (isset($this->params[$param])) {
            return $this->params[$param];
        }
        return '';
    }

    function getPage($p) {
        switch ($p) {
            case 'storeremark':
                $this->storeRemark();
                exit;
            case 'removeremark':
                $this->removeRemark();
                exit;
            case 'keepalive':
                $this->keepAlive();
                exit;
            case 'storeparadata':
                $this->storeParadata();
                exit;
            case 'capturescreenshot':
                $this->capturescreenshot();
        }
    }

    function keepAlive() {
        session_set_cookie_params(0, getSessionPath()); // set path
        session_start();
        session_write_close();
        exit;
    }

    function storeRemark() {
        global $db;
        $i = array('/>[^S ]+/s', '/[^S ]+</s', '/(s)+/s');
        $ii = array('>', '<', '1');
        //exit;

        $result = urldecode(loadvar(POST_PARAM_REMARK));
        $stateid = $this->getParam(POST_PARAM_STATEID);
        $displayed = $this->getParam(POST_PARAM_DISPLAYED);
        if ($stateid == "") {
            $stateid = 1;
        }

        $primkey = $this->getParam(POST_PARAM_PRIMKEY);
        $suid = $this->getParam(POST_PARAM_SUID);
        $l = $this->getParam(POST_PARAM_LANGUAGE);
        $m = $this->getParam(POST_PARAM_MODE);
        $v = $this->getParam(POST_PARAM_VERSION);
        $dirty = DATA_DIRTY;
        $key = $this->survey->getDataEncryptionKeyDirectly($m, $l, $this->getParam(POST_PARAM_DEFAULT_MODE), $this->getParam(POST_PARAM_DEFAULT_LANGUAGE));

        if (Config::prepareDataQueries() == false) {
            $query = "replace into " . Config::dbSurveyData() . "_observations(suid, primkey, stateid, displayed, remark, dirty, mode, language, version) values (";
            $query .= prepareDatabaseString($suid) . ", ";
            $query .= "'" . prepareDatabaseString($primkey) . "', ";
            $query .= "" . prepareDatabaseString($stateid) . ", ";
            $query .= "'" . prepareDatabaseString($displayed) . "', ";

            if ($key == "") {
                $query .= "'" . prepareDatabaseString($result) . "', ";
            } else {
                $query .= "aes_encrypt('" . prepareDatabaseString($result) . "', '" . prepareDatabaseString($key) . "'), ";
            }

            $query .= "" . prepareDatabaseString($dirty) . ", ";
            $query .= "" . prepareDatabaseString($m) . ", ";
            $query .= "" . prepareDatabaseString($l) . ", ";
            $query .= "" . prepareDatabaseString($v) . ")";
            $db->executeQuery($query);
        } else {
            $bp = new BindParam();
            $bp->add(MYSQL_BINDING_INTEGER, $suid);
            $bp->add(MYSQL_BINDING_STRING, $primkey);
            $bp->add(MYSQL_BINDING_INTEGER, $stateid);
            $bp->add(MYSQL_BINDING_STRING, $displayed);
            $bp->add(MYSQL_BINDING_STRING, $result);
            $bp->add(MYSQL_BINDING_INTEGER, $dirty);
            $bp->add(MYSQL_BINDING_INTEGER, $m);
            $bp->add(MYSQL_BINDING_INTEGER, $l);
            $bp->add(MYSQL_BINDING_INTEGER, $v);
            if ($key == "") {
                $query = "replace into " . Config::dbSurveyData() . "_observations(suid, primkey, stateid, displayed, remark, dirty, mode, language, version) values (?,?,?,?,?,?,?,?,?)";
            } else {
                $query = "replace into " . Config::dbSurveyData() . "_observations(suid, primkey, stateid, displayed, remark, dirty, mode, language, version) values (?,?,?,?,aes_encrypt(?, '" . $key . "'),?,?,?,?)";
            }
            $db->executeBoundQuery($query, $bp->get());
        }
        return "";
    }

    function removeRemark() {
        global $db;
        $stateid = $this->getParam(POST_PARAM_STATEID);
        $displayed = $this->getParam(POST_PARAM_DISPLAYED);
        if ($stateid == "") {
            $stateid = 1;
        }
        $primkey = $this->getParam(POST_PARAM_PRIMKEY);
        $suid = $this->getParam(POST_PARAM_SUID);
        $query = "delete from " . Config::dbSurveyData() . "_observations where suid=" . prepareDatabaseString($suid) . " and primkey='" . prepareDatabaseString($primkey) . "' and stateid=" . prepareDatabaseString($stateid);
        $db->executeQuery($query);
        return "";
    }

    function storeParadata() {
        global $db;

        $l = $this->getParam(POST_PARAM_LANGUAGE);
        $m = $this->getParam(POST_PARAM_MODE);
        $v = $this->getParam(POST_PARAM_VERSION);

        $pardata = urldecode(loadvar(POST_PARAM_PARADATA));
        $displayed = urldecode(loadvar(POST_PARAM_DISPLAYED));
        $stateid = $this->getParam(POST_PARAM_STATEID);
        $primkey = $this->getParam(POST_PARAM_PRIMKEY);
        $suid = $this->getParam(POST_PARAM_SUID);
        $rgid = $this->getParam(POST_PARAM_RGID);

        $screen = gzcompress($result, 9);
        if ($stateid == "") {
            $stateid = 1;
        }

        $bp = new BindParam();
        $scid = null;

        $bp->add(MYSQL_BINDING_INTEGER, $scid);
        $bp->add(MYSQL_BINDING_INTEGER, $suid);
        $bp->add(MYSQL_BINDING_STRING, $primkey);
        $bp->add(MYSQL_BINDING_INTEGER, $stateid);
        $bp->add(MYSQL_BINDING_INTEGER, $rgid);
        $bp->add(MYSQL_BINDING_STRING, $displayed);
        $bp->add(MYSQL_BINDING_STRING, $pardata);
        $bp->add(MYSQL_BINDING_INTEGER, $m);
        $bp->add(MYSQL_BINDING_INTEGER, $l);
        $bp->add(MYSQL_BINDING_INTEGER, $v);
        $key = $this->survey->getDataEncryptionKeyDirectly($m, $l, $this->getParam(POST_PARAM_DEFAULT_MODE), $this->getParam(POST_PARAM_DEFAULT_LANGUAGE));
        if ($key == "") {
            $query = "insert into " . Config::dbSurveyData() . "_paradata(pid, suid, primkey, stateid, rgid, displayed, paradata, mode, language, version) values (?,?,?,?,?,?,?,?)";
        } else {
            $query = "insert into " . Config::dbSurveyData() . "_paradata(pid, suid, primkey, stateid, rgid, displayed, paradata, mode, language, version) values (?,?,?,?,aes_encrypt(?, '" . $key . "'),?,?,?)";
        }

        $db->executeBoundQuery($query, $bp->get());
        exit;
    }

    function captureScreenshot() {

        global $db;

        $l = $this->getParam(POST_PARAM_LANGUAGE);
        $m = $this->getParam(POST_PARAM_MODE);
        $v = $this->getParam(POST_PARAM_VERSION);

        $result = urldecode(loadvar(POST_PARAM_SCREENSHOT));
        $stateid = $this->getParam(POST_PARAM_STATEID);
        $primkey = $this->getParam(POST_PARAM_PRIMKEY);
        $suid = $this->getParam(POST_PARAM_SUID);

        $screen = gzcompress($result, 9);
        if ($stateid == "") {
            $stateid = 1;
        }

        $bp = new BindParam();
        $scid = null;

        $bp->add(MYSQL_BINDING_INTEGER, $scid);
        $bp->add(MYSQL_BINDING_INTEGER, $suid);
        $bp->add(MYSQL_BINDING_STRING, $primkey);
        $bp->add(MYSQL_BINDING_INTEGER, $stateid);
        $bp->add(MYSQL_BINDING_STRING, $screen);
        $bp->add(MYSQL_BINDING_INTEGER, $m);
        $bp->add(MYSQL_BINDING_INTEGER, $l);
        $bp->add(MYSQL_BINDING_INTEGER, $v);
        $key = $this->survey->getDataEncryptionKeyDirectly($m, $l, $this->getParam(POST_PARAM_DEFAULT_MODE), $this->getParam(POST_PARAM_DEFAULT_LANGUAGE));
        if ($key == "") {
            $query = "insert into " . Config::dbSurveyData() . "_screendumps(scdid, suid, primkey, stateid, screen, mode, language, version) values (?,?,?,?,?,?,?,?)";
        } else {
            $query = "insert into " . Config::dbSurveyData() . "_screendumps(scdid, suid, primkey, stateid, screen, mode, language, version) values (?,?,?,?,aes_encrypt(?, '" . $key . "'),?,?,?)";
        }

        $db->executeBoundQuery($query, $bp->get());
        exit;
    }

}

?>