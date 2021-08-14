<?php
// $data = file_get_contents("https://gist.githubusercontent.com/curran/a1b7699480e05ed923be0115381d8f19/raw/ebded09c550b026b0359627be8718f580980ef2b/Timezones.csv");
// echo $data;

$handle = fopen("https://gist.githubusercontent.com/curran/a1b7699480e05ed923be0115381d8f19/raw/ebded09c550b026b0359627be8718f580980ef2b/Timezones.csv", 'r');
$array = [];
if ($handle) {
    while (($row = fgetcsv($handle, 4096)) !== false) {
        if (empty($fields)) {
            $fields = $row;
            continue;
        }
        foreach ($row as $k=>$value) {
            $array[$i][$fields[$k]] = $value ? trim($value) : '';
        }
        $i++;
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

// print_r($array);

$mapping = [];

foreach ($array AS $item) {
    $hour = $item['Raw offset (Hours : Minutes)'];
    $hour = explode(':', $hour);
    $mapping[$item['Time zone ID']] = trim($hour[0]);
}
$mapping['America/Argentina/Mendoza'] = -3;
$mapping['Atlantic/Reykjavik'] = 0;
$mapping['America/Sitka'] = -9;
$mapping["Asia/Ho_Chi_Minh"] = 7;
$mapping["America/Bahia_Banderas"] = -6;
$mapping["Australia/Currie"] = 10;
$mapping["America/North_Dakota/New_Salem"] = -6;
$mapping["America/Indiana/Petersburg"] = -5;
$mapping["Antarctica/Macquarie"] = 10;
$mapping["America/Coral_Harbour"] = -5;
$mapping["America/North_Dakota/Beulah"] = -6;
$mapping["America/Moncton"] = -4;
$mapping["Australia/Eucla"] = 8;
$mapping["America/Argentina/Rio_Gallegos"] = -3;
$mapping["Asia/Novokuznetsk"] = 7;
$mapping["America/Argentina/Catamarca"] = -3;
$mapping["America/Indiana/Tell_City"] = -6;
$mapping["Europe/Volgograd"] = 3;
$mapping["Asia/Khandyga"] = 9;
$mapping["uninhabited"] = 0;
$mapping["America/Argentina/Salta"] = -3;
$mapping["America/Argentina/Tucuman"] = -3;
$mapping["America/Ojinaga"] = -7;
$mapping["America/Kralendijk"] = -4;
$mapping["Europe/Jersey"] = 0;
$mapping["Asia/Ust-Nera"] = 10;
$mapping["Africa/Asmara"] = 3;
$mapping["America/Resolute"] = -6;
$mapping["America/Argentina/Jujuy"] = -3;
$mapping["America/Bahia"] = -3;
$mapping["America/St_Barthelemy"] = -4;
$mapping["Europe/Mariehamn"] = 2;
$mapping["America/Tijuana"] = -8;
$mapping["America/Metlakatla"] = -9;
$mapping["Europe/Podgorica"] = 1;
$mapping["Pacific/Chuuk"] = 10;
$mapping["Pacific/Pohnpei"] = 11;
$mapping["America/Atikokan"] = -5;
$mapping["America/Argentina/Buenos_Aires"] = -3;
$mapping["America/Toronto"] = -5;
$mapping["Europe/Isle_of_Man"] = 0;
$mapping["America/Blanc-Sablon"] = -4;
$mapping["America/Indiana/Winamac"] = -5;
$mapping["America/Argentina/La_Rioja"] = -3;
$mapping["America/Creston"] = -7;
$mapping["Asia/Kathmandu"] = 6;
$mapping["America/Argentina/Cordoba"] = -3;
$mapping["America/Argentina/Ushuaia"] = -3;
$mapping["Europe/Guernsey"] = 0;
$mapping["America/Indiana/Vincennes"] = -5;
$mapping["America/Santarem"] = -3;
$mapping["America/Argentina/San_Luis"] = -3;
$mapping["America/Campo_Grande"] = -4;
$mapping["Asia/Hebron"] = 2;
$mapping["America/Argentina/San_Juan"] = -3;
$mapping["America/Lower_Princes"] = -4;
$mapping["Atlantic/Faroe"] = 0;
$mapping["America/Santa_Isabel"] = -8;
$mapping["Asia/Kolkata"] = 6;
$mapping["America/Matamoros"] = -6;
$mapping["America/Marigot"] = -4;
$mapping["Africa/Juba"] = 2;


$timezoneStrings = [
    "unknown",
    "America/Dominica",
    "America/St_Vincent",
    "Australia/Lord_Howe",
    "Asia/Kashgar",
    "Pacific/Wallis",
    "Europe/Berlin",
    "America/Manaus",
    "Asia/Jerusalem",
    "America/Phoenix",
    "Australia/Darwin",
    "Asia/Seoul",
    "Africa/Gaborone",
    "Indian/Chagos",
    "America/Argentina/Mendoza",
    "Asia/Hong_Kong",
    "America/Godthab",
    "Africa/Dar_es_Salaam",
    "Pacific/Majuro",
    "America/Port-au-Prince",
    "America/Montreal",
    "Atlantic/Reykjavik",
    "America/Panama",
    "America/Sitka",
    "Asia/Ho_Chi_Minh",
    "America/Danmarkshavn",
    "Asia/Jakarta",
    "America/Boise",
    "Asia/Baghdad",
    "Africa/El_Aaiun",
    "Europe/Zagreb",
    "America/Santiago",
    "America/Merida",
    "Africa/Nouakchott",
    "America/Bahia_Banderas",
    "Australia/Perth",
    "Asia/Sakhalin",
    "Asia/Vladivostok",
    "Africa/Bissau",
    "America/Los_Angeles",
    "Asia/Rangoon",
    "America/Belize",
    "Asia/Harbin",
    "Australia/Currie",
    "Pacific/Pago_Pago",
    "America/Vancouver",
    "Asia/Magadan",
    "Asia/Tbilisi",
    "Asia/Yerevan",
    "Europe/Tallinn",
    "Pacific/Johnston",
    "Asia/Baku",
    "America/North_Dakota/New_Salem",
    "Europe/Vilnius",
    "America/Indiana/Petersburg",
    "Asia/Tehran",
    "America/Inuvik",
    "Europe/Lisbon",
    "Europe/Vatican",
    "Pacific/Chatham",
    "Antarctica/Macquarie",
    "America/Araguaina",
    "Asia/Thimphu",
    "Atlantic/Madeira",
    "America/Coral_Harbour",
    "Pacific/Funafuti",
    "Indian/Mahe",
    "Australia/Adelaide",
    "Africa/Freetown",
    "Atlantic/South_Georgia",
    "Africa/Accra",
    "America/North_Dakota/Beulah",
    "America/Jamaica",
    "America/Scoresbysund",
    "America/Swift_Current",
    "Europe/Tirane",
    "Asia/Ashgabat",
    "America/Moncton",
    "Europe/Vaduz",
    "Australia/Eucla",
    "America/Montserrat",
    "America/Glace_Bay",
    "Atlantic/Stanley",
    "Africa/Bujumbura",
    "Africa/Porto-Novo",
    "America/Argentina/Rio_Gallegos",
    "America/Grenada",
    "Asia/Novokuznetsk",
    "America/Argentina/Catamarca",
    "America/Indiana/Indianapolis",
    "America/Indiana/Tell_City",
    "America/Curacao",
    "America/Miquelon",
    "America/Detroit",
    "America/Menominee",
    "Asia/Novosibirsk",
    "Africa/Lagos",
    "Indian/Cocos",
    "America/Yakutat",
    "Europe/Volgograd",
    "Asia/Qatar",
    "Indian/Antananarivo",
    "Pacific/Marquesas",
    "America/Grand_Turk",
    "Asia/Khandyga",
    "America/North_Dakota/Center",
    "Pacific/Guam",
    "Pacific/Pitcairn",
    "America/Cambridge_Bay",
    "Asia/Bahrain",
    "America/Kentucky/Monticello",
    "Arctic/Longyearbyen",
    "Africa/Cairo",
    "Australia/Hobart",
    "Pacific/Galapagos",
    "Asia/Oral",
    "America/Dawson_Creek",
    "Africa/Mbabane",
    "America/Halifax",
    "Pacific/Tongatapu",
    "Asia/Aqtau",
    "Asia/Hovd",
    "uninhabited",
    "Africa/Nairobi",
    "Asia/Ulaanbaatar",
    "Indian/Christmas",
    "Asia/Taipei",
    "Australia/Melbourne",
    "America/Argentina/Salta",
    "Australia/Broken_Hill",
    "America/Argentina/Tucuman",
    "America/Kentucky/Louisville",
    "Asia/Jayapura",
    "Asia/Macau",
    "America/Ojinaga",
    "America/Nome",
    "Pacific/Wake",
    "Europe/Andorra",
    "America/Iqaluit",
    "America/Kralendijk",
    "Europe/Jersey",
    "Asia/Ust-Nera",
    "Asia/Yakutsk",
    "America/Yellowknife",
    "America/Fortaleza",
    "Asia/Irkutsk",
    "America/Tegucigalpa",
    "Europe/Zaporozhye",
    "Pacific/Fiji",
    "Pacific/Tarawa",
    "Africa/Asmara",
    "Asia/Dhaka",
    "Asia/Pyongyang",
    "Europe/Athens",
    "America/Resolute",
    "Africa/Brazzaville",
    "Africa/Libreville",
    "Atlantic/St_Helena",
    "Europe/Samara",
    "America/Adak",
    "America/Argentina/Jujuy",
    "America/Chicago",
    "Africa/Sao_Tome",
    "Europe/Bratislava",
    "Asia/Riyadh",
    "America/Lima",
    "America/New_York",
    "America/Pangnirtung",
    "Asia/Samarkand",
    "America/Port_of_Spain",
    "Africa/Johannesburg",
    "Pacific/Port_Moresby",
    "America/Bahia",
    "Europe/Zurich",
    "America/St_Barthelemy",
    "Asia/Nicosia",
    "Europe/Kaliningrad",
    "America/Anguilla",
    "Europe/Ljubljana",
    "Asia/Yekaterinburg",
    "Africa/Kampala",
    "America/Rio_Branco",
    "Africa/Bamako",
    "America/Goose_Bay",
    "Europe/Moscow",
    "Africa/Conakry",
    "America/Chihuahua",
    "Europe/Warsaw",
    "Pacific/Palau",
    "Europe/Mariehamn",
    "Africa/Windhoek",
    "America/La_Paz",
    "America/Recife",
    "America/Mexico_City",
    "Asia/Amman",
    "America/Tijuana",
    "America/Metlakatla",
    "Pacific/Midway",
    "Europe/Simferopol",
    "Europe/Budapest",
    "Pacific/Apia",
    "America/Paramaribo",
    "Africa/Malabo",
    "Africa/Ndjamena",
    "Asia/Choibalsan",
    "America/Antigua",
    "Europe/Istanbul",
    "Africa/Blantyre",
    "Australia/Sydney",
    "Asia/Dushanbe",
    "Europe/Belgrade",
    "Asia/Karachi",
    "Europe/Luxembourg",
    "Europe/Podgorica",
    "Australia/Lindeman",
    "Africa/Bangui",
    "Asia/Aden",
    "Pacific/Chuuk",
    "Asia/Brunei",
    "Indian/Comoro",
    "America/Asuncion",
    "Europe/Prague",
    "America/Cayman",
    "Pacific/Pohnpei",
    "America/Atikokan",
    "Pacific/Norfolk",
    "Africa/Dakar",
    "America/Argentina/Buenos_Aires",
    "America/Edmonton",
    "America/Barbados",
    "America/Santo_Domingo",
    "Asia/Bishkek",
    "Asia/Kuwait",
    "Pacific/Efate",
    "Indian/Mauritius",
    "America/Aruba",
    "Australia/Brisbane",
    "Indian/Kerguelen",
    "Pacific/Kiritimati",
    "America/Toronto",
    "Asia/Qyzylorda",
    "Asia/Aqtobe",
    "America/Eirunepe",
    "Europe/Isle_of_Man",
    "America/Blanc-Sablon",
    "Pacific/Honolulu",
    "America/Montevideo",
    "Asia/Tashkent",
    "Pacific/Kosrae",
    "America/Indiana/Winamac",
    "America/Argentina/La_Rioja",
    "Africa/Mogadishu",
    "Asia/Phnom_Penh",
    "Africa/Banjul",
    "America/Creston",
    "Europe/Brussels",
    "Asia/Gaza",
    "Atlantic/Bermuda",
    "America/Indiana/Knox",
    "America/El_Salvador",
    "America/Managua",
    "Africa/Niamey",
    "Europe/Monaco",
    "Africa/Ouagadougou",
    "Pacific/Easter",
    "Atlantic/Canary",
    "Asia/Vientiane",
    "Europe/Bucharest",
    "Africa/Lusaka",
    "Asia/Kathmandu",
    "Africa/Harare",
    "Asia/Bangkok",
    "Europe/Rome",
    "Africa/Lome",
    "America/Denver",
    "Indian/Reunion",
    "Europe/Kiev",
    "Europe/Vienna",
    "America/Guadeloupe",
    "America/Argentina/Cordoba",
    "Asia/Manila",
    "Asia/Tokyo",
    "America/Nassau",
    "Pacific/Enderbury",
    "Atlantic/Azores",
    "America/Winnipeg",
    "Europe/Dublin",
    "Asia/Kuching",
    "America/Argentina/Ushuaia",
    "Asia/Colombo",
    "Asia/Krasnoyarsk",
    "America/St_Johns",
    "Asia/Shanghai",
    "Pacific/Kwajalein",
    "Africa/Kigali",
    "Europe/Chisinau",
    "America/Noronha",
    "Europe/Guernsey",
    "Europe/Paris",
    "America/Guyana",
    "Africa/Luanda",
    "Africa/Abidjan",
    "America/Tortola",
    "Europe/Malta",
    "Europe/London",
    "Pacific/Guadalcanal",
    "Pacific/Gambier",
    "America/Thule",
    "America/Rankin_Inlet",
    "America/Regina",
    "America/Indiana/Vincennes",
    "America/Santarem",
    "Africa/Djibouti",
    "Pacific/Tahiti",
    "Europe/San_Marino",
    "America/Argentina/San_Luis",
    "Africa/Ceuta",
    "Asia/Singapore",
    "America/Campo_Grande",
    "Africa/Tunis",
    "Europe/Copenhagen",
    "Asia/Pontianak",
    "Asia/Dubai",
    "Africa/Khartoum",
    "Europe/Helsinki",
    "America/Whitehorse",
    "America/Maceio",
    "Africa/Douala",
    "Asia/Kuala_Lumpur",
    "America/Martinique",
    "America/Sao_Paulo",
    "America/Dawson",
    "Africa/Kinshasa",
    "Europe/Riga",
    "Africa/Tripoli",
    "Europe/Madrid",
    "America/Nipigon",
    "Pacific/Fakaofo",
    "Europe/Skopje",
    "America/St_Thomas",
    "Africa/Maseru",
    "Europe/Sofia",
    "America/Porto_Velho",
    "America/St_Kitts",
    "Africa/Casablanca",
    "Asia/Hebron",
    "Asia/Dili",
    "America/Argentina/San_Juan",
    "Asia/Almaty",
    "Europe/Sarajevo",
    "America/Boa_Vista",
    "Africa/Addis_Ababa",
    "Indian/Mayotte",
    "Africa/Lubumbashi",
    "Atlantic/Cape_Verde",
    "America/Lower_Princes",
    "Europe/Oslo",
    "Africa/Monrovia",
    "Asia/Muscat",
    "America/Thunder_Bay",
    "America/Juneau",
    "Pacific/Rarotonga",
    "Atlantic/Faroe",
    "America/Cayenne",
    "America/Cuiaba",
    "Africa/Maputo",
    "Asia/Anadyr",
    "Asia/Kabul",
    "America/Santa_Isabel",
    "Asia/Damascus",
    "Pacific/Noumea",
    "America/Anchorage",
    "Asia/Kolkata",
    "Pacific/Niue",
    "Asia/Kamchatka",
    "America/Matamoros",
    "Europe/Stockholm",
    "America/Havana",
    "Pacific/Auckland",
    "America/Rainy_River",
    "Asia/Omsk",
    "Africa/Algiers",
    "America/Guayaquil",
    "Indian/Maldives",
    "Asia/Makassar",
    "America/Monterrey",
    "Europe/Amsterdam",
    "America/St_Lucia",
    "Europe/Uzhgorod",
    "America/Indiana/Marengo",
    "Pacific/Saipan",
    "America/Bogota",
    "America/Indiana/Vevay",
    "America/Guatemala",
    "America/Puerto_Rico",
    "America/Marigot",
    "Africa/Juba",
    "America/Costa_Rica",
    "America/Caracas",
    "Pacific/Nauru",
    "Europe/Minsk",
    "America/Belem",
    "America/Cancun",
    "America/Hermosillo",
    "Asia/Chongqing",
    "Asia/Beirut",
    "Europe/Gibraltar",
    "Asia/Urumqi",
    "America/Mazatlan"
];

foreach ($timezoneStrings AS $timezone) {
    $hour = "未設定";
    if (isset($mapping[$timezone])) {
        $hour = $mapping[$timezone];
        echo "\"$timezone\" => $hour ,\n";
    } else{
        // echo "\$mapping[\"".$timezone."\"] = ;\n";
    }
    // echo "$timezone => $hour  \n";
}
