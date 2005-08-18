



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
CREATE TABLE organization_group (
  group_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  group_name VARCHAR(45) NOT NULL,
  disabled TINYINT(1) NOT NULL,
  export_key VARCHAR(32) NULL,
  PRIMARY KEY(group_id)
)
TYPE=InnoDB;

CREATE TABLE organization (
  org_id INTEGER(10) UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  org_name VARCHAR(255) NOT NULL,
  website VARCHAR(255) NOT NULL,
  org_description BLOB NOT NULL,
  logo_file_name VARCHAR(45) NOT NULL,
  enable_export BOOL NULL,
  PRIMARY KEY(org_id),
  INDEX group_id(group_id),
  FOREIGN KEY(group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE organization_user (
  organization_user_id INTEGER(10) UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  is_group_admin TINYINT(1) NOT NULL,
  PRIMARY KEY(organization_user_id),
  INDEX org_user_group_id(group_id),
  FOREIGN KEY(group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE profession (
  profession_id INTEGER(10) UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  PRIMARY KEY(profession_id),
  INDEX profession_group_id(group_id),
  FOREIGN KEY(group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE postal_address (
  address_id INTEGER(10) UNSIGNED NOT NULL,
  org_id INTEGER(10) UNSIGNED NOT NULL,
  street VARCHAR(45) NOT NULL,
  building_number VARCHAR(20) NOT NULL,
  postal_code VARCHAR(20) NOT NULL,
  country_code VARCHAR(20) NOT NULL,
  address VARCHAR(45) NOT NULL,
  region VARCHAR(45) NOT NULL,
  PRIMARY KEY(address_id),
  INDEX postal_address_org_id(org_id),
  FOREIGN KEY(org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE location (
  location_id INTEGER(10) UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  location_type ENUM('region','country','state','city','zip') NOT NULL,
  PRIMARY KEY(location_id),
  INDEX location_group_id(group_id),
  FOREIGN KEY(group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE industry (
  industry_id INTEGER(10) UNSIGNED NOT NULL,
  group_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(80) NOT NULL,
  PRIMARY KEY(industry_id),
  INDEX industry_group_id(group_id),
  FOREIGN KEY(group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE contact (
  contact_id INTEGER(10) UNSIGNED NOT NULL,
  org_id INTEGER(10) UNSIGNED NOT NULL,
  given_name VARCHAR(45) NOT NULL,
  family_name VARCHAR(45) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone_intlcode VARCHAR(10) NOT NULL,
  phone_areacode VARCHAR(10) NOT NULL,
  phone_number VARCHAR(20) NOT NULL,
  phone_extention VARCHAR(10) NOT NULL,
  fax_intlcode VARCHAR(10) NOT NULL,
  fax_areacode VARCHAR(10) NOT NULL,
  fax_number VARCHAR(20) NOT NULL,
  fax_extention VARCHAR(10) NOT NULL,
  PRIMARY KEY(contact_id),
  INDEX contact_org_id(org_id),
  FOREIGN KEY(org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE job_posting (
  job_id INTEGER(10) UNSIGNED NOT NULL,
  job_reference VARCHAR(30) NULL,
  org_id INTEGER(10) UNSIGNED NOT NULL,
  job_title VARCHAR(255) NOT NULL,
  job_description BLOB NULL,
  job_requirements BLOB NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  apply_contact_id INTEGER(10) UNSIGNED NULL,
  apply_by_email TINYINT(1) NULL,
  apply_by_web TINYINT(1) NULL,
  apply_by_web_url VARCHAR(255) NULL,
  job_status ENUM('active','inactive','deleted') NULL,
  is_template TINYINT(1) NULL,
  stylesheet VARCHAR(45) NULL,
  PRIMARY KEY(job_id),
  INDEX job_org_id(org_id),
  INDEX job_contact_id(apply_contact_id),
  FOREIGN KEY(org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(apply_contact_id)
    REFERENCES contact(contact_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE organization_industries (
  industry_id INTEGER(10) UNSIGNED NOT NULL,
  org_id INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY(industry_id, org_id),
  INDEX organization_industries_org_id(org_id),
  INDEX organization_industries_industry_id(industry_id),
  FOREIGN KEY(industry_id)
    REFERENCES industry(industry_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE job_locations (
  job_id INTEGER(10) UNSIGNED NOT NULL,
  location_id INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY(job_id, location_id),
  INDEX job_posting_job_id(job_id),
  INDEX job_locations_location_id(location_id),
  FOREIGN KEY(job_id)
    REFERENCES job_posting(job_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(location_id)
    REFERENCES location(location_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

CREATE TABLE job_professions (
  profession_id INTEGER(10) UNSIGNED NOT NULL,
  job_id INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY(profession_id, job_id),
  INDEX job_profession_job_id(job_id),
  INDEX job_profession_profession_id(profession_id),
  FOREIGN KEY(job_id)
    REFERENCES job_posting(job_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(profession_id)
    REFERENCES profession(profession_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
)
TYPE=InnoDB;

