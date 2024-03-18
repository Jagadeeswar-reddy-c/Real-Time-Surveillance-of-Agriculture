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
    $email = sanitizeData($_POST["email"], $conn);
    $password = sanitizeData($_POST["password"], $conn);

    // Query to check login credentials
    $query = "SELECT UD.USER_NAME
            FROM USER_DETAILS UD
            JOIN USER_PASSWORD UP ON UD.USER_EMAIL = UP.USER_EMAIL
            WHERE UP.USER_EMAIL = '$email' AND UP.PASSWORD = '$password';";

    $result = $conn->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Store email in session variable for further use
            $_SESSION['user_email'] = $email;

            $response['success'] = true;
        } else {
            // Invalid credentials
            $response['success'] = false;
            $response['message'] = "Invalid email or password. Please try again.";
        }
    } else {
        // Error in query execution
        $response['success'] = false;
        $response['message'] = "Error executing query: " . $conn->error;
    }
}

// Close the database connection
$conn->close();

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
