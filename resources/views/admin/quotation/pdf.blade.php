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
        @page { size: A4; margin: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #374151; margin: 0; padding: 40px 40px 60px 40px; line-height: 1.5; }
        h1, h2, h3, h4, p { margin: 0; padding: 0; }
        
        .top-section { background-color: #f4f7fa; padding: 40px; margin: -40px -40px 30px -40px; }
        .top-table { width: 100%; }
        .doc-title { font-size: 34px; font-weight: bold; color: #4a88f7; letter-spacing: 1px; margin-top: 15px; }
        .company-by { font-size: 10px; color: #9ca3af; margin-bottom: 2px; }
        .company-name { font-size: 14px; font-weight: bold; color: #111827; margin-bottom: 4px; }
        .company-detail { font-size: 10px; color: #6b7280; line-height: 1.4; }
        .company-logo-img { max-height: 60px; max-width: 150px; }
        
        .middle-section { width: 100%; margin-bottom: 30px; }
        .section-label { font-size: 11px; color: #9ca3af; margin-bottom: 12px; border-left: 3px solid #34d399; padding-left: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .customer-name { font-size: 14px; font-weight: bold; color: #111827; margin-bottom: 4px; }
        .customer-detail { font-size: 11px; color: #4b5563; line-height: 1.5; }
        .customer-tax { font-size: 10px; margin-top: 8px; color: #4b5563; }
        .customer-tax strong { font-weight: 600; color: #111827; }
        
        .q-details-table { width: 100%; font-size: 11px; }
        .q-details-table td { padding: 4px 0; }
        .q-label { font-weight: bold; color: #111827; width: 45%; }
        .q-value { color: #4b5563; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; border-radius: 6px; overflow: hidden; }
        .items-table thead { background-color: #4a88f7; color: #ffffff; }
        .items-table th { padding: 12px 10px; text-align: center; font-weight: normal; font-size: 11px; }
        .items-table th.left { text-align: left; }
        .items-table th.right { text-align: right; }
        .items-table td { padding: 8px 10px; text-align: center; vertical-align: middle; color: #374151; font-size: 11px; }
        .items-table td.left { text-align: left; }
        .items-table td.right { text-align: right; }
        .items-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        
        .totals-section { width: 100%; margin-bottom: 40px; page-break-inside: avoid; }
        .totals-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .totals-table td { padding: 10px 0; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
        .totals-table .t-label { color: #374151; }
        .totals-table .t-value { text-align: right; font-weight: 600; color: #111827; }
        .totals-table .total-row td { border-bottom: none; border-top: 1px solid #e5e7eb; padding-top: 15px; font-size: 18px; }
        .totals-table .total-row .t-label { color: #4b5563; font-weight: normal; }
        .totals-table .total-row .t-value { color: #4a88f7; font-weight: bold; }
        
        .gst-table { width: 80%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
        .gst-table th, .gst-table td { padding: 6px 0; text-align: left; border-bottom: 1px solid #f3f4f6; }
        .gst-table th { color: #9ca3af; font-weight: normal; }
        .gst-table td { color: #4b5563; }
        
        .bottom-section { background-color: #f4f7fa; padding: 40px; margin: 30px -40px -40px -40px; page-break-inside: avoid; }
        .terms-title { font-size: 12px; font-weight: bold; color: #111827; margin-bottom: 10px; }
        .terms-content { font-size: 10px; color: #4b5563; line-height: 1.6; white-space: pre-wrap; margin-bottom: 20px; }
        
        .footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="top-section">
        <table class="top-table">
            <tr>
                <td style="width: 45%; vertical-align: middle;">
                    <h1 class="doc-title">Quotation</h1>
                </td>
                <td style="width: 30%; vertical-align: top; padding-right: 15px;">
                    <p class="company-by">Quotation by</p>
                    <p class="company-name">{{ $company?->company_name ?? config('app.name') }}</p>
                    <p class="company-detail">{{ $company?->address }}{{ $company?->city ? ', '.$company?->city : '' }}</p>
                    <p class="company-detail">{{ $company?->state ? $company?->state.', ' : '' }}{{ $company?->zip_code ? ' - '.$company?->zip_code : '' }}</p>
                    @if($company?->phone) <p class="company-detail">Ph: {{ $company?->phone }}</p> @endif
                    @if($company?->email) <p class="company-detail">Email: {{ $company?->email }}</p> @endif
                    @if($company?->gst_number) <p class="company-detail">GST: {{ $company?->gst_number }}</p> @endif
                </td>
                <td style="width: 25%; vertical-align: top; text-align: right;">
                    @if($company?->logo)
                        <img src="{{ getLocalImagePath($company?->logo) }}" alt="Logo" class="company-logo-img">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="middle-section">
        <table width="100%">
            <tr>
                <td width="55%" style="vertical-align: top; padding-right: 20px;">
                    <p class="section-label">Billed to</p>
                    <p class="customer-name">{{ $quotation->customer->company_name ?? 'N/A' }}</p>
                    <p class="customer-detail">{{ $quotation->customer->address ?? '' }}</p>
                    @if($quotation->customer->gst_number)
                        <p class="customer-tax"><strong>GST</strong> {{ $quotation->customer->gst_number }}</p>
                    @endif
                    @if($quotation->customer->contact_person)
                        <p class="customer-tax"><strong>Contact</strong> {{ $quotation->customer->contact_person }}</p>
                    @endif
                </td>
                <td width="45%" style="vertical-align: top;">
                    <p class="section-label">Quotation Details</p>
                    <table class="q-details-table">
                        <tr><td class="q-label">Quotation #</td><td class="q-value">{{ $quotation->quotation_number }}</td></tr>
                        <tr><td class="q-label">Quotation Date</td><td class="q-value">{{ $quotation->created_at ? date('M d, Y', strtotime($quotation->created_at)) : 'N/A' }}</td></tr>
                        <tr><td class="q-label">Due Date</td><td class="q-value">{{ $quotation->valid_until ? date('M d, Y', strtotime($quotation->valid_until)) : 'N/A' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="items-section">
        <table class="items-table">
            <thead>
                <tr>
                    <th class="left" style="width:40%;">Item #/Item description</th>
                    <th style="width:10%;">Img</th>
                    <th style="width:15%;">HSN</th>
                    <th style="width:10%;">Qty.</th>
                    <th style="width:10%;">Rate</th>
                    <th class="right" style="width:15%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotation->items as $key => $item)
                <tr>
                    <td class="left">
                        <strong>{{ $key + 1 }}. {{ $item->item->name ?? $item->item_name ?? 'N/A' }}</strong>
                    </td>
                    <td>
                        @if($item->item && $item->item->image)
                            <img src="{{ getLocalImagePath($item->item->image) }}" style="max-width:30px; max-height:30px; border-radius:4px;" alt="img">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->item->hsn_code ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td class="right">{{ number_format($item->total, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:20px;">No items found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="totals-section">
        <table width="100%">
            <tr>
                <td width="55%" style="vertical-align: top;">
                    @php
                        $cgstTotal = $quotation->items->sum('cgst_amount');
                        $sgstTotal = $quotation->items->sum('sgst_amount');
                        $igstTotal = $quotation->igst_amount ?? 0;
                    @endphp
                    @if($cgstTotal > 0 || $sgstTotal > 0 || $igstTotal > 0)
                        <table class="gst-table">
                            <thead>
                                <tr><th>Tax Type</th><th>Amount</th></tr>
                            </thead>
                            <tbody>
                                @if($cgstTotal > 0)
                                <tr><td>CGST</td><td>{{ number_format($cgstTotal, 2) }}</td></tr>
                                @endif
                                @if($sgstTotal > 0)
                                <tr><td>SGST</td><td>{{ number_format($sgstTotal, 2) }}</td></tr>
                                @endif
                                @if($igstTotal > 0)
                                <tr><td>IGST</td><td>{{ number_format($igstTotal, 2) }}</td></tr>
                                @endif
                            </tbody>
                        </table>
                    @endif
                </td>
                <td width="45%" style="vertical-align: top;">
                    <table class="totals-table">
                        <tr>
                            <td class="t-label">Sub Total</td>
                            <td class="t-value">{{ number_format($quotation->subtotal, 2) }}</td>
                        </tr>
                        @if($quotation->discount_amount > 0)
                        <tr>
                            <td class="t-label">Discount({{ $quotation->discount_type == 'percentage' ? $quotation->discount_value.'%' : 'Fixed' }})</td>
                            <td class="t-value">- {{ number_format($quotation->discount_amount, 2) }}</td>
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
                            <td class="t-label">Total</td>
                            <td class="t-value">{{ number_format($quotation->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="bottom-section">
        <p class="terms-title">Terms and Conditions</p>
        <div class="terms-content">{{ $quotation->terms_conditions ?: ($company?->terms_conditions ?? 'N/A') }}</div>
        
        @if($company?->signature)
            <div style="margin-top: 20px;">
                <img src="{{ getLocalImagePath($company?->signature) }}" style="max-height: 50px;" alt="Signature">
                <p style="font-size: 10px; color: #6b7280; margin-top: 5px;">Authorised Signatory</p>
            </div>
        @endif
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
