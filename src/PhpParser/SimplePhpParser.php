<?php

declare(strict_types=1);

namespace Rector\Behastan\PhpParser;

use Entropy\Utils\FileSystem;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Webmozart\Assert\Assert;

final readonly class SimplePhpParser
{
    private Parser $phpParser;

    public function __construct()
    {
        $this->phpParser = (new ParserFactory())->createForHostVersion();
    }

    /**
     * @return Stmt[]
     */
    public function parseFilePath(string $filePath): array
    {
        $fileContents = FileSystem::read($filePath);

        $stmts = $this->phpParser->parse($fileContents);
        Assert::isArray($stmts);

        $nameNodeTraverser = new NodeTraverser();
        $nameNodeTraverser->addVisitor(new NameResolver());
        $nameNodeTraverser->traverse($stmts);

        return $stmts;
    }
}
