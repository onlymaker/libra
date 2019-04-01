CREATE DATABASE libra DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'libra'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON libra.* to 'libra'@'%';
FLUSH PRIVILEGES;

drop table if exists customer;
create table customer
(
  id          int unsigned not null auto_increment primary key,
  email       varchar(50)  not null,
  ranking     int unsigned not null,
  status      tinyint(1) unsigned not null default 0,
  code        varchar(50)  not null,
  data        json,
  create_time timestamp default current_timestamp,
  unique index (email)
);

drop table if exists  admin;
create table admin
(
  id          int unsigned not null auto_increment primary key,
  username    varchar(50)  not null,
  password    varchar(50)  not null,
  create_time timestamp default current_timestamp,
  unique index (username)
);
#insert into admin (username, password) values ('username', md5('password'));
