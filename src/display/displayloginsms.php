<?php

/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

class DisplayLoginSMS extends Display {

    public function __construct() {
        parent::__construct();
    }

    public function showLogin($message) {
        $extra2 = '<link href="js/formpickers/css/bootstrap-formhelpers.min.css" rel="stylesheet">
                  <link href="css/uscicadmin.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">';
        $returnStr = $this->showHeader(Language::messageSMSTitle(), $extra2);
        $returnStr .= '<div id = "wrap">';
        $returnStr .= '<div class = "container"><p>';
        $returnStr .= '<center><table style = "width:300px"><tr><td><form method="post" autocomplete="off">';
        $returnStr .= '<h2>' . Language::messageSMSTitle() . '</h2>';
        if ($message != '') {
            $returnStr .= '<span class = "label label-warning">' . $message . '</span><br/><br/>';
        }
        $returnStr .= Language::messageSMSWelcome();
        $returnStr .= '<br/><br/>';
        if ($message != "") {
            $returnStr .= "<input type=hidden name=" . POST_PARAM_SE . " value=" . USCIC_SMS . " />";
        }
        $returnStr .= '<input type = "text" class = "form-control" name = username placeholder = "' . Language::labelUsername() . '" autofocus>';
        $returnStr .= '<input type = "password" class = "form-control" name = password placeholder = "' . Language::labelPassword() . '">';
        $returnStr .= '<button class = "btn btn-lg btn-default btn-block" type = "submit">' . Language::buttonLogin() . '</button>';
        $returnStr .= '</form></td></tr></table></center>';
        $returnStr .= '</p></div> <!--/container-->';
        $returnStr .= '</div>';
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter();
        return $returnStr;
    }

    function showBottomBar() {
        return '
        <div id = "footer">
        <div class = "container">
        <p class = "text-muted credit" style = "text-align:right">' . Language::nubisFooter() . '</p>
        </div>
        </div>
        ';
    }
    
    public function showSysadminKey() {
        $extra2 = '<link href="js/formpickers/css/bootstrap-formhelpers.min.css" rel="stylesheet">
                  <link href="css/uscicadmin.css" rel="stylesheet">
                  <link href="bootstrap/css/sticky-footer-navbar.min.css" rel="stylesheet">';
        $returnStr = $this->showHeader(Language::messageSMSTitle(), $extra2);
        $returnStr .= '<div id = "wrap">';
        $returnStr .= '<div class = "container"><p>';
        $returnStr .= '<center><table style = "width:300px"><tr><td><form method="post" autocomplete="off">';
        $returnStr .= '<h2>' . Language::messageSMSTitle() . '</h2>';
        //if ($message != '') {
            $returnStr .= '<span class = "label label-warning">' . Language::headerSysadminLocked() . '</span><br/><br/>';
        //}
        $returnStr .= Language::messageSysadminLocked();
        $returnStr .= '<br/><br/>';
        if ($message != "") {
            $returnStr .= "<input type=hidden name=" . POST_PARAM_SE . " value=" . USCIC_SMS . " />";
        }
        $returnStr .= '<input type = "text" class = "form-control" name = "sk" placeholder = "' . Language::labelSysadminKey() . '" autofocus>';
        $returnStr .= '<button class = "btn btn-lg btn-default btn-block" type = "submit">' . Language::buttonSubmit() . '</button>';
        $returnStr .= '</form></td></tr></table></center>';
        $returnStr .= '</p></div> <!--/container-->';
        $returnStr .= '</div>';
        $returnStr .= $this->showBottomBar();
        $returnStr .= $this->showFooter();
        return $returnStr;
    }

}

?>