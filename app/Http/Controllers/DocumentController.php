<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentPermission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentBackup;

class DocumentController extends Controller
{
    /**
     * عرض قائمة المستندات
     */
    public function index()
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        $documents = $user->accessibleDocuments()->paginate(12);
        
        return view('documents.index', compact('documents'));
    }

    /**
     * عرض نموذج إنشاء مستند جديد
     */
    public function create()
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        return view('documents.create');
    }

    /**
     * عرض المستند
     */
    public function show(Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if (!$document->canAccess($user)) {
            abort(403, 'ليس لديك صلاحية لعرض هذا المستند');
        }

        return view('documents.show', compact('document'));
    }

    /**
     * عرض نموذج تعديل المستند
     */
    public function edit(Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'edit')) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا المستند');
        }

        return view('documents.edit', compact('document'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif', // 10MB max
            'privacy_level' => 'required|in:public,private,restricted',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('document');
        $fileName = $file->getClientOriginalName();
        $fileType = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        // إنشاء مسار فريد للملف
        $filePath = 'documents/' . date('Y/m/d/') . Str::random(40) . '.' . $fileType;

        // حفظ الملف
        if (Storage::putFileAs(dirname($filePath), $file, basename($filePath))) {
            // إنشاء hash فريد للملف
            $fileHash = hash_file('sha256', Storage::path($filePath));
            
            // التحقق من أن الـ hash فريد
            $existingDocument = Document::where('hash', $fileHash)->first();
            if ($existingDocument) {
                // حذف الملف المرفوع حديثاً
                Storage::delete($filePath);
                return redirect()->back()
                    ->with('error', 'هذا الملف موجود بالفعل في النظام')
                    ->withInput();
            }
            
            try {
                $document = Document::create([
                    'user_id' => Auth::id(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'hash' => $fileHash,
                    'privacy_level' => $request->privacy_level,
                    'expires_at' => $request->expires_at,
                    'metadata' => [
                        'uploaded_by' => Auth::id(),
                        'upload_ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                ]);
            } catch (\Exception $e) {
                // حذف الملف في حالة حدوث خطأ
                Storage::delete($filePath);
                
                \Log::error('خطأ في إنشاء المستند', [
                    'user_id' => Auth::id(),
                    'file_name' => $fileName,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->back()
                    ->with('error', 'حدث خطأ أثناء حفظ المستند: ' . $e->getMessage())
                    ->withInput();
            }

            try {
                // إنشاء نسخة احتياطية
                $backup = $document->createBackup('automatic', 'نسخة احتياطية تلقائية عند الرفع');
                
                if ($backup) {
                    return redirect()->route('documents.show', $document)
                        ->with('success', 'تم رفع المستند بنجاح مع إنشاء نسخة احتياطية');
                } else {
                    return redirect()->route('documents.show', $document)
                        ->with('warning', 'تم رفع المستند بنجاح ولكن فشل في إنشاء النسخة الاحتياطية');
                }
            } catch (\Exception $e) {
                // في حالة حدوث خطأ، حذف المستند وإنشاء رسالة خطأ
                $document->delete();
                Storage::delete($filePath);
                
                \Log::error('خطأ في إنشاء نسخة احتياطية', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
                
                return redirect()->back()
                    ->with('error', 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage())
                    ->withInput();
            }
        }

        return redirect()->back()
            ->with('error', 'حدث خطأ أثناء رفع الملف')
            ->withInput();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'edit')) {
            abort(403, 'ليس لديك صلاحية لتعديل هذا المستند');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'privacy_level' => 'required|in:public,private,restricted',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $document->update([
            'title' => $request->title,
            'description' => $request->description,
            'privacy_level' => $request->privacy_level,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'تم تحديث المستند بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'يجب تسجيل الدخول أولاً'], 401);
            }
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'delete')) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'ليس لديك صلاحية لحذف هذا المستند'], 403);
            }
            abort(403, 'ليس لديك صلاحية لحذف هذا المستند');
        }

        try {
            // حذف النسخ الاحتياطية أولاً
            $document->backups()->delete();
            
            // حذف الصلاحيات
            $document->permissions()->delete();
            
            // حذف الملف الفعلي
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }
            
            // حذف المستند من قاعدة البيانات
            $document->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'تم حذف المستند بنجاح',
                    'document_id' => $document->id
                ], 200);
            }

            return redirect()->route('documents.index')
                ->with('success', 'تم حذف المستند بنجاح');
                
        } catch (\Exception $e) {
            Log::error('خطأ في حذف المستند', [
                'document_id' => $document->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'حدث خطأ أثناء حذف المستند: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المستند: ' . $e->getMessage());
        }
    }

    /**
     * تحميل المستند
     */
    public function download(Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if (!$document->canAccess($user)) {
            abort(403, 'ليس لديك صلاحية لتحميل هذا المستند');
        }

        if (!Storage::exists($document->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::download($document->file_path, $document->file_name);
    }

    /**
     * مشاركة المستند مع مستخدم آخر
     */
    public function share(Request $request, Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'share')) {
            abort(403, 'ليس لديك صلاحية لمشاركة هذا المستند');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'permission_type' => 'required|in:view,download,edit,delete,share,admin',
            'expires_at' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // التحقق من عدم وجود صلاحية مسبقة
        $existingPermission = DocumentPermission::where('user_id', $request->user_id)
            ->where('document_id', $document->id)
            ->where('permission_type', $request->permission_type)
            ->first();

        if ($existingPermission) {
            return redirect()->back()
                ->with('error', 'المستخدم لديه هذه الصلاحية بالفعل');
        }

        DocumentPermission::grantPermission(
            $request->user_id,
            $document->id,
            $request->permission_type,
            'direct',
            $request->expires_at
        );

        return redirect()->back()
            ->with('success', 'تم منح الصلاحية بنجاح');
    }

    /**
     * إلغاء صلاحية مستخدم
     */
    public function revokePermission(Request $request, Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'admin')) {
            abort(403, 'ليس لديك صلاحية لإلغاء الصلاحيات');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'permission_type' => 'required|in:view,download,edit,delete,share,admin',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DocumentPermission::revokePermission(
            $request->user_id,
            $document->id,
            $request->permission_type
        );

        return redirect()->back()
            ->with('success', 'تم إلغاء الصلاحية بنجاح');
    }

    /**
     * إنشاء نسخة احتياطية
     */
    public function createBackup(Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'admin')) {
            abort(403, 'ليس لديك صلاحية لإنشاء نسخة احتياطية');
        }

        $backupType = request('backup_type', 'manual');
        $useDrive = request('use_drive', false);
        $notes = request('backup_notes', 'نسخة احتياطية يدوية');
        
        try {
            if ($useDrive) {
                // محاولة إنشاء نسخة احتياطية في Google Drive
                try {
                    $backup = $document->createDriveBackup($backupType, $notes);
                    $storageType = 'Google Drive';
                } catch (\Exception $e) {
                    // إذا فشل Google Drive، استخدم التخزين المحلي
                    Log::warning('فشل في إنشاء نسخة احتياطية في Google Drive، استخدام التخزين المحلي', [
                        'document_id' => $document->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    $backup = $document->createBackup($backupType, $notes . ' (محلي - فشل Google Drive)');
                    $storageType = 'التخزين المحلي (فشل Google Drive)';
                }
            } else {
                // إنشاء نسخة احتياطية محلية
                $backup = $document->createBackup($backupType, $notes);
                $storageType = 'التخزين المحلي';
            }

            if ($backup) {
                return redirect()->back()
                    ->with('success', "تم إنشاء النسخة الاحتياطية بنجاح في {$storageType}");
            }

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء النسخة الاحتياطية');

        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء نسخة احتياطية', [
                'document_id' => $document->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * استعادة نسخة احتياطية
     */
    public function restoreBackup(DocumentBackup $backup)
    {
        $user = Auth::user();
        $document = $backup->document;
        
        if ($document->user_id !== $user->id && !$user->hasDocumentPermission($document->id, 'admin')) {
            abort(403, 'ليس لديك صلاحية لاستعادة النسخة الاحتياطية');
        }

        try {
            if ($backup->restore()) {
                return redirect()->back()
                    ->with('success', 'تم استعادة النسخة الاحتياطية بنجاح');
            }

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء استعادة النسخة الاحتياطية');

        } catch (\Exception $e) {
            Log::error('خطأ في استعادة نسخة احتياطية', [
                'backup_id' => $backup->id,
                'document_id' => $document->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء استعادة النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * عرض النسخ الاحتياطية
     */
    public function showBackups(Document $document)
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم مسجل دخول
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }
        
        // التحقق من وجود المستند
        if (!$document) {
            abort(404, 'المستند غير موجود');
        }
        
        // التحقق من أن المستند نشط
        if (!$document->isActive()) {
            abort(404, 'المستند غير نشط');
        }
        
        // التحقق من الصلاحيات
        if (!$document->canAccess($user)) {
            // إذا كان المستند عام، يمكن للجميع الوصول إليه
            if ($document->privacy_level === 'public') {
                // السماح بالوصول للمستندات العامة
            } else {
                abort(403, 'ليس لديك صلاحية لعرض النسخ الاحتياطية');
            }
        }

        try {
            // جلب النسخ الاحتياطية مع معالجة الأخطاء
            $allBackups = collect();
            $driveBackups = collect();
            $localBackups = collect();
            
            try {
                $allBackups = $document->backups()->orderBy('backup_date', 'desc')->get();
                $backups = $document->backups()->orderBy('backup_date', 'desc')->paginate(10);
            } catch (\Exception $e) {
                Log::error('خطأ في جلب النسخ الاحتياطية', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
                $backups = collect()->paginate(10);
            }
            
            try {
                $driveBackups = $document->getDriveBackups();
            } catch (\Exception $e) {
                Log::warning('خطأ في جلب النسخ الاحتياطية من Google Drive', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            try {
                $localBackups = $document->getLocalBackups();
            } catch (\Exception $e) {
                Log::warning('خطأ في جلب النسخ الاحتياطية المحلية', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage()
                ]);
            }

            return view('documents.backups', compact('document', 'backups', 'allBackups', 'driveBackups', 'localBackups'));
        } catch (\Exception $e) {
            \Log::error('خطأ في عرض النسخ الاحتياطية', [
                'document_id' => $document->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء عرض النسخ الاحتياطية: ' . $e->getMessage());
        }
    }

    /**
     * عرض جميع النسخ الاحتياطية للمستخدم
     */
    public function allBackups()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        try {
            // جلب جميع المستندات التي يمكن للمستخدم الوصول إليها
            $documents = Document::where('user_id', $user->id)
                ->orWhere('privacy_level', 'public')
                ->orWhereHas('permissions', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->with(['backups' => function($query) {
                    $query->orderBy('backup_date', 'desc');
                }])
                ->get();

            $allBackups = collect();
            foreach ($documents as $document) {
                $allBackups = $allBackups->merge($document->backups);
            }

            return view('documents.all-backups', compact('documents', 'allBackups'));
        } catch (\Exception $e) {
            \Log::error('خطأ في عرض جميع النسخ الاحتياطية', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء عرض النسخ الاحتياطية: ' . $e->getMessage());
        }
    }
}
