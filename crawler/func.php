<?php
require '../config/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function strposa($haystack, $needles=array(), $offset=0) {
    $chr = array();
    foreach($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
    }
    if(empty($chr)) return false;
    return min($chr);
}

function get($path){
    return @json_decode(file_get_contents($path),true) ;
}

function save($path, $data, $pretty = false) {
    $dir = dirname(__FILE__)."/";

    $path = explode("/",$path);
    $checkPath = [];
    for ($i = 0; $i < count($path) -1 ; $i++) {
        $checkPath[] = $path[$i];
        $folderPath = $dir.implode("/",$checkPath);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
    }

    $file = fopen(implode("/",$path),"w");
    if ($pretty ) {
        fwrite($file,json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    } else {
        fwrite($file,json_encode($data));
    }

}

function del($path) {
    return @unlink($path);
}


function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
            $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}


function VincentyGreatCircleDistance(
    $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
  {
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $lonDelta = $lonTo - $lonFrom;
    $a = pow(cos($latTo) * sin($lonDelta), 2) +
      pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
    $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

    $angle = atan2(sqrt($a), $b);
    return $angle * $earthRadius;
  }

function showMsg($content){
    $date = date('Y-m-d H:i:s');
    if (!is_string($content)) {
        $content = print_r($content, true);
    }
    echo "[訊息][{$date}] {$content} \n";
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


function sendMail($subject, $content, $recipient){
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