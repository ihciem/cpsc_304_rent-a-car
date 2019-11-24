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
	constraint branch_pk primary key(location, city)
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
	vid integer not null unique,
	vlicense varchar2(20) primary key,
	make varchar2(20) not null,
	model varchar2(20) not null,
	color varchar2(20) not null,
	odometer varchar2(20) not null,
	status varchar2(20) not null ,
	vtname varchar2(20) not null,
	location varchar2(50) not null,
	city varchar2(20) not null,
	constraint vehicle_check_status check (status in ('available', 'rented', 'maintenance')),
	constraint vehicle_fk_vType foreign key (vtname) references vehicleType(vtname) ON DELETE CASCADE,
	constraint vehicle_fk_branch foreign key (location, city) references branch(location, city) ON DELETE CASCADE
);

create table customer(
	dlicense varchar2(20) PRIMARY KEY,
	name varchar2(20) not null,
	address varchar2(50) not null,
	cellphone integer not null unique
);

create table reservation(
	confno varchar2(50) PRIMARY KEY,
	vtname varchar2(20) not null,
	dlicense varchar2(20) not null,
	fromdt timestamp(0) not null,
	todt timestamp(0) not null,
	constraint reservation_fk_vType foreign key (vtname) references vehicleType(vtname) ON DELETE CASCADE,
	constraint reservation_fk_customer foreign key (dlicense) references customer(dlicense) ON DELETE CASCADE
);

create table rental(
	rentid varchar2(20) PRIMARY KEY,
	cardno integer not null,
	odometer float not null,
	vlicense varchar2(20) not null,
	fromdt timestamp(0) not null,
	todt timestamp(0) not null,
	dlicense varchar2(20) not null,
	confno varchar2(50) null unique,
	constraint rental_fk_res foreign key (confno) references reservation(confno) ON DELETE CASCADE,
	constraint rental_fk_vehicle foreign key (vlicense) references vehicle(vlicense) ON DELETE CASCADE,
	constraint rental_fk_customer foreign key (dlicense) references customer(dlicense) ON DELETE CASCADE
);

create table return(
	rentid varchar2(20) PRIMARY KEY,
	returndt timestamp(0) not null,
	odometer float not null,
	fulltank varchar2(20) not null,
	value float not null,
	constraint return_fk_rental foreign key (rentid) references rental(rentid) ON DELETE CASCADE
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

-- rented and returned (in both rental and return tables)
insert into vehicle values(0001, '123ABC', 'Toyota', 'Camry', 'red', '50000', 'available', 'Economy', '123 Cordova St.', 'Vancouver');
insert into vehicle values(0003, '125ABC', 'Toyota', 'Tundra', 'blue', '456300', 'available', 'Truck', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0024, '126ABC', 'Toyota', 'Tundra', 'hotpink', '456300', 'available', 'Truck', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0005, '127ABC', 'Toyota', 'Corolla', 'green', '45344', 'available', 'Mid-size', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0028, '150ABC', 'Toyota', 'Corolla', 'green', '45344', 'available', 'Mid-size', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0007, '128ABC', 'Honda', 'Avancier', 'dark', '45323', 'available', 'Standard', '2329 West Mall', 'Vancouver');
insert into vehicle values(0021, '129ABC', 'Honda', 'Avancier', 'light', '45323', 'available', 'Standard', '2329 West Mall', 'Vancouver');
insert into vehicle values(0008, '130ABC', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '2329 West Mall', 'Vancouver');
insert into vehicle values(0014, '131ABC', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '555 Seymour St.', 'Vancouver');
insert into vehicle values(0011, '136ABC', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '123 Bay St.', 'Toronto');
insert into vehicle values(0018, '137ABC', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '598 Bay St.', 'Toronto');
insert into vehicle values(0019, '138ABC', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '27 Kings College Circle', 'Toronto');
insert into vehicle values(0020, '139ABC', 'Honda', 'Civic', 'gold', '345643', 'available', 'Compact', '27 Kings College Circle', 'Toronto');
insert into vehicle values(0013, '140ABC', 'Honda', 'Civic', 'pink', '490823', 'available', 'Compact', '100 Wall St.', 'New York City');
-- rented (in rental table), not in returns table
insert into vehicle values(0023, '142ABC', 'Mercedes', 'C-Class', 'white', '23534', 'rented', 'Compact', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0004, '144ABC', 'Toyota', 'Corolla', 'gold', '45344', 'rented', 'Mid-size', '435 W. 41st Ave.', 'Vancouver');
insert into vehicle values(0009, '145ABC', 'Mercedes', 'GLS', 'silver', '423533', 'rented', 'SUV', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0022, '146ABC', 'Mercedes', 'GLS', 'pink', '423533', 'rented', 'SUV', '8888 University Dr.', 'Burnaby');
-- never rented or returned
insert into vehicle values(0002, '124ABC', 'Toyota', 'Avalon', 'red', '51243', 'available', 'Full-size', '123 Cordova St.', 'Vancouver');
insert into vehicle values(0015, '132ABC', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '555 Seymour St.', 'Vancouver');
insert into vehicle values(0016, '133ABC', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '3700 Willingdon Ave.', 'Burnaby');
insert into vehicle values(0017, '134ABC', 'Mercedes', 'GLS', 'yellow', '75634', 'available', 'SUV', '3700 Willingdon Ave.', 'Burnaby');
insert into vehicle values(0025, '135ABC', 'Mercedes', 'GLS', 'black', '75634', 'available', 'SUV', '555 Seymour St.', 'Vancouver');
insert into vehicle values(0006, '141ABC', 'Honda', 'Civic', 'purple', '45345', 'available', 'Compact', '264 W. 67th Ave.', 'Vancouver');
insert into vehicle values(0027, '143ABC', 'Mercedes', 'C-Class', 'white', '23534', 'available', 'Compact', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0026, '147ABC', 'Mercedes', 'GLS', 'pink', '423533', 'available', 'SUV', '8888 University Dr.', 'Burnaby');
-- never rented or returned (currently in maintenance)
insert into vehicle values(0010, '148ABC', 'Mercedes', 'C-Class', 'white', '23534', 'maintenance', 'Compact', '8888 University Dr.', 'Burnaby');
insert into vehicle values(0012, '149ABC', 'Honda', 'Civic', 'white', '43566', 'maintenance', 'Compact', '100 Wall St.', 'New York City');

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
insert into customer values('VA00018', 'Hermoine Granger', '14 Crookshanks Rd.', 6041234578);
insert into customer values('VA00019', 'Ronald Weasley', '280 Ginger St.', 6041234579);
insert into customer values('VA00020', 'Severus Snape', '4580 Death Eater Dr.', 6041234580);
insert into customer values('VA00021', 'Albus Dumbledore', '100 Hogwarts Pl.', 6041234581);
insert into customer values('VA00022', 'Newton Scamander', '100 F.B. Dr.', 6041234582);
-- hasn't made a reservation
insert into customer values('VA00023', 'Ji-Eun Park', '25th St.', 6040001111);


-- rented and returned
insert into reservation values('RES0001', 'Compact', 'VA00001', TO_TIMESTAMP('2019/11/10 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0002', 'Compact', 'VA00002', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0003', 'Truck', 'VA00003', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0005', 'Compact', 'VA00005', TO_TIMESTAMP('2019/11/05 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0006', 'Compact', 'VA00006', TO_TIMESTAMP('2019/11/01 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0007', 'Standard', 'VA00007', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0009', 'SUV', 'VA00009', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0010', 'SUV', 'VA00010', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0011', 'Compact', 'VA00011', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0012', 'Mid-size', 'VA00012', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0018', 'Standard', 'VA00018', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/02 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0019', 'Mid-size', 'VA00019', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0020', 'Truck', 'VA00020', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/08 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0021', 'Economy', 'VA00020', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0022', 'Full-size', 'VA00022', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- rented, not yet returned; not in returns table
insert into reservation values('RES0004', 'Mid-size', 'VA00004',  TO_TIMESTAMP('2019/11/23 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/15 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0008', 'SUV', 'VA00008', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0015', 'SUV', 'VA00015', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0014', 'Compact', 'VA00014', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/21 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
-- has not started yet, not in rental table
insert into reservation values('RES0013', 'SUV', 'VA00013', TO_TIMESTAMP('2019/12/15 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0017', 'Compact', 'VA00017', TO_TIMESTAMP('2019/11/30 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));
insert into reservation values('RES0016', 'SUV', 'VA00016', TO_TIMESTAMP('2019/11/30 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'));

-- rented and returned
insert into rental values('R0001', 1123453343325454, 45344, '136ABC', TO_TIMESTAMP('2019/11/10 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00001', 'RES0001');
insert into rental values('R0002', 1234454656344564, 423533, '137ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00002', 'RES0002');
insert into rental values('R0003', 2334243545623445, 206300, '125ABC', TO_TIMESTAMP('2019/11/03 09:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00003', 'RES0003');
insert into rental values('R0005', 1234454656344564, 423533, '138ABC', TO_TIMESTAMP('2019/11/05 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00005', 'RES0005');
insert into rental values('R0006', 1234454656344564, 423533, '139ABC', TO_TIMESTAMP('2019/11/01 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00006', 'RES0006');
insert into rental values('R0007', 1234454656344564, 423533, '128ABC', TO_TIMESTAMP('2019/10/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00007', 'RES0007');
insert into rental values('R0009', 1234454656344564, 423533, '131ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00009', 'RES0009');
insert into rental values('R0010', 1234454656344564, 423533, '130ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00010', 'RES0010');
insert into rental values('R0011', 1234454656344564, 423533, '140ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00011', 'RES0011');
insert into rental values('R0012', 1234454656344564, 423533, '127ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00012', 'RES0012');
insert into rental values('R0018', 1234454656344564, 423533, '129ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00018', 'RES0018');
insert into rental values('R0019', 1234454656344564, 423533, '150ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00019', 'RES0019');
insert into rental values('R0020', 1234454656344564, 423533, '126ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00020', 'RES0020');
insert into rental values('R0021', 1234454656344564, 423533, '123ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00021', 'RES0021');
insert into rental values('R0022', 1234454656344564, 423533, '124ABC', TO_TIMESTAMP('2019/11/03 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00022', 'RES0022');
-- vehicle status = rented, not yet returned (not in return table)
insert into rental values('R0014', 1234454656344564, 423533, '142ABC', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00014', 'RES0014');
insert into rental values('R0004', 1234567890123456, 25345, '144ABC', TO_TIMESTAMP('2019/11/23 10:45:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/15 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00004', 'RES0004');
insert into rental values('R0008', 1234567890123456, 25345, '145ABC', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00008', 'RES0008');
insert into rental values('R0015', 1234454656344564, 423533, '146ABC', TO_TIMESTAMP('2019/11/23 10:30:00', 'YYYY/MM/DD HH24:MI:SS'), TO_TIMESTAMP('2019/12/30 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 'VA00015', 'RES0015');

insert into return values('R0001', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 1000);
insert into return values('R0002', TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 300);
insert into return values('R0003', TO_TIMESTAMP('2019/11/06 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 45345, 'yes', 300);
insert into return values('R0005', TO_TIMESTAMP('2019/11/07 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 8000);
insert into return values('R0006', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 850);
insert into return values('R0007', TO_TIMESTAMP('2019/11/01 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
insert into return values('R0009', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 800);
insert into return values('R0010', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 567090, 'yes', 810);
insert into return values('R0011', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 234230, 'yes', 230);
insert into return values('R0012', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 453233, 'yes', 800);
insert into return values('R0018', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 545234, 'yes', 833);
insert into return values('R0019', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 545230, 'yes', 1089);
insert into return values('R0020', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 400);
insert into return values('R0021', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 231);
insert into return values('R0022', TO_TIMESTAMP('2019/11/23 16:30:00', 'YYYY/MM/DD HH24:MI:SS'), 456300, 'yes', 123);
