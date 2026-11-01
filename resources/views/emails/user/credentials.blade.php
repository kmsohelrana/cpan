<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Credentials</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .header {
            background-color: #003399;
            padding: 32px 40px;
            text-align: center;
        }

        .header img {
            height: 48px;
            margin-bottom: 12px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .header p {
            color: #b3c6ff;
            font-size: 13px;
            margin-top: 4px;
        }

        .body {
            padding: 36px 40px;
        }

        .greeting {
            font-size: 16px;
            color: #444;
            margin-bottom: 20px;
        }

        .greeting strong {
            color: #003399;
        }

        .intro {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .credentials-box {
            background: #f0f4ff;
            border-left: 4px solid #003399;
            border-radius: 6px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }

        .credentials-box h3 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #003399;
            margin-bottom: 16px;
        }

        .credential-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dce5ff;
        }

        .credential-row:last-child {
            border-bottom: none;
        }

        .credential-label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }

        .credential-value {
            font-size: 14px;
            color: #111;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            background: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            border: 1px solid #c8d5ff;
        }

        .login-btn {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-btn a {
            display: inline-block;
            background-color: #003399;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .notice {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-radius: 6px;
            padding: 14px 18px;
            font-size: 13px;
            color: #7a5c00;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .notice strong {
            display: block;
            margin-bottom: 4px;
        }

        .footer {
            background: #f4f6f9;
            padding: 20px 40px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .footer p {
            font-size: 12px;
            color: #999;
            line-height: 1.6;
        }

        .footer a {
            color: #003399;
            text-decoration: none;
        }

    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="body">
            <p class="greeting">Dear <strong>{{ $name }}</strong>,</p>
            <p class="intro">
                @if(isset($type) && $type === 'reset')
                Your password has been reset successfully. Below are your new login credentials.
                @else
                Welcome! Your account has been created on <strong>{{ config('app.name') }}</strong>.
                Below are your login credentials. Please keep this information confidential and do not share it with anyone.
                @endif
            </p>

            <div class="credentials-box">
                <h3>Your Login Credentials</h3>
                <div class="credential-row">
                    <span class="credential-label">Login Email</span>
                    <span class="credential-value">{{ $email }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Temporary Password</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
                <div class="credential-row">
                    <span class="credential-label">Login URL</span>
                    <span class="credential-value">{{ $loginUrl }}</span>
                </div>
            </div>

            <div class="login-btn">
                <a href="{{ $loginUrl }}">Login to Your Account</a>
            </div>

            <div class="notice">
                <strong>⚠ Important Security Notice</strong>
                You are required to change your password immediately after your first login.
                Do not share your credentials with anyone, including {{ config('app.name') }} staff.
            </div>

            <p style="font-size:13px; color:#777;">
                If you did not expect this email or believe this was sent in error, please contact your system administrator immediately.
            </p>
        </div>

        <div class="footer">
            <p>
                This is an automated email from <a href="{{ $loginUrl }}">{{ config('app.name') }}</a>.<br>
                Please do not reply to this email.
            </p>
            <p style="margin-top:8px;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
