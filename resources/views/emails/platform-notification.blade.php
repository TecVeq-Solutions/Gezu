<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GEZUX Notification</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #070708; color: #f7f7f5; margin: 0; padding: 40px 20px; }
        .container { max-width: 580px; margin: 0 auto; background-color: #121218; border: 1px solid #27272a; border-radius: 16px; padding: 32px; }
        .header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #27272a; padding-bottom: 16px; }
        .logo-text { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; color: #ffffff; }
        .logo-accent { color: #7c3aed; }
        h1 { font-size: 18px; font-weight: 700; color: #ffffff; margin-top: 0; margin-bottom: 12px; }
        p { font-size: 14px; line-height: 1.6; color: #a1a1aa; margin-bottom: 20px; }
        .metadata-box { background-color: #18181f; border-radius: 12px; padding: 16px; margin-bottom: 24px; border: 1px solid #27272a; }
        .metadata-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .metadata-label { color: #71717a; }
        .metadata-value { color: #f4f4f5; font-weight: 600; }
        .warning-badge { background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #fbbf24; padding: 12px; border-radius: 8px; font-size: 12px; margin-bottom: 24px; }
        .btn { display: inline-block; background-color: #7c3aed; color: #ffffff; font-weight: 600; font-size: 14px; text-decoration: none; padding: 12px 24px; border-radius: 10px; text-align: center; }
        .footer { font-size: 11px; color: #52525b; margin-top: 32px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-text">GEZUX<span class="logo-accent">.</span></div>
        </div>

        <h1>{{ $heading }}</h1>
        <p>{{ $bodyContent }}</p>

        @if(!empty($codePresentation))
        <div style="text-align: center; margin: 30px 0; background-color: #18181f; border: 2px dashed #7c3aed; border-radius: 12px; padding: 24px;">
            <div style="font-size: 13px; color: #a1a1aa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Your Verification Code</div>
            <div style="font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #a855f7; font-family: monospace;">{{ $codePresentation }}</div>
        </div>
        @endif

        @if(!empty($contextData))
        <div class="metadata-box">
            @foreach($contextData as $key => $value)
                @if(is_scalar($value))
                <div class="metadata-row">
                    <span class="metadata-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                    <span class="metadata-value">{{ $value }}</span>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        @if(str_contains(strtolower($heading), 'payout') || str_contains(strtolower($heading), 'approval'))
        <div class="warning-badge">
            ⚠️ <strong>Important:</strong> Brand approval does not automatically transfer money. Admin review and manual payout execution are required.
        </div>
        @endif

        @if(!empty($actionUrl) && empty($codePresentation))
        <div style="text-align: center; margin-top: 24px;">
            <a href="{{ url($actionUrl) }}" class="btn">View on GEZUX Platform &rarr;</a>
        </div>
        @endif

        <div class="footer">
            &copy; {{ date('Y') }} GEZUX Marketplace. All rights reserved. Automated security & transaction dispatch.
        </div>
    </div>
</body>
</html>
