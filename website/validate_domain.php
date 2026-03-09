<?php

// Check if the domain parameter exists
if (isset($_GET['domain'])) {
    $domain = $_GET['domain'];

    // Check if the domain has MX records (Mail Exchange records)
    if (checkdnsrr($domain, 'MX')) {
        echo 'valid';  // Domain is valid
    } else {
        echo 'invalid';  // Domain is invalid
    }
} else {
    echo 'invalid';  // No domain provided
}
?>
