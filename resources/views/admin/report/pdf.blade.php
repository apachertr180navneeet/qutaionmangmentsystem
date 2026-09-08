<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #2b2f32;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #8E2DE2;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #4A00E0;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 9px;
            color: #6c757d;
            line-height: 1.4;
        }
        .report-title-box {
            text-align: right;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .report-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #8E2DE2;
            margin: 0 0 3px 0;
        }
        .report-meta {
            font-size: 9px;
            color: #888;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 5px;
        }
        table.data-table th {
            background-color: #4A00E0;
            color: #ffffff;
            padding: 8px 6px;
            font-size: 9.5px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #3b00b3;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 7px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-end, .text-right { text-align: right; }
        .text-start, .text-left { text-align: left; }
        
        .badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-draft { background-color: #e2e8f0; color: #475569; }
        .badge-sent { background-color: #dbeafe; color: #1d4ed8; }
        .badge-approved { background-color: #dcfce7; color: #15803d; }
        .badge-expired { background-color: #fef3c7; color: #b45309; }
        .badge-rejected { background-color: #fee2e2; color: #b91c1c; }

        .summary-row td {
            font-weight: bold;
            background-color: #f1f5f9;
            border-top: 2px solid #cbd5e1;
            font-size: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <div class="company-title">{{ $company->company_name ?? config('app.name', 'Quotation Management') }}</div>
                <div class="company-info">
                    @if(!empty($company->address)){{ $company->address }}@endif
                    @if(!empty($company->city)), {{ $company->city }}@endif
                    @if(!empty($company->state)) - {{ $company->state }}@endif
                    @if(!empty($company->zip_code)) ({{ $company->zip_code }})@endif
                    <br>
                    @if(!empty($company->phone)) Phone: {{ $company->phone }} @endif
                    @if(!empty($company->email)) | Email: {{ $company->email }} @endif
                    @if(!empty($company->gst_number)) | GST: {{ $company->gst_number }} @endif
                </div>
            </td>
            <td style="width: 45%;" class="report-title-box">
                <h1 class="report-title">{{ $title }}</h1>
                @if(!empty($subtitle))
                    <div class="report-subtitle">{{ $subtitle }}</div>
                @endif
                <div class="report-meta">Generated on {{ date('d-m-Y h:i A') }}</div>
            </td>
        </tr>
    </table>

    @if(($report_type ?? '') === 'item_wise' || (isset($quotationItems) && $quotationItems->count() > 0))
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 18%;">Quotation No</th>
                <th style="width: 28%; text-align: left;">Customer</th>
                <th style="width: 14%;">Date</th>
                <th style="width: 11%; text-align: right;">Quantity</th>
                <th style="width: 11%; text-align: right;">Rate</th>
                <th style="width: 13%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotationItems as $key => $qi)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td class="text-center" style="font-weight: 600;">{{ $qi->quotation->quotation_number ?? 'N/A' }}</td>
                <td class="text-left">{{ $qi->quotation->customer->company_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $qi->quotation->date ? date('d-m-Y', strtotime($qi->quotation->date)) : ($qi->quotation->created_at ? date('d-m-Y', strtotime($qi->quotation->created_at)) : 'N/A') }}</td>
                <td class="text-right">{{ formatNumber($qi->quantity) }}</td>
                <td class="text-right">{{ formatNumber($qi->rate) }}</td>
                <td class="text-right" style="font-weight: 600;">{{ formatNumber($qi->total) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 18px; color: #64748b;">No records found for the selected criteria.</td>
            </tr>
            @endforelse
        </tbody>
        @if(isset($quotationItems) && $quotationItems->count() > 0)
        <tfoot>
            <tr class="summary-row">
                <td colspan="4" class="text-right">Total Summary ({{ $quotationItems->count() }} records):</td>
                <td class="text-right">{{ formatNumber($totalQuantity ?? $quotationItems->sum('quantity')) }}</td>
                <td></td>
                <td class="text-right">{{ formatNumber($totalAmount ?? $quotationItems->sum('total')) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
    @else
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
                <td class="text-center" style="font-weight: 600;">{{ $quotation->quotation_number }}</td>
                <td class="text-left">{{ $quotation->customer->company_name ?? 'N/A' }}</td>
                <td class="text-center">{{ $quotation->date ? date('d-m-Y', strtotime($quotation->date)) : ($quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A') }}</td>
                <td class="text-right" style="font-weight: 600;">{{ formatNumber($quotation->grand_total) }}</td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ ucfirst($quotation->status) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 18px; color: #64748b;">No quotations found for the selected criteria.</td>
            </tr>
            @endforelse
        </tbody>
        @if(isset($quotations) && $quotations->count() > 0)
        <tfoot>
            <tr class="summary-row">
                <td colspan="4" class="text-right">Total Summary ({{ $quotations->count() }} quotations):</td>
                <td class="text-right">{{ formatNumber($totalGrandTotal ?? $quotations->sum('grand_total')) }}</td>
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
