<?php

/* 
------------------------------------------------------------------------
Copyright (C) 2014 Bart Orriens, Albert Weerman

This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
------------------------------------------------------------------------
*/


class RoutingInstruction {

    
    private $suid;
    private $seid;
    private $rid;
    private $text;

    

    function __construct($suid, $seid, $rid, $text) {
        $this->suid = $suid;
        $this->seid = $seid;
        $this->rid = $rid;
        $this->text = $text;
    }    

     function getSuid() {

        return $this->rid;

    }
    
    function getSeid() {

        return $this->rid;

    }
    

    function getRgid() {

        return $this->rid;

    }

    

    function getRule() {

        return $this->text;

    }

    

    function setRgid($rgid) {

        $this->rgid = $rgid;

    }

    

    function setRule($text) {

        $this->text = $text;

    }

}



?>