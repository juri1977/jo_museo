<?php

namespace JO\JoMuseo\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class CleanstringViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * Initialize arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     */
    public function initializeArguments()
    {
        $this->registerArgument('string', 'string', 'Text to clean.');
        $this->registerArgument('type', 'string', 'Type of text.');
    }

    /**
     * Return array element by key.
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @throws Exception
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $string = $arguments['string'];
        $type = $arguments['type'];

        if ((string) $string === '') {
            throw new Exception('An argument "string" has to be provided', 1351584844);
        }

        $string = strtolower($string);

        // $value = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        // nur a-z buchstaben beibehalten
        $string = preg_replace('/[^a-z]/', '', $string);

        return $string;
    }
}
