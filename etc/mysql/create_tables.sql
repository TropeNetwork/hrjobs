CREATE TABLE location (
  location_id INTEGER UNSIGNED NOT NULL,
  parent_location_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(80) NULL,
  location_type ENUM('region','country','state','city','zip') NULL,
  PRIMARY KEY(location_id),
  INDEX location_FKIndex1(parent_location_id),
  FOREIGN KEY(parent_location_id)
    REFERENCES location(location_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE organization_group (
  group_id INTEGER UNSIGNED NOT NULL,
  group_name VARCHAR(45) NULL,
  disabled BOOL NULL DEFAULT 0,
  PRIMARY KEY(group_id)
);

CREATE TABLE profession (
  profession_id INTEGER UNSIGNED NOT NULL,
  parent_profession_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(80) NULL,
  PRIMARY KEY(profession_id),
  INDEX profession_FKIndex1(parent_profession_id),
  FOREIGN KEY(parent_profession_id)
    REFERENCES profession(profession_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE industry (
  industry_id INTEGER UNSIGNED NOT NULL,
  parent_industry_id INTEGER UNSIGNED NOT NULL,
  name VARCHAR(80) NULL,
  PRIMARY KEY(industry_id),
  INDEX industry_FKIndex1(parent_industry_id),
  FOREIGN KEY(parent_industry_id)
    REFERENCES industry(industry_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE organization (
  org_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  organization_group_id INTEGER UNSIGNED NOT NULL,
  org_name VARCHAR(255) NULL,
  website VARCHAR(255) NULL,
  org_description BLOB NULL,
  logo_file_name VARCHAR(45) NULL,
  PRIMARY KEY(org_id),
  INDEX organization_FKIndex1(organization_group_id),
  FOREIGN KEY(organization_group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE organization_user (
  organization_user_id INTEGER UNSIGNED NOT NULL,
  organization_group_id INTEGER UNSIGNED NOT NULL,
  is_group_admin BOOL NULL,
  PRIMARY KEY(organization_user_id),
  INDEX organization_user_FKIndex1(organization_group_id),
  FOREIGN KEY(organization_group_id)
    REFERENCES organization_group(group_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE contact (
  contact_id INTEGER UNSIGNED NOT NULL,
  organization_org_id INTEGER UNSIGNED NOT NULL,
  given_name VARCHAR(45) NULL,
  family_name VARCHAR(45) NULL,
  email VARCHAR(255) NULL,
  phone_intlcode VARCHAR(10) NULL,
  phone_areacode VARCHAR(10) NULL,
  phone_number VARCHAR(20) NULL,
  phone_extention VARCHAR(10) NULL,
  fax_intlcode VARCHAR(10) NULL,
  fax_areacode VARCHAR(10) NULL,
  fax_number VARCHAR(20) NULL,
  fax_extention VARCHAR(10) NULL,
  PRIMARY KEY(contact_id),
  INDEX contact_FKIndex1(organization_org_id),
  FOREIGN KEY(organization_org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE postal_address (
  address_id INTEGER UNSIGNED NOT NULL,
  organization_org_id INTEGER UNSIGNED NOT NULL,
  street VARCHAR(45) NULL,
  building_number VARCHAR(20) NULL,
  postal_code VARCHAR(20) NULL,
  country_code VARCHAR(20) NULL,
  address VARCHAR(45) NULL,
  region VARCHAR(45) NULL,
  PRIMARY KEY(address_id),
  INDEX postal_address_FKIndex1(organization_org_id),
  FOREIGN KEY(organization_org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE organization_industries (
  industry_id INTEGER UNSIGNED NOT NULL,
  org_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(industry_id, org_id),
  INDEX organization_has_industry_FKIndex1(org_id),
  INDEX organization_has_industry_FKIndex2(industry_id),
  FOREIGN KEY(org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(industry_id)
    REFERENCES industry(industry_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE job_posting (
  job_id INTEGER UNSIGNED NOT NULL,
  organization_org_id INTEGER UNSIGNED NOT NULL,
  job_title VARCHAR(255) NULL,
  job_description BLOB NULL,
  job_requirements BLOB NULL,
  start_date DATE NULL,
  end_date DATE NULL,
  apply_contact_id INTEGER UNSIGNED NULL,
  apply_by_email BOOL NULL,
  apply_by_web BOOL NULL,
  apply_by_web_url VARCHAR(255) NULL,
  job_status ENUM('active','inactive','deleted') NULL,
  is_template BOOL NULL,
  stylesheet VARCHAR(45) NULL,
  PRIMARY KEY(job_id),
  INDEX job_posting_FKIndex1(organization_org_id),
  INDEX job_posting_FKIndex2(apply_contact_id),
  FOREIGN KEY(organization_org_id)
    REFERENCES organization(org_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(apply_contact_id)
    REFERENCES contact(contact_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE job_professions (
  profession_id INTEGER UNSIGNED NOT NULL,
  job_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(profession_id, job_id),
  INDEX job_posting_has_profession_FKIndex1(job_id),
  INDEX job_posting_has_profession_FKIndex2(profession_id),
  FOREIGN KEY(job_id)
    REFERENCES job_posting(job_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(profession_id)
    REFERENCES profession(profession_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

CREATE TABLE job_locations (
  location_id INTEGER UNSIGNED NOT NULL,
  job_id INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY(location_id, job_id),
  INDEX job_posting_has_location_FKIndex1(job_id),
  INDEX job_posting_has_location_FKIndex2(location_id),
  FOREIGN KEY(job_id)
    REFERENCES job_posting(job_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION,
  FOREIGN KEY(location_id)
    REFERENCES location(location_id)
      ON DELETE NO ACTION
      ON UPDATE NO ACTION
);

