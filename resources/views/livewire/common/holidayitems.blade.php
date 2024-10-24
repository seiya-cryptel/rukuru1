<tr class="border-b">
    <th><label for="holiday_date">{{ __('Date') }}</label></th>
    <td>
        <input type="date" class="form-control @error('holiday_date') is-invalid @enderror py-1" id="holiday_date" placeholder="Date" wire:model="holiday_date">
        @error('holiday_date') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="client_id">{{ __('Client') }}</label></th>
    <td>
        <select class="form-control @error('client_id') is-invalid @enderror py-1" id="client_id" wire:model="client_id">
            <option value="0">{{ __('Common') }}</option>
            @foreach($refClients as $client)
                <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
            @endforeach
        </select>
        @error('client_id') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="holiday_name">{{ __('Holiday') . __('Name') }}</label></th>
    <td>
        <input type="text" class="form-control @error('holiday_name') is-invalid @enderror py-1" id="holiday_name" placeholder="Enter Name" wire:model="holiday_name">
        @error('holiday_name') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="notes">{{ __('Notes') }}</label></th>
    <td>
        <input type="text" class="form-control @error('notes') is-invalid @enderror py-1" id="notes" placeholder="Enter Notes" wire:model="notes">
        @error('notes') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
