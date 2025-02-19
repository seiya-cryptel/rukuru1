<tr class="border-b">
    <th><label for="mad_cd">{{ __('Code') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="0" class="form-control @error('mad_cd') is-invalid @enderror text-sm py-1" id="mad_cd" placeholder="{{ __('Code') }}" wire:model="mad_cd" style="width: 6rem;">
        @error('mad_cd') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="mad_allow">{{ __('Is Allow') }}</label></th>
    <td>
        <input type="checkbox" tabindex="1" class="form-control @error('mad_allow') is-invalid @enderror" id="mad_allow" wire:model="mad_allow">
        @error('mad_allow') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="mad_deduct">{{ __('Is Deduct') }}</label></th>
    <td>
        <input type="checkbox" tabindex="2" class="form-control @error('mad_deduct') is-invalid @enderror" id="mad_deduct" wire:model="mad_deduct">
        @error('mad_deduct') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="mad_name">{{ __('Item Name') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="3" class="form-control @error('mad_name') is-invalid @enderror text-sm py-1" id="mad_name" placeholder="{{ __('Name') }}" wire:model="mad_name">
        @error('mad_name') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
    </tr>
<tr class="border-b">
    <th><label for="mad_notes">{{ __('Notes') }}</label></th>
    <td>
        <textarea tabindex="4" class="form-control @error('mad_notes') is-invalid @enderror text-sm py-1" id="mad_notes" placeholder="{{ __('Notes') }}" wire:model="mad_notes"></textarea>
        @error('mad_notes') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
