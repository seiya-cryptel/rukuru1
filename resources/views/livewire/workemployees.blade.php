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
    </div>
    <div class="col-md-8">
        <table class="py-1 text-sm">
        <tr>
            <td>
                <input type="text" class="form-control @error('workYear') is-invalid @enderror py-1 text-sm" id="workYear" wire:model="workYear" style="width: 4rem;" wire:change="updateWorkYear($event.target.value)">
                @error('workYear') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                年
            </td>
            <td>
                <input type="text" class="form-control @error('workMonth') is-invalid @enderror py-1 text-sm" id="workMonth" wire:model="workMonth" style="width: 3rem;" wire:change="updateWorkMonth($event.target.value)">
                @error('workMonth') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                月
            </td>
            <td>
                <select class="form-control @error('client_id') is-invalid @enderror py-1 text-sm" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
                    <option value="">{{ __('Select Client') }}</option>
                    @foreach($refClients as $client)
                        <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
                    @endforeach
                </select>
                @error('client_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <select class="form-control @error('clientplace_id') is-invalid @enderror py-1 text-sm" id="clientplace_id" wire:model="clientplace_id" wire:change="updateClientplaceId($event.target.value)">
                    <option value="">{{ __('Select Work Place') }}</option>
                    @foreach($refClientPlaces as $clientplace)
                        <option value="{{ $clientplace->id }}">{{ $clientplace->cl_pl_cd }}:{{ $clientplace->cl_pl_name }}</option>
                    @endforeach
                </select>
                @error('clientplace_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                従業員検索
                <input wire:model.live="search" type="text" class="form-control py-1 text-sm" id="search" placeholder="{{ __('Search Employees...') }}" wire:change="changeSearch($event.target.value)">
                <span>
                    <button wire:click="clearSearch()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">X</button>
                </span>
            </td>
        </tr>
        </table>
    </div>
    <div>
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th>{{ __('Timekeeping') }}</th>
                    <th>{{ __('Code') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Kana') }}</th>
                    <th>{{ __('Alpha') }}</th>
                    <th>{{ __('Hire Date') . '〜' . __('Termination Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (count($Employees) > 0)
                    @foreach ($Employees as $Employee)
                        <tr class="border-b">
                            <td>
                                @php
                                    $cond = $this->timekeepingExists($Employee->id)
                                @endphp
                                @if($cond == 'exists')
                                    <button wire:click="editTimekeeping({{ $Employee->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Timekeeping') . __('Edit') }}</button>
                                @elseif($cond == 'notexists')
                                    <button wire:click="editTimekeeping({{ $Employee->id }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Timekeeping') . __('Add') }}</button>
                                @else
                                @endif
                            </td>
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
                                {{$Employee->empl_hire_date}} 〜 {{$Employee->empl_resign_date}}
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
