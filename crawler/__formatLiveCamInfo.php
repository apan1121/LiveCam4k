<?php
require __DIR__ . '/../vendor/autoload.php';

use Lib\GpsMapper\GpsMapper;
use Lib\SpreedSheet\Spreadsheet;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

define("LIVE_CAM_LIST", "../log/LiveCamList.log");

$client = Spreadsheet::getClient(__DIR__ . '/../config/credentials.json');

$spreadSheet = $client->spreadSheet(GOOGLE_SHEET_ID);
$tabs = $spreadSheet->getSheets();

$tabNames = [];
foreach ($tabs AS $tabItem) {
    $tabNames[] = $tabItem->properties->title;
}

$data = [];
foreach ($tabNames AS $tabName) {
    $tmpData = $spreadSheet->get($tabName, "A", 1, "Z");
    $tmpData = $tmpData->values;
    $columns = array_shift($tmpData);
    foreach ($tmpData AS $_data) {
        if (!empty($_data)) {
            $tmp = [];
            foreach ($columns AS $key => $column) {
                $tmp[$column] = $_data[$key] ?? '';
            }
            $data[] = $tmp;
        }
    }
}


$youtube_ids = [];
$sheetData = [];

$gpsMapper = new GpsMapper();

foreach ($data AS $_data) {
    if (!empty($_data['gps'])){
        $gps = explode(",", $_data['gps']);
        $_data['gps'] = [
            'lat' => (float)$gps[0],
            'lng' => (float)$gps[1],
        ];
    } else {
        $_data['gps'] = false;
    }

    if (!empty($_data['gps'])) {
        list("timezone" => $timezone, "hour" => $hour) = $gpsMapper->latLngToTimezoneString($_data['gps']['lat'], $_data['gps']['lng']);
        $_data['timezone'] = $timezone;
        $_data['timezone_hour'] = $hour;
    } else {
        $_data['timezone'] = false;
        $_data['timezone_hour'] = $false;
    }

    $_data['youtube_id'] = false;
    if (preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $_data['youtube'], $matches)) {
        $_data['youtube_id'] = $matches[0];

        $youtube_ids[] = $_data['youtube_id'];
    }
    $sheetData[] = $_data;
}

$youtubeList = [];

do{
    $tmp_youtube_ids = array_splice($youtube_ids, 0, 10);
    $tmp_youtubeList = getYoutubeInfoByIds($tmp_youtube_ids);
    $youtubeList = $youtubeList + $tmp_youtubeList;
} while ( count($youtube_ids) > 0 );


$output = [];
$error = [];
foreach ($sheetData AS $_sheetData) {
    $youtube_id = $_sheetData['youtube_id'];
    if (!empty($youtubeList[$youtube_id])) {
        $video_info = $youtubeList[$youtube_id];
        $output[] = $_sheetData + [
            'title' => $video_info['title'],
            'channel_id' => $video_info['channel_id'],
            'channel_title' => $video_info['channel_title'],
            'tags' => $video_info['tags'],
            'live' => $video_info['live'],
            'thumbnail' => $video_info['thumbnail'],
            'published_at' => $video_info['published_at'],
            'statistics' => $video_info['statistics'],
        ];

        if ($video_info['live'] === false) {
            $error[] = [
                'type' => 'Not Live',
                'youtube_id' => $youtube_id,
            ];
        }
    } else {
        $error[] = [
            'type' => 'Not Found',
            'local' => $_sheetData['local'],
            'serial_number' => $_sheetData['serial_number'],
            'youtube_id' => $youtube_id,
        ];
    }
}

save(LIVE_CAM_LIST, $output, true);

if (!empty($error)) {
    $error_string = json_encode($error, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    $error_string = "<code><pre>{$error_string}</pre></code>";
    sendErrorMail("[警告] 有影片處理失敗", $error_string, OWNER_MAIL);
}





function getYoutubeInfoByIds($youtube_ids)
{
    $youtube_ids = array_values(array_filter($youtube_ids, function($youtube_id){
        return !empty($youtube_id);
    }));

    $params = [
        "part" => "snippet,contentDetails,statistics",
        "id" => implode(",", $youtube_ids),
        "key" => YOUTUBE_API_KEY,
    ];

    $url = "https://youtube.googleapis.com/youtube/v3/videos?".http_build_query($params);
    $data = @json_decode(file_get_contents($url), true);

    $youtubeList = [];
    foreach ($data['items'] AS $item) {
        $thumbnail = array_pop($item['snippet']['thumbnails']);
        $tmp = [
            'id' => $item['id'],
            'title' => $item['snippet']['title'] ?? '',
            'channel_id' => $item['snippet']['channelId'] ?? '',
            'channel_title' => $item['snippet']['channelTitle'] ?? '',
            'tags' => $item['snippet']['tags'] ?? '',
            'live' => $item['snippet']['liveBroadcastContent'] === 'live' ?? '',
            'thumbnail' => $thumbnail,
            'published_at' => $item['snippet']['publishedAt'],
            'statistics' => [
                'view_count' => (int)($item['statistics']['viewCount'] ?? 0),
                'like_count' => (int)($item['statistics']['likeCount'] ?? 0),
                'dislike_count' => (int)($item['statistics']['dislikeCount'] ?? 0),
                'favorite_count' => (int)($item['statistics']['favoriteCount'] ?? 0),
                'comment_count' => (int)($item['statistics']['commentCount'] ?? 0),
            ],
        ];

        if (!empty($tmp['tags'])) {
            $tmp['tags'] = array_values(array_unique($tmp['tags']));
        } else {
            $tmp['tags'] = [];
        }

        $youtubeList[$item['id']] = $tmp;
    }
    return $youtubeList;
}


function sendErrorMail($subject, $content, $recipient){
    $mail = getMailer();

    foreach ($recipient AS $_recipient) {
        $mail->addAddress($_recipient['email'], $_recipient['name']);     //Add a recipient        //Name is optional
    }

                                 //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->AltBody = $content;

    $mail->send();

}

function getMailer(){
    $mail = new PHPMailer(true);
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = SMTP_Host;                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = SMTP_Username;                     //SMTP username
    $mail->Password   = SMTP_Password;                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;
    $mail->CharSet="UTF-8";                                  //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom(FROM_EMAIL, FROM_NAME);

    //Content
    $mail->isHTML(true);
    return $mail;
}