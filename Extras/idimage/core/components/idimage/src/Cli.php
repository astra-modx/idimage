<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 16.03.2025
 * Time: 14:22
 */

namespace IdImage;


use Symfony\Component\Console\Output\ConsoleOutput;

class Cli
{
    private  $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput();
    }

    public function title(string $message): void
    {
        $this->output->writeln("\n\033[1;34m$message\033[0m\n".str_repeat('=', mb_strlen($message))."\n");
    }

    public function info(string $message): void
    {
        $this->output->writeln("\033[1;36m[INFO] $message\033[0m");
    }

    public function success(string $message): void
    {
        $this->output->writeln("\033[1;32m[OK] $message\033[0m");
    }

    public function warning(string $message): void
    {
        $this->output->writeln("\033[1;33m[WARNING] $message\033[0m");
    }

    public function error(string $message): void
    {
        $this->output->writeln("\033[1;31m[ERROR] $message\033[0m");
    }

    public function text(array $messages): void
    {
        foreach ((array)$messages as $message) {
            $this->output->writeln("  $message");
        }
    }
}
