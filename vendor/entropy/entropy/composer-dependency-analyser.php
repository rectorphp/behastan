<?php

// @see https://github.com/shipmonk-rnd/composer-dependency-analyser/
declare (strict_types=1);
namespace Behastan202512;

use Behastan202512\ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use Behastan202512\ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;
return (new Configuration())->ignoreErrorsOnExtension('ext-filter', [ErrorType::SHADOW_DEPENDENCY]);
