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
            <button wire:click="newClientPlace()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">{{ __('Add') }}</button>
        </div>
        <div>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Client') . __('Code') }}</th>
                        <th>{{ __('Client') . __('Name') }}</th>
                        <th>{{ __('Work Place') . __('Code') }}</th>
                        <th>{{ __('Work Place') }}{{ __('Name') }}</th>
                        <th>{{ __('Work Place') }}{{ __('Kana') }}</th>
                        <th>{{ __('Work Place') }}{{ __('Alpha') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($ClientPlaces) > 0)
                        @foreach ($ClientPlaces as $ClientPlace)
                            <tr class="border-b">
                                <td>
                                    {{$ClientPlace->client->cl_cd}}
                                </td>
                                <td>
                                    {{$ClientPlace->client->cl_name}}
                                </td>
                                <td>
                                    {{$ClientPlace->cl_pl_cd}}
                                </td>
                                <td>
                                    {{$ClientPlace->cl_pl_name}}
                                </td>
                                <td>
                                    {{$ClientPlace->cl_pl_kana}}
                                </td>
                                <td>
                                    {{$ClientPlace->cl_pl_alpha}}
                                </td>
                                <td>
                                    <button wire:click="editClientPlace({{$ClientPlace->clientplace_id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteClientPlace({{$ClientPlace->clientplace_id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center">
                                No ClientPlaces Found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{ $ClientPlaces->links() }}
    </div>    
    <script>
        function deleteClientPlace(id){
            if(confirm("削除しますか?"))
                Livewire.dispatch('deleteClientPlaceListener', { id: id });
        }
    </script>
</div>