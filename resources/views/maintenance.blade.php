<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>الموقع تحت الصيانة - منصة الطارق في الرياضيات</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Cairo', system-ui, sans-serif; }
        .hero-bg {
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: patternMove 25s linear infinite;
        }
        @keyframes patternMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(60px, 60px); }
        }
        .glass-box {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.4s ease;
        }
        .glass-box:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }
        .icon-pulse {
            animation: iconPulse 2s ease-in-out infinite;
        }
        @keyframes iconPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="m-0 p-0">
    <div class="hero-bg flex items-center justify-center px-4 py-12">
        <div class="relative z-10 w-full max-w-2xl">
            <div class="glass-box rounded-3xl p-8 sm:p-12 text-center">
                <div class="inline-flex items-center justify-center w-28 h-28 rounded-full bg-amber-500/20 border-2 border-amber-400/50 mb-8 icon-pulse">
                    <i class="fas fa-tools text-5xl sm:text-6xl text-amber-400"></i>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-white mb-4">
                    الموقع تحت الصيانة
                </h1>
                <p class="text-lg sm:text-xl text-gray-300 mb-2 font-semibold text-gradient">
                    منصة الطارق في الرياضيات — مستر طارق الداجن
                </p>
                <p class="text-gray-400 text-base sm:text-lg max-w-md mx-auto leading-relaxed mb-8">
                    نعمل على تحسين تجربتك. سنعود قريباً.
                </p>
                <div class="flex flex-wrap justify-center gap-3 text-sm text-gray-500">
                    <span class="inline-flex items-center gap-2">
                        <i class="fas fa-cog fa-spin"></i>
                        جاري التحديث
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
