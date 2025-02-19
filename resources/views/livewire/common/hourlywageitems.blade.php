<tr class="border-b">
    {{-- 従業員 --}}
    {{ $Employee->empl_cd }}:{{ $Employee->empl_name_last }} {{ $Employee->empl_name_first }}
    <table class="text-sm">
        <tr>
            {{-- 顧客 選択 --}}
            <td style="width: 8rem;"><label for="client_id">{{ __('Client') }}</label><span class="text-lg" style="color: red;">*</span></td>
            <td style="width: 12rem;">
                <select tabindex="1" class="form-control @error('client_id') is-invalid @enderror text-sm py-1" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
                    <option value="">{{ __('Common') }}</option>
                    @foreach($refClients as $client)
                        <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
                    @endforeach
                </select>
                @error('client_id') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
            </td>

            {{-- 部門 選択 --}}
            <td class="px-4" style="width: 8rem;"><label for="clientplace_id">{{ __('Work Place') }}</label></td>
            <td style="width: 12rem;">
                <select tabindex="2" class="form-control @error('clientplace_id') is-invalid @enderror text-sm py-1" id="clientplace_id" wire:model="clientplace_id" wire:change="updateClientPlaceId($event.target.value)">
                    <option value="">{{ __('Common') }}</option>
                    @foreach($refClientPlaces as $clientplace)
                        <option value="{{ $clientplace->id }}">{{ $clientplace->cl_pl_cd }}:{{ $clientplace->cl_pl_name }}</option>
                    @endforeach
                </select>
                @error('clientplace_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
    </table>
</tr>
<tr class="border-b">
    <table class="text-sm">
        <tr>
            {{-- 作業種別 選択 --}}
            <td style="width: 8rem;"><label for="clientworktype_id">{{ __('Work Type') }}</label><span class="text-lg" style="color: red;">*</span></td>
            <td>
                @if(count($refClientWorkTypes) > 0)
                    <select tabindex="11" class="form-control @error('clientworktype_id') is-invalid @enderror text-sm py-1" id="clientworktype_id" wire:model="clientworktype_id" wire:change="updateClientWorkTypeId($event.target.value)">
                        <option value="">{{ __('Select') }}</option>
                        @foreach($refClientWorkTypes as $clientworktype)
                            <option value="{{ $clientworktype->id }}">
                                {{-- 
                                {{ $clientworktype->client_id ? $clientworktype->client->cl_name : "共通"}}/
                                {{ $clientworktype->clientplace_id ? $clientworktype->clientplace->cl_pl_name : "共通"}}/
                                --}}
                                {{ $clientworktype->wt_cd }}:{{ $clientworktype->wt_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('clientworktype_id') 
                        <span class="text-danger" style="color: red;">{{ $message }}</span>
                    @enderror
                @else
                    <span>作業種別が登録されていません。顧客や部門を選択してください。</span>
                @endif
            </td>
        </tr>
    </table>
</tr>
<tr class="border-b">
    <table class="text-sm">
    {{-- 作業種別名 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_name">{{ __('Work Type') . __('Name') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_name" wire:model="wt_name" style="width: 12rem;" readonly="readonly">
            </td>

            <td class="px-4" style="width: 8rem;"><label for="wt_kana">{{ __('Kana') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_kana" wire:model="wt_kana" style="width: 12rem;" readonly="readonly">
            </td>

            <td class="px-4" style="width: 8rem;"><label for="wt_alpha">{{ __('Alpha') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_alpha" wire:model="wt_alpha" style="width: 12rem;" readonly="readonly">
            </td>
        </tr>
    </table>
</tr>
<tr class="border-b">
    <table class="text-sm">
    {{-- 作業時間 --}}
        <tr>
            {{-- 日勤夜勤 --}}
            <td style="width: 8rem;"><label for="wt_day_night_name">{{ __('Day or Night') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_day_night_name" wire:model="wt_day_night_name" style="width: 4rem;" readonly="readonly">
            </td>
            
            {{-- 作業開始時刻 --}}
            <td class="px-4" style="width: 8rem;"><label for="wt_work_start">{{ __('Start Time') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_work_start" wire:model="wt_work_start" style="width: 4rem;" readonly="readonly">
            </td>
            
            {{-- 作業終了時刻 --}}
            <td class="px-4" style="width: 8rem;"><label for="wt_work_end">{{ __('End Time') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_work_end" wire:model="wt_work_end" style="width: 4rem;" readonly="readonly">
            </td>
        </tr>
    </table>
</tr>
<tr class="border-b">
    <table class="text-sm">
        {{-- 日勤休憩時間 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_lunch_break">{{ __('Lunch Break') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_lunch_break" wire:model="wt_lunch_break" style="width: 4rem;" readonly="readonly">
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_evening_break">{{ __('Evening Break') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_evening_break" wire:model="wt_evening_break" style="width: 4rem;" readonly="readonly">
            </td>
        </tr>
    </table>
</tr>
<tr class="border-b">
    <table class="text-sm">
        {{-- 夜勤休憩時間 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_night_break">{{ __('Night Break') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_night_break" wire:model="wt_night_break" style="width: 4rem;" readonly="readonly">
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_midnight_break">{{ __('Midnight Break') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control text-sm py-1 bg-gray-100" id="wt_midnight_break" wire:model="wt_midnight_break" style="width: 4rem;" readonly="readonly">
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
    <table class="text-sm">
        {{-- 時給 --}}
        <tr>
            <td style="width: 8rem;"> </td>
            <td style="width: 12rem;">時給</td>
            <td class="px-4" style="width: 8rem;"> </td>
            <td style="width: 12rem;">請求</td>
        </tr>
        <tr>
            <td><label for="wt_pay_std">{{ __('Regular') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="31" class="form-control @error('wt_pay_std') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_pay_std }}" id="wt_pay_std" wire:model="wt_pay_std" wire:change="updateWage($event.target.value, 'wt_pay_std')" style="width: 4rem;">
                @error('wt_pay_std') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td><label for="wt_bill_std">{{ __('Regular') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="41" class="form-control @error('wt_bill_std') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_bill_std }}" id="wt_bill_std" wire:model="wt_bill_std" wire:change="updateWage($event.target.value, 'wt_bill_std')" style="width: 4rem;">
                @error('wt_bill_std') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr>
            <td><label for="wt_pay_ovr">{{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="32" class="form-control @error('wt_pay_ovr') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_pay_ovr }}" id="wt_pay_ovr" wire:model="wt_pay_ovr" wire:change="updateWage($event.target.value, 'wt_pay_ovr')" style="width: 4rem;">
                @error('wt_pay_std') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td><label for="wt_bill_ovr">{{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="42" class="form-control @error('wt_bill_ovr') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_bill_ovr }}" id="wt_bill_ovr" wire:model="wt_bill_ovr" wire:change="updateWage($event.target.value, 'wt_bill_ovr')" style="width: 4rem;">
                @error('wt_bill_ovr') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr>
            <td><label for="wt_pay_ovr_midnight">{{ __('Midnight') }} {{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="33" class="form-control @error('wt_pay_ovr_midnight') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_pay_ovr_midnight }}" id="wt_pay_ovr_midnight" wire:model="wt_pay_ovr_midnight" wire:change="updateWage($event.target.value, 'wt_pay_ovr_midnight')" style="width: 4rem;">
                @error('wt_pay_ovr_midnight') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td><label for="wt_bill_ovr_midnight">{{ __('Midnight') }} {{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="43" class="form-control @error('wt_bill_ovr_midnight') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_bill_ovr_midnight }}" id="wt_bill_ovr_midnight" wire:model="wt_bill_ovr_midnight" wire:change="updateWage($event.target.value, 'wt_bill_ovr_midnight')" style="width: 4rem;">
                @error('wt_bill_ovr_midnight') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr>
            <td><label for="wt_pay_holiday">{{ __('Statutory Holiday') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="34" class="form-control @error('wt_pay_holiday') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_pay_holiday }}" id="wt_pay_holiday" wire:model="wt_pay_holiday" wire:change="updateWage($event.target.value, 'wt_pay_holiday')" style="width: 4rem;">
                @error('wt_pay_holiday') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td><label for="wt_bill_holiday">{{ __('Statutory Holiday') }} {{ __('Unit Price') }}</label></td>
            <td>
                <input type="text" tabindex="44" class="form-control @error('wt_bill_holiday') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_bill_holiday }}" id="wt_bill_holiday" wire:model="wt_bill_holiday" wire:change="updateWage($event.target.value, 'wt_bill_holiday')" style="width: 4rem;">
                @error('wt_bill_holiday') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr>
            <td><label for="wt_pay_holiday_midnight">{{ __('Statutory Holiday') }} {{ __('Midnight') }} {{ __('Overtime Work') }}</label></td>
            <td>
                <input type="text" tabindex="35" class="form-control @error('wt_pay_holiday_midnight') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_pay_holiday_midnight }}" id="wt_pay_holiday_midnight" wire:model="wt_pay_holiday_midnight" wire:change="updateWage($event.target.value, 'wt_pay_holiday_midnight')" style="width: 4rem;">
                @error('wt_pay_holiday_midnight') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td><label for="wt_bill_holiday_midnight">{{ __('Statutory Holiday') }} {{ __('Midnight') }} {{ __('Overtime Work') }}</label></td>
            <td>
                <input type="text" tabindex="45" class="form-control @error('wt_bill_holiday_midnight') is-invalid @enderror text-sm py-1 text-right {{ $bg_wt_bill_holiday_midnight }}" id="wt_bill_holiday_midnight" wire:model="wt_bill_holiday_midnight" wire:change="updateWage($event.target.value, 'wt_bill_holiday_midnight')" style="width: 4rem;">
                @error('wt_bill_holiday_midnight') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
    </table>
</tr>
