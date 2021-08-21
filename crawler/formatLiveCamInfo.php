<?php
require __DIR__ . '/../vendor/autoload.php';

use Lib\GpsMapper\GpsMapper;
use Lib\SpreedSheet\Spreadsheet;

define("LIVE_CAM_LIST", "../log/LiveCamList.log");

$client = Spreadsheet::getClient(__DIR__ . '/../config/credentials.json');

$spreadSheet = $client->spreadSheet(GOOGLE_SHEET_ID);

/**
 * 取得 API 與使用次數
 */
$API_KEYS_Sheet = $spreadSheet->get("API_KEY_USED", "A", 1, "Z");
$API_KEYS = (array) formatSheetToArr($API_KEYS_Sheet->values);

/**
 * 檢查重置 API_KEYS
 */
$API_KEYS = resetAPIKeys($API_KEYS);

/**
 * 取出天氣 KEY 數量
 */
$OPEN_WEATHER_KEY_COUNT = count(array_values(array_filter($API_KEYS, function($API_INFO){
    return $API_INFO['type'] === 'OPEN_WEATHER';
})));



/**
 * 從 sheet 中取出 list
 */
$LiveCamSheet = $spreadSheet->get("LiveCamList", "A", 1, "Z");
$SheetTmpLiveCamList = (array) formatSheetToArr($LiveCamSheet->values);

/**
 * Sheet LiveCam 內容
 */
$SheetLiveCamList = [];

/**
 * 沒有 Key 的 LiveCam
 */
$SheetEmptyLiveCamList = [];

/**
 * Youtube Ids 容器
 */
$youtube_ids = [];

/**
 * GPS 容器
 */
$gps_storage = [];

/**
 * Youtube ID 對應 LiveCamKey
 */
$YoutubeIdMapToLiveCamKey = [];

/**
 * 格式化內容
 */
showMsg("從 sheet 中取得 ".count($SheetTmpLiveCamList)." 筆資料");
foreach($SheetTmpLiveCamList AS $liveCamInfo) {

    /**
     * 格式化 error 訊息成 arr
     */
    if (!empty($liveCamInfo['error'])) {
        $liveCamInfo['error'] = explode(",", $liveCamInfo['error']);
    } else {
        $liveCamInfo['error'] = [];
    }

    $liveCamInfo['gps'] = explode(",", $liveCamInfo['gps']);
    if (!empty($liveCamInfo['gps'])) {
        foreach ($liveCamInfo['gps'] AS &$val) {
            $val = (float)trim($val);
        }
    }

    /**
     * 取得 youtube_id
     */
    $youtube_ids[] = $liveCamInfo['youtube_id'] = parseYoutubeId($liveCamInfo['youtube']);

    /**
     * 檢查放入陣列
     */
    if (!empty($liveCamInfo['key'])) {
        $SheetLiveCamList[$liveCamInfo['key']] = $liveCamInfo;

        $gps_storage[$liveCamInfo['key']] = $liveCamInfo['gps'];
        $YoutubeIdMapToLiveCamKey[$liveCamInfo['youtube_id']] = $liveCamInfo['key'];
    } else {
        $SheetEmptyLiveCamList[] = $liveCamInfo;
    }
}


/**
 * 取得表單新增的內容
 */
$LiveCamInputSheet = $spreadSheet->get("LiveCamListInput", "A", 1, "Z");
$LiveCamInputSheetCount = count($LiveCamInputSheet->values);
$LiveCameInputList = (array) formatSheetToArr($LiveCamInputSheet->values);

$LiveCameInputListUsedIndex = [];
/**
 *  檢查放入 empty
 */
foreach ($LiveCameInputList AS $LiveCameInputListIndex => $_LiveCameInputList) {
    $youtube_url = $_LiveCameInputList['Youtube 影片'];
    $youtube_id = parseYoutubeId($youtube_url);
    $gps = $_LiveCameInputList['GPS 座標'];
    if (empty($gps)) {
        $gps = [];
    } else {
        $gps = explode(',', $gps);
        foreach ($gps AS $index => $val) {
            $gps[$index] = (float)trim($val);
        }
    }

    if (!empty($youtube_id)) {
        if (!in_array($youtube_id, $youtube_ids)) {
            if (count($gps) === 2) {
                $tmp = [
                    'key' => '',
                    'local' => '',
                    'city' => '',
                    'youtube' => $youtube_url,
                    'gps' => $gps,
                    'embed' => 1,
                    'error' => [],
                    'created_at' => '',
                    'updated_at' => '',
                    'youtube_id' => $youtube_id,
                ];
                $SheetEmptyLiveCamList[] = $tmp;
                $LiveCameInputListUsedIndex[] = $LiveCameInputListIndex;
                $youtube_ids[] = $youtube_id;
            }
        } else {
            $LiveCameInputListUsedIndex[] = $LiveCameInputListIndex;
        }
    }
}
/**
 * 移除表單內容
 */
if (!empty($LiveCameInputListUsedIndex)) {
    foreach ($LiveCameInputListUsedIndex AS $index) {
        unset($LiveCameInputList[$index]);
    }
    $LiveCameInputList = array_values($LiveCameInputList);
    $LiveCameInputList = formatArrToSheet($LiveCameInputList, ['時間戳記', 'Youtube 影片', 'GPS 座標']);
    $spreadSheet->clear("LiveCamListInput", "A", 1, "Z", $LiveCamInputSheetCount);
    $spreadSheet->set("LiveCamListInput", $LiveCameInputList);
}


/**
 * 如果有 empty key，產生亂碼 key 值
 */
showMsg("新加入未建立 Key ".count($SheetEmptyLiveCamList)." 筆資料");
foreach ($SheetEmptyLiveCamList AS $liveCamInfo) {
    $key = '';
    for ($i = 0; $i < 1000; $i++) {
        $key = generateRandomString(10);
        if (empty($SheetLiveCamList[$key])) {
            break;
        }
    }
    $liveCamInfo['key'] = $key;
    $SheetLiveCamList[$key] = $liveCamInfo;

    $gps_storage[$key] = $liveCamInfo['gps'];

    $YoutubeIdMapToLiveCamKey[$liveCamInfo['youtube_id']] = $key;
}


/**
 * 取得 youtube 內容
 */
$youtubeList = [];
showMsg("------ START: 開始處理 Youtube 影片 ------");
do{
    $tmp_youtube_ids = array_splice($youtube_ids, 0, 20);
    // showMsg($tmp_youtube_ids );
    showMsg("查找 Youtube data ". count($tmp_youtube_ids) ." 筆");
    $tmp_youtubeList = getYoutubeInfoByIds($tmp_youtube_ids);
    // showMsg(array_keys($tmp_youtubeList));
    $youtubeList = $youtubeList + $tmp_youtubeList;
} while ( count($youtube_ids) > 0 );


/**
 * 取出沒有 live 的 youtube
 */
$notLiveYoutubeFind = [];
foreach ($youtubeList AS $youtubeInfo) {
    $youtube_id = $youtubeInfo['id'];

    $LiveCameKey = $YoutubeIdMapToLiveCamKey[$youtube_id];
    $errorInfo = $SheetLiveCamList[$LiveCameKey]['error'];

    if (!$youtubeInfo['live']) {
        if (!in_array("NOT_FOUND_NEW_VIDEO", $errorInfo)) {
            $notLiveYoutubeFind[] = [
                'youtube_id' => $youtubeInfo['id'],
                'channel_id' => $youtubeInfo['channel_id'],
                'title' => $youtubeInfo['localized']['title'] ?? $youtubeInfo['title'],
            ];
        }
    }
}
showMsg("發現不是直播有 ". count($notLiveYoutubeFind) ." 筆");


/**
 * 查詢沒有 live 的 youtube，嘗試尋找下一個檔案
 */
$transNewYoutubeIdsMapping = [];
$ErrorLog = [];
showMsg("嘗試根據 title 重找新影片");
foreach ($notLiveYoutubeFind AS $_notLiveYoutubeFind) {
    showMsg("查找：{$_notLiveYoutubeFind['title']}");
    $search_list = searchYoutubeByKeyword($_notLiveYoutubeFind['title'], $_notLiveYoutubeFind['channel_id'], true);

    if (!empty($search_list)) {
        showMsg("發現： {$search_list[0]['title']}");
        showMsg("相似度：{$search_list[0]['similar']}");
        if ($search_list[0]['similar'] > 70) {
            $transNewYoutubeIdsMapping[$_notLiveYoutubeFind['youtube_id']] = $search_list[0]['id'];
        }
    } else {
        showMsg("查無資料相似影片");
    }
}

showMsg("查詢新查到的影片");
if (!empty($transNewYoutubeIdsMapping)) {
    $transNewYoutubeIds = array_values($transNewYoutubeIdsMapping);
    do{
        $tmp_youtube_ids = array_splice($transNewYoutubeIds, 0, 10);
        $tmp_youtubeList = getYoutubeInfoByIds($tmp_youtube_ids);
        $youtubeList = $youtubeList + $tmp_youtubeList;
    } while ( count($transNewYoutubeIds) > 0 );
}

showMsg("------ END: 處理 Youtube 影片 ------");


showMsg("------ START: 開始處理 GPS 對應天氣 ------");
/**
 * 計算 gps 分群
 */
list('GroupInfo' => $GPSGroupInfo, 'PointMapping' => $pointGroupMapping) = calcGPSGroup($gps_storage);

showMsg("GPS 分群出 ".count(array_values($GPSGroupInfo))." 組");

/**
 * 取得分群 GPS 天氣
 */
foreach ($GPSGroupInfo AS $index => &$_GPSGroupInfo) {
    showMsg("取得 ".implode(",", $_GPSGroupInfo['center'])." 天氣");
    $_GPSGroupInfo['weather'] = getWeatherInfo($_GPSGroupInfo['center']);
}
showMsg("------ END: 開始處理 GPS 對應天氣 ------");


$gpsMapper = new GpsMapper();

$LiveCamList = [];


$ResponseMailError = [];
foreach ($SheetLiveCamList AS &$liveCamInfo) {
    $orgLiveCamInfo = json_decode(json_encode($liveCamInfo), true);
    $key = $liveCamInfo['key'];
    showMsg("執行 Live Cam Key: {$key}");

    /**
     * 取得 youtube_id
     */
    $youtube_id = $liveCamInfo['youtube_id'];

    /**
     * 檢查是否有被換過
     */
    if (isset($transNewYoutubeIdsMapping[$youtube_id])) {
        $youtube_id = $transNewYoutubeIdsMapping[$youtube_id];
    }

    /**
     *  重建網址
     */

    $liveCamInfo['youtube'] = "https://www.youtube.com/watch?v={$youtube_id}";


    /**
     * 取得影片資訊
     */
    $youtubeInfo = $youtubeList[$youtube_id];


    /**
     * 取得天氣分群 key
     */
    $weatherGroupKey = $pointGroupMapping[$key];
    $weatherInfo = $GPSGroupInfo[$weatherGroupKey]['weather'];

    $gps = false;
    $timezone = false;
    if (isset($liveCamInfo['gps']) && isset($liveCamInfo['gps'][0]) && isset($liveCamInfo['gps'][1])) {
        $gps = [
            "lat" => $liveCamInfo['gps'][0],
            "lng" => $liveCamInfo['gps'][1],
        ];
        list("timezone" => $code, "hour" => $hour) = $gpsMapper->latLngToTimezoneString($gps['lat'], $gps['lng']);
        $timezone = [
            "code" => $code,
            "sec" => $hour * 3600,
        ];
    }

    $weather = false;
    if (!empty($weatherInfo)) {
        /**
         * 重設國家
         */
        if (empty($liveCamInfo['local'])) {
            $liveCamInfo['local'] = $weatherInfo['sys']['country'];
        }

        /**
         * 重設城市
         */
        if (empty($liveCamInfo['city'])) {
            $liveCamInfo['city'] = $weatherInfo['name'];
        }

        /**
         * 重設時區
         */
        $timezone['sec'] = $weatherInfo['timezone'];
        $weather = [
            'sunrise' => $weatherInfo['sys']['sunrise'],
            'sunset' => $weatherInfo['sys']['sunset'],
            'wind_speed' => $weatherInfo['wind']['speed'],
            'wind_deg' => $weatherInfo['wind']['deg'],
            // 'wind_gust' => $weatherInfo['wind']['gust'],
            'weather_desc' => $weatherInfo['weather'][0]['description'],
            'weather_icon' => $weatherInfo['weather'][0]['icon'],
            'weather_status' => $weatherInfo['weather'][0]['main'],
            'base' => $weatherInfo['base'],
            'temp' => $weatherInfo['main']['temp'],
            'feels_like' => $weatherInfo['main']['feels_like'],
            'temp_min' => $weatherInfo['main']['temp_min'],
            'temp_max' => $weatherInfo['main']['temp_max'],
            'pressure' => $weatherInfo['main']['pressure'],
            'humidity' => $weatherInfo['main']['humidity'],
            // 'sea_level' => $weatherInfo['main']['sea_level'],
            // 'grnd_level' => $weatherInfo['main']['grnd_level'],
        ];
    }

    $video_info = false;
    if (!empty($youtubeInfo)) {
        $video_info = [
            "youtube_id" => $youtube_id,
            "title" => $youtubeInfo["title"],
            "description" => $youtubeInfo["description"],
            "channel_id" => $youtubeInfo["channel_id"],
            "channel_title" => $youtubeInfo["channel_title"],
            "tags" => $youtubeInfo["tags"],
            "live" => $youtubeInfo["live"],
            "thumbnail" => $youtubeInfo["thumbnail"],
            "published_at" => $youtubeInfo["published_at"],
            "statistics" => $youtubeInfo["statistics"],
        ];
    }


    $tmp = [
        "key" => $key,
        "local" => $liveCamInfo['local'],
        "city" => $liveCamInfo['city'],
        "youtube" => $liveCamInfo['youtube'],
        "gps" => $gps,
        "embed" => $liveCamInfo['embed'],
        "timezone" => $timezone,

        "video" => $video_info,

        "weather" => $weather,
    ];

    if (!$video_info['live']) {
        if (!in_array("NOT_FOUND_NEW_VIDEO", $SheetLiveCamList[$key]['error'])) {
            $SheetLiveCamList[$key]['error'][] = "NOT_FOUND_NEW_VIDEO";

            $ResponseMailError[] = [
                "key" => $key,
                "local" => $liveCamInfo['local'],
                "city" => $liveCamInfo['city'],
                "youtube" => $liveCamInfo['youtube'],
                "error" => "NOT_FOUND_NEW_VIDEO",
            ];
        }
    } else {
        $error = $SheetLiveCamList[$key]['error'];
        $error = array_values(array_filter($error, function($info){
            return $info !== 'NOT_FOUND_NEW_VIDEO';
        }));
        $SheetLiveCamList[$key]['error'] = $error;
    }

    $LiveCamList[] = $tmp;

    if (empty($liveCamInfo['created_at'])) {
        $liveCamInfo['created_at'] = date('Y-m-d H:i:s');
    }

    if (json_encode($orgLiveCamInfo) !== json_encode($liveCamInfo)) {
        $liveCamInfo['updated_at'] = date('Y-m-d H:i:s');
    }
}


save(LIVE_CAM_LIST, $LiveCamList, true);


/**
 * 儲存回 Sheet LiveCamList
 */
showMsg("寫回 Sheet 中");
$SheetLiveCamList = array_values($SheetLiveCamList);
$SheetLiveCamList = array_orderby($SheetLiveCamList, "local", SORT_ASC, "city", SORT_ASC);
$SheetLiveCamList = formatArrToSheet($SheetLiveCamList, ['key','local', 'city', 'youtube', 'gps', 'embed', 'error', 'created_at', 'updated_at']);
$spreadSheet->set("LiveCamList", $SheetLiveCamList);

/**
 * 儲存回 Sheet
 */

$API_KEYS = array_values($API_KEYS);
$API_KEYS = array_orderby($API_KEYS, "type", SORT_ASC, "quota", SORT_ASC);
$API_KEYS = formatArrToSheet($API_KEYS, ['type','key','quota', 'from']);
$spreadSheet->set("API_KEY_USED", $API_KEYS);


if (!empty($ResponseMailError)) {
    // "key" => $key,
    // "local" => $liveCamInfo['local'],
    // "city" => $liveCamInfo['city'],
    // "youtube" => $liveCamInfo['youtube'],
    // "error" => "NOT_FOUND_NEW_VIDEO",

    $content[] = "<a target='_blank' href='https://docs.google.com/spreadsheets/d/1zCB6jNEbz4tBRKNBRI50Da-Cx8NfdYND2MOqMZk5_SA/edit#gid=1640255966'>Live Cam 4k 表格</a>";
    $content[] = "<table style='width: 100%;' border='1' cellspacing='0' cellpadding='5'>";
    $content[] = "
    <tr style='background: #666; color: #FFF;'>
        <th>key</th>
        <th>local</th>
        <th>city</th>
        <th>youtube</th>
        <th>error</th>
    </tr>";
    foreach ($ResponseMailError AS $index => $_ResponseMailError) {
        $style = [];
        $style[] = ($index % 2) ? 'background:#DDD': '';
        $style = implode(";", $style);


        $content[] = "
        <tr style='{$style}'>
            <td>{$_ResponseMailError['key']}</td>
            <td>{$_ResponseMailError['local']}</td>
            <td>{$_ResponseMailError['city']}</td>
            <td>{$_ResponseMailError['youtube']}</td>
            <td>{$_ResponseMailError['error']}</td>
        </tr>
        ";
    }
    $content[] = '</table>';

    $content = implode("\n", $content);

    sendMail("[警告] 有影片處理失敗", $content, OWNER_MAIL);
}

























/**
 * Sheet 內容轉 array
 *
 * @param [type] $data
 * @return void
 */
function formatSheetToArr(array $data) {
    $columns = array_shift($data);
    $list = [];
    foreach ($data AS $_data) {
        if (!empty($_data)) {
            $tmp = [];
            foreach ($columns AS $key => $column) {
                $tmp[$column] = $_data[$key] ?? '';
            }
            $list[] = $tmp;
        }
    }
    return $list;
}

/**
 * array 內容轉 Sheet
 *
 * @param [type] $data
 * @return void
 */
function formatArrToSheet($data, $columns) {
    $list = [];
    $list[] = $columns;
    if (!empty($data)) {
        // $columns = array_keys($data[0]);
        // $columns = ['key','local', 'city', 'youtube', 'gps', 'embed', 'error'];
        foreach ($data AS $_data) {
            $tmp = [];
            if (isset($_data['error'])) {
                $_data['error'] = array_values(array_unique($_data['error']));
            }

            foreach ($columns AS $key => $column) {
                $value = $_data[$column];
                if (is_array($value)) {
                    $value = implode(",", $value);
                }
                $tmp[$key] = $value;
            }
            $list[] = $tmp;
        }
    }
    return $list;
}

/**
 * 取得 youtube_id
 *
 * @param [type] $url
 * @return void
 */
function parseYoutubeId($url) {
    $youtube_id = '';
    if (preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches)) {
        $_data['youtube_id'] = $matches[0];

        $youtube_id = $_data['youtube_id'];
    }
    return $youtube_id;
}


/**
 * 取得 youtube 內容
 *
 * @param [type] $youtube_ids
 * @return void
 */
function getYoutubeInfoByIds(array $youtube_ids)
{
    $youtube_ids = array_values(array_filter($youtube_ids, function($youtube_id){
        return !empty($youtube_id);
    }));

    $YOUTUBE_API_KEY = getAPIKey('YOUTUBE', 1);

    $params = [
        "part" => "snippet,contentDetails,statistics",
        "id" => implode(",", $youtube_ids),
        "key" => $YOUTUBE_API_KEY,
    ];

    $url = "https://youtube.googleapis.com/youtube/v3/videos?".http_build_query($params);
    showMsg("執行： {$url}");

    $data = @json_decode(file_get_contents($url), true);
    $youtubeList = [];
    foreach ($data['items'] AS $item) {
        $thumbnail = array_pop($item['snippet']['thumbnails']);
        $tmp = [
            'id' => $item['id'],
            'title' => $item['snippet']['title'] ?? '',
            'description' => $item['snippet']['description'] ?? '',
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
            'localized' => $item['snippet']['localized'],
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


function searchYoutubeByKeyword(string $keyword, $channelId)
{
    $YOUTUBE_API_KEY = getAPIKey('YOUTUBE', 100);
    $params = [
        "part" => "snippet",
        // "type" => "video",
        // "channelId" => $channelId,
        // "eventType" => 'live',
        "q" => $keyword,
        "key" => $YOUTUBE_API_KEY,
        "maxResults" => 10,
    ];

    $url = "https://www.googleapis.com/youtube/v3/search?".http_build_query($params);
    showMsg("執行： {$url}");

    $data = @json_decode(file_get_contents($url), true);

    $youtubeList = [];
    if (!empty($data['items'])) {
        foreach ($data['items'] AS $item) {
            if ($item['snippet']['channelId'] === $channelId && !empty($item['id']['videoId'])) {
                $thumbnail = array_pop($item['snippet']['thumbnails']);
                $id = $item['id']['videoId'];
                $tmp = [
                    'id' => $id,
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
                    'localized' => $item['snippet']['localized'] ?? '',
                ];

                if (!empty($tmp['tags'])) {
                    $tmp['tags'] = array_values(array_unique($tmp['tags']));
                } else {
                    $tmp['tags'] = [];
                }

                similar_text($keyword, $tmp['title'], $similar);
                $tmp['similar'] = $similar;

                $youtubeList[] = $tmp;
            }
        }

        usort($youtubeList, function($a, $b){
            if ($a['similar'] < $b['similar']) {
                return 1;
            } else if ($a['similar'] === $b['similar']) {
                return 0;
            } else {
                return -1;
            }
        });

    }

    return $youtubeList;
}


function calcGPSGroup($gps_storage) {
    $groupMapping = [];

    $i = 0;
    foreach ($gps_storage AS $liveKey => $gps) {
        $matchGroupKey = null;
        foreach ($groupMapping AS $groupKey => $_groupMapping) {
            $distance = VincentyGreatCircleDistance($gps[0], $gps[1], $_groupMapping['center'][0], $_groupMapping['center'][1]);
            if ($distance < 5000) {
                $matchGroupKey = $groupKey;
                break;
            }
        }
        if ($matchGroupKey === null) {
            $matchGroupKey = generateRandomString(10);
            $groupMapping[$matchGroupKey] = [
                'groupKey' => $matchGroupKey,
                'center' => [],
                'liveKeys' => [],
                'gps' => [],
            ];
        }

        $groupMapping[$matchGroupKey]['gps'][] = $gps;
        $groupMapping[$matchGroupKey]['liveKeys'][] = $liveKey;
        $groupMapping[$matchGroupKey]['center'] = calcGpsCenter($groupMapping[$matchGroupKey]['gps']);
    }

    $pointGroupMapping = [];
    $GPSGroupInfo = [];
    foreach ($groupMapping AS $groupKey => $info) {
        $GPSGroupInfo[$groupKey] = [
            'center' => $info['center'],
            'weather' => false,
        ];
        foreach ($info['liveKeys'] AS $liveKey) {
            $pointGroupMapping[$liveKey] = $groupKey;
        }
    }

    return ['GroupInfo' => $GPSGroupInfo, 'PointMapping' => $pointGroupMapping];
}

function calcGpsCenter($points) {
    $lat = [];
    $lng = [];
    foreach ($points AS $point) {
        $lat[] = $point[0];
        $lng[] = $point[1];
    }

    $avg_gps = [];
    $avg_gps[] = array_sum($lat) / count($lat);
    $avg_gps[] = array_sum($lng) / count($lng);
    return $avg_gps;
}


$getWeatherInfoCount = 0;
function getWeatherInfo($point){
    global $getWeatherInfoCount, $OPEN_WEATHER_KEY_COUNT;

    $APIKeyIndex = $getWeatherInfoCount % $OPEN_WEATHER_KEY_COUNT;
    if ($getWeatherInfoCount !== 0 && $APIKeyIndex === 0 && floor($getWeatherInfoCount / $OPEN_WEATHER_KEY_COUNT) % 4 == 0) {
        showMsg("sleep 5");
        sleep(5);
    }

    $APIKey = getAPIKey('OPEN_WEATHER', 1);


    $params = [
        'lat' => $point[0],
        'lon' => $point[1],
        'appid' => $APIKey,
        'units' => 'standard',
    ];

    $url = "https://api.openweathermap.org/data/2.5/weather?".http_build_query($params);
    showMsg("執行： {$url}");
    $data = @json_decode(file_get_contents($url), true);
    $getWeatherInfoCount += 1;
    return $data;
}


function getAPIKey($type, $quota){
    global $API_KEYS;
    usort($API_KEYS, function($a, $b){
        if ($a['quota'] > $b['quota']) {
            return 1;
        }  elseif ($a === $b) {
            return 0;
        } else {
            return -1;
        }
    });

    foreach ($API_KEYS AS $index => $API_INFO) {
        $API_KEYS[$index]['index'] = $index;
    }

    $API_KEYS_FILTER = array_values(array_filter($API_KEYS, function($API_INFO) use ($type){
        return $API_INFO['type'] === $type;
    }));
    showMsg($API_KEYS_FILTER[0]);

    $index = $API_KEYS_FILTER[0]['index'];
    $key = $API_KEYS[$index]['key'];
    $API_KEYS[$index]['quota'] += $quota;

    return $key;
}


function resetAPIKeys($API_KEYS) {

    $reset_Youtube = false;
    $reset_Weather = false;

    $HourTime = "00:00:00";
    $now = time();
    $reset_Youtube_timestamp = strtotime(date("Y-m-d {$HourTime}"));
    $reset_Weather_timestamp = strtotime(date("Y-m-01 {$HourTime}"));

    if ($reset_Youtube_timestamp > $now) {
        $reset_Youtube = true;
        showMsg('API KEY: Youtube quota 需要重置');
    }

    if ($reset_Weather_timestamp > $now) {
        $reset_Weather = true;
        showMsg('API KEY: Weather quota 需要重置');
    }

    if ($reset_Youtube || $reset_Weather) {
        foreach ($API_KEYS AS $index => $API_INFO) {
            $reset = false;
            if ($API_INFO['type'] === 'YOUTUBE' && $reset_Youtube) {
                $reset = true;
            }

            if ($API_INFO['type'] === 'OPEN_WEATHER' && $reset_Weather) {
                $reset = true;
            }

            if ($reset) {
                $API_KEYS[$index]['quota'] = 0;
            }
        }
    }

    return $API_KEYS;
}