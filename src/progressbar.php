<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Progressbar {

    private $suid;
    private $seid;
    private $entries;
    private $storeentries;
    private $counter;
    //private $realcounter;
    //private $resetted;
    //private $oldifrgid;
    private $loopentries;

    //private $maxscreens;

    function __construct($suid, $seid) {

        $this->suid = prepareDatabaseString($suid);

        $this->seid = prepareDatabaseString($seid);

        $this->entries = array();
        $this->storeentries = array();
        $this->counter = 1;
        //$this->realcounter = 1;
        //$this->resetted = array();
        //$this->oldifrgid = "";
        $this->loopentries = array();
        //$this->maxscreens = 0;
    }

    function addEntry($seid, $seidrgid, $rgid, $loopstring, $ifrgid, $plus = "", $looprgid = "") {

        if ($seidrgid == "") {
            $seidrgid = 0;
        }
        $number = $this->counter;
        $this->entries[$this->seid . '-' . $seid . '-' . $seidrgid . '-' . $rgid . '-' . $loopstring] = array("seid" => $seid, "seidrgid" => $seidrgid, "rgid" => $rgid, "number" => $number, "loopstring" => $loopstring);
        $this->storeentries[] = array("seid" => $seid, "seidrgid" => $seidrgid, "rgid" => $rgid, "number" => $number, "loopstring" => $loopstring);
        $this->counter++;
    }
    
    function getScreenNumber($seid, $seidrgid, $rgid, $loopstring) {

        if (isset($this->entries[$this->seid . '-' . $seid . '-' . $seidrgid . '-' . $rgid . '-' . $loopstring])) {
            $entry = $this->entries[$this->seid . '-' . $seid . '-' . $seidrgid . '-' . $rgid . '-' . $loopstring];
            return $entry["number"];
        }
        return 0; // something went wrong
    }

    function getCounter() {
        return $this->counter;
    }

    function getNumberOfScreens() {
        return sizeof(array_keys($this->entries)); // $this->maxscreens;
    }

    function delete() {

        global $db;

        $del = "delete from " . Config::dbSurvey() . "_progressbars where suid=" . prepareDatabaseString($this->suid) . " and mainseid=" . prepareDatabaseString($this->seid);

        $db->executeQuery($del);
    }

    function load() {
        
    }

    function save() {
        $this->delete();
        global $db;
        $number = 0;
        for ($j = 0; $j < sizeof($this->storeentries); $j++) {
            $entry = $this->storeentries[$j];
            $i = "replace into " . Config::dbSurvey() . "_progressbars (suid, mainseid, seid, seidrgid, rgid, number, loopstring) values(" . prepareDatabaseString($this->suid) . "," . prepareDatabaseString($this->seid) . "," . prepareDatabaseString($entry["seid"]) . "," . prepareDatabaseString($entry["seidrgid"]) . "," . prepareDatabaseString($entry["rgid"]) . "," . prepareDatabaseString($entry["number"]) . ",'" . prepareDatabaseString($entry["loopstring"]) . "')";
            $db->executeQuery($i);
            $number++;
        }
    }

    /* section based progress */

    function getSectionProgress($suid, $mainseid, $seid, $rgid, $loopstring, $looprgid) {
        global $engine;

        if ($loopstring == "") {
            $loopstring = 1;
        }
        global $db;
        $query = "select number, looptimes, outerlooprgids from " . Config::dbSurvey() . "_screens where suid=" . $suid . " and seid=" . $seid . ' and rgid=' . $rgid;

        $res = $db->selectQuery($query);
        if ($res) {
            if ($db->getNumberOfRows($res) > 0) {
                $row = $db->getRow($res);
                if ($row["looptimes"] == 1 && ($looprgid == 0 || $looprgid == "")) {
                    return $row["number"];
                }
                // we are in a loop!
                else {

                    // get number of first action in loop
                    $query = "select number from " . Config::dbSurvey() . "_screens where suid=" . $suid . " and seid=" . $seid . ' and rgid > ' . $looprgid . ' limit 0,1';
                    $res = $db->selectQuery($query);
                    if ($res) {
                        if ($db->getNumberOfRows($res) > 0) {
                            $row2 = $db->getRow($res);
                            $startloop = $row2["number"];

                            // get number right after the loop
                            $query = "select number from " . Config::dbSurvey() . "_screens where suid=" . $suid . " and seid=" . $seid . ' and rgid > ' . $rgid . ' and outerlooprgids != "' . $row["outerlooprgids"] . '" limit 0,1';
                            $res = $db->selectQuery($query);
                            if ($res) {
                                if ($db->getNumberOfRows($res) > 0) {
                                    $row1 = $db->getRow($res);

                                    $numberofquestions = $row1["number"] - $startloop; // number of question screens
                                    //$factor = $difference/($row["looptimes"]*$difference); // moving from one question to another the increment must be this                                
                                    //$loopmax = $engine->loopmax;
                                    //$loopmin = $engine->loopmin;
                                    //$loopcounters = $engine->loopcounter;
                                    // get loop data
                                    global $db;
                                    $query = "select loopmin, loopmax, loopcounter from " . Config::dbSurveyData() . "_loopdata where suid=" . $suid . " and primkey='" . $engine->getPrimaryKey() . "' and mainseid=" . $mainseid . " and seid=" . $seid . " and looprgid=" . $looprgid;
                                    $res = $db->selectQuery($query);
                                    
                                    if ($res) {
                                        $row4 = $db->getRow($res);
                                        $loopmin = $row4["loopmin"];
                                        $loopmax = $row4["loopmax"];
                                        $loopcounters = $row4["loopcounter"];
                                    }

                                    // go through outer/inner loops
                                    $counters = explode("~", $loopcounters);
                                    $current = "";
                                    foreach ($counters as $count) {
                                        if ($current == "") {
                                            $current = $engine->getAnswer($count);
                                        } else {
                                            $current = $current * $engine->getAnswer($count);
                                        }
                                    }

                                    // calculate total number of outer loops
                                    $outer = 1;
                                    
                                    // untested code for calculating total number of outer loops
                                    /*$beforelooprgid = $looprgid;
                                    foreach ($counters as $count) {                                        
                                        $qb = "select * from " . Config::dbSurveyData() . "_loopdata where suid=" . $suid . " and primkey='" . $engine->getPrimaryKey() . "' and mainseid=" . $mainseid . " and seid=" . $seid . " and loopcounter='" . $count . "' and looprgid <" . $beforelooprgid . " order by looprgid desc";
                                        $resb = $db->selectQuery($qb);
                                        if ($resb) {
                                            if ($db->getNumberOfRows($resb) > 0) {
                                                $rowb = $db->getRow($resb);
                                                $outer = $outer * ($rowb["loopmax"] - $rowb["loopmin"] + 1);
                                                $beforelooprgid = $rowb["looprgid"];
                                            }
                                        }
                                    }*/
                                    $numberofloops = ($loopmax - $loopmin + 1) * $outer; // calculate total number of loops
                                    // calculate factor: number of questions in one loop divided by the total number of questions across all loops
                                    $factor = $numberofquestions / ($numberofloops * $numberofquestions);

                                    $extra = 0;

                                    // calculate how much covered in this loop so far!                                
                                    //$extra = ($loopstring - $loopmin) * $factor;
                                    $extra = ($row["number"] - $startloop) * $factor;
                                    $extra = $extra + ($current - $loopmin) * $numberofquestions * $factor;
                                    $newnumber = $startloop + $extra;

                                    return $newnumber;
                                }
                            }
                        }
                    }
                }
            }
        }
        return "";
    }

    function getSectionTotal($suid, $seid) {

        global $db;
        $query = "select * from " . Config::dbSurvey() . "_screens where suid=" . $suid . " and seid=" . $seid;
        $res = $db->selectQuery($query);
        if ($res) {
            return $db->getNumberOfRows($res);            
        }
        return 1;
    }

}

?>