<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-gray-800 leading-tight">
            {{ __('Deduct Entry') }}
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
                                <div style="text-align: right;">
                                    <label for="employeekey" class="block text-sm font-medium text-gray-700">{{ __('Employee') }}
                                </div>
                            </td>
                            <td>
                                <input type="text" name="employeekey" id="employeekey" size="6" class="form-control py-1 text-sm">
                            </td>
                            <td width="60"> 
                                <div style="text-align: center;">
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                </div>
                            </td>                              
                        </tr>
                    </table>
<br>
                    <table style="table, th, td {border: 1px solid black;}">
                        <tr>
                        <td>コード</td>
                        <td>Name</td>
                        <td>氏名</td>
</tr>
<tr>
<td><a href="/{{ App::getLocale() }}/deductperson">12345</a></td>
<td><a href="/{{ App::getLocale() }}/deductperson">Izumi Kyushu</a></td>
<td><a href="/{{ App::getLocale() }}/deductperson">九州 泉</a></td>
</tr>
</table>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
