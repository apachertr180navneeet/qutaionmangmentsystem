<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        @page { size: A4; margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { width: 100%; margin-bottom: 20px; }
        .header .company-info { width: 70%; float: left; }
        .header .company-logo { width: 30%; float: right; text-align: right; }
        .header::after { content: ''; display: table; clear: both; }
        .company-name { font-size: 20px; font-weight: bold; color: #2c3e50; margin: 0 0 5px 0; }
        .company-detail { font-size: 11px; color: #555; line-height: 1.5; margin: 0; }
        .title-section { text-align: center; margin: 25px 0; }
        .title-section h1 { font-size: 28px; font-weight: bold; color: #2c3e50; letter-spacing: 3px; margin: 0; padding: 0; text-transform: uppercase; }
        .info-section { width: 100%; margin-bottom: 20px; }
        .info-section .quotation-info { width: 48%; float: left; }
        .info-section .customer-info { width: 48%; float: right; }
        .info-section::after { content: ''; display: table; clear: both; }
        .info-box { border: 1px solid #ddd; border-radius: 4px; padding: 10px; }
        .info-box h4 { margin: 0 0 8px 0; font-size: 13px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .info-box p { margin: 3px 0; font-size: 11px; color: #555; }
        .info-box .label { font-weight: bold; color: #333; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10px; }
        table.items th { background: #2c3e50; color: #fff; padding: 7px 5px; text-align: center; font-weight: 600; }
        table.items td { padding: 5px; border: 1px solid #ddd; text-align: center; }
        table.items td.left { text-align: left; }
        table.items tr:nth-child(even) { background: #f9f9f9; }
        .summary-section { width: 100%; margin-bottom: 20px; }
        .summary-section .terms { width: 55%; float: left; }
        .summary-section .totals { width: 42%; float: right; }
        .summary-section::after { content: ''; display: table; clear: both; }
        .terms-box { border: 1px solid #ddd; border-radius: 4px; padding: 10px; min-height: 120px; }
        .terms-box h4 { margin: 0 0 8px 0; font-size: 13px; color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        .terms-box p { margin: 3px 0; font-size: 10px; color: #555; line-height: 1.5; }
        table.totals { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.totals td { padding: 4px 8px; border: 1px solid #ddd; }
        table.totals td.label-cell { text-align: left; font-weight: 600; }
        table.totals td.value-cell { text-align: right; }
        table.totals .grand-total td { font-weight: bold; font-size: 13px; background: #2c3e50; color: #fff; }
        .gst-summary { margin-bottom: 10px; }
        .gst-summary table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .gst-summary th { background: #ecf0f1; color: #333; padding: 5px; border: 1px solid #ddd; text-align: center; font-weight: 600; }
        .gst-summary td { padding: 4px 8px; border: 1px solid #ddd; text-align: center; }
        .signature-section { margin-top: 30px; }
        .signature-section .signature { width: 50%; float: right; text-align: right; }
        .signature-section::after { content: ''; display: table; clear: both; }
        .signature-img { max-height: 60px; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
        .badge { display: inline-block; padding: 2px 8px; font-size: 10px; border-radius: 3px; }
        .badge-success { background: #27ae60; color: #fff; }
        .badge-warning { background: #f39c12; color: #fff; }
        .badge-danger { background: #e74c3c; color: #fff; }
        .badge-primary { background: #3498db; color: #fff; }
        .badge-secondary { background: #95a5a6; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <p class="company-name">{{ $company?->company_name ?? config('app.name') }}</p>
            <p class="company-detail">{{ $company?->address }}{{ $company?->city ? ', '.$company?->city : '' }}{{ $company?->state ? ', '.$company?->state : '' }}{{ $company?->zip_code ? ' - '.$company?->zip_code : '' }}</p>
            <p class="company-detail">Phone: {{ $company?->phone }}</p>
            <p class="company-detail">Email: {{ $company?->email }}</p>
            @if($company?->gst_number)
                <p class="company-detail">GST: {{ $company?->gst_number }}</p>
            @endif
        </div>
        <div class="company-logo">
            @if($company?->logo)
                <img src="{{ $company?->logo }}" alt="Logo" style="max-height: 80px;">
            @endif
        </div>
    </div>

    <div class="title-section">
        <h1>Quotation</h1>
    </div>

    <div class="info-section">
        <div class="quotation-info">
            <div class="info-box">
                <h4>Quotation Details</h4>
                <p><span class="label">Quotation #:</span> {{ $quotation->quotation_number }}</p>
                <p><span class="label">Date:</span> {{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</p>
                <p><span class="label">Valid Until:</span> {{ $quotation->valid_until ? date('d-m-Y', strtotime($quotation->valid_until)) : 'N/A' }}</p>
                <p><span class="label">Status:</span>
                    @php
                        $statusBadge = ['draft' => 'secondary', 'sent' => 'primary', 'approved' => 'success', 'expired' => 'warning', 'rejected' => 'danger'];
                    @endphp
                    <span class="badge badge-{{ $statusBadge[$quotation->status] ?? 'secondary' }}">{{ ucfirst($quotation->status) }}</span>
                </p>
            </div>
        </div>
        <div class="customer-info">
            <div class="info-box">
                <h4>Customer Details</h4>
                <p><span class="label">{{ $quotation->customer->company_name ?? 'N/A' }}</span></p>
                <p>{{ $quotation->customer->address ?? '' }}</p>
                @if($quotation->customer->gst_number)
                    <p><span class="label">GST:</span> {{ $quotation->customer->gst_number }}</p>
                @endif
                <p><span class="label">Contact:</span> {{ $quotation->customer->contact_person ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:6%;">#</th>
                <th style="width:10%;">Img</th>
                <th style="width:38%;">Item Name</th>
                <th style="width:12%;">HSN</th>
                <th style="width:10%;">Qty</th>
                <th style="width:12%;">Rate</th>
                <th style="width:12%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>
                    @if($item->item && $item->item->image)
                        <img src="{{ $item->item->image }}" style="max-width:35px; max-height:35px; border-radius:4px;" alt="img">
                    @else
                        -
                    @endif
                </td>
                <td class="left">{{ $item->item->name ?? $item->item_name ?? 'N/A' }}</td>
                <td>{{ $item->item->hsn_code ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->rate, 2) }}</td>
                <td>{{ number_format($item->total, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:15px;">No items found.</td></tr>
            @endforelse
        </tbody>
    </table>

    @php
        $cgstTotal = $quotation->items->sum('cgst_amount');
        $sgstTotal = $quotation->items->sum('sgst_amount');
    @endphp

    <div class="gst-summary">
        <table>
            <thead>
                <tr>
                    <th>Tax Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($cgstTotal > 0)
                <tr><td>CGST</td><td>{{ number_format($cgstTotal, 2) }}</td></tr>
                @endif
                @if($sgstTotal > 0)
                <tr><td>SGST</td><td>{{ number_format($sgstTotal, 2) }}</td></tr>
                @endif
                @if($quotation->igst_amount ?? 0 > 0)
                <tr><td>IGST</td><td>{{ number_format($quotation->igst_amount, 2) }}</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="summary-section">
        <div class="terms">
            <div class="terms-box">
                <h4>Terms & Conditions</h4>
                <p>{{ $quotation->terms_conditions ?: ($company?->terms_conditions ?? 'N/A') }}</p>
            </div>
        </div>
        <div class="totals">
            <table class="totals">
                <tr><td class="label-cell">Subtotal</td><td class="value-cell">{{ number_format($quotation->subtotal, 2) }}</td></tr>
                @if($quotation->discount_amount > 0)
                <tr>
                    <td class="label-cell">Discount ({{ $quotation->discount_type == 'percentage' ? $quotation->discount_value.'%' : 'Fixed' }})</td>
                    <td class="value-cell">-{{ number_format($quotation->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr><td class="label-cell">Total Tax</td><td class="value-cell">{{ number_format($quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount, 2) }}</td></tr>
                <tr><td class="label-cell">Round Off</td><td class="value-cell">{{ number_format($quotation->round_off, 2) }}</td></tr>
                <tr class="grand-total"><td class="label-cell">Grand Total</td><td class="value-cell">{{ number_format($quotation->grand_total, 2) }}</td></tr>
            </table>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature">
            @if($company?->signature)
                <img src="{{ $company?->signature }}" class="signature-img" alt="Signature">
            @endif
            <p style="margin:5px 0 0 0;font-size:11px;color:#555;">Authorised Signatory</p>
        </div>
    </div>

    <div class="footer">
        Page {PAGE_NUM} of {PAGE_COUNT} &mdash; {{ $company?->company_name ?? config('app.name') }}
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans");
            $pdf->page_text(515, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        }
    </script>
</body>
</html>
