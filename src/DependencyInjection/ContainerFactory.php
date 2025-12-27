<?php

declare(strict_types=1);

namespace Rector\Behastan\DependencyInjection;

use Entropy\Container\Container;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\RulesRegistry;

final class ContainerFactory
{
    public static function create(): Container
    {
        $container = new Container();

        $container->autodiscover(__DIR__ . '/../Rule');
        $container->autodiscover(__DIR__ . '/../Command');

        $container->service(RulesRegistry::class, function (Container $container) {
            return new RulesRegistry($container->findByContract(RuleInterface::class));
        });

        return $container;
    }
}
