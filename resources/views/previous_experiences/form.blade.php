<form method="POST" action="{{ isset($experience) ? route('previous_experiences.update', $experience->id) : route('previous-experiences.store') }}">
    @csrf
    @if(isset($experience))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="description">وصف الخبرة السابقة</label>
        <textarea name="description" id="description" class="form-control" required>{{ old('description', $experience->description ?? '') }}</textarea>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($experience) ? 'تعديل' : 'إضافة' }}
    </button>
</form> 