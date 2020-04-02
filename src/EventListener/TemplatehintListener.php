<?php

namespace Gebi84\TemplatehintBundle\EventListener;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Gebi84\TemplatehintBundle\Helper\Templatehint;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;

class TemplatehintListener implements ServiceAnnotationInterface
{
    /**
     * @var Templatehint
     */
    private $templatehint;

    public function __construct(Templatehint $templatehint)
    {
        $this->templatehint = $templatehint;
    }

    /**
     * @Hook("outputFrontendTemplate")
     * @Hook("outputBackendTemplate")
     */
    public function addAssets(string $buffer, string $template)
    {
        if ($this->templatehint->isTemplateHintEnabled()) {
            $assetsDir = 'bundles/templatehint';
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
        if ($this->templatehint->isTemplateHintEnabled()) {
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
        if (!$this->isTwigTemplate()) {
            $templateString = Controller::getTemplate($template);
        } else {
            $twig = \ContaoTwig::getInstance();
            $loader = $twig->getLoaderFilesystem();
            $template = $template . '.html5.twig';
            $templateString = $loader->getCacheKey($template);
        }

        $templateString = str_replace(TL_ROOT, '', Controller::getTemplate($template));

        if (strstr($templateString, '/system/modules/core/') !== false) {
            $icon = '<i class="fa fa-file-image-o"></i>';
        } else {
            $icon = '<i class="fa fa-cog"></i>';
        }

        return $icon . ' ' . $templateString;
    }

    protected function isTwigTemplate(): bool
    {
        $backtrace = debug_backtrace();

        $isTwig = false;
        foreach ($backtrace as $v) {
            if ('Twig' === substr($v['class'], 0, 4)) {
                $isTwig = true;
                break;
            }
        }

        return $isTwig;
    }

    protected function getCalledClass(): string
    {
        $backtrace = debug_backtrace();
        if (!$this->isTwigTemplate()) {
            foreach ($backtrace as $v) {
                if ($v['class'] != get_class($this) && $v['class'] != 'Contao\FrontendTemplate') {
                    return $v['class'];
                }
            }
        } else {
            foreach ($backtrace as $v) {
                $class = get_class($v['object']);
                if (substr($class, 0, 4) != 'Twig' && $v['class'] != get_class($this)) {
                    return $class;
                }
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
