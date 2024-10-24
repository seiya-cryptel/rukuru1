<div>
    <form>
        <table class="min-w-full table-auto text-sm">
        @include('livewire.common.clientplaceitems')
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="updateClientPlace2()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Update') }}</button>
                <button wire:click.prevent="cancelClientPlace()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>
<script src="{{ asset('js/dirtycheck.js') }}"></script>
