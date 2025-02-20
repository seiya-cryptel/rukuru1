<div>
    <div class="col-md-8">
        <div class="row">
            <div>
            {{ $Employee->empl_cd }}:{{ $Employee->empl_name_last }} {{ $Employee->empl_name_first }}
            </div>
            <div class="text-right">
                <button wire:click="newEmployeepay()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Hourly Wage') . __('Settings') . __('Add') }}</button>
            </div>
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                <tr>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Work Place') }}</th>
                    <th>{{ __('Work Type') }}</th>
                    <th> </th>
                    <th> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($EmployeePays as $EmployeePay)
                <tr class="border-b">
                    <td>{{ $EmployeePay->clientworktype->client->cl_cd }}:{{ $EmployeePay->clientworktype->client->cl_name }}</td>
                    <td>
                        @if(!empty($EmployeePay->clientworktype->clientplace_id))
                            {{ $EmployeePay->clientworktype->clientplace->cl_pl_cd }}:{{ $EmployeePay->clientworktype->clientplace->cl_pl_name }}</td>
                        @endif
                    <td>
                        {{ $EmployeePay->clientworktype->wt_cd }}:{{ $EmployeePay->clientworktype->wt_name }}
                    </td>
                    <td>
                        {{ $EmployeePay->clientworktype->wt_day_night == 2 ? __('Night Work') : __('Day Work') }}
                        {{ $EmployeePay->clientworktype->wt_work_start }}-{{ $EmployeePay->clientworktype->wt_work_end }}
                    </td>
                    <td>
                        <button wire:click="editEmployeePay({{$EmployeePay->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                        <button onclick="deleteEmployeePay({{$EmployeePay->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        function deleteEmployeePay(id){
            if(confirm("削除しますか？"))
                Livewire.dispatch('deleteEmployeePayListener', { id: id });
        }
    </script>
</div>
