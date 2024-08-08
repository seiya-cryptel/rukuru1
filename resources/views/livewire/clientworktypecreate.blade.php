<div>
    <form>
        <table class="min-w-full table-auto">
        <tr class="border-b">
            <th><label for="client_id">{{ __('Client') }}</label></th>
            <td>
                <select class="form-control @error('client_id') is-invalid @enderror" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
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
                <select class="form-control @error('clientplace_id') is-invalid @enderror" id="clientplace_id" wire:model="clientplace_id">
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
                <input type="text" class="form-control @error('wt_cd') is-invalid @enderror" id="wt_cd" placeholder="Enter Code" wire:model="wt_cd">
                @error('wt_cd') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="wt_name">{{ __('Work Type') . __('Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('wt_name') is-invalid @enderror" id="wt_name" placeholder="Enter Name" wire:model="wt_name">
                @error('wt_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="wt_kana">{{ __('Work Type') . __('Kana') }}</label></th>
            <td>
                <input type="wt_kana" class="form-control @error('wt_kana') is-invalid @enderror" id="wt_kana" placeholder="Enter Kana" wire:model="wt_kana">
                @error('wt_kana') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="wt_alpha">{{ __('Work Type') . __('Alpha') }}</label></th>
            <td>
                <input type="wt_alpha" class="form-control @error('wt_alpha') is-invalid @enderror" id="wt_alpha" placeholder="Enter Alpha" wire:model="wt_alpha">
                @error('wt_alpha') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="storeClientWorktype()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Save') }}</button>
                <button wire:click.prevent="cancelClientWorktype()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
