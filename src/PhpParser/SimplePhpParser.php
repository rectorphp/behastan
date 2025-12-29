<?php

declare (strict_types=1);
namespace Rector\Behastan\PhpParser;

use Behastan202512\Entropy\Utils\FileSystem;
use Behastan202512\PhpParser\Node\Stmt;
use Behastan202512\PhpParser\NodeTraverser;
use Behastan202512\PhpParser\NodeVisitor\NameResolver;
use Behastan202512\PhpParser\Parser;
use Behastan202512\PhpParser\ParserFactory;
use Webmozart\Assert\Assert;
final class SimplePhpParser
{
    /**
     * @readonly
     * @var \PhpParser\Parser
     */
    private $phpParser;
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
