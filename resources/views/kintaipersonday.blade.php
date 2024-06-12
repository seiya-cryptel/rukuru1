<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-gray-800 leading-tight">
            {{ __('Kintai Entry') }} > {{ __('Kintai Person') }}
        </h3>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(session('message'))
                <div class="p-6 text-gray-900">
                    {{ session('message') }}
                </div>
                @endif
            </div>

            <div>
                A食品 A工場 2024年 5月 12345 Izumi Kyushu／九州 泉 
                <input type="button" value="登録" onclick="location.href='{{ route('kintaientry', ['year' => 2024, 'month' => 4, 'person' => 12345]) }}'">
            </div>
            <div>
                【業務】 1: 通常 2: 機器洗浄
            </div>
            <div>
                <table>
                <tr>
                    <th>日付</th>
                    <th>曜日</th>
                    <th>有給</th>
                    <th>業務</th>
                    <th>開始</th>
                    <th>終了</th>
                    <th>時間</th>
                    <th>業務</th>
                    <th>開始</th>
                    <th>終了</th>
                    <th>時間</th>
                    <th>業務</th>
                    <th>開始</th>
                    <th>終了</th>
                    <th>時間</th>
                    <th>業務</th>
                    <th>開始</th>
                    <th>終了</th>
                    <th>時間</th>
                </tr>
                @php
                    $week = array("日", "月", "火", "水", "木", "金", "土");
                @endphp
                @for($d = 1; $d <= 31; $d++)
                <tr>
                    @php
                        $dayofweek = date("w", strtotime('2024' . '-' . '5' . '-' . $d));
                    @endphp
                    <td style="text-align: center;">{{ $d }}</td>
                    <td style="{{ ($dayofweek == 0 || $dayofweek == 6) ? 'color: red;' : 'color: black' }}">
                        {{ $week[$dayofweek] }}
                    </td>
                    <td><input type="checkbox" id="chk_yukyu" name="chk_yukyu"></td>

                    <td>
                        <input type="text" name="gyoumu" id="gyoumu" size="2" maxlength="2" class="form-control py-1 text-sm" value="1">
                        通常
                    </td>
                    <td>
                        <input type="text" name="time_s" id="time_s" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>
                        <input type="text" name="time_e" id="time_e" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>0:00</td>

                    <td>
                        <input type="text" name="gyoumu" id="gyoumu" size="2" maxlength="2" class="form-control py-1 text-sm" value="1">
                        通常
                    </td>
                    <td>
                        <input type="text" name="time_s" id="time_s" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>
                        <input type="text" name="time_e" id="time_e" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>0:00</td>

                    <td>
                        <input type="text" name="gyoumu" id="gyoumu" size="2" maxlength="2" class="form-control py-1 text-sm" value="1">
                        通常
                    </td>
                    <td>
                        <input type="text" name="time_s" id="time_s" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>
                        <input type="text" name="time_e" id="time_e" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>0:00</td>

                    <td>
                        <input type="text" name="gyoumu" id="gyoumu" size="2" maxlength="2" class="form-control py-1 text-sm" value="1">
                        通常
                    </td>
                    <td>
                        <input type="text" name="time_s" id="time_s" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>
                        <input type="text" name="time_e" id="time_e" size="2" maxlength="5" class="form-control py-1 text-sm">
                    </td>
                    <td>0:00</td>

                </tr>
                @endfor
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
