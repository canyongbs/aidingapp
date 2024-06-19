<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class BuildAssets extends Command
{
    protected $signature = 'app:build-assets {script?}';

    protected $description = 'Builds all assets, including custom CSS from the database.';

    public function handle()
    {
        $script = $this->argument('script');
        $script = filled($script) ? "build:{$script}" : 'build';

        $process = Process::timeout(3600)
            ->run(
                command: <<<BASH
                    #!/bin/bash
                    [ -s "/usr/local/nvm/nvm.sh" ] && \. "/usr/local/nvm/nvm.sh"
                    npm run {$script}
                BASH,
                output: function (string $type, string $output) {
                    $this->line($output);
                }
            )
            ->throw();

        $this->line($process->output());

        $this->info('Assets have been built.');

        return static::SUCCESS;
    }
}
