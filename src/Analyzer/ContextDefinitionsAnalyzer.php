<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

use Jack202512\PhpParser\Node\Name;
use Jack202512\PhpParser\Node\Stmt\Class_;
use Jack202512\PhpParser\Node\Stmt\ClassMethod;
use Jack202512\PhpParser\NodeFinder;
use Jack202512\PhpParser\PrettyPrinter\Standard;
use Rector\Behastan\PhpParser\SimplePhpParser;
use Rector\Behastan\Resolver\ClassMethodMasksResolver;
use Rector\Behastan\ValueObject\ContextDefinition;
use Jack202512\Symfony\Component\Finder\SplFileInfo;
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
     * @var \Rector\Behastan\Resolver\ClassMethodMasksResolver
     */
    private $classMethodMasksResolver;
    /**
     * @var array<string, array<string, ContextDefinition[]>>
     */
    private $contextDefinitionsByContentHash = [];
    public function __construct(SimplePhpParser $simplePhpParser, NodeFinder $nodeFinder, Standard $printerStandard, ClassMethodMasksResolver $classMethodMasksResolver)
    {
        $this->simplePhpParser = $simplePhpParser;
        $this->nodeFinder = $nodeFinder;
        $this->printerStandard = $printerStandard;
        $this->classMethodMasksResolver = $classMethodMasksResolver;
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
                $rawMasks = $this->classMethodMasksResolver->resolve($classMethod);
                // no masks :(
                if ($rawMasks === []) {
                    continue;
                }
                $contextDefinition = new ContextDefinition(
                    $contextFileInfo->getRealPath(),
                    $className,
                    $classMethod->name->toString(),
                    // @todo what about multiple masks?
                    $rawMasks[0],
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
