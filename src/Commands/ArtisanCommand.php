<?php

/**
 * Plugin for ProjectCLI. More info at
 * https://github.com/chriha/project-cli
 */

namespace ProjectCLI\Laravel\Commands;

use Chriha\ProjectCLI\Commands\Command;
use Chriha\ProjectCLI\Contracts\Plugin;
use Chriha\ProjectCLI\Helpers;
use Chriha\ProjectCLI\Services\Docker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ArtisanCommand extends Command
{

    /** @var string */
    protected static $defaultName = 'artisan';

    /** @var string */
    protected $description = 'Run artisan commands inside the web container';


    public function configure() : void
    {
        $this->addDynamicArguments()->addDynamicOptions();
    }

    public function handle(Docker $docker) : void
    {
        $docker->exec('web', $this->getParameters(['php', 'artisan']))
            ->setTty(true)
            ->run(
                function ($type, $buffer)
                {
                    $this->output->write($buffer);
                }
            );
    }

    public static function isActive() : bool
    {
        return PROJECT_IS_INSIDE
            && file_exists(Helpers::projectPath('src/artisan'));
    }

}
