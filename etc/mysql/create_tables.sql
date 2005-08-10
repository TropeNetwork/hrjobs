
DROP TABLE IF EXISTS liveuser_users;
DROP TABLE IF EXISTS liveuser_userrights;
DROP TABLE IF EXISTS liveuser_translations;
DROP TABLE IF EXISTS liveuser_right_implied;
DROP TABLE IF EXISTS liveuser_rights;
DROP TABLE IF EXISTS liveuser_perm_users;
DROP TABLE IF EXISTS liveuser_group_subgroups;
DROP TABLE IF EXISTS liveuser_groupusers;
DROP TABLE IF EXISTS liveuser_groups;
DROP TABLE IF EXISTS liveuser_grouprights;
DROP TABLE IF EXISTS liveuser_area_admin_areas;
DROP TABLE IF EXISTS liveuser_areas;
DROP TABLE IF EXISTS liveuser_applications;


CREATE TABLE liveuser_applications (
  application_id INTEGER(11) NOT NULL,
  application_define_name CHAR(32) NOT NULL,
  INDEX application_id(application_id),
  INDEX application_define_name(application_define_name)
)
TYPE=InnoDB;

CREATE TABLE liveuser_areas (
  area_id INTEGER(11) NOT NULL,
  application_id INTEGER(11) NOT NULL,
  area_define_name CHAR(32) NOT NULL,
  INDEX area_id(area_id),
  INDEX area_define_name(application_id, area_define_name)
)
TYPE=InnoDB;

CREATE TABLE liveuser_area_admin_areas (
  area_id INTEGER(11) NOT NULL,
  perm_user_id INTEGER(11) NOT NULL,
  INDEX area_admin_area_rel(area_id, perm_user_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_grouprights (
  group_id INTEGER(11) NOT NULL,
  right_id INTEGER(11) NOT NULL,
  right_level INTEGER(11) NOT NULL DEFAULT '3',
  INDEX group_right_rel(group_id, right_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_groups (
  group_id INTEGER(11) NOT NULL,
  group_type INTEGER(11) NOT NULL DEFAULT '1',
  group_define_name CHAR(32) NOT NULL,
  owner_user_id INTEGER(11) NOT NULL,
  owner_group_id INTEGER(11) NOT NULL,
  is_active CHAR(1) NOT NULL DEFAULT 'Y',
  INDEX group_id(group_id),
  INDEX group_define_name(group_define_name)
)
TYPE=InnoDB;

CREATE TABLE liveuser_groupusers (
  perm_user_id INTEGER(11) NOT NULL,
  group_id INTEGER(11) NOT NULL,
  INDEX perm_user_group_rel(perm_user_id, group_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_group_subgroups (
  group_id INTEGER(11) NOT NULL,
  subgroup_id INTEGER(11) NOT NULL,
  INDEX group_subgroup_rel(group_id, subgroup_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_perm_users (
  perm_user_id INTEGER(11) NOT NULL,
  auth_user_id CHAR(32) NOT NULL,
  perm_type INTEGER(11) NOT NULL,
  auth_container_name CHAR(32) NOT NULL,
  INDEX perm_user_id(perm_user_id),
  INDEX auth_user_container_rel(auth_user_id, auth_container_name)
)
TYPE=InnoDB;

CREATE TABLE liveuser_rights (
  right_id INTEGER(11) NOT NULL,
  area_id INTEGER(11) NOT NULL,
  right_define_name CHAR(32) NOT NULL,
  has_implied CHAR(1) NOT NULL DEFAULT 'Y',
  INDEX right_id(right_id),
  INDEX right_define_name(area_id, right_define_name)
)
TYPE=InnoDB;

CREATE TABLE liveuser_right_implied (
  right_id INTEGER(11) NOT NULL,
  implied_right_id INTEGER(11) NOT NULL,
  INDEX right_implied_right_rel(right_id, implied_right_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_translations (
  translation_id INTEGER(11) NOT NULL,
  section_id INTEGER(11) NOT NULL,
  section_type INTEGER(11) NOT NULL,
  language_id CHAR(2) NOT NULL,
  name CHAR(50) NOT NULL,
  description CHAR(255) NOT NULL,
  INDEX translation_id(translation_id),
  INDEX section_item(section_id, section_type, language_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_userrights (
  perm_user_id INTEGER(11) NOT NULL,
  right_id INTEGER(11) NOT NULL,
  right_level INTEGER(11) NOT NULL DEFAULT '3',
  INDEX perm_user_right_rel(perm_user_id, right_id)
)
TYPE=InnoDB;

CREATE TABLE liveuser_users (
  auth_user_id CHAR(32) NOT NULL,
  handle CHAR(32) NOT NULL,
  passwd CHAR(32) NOT NULL,
  lastlogin DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  owner_user_id INTEGER(11) NOT NULL,
  owner_group_id INTEGER(11) NOT NULL,
  is_active CHAR(1) NOT NULL DEFAULT 'Y',
  name CHAR(100) NOT NULL,
  email CHAR(100) NOT NULL,
  INDEX auth_user_id(auth_user_id),
  INDEX handle(handle)
)
TYPE=InnoDB;


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
  `job_reference` varchar(30) NOT NULL default '',
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

