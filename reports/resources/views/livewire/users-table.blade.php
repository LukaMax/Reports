
<div>
    <div>

        <label>....... Имя пользователя</label>
        <label>.............. Дата от</label>
        <label>............... Дата до</label>
        <label>.................. Номер недели</label>
        <label>.................... Номер месяца .......</label>
        <br/>
        <input type="text" wire:model="search">
        <input type="date" wire:model="filterFrom">
        <input type="date" wire:model="filterTo">
        <input type="number" wire:model="filterWeek">
        <input type="number" wire:model="filterMonth">
        <button type="button" wire:click="applyFilters" style="cursor: pointer;">Применить фильтры</button>
    </div>
    <p/>
    <div>
        <button type="button" wire:click="groupWeek" style="cursor: pointer;">Группировать по неделе</button>
        <button type="button" wire:click="groupMonth" style="cursor: pointer;">Группировать по месяцу</button>
        <button type="button" wire:click="groupAll" style="cursor: pointer;">Итоговая группировка</button>
    </div>
    <p/>
    <table>
        <thead>
        <tr>
            <th wire:click="sortBy('name')" style="cursor: pointer;">ID задачи {{$this->getSortArrow('index')}}</th>
            <th wire:click="sortBy('user_name')" style="cursor: pointer;">Работник {{$this->getSortArrow('user_name')}}</th>
            <th>Затраченное время (секунд)</th>
            <th>Дата работы</th>
        </tr>
        </thead>
        <tbody>
{{--        @dd($users)--}}
        @foreach($users as $user)
            <tr>
                <td>{{ $user['name'] }}</td>
                <td>{{ $user['user_name'] }}</td>
                <td>{{ $user['time'] }}</td>
                <td>{{ $user['created'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

