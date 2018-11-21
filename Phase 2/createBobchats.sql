CREATE TABLE groups (
	group_id int not null, 
	group_name varchar(90) not null, 
	cat_id int);

CREATE TABLE user (
	user_id int not null, 
	first_name varchar(90) not null, 
	last_name varchar(90) not null);

CREATE TABLE user_group (
	user_group_id int not null, 
	user_id int not null, 
	group_id int not null);

CREATE TABLE message (
	msg_id int not null, 
	subject varchar(90), 
	message clob not null,
	creator_id int not null,
	create_date date not null,
	parent_msg_id int,
	attach_id int);

CREATE TABLE msg_recipient (
	msg_rec_id int not null, 
	recipient_id int not null, 
	recipient_group_id int,
	msg_id int not null);

CREATE TABLE msg_flair (
	flair_id int not null, 
	flair varchar(90) not null);

CREATE TABLE attachment (
	attach_id int not null, 
	attach_msg varchar(255) not null);

CREATE TABLE category (
	cat_id int not null, 
	c_name varchar(90) not null);
