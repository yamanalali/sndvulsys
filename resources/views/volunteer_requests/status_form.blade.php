<form method="POST" action="{{ route('volunteer_requests.updateStatus', $volunteerRequest->id) }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
        <label for="status">حالة الطلب</label>
        <select name="status" id="status" class="form-control" required>
            <option value="pending" {{ $volunteerRequest->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
            <option value="approved" {{ $volunteerRequest->status == 'approved' ? 'selected' : '' }}>مقبول</option>
            <option value="rejected" {{ $volunteerRequest->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
            <option value="withdrawn" {{ $volunteerRequest->status == 'withdrawn' ? 'selected' : '' }}>منسحب</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">تحديث الحالة</button>
</form> 