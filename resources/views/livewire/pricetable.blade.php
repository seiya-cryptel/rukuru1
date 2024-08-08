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
        @if($addPriceTable)
            @include('livewire.pricetablecreate')
        @endif            
        @if($updatePriceTable)
            @include('livewire.pricetableupdate')
        @endif
    </div>
    <div class="col-md-8">
        <div class="text-right">            
            @if(!$addPriceTable)
                <button wire:click="newPriceTable()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">{{ __('Add') }}</button>
            @endif
        </div>
        <div>
            <table class="min-w-full table-auto">
                <thead class="bg-gray-200">
                    <tr>
                        <th>{{ __('Client') }}</th>
                        <th>{{ __('Work Place') }}</th>
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Item Name') }}</th>
                        <th>{{ __('Print Name') }}</th>
                        <th>{{ __('Unit Price') }}</th>
                        <th>{{ __('Display Order') }}</th>
                        <th>{{ __('Notes') }}</th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($PriceTables) > 0)
                        @foreach ($PriceTables as $PriceTable)
                            <tr class="border-b">
                                <td>
                                    {{$PriceTable->client->cl_cd}}:{{$PriceTable->client->cl_name}}
                                </td>
                                <td>
                                    {{$PriceTable->clientplace->cl_pl_cd}}:{{$PriceTable->clientplace->cl_pl_name}}
                                </td>
                                <td>
                                    {{$PriceTable->wt_cd}}
                                </td>
                                <td>
                                    {{$PriceTable->bill_name}}
                                </td>
                                <td>
                                    {{$PriceTable->bill_print_name}}
                                </td>
                                <td>
                                    {{$PriceTable->bill_unitprice}}
                                </td>
                                <td>
                                    {{$PriceTable->display_order}}
                                </td>
                                <td>
                                    <button wire:click="editPriceTable({{$PriceTable->id}})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">{{ __('Edit') }}</button>
                                    <button onclick="deletePriceTable({{$PriceTable->id}})" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" align="center">
                                No PriceTable Found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>    
    <script>
        function deletePriceTable(id){
            if(confirm("Are you sure to delete this record?"))
                Livewire.dispatch('deletePriceTableListener', { id: id });
        }
    </script>
</div>