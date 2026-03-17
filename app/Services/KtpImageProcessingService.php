<?php

namespace App\Services;


class KtpImageProcessingService
{
    public static function process($inputPath, $outputPath)
    {
        $img = new \Imagick($inputPath);

        $img->resizeImage(1500, 0, \Imagick:: FILTER_LANCZOS, 1);

        //$img->setImageColorspace(\Imagick::COLORSPACE_GRAY);

        $img->contrastImage(1);

        //$img->thresholdImage(0.4 * \Imagick::getQuantum());

        $img->sharpenImage(1, 0.6);

        $img->autoOrient();
        // $img->deskewImage(40);

        $img->setImageFormat('png');
        $img->writeImage($outputPath);

        $img->clear();
        $img->destroy();

        return $outputPath;
    }
}