@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>تعديل الإرسال</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('submissions.update', $submission->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="volunteer-request_id">طلب التطوع</label>
                            <select name="volunteer-request_id" id="volunteer-request_id" class="form-control" required>
                                @foreach($volunteerRequests as $request)
                                    <option value="{{ $request->id }}" {{ $submission->volunteer-request_id == $request->id ? 'selected' : '' }}>
                                        {{ $request->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="assigned_to">تعيين إلى</label>
                            <select name="assigned_to" id="assigned_to" class="form-control">
                                <option value="">اختر المراجع</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $submission->assigned_to == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="priority">الأولوية</label>
                            <select name="priority" id="priority" class="form-control" required>
                                @foreach($priorities as $key => $value)
                                    <option value="{{ $key }}" {{ $submission->priority == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="due_date">تاريخ الاستحقاق</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" 
                                   value="{{ $submission->due_date ? $submission->due_date->format('Y-m-d') : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="notes">ملاحظات</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ $submission->notes }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="attachments">مرفقات إضافية</label>
                            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">تحديث الإرسال</button>
                            <a href="{{ route('submissions.show', $submission->id) }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 