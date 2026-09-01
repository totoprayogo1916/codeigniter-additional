<?php

namespace Totoprayogo1916\Additionals\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class DBWipe extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'db:wipe';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Drop all tables and views';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'db:wipe';

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
        $baseConnection = Database::connect();
        $tables         = $baseConnection->listTables();
        $forge          = Database::forge();

        foreach ($tables as $table) {
            $query = $baseConnection->query(
                'SHOW FULL TABLES WHERE Tables_in_' . $baseConnection->database . ' = ?',
                [$table]
            );

            $result = $query->getRowArray();

            if ($result === null) {
                continue;
            }

            $tableType = end($result);

            if ($tableType === 'VIEW') {
                $baseConnection->query(
                    'DROP VIEW IF EXISTS `' . str_replace('`', '``', $table) . '`'
                );

                CLI::write('Dropped view: ' . $table);
            } else {
                $forge->dropTable($table, true);

                CLI::write('Dropped table: ' . $table);
            }
        }

        CLI::write(CLI::color('Dropped all tables and views successfully.', 'green'));
    }
}
