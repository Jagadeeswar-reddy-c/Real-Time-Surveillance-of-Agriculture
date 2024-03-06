<?php
// Set your MySQL credentials
$servername = "localhost";
$username = "root";
$password = "";
$databaseName = "IOT_DATABASE";

// Create a connection
$conn = new mysqli($servername, $username, $password);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database
$sql = "CREATE DATABASE IF NOT EXISTS $databaseName";

if ($conn->query($sql) === TRUE) {
    echo "Database created successfully <br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($databaseName);

// Creating Device Table
$sql = "CREATE TABLE IF NOT EXISTS DEVICE_TYPE (
    DEVICE_ID INT PRIMARY KEY,
    DEVICE_MODEL_NAME VARCHAR(30)
)";

if ($conn->query($sql) === TRUE) {
    echo "Device Table created successfully <br>";
} else {
    echo "Error creating Device Table: " . $conn->error;
}

// Creating User Table
$sql = "CREATE TABLE IF NOT EXISTS USER_DETAILS (
    USER_NAME VARCHAR(60),
    USER_BOD DATE,
    USER_PHONE INT,
    USER_EMAIL VARCHAR(30),
    DEVICE_ID INT,
    UNIQUE(USER_EMAIL),
    PRIMARY KEY (USER_EMAIL),
    FOREIGN KEY (DEVICE_ID) REFERENCES DEVICE_TYPE(DEVICE_ID)
)";

if ($conn->query($sql) === TRUE) {
    echo "User Table created successfully <br>";
} else {
    echo "Error creating User Table: " . $conn->error;
}

// Creating Device Table
$sql = "CREATE TABLE IF NOT EXISTS USER_PASSWORD (
    USER_EMAIL VARCHAR(30),
    PASSWORD VARCHAR(15),
    FOREIGN KEY (USER_EMAIL) REFERENCES USER_DETAILS(USER_EMAIL)
)";

if ($conn->query($sql) === TRUE) {
    echo "User Password Table created successfully <br>";
} else {
    echo "Error creating Device Table: " . $conn->error;
}

// Close the connection
$conn->close();
?>
