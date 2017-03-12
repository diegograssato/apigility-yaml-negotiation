<?php

namespace ZF\ContentNegotiation\YAML\View;

use Zend\View\Renderer\RendererInterface;
use Zend\View\ViewEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\View\ApiProblemModel;
use ZF\ApiProblem\View\ApiProblemRenderer;
use Zend\View\Resolver;
use Zend\View\Exception;

/**
 * Class YamlRenderer.
 */
class YamlRenderer implements RendererInterface
{
    /**
     * @var ApiProblemRenderer
     */
    protected $apiProblemRenderer;

    /**
     * @var ViewEvent
     */
    protected $viewEvent;

    /**
     * @var Config
     */
    protected $config;

    private $_entity,
            $_collection,
            $_payload;

    /**
     * @return ViewEvent
     */
    public function getViewEvent()
    {
        return $this->viewEvent;
    }

    /**
     * Return the template engine object, if any.
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return mixed
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     *
     * @param ResolverInterface $resolver
     *
     * @return RendererInterface
     */
    public function setResolver(Resolver\ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param ApiProblemRenderer $apiProblemRenderer
     */
    public function __construct(ApiProblemRenderer $apiProblemRenderer, $config)
    {
        $this->apiProblemRenderer = $apiProblemRenderer;
        $this->config = $config;
    }

    /**
     * @param ViewEvent $event
     *
     * @return self
     */
    public function setViewEvent(ViewEvent $event)
    {
        $this->viewEvent = $event;

        return $this;
    }

    /**
     * Render a view model.
     *
     * If the view model is a YamlRenderer, determines if it represents
     * a Collection or Entity, and, if so, creates a custom
     * representation appropriate to the type.
     *
     * If not, it passes control to the parent to render.
     *
     * @param YamlModel $nameOrModel
     * @param mixed     $values
     *
     * @return string
     */
    public function render($nameOrModel, $values = null)
    {
        if ($nameOrModel instanceof YamlModel) {
            return $this->_render($nameOrModel, $values);
        }

        throw new Exception\DomainException(sprintf(
            '%s: Do not know how to handle operation when both $nameOrModel and $values are populated',
            __METHOD__
        ));
    }

    protected function _render($nameOrModel, $values = null)
    {
        $yaml = [];
        if ($nameOrModel->isEntity() || $nameOrModel->isCollection()) {
            $payload = $nameOrModel->getPayload();

            if ($payload instanceof ApiProblem) {
                return $this->renderApiProblem($payload);
            }

            $collection = $nameOrModel->getPayload()->getCollection();
            $yaml = yaml_emit($collection);
        }

        return $yaml;
    }

    /**
     * Render an API-Problem result.
     *
     * Creates an ApiProblemModel with the provided ApiProblem, and passes it
     * on to the composed ApiProblemRenderer to render.
     *
     * If a ViewEvent is composed, it passes the ApiProblemModel to it so that
     * the ApiProblemStrategy can be invoked when populating the response.
     *
     * @param ApiProblem $problem
     *
     * @return string
     */
    protected function renderApiProblem(ApiProblem $problem)
    {
        $model = new ApiProblemModel($problem);
        $event = $this->getViewEvent();
        if ($event) {
            $event->setModel($model);
        }

        return $this->apiProblemRenderer->render($model);
    }
}
