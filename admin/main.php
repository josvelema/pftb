<?php
session_start();
// Include the configuration file
include_once '../config.php';
// Check if admin is logged in
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: login.php');
    exit;
}
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database!');
}
// Retrieve account based on the session ID
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
$stmt->execute([ $_SESSION['account_id'] ]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Ensure account is an admin or redirectc them to the login page
if (!$account || $account['role'] != 'Admin') {
    header('Location: login.php');
    exit;
}
// The following function will be used to assign a unique icon color to our users
function color_from_string($string) {
    // The list of hex colors
    $colors = ['#34568B','#FF6F61','#6B5B95','#88B04B','#F7CAC9','#92A8D1','#955251','#B565A7','#009B77','#DD4124','#D65076','#45B8AC','#EFC050','#5B5EA6','#9B2335','#DFCFBE','#BC243C','#C3447A','#363945','#939597','#E0B589','#926AA6','#0072B5','#E9897E','#B55A30','#4B5335','#798EA4','#00758F','#FA7A35','#6B5876','#B89B72','#282D3C','#C48A69','#A2242F','#006B54','#6A2E2A','#6C244C','#755139','#615550','#5A3E36','#264E36','#577284','#6B5B95','#944743','#00A591','#6C4F3D','#BD3D3A','#7F4145','#485167','#5A7247','#D2691E','#F7786B','#91A8D0','#4C6A92','#838487','#AD5D5D','#006E51','#9E4624'];
    // Find color based on the string
    $colorIndex = hexdec(substr(sha1($string), 0, 10)) % count($colors);
    // Return the hex color
    return $colors[$colorIndex];
}
// Template admin header
function template_admin_header($title, $selected = 'orders', $selected_child = 'view') {
    $admin_links = '
        <a href="index.php"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '><i class="fas fa-tachometer-alt"></i>Dashboard</a>
        <a href="campaigns.php"' . ($selected == 'campaigns' ? ' class="selected"' : '') . '><i class="fa-solid fa-list"></i>Campaigns</a>
        <div class="sub">
            <a href="campaigns.php"' . ($selected == 'campaigns' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Campaigns</a>
            <a href="campaign.php"' . ($selected == 'campaigns' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Campaign</a>
        </div>
        <a href="newsletters.php"' . ($selected == 'newsletters' ? ' class="selected"' : '') . '><i class="fa-solid fa-envelope"></i>Newsletters</a>
        <div class="sub">
            <a href="newsletters.php"' . ($selected == 'newsletters' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Newsletters</a>
            <a href="newsletter.php"' . ($selected == 'newsletters' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Newsletter</a>
        </div>
        <a href="subscribers.php"' . ($selected == 'subscribers' ? ' class="selected"' : '') . '><i class="fa-solid fa-user-group"></i>Subscribers</a>
        <div class="sub">
            <a href="subscribers.php"' . ($selected == 'subscribers' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Subscribers</a>
            <a href="subscriber.php"' . ($selected == 'subscribers' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Subscriber</a>
        </div>
        <a href="sendmail.php"' . ($selected == 'sendmail' ? ' class="selected"' : '') . '><i class="fa-solid fa-paper-plane"></i>Send Mail</a>
        <a href="accounts.php"' . ($selected == 'accounts' ? ' class="selected"' : '') . '><i class="fas fa-users"></i>Accounts</a>
        <div class="sub">
            <a href="accounts.php"' . ($selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span>&#9724;</span>View Accounts</a>
            <a href="account.php"' . ($selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span>&#9724;</span>Create Account</a>
        </div>
        <a href="settings.php"' . ($selected == 'settings' ? ' class="selected"' : '') . '><i class="fas fa-tools"></i>Settings</a>
    ';
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>$title</title>
		<link href="admin.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.1.1/css/all.css">
	</head>
	<body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>Admin</h1>
            $admin_links
            <div class="footer">
                <a href="https://codeshack.io/package/php/advanced-newsletter-mailing-system/" target="_blank">Advanced Newsletter System</a>
                Version 1.0.1
            </div>
        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="fas fa-bars"></i>
                </a>
                <div class="space-between"></div>
                <div class="dropdown right">
                    <i class="fas fa-user-circle"></i>
                    <div class="list">
                        <a href="account.php?id={$_SESSION['account_id']}">Edit Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </header>
EOT;
}
// Template admin footer
function template_admin_footer($js_script = '') {
        $ajax_updates = ajax_updates;
        $ajax_interval = ajax_interval;
        $js_script = $js_script ? '<script>' . $js_script . '</script>' : '';
// DO NOT INDENT THE BELOW CODE
echo <<<EOT
        </main>
        <script>
        const ajax_updates = {$ajax_updates};
        const ajax_interval = {$ajax_interval};
        </script>
        <script src="admin.js"></script>
        {$js_script}
    </body>
</html>
EOT;
}
?>