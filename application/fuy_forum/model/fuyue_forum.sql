grant select,insert,update on fuyue_forum.* to 'change'@'localhost' IDENTIFIED BY '666666';
grant select on fuyue_forum.* to 'read'@'localhost' IDENTIFIED BY '87654321';
grant select,insert,update,delete on fuyue_forum.* to 'admin'@'localhost' IDENTIFIED BY '99999999';
grant select,insert,update on fuyue_forum.* to 'change2'@'localhost' IDENTIFIED BY '666666';

-- 创建数据库
create database fuyue_forum;
use fuyue_forum;
-- 创建user表---存储用户信息
create table fuyue_user(
	unick varchar(10) primary key not null,
	upa char(32) not null,
	uemail varchar(15) not null,
	utel varchar(15) not null
);
-- 创建save表---存储版块信息
create table fuyue_save(
	sid int primary key auto_increment not null,
	sname varchar(15) not null,
	sremark varchar(50) not null
);

-- 创建mes表---存储原贴信息
	create table fuyue_mes(
		mid int primary key auto_increment not null,
		mtitle varchar(30) not null,
		mcontent text not null,
		unick varchar(10) not null,
		mcreateat int not null,
		sid int not null,
		foreign key(sid) references fuyue_save(sid)
			on delete cascade
			on update cascade
	);
-- 创建respones表---存储回复信息
create table fuyue_res(
	rid int primary key auto_increment not null,
	rcontent text not null,
	unick varchar(10) not null,
	rcreateat int not null,
	mid int not null,
	
	foreign key(mid) references fuyue_mes(mid)
		on delete cascade
		on update cascade
);

insert into fuyue_res(unick,rcontent,mid)
	values("kyle",'akjhflkahflkajflkahlkf',1);

insert into fuyue_save(sname,sremark)
	values('ThinkPHP5','ThinkPHP5');
insert into fuyue_save(sname,sremark)
	values('ThinkPHP6','ThinkPHP6');
insert into fuyue_save(sname,sremark)
	values('网站开发','网站开发');
insert into fuyue_save(sname,sremark)
	values('开发手册','开发手册');
insert into fuyue_save(sname,sremark)
	values('水军','水军');

alter table fuyue_user add uimg varchar(50) not null;

create table fuyue_admin
(
	admin varchar(10) primary key not null,
	apa char(32) not null
);

insert into fuyue_admin
values("admin", "123456");

create  table fuyue_carousel(
	cid int primary key auto_increment not null,
	cimg varchar(50) not null,
	cback varchar(50) not null,
	ctitle varchar(50) not null,
	mid int not null,
	foreign key(mid) references fuyue_mes(mid)
		on delete cascade
		on update cascade
);

alter table fuyue_carousel add cpause varchar(5) not null;
alter table fuyue_carousel add color varchar(10) not null;

alter table fuyue_user add upower char(2) not null; 