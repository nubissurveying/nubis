<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class Contact {

    var $contact;

    function __construct($row) {
        $this->contact = $row;
    }

    function getProxyName() {
        return $this->contact['proxyname_dec'];
    }

    function getProxy() {
        return $this->contact['proxy'];
    }

    function getRemark() {
        return $this->contact['remark_dec'];
    }

    function getCode() {
        return $this->contact['code'];
    }

    function getText($respondentOrHousehold = null) {
        $dispositionCodes = Language::optionsDispositionContactCode($respondentOrHousehold);
        $finalDispositionCodes = Language::optionsFinalDispositionContactCode($respondentOrHousehold);
        if (isset($dispositionCodes[$this->getCode()])) {
            return $dispositionCodes[$this->getCode()][1];
        } else {
            return $finalDispositionCodes[$this->getCode()][1];
        }
    }

    function getUsername() {
        return $this->contact['username'];
    }

    function getContactTs() {
        return $this->contact['contactts'];
    }

    function isProxy() {
        return $this->getProxy() == 1;
    }

    function getEvent() {
        return $this->contact['event'];
    }

    function getPrimkey() {
        return $this->contact['primkey'];
    }

    function isRefusal() {
        $dispositionCodes = Language::optionsDispositionContactCode();
        $finalDispositionCodes = Language::optionsFinalDispositionContactCode();
        if (isset($dispositionCodes[$this->getCode()])) {
            return $dispositionCodes[$this->getCode()][3] == 1;
        } else {
            return $finalDispositionCodes[$this->getCode()][3] == 1;
        }
    }

    function isFinalCode() {
        //$dispositionCodes = Language::optionsDispositionContactCode();
        $finalDispositionCodes = Language::optionsFinalDispositionContactCode();
        /* if (in_array($dispositionCodes, $this->getCode())){
          return false;
          } */
        if (isset($finalDispositionCodes[$this->getCode()])) {
            return true;
        }
        return false;
    }

    function isNonSample() {
        $dispositionCodes = Language::optionsDispositionContactCode();
        return $dispositionCodes[$this->getCode()][4] == 1;
    }

}

?>