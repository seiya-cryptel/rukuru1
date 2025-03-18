<tr class="border-b">
    <th><label for="empl_cd">{{ __('Code') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <input type="text" 
            tabindex="1" 
            class="form-control @error('empl_cd') is-invalid @enderror text-sm py-1" 
            id="empl_cd" 
            wire:model="empl_cd">
        @error('empl_cd') 
            <span class="text-danger" style="color: red;">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_name_last">{{ __('Name') }}</label><span class="text-lg" style="color: red;">*</span></th>
    <td>
        <table>
        <tr>
            <td>
                <input type="text" 
                    tabindex="11" 
                    class="form-control @error('empl_name_last') is-invalid @enderror text-sm py-1" 
                    id="empl_name_last" 
                    placeholder="姓を入力" 
                    wire:model="empl_name_last">
                @error('empl_name_last') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" 
                    tabindex="13" 
                    class="form-control @error('empl_name_first') is-invalid @enderror text-sm py-1" 
                    id="empl_name_first" 
                    placeholder="名" 
                    wire:model="empl_name_first">
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
                <input type="text" 
                    tabindex="21" 
                    class="form-control @error('empl_kana_last') is-invalid @enderror text-sm py-1" 
                    id="empl_kana_last" 
                    placeholder="姓カナ" 
                    wire:model="empl_kana_last">
                @error('empl_kana_last') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <input type="text" 
                    tabindex="23" 
                    class="form-control @error('empl_kana_first') is-invalid @enderror text-sm py-1" 
                    id="empl_kana_first" 
                    placeholder="名カナ" 
                    wire:model="empl_kana_first">
                @error('empl_kana_first') 
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
        <select tabindex="41" 
            class="form-control @error('empl_sex') is-invalid @enderror text-sm py-1" 
            id="empl_sex" 
            wire:model="empl_sex">
            <option value="">{{ __('Sex') }}</option>
            <option value="F">女性</option>
            <option value="M">男性</option>
        </select>
        @error('empl_sex') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_email">{{ __('Email') }}</label></th>
    <td>
        <input type="text" 
            tabindex="42" 
            class="form-control @error('empl_email') is-invalid @enderror text-sm py-1" 
            id="empl_email" 
            wire:model="empl_email">
        @error('empl_email') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_mobile">{{ __('Mobile Number') }}</label></th>
    <td>
        <input type="text" 
            tabindex="43" 
            class="form-control @error('empl_mobile') is-invalid @enderror text-sm py-1" 
            id="empl_mobile" 
            wire:model="empl_mobile">
        @error('empl_mobile') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_hire_date">{{ __('Hire Date') }}</label></th>
    <td>
        <input type="date" 
            tabindex="51" 
            class="form-control @error('empl_hire_date') is-invalid @enderror text-sm py-1" 
            id="empl_hire_date" 
            wire:model="empl_hire_date">
        @error('empl_hire_date') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_resign_date">{{ __('Termination Date') }}</label></th>
    <td>
        <input type="date" 
            tabindex="52" 
            class="form-control @error('empl_resign_date') is-invalid @enderror text-sm py-1" 
            id="empl_resign_date" 
            wire:model="empl_resign_date">
        @error('empl_resign_date') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
{{--
<tr class="border-b">
    <th><label for="empl_paid_leave_pay">{{ __('Paid Leave Payout') }}</label></th>
    <td>
        <input type="text" tabindex="61" 
            class="form-control @error('empl_paid_leave_pay') is-invalid @enderror text-sm py-1 text-right" 
            id="empl_paid_leave_pay" 
            wire:model="empl_paid_leave_pay" 
            wire:change="moneyChange($event.target.value, 'empl_paid_leave_pay')" 
            style="width: 6rem;">
        @error('empl_paid_leave_pay') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
--}}
<tr class="border-b">
    <th><label for="empl_main_client_id">{{ __('Main Client') }}</label></th>
    <td>
        <select 
            tabindex="71" 
            class="form-control @error('empl_main_client_id') is-invalid @enderror text-sm py-1" 
            id="empl_main_client_id" 
            wire:model="empl_main_client_id"
            wire:change="emplMainClientIdChange($event.target.value)">
            <option value="">{{ __('Main Client') }}</option>
            @foreach($refClients as $client)
                <option value="{{ $client->id }}">{{ $client->cl_cd }} {{ $client->cl_name }}</option>
            @endforeach
        </select>
        @error('empl_main_client_id') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_main_clientplace_id">{{ __('Main Client Place') }}</label></th>
    <td>
        <select 
            tabindex="72" 
            class="form-control @error('empl_main_clientplace_id') is-invalid @enderror text-sm py-1" 
            id="empl_main_clientplace_id" 
            wire:model="empl_main_clientplace_id">
            <option value="">{{ __('Main Client Place') }}</option>
            @foreach($refClientPlaces as $clientPlace)
                <option value="{{ $clientPlace->id }}">{{ $clientPlace->cl_pl_cd }} {{ $clientPlace->cl_pl_name }}</option>
            @endforeach
        </select>
        @error('empl_main_clientplace_id') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
<tr class="border-b">
    <th>{{ __('Work Type') }}</th>
    <td>
        @for($i=0; $i<self::MAX_SLOTS; $i++)
            <select tabindex="8{{$i}}" 
                class="form-control @error('empl_main_clientplace_id'.$i) is-invalid @enderror text-sm py-1" 
                id="wt_cd_list.{{$i}}" 
                wire:model="wt_cd_list.{{$i}}"
                style="width: 8rem;">
                <option value=""></option>
                @foreach($refWtCdList as $wtCdList)
                    <option value="{{ $wtCdList->wt_cd }}">{{ $wtCdList->wt_cd }} {{ $wtCdList->wt_name }}</option>
                @endforeach
            </select>
        @endfor
    </td>
</tr>
<tr class="border-b">
    <th><label for="empl_notes">{{ __('Notes') }}</label></th>
    <td>
        <textarea 
            tabindex="91" 
            class="w-full form-control @error('empl_notes') is-invalid @enderror text-sm py-1" 
            id="empl_notes" 
            wire:model="empl_notes"
            style="width: 50rem;"></textarea>
        @error('empl_notes') 
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </td>
</tr>
