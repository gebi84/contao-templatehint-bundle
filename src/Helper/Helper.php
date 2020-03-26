<?php

namespace Gebi84\TemplatehintBundle\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Helper
{
    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function isTemplateHintActive(): bool
    {
        return true;
        $session = $this->session;

        if (null === $session->get('frontendTemplateHints')) {
            $session->set('frontendTemplateHints', false);
        }

        if (null === $session->get('backendTemplateHints')) {
            $session->set('backendTemplateHints', false);
        }

        if (defined('TL_MODE') && TL_MODE === 'BE') {
            return $session->get('backendTemplateHints');
        }

        return $session->get('frontendTemplateHints');
    }
}