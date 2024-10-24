<tr class="border-b">
    <th><label for="empl_cd">{{ __('Code') }}</label></th>
    <td>
        <input type="text" class="form-control @error('empl_cd') is-invalid @enderror text-sm py-1" id="empl_cd" placeholder="Enter Code" wire:model="empl_cd">
        @error('empl_cd') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_name_last">{{ __('Name') }}</label></th>
    <td>
        <table>
        <tr>
            <td>
                <input type="text" class="form-control @error('empl_name_last') is-invalid @enderror text-sm py-1" id="empl_name_last" placeholder="Enter Name" wire:model="empl_name_last">
                @error('empl_name_last') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" class="form-control @error('empl_name_middle') is-invalid @enderror text-sm py-1" id="empl_name_middle" placeholder="Enter Name" wire:model="empl_name_middle">
                @error('empl_name_middle') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" class="form-control @error('empl_name_first') is-invalid @enderror text-sm py-1" id="empl_name_first" placeholder="Enter Name" wire:model="empl_name_first">
                @error('empl_name_first') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_kana_last">{{ __('Kana') }}</label></th>
    <td>
        <table>
        <tr>
            <td>
                <input type="text" class="form-control @error('empl_kana_last') is-invalid @enderror text-sm py-1" id="empl_kana_last" placeholder="Enter Name" wire:model="empl_kana_last">
                @error('empl_kana_last') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" class="form-control @error('empl_kana_middle') is-invalid @enderror text-sm py-1" id="empl_kana_middle" placeholder="Enter Name" wire:model="empl_kana_middle">
                @error('empl_kana_middle') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" class="form-control @error('empl_kana_first') is-invalid @enderror text-sm py-1" id="empl_kana_first" placeholder="Enter Name" wire:model="empl_kana_first">
                @error('empl_kana_first') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_alpha_last">{{ __('Alpha') }}</label></th>
    <td>
        <table>
        <tr>
            <td>
                <input type="text" class="form-control @error('empl_alpha_last') is-invalid @enderror text-sm py-1" id="empl_alpha_last" placeholder="Enter Name" wire:model="empl_alpha_last">
                @error('empl_alpha_last') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" class="form-control @error('empl_alpha_middle') is-invalid @enderror text-sm py-1" id="empl_alpha_middle" placeholder="Enter Name" wire:model="empl_alpha_middle">
                @error('empl_alpha_middle') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" class="form-control @error('empl_alpha_first') is-invalid @enderror text-sm py-1" id="empl_alpha_first" placeholder="Enter Name" wire:model="empl_alpha_first">
                @error('empl_alpha_first') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        </table>
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_sex">{{ __('Sex') }}</label></th>
    <td>
        <select class="form-control @error('empl_sex') is-invalid @enderror text-sm py-1" id="empl_sex" wire:model="empl_sex">
            <option value="">{{ __('Select Sex') }}</option>
            <option value="{{ 'F' }}">女性</option>
            <option value="{{ 'M' }}">男性</option>
        </select>
        @error('empl_sex') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_email">{{ __('Email') }}</label></th>
    <td>
        <input type="text" class="form-control @error('empl_email') is-invalid @enderror text-sm py-1" id="empl_email" placeholder="Enter Alpha" wire:model="empl_email">
        @error('empl_email') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_mobile">{{ __('Mobile Number') }}</label></th>
    <td>
        <input type="text" class="form-control @error('empl_mobile') is-invalid @enderror text-sm py-1" id="empl_mobile" placeholder="Enter Alpha" wire:model="empl_mobile">
        @error('empl_mobile') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_hire_date">{{ __('Hire Date') }}</label></th>
    <td>
        <input type="date" class="form-control @error('empl_hire_date') is-invalid @enderror text-sm py-1" id="empl_hire_date" placeholder="Enter Alpha" wire:model="empl_hire_date">
        @error('empl_hire_date') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_resign_date">{{ __('Termination Date') }}</label></th>
    <td>
        <input type="date" class="form-control @error('empl_resign_date') is-invalid @enderror text-sm py-1" id="empl_resign_date" placeholder="Enter Alpha" wire:model="empl_resign_date">
        @error('empl_resign_date') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
