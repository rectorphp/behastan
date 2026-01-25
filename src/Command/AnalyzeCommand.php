<?php

declare (strict_types=1);
namespace Rector\Behastan\Command;

use Behastan202601\Entropy\Console\Contract\CommandInterface;
use Behastan202601\Entropy\Console\Enum\ExitCode;
use Behastan202601\Entropy\Console\Output\OutputPrinter;
use Rector\Behastan\DefinitionPatternsExtractor;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\Reporting\PatternCollectionStatsPrinter;
use Rector\Behastan\RulesRegistry;
use Rector\Behastan\ValueObject\RuleError;
use Webmozart\Assert\Assert;
final class AnalyzeCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \Rector\Behastan\DefinitionPatternsExtractor
     */
    private $definitionPatternsExtractor;
    /**
     * @readonly
     * @var \Rector\Behastan\Reporting\PatternCollectionStatsPrinter
     */
    private $patternCollectionStatsPrinter;
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
    public function __construct(DefinitionPatternsExtractor $definitionPatternsExtractor, PatternCollectionStatsPrinter $patternCollectionStatsPrinter, OutputPrinter $outputPrinter, RulesRegistry $rulesRegistry)
    {
        $this->definitionPatternsExtractor = $definitionPatternsExtractor;
        $this->patternCollectionStatsPrinter = $patternCollectionStatsPrinter;
        $this->outputPrinter = $outputPrinter;
        $this->rulesRegistry = $rulesRegistry;
    }
    /**
     * @param string $projectDirectory Project directory (we find *.Context.php definition files and *.feature script files there)
     * @param string[] $skips Skip a rule by identifier
     *
     * @return ExitCode::*
     */
    public function run(?string $projectDirectory = null, array $skips = []): int
    {
        // fallback to current directory
        if ($projectDirectory === null) {
            $projectDirectory = getcwd();
            Assert::string($projectDirectory);
        }
        Assert::directory($projectDirectory);
        $contextFileInfos = BehatMetafilesFinder::findContextFiles([$projectDirectory]);
        if ($contextFileInfos === []) {
            $this->outputPrinter->redBackground(sprintf('No *.Context files found in "%s".%sPlease provide correct directory', $projectDirectory, \PHP_EOL));
            return ExitCode::ERROR;
        }
        $featureFileInfos = BehatMetafilesFinder::findFeatureFiles([$projectDirectory]);
        if ($featureFileInfos === []) {
            $this->outputPrinter->redBackground(sprintf('No *.feature files found in "%s".%sPlease provide correct directory', $projectDirectory, \PHP_EOL));
            return ExitCode::ERROR;
        }
        $this->outputPrinter->writeln(sprintf('<fg=green>Found %d Context and %d feature files</>', count($contextFileInfos), count($featureFileInfos)));
        $this->outputPrinter->writeln('<fg=yellow>Extracting definitions patterns...</>');
        $patternCollection = $this->definitionPatternsExtractor->extract($contextFileInfos);
        $this->outputPrinter->newLine();
        $this->patternCollectionStatsPrinter->print($patternCollection);
        $this->outputPrinter->newLine();
        $this->outputPrinter->writeln('<fg=yellow>Running analysis...</>');
        $this->outputPrinter->newLine();
        /** @var RuleError[] $allRuleErrors */
        $allRuleErrors = [];
        foreach ($this->rulesRegistry->all() as $rule) {
            if (in_array($rule->getIdentifier(), $skips, \true)) {
                $this->outputPrinter->writeln(sprintf('<fg=cyan>Skipping "%s" rule</>', $rule->getIdentifier()));
                $this->outputPrinter->newLine();
                continue;
            }
            $ruleErrors = $rule->process($contextFileInfos, $featureFileInfos, $patternCollection, $projectDirectory);
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
            $this->outputPrinter->writeln(sprintf('<fg=yellow>%d) %s</> [id: <fg=cyan>%s</>]', $i, $allRuleError->getMessage(), $allRuleError->getIdentifier()), 1);
            foreach ($allRuleError->getLineFilePaths() as $lineFilePath) {
                // compared to listing() this allow to make paths clickable in IDE
                // use relative paths to getcwd()
                $relativePath = str_replace(getcwd() . \DIRECTORY_SEPARATOR, '', $lineFilePath);
                $this->outputPrinter->writeln($relativePath);
            }
            $this->outputPrinter->newLine(2);
            ++$i;
        }
        $this->outputPrinter->newLine();
        $this->outputPrinter->redBackground(sprintf('Found %d error%s', count($allRuleErrors), count($allRuleErrors) > 1 ? 's' : ''));
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
