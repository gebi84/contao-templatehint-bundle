<?php

declare(strict_types=1);

namespace Gebi84\TemplatehintBundle\DataCollector;

use Gebi84\TemplatehintBundle\Helper\Templatehint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class TemplatehintDataCollector implements DataCollectorInterface
{
    /**
     * @var Templatehint
     */
    private $templatehint;

    public function __construct(Templatehint $templatehint)
    {
        $this->templatehint = $templatehint;
    }

    public function collect(Request $request, Response $response)
    {
    }

    public function getName()
    {
        return 'templatehint_data_collector';
    }

    public function reset()
    {
        $this->data = [];
    }

    public function isTemplateHintEnabled(): bool
    {
        return $this->templatehint->isTemplateHintEnabled();
    }

    public function getScope(): string
    {
        return $this->templatehint->getScope();
    }
}
