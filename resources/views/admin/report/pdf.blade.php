<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page { size: A4 landscape; margin: 15mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .report-header { text-align: center; margin-bottom: 25px; }
        .report-header h1 { font-size: 22px; color: #2c3e50; margin: 0 0 5px 0; text-transform: uppercase; letter-spacing: 2px; }
        .report-header p { font-size: 12px; color: #777; margin: 0; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #2c3e50; color: #fff; padding: 8px 6px; text-align: center; font-weight: 600; }
        td { padding: 6px; border: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { display: inline-block; padding: 2px 8px; font-size: 9px; border-radius: 3px; }
        .badge-draft { background: #95a5a6; color: #fff; }
        .badge-sent { background: #3498db; color: #fff; }
        .badge-approved { background: #27ae60; color: #fff; }
        .badge-expired { background: #f39c12; color: #fff; }
        .badge-rejected { background: #e74c3c; color: #fff; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 5px; }
        .summary-row td { font-weight: bold; background: #ecf0f1; }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>{{ $title }}</h1>
        <p>Generated on {{ date('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:20%;">Quotation No</th>
                <th style="width:25%;">Customer</th>
                <th style="width:15%;">Date</th>
                <th style="width:18%;">Grand Total</th>
                <th style="width:17%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotations as $key => $quotation)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $quotation->quotation_number }}</td>
                <td>{{ $quotation->customer->company_name ?? 'N/A' }}</td>
                <td>{{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</td>
                <td>{{ number_format($quotation->grand_total, 2) }}</td>
                <td><span class="badge badge-{{ $quotation->status }}">{{ ucfirst($quotation->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;">No quotations found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="summary-row">
                <td colspan="4" style="text-align:right;">Total Quotations: {{ $quotations->count() }}</td>
                <td>{{ number_format($quotations->sum('grand_total'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Page {PAGE_NUM} of {PAGE_COUNT} &mdash; {{ $title }}
    </div>
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans");
            $pdf->page_text(515, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        }
    </script>
</body>
</html>
