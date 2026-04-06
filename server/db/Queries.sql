CREATE TABLE att_track_users (
	id INT PRIMARY KEY IDENTITY(1,1),
	lastName NVARCHAR(50),
	firstName NVARCHAR(255),
	middleName NVARCHAR(50),
	school NVARCHAR(255),
	address NVARCHAR(255),
	contactNumber NVARCHAR(20),
	birthday DATE,
	gender NVARCHAR(10),
	department NVARCHAR(50),
	employee_id NVARCHAR(30),
	biometrics_id NVARCHAR(20),
    gmail NVARCHAR(255),
	position NVARCHAR(50)
)

CREATE TABLE att_track_login_credentials (
	id INT PRIMARY KEY IDENTITY(1,1),
	biometrics_id NVARCHAR(20),
	password NVARCHAR(MAX) NOT NULL,
)

CREATE TABLE att_track_restrictions (
    id INT PRIMARY KEY IDENTITY(1,1),
    biometrics_id NVARCHAR(20),
    role NVARCHAR(20)
)