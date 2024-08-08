<div>
    <form>
        <table class="min-w-full table-auto">
        <tr class="border-b">
            <th><label for="client_id">{{ __('Client') }}</label></th>
            <td>
                <select class="form-control @error('client_id') is-invalid @enderror" id="client_id" wire:model="client_id">
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
            <th><label for="cl_pl_cd">{{ __('Work Place') . __('Code') }}</label></th>
            <td>
                <input type="text" class="form-control @error('cl_pl_cd') is-invalid @enderror" id="cl_pl_cd" placeholder="Enter Code" wire:model="cl_pl_cd">
                @error('cl_pl_cd') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="cl_pl_name">{{ __('Work Place') . __('Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('cl_pl_name') is-invalid @enderror" id="cl_pl_name" placeholder="Enter Name" wire:model="cl_pl_name">
                @error('cl_pl_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="cl_pl_kana">{{ __('Work Place') . __('Kana') }}</label></th>
            <td>
                <input type="cl_pl_kana" class="form-control @error('cl_pl_kana') is-invalid @enderror" id="cl_pl_kana" placeholder="Enter Kana" wire:model="cl_pl_kana">
                @error('cl_pl_kana') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="cl_pl_alpha">{{ __('Work Place') . __('Alpha') }}</label></th>
            <td>
                <input type="cl_pl_alpha" class="form-control @error('cl_pl_alpha') is-invalid @enderror" id="cl_pl_alpha" placeholder="Enter Alpha" wire:model="cl_pl_alpha">
                @error('cl_pl_alpha') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="storeClientPlace()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Save') }}</button>
                <button wire:click.prevent="cancelClientPlace()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
