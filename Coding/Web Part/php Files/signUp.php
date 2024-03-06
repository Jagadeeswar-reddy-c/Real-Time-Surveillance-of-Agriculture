<?php
// Include the database connection file
include 'db.php';

// Function to sanitize input data
function sanitizeData($data, $conn) {
    return mysqli_real_escape_string($conn, trim($data));
}

$response = array(); // Initialize an array for the response

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $user_name = sanitizeData($_POST['user_name'], $conn);
    $user_dob = sanitizeData($_POST['user_dob'], $conn);
    $user_ph = sanitizeData($_POST['user_ph'], $conn);
    $email = sanitizeData($_POST['email'], $conn);
    $password = sanitizeData($_POST['password'], $conn);
    $cpassword = sanitizeData($_POST['cpassword'], $conn);

    // Perform necessary validations
    // (You may want to add more validations based on your requirements)

    // Check if passwords match
    if ($password != $cpassword) {
        $response['success'] = false;
        $response['message'] = "Passwords do not match";
    } else {
        // Check if the email is already in use
        $emailCheckQuery = "SELECT USER_EMAIL FROM USER_DETAILS WHERE USER_EMAIL = '$email'";
        $emailCheckResult = $conn->query($emailCheckQuery);

        if ($emailCheckResult->num_rows > 0) {
            // Email is already in use
            $response['success'] = false;
            $response['message'] = "Email address is already in use. Please choose another one.";
        } else {
            // Insert data into USER_DETAILS table
            $insertUserQuery = "INSERT INTO USER_DETAILS (USER_NAME, USER_BOD, USER_PHONE, USER_EMAIL) VALUES ('$user_name', '$user_dob', '$user_ph', '$email')";
            $insertUserResult = $conn->query($insertUserQuery);

            if ($insertUserResult) {
                // Insert data into USER_PASSWORD table
                $insertPasswordQuery = "INSERT INTO USER_PASSWORD (USER_EMAIL, PASSWORD) VALUES ('$email', '$password')";
                $insertPasswordResult = $conn->query($insertPasswordQuery);

                if ($insertPasswordResult) {
                    $response['success'] = true;
                    $response['message'] = "Signup successful!";
                } else {
                    $response['success'] = false;
                    $response['message'] = "Error inserting password: " . $conn->error;
                }
            } else {
                $response['success'] = false;
                $response['message'] = "Error inserting user details: " . $conn->error;
            }
        }
    }
}

// Close the database connection
$conn->close();

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
