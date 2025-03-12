<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <div class="col-md-8">
        <table class="py-1 text-sm">
        <tr>
            <td>
                <input type="text" class="form-control @error('workYear') is-invalid @enderror py-1 text-sm" id="workYear" wire:model="workYear" wire:change="changeWorkYear($event.target.value)" style="width: 4rem;">
                @error('workYear') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                年
            </td>
            <td>
                <input type="text" class="form-control @error('workMonth') is-invalid @enderror py-1 text-sm" id="workMonth" wire:model="workMonth" wire:change="changeWorkMonth($event.target.value)" style="width: 3rem;">
                @error('workMonth') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                月
            </td>
            <td>
                <select class="form-control @error('client_id') is-invalid @enderror py-1 text-sm" id="client_id" wire:model="client_id" wire:change="updateClientId($event.target.value)">
                    <option value="">{{ __('Select Client') }}</option>
                    @foreach($refClients as $client)
                        <option value="{{ $client->id }}">{{ $client->cl_cd }}:{{ $client->cl_name }}</option>
                    @endforeach
                </select>
                @error('client_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
            <td>
                <select class="form-control @error('clientplace_id') is-invalid @enderror py-1 text-sm" id="clientplace_id" wire:model="clientplace_id" wire:change="updateClientplaceId($event.target.value)">
                    <option value="">{{ __('Select Work Place') }}</option>
                    @foreach($refClientPlaces as $clientplace)
                        <option value="{{ $clientplace->id }}">{{ $clientplace->cl_pl_cd }}:{{ $clientplace->cl_pl_name }}</option>
                    @endforeach
                </select>
                @error('clientplace_id') 
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </td>
        </tr>
        </table>
    </div>
    <div>
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200">
                <tr>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Work Place') }}</th>
                    <th>{{ __('Bill Date') }}</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th> </th>
                </tr>
            </thead>
            <tbody>
                @if (count($Bills) > 0)
                    @foreach ($Bills as $Bill)
                        <tr class="border-b">
                            <td>
                                {{$Bill->client->cl_cd}}:{{$Bill->client->cl_name}}
                            </td>
                            <td>
                                {{$Bill->clientplace->cl_pl_cd}}:{{$Bill->clientplace->cl_pl_name}}
                            </td>
                            <td>
                                {{$Bill->bill_date}}
                            </td>
                            <td>
                                {{$Bill->bill_title}}
                            </td>
                            <td>
                                {{number_format($Bill->bill_total)}}
                            </td>
                            <td>
                                <button wire:click="showBillDetails({{ $Bill->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Details') }}</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" align="center">
                            No Bills Found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    {{ $Bills->links() }}
</div>
