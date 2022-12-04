<?php
// database hostname, you don't usually need to change this
define('db_host','localhost');
// database username
define('db_user','root');
// database password
define('db_pass','');
// database name
define('db_name','pftb');
// database charset, change this only if utf8 is not supported by your language
define('db_charset','utf8');
// URL to newsletter root directory
define('website_url','http://localhost/newsletter/');
// email confirmation
define('email_confirmation',true);
/* Mail */
// Send mail from which address?
define('mail_from','newsletters@example.com');
// Mail from name
define('mail_from_name','Your Business Name');
// Is SMTP server?
define('SMTP',false);
// SMTP Hostname
define('smtp_host','smtp.example.com');
// SMTP Port number
define('smtp_port',465);
// SMTP Username
define('smtp_user','user@example.com');
// SMTP Password
define('smtp_pass','secret');
/* Cron */
// cron secret
define('cron_secret','your_secret_key');
// Cron url
define('cron_mails_per_request',1);
// Cron sleep per request in seconds
define('cron_sleep_per_request',5);
/* Admin */
// Enable AJAX updates in the admin panel?
define('ajax_updates',true);
// AJAX interval in seconds
define('ajax_interval',10000);
?>