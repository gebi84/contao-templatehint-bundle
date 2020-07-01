<?php

namespace Gebi84\TemplatehintBundle\Helper;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class Templatehint
{
    public const SCOPE_BACKEND = 'backend';
    public const SCOPE_FRONTEND = 'frontend';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        SessionInterface $session,
        KernelInterface $kernel,
        RequestStack $requeststack
    ) {
        $this->session = $session;
        $this->kernel = $kernel;
        $this->request = $requeststack->getCurrentRequest();
    }

    public function isTemplateHintEnabled(?string $scope = null): bool
    {
        $session = $this->session;

        /* set defaults */
        if (null === $session->get('frontendTemplateHints')) {
            $session->set('frontendTemplateHints', false);
        }

        if (null === $session->get('backendTemplateHints')) {
            $session->set('backendTemplateHints', false);
        }

        /* only active if in debug modus */
        if (!$this->kernel->isDebug()) {
            $session->set('frontendTemplateHints', false);
            $session->set('backendTemplateHints', false);
        }

        if (null === $scope) {
            $scope = $this->getScope();
        }

        if (self::SCOPE_BACKEND === $scope) {
            return $session->get('backendTemplateHints');
        }

        return $session->get('frontendTemplateHints');
    }

    /**
     * returns the contao scope bacckend|frondend
     */
    public function getScope(): string
    {
        if (!$this->request instanceof Request) {
            return self::SCOPE_FRONTEND;
        }

        if (self::SCOPE_BACKEND === $this->request->attributes->get('_scope')) {
            return self::SCOPE_BACKEND;
        }

        return self::SCOPE_FRONTEND;
    }

    public function enableTempalteHint(?string $scope = null): void
    {
        $session = $this->session;
        
        if (null === $scope) {
            $scope = $this->getScope();
        }

        if (self::SCOPE_BACKEND === $scope) {
            $session->set('backendTemplateHints', true);
        } else {
            $session->set('frontendTemplateHints', true);
        }
    }

    public function disableTempalteHint(?string $scope = null): void
    {
        $session = $this->session;

        if (null === $scope) {
            $scope = $this->getScope();
        }

        if (self::SCOPE_BACKEND === $scope) {
            $session->set('backendTemplateHints', false);
        } else {
            $session->set('frontendTemplateHints', false);
        }
    }
}
