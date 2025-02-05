<tr class="border-b">
    <table class="text-sm">
        <tr>
            {{-- 顧客選択 --}}
            <td style="width: 8rem;"><label for="client_id">{{ __('Client') }}</label></td>
            <td style="width: 12rem;">
                <select class="form-control @error('client_id') is-invalid @enderror text-sm py-1" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
                    <option value="">{{ __('Select Client') }}</option>
                    @foreach($refClients as $client)
                        <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
                    @endforeach
                </select>
                @error('client_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>

            {{-- 作業場所選択 --}}
            <td class="px-4" style="width: 8rem;"><label for="clientplace_id">{{ __('Work Place') }}</label></td>
            <td style="width: 12rem;">
                <select class="form-control @error('clientplace_id') is-invalid @enderror text-sm py-1" id="clientplace_id" wire:model="clientplace_id" wire:change="updateClientPlaceId($event.target.value)">
                    <option value="">{{ __('Select Work Place') }}</option>
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
        {{-- 作業種別 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_cd">{{ __('Work Type') . __('Code') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_cd') is-invalid @enderror text-sm py-1" id="wt_cd" placeholder="Enter Code" wire:model="wt_cd" style="width: 4rem;">
                @error('wt_cd') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
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
                <input type="text" class="form-control @error('wt_name') is-invalid @enderror text-sm py-1" id="wt_name" placeholder="Enter Name" wire:model="wt_name" style="width: 12rem;">
                @error('wt_name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>

            <td class="px-4" style="width: 8rem;"><label for="wt_kana">{{ __('Kana') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_kana') is-invalid @enderror text-sm py-1" id="wt_kana" placeholder="Enter Kana" wire:model="wt_kana" style="width: 12rem;">
                @error('wt_kana') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>

            <td class="px-4" style="width: 8rem;"><label for="wt_alpha">{{ __('Alpha') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_alpha') is-invalid @enderror text-sm py-1" id="wt_alpha" placeholder="Enter Alpha" wire:model="wt_alpha" style="width: 12rem;">
                @error('wt_alpha') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
    <table class="text-sm">
        {{-- 作業時間 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_day_night">{{ __('Day or Night') }}</label></td>
            <td style="width: 12rem;">
                <select class="form-control @error('wt_day_night') is-invalid @enderror text-sm py-1" id="wt_day_night" wire:model="wt_day_night">
                    <option value="1">{{ __('Day Work') }}</option>
                    <option value="2">{{ __('Night Work') }}</option>
                </select>
                @error('wt_day_night') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_work_start">{{ __('Start Time') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_work_start') is-invalid @enderror text-sm py-1" id="wt_work_start" wire:model="wt_work_start" wire:change="timeChange($event.target.value, 'wt_work_start')" style="width: 4rem;">
                @error('wt_work_start') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                例 8:00
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_work_end">{{ __('End Time') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_work_end') is-invalid @enderror text-sm py-1" id="wt_work_end" wire:model="wt_work_end" wire:change="timeChange($event.target.value, 'wt_work_end')" style="width: 4rem;">
                @error('wt_work_end') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                例 17:00
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
{{-- 日勤休憩時間 --}}
    <table class="text-sm">
        <tr>
            <td style="width: 8rem;"> </td>
            <td style="width: 4.2rem;">開始時刻</td>
            <td style="width: 1rem;">〜</td>
            <td style="width: 4rem;">終了時刻</td>
        </tr>
    </table>
    <table class="text-sm">
        <tr>
            <td style="width: 8rem;"><label for="wt_lunch_break_start">{{ __('Lunch Break') }} {{ __('Time Zone') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" 
                    class="form-control @error('wt_lunch_break_start') is-invalid @enderror text-sm py-1"
                    id="wt_lunch_break_start" 
                    wire:model="wt_lunch_break_start" 
                    wire:change="timeChange($event.target.value, 'wt_lunch_break_start')"
                    style="width: 4rem;">
                @error('wt_lunch_break_start') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                〜
                <input type="text" 
                    class="form-control @error('wt_lunch_break_end') is-invalid @enderror text-sm py-1"
                    id="wt_lunch_break_end" 
                    wire:model="wt_lunch_break_end" 
                    wire:change="timeChange($event.target.value, 'wt_lunch_break_end')"
                    style="width: 4rem;">
                @error('wt_lunch_break_end') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            <td class="px-4" style="width: 8rem;"><label for="wt_lunch_break">{{ __('Or') }}{{ __('Hours') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" 
                    class="form-control @error('wt_lunch_break') is-invalid @enderror text-sm py-1" 
                    id="wt_lunch_break" 
                    wire:model="wt_lunch_break" 
                    wire:change="timeChange($event.target.value, 'wt_lunch_break')" 
                    style="width: 4rem;">
                @error('wt_lunch_break') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                例 0:45
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
    <table class="text-sm">
        {{-- 夕休憩時間 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_evening_break_start">{{ __('Evening Break') }} {{ __('Time Zone') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" 
                    class="form-control @error('wt_evening_break_start') is-invalid @enderror text-sm py-1"
                    id="wt_evening_break_start" 
                    wire:model="wt_evening_break_start" 
                    wire:change="timeChange($event.target.value, 'wt_evening_break_start')"
                    style="width: 4rem;">
                @error('wt_evening_break_start') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                〜
                <input type="text" 
                    class="form-control @error('wt_evening_break_end') is-invalid @enderror text-sm py-1"
                    id="wt_evening_break_end" 
                    wire:model="wt_evening_break_end" 
                    wire:change="timeChange($event.target.value, 'wt_evening_break_end')"
                    style="width: 4rem;">
                @error('wt_evening_break_end') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_evening_break">{{ __('Or') }}{{ __('Hours') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_evening_break') is-invalid @enderror text-sm py-1" id="wt_evening_break" wire:model="wt_evening_break" wire:change="timeChange($event.target.value, 'wt_evening_break')" style="width: 4rem;">
                @error('wt_evening_break') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                例 0:15
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
    <table class="text-sm">
        {{-- 夜勤休憩時間 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_night_break_start">{{ __('Night Break') }} {{ __('Time Zone') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" 
                    class="form-control @error('wt_night_break_start') is-invalid @enderror text-sm py-1"
                    id="wt_night_break_start" 
                    wire:model="wt_night_break_start" 
                    wire:change="timeChange($event.target.value, 'wt_night_break_start')"
                    style="width: 4rem;">
                @error('wt_night_break_start') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                〜
                <input type="text" 
                    class="form-control @error('wt_night_break_end') is-invalid @enderror text-sm py-1"
                    id="wt_night_break_end" 
                    wire:model="wt_night_break_end" 
                    wire:change="timeChange($event.target.value, 'wt_night_break_end')"
                    style="width: 4rem;">
                @error('wt_night_break_end') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_night_break">{{ __('Or') }}{{ __('Hours') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_night_break') is-invalid @enderror text-sm py-1" id="wt_night_break" wire:model="wt_night_break" wire:change="timeChange($event.target.value, 'wt_night_break')" style="width: 4rem;">
                @error('wt_night_break') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                例 1:00
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
    <table class="text-sm">
        {{-- 深夜休憩時間 --}}
        <tr>
            <td style="width: 8rem;"><label for="wt_midnight_break_start">{{ __('Midnight Break') }} {{ __('Time Zone') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" 
                    class="form-control @error('wt_midnight_break_start') is-invalid @enderror text-sm py-1"
                    id="wt_midnight_break_start" 
                    wire:model="wt_midnight_break_start" 
                    wire:change="timeChange($event.target.value, 'wt_midnight_break_start')"
                    style="width: 4rem;">
                @error('wt_midnight_break_start') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                〜
                <input type="text" 
                    class="form-control @error('wt_midnight_break_end') is-invalid @enderror text-sm py-1"
                    id="wt_midnight_break_end" 
                    wire:model="wt_midnight_break_end" 
                    wire:change="timeChange($event.target.value, 'wt_midnight_break_end')"
                    style="width: 4rem;">
                @error('wt_midnight_break_end') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
            </td>
            
            <td class="px-4" style="width: 8rem;"><label for="wt_midnight_break">{{ __('Or') }}{{ __('Hours') }}</label></td>
            <td style="width: 12rem;">
                <input type="text" class="form-control @error('wt_midnight_break') is-invalid @enderror text-sm py-1" id="wt_midnight_break" wire:model="wt_midnight_break" wire:change="timeChange($event.target.value, 'wt_midnight_break')" style="width: 4rem;">
                @error('wt_midnight_break') 
                    <span class="text-danger" style="color: red;">{{ $message }}</span>
                @enderror
                例 1:00
            </td>
        </tr>
    </table>
</tr>

<tr class="border-b">
    <table class="text-sm">
    <tr>
        <td style="width: 8rem;"> </td>
        <td style="width: 12rem;">時給</td>
        <td class="px-4" style="width: 8rem;"> </td>
        <td style="width: 12rem;">請求</td>
    </tr>
    <tr>
        <td><label for="wt_pay_std">{{ __('Regular') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_pay_std') is-invalid @enderror text-sm py-1 text-right" id="wt_pay_std" wire:model="wt_pay_std" wire:change="moneyChange($event.target.value, 'wt_pay_std')" style="width: 4rem;">
            @error('wt_pay_std') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
        <td><label for="wt_bill_std">{{ __('Regular') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_bill_std') is-invalid @enderror text-sm py-1 text-right" id="wt_bill_std" wire:model="wt_bill_std" wire:change="moneyChange($event.target.value, 'wt_bill_std')" style="width: 4rem;">
            @error('wt_bill_std') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
    </tr>
    <tr>
        <td><label for="wt_pay_ovr">{{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_pay_ovr') is-invalid @enderror text-sm py-1 text-right" id="wt_pay_ovr" wire:model="wt_pay_ovr" wire:change="moneyChange($event.target.value, 'wt_pay_ovr')" style="width: 4rem;">
            @error('wt_pay_std') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
        <td><label for="wt_bill_ovr">{{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_bill_ovr') is-invalid @enderror text-sm py-1 text-right" id="wt_bill_ovr" wire:model="wt_bill_ovr" wire:change="moneyChange($event.target.value, 'wt_bill_ovr')" style="width: 4rem;">
            @error('wt_bill_ovr') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
    </tr>
    <tr>
        <td><label for="wt_pay_ovr_midnight">{{ __('Midnight') }} {{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_pay_ovr_midnight') is-invalid @enderror text-sm py-1 text-right" id="wt_pay_ovr_midnight" wire:model="wt_pay_ovr_midnight" wire:change="moneyChange($event.target.value, 'wt_pay_ovr_midnight')" style="width: 4rem;">
            @error('wt_pay_ovr_midnight') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
        <td><label for="wt_bill_ovr_midnight">{{ __('Midnight') }} {{ __('Overtime Work') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_bill_ovr_midnight') is-invalid @enderror text-sm py-1 text-right" id="wt_bill_ovr_midnight" wire:model="wt_bill_ovr_midnight" wire:change="moneyChange($event.target.value, 'wt_bill_ovr_midnight')" style="width: 4rem;">
            @error('wt_bill_ovr_midnight') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
    </tr>
    <tr>
        <td><label for="wt_pay_holiday">{{ __('Statutory Holiday') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_pay_holiday') is-invalid @enderror text-sm py-1 text-right" id="wt_pay_holiday" wire:model="wt_pay_holiday" wire:change="moneyChange($event.target.value, 'wt_pay_holiday')" style="width: 4rem;">
            @error('wt_pay_holiday') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
        <td><label for="wt_bill_holiday">{{ __('Statutory Holiday') }} {{ __('Unit Price') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_bill_holiday') is-invalid @enderror text-sm py-1 text-right" id="wt_bill_holiday" wire:model="wt_bill_holiday" wire:change="moneyChange($event.target.value, 'wt_bill_holiday')" style="width: 4rem;">
            @error('wt_bill_holiday') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
    </tr>
    <tr>
        <td><label for="wt_pay_holiday_midnight">{{ __('Statutory Holiday') }} {{ __('Midnight') }} {{ __('Overtime Work') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_pay_holiday_midnight') is-invalid @enderror text-sm py-1 text-right" id="wt_pay_holiday_midnight" wire:model="wt_pay_holiday_midnight" wire:change="moneyChange($event.target.value, 'wt_pay_holiday_midnight')" style="width: 4rem;">
            @error('wt_pay_holiday_midnight') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
        <td><label for="wt_bill_holiday_midnight">{{ __('Statutory Holiday') }} {{ __('Midnight') }} {{ __('Overtime Work') }}</label></td>
        <td>
            <input type="text" class="form-control @error('wt_bill_holiday_midnight') is-invalid @enderror text-sm py-1 text-right" id="wt_bill_holiday_midnight" wire:model="wt_bill_holiday_midnight" wire:change="moneyChange($event.target.value, 'wt_bill_holiday_midnight')" style="width: 4rem;">
            @error('wt_bill_holiday_midnight') 
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </td>
    </tr>
    </table>
</tr>
