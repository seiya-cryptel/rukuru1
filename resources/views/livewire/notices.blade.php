<div>
    <div>
        <span class="text-sm">【{{ __('Notices') }}】</span>
        <table class="min-w-full table-auto">
            <tbody>
            @foreach ($Notices as $Notice)
                <tr class="border-b">
                    <td style="width: 6rem;">
                        <span class="text-sm">{{$Notice->notice_date}}</span>
                    </td>
                    <td>
                        <span class="text-sm" title="{{ $Notice->title }}">{{ Str::limit($Notice->title, 32) }}</span>
                    </td>
                    <td>
                        <span class="text-sm" title="{{ $Notice->content }}">{{ Str::limit($Notice->content, 32) }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
