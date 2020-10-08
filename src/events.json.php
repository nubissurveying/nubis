<?php

/* 
------------------------------------------------------------------------
Copyright (C) 2014 Bart Orriens, Albert Weerman

This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
------------------------------------------------------------------------
*/
require_once("constants.php");
require_once("functions.php");
require_once('globals.php');

session_start();
$urid = $_SESSION['URID'];

$contacts = new Contacts();
$events = $contacts->getAppointments($urid);

$eventsColor = array ('important', 'special', 'success', 'info', 'warning', 'inverse');

echo '{
        "success": 1,
        "result": [';
$id = 1;
foreach ($events as $event){
  if ($id != 1){ echo ','; }
  echo '{';
  echo '                    "id": "' . $id . '",';
  echo '                    "title": "' . date('H:i', strtotime($event->getEvent())) . ': ' . $event->getPrimkey() . ' - ' . $event->getRemark() . '",';
  echo '                    "url": "' . setSessionParams(array('page' => 'interviewer.info', 'primkey' => $event->getPrimkey())) . '",';
  echo '                    "class": "event-' . $eventsColor[mt_rand(0, sizeof($events) - 1)] . '",';
  echo '                    "start": "' . strtotime($event->getEvent()) . '000",';
  echo '                    "end":   "' . strtotime($event->getEvent()) . '000"';
  echo '}';
  $id++;
}

echo '        ]
}';


/*
{
        "success": 1,
        "result": [
                {
                        "id": "293",
                        "title": "This is warning class event",
                        "url": "http://www.example.com/",
                        "class": "event-warning",
                        "start": "1362938400000",
                        "end":   "1363197686300"
                },
                {
                        "id": "294",
                        "title": "This is information class ",
                        "url": "http://www.example.com/",
                        "class": "event-info",
                        "start": "1363111200000",
                        "end":   "1363284086400"
                },
                {
                        "id": "297",
                        "title": "This is success event",
                        "url": "http://www.example.com/",
                        "class": "event-success",
                        "start": "1363284000000",
                        "end":   "1363284086400"
                                  
                },
                {
                        "id": "54",
                        "title": "This is a test event",
                        "url": "index.php?page=test",
                        "class": "",
                        "start": "1363629600000",
                        "end":   "1363629600000"
                },
                {
                        "id": "532",
                        "title": "This is inverse event",
                        "url": "http://www.example.com/",
                        "class": "event-inverse",
                        "start": "1364407200000",
                        "end":   "1364493686400"
                },
                {
                        "id": "548",
                        "title": "This is special event",
                        "url": "http://www.example.com/",
                        "class": "event-special",
                        "start": "1363197600000",
                        "end":   "1363629686400"
                                   
                },
                {
                        "id": "295",
                        "title": "Event 3",
                        "url": "http://www.example.com/",
                        "class": "event-important",
                        "start": "1364320800000",
                        "end":   "1364407286400"
                }
        ]
}*/

?>