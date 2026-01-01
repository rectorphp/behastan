<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

use Behastan202601\PhpParser\Node\Name;
use Behastan202601\PhpParser\Node\Stmt\Class_;
use Behastan202601\PhpParser\Node\Stmt\ClassMethod;
use Behastan202601\PhpParser\NodeFinder;
use Behastan202601\PhpParser\PrettyPrinter\Standard;
use Rector\Behastan\PhpParser\SimplePhpParser;
use Rector\Behastan\Resolver\ClassMethodPatternResolver;
use Rector\Behastan\ValueObject\ContextDefinition;
use Behastan202601\Symfony\Component\Finder\SplFileInfo;
final class ContextDefinitionsAnalyzer
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
     * @var \PhpParser\PrettyPrinter\Standard
     */
    private $printerStandard;
    /**
     * @readonly
     * @var \Rector\Behastan\Resolver\ClassMethodPatternResolver
     */
    private $classMethodPatternResolver;
    /**
     * @var array<string, array<string, ContextDefinition[]>>
     */
    private $contextDefinitionsByContentHash = [];
    public function __construct(SimplePhpParser $simplePhpParser, NodeFinder $nodeFinder, Standard $printerStandard, ClassMethodPatternResolver $classMethodPatternResolver)
    {
        $this->simplePhpParser = $simplePhpParser;
        $this->nodeFinder = $nodeFinder;
        $this->printerStandard = $printerStandard;
        $this->classMethodPatternResolver = $classMethodPatternResolver;
    }
    /**
     * @param SplFileInfo[] $contextFileInfos
     * @return ContextDefinition[]
     */
    public function resolve(array $contextFileInfos): array
    {
        $contextDefinitionByClassMethodHash = $this->resolveAndGroupByContentHash($contextFileInfos);
        $allContextDefinitions = [];
        foreach ($contextDefinitionByClassMethodHash as $contextDefinition) {
            $allContextDefinitions = array_merge($allContextDefinitions, $contextDefinition);
        }
        return $allContextDefinitions;
    }
    /**
     * @param SplFileInfo[] $contextFileInfos
     * @return array<string, ContextDefinition[]>
     */
    public function resolveAndGroupByContentHash(array $contextFileInfos): array
    {
        // re-use cached result if already done
        $cacheKey = sha1((string) json_encode($contextFileInfos));
        if (isset($this->contextDefinitionsByContentHash[$cacheKey])) {
            return $this->contextDefinitionsByContentHash[$cacheKey];
        }
        $contextDefinitionByContentsHash = [];
        foreach ($contextFileInfos as $contextFileInfo) {
            $contextClassStmts = $this->simplePhpParser->parseFilePath($contextFileInfo->getRealPath());
            $class = $this->nodeFinder->findFirstInstanceOf($contextClassStmts, Class_::class);
            if (!$class instanceof Class_) {
                continue;
            }
            if (!$class->namespacedName instanceof Name) {
                continue;
            }
            $className = $class->namespacedName->toString();
            foreach ($class->getMethods() as $classMethod) {
                if (!$classMethod->isPublic()) {
                    continue;
                }
                if ($classMethod->isMagic()) {
                    continue;
                }
                $classMethodHash = $this->createClassMethodHash($classMethod);
                $rawPatterns = $this->classMethodPatternResolver->resolve($classMethod);
                // no patterns found :(
                if ($rawPatterns === []) {
                    continue;
                }
                $contextDefinition = new ContextDefinition(
                    $contextFileInfo->getRealPath(),
                    $className,
                    $classMethod->name->toString(),
                    // @todo what about multiple patterns?
                    $rawPatterns[0],
                    $classMethod->getStartLine()
                );
                $contextDefinitionByContentsHash[$classMethodHash][] = $contextDefinition;
            }
        }
        $this->contextDefinitionsByContentHash[$cacheKey] = $contextDefinitionByContentsHash;
        return $contextDefinitionByContentsHash;
    }
    private function createClassMethodHash(ClassMethod $classMethod): string
    {
        $printedClassMethod = $this->printerStandard->prettyPrint((array) $classMethod->stmts);
        return sha1($printedClassMethod);
    }
}
