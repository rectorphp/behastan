<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\Analyzer\UnusedDefinitionsAnalyzer\Fixture\UnusedPattern;

final class BehatContext
{
    /**
     * @When I click homepage
     */
    public function action(): void
    {
    }

    /**
     * @Then never used
     */
    public function deadAction(): void
    {
    }
}
