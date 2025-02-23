<tr class="border-b">
    <th><label for="cl_cd">{{ __('Code') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="0" class="form-control @error('cl_cd') is-invalid @enderror text-sm py-1" id="cl_cd" placeholder="{{ __('Client') }}{{ __('Code') }}" wire:model="cl_cd">
        @error('cl_cd') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_name">{{ __('Client') . __('Name') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="1" class="form-control @error('cl_name') is-invalid @enderror text-sm py-1" id="cl_name" placeholder="{{ __('Client') . __('Name') }}" wire:model="cl_name">
        @error('cl_name') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_full_name">{{ __('Client Full Name') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" tabindex="1" class="form-control @error('cl_full_name') is-invalid @enderror text-sm py-1" id="cl_full_name" placeholder="{{ __('Client Full Name') }}" wire:model="cl_full_name">
        @error('cl_full_name') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
{{--
<tr class="border-b">
    <th><label for="cl_kana">{{ __('Client') . __('Kana') }}</label></th>
    <td>
        <input type="text" tabindex="2" class="form-control @error('cl_kana') is-invalid @enderror text-sm py-1" id="cl_kana" placeholder="Enter Kana" wire:model="cl_kana">
        @error('cl_kana') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_alpha">{{ __('Client') . __('Alpha') }}</label</th>
    <td>
        <input type="text" tabindex="3" class="form-control @error('cl_alpha') is-invalid @enderror text-sm py-1" id="cl_alpha" placeholder="Enter Alpha" wire:model="cl_alpha">
        @error('cl_alpha') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
--}}

<tr class="border-b">
    <th><label for="cl_close_day">{{ __('Kintai Close') }}{{ __('Day') }}</label></th>
    <td>
        <input type="text" tabindex="3" class="form-control @error('cl_close_day') is-invalid @enderror text-sm py-1" id="cl_close_day" placeholder="{{ __('Kintai Close') }}{{ __('Day') }}" wire:model="cl_close_day" style="width: 4rem;">
        @error('cl_close_day') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
        0: 末日
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_dow_statutory">{{ __('Statutory Holiday') }}</label></th>
    <td>
        <select tabindex="4" class="form-control @error('cl_dow_statutory') is-invalid @enderror text-sm py-1" id="cl_dow_statutory" wire:model="cl_dow_statutory">
            @foreach($dayOfWeek as $key => $value)
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
        <select tabindex="5" class="form-control @error('cl_dow_non_statutory') is-invalid @enderror text-sm py-1" id="cl_dow_non_statutory" wire:model="cl_dow_non_statutory">
            @foreach($dayOfWeek as $key => $value)
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
        <select tabindex="6" class="form-control @error('cl_over_40hpw') is-invalid @enderror text-sm py-1" id="cl_over_40hpw" wire:model="cl_over_40hpw">
            <option value="0">{{ __('Not Applicable') }}</option>
            <option value="1">{{ __('Applicable') }}</option>
        </select>
        @error('cl_over_40hpw') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_dow_first">{{ __('First Day of Week') }}</label></th>
    <td>
        <select tabindex="7" class="form-control @error('cl_dow_first') is-invalid @enderror text-sm py-1" id="cl_dow_first" wire:model="cl_dow_first">
            @foreach($dayOfWeek as $key => $value)
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
        <input type="text" tabindex="8" class="form-control @error('cl_round_start') is-invalid @enderror text-sm py-1" id="cl_round_start" wire:model="cl_round_start" style="width: 4rem;">
        @error('cl_round_start') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
        分
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_round_end">{{ __('Round End Time') }}</label></th>
    <td>
        <input type="text" tabindex="9" class="form-control @error('cl_round_end') is-invalid @enderror text-sm py-1" id="cl_round_end" wire:model="cl_round_end" style="width: 4rem;">
        @error('cl_round_end') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
        分
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_kintai_style">{{ __('Kintai Entry') }}{{ __('Style') }}</label></th>
    <td>
        <select tabindex="4" class="form-control @error('cl_kintai_style') is-invalid @enderror text-sm py-1" id="cl_kintai_style" wire:model="cl_kintai_style">
        <option value="0">{{ __('Normal') }}</option>
        <option value="1">{{ __('Shift') }}</option>
        </select>
        @error('cl_kintai_style') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="cl_notes">{{ __('Notes') }}</label></th>
    <td>
        <textarea tabindex="10" class="form-control @error('cl_notes') is-invalid @enderror py-1" id="cl_notes" placeholder="{{ __('Notes') }}" wire:model="cl_notes"></textarea>
        @error('cl_notes') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
