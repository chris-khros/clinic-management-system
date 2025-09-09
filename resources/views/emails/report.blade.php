<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #3b82f6;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
    </head>
<body>
    <div class="header">
        <img src="{{ $message->embed(public_path('clinic-logo.png')) }}" alt="{{ config('app.name') }} Logo" style="max-height: 48px; display: block; margin: 0 auto 8px;" />
        <h1>{{ config('app.name') }}</h1>
        <p>{{ ucfirst(str_replace('_', ' ', $reportType)) }} Report</p>
    </div>

    <div class="content">
        <p>Hello,</p>

        <p>Please find attached the requested <strong>{{ str_replace('_', ' ', $reportType) }}</strong> report from {{ config('app.name') }}.</p>

        <p>If you did not request this report or have any questions, please contact our support team.</p>

        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>


