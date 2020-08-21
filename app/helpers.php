<?php

if (!function_exists('decode_id')) {
    function decode_id($id) {
        if (is_null(request()->route('id'))) {
            abort(404);
        }

        return json_decode(base64_decode($id), true);
    }
}

if (!function_exists('lower_replace')) {
    function lower_replace($name) {
        return strtolower(str_replace(' ', '_', $name));
    }
}

if (!function_exists('generate_machine_id')) {
    function generate_machine_id() {
        $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyz';

        return substr(str_shuffle($permittedChars), 0, 10);
    }
}

if (!function_exists('conArr')) {
    function conArr($arr) {
        $last = '';
        for ($i = 1; $i < count($arr); $i++) {
            $last .= $arr[$i] . ' ';
        }

        return [
            $arr[0],
            $last
        ];
    }
}