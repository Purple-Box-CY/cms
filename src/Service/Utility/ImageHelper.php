<?php

namespace App\Service\Utility;

class ImageHelper
{
    public static function isHeic(string $path): bool
    {
        try {
            $h = fopen($path, 'rb');
            $f = fread($h, 12);
            fclose($h);
            $magicNumber = strtolower(trim(substr($f, 8)));

            $heicMagicNumbers = [
                'heic', // official
                'mif1', // unofficial but can be found in the wild
                'ftyp', // 10bit images, or anything that uses h265 with range extension
                'hevc', // brands for image sequences
                'hevx', // brands for image sequences
                'heim', // multiview
                'heis', // scalable
                'hevm', // multiview sequence
                'hevs', // multiview sequence
            ];

            if (in_array($magicNumber, $heicMagicNumbers)) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    public static function isWebp(string $path): bool
    {
        try {
            $h = fopen($path, 'rb');
            $f = fread($h, 12);
            fclose($h);
            $magicNumber = strtolower(trim(substr($f, 8)));

            $webpMagicNumbers = [
                'webp', // official
            ];

            if (in_array($magicNumber, $webpMagicNumbers)) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }

    public static function isJpg(string $path): bool
    {
        try {
            return exif_imagetype($path) == IMAGETYPE_JPEG;
        } catch (\Exception $e) {

            return false;
        }
    }

    public static function isPng(string $path): bool
    {
        try {
            $h = fopen($path, 'rb');
            $f = fread($h, 12);
            fclose($h);
            $magicNumber = strtolower(trim(substr($f, 8)));
            if (!$magicNumber) {
                $magicNumber = strtolower(trim(str_replace(["\n", "\r", ""], '', substr($f, 1))));
            }

            $pngMagicNumbers = [
                'png', // official
            ];

            if (in_array($magicNumber, $pngMagicNumbers)) {
                return true;
            }
        } catch (\Throwable $e) {
            return false;
        }

        return false;
    }


    public static function correctImageOrientation(string $filename): void {
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }
}