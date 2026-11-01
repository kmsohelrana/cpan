<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class TestOracleConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oracle:test {connection=oracle : The name of the Oracle connection to test (e.g. oracle, oracle2, oracle3)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connectivity to an Oracle database connection';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connectionName = $this->argument('connection');
        $connections = config('database.connections');

        if (!isset($connections[$connectionName])) {
            $this->error("Connection [{$connectionName}] is not defined in config/database.php or config/oracle.php.");
            $this->line('Available connections: ' . implode(', ', array_keys($connections)));
            return self::FAILURE;
        }

        $config = $connections[$connectionName];

        if (($config['driver'] ?? '') !== 'oracle') {
            $this->warn("Warning: Connection [{$connectionName}] driver is '{$config['driver']}', not 'oracle'.");
        }

        $this->info("--------------------------------------------------");
        $this->info(" Testing Oracle Connection: [{$connectionName}]");
        $this->info("--------------------------------------------------");
        $this->table(
            ['Setting', 'Configured Value'],
            [
                ['Driver', $config['driver'] ?? 'N/A'],
                ['Host', $config['host'] ?? 'N/A'],
                ['Port', $config['port'] ?? '1521'],
                ['Database / SID', $config['database'] ?: ($config['service_name'] ?? 'N/A')],
                ['Service Name', $config['service_name'] ?: 'N/A'],
                ['Username', $config['username'] ?: '(empty)'],
                ['Charset', $config['charset'] ?? 'AL32UTF8'],
                ['Schema', $config['schema'] ?: '(default)'],
                ['TNS', $config['tns'] ?: '(none)'],
            ]
        );

        $this->line('Attempting to connect...');

        try {
            $start = microtime(true);
            $conn = DB::connection($connectionName);
            $conn->getPdo();

            // Run lightweight verification query
            $ping = $conn->selectOne('SELECT 1 AS PING FROM DUAL');
            $latencyMs = round((microtime(true) - $start) * 1000, 2);

            $version = 'Unknown';
            try {
                $versionRow = $conn->selectOne('SELECT BANNER AS VERSION FROM V$VERSION WHERE ROWNUM = 1');
                $version = $versionRow->version ?? ($versionRow->VERSION ?? 'Unknown');
            } catch (Throwable) {
                // Non-DBA user might not have access to V$VERSION
            }

            $currentUser = 'Unknown';
            try {
                $userRow = $conn->selectOne('SELECT USER AS CURRENT_USER FROM DUAL');
                $currentUser = $userRow->current_user ?? ($userRow->CURRENT_USER ?? 'Unknown');
            } catch (Throwable) {
                // Ignore
            }

            $this->newLine();
            $this->info(" Connection SUCCESSFUL!");
            $this->line("   - Response Latency : {$latencyMs} ms");
            $this->line("   - Connected As     : {$currentUser}");
            $this->line("   - Server Version   : {$version}");
            $this->newLine();

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error(" Connection FAILED!");
            $this->line("Error Message: " . $e->getMessage());
            $this->newLine();
            $this->line("Troubleshooting Tips:");
            $this->line(" 1. Check if Oracle Listener is running on port {$config['port']}.");
            $this->line(" 2. Verify HOST, SERVICE_NAME/SID, USERNAME, and PASSWORD in your .env.");
            $this->line(" 3. If connecting via TNS, verify ORACLE_DB_TNS in your .env.");
            $this->line(" 4. Verify Oracle Instant Client (oci.dll) is on system PATH.");
            $this->newLine();

            return self::FAILURE;
        }
    }
}

