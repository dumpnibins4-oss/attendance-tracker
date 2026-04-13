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
	position NVARCHAR(50),
	required_hours DECIMAL(5, 2),
	accumulated_hours DECIMAL(5, 2),
	in_location NVARCHAR(255),
	out_location NVARCHAR(255)
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

CREATE TABLE att_track_attendance (
	id INT PRIMARY KEY IDENTITY(1, 1),
	user_id INT,
	time_in DATETIME,
	time_out DATETIME,
	status VARCHAR(50),
	hours DECIMAL(5, 2),
	journal NVARCHAR(MAX)
)

-- INSERT INTO hik_logs_staging (id, emp_id, log_datetime, log_date, log_time, device_name, device_sn, first_name, last_name, processed)
-- VALUES (999, 41882, GETDATE(), CAST(GETDATE() AS DATE), CAST(GETDATE() AS TIME), 'GO Entrance Door Access', 'FX6367196', 'Vince', 'Emanuelle Salenga', 0)