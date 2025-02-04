<?php

namespace app\Livewire;

use Livewire\Component;
use App\Models\User;

class UsersTable extends Component
{
    public $users;
    public $users_stored = [];

    public $doGroupWeek = false;
    public $doGroupMonth = false;
    public $doGroupAll = true;
    public $sortField = 'created';
    public $sortDirection = 'desc';
    public $filterFrom = '';
    public $filterTo = '';
    public $filterWeek = '';
    public $filterMonth = '';
    public $search = '';

    public function mount($users)
    {
        $this->users_stored = collect($users)->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'asc')->toArray();
    }

    public function applyFilters()
    {
        $this->users = array_values(array_filter($this->users_stored, function ($user) {
            $filterBySearch = stripos($user['user_name'], $this->search) !== false;
            $filterByTime = true;
            $filterByWeek = true;
            $filterByMonth = true;

            if ($this->filterFrom || $this->filterTo) {
                $userTime = strtotime($user['created']);
                $timeFrom = $this->filterFrom ? strtotime($this->filterFrom) : null;
                $timeTo = $this->filterTo ? strtotime($this->filterTo) : null;

                if ($timeFrom && $userTime < $timeFrom) $filterByTime = false;
                if ($timeTo && $userTime > $timeTo) $filterByTime = false;
            }

            if ($this->filterWeek) {
                $userWeek = date('W', strtotime($user['created']));
                if ($userWeek != $this->filterWeek) $filterByWeek = false;
            }

            if ($this->filterMonth) {
                $userMonth = date('m', strtotime($user['created']));
                if ($userMonth != $this->filterMonth) $filterByMonth = false;
            }

            return $filterBySearch && $filterByTime && $filterByWeek && $filterByMonth;
        }));
    }

    public function groupWeek()
    {
        $this->doGroupWeek = true;
        $this->doGroupMonth = false;
        $this->doGroupAll = false;
    }

    public function groupMonth()
    {
        $this->doGroupWeek = false;
        $this->doGroupMonth = true;
        $this->doGroupAll = false;
    }

    public function groupAll()
    {
        $this->doGroupWeek = false;
        $this->doGroupMonth = false;
        $this->doGroupAll = true;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function getSortArrow($field)
    {
        if ($this->sortField === $field) {
            return $this->sortDirection === 'asc' ? '▲' : '▼';
        }
        return '';
    }

    public function render()
    {
        $this->applyFilters();

        $groupedTimes = [];
        $currentGroup = [];

        $currentWeek = 0;
        $currentMonth = 0;
        $sum = 0;

        foreach ($this->users as $user) {
            $currentWeek = date('W', strtotime($user['created']));
            $currentMonth = date('n', strtotime($user['created']));
            break;
        }

        foreach ($this->users as $user) {

            $userWeek = date('W', strtotime($user['created']));
            $userMonth = date('n', strtotime($user['created']));

            if ($this->doGroupWeek && ($userWeek > $currentWeek)) {
                $groupedTimes[] = $currentGroup;
                $groupedTimes[] = [
                    'name' => '-------------',
                    'user_name' => "Итог $currentWeek недели:",
                    'time' => $sum,
                    'created' => '',
                    'is_summary' => true,
                ];
                $currentWeek = $userWeek;
                $currentGroup = [];
                $sum = 0;
            }

            if ($this->doGroupMonth && ($userMonth > $currentMonth)) {
                $groupedTimes[] = $currentGroup;
                $groupedTimes[] = [
                    'name' => '-------------',
                    'user_name' => "Итог $currentMonth месяца:",
                    'time' => $sum,
                    'created' => '',
                    'is_summary' => true,
                ];
                $currentMonth = $userMonth;
                $currentGroup = [];
                $sum = 0;
            }

                $currentGroup[] = $user;
                $sum += $user['time'];
        }
        unset($this->users);

        if ($this->doGroupAll) {
            $groupedTimes[] = $currentGroup;
            $groupedTimes[] = [
                'name' => '-------------',
                'user_name' => "Общий итог: ",
                'time' => $sum,
                'created' => '',
                'is_summary' => true,
            ];
            unset($currentGroup);
            unset($sum);
        }

        if (!empty($currentGroup))
            $groupedTimes[] = $currentGroup;

        $this->users = [];

        foreach ($groupedTimes as $group) {
            if (isset($group['is_summary']) && $group['is_summary']) {
                $this->users[] = $group;
            } else {
                $sortedGroup = collect($group)->sortBy($this->sortField, SORT_REGULAR, $this->sortDirection === 'asc')->toArray();
                $this->users = array_merge($this->users, $sortedGroup);
            }
        }
        unset($groupedTimes);
        unset($sortedGroup);
        return view('livewire.users-table');
    }
}
