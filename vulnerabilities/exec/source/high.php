<?php

if (isset($_POST['Submit'])) {
    // Get and trim the user input
    $target = trim($_REQUEST['ip']);

    // Validate the input as a proper IP address
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        // Safely escape the input for use in shell
        $escaped_target = escapeshellarg($target);

        // Detect OS and build command
        if (stristr(php_uname('s'), 'Windows NT')) {
            $cmd = shell_exec("ping $escaped_target");
        } else {
            $cmd = shell_exec("ping -c 4 $escaped_target");
        }

        // Output result
        $html = "<pre>{$cmd}</pre>";
    } else {
        $html = "<pre>Invalid IP address provided.</pre>";
    }

    echo $html;
}
?>
