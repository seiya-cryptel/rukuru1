<div>
    <div>
        <span class="text-sm">【{{ __('Application Logs') }}】</span>
        <table class="min-w-full table-auto">
            <tbody>
            @foreach ($Applogs as $Applog)
                <tr class="border-b">
                    <td style="width: 10rem;">
                        <span class="text-sm">{{$Applog->logged_at}}</span>
                    </td>
                    <td>
                        @php
                        $logTypeString = $this->logTypeString($Applog->log_type);
                        @endphp
                        <span class="text-sm">{{ $logTypeString }}</span>
                    </td>
                    <td>
                        <span class="text-sm">{{$Applog->log_user}}</span>
                    </td>
                    <td>
                        <span class="text-sm">{{$Applog->remote_addr}}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $Applogs->links() }}
</div>
