


CREATE TABLE IF NOT EXISTS `survey1_actions` (

  `asid` int(11) NOT NULL AUTO_INCREMENT,

  `sessionid` varchar(50) NOT NULL,

  `suid` int(11) DEFAULT NULL,

  `primkey` varchar(150) DEFAULT NULL,

  `urid` int(11) DEFAULT NULL,

  `ipaddress` varchar(50) NOT NULL,

  `systemtype` int(11) NOT NULL,

  `actiontype` int(11) NOT NULL,

  `action` varchar(250) NOT NULL,

  `params` blob,

  `language` int(11) DEFAULT NULL,

  `mode` int(11) DEFAULT NULL,

  `version` int(11) DEFAULT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`asid`),
  KEY `actionindex` (`sessionid`),
  KEY `sessionindex` (`sessionid`,`systemtype`)
) ENGINE=MyIsam  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_communication` (
  `hnid` int(11) NOT NULL AUTO_INCREMENT,
  `urid` int(11) NOT NULL,
  `insertts` datetime NOT NULL,
  `received` int(11) NOT NULL DEFAULT '0',
  `sqlcode` blob NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`hnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_contacts` (

  `primkey` varchar(150) NOT NULL,

  `code` int(11) NOT NULL,

  `contactts` datetime NOT NULL,

  `proxy` int(11) NOT NULL DEFAULT '0',

  `proxyname` blob,

  `remark` blob,

  `event` varchar(250) DEFAULT NULL,

  `urid` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_context` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL,

  `settings` mediumblob,

  `variables` mediumblob,

  `sections` mediumblob,

  `types` mediumblob,

  `groups` mediumblob,

  `getfills` mediumblob,

  `setfills` mediumblob,

  `checks` mediumblob,

  `inlinefields` mediumblob,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`version`,`suid`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_consolidated_times` (

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



CREATE TABLE IF NOT EXISTS `survey1_data` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `variablename` varchar(150) NOT NULL,

  `answer` blob,

  `dirty` int(11) NOT NULL DEFAULT '0',

  `language` int(11) NOT NULL,

  `mode` int(11) NOT NULL,

  `version` int(11) NOT NULL,

  `completed` int(11) NOT NULL DEFAULT '0',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`suid`, `primkey`,`variablename`),

  KEY `variablenameindex` (`suid`,`variablename`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_datarecords` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `datanames` blob,
  `data` mediumblob,
  `completed` int(11) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`,`primkey`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_engines` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL,

  `engine` blob,

  `instructions` blob,

  `progressbar` blob,

  `setfills` blob,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`version`,`suid`,`seid`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_error_paradata` (
  `epid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `code` varchar(150) NOT NULL,
  `rgid` int(11) NOT NULL DEFAULT '0',
  `variablename` varchar(150) NOT NULL,
  `answer` blob,
  `language` int(11) NOT NULL,
  `mode` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`epid`),
  KEY `primkeyindex` (`suid`,`primkey`),
  KEY `variablenameindex` (`suid`,`variablename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_files` (
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


CREATE TABLE IF NOT EXISTS `survey1_groups` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `gid` int(11) NOT NULL AUTO_INCREMENT,

  `name` varchar(150) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`gid`,`suid`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_households` (
  `primkey` varchar(150) NOT NULL,
  `urid` int(11) NOT NULL DEFAULT '0',
  `name` blob,
  `hhhead` varchar(50) DEFAULT NULL,
  `puid` int(11) NOT NULL DEFAULT '-1',
  `address1` blob,
  `address2` blob,
  `city` blob,
  `zip` blob,
  `state` blob,
  `longitude` blob,
  `latitude` blob,
  `telephone1` blob,
  `telephone2` blob,
  `email` blob,
  `status` int(11) NOT NULL DEFAULT '0',
  `test` int(11) NOT NULL DEFAULT '0',
  `dummy1` blob,
  `dummy2` blob,
  `dummy3` blob,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_interviewstatus` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `mainseid` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`, `primkey`, `mainseid`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_issues` (
  `isid` int(11) NOT NULL AUTO_INCREMENT,
  `urid` int(11) NOT NULL DEFAULT '1',
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
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


CREATE TABLE IF NOT EXISTS `survey1_lab` (
  `primkey` varchar(150) CHARACTER SET utf8 NOT NULL,
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

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



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
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_next` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL DEFAULT '1',

  `fromrgid` int(11) NOT NULL,

  `torgid` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`fromrgid`,`suid`,`seid`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



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

) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_pictures` (
  `primkey` varchar(150) NOT NULL DEFAULT '',
  `variablename` varchar(125) NOT NULL DEFAULT '',
  `picture` longblob NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`,`variablename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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


CREATE TABLE IF NOT EXISTS `survey1_progressbars` (

  `suid` int(11) NOT NULL,

  `mainseid` int(11) NOT NULL,

  `seid` int(11) NOT NULL,

  `seidrgid` int(11) DEFAULT NULL,

  `rgid` int(11) NOT NULL,

  `number` int(11) NOT NULL,

  `loopstring` varchar(150) NOT NULL DEFAULT '',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`suid`,`seid`,`rgid`,`number`,`loopstring`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_psus` (
  `puid` int(11) NOT NULL,
  `code` varchar(25) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `region` varchar(150) DEFAULT NULL,
  `state` varchar(150) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`puid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_remarks` (

  `primkey` varchar(150) NOT NULL,

  `remark` blob NOT NULL,

  `urid` int(11) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_respondents` (
  `primkey` varchar(150) NOT NULL,
  `hhid` varchar(25) NOT NULL,
  `hhorder` int(11) DEFAULT NULL,
  `logincode` blob,
  `urid` int(11) NOT NULL DEFAULT '0',
  `firstname` blob,
  `lastname` blob,
  `puid` int(11) NOT NULL DEFAULT '-1',
  `address1` blob,
  `address2` blob,
  `city` blob,
  `zip` blob,
  `state` blob,
  `longitude` blob,
  `latitude` blob,
  `telephone1` blob,
  `telephone2` blob,
  `email` blob,
  `status` int(11) NOT NULL DEFAULT '0',
  `test` int(11) NOT NULL DEFAULT '0',
  `selected` int(11) NOT NULL DEFAULT '0',
  `present` int(11) NOT NULL DEFAULT '0',
  `awigenid` varchar(150) DEFAULT NULL,
  `hhhead` int(11) NOT NULL DEFAULT '0',
  `finr` int(11) NOT NULL DEFAULT '0',
  `famr` int(11) NOT NULL DEFAULT '0',
  `covr` int(11) NOT NULL DEFAULT '0',
  `permanent` int(11) NOT NULL DEFAULT '1',
  `sex` blob,
  `age` blob,
  `birthdate` blob,
  `schoolingyears` int(11) DEFAULT NULL,
  `educationlevel` varchar(11) DEFAULT NULL,
  `occupationalstatus` int(11) DEFAULT NULL,
  `relationshiphh` varchar(11) DEFAULT NULL,
  `spouseprimkey` varchar(25) DEFAULT NULL,
  `consenttype` int(11) DEFAULT NULL,
  `movedout` int(11) NOT NULL DEFAULT '0',
  `validation` int(11) NOT NULL DEFAULT '0',
  `dummy1` blob,
  `dummy2` blob,
  `dummy3` blob,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `survey1_routing` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL DEFAULT '1',

  `rgid` int(11) NOT NULL AUTO_INCREMENT,

  `rule` text NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`rgid`,`suid`,`seid`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_screendumps` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `scdid` int(11) NOT NULL AUTO_INCREMENT,

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) NOT NULL DEFAULT '1',

  `screen` blob,

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`scdid`,`primkey`,`suid`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_screens` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL DEFAULT '1',

  `rgid` int(11) NOT NULL,

  `ifrgid` int(11) DEFAULT NULL,

  `number` int(11) NOT NULL,

  `section` int(11) DEFAULT '-1',

  `looptimes` int(11) NOT NULL DEFAULT '-1',

  `outerlooptimes` varchar(150) NOT NULL DEFAULT '-1',

  `outerlooprgids` varchar(150) DEFAULT NULL,

  `outerwhilergids` varchar(150) DEFAULT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  `dummy` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (`rgid`,`suid`,`seid`,`number`,`dummy`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_sections` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL AUTO_INCREMENT,

  `pid` int(11) NOT NULL DEFAULT '0',

  `name` varchar(150) NOT NULL,

  `position` int(11) DEFAULT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`seid`,`suid`)

) ENGINE=MyIsam  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_settings` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `object` int(11) NOT NULL DEFAULT '1',

  `objecttype` int(11) NOT NULL DEFAULT '1',

  `name` varchar(50) NOT NULL,

  `value` blob NOT NULL,

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`name`,`suid`,`object`,`language`,`mode`,`objecttype`),
  KEY `stateindex` (`suid`,`object`, objecttype)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_states` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `mainseid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL DEFAULT '1',

  `parentseid` int(11) NOT NULL DEFAULT '1',

  `parentrgid` int(11) NOT NULL DEFAULT '1',

  `prefix` varchar(150) DEFAULT NULL,

  `parentprefix` varchar(150) DEFAULT NULL,

  `stateid` int(11) NOT NULL,

  `primkey` varchar(150) NOT NULL,

  `displayed` text,

  `template` varchar(150) DEFAULT NULL,

  `looprgid` int(11) DEFAULT NULL,

  `loopstring` varchar(150) NOT NULL DEFAULT '',

  `looplastaction` varchar(50) NOT NULL DEFAULT '',

  `whilergid` int(11) DEFAULT NULL,

  `whilestring` varchar(150) NOT NULL DEFAULT '',

  `whilelastaction` varchar(50) NOT NULL DEFAULT '',

  `assigned` text,

  `rgid` int(11) NOT NULL,

  `data` mediumblob,

  `assignments` blob,

  `fills` blob,

  `subdisplays` blob,

  `inlinefields` blob,

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`stateid`,`primkey`,`suid`,`seid`,`mainseid`),
  KEY `stateindex` (`suid`,`primkey`),
  KEY `primindex` (`primkey`),
  KEY `mainseidindex` (`suid`,`primkey`,`mainseid`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_surveys` (

  `suid` int(11) NOT NULL AUTO_INCREMENT,

  `name` varchar(150) NOT NULL,

  `description` text,

  `position` int(11) DEFAULT 1,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`suid`)

) ENGINE=MyIsam  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_test_actions` (

  `asid` int(11) NOT NULL AUTO_INCREMENT,

  `sessionid` varchar(50) NOT NULL,

  `suid` int(11) DEFAULT NULL,

  `primkey` varchar(150) DEFAULT NULL,

  `urid` int(11) DEFAULT NULL,

  `ipaddress` varchar(50) NOT NULL,

  `systemtype` int(11) NOT NULL,

  `actiontype` int(11) NOT NULL,

  `action` varchar(250) NOT NULL,

  `params` blob,

  `language` int(11) DEFAULT NULL,

  `mode` int(11) DEFAULT NULL,

  `version` int(11) DEFAULT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`asid`),
  KEY `actionindex` (`sessionid`),
  KEY `sessionindex` (`sessionid`,`systemtype`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_test_data` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `primkey` varchar(150) NOT NULL,

  `variablename` varchar(150) NOT NULL,

  `answer` blob,

  `dirty` int(11) NOT NULL DEFAULT '0',

  `language` int(11) NOT NULL,

  `mode` int(11) NOT NULL,

  `version` int(11) NOT NULL,

  `completed` int(11) NOT NULL DEFAULT '0',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`suid`,`primkey`,`variablename`),
  KEY `variablenameindex` (`suid`,`variablename`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_test_datarecords` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `datanames` blob,
  `data` mediumblob,
  `completed` int(11) DEFAULT '0',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`,`primkey`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `survey1_test_error_paradata` (
  `epid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `code` varchar(150) NOT NULL,
  `rgid` int(11) NOT NULL DEFAULT '0',
  `variablename` varchar(150) NOT NULL,
  `answer` blob,
  `language` int(11) NOT NULL,
  `mode` int(11) NOT NULL,
  `version` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`epid`),
  KEY `primkeyindex` (`suid`,`primkey`),
  KEY `variablenameindex` (`suid`,`variablename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `survey1_test_interviewstatus` (
  `suid` int(11) NOT NULL DEFAULT '1',
  `primkey` varchar(150) NOT NULL,
  `mainseid` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`suid`, `primkey`, `mainseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_test_lab` (
  `primkey` varchar(150) CHARACTER SET utf8 NOT NULL,
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

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



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
) ENGINE=MyIsam DEFAULT CHARSET=utf8;



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

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_test_pictures` (
  `primkey` varchar(150) NOT NULL DEFAULT '',
  `variablename` varchar(125) NOT NULL DEFAULT '',
  `picture` longblob NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`primkey`,`variablename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `survey1_test_screendumps` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `scdid` int(11) NOT NULL AUTO_INCREMENT,

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) NOT NULL DEFAULT '1',

  `screen` blob,

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`scdid`,`primkey`,`suid`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_test_states` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `mainseid` int(11) NOT NULL DEFAULT '1',

  `seid` int(11) NOT NULL DEFAULT '1',

  `parentseid` int(11) NOT NULL DEFAULT '1',

  `parentrgid` int(11) NOT NULL DEFAULT '1',

  `prefix` varchar(150) DEFAULT NULL,

  `parentprefix` varchar(150) DEFAULT NULL,

  `stateid` int(11) NOT NULL,

  `primkey` varchar(150) NOT NULL,

  `displayed` text,

  `template` varchar(150) DEFAULT NULL,

  `looprgid` int(11) DEFAULT NULL,

  `loopstring` varchar(150) NOT NULL DEFAULT '',

  `looplastaction` varchar(50) NOT NULL DEFAULT '',

  `whilergid` int(11) DEFAULT NULL,

  `whilelastaction` varchar(50) NOT NULL DEFAULT '',

  `assigned` text,

  `rgid` int(11) NOT NULL,

  `data` mediumblob,

  `assignments` blob,

  `fills` blob,

  `subdisplays` blob,

  `inlinefields` blob,

   `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`stateid`,`primkey`,`suid`,`seid`,`mainseid`),
  KEY `stateindex` (`suid`,`primkey`),
  KEY `primindex` (`primkey`),
  KEY `mainseidindex` (`suid`,`primkey`,`mainseid`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `survey1_test_times` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `tmid` int(11) NOT NULL AUTO_INCREMENT,

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) DEFAULT NULL,  

  `rgid` int(11) DEFAULT NULL,

  `variable` varchar(50) NOT NULL,

  `begintime` timestamp NULL DEFAULT NULL,

  `begintime2` timestamp NULL DEFAULT NULL,

  `endtime` timestamp NULL DEFAULT NULL,

  `endtime2` timestamp NULL DEFAULT NULL,

  `timespent` int(11) NOT NULL DEFAULT '0',

  `timespent2` int(11) NOT NULL DEFAULT '0',

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`tmid`,`suid`,`primkey`)

) ENGINE=MyIsam DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_times` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `tmid` int(11) NOT NULL AUTO_INCREMENT,

  `primkey` varchar(150) NOT NULL,

  `stateid` int(11) DEFAULT NULL,  

  `rgid` int(11) DEFAULT NULL,  

  `variable` varchar(50) NOT NULL,

  `begintime` timestamp NULL DEFAULT NULL,

  `begintime2` timestamp NULL DEFAULT NULL,

  `endtime` timestamp NULL DEFAULT NULL,

  `endtime2` timestamp NULL DEFAULT NULL,

  `timespent` int(11) NOT NULL DEFAULT '0',

  `timespent2` int(11) NOT NULL DEFAULT '0',

  `language` int(11) NOT NULL DEFAULT '1',

  `mode` int(11) NOT NULL DEFAULT '1',

  `version` int(11) NOT NULL DEFAULT '1',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`tmid`,`suid`,`primkey`)

) ENGINE=MyIsam  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_tracks` (
  `trid` int(11) NOT NULL AUTO_INCREMENT,
  `urid` int(11) NOT NULL DEFAULT '1',
  `suid` int(11) NOT NULL DEFAULT '1',
  `object` int(11) DEFAULT NULL,
  `objecttype` int(11) DEFAULT '1',
  `setting` varchar(150) DEFAULT NULL,
  `value` blob,
  `mode` int(11) DEFAULT NULL,
  `language` int(11) DEFAULT NULL,
  `version` int(11) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`trid`,`suid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_types` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `tyd` int(11) NOT NULL AUTO_INCREMENT,

  `name` varchar(150) NOT NULL,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`tyd`,`suid`)

) ENGINE=MyIsam  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_users` (
  `urid` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `name` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` blob NOT NULL,
  `usertype` int(11) NOT NULL DEFAULT '0',
  `usersubtype` int(11) NOT NULL DEFAULT '0',
  `sup` int(11) DEFAULT NULL,
  `filter` int(11) NOT NULL DEFAULT '1',
  `regionfilter` int(11) NOT NULL DEFAULT '0',
  `testmode` int(11) NOT NULL DEFAULT '0',
  `communication` int(11) NOT NULL DEFAULT '2',
  `settings` blob,
  `access` blob NULL DEFAULT NULL,
  `lastdata` DATETIME NULL DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`urid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_variables` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `vsid` int(11) NOT NULL AUTO_INCREMENT,

  `seid` int(11) NOT NULL DEFAULT '1',

  `variablename` varchar(150) NOT NULL,

  `position` int(11) DEFAULT NULL,

  `tyd` int(11) NOT NULL DEFAULT '0',

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`vsid`,`suid`),
  
  KEY `variableindex` (`suid`,`variablename`)

) ENGINE=MyIsam  DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `survey1_versions` (

  `suid` int(11) NOT NULL DEFAULT '1',

  `vnid` int(11) NOT NULL,

  `name` varchar(50) NOT NULL,

  `description` text,

  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`vnid`,`suid`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


