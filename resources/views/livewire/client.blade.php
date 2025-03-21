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
            <button wire:click="newClient()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Add') }}</button>
        </div>
        <div>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th style="width: 8rem;"> </th>
                        <th style="width: 3rem;">{{ __('Code') }}</th>
                        <th style="width: 20rem;">{{ __('Client') }}</th>
                        <th>{{ __('Name') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($Clients) > 0)
                        @foreach ($Clients as $Client)
                            <tr class="border-b">
                                <td>
                                    <button wire:click="editClient({{$Client->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteClient({{$Client->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                                <td>
                                    {{$Client->cl_cd}}
                                </td>
                                <td>
                                    {{$Client->cl_name}}
                                </td>
                                <td>
                                    {{$Client->cl_notes}}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center">
                                No Clients Found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        {{ $Clients->links() }}
    </div>    
    <script>
        function deleteClient(id){
            if(confirm("削除しますか？"))
                Livewire.dispatch('deleteClientListener', { id: id });
        }
    </script>
</div>