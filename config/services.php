<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('strata.preview_mode.data_provider', '');

    $services
        ->set('strata.query_manager', \Strata\Data\Query\QueryManager::class)
        ->alias(\Strata\Data\Query\QueryManager::class, 'strata.query_manager')

        ->set('strata.site', \Strata\Frontend\Site::class)
        ->alias(\Strata\Frontend\Site::class, 'strata.site')

        ->set('strata.response_helper', \Strata\SymfonyBundle\ResponseHelper\SymfonyResponseHelper::class)
        ->alias(\Strata\SymfonyBundle\ResponseHelper\SymfonyResponseHelper::class, 'strata.response_helper')

        ->set('strata.twig', \Strata\SymfonyBundle\Twig\TwigExtension::class)
        ->tag('twig.extension')

        ->set('strata.event_subscriber.twig', \Strata\SymfonyBundle\EventSubscriber\TwigEventSubscriber::class)
        ->args([
            service(\Twig\Environment::class),
            service('strata.site'),
        ])
        ->tag('kernel.event_subscriber')

        ->set('strata.event_subscriber.preview_mode', \Strata\SymfonyBundle\EventSubscriber\PreviewModeEventSubscriber::class)
        ->args([
            '',
            service('strata.query_manager'),
        ])
        ->tag('kernel.event_subscriber')

        ->set('strata.event_subscriber.response_tags', \Strata\SymfonyBundle\EventSubscriber\ResponseHelperEventSubscriber::class)
        ->args([service('strata.response_helper')])
        ->tag('kernel.event_subscriber')

        ->set('strata.data_collector', \Strata\SymfonyBundle\DataCollector\StrataDataCollector::class)
        ->args([
            service('strata.query_manager'),
            service('strata.site'),
        ])
        ->tag('data_collector', ['id' => 'strata'])

        ->set('strata.event_subscriber.response_tags', \Strata\SymfonyBundle\EventSubscriber\ResponseTagsEventSubscriber::class)
        ->args([
            service('fos_http_cache.http.symfony_response_tagger'),
            service('strata.query_manager'),
        ])
        ->tag('kernel.event_subscriber')
    ;
};
