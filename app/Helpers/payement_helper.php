<?php

if (!function_exists('generate_structured_com')) {
    function generate_structured_com(int $id_compte, int $id_session): string
    {
        $reference = str_pad($id_compte, 3, '0', STR_PAD_LEFT) . str_pad($id_session, 7, '0', STR_PAD_LEFT);
        $check     = (int) $reference % 97;
        if ($check === 0) $check = 97;
        $full = $reference . str_pad($check, 2, '0', STR_PAD_LEFT);
        return '+++' . substr($full, 0, 3) . '/' . substr($full, 3, 4) . '/' . substr($full, 7, 5) . '+++';
    }
}
