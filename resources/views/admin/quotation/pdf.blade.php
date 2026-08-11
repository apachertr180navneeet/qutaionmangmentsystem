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
            font-size: 10.5px;
            color: #495057;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
            background-color: #ffffff;
        }
        
        h1, h2, h3, h4, h5, h6, p { margin: 0; padding: 0; }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-logo-text {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .company-logo-img {
            max-height: 50px;
            max-width: 160px;
            margin-bottom: 8px;
        }
        .company-details {
            font-size: 9px;
            color: #6c757d;
            line-height: 1.4;
        }
        
        .quote-title-container {
            text-align: right;
        }
        .quote-title {
            font-size: 20px;
            font-weight: bold;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .quote-meta-text {
            font-size: 10px;
            color: #212529;
            margin-bottom: 3px;
        }
        .quote-meta-text strong {
            color: #212529;
        }

        .divider {
            border-top: 1px solid #dee2e6;
            margin: 10px 0 25px 0;
        }

        /* Client Info Section */
        .client-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .client-section td {
            vertical-align: top;
        }
        .section-subtitle {
            font-size: 9px;
            font-weight: bold;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .client-name {
            font-size: 13px;
            font-weight: bold;
            color: #212529;
            margin-bottom: 4px;
        }
        .client-details {
            font-size: 9.5px;
            color: #6c757d;
            line-height: 1.45;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #f8f9fa;
            color: #212529;
            font-weight: bold;
            font-size: 10px;
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .items-table th.left { text-align: left; }
        .items-table th.center { text-align: center; }
        .items-table th.right { text-align: right; }
        
        .items-table td {
            padding: 12px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
            color: #212529;
            font-size: 10px;
        }
        .items-table td.left { text-align: left; }
        .items-table td.center { text-align: center; }
        .items-table td.right { text-align: right; }
        
        .item-img-preview {
            width: 50px;
            height: 50px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        .item-img-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            text-align: center;
            line-height: 50px;
            color: #adb5bd;
            font-size: 14px;
        }
        
        .item-name {
            font-size: 10.5px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 2px;
        }
        .item-description {
            font-size: 9px;
            color: #6c757d;
        }
        
        /* Summary Section */
        .summary-table-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .summary-table-container td {
            vertical-align: top;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 10.5px;
        }
        .totals-table .label {
            color: #6c757d;
            text-align: left;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 600;
            color: #212529;
        }
        .totals-table .grand-total-row td {
            font-size: 14px;
            font-weight: bold;
            color: #0d6efd;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .totals-table .grand-total-row .label {
            color: #0d6efd;
        }
        .totals-table .grand-total-row .value {
            color: #0d6efd;
        }

        /* Terms and Signature Footer */
        .footer-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            page-break-inside: avoid;
        }
        .footer-section td {
            vertical-align: top;
        }

        .terms-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .terms-title {
            font-size: 10px;
            font-weight: bold;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .terms-content {
            font-size: 9px;
            color: #495057;
            line-height: 1.7;
        }
        .terms-content ol {
            margin: 0;
            padding-left: 16px;
        }
        .terms-content ol li {
            margin-bottom: 3px;
        }
        .terms-list {
            font-size: 9px;
            color: #495057;
            line-height: 1.7;
            padding-left: 16px;
            margin: 0;
        }
        .terms-list li {
            margin-bottom: 3px;
        }
        
        .signature-container {
            text-align: right;
        }
        .signature-img {
            max-height: 40px;
            margin-bottom: 4px;
        }
        .signature-line {
            border-top: 1px solid #dee2e6;
            width: 140px;
            display: inline-block;
            margin-top: 5px;
        }
        .signature-label {
            font-size: 8.5px;
            color: #6c757d;
            margin-top: 3px;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .page-number-footer {
            position: fixed;
            bottom: 15px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #adb5bd;
            border-top: 1px solid #f8f9fa;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header / Branding -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                @if($company?->logo)
                    <img src="{{ getLocalImagePath($company?->logo) }}" alt="Logo" class="company-logo-img">
                @else
                    <div class="company-logo-text">{{ $company?->company_name ?? config('app.name') }}</div>
                @endif
                <div class="company-details">
                    {{ $company?->address }}{{ $company?->city ? ', '.$company?->city : '' }}{{ $company?->state ? ', '.$company?->state : '' }}{{ $company?->zip_code ? ' - '.$company?->zip_code : '' }}
                    <br>
                    @if($company?->email) Email: {{ $company?->email }} @endif
                    @if($company?->phone) | Phone: {{ $company?->phone }} @endif
                    @if($company?->gst_number) <br>GSTIN: {{ $company?->gst_number }} @endif
                </div>
            </td>
            <td style="width: 45%;">
                <div class="quote-title-container">
                    <h3 class="quote-title">Price Quotation</h3>
                    <p class="quote-meta-text"><strong>Quote #:</strong> {{ $quotation->quotation_number }}</p>
                    <p class="quote-meta-text"><strong>Date:</strong> {{ $quotation->created_at ? date('M d, Y', strtotime($quotation->created_at)) : 'N/A' }}</p>
                    <p class="quote-meta-text"><strong>Valid Until:</strong> {{ $quotation->valid_until ? date('M d, Y', strtotime($quotation->valid_until)) : 'N/A' }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Client Info -->
    <table class="client-section">
        <tr>
            <td>
                <h6 class="section-subtitle">Prepared For:</h6>
                <h5 class="client-name">{{ $quotation->customer->company_name ?? 'N/A' }}</h5>
                <p class="client-details">
                    @if($quotation->customer->contact_person)
                        Attn: {{ $quotation->customer->contact_person }}<br>
                    @endif
                    {{ $quotation->customer->address ?? '' }}
                    @if($quotation->customer->gst_number)
                        <br>GSTIN: {{ $quotation->customer->gst_number }}
                    @endif
                </p>
            </td>
        </tr>
    </table>

    <!-- Quotation Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="left" style="width: 45px;">Image</th>
                <th class="left">Item & SKU</th>
                <th class="center" style="width: 40px;">Qty</th>
                <th class="right" style="width: 75px;">MRP</th>
                <th class="right" style="width: 75px;">Rate</th>
                <th class="right" style="width: 85px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $key => $item)
            <tr>
                <td class="left">
                    @if($item->item && $item->item->image)
                        <img src="{{ getLocalImagePath($item->item->image) }}" class="item-img-preview" alt="Product">
                    @else
                        <div class="item-img-placeholder">📦</div>
                    @endif
                </td>
                <td class="left">
                    <h6 class="item-name">{{ $item->item->name ?? $item->item_name ?? 'N/A' }}</h6>
                    @if($item->sku || ($item->item && $item->item->sku))
                        <div style="font-size: 8.5px; color: #6c5ce7; font-weight: bold; margin-bottom: 2px;">SKU: {{ $item->sku ?: $item->item->sku }}</div>
                    @endif
                    @if($item->item && $item->item->description)
                        <span class="item-description">{{ $item->item->description }}</span>
                    @endif
                </td>
                <td class="center">{{ formatNumber($item->quantity) }}</td>
                <td class="right">{{ formatNumber($item->mrp ?: ($item->item->mrp ?? $item->rate)) }}</td>
                <td class="right">{{ formatNumber($item->rate) }}</td>
                <td class="right" style="font-weight: bold;">{{ formatNumber($item->total) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center" style="color: #6c757d; padding: 20px;">No items found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Calculation / Summary -->
    <table class="summary-table-container">
        <tr>
            <td style="width: 55%;">
                <!-- Left column remains blank or for watermark/notes -->
            </td>
            <td style="width: 45%;">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">{{ formatNumber($quotation->subtotal) }}</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                    <tr>
                        <td class="label">Discount ({{ $quotation->discount_type == 'percentage' ? formatNumber($quotation->discount_value).'%' : 'Fixed' }})</td>
                        <td class="value" style="color: #dc3545;">- {{ formatNumber($quotation->discount_amount) }}</td>
                    </tr>
                    @endif
                    @php
                        $totalTax = $quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount;
                    @endphp
                    @if($totalTax > 0)
                    <tr>
                        <td class="label">Tax</td>
                        <td class="value">{{ formatNumber($totalTax) }}</td>
                    </tr>
                    @endif
                    @if($quotation->round_off != 0)
                    <tr>
                        <td class="label">Round Off</td>
                        <td class="value">{{ formatNumber($quotation->round_off) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td class="label">Grand Total</td>
                        <td class="value">{{ formatNumber($quotation->grand_total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Terms & Footer -->
    <table class="footer-section">
        <tr>
            <td style="width: 60%; padding-top: 15px;">
                <div class="terms-box">
                    <h6 class="terms-title">Terms & Conditions</h6>
                    <div class="terms-content">
                        @if($quotation->terms_conditions || $company?->terms_conditions)
                            @php
                                $termsText = $quotation->terms_conditions ?: $company?->terms_conditions;
                                $termsLines = array_filter(array_map('trim', preg_split('/\r?\n/', $termsText)));
                            @endphp
                            <ol>
                                @foreach($termsLines as $line)
                                    <li>{{ preg_replace('/^\d+[\.\)\-]\s*/', '', $line) }}</li>
                                @endforeach
                            </ol>
                        @else
                            <ol>
                                <li>Payment is due within 15 days from the date of invoice.</li>
                                <li>Items will be delivered within 5–7 business days upon quote acceptance.</li>
                            </ol>
                        @endif
                    </div>
                </div>
            </td>
            <td style="width: 40%; padding-top: 15px;">
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

    <!-- Page Number Footer -->
    <div class="page-number-footer">
        Page {PAGE_NUM} of {PAGE_COUNT} &mdash; {{ $company?->company_name ?? config('app.name') }}
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans");
            $pdf->page_text(520, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(108, 117, 125));
        }
    </script>
</body>
</html>
