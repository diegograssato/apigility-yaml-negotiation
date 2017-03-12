<?php

namespace ZF\ContentNegotiation\YAML\View;

use Zend\View\Strategy\PhpRendererStrategy;
use Zend\View\ViewEvent;

/**
 * Class YamlStrategy.
 */
class YamlStrategy extends PhpRendererStrategy
{
    /**
     * Character set for associated content-type.
     *
     * @var string
     */
    protected $charset = 'utf-8';

    public function __construct(YamlRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Detect if we should use the HalJsonRenderer based on model type.
     *
     * @param ViewEvent $e
     *
     * @return null|HalJsonRenderer
     */
    public function selectRenderer(ViewEvent $e)
    {
        $model = $e->getModel();

        if (!$model instanceof YamlModel) {
            // unrecognized model; do nothing
            return;
        }

        // YamlModel found
        $this->renderer->setViewEvent($e);

        return $this->renderer;
    }

    /**
     * Inject the response.
     *
     * Injects the response with the rendered content, and sets the content
     * type based on the detection that occurred during renderer selection.
     *
     * @param ViewEvent $e
     */
    public function injectResponse(ViewEvent $e)
    {
        $result = $e->getResult();

        if (!is_string($result)) {
            // We don't have a string
            return;
        }

        // Populate response
        /** @var \Zend\Http\Response $response */
        $response = $e->getResponse();
        $response->setContent($result);
        $headers = $response->getHeaders();

        $contentType = 'text/yaml';

        $contentType .= '; charset='.$this->charset;

        $headers->addHeaderLine('content-type', $contentType);
    }
}
