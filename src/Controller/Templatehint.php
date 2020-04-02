<?php

namespace Gebi84\TemplatehintBundle\Controller;

use Contao\CoreBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Gebi84\TemplatehintBundle\Helper\Templatehint as TemplateHintHelper;

/**
 * @Route("/{_locale}/tempelathint")
 */
class Templatehint extends AbstractController
{
    /**
     * @var TemplateHintHelper
     */
    protected $tempaltehint;

    public function __construct(TemplateHintHelper $templatehint)
    {
        $this->templatehint = $templatehint;
    }

    /**
     * @Route("/on/{scope}", name="template_hint_on")
     */
    public function setOn(string $scope)
    {
        $this->templatehint->enableTempalteHint($scope);

        $response = sprintf(
            '<html><body>todo ajax, scope:%s, status:%s</bod></html>',
            $scope,
            $this->templatehint->isTemplateHintEnabled($scope)
        );

        return new Response($response, 200);
    }

    /**
     * @Route("/off/{scope}", name="template_hint_off")
     */
    public function setOff(string $scope)
    {
        $this->templatehint->disableTempalteHint($scope);

        $response = sprintf(
            '<html><body>todo ajax, scope:%s, status:%s</bod></html>',
            $scope,
            $this->templatehint->isTemplateHintEnabled($scope)
        );

        return new Response($response, 200);
    }
}
