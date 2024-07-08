<div>
    <form>
        <table class="min-w-full table-auto">
        <tr class="border-b">
            <th><label for="name">{{ __('Name') }}</label></th>
            <td>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Enter Name" wire:model="name">
                @error('name') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="email">{{ __('Email') }}</label></th>
            <td>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Enter EMail" wire:model="email">
                @error('email') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <th><label for="password">{{ __('Password') }}</label></th>
            <td>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Enter Password" wire:model="password">
                @error('password') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        <tr class="border-b">
            <td colspan="2">
                <button wire:click.prevent="storeUser()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Save') }}</button>
                <button wire:click.prevent="cancelUser()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded">{{ __('Cancel') }}</button>
            </td>
        </tr>
        </table>
    </form>
</div>