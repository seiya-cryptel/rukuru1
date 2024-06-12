<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold text-xl --}} text-gray-800 leading-tight">
            {{ __('Master Mainte') }} > {{ __('Client') }} > {{ __('Edit') }}
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
                <table>
                <tr>
                <th>ID</th>
                <td><input type="text" id="id" name="id" readonly="readonly"></td>
                </tr>
                <tr>
                <th>コード</th>
                <td><input type="text" id="client_code" name="client_code"></td>
                </tr>
                <tr>
                <th>社名</th>
                <td><input type="text" id="client_corp" name="client_corp"></td>
                </tr>
                <tr>
                <th>郵便番号</th>
                <td><input type="text" id="client_zip" name="client_zip"></td>
                </tr>
                <tr>
                <th>住所１</th>
                <td><input type="text" id="client_addr1" name="client_addr1"></td>
                </tr>
                <tr>
                <th>住所２</th>
                <td><input type="text" id="client_addr2" name="client_addr2"></td>
                </tr>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
