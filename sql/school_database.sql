CREATE TABLE Teachers (
    teacherId INT AUTO_INCREMENT PRIMARY KEY,
    teacherNames VARCHAR(100),
    address VARCHAR(255),
    phoneNumber VARCHAR(100),
    annualSalary DECIMAL (10, 2),
    backgroundCheck BOOLEAN
    );

CREATE TABLE Class (
    classId INT AUTO_INCREMENT PRIMARY KEY,
    teacherId INT,
    className VARCHAR(100),
    classCapacity INT,
    
    FOREIGN KEY (teacherId) REFERENCES Teachers(teacherId)
);

CREATE TABLE Pupils (
    pupilId INT AUTO_INCREMENT PRIMARY KEY,
    classId INT,
    pupilNames VARCHAR(100),
    pupilAddress VARCHAR(255),
    medicalInformation VARCHAR(255),
    FOREIGN KEY (classId) REFERENCES Class(classId)
);

CREATE TABLE Parents (
    parentId INT AUTO_INCREMENT PRIMARY KEY,
    parentAddress VARCHAR(255),
    parentEmail VARCHAR(100),
    telephone VARCHAR(100)
);

CREATE TABLE Family (
    parentId INT,
    pupilId INT,
    FOREIGN KEY (pupilId) REFERENCES Pupils(pupilId),
    FOREIGN KEY (parentId) REFERENCES Parents(parentId)
)


INSERT INTO Teachers (teacherId, teacherNames, address, phoneNumber, annualSalary, backgroundCheck)
VALUES 
(1, 'Mr. James Thompson', '44 Maple Avenue, Manchester', '07700 900001', 32000.00, 1),
(2, 'Mrs. Sarah Jenkins', '12 Piccadilly Gardens, Manchester', '07700 900002', 34500.50, 1),
(3, 'Ms. Emily Clarke', '89 Wilmslow Road, Didsbury', '07700 900003', 31000.00, 1),
(4, 'Mr. Robert Evans', '55 Oak Lane, Salford', '07700 900004', 38000.00, 1),
(5, 'Dr. Fiona Wright', '3 Birch Way, Stockport', '07700 900005', 42000.00, 1),
(6, 'Mr. David Green', '77 Deansgate, Manchester', '07700 900006', 30000.00, 1),
(7, 'Mrs. Hannah Mills', '21 Chorlton Road, Manchester', '07700 900007', 33000.00, 1),
(8, 'Mr. Ian Scott', '10 Trafford Park, Manchester', '07700 900008', 31500.00, 1),
(9, 'Ms. Lucy Taylor', '6 Northern Quarter, Manchester', '07700 900009', 29500.00, 1),
(10, 'Mr. George Harris', '15 Oxford Road, Manchester', '07700 900010', 36000.00, 1),
(11, 'Mrs. Oliver King', '99 Chester Road, Stretford', '07700 900011', 32500.00, 1),
(12, 'Ms. Sophie Turner', '4 Garden Close, Altrincham', '07700 900012', 31200.00, 1),
(13, 'Mr. Harry White', '72 Station Road, Wigan', '07700 900013', 35000.00, 1),
(14, 'Dr. Jack Robinson', '50 The Avenue, Sale', '07700 900014', 40000.00, 1),
(15, 'Ms. Mia Walker', '21 Main St, Bolton', '07700 900015', 29000.00, 1),
(16, 'Mr. Charlie Hall', '8 West St, Bury', '07700 900016', 33500.00, 1),
(17, 'Mrs. Amelia Young', '65 North Rd, Rochdale', '07700 900017', 34000.00, 0),
(18, 'Mr. Thomas Allen', '12 South St, Oldham', '07700 900018', 31800.00, 1),
(19, 'Ms. Isla Nelson', '3 East St, Manchester', '07700 900019', 30500.00, 1),
(20, 'Mr. Jacob Carter', '9 Broad St, Manchester', '07700 900020', 37000.00, 1);


INSERT INTO Class (classId, teacherId, className, classCapacity)
VALUES
(1, 1, 'Reception - Sunflowers', 30),
(2, 2, 'Reception - Poppies', 30),
(3, 3, 'Year 1 - Science', 30),
(4, 4, 'Year 1 - Maths', 30),
(5, 5, 'Year 2 - English', 30),
(6, 6, 'Year 2 - Maths', 30),
(7, 7, 'Year 3 - Science', 32),
(8, 8, 'Year 3 - Maths', 32),
(9, 9, 'Year 4 - English', 32),
(10, 10, 'Year 4 - Maths', 32),
(11, 11, 'Year 5 - Maths', 32),
(12, 12, 'Year 5 - English', 32),
(13, 13, 'Year 6 - Maths', 32),
(14, 14, 'Year 6 - English', 32),
(15, 15, 'Reception - Daisies', 30),
(16, 16, 'Year 1 - English', 30),
(17, 17, 'Year 2 - Science', 30),
(18, 18, 'Year 3 - English', 32),
(19, 19, 'Year 4 - Science', 32),
(20, 20, 'Year 5 - Science', 32);



INSERT INTO Pupils (pupilId, classId, pupilNames, pupilAddress, medicalInformation)
VALUES
(1, 1, 'Oliver Smith', '12 High St, Manchester', 'None'),
(2, 2, 'George Jones', '45 Victoria Rd, London', 'Asthma'),
(3, 3, 'Harry Williams', '88 Queen St, Birmingham', 'None'),
(4, 4, 'Jack Brown', '23 Baker St, Leeds', 'Peanut Allergy'),
(5, 5, 'Jacob Taylor', '7 Park Lane, Liverpool', 'None'),
(6, 6, 'Leo Davies', '19 King St, Bristol', 'None'),
(7, 7, 'Oscar Evans', '56 Church Rd, Newcastle', 'Diabetes'),
(8, 8, 'Charlie Thomas', '101 London Rd, Sheffield', 'None'),
(9, 9, 'Muhammad Johnson', '33 York Rd, Brighton', 'None'),
(10, 10, 'William Roberts', '4 Oxford St, Nottingham', 'Hayfever'),
(11, 11, 'Amelia Walker', '99 Cambridge Rd, Cardiff', 'None'),
(12, 12, 'Olivia Wright', '14 Garden Close, Edinburgh', 'Eczema'),
(13, 13, 'Isla Robinson', '72 Station Rd, Glasgow', 'None'),
(14, 14, 'Ava Thompson', '50 The Avenue, Belfast', 'None'),
(15, 15, 'Mia White', '21 Main St, York', 'None'),
(16, 16, 'Emily Hughes', '8 West St, Leicester', 'None'),
(17, 17, 'Poppy Edwards', '65 North Rd, Southampton', 'None'),
(18, 18, 'Sophie Green', '12 South St, Portsmouth', 'None'),
(19, 19, 'Grace Jones', '45 Victoria Rd, London', 'Gluten Intolerance'),
(20, 20, 'Lily Smith', '12 High St, Manchester', 'None');



INSERT INTO Parents (parentId, parentAddress, parentEmail, telephone)
VALUES
(1, '12 High St, Manchester', 'parent1@example.co.uk', '07911 123456'),
(2, '45 Victoria Rd, London', 'parent2@example.co.uk', '07911 123457'),
(3, '88 Queen St, Birmingham', 'parent3@example.co.uk', '07911 123458'),
(4, '23 Baker St, Leeds', 'parent4@example.co.uk', '07911 123459'),
(5, '7 Park Lane, Liverpool', 'parent5@example.co.uk', '07911 123460'),
(6, '19 King St, Bristol', 'parent6@example.co.uk', '07911 123461'),
(7, '56 Church Rd, Newcastle', 'parent7@example.co.uk', '07911 123462'),
(8, '101 London Rd, Sheffield', 'parent8@example.co.uk', '07911 123463'),
(9, '33 York Rd, Brighton', 'parent9@example.co.uk', '07911 123464'),
(10, '4 Oxford St, Nottingham', 'parent10@example.co.uk', '07911 123465'),
(11, '99 Cambridge Rd, Cardiff', 'parent11@example.co.uk', '07911 123466'),
(12, '14 Garden Close, Edinburgh', 'parent12@example.co.uk', '07911 123467'),
(13, '72 Station Rd, Glasgow', 'parent13@example.co.uk', '07911 123468'),
(14, '50 The Avenue, Belfast', 'parent14@example.co.uk', '07911 123469'),
(15, '21 Main St, York', 'parent15@example.co.uk', '07911 123470'),
(16, '8 West St, Leicester', 'parent16@example.co.uk', '07911 123471'),
(17, '65 North Rd, Southampton', 'parent17@example.co.uk', '07911 123472'),
(18, '12 South St, Portsmouth', 'parent18@example.co.uk', '07911 123473'),
(19, '3 East St, Coventry', 'parent19@example.co.uk', '07911 123474'),
(20, '9 Broad St, Reading', 'parent20@example.co.uk', '07911 123475');


INSERT INTO Family (parentId, pupilId) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5), (6, 6), (7, 7), (8, 8), (9, 9), (10, 10),
(11, 11), (12, 12), (13, 13), (14, 14), (15, 15), (16, 16), (17, 17), (18, 18), (19, 19), (20, 20);


SELECT * FROM Pupils WHERE pupilId = 9;

SELECT * FROM Teachers WHERE teacherId = 15;

SELECT * FROM Pupils WHERE classId = 3;

SELECT teacherNames FROM Teachers JOIN Class ON Teachers.teacherId = Class.teacherId WHERE className = 'Year 5 - Science';

SELECT Parents.* FROM Parents JOIN Family ON Parents.parentId = Family.parentId JOIN Pupils ON Family.pupilId = Pupils.pupilId WHERE Pupils.pupilNames = 'Oliver Smith';

SELECT Pupils.pupilNames FROM Pupils JOIN Family ON Parents.parentId = Family.pupilId WHERE Family.parentId = 2;