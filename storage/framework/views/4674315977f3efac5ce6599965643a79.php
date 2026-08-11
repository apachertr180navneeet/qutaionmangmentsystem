<!DOCTYPE html>
<html>
<?php
if (!function_exists('getLocalImagePath')) {
    function getLocalImagePath($url) {
        if (!$url) return '';
        if (str_starts_with($url, 'data:image')) return $url;
        
        $path = '';
        
        // Try to find 'uploads/' in URL (handles full URLs like http://localhost/uploads/...)
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

// Logo from company settings with reliable fallback
$logoPath = $company?->logo ?: 'uploads/company/logo.png';
$logoImg = getLocalImagePath($logoPath);
if (!$logoImg && file_exists(public_path('uploads/company/logo.png'))) {
    $logoImg = getLocalImagePath('uploads/company/logo.png');
}

$wmPath = 'uploads/company/logo_watermark.png';
$watermarkImg = file_exists(public_path($wmPath)) ? getLocalImagePath($wmPath) : $logoImg;
?>
<head>
    <meta charset="utf-8">
    <title>Quotation #<?php echo e($quotation->quotation_number); ?></title>
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
            padding: 32px 36px;
            line-height: 1.45;
            background-color: #ffffff;
        }
        
        h1, h2, h3, h4, h5, h6, p { margin: 0; padding: 0; }

        /* Top Ocean Accent Line */
        .top-accent-bar {
            height: 5px;
            background-color: #0e7490;
            margin: -32px -36px 20px -36px;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 25%;
            left: 20%;
            width: 60%;
            text-align: center;
            opacity: 0.08;
            z-index: -1000;
        }
        .watermark img {
            max-width: 320px;
            max-height: 320px;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-logo-img {
            max-height: 85px;
            max-width: 190px;
            margin-bottom: 4px;
        }
        .company-logo-text {
            font-size: 20px;
            font-weight: bold;
            color: #0e7490;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .company-name-subtitle {
            font-size: 11px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .company-details {
            font-size: 9px;
            color: #64748b;
            line-height: 1.45;
        }
        .detail-label {
            color: #475569;
            font-weight: 600;
        }

        /* Quotation Meta Box */
        .quote-title-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            text-align: right;
            display: inline-block;
            min-width: 200px;
        }
        .quote-title {
            font-size: 16px;
            font-weight: bold;
            color: #0e7490;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .quote-number-badge {
            display: inline-block;
            background-color: #ecfeff;
            border: 1px solid #cff4fc;
            color: #0891b2;
            font-weight: bold;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            margin-bottom: 6px;
        }
        .quote-meta-table {
            width: 100%;
            margin-top: 4px;
            border-collapse: collapse;
        }
        .quote-meta-table td {
            font-size: 9px;
            padding: 1.5px 0;
        }
        .meta-label {
            color: #64748b;
            text-align: left;
        }
        .meta-val {
            color: #0f172a;
            font-weight: 600;
            text-align: right;
        }

        /* Status Pills */
        .status-pill {
            font-size: 8px;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .status-draft { background: #f1f5f9; color: #475569; }
        .status-sent { background: #e0f2fe; color: #0284c7; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-expired { background: #fef3c7; color: #92400e; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        /* Client Info Section */
        .client-card {
            background-color: #f8fafc;
            border-left: 4px solid #0e7490;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 20px;
        }
        .client-subtitle {
            font-size: 8.5px;
            font-weight: bold;
            color: #0e7490;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 4px;
        }
        .client-name {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .client-details {
            font-size: 9px;
            color: #475569;
            line-height: 1.45;
        }
        .gstin-badge {
            display: inline-block;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            font-weight: bold;
            color: #334155;
            padding: 1px 6px;
            border-radius: 3px;
            margin-top: 3px;
            font-size: 8.5px;
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
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border-bottom: 2px solid #0e7490;
        }
        .items-table th.left { text-align: left; }
        .items-table th.center { text-align: center; }
        .items-table th.right { text-align: right; }
        
        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .items-table td {
            padding: 8px 10px;
            vertical-align: middle;
            color: #334155;
            font-size: 9.5px;
        }
        .items-table td.left { text-align: left; }
        .items-table td.center { text-align: center; }
        .items-table td.right { text-align: right; }
        
        .item-img-preview {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            object-fit: cover;
        }
        .item-img-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            background-color: #f1f5f9;
            text-align: center;
            line-height: 40px;
            color: #94a3b8;
            font-size: 14px;
        }
        
        .item-name {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
        }
        .sku-badge {
            display: inline-block;
            font-size: 8px;
            color: #0284c7;
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            font-weight: bold;
            padding: 0px 5px;
            border-radius: 3px;
            margin-left: 4px;
        }
        .sdp-info {
            font-size: 8px;
            color: #0e7490;
            font-weight: bold;
            margin-top: 1px;
        }
        .item-description {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .item-total-val {
            font-weight: bold;
            color: #0f172a;
        }
        
        /* Summary Section */
        .summary-table-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .summary-table-container td {
            vertical-align: top;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 9.5px;
            border-bottom: 1px solid #f1f5f9;
        }
        .totals-table .label {
            color: #64748b;
            text-align: left;
        }
        .totals-table .value {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }
        .totals-table .discount-text {
            color: #dc2626;
        }
        .totals-table .grand-total-row td {
            font-size: 13px;
            font-weight: bold;
            background-color: #0e7490;
            color: #ffffff;
            border-top: none;
            padding: 9px 12px;
        }
        .totals-table .grand-total-row .label {
            color: #ffffff;
        }
        .totals-table .grand-total-row .value {
            color: #ffffff;
        }

        /* Terms and Signature Footer */
        .footer-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .footer-section td {
            vertical-align: top;
        }

        .terms-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .terms-title {
            font-size: 9px;
            font-weight: bold;
            color: #0e7490;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }
        .terms-content {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.6;
        }
        .terms-content ol {
            margin: 0;
            padding-left: 14px;
        }
        .terms-content ol li {
            margin-bottom: 2px;
        }
        
        .signature-container {
            text-align: right;
        }
        .signature-img {
            max-height: 45px;
            margin-bottom: 4px;
        }
        .signature-line {
            border-top: 1.5px solid #0f172a;
            width: 140px;
            display: inline-block;
            margin-top: 4px;
        }
        .signature-label {
            font-size: 8.5px;
            color: #0f172a;
            margin-top: 3px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .signature-company {
            font-size: 8px;
            color: #64748b;
            margin-top: 1px;
        }

        .page-number-footer {
            position: fixed;
            bottom: 12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Top Accent Bar -->
    <div class="top-accent-bar"></div>

    <!-- Background Watermark Logo -->
    <?php if($watermarkImg): ?>
    <div class="watermark">
        <img src="<?php echo e($watermarkImg); ?>" alt="Watermark Logo">
    </div>
    <?php endif; ?>

    <!-- Header / Branding -->
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                <?php if($logoImg): ?>
                    <img src="<?php echo e($logoImg); ?>" alt="Logo" class="company-logo-img">
                <?php else: ?>
                    <div class="company-logo-text"><?php echo e($company?->company_name ?? config('app.name')); ?></div>
                    <div class="company-name-subtitle"><?php echo e($company?->company_name ?? 'BHAGYASHREE SANITARYWARE'); ?></div>
                <?php endif; ?>
                <div class="company-details">
                    <?php echo e($company?->address); ?><?php echo e($company?->city ? ', '.$company?->city : ''); ?><?php echo e($company?->state ? ', '.$company?->state : ''); ?><?php echo e($company?->zip_code ? ' - '.$company?->zip_code : ''); ?>

                    <?php if($company?->email): ?><br><span class="detail-label">Email:</span> <?php echo e($company?->email); ?><?php endif; ?>
                    <?php if($company?->phone): ?> | <span class="detail-label">Phone:</span> <?php echo e($company?->phone); ?><?php endif; ?>
                    <?php if($company?->gst_number): ?><br><span class="detail-label">GSTIN:</span> <strong><?php echo e($company?->gst_number); ?></strong><?php endif; ?>
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="quote-title-box">
                    <div class="quote-title">PRICE QUOTATION</div>
                    <div class="quote-number-badge"># <?php echo e($quotation->quotation_number); ?></div>
                    
                    <table class="quote-meta-table">
                        <tr>
                            <td class="meta-label">Date:</td>
                            <td class="meta-val"><?php echo e($quotation->created_at ? date('d M, Y', strtotime($quotation->created_at)) : 'N/A'); ?></td>
                        </tr>
                        <tr>
                            <td class="meta-label">Valid Until:</td>
                            <td class="meta-val"><?php echo e($quotation->valid_until ? date('d M, Y', strtotime($quotation->valid_until)) : 'N/A'); ?></td>
                        </tr>
                        <?php if($quotation->status): ?>
                        <tr>
                            <td class="meta-label">Status:</td>
                            <td class="meta-val">
                                <span class="status-pill status-<?php echo e($quotation->status); ?>"><?php echo e(strtoupper($quotation->status)); ?></span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Client Info Card -->
    <div class="client-card">
        <div class="client-subtitle">PREPARED FOR:</div>
        <div class="client-name"><?php echo e($quotation->customer->company_name ?? 'N/A'); ?></div>
        <div class="client-details">
            <?php if($quotation->customer?->contact_person): ?>
                <strong>Attn:</strong> <?php echo e($quotation->customer->contact_person); ?><br>
            <?php endif; ?>
            <?php if($quotation->customer?->address): ?>
                <?php echo e($quotation->customer->address); ?><br>
            <?php endif; ?>
            <?php if($quotation->customer?->gst_number): ?>
                <span class="gstin-badge">GSTIN: <?php echo e($quotation->customer->gst_number); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quotation Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th class="center" style="width: 25px;">#</th>
                <th class="left" style="width: 45px;">Image</th>
                <th class="left">Item & Description</th>
                <th class="center" style="width: 35px;">Qty</th>
                <th class="right" style="width: 70px;">MRP</th>
                <th class="right" style="width: 70px;">Rate</th>
                <th class="right" style="width: 80px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $quotation->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td class="center" style="color: #64748b; font-weight: 500;"><?php echo e($key + 1); ?></td>
                <td class="left">
                    <?php if($item->item && $item->item->image): ?>
                        <img src="<?php echo e(getLocalImagePath($item->item->image)); ?>" class="item-img-preview" alt="Product">
                    <?php else: ?>
                        <div class="item-img-placeholder">📦</div>
                    <?php endif; ?>
                </td>
                <td class="left">
                    <span class="item-name"><?php echo e($item->item->name ?? $item->item_name ?? 'N/A'); ?></span>
                    <?php if($item->sku || ($item->item && $item->item->sku)): ?>
                        <span class="sku-badge">SKU: <?php echo e($item->sku ?: $item->item->sku); ?></span>
                    <?php endif; ?>
                    <?php if($item->item && $item->item->description): ?>
                        <div class="item-description"><?php echo e($item->item->description); ?></div>
                    <?php endif; ?>
                </td>
                <td class="center" style="font-weight: bold;"><?php echo e(formatNumber($item->quantity)); ?></td>
                <td class="right" style="color: #64748b;"><?php echo e(formatNumber($item->mrp ?: ($item->item->mrp ?? $item->rate))); ?></td>
                <td class="right"><?php echo e(formatNumber($item->rate)); ?></td>
                <td class="right item-total-val"><?php echo e(formatNumber($item->total)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7" class="center" style="color: #64748b; padding: 25px;">No items found in this quotation.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Calculation / Summary -->
    <table class="summary-table-container">
        <tr>
            <td style="width: 50%;">
                <!-- Left spacing column -->
            </td>
            <td style="width: 50%;">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rs. <?php echo e(formatNumber($quotation->subtotal)); ?></td>
                    </tr>
                    <?php if($quotation->discount_amount > 0): ?>
                    <tr>
                        <td class="label">Discount <?php if($quotation->discount_type == 'percentage'): ?>(<?php echo e(formatNumber($quotation->discount_value)); ?>%)<?php endif; ?></td>
                        <td class="value discount-text">- Rs. <?php echo e(formatNumber($quotation->discount_amount)); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if($quotation->cgst_amount > 0): ?>
                    <tr>
                        <td class="label">CGST</td>
                        <td class="value">Rs. <?php echo e(formatNumber($quotation->cgst_amount)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($quotation->sgst_amount > 0): ?>
                    <tr>
                        <td class="label">SGST</td>
                        <td class="value">Rs. <?php echo e(formatNumber($quotation->sgst_amount)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($quotation->igst_amount > 0): ?>
                    <tr>
                        <td class="label">IGST</td>
                        <td class="value">Rs. <?php echo e(formatNumber($quotation->igst_amount)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php
                        $totalTax = $quotation->cgst_amount + $quotation->sgst_amount + $quotation->igst_amount;
                    ?>
                    <?php if($totalTax > 0 && $quotation->cgst_amount == 0 && $quotation->sgst_amount == 0 && $quotation->igst_amount == 0): ?>
                    <tr>
                        <td class="label">Tax</td>
                        <td class="value">Rs. <?php echo e(formatNumber($totalTax)); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if($quotation->round_off != 0): ?>
                    <tr>
                        <td class="label">Round Off</td>
                        <td class="value">Rs. <?php echo e(formatNumber($quotation->round_off)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="grand-total-row">
                        <td class="label">Grand Total</td>
                        <td class="value">Rs. <?php echo e(formatNumber($quotation->grand_total)); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Terms & Footer -->
    <table class="footer-section">
        <tr>
            <td style="width: 58%; padding-right: 15px;">
                <div class="terms-box">
                    <div class="terms-title">TERMS & CONDITIONS</div>
                    <div class="terms-content">
                        <?php if($quotation->terms_conditions || $company?->terms_conditions): ?>
                            <?php
                                $termsText = $quotation->terms_conditions ?: $company?->terms_conditions;
                                $termsLines = array_filter(array_map('trim', preg_split('/\r?\n/', $termsText)));
                            ?>
                            <ol>
                                <?php $__currentLoopData = $termsLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e(preg_replace('/^\d+[\.\)\-]\s*/', '', $line)); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        <?php else: ?>
                            <ol>
                                <li>Quotation is valid for 30 days from the issue date.</li>
                                <li>Delivery schedule will be confirmed upon purchase order confirmation.</li>
                            </ol>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td style="width: 42%; text-align: right; vertical-align: bottom;">
                <div class="signature-container">
                    <?php if($company?->signature): ?>
                        <img src="<?php echo e(getLocalImagePath($company?->signature)); ?>" alt="Signature" class="signature-img">
                        <br>
                    <?php else: ?>
                        <div style="height: 45px;"></div>
                    <?php endif; ?>
                    <div class="signature-line"></div>
                    <div class="signature-label">AUTHORISED SIGNATORY</div>
                    <div class="signature-company"><?php echo e($company?->company_name ?? 'BHAGYASHREE SANITARYWARE'); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Page Number Footer -->
    <div class="page-number-footer">
        Page {PAGE_NUM} of {PAGE_COUNT} &mdash; <?php echo e($company?->company_name ?? 'BHAGYASHREE SANITARYWARE'); ?>

    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans");
            $pdf->page_text(515, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(148, 163, 184));
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel_project\qutaionmangmentsystem\resources\views/admin/quotation/pdf.blade.php ENDPATH**/ ?>