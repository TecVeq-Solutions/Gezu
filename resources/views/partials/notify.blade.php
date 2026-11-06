<link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}">
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>

<script>
    "use strict";

    const notifyColors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6',
    };

    const notifyIcons = {
        success: 'las la-check-circle',
        error: 'las la-times-circle',
        warning: 'las la-exclamation-triangle',
        info: 'las la-info-circle',
    };

    function triggerToast(status, message) {
        if (typeof iziToast === 'undefined') return;

        const safeStatus = notifyColors[status] ? status : 'info';
        iziToast[safeStatus]({
            title: safeStatus.charAt(0).toUpperCase() + safeStatus.slice(1),
            message: message,
            position: "topRight",
            backgroundColor: '#ffffff',
            icon: notifyIcons[safeStatus] || 'las la-bell',
            iconColor: notifyColors[safeStatus],
            progressBarColor: notifyColors[safeStatus],
            titleSize: '0.95rem',
            messageSize: '0.9rem',
            titleColor: '#0f172a',
            messageColor: '#475569',
            transitionIn: 'bounceInLeft',
            transitionOut: 'fadeOutRight',
            timeout: 5000,
            theme: 'light',
            layout: 2,
        });
    }

    window.notify = function(status, message) {
        if (Array.isArray(message)) {
            message.forEach(function(msg) {
                triggerToast(status, msg);
            });
        } else if (typeof message === 'object' && message !== null) {
            Object.values(message).forEach(function(msg) {
                triggerToast(status, msg);
            });
        } else {
            triggerToast(status, message);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        @if(session()->has('notify'))
            @php $notifyList = session('notify'); @endphp
            @if(is_array($notifyList))
                @foreach($notifyList as $n)
                    @if(is_array($n) && count($n) >= 2)
                        triggerToast("{{ $n[0] }}", "{{ addslashes($n[1]) }}");
                    @endif
                @endforeach
            @endif
        @endif

        @if(session()->has('success'))
            triggerToast('success', "{{ addslashes(session('success')) }}");
        @endif

        @if(session()->has('error'))
            triggerToast('error', "{{ addslashes(session('error')) }}");
        @endif

        @if(session()->has('warning'))
            triggerToast('warning', "{{ addslashes(session('warning')) }}");
        @endif

        @if(session()->has('info'))
            triggerToast('info', "{{ addslashes(session('info')) }}");
        @endif

        @if(isset($errors) && $errors->any())
            @foreach($errors->all() as $err)
                triggerToast('error', "{{ addslashes($err) }}");
            @endforeach
        @endif
    });
</script>
