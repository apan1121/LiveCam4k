<?php

define('SMTP_Host', 'smtp.gmail.com');
define('SMTP_Username', '');
define('SMTP_Password', '');

define('FROM_EMAIL', 'livecam4k@gmail.com');
define('FROM_NAME', 'LiveCam4K');

define('WEB_DOMAIN', 'https://apan1121.github.io/liveCam4k');

$OWNER_MAIL = [];
$OWNER_MAIL[] = [
    'email' => 'apan1121@gmail.com',
    'name' => 'apan1121',
];

define('OWNER_MAIL', $OWNER_MAIL);

$NOTICE_MAIL = [];
define('NOTICE_MAIL', $NOTICE_MAIL);

define('GOOGLE_SHEET_ID', '');
define('YOUTUBE_API_KEY', '');