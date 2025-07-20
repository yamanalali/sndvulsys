<aside class="w-64 h-full bg-white border-l border-slate-200 shadow-lg fixed right-0 top-0 z-30 flex flex-col" dir="rtl">
    <div class="p-6 border-b border-slate-100">
        <h3 class="text-lg font-bold text-primary mb-2">مهام المشروع</h3>
        <p class="text-xs text-slate-400 mb-4">لوحة تحكم سريعة للمهام</p>
        <div class="flex flex-col gap-2 mb-2">
            <a href="{{ route('projects.index') }}" class="block rounded-lg px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 font-semibold transition text-center flex items-center gap-2 justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7V6a2 2 0 012-2h2a2 2 0 012 2v1m0 0v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7h6zm6 0V6a2 2 0 012-2h2a2 2 0 012 2v1m0 0v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V7h6z"/></svg>
                المشاريع
            </a>
            <a href="{{ route('task-groups.index') }}" class="block rounded-lg px-4 py-2 bg-purple-50 text-purple-700 hover:bg-purple-100 font-semibold transition text-center flex items-center gap-2 justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                مجموعات المهام
            </a>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-2">
        <a href="{{ route('tasks.index') }}" class="block rounded-lg px-4 py-2 text-slate-700 hover:bg-primary/10 hover:text-primary font-medium transition">جميع المهام</a>
        <a href="{{ route('tasks.create') }}" class="block rounded-lg px-4 py-2 text-slate-700 hover:bg-primary/10 hover:text-primary font-medium transition">إضافة مهمة</a>
        <a href="{{ route('tasks.index', ['status' => 'completed']) }}" class="block rounded-lg px-4 py-2 text-green-700 hover:bg-green-100 font-medium transition">المهام المنجزة</a>
        <a href="{{ route('tasks.index', ['status' => 'pending']) }}" class="block rounded-lg px-4 py-2 text-yellow-700 hover:bg-yellow-100 font-medium transition">المهام المعلقة</a>
        <a href="{{ route('tasks.index', ['status' => 'in_progress']) }}" class="block rounded-lg px-4 py-2 text-blue-700 hover:bg-blue-100 font-medium transition">قيد التنفيذ</a>
        <a href="{{ route('tasks.index', ['status' => 'cancelled']) }}" class="block rounded-lg px-4 py-2 text-gray-500 hover:bg-gray-100 font-medium transition">المهام الملغاة</a>
        <a href="{{ route('tasks.index', ['overdue' => 1]) }}" class="block rounded-lg px-4 py-2 text-red-700 hover:bg-red-100 font-medium transition">المهام المتأخرة</a>
    </nav>
</aside> 