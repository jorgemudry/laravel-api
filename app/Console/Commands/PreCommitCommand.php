<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Throwable;

class PreCommitCommand extends Command
{
    protected const string GIT_DIFF_COMMAND = 'git diff --diff-filter=ACMR --name-only --cached  | grep \'\.php$\'';

    protected const string GIT_ADD_COMMAND = 'git add %s';

    protected const string PHP_LINT_COMMAND = 'php -l %s';

    protected const string PINT_COMMAND = '%s/vendor/bin/pint %s';

    protected const string PHP_STAN_COMMAND = '%s/vendor/bin/phpstan analyse %s --memory-limit=256M';

    protected const string PEST_COMMAND = './vendor/bin/pest --parallel';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dev:precommit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute checks and tests on all the PHP files to be commited.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $files = array_filter(explode("\n", Process::run(self::GIT_DIFF_COMMAND)->output()));

        if (count($files) === 0) {
            $this->components->info('Nothing to process.');

            return Command::SUCCESS;
        }

        try {
            $this->components->info('Running PHP Lint...');
            $this->runPhpLint($files);
            echo "\n";

            $this->components->info('Running Laravel Pint...');
            $this->runPint($files);
            echo "\n";

            $this->components->info('Running PHP Stan...');
            $this->runPhpStan($files);
            echo "\n";
        } catch (Throwable $th) {
            echo "\n";
            $this->components->error($th->getMessage());

            return Command::FAILURE;
        }

        $this->components->info('Running Unit/Feature tests...');

        $process = Process::path(base_path())
            ->env([
                'APP_ENV' => 'testing',
                'BCRYPT_ROUNDS' => '4',
                'CACHE_DRIVER' => 'array',
                'DB_CONNECTION' => 'sqlite',
                'DB_DATABASE' => ':memory:',
                'MAIL_MAILER' => 'array',
                'QUEUE_CONNECTION' => 'sync',
                'SESSION_DRIVER' => 'array',
                'TELESCOPE_ENABLED' => 'false',
            ])->run(self::PEST_COMMAND);

        $this->info($process->output());

        return $process->exitCode() ?? Command::FAILURE;
    }

    /**
     * @param array<int, string> $files
     */
    protected function runPhpLint(array $files): void
    {
        foreach ($files as $file) {
            $this->components->task($file, function () use ($file) {
                $process = Process::run(sprintf(self::PHP_LINT_COMMAND, $file));

                if ($process->failed()) {
                    throw new Exception($process->errorOutput());
                }

                return true;
            });
        }
    }

    /**
     * @param array<int, string> $files
     */
    protected function runPint(array $files): void
    {
        foreach ($files as $file) {
            $this->components->task($file, function () use ($file) {
                $base = mb_rtrim(base_path(), '/');
                $process = Process::run(sprintf(self::PINT_COMMAND, $base, $file));

                if ($process->failed()) {
                    throw new Exception($process->errorOutput());
                }

                Process::run(sprintf(self::GIT_ADD_COMMAND, $file));

                return true;
            });
        }
    }

    /**
     * @param array<int, string> $files
     */
    protected function runPhpStan(array $files): void
    {
        foreach ($files as $file) {
            $this->components->task($file, function () use ($file) {
                $base = mb_rtrim(base_path(), '/');
                $process = Process::run(sprintf(self::PHP_STAN_COMMAND, $base, $file));

                if ($process->failed()) {
                    throw new Exception($process->errorOutput());
                }

                return true;
            });
        }
    }
}
