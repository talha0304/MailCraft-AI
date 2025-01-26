<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 10px !important;
            }
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }
        .header {
            padding: 32px;
            border-bottom: 1px solid #e5e7eb;
        }
        .content {
            padding: 32px;
            color: #374151;
            line-height: 1.5;
        }
        .footer {
            padding: 24px 32px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 style="margin:0;font-size:24px;font-weight:600;color:#1f2937;">
                 MailCraft
            </h1>
        </div>
        
        <div class="content">
            {!! nl2br(e($content)) !!}
        </div>
        
        <div class="footer">
            <p style="margin:0 0 8px;">
                © {{ date('Y') }} MailCraft. All rights reserved.
            </p>
            {{-- <p style="margin:0;">
                <a href="{{ config('app.url') }}" style="color:#4f46e5;text-decoration:none;">Visit our website</a>
                <span style="margin:0 8px;">•</span>
                <a href="#" style="color:#4f46e5;text-decoration:none;">Unsubscribe</a>
            </p> --}}
        </div>
    </div>
</body>
</html>