<?php

namespace Ivo\LazyloadBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Lupcom\LazyloadBundle\LazyloadBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

class Plugin implements BundlePluginInterface
{

    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(LazyloadBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}