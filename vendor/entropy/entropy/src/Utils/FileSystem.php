<?php

declare (strict_types=1);
namespace Behastan202601\Entropy\Utils;

use Webmozart\Assert\Assert;
/**
 * @api public api to use
 */
final class FileSystem
{
    public static function read(string $filePath): string
    {
        Assert::fileExists($filePath);
        $fileContents = file_get_contents($filePath);
        Assert::notFalse($fileContents, sprintf('Failed to read the "%s" file', $filePath));
        return $fileContents;
    }
    public static function delete(string $fileOrDirectory): void
    {
        if (is_dir($fileOrDirectory)) {
            $files = array_diff(scandir($fileOrDirectory, \SCANDIR_SORT_NONE), ['.', '..']);
            foreach ($files as $file) {
                self::delete($fileOrDirectory . \DIRECTORY_SEPARATOR . $file);
            }
            rmdir($fileOrDirectory);
        } elseif (is_file($fileOrDirectory)) {
            unlink($fileOrDirectory);
        }
    }
    /**
     * @return array<string, mixed>
     */
    public static function loadFileToJson(string $filePath): array
    {
        $fileContents = self::read($filePath);
        return json_decode($fileContents, \true, 512, 0);
    }
}
