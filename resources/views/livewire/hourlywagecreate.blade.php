<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" role="alert" style="color: red;">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <form>
        <table class="min-w-full table-auto text-sm">
        @include('livewire.common.hourlywageitems')
        <tr class="border-b">
            <td colspan="2">
            <button wire:click.prevent="saveEmployeePay()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded" data-save="true">{{ __('Save') }}</button>
            <button wire:click.prevent="cancelEmployeePay()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold text-sm py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
<script src="{{ asset('js/enter2tab.js') }}"></script>
