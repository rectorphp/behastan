<?php

declare(strict_types=1);

namespace Rector\Behastan\Analyzer;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\PrettyPrinter\Standard;
use Rector\Behastan\PhpParser\SimplePhpParser;
use Rector\Behastan\Resolver\ClassMethodPatternResolver;
use Rector\Behastan\ValueObject\ContextDefinition;
use Symfony\Component\Finder\SplFileInfo;

final class ContextDefinitionsAnalyzer
{
    /**
     * @var array<string, array<string, ContextDefinition[]>>
     */
    private array $contextDefinitionsByContentHash = [];

    public function __construct(
        private readonly SimplePhpParser $simplePhpParser,
        private readonly NodeFinder $nodeFinder,
        private readonly Standard $printerStandard,
        private readonly ClassMethodPatternResolver $classMethodPatternResolver,
    ) {
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
            if (! $class instanceof Class_) {
                continue;
            }

            if (! $class->namespacedName instanceof Name) {
                continue;
            }

            $className = $class->namespacedName->toString();

            foreach ($class->getMethods() as $classMethod) {
                if (! $classMethod->isPublic()) {
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
