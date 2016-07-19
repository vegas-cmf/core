<?php
/**
 * This file is part of Vegas package
 *
 * @author Slawomir Zytko <slawek@amsterdam-standard.pl>
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
     * @param $content
     * @param bool $compareContents     Determines if new content should be compared with the
     *                                  current file content. When contents are the same, then
     *                                  new content will not be written to the file.
     * @return int                      Number of bytes that were written to the file
     */
    public static function write($filePath, $content, $compareContents = false)
    {
        if ($compareContents && self::compareContents($filePath, $content)) {
            return 0;
        }

        return file_put_contents($filePath, $content);
    }

    /**
     * Writes string representation of PHP object into plain file
     *
     * @param $filePath
     * @param $object
     * @param bool $compareContents     Determines if new content should be compared with the
     *                                  current file content. When contents are the same, then
     *                                  new content will not be written to the file.
     * @return int                      Number of bytes that were written to the file
     */
    public static function writeObject($filePath, $object, $compareContents = false)
    {
        $content = '<?php return ' . var_export($object, true) . ';';
        return self::write($filePath, $content, $compareContents);
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
