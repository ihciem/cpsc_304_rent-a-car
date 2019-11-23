drop table branch cascade constraints;
drop table vehicleType cascade constraints;
drop table vehicle cascade constraints;
drop table customer cascade constraints;
drop table rental cascade constraints;
drop table reservation cascade constraints;
drop table return cascade constraints;


--
-- Now, add each table.
--

create table branch(
	location varchar2(50),
	city varchar2(20),
	primary key(location, city)
);

create table vehicleType(
	vtname varchar2(20) primary key,
	features varchar2(20) not null,
	wrate float not null,
	drate float not null,
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
	foreign key (location, city) references branch(location, city)
);

create table customer(
	dlicense varchar2(20) PRIMARY KEY,
	name varchar2(20) not null,
	address varchar2(50) not null,
	cellphone integer not null
);

create table reservation(
	confno varchar2(50) PRIMARY KEY,
	vtname varchar2(20) not null,
	dlicense varchar2(20) not null,
	fromdt timestamp(0) not null,
	todt timestamp(0) not null,
	foreign key (vtname) references vehicleType(vtname),
	foreign key (dlicense) references customer(dlicense)
);

create table rental(
	rentid varchar2(20) PRIMARY KEY,
	cardno integer not null,
	odometer float not null,
	vlicense varchar2(20) not null,
	fromdt timestamp(0) not null,
	todt timestamp(0) not null,
	dlicense varchar2(20) not null,
	confno varchar2(50) null,
	foreign key (confno) references reservation(confno),
	foreign key (vlicense) references vehicle(vlicense),
	foreign key (dlicense) references customer(dlicense)
);

create table return(
	rentid varchar2(20) PRIMARY KEY,
	returndt timestamp(0) not null,
	odometer float not null,
	fulltank varchar2(20) not null,
	value float not null,
	foreign key (rentid) references rental(rentid)
);
--

insert into branch values('123 Cordova St.', 'Vancouver');
insert into branch values('435 W. 41st Ave.', 'Vancouver');
insert into branch values('264 W. 67th Ave.', 'Vancouver');
insert into branch values('2329 West Mall', 'Vancouver');
insert into branch values('555 Seymour St.', 'Vancouver');
insert into branch values('8888 University Dr.', 'Burnaby');
insert into branch values('3700 Willingdon Ave.', 'Burnaby');
insert into branch values('123 Bay St.', 'Toronto');
insert into branch values('598 Bay St.', 'Toronto');
insert into branch values('27 Kings College Circle', 'Toronto');
insert into branch values('100 Wall St.', 'New York City');

insert into vehicleType values('Economy', 'N/A', 300, 50, 10, 100, 15, 5, 10);
insert into vehicleType values('Compact', 'N/A', 350, 55, 15, 110, 20, 6, 10);
insert into vehicleType values('Mid-size', 'N/A', 400, 60, 20, 120, 25, 7, 10);
insert into vehicleType values('Standard', 'N/A', 450, 65, 25, 130, 30, 8, 10);
insert into vehicleType values('Full-size', 'N/A', 500, 70, 30, 140, 40, 9, 10);
insert into vehicleType values('SUV', 'N/A', 500, 70, 30, 140, 40, 9, 10);
insert into vehicleType values('Truck', 'N/A', 600, 80, 40, 150, 50, 10, 10);

insert into vehicle values(0001, '123ABC', 'Toyota', 'Camry', 'red', '50000', 'available', 'Economy', '123 Cordova St.', 'Vancouver');
insert into vehicle values(0002, '124ABC', 'Toyota', 'Avalon', 'red', '51243', 'available', 'Full-size', '123 Cordova St.', 'Vancouver');
insert into vehicle values(0003, '125ABC', 'Toyota', 'Tundra', 'blue', '456300', 'available', 'Truck', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0024, '126ABC', 'Toyota', 'Tundra', 'hotpink', '456300', 'available', 'Truck', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0005, '789AND', 'Toyota', 'Corolla', 'green', '45344', 'available', 'Mid-size', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0007, '789NWD', 'Honda', 'Avancier', 'dark', '45323', 'available', 'Standard', '2329 West Mall', 'Vancouver');
insert into vehicle values(0021, '890NWD', 'Honda', 'Avancier', 'light', '45323', 'available', 'Standard', '2329 West Mall', 'Vancouver');
insert into vehicle values(0008, '456HEY', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '2329 West Mall', 'Vancouver');
insert into vehicle values(0014, '123HEY', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '555 Seymour St.', 'Vancouver');
insert into vehicle values(0015, '234HEY', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '555 Seymour St.', 'Vancouver');
insert into vehicle values(0016, '345HEY', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '3700 Willingdon Ave.', 'Burnaby');
insert into vehicle values(0017, '567HEY', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '3700 Willingdon Ave.', 'Burnaby');
insert into vehicle values(0025, '678HEY', 'Mercedes', 'GLS', 'black', '75634', 'available', 'SUV', '555 Seymour St.', 'Vancouver');
insert into vehicle values(0011, 'XXORXX', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '123 Bay St.', 'Toronto');
insert into vehicle values(0018, 'XXABXX', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '598 Bay St.', 'Toronto');
insert into vehicle values(0019, 'XXBCXX', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '27 Kings College Circle', 'Toronto');
insert into vehicle values(0020, 'XXCDXX', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '27 Kings College Circle', 'Toronto');
insert into vehicle values(0013, '12AB56', 'Honda', 'Civic', 'pink', '490823', 'available', 'Compact', '100 Wall St.', 'New York City');
insert into vehicle values(0006, 'HELLOO', 'Honda', 'Civic', 'purple', '45345', 'available', 'Compact', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0004, '456ABD', 'Toyota', 'Corolla', 'gold', '45344', 'rented', 'Mid-size', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0009, '143ILY', 'Mercedes', 'GLS', 'silver', '423533', 'rented', 'SUV', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0022, '143IHY', 'Mercedes', 'GLS', 'pink', '423533', 'rented', 'SUV', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0023, '88YU88', 'Mercedes', 'C-Class', 'white', '23534', 'rented', 'Compact', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0010, '88ME88', 'Mercedes', 'C-Class', 'white', '23534', 'maintenance', 'Compact', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0012, '123456', 'Honda', 'Civic', 'white', '43566', 'maintenance', 'Compact', '100 Wall St.', 'New York City');


insert into customer values('VA00001', 'John Doe', '1234 W. 1st Ave.', 6041234567);
insert into customer values('VA00002', 'Jane Doe', '1234 W. 1st Ave.', 6044358798);
insert into customer values('VA00003', 'Jill Katrina', '456 Main St.', 6043409583);
insert into customer values('VA00004', 'Helen Smith', '980 Cresent Dr.', 7782459876);
insert into customer values('VA00005', 'Paul Krumann', '897 W. 45th Ave.', 7782763495);
insert into customer values('VA00006', 'Chloe Li', '145 Leona Rd.', 6049872345);
insert into customer values('VA00007', 'Angela Baby', '435 W. 41st Ave.', 6043253434);
insert into customer values('VA00008', 'Steven Li', '435 W. 41st Ave.', 6041234568);
insert into customer values('VA00009', 'Kevin Wong', '435 W. 41st Ave.', 6041234569);
insert into customer values('VA00010', 'Never Gonna', 'Give You Up Ave.', 6041234570);
insert into customer values('VA00011', 'OK Boomer', 'Millenials St.', 6041234571);
insert into customer values('VA00012', 'Much Data', 'Such Tedious Dr.', 6041234572);
insert into customer values('VA00013', 'Such Tedious', 'Much Wow Dr.', 6041234573);
insert into customer values('VA00014', 'Hi Hungry', 'Im Dad Rd.', 6041234574);
insert into customer values('VA00015', 'Harry Potter', 'Number 12 Grimmauld Pl.', 6041234575);
insert into customer values('VA00016', 'Dudley Dursley', 'Number 4 Privet Dr.', 6041234576);
insert into customer values('VA00017', 'Bilbo Baggins', 'Bag End Bagshot Row', 6041234577);

-- rented and returned
insert into reservation values('RES0000000001', 'Compact', 'VA00001', TO_TIMESTAMP('2019/11/10 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000002', 'Compact', 'VA00002', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000003', 'Truck', 'VA00003', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000005', 'Compact', 'VA00005', TO_TIMESTAMP('2019/11/05 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000006', 'Compact', 'VA00006', TO_TIMESTAMP('2019/11/01 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000007', 'Standard', 'VA00007', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000009', 'SUV', 'VA00009', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000010', 'SUV', 'VA00010', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000011', 'Compact', 'VA00011', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000012', 'Mid-size', 'VA00012', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));

-- has not started yet
insert into reservation values('RES0000000013', 'SUV', 'VA00013', TO_TIMESTAMP('2019/12/15 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- rented, not yet returned
insert into reservation values('RES0000000004', 'Mid-size', 'VA00004',  TO_TIMESTAMP('2019/11/23 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/15 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000008', 'SUV', 'VA00008', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000015', 'SUV', 'VA00015', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0000000014', 'Compact', 'VA00014', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/21 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));


insert into rental values('R0000000001', 1123453343325454, 45344, 'XXORXX', TO_TIMESTAMP('2019/11/10 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00001', 'RES0000000001');
insert into rental values('R0000000002', 1234454656344564, 423533, 'XXABXX', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00002', 'RES0000000002');
insert into rental values('R0000000003', 2334243545623445, 206300, '125ABC', TO_TIMESTAMP('2019/11/03 09:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00003', 'RES0000000003');
insert into rental values('R0000000004', 1234567890123456, 25345, '456ABD', TO_TIMESTAMP('2019/11/23 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/15 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00004', 'RES0000000004');
insert into rental values('R0000000005', 1234454656344564, 423533, 'XXBCXX', TO_TIMESTAMP('2019/11/05 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00005', 'RES0000000005');
insert into rental values('R0000000006', 1234454656344564, 423533, 'XXCDXX', TO_TIMESTAMP('2019/11/01 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00006', 'RES0000000006');
insert into rental values('R0000000007', 1234454656344564, 423533, '789NWD', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00007', 'RES0000000007');
insert into rental values('R0000000008', 1234567890123456, 25345, '143ILY', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00008', 'RES0000000008');
insert into rental values('R0000000009', 1234454656344564, 423533, '123HEY', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00009', 'RES0000000009');
insert into rental values('R0000000010', 1234454656344564, 423533, '456HEY', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00010', 'RES0000000010');
insert into rental values('R0000000011', 1234454656344564, 423533, '12AB56', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00011', 'RES0000000011');
insert into rental values('R0000000012', 1234454656344564, 423533, '789AND', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00012', 'RES0000000012');
insert into rental values('R0000000014', 1234454656344564, 423533, '88YU88', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00014', 'RES0000000014');
insert into rental values('R0000000015', 1234454656344564, 423533, '143IHY', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00015', 'RES0000000015');


insert into return values('R0000000003', TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 300);
insert into return values('R0000000002', TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 300);
insert into return values('R0000000005', TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 8000);
insert into return values('R0000000007', TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
insert into return values('R0000000006', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 850);
insert into return values('R0000000001', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 1000);
insert into return values('R0000000012', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
insert into return values('R0000000009', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
insert into return values('R0000000010', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 810);
insert into return values('R0000000011', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 230);

-- insert into reservation values('RES0000000001', 'Compact', 'VA00001', TO_TIMESTAMP('2019/11/10 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000002', 'Compact', 'VA00002', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000003', 'Truck', 'VA00003', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000005', 'Compact', 'VA00005', TO_TIMESTAMP('2019/11/05 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000006', 'Compact', 'VA00006', TO_TIMESTAMP('2019/11/01 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000007', 'Standard', 'VA00007', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000009', 'SUV', 'VA00009', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000010', 'SUV', 'VA00010', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000011', 'Compact', 'VA00011', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000012', 'Mid-size', 'VA00012', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- -- has not started yet
-- insert into reservation values('RES0000000013', 'SUV', 'VA00013', TO_TIMESTAMP('2019/12/15 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- -- rented, not yet returned
-- insert into reservation values('RES0000000004', 'Mid-size', 'VA00004',  TO_TIMESTAMP('2019/11/28 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/15 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- insert into reservation values('RES0000000008', 'SUV', 'VA00008', TO_TIMESTAMP('2019/11/28 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
--
-- insert into rental values('R0000000001', 1123453343325454, 45344, 'XXORXX', TO_TIMESTAMP('2019/11/10 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00001', 'RES0000000001');
-- insert into rental values('R0000000002', 1234454656344564, 423533, 'XXABXX', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00002', 'RES0000000002');
-- insert into rental values('R0000000003', 2334243545623445, 206300, '125ABC', TO_TIMESTAMP('2019/11/03 09:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00003', 'RES0000000003');
-- insert into rental values('R0000000004', 1234567890123456, 25345, '456ABD', TO_TIMESTAMP('2019/11/28 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/15 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00004', 'RES0000000004');
-- insert into rental values('R0000000005', 1234454656344564, 423533, 'XXBCXX', TO_TIMESTAMP('2019/11/05 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00005', 'RES0000000005');
-- insert into rental values('R0000000006', 1234454656344564, 423533, 'XXCDXX', TO_TIMESTAMP('2019/11/01 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00006', 'RES0000000006');
-- insert into rental values('R0000000007', 1234454656344564, 423533, '789NWD', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00007', 'RES0000000007');
-- insert into rental values('R0000000008', 1234567890123456, 25345, '143ILY', TO_TIMESTAMP('2019/11/28 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00008', 'RES0000000008');
-- insert into rental values('R0000000009', 1234454656344564, 423533, '123HEY', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00009', 'RES0000000009');
-- insert into rental values('R0000000010', 1234454656344564, 423533, '456HEY', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00010', 'RES0000000010');
-- insert into rental values('R0000000011', 1234454656344564, 423533, '12AB56', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00011', 'RES0000000011');
-- insert into rental values('R0000000012', 1234454656344564, 423533, '789AND', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00012', 'RES0000000012');

-- insert into return values('R0000000003', TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 300);
-- insert into return values('R0000000002', TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 300);
-- insert into return values('R0000000005', TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 8000);
-- insert into return values('R0000000007', TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
-- insert into return values('R0000000006', TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 850);
-- insert into return values('R0000000001', TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 1000);
-- insert into return values('R0000000012', TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
-- insert into return values('R0000000009', TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
-- insert into return values('R0000000010', TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 810);
-- insert into return values('R0000000011', TO_TIMESTAMP('2019/11/28 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 230);
