
--
-- Table structure for table `liveuser_applications`
--

DROP TABLE IF EXISTS `liveuser_applications`;
CREATE TABLE `liveuser_applications` (
  `application_id` int(11) NOT NULL default '0',
  `application_define_name` char(32) NOT NULL default '',
  KEY `application_id` (`application_id`),
  KEY `application_define_name` (`application_define_name`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_area_admin_areas`
--

DROP TABLE IF EXISTS `liveuser_area_admin_areas`;
CREATE TABLE `liveuser_area_admin_areas` (
  `area_id` int(11) NOT NULL default '0',
  `perm_user_id` int(11) NOT NULL default '0',
  KEY `area_admin_area_rel` (`area_id`,`perm_user_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_areas`
--

DROP TABLE IF EXISTS `liveuser_areas`;
CREATE TABLE `liveuser_areas` (
  `area_id` int(11) NOT NULL default '0',
  `application_id` int(11) NOT NULL default '0',
  `area_define_name` char(32) NOT NULL default '',
  KEY `area_id` (`area_id`),
  KEY `area_define_name` (`application_id`,`area_define_name`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_group_subgroups`
--

DROP TABLE IF EXISTS `liveuser_group_subgroups`;
CREATE TABLE `liveuser_group_subgroups` (
  `group_id` int(11) NOT NULL default '0',
  `subgroup_id` int(11) NOT NULL default '0',
  KEY `group_subgroup_rel` (`group_id`,`subgroup_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_grouprights`
--

DROP TABLE IF EXISTS `liveuser_grouprights`;
CREATE TABLE `liveuser_grouprights` (
  `group_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  KEY `group_right_rel` (`group_id`,`right_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_groups`
--

DROP TABLE IF EXISTS `liveuser_groups`;
CREATE TABLE `liveuser_groups` (
  `group_id` int(11) NOT NULL default '0',
  `group_type` int(11) NOT NULL default '1',
  `group_define_name` char(32) NOT NULL default '',
  `owner_user_id` int(11) NOT NULL default '0',
  `owner_group_id` int(11) NOT NULL default '0',
  `is_active` char(1) NOT NULL default 'Y',
  KEY `group_id` (`group_id`),
  KEY `group_define_name` (`group_define_name`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_groupusers`
--

DROP TABLE IF EXISTS `liveuser_groupusers`;
CREATE TABLE `liveuser_groupusers` (
  `perm_user_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  KEY `perm_user_group_rel` (`perm_user_id`,`group_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_perm_users`
--

DROP TABLE IF EXISTS `liveuser_perm_users`;
CREATE TABLE `liveuser_perm_users` (
  `perm_user_id` int(11) NOT NULL default '0',
  `auth_user_id` char(32) NOT NULL default '',
  `perm_type` int(11) NOT NULL default '0',
  `auth_container_name` char(32) NOT NULL default '',
  KEY `perm_user_id` (`perm_user_id`),
  KEY `auth_user_container_rel` (`auth_user_id`,`auth_container_name`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_right_implied`
--

DROP TABLE IF EXISTS `liveuser_right_implied`;
CREATE TABLE `liveuser_right_implied` (
  `right_id` int(11) NOT NULL default '0',
  `implied_right_id` int(11) NOT NULL default '0',
  KEY `right_implied_right_rel` (`right_id`,`implied_right_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_rights`
--

DROP TABLE IF EXISTS `liveuser_rights`;
CREATE TABLE `liveuser_rights` (
  `right_id` int(11) NOT NULL default '0',
  `area_id` int(11) NOT NULL default '0',
  `right_define_name` char(32) NOT NULL default '',
  `has_implied` char(1) NOT NULL default 'Y',
  KEY `right_id` (`right_id`),
  KEY `right_define_name` (`area_id`,`right_define_name`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_translations`
--

DROP TABLE IF EXISTS `liveuser_translations`;
CREATE TABLE `liveuser_translations` (
  `translation_id` int(11) NOT NULL default '0',
  `section_id` int(11) NOT NULL default '0',
  `section_type` int(11) NOT NULL default '0',
  `language_id` char(2) NOT NULL default '',
  `name` char(50) NOT NULL default '',
  `description` char(255) NOT NULL default '',
  KEY `translation_id` (`translation_id`),
  KEY `section_item` (`section_id`,`section_type`,`language_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_userrights`
--

DROP TABLE IF EXISTS `liveuser_userrights`;
CREATE TABLE `liveuser_userrights` (
  `perm_user_id` int(11) NOT NULL default '0',
  `right_id` int(11) NOT NULL default '0',
  `right_level` int(11) NOT NULL default '3',
  KEY `perm_user_right_rel` (`perm_user_id`,`right_id`)
) TYPE=INNODB;

--
-- Table structure for table `liveuser_users`
--

DROP TABLE IF EXISTS `liveuser_users`;
CREATE TABLE `liveuser_users` (
  `auth_user_id` char(32) NOT NULL default '',
  `handle` char(32) NOT NULL default '',
  `passwd` char(32) NOT NULL default '',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `owner_user_id` int(11) NOT NULL default '0',
  `owner_group_id` int(11) NOT NULL default '0',
  `is_active` char(1) NOT NULL default 'Y',
  `name` char(100) NOT NULL default '',
  `email` char(100) NOT NULL default '',
  KEY `auth_user_id` (`auth_user_id`),
  KEY `handle` (`handle`)
) TYPE=INNODB;

-----------------------
-- HRJOBS TABLES ------
-----------------------

DROP TABLE IF EXISTS `address_seq`;
DROP TABLE IF EXISTS `contact_seq`;
DROP TABLE IF EXISTS `group_seq`;
DROP TABLE IF EXISTS `job_seq`;
DROP TABLE IF EXISTS `organization_seq`;


DROP TABLE IF EXISTS `organization_industries`;
DROP TABLE IF EXISTS `job_professions`;
DROP TABLE IF EXISTS `job_locations`;
DROP TABLE IF EXISTS `location`;
DROP TABLE IF EXISTS `industry`;
DROP TABLE IF EXISTS `profession`;
DROP TABLE IF EXISTS `job_posting`;
DROP TABLE IF EXISTS `contact`;
DROP TABLE IF EXISTS `organization_user`;
DROP TABLE IF EXISTS `postal_address`;
DROP TABLE IF EXISTS `organization`;
DROP TABLE IF EXISTS `organization_group`;

CREATE TABLE `organization_group` (
  `group_id` int(10) unsigned NOT NULL default '0',
  `group_name` varchar(45) default NULL,
  `disabled` tinyint(1) default '0',
  PRIMARY KEY  (`group_id`)
) TYPE=INNODB;


CREATE TABLE `organization` (
  `org_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '0',
  `org_name` varchar(255) default NULL,
  `website` varchar(255) default NULL,
  `org_description` blob,
  `logo_file_name` varchar(45) default NULL,
  PRIMARY KEY  (`org_id`),
  INDEX `group_id` (`group_id`),
  FOREIGN KEY (`group_id`) REFERENCES `organization_group`(`group_id`)
) TYPE=INNODB;


CREATE TABLE `postal_address` (
  `address_id` int(10) unsigned NOT NULL default '0',
  `org_id` int(10) unsigned NOT NULL default '0',
  `street` varchar(45) default NULL,
  `building_number` varchar(20) default NULL,
  `postal_code` varchar(20) default NULL,
  `country_code` varchar(20) default NULL,
  `address` varchar(45) default NULL,
  `region` varchar(45) default NULL,
  PRIMARY KEY  (`address_id`),
  INDEX `postal_address_org_id` (`org_id`),
  FOREIGN KEY (`org_id`) REFERENCES `organization`(`org_id`)
) TYPE=INNODB;

CREATE TABLE `organization_user` (
  `organization_user_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  `is_group_admin` tinyint(1) default NULL,
  PRIMARY KEY  (`organization_user_id`),
  INDEX `org_user_group_id` (`group_id`),
  FOREIGN KEY (`group_id`) REFERENCES `organization_group`(`group_id`)
) TYPE=INNODB;

CREATE TABLE `contact` (
  `contact_id` int(10) unsigned NOT NULL default '0',
  `org_id` int(10) unsigned NOT NULL default '0',
  `given_name` varchar(45) default NULL,
  `family_name` varchar(45) default NULL,
  `email` varchar(255) default NULL,
  `phone_intlcode` varchar(10) default NULL,
  `phone_areacode` varchar(10) default NULL,
  `phone_number` varchar(20) default NULL,
  `phone_extention` varchar(10) default NULL,
  `fax_intlcode` varchar(10) default NULL,
  `fax_areacode` varchar(10) default NULL,
  `fax_number` varchar(20) default NULL,
  `fax_extention` varchar(10) default NULL,
  PRIMARY KEY  (`contact_id`),
  INDEX `contact_org_id` (`org_id`),
  FOREIGN KEY (`org_id`) REFERENCES `organization`(`org_id`)
) TYPE=INNODB;


CREATE TABLE `job_posting` (
  `job_id` int(10) unsigned NOT NULL default '0',
  `org_id` int(10) unsigned NOT NULL default '0',
  `job_title` varchar(255) default NULL,
  `job_description` blob,
  `job_requirements` blob,
  `start_date` date default NULL,
  `end_date` date default NULL,
  `apply_contact_id` int(10) unsigned default NULL,
  `apply_by_email` tinyint(1) default NULL,
  `apply_by_web` tinyint(1) default NULL,
  `apply_by_web_url` varchar(255) default NULL,
  `job_status` enum('active','inactive','deleted') default NULL,
  `is_template` tinyint(1) default NULL,
  `stylesheet` varchar(45) default NULL,
  PRIMARY KEY  (`job_id`),
  INDEX `job_org_id` (`org_id`),
  INDEX `job_contact_id` (`apply_contact_id`),
  FOREIGN KEY (`org_id`) REFERENCES `organization`(`org_id`),
  FOREIGN KEY (`apply_contact_id`) REFERENCES `contact`(`contact_id`)
) TYPE=INNODB;


CREATE TABLE `profession` (
  `group_id` int(10) unsigned NOT NULL default '0',
  `profession_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(80) default NULL,
  PRIMARY KEY  (`group_id`, `profession_id`),
  INDEX `profession_group_id` (`group_id`),
  FOREIGN KEY (`group_id`) REFERENCES `organization_group`(`group_id`)
) TYPE=INNODB;


CREATE TABLE `industry` (
  `group_id` int(10) unsigned NOT NULL default '0',
  `industry_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(80) default NULL,
  PRIMARY KEY  (`group_id`, `industry_id`),
  INDEX `industry_group_id` (`group_id`),
  FOREIGN KEY (`group_id`) REFERENCES `organization_group`(`group_id`)
) TYPE=INNODB;


CREATE TABLE `location` (
  `group_id` int(10) unsigned NOT NULL default '0',
  `location_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(80) default NULL,
  `location_type` enum('region','country','state','city','zip') default NULL,
  PRIMARY KEY  (`group_id`, `location_id`),
  INDEX `location_group_id` (`group_id`),
  FOREIGN KEY (`group_id`) REFERENCES `organization_group`(`group_id`)
) TYPE=INNODB;


CREATE TABLE `job_locations` (
  `location_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  `job_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`location_id`,`group_id`,`job_id`),
  INDEX `job_posting_job_id` (`job_id`),
  INDEX `job_posting_location_id` (`group_id`,`location_id`),
  FOREIGN KEY (`job_id`) REFERENCES `organization_group`(`group_id`),
  FOREIGN KEY (`group_id`,`location_id`) REFERENCES `location`(`group_id`,`location_id`)
) TYPE=INNODB;


CREATE TABLE `job_professions` (
  `profession_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  `job_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`profession_id`,`job_id`),
  INDEX `job_profession_job_id` (`job_id`),
  INDEX `job_profession_profession_id` (`group_id`,`profession_id`),
  FOREIGN KEY (`job_id`) REFERENCES `organization_group`(`group_id`),
  FOREIGN KEY (`group_id`,`profession_id`) REFERENCES `profession`(`group_id`,`profession_id`)
) TYPE=INNODB;


CREATE TABLE `organization_industries` (
  `industry_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  `org_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`industry_id`,`group_id`,`org_id`),
  INDEX `organization_industries_org_id` (`org_id`),
  INDEX `organization_industries_industry_id` (`group_id`,`industry_id`),
  FOREIGN KEY (`org_id`) REFERENCES `organization_group`(`group_id`),
  FOREIGN KEY (`group_id`,`industry_id`) REFERENCES `industry`(`group_id`,`industry_id`)
) TYPE=INNODB;

