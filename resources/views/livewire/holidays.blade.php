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
            <button wire:click="newHoliday()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Holiday') . __('Add') }}</button>
        </div>
        <!-- 検索窓の追加 -->
        <div>
            <input type="text" class="form-control text-sm py-1" id="search" placeholder="{{ __('Year') }}" wire:model="targetYear" wire:change="changeYear($event.target.value)" style="width: 4rem;">
            年
        </div>
        <div>
            <table class="min-w-full table-auto text-sm">
                <thead class="bg-gray-200">
                    <tr>
                        <th style="width: 6rem;">{{ __('Date') }}</th>
                        <th style="width: 10rem;">{{ __('Name') }}</th>
                        <th style="width: 10rem;">{{ __('Notes') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                @if (count($Holidays) > 0)
                @foreach ($Holidays as $Holiday)
                    <tr class="border-b">
                        <td>
                            {{$Holiday->holiday_date}}
                        </td>
                        <td>
                            {{$Holiday->holiday_name}} 
                        </td>
                        <td>
                            {{$Holiday->notes}} 
                        </td>
                        <td>
                            <button wire:click="editHoliday({{$Holiday->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                            <button onclick="deleteHoliday({{$Holiday->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                @endforeach
                @else
                    <tr>
                        <td colspan="3" align="center">
                            No Holidays Found.
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
        {{ $Holidays->links() }}
    </div>    
    <script>
        function deleteHoliday(id){
            if(confirm('削除しますか？'))
                Livewire.dispatch('deleteHolidayListener', { id: id });
        }
    </script>
</div>
