<?php

if (!function_exists('is_cli')) {
    function is_cli() {
        if ( defined('STDIN') ) {
            return true;
        }
        if ( php_sapi_name() === 'cli' ) {
            return true;
        }
        if ( array_key_exists('SHELL', $_ENV) ) {
            return true;
        }
        if ( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return true;
        }
        if ( !array_key_exists('REQUEST_METHOD', $_SERVER) ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('camelize')) {
    function camelize(string $string): string
    {
        $separator = '-';
        $result = lcfirst(str_replace($separator, '', ucwords($string, $separator)));

        $separator = '_';
        return lcfirst(str_replace($separator, '', ucwords($result, $separator)));
    }
}

if (!function_exists('csv_to_array')) {
    function csv_to_array($csv)
    {
        return array_map('str_getcsv', file($csv));
    }
}

if (!function_exists('determine_pixels')) {
    function determine_pixels(array $pixels)
    {
        $determinedPixels = [];

        foreach ($pixels as $pixel) {
            $y = $pixel[0];
            $x = $pixel[1];
            $color = $pixel[2];
            $opacity = 1;
            if (array_key_exists(3, $pixel)) {
                $opacity = $pixel[3] / 100;
                $pixel[3] = $pixel[3] / 100;
            } else {
                $pixel[3] = 1;
            }

            $xMultiple = explode('-', $x);
            if (count($xMultiple) === 2) {
                foreach (range($xMultiple[0], $xMultiple[1]) as $xExtend) {
                    $determinedPixel = [
                        (int) $y,
                        (int) $xExtend,
                        $color,
                        $opacity
                    ];
                    $determinedPixels[$color][] = $determinedPixel;
                }
            } else {
                $determinedPixels[$color][] = $pixel;
            }
        }

        $finalPixels = [];
        foreach ($determinedPixels as $determinedPixelColor) {
            $finalPixels = array_merge($finalPixels, $determinedPixelColor);
        }

        return $finalPixels;
    }
}
