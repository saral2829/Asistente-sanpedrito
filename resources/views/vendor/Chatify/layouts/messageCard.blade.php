{{-- -------------------- The default card (white) -------------------- --}}
@if($viewType == 'default')
    @if($from_id != $to_id)
    <div class="message-card" data-id="{{ $id }}" style="max-width: 75%;">
        <p  style="padding: 7px 12px; border-radius: 18px;">{!! ($message == null && $attachment != null && @$attachment[2] != 'file') ? $attachment[1] : nl2br($message) !!}
            {{-- If attachment is a file --}}
            @if(@$attachment[2] == 'file')
            <a href="{{ route(config('chatify.attachments.download_route_name'),['fileName'=>$attachment[0]]) }}" style="background:rgba(157, 157, 157,.5); border: none; margin-top: 0; padding: 5px 8px; display: flex; align-items: center; line-height: 0; width: 100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" class="file-download">
                <span class="fas fa-file" style="margin-right: 5px; text-overflow: ellipsis;"></span> {{$attachment[1]}}</a>
            @endif
            <sub class="message-time" style="opacity: 50%; float:right; margin-bottom: 5px; padding-top: 5px;">{{ $time }}</sub>
        </p>
    </div>
    {{-- If attachment is an image --}}
    @if(@$attachment[2] == 'image')
    <div>
        <div class="message-card" style="">
            <div class="image-file chat-image" style="border-radius: 18px; background-image: url('{{ asset('storage/'.config('chatify.attachments.folder').'/'.$attachment[0]) }}')">
            </div>
        </div>
    </div>
    @endif
    @endif
@endif

{{-- -------------------- Sender card (owner) -------------------- --}}
@if($viewType == 'sender')
    <div class="message-card mc-sender" data-id="{{ $id }}" >
        <p style="padding: 7px 12px; border-radius: 18px;">{!! ($message == null && $attachment != null && @$attachment[2] != 'file') ? $attachment[1] : nl2br($message) !!}
                {{-- If attachment is a file --}}
            @if(@$attachment[2] == 'file')
            <a href="{{ route(config('chatify.attachments.download_route_name'),['fileName'=>$attachment[0]]) }}" class="file-download">
                <span class="fas fa-file"></span> {{$attachment[1]}}</a>
            @endif
            <sub class="message-time" style="opacity: 50%; float:right; margin-bottom: 5px; padding-top: 5px">
                <span class="fas fa-{{ $seen > 0 ? 'check-double' : 'check' }} seen"></span> {{ $time }}
            </sub>
        </p>
    </div>
    {{-- If attachment is an image --}}
    @if(@$attachment[2] == 'image')
    <div>
        <div class="message-card mc-sender">
            <div class="image-file chat-image" style="width: 250px; height: 150px;background-image: url('{{ asset('storage/'.config('chatify.attachments.folder').'/'.$attachment[0]) }}')">
            </div>
        </div>
    </div>
    @endif
@endif
