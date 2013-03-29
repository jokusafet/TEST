# MySQL-Front Dump 1.16 beta
#
# Host: localhost Database: epro
#--------------------------------------------------------
# Server version 4.0.12-nt
#
# Table structure for table 'dentalis_licence'
#

#
# Table structure for table 'kontakti'
#

CREATE TABLE kontakti (
  id smallint(6) unsigned NOT NULL auto_increment,
  first varchar(25) NOT NULL DEFAULT '' ,
  middle varchar(25) ,
  last varchar(25) NOT NULL DEFAULT '' ,
  title varchar(12) ,
  email1 varchar(100) ,
  email2 varchar(100) ,
  email3 varchar(100) ,
  email_text_only enum('Y','N') DEFAULT 'N' ,
  home_street_addrese varchar(100) ,
  home_city varchar(50) ,
  home_state varchar(50) ,
  home_zip_code varchar(10) ,
  home_country varchar(50) ,
  home_web_page varchar(100) ,
  home_phone varchar(14) ,
  home_fax varchar(14) ,
  home_mobile varchar(14) ,
  business_company varchar(80) ,
  business_job_title varchar(60) ,
  business_department varchar(50) ,
  business_office varchar(50) ,
  business_street_addrese varchar(100) ,
  business_city varchar(50) ,
  business_state varchar(50) ,
  business_zip_code varchar(10) ,
  business_country varchar(50) ,
  business_web_page varchar(100) ,
  business_phone varchar(14) ,
  business_fax varchar(14) ,
  business_mobile varchar(14) ,
  business_fix_ip varchar(15) ,
  personal_spouse varchar(50) ,
  personal_children varchar(60) ,
  personal_gender enum('M','F','U') DEFAULT 'U' ,
  personal_birthday date ,
  personal_annaversary date ,
  note text ,
  messinger_netmeeting_server varchar(100) ,
  messinger_netmeeting_address varchar(100) ,
  messinger_icq varchar(8) ,
  messinger_yahoo varchar(20) ,
  messinger_msn varchar(20) ,
  messinger_skype varchar(20) ,
  pgp_digital_id text ,
  user_id smallint(6) DEFAULT '0' ,
  PRIMARY KEY (id),
  KEY id_2 (id),
  KEY prezime_ime (last),
  KEY prezime_ime (first),
  UNIQUE id (id)
);


#
# Table structure for table 'tbl_sess'
#

CREATE TABLE tbl_sess (
  id bigint(16) NOT NULL auto_increment,
  sess_key varchar(18) NOT NULL DEFAULT '' ,
  var text NOT NULL DEFAULT '' ,
  varname text NOT NULL DEFAULT '' ,
  exp bigint(20) NOT NULL DEFAULT '0' ,
  vartype tinyint(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (id),
  KEY sess_key (sess_key),
  KEY id (id),
  UNIQUE sess_key_2 (sess_key)
);


#
# Table structure for table 'user_groups'
#

CREATE TABLE user_groups (
  id tinyint(3) unsigned NOT NULL auto_increment,
  group_name varchar(12) NOT NULL DEFAULT '' ,
  opis varchar(30) ,
  PRIMARY KEY (id),
  KEY id_2 (id),
  UNIQUE id (id)
);


#
# Table structure for table 'users'
#

CREATE TABLE users (
  id smallint(6) unsigned NOT NULL auto_increment,
  username varchar(16) NOT NULL DEFAULT '' ,
  passwd varchar(32) NOT NULL DEFAULT '' ,
  user_group set('super_user','hw_admin','sw_admin','dentalis','gost','diler','kupac','SmB') DEFAULT 'gost' ,
  prezime_ime varchar(70) NOT NULL DEFAULT '' ,
  email varchar(100) ,
  PRIMARY KEY (id),
  KEY username_2 (username),
  UNIQUE username (username)
);