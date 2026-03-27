CREATE TABLE att_track_users (
	id INT PRIMARY KEY IDENTITY(1,1),
	lastName NVARCHAR(50),
	firstName NVARCHAR(255),
	middleName NVARCHAR(50),
	employee_id NVARCHAR(30),
	biometrics_id NVARCHAR(20),
    password NVARCHAR(MAX) NOT NULL,
	position NVARCHAR(50)
)

CREATE TABLE att_track_restrictions (
    id INT PRIMARY KEY IDENTITY(1,1),
    biometrics_id NVARCHAR(20),

)