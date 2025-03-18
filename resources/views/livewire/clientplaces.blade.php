<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" style="color: blue;" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" style="color: red;" role="alert">
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
                        <th style="width: 7rem;"> </th>
                        <th style="width: 16rem;">{{ __('Client')}}</th>
                        <th style="width: 16rem;">{{ __('Work Place') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($ClientPlaces) > 0)
                        @foreach ($ClientPlaces as $ClientPlace)
                            <tr class="border-b">
                                <td>
                                    <button wire:click="editClientPlace({{$ClientPlace->clientplace_id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteClientPlace({{$ClientPlace->clientplace_id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                                <td>
                                    {{$ClientPlace->client->cl_cd}} {{$ClientPlace->client->cl_name}}                                    
                                </td>
                                <td>
                                    {{$ClientPlace->cl_pl_cd}} {{$ClientPlace->cl_pl_name}}
                                </td>
                                <td>
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