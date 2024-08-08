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
        @if($addClientWorktype)
            @include('livewire.clientworktypecreate')
        @endif            
        @if($updateClientWorktype)
            @include('livewire.clientworktypeupdate')
        @endif
    </div>
    <div class="col-md-8">
        <div class="text-right">            
            @if(!$addClientWorktype)
                <button wire:click="newClientWorktype()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Add') }}</button>
            @endif
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Work Place') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Kana') }}</th>
                        <th>{{ __('Alpha') }}</th>
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
                                    {{$ClientWorktype->wt_kana}}
                                </td>
                                <td>
                                    {{$ClientWorktype->wt_alpha}}
                                </td>
                                <td>
                                    <button wire:click="editClientWorktype({{$ClientWorktype->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteClientWorktype({{$ClientWorktype->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
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
    </div>    
    <script>
        function deleteClientWorktype(id){
            if(confirm("Are you sure to delete this record?"))
                Livewire.dispatch('deleteClientWorktypeListener', { id: id });
        }
    </script>
</div>