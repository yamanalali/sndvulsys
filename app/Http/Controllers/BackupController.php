<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get backup statistics
        $totalDocuments = Document::where('user_id', auth()->id())->count();
        $totalSize = Document::where('user_id', auth()->id())->get()->sum(function($doc) {
            $path = storage_path('app/public/' . $doc->file_path);
            return file_exists($path) ? filesize($path) : 0;
        }) / (1024 * 1024); // Convert to MB
        
        // Get recent backups (in real app, you'd have a backups table)
        $recentBackups = $this->getRecentBackups();
        
        return view('backup.index', compact('totalDocuments', 'totalSize', 'recentBackups'));
    }

    public function backupDocuments()
    {
        try {
            $files = Storage::disk('public')->files('documents');
            
            if (empty($files)) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا توجد ملفات للنسخ الاحتياطي'
                    ], 404);
                }
                return redirect()->back()->with('error', 'لا توجد ملفات للنسخ الاحتياطي');
            }
            
            $zipFile = storage_path('app/public/backup_'.date('Y-m-d_H-i-s').'.zip');
        
            $zip = new \ZipArchive;
            if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
                foreach ($files as $file) {
                    $zip->addFile(storage_path('app/public/'.$file), $file);
                }
                $zip->close();
            }
        
            return response()->download($zipFile)->deleteFileAfterSend();
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إنشاء النسخة الاحتياطية'
                ], 500);
            }
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء النسخة الاحتياطية');
        }
    }

    private function getRecentBackups()
    {
        // In a real application, you would have a backups table
        // For now, we'll return sample data
        return [
            [
                'id' => 1,
                'name' => 'backup_2024-01-15_14-30-00.zip',
                'size' => '2.5 MB',
                'date' => '2024-01-15 14:30',
                'status' => 'success',
                'status_text' => 'مكتمل'
            ],
            [
                'id' => 2,
                'name' => 'backup_2024-01-10_09-15-00.zip',
                'size' => '1.8 MB',
                'date' => '2024-01-10 09:15',
                'status' => 'success',
                'status_text' => 'مكتمل'
            ],
            [
                'id' => 3,
                'name' => 'backup_2024-01-05_16-45-00.zip',
                'size' => '2.1 MB',
                'date' => '2024-01-05 16:45',
                'status' => 'success',
                'status_text' => 'مكتمل'
            ]
        ];
    }
}
