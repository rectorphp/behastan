<?php

declare (strict_types=1);
namespace Webmozart\Assert;

use Behastan202606\Psalm\Internal\Analyzer\Statements\Expression\ExpressionIdentifier;
use Behastan202606\Psalm\Plugin\EventHandler\AfterMethodCallAnalysisInterface;
use Behastan202606\Psalm\Plugin\EventHandler\Event\AfterMethodCallAnalysisEvent;
use Behastan202606\Psalm\Plugin\PluginEntryPointInterface;
use Behastan202606\Psalm\PluginRegistrationSocket;
use SimpleXMLElement;
final class PsalmPlugin implements PluginEntryPointInterface, AfterMethodCallAnalysisInterface
{
    public function __invoke(PluginRegistrationSocket $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }
    public static function afterMethodCallAnalysis(AfterMethodCallAnalysisEvent $event): void
    {
        [$class, $method] = explode('::', $event->getAppearingMethodId());
        if ($class !== \Webmozart\Assert\Assert::class) {
            return;
        }
        if (!isset(\Webmozart\Assert\HasAssert::HAS_ASSERT[$method])) {
            return;
        }
        $firstArg = $event->getExpr()->getArgs()[0] ?? null;
        if ($firstArg === null) {
            return;
        }
        $varId = ExpressionIdentifier::getExtendedVarId($firstArg->value, $event->getContext()->self, $event->getStatementsSource());
        if ($varId === null || !isset($event->getContext()->vars_in_scope[$varId])) {
            return;
        }
        $candidateType = $event->getContext()->vars_in_scope[$varId];
        $event->setReturnTypeCandidate($candidateType);
    }
}
