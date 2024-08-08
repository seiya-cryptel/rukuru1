<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
        @if($addEmployee)
            @include('livewire.employeecreate')
        @endif            
        @if($updateEmployee)
            @include('livewire.employeeupdate')
        @endif
        @if($hourlyWageEmployee)
            @livewire('employeepays', ['employee_id' => $employeeId])
        @endif
    </div>
    <div class="col-md-8">
        <div class="text-right">            
            @if(!$addEmployee && !$updateEmployee && !$hourlyWageEmployee)
                <button wire:click="newEmployee()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Employee') . __('Add') }}</button>
            @endif
        </div>
        <!-- 検索窓の追加 -->
        <div>
            <input wire:model.live="search" type="text" class="form-control" id="search" placeholder="{{ __('Search Employees...') }}">
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Kana') }}</th>
                        <th>{{ __('Alpha') }}</th>
                        <th>{{ __('Hire Date') }}</th>
                        <th>{{ __('Termination Date') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($Employees) > 0)
                        @foreach ($Employees as $Employee)
                            <tr class="border-b">
                                <td>
                                    {{$Employee->empl_cd}}
                                </td>
                                <td>
                                    {{$Employee->empl_name_last}} {{$Employee->empl_name_first}} 
                                </td>
                                <td>
                                    {{$Employee->empl_kana_last}} {{$Employee->empl_kana_first}} 
                                </td>
                                <td>
                                    {{$Employee->empl_alpha_first}} {{$Employee->empl_alpha_last}}
                                </td>
                                <td>
                                    {{$Employee->empl_hire_date}}
                                <td>
                                <td>
                                    {{$Employee->empl_resign_date}}
                                <td>
                                    <button wire:click="editEmployee({{$Employee->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button wire:click="hourlywageEmployee({{$Employee->id}})" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 rounded">{{ __('Hourly Wage') }}</button>
                                    <button onclick="deleteEmployee({{$Employee->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center">
                                No Employees Found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{ $Employees->links() }}
    </div>    
    <script>
        function deleteEmployee(id){
            if(confirm("Are you sure to delete this record?"))
                Livewire.dispatch('deleteEmployeeListener', { id: id });
        }
    </script>
</div>