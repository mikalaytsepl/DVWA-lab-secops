<?php

if (isset($_POST['Submit'])) {
    // Get and trim the user input
    $target = trim($_REQUEST['ip']);

    // Validate the input as a proper IP
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        // Determine OS and execute the ping command securely.
        if (stristr(php_uname('s'), 'Windows NT')) {
            // Windows
            $cmd = shell_exec("ping " . escapeshellarg($target));
        } else {
            // *nix
            $cmd = shell_exec("ping -c 4 " . escapeshellarg($target));
        }

        // Feedback for the end user
        $html = "<pre>{$cmd}</pre>";
    } else {
        $html = "<pre>Invalid IP address provided.</pre>";
    }

    echo $html;
}

?>