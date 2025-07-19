<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = auth()->user()->documents()->latest()->get();
        return view('documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'nullable|string|max:255',
                'file' => 'required|mimes:pdf,docx,zip|max:2048',
            ]);

            $file = $request->file('file');
            
            // Check if file was uploaded successfully
            if (!$file || !$file->isValid()) {
                throw new \Exception('فشل في رفع الملف');
            }
            
            $filePath = $file->store('documents', 'public');
            
            // Use file name if title is not provided
            $title = $request->title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $document = Document::create([
                'title' => $title,
                'file_path' => $filePath,
                'user_id' => auth()->id(),
            ]);

            \Log::info('Document uploaded successfully', [
                'document_id' => $document->id,
                'file_path' => $filePath,
                'user_id' => auth()->id()
            ]);

            if ($request->expectsJson() || $request->header('Accept') === 'application/json' || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم رفع الملف بنجاح',
                    'document' => $document
                ]);
            }

            return redirect()->back()->with('success', 'تم رفع الملف بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Document upload failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'حدث خطأ أثناء رفع الملف');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $document = Document::where('id', $id)
                               ->where('user_id', auth()->id())
                               ->firstOrFail();
            
            // حذف الملف من التخزين
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // حذف السجل من قاعدة البيانات
            $document->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المستند بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المستند'
            ], 500);
        }
    }
}

