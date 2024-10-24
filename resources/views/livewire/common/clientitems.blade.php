<tr class="border-b">
    <th><label for="cl_cd">{{ __('Code') }}</label><span style="color: red;">*</span></th>
    <td>
        <input type="text" class="form-control @error('cl_cd') is-invalid @enderror text-sm py-1" id="cl_cd" placeholder="Enter Code" wire:model="cl_cd">
        @error('cl_cd') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_name">{{ __('Client') . __('Name') }}</label><span style="color: red;">*</span></th>
    <td>
        <input type="text" class="form-control @error('cl_name') is-invalid @enderror text-sm py-1" id="cl_name" placeholder="Enter Name" wire:model="cl_name">
        @error('cl_name') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_kana">{{ __('Client') . __('Kana') }}</label><span style="color: red;">*</span></th>
    <td>
        <input type="text" class="form-control @error('cl_kana') is-invalid @enderror text-sm py-1" id="cl_kana" placeholder="Enter Kana" wire:model="cl_kana">
        @error('cl_kana') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_alpha">{{ __('Client') . __('Alpha') }}</label><span style="color: red;">*</span></th>
    <td>
        <input type="text" class="form-control @error('cl_alpha') is-invalid @enderror text-sm py-1" id="cl_alpha" placeholder="Enter Alpha" wire:model="cl_alpha">
        @error('cl_alpha') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>

@php
$dayOfWeeks = [0 => '日曜', 1 => '月曜', 2 => '火曜', 3 => '水曜', 4 => '木曜', 5 => '金曜', 6 => '土曜'];
@endphp
<tr class="border-b">
    <th><label for="cl_dow_statutory">{{ __('Statutory Holiday') }}</label></th>
    <td>
        <select class="form-control @error('cl_dow_statutory') is-invalid @enderror text-sm py-1" id="cl_dow_statutory" wire:model="cl_dow_statutory">
            @foreach($dayOfWeeks as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        @error('cl_dow_statutory') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_dow_non_statutory">{{ __('Non-Statutory Holiday') }}</label></th>
    <td>
        <select class="form-control @error('cl_dow_non_statutory') is-invalid @enderror text-sm py-1" id="cl_dow_non_statutory" wire:model="cl_dow_non_statutory">
            @foreach($dayOfWeeks as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        @error('cl_dow_non_statutory') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_over_40hpw">{{ __('40 Hours a Week') }}</label></th>
    <td>
        <select class="form-control @error('cl_over_40hpw') is-invalid @enderror text-sm py-1" id="cl_over_40hpw" wire:model="cl_over_40hpw">
            <option value="0">{{ __('No') }}</option>
            <option value="1">{{ __('Yes') }}</option>
        </select>
        @error('cl_over_40hpw') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_dow_first">{{ __('First Day of Week') }}</label></th>
    <td>
        <select class="form-control @error('cl_dow_first') is-invalid @enderror text-sm py-1" id="cl_dow_first" wire:model="cl_dow_first">
            @foreach($dayOfWeeks as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        @error('cl_dow_first') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_round_start">{{ __('Round Start Time') }}</label></th>
    <td>
        <input type="text" class="form-control @error('cl_round_start') is-invalid @enderror text-sm py-1" id="cl_round_start" wire:model="cl_round_start" style="width: 4rem;">
        @error('cl_round_start') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_round_end">{{ __('Round End Time') }}</label></th>
    <td>
        <input type="text" class="form-control @error('cl_round_end') is-invalid @enderror text-sm py-1" id="cl_round_end" wire:model="cl_round_end" style="width: 4rem;">
        @error('cl_round_end') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
