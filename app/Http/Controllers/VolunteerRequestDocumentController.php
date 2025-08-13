<?php

namespace App\Http\Controllers;

use App\Models\VolunteerRequest;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VolunteerRequestDocumentController extends Controller
{
    public function index(VolunteerRequest $volunteerRequest)
    {
        $this->authorizeAccess();
        $documents = $volunteerRequest->documents()->orderByDesc('created_at')->get();
        return response()->json($documents);
    }

    public function store(Request $request, VolunteerRequest $volunteerRequest)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:10240|mimes:pdf,doc,docx,txt,jpg,jpeg,png',
            'privacy_level' => 'required|in:public,private,restricted',
        ]);

        $file = $validated['file'];
        $fileName = $file->getClientOriginalName();
        $fileType = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $mimeType = $file->getMimeType();

        $filePath = 'volunteer-requests/' . $volunteerRequest->id . '/documents/' . time() . '_' . $fileName;
        $path = $file->storeAs('public/' . dirname($filePath), basename($filePath));

        $fullPath = str_replace('public/', '', $path);

        $document = Document::create([
            'user_id' => Auth::id(),
            'volunteer-request_id' => $volunteerRequest->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name' => $fileName,
            'file_path' => $fullPath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'hash' => hash_file('sha256', storage_path('app/public/' . $fullPath)),
            'privacy_level' => $validated['privacy_level'],
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'تم رفع المستند وربطه بالطلب');
    }

    public function destroy(VolunteerRequest $volunteerRequest, Document $document)
    {
        $this->authorizeAccess();

        if ($document->volunteer_request_id !== $volunteerRequest->id && $document->getAttribute('volunteer-request_id') !== $volunteerRequest->id) {
            abort(404);
        }

        // حذف الملف من التخزين إذا موجود
        $storagePath = 'public/' . ltrim($document->file_path, '/');
        if (Storage::exists($storagePath)) {
            Storage::delete($storagePath);
        }

        $document->delete();

        return redirect()->back()->with('success', 'تم حذف المستند');
    }

    private function authorizeAccess(): void
    {
        if (!Auth::check()) {
            abort(401);
        }
    }
}


