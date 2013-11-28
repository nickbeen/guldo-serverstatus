<?php

require 'config.php';

// ######################## SET SESSION ###########################
session_name(COOKIE_NAME);
session_start();
setcookie(session_name(), session_id(), time() + 86400 * 7, '/; samesite=strict', '', true, true);

// ######################## URL ROUTING ###########################
if (isset($_SESSION['loggedin'])) {
    if (empty($_REQUEST['do']) OR $_REQUEST['do'] == 'loginform') {
        $_REQUEST['do'] = 'view';
    }
} else { // logged out
    if (empty($_REQUEST['do']) OR $_REQUEST['do'] == 'view') {
        $_REQUEST['do'] = 'loginform';
    }
}

// ######################## FUNCTIONS ###########################
/**
 * Print login form
 */
function html_loginform()
{
    echo '<form id="login" action="' . $_SERVER['PHP_SELF'] . '?do=login" method="post">
				<div class="divider">Login</div>
				<input type="password" value="" name="password" placeholder="Enter password" required>
				<input type="submit" value="Login">
			</form>';
}

/**
 * Print logout form
 */
function html_logoutform()
{
    echo '<form id="logout" action="' . $_SERVER['PHP_SELF'] . '?do=logout" method="post">
			<div class="divider">Logout</div>
			<input type="submit" value="Logout">
		</form>';
}

/**
 * Print opening tags of page
 *
 * @param string $id
 */
function html_header($id = '')
{
    if (!empty($id)) {
        $id = " id=\"$id\"";
    } else {
        $id = null;
    }

    echo '<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, minimum-scale=1, initial-scale=1, user-scalable=no">
		<meta name="theme-color" content="#000000">
		<meta name="robots" content="noindex, nofollow, noarchive">
		<style>
			html {
				height: 100%;
				overflow-y: scroll;
			}
			body {
				background-color: #222;
				height: 100%;
				margin: 0;
				padding: 0;
			}
			section {
				color: #fff;
				font: 13pt "Segoe UI", "Roboto Light", Arial, sans-serif;
				margin: 0 auto;
				position: relative;
				top: 40%;
				width: 500px;
			}
			section#content {
				top: 0;
			}
			section#loginanimation {
				animation: login 3s;
				top: 0;
			}
			@keyframes login {
				0% { top: 40% }
				100% { top: 0 }
			}
			section#logoutanimation {
				animation: logout 3s;
			}
			@keyframes logout {
				0% { top: 0 }
				100% { top: 40% }
			}
			p {
				font-size: 0.9em;
				margin: 0;
				padding: 15px 7.5px;
			}
			p div {
				padding: 15px 7.5px;
			}
			form#login input[type=submit] {
				display: none;
			}
			input {
				-webkit-appearance: none;
				border-radius: 0;
				margin: 0;
			}				
			input[type=password] {
				background-color: #fff;
				border: 0;
				box-sizing: border-box;
				font: 12pt "Segoe UI", "Roboto Light", Arial, Sans-Serif;
				outline-width: 0;
				padding: 9px;
				text-transform: uppercase;
				width: 100%;
			}
			p {
				font-weight: bold;
			}
			.divider {
				background-color: #f33;
				font-weight: bold;
				padding: 6px 8px;
				text-transform: uppercase;
			}
			.half_div {
				float: left;
				width: 50%;
			}
			.server_load {
				float: left;
				font-weight: bold;
				padding: 15px 0;
				text-align: center;
				width: 33.3%;
			}
			.red {
				color: #f99;
			}
			.orange {
				color: #ffa500;
			}
			.green {
				color: #9c9;
			}
			.message {
				background-color: #fff;
				color: #000;
			}
			.alert {
				transform: translateY(40%);
			}
			.okcheck {
				margin: 15px 0;
				text-align: center;
			}
			.graph {
				height: 32px;
				margin: 15px 7.5px;
			}
			.graphbar {
				height: 32px;
				position: relative;
				z-index: 2;
			}
			.graphtext {
				font-size: 0.9em;
				font-weight: bold;
				padding: 6px 0;
				position: absolute;
				text-align: center;
				width: 46%;
				z-index: 3;
			}
			@media(max-device-width: 480px){
				section {
					width: 100%;					
				}
			}
		</style>
		<title>Server Status</title>
	</head>
	<body>
		<section' . $id . '>';
}

/**
 * Print ajax template
 */
function html_ajax()
{
    echo '<script>
	function getNewPosts() {
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
				document.getElementById("ajax").innerHTML = xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET", "serverstatus.php?do=ajax", true);
		xmlhttp.timeout = 5000;
		xmlhttp.ontimeout = function() { document.getElementById("ajax").innerHTML = ("<div class=\"divider alert\">Connection timeout</div>") }
		xmlhttp.send();
	}

	window.onload = function(){getNewPosts()}; // load during first pageload
	const myVar = setInterval(function(){getNewPosts()},60000); // refresh every minute
</script>';
}

/**
 * Print ending tags of page
 */
function html_footer()
{
    echo '</section>
	</body>
</html>';
}

/**
 * Print dividing div tag
 *
 * @param string $text
 */
function divider($text = '')
{
    echo "<div class=\"divider\">$text</div>\n";
}

/**
 * Print closing div tag
 */
function close_div()
{
    echo "</div>\n";
}

/**
 * Print graphic bar
 *
 * @param $used
 * @param $total
 */
function graphic_bar ($used, $total)
{
    $bar_percentage = floor($used / $total * 100);

    switch (true) {
        case ($bar_percentage > 88) :
            $bar_color = 	'#f99'; // red
            $bar_color2 = 	'#fcc';
            break;
        case ($bar_percentage > 75):
            $bar_color = 	'#ffa500'; // orange
            $bar_color2 = 	'#ffd27f';
            break;
        default:
            $bar_color = 	'#89b789'; // green
            $bar_color2 = 	'#b7ceb7';
    }

    switch (true) {
        case (strlen($used) >= 7):
            $number = number_format($used / 1000000000, 1, ",", ".") . ' GB';
            break;
        case (strlen($used) >= 4):
            $number = number_format($used / 1000, 0, ",", ".") . ' MB';
            break;
        case (strlen($used) <= 3):
            $number = number_format($used, 0, ",", ".") . ' KB';
            break;
        default:
            $number = number_format($used, 0, ",", ".") . ' B';
    }

    echo '<div class="graph" style="background-color:' . $bar_color2 . '">
			<div class="graphtext">' . $number . '</div>
			<div class="graphbar" style="background-color:' . $bar_color . '; width:' . $bar_percentage . '%"></div>
		</div>' . "\n";
}

/**
 * Get system time
 */
function system_time()
{
    echo "<p>" . date("d-m-Y H:i", time()) . "</p>\n";
}

/**
 * Get memory info of system
 *
 * @return array
 */
function system_memoryinfo()
{
    $data = explode("\n", file_get_contents("/proc/meminfo"));
    $memory = array();
    foreach ($data as $line) {
        list($key, $val) = array_pad(explode(':', $line, 2), 2, null);
        $memory[$key] = trim($val);
    }

    return $memory;
}

/**
 * Get uptime of system
 */
function system_uptimeinfo()
{
    $uptime = shell_exec("cut -d. -f1 /proc/uptime");
    $days = floor($uptime / 60 / 60 / 24) . " days";
    $hours = $uptime / 60 / 60 % 24 . " hours";
    $minutes = $uptime / 60 % 60 . " minutes";

    echo "<p>$days $hours $minutes</p>\n";
}

/**
 * Get size of directory
 *
 * @param $directory
 * @return int
 */
function get_dir_size($directory)
{
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
        $size += $file->getSize();
    }

    return $size;
}

// disk quota
$disc_used = disk_total_space("/") - disk_free_space("/");
$disc_total = disk_total_space("/");

// root quota
$root_used_storage = get_dir_size(ROOT);
$root_total_storage = 1024 * 1024 * 5000; // 5 GB

// server loads
$server_load = explode(' ', trim(file_get_contents('/proc/loadavg')));

switch (true) {
    case ($server_load[0] >= 5.00):
        $server_load[0] = '<div class="red server_load">'. $server_load[0] .'</div>';
        break;
    case ($server_load[0] >= 1.00):
        $server_load[0] = '<div class="orange server_load">'. $server_load[0] .'</div>';
        break;
    default:
        $server_load[0] = '<div class="server_load">'. $server_load[0] .'</div>';
}

switch (true) {
    case ($server_load[1] >= 5.00):
        $server_load[1] = '<div class="red server_load">'. $server_load[1] .'</div>';
        break;
    case ($server_load[1] >= 1.00):
        $server_load[1] = '<div class="orange server_load">'. $server_load[1] .'</div>';
        break;
    default:
        $server_load[1] = '<div class="server_load">'. $server_load[1] .'</div>';
}

switch (true) {
    case ($server_load[2] >= 5.00):
        $server_load[2] = '<div class="red server_load">'. $server_load[2] .'</div>';
        break;
    case ($server_load[2] >= 1.00):
        $server_load[2] = '<div class="orange server_load">'. $server_load[2] .'</div>';
        break;
    default:
        $server_load[2] = '<div class="server_load">'. $server_load[2] .'</div>';
}

// cpu memory & swap memory
$memory = system_memoryinfo();
$memory['MemUsed'] = 	$memory['MemTotal'] - $memory['MemFree'];
$memory['SwapUsed'] = 	$memory['SwapTotal'] - $memory['SwapFree'];
$memory['MemFree'] = 	number_format(substr($memory['MemFree'], 0, -3) / 1000);
$memory['SwapFree'] = 	number_format(substr($memory['SwapFree'], 0, -3) / 1000);

// ######################## TEMPLATE: LOGINFORM ###########################
if ($_REQUEST['do'] == 'loginform') {
    html_header();
    html_loginform();
    html_footer();
}

// ######################## TEMPLATE: LOGIN ###########################
if ($_REQUEST['do'] == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (hash('sha1', $_POST['password']) == PASSWORD) {
        $_SESSION['loggedin'] = true;
        header('Refresh:3; url=' . $_SERVER['PHP_SELF']);
        html_header('loginanimation');
        divider('Logged in');
        html_footer();
    } else {
        header('Refresh:3; url=' . $_SERVER['PHP_SELF']);
        html_header();
        divider('Wrong password');
        html_footer();
    }
} else {
    header('Refresh:3; url=' . $_SERVER['PHP_SELF']);
    html_header();
    divider('Login failed');
    html_footer();
}

// ######################## TEMPLATE: LOGOUT ###########################
if ($_REQUEST['do'] == 'logout' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    setcookie(COOKIE_NAME, '', time() - 60); // delete cookie
    $_SESSION = array(); // nullify
    session_unset(); // clean
    session_destroy(); // clear session from server
    session_regenerate_id(true); // refresh sessionid

    header('Refresh:3; url=' . $_SERVER['PHP_SELF']);
    html_header('logoutanimation');
    divider('Logged out');
    html_footer();
} else {
    header('Refresh:3; url=' . $_SERVER['PHP_SELF']);
    html_header();
    divider('Logout failed');
    html_footer();
}

// ######################## TEMPLATE: VIEW ###########################
if ($_REQUEST['do'] == 'view') {
    html_header('content');
    html_ajax();
    echo '<div id="ajax"></div>';
    html_logoutform();
    html_footer();
}

// ######################## TEMPLATE: AJAX ###########################
if ($_REQUEST['do'] == 'ajax') {
    echo '<div>';
    divider($_SERVER['SERVER_ADDR']);
    echo $server_load[0] . $server_load[1] . $server_load[2];
    close_div();
    echo '<div class="half_div">';
    divider('RAM memory');
    graphic_bar($memory['MemUsed'], $memory['MemTotal']);
    close_div();
    echo '<div class="half_div">';
    divider('Swap memory');
    graphic_bar($memory['SwapUsed'], $memory['SwapTotal']);
    close_div();
    echo '<div class="half_div">';
    divider('Time');
    system_time();
    close_div();
    echo '<div class="half_div">';
    divider('Uptime');
    system_uptimeinfo();
    close_div();
    echo '<div class="half_div">';
    divider('Server storage');
    graphic_bar($disc_used, $disc_total);
    close_div();
    echo '<div class="half_div">';
    divider('Site storage');
    graphic_bar($root_used_storage, $root_total_storage);
    close_div();
}