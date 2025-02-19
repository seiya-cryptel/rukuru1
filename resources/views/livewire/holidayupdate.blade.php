<div>
    <form>
        <table class="min-w-full table-auto text-sm">
        @include('livewire.common.holidayitems')
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="updateHoliday2()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded" data-save="true">{{ __('Update') }}</button>
                <button wire:click.prevent="cancelHoliday()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
<script src="{{ asset('js/enter2tab.js') }}"></script>
