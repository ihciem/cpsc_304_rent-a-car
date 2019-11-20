drop table if exists branch cascade;
drop table if exists vehicleType cascade;
drop table if exists vehicle cascade;
drop table if exists customer cascade;
drop table if exists reservation cascade;
drop table if exists rentals cascade;
drop table if exists creditCard cascade;
drop table if exists return cascade;
drop table if exists timePeriod cascade;

--
-- Now, add each table.
--

create table branch (
	location varchar2(50),
	city varchar2(20),
	primary key(location, city)
);

create table vehicleType (
	vtname varchar2(20) primary key,
	features varchar2(20) not null,
	wrate float not null,
	hrate float not null,
	wirate float not null,
	dirate float not null,
	hirate float not null,
	krate float not null
);

create table vehicle(
	vid integer not null,
	vlicense varchar2(20) primary key,
	make varchar2(20) not null,
	model varchar2(20) not null,
	color varchar2(20) not null,
	odometer varchar2(20) not null,
	status varchar2(20) not null,
	vtname varchar2(20) not null,
	location varchar2(50) not null,
	city varchar2(20) not null,
	foreign key (vtname) references vehicleType(vtname),
	foreign key (location) references branch(location),
	foreign key (city) references branch(city)
);

create table customer(
	dlicense varchar2(20) PRIMARY KEY,
	name varchar2(20) not null,
	address varchar2(50) not null,
	cellphone integer not null,
);

create table reservation(
	confno varchar2(50) PRIMARY KEY
	rentid varchar2(20) not null,
	vtname varchar2(20) not null,
	dlicense varchar2(20) not null,
	fromdate date not null,
	fromtime time not null,
	todate date not null,
	totime time not null,
	foreign key (rentid) references rentals(rentid),
	foreign key (vtname) references vehicleType(vtname),
	foreign key (dlicense) references customer(dlicense),
	foreign key (fromdate) references timePeriod(fromdate),
	foreign key (fromtime) references timePeriod(fromtime),
	foreign key (todate) references timePeriod(todate),
	foreign key (totime) references timePeriod(totime)
);

create table rentals (
	rentid varchar2(20) PRIMARY KEY,
	cardno integer not null,
	odometer float not null,
	vid integer not null,
	fromdate date not null,
	fromtime time not null,
	todate date not null,
	totime time not null,
	dlicense varchar2(20) not null,
	foreign key (cardno) references creditCard(cardno),
	foreign key (vid) references vehicle(vid),
	foreign key (fromdate) references timePeriod(fromdate),
	foreign key (fromtime) references timePeriod(fromtime),
	foreign key (todate) references timePeriod(todate),
	foreign key (totime) references timePeriod(totime),
	foreign key (dlicense) references customer(dlicense)
);

create table creditCard (
	cardno integer PRIMARY KEY,
	cardname varchar(30) not null,
	expdate date not null
);

create table return (
	rentid varchar2(20) PRIMARY KEY,
	date Date not null,
	time time not null,
	odometer float not null,
	fulltank varchar2(20) not null,
	value float not null,
	foreign key (rentid) references rentals(rentid)
);

create table timePeriod (
	fromdate date,
	fromtime time,
	todate date,
	totime time,
	primary key (fromdate, formtime, todate, totime)
);

insert into branch values('123 Cordova Street', 'Vancouver');
insert into branch values('435 W. 41 Ave.', 'Vancouver');
insert into branch values('264 W. 67 Ave.', 'Vancouver');
insert into branch values('2329 West Mall', 'Vancouver');
insert into branch values('8888 University Dr.', 'Burnaby');
insert into branch values('123 Bay Street', 'Toronto');
insert into branch values('123 Wall Street', 'New York City');

insert into vehicleType values('Economy', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Compact', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Mid-size', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Standard', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Full-size', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('SUV', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Truck', 'N/A', 300, 10, 100, 15, 5);

insert into vehicle values(0001, '123ABC', 'Toyota', 'Camry', 'red', '50000', 'available', 'Truck', '123 Cordova Street', 'Vancouver');
insert into vehicle values(0002, '124ABC', 'Toyota', 'Camry', 'red', '51243', 'available', 'Truck', '123 Cordova Street', 'Vancouver');
insert into vehicle values(0003, '125ABC', 'Toyota', 'Camry', 'blue', '45630', 'available', 'Truck', '435 W. 41 Ave.', 'Vancouver');
insert into vehicle values(0004, '456ABD', 'Toyota', 'Corolla', 'gold', '45344', 'available', 'Standard', '435 W. 41 Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'gold', '45344', 'available', 'Standard', '264 W. 67 Ave.', 'Vancouver');
insert into vehicle values(0006, 'HELLO', 'Honda', 'Civic', 'purple', '25345', 'rented', 'Standard', '264 W. 67 Ave.', 'Vancouver');
insert into vehicle values(0007, '789AND', 'Honda', 'Avancier', 'dark', '45323', 'available', 'Standard', '2329 West Mall', 'Vancouver');
insert into vehicle values(0008, 'AMRICH', 'Mercedes', 'GLS', 'gold', '45344', 'available', 'SUV', '2329 West Mall', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'gold', '45344', 'rented', 'Standard', '264 W. 67 Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'gold', '45344', 'maintenance', 'Standard', '264 W. 67 Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'gold', '45344', 'available', 'Standard', '264 W. 67 Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'gold', '45344', 'available', 'Standard', '264 W. 67 Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'gold', '45344', 'available', 'Standard', '264 W. 67 Ave.', 'Vancouver');

SET FOREIGN_KEY_CHECKS=1;
