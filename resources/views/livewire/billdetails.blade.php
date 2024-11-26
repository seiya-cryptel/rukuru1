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
    <div class="col-md-8 py-1">
        {{ $Bill->work_year }}年 {{ $Bill->work_month }}月 {{ $Client['cl_cd'] }}:{{ $Client['cl_name'] }} 様 {{ $ClientPlace['cl_pl_cd'] }}:{{ $ClientPlace['cl_pl_name'] }}

        <button wire:click.prevent="cancelBillDetails()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div class="col-md-8 py-1 text-sm">
        <table>
            <thead>
            <tr>
                <th>No.</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Unit Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Unit') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Tax') }}</th>
                <th>{{ __('Total') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($BillDetails as $BillDetail)
            <tr>
                <td>{{ $BillDetail->display_order }}</td> 
                <td>{{ $BillDetail->title }}</td> 
                <td>{{ $BillDetail->unit_price }}</td> 
                <td>{{ $BillDetail->quantity }}</td> 
                <td>{{ $BillDetail->unit }}</td> 
                <td>{{ $BillDetail->amount }}</td> 
                <td>{{ $BillDetail->tax }}</td> 
                <td>{{ $BillDetail->total }}</td> 
            </tr>
            @endforeach
            </tbody>
        </table>
        <button wire:click.prevent="cancelBillDetails()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
</div>
