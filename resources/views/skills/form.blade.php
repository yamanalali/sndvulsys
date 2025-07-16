<form method="POST" action="{{ isset($skill) ? route('skills.update', $skill->id) : route('skills.store') }}">
    @csrf
    @if(isset($skill))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="name">اسم المهارة</label>
        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $skill->name ?? '') }}" required>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($skill) ? 'تعديل' : 'إضافة' }}
    </button>
</form> 