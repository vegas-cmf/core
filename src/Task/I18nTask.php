<?php

namespace Vegas\Task;

use Vegas\Cli\Exception;
use Vegas\Cli\Task\Action;
use Vegas\Cli\TaskAbstract;

/**
 * `php cli/cli.php vegas:i18n generate`
 *
 * Task will generate "{langDir}/{lang}/LC_MESSAGES/messages.po" file for each `$langs` language item.
 * If final file exists, overwrite it by adding new translations, but without modify translated items.
 *
 * Task requires xgettext installed, node.js and xgettext-template (as global), see:
 * - https://www.gnu.org/software/gettext/
 * - https://www.npmjs.com/package/xgettext-template
 *
 * Supported file types:
 * - PHP
 * - VOLT
 *
 * HOWTO:
 * 1) Extend I18nTask into Your app
 * 2) Overwrite `$directories`, `$langDir` and `$langs` (+ other params if needed)
 * 3) Overwrite `addToFinalPo(PO_FILE_PATH);` to add database translation into final *.PO
 *  You may use `generatePoContent` method to generate PO content
 *  from array of string or array [msgid,?msgstr,?comment] where "?" keys are optional (see sample below)
 * 4) Use "POEdit" to manage translations
 *
 *
 * Sample of `addToFinalPo` using:
 * ```
 * protected function addToFinalPo($tmpPo)
 * {
 * $tmpPo = $this->getTmpFile();
 *
 * file_put_contents($tmpPo, $this->generatePoContent([
 * 'message1', 'other message', 'dynamic %s message'
 * ]));
 *
 * $this->addPo($tmpPo, $tmpPo);
 * }
 * ```
 *
 * If You are using non-utf8 standard charset, consider adding own header into PO files.
 */
class I18nTask extends TaskAbstract
{
    protected $xgettext = 'xgettext';
    protected $xgettextTemplate = 'xgettext-template';
    protected $msgcat = 'msgcat';

    protected $encoding = 'UTF-8';
    protected $directories = [
        APP_ROOT . '/app/modules',
        APP_ROOT . '/app/layouts',
    ];
    protected $langDir = APP_ROOT . '/lang';
    protected $langs = [
        'nl_NL.utf8'
    ];
    protected $textKeywords = [
        '_'
    ];
    protected $templateKeywords = [
        'i18n._', '_'
    ];

    private $parser = '{xgettext} --omit-header --no-wrap --language="{lang}" --from-code="{encoding}" -k"{keys}" -j -o"{out}" {in}';
    private $templateParser = '{xgettextTemplate} --force-po=false --language="{lang}" --from-code="{encoding}" --keyword="{keys}" --output="{out}" {in}';
    private $mergePoCmd = '{msgcat} --force-po --no-wrap --use-first {extra} {base} -o {base} 2>/dev/null';

    public function setupOptions()
    {
        $action = new Action('generate', 'Generate *.PO files for each lang');
        $this->addTaskAction($action);
    }

    public function generateAction()
    {
        if ( ! $this->isInstalled($this->xgettext) ) {
            $this->throwError("xgettext not installed : https://www.gnu.org/software/gettext/");
        }
        if ( ! $this->isInstalled($this->msgcat) ) {
            $this->throwError("msgcat not installed");
        }
        if ( ! $this->isInstalled($this->xgettextTemplate) ) {
            $this->throwError("xgettext-template not installed : https://www.npmjs.com/package/xgettext-template");
        }

        $textTmpPo = $this->getTmpFile();
        $templateTmpPo = $this->getTmpFile();

        foreach ($this->directories as $directory) {
            $directoryIterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $directory,
                    \RecursiveDirectoryIterator::SKIP_DOTS
                ),
                \RecursiveIteratorIterator::SELF_FIRST,
                \RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            $phpTmpPo = $this->getTmpFile();
            $voltTmpPo = $this->getTmpFile();

            echo "Scanning $directory\n";

            foreach ($directoryIterator as $file=>$info) {
                if ( $this->isFile($file, 'php') ) {
                    $this->exec($this->getTextParser('PHP', $file, $phpTmpPo));
                } elseif ( $this->isFile($file, 'volt') ) {
                    $tmpPo = $this->getTmpFile();
                    $this->exec($this->getTemplateParser('Volt', $file, $tmpPo));

                    $lines = count( file( $tmpPo ));
                    if ($lines > 2) {
                        $this->removeTopLines($tmpPo, 2);

                        $this->addPo($voltTmpPo, $tmpPo);
                    } else {
                        unlink($tmpPo);
                    }
                }
            }

            $this->addPo($textTmpPo, $phpTmpPo);
            $this->addPo($templateTmpPo, $voltTmpPo);
        }

        echo "Preparing final files...\n";

        $this->addPo($textTmpPo, $templateTmpPo);

        $this->addToFinalPo($textTmpPo);

        $langPos = $this->savePo($textTmpPo);

        echo "\nGenerated files:";
        foreach ($langPos as $langPo) {
            echo "\n$langPo";
        }

    }

    /**
     * @param string $tmpPo generated PO file
     */
    protected function addToFinalPo($tmpPo)
    {

    }

    /**
     * @param string[]|array $items string|[msgid,?msgstr,?comment]
     * @return string
     */
    protected function generatePoContent(array $items)
    {
        $poText = '';

        foreach ($items as $item) {
            if (is_array($item) && isset($item['msgid'])) {
                if (isset($item['comment'])) {
                    $poText .= sprintf(
                        "\n#: %s",
                        $item['comment']
                    );
                }
                $poText .= sprintf(
                    "\nmsgid \"%s\"\nmsgstr \"%s\"\n",
                    $item['msgid'],
                    ( isset($item['msgstr']) ) ? $item['msgstr'] : ''
                );
            } elseif (is_string($item)) {
                $poText .= sprintf(
                    "\nmsgid \"%s\"\nmsgstr \"\"\n",
                    $item
                );
            }
        }

        return $poText;
    }

    /**
     * merge new PO file and remove it
     * @param string $basePo
     * @param string $newPo
     */
    protected function addPo($basePo, $newPo)
    {
        $this->exec($this->getMsgCat($newPo, $basePo));
        unlink($newPo);
    }

    /**
     * @param string $poFile
     * @return string[]
     */
    protected function savePo($poFile)
    {
        $files = [];

        foreach ($this->langs as $lang) {
            $poBaseFile = $this->langDir . "/$lang/LC_MESSAGES/messages.po";

            if (!is_file($poBaseFile)) {
                mkdir( dirname($poBaseFile), 0777, true );
                touch($poBaseFile);
            }

            $this->addPo($poBaseFile, $poFile);

            $files[] = $poBaseFile;
        }

        return $files;
    }

    protected function getMsgCat($extra, $base)
    {
        return str_replace(
            [
                '{msgcat}',
                '{extra}',
                '{base}'
            ],
            [
                $this->msgcat,
                $extra,
                $base
            ],
            $this->mergePoCmd
        );
    }

    /**
     * @param string $lang PHP
     * @param string $in filename
     * @param string $out filename
     * @return string command
     */
    protected function getTemplateParser($lang, $in, $out)
    {
        return str_replace(
            [
                '{xgettextTemplate}',
                '{lang}',
                '{encoding}',
                '{keys}',
                '{out}',
                '{in}'
            ],
            [
                $this->xgettextTemplate,
                $lang,
                $this->encoding,
                implode(',', $this->templateKeywords),
                $out,
                $in
            ],
            $this->templateParser
        );
    }

    /**
     * @param string $lang PHP
     * @param string $in filename
     * @param string $out filename
     * @return string command
     */
    protected function getTextParser($lang, $in, $out)
    {
        return str_replace(
            [
                '{xgettext}',
                '{lang}',
                '{encoding}',
                '{keys}',
                '{out}',
                '{in}'
            ],
            [
                $this->xgettext,
                $lang,
                $this->encoding,
                implode(',', $this->textKeywords),
                $out,
                $in
            ],
            $this->parser
        );
    }

    protected function getTmpFile($file=null)
    {
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . ($file ?: uniqid());
        touch($tmpFile);
        if (!is_file($tmpFile))
            $this->throwError("Cant create $tmpFile from $file");

        return $tmpFile;
    }

    protected function isInstalled($cmd)
    {
        return strlen($this->exec("command -v $cmd")) > 0;
    }

    protected function exec($cmd)
    {
        return shell_exec($cmd);
    }

    protected function isFile($filename, $ext)
    {
        return is_file($filename) && preg_match('/^.+\.'.$ext.'$/i', $filename);
    }

    private function removeTopLines($filename, $lines=0)
    {
        if ($lines<1)
            return false;

        $content = file_get_contents($filename);
        for ($i=0;$i<$lines;$i++) {
            $content = preg_replace('/^.+\n/', '', $content);
        }
        file_put_contents($filename, $content);
    }

}