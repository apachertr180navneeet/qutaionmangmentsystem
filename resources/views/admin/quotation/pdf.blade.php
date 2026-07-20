<!DOCTYPE html>
<html>
@php
function getLocalImagePath($url) {
    if (!$url) return '';
    if (str_starts_with($url, 'data:image')) return $url;
    
    $path = '';
    $pos = strpos($url, 'uploads/');
    if ($pos !== false) {
        $path = public_path(substr($url, $pos));
    } else {
        $path = public_path($url);
    }

    if ($path && file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    
    return $url;
}
@endphp
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #334155;
            margin: 0;
            padding: 30px 40px 60px 40px;
            line-height: 1.5;
            background-color: #ffffff;
        }
        
        /* Brand Accent Top Line */
        .brand-top-bar {
            height: 6px;
            background: #2563eb;
            margin: -30px -40px 25px -40px;
        }
        
        h1, h2, h3, h4, p { margin: 0; padding: 0; }
        
        /* Top Section Grid */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .header-table td {
            vertical-align: top;
        }
        .doc-title-container {
            text-align: left;
        }
        .doc-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 5px;
        }
        .quotation-badge {
            display: inline-block;
            background-color: #dbeafe;
            color: #2563eb;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 12px;
            text-transform: uppercase;
        }
        
        .company-info-container {
            text-align: right;
        }
        .company-logo-img {
            max-height: 55px;
            max-width: 160px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .company-detail {
            font-size: 9px;
            color: #64748b;
            line-height: 1.35;
        }

        /* Divider */
        .divider {
            border-top: 1px solid #e2e8f0;
            margin: 15px 0 20px 0;
        }

        /* Details Section */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .details-table td {
            vertical-align: top;
            width: 50%;
        }
        
        /* Cards */
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-right: 10px;
        }
        .info-card.right-card {
            margin-right: 0;
            margin-left: 10px;
        }
        .card-title {
            font-size: 10px;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .customer-name {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }
        .customer-detail {
            font-size: 9px;
            color: #475569;
            line-height: 1.45;
        }
        .customer-meta {
            font-size: 9px;
            margin-top: 8px;
            color: #475569;
        }
        .customer-meta strong {
            color: #1e293b;
        }

        .meta-list-table {
            width: 100%;
        }
        .meta-list-table td {
            padding: 4px 0;
            font-size: 9.5px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .meta-list-table tr:last-child td {
            border-bottom: none;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 45%;
        }
        .meta-value {
            color: #0f172a;
            text-align: right;
            font-weight: 600;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 8px;
            border: none;
        }
        .items-table th.left { text-align: left; }
        .items-table th.right { text-align: right; }
        
        .items-table td {
            padding: 10px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            font-size: 9.5px;
        }
        .items-table td.left { text-align: left; }
        .items-table td.right { text-align: right; }
        
        .items-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .item-number-title {
            color: #0f172a;
            font-weight: bold;
        }
        
        /* Summary Grid */
        .summary-container-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .summary-container-table td {
            vertical-align: top;
        }
        
        .gst-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            width: 85%;
        }
        .gst-card-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .gst-table {
            width: 100%;
            border-collapse: collapse;
        }
        .gst-table th, .gst-table td {
            padding: 5px 0;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
        }
        .gst-table th {
            color: #64748b;
            font-weight: bold;
            text-align: left;
        }
        .gst-table td {
            color: #334155;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 12px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals-table .t-label {
            color: #64748b;
            text-align: left;
        }
        .totals-table .t-value {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .totals-table .total-row td {
            background-color: #2563eb;
            color: #ffffff;
            border-bottom: none;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .totals-table .total-row .t-label {
            color: #ffffff;
        }
        .totals-table .total-row .t-value {
            color: #ffffff;
        }

        /* Bottom Section / Terms & Signatures */
        .bottom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .bottom-table td {
            vertical-align: top;
        }
        
        .terms-card {
            background-color: #f8fafc;
            border-left: 3px solid #64748b;
            padding: 12px;
            border-radius: 0 8px 8px 0;
            margin-right: 15px;
        }
        .terms-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .terms-content {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        
        .signature-container {
            text-align: right;
            padding-top: 10px;
        }
        .signature-img {
            max-height: 45px;
            margin-bottom: 5px;
        }
        .signature-line {
            border-top: 1px solid #cbd5e1;
            width: 150px;
            display: inline-block;
            margin-top: 5px;
        }
        .signature-label {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 3px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="brand-top-bar"></div>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="doc-title-container">
                    <h1 class="doc-title">Quotation</h1>
                    <span class="quotation-badge">Official Document</span>
                </div>
            </td>
            <td style="width: 50%;">
                <div class="company-info-container">
                    @if($company?->logo)
                        <img src="{{ getLocalImagePath($company?->logo) }}" alt="Logo" class="company-logo-img">
                    @endif
                    <p class="company-name">{{ $company?->company_name ?? config('app.name') }}</p>
                    <p class="company-detail">{{ $company?->address }}{{ $company?->city ? ', '.$company?->city : '' }}{{ $company?->state ? ', '.$company?->state : '' }}{{ $company?->zip_code ? ' - '.$company?->zip_code : '' }}</p>
                    @if($company?->phone || $company?->email)
                        <p class="company-detail">
                            @if($company?->phone) Tel: {{ $company?->phone }} @endif
                            @if($company?->email) &bull; Email: {{ $company?->email }} @endif
                        </p>
                    @endif
                    @if($company?->gst_number) 
                        <p class="company-detail"><strong>GSTIN:</strong> {{ $company?->gst_number }}</p> 
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Client and Quotation details -->
    <table class="details-table">
        <tr>
            <td style="padding-right: 5px;">
                <div class="info-card">
                    <div class="card-title">Billed To</div>
                    <p class="customer-name">{{ $quotation->customer->company_name ?? 'N/A' }}</p>
                    <p class="customer-detail">{{ $quotation->customer->address ?? '' }}</p>
                    
                    @if($quotation->customer->gst_number || $quotation->customer->contact_person)
                        <div class="customer-meta">
                            @if($quotation->customer->gst_number)
                                <div><strong>GSTIN:</strong> {{ $quotation->customer->gst_number }}</div>
                            @endif
                            @if($quotation->customer->contact_person)
                                <div style="margin-top: 2px;"><strong>Contact Person:</strong> {{ $quotation->customer->contact_person }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </td>
            <td style="padding-left: 5px;">
                <div class="info-card right-card">
                    <div class="card-title">Quotation Details</div>
                    <table class="meta-list-table">
                        <tr>
                            <td class="meta-label">Quotation #</td>
                            <td class="meta-value">{{ $quotation->quotation_number }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Quotation Date</td>
                            <td class="meta-value">{{ $quotation->created_at ? date('M d, Y', strtotime($quotation->created_at)) : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Valid Until</td>
                            <td class="meta-value">{{ $quotation->valid_until ? date('M d, Y', strtotime($quotation->valid_until)) : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="left" style="width: 55%;">Item / Description</th>
                <th style="width: 12%;">Image</th>
                <th style="width: 8%;">Qty</th>
                <th class="right" style="width: 12%;">Rate</th>
                <th class="right" style="width: 13%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $key => $item)
            <tr>
                <td class="left">
                    <span class="item-number-title">{{ $key + 1 }}.</span> {{ $item->item->name ?? $item->item_name ?? 'N/A' }}
                </td>
                <td style="text-align: center;">
                    @if($item->item && $item->item->image)
                        <img src="{{ getLocalImagePath($item->item->image) }}" style="max-width: 25px; max-height: 25px; border-radius: 4px;" alt="img">
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->rate, 2) }}</td>
                <td class="right" style="font-weight: 600;">{{ number_format($item->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">No items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary / Totals Section -->
    <table class="summary-container-table">
        <tr>
            <td style="width: 50%;">
                @php
                    $cgstTotal = $quotation->items->sum('cgst_amount');
                    $sgstTotal = $quotation->items->sum('sgst_amount');
                    $igstTotal = $quotation->igst_amount ?? 0;
                @endphp
                @if($cgstTotal > 0 || $sgstTotal > 0 || $igstTotal > 0)
                    <div class="gst-card">
                        <div class="gst-card-title">Tax Breakdown</div>
                        <table class="gst-table">
                            <thead>
                                <tr>
                                    <th>Tax Type</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($cgstTotal > 0)
                                <tr>
                                    <td>CGST</td>
                                    <td style="text-align: right; font-weight: 600;">{{ number_format($cgstTotal, 2) }}</td>
                                </tr>
                                @endif
                                @if($sgstTotal > 0)
                                <tr>
                                    <td>SGST</td>
                                    <td style="text-align: right; font-weight: 600;">{{ number_format($sgstTotal, 2) }}</td>
                                </tr>
                                @endif
                                @if($igstTotal > 0)
                                <tr>
                                    <td>IGST</td>
                                    <td style="text-align: right; font-weight: 600;">{{ number_format($igstTotal, 2) }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                @endif
            </td>
            <td style="width: 50%;">
                <table class="totals-table">
                    <tr>
                        <td class="t-label">Sub Total</td>
                        <td class="t-value">{{ number_format($quotation->subtotal, 2) }}</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                    <tr>
                        <td class="t-label">Discount ({{ $quotation->discount_type == 'percentage' ? $quotation->discount_value.'%' : 'Fixed' }})</td>
                        <td class="t-value" style="color: #dc2626;">- {{ number_format($quotation->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="t-label">Total Tax</td>
                        <td class="t-value">{{ number_format($quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="t-label">Round Off</td>
                        <td class="t-value">{{ number_format($quotation->round_off, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="t-label">Grand Total</td>
                        <td class="t-value">{{ number_format($quotation->grand_total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Terms & Signatures -->
    <table class="bottom-table">
        <tr>
            <td style="width: 60%;">
                <div class="terms-card">
                    <div class="terms-title">Terms & Conditions</div>
                    <div class="terms-content">{{ $quotation->terms_conditions ?: ($company?->terms_conditions ?? 'N/A') }}</div>
                </div>
            </td>
            <td style="width: 40%; vertical-align: bottom;">
                <div class="signature-container">
                    @if($company?->signature)
                        <img src="{{ getLocalImagePath($company?->signature) }}" alt="Signature" class="signature-img">
                        <br>
                    @endif
                    <div class="signature-line"></div>
                    <p class="signature-label">Authorised Signatory</p>
                </div>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
        Page {PAGE_NUM} of {PAGE_COUNT} &mdash; {{ $company?->company_name ?? config('app.name') }}
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans");
            $pdf->page_text(520, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(148, 163, 184));
        }
    </script>
</body>
</html>
