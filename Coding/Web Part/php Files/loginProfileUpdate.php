<?php
// Include the database connection file
include 'db.php';

// Function to sanitize input data
function sanitizeData($data, $conn) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Start a PHP session
session_start();

$response = array(); // Initialize an array for the response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_name = sanitizeData($_POST['user_name'], $conn);
    $user_dob = sanitizeData($_POST['user_dob'], $conn);
    $user_ph = sanitizeData($_POST['user_ph'], $conn);

    // Retrieve user email from session
    if (!isset($_SESSION['user_email'])) {
        $response['success'] = false;
        $response['message'] = "User is not logged in.";
        echo json_encode($response);
        exit; // Stop further execution
    }
    $user_email = $_SESSION['user_email'];

    // Perform necessary validations
    // (You may want to add more validations based on your requirements)

    // Update data in the database
    $updateProfileQuery = "UPDATE USER_DETAILS SET USER_NAME='$user_name', USER_BOD='$user_dob', USER_PHONE='$user_ph' WHERE USER_EMAIL='$user_email'";
    $updateProfileResult = $conn->query($updateProfileQuery);

    if ($updateProfileResult) {
        $response['success'] = true;
        $response['message'] = "Profile updated successfully!";
    } else {
        $response['success'] = false;
        $response['message'] = "Error updating profile: " . $conn->error;
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
