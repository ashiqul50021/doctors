@extends('layouts.app')

@section('title', 'Chat Function - abcsheba')

@section('content')
@include('frontend.includes.chat-page-assets')
<!-- Page Content -->
<div class="content chat-page-shell">
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-lg-4 col-xl-3 theiaStickySidebar">
                @if(auth()->user()->role === 'patient')
                    @include('frontend.includes.patient-sidebar')
                @elseif(auth()->user()->role === 'doctor')
                    @include('frontend.includes.doctor-sidebar')
                @endif
            </div>

            <div class="col-md-7 col-lg-8 col-xl-9">
                <div class="chat-window">

                    <!-- Chat Left -->
                    <div class="chat-cont-left">
                        <div class="chat-header">
                            <span>Chats</span>
                            <a href="javascript:void(0)" class="chat-compose">
                                <i class="material-icons">control_point</i>
                            </a>
                        </div>
                        <form class="chat-search">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <i class="fas fa-search"></i>
                                </div>
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                        </form>
                        <div class="chat-users-list">
                            <div class="chat-scroll">
                                @foreach($contacts as $contact)
                                <a href="{{ route('chat.doctor', ['user_id' => $contact->id]) }}" class="media {{ $activeContact && $activeContact->id == $contact->id ? 'active read-chat' : '' }}">
                                    <div class="media-img-wrap">
                                        <div class="avatar {{ $contact->is_online ? 'avatar-online' : 'avatar-offline' }}">
                                            <img src="{{ $contact->patient && $contact->patient->profile_image ? asset($contact->patient->profile_image) : ($contact->doctor && $contact->doctor->profile_image ? asset($contact->doctor->profile_image) : asset('assets/img/patients/patient.jpg')) }}" alt="User Image" class="avatar-img rounded-circle">
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div>
                                            <div class="user-name">{{ $contact->name }}</div>
                                            <div class="user-last-chat">Click to view messages</div>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- /Chat Left -->

                    <!-- Chat Right -->
                    <div class="chat-cont-right">
                        <div class="chat-header">
                            <a id="back_user_list" href="javascript:void(0)" class="back-user-list">
                                <i class="material-icons">chevron_left</i>
                            </a>
                            @if($activeContact)
                            <div class="media">
                                <div class="media-img-wrap">
                                    <div class="avatar {{ $activeContact->is_online ? 'avatar-online' : 'avatar-offline' }}">
                                        <img src="{{ $activeContact->patient && $activeContact->patient->profile_image ? asset($activeContact->patient->profile_image) : ($activeContact->doctor && $activeContact->doctor->profile_image ? asset($activeContact->doctor->profile_image) : asset('assets/img/patients/patient.jpg')) }}" alt="User Image" class="avatar-img rounded-circle">
                                    </div>
                                </div>
                                <div class="media-body">
                                    <div class="user-name">{{ $activeContact->name }}</div>
                                    <div class="user-status">{{ $activeContact->is_online ? 'online' : 'offline' }}</div>
                                </div>
                            </div>
                            @endif
                            <div class="chat-options">
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#voice_call">
                                    <i class="material-icons">local_phone</i>
                                </a>
                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#video_call">
                                    <i class="material-icons">videocam</i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="material-icons">more_vert</i>
                                </a>
                            </div>
                        </div>
                        <div class="chat-body">
                            <div class="chat-scroll">
                                <ul class="list-unstyled">
                                @foreach($messages as $msg)
                                    @if($msg->sender_id == auth()->id())
                                    <li class="media sent">
                                        <div class="media-body">
                                            <div class="msg-box">
                                                <div>
                                                    <p>{{ $msg->message }}</p>
                                                    <ul class="chat-msg-info">
                                                        <li>
                                                            <div class="chat-time">
                                                                <span>{{ $msg->created_at->format('g:i A') }}</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @else
                                    <li class="media received">
                                        <div class="avatar">
                                            <img src="{{ $activeContact->patient && $activeContact->patient->profile_image ? asset($activeContact->patient->profile_image) : ($activeContact->doctor && $activeContact->doctor->profile_image ? asset($activeContact->doctor->profile_image) : asset('assets/img/patients/patient.jpg')) }}" alt="User Image" class="avatar-img rounded-circle">
                                        </div>
                                        <div class="media-body">
                                            <div class="msg-box">
                                                <div>
                                                    <p>{{ $msg->message }}</p>
                                                    <ul class="chat-msg-info">
                                                        <li>
                                                            <div class="chat-time">
                                                                <span>{{ $msg->created_at->format('g:i A') }}</span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="chat-footer">
                            <form id="chat-form" onsubmit="return false;">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="btn-file btn">
                                            <i class="fa fa-paperclip"></i>
                                            <input type="file" id="chat-attachment" name="attachment">
                                        </div>
                                    </div>
                                    <input type="hidden" id="receiver_id" value="{{ $activeContact ? $activeContact->id : '' }}">
                                    <input type="text" id="message-input" class="input-msg-send form-control" placeholder="Type something" {{ !$activeContact ? 'disabled' : '' }}>
                                    <div class="input-group-append">
                                        <button type="button" id="send-btn" class="btn msg-send-btn" {{ !$activeContact ? 'disabled' : '' }}><i class="fab fa-telegram-plane"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /Chat Right -->

                </div>
            </div>
        </div>
        <!-- /Row -->

    </div>

</div>
<!-- /Page Content -->

<!-- Voice Call Modal -->
<div class="modal fade call-modal" id="voice_call">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <!-- Outgoing Call -->
                <div class="call-box incoming-box">
                    <div class="call-wrapper">
                        <div class="call-inner">
                            <div class="call-user">
                                <img alt="User Image" src="{{ asset('assets/img/patients/patient.jpg') }}" class="call-avatar">
                                <h4>Richard Wilson</h4>
                                <span>Connecting...</span>
                            </div>
                            <div class="call-items">
                                <a href="javascript:void(0);" class="btn call-item call-end" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
                                <a href="{{ route('voice.call') }}" class="btn call-item call-start"><i class="material-icons">call</i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Outgoing Call -->

            </div>
        </div>
    </div>
</div>
<!-- /Voice Call Modal -->

<!-- Video Call Modal -->
<div class="modal fade call-modal" id="video_call">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <!-- Incoming Call -->
                <div class="call-box incoming-box">
                    <div class="call-wrapper">
                        <div class="call-inner">
                            <div class="call-user">
                                <img alt="User Image" src="{{ asset('assets/img/patients/patient.jpg') }}" class="call-avatar">
                                <h4>Richard Wilson</h4>
                                <span>Calling ...</span>
                            </div>
                            <div class="call-items">
                                <a href="javascript:void(0);" class="btn call-item call-end" data-bs-dismiss="modal" aria-label="Close"><i class="material-icons">call_end</i></a>
                                <a href="{{ route('video.call') }}" class="btn call-item call-start"><i class="material-icons">videocam</i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Incoming Call -->

            </div>
        </div>
    </div>
</div>
<!-- Video Call Modal -->
<!-- Video Call Modal -->

@push('scripts')
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
<script>
    $(document).ready(function() {
        // Scroll to bottom
        var chatScroll = $('.chat-scroll');
        if (chatScroll.length) {
            chatScroll.scrollTop(chatScroll[0].scrollHeight);
        }

        function sendMessage() {
            var message = $('#message-input').val();
            var receiverId = $('#receiver_id').val();

            if (message.trim() == '' || !receiverId) {
                return;
            }

            $.ajax({
                url: "{{ route('chat.send') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    receiver_id: receiverId,
                    message: message
                },
                success: function(response) {
                    if (response.status === 'success') {
                        var html = '<li class="media sent">' +
                            '<div class="media-body">' +
                            '<div class="msg-box">' +
                            '<div>' +
                            '<p>' + response.data.message + '</p>' +
                            '<ul class="chat-msg-info">' +
                            '<li><div class="chat-time"><span>' + response.data.time + '</span></div></li>' +
                            '</ul>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</li>';
                        $('.chat-body .list-unstyled').append(html);
                        $('#message-input').val('');
                        
                        var chatScroll = $('.chat-body .chat-scroll');
                        chatScroll.scrollTop(chatScroll[0].scrollHeight);
                    }
                }
            });
        }

        $('#send-btn').click(sendMessage);
        $('#message-input').keypress(function(e) {
            if (e.which == 13) {
                sendMessage();
            }
        });
    });
</script>
@endpush

@endsection
