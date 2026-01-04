<?php

declare (strict_types=1);
namespace Rector\Behastan\Resolver;

use Behastan202601\PhpParser\Comment\Doc;
use Behastan202601\PhpParser\Node\Scalar\String_;
use Behastan202601\PhpParser\Node\Stmt\ClassMethod;
final class ClassMethodPatternResolver
{
    /**
     * @var string
     */
    private const INSTRUCTION_DOCBLOCK_REGEX = '#\@(Given|Then|When)\s+(?<instruction>.*?)\n#m';
    /**
     * @return string[]
     */
    public function resolve(ClassMethod $classMethod): array
    {
        $rawPatterns = [];
        // 1. collect from docblock
        if ($classMethod->getDocComment() instanceof Doc) {
            preg_match_all(self::INSTRUCTION_DOCBLOCK_REGEX, $classMethod->getDocComment()->getText(), $match);
            foreach ($match['instruction'] as $instruction) {
                $rawPatterns[] = $this->clearPattern($instruction);
            }
        }
        // 2. collect from attributes
        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attributeName = $attr->name->toString();
                /** @see https://github.com/Behat/Behat/tree/3.x/src/Behat/Step */
                if (strncmp($attributeName, 'Behat\Step', strlen('Behat\Step')) !== 0) {
                    continue;
                }
                $firstArgValue = $attr->args[0]->value;
                if (!$firstArgValue instanceof String_) {
                    continue;
                }
                $rawPatterns[] = $firstArgValue->value;
            }
        }
        return $rawPatterns;
    }
    private function clearPattern(string $pattern): string
    {
        $pattern = trim($pattern);
        // clear extra quote escaping that would cause miss-match with feature patterns
        $pattern = str_replace('\\\'', "'", $pattern);
        return str_replace('\/', '/', $pattern);
    }
}
