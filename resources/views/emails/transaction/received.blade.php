<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Received</title>
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
            background-color: #0b7a4b;
            padding: 32px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .header p {
            color: #bfe8d3;
            font-size: 13px;
            margin-top: 4px;
        }

        .body {
            padding: 36px 40px;
        }

        .success-badge {
            text-align: center;
            margin-bottom: 24px;
        }

        .success-badge .icon {
            display: inline-block;
            width: 64px;
            height: 64px;
            line-height: 64px;
            border-radius: 50%;
            background: #e6f7ee;
            color: #0b7a4b;
            font-size: 32px;
            font-weight: 700;
        }

        .amount {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #0b7a4b;
            margin-bottom: 8px;
        }

        .amount-label {
            text-align: center;
            font-size: 13px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 28px;
        }

        .greeting {
            font-size: 15px;
            color: #444;
            margin-bottom: 12px;
        }

        .greeting strong {
            color: #0b7a4b;
        }

        .intro {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .details-box {
            background: #f5faf7;
            border-left: 4px solid #0b7a4b;
            border-radius: 6px;
            padding: 20px 24px;
            margin-bottom: 28px;
        }

        .details-box h3 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0b7a4b;
            margin-bottom: 16px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dcefe4;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            font-size: 14px;
            color: #111;
            font-weight: 600;
            font-family: 'Courier New', monospace;
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
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ $bankName }}</h1>
            <p>Transaction Notification</p>
        </div>

        <div class="body">
            <div class="success-badge">
                <span class="icon">&#10003;</span>
            </div>

            <div class="amount">&#2547;{{ $amount }}</div>
            <div class="amount-label">Payment Received</div>

            @if(!empty($merchantName))
            <p class="greeting">Dear <strong>{{ $merchantName }}</strong>,</p>
            @endif
            <p class="intro">{!! nl2br(e($message)) !!}</p>

            <div class="details-box">
                <h3>Transaction Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Reference No</span>
                    <span class="detail-value">{{ $trxId }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount</span>
                    <span class="detail-value">&#2547;{{ $amount }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Charge</span>
                    <span class="detail-value">&#2547;{{ $charge }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Merchant Amount</span>
                    <span class="detail-value">&#2547;{{ $merchantAmount }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Card/Token No</span>
                    <span class="detail-value">{{ $maskedPan }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction Date</span>
                    <span class="detail-value">{{ $trxTime }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated message from {{ $bankName }}. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $bankName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
