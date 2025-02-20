<x-app-layout>
    <x-slot name="header">
        <h3 class="{{-- font-semibold --}} text-xl text-gray-800 leading-tight">
            {{ __('Import Kintai') }}
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
                <form action="{{ route('importkintai') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <table>
                        <tr>
                            <td>
                                <label for="client" class="block text-sm font-medium text-gray-700">{{ __('Client') }}
                            </td>
                            <td>
                                <select name="client" id="client" class="form-control">
                                    <option value="1">A食品</option>
                                    <option value="2">株式会社B</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="workplace" class="block text-sm font-medium text-gray-700">{{ __('Work Place') }}
                            </td>
                            <td>
                                <select name="workplace" id="workplace" class="form-control">
                                    <option value="1">A工場</option>
                                    <option value="2">B部門</option>
                                </select>
                            <td>
                        </tr>
                        <tr>
                            <td>
                                <label for="targetyear" class="block text-sm font-medium text-gray-700">{{ __('Target Year') }}
                            </td>
                            <td>
                                <input type="text" name="targetyear" id="targetyear" class="form-control">
                            </td>
                            <td>
                                <label for="targetmonth" class="block text-sm font-medium text-gray-700">{{ __('Target Month') }}
                            </td>
                            <td>
                                <input type="text" name="targetmonth" id="targetmonth" class="form-control">
                            <td>
                        </tr>
                    </table>

                    <div>
                        {{ __('Import File') }}
                        <input type="file" name="file" id="file" class="form-control">
                        <button type="submit" class="btn btn-success">{{ __('Import Kintai') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
