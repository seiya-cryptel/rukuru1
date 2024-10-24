<div>
    <form>
        <table class="min-w-full table-auto text-sm">
        @include('livewire.common.hourlywageitems')
        <tr class="border-b">
            <td colspan="2">
            <button wire:click.prevent="saveEmployeePay()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Save') }}</button>
            <button wire:click.prevent="cancelEmployeePay()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Cancel') }}</button>
            <button wire:click.prevent="resetEmployeePay()" class="bg-green-500 hover:bg-green-700 text-white font-bold text-sm py-1 px-2 rounded">{{ __('Reset Content') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
