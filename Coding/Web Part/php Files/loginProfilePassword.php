<?php
// Include the database connection file
include 'db.php';

// Start a PHP session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = array(); // Initialize an array for the response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $curpassword = $_POST['curpassword'];
    $npassword = $_POST['npassword'];
    $cpassword = $_POST['cpassword'];

    // Retrieve user email from session
    if (!isset($_SESSION['user_email'])) {
        $response['success'] = false;
        $response['message'] = "User is not logged in.";
        echo json_encode($response);
        exit; // Stop further execution
    }
    $user_email = $_SESSION['user_email'];

    // Query to fetch current password
    $fetchPasswordQuery = "SELECT PASSWORD FROM USER_PASSWORD WHERE USER_EMAIL='$user_email'";
    $fetchPasswordResult = $conn->query($fetchPasswordQuery);

    if ($fetchPasswordResult->num_rows > 0) {
        $row = $fetchPasswordResult->fetch_assoc();
        $currentPassword = $row['PASSWORD'];

        // Check if the current password matches the entered current password
        if ($curpassword !== $currentPassword) {
            $response['success'] = false;
            $response['message'] = "Current password does not match.";
            echo json_encode($response);
            exit; // Stop further execution
        }

        // Update password in the database
        $updatePasswordQuery = "UPDATE USER_PASSWORD SET PASSWORD='$npassword' WHERE USER_EMAIL='$user_email'";
        $updatePasswordResult = $conn->query($updatePasswordQuery);

        if ($updatePasswordResult) {
            $response['success'] = true;
            $response['message'] = "Password updated successfully!";
        } else {
            $response['success'] = false;
            $response['message'] = "Error updating password: " . $conn->error;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "User not found.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

// Close the database connection
$conn->close();

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
