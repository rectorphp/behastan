<?php

declare (strict_types=1);
namespace Rector\Behastan\Command;

use Jack202512\Entropy\Console\Contract\CommandInterface;
use Jack202512\Entropy\Console\Output\OutputPrinter;
use Rector\Behastan\DefinitionMasksExtractor;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\Reporting\MaskCollectionStatsPrinter;
use Rector\Behastan\RulesRegistry;
use Rector\Behastan\ValueObject\RuleError;
use Jack202512\Rector\Console\ExitCode;
use Webmozart\Assert\Assert;
final class AnalyzeCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Rector\Behastan\DefinitionMasksExtractor
     */
    private $definitionMasksExtractor;
    /**
     * @readonly
     * @var \Rector\Behastan\Reporting\MaskCollectionStatsPrinter
     */
    private $maskCollectionStatsPrinter;
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    /**
     * @readonly
     * @var \Rector\Behastan\RulesRegistry
     */
    private $rulesRegistry;
    public function __construct(DefinitionMasksExtractor $definitionMasksExtractor, MaskCollectionStatsPrinter $maskCollectionStatsPrinter, OutputPrinter $outputPrinter, RulesRegistry $rulesRegistry)
    {
        $this->definitionMasksExtractor = $definitionMasksExtractor;
        $this->maskCollectionStatsPrinter = $maskCollectionStatsPrinter;
        $this->outputPrinter = $outputPrinter;
        $this->rulesRegistry = $rulesRegistry;
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
            $this->outputPrinter->redBackground(sprintf('No *.Context files found in "%s". Please provide correct directory', $projectDirectory));
            return ExitCode::FAILURE;
        }
        $featureFileInfos = BehatMetafilesFinder::findFeatureFiles([$projectDirectory]);
        if ($featureFileInfos === []) {
            $this->outputPrinter->redBackground(sprintf('No *.feature files found in "%s". Please provide correct directory', $projectDirectory));
            return ExitCode::FAILURE;
        }
        $this->outputPrinter->writeln(sprintf('<fg=green>Found %d Context and %d feature files</>', count($contextFileInfos), count($featureFileInfos)));
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
            if ($skip !== [] && in_array($rule->getIdentifier(), $skip, \true)) {
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
            $this->outputPrinter->newLine(2);
            ++$i;
        }
        $this->outputPrinter->newLine();
        $this->outputPrinter->redBackground(sprintf('Found %d error%s', count($allRuleErrors), count($allRuleErrors) > 1 ? 's' : ''));
        return ExitCode::FAILURE;
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
