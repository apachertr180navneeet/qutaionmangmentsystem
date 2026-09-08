<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        
        /* ── Header ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border-bottom: 2px solid #6366f1;
            padding-bottom: 10px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .company-name {
            font-size: 17px;
            font-weight: bold;
            color: #4338ca;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 9px;
            color: #64748b;
            line-height: 1.5;
        }
        .report-box {
            text-align: right;
        }
        .report-main-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }
        .report-badge-subtitle {
            display: inline-block;
            background: #ede9fe;
            color: #6d28d9;
            font-weight: bold;
            font-size: 9.5px;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 3px;
        }
        .report-gen-date {
            font-size: 8.5px;
            color: #94a3b8;
        }

        /* ── KPI Summary Cards ── */
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin-bottom: 12px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }
        .kpi-card-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .kpi-card-value {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
        }
        .kpi-card-value.highlight {
            color: #4338ca;
        }

        /* ── Data Table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-top: 4px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 6px;
            border: 1px solid #1e293b;
            text-align: center;
        }
        .data-table td {
            padding: 6px 7px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }

        /* ── Status Badges ── */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-draft { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-sent { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-approved { background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-expired { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* ── Totals Footer Row ── */
        .summary-row td {
            font-weight: bold;
            background-color: #eef2ff;
            color: #1e1b4b;
            border-top: 2px solid #6366f1;
            font-size: 10px;
            padding: 8px 7px;
        }

        /* ── Document Footer ── */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-name">{{ $company->company_name ?? config('app.name', 'Quotation Management') }}</div>
                <div class="company-info">
                    @if(!empty($company->address)){{ $company->address }}@endif
                    @if(!empty($company->city)), {{ $company->city }}@endif
                    @if(!empty($company->state)) - {{ $company->state }}@endif
                    @if(!empty($company->zip_code)) ({{ $company->zip_code }})@endif
                    <br>
                    @if(!empty($company->phone)) Phone: {{ $company->phone }} @endif
                    @if(!empty($company->email)) | Email: {{ $company->email }} @endif
                    @if(!empty($company->gst_number)) | GSTIN: {{ $company->gst_number }} @endif
                </div>
            </td>
            <td style="width: 45%;" class="report-box">
                <div class="report-main-title">{{ $title }}</div>
                @if(!empty($subtitle))
                    <div class="report-badge-subtitle">{{ $subtitle }}</div><br>
                @endif
                <div class="report-gen-date">Generated on: {{ date('d-m-Y h:i A') }}</div>
            </td>
        </tr>
    </table>

    @if(($report_type ?? '') === 'item_wise' || (isset($quotationItems) && $quotationItems->count() > 0))
        <!-- KPI Summary for Item-Wise -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-card" style="width: 33%;">
                    <div class="kpi-card-label">Total Quotation Records</div>
                    <div class="kpi-card-value">{{ $quotationItems->count() }}</div>
                </td>
                <td class="kpi-card" style="width: 33%;">
                    <div class="kpi-card-label">Total Quantity Sold</div>
                    <div class="kpi-card-value">{{ formatNumber($totalQuantity ?? $quotationItems->sum('quantity')) }}</div>
                </td>
                <td class="kpi-card" style="width: 34%;">
                    <div class="kpi-card-label">Total Amount</div>
                    <div class="kpi-card-value highlight">Rs. {{ formatNumber($totalAmount ?? $quotationItems->sum('total')) }}</div>
                </td>
            </tr>
        </table>

        <!-- Item-Wise Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 17%;">Quotation No</th>
                    <th style="width: 28%; text-align: left;">Customer</th>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 12%; text-align: right;">Quantity</th>
                    <th style="width: 12%; text-align: right;">Rate</th>
                    <th style="width: 12%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotationItems as $key => $qi)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center" style="font-weight: bold; color: #4338ca;">{{ $qi->quotation->quotation_number ?? 'N/A' }}</td>
                    <td class="text-left">{{ $qi->quotation->customer->company_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $qi->quotation->date ? date('d-m-Y', strtotime($qi->quotation->date)) : ($qi->quotation->created_at ? date('d-m-Y', strtotime($qi->quotation->created_at)) : 'N/A') }}</td>
                    <td class="text-right">{{ formatNumber($qi->quantity) }}</td>
                    <td class="text-right">{{ formatNumber($qi->rate) }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ formatNumber($qi->total) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 16px; color: #94a3b8;">No records found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>
            @if(isset($quotationItems) && $quotationItems->count() > 0)
            <tfoot>
                <tr class="summary-row">
                    <td colspan="4" class="text-right">Grand Total:</td>
                    <td class="text-right">{{ formatNumber($totalQuantity ?? $quotationItems->sum('quantity')) }}</td>
                    <td></td>
                    <td class="text-right">Rs. {{ formatNumber($totalAmount ?? $quotationItems->sum('total')) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    @else
        <!-- KPI Summary for Quotations -->
        <table class="kpi-table">
            <tr>
                <td class="kpi-card" style="width: 50%;">
                    <div class="kpi-card-label">Total Quotations</div>
                    <div class="kpi-card-value">{{ $quotations->count() }}</div>
                </td>
                <td class="kpi-card" style="width: 50%;">
                    <div class="kpi-card-label">Total Quotation Value</div>
                    <div class="kpi-card-value highlight">Rs. {{ formatNumber($totalGrandTotal ?? $quotations->sum('grand_total')) }}</div>
                </td>
            </tr>
        </table>

        <!-- Quotations Data Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 20%;">Quotation No</th>
                    <th style="width: 32%; text-align: left;">Customer</th>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 15%; text-align: right;">Grand Total</th>
                    <th style="width: 13%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotations as $key => $quotation)
                @php
                    $badgeClass = 'badge-draft';
                    if ($quotation->status === 'sent') $badgeClass = 'badge-sent';
                    elseif ($quotation->status === 'approved') $badgeClass = 'badge-approved';
                    elseif ($quotation->status === 'expired') $badgeClass = 'badge-expired';
                    elseif ($quotation->status === 'rejected') $badgeClass = 'badge-rejected';
                @endphp
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td class="text-center" style="font-weight: bold; color: #4338ca;">{{ $quotation->quotation_number }}</td>
                    <td class="text-left">{{ $quotation->customer->company_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $quotation->date ? date('d-m-Y', strtotime($quotation->date)) : ($quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A') }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ formatNumber($quotation->grand_total) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($quotation->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 16px; color: #94a3b8;">No quotations found for the selected criteria.</td>
                </tr>
                @endforelse
            </tbody>
            @if(isset($quotations) && $quotations->count() > 0)
            <tfoot>
                <tr class="summary-row">
                    <td colspan="4" class="text-right">Grand Total ({{ $quotations->count() }} quotations):</td>
                    <td class="text-right">Rs. {{ formatNumber($totalGrandTotal ?? $quotations->sum('grand_total')) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    @endif

    <div class="footer">
        {{ $company->company_name ?? config('app.name') }} &mdash; {{ $title }} &mdash; Printed on {{ date('d-m-Y H:i') }}
    </div>
</body>
</html>
