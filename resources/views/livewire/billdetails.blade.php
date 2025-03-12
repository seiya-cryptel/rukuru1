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

        <button wire:click.prevent="downloadBill()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 text-sm rounded" data-cancel="true">{{ __('Bill Export') }}</button>
        <button wire:click.prevent="downloadBillDetails()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 text-sm rounded" data-cancel="true">{{ __('Bill Detail Export') }}</button>
        <button wire:click.prevent="cancelBillDetails()" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-1 px-2 text-sm rounded" data-cancel="true">{{ __('Cancel') }}</button>
    </div>
    <div>
        <table class="min-w-full table-auto">
            <thead class="bg-gray-200 text-sm">
            <tr>
                <th>No.</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Unit Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Unit') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Tax') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($BillDetails as $BillDetail)
            <tr>
                <td class="text-center">{{ $BillDetail->display_order }}</td> 
                <td>{{ $BillDetail->title }}</td> 
                <td class="text-right">{{ number_format($BillDetail->unit_price) }}</td> 
                <td class="text-center">{{ $BillDetail->quantity_string }}</td> 
                <td class="text-center">{{ $BillDetail->unit }}</td> 
                <td class="text-right">{{ number_format($BillDetail->amount) }}</td> 
                <td class="text-right">{{ number_format($BillDetail->tax) }}</td> 
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
