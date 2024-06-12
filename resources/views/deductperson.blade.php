<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-gray-800 leading-tight">
            {{ __('Deduct Entry') }} > {{ __('Deduct Person') }}
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
                2024年 5月 12345 Izumi Kyushu／九州 泉 
                <input type="button" value="登録" onclick="location.href='{{ route('kintaientry', ['year' => 2024, 'month' => 4, 'person' => 12345]) }}'">
                </div>
            
            <div>
                <table>
                <tr>
                <th>手当計</th>
                    @for($i = 1; $i <= 10; $i++)
                    <th>手当{{ $i }}</th>
                    @endfor
                </tr>
                <tr>
                <td>
                        {{ 10,000 }}
</td>
                    @for($i = 1; $i <= 10; $i++)
                    <td>
                        <input type="text" name="teate{{ $i }}" id="teate{{ $i }}" size="4" maxlength="8" class="form-control py-1 text-sm" value="1">
                    </td>
                    @endfor
                </tr>
                </table>
            </div>

            <div>
                <table>
                <tr>
                    <th>控除計</th>
                    @for($i = 1; $i <= 10; $i++)
                    <th>控除{{ $i }}</th>
                    @endfor
                </tr>
                <tr>
                    <td>
                        {{ 10,000 }}
</td>
                    @for($i = 1; $i <= 10; $i++)
                    <td>
                        <input type="text" name="koujyo{{ $i }}" id="koujyo{{ $i }}" size="4" maxlength="8" class="form-control py-1 text-sm" value="1">
                    </td>
                    @endfor
                </tr>
                </table>
            </div>
            
            <div>
            勤怠額 100,000 円<br>
            支給額 100,000 円
            </div>
            
            @php
                    $week = array("日", "月", "火", "水", "木", "金", "土");
                @endphp
            <div>
            <table>
                <tr>
                <th>日</th>
                <th>曜日</th>
                <th>有給</th>
                <th>業務</th>
                <th>開始</th>
                <th>終了</th>
                <th>時間</th>
                <th>金額</th>
                <th>割増</th>
                </tr>
                @for($i = 1; $i <= 31; $i+=2)
                @for($j = 1; $j <= 2; $j++)
                @php
                        $dayofweek = date("w", strtotime('2024' . '-' . '5' . '-' . $i));
                    @endphp
                <tr>
                    @if($j == 1)
                <td>
                    {{ ($i - 1) * 3 +1 }}
</td>
                <td>
                    {{ $week[$dayofweek] }}
                    </td>
                    @else
                    <td colspan="2">
                    &nbsp;
                    </td>
                    @endif
                    <td><input type="checkbox" id="chk_yukyu" name="chk_yukyu" readonly="readonly"></td>
                    <td>通常</td>
                    <td>08:00</td>
                    <td>12:00</td>
                    <td>4:00</td>
                    <td>4,000</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
                @endfor
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
