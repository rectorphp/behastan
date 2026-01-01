<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\DefinitionPatternExtractor\Fixture;

use Behat\Step\Then;

final class AnotherBehatContext
{
    /**
     * @When I click homepage
     */
    public function action(): void
    {
    }

    #[Then('never used')]
    public function deadAction(): void
    {
    }

    #[Then('Do this and \/ that')]
    public function trickyAction(): void
    {
    }
}
