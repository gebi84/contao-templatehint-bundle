<?php

declare(strict_types=1);

namespace Gebi84\TemplatehintBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Routing\RoutingPluginInterface;
use Gebi84\TemplatehintBundle\Gebi84TemplatehintBundle;
use Gebi84\TemplatehintBundle\TemplatehintBundle;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Plugin implements BundlePluginInterface, RoutingPluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
          BundleConfig::create(Gebi84TemplatehintBundle::class)
            ->setLoadAfter(
                [
                    ContaoCoreBundle::class
                ]
            )
        ];
    }

    public function getRouteCollection(LoaderResolverInterface $resolver, KernelInterface $kernel)
    {
        $file = '@Gebi84TemplatehintBundle/Resources/config/routing.yml';

        return $resolver->resolve($file)->load($file);
    }
}
