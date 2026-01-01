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
        $masks = [];
        $classMethodContextDefinitions = $this->resolveMasksFromFiles($contextFiles);
        foreach ($classMethodContextDefinitions as $classMethodContextDefinition) {
            $rawMask = $classMethodContextDefinition->getMask();
            // @todo edge case - handle next
            if (strpos($rawMask, ' [:') !== \false) {
                $masks[] = new SkippedPattern($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // regex pattern, handled else-where
            if (PatternAnalyzer::isRegex($rawMask)) {
                $masks[] = new RegexPattern($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // handled in mask one
            if (PatternAnalyzer::isValuePattern($rawMask)) {
                //  if (str_contains($rawMask, ':')) {
                $masks[] = new NamedPattern($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // remove \/ escape from mask
            $rawMask = str_replace('\/', '/', $rawMask);
            $masks[] = new ExactPattern($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getMethodLine(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
        }
        return new PatternCollection($masks);
    }
    /**
     * @param SplFileInfo[] $fileInfos
     * @return ContextDefinition[]
     */
    private function resolveMasksFromFiles(array $fileInfos): array
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
                $rawMasks = $this->classMethodPatternResolver->resolve($classMethod);
                foreach ($rawMasks as $rawMask) {
                    $classMethodContextDefinitions[] = new ContextDefinition($fileInfo->getRealPath(), $className, $classMethod->name->toString(), $rawMask, $classMethod->getStartLine());
                }
            }
        }
        return $classMethodContextDefinitions;
    }
}
