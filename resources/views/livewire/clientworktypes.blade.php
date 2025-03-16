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
        <div class="text-right">            
            <button wire:click="newClientWorkType()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Add') }}</button>
        </div>
        <div>
            <table class="min-w-full table-auto text-sm">
                <tr>
                    <td>
                        <label for="client_id">{{ __('Client') }}</label>
                        <select class="form-control @error('client_id') is-invalid @enderror text-sm py-1" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
                            <option value="">{{ __('Select Client') }}</option>
                            @foreach ($Clients as $Client)
                                <option value="{{$Client->id}}">{{$Client->cl_cd}} {{$Client->cl_name}}</option>
                            @endforeach
                        </select>
                        @error('client_id') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <label class="px-4" for="clientplace_id">{{ __('Work Place') }}</label>
                        <select class="form-control @error('clientplace_id') is-invalid @enderror text-sm py-1" id="clientplace_id" wire:model="clientplace_id" wire:change="updateClientplaceId($event.target.value)">
                            <option value="">{{ __('Select Work Place') }}</option>
                            @foreach ($ClientPlaces as $ClientPlace)
                                <option value="{{$ClientPlace->id}}">{{$ClientPlace->cl_pl_cd}} {{$ClientPlace->cl_pl_name}}</option>
                            @endforeach
                        </select>
                        @error('clientplace_id') 
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <span>
                            <button wire:click="clearSearch()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">X</button>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <div>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Work Place') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Begin') }}-{{ __('End') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($ClientWorktypes) > 0)
                        @foreach ($ClientWorktypes as $ClientWorktype)
                            <tr class="border-b">
                                <td>
                                    @if($ClientWorktype->client)
                                        {{$ClientWorktype->client->cl_cd}} {{$ClientWorktype->client->cl_name}}
                                    @endif
                                </td>
                                <td>
                                    @if($ClientWorktype->clientplace)
                                        {{$ClientWorktype->clientplace->cl_pl_cd}} {{$ClientWorktype->clientplace->cl_pl_name}}
                                    @endif
                                </td>
                                <td>
                                    {{$ClientWorktype->wt_cd}}
                                </td>
                                <td>
                                    {{$ClientWorktype->wt_name}}
                                </td>
                                <td>
                                    {{$ClientWorktype->wt_work_start}}-{{$ClientWorktype->wt_work_end}}
                                </td>
                                <td>
                                    <button wire:click="editClientWorkType({{$ClientWorktype->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteClientWorkType({{$ClientWorktype->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center">
                                No ClientWorktypes Found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{ $ClientWorktypes->links() }}
    </div>    
    <script>
        function deleteClientWorkType(id){
            if(confirm("削除しますか?"))
                Livewire.dispatch('deleteClientWorkTypeListener', { id: id });
        }
    </script>
</div>