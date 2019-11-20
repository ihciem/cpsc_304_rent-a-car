
drop table if exists branch;
drop table if exists vehicleType;
drop table if exists vehicle;
drop table if exists customer;
drop table if exists reservation;
drop table if exists rental
drop table if exists creditCard;
drop table if exists return;
drop table if exists timePeriod;

--
-- Now, add each table.
--

create table branch (
	location varchar2(50) not null,
	city varchar2(20) not null,
	PRIMARY KEY (location, city)
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
	foreign key (vtname) references vehicleType,
	foreign key (location, city) references branch
);

create table customer(
	dlicense varchar2(20) PRIMARY KEY,
	name varchar2(20) not null,
	address varchar2(50) not null,
	cellphone integer not null
);

create table timePeriod (
	fromdate date not null,
	fromtime time(0) not null,
	todate date not null,
	totime time(0) not null,
	PRIMARY KEY (fromdate, fromtime, todate, totime)
);

create table reservation(
	confno varchar2(50) PRIMARY KEY
	/*rentid varchar2(20),*/
	vtname varchar2(20) not null,
	dlicense varchar2(20) not null,
	fromdate date not null,
	fromtime time(0) not null,
	todate date not null,
	totime time(0) not null,
	/*foreign key (rentid) references rental,*/
	foreign key (vtname) references vehicleType,
	foreign key (dlicense) references customer,
	foreign key (fromdate, fromtime, todate, totime) references timePeriod
);

create table rental (
	rentid varchar2(20) PRIMARY KEY,
	confno varchar2(20),
	cardno integer not null,
	odometer float not null,
	vlicense varchar2(20) not null,
	fromdate date not null,
	fromtime time(0) not null,
	todate date not null,
	totime time(0) not null,
	dlicense varchar2(20) not null,
	/*foreign key (cardno) references creditCard,*/
	foreign key (confno) references reservation,
	foreign key (vlicense) references vehicle,
	foreign key (fromdate, fromtime, todate, totime) references timePeriod,
	foreign key (dlicense) references customer
);

/*
create table creditCard (
	cardno integer PRIMARY KEY,
	cardname varchar(30) not null,
	expdate date not null
);
*/

create table return (
	rentid varchar2(20) PRIMARY KEY,
	rdate date not null,
	rtime time(0) not null,
	odometer float not null,
	fulltank varchar2(20) not null,
	value float not null,
	foreign key (rentid) references rental
);



/*
insert into branch values('123 Cordova Street', 'Vancouver');
insert into branch values('435 W. 41st Ave.', 'Vancouver');
insert into branch values('264 W. 67th Ave.', 'Vancouver');
insert into branch values('2329 West Mall', 'Vancouver');
insert into branch values('8888 University Dr.', 'Burnaby');
insert into branch values('123 Bay Street', 'Toronto');
insert into branch values('100 Wall Street', 'New York City');

insert into vehicleType values('Economy', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Compact', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Mid-size', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Standard', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Full-size', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('SUV', 'N/A', 300, 10, 100, 15, 5);
insert into vehicleType values('Truck', 'N/A', 300, 10, 100, 15, 5);

insert into vehicle values(0001, '123ABC', 'Toyota', 'Camry', 'red', '50000', 'available', 'Economy', '123 Cordova Street', 'Vancouver');
insert into vehicle values(0002, '124ABC', 'Toyota', 'Avalon', 'red', '51243', 'available', 'Full-size', '123 Cordova Street', 'Vancouver');
insert into vehicle values(0003, '125ABC', 'Toyota', 'Tundra', 'blue', '456300', 'available', 'Truck', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'green', '45344', 'available', 'Mid-size', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0007, '789AND', 'Honda', 'Avancier', 'dark', '45323', 'available', 'Standard', '2329 West Mall', 'Vancouver');
insert into vehicle values(0008, '456HEY', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '2329 West Mall', 'Vancouver');
insert into vehicle values(0011, 'XXORXX', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '123 Bay Street', 'Toronto');
insert into vehicle values(0013, '12CD56', 'Honda', 'Civic', 'pink', '490823', 'available', 'Compact', '100 Wall Street', 'New York City');
insert into vehicle values(0004, '456ABD', 'Toyota', 'Corolla', 'gold', '45344', 'rented', 'Mid-size', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0006, 'HELLOO', 'Honda', 'Civic', 'purple', '45345', 'available', 'Compact', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0009, '143ILY', 'Mercedes', 'GLS', 'silver', '423533', 'rented', 'SUV', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0010, '88ME88', 'Mercedes', 'C-Class', 'white', '23534', 'maintenance', 'Compact', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0012, '123456', 'Honda', 'Civic', 'white', '43566', 'maintenance', 'Compact', '100 Wall Street', 'New York City');

insert into customer values('VA12345', 'John Doe', '1234 W. 1st Ave.', 6041234567);
insert into customer values('VA14566', 'Jane Doe', '1234 W. 1st Ave.', 6044358798);
insert into customer values('VA24553', 'Jill Katrina', '456 Main Street', 6043409583);
insert into customer values('VA23534', 'Helen Smith', '980 Cresent Dr.', 7782459876);
insert into customer values('VA92837', 'Paul Krumann', '897 W. 45th Ave.', 7782763495);
insert into customer values('VA09811', 'Chloe Li', '145 Leona Rd.', 6049872345);

insert into reservation values('RES1234567890', 'R1234567890', 'Compact', 'VA12345', date '2019-11-01', time '13:30', date '2019-11-11', time '16:45');
insert into reservation values('RES4543532454', NULL, 'Compact', 'VA14566', date '2019-11-01', time '13:23', date '2019-11-11', time '16:47');
insert into reservation values('RES1456542298', 'R1456542298', 'Truck', 'VA24553', date '2019-11-03', time '09:30', date '2019-11-15', time '13:21');
insert into reservation values('RES4564932499', 'R4564932499', 'Mid-size', 'VA92837', date '2019-11-30', time '10:53', date '2019-12-13', time '13:23');
insert into reservation values('RES2358793411', 'R2358793411', 'SUV', 'VA09811', date '2019-11-03', time '10:30', date '2019-11-15', time '12:57');

insert into rental values('R1234567890', 1234567890123456, 25345, 'HELLOO', date '2019-11-01', time '13:30', date '2019-11-11', time '16:30', 'VA12345');
insert into rental values('R1456542298', 2334243545623445, 206300, '125ABC', date '2019-11-03', time '09:30', date '2019-11-15', time '16:30', 'VA24553');
insert into rental values('R4564932499', 1123453343325454, 45344, '456ABD', date '2019-11-30', time '10:53', date '2019-12-13', time '16:30', 'VA92837');
insert into rental values('R2358793411', 1234454656344564, 423533, '143ILY', date '2019-11-03', time '10:30', date '2019-11-30', time '16:30', 'VA09811');

insert into creditCard values(1234567890123456, 'John Doe', date '2020-01-31');
insert into creditCard values(2334243545623445, 'Jill Katrina', date '2020-08-31');
insert into creditCard values(1123453343325454, 'Paul Krumann', date '2021-03-31');
insert into creditCard values(1234454656344564, 'Chloe Li', date '2020-11-30');

insert into returns values('R1234567890', date '2019-11-11', time '16:45', 45345, 'yes', 300);
insert into returns values('R1456542298', date '2019-11-15', time '14:25', 456300, 'yes', 800);

insert into timePeriod values(date '2019-11-01', time '13:30', date '2019-11-11', time '16:30');
insert into timePeriod values(date '2019-11-03', time '09:30', date '2019-11-15', time '16:30');
insert into timePeriod values(date '2019-11-30', time '10:53', date '2019-12-13', time '16:30');
insert into timePeriod values(date '2019-11-03', time '10:30', date '2019-11-30', time '16:30');

/*SET FOREIGN_KEY_CHECKS=1;
