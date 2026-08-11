<?php

if (!function_exists('formatNumber')) {
    function formatNumber($value, $decimals = 2, $useCommas = true) {
        return \App\Helpers\Helper::formatNumber($value, $decimals, $useCommas);
    }
}
