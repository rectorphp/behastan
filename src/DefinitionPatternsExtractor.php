<?php

declare (strict_types=1);
namespace Rector\Behastan;

use Behastan202601\Entropy\Attributes\RelatedTest;
use Behastan202601\PhpParser\Node\Name;
use Behastan202601\PhpParser\Node\Stmt\Class_;
use Behastan202601\PhpParser\NodeFinder;
use Rector\Behastan\Analyzer\PatternAnalyzer;
use Rector\Behastan\PhpParser\SimplePhpParser;
use Rector\Behastan\Resolver\ClassMethodPatternResolver;
use Rector\Behastan\Tests\DefinitionPatternExtractor\DefinitionPatternExtractorTest;
use Rector\Behastan\ValueObject\ContextDefinition;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;
use Rector\Behastan\ValueObject\Pattern\NamedPattern;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;
use Rector\Behastan\ValueObject\Pattern\SkippedPattern;
use Rector\Behastan\ValueObject\PatternCollection;
use SplFileInfo;
final class DefinitionPatternsExtractor
{
    /**
     * @readonly
     * @var \Rector\Behastan\PhpParser\SimplePhpParser
     */
    private $simplePhpParser;
    /**
     * @readonly
     * @var \PhpParser\NodeFinder
     */
    private $nodeFinder;
    /**
     * @readonly
     * @var \Rector\Behastan\Resolver\ClassMethodPatternResolver
     */
    private $classMethodPatternResolver;
    public function __construct(SimplePhpParser $simplePhpParser, NodeFinder $nodeFinder, ClassMethodPatternResolver $classMethodPatternResolver)
    {
        $this->simplePhpParser = $simplePhpParser;
        $this->nodeFinder = $nodeFinder;
        $this->classMethodPatternResolver = $classMethodPatternResolver;
    }
    /**
     * @param SplFileInfo[] $contextFiles
     */
    public function extract(array $contextFiles): PatternCollection
    {
        $patterns = [];
        $classMethodContextDefinitions = $this->resolvePatternsFromFiles($contextFiles);
        foreach ($classMethodContextDefinitions as $classMethodContextDefinition) {
            $rawPattern = $classMethodContextDefinition->getPattern();
            // @todo edge case - handle next
            if (strpos($rawPattern, ' [:') !== \false) {
                $patterns[] = new SkippedPattern($rawPattern, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // regex pattern, handled else-where
            if (PatternAnalyzer::isRegex($rawPattern)) {
                $patterns[] = new RegexPattern($rawPattern, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // handled in pattern one
            if (PatternAnalyzer::isValuePattern($rawPattern)) {
                //  if (str_contains($rawPattern, ':')) {
                $patterns[] = new NamedPattern($rawPattern, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // remove \/ escape from pattern
            $rawPattern = str_replace('\/', '/', $rawPattern);
            $patterns[] = new ExactPattern($rawPattern, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
        }
        return new PatternCollection($patterns);
    }
    /**
     * @param SplFileInfo[] $fileInfos
     * @return ContextDefinition[]
     */
    private function resolvePatternsFromFiles(array $fileInfos): array
    {
        $classMethodContextDefinitions = [];
        foreach ($fileInfos as $fileInfo) {
            $stmts = $this->simplePhpParser->parseFilePath($fileInfo->getRealPath());
            // 1. get class name
            $class = $this->nodeFinder->findFirstInstanceOf($stmts, Class_::class);
            if (!$class instanceof Class_) {
                continue;
            }
            // is magic class?
            if ($class->isAnonymous()) {
                continue;
            }
            if (!$class->namespacedName instanceof Name) {
                continue;
            }
            $className = $class->namespacedName->toString();
            foreach ($class->getMethods() as $classMethod) {
                $rawPatterns = $this->classMethodPatternResolver->resolve($classMethod);
                foreach ($rawPatterns as $rawPattern) {
                    $classMethodContextDefinitions[] = new ContextDefinition($fileInfo->getRealPath(), $className, $classMethod->name->toString(), $rawPattern, $classMethod->getStartLine());
                }
            }
        }
        return $classMethodContextDefinitions;
    }
}
