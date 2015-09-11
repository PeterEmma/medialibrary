<?php

if (!function_exists('filesize_to_human')) {

    /**
     * Convert bytes to human readable size.
     *
     * @param     $bytes
     * @param int $decimals
     *
     * @return string
     */
    function filesize_to_human($bytes, $decimals = 2)
    {
        $size   = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }

}
