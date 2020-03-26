<?php

namespace Gebi84\TemplatehintBundle\EventListener;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Gebi84\TemplatehintBundle\Helper\Helper;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;

class TemplatehintListener implements ServiceAnnotationInterface
{
    /**
     * @var Helper
     */
    private $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @Hook("outputFrontendTemplate")
     * @Hook("outputBackendTemplate")
     */
    public function addAssets(string $buffer, string $template)
    {
        if ($this->helper->isTemplateHintActive()) {
            $assetsDir = 'bundles/templatehint';
            $GLOBALS['TL_CSS'][] = $assetsDir . '/css/templatehint.css';
            $head = '';
            $head .= '<link type="text/css" rel="stylesheet" href="' . $assetsDir . '/css/templatehint.css"/>';

            return str_replace('</head>', $head . '</head>', $buffer);
        }

        return $buffer;
    }

    /**
     * @Hook("parseFrontendTemplate")
     */
    public function parseFrontendTemplateHint(string $buffer, string $template): string
    {
        return $this->parseTempateHint($buffer, $template);
    }

    protected function parseTempateHint($buffer, $template): string
    {
        if ($this->helper->isTemplateHintActive()) {
            $return = '';
            $return .= '<div class="templatehint-container">';
            $return .= '<div class="templatehint-div templatehint-template templatehint-hover">' . $this->getTemplate($template) . '</div>';
            $return .= '<div class="templatehint-div templatehint-class templatehint-hover">' . $this->getCalledClass() . '</div>';
            $return .= $buffer;
            $return .= '</div>';

            return $return;
        }

        return $buffer;
    }

    protected function getTemplate(string $template): string
    {
        $templateString = str_replace(TL_ROOT, '', Controller::getTemplate($template));

        if (strstr($templateString, '/system/modules/core/') !== false) {
            $icon = '<i class="fa fa-file-image-o"></i>';
        } else {
            $icon = '<i class="fa fa-cog"></i>';
        }

        return $icon . ' ' . $templateString;
    }

    protected function getCalledClass(): string
    {

        $backtrace = debug_backtrace();
        foreach ($backtrace as $k => $v) {
            if ($v['class'] != get_class($this) && $v['class'] != 'Contao\FrontendTemplate') {
                return $v['class'];
            }
        }
    }

    /**
     * @Hook("parseBackendTemplate")
     */
    public function parseBackendTemplateHint(string $buffer, string $template): string
    {
        return $this->parseTempateHint($buffer, $template);
    }
}
