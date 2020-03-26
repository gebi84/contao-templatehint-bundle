<?php

declare(strict_types=1);

namespace Gebi84\TemplatehintBundle;

use Gebi84\TemplatehintBundle\DependencyInjection\TemplatehintBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class TemplatehintBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new TemplatehintBundleExtension();
    }
}
