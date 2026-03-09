<?php
// Get the token from the URL
$token = $_GET['token'];

include '../config.php'; // Include MySQLi connection

// Prepare the query to select the user by token
$sql = "SELECT email FROM new_users WHERE activation_token = ?";
$stmt = mysqli_prepare($conn, $sql);

// Bind the token parameter
mysqli_stmt_bind_param($stmt, "s", $token);

// Execute the query
mysqli_stmt_execute($stmt);

// Get the result
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if ($user) {
    // Token is valid, update customer status to 1 (active)
    $update_sql = "UPDATE new_users SET user_status = 1, activation_token = NULL WHERE email = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);

    // Bind the email parameter for the update query
    mysqli_stmt_bind_param($update_stmt, "s", $user['email']);

    // Execute the update query
    $update_stmt->execute();

    // Inform the user that their account is activated
    echo "Your account has been successfully activated! <a href='http://localhost/boac/gms/website/index'>Back to Login</a>";

} else {
    // Token is invalid
    echo "Invalid activation link!";
}

// Close the prepared statements
mysqli_stmt_close($stmt);
mysqli_stmt_close($update_stmt);
?>
