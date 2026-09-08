<!DOCTYPE html>
<html>
@php
if (!function_exists('getLocalImagePath')) {
    function getLocalImagePath($url) {
        if (!$url) return '';
        if (str_starts_with($url, 'data:image')) return $url;
        
        $path = '';
        
        // Try to find 'uploads/' in URL
        $pos = strpos($url, 'uploads/');
        if ($pos !== false) {
            $path = public_path(substr($url, $pos));
        }
        // Try 'storage/' path
        if ((!$path || !file_exists($path)) && strpos($url, 'storage/') !== false) {
            $storagePos = strpos($url, 'storage/');
            $path = public_path(substr($url, $storagePos));
        }
        // Try as direct relative path from public
        if ((!$path || !file_exists($path)) && !str_starts_with($url, 'http')) {
            $path = public_path($url);
        }
        // Try as absolute path
        if ((!$path || !file_exists($path)) && file_exists($url)) {
            $path = $url;
        }

        if ($path && file_exists($path)) {
            $type = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                $data = file_get_contents($path);
                return 'data:image/' . ($type === 'svg' ? 'svg+xml' : $type) . ';base64,' . base64_encode($data);
            }
        }
        
        return $url;
    }
}

if (!function_exists('getIndianCurrencyInWords')) {
    function getIndianCurrencyInWords($number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
            6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
            11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
            19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
            50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty',
            90 => 'Ninety'
        );
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? '' : '';
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : '';
                $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred
                    : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
            } else $str[] = '';
        }
        $Rupees = implode('', array_reverse($str));
        $paise = ($decimal > 0) ? " and " . (isset($words[$decimal]) ? $words[$decimal] : ($words[floor($decimal / 10) * 10] . " " . $words[$decimal % 10])) . ' Paise' : '';
        return ($Rupees ? trim($Rupees) . ' Rupees' : 'Zero Rupees') . $paise . ' Only';
    }
}

// Logo from company settings with fallback
$logoPath = $company?->logo ?: 'uploads/company/logo_header.png';
$logoImg = getLocalImagePath($logoPath);
if (!$logoImg && file_exists(public_path('uploads/company/logo_header.png'))) {
    $logoImg = getLocalImagePath('uploads/company/logo_header.png');
}

$wmPath = 'uploads/company/logo_watermark.png';
$watermarkImg = file_exists(public_path($wmPath)) ? getLocalImagePath($wmPath) : $logoImg;

$jgLogoPath = $company?->jg_logo ?: '';
$jgLogoImg = $jgLogoPath ? getLocalImagePath($jgLogoPath) : '';

$showMrp = isset($show_mrp) ? (bool)$show_mrp : (isset($quotation->show_mrp) ? (bool)$quotation->show_mrp : true);
$itemCount = count($quotation->items ?? []);
$isCompact = $itemCount > 6;
@endphp
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->quotation_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14px 20px 20px 20px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: {{ $isCompact ? '8px' : '9px' }};
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.25;
            background-color: #ffffff;
        }
        
        h1, h2, h3, h4, h5, h6, p { margin: 0; padding: 0; }

        /* Top Accent Bar */
        .top-accent-bar {
            width: 100%;
            height: 3.5px;
            background-color: #0284c7;
            margin-bottom: 8px;
            border-radius: 2px;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 28%;
            left: 25%;
            width: 50%;
            text-align: center;
            opacity: 0.04;
            z-index: -1000;
        }
        .watermark img {
            max-width: 280px;
            max-height: 280px;
        }

        /* Layout Tables */
        .table-layout {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }
        .table-layout td {
            vertical-align: top;
            padding: 0;
        }

        /* Header Section */
        .header-section {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .company-logo-img {
            max-height: {{ $isCompact ? '38px' : '45px' }};
            max-width: 140px;
            margin-bottom: 2px;
        }
        .company-name {
            font-size: {{ $isCompact ? '12px' : '13.5px' }};
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            margin-bottom: 1.5px;
        }
        .company-meta {
            font-size: {{ $isCompact ? '7.5px' : '8px' }};
            color: #475569;
            line-height: 1.35;
        }
        .company-meta strong {
            color: #0f172a;
        }

        /* Header Right: Quotation Details */
        .quote-header-right {
            text-align: right;
        }
        .quote-doc-title {
            font-size: {{ $isCompact ? '16px' : '18px' }};
            font-weight: 800;
            color: #0284c7;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .quote-badge {
            display: inline-block;
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #0369a1;
            font-weight: bold;
            font-size: 8.5px;
            padding: 1px 8px;
            border-radius: 10px;
            margin-bottom: 3px;
        }
        .quote-meta-mini {
            width: auto;
            margin-left: auto;
            border-collapse: collapse;
        }
        .quote-meta-mini td {
            padding: 1px 0 1px 6px;
            font-size: {{ $isCompact ? '7.5px' : '8px' }};
        }
        .quote-meta-label {
            color: #64748b;
            text-align: right;
            font-weight: 500;
        }
        .quote-meta-value {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }

        /* Customer Box / Info Card */
        .info-cards-table {
            margin-bottom: 6px;
        }
        .client-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            border-radius: 3px;
            padding: 4px 8px;
        }
        .card-label {
            font-size: 7px;
            font-weight: 700;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 2px;
        }
        .client-name {
            font-size: {{ $isCompact ? '10px' : '11px' }};
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 1px;
        }
        .client-info-text {
            font-size: {{ $isCompact ? '7.5px' : '8px' }};
            color: #475569;
            line-height: 1.3;
        }
        .gst-pill {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            font-weight: 700;
            font-size: 7.5px;
            padding: 0.5px 5px;
            border-radius: 3px;
            margin-top: 1px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .items-table thead {
            display: table-header-group;
        }
        .items-table tr {
            page-break-inside: avoid;
        }
        .items-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: {{ $isCompact ? '7.5px' : '8px' }};
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: {{ $isCompact ? '3px 5px' : '5px 6px' }};
            border-top: 1px solid #0f172a;
            border-bottom: 1px solid #0f172a;
        }
        .items-table th.left { text-align: left; }
        .items-table th.center { text-align: center; }
        .items-table th.right { text-align: right; }
        
        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #fbfcfe;
        }
        .items-table td {
            padding: {{ $isCompact ? '2px 5px' : '4px 6px' }};
            vertical-align: middle;
            color: #334155;
            font-size: {{ $isCompact ? '7.8px' : '8.5px' }};
        }
        .items-table td.left { text-align: left; }
        .items-table td.center { text-align: center; }
        .items-table td.right { text-align: right; }
        
        .item-thumb {
            width: {{ $isCompact ? '24px' : '30px' }};
            height: {{ $isCompact ? '24px' : '30px' }};
            border-radius: 2px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            object-fit: cover;
        }
        .item-thumb-placeholder {
            width: {{ $isCompact ? '24px' : '30px' }};
            height: {{ $isCompact ? '24px' : '30px' }};
            border-radius: 2px;
            border: 1px solid #e2e8f0;
            background-color: #f1f5f9;
            text-align: center;
            line-height: {{ $isCompact ? '24px' : '30px' }};
            color: #94a3b8;
            font-size: 10px;
        }
        
        .item-title {
            font-size: {{ $isCompact ? '8px' : '9px' }};
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .item-sku {
            display: inline-block;
            font-size: 7px;
            color: #0369a1;
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            font-weight: 700;
            padding: 0px 3px;
            border-radius: 2px;
            margin-left: 2px;
        }
        .item-desc {
            font-size: 7px;
            color: #64748b;
            margin-top: 1px;
            line-height: 1.2;
        }
        .item-total-col {
            font-weight: 700;
            color: #0f172a;
        }

        /* Summary & Calculations Section */
        .summary-wrapper {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-bottom: 6px;
        }
        .summary-wrapper td {
            vertical-align: top;
            padding: 0;
        }
        
        .amount-words-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 4px 8px;
            margin-right: 10px;
        }
        .amount-words-title {
            font-size: 7px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1.5px;
        }
        .amount-words-val {
            font-size: {{ $isCompact ? '7.5px' : '8px' }};
            font-weight: 700;
            color: #0f172a;
            font-style: italic;
            line-height: 1.25;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }
        .totals-table td {
            padding: {{ $isCompact ? '1.5px 6px' : '3px 8px' }};
            font-size: {{ $isCompact ? '7.5px' : '8px' }};
            border-bottom: 1px solid #f1f5f9;
        }
        .totals-table .t-label {
            color: #64748b;
            text-align: left;
        }
        .totals-table .t-val {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .totals-table .discount-row .t-val {
            color: #dc2626;
            font-weight: 700;
        }
        .totals-table .grand-row td {
            font-size: {{ $isCompact ? '9.5px' : '10.5px' }};
            font-weight: 800;
            background-color: #0f172a;
            color: #ffffff;
            border: none;
            padding: {{ $isCompact ? '3.5px 6px' : '5px 8px' }};
        }
        .totals-table .grand-row .t-label {
            color: #ffffff;
            font-weight: 800;
        }
        .totals-table .grand-row .t-val {
            color: #ffffff;
            font-weight: 800;
        }

        /* Terms and Signatory Section */
        .bottom-section {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            margin-top: 4px;
        }
        .bottom-section td {
            vertical-align: top;
            padding: 0;
        }

        .terms-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 3px;
            padding: 4px 6px;
            margin-right: 10px;
        }
        .terms-heading {
            font-size: 7px;
            font-weight: 700;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
            padding-bottom: 1.5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .terms-body {
            font-size: {{ $isCompact ? '6.8px' : '7.5px' }};
            color: #475569;
            line-height: 1.3;
        }
        .terms-body ol {
            margin: 0;
            padding-left: 10px;
        }
        .terms-body ol li {
            margin-bottom: 1px;
        }

        .signatory-box {
            text-align: right;
            padding-top: 2px;
        }
        .signature-img {
            max-height: {{ $isCompact ? '26px' : '32px' }};
            margin-bottom: 1px;
        }
        .sign-line {
            border-top: 1px solid #0f172a;
            width: 110px;
            display: inline-block;
            margin-top: 2px;
        }
        .sign-label {
            font-size: 7px;
            color: #0f172a;
            margin-top: 2px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.3px;
        }
        .sign-company {
            font-size: 6.5px;
            color: #64748b;
            margin-top: 0.5px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Top Accent Bar -->
    <div class="top-accent-bar"></div>

    <!-- Background Watermark Logo -->
    @if($watermarkImg)
    <div class="watermark">
        <img src="{{ $watermarkImg }}" alt="Watermark">
    </div>
    @endif

    <!-- Header Section -->
    <table class="table-layout header-section">
        <tr>
            <!-- Company Info (Left) -->
            <td style="width: 56%;">
                @if($logoImg)
                    <div style="margin-bottom: 2px;">
                        <img src="{{ $logoImg }}" alt="Logo" class="company-logo-img">
                    </div>
                @endif
                <div class="company-name">{{ $company?->company_name ?? config('app.name') }}</div>
                <div class="company-meta">
                    @if($company?->address)
                        {{ $company->address }}{{ $company?->city ? ', '.$company->city : '' }}{{ $company?->state ? ', '.$company->state : '' }}{{ $company?->zip_code ? ' - '.$company->zip_code : '' }}<br>
                    @endif
                    @if($company?->email)
                        <strong>Email:</strong> {{ $company->email }}
                    @endif
                    @if($company?->phone)
                        {{ $company?->email ? ' | ' : '' }}<strong>Phone:</strong> {{ $company->phone }}
                    @endif
                    @if($company?->gst_number)
                        <br><strong>GSTIN:</strong> {{ $company->gst_number }}
                    @endif
                </div>
            </td>

            <!-- Quotation Info (Right) -->
            <td style="width: 44%;" class="quote-header-right">
                @if($jgLogoImg)
                    <div style="margin-bottom: 3px;">
                        <img src="{{ $jgLogoImg }}" alt="Logo" style="max-height: {{ $isCompact ? '28px' : '34px' }}; max-width: 120px; vertical-align: middle;">
                    </div>
                @endif
                @if(!empty($company?->slogan))
                    <div style="font-size: 7.5px; color: #0284c7; font-weight: 700; font-style: italic; margin-bottom: 2px;">{{ $company->slogan }}</div>
                @endif
                
                <div class="quote-doc-title">QUOTATION</div>
                <div class="quote-badge"># {{ $quotation->quotation_number }}</div>

                <table class="quote-meta-mini">
                    <tr>
                        <td class="quote-meta-label">Date:</td>
                        <td class="quote-meta-value">{{ $quotation->created_at ? date('d M, Y', strtotime($quotation->created_at)) : 'N/A' }}</td>
                    </tr>
                    @if($quotation->valid_until)
                    <tr>
                        <td class="quote-meta-label">Valid Until:</td>
                        <td class="quote-meta-value">{{ date('d M, Y', strtotime($quotation->valid_until)) }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <!-- Client / Customer Section -->
    <table class="table-layout info-cards-table">
        <tr>
            <td style="width: 100%;">
                <div class="client-box">
                    <div class="card-label">QUOTATION FOR:</div>
                    <div class="client-name">{{ $quotation->customer->company_name ?? ($quotation->customer->contact_person ?? 'N/A') }}</div>
                    <div class="client-info-text">
                        @if($quotation->customer?->contact_person && $quotation->customer?->company_name)
                            <strong>Attn:</strong> {{ $quotation->customer->contact_person }} &nbsp;|&nbsp;
                        @endif
                        @if($quotation->customer?->phone)
                            <strong>Phone:</strong> {{ $quotation->customer->phone }} &nbsp;|&nbsp;
                        @endif
                        @if($quotation->customer?->email)
                            <strong>Email:</strong> {{ $quotation->customer->email }}
                        @endif
                        @php
                            $custAddress = $quotation->customer?->billing_address ?: ($quotation->customer?->address ?? '');
                            $cityState = array_filter([$quotation->customer?->city, $quotation->customer?->state, $quotation->customer?->zip_code]);
                        @endphp
                        @if($custAddress || count($cityState) > 0)
                            <br>{{ $custAddress }}{{ ($custAddress && count($cityState)) ? ', ' : '' }}{{ implode(', ', $cityState) }}
                        @endif
                        @if($quotation->customer?->gst_number)
                            <br><span class="gst-pill">GSTIN: {{ $quotation->customer->gst_number }}</span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Quotation Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="center" style="width: 4%;">#</th>
                <th class="left" style="width: 7%;">Image</th>
                <th class="left" style="width: {{ $showMrp ? '41%' : '51%' }};">Item & Description</th>
                <th class="center" style="width: 6%;">Qty</th>
                @if($showMrp)
                    <th class="right" style="width: 13%;">MRP (Rs.)</th>
                @endif
                <th class="right" style="width: 13%;">Rate (Rs.)</th>
                <th class="right" style="width: 16%;">Total (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $key => $item)
            <tr>
                <td class="center" style="color: #64748b; font-weight: 600;">{{ $key + 1 }}</td>
                <td class="left">
                    @if($item->item && $item->item->image)
                        <img src="{{ getLocalImagePath($item->item->image) }}" class="item-thumb" alt="Product">
                    @else
                        <div class="item-thumb-placeholder">📦</div>
                    @endif
                </td>
                <td class="left">
                    <span class="item-title">{{ $item->item->name ?? $item->item_name ?? 'N/A' }}</span>
                    @if($item->sku || ($item->item && $item->item->sku))
                        <span class="item-sku">{{ $item->sku ?: $item->item->sku }}</span>
                    @endif
                    @if($item->item && $item->item->description)
                        <div class="item-desc">{{ $item->item->description }}</div>
                    @endif
                </td>
                <td class="center" style="font-weight: 700; color: #0f172a;">{{ formatNumber($item->quantity) }}</td>
                @if($showMrp)
                    <td class="right" style="color: #64748b;">{{ formatNumber($item->mrp ?: ($item->item->mrp ?? $item->rate)) }}</td>
                @endif
                <td class="right">{{ formatNumber($item->rate) }}</td>
                <td class="right item-total-col">{{ formatNumber($item->total) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ $showMrp ? 7 : 6 }}" class="center" style="color: #64748b; padding: 10px;">No items found in this quotation.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Calculation & Summary Section -->
    <table class="summary-wrapper">
        <tr>
            <!-- Left: Amount in Words -->
            <td style="width: 54%;">
                <div class="amount-words-box">
                    <div class="amount-words-title">Amount Chargeable (in words)</div>
                    <div class="amount-words-val">{{ getIndianCurrencyInWords($quotation->grand_total) }}</div>
                </div>
            </td>

            <!-- Right: Totals Breakdown Table -->
            <td style="width: 46%;">
                <table class="totals-table">
                    <tr>
                        <td class="t-label">Subtotal</td>
                        <td class="t-val">Rs. {{ formatNumber($quotation->subtotal) }}</td>
                    </tr>
                    @if($quotation->discount_amount > 0)
                    <tr class="discount-row">
                        <td class="t-label">Discount @if($quotation->discount_type == 'percentage')({{ formatNumber($quotation->discount_value) }}%)@endif</td>
                        <td class="t-val">- Rs. {{ formatNumber($quotation->discount_amount) }}</td>
                    </tr>
                    @endif
                    
                    @if($quotation->cgst_amount > 0)
                    <tr>
                        <td class="t-label">CGST @if($quotation->cgst_percentage > 0)({{ formatNumber($quotation->cgst_percentage) }}%)@endif</td>
                        <td class="t-val">Rs. {{ formatNumber($quotation->cgst_amount) }}</td>
                    </tr>
                    @endif

                    @if($quotation->sgst_amount > 0)
                    <tr>
                        <td class="t-label">SGST @if($quotation->sgst_percentage > 0)({{ formatNumber($quotation->sgst_percentage) }}%)@endif</td>
                        <td class="t-val">Rs. {{ formatNumber($quotation->sgst_amount) }}</td>
                    </tr>
                    @endif

                    @if($quotation->igst_amount > 0)
                    <tr>
                        <td class="t-label">IGST @if($quotation->igst_percentage > 0)({{ formatNumber($quotation->igst_percentage) }}%)@endif</td>
                        <td class="t-val">Rs. {{ formatNumber($quotation->igst_amount) }}</td>
                    </tr>
                    @endif

                    @php
                        $totalTax = $quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount;
                    @endphp
                    @if($totalTax > 0 && $quotation->cgst_amount == 0 && $quotation->sgst_amount == 0 && $quotation->igst_amount == 0)
                    <tr>
                        <td class="t-label">Tax</td>
                        <td class="t-val">Rs. {{ formatNumber($totalTax) }}</td>
                    </tr>
                    @endif
                    
                    @if($quotation->round_off != 0)
                    <tr>
                        <td class="t-label">Round Off</td>
                        <td class="t-val">{{ ($quotation->round_off > 0 ? '+ ' : '') }}Rs. {{ formatNumber($quotation->round_off) }}</td>
                    </tr>
                    @endif

                    <tr class="grand-row">
                        <td class="t-label">Grand Total</td>
                        <td class="t-val">Rs. {{ formatNumber($quotation->grand_total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Terms & Authorized Signatory -->
    <table class="bottom-section">
        <tr>
            <!-- Terms and Conditions (Left) -->
            <td style="width: 58%;">
                <div class="terms-card">
                    <div class="terms-heading">TERMS & CONDITIONS</div>
                    <div class="terms-body">
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
                                <li>Quotation is valid for 30 days from date of issue.</li>
                                <li>Payment terms as mutually agreed.</li>
                                <li>Delivery schedule confirmed upon order.</li>
                            </ol>
                        @endif
                    </div>
                </div>
            </td>

            <!-- Signature (Right) -->
            <td style="width: 42%; text-align: right; vertical-align: bottom;">
                <div class="signatory-box">
                    @if($company?->signature)
                        <img src="{{ getLocalImagePath($company?->signature) }}" alt="Signature" class="signature-img">
                        <br>
                    @else
                        <div style="height: 24px;"></div>
                    @endif
                    <div class="sign-line"></div>
                    <div class="sign-label">AUTHORISED SIGNATORY</div>
                    <div class="sign-company">{{ $company?->company_name ?? 'Bhagyashree Sanitaryware' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Dynamic Dompdf Page Footer Script -->
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $size = 7;
            $color = array(0.58, 0.64, 0.72); // slate-400
            
            // Left company footer note
            $pdf->page_text(20, 824, "{{ $company?->company_name ?? 'Bhagyashree Sanitary' }} — Computer Generated Quotation", $font, $size, $color);
            
            // Right page count
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $width = $fontMetrics->getTextWidth($pageText, $font, $size);
            $pdf->page_text(595 - 20 - $width, 824, $pageText, $font, $size, $color);
        }
    </script>
</body>
</html>
