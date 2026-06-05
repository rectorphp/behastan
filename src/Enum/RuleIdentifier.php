<?php

declare (strict_types=1);
namespace Rector\Behastan\Enum;

final class RuleIdentifier
{
    /**
     * @var string
     */
    public const DUPLICATED_CONTENTS = 'duplicated-contents';
    /**
     * @var string
     */
    public const DUPLICATED_SCENARIO_TITLES = 'duplicated-scenario-titles';
    /**
     * @var string
     */
    public const DUPLICATED_PATTERNS = 'duplicated-patterns';
    /**
     * @var string
     */
    public const UNUSED_DEFINITIONS = 'unused-definitions';
    /**
     * @var string
     */
    public const REDUNDANT_REGEX_DEFINITION = 'redundant-regex-definition';
}
