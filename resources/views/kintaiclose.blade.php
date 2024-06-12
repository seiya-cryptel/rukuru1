<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-gray-800 leading-tight">
            {{ __('Kintai Close') }}
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
                <form action="{{ route('kintaientry') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <table>
                        <tr>
                            <td width="60">
                                <div style="text-align: right;">
                                    <label for="targetyear" class="text-sm font-medium text-gray-700">{{ __('Target Year') }}
                                </div>
                            </td>
                            <td width="40">
                                <input type="text" name="targetyear" id="targetyear" size="3" maxlength="4" class="form-control py-1 text-sm" value="2024">
                            </td>
                            <td width="60">
                                <div style="text-align: right;">
                                    <label for="targetmonth" class="block text-sm font-medium text-gray-700">{{ __('Target Month') }}
                                </div>
                            </td>
                            <td>
                                <input type="text" name="targetmonth" id="targetmonth" size="1" maxlength="2" class="form-control py-1 text-sm" value="05">
                            </td>
                            <td width="60"> 
                                <div style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">{{ __('Kintai Lock') }}</button>
                                </div>
                            </td>                              
                        </tr>
                    </table>
<div>
    処理結果
</div>
                    <table style="table, th, td {border: 1px solid black;}">
                        <tr>
                        <td>コード</td>
                        <td>Name</td>
                        <td>氏名</td>
                        <td>日</td>
                        <td>確認</td>
</tr>
<tr>
<td><a href="/{{ App::getLocale() }}/kintaipersonday">12345</a></td>
<td><a href="/{{ App::getLocale() }}/kintaipersonday">Izumi Kyushu</a></td>
<td><a href="/{{ App::getLocale() }}/kintaipersonday">九州 泉</a></td>
<td><a href="/{{ App::getLocale() }}/kintaipersonday">2024/05/20</a></td>
<td><a href="/{{ App::getLocale() }}/kintaipersonday">時間が重複しています</a></td>
</tr>
</table>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
