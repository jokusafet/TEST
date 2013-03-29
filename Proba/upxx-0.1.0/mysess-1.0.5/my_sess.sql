CREATE TABLE tbl_sess(

id BIGINT( 16 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
sess_key VARCHAR( 18 ) NOT NULL ,
var TEXT NOT NULL ,
varname TEXT NOT NULL ,
exp BIGINT( 20 ) NOT NULL ,
vartype TINYINT( 1 ) NOT NULL ,
PRIMARY KEY ( sess_key ) ,
INDEX ( id ) 
) 