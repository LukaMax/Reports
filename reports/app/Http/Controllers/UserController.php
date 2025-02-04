<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Livewire\Livewire;

class UserController extends Controller
{
    public function index()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://git.kwax.ru/api/v1/repos/search?state=all&access_token=");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response_owners_repos = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $issues = [];
        foreach ($response_owners_repos['data'] as $item) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://git.kwax.ru/api/v1/repos/" . $item['full_name'] . "/issues?state=all&access_token=");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $issues[] = json_decode(curl_exec($ch), true);
            curl_close($ch);
        }
        unset($response_owners_repos);

        $issue_times = [];
        foreach ($issues as $index => $issue) {
            if (count($issue) == 3) {
                unset($issues[$index]);
            } else {
                foreach ($issue as $issue_array_field) {
                    if (!is_string($issue_array_field)) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, "https://git.kwax.ru/api/v1/repos/" . $issue_array_field['repository']['full_name'] . "/issues/" . $issue_array_field['number'] . "/times?access_token=");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $issue_times[] = json_decode(curl_exec($ch), true) + ['name' => $issue_array_field['repository']['full_name']];
                        curl_close($ch);
                    }
                }
                unset($issue);
            }
        }
        unset($issues);
        $users = [];
        foreach ($issue_times as $index => &$time_array)
            if (isset($time_array['errors']) || count($time_array) == 1)
                unset($issue_times[$index]);
            else {
                foreach ($time_array as &$arr) {
                    if (is_array($arr))
                        $arr = array_merge($arr, ['name' => $time_array['name']]);
                }
                unset($arr);
                $users = array_merge($users, $time_array);
            }
        unset($issue_times);
        unset($time_array);
        unset($users['name']);

        $component_user = [];
        foreach ($users as $user)
            $component_user[] = [
                'created' => $user['created'],
                'time' => $user['time'],
                'user_name' => $user['user_name'],
                'name' => $user['issue']['repository']['full_name'] . '#' . $user['issue']['number'],
            ];
        return view('home', [
            'users' => $component_user,
        ]);
    }
}
