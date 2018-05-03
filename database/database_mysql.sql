CREATE TABLE IF NOT EXISTS `theory_case_studies` (
  `casestudyno` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cssituation` longtext,
  `type` varchar(50) DEFAULT NULL,
  `dsacat` tinyint(3) UNSIGNED DEFAULT '0',
  `lp` tinyint(3) UNSIGNED DEFAULT '0',
  `free` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`casestudyno`),
  UNIQUE KEY `casestudyno` (`casestudyno`),
  KEY `dsacat` (`dsacat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `theory_dsa_sections` (
  `section` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `free` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`section`),
  UNIQUE KEY `dsacat` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `theory_hc_sections` (
  `section` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `free` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`section`),
  UNIQUE KEY `hcsection` (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `theory_l2d_sections` (
  `section` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `free` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`section`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `theory_questions_2016` (
  `prim` smallint(6) UNSIGNED NOT NULL DEFAULT '0',
  `dsacat` tinyint(3) UNSIGNED DEFAULT NULL,
  `dsaqposition` smallint(6) UNSIGNED DEFAULT NULL,
  `ldclessonno` tinyint(3) UNSIGNED DEFAULT NULL,
  `ldcqno` smallint(6) UNSIGNED DEFAULT NULL,
  `hcsection` tinyint(3) UNSIGNED DEFAULT NULL,
  `hcqposition` smallint(6) UNSIGNED DEFAULT NULL,
  `hcrule1` int(11) UNSIGNED DEFAULT NULL,
  `hcrule2` int(11) UNSIGNED DEFAULT NULL,
  `hcrule3` int(11) UNSIGNED DEFAULT NULL,
  `question` varchar(255) DEFAULT NULL,
  `mark` tinyint(3) UNSIGNED DEFAULT NULL,
  `tickamount` tinyint(3) UNSIGNED DEFAULT NULL,
  `option1` varchar(255) DEFAULT NULL,
  `option2` varchar(255) DEFAULT NULL,
  `option3` varchar(255) DEFAULT NULL,
  `option4` varchar(255) DEFAULT NULL,
  `option5` varchar(255) DEFAULT NULL,
  `option6` varchar(255) DEFAULT NULL,
  `answer1` varchar(1) DEFAULT NULL,
  `answer2` varchar(1) DEFAULT NULL,
  `answer3` varchar(1) DEFAULT NULL,
  `answer4` varchar(1) DEFAULT NULL,
  `answer5` varchar(1) DEFAULT NULL,
  `answer6` varchar(1) DEFAULT NULL,
  `answerletters` varchar(5) DEFAULT NULL,
  `type` varchar(5) DEFAULT NULL,
  `carquestion` varchar(1) DEFAULT NULL,
  `bikequestion` varchar(1) DEFAULT NULL,
  `dsaimageid` tinyint(1) DEFAULT NULL,
  `excludesni` varchar(5) DEFAULT NULL,
  `format` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `explanation` longtext,
  `dsaexplanation` longtext,
  `casestudyno` smallint(5) UNSIGNED DEFAULT NULL,
  `caseqposition` int(11) UNSIGNED DEFAULT NULL,
  `alertcasestudy` smallint(6) DEFAULT NULL,
  `mocktestcarno` smallint(6) DEFAULT NULL,
  `mocktestcarqposition` smallint(6) DEFAULT NULL,
  `mocktestbikeno` smallint(6) DEFAULT NULL,
  `mocktestbikeqposition` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`prim`),
  UNIQUE KEY `prim` (`prim`),
  KEY `dsacat` (`dsacat`),
  KEY `ldclesssonno` (`ldclessonno`),
  KEY `hcsection` (`hcsection`),
  KEY `mocktestcarno` (`mocktestcarno`),
  KEY `carquestion` (`carquestion`),
  KEY `bikequestion` (`bikequestion`),
  KEY `mocktestbikeno` (`mocktestbikeno`),
  KEY `casestudyno` (`casestudyno`),
  KEY `hcrule1` (`hcrule1`),
  KEY `hcrule2` (`hcrule2`),
  KEY `hcrule3` (`hcrule3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users_progress` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `progress` longtext NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_test_progress` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `questions` text NOT NULL,
  `answers` text NOT NULL,
  `results` text,
  `test_id` int(11) NOT NULL,
  `question_no` int(11) NOT NULL DEFAULT '1',
  `started` datetime NOT NULL,
  `complete` datetime DEFAULT NULL,
  `time_remaining` varchar(10) DEFAULT NULL,
  `time_taken` varchar(10) DEFAULT NULL,
  `totalscore` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `type` varchar(5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `test_id` (`test_id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `users`
ADD `first_name` VARCHAR(50) NOT NULL AFTER `id`,
ADD `last_name` VARCHAR(50) NOT NULL AFTER `first_name`,
ADD `settings` TEXT NULL DEFAULT NULL AFTER `isactive`;

--
-- Constraints
--

ALTER TABLE `theory_case_studies`
  ADD CONSTRAINT `theory_case_studies_ibfk_1` FOREIGN KEY (`dsacat`) REFERENCES `theory_dsa_sections` (`section`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `theory_questions_2016`
  ADD CONSTRAINT `theory_questions_2016_ibfk_1` FOREIGN KEY (`casestudyno`) REFERENCES `theory_case_studies` (`casestudyno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `theory_questions_2016_ibfk_2` FOREIGN KEY (`dsacat`) REFERENCES `theory_dsa_sections` (`section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `theory_questions_2016_ibfk_3` FOREIGN KEY (`hcsection`) REFERENCES `theory_hc_sections` (`section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `theory_questions_2016_ibfk_4` FOREIGN KEY (`ldclessonno`) REFERENCES `theory_l2d_sections` (`section`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `theory_questions_2016_ibfk_5` FOREIGN KEY (`hcrule1`) REFERENCES `highway_code` (`hcno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `theory_questions_2016_ibfk_6` FOREIGN KEY (`hcrule2`) REFERENCES `highway_code` (`hcno`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `theory_questions_2016_ibfk_7` FOREIGN KEY (`hcrule3`) REFERENCES `highway_code` (`hcno`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users_progress`
  ADD CONSTRAINT `users_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `users_test_progress`
  ADD CONSTRAINT `users_test_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;