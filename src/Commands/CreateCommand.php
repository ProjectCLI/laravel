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

class CreateCommand extends Command
{

    /** @var string */
    protected static $defaultName = 'laravel:create';

    /** @var string */
    protected $description = 'Create a new Laravel project.';


    public function handle(Docker $docker) : void
    {
        $src = Helpers::projectPath('src');

        if ( ! file_exists($src . '/.env')) {
            touch($src . '/.env');
        }

        $this->call('composer', ['create-project', 'laravel/laravel', '_setup']);

        $tmp = Helpers::projectPath('temp/cp');

        mkdir($tmp, 0755, true);

        Helpers::recursiveCopy("{$src}/_setup", $tmp);

        Helpers::rmdir($src);
        Helpers::recursiveCopy($tmp, $src);
        Helpers::rmdir($tmp);

        $this->call('restart');
        $this->info('Project successfully created');
    }

    public static function isActive() : bool
    {
        return PROJECT_IS_INSIDE
            && Helpers::isProjectType('php')
            && ! file_exists(Helpers::projectPath('src/artisan'));
    }

}
