<?php

namespace Gebi84\TemplatehintBundle\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Helper
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(
        SessionInterface $session,
        KernelInterface $kernel
    ) {
        $this->session = $session;
        $this->kernel = $kernel;
    }

    public function isTemplateHintActive(): bool
    {
        $session = $this->session;

        if (null === $session->get('frontendTemplateHints')) {
            $session->set('frontendTemplateHints', false);
        }

        if (null === $session->get('backendTemplateHints')) {
            $session->set('backendTemplateHints', false);
        }

        if (!$this->kernel->isDebug()) {
            $session->set('frontendTemplateHints', false);
            $session->set('backendTemplateHints', false);
            return false;
        }

        if (defined('TL_MODE') && TL_MODE === 'BE') {
            return $session->get('backendTemplateHints');
        }

        return $session->get('frontendTemplateHints');
    }
}
