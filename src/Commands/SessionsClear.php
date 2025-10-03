<?php

namespace Totoprayogo1916\Additionals\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SessionsClear extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Sessions';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'sessions:clear';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clear all session files.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'sessions:clear';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        helper('directory');

        $sessionPath = WRITEPATH . 'sessions' . DIRECTORY_SEPARATOR;

        if (! is_dir($sessionPath)) {
            CLI::write(CLI::color('Session path does not exist, nothing to clear.', 'yellow'));

            return;
        }

        $files = directory_map($sessionPath, 1);

        if (empty($files)) {
            CLI::write(CLI::color('No session files to clear.', 'green'));

            return;
        }

        $clearedCount = 0;

        foreach ($files as $file) {
            if (is_file($sessionPath . $file)) {
                if ($file === 'index.html' || strpos($file, '.') === 0) {
                    continue;
                }

                if (unlink($sessionPath . $file)) {
                    $clearedCount++;
                }
            }
        }

        CLI::write(CLI::color("Cleared {$clearedCount} session file(s) successfully.", 'green'));
    }
}
