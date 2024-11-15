<?php

use Carbon\Carbon;

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('clearRupiah')) {
    function clearRupiah($angka)
    {
        return (int) preg_replace('/[^0-9]/', '', $angka);
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal, $format = 'd F Y')
    {
        return Carbon::parse($tanggal)->format($format);
    }
}
