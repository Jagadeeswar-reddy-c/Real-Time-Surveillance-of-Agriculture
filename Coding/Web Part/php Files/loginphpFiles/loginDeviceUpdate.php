<?php
// Include the database connection file
include '../db.php';

session_start();
$response = array(); // Initialize an array for the response

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $device_log_id = $_POST['device_log_id'];

    // Retrieve user email from session
    if (!isset($_SESSION['user_email'])) {
        $response['success'] = false;
        $response['message'] = "User is not logged in.";
        echo json_encode($response);
        exit; // Stop further execution
    }
    $user_email = $_SESSION['user_email']; // Assuming email is stored in session
    
    $device_id = substr($device_log_id, 3, 3);
    // Perform necessary validations
    // (You may want to add more validations based on your requirements)

    if( substr($device_log_id, 0, 3)=="AAT") {
        // Insert new record into the DEVICE_TYPE_LOG table
        $insertDeviceQuery = "INSERT INTO DEVICE_TYPE_LOG (DEVICE_LOG_ID,USER_EMAIL, DEVICE_ID) VALUES ('$device_log_id','$user_email', '$device_id')";
        $insertDeviceResult = $conn->query($insertDeviceQuery);

        if ($insertDeviceResult) {
            $response['success'] = true;
            $response['message'] = "New device record inserted successfully!";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Error inserting device record: " . $conn->error;
    }
}

// Close the database connection
$conn->close();

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
