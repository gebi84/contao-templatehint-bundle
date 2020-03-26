<?php

declare(strict_types=1);

namespace Gebi84\TemplatehintBundle\DataCollector;

use Gebi84\TemplatehintBundle\Helper\Helper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class TemplatehintDataCollector implements DataCollectorInterface
{
    /**
     * @var Helper
     */
    private $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
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

    public function isTemplateHintActive(): bool
    {
        return $this->helper->isTemplateHintActive();
    }
}
