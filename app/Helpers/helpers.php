<?php 

if (! function_exists('moneyFormat')) {    
    /**
     * moneyFormat
     *
     * @param  mixed $str
     * @return void
     */
    function moneyFormat($str) {
        // number_format(angka, angka_di_belakang_koma, pemisah_desimal, pemisah_ribuan);
        return 'Rp. ' . number_format($str, '0', '', '.');
    }
}