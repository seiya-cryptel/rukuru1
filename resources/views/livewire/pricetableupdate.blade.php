<div>
    <form>
        <table class="min-w-full table-auto">
        <tr class="border-b">
            <th><label for="client_id">{{ __('Client') }}</label></th>
            <td>
                <select class="form-control @error('client_id') is-invalid @enderror py-1 text-sm" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
                    <option value="">{{ __('Select Client') }}</option>
                    @foreach($Clients as $client)
                        <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
                    @endforeach
                </select>
                @error('client_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="clientplace_id">{{ __('Work Place') }}</label></th>
            <td>
                <select class="form-control @error('clientplace_id') is-invalid @enderror py-1 text-sm" id="clientplace_id" wire:model="clientplace_id">
                    <option value="">{{ __('Select Work Place') }}</option>
                    @foreach($ClientPlaces as $clientplace)
                        <option value="{{ $clientplace->id }}">{{ $clientplace->cl_pl_cd }}:{{ $clientplace->cl_pl_name }}</option>
                    @endforeach
                </select>
                @error('clientplace_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="wt_cd">{{ __('Work Type') . __('Code') }}</label></th>
            <td>
                <input type="text" class="form-control @error('wt_cd') is-invalid @enderror py-1 text-sm" id="wt_cd" placeholder="Enter Code" wire:model="wt_cd">
                @error('wt_cd') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="bill_name">{{ __('Item Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('bill_name') is-invalid @enderror py-1 text-sm" id="bill_name" placeholder="Enter Name" wire:model="bill_name">
                @error('bill_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="bill_print_name">{{ __('Print Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('bill_print_name') is-invalid @enderror py-1 text-sm" id="bill_print_name" placeholder="Enter Print Name" wire:model="bill_print_name">
                @error('bill_print_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="bill_unitprice">{{ __('Unit Price') }}</label></th>
            <td>
                <input type="text" class="form-control @error('bill_unitprice') is-invalid @enderror py-1 text-sm" id="bill_unitprice" placeholder="Enter Unit Price" wire:model="bill_unitprice">
                @error('bill_unitprice') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="display_order">{{ __('Display Order') }}</label></th>
            <td>
                <input type="text" class="form-control @error('display_order') is-invalid @enderror py-1 text-sm" id="display_order" placeholder="Enter Display Order" wire:model="display_order">
                @error('display_order') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="notes">{{ __('Notes') }}</label></th>
            <td>
                <input type="text" class="form-control @error('notes') is-invalid @enderror py-1 text-sm" id="notes" placeholder="Enter Notes" wire:model="notes">
                @error('notes') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="updatePriceTable()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" data-save="true">{{ __('Update') }}</button>
                <button wire:click.prevent="cancelPriceTable()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
