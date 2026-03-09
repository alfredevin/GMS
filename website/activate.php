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
    // Show confirmation dialog
    echo "
    <script>
        var confirmAction = confirm('Do you want to activate your account?');
        if (confirmAction) {
            // Proceed to update the status in the database
            window.location.href = 'activate_confirm.php?token=" . $token . "';
        } else {
            alert('Account activation cancelled.');
            window.location.href = 'login.php'; // Redirect to the login page (or another appropriate page)
        }
    </script>
    ";
} else {
    // Token is invalid
    echo "Invalid activation link!";
}

// Close the prepared statement
mysqli_stmt_close($stmt);
?>
