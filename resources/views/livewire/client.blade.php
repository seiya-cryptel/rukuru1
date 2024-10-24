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
            <button wire:click="newClient()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Add') }}</button>
        </div>
        <div>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th style="width: 4rem;">{{ __('Code') }}</th>
                        <th>{{ __('Client') }}{{ __('Name') }}</th>
                        <th>{{ __('Client') }}{{ __('Kana') }}</th>
                        <th>{{ __('Client') }}{{ __('Alpha') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($Clients) > 0)
                        @foreach ($Clients as $Client)
                            <tr class="border-b">
                                <td>
                                    {{$Client->cl_cd}}
                                </td>
                                <td>
                                    {{$Client->cl_name}}
                                </td>
                                <td>
                                    {{$Client->cl_kana}}
                                </td>
                                <td>
                                    {{$Client->cl_alpha}}
                                </td>
                                <td>
                                    <button wire:click="editClient({{$Client->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteClient({{$Client->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
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
    </div>    
    <script>
        function deleteClient(id){
            if(confirm("Are you sure to delete this record?"))
                Livewire.dispatch('deleteClientListener', { id: id });
        }
    </script>
</div>