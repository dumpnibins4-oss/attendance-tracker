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
    accumulated_hours DECIMAL(5, 2)
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

create table att_track_notif_type(
id int primary key IDENTITY(1,1),
notif_type NVARCHAR(200)
)

CREATE TABLE att_track_notif_setting(
id int primary key identity (1,1),
notif_type_id int,
is_on BIT DEFAULT 1,
user_id int,
)

-- Seed default notification types (idempotent - safe to run multiple times)
IF NOT EXISTS (SELECT 1 FROM att_track_notif_type WHERE notif_type = 'Email Notifications')
    INSERT INTO att_track_notif_type (notif_type) VALUES ('Email Notifications');
IF NOT EXISTS (SELECT 1 FROM att_track_notif_type WHERE notif_type = 'Late Alerts')
    INSERT INTO att_track_notif_type (notif_type) VALUES ('Late Alerts');
IF NOT EXISTS (SELECT 1 FROM att_track_notif_type WHERE notif_type = 'Journal Reminders')
    INSERT INTO att_track_notif_type (notif_type) VALUES ('Journal Reminders');
IF NOT EXISTS (SELECT 1 FROM att_track_notif_type WHERE notif_type = 'Browser Notifications')
    INSERT INTO att_track_notif_type (notif_type) VALUES ('Browser Notifications');

CREATE TABLE att_track_notif(
id int primary key identity (1,1),
title NVARCHAR(200),
content NVARCHAR(200),
created_at DATETIME DEFAULT GETDATE(),
user_id int
)