<div>
    <form>
        <table class="min-w-full table-auto">
        <tr class="border-b">
            <th><label for="cl_cd">{{ __('Code') }}</label></th>
            <td>
                <input type="text" class="form-control @error('cl_cd') is-invalid @enderror" id="cl_cd" placeholder="Enter Code" wire:model="cl_cd">
                @error('cl_cd') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="cl_name">{{ __('Client') . __('Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('cl_name') is-invalid @enderror" id="cl_name" placeholder="Enter Name" wire:model="cl_name">
                @error('cl_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="cl_kana">{{ __('Client') . __('Kana') }}</label></th>
            <td>
                <input type="cl_kana" class="form-control @error('cl_kana') is-invalid @enderror" id="cl_kana" placeholder="Enter Kana" wire:model="cl_kana">
                @error('cl_kana') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="cl_alpha">{{ __('Client') . __('Alpha') }}</label></th>
            <td>
                <input type="cl_alpha" class="form-control @error('cl_alpha') is-invalid @enderror" id="cl_alpha" placeholder="Enter Alpha" wire:model="cl_alpha">
                @error('cl_alpha') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="storeClient()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Save') }}</button>
                <button wire:click.prevent="cancelClient()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
