<tr class="border-b">
    <th><label for="client_id">{{ __('Client') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <select tabindex="0" class="form-control @error('client_id') is-invalid @enderror text-sm py-1" id="client_id" wire:model="client_id">
            <option value="">{{ __('Select Client') }}</option>
            @foreach($refClients as $client)
                <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
            @endforeach
        </select>
        @error('client_id') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_pl_cd">{{ __('Work Place') . __('Code') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="1" class="form-control @error('cl_pl_cd') is-invalid @enderror text-sm py-1" id="cl_pl_cd" placeholder="{{ __('Work Place') }}{{ __('Code') }}" wire:model="cl_pl_cd">
        @error('cl_pl_cd') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_pl_name">{{ __('Work Place') . __('Name') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="2" class="form-control @error('cl_pl_name') is-invalid @enderror text-sm py-1" id="cl_pl_name" placeholder="{{ __('Work Place') }}{{ __('Name') }}" wire:model="cl_pl_name">
        @error('cl_pl_name') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
{{--
<tr class="border-b">
    <th><label for="cl_pl_kana">{{ __('Work Place') . __('Kana') }}</label></th>
    <td>
        <input type="text" class="form-control @error('cl_pl_kana') is-invalid @enderror text-sm py-1" id="cl_pl_kana" placeholder="Enter Kana" wire:model="cl_pl_kana">
        @error('cl_pl_kana') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_pl_alpha">{{ __('Work Place') . __('Alpha') }}</label></th>
    <td>
        <input type="text" class="form-control @error('cl_pl_alpha') is-invalid @enderror text-sm py-1" id="cl_pl_alpha" placeholder="Enter Alpha" wire:model="cl_pl_alpha">
        @error('cl_pl_alpha') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
--}}
<tr class="border-b">
    <th><label for="cl_notes">{{ __('Notes') }}</label></th>
    <td>
        <textarea tabindex="4" class="form-control @error('cl_pl_notes') is-invalid @enderror py-1" id="cl_notes" placeholder="{{ __('Notes') }}" wire:model="cl_notcl_pl_noteses" style="width: 50%;"></textarea>
        @error('cl_pl_notes') 
            <span class="text-danger">{{ $cl_pl_notes }}</span>
        @enderror
    </td>
</tr>
