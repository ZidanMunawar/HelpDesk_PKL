<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        // Get system health data
        $health = $this->getSystemHealth();

        // Get backup files list
        $backups = $this->getBackupFiles();

        // Get email notification config from storage or default
        $emailConfig = $this->getEmailConfig();

        return view('settings.index', compact('health', 'backups', 'emailConfig'));
    }

    public function refreshCache(Request $request)
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get email notification config from file storage
     */
    private function getEmailConfig()
    {
        $configPath = storage_path('app/email_config.json');
        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true);
            return [
                'email' => $config['email'] ?? 'engreq@maintenancerequest.cfcb.my.id',
                'password' => $config['password'] ?? 'password123'
            ];
        }
        return [
            'email' => 'engreq@maintenancerequest.cfcb.my.id',
            'password' => 'password123'
        ];
    }

    /**
     * Save email notification config (SuperAdmin only)
     */
    public function saveEmailConfig(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'admin_password' => 'required|string'
        ]);

        // Verify admin password
        if (!Hash::check($request->admin_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Admin password is incorrect'
            ], 422);
        }

        $configPath = storage_path('app/email_config.json');
        $config = [
            'email' => $request->email,
            'password' => $request->password,
            'updated_by' => $user->id,
            'updated_at' => now()->toDateTimeString()
        ];

        File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated',
            'description' => "Email notification config updated to: {$request->email}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email notification configuration saved successfully!'
        ]);
    }

    /**
     * Get system health information
     */
    private function getSystemHealth()
    {
        // Database size
        $databaseName = DB::connection()->getDatabaseName();
        $dbSize = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables
            WHERE table_schema = ?", [$databaseName]);
        $databaseSize = $dbSize[0]->size_mb ?? 0;

        // Get table counts
        $tables = DB::select("SELECT table_name, table_rows
            FROM information_schema.tables
            WHERE table_schema = ?
            ORDER BY table_rows DESC", [$databaseName]);

        $totalRecords = 0;
        foreach ($tables as $table) {
            $totalRecords += $table->table_rows ?? 0;
        }

        // Storage usage
        $storagePath = storage_path();
        $storageSize = $this->getDirectorySize($storagePath);

        $publicPath = public_path();
        $publicSize = $this->getDirectorySize($publicPath);

        // PHP Info
        $phpVersion = phpversion();
        $maxExecutionTime = ini_get('max_execution_time');
        $memoryLimit = ini_get('memory_limit');
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        $postMaxSize = ini_get('post_max_size');

        // Laravel Info
        $laravelVersion = app()->version();
        $environment = app()->environment();
        $debugMode = config('app.debug');

        // Database connection status
        $dbStatus = 'Connected';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }

        // Last backup info
        $lastBackup = $this->getLastBackupInfo();

        // Server info
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $serverName = $_SERVER['SERVER_NAME'] ?? 'Unknown';

        return [
            'database' => [
                'name' => $databaseName,
                'size_mb' => $databaseSize,
                'size_display' => $this->formatSize($databaseSize * 1024 * 1024),
                'total_tables' => count($tables),
                'total_records' => $totalRecords,
                'status' => $dbStatus
            ],
            'storage' => [
                'storage_size' => $storageSize,
                'storage_display' => $this->formatSize($storageSize),
                'public_size' => $publicSize,
                'public_display' => $this->formatSize($publicSize),
                'total_size' => $this->formatSize($storageSize + $publicSize)
            ],
            'php' => [
                'version' => $phpVersion,
                'max_execution_time' => $maxExecutionTime,
                'memory_limit' => $memoryLimit,
                'upload_max_filesize' => $uploadMaxFilesize,
                'post_max_size' => $postMaxSize
            ],
            'laravel' => [
                'version' => $laravelVersion,
                'environment' => $environment,
                'debug_mode' => $debugMode ? 'On' : 'Off'
            ],
            'server' => [
                'software' => $serverSoftware,
                'name' => $serverName,
                'last_backup' => $lastBackup
            ]
        ];
    }

    /**
     * Get directory size
     */
    private function getDirectorySize($path)
    {
        $size = 0;
        if (!is_dir($path))
            return $size;

        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    /**
     * Format bytes to human readable
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get backup files list
     */
    private function getBackupFiles()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $files = [];
        foreach (File::files($backupDir) as $file) {
            if ($file->getExtension() === 'sql') {
                $files[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'size_display' => $this->formatSize($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'path' => $file->getPathname()
                ];
            }
        }

        // Sort by created date descending
        usort($files, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $files;
    }

    /**
     * Get last backup info
     */
    private function getLastBackupInfo()
    {
        $backups = $this->getBackupFiles();
        if (count($backups) > 0) {
            $last = $backups[0];
            return [
                'exists' => true,
                'name' => $last['name'],
                'date' => $last['created_at'],
                'size' => $last['size_display']
            ];
        }

        return [
            'exists' => false,
            'message' => 'No backup found'
        ];
    }

    /**
     * Export user's own activity log (semua role bisa)
     */
    public function exportActivityLog(Request $request)
    {
        $user = Auth::user();

        $query = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        $range = $request->range;

        if ($range === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($range === 'last7') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($range === 'last15') {
            $query->where('created_at', '>=', now()->subDays(15));
        } elseif ($range === 'last30') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($range === 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
        }

        $logs = $query->get();

        $dateLabel = $this->getDateLabel($range, $request);
        $filename = 'my_activity_log_' . $dateLabel . '_' . date('Ymd_His') . '.csv';

        $csvContent = $this->generateCSV($logs, $user);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Backup Database - Download SQL
     */
    public function backupDatabase(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            abort(403, 'Only Super Admin can backup database.');
        }

        $type = $request->type ?? 'full';

        // Create backup directory if not exists
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        // Generate filename
        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // Get database connection details
        $database = DB::connection()->getDatabaseName();
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        // Use mysqldump if available
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        try {
            // Try using mysqldump first
            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !File::exists($filepath) || File::size($filepath) === 0) {
                throw new \Exception('mysqldump failed, using PHP fallback');
            }
        } catch (\Exception $e) {
            // Fallback: manual export using PHP
            $this->manualBackup($filepath);
        }

        if (!File::exists($filepath)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create backup'
            ], 500);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'description' => "Database backup created: {$filename}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // If request is AJAX, return file info
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'size' => File::size($filepath),
                'message' => 'Backup created successfully!'
            ]);
        }

        // Download the file
        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(false);
    }

    /**
     * Manual backup using PHP (fallback)
     */
    private function manualBackup($filepath)
    {
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::connection()->getDatabaseName();
        $tableKey = "Tables_in_{$databaseName}";

        $sql = "-- Backup created on " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$databaseName}\n";
        $sql .= "-- Generated by Hotel Maintenance System\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Drop table if exists
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

            // Create table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            // Get data
            $rows = DB::table($tableName)->get();
            if (count($rows) > 0) {
                $columns = array_keys((array) $rows[0]);
                $columnNames = implode('`, `', $columns);

                foreach ($rows as $row) {
                    $values = [];
                    foreach ($columns as $col) {
                        $value = $row->$col;
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $sql .= "INSERT INTO `{$tableName}` (`{$columnNames}`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($filepath, $sql);
    }

    /**
     * Download existing backup
     */
    public function downloadBackup($filename)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            abort(403);
        }

        $filepath = storage_path('app/backups/' . $filename);

        if (!File::exists($filepath)) {
            abort(404, 'Backup file not found');
        }

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        $user = Auth::user();
        if ($user->role !== 'superadmin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $filepath = storage_path('app/backups/' . $filename);

        if (File::exists($filepath)) {
            File::delete($filepath);

            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'deleted',
                'description' => "Backup deleted: {$filename}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return response()->json(['success' => true, 'message' => 'Backup deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Backup file not found'], 404);
    }

    private function getDateLabel($range, $request)
    {
        if ($range === 'today') {
            return date('Y-m-d');
        } elseif ($range === 'last7') {
            return 'last_7_days';
        } elseif ($range === 'last15') {
            return 'last_15_days';
        } elseif ($range === 'last30') {
            return 'last_30_days';
        } elseif ($range === 'all') {
            return 'all_time';
        } elseif ($range === 'custom') {
            $start = $request->start_date ?? 'start';
            $end = $request->end_date ?? 'end';
            return $start . '_to_' . $end;
        }
        return 'export';
    }

    private function generateCSV($logs, $user)
    {
        ob_start();
        $output = fopen('php://output', 'w');
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, ['ID', 'Tanggal & Waktu', 'Action', 'Description', 'IP Address', 'User Agent']);

        if ($logs->isEmpty()) {
            fputcsv($output, ['No activity logs found for the selected period']);
        } else {
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->action,
                    $log->description,
                    $log->ip_address ?? '-',
                    $log->user_agent ?? '-'
                ]);
            }
        }

        fputcsv($output, []);
        fputcsv($output, ['Exported by: ' . $user->name . ' (' . $user->email . ')']);
        fputcsv($output, ['Export date: ' . now()->format('Y-m-d H:i:s')]);
        fputcsv($output, ['Total records: ' . $logs->count()]);

        fclose($output);
        $csvContent = ob_get_contents();
        ob_end_clean();

        return $csvContent;
    }
}
