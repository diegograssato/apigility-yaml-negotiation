<?php

namespace ZF\ContentNegotiation\YAML;

use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\View\ApiProblemRenderer;
use ZF\ContentNegotiation\YAML\View\YamlRenderer;
use ZF\ContentNegotiation\YAML\View\YamlStrategy;

class Module
{
    public function getConfig()
    {
        return include __DIR__.'/../config/module.config.php';
    }

    /**
     * Retrieve Service Manager configuration.
     *
     * Defines ZF\ContentNegotiation\YAML\View\YamlStrategy service factory.
     *
     * @return array
     */
    public function getServiceConfig()
    {
        return ['factories' => [
                YamlRenderer::class => function ($services) {
                    $apiProblemRenderer = $services->get(ApiProblemRenderer::class);

                    $config = $services->get('Config');
                    $renderer = new View\YamlRenderer($apiProblemRenderer, $config);

                    return $renderer;
                },
                YamlStrategy::class => function ($services) {
                    $renderer = $services->get(YamlRenderer::class);

                    return new View\YamlStrategy($renderer);
                },
            ],
        ];
    }

    /**
     * Listener for bootstrap event.
     *
     * Attaches a render event.
     *
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $app = $event->getTarget();
        $events = $app->getEventManager();
        $events->attach(MvcEvent::EVENT_RENDER, array($this, 'onRender'), 100);
    }

    /**
     * Listener for the render event.
     *
     * Attaches a rendering/response strategy to the View.
     *
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        $result = $event->getResult();

        if (!$result instanceof View\YamlModel) {
            return;
        }

        $app = $event->getTarget();
        $services = $app->getServiceManager();

        if ($services->has('View')) {
            $view = $services->get('View');
            $events = $view->getEventManager();

            // register at high priority, to "beat" normal json strategy registered
            // via view manager, as well as HAL strategy.
            $services->get(YamlStrategy::class)->attach($events, 100);
        }
    }
}
