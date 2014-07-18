<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawomir.zytko@gmail.com>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://bitbucket.org/amsdard/vegas-phalcon
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace Vegas\Util;

/**
 * Simple File Writer
 *
 * Class FileWriter
 * @package Vegas\Util
 */
class FileWriter
{
    /**
     * Writes string content to a file
     *
     * @param $filePath
     * @param $newContent
     * @param bool $compareContents     Determines if new content should be compared with the
     *                                  current file content. When contents are the same, then
     *                                  new content will not be written to the file.
     * @return int                      Number of bytes that were written to the file
     */
    public static function write($filePath, $newContent, $compareContents = false)
    {
        if ($compareContents) {
            if (self::compareContents($filePath, $newContent)) {
                return 0;
            }
        }

        return file_put_contents($filePath, $newContent);
    }

    /**
     * Compares file contents
     *
     * @param $filePath
     * @param $newContent
     * @return bool
     * @internal
     */
    private static function compareContents($filePath, $newContent)
    {
        if (file_exists($filePath)) {
            $currentContent = file_get_contents($filePath);
            return strcmp($currentContent, $newContent) === 0;
        }

        return false;
    }
}