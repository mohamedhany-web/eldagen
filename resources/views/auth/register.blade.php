<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>إنشاء حساب - منصة التعلم</title>

    <!-- الخطوط العربية الاحترافية -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- تخصيص TailwindCSS -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'arabic': ['IBM Plex Sans Arabic', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome للأيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || false }"
      x-init="
          $watch('darkMode', value => {
              localStorage.setItem('darkMode', value);
              if (value) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          });
          if (darkMode) document.documentElement.classList.add('dark');
      ">

    <div class="max-w-lg w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">إنشاء حساب جديد</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">انضم إلى منصة التعلم واستكشف عالم المعرفة</p>
        </div>

        <!-- مفتاح الوضع المظلم -->
        <div class="flex justify-center">
            <button @click="darkMode = !darkMode" 
                    class="p-2 rounded-lg bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700">
                <i x-show="!darkMode" class="fas fa-moon text-gray-600"></i>
                <i x-show="darkMode" class="fas fa-sun text-yellow-500"></i>
            </button>
        </div>

        <!-- نموذج التسجيل -->
        <form class="mt-8 space-y-6 bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700" 
              action="{{ route('register') }}" method="POST" 
              x-data="{ showPassword: false, showPasswordConfirm: false }">
            @csrf
            
            <div class="space-y-4">
                <!-- الاسم -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user ml-2"></i>
                        الاسم الكامل
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           required 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('name') border-red-500 @enderror" 
                           placeholder="أدخل اسمك الكامل">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رقم هاتف الطالب -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-phone ml-2"></i>
                        رقم هاتف الطالب
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone" 
                           value="{{ old('phone') }}"
                           required 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('phone') border-red-500 @enderror" 
                           placeholder="01xxxxxxxx" 
                           dir="ltr">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رقم هاتف ولي الأمر -->
                <div>
                    <label for="parent_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-user-friends ml-2"></i>
                        رقم هاتف ولي الأمر <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="parent_phone" 
                           id="parent_phone" 
                           value="{{ old('parent_phone') }}"
                           required 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('parent_phone') border-red-500 @enderror" 
                           placeholder="01xxxxxxxx" 
                           dir="ltr">
                    @error('parent_phone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle ml-1"></i>
                        سيتمكن ولي الأمر من الدخول بنفس بيانات الطالب لمتابعة التقدم
                    </p>
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-lock ml-2"></i>
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="password" 
                               required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors duration-200 @error('password') border-red-500 @enderror" 
                               placeholder="أدخل كلمة مرور قوية">
                        <button type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i x-show="!showPassword" class="fas fa-eye"></i>
                            <i x-show="showPassword" class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- تأكيد كلمة المرور -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-lock ml-2"></i>
                        تأكيد كلمة المرور
                    </label>
                    <div class="relative">
                        <input :type="showPasswordConfirm ? 'text' : 'password'" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" 
                               placeholder="أعد إدخال كلمة المرور">
                        <button type="button" 
                                @click="showPasswordConfirm = !showPasswordConfirm" 
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i x-show="!showPasswordConfirm" class="fas fa-eye"></i>
                            <i x-show="showPasswordConfirm" class="fas fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <!-- موافقة على الشروط -->
                <div class="flex items-start">
                    <input type="checkbox" 
                           id="terms" 
                           required
                           class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="terms" class="mr-2 text-sm text-gray-700 dark:text-gray-300">
                        أوافق على 
                        <a href="#" class="text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300 underline">شروط الاستخدام</a>
                        و
                        <a href="#" class="text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300 underline">سياسة الخصوصية</a>
                    </label>
                </div>
            </div>

            <!-- زر إنشاء الحساب -->
            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                    <span class="absolute right-0 inset-y-0 flex items-center pr-3">
                        <i class="fas fa-user-plus group-hover:text-green-200"></i>
                    </span>
                    إنشاء الحساب
                </button>
            </div>

            <!-- رابط تسجيل الدخول -->
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300">
                        سجل الدخول
                    </a>
                </p>
            </div>
        </form>

        <!-- العودة للصفحة الرئيسية -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للصفحة الرئيسية
            </a>
        </div>
    </div>
</body>
</html>
