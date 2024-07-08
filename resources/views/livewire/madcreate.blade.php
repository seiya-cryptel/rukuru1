<div>
    <form>
        <table class="min-w-full table-auto">
        <tr class="border-b">
            <th><label for="mad_cd">{{ __('Code') }}</label></th>
            <td>
                <input type="text" class="form-control @error('mad_cd') is-invalid @enderror" id="mad_cd" placeholder="Enter Code" wire:model="mad_cd">
                @error('name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <th><label for="mad_allow">{{ __('Is Allow') }}</label></th>
            <td>
                <input type="checkbox" class="form-control @error('mad_allow') is-invalid @enderror" id="mad_allow" wire:model="mad_allow">
                @error('mad_allow') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <th><label for="mad_deduct">{{ __('Is Deduct') }}</label></th>
            <td>
                <input type="checkbox" class="form-control @error('mad_deduct') is-invalid @enderror" id="mad_deduct" wire:model="mad_deduct">
                @error('mad_deduct') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <th><label for="mad_name">{{ __('Item Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('mad_name') is-invalid @enderror" id="mad_name" placeholder="Enter Item Name" wire:model="mad_name">
                @error('mad_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <th><label for="mad_notes">{{ __('Notes') }}</label></th>
            <td>
                <input type="text" class="form-control @error('mad_notes') is-invalid @enderror" id="mad_notes" placeholder="Enter Notes" wire:model="mad_notes">
                @error('mad_notes') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="storeMad()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Save') }}</button>
                <button wire:click.prevent="cancelMad()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>