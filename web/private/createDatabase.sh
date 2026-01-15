#!/bin/bash

sqlite3 database.db <<EOD
drop table if exists users;
create table users(userId integer primary key autoincrement, username varchar(50), password varchar(50));
create unique index uniqueUsers on users (username);
insert into users values (1, "pepito", "d08234fdasjklñ3121");
insert into users values (2, "luis", "1234");
insert into users values (3, "marcos", "lfjh3284ljksdfa");
insert into users values (4, "lucas", "3244sadd2");
insert into users values (5, "eduardo", "lfjh3284ljksdfa");
insert into users values (6, "carlos", "fdsafyy54sd33");
insert into users values (7, "ana", "fds3434445");
insert into users values (8, "lorena", "fdasdfa43423");
insert into users values (9, "ignacio", "654f32as1321");
insert into users values (10, "maria", "thisisaverystrongpasswordimreallysure");

drop table if exists players;
create table players(playerid integer primary key autoincrement, name varchar(50), team varchar(50));
insert into players values (1, "Castolo", "Masterleague Default");
insert into players values (2, "El Moubarki", "Masterleague Default");
insert into players values (3, "Espimas", "Masterleague Default");
insert into players values (4, "Minanda", "Masterleague Classics");
insert into players values (5, "Iouga", "Benchmasters");

drop table if exists comments;
create table comments(commentId integer primary key autoincrement, playerId integer, userId integer, body text);
insert into comments values (1, 1,1, "Very good speed!");
insert into comments values (2, 1,3, "Age: 35, he is too old for us.");
insert into comments values (3, 2,9, "Very tall, 188cm, good header.");
insert into comments values (4, 2,4, "I don't like him.");
insert into comments values (5, 4,1, "Good at passing, but not at defending.");
insert into comments values (6, 5,7, "I don't like him.");
insert into comments values (7, 5,9, "I don't like him.");
insert into comments values (8, 3,7, "Good at defending, but too slow.");


EOD
