<?php

declare(strict_types=1);

namespace Rector\Behastan\Command;

use Entropy\Console\Contract\CommandInterface;
use Entropy\Console\Enum\ExitCode;
use Entropy\Console\Output\OutputPrinter;
use Rector\Behastan\DefinitionMasksExtractor;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\Reporting\MaskCollectionStatsPrinter;
use Rector\Behastan\RulesRegistry;
use Rector\Behastan\ValueObject\RuleError;
use Webmozart\Assert\Assert;

final readonly class AnalyzeCommand implements CommandInterface
{
    public function __construct(
        private DefinitionMasksExtractor $definitionMasksExtractor,
        private MaskCollectionStatsPrinter $maskCollectionStatsPrinter,
        private OutputPrinter $outputPrinter,
        private RulesRegistry $rulesRegistry,
    ) {
    }

    /**
     * @param string $projectDirectory Project directory (we find *.Context.php definition files and *.feature script files there)
     * @param string[] $skip Skip a rule by identifier
     *
     * @return ExitCode::*
     */
    public function run(?string $projectDirectory = null, array $skip = []): int
    {
        // fallback to current directory
        if ($projectDirectory === null) {
            $projectDirectory = getcwd();
            Assert::string($projectDirectory);
        }

        Assert::directory($projectDirectory);

        $contextFileInfos = BehatMetafilesFinder::findContextFiles([$projectDirectory]);
        if ($contextFileInfos === []) {
            $this->outputPrinter->redBackground(sprintf(
                'No *.Context files found in "%s". Please provide correct directory',
                $projectDirectory
            ));

            return ExitCode::ERROR;
        }

        $featureFileInfos = BehatMetafilesFinder::findFeatureFiles([$projectDirectory]);
        if ($featureFileInfos === []) {
            $this->outputPrinter->redBackground(sprintf(
                'No *.feature files found in "%s". Please provide correct directory',
                $projectDirectory
            ));

            return ExitCode::ERROR;
        }

        $this->outputPrinter->writeln(sprintf(
            '<fg=green>Found %d Context and %d feature files</>',
            count($contextFileInfos),
            count($featureFileInfos)
        ));
        $this->outputPrinter->writeln('<fg=yellow>Extracting definitions masks...</>');

        $maskCollection = $this->definitionMasksExtractor->extract($contextFileInfos);
        $this->outputPrinter->newLine();

        $this->maskCollectionStatsPrinter->print($maskCollection);
        $this->outputPrinter->newLine();

        $this->outputPrinter->writeln('<fg=yellow>Running analysis...</>');
        $this->outputPrinter->newLine();

        /** @var RuleError[] $allRuleErrors */
        $allRuleErrors = [];
        foreach ($this->rulesRegistry->all() as $rule) {
            if ($skip !== [] && in_array($rule->getIdentifier(), $skip, true)) {
                $this->outputPrinter->writeln(sprintf('<fg=cyan>Skipping "%s" rule</>', $rule->getIdentifier()));
                $this->outputPrinter->newLine();
                continue;
            }

            $ruleErrors = $rule->process($contextFileInfos, $featureFileInfos, $maskCollection, $projectDirectory);
            $allRuleErrors = array_merge($allRuleErrors, $ruleErrors);
        }

        if ($allRuleErrors === []) {
            $this->outputPrinter->newLine(2);
            $this->outputPrinter->greenBackground('No errors found. Good job!');

            return ExitCode::SUCCESS;
        }

        $this->outputPrinter->newLine(2);

        $i = 1;
        foreach ($allRuleErrors as $allRuleError) {
            $this->outputPrinter->writeln(sprintf('<fg=yellow>%d) %s</>', $i, $allRuleError->getMessage()));
            foreach ($allRuleError->getLineFilePaths() as $lineFilePath) {
                // compared to listing() this allow to make paths clickable in IDE
                $this->outputPrinter->writeln($lineFilePath);
            }

            // identifier
            $this->outputPrinter->writeln(sprintf('* id:  <fg=cyan>[%s]</>', $allRuleError->getIdentifier()));
            $this->outputPrinter->newLine(2);

            $this->outputPrinter->newLine(2);

            ++$i;
        }

        $this->outputPrinter->newLine();

        $this->outputPrinter->redBackground(sprintf(
            'Found %d error%s',
            count($allRuleErrors),
            count($allRuleErrors) > 1 ? 's' : ''
        ));

        return ExitCode::ERROR;
    }

    public function getName(): string
    {
        return 'analyze';
    }

    public function getDescription(): string
    {
        return 'Run complete static analysis on Behat definitions and features';
    }
}
