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
        @if($addUser)
            @include('livewire.usercreate')
        @endif            
        @if($updateUser)
            @include('livewire.userupdate')
        @endif
    </div>
    <div class="col-md-8">
        <div class="text-right">            
            @if(!$addUser)
                <button wire:click="newUser()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Add') }}</button>
            @endif
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr class="border-b">
                                <td>
                                    {{$user->name}}
                                </td>
                                <td>
                                    {{$user->email}}
                                </td>
                                <td>
                                    <button wire:click="editUser({{$user->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deleteUser({{$user->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
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
        function deleteUser(id){
            if(confirm("Are you sure to delete this record?"))
                Livewire.dispatch('deleteUserListener', { id: id });
        }
    </script>
</div>