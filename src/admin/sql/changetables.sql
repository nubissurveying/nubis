/*
  ------------------------------------------------------------------------
  Copyright (C) 2014 Bart Orriens, Albert Weerman

  This library/program is free software; you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation; either version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public License along with this library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
  ------------------------------------------------------------------------
 */

/* AFTER DECEMBER 5, 2013 */
ALTER TABLE `survey1_actions` ADD `suid` INT( 11 ) NULL DEFAULT NULL AFTER `sessionid` ;

ALTER TABLE `survey1_surveys` ADD `description` TEXT NULL DEFAULT NULL AFTER `name` ;

/* AFTER DECEMBER 7, 2013 */
CREATE TABLE IF NOT EXISTS `survey1_versions` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `vnid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vnid`,`suid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_variables` ADD `tyd` INT( 11 ) NULL DEFAULT 0 AFTER `answertype` ;

ALTER TABLE `survey1_translations` CHANGE `type` `type` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `survey1_translations` ADD `translationtype` INT( 11 ) NOT NULL DEFAULT '1' AFTER `options` ;

ALTER TABLE `survey1_settings` ADD `objecttype` INT( 11 ) NOT NULL DEFAULT '1' AFTER `object` ;

ALTER TABLE `survey1_settings` CHANGE `object` `object` INT( 11 ) NOT NULL DEFAULT '1';

DROP TABLE `survey1_translations`;

CREATE TABLE IF NOT EXISTS `survey1_groups` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`gid`,`suid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_context` CHANGE `functions` `groups` BLOB NULL DEFAULT NULL ;

ALTER TABLE `survey1_context` ADD `getfills` BLOB NULL DEFAULT NULL AFTER `groups` ;
ALTER TABLE `survey1_context` ADD `setfills` BLOB NULL DEFAULT NULL AFTER `getfills` ;

ALTER TABLE `survey1_screens` ADD `looptimes` INT( 11 ) NOT NULL DEFAULT '-1' AFTER `section` ;

ALTER TABLE `survey1_engines` ADD `progressbar` BLOB NULL DEFAULT NULL AFTER `engine` ;

ALTER TABLE `survey1_states` ADD `loopstring` varchar(150) NOT NULL DEFAULT '' AFTER `template` ;
ALTER TABLE `survey1_test_states` ADD `loopstring` varchar(150) NOT NULL DEFAULT '' AFTER `template` ;

ALTER TABLE `survey1_states` ADD `inlinefields` BLOB NOT NULL DEFAULT NULL AFTER `subdisplays` ;
ALTER TABLE `survey1_test_states` ADD `inlinefields` BLOB NOT NULL DEFAULT NULL AFTER `subdisplays` ;

ALTER TABLE `survey1_users` ADD `status` INT NOT NULL DEFAULT '1' AFTER `urid` ;

ALTER TABLE `survey1_users` ADD `settings` BLOB NULL AFTER `communication` ;

ALTER TABLE `survey1_respondents` ADD `info` BLOB NULL AFTER `email` , ADD `contactperson` BLOB NULL AFTER `info` ;

ALTER TABLE `survey1_respondents` ADD `puid` INT NOT NULL DEFAULT '0' AFTER `urid` ;

ALTER TABLE `survey1_sections` ADD `position` INT NULL DEFAULT NULL AFTER `name`;
ALTER TABLE `survey1_variables` ADD `position` INT NULL DEFAULT NULL AFTER `variablename`;

CREATE TABLE IF NOT EXISTS `survey1_tracks` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `object` int(11) DEFAULT NULL,
  `setting` varchar(150) DEFAULT NULL,
  `value` blob,
  `mode` int(11) DEFAULT NULL,
  `language` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_actions` ADD `language` INT( 11 ) NULL DEFAULT NULL AFTER `params` ;
ALTER TABLE `survey1_actions` ADD `mode` INT(11) NULL DEFAULT NULL AFTER `language`;
ALTER TABLE `survey1_actions` ADD `version` INT(11) NULL DEFAULT NULL AFTER `mode`;

ALTER TABLE `survey1_test_actions` ADD `language` INT( 11 ) NULL DEFAULT NULL AFTER `params` ;
ALTER TABLE `survey1_test_actions` ADD `mode` INT(11) NULL DEFAULT NULL AFTER `language`;
ALTER TABLE `survey1_test_actions` ADD `version` INT(11) NULL DEFAULT NULL AFTER `mode`;

ALTER TABLE `survey1_states` ADD `language` INT( 11 ) NOT NULL DEFAULT '1' AFTER `inlinefields` ;
ALTER TABLE `survey1_states` ADD `mode` INT(11) NOT NULL DEFAULT '1' AFTER `language`;
ALTER TABLE `survey1_states` ADD `version` INT(11) NOT NULL DEFAULT '1' AFTER `mode`;

ALTER TABLE `survey1_test_states` ADD `language` INT( 11 ) NOT NULL DEFAULT '1' AFTER `inlinefields` ;
ALTER TABLE `survey1_test_states` ADD `mode` INT(11) NOT NULL DEFAULT '1' AFTER `language`;
ALTER TABLE `survey1_test_states` ADD `version` INT(11) NOT NULL DEFAULT '1' AFTER `mode`;

ALTER TABLE `survey1_context` ADD `setfills` BLOB NULL DEFAULT NULL AFTER `getfills` ;

ALTER TABLE `survey1_states` ADD INDEX `stateindex` (`suid`,`primkey`);
ALTER TABLE `survey1_test_states` ADD INDEX `stateindex` (`suid`,`primkey`);

ALTER TABLE `survey1_actions` ADD INDEX `actionindex` (`sessionid`);
ALTER TABLE `survey1_test_actions` ADD INDEX `actionindex` (`sessionid`);

ALTER TABLE `survey1_screendumps` CHANGE `scdid` `scdid` INT( 11 ) NOT NULL AUTO_INCREMENT ;
ALTER TABLE `survey1_test_screendumps` CHANGE `scdid` `scdid` INT( 11 ) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `survey1_screendumps` ADD `stateid` INT( 11 ) NULL AFTER `primkey` ;
ALTER TABLE `survey1_test_screendumps` ADD `stateid` INT( 11 ) NULL AFTER `primkey` ;

ALTER TABLE `survey1_screendumps` ADD `language` INT( 11 ) NULL DEFAULT NULL AFTER `screen` ;
ALTER TABLE `survey1_screendumps` ADD `mode` INT(11) NULL DEFAULT NULL AFTER `language`;
ALTER TABLE `survey1_screendumps` ADD `version` INT(11) NULL DEFAULT NULL AFTER `mode`;

ALTER TABLE `survey1_test_screendumps` ADD `language` INT( 11 ) NULL DEFAULT NULL AFTER `screen` ;
ALTER TABLE `survey1_test_screendumps` ADD `mode` INT(11) NULL DEFAULT NULL AFTER `language`;
ALTER TABLE `survey1_test_screendumps` ADD `version` INT(11) NULL DEFAULT NULL AFTER `mode`;


CREATE TABLE IF NOT EXISTS `survey1_observations` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) NOT NULL DEFAULT '1',

  `displayed` text,

  `remark` blob,

  `dirty` int(11) NOT NULL DEFAULT '0',

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`primkey`,`suid`,`stateid`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_test_observations` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) NOT NULL DEFAULT '1',

  `displayed` text,

  `remark` blob,

  `dirty` int(11) NOT NULL DEFAULT '0',

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`primkey`,`suid`,`stateid`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `survey1_households` (
  `hhid` varchar(25) NOT NULL,
  `urid` int(11) NOT NULL DEFAULT '0',
  `name` blob,
  `puid` int(11) NOT NULL DEFAULT '-1',
  `address1` blob,
  `address2` blob,
  `city` blob,
  `zip` blob,
  `state` blob,
  `telephone1` blob,
  `telephone2` blob,
  `email` blob,
  `status` int(11) NOT NULL DEFAULT '0',
  `test` int(11) NOT NULL DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_respondents` ADD `hhid` varchar( 25 ) NULL DEFAULT NULL AFTER `primkey` ;
ALTER TABLE `survey1_respondents` ADD `selected` INT( 11 ) NULL DEFAULT '0' AFTER `test` ;
ALTER TABLE `survey1_respondents` ADD `present` INT( 11 ) NULL DEFAULT '0' AFTER `selected` ;

ALTER TABLE `survey1_respondents` ADD `finr` INT( 11 ) NULL DEFAULT '0' AFTER `selected` ;
ALTER TABLE `survey1_respondents` ADD `famr` INT( 11 ) NULL DEFAULT '0' AFTER `finr` ;
ALTER TABLE `survey1_respondents` ADD `sex` blob NULL DEFAULT NULL AFTER `famr` ;
ALTER TABLE `survey1_respondents` ADD `age` blob NULL DEFAULT NULL AFTER `sex` ;
ALTER TABLE `survey1_respondents` ADD `birthdate` blob NULL DEFAULT NULL AFTER `age` ;
ALTER TABLE `survey1_respondents` ADD `schoolingyears` INT( 11 ) NULL DEFAULT '0' AFTER `birthdate` ;
ALTER TABLE `survey1_respondents` ADD `educationlevel` INT( 11 ) NULL DEFAULT '0' AFTER `schoolingyears` ;
ALTER TABLE `survey1_respondents` ADD `occupationalstatus` INT( 11 ) NULL DEFAULT '0' AFTER `educationlevel` ;
ALTER TABLE `survey1_respondents` ADD `relationshiphh` INT( 11 ) NULL DEFAULT '0' AFTER `occupationalstatus` ;
ALTER TABLE `survey1_respondents` ADD `spouseprimkey` varchar( 25 ) NULL DEFAULT NULL AFTER `relationshiphh` ;


ALTER TABLE `survey1_states` ADD `looprgid` INT( 11 ) NULL DEFAULT NULL AFTER `template` ;
ALTER TABLE `survey1_test_states` ADD `looprgid` INT( 11 ) NULL DEFAULT NULL AFTER `template` ;

ALTER TABLE `survey1_users` ADD `access` BLOB NULL DEFAULT NULL AFTER `settings`;

CREATE TABLE IF NOT EXISTS `survey1_logs` (

  `lgid` int(11) NOT NULL AUTO_INCREMENT,

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `variablename` varchar(150) NOT NULL,

  `answer` blob,

  `dirty` int(11) NOT NULL DEFAULT '0',

  `action` int(11) NOT NULL DEFAULT '0',

  `version` int(11) NOT NULL,

  `language` int(11) NOT NULL,

  `mode` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`lgid`, `suid`, `primkey`,`variablename`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_test_logs` (

  `lgid` int(11) NOT NULL AUTO_INCREMENT,

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `variablename` varchar(150) NOT NULL,

  `answer` blob,

  `dirty` int(11) NOT NULL DEFAULT '0',

  `action` int(11) NOT NULL DEFAULT '0',

  `version` int(11) NOT NULL,

  `language` int(11) NOT NULL,

  `mode` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`lgid`, `suid`, `primkey`,`variablename`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `survey1_users` ADD `lastdata` DATETIME NULL DEFAULT NULL AFTER `access`;

CREATE TABLE IF NOT EXISTS `survey1_communication` (
  `hnid` int(11) NOT NULL AUTO_INCREMENT,
  `urid` int(11) NOT NULL,
  `insertts` datetime NOT NULL,
  `received` int(11) NOT NULL DEFAULT '0',
  `sqlcode` blob NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`hnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `survey1_psus` (
  `puid` int(11) NOT NULL,
  `code` varchar(25) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `region` varchar(150) DEFAULT NULL,
  `state` varchar(150) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`puid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `survey1_data` ADD INDEX `variablenameindex` (`suid`,`variablename`);
ALTER TABLE `survey1_test_data` ADD INDEX `variablenameindex` (`suid`,`variablename`);

ALTER TABLE `survey1_test_data` DROP PRIMARY KEY, ADD PRIMARY KEY (`suid`, `primkey`, `variablename`);

ALTER TABLE `survey1_states` ADD `undoassignments` BLOB NULL DEFAULT NULL AFTER `assignments` ;
ALTER TABLE `survey1_test_states` ADD `undoassignments` BLOB NULL DEFAULT NULL AFTER `assignments` ;


CREATE TABLE IF NOT EXISTS `survey1_interviewstatus` (  
  `primkey` varchar(150) NOT NULL,
  `status` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_interviewstatus` ADD `suid` int(11) NOT NULL DEFAULT '1' AFTER `primkey`;

ALTER TABLE `survey1_interviewstatus` DROP PRIMARY KEY, ADD PRIMARY KEY (`suid`, `primkey`);

ALTER TABLE `survey1_interviewstatus` ADD `mainseid` int(11) NOT NULL DEFAULT '1' AFTER `primkey`;

ALTER TABLE `survey1_interviewstatus` DROP PRIMARY KEY, ADD PRIMARY KEY (`suid`, `primkey`, `mainseid`);

CREATE TABLE IF NOT EXISTS `survey1_test_interviewstatus` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `mainseid` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`, `primkey`, `mainseid`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_times` ADD `rgid` INT( 11 ) NULL DEFAULT NULL AFTER `primkey` ;
ALTER TABLE `survey1_test_times` CHANGE `prim_key` `primkey` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `survey1_test_times` ADD `rgid` INT( 11 ) NULL DEFAULT NULL AFTER `primkey` ;

ALTER TABLE `survey1_states` ADD `parentrgid` INT( 11 ) NULL DEFAULT NULL AFTER `parentseid` ;
ALTER TABLE `survey1_test_states` ADD `parentrgid` INT( 11 ) NULL DEFAULT NULL AFTER `parentseid` ;

ALTER TABLE `survey1_screens` ADD `ifrgid` INT( 11 ) NULL DEFAULT NULL AFTER `rgid` ;
ALTER TABLE `survey1_progressbars` ADD `seidrgid` INT( 11 ) NULL DEFAULT NULL AFTER `seid` ;

ALTER TABLE `survey1_states` ADD INDEX `primindex` (`primkey`);
ALTER TABLE `survey1_test_states` ADD INDEX `primindex` (`primkey`);

CREATE TABLE IF NOT EXISTS `survey1_loopdata` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `mainseid` int(11) NOT NULL,
  `seid` int(11) NOT NULL,
  `looprgid` int(11) NOT NULL,
  `loopmin` int(11) NOT NULL,
  `loopmax` int(11) NOT NULL,
  `loopcounter` varchar(150) NOT NULL,
  `looptype` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`,`primkey`,`mainseid`,`seid`,`looprgid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `survey1_test_loopdata` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `mainseid` int(11) NOT NULL,
  `seid` int(11) NOT NULL,
  `looprgid` int(11) NOT NULL,
  `loopmin` int(11) NOT NULL,
  `loopmax` int(11) NOT NULL,
  `loopcounter` varchar(150) NOT NULL,
  `looptype` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`,`primkey`,`mainseid`,`seid`,`looprgid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `survey1_lab` (
  `primkey` varchar(50) CHARACTER SET utf8 NOT NULL,
  `barcode` blob,
  `labbarcode` blob,
  `refusal` int(11) NOT NULL DEFAULT '0',
  `refusalreason` blob,
  `refusaldate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `consent1` int(11) NOT NULL DEFAULT '0',
  `consent2` int(11) NOT NULL DEFAULT '0',
  `consent3` int(11) NOT NULL DEFAULT '0',
  `consent4` int(11) NOT NULL DEFAULT '0',
  `consent5` int(11) NOT NULL DEFAULT '0',
  `consent6` int(11) NOT NULL DEFAULT '0',
  `consent7` int(11) NOT NULL DEFAULT '0',
  `consent8` int(11) NOT NULL DEFAULT '0',
  `survey` int(11) NOT NULL DEFAULT '0',
  `measures` int(11) NOT NULL DEFAULT '0',
  `vision` int(11) NOT NULL DEFAULT '0',
  `anthropometrics` int(11) NOT NULL DEFAULT '0',
  `cd4res` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cd4date` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbscollecteddate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbsreceiveddate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbsstatus` int(11) NOT NULL DEFAULT '0',
  `fielddbsshipmentdate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbsshipmentreturneddate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fielddbsclinicresultsissueddate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fielddbsclinicname` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fielddbshivfinalanon` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `labvisitts` datetime DEFAULT NULL,
  `labdbsposition` int(11) NOT NULL,
  `labdbslocation` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `labbloodposition` int(11) NOT NULL,
  `labbloodlocation` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `labbloodsenttolab` text COLLATE utf8_unicode_ci NOT NULL,
  `labbloodnotcollected` text COLLATE utf8_unicode_ci,
  `labbloodstatus` int(11) NOT NULL DEFAULT '0',
  `labbloodshipmentdate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `labbloodshipmentreturneddate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `requestform` blob,
  `urid` int(11) NOT NULL DEFAULT '0',
  `consenturid` int(11) NOT NULL DEFAULT '0',
  `consentts` timestamp NULL DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `survey1_respondents` ADD `permanent` INT( 11 ) NULL DEFAULT '0' AFTER `present` ;
ALTER TABLE `survey1_respondents` ADD `hhorder` INT( 11 ) NULL DEFAULT '0' AFTER `permanent` ;

CREATE TABLE IF NOT EXISTS `survey1_files` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `urid` int(25) DEFAULT NULL,
  `labbarcode` varchar(25) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `content` longblob,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_files` ADD PRIMARY KEY (`id`);
ALTER TABLE `survey1_files` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `survey1_pictures` (
  `primkey` varchar(25) NOT NULL DEFAULT '',
  `variablename` varchar(125) NOT NULL DEFAULT '',
  `picture` longblob NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `survey1_pictures` ADD PRIMARY KEY (`primkey`,`variablename`);

ALTER TABLE `survey1_respondents` ADD `longitude` BLOB NULL DEFAULT NULL AFTER `state`;
ALTER TABLE `survey1_respondents` ADD `latitude` BLOB NULL DEFAULT NULL AFTER `longitude`;

CREATE TABLE IF NOT EXISTS `survey1_test_lab` (
  `primkey` varchar(50) CHARACTER SET utf8 NOT NULL,
  `barcode` blob,
  `labbarcode` blob,
  `refusal` int(11) NOT NULL DEFAULT '0',
  `refusalreason` blob,
  `refusaldate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `consent1` int(11) NOT NULL DEFAULT '0',
  `consent2` int(11) NOT NULL DEFAULT '0',
  `consent3` int(11) NOT NULL DEFAULT '0',
  `consent4` int(11) NOT NULL DEFAULT '0',
  `consent5` int(11) NOT NULL DEFAULT '0',
  `consent6` int(11) NOT NULL DEFAULT '0',
  `consent7` int(11) NOT NULL DEFAULT '0',
  `consent8` int(11) NOT NULL DEFAULT '0',
  `survey` int(11) NOT NULL DEFAULT '0',
  `measures` int(11) NOT NULL DEFAULT '0',
  `vision` int(11) NOT NULL DEFAULT '0',
  `anthropometrics` int(11) NOT NULL DEFAULT '0',
  `cd4res` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cd4date` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbscollecteddate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbsreceiveddate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbsstatus` int(11) NOT NULL DEFAULT '0',
  `fielddbsshipmentdate` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fielddbsshipmentreturneddate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fielddbsclinicresultsissueddate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fielddbsclinicname` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `fielddbshivfinalanon` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `labvisitts` datetime DEFAULT NULL,
  `labdbsposition` int(11) NOT NULL,
  `labdbslocation` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `labbloodposition` int(11) NOT NULL,
  `labbloodlocation` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `labbloodsenttolab` text COLLATE utf8_unicode_ci NOT NULL,
  `labbloodnotcollected` text COLLATE utf8_unicode_ci,
  `labbloodstatus` int(11) NOT NULL DEFAULT '0',
  `labbloodshipmentdate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `labbloodshipmentreturneddate` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `requestform` blob,
  `urid` int(11) NOT NULL DEFAULT '0',
  `consenturid` int(11) NOT NULL DEFAULT '0',
  `consentts` timestamp NULL DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `survey1_test_pictures` (
  `primkey` varchar(25) NOT NULL DEFAULT '',
  `variablename` varchar(125) NOT NULL DEFAULT '',
  `picture` longblob NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`,`variablename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_households` ADD `dummy1` BLOB NULL DEFAULT NULL AFTER `test`;
ALTER TABLE `survey1_households` ADD `dummy2` BLOB NULL DEFAULT NULL AFTER `dummy1`;
ALTER TABLE `survey1_households` ADD `dummy3` BLOB NULL DEFAULT NULL AFTER `dummy2`;

CREATE TABLE IF NOT EXISTS `survey1_test_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `urid` int(25) DEFAULT NULL,
  `labbarcode` varchar(25) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `content` longblob,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `survey1_issues` (
  `isid` int(11) NOT NULL AUTO_INCREMENT,
  `urid` int(11) NOT NULL DEFAULT '1',
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(50) NOT NULL,
  `mainseid` int(11) NOT NULL DEFAULT '1',
  `seid` int(11) NOT NULL DEFAULT '1',
  `rgid` int(11) NOT NULL,
  `displayed` text NOT NULL,
  `category` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `language` int(11) NOT NULL DEFAULT '1',
  `mode` int(11) NOT NULL DEFAULT '1',
  `version` int(11) NOT NULL DEFAULT '1',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`isid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `survey1_users` ADD `usersubtype` INT( 11 ) NOT NULL DEFAULT '0' AFTER `usertype` ;

ALTER TABLE `survey1_surveys` ADD `position` INT( 11 ) NOT NULL DEFAULT '1' AFTER `description` ;

ALTER TABLE `survey1_engines` ADD `instructions` BLOB NULL DEFAULT NULL AFTER `engine` ;

ALTER TABLE `survey1_tracks` ADD `objecttype` INT( 11 ) NOT NULL DEFAULT '1' AFTER `object` ;

ALTER TABLE `survey1_tracks` ADD `urid` INT( 11 ) NOT NULL DEFAULT '1' FIRST ;

ALTER TABLE `survey1_tracks` ADD `trid` INT( 11 ) NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

CREATE TABLE IF NOT EXISTS `survey1_paradata` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `pid` int(11) NOT NULL AUTO_INCREMENT,

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) NOT NULL DEFAULT '1',

  `rgid` int(11) NOT NULL DEFAULT '1',

  `displayed` varchar(150) NOT NULL,

  `paradata` blob,

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`pid`,`primkey`,`suid`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `survey1_test_paradata` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `pid` int(11) NOT NULL AUTO_INCREMENT,

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) NOT NULL DEFAULT '1',

  `rgid` int(11) NOT NULL DEFAULT '1',

  `displayed` varchar(150) NOT NULL,

  `paradata` blob,

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`pid`,`primkey`,`suid`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_states` ADD `whilergid` INT( 11 ) NULL DEFAULT NULL AFTER `looplastaction` ;
ALTER TABLE `survey1_states` ADD `whilelastaction` varchar( 50 ) NULL DEFAULT NULL AFTER `whilergid` ;

ALTER TABLE `survey1_test_states` ADD `whilergid` INT( 11 ) NULL DEFAULT NULL AFTER `looplastaction` ;
ALTER TABLE `survey1_test_states` ADD `whilelastaction` varchar( 50 ) NULL DEFAULT NULL AFTER `whilergid` ;

ALTER TABLE survey1_settings ENGINE=MyIsam;
ALTER TABLE survey1_variables ENGINE=MyIsam;
ALTER TABLE survey1_groups ENGINE=MyIsam;
ALTER TABLE survey1_engines ENGINE=MyIsam;
ALTER TABLE survey1_types ENGINE=MyIsam;
ALTER TABLE survey1_surveys ENGINE=MyIsam;
ALTER TABLE survey1_sections ENGINE=MyIsam;
ALTER TABLE survey1_next ENGINE=MyIsam;
ALTER TABLE survey1_progressbars ENGINE=MyIsam;
ALTER TABLE survey1_context ENGINE=MyIsam;

ALTER TABLE survey1_times ENGINE=MyIsam;
ALTER TABLE survey1_logs ENGINE=MyIsam;
ALTER TABLE survey1_data ENGINE=MyIsam;
ALTER TABLE survey1_actions ENGINE=MyIsam;
ALTER TABLE survey1_loopdata ENGINE=MyIsam;
ALTER TABLE survey1_paradata ENGINE=MyIsam;
ALTER TABLE survey1_screendumps ENGINE=MyIsam;
ALTER TABLE survey1_states ENGINE=MyIsam;
ALTER TABLE survey1_datarecords ENGINE=MyIsam;

ALTER TABLE survey1_test_times ENGINE=MyIsam;
ALTER TABLE survey1_test_logs ENGINE=MyIsam;
ALTER TABLE survey1_test_data ENGINE=MyIsam;
ALTER TABLE survey1_test_actions ENGINE=MyIsam;
ALTER TABLE survey1_test_loopdata ENGINE=MyIsam;
ALTER TABLE survey1_test_paradata ENGINE=MyIsam;
ALTER TABLE survey1_test_screendumps ENGINE=MyIsam;
ALTER TABLE survey1_test_states ENGINE=MyIsam;
ALTER TABLE survey1_test_datarecords ENGINE=MyIsam;

CREATE TABLE IF NOT EXISTS `survey1_consolidated_times` (

    `suid` int(11) NOT NULL DEFAULT '1',

    `primkey` varchar(150) NOT NULL,

    `begintime` varchar(50) NOT NULL,

    `variable` varchar(50) NOT NULL,

    `timespent` int(11) NOT NULL DEFAULT '0',

    `language` int(11) NOT NULL DEFAULT '1',

    `mode` int(11) NOT NULL DEFAULT '1',

    `version` int(11) NOT NULL DEFAULT '1',

    `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`suid`,`primkey`,`begintime`,`variable`)
) ENGINE=MyIsam  DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_times` ADD `stateid` INT( 11 ) NULL DEFAULT NULL AFTER `primkey` ;
ALTER TABLE `survey1_test_times` ADD `stateid` INT( 11 ) NULL DEFAULT NULL AFTER `primkey` ;
ALTER TABLE `survey1_consolidated_times` ADD `stateid` INT( 11 ) NULL DEFAULT NULL AFTER `primkey` ;

ALTER TABLE `survey1_screens` ADD `outerwhilergids` VARCHAR( 150 ) NULL DEFAULT NULL AFTER `outerlooprgids` ;

CREATE TABLE IF NOT EXISTS `survey1_test_consolidated_times` (

    `suid` int(11) NOT NULL DEFAULT '1',

    `primkey` varchar(150) NOT NULL,

    `begintime` varchar(50) NOT NULL,

    `stateid` int(11) DEFAULT NULL,  

    `variable` varchar(50) NOT NULL,

    `timespent` int(11) NOT NULL DEFAULT '0',

    `language` int(11) NOT NULL DEFAULT '1',

    `mode` int(11) NOT NULL DEFAULT '1',

    `version` int(11) NOT NULL DEFAULT '1',

    `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`suid`,`primkey`,`begintime`,`variable`)
) ENGINE=MyIsam  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_processed_paradata` (

  `pid` int(11) NOT NULL,

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `rgid` int(11) NOT NULL DEFAULT '0',

  `variablename` varchar(150) NOT NULL,

  `answer` blob,

  `language` int(11) NOT NULL,

  `mode` int(11) NOT NULL,

  `version` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`pid`, `suid`, `primkey`,`variablename`),
  KEY `primkeyindex` (`suid`,`primkey`),   
  KEY `variablenameindex` (`suid`,`variablename`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_test_processed_paradata` (

  `pid` int(11) NOT NULL,

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `rgid` int(11) NOT NULL DEFAULT '0',

  `variablename` varchar(150) NOT NULL,

  `answer` blob,

  `language` int(11) NOT NULL,

  `mode` int(11) NOT NULL,

  `version` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`pid`, `suid`, `primkey`,`variablename`),
  KEY `primkeyindex` (`suid`,`primkey`),   
  KEY `variablenameindex` (`suid`,`variablename`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;

ALTER TABLE `survey1_context` ADD `checks` BLOB NULL DEFAULT NULL AFTER `setfills` ;

ALTER TABLE `survey1_times` ADD `timespent2` INT(11) NULL DEFAULT NULL AFTER `timespent`;

ALTER TABLE `survey1_times` ADD `begintime2` TIMESTAMP NULL DEFAULT NULL AFTER `begintime`;

ALTER TABLE `survey1_times` ADD `endtime2` TIMESTAMP NULL DEFAULT NULL AFTER `endtime`;

ALTER TABLE `survey1_test_times` ADD `timespent2` INT(11) NULL DEFAULT NULL AFTER `timespent`;

ALTER TABLE `survey1_test_times` ADD `begintime2` TIMESTAMP NULL DEFAULT NULL AFTER `begintime`;

ALTER TABLE `survey1_test_times` ADD `endtime2` TIMESTAMP NULL DEFAULT NULL AFTER `endtime`;