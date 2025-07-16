<form method="POST" action="{{ isset($availability) ? route('availabilities.update', $availability->id) : route('availabilities.store') }}">
    @csrf
    @if(isset($availability))
        @method('PUT')
    @endif

    <div class="form-group">
        <label for="day">اليوم</label>
        <select name="day" id="day" class="form-control" required>
            @foreach(['السبت','الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة'] as $day)
                <option value="{{ $day }}" {{ (old('day', $availability->day ?? '') == $day) ? 'selected' : '' }}>{{ $day }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="period">الفترة</label>
        <select name="period" id="period" class="form-control" required>
            <option value="morning" {{ (old('period', $availability->period ?? '') == 'morning') ? 'selected' : '' }}>صباحاً</option>
            <option value="evening" {{ (old('period', $availability->period ?? '') == 'evening') ? 'selected' : '' }}>مساءً</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($availability) ? 'تعديل' : 'إضافة' }}
    </button>
</form> 