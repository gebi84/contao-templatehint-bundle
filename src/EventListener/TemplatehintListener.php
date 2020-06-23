<?php

namespace Gebi84\TemplatehintBundle\EventListener;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Widget;
use Gebi84\TemplatehintBundle\Helper\Templatehint;
use Symfony\Component\HttpKernel\Debug\FileLinkFormatter;
use Terminal42\ServiceAnnotationBundle\ServiceAnnotationInterface;

class TemplatehintListener implements ServiceAnnotationInterface
{
    /**
     * @var Templatehint
     */
    private $templatehint;

    /**
     * @var FileLinkFormatter
     */
    private $fileLinkFormatter;

    public function __construct(
        Templatehint $templatehint,
        FileLinkFormatter $fileLinkFormatter
    ) {
        $this->templatehint = $templatehint;
        $this->fileLinkFormatter = $fileLinkFormatter;
    }

    /**
     * @Hook("outputFrontendTemplate")
     * @Hook("outputBackendTemplate")
     */
    public function addAssets(string $buffer, string $template)
    {
        if ($this->templatehint->isTemplateHintEnabled()) {
            $assetsDir = 'bundles/gebi84templatehint';
            $head = '<link type="text/css" rel="stylesheet" href="' . $assetsDir . '/css/templatehint.css"/>';

            return str_replace('</head>', $head . '</head>', $buffer);
        }

        return $buffer;
    }

    /**
     * @Hook("parseFrontendTemplate")
     */
    public function parseFrontendTemplateHint(string $buffer, string $template): string
    {
        return $this->parseTemplateHint($buffer, $template);
    }

    protected function parseTemplateHint($buffer, $template): string
    {
        if ($this->templatehint->isTemplateHintEnabled()) {
            $return = '';
            $return .= '<div class="templatehint-container">';
            $templatePath = $this->getTemplate($template);
            $templateLink = $templatePath;
            $return .= '<div class="templatehint-div templatehint-template templatehint-hover"><a class="templatehintlinks" href="' . $this->fileLinkFormatter->format($templateLink, 0) . '">' . $templatePath . '</a></div>';

            $calledFile = null;
            $calledClass = null;
            $calledFunction = null;
            $calledLine = null;
            [$calledFile, $calledClass, $calledFunction, $calledLine] = $this->getCalledClass();

            $return .= '<div class="templatehint-div templatehint-class templatehint-hover"><a class="templatehintlinks" href="' . $this->fileLinkFormatter->format($calledFile, $calledLine) . '">' . $calledClass . ':' . $calledFunction . '</a></div>';
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

        return str_replace(TL_ROOT . '/', '', Controller::getTemplate($template));
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

    protected function getCalledClass(): array
    {
        $backtrace = debug_backtrace();
        if (!$this->isTwigTemplate()) {
            $found = [];

            foreach ($backtrace as $v) {
                if ($v['class'] === get_class($this) || $v['class'] === 'Contao\FrontendTemplate') {
                    continue;
                }

                /* also check parent function */
                if (!empty($found) && $found[2] !== $v['function']) {
                    break;
                }

                $found = [
                    $v['file'],
                    $v['class'],
                    $v['function'],
                    $v['line']
                ];
            }

            return $found;
        } else {
            foreach ($backtrace as $v) {
                $class = get_class($v['object']);
                if (substr($class, 0, 4) !== 'Twig' && $v['class'] !== get_class($this)) {
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
        return $this->parseTemplateHint($buffer, $template);
    }

    /**
     * @Hook("parseWidget")
     */
    public function parseWidghetHint(string $buffer, Widget $widget)
    {
        return $this->parseTemplateHint($buffer, $widget->template);
    }
}
