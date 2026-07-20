<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .email-header { background: #2c3e50; padding: 20px 30px; text-align: center; }
        .email-header img { max-height: 60px; }
        .email-body { padding: 30px; }
        .email-body p { font-size: 14px; line-height: 1.6; color: #555; margin: 0 0 15px 0; }
        .greeting { font-size: 16px; font-weight: bold; color: #333; }
        .quote-details { background: #f9f9f9; border-left: 4px solid #2c3e50; padding: 15px; margin: 20px 0; }
        .quote-details p { margin: 5px 0; font-size: 13px; }
        .btn { display: inline-block; padding: 10px 25px; background: #2c3e50; color: #fff !important; text-decoration: none; border-radius: 4px; font-size: 14px; margin-top: 10px; }
        .email-footer { background: #f4f4f4; padding: 20px 30px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #ddd; }
        .email-footer p { margin: 3px 0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            @if($company->logo)
                <img src="{{ $company->logo }}" alt="{{ $company->company_name }}">
            @else
                <h2 style="color:#fff;margin:0;">{{ $company->company_name ?? config('app.name') }}</h2>
            @endif
        </div>
        <div class="email-body">
            <p class="greeting">Dear Customer,</p>
            <p>{{ $messageContent }}</p>
            <div class="quote-details">
                <p><strong>Quotation #:</strong> {{ $quotation->quotation_number }}</p>
                <p><strong>Date:</strong> {{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</p>
                <p><strong>Grand Total:</strong> {{ number_format($quotation->grand_total, 2) }}</p>
            </div>
            <p>
                <a href="{{ url('/quotation/' . $quotation->uuid) }}" class="btn">View Quotation</a>
            </p>
            <p>Regards,<br>{{ $company->company_name ?? config('app.name') }}</p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ $company->company_name ?? config('app.name') }}. All rights reserved.</p>
            <p>{{ $company->email }} | {{ $company->phone }}</p>
        </div>
    </div>
</body>
</html>
