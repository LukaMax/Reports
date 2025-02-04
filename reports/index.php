<?php

$api_key = '';

function print_info() {
    echo 'Usage: php ' . basename(__FILE__) . ' owner repo id [start-date end-date]' . PHP_EOL;
    echo '  e.g. php KwaX Work 1 2024-01-01 2024-01-31' . PHP_EOL;
    echo PHP_EOL;
    echo '  owner       ' . 'repository owner' . PHP_EOL;
    echo '  repo        ' . 'repository name' . PHP_EOL;
    echo '  id          ' . 'issue id' .  PHP_EOL;
    echo '  start-date  ' . 'date in format yyyy-mm-dd, started from 00:00:00' .  PHP_EOL;
    echo '  end-date    ' . 'date in format yyyy-mm-dd, ended with 23:59:59' .  PHP_EOL;
    echo PHP_EOL;
}

function check_date(string $str): bool {
    $fragments = explode('-', $str);
    if (count($fragments) !== 3)
        return false;
    $year = $fragments[0];
    $month = $fragments[1];
    $day = $fragments[2];
    return checkdate($month, $day, $year);
}

switch (count($argv)) {
    case 6:
        if (!(check_date($argv[4]) && check_date($argv[5]))) {
            echo 'Invalid date format' . PHP_EOL . PHP_EOL;
            return print_info();
        }
        $date_start = $argv[4];
        $date_end   = $argv[5];
    case 4:
        $owner = $argv[1];
        $repository = $argv[2];
        $issue_id = $argv[3];
        break;
    default:
        return print_info();
}

$url = 'https://git.kwax.ru/api/v1/repos/' . $owner . '/' . $repository .'/issues/' . $issue_id. '/times?';

if (!empty($date_start)) {
    $url .= http_build_query([
        'since' => $date_start . 'T00:00:00+10:00',
        'before' => $date_end . 'T23:59:59+10:00',
    ]);
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: token ' . $api_key,
]);
$json = curl_exec($ch);
curl_close($ch);

$data = json_decode($json);

$workers = [];
foreach ($data as $item) {
    if (!isset($workers[$item->user_name]))
        $workers[$item->user_name] = 0;
    $workers[$item->user_name] += $item->time;
}

foreach ($workers as $user_name => $time) {
    echo $user_name . "\t\t" . $time . PHP_EOL;
}
