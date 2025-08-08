<!DOCTYPE html>
<html>
<head>
    <title>اختبار تحديث الحالة</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>اختبار تحديث حالة المهمة</h1>
    
    @if(isset($task))
        <form id="test-form" action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
            @csrf
            <div>
                <label>الحالة الحالية: {{ $task->status }}</label>
            </div>
            <div>
                <label>الحالة الجديدة:</label>
                <select name="status">
                    <option value="new" {{ $task->status == 'new' ? 'selected' : '' }}>جديدة</option>
                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>منجزة</option>
                    <option value="cancelled" {{ $task->status == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            <button type="submit">تحديث بـ JavaScript</button>
            <button type="submit" onclick="document.getElementById('test-form').submit(); return false;">تحديث عادي</button>
        </form>
        
        <div id="result"></div>
    @else
        <p>لا توجد مهمة للاختبار</p>
    @endif

    <script>
        $('#test-form').on('submit', function(e) {
            e.preventDefault();
            
            console.log('Form submitted');
            console.log('Action:', this.action);
            
            $.ajax({
                url: this.action,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Success:', response);
                    $('#result').html('<div style="color: green;">نجح: ' + response.message + '</div>');
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                    $('#result').html('<div style="color: red;">خطأ: ' + xhr.responseText + '</div>');
                }
            });
        });
    </script>
</body>
</html>