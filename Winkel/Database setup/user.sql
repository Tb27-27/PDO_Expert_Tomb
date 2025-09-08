create database winkel;
use winkel;
create table users (
id int auto_increment primary key,
username varchar(255),
email varchar(255),
password varchar(255)
);
