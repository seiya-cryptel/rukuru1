<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" role="alert" style="color: red;">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <div class="row">
            <div>
            {{ $empl_cd }}:{{ $empl_name_last }} {{ $empl_name_first }}
            </div>
            <div class="text-right">
                <button wire:click="newEmployeepays()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Hourly Wage') . __('Settings') . __('Add') }}</button>
            </div>
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                <tr>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Work Place') }}</th>
                    <th>{{ __('Work Type') }}</th>
                    <th>{{ __('Hourly Wage') }}</th>
                    <th>{{ __('Hourly Bill') }}</th>
                    <th> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($vEmployeepays as $key => $employeepay)
                <tr class="border-b">
                    <td>
                        <select class="form-control @error('vEmployeepays'.$key.'client_id') is-invalid @enderror" id="vEmployeepays.{{ $key }}.client_id" wire:model="vEmployeepays.{{ $key }}.client_id" wire:change="updateClientId($event.target.value,{{ $key }})">
                            <option value="">{{ __('Client') . __('Not Selected') }}</option>
                            @foreach($refClients as $client)
                                <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
                            @endforeach
                        </select>
                        @error('vEmployeepays'.$key.'client_id') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <select class="form-control @error('vEmployeepays'.$key.'clientplace_id') is-invalid @enderror" id="vEmployeepays.{{ $key }}.clientplace_id" wire:model="vEmployeepays.{{ $key }}.clientplace_id" wire:change="updateClientplaceId($event.target.value,{{ $key }})">
                            <option value="">{{ __('Work Place') . __('Not Selected') }}</option>
                            @if(isset($vrefClientPlaces[$key]))
                            @foreach($vrefClientPlaces[$key] as $clientplace)
                                <option value="{{ $clientplace->id }}">{{ $clientplace->cl_pl_cd }}:{{ $clientplace->cl_pl_name }}</option>
                            @endforeach
                            @endif
                        </select>
                        @error('vEmployeepays'.$key.'clientplace_id') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <select class="form-control @error('vEmployeepays'.$key.'wt_cd') is-invalid @enderror" id="vEmployeepays.{{ $key }}.wt_cd" wire:model="vEmployeepays.{{ $key }}.wt_cd">
                            <option value="">{{ __('Select Work Type') }}</option>
                            @if(isset($vrefClientWorktypes[$key]))
                            @foreach($vrefClientWorktypes[$key] as $clientworktype)
                                <option value="{{ $clientworktype->wt_cd }}">{{ $clientworktype->wt_cd }}:{{ $clientworktype->wt_name }}</option>
                            @endforeach
                            @endif
                        </select>
                        @error('vEmployeepays'.$key.'wt_cd') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('vEmployeepays'.$key.'payhour') is-invalid @enderror" id="vEmployeepays.{{ $key }}.payhour" wire:model="vEmployeepays.{{ $key }}.payhour">
                        @error('vEmployeepays'.$key.'payhour') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <input type="text" class="form-control @error('vEmployeepays'.$key.'billhour') is-invalid @enderror" id="vEmployeepays.{{ $key }}.billhour" wire:model="vEmployeepays.{{ $key }}.billhour">
                        @error('vEmployeepays'.$key.'billhour') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </td>
                    <td>
                        <button type="button" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="removeEmployeepays({{$key}})">{{ __('Delete') }}</button>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" wire:click.prevent="saveEmployeepays" data-save="true">{{ __('Hourly Wage') . __('Settings') . __('Save') }}</button>
        <button wire:click.prevent="cancelEmployeepay()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true"{{ __('Cancel') }}</button>
    </div>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
