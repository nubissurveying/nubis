<?php

/*

  ------------------------------------------------------------------------

  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

  ------------------------------------------------------------------------

 */


error_reporting(E_ALL);

ini_set("display_errors", 1);


set_include_path(dirname(getcwd()));

require_once("constants.php");
require_once('users.php');
require_once('user.php');
require_once("functions.php");
require_once('dbConfig.php');
require_once('config.php');

date_default_timezone_set(Config::timezone());

require_once('database.php');
$db = new Database();

require_once('contacts.php');
require_once('contact.php');

session_start();

$urid = $_SESSION['URID'];

$contacts = new Contacts();
$events = $contacts->getAppointments($urid);
$eventsColor = array('important', 'special', 'success', 'info', 'warning', 'inverse');

echo '{

        "success": 1,

        "result": [';

$id = 1;

foreach ($events as $event) {

    if ($id != 1) {
        echo ',';
    }

    echo '{';

    echo '                    "id": "' . $id . '",';

    echo '                    "title": "' . date('H:i', strtotime($event->getEvent())) . ': ' . $event->getPrimkey() . ' - ' . $event->getRemark() . '",';

    echo '                    "url": "' . setSessionParams(array('page' => 'catiinterviewer.info', 'primkey' => $event->getPrimkey())) . '",';

    echo '                    "class": "event-' . $eventsColor[mt_rand(0, sizeof($events) - 1)] . '",';

    echo '                    "start": "' . strtotime($event->getEvent()) . '000",';

    echo '                    "end":   "' . strtotime($event->getEvent()) . '000"';

    echo '}';

    $id++;
}



echo '        ]

}';

?>