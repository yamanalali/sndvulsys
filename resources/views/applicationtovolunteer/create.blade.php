@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">طلب التطوع الجديد</h1>
                <p class="text-gray-600">ساعدنا في بناء مجتمع أفضل من خلال التطوع</p>
            </div>

            <!-- Main Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Progress Bar -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2"></div>
                
                <div class="p-8">
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-green-800 font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-red-800 font-medium">يرجى تصحيح الأخطاء التالية:</span>
                            </div>
                            <ul class="text-red-700 text-sm space-y-1">
                                @foreach($errors->all() as $error)
                                    <li class="flex items-center">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></span>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('applicationtovolunteer.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Personal Information Section -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">المعلومات الشخصية</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        الاسم الأول <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="full_name" name="full_name" value="{{ old('full_name') }}" required>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        الكنية <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        البريد الإلكتروني <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        رقم الهاتف <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                </div>
                                <div>
                                    <label for="national_id" class="block text-sm font-medium text-gray-700 mb-2">رقم الهوية الوطنية</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="national_id" name="national_id" value="{{ old('national_id') }}">
                                </div>
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">البلد</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="country" name="country" value="{{ old('country', 'السعودية') }}">
                                </div>
                                <div>
                                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الميلاد</label>
                                    <input type="date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                           id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">الجنس</label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                                            id="gender" name="gender">
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>آخر</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Location & Education Section -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-xl border border-green-100">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">الموقع والتعليم</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" 
                                            id="city" name="city">
                                        <option value="">اختر المدينة</option>
                                        @foreach($cities as $key => $city)
                                            <option value="{{ $key }}" {{ old('city') == $key ? 'selected' : '' }}>{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">المستوى التعليمي</label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" 
                                            id="education_level" name="education_level">
                                        <option value="">اختر المستوى التعليمي</option>
                                        @foreach($educationLevels as $key => $level)
                                            <option value="{{ $key }}" {{ old('education_level') == $key ? 'selected' : '' }}>{{ $level }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" 
                                          id="address" name="address" rows="2" placeholder="أدخل عنوانك الكامل">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <!-- Professional Information Section -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 rounded-xl border border-purple-100">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">المعلومات المهنية</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">المهنة</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                           id="occupation" name="occupation" value="{{ old('occupation') }}" placeholder="مثال: مهندس، طبيب، معلم...">
                                </div>
                                <div>
                                    <label for="preferred_area" class="block text-sm font-medium text-gray-700 mb-2">المجال المفضل</label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                            id="preferred_area" name="preferred_area">
                                        <option value="">اختر المجال المفضل</option>
                                        @foreach($preferredAreas as $key => $area)
                                            <option value="{{ $key }}" {{ old('preferred_area') == $key ? 'selected' : '' }}>{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label for="skills" class="block text-sm font-medium text-gray-700 mb-2">المهارات</label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition duration-200" 
                                          id="skills" name="skills" rows="3" placeholder="اذكر مهاراتك وخبراتك (مثال: العمل مع الأطفال، الترجمة، التصميم...)">{{ old('skills') }}</textarea>
                            </div>
                        </div>

                        <!-- Volunteering Information Section -->
                        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 p-6 rounded-xl border border-orange-100">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">معلومات التطوع</h3>
                            </div>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="motivation" class="block text-sm font-medium text-gray-700 mb-2">الدافع للتطوع</label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                              id="motivation" name="motivation" rows="3" placeholder="ما هو دافعك للتطوع؟ ما الذي تريد تحقيقه من خلال التطوع؟">{{ old('motivation') }}</textarea>
                                </div>
                                
                                <div>
                                    <label for="previous_experience" class="block text-sm font-medium text-gray-700 mb-2">الخبرات السابقة</label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                              id="previous_experience" name="previous_experience" rows="3" placeholder="اذكر خبراتك السابقة في التطوع أو العمل التطوعي">{{ old('previous_experience') }}</textarea>
                                </div>
                                
                                <div>
                                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-2">التوفر</label>
                                    <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                              id="availability" name="availability" rows="2" placeholder="أوقات توفرك للتطوع (مثال: أيام الأسبوع، عطلات نهاية الأسبوع...)">{{ old('availability') }}</textarea>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="preferred_organization_type" class="block text-sm font-medium text-gray-700 mb-2">نوع المؤسسة المفضلة</label>
                                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                                id="preferred_organization_type" name="preferred_organization_type">
                                            <option value="">اختر نوع المؤسسة</option>
                                            @foreach($organizationTypes as $key => $type)
                                                <option value="{{ $key }}" {{ old('preferred_organization_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="flex items-center h-12">
                                            <input type="checkbox" class="w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500" 
                                                   id="has_previous_volunteering" name="has_previous_volunteering" value="1" {{ old('has_previous_volunteering') ? 'checked' : '' }}>
                                            <label class="mr-3 text-sm font-medium text-gray-700" for="has_previous_volunteering">
                                                لدي خبرة سابقة في التطوع
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="bg-gradient-to-r from-red-50 to-pink-50 p-6 rounded-xl border border-red-100">
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">جهة الاتصال في الطوارئ</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">اسم جهة الاتصال</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-200" 
                                           id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" placeholder="اسم الشخص للاتصال في حالة الطوارئ">
                                </div>
                                <div>
                                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-200" 
                                           id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="رقم هاتف جهة الاتصال">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center pt-6">
                            <button type="submit" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:from-blue-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                إرسال طلب التطوع
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Footer Note -->
            <div class="text-center mt-8 text-gray-500 text-sm">
                <p>سيتم مراجعة طلبك والرد عليك في أقرب وقت ممكن</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions for form elements */
.form-control:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-gradient-to-br {
    animation: fadeInUp 0.6s ease-out;
}
</style>
@endsection 