<?php

namespace Ivo\LazyloadBundle;

use Imagine\Gd\Imagine;
use Contao\Image\Image as ContaoImageImage;
use Contao\Image\ResizeConfiguration;
use Contao\Image\ResizeOptions;
use Contao\Image\Resizer;
use Imagine\Image\ImageInterface;
use Contao\System;

class LazyloadHelper
{

    public static function getBase64($singleSRC = false, $width = false, $height = false)
    {
        $container = System::getContainer();
        $rootDir = $container->getParameter('kernel.project_dir');
        $lazyPath = '/assets/images/lazy';
        $lazyTarget = null;
        $strFile = null;
        if ($singleSRC && substr($singleSRC, -4) != '.svg' && \file_exists($rootDir . '/' . $singleSRC)) {
            $lazyTarget = $lazyPath . '/lazy-' . hash("md5", ($singleSRC . $width . $height)) . substr($singleSRC, -4);
            $image = $rootDir . '/' . $singleSRC;
        }

        if (!file_exists($rootDir . $lazyPath)) {
            mkdir($rootDir . $lazyPath);
        }

        if ($lazyTarget && !file_exists(TL_ROOT . '/' . $lazyTarget)) {
            $imagine = new Imagine();
            $resizer = new Resizer($rootDir . '/system/tmp');
            $image = new ContaoImageImage($image, $imagine);
            $config = (new ResizeConfiguration());
            if ($width && $height) {
                $config->setWidth($width)->setHeight($height);
            }
            $config->setMode(ResizeConfiguration::MODE_CROP);
            $options = (new ResizeOptions())
                ->setImagineOptions([
                    'jpeg_quality' => 2,
                    'webp_quality' => 1,
                    'webp_lossless' => true,
                    'interlace' => ImageInterface::INTERLACE_PLANE,
                ])
                ->setBypassCache(true)
                ->setTargetPath($rootDir . $lazyTarget);
            $resizer->resize($image, $config, $options);
        }
        if ($lazyTarget && file_exists(TL_ROOT . '/' . $lazyTarget)) {
            $strFile = @file_get_contents(TL_ROOT . '/' . $lazyTarget);
        }
        if ($strFile) {
            $strImg = "data:image/" . str_replace(
                ".",
                "",
                substr($lazyTarget, -4)
            ) . ";base64," . base64_encode($strFile);
        } else {
            if ($width && $height) {
                $strImg = 'data:image/svg+xml;charset=UTF-8,' . urlencode('<svg height="' . $height . 'px" viewBox="0 0 ' . $width . 'px ' . $height . 'px" width="' . $width . 'px" xmlns="http://www.w3.org/2000/svg"></svg>');
            } else {
                $strImg = 'data:image/svg+xml;charset=UTF-8,' . urlencode('<svg height="1px" viewBox="0 0 1px 1px" width="1px" xmlns="http://www.w3.org/2000/svg"></svg>');
            }
        }
        return $strImg;
    }
}