<?php

/**
 * Launch from --snippet=my
 */
class CrontabControllerSnippet extends modCrontabController
{
    protected $signature = 'snippet {--snippet}'; // no required arguments


    public function process()
    {
        $snippet = $this->getArgument('snippet');

        $properties = [];
        if (empty($snippet)) {
            if ($Snippet = $this->snippet()) {
                $snippet = $Snippet->get('name');
                $properties = $Snippet->getProperties();
            }
        }

        if (!$snippet) {
            $this->error('No snippet specified (--snippet=my)');
        } else {
            $this->print_msg('Snippet: '.$snippet);

            // Параметры, которые могут передаваться в сниппет
            // Запуск сниппета
            $output = $this->modx->runSnippet($snippet, $properties);
            $out = explode(PHP_EOL, $output);


            if (!empty($out)) {
                $last = substr($output, -1);
                if (!empty($out)) {
                    foreach ($out as $item) {
                        $this->print_msg($item);
                    }
                }

                if ($last == 1 || $last == "1" || $last == "true") {
                    $this->success('OK');
                } else {
                    $this->error('[Error] snippet return: '.$last);
                    $this->error('To succeed, the snippet must return: return 1; or true');
                    exit();
                }
            }
            $this->print_msg('Завершилось');
        }
    }
}
