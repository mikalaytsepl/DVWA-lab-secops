<?php

if (isset($_POST['Submit'])) {
    // Check Anti-CSRF token
    checkToken($_REQUEST['user_token'], $_SESSION['session_token'], 'index.php');

    // Get and trim input
    $target = trim($_REQUEST['ip']);

    // Validate the input as a proper IP address
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        // Determine OS and execute the ping command securely
        if (stristr(php_uname('s'), 'Windows NT')) {
            // Windows
            $cmd = shell_exec('ping ' . escapeshellarg($target));
        } else {
            // *nix
            $cmd = shell_exec('ping -c 4 ' . escapeshellarg($target));
        }

        // Feedback for the end user
        $html = "<pre>{$cmd}</pre>";
    } else {
        // Notify the user of the error
        $html = '<pre>ERROR: You have entered an invalid IP.</pre>';
    }

    echo $html;
}

// Generate Anti-CSRF token
generateSessionToken();

?>