<div>
    <form>
        <table class="min-w-full table-auto text-sm">
        @include('livewire.common.allowdeductitems')
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="storeMad()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Save') }}</button>
                <button wire:click.prevent="cancelMad()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
