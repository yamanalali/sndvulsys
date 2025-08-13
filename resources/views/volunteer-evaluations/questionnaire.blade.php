<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استبيان تقييم المتطوع - {{ $volunteerRequest->full_name ?? 'متطوع' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .q-card { border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,.08); margin-bottom: 16px; }
        .guidance { color: #6c757d; font-size: .9rem; }
        .score-badge { min-width: 56px; }
        .sticky-total { position: sticky; top: 0; z-index: 100; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 sticky-total bg-white py-2">
        <h4 class="mb-0">استبيان تقييم المتطوع</h4>
        <div class="text-end">
            <div><span class="badge bg-primary p-2 score-badge">المجموع: <span id="total">0</span> / {{ $fullMark ?? 0 }}</span></div>
            <small class="text-muted">يتم الحساب تلقائياً عند تعديل الدرجات</small>
        </div>
    </div>

    <div class="alert alert-light">
        <strong>المتطوع:</strong> {{ $volunteerRequest->full_name }}
        <span class="mx-2">|</span>
        <strong>التخصص:</strong> {{ $volunteerRequest->field_of_study ?? 'غير محدد' }}
    </div>

    <form method="POST" action="{{ route('volunteer-evaluations.store-questionnaire', $volunteerRequest->id) }}" id="qForm">
        @csrf
        <input type="hidden" name="evaluation_date" value="{{ date('Y-m-d') }}">

        @foreach($questions as $key => $q)
            <div class="card q-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="me-3">
                            <h6 class="mb-1">{{ $q['text'] }}</h6>
                            <div class="guidance">{{ $q['guidance'] }}</div>
                            <div class="mt-2">
                                <strong>إجابة المتطوع:</strong>
                                <div class="border rounded p-2 bg-light">{{ $answers[$key] ?? 'غير متوفر' }}</div>
                            </div>
                            <div class="mt-2">
                                <strong>إجابة مرجعية للموظف:</strong>
                                <div class="border rounded p-2">{{ $q['guidance'] }}</div>
                            </div>
                            <div class="small text-muted">الوزن: {{ $q['weight'] }} | الدرجة القصوى: {{ $q['max'] }}</div>
                        </div>
                        <div style="min-width:240px;">
                            <div class="input-group">
                                <input type="number" step="1" min="0" max="{{ (int)$q['max'] }}" class="form-control" name="questions[{{ $key }}][score]" value="0" oninput="recalc()">
                                <span class="input-group-text">/{{ (int)$q['max'] }}</span>
                            </div>
                            <textarea class="form-control mt-2" name="questions[{{ $key }}][comment]" rows="2" placeholder="ملاحظات (اختياري)"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">ملاحظات عامة</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                    حفظ التقييم
                </button>
                <a href="{{ route('volunteer-requests.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
        </div>
    </form>
</div>

<script>
function recalc(){
    const groups = document.querySelectorAll('input[name^="questions"][name$="[score]"]');
    let total = 0;
    groups.forEach(i=>{ const v = parseFloat(i.value||0); total += isNaN(v)?0:v; });
    document.getElementById('total').textContent = total;
}
recalc();
</script>
</body>
</html>

