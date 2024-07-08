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
        @if($addMad)
            @include('livewire.madcreate')
        @endif            
        @if($updateMad)
            @include('livewire.madupdate')
        @endif
    </div>
    <div class="col-md-8">
        <div class="text-right">            
            @if(!$addMad)
                <button wire:click="newMad()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Add') }}</button>
            @endif
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Is Allow') }}</th>
                        <th>{{ __('Is Deduct') }}</th>
                        <th>{{ __('Item Name') }}</th>
                        <th>{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($Mads) > 0)
                        @foreach ($Mads as $Mad)
                            <tr class="border-b">
                                <td>
                                    {{$Mad->mad_cd}}
                                </td>
                                <td>
                                    {{$Mad->mad_allow}}
                                </td>
                                <td>
                                    {{$Mad->mad_deduct}}
                                </td>
                                <td>
                                    {{$Mad->mad_name}}
                                </td>
                                <td>
                                    {{$Mad->mad_notes}}
                                </td>
                                <td>
                                    <button wire:click="editMad({{$Mad->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteMad({{$Mad->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center">
                                No Users Found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>    
    <script>
        function deleteMad(id){
            if(confirm("Are you sure to delete this record?"))
                Livewire.dispatch('deleteMadListener', { id: id });
        }
    </script>
</div>