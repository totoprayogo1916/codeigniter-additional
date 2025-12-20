<?php

namespace Totoprayogo1916\Additionals\CodeIgniter\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;

class SessionsClear extends BaseCommand
{
    protected $group       = 'Housekeeping';
    protected $name        = 'session:clear';
    protected $description = 'Hapus data session (support --force dan --expired)';

    protected $usage = 'session:clear [options]';
    protected $options = [
        '--force'   => 'Wajib. Konfirmasi penghapusan session',
        '--expired' => 'Hanya hapus session yang sudah expired',
    ];

    public function run(array $params)
    {
        $force   = array_key_exists('force', $params);
        $expired = array_key_exists('expired', $params);

        if (! $force) {
            CLI::error('Gunakan --force untuk menjalankan command ini');
            CLI::write('Contoh:', 'yellow');
            CLI::write('php spark session:clear --force');
            CLI::write('php spark session:clear --expired --force');
            return;
        }

        $driver = config('Session')->driver;

        CLI::write('Session driver : ' . $driver, 'yellow');
        CLI::write('Mode           : ' . ($expired ? 'Expired only' : 'ALL'), 'yellow');

        switch ($driver) {

            case 'CodeIgniter\Session\Handlers\FileHandler':
                $this->clearFileSession($expired);
                break;

            case 'CodeIgniter\Session\Handlers\DatabaseHandler':
                $this->clearDatabaseSession($expired);
                break;

            case 'CodeIgniter\Session\Handlers\RedisHandler':
                $this->clearRedisSession($expired);
                break;

            default:
                CLI::error('Driver session tidak didukung');
                return;
        }

        CLI::write('✔ Session cleanup selesai', 'green');
    }

    /* =====================================================
     * FILE SESSION
     * ===================================================== */
    protected function clearFileSession(bool $expired)
    {
        $path     = config('Session')->savePath;
        $lifetime = config('Session')->expiration;
        $prefix = config('Session')->cookieName;

        $now   = time();
        $count = 0;

        foreach (glob($path . '/' . $prefix . '*') as $file) {

            if ($expired) {
                if (($now - filemtime($file)) < $lifetime) {
                    continue;
                }
            }

            @unlink($file);
            $count++;
        }

        CLI::write("File session dihapus: {$count}");
    }

    /* =====================================================
     * DATABASE SESSION
     * ===================================================== */
    protected function clearDatabaseSession(bool $expired)
    {
        $db    = db_connect();
        $table = config('Session')->savePath;

        if ($expired) {
            $db->query("
                DELETE FROM {$table}
                WHERE timestamp < UNIX_TIMESTAMP() - ?
            ", [config('Session')->expiration]);

            $count = $db->affectedRows();
        } else {
            $db->table($table)->truncate();
            $count = 'ALL';
        }

        CLI::write("Database session dihapus: {$count}");
    }

    /* =====================================================
     * REDIS SESSION
     * ===================================================== */
    protected function clearRedisSession(bool $expired)
    {
        $redis  = Services::redis();
        $prefix = config('Session')->cookieName ?? 'ci_session';
        $keys   = $redis->keys($prefix . '*');

        $count = 0;

        foreach ($keys as $key) {

            if ($expired && $redis->ttl($key) > 0) {
                continue;
            }

            $redis->del($key);
            $count++;
        }

        CLI::write("Redis session dihapus: {$count}");
    }
}
