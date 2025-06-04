<?php

if (isset($_POST['Submit'])) {
    // Get and trim the user input
    $target = trim($_REQUEST['ip']);

    // Validate the input as a proper IP
    if (filter_var($target, FILTER_VALIDATE_IP)) {
        if (stristr(php_uname('s'), 'Windows NT')) {
            $cmd = shell_exec("ping " . escapeshellarg($target));
        } else {
            $cmd = shell_exec("ping -c 4 " . escapeshellarg($target));
        }

        // Output result
        $html = "<pre>{$cmd}</pre>";
    } else {
        $html = "<pre>Invalid IP address provided.</pre>";
    }

    echo $html;
}
?>