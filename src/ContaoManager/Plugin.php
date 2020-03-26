<?php

declare(strict_types=1);

namespace Gebi84\TemplatehintBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Gebi84\TemplatehintBundle\TemplatehintBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
          BundleConfig::create(TemplatehintBundle::class)
            ->setLoadAfter(
                [
                    ContaoCoreBundle::class
                ]
            )
        ];
    }
}
