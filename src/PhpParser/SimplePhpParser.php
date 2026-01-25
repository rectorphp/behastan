<?php

declare (strict_types=1);
namespace Rector\Behastan\PhpParser;

use Behastan202601\Entropy\Utils\FileSystem;
use Behastan202601\PhpParser\Node\Stmt;
use Behastan202601\PhpParser\NodeTraverser;
use Behastan202601\PhpParser\NodeVisitor\NameResolver;
use Behastan202601\PhpParser\Parser;
use Behastan202601\PhpParser\ParserFactory;
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
