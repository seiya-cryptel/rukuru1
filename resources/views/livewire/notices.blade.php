<div>
    <div class="col-md-8 mb-2">
        @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
        @endif                
        @if(session()->has('error'))
            <div class="alert alert-danger" role="alert" style="color: red;">
                {{ session()->get('error') }}
            </div>
        @endif
    </div>
    <div>
        <span class="text-sm">【{{ __('Notices') }}】</span>
        <table class="min-w-full table-auto">
            <tbody>
            @foreach ($Notices as $Notice)
                <tr class="border-b">
                    <td style="width: 6rem;">
                        <span class="text-sm">{{$Notice->notice_date}}</span>
                    </td>
                    <td style="width: 20rem;">
                        <span class="text-sm" title="{{ $Notice->title }}">{{ Str::limit($Notice->title, 32) }}</span>
                    </td>
                    <td>
                        <span class="text-sm" title="{{ $Notice->content }}">{{ Str::limit($Notice->content, 64) }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
