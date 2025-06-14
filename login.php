<?php
// Headers must be sent before any output
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
// Secure CSP configuration (no wildcard, no unsafe-inline)
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; frame-ancestors 'none';");

ini_set('display_errors', 0);
error_reporting(0);

define('DVWA_WEB_PAGE_TO_ROOT', '');
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup(array());
dvwaDatabaseConnect();

if (isset($_POST['Login'])) {
	// Anti-CSRF
	$session_token = $_SESSION['session_token'] ?? '';
	checkToken($_REQUEST['user_token'], $session_token, 'login.php');

	$user = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($_POST['username']));
	$pass = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], stripslashes($_POST['password']));
	$pass = md5($pass);

	// Check if users table exists
	$query = "SELECT table_schema, table_name FROM information_schema.tables WHERE table_schema='{$_DVWA['db_database']}' AND table_name='users' LIMIT 1";
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
	if (mysqli_num_rows($result) != 1) {
		dvwaMessagePush("First time using DVWA.<br />Need to run 'setup.php'.");
		dvwaRedirect(DVWA_WEB_PAGE_TO_ROOT . 'setup.php');
	}

	// Check user credentials
	$query = "SELECT * FROM `users` WHERE user='$user' AND password='$pass';";
	$result = @mysqli_query($GLOBALS["___mysqli_ston"], $query);
	if ($result && mysqli_num_rows($result) === 1) {
		dvwaMessagePush("You have logged in as '{$user}'");
		dvwaLogin($user);
		dvwaRedirect(DVWA_WEB_PAGE_TO_ROOT . 'index.php');
	}

	dvwaMessagePush('Login failed');
	dvwaRedirect('login.php');
}

$messagesHtml = messagesPopAllToHtml();

header('Cache-Control: no-cache, must-revalidate');
header('Content-Type: text/html;charset=utf-8');
header('Expires: Tue, 23 Jun 2009 12:00:00 GMT');

generateSessionToken();

echo "<!DOCTYPE html>
<html lang=\"en-GB\">
<head>
	<meta charset=\"UTF-8\">
	<title>Login :: Damn Vulnerable Web Application (DVWA)</title>
	<link rel=\"stylesheet\" type=\"text/css\" href=\"" . DVWA_WEB_PAGE_TO_ROOT . "dvwa/css/login.css\" />
</head>
<body>
<div id=\"wrapper\">
	<div id=\"header\">
		<br />
		<p><img src=\"" . DVWA_WEB_PAGE_TO_ROOT . "dvwa/images/login_logo.png\" /></p>
		<br />
	</div>
	<div id=\"content\">
		<form action=\"login.php\" method=\"post\">
			<fieldset>
				<label for=\"user\">Username</label>
				<input type=\"text\" class=\"loginInput\" size=\"20\" name=\"username\"><br />
				<label for=\"pass\">Password</label>
				<input type=\"password\" class=\"loginInput\" AUTOCOMPLETE=\"off\" size=\"20\" name=\"password\"><br />
				<br />
				<p class=\"submit\"><input type=\"submit\" value=\"Login\" name=\"Login\"></p>
			</fieldset>
			" . tokenField() . "
		</form>
		<br />
		{$messagesHtml}
	</div>
	<div id=\"footer\">
		<p>" . dvwaExternalLinkUrlGet('https://github.com/digininja/DVWA/', 'Damn Vulnerable Web Application (DVWA)') . "</p>
	</div>
</div>
</body>
</html>";
?>
