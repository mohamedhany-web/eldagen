@extends('layouts.focus')

@section('title', $lesson->title)

@section('content')
<div class="h-screen flex flex-col overflow-hidden bg-black">
    <!-- شريط التحكم العلوي -->
    <header class="flex-shrink-0 bg-gray-900 text-white px-4 py-2 flex items-center justify-between">
        <div class="flex items-center space-x-4 space-x-reverse min-w-0">
            <button onclick="exitLesson()" 
                    class="flex-shrink-0 text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="min-w-0 truncate">
                <h1 class="text-base font-semibold truncate">{{ $lesson->title }}</h1>
                <p class="text-xs text-gray-400 truncate">{{ $course->title }}</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3 flex-shrink-0">
            @if($lesson->allow_flexible_submission)
            <form method="POST" action="{{ route('my-courses.lesson.progress', [$course, $lesson]) }}" class="inline-block" id="form-submit-lesson">
                @csrf
                <input type="hidden" name="watch_time" value="0" id="form-watch-time">
                <input type="hidden" name="progress_percent" value="100">
                <input type="hidden" name="completed" value="1">
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white text-sm font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-check-double ml-1"></i>
                    تسليم الحصة
                </button>
            </form>
            @endif
            <div class="flex items-center space-x-2 space-x-reverse">
                <span class="text-xs text-gray-400">التقدم:</span>
                <span id="lesson-progress" class="text-xs font-medium text-white">0%</span>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <span class="text-xs text-gray-400">الوقت:</span>
                <span id="time-display" class="text-xs font-medium text-white">00:00 / {{ gmdate('i:s', ($lesson->duration_minutes ?? 0) * 60) }}</span>
            </div>
        </div>
    </header>

    @php
        $lessonAttachments = $lesson->getAttachmentsArray();
    @endphp
    @if($lessonAttachments && count($lessonAttachments) > 0)
        <div class="flex-shrink-0 bg-gray-800 text-white px-4 py-2 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-400 whitespace-nowrap"><i class="fas fa-paperclip ml-1"></i> مرفقات الدرس:</span>
            @foreach($lessonAttachments as $att)
                <a href="{{ storage_url($att['path'] ?? '') }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1 px-2 py-1 bg-white/10 hover:bg-white/20 rounded text-xs text-white truncate max-w-[140px]" title="{{ $att['name'] ?? 'تحميل' }}">
                    <i class="fas fa-download flex-shrink-0"></i>
                    <span class="truncate">{{ Str::limit($att['name'] ?? 'ملف', 18) }}</span>
                </a>
            @endforeach
        </div>
    @endif

    @if(session('success'))
    <div class="flex-shrink-0 bg-green-700 text-white px-4 py-2 text-center text-sm" role="alert">
        <i class="fas fa-check-circle ml-1"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- منطقة الفيديو + الكونترولات (تأخذ الباقي من الشاشة وتناسبها) -->
    <div class="flex-1 min-h-0 flex flex-col relative bg-black" id="video-container">
        <div id="protection-overlay" class="absolute inset-0 z-10 pointer-events-none opacity-0" aria-hidden="true"></div>
        <!-- الفيديو فقط (يملأ المساحة فوق الكونترولات) -->
        <div class="flex-1 min-h-0 w-full flex items-center justify-center relative overflow-hidden" id="video-player">
            @if($lesson->video_url)
                <div class="w-full h-full relative">
                    <div id="video-iframe-container" class="w-full h-full relative">
                        {!! \App\Helpers\VideoHelper::generateEmbedHtml($lesson->video_url, '100%', '100%', $lesson->video_source) !!}
                        <div id="focus-keeper" 
                             class="absolute inset-0 z-20 pointer-events-none" 
                             tabindex="0" 
                             style="outline: none;"
                             title="الحماية من التصوير (النقر يمر للمشغّل)"></div>
                    </div>
                    <canvas id="protection-canvas" class="absolute inset-0 w-full h-full opacity-0 pointer-events-none" aria-hidden="true"></canvas>
                </div>
            @else
                <div class="text-center text-white">
                    <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
                    <p>لا يوجد فيديو متاح لهذا الدرس</p>
                </div>
            @endif
        </div>
        
        <!-- شريط التقدم والكونترولات (ثابت أسفل منطقة الفيديو ضمن الشاشة) -->
        <div class="flex-shrink-0 w-full bg-gradient-to-t from-black to-transparent px-4 py-3 z-30">
            <div class="flex flex-wrap items-center gap-2 gap-y-2 space-x-reverse">
                @if($lesson->allow_flexible_submission)
                <button type="button" onclick="seekBy(-15)" class="flex-shrink-0 px-2 py-1.5 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-white text-xs font-medium transition-colors" title="رجوع 15 ثانية">
                    <i class="fas fa-backward ml-0.5"></i> 15
                </button>
                @endif
                <button id="play-pause-btn" onclick="togglePlayPause()" 
                        class="w-10 h-10 flex-shrink-0 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center text-white transition-colors">
                    <i id="play-pause-icon" class="fas fa-play"></i>
                </button>
                @if($lesson->allow_flexible_submission)
                <button type="button" onclick="seekBy(15)" class="flex-shrink-0 px-2 py-1.5 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg text-white text-xs font-medium transition-colors" title="تقديم 15 ثانية">
                    15 <i class="fas fa-forward mr-0.5"></i>
                </button>
                @endif
                <div class="flex-1 min-w-0" id="progress-bar-container">
                    <div class="w-full bg-gray-600 rounded-full h-1.5 relative select-none {{ $lesson->allow_flexible_submission ? 'cursor-pointer' : 'cursor-default' }}" id="timeline-track"
                         onclick="seekToFromEvent(event)"
                         @if($lesson->allow_flexible_submission) onmousedown="startTimelineDrag(event)" @endif>
                        <div id="progress-bar" class="bg-blue-500 h-1.5 rounded-full transition-all duration-300 pointer-events-none" style="width: 0%"></div>
                        @if($lesson->allow_flexible_submission)
                        <div id="progress-thumb" class="absolute top-1/2 w-3 h-3 bg-white rounded-full shadow cursor-grab border border-gray-300 hidden" style="left: 0%; transform: translate(-50%, -50%);"></div>
                        @endif
                    </div>
                </div>
                <button onclick="toggleMute()" 
                        class="w-9 h-9 flex-shrink-0 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center text-white transition-colors">
                    <i id="volume-icon" class="fas fa-volume-up"></i>
                </button>
                <button onclick="toggleFullscreen()" 
                        class="w-9 h-9 flex-shrink-0 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-full flex items-center justify-center text-white transition-colors">
                    <i class="fas fa-expand"></i>
                </button>
                @if(!empty($allowFlexibleSubmission))
                <button type="button" id="btn-submit-lesson" onclick="submitLessonManually()"
                        class="flex-shrink-0 px-3 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white text-sm font-medium transition-colors">
                    <i class="fas fa-check-double ml-1"></i>
                    تسليم الحصة
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- نافذة سؤال الفيديو (نقطة توقف) -->
    <div id="video-question-popup" class="hidden fixed inset-0 bg-black bg-opacity-80 z-[100] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full max-h-[90vh] flex flex-col text-right">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-pause-circle ml-2"></i>
                    سؤال عند نقطة التوقف
                </h3>
            </div>
            <div id="video-question-body" class="p-4 overflow-y-auto flex-1">
                <!-- يُملأ ديناميكياً -->
            </div>
            <div id="video-question-feedback" class="hidden p-4 border-t border-gray-200 dark:border-gray-700">
                <p id="video-question-feedback-text" class="font-medium"></p>
            </div>
            <div id="video-question-actions" class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                <button type="button" id="video-question-submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium">
                    <i class="fas fa-check ml-2"></i>
                    إرسال الإجابة
                </button>
            </div>
        </div>
    </div>

    <!-- تأكيد الخروج مع حفظ التقدم -->
    <div id="exit-warning" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md mx-4">
            <div class="text-center">
                <i class="fas fa-sign-out-alt text-4xl text-primary-500 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">الخروج من الدرس</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">سيتم حفظ تقدمك في الدرس ثم العودة لصفحة الكورس. هل تريد المتابعة؟</p>
                <div class="flex space-x-4 space-x-reverse">
                    <button id="confirm-exit-btn" type="button" onclick="confirmExit()" 
                            class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التقدم والخروج
                    </button>
                    <button type="button" onclick="cancelExit()" 
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let youtubePlayer = null;
let vimeoPlayer = null;
let videoElement = null;
let watchStartTime = Date.now();
let totalWatchTime = {{ (int) ($savedWatchTime ?? 0) }};
let lastProgressUpdate = {{ (int) ($savedWatchTime ?? 0) }};
const savedWatchTimeSeconds = {{ (int) ($savedWatchTime ?? 0) }};
let isVideoReady = false;
let progressInterval = null;
let protectionCanvas = null;
let violationReported = false;

// نقاط التوقف (أسئلة الفيديو)
const videoCheckpoints = @json($videoCheckpoints ?? []);
const videoCheckpointsShown = {};
var savedWatchTimeForCheckpoints = {{ (int) ($savedWatchTime ?? 0) }};
videoCheckpoints.forEach(function(cp) {
    if (cp.time_seconds <= savedWatchTimeForCheckpoints) {
        videoCheckpointsShown[cp.id] = true;
    }
});
let currentCheckpoint = null;
const videoQuestionAnswerUrl = '{{ route("my-courses.lesson.video-question-answer", [$course, $lesson]) }}';
const reportDurationUrl = '{{ route("my-courses.lesson.report-duration", [$course, $lesson]) }}';
let videoDurationSeconds = {{ ($lesson->duration_minutes ?? 0) * 60 }};
let durationReported = false;
const allowFlexibleSubmission = @json($allowFlexibleSubmission ?? false);

// عند تسليم الحصة عبر النموذج: تحديث وقت المشاهدة قبل الإرسال
(function() {
    var f = document.getElementById('form-submit-lesson');
    if (f) f.addEventListener('submit', function() {
        try {
            var wt = totalWatchTime + (watchStartTime ? Math.floor((Date.now() - watchStartTime) / 1000) : 0);
            var inp = document.getElementById('form-watch-time');
            if (inp) inp.value = wt;
        } catch(e) {}
    });
})();

// إبلاغ الخادم بمخالفة (سكرين شوت أو تسجيل) — يؤدي لتعليق الحساب
function reportViolationToServer(type) {
    if (violationReported) return;
    violationReported = true;
    fetch('{{ route("my-courses.report-violation") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ type: type, notes: 'درس', _token: '{{ csrf_token() }}' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.suspended && data.redirect) {
            if (window.top !== window.self) {
                window.top.location.href = data.redirect;
            } else {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(() => { violationReported = false; });
}

// كشف ومنع كل اختصارات التصوير (Print Screen، أداة القص، إلخ)
function isScreenshotShortcut(e) {
    var k = (e.key || '').toLowerCase();
    var c = e.keyCode || e.which;
    // زر Print Screen (مختلف المتصفحات/الأنظمة)
    if (k === 'printscreen' || k === 'print' || k === 'snapshot' || c === 44) return true;
    // Win+Shift+S (أداة القص في Windows)
    if ((c === 83 || k === 's') && e.shiftKey && (e.metaKey || e.ctrlKey)) return true;
    // Ctrl+Shift+S
    if ((c === 83 || k === 's') && e.shiftKey && e.ctrlKey) return true;
    // Alt+Print Screen
    if (e.altKey && (c === 44 || k === 'printscreen' || k === 'print')) return true;
    // Cmd+Shift+3 / Cmd+Shift+4 / Cmd+Shift+5 (Mac)
    if (e.metaKey && e.shiftKey && (c === 51 || c === 52 || c === 53 || k === '3' || k === '4' || k === '5')) return true;
    // أي زر مع Win/Cmd + Print Screen
    if ((e.metaKey || e.ctrlKey) && (c === 44 || k === 'printscreen' || k === 'print')) return true;
    return false;
}
function handleScreenshotAttempt(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    reportViolationToServer('screenshot');
    return false;
}
window.addEventListener('keydown', function(e) {
    if (isScreenshotShortcut(e)) return handleScreenshotAttempt(e);
}, true);
window.addEventListener('keyup', function(e) {
    if (isScreenshotShortcut(e)) return handleScreenshotAttempt(e);
}, true);
document.addEventListener('keydown', function(e) {
    if (isScreenshotShortcut(e)) return handleScreenshotAttempt(e);
}, true);
document.addEventListener('keyup', function(e) {
    if (isScreenshotShortcut(e)) return handleScreenshotAttempt(e);
}, true);

// كشف اللصق بعد السكرين شوت: عند لصق صورة من الحافظة = إبلاغ وتعليق وتوجيه تلقائي
document.addEventListener('paste', function(e) {
    e.preventDefault();
    var items = e.clipboardData && e.clipboardData.items;
    if (items) {
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                reportViolationToServer('screenshot');
                return;
            }
        }
    }
}, true);

// تركيز الصفحة على الطبقة فوق الفيديو حتى نستقبل زر التصوير
function focusFocusKeeper() {
    var el = document.getElementById('focus-keeper');
    if (el) {
        el.focus();
    }
}
document.addEventListener('click', function(e) {
    var keeper = document.getElementById('focus-keeper');
    if (keeper && (e.target === keeper || keeper.contains(e.target))) {
        keeper.focus();
    }
}, true);
setInterval(function() {
    var keeper = document.getElementById('focus-keeper');
    if (!keeper) return;
    if (document.getElementById('exit-warning').classList.contains('hidden') === false) return;
    var active = document.activeElement;
    if (active === keeper) return;
    if (active && (active.tagName === 'IFRAME' || active === document.body || active.id === 'video-iframe-container')) {
        keeper.focus();
    }
}, 2000);

// حماية متقدمة من التصوير (اختصارات أخرى)
document.addEventListener('keydown', function(e) {
    if (isScreenshotShortcut(e)) {
        handleScreenshotAttempt(e);
        return false;
    }
    // منع جميع اختصارات أدوات المطور
    if ((e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
        (e.ctrlKey && e.key === 'u') ||
        e.key === 'F12' ||
        (e.ctrlKey && e.key === 's') ||
        (e.ctrlKey && e.shiftKey && e.key === 'Delete')) {
        e.preventDefault();
        activateScreenshotProtection();
        showProtectionMessage('هذا الإجراء معطل لحماية المحتوى');
        return false;
    }
    
    // منع Alt+Tab (تغيير النوافذ)
    if (e.altKey && e.key === 'Tab') {
        e.preventDefault();
        showProtectionMessage('يجب التركيز على الدرس');
        return false;
    }
});

// منع النقر بالزر الأيمن مع تفعيل الحماية
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    activateScreenshotProtection();
    showProtectionMessage('القوائم معطلة لحماية المحتوى');
});

// حماية من التصوير عند الكشف — بدون عرض أي نص أو طبقة فوق الفيديو
function activateScreenshotProtection() {
    // لا نعرض "محتوى محمي" ولا أي طبقة سوداء فوق الفيديو
}

// منع السحب والإفلات
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
});

// مراقبة تغيير النافذة (منع فتح نوافذ أخرى)
window.addEventListener('blur', function() {
    if (videoElement && !videoElement.paused) {
        pauseVideo();
        showProtectionMessage('تم إيقاف الفيديو - التركيز مطلوب');
    }
});

// تحميل YouTube API
function loadYouTubeAPI() {
    if (!window.YT) {
        const tag = document.createElement('script');
        tag.src = 'https://www.youtube.com/iframe_api';
        const firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }
}

// تهيئة مشغل الفيديو
document.addEventListener('DOMContentLoaded', function() {
    setupProtection();
    initializeVideoPlayer();
    startProgressTracking();
    focusFocusKeeper();
    // تحديث شريط التقدم بعد ثوانٍ (عندما تصبح مدة الفيديو متاحة في YouTube غالباً)
    [1000, 2000, 3500].forEach(function(ms) {
        setTimeout(function() { if (typeof updateProgress === 'function') updateProgress(); }, ms);
    });
    // منع الـ iframe من سرقة التركيز: جعله غير قابل للتركيز بالتاب
    setTimeout(function() {
        var iframes = document.querySelectorAll('#video-iframe-container iframe');
        iframes.forEach(function(iframe) {
            iframe.setAttribute('tabindex', '-1');
        });
    }, 500);
    // تحميل YouTube API للتحكم في الفيديو
    loadYouTubeAPI();
});

function setupProtection() {
    // منع التحديد
    document.body.style.userSelect = 'none';
    document.body.style.webkitUserSelect = 'none';
    document.body.style.mozUserSelect = 'none';
    document.body.style.msUserSelect = 'none';
    
    // إعداد canvas الحماية
    const canvas = document.getElementById('protection-canvas');
    if (canvas) {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    
    // مراقبة مستمرة للحماية
    setInterval(activateRandomProtection, 5000);
}

function activateRandomProtection() {
    // لا نعرض أي طبقة فوق الفيديو
}

function initializeVideoPlayer() {
    const iframe = document.querySelector('iframe');
    const video = document.querySelector('video');
    
    if (iframe && iframe.src.includes('youtube')) {
        setupYouTubePlayer(iframe);
    } else if (iframe && iframe.src.includes('vimeo')) {
        setupVimeoPlayer(iframe);
    } else if (video) {
        setupDirectVideoPlayer(video);
    } else if (iframe) {
        setupGenericIframePlayer(iframe);
    }
}

// إعداد YouTube Player مع API
function setupYouTubePlayer(iframe) {
    const videoId = iframe.src.match(/embed\/([^?&]+)/)[1];
    
    // استبدال iframe بـ div للـ API
    const playerDiv = document.createElement('div');
    playerDiv.id = 'youtube-player';
    iframe.parentNode.replaceChild(playerDiv, iframe);
    
    var playerVars = {
        'autoplay': 0,
        'controls': 0,
        'rel': 0,
        'showinfo': 0,
        'modestbranding': 1,
        'iv_load_policy': 3,
        'cc_load_policy': 0,
        'disablekb': 1,
        'fs': 0,
        'playsinline': 1,
        'origin': window.location.origin,
        'widget_referrer': window.location.origin
    };
    if (savedWatchTimeSeconds > 0) {
        playerVars.start = savedWatchTimeSeconds;
    }
    window.onYouTubeIframeAPIReady = function() {
        youtubePlayer = new YT.Player('youtube-player', {
            height: '100%',
            width: '100%',
            videoId: videoId,
            playerVars: playerVars,
            events: {
                'onReady': function(event) {
                    isVideoReady = true;
                    updatePlayButton(true);
                    hideYouTubeElements();
                    if (savedWatchTimeSeconds > 0 && typeof youtubePlayer.seekTo === 'function') {
                        youtubePlayer.seekTo(savedWatchTimeSeconds, true);
                    }
                    var checkDuration = setInterval(function() {
                        if (youtubePlayer && typeof youtubePlayer.getDuration === 'function') {
                            var d = youtubePlayer.getDuration();
                            if (d && d > 0) {
                                videoDurationSeconds = Math.floor(d);
                                reportLessonDurationOnce(videoDurationSeconds);
                                updateProgress();
                                clearInterval(checkDuration);
                            }
                        }
                    }, 500);
                    setTimeout(function() { clearInterval(checkDuration); }, 30000);
                },
                'onStateChange': function(event) {
                    if (event.data == YT.PlayerState.PLAYING) {
                        startWatchTimer();
                        updatePlayButton(false);
                        hideYouTubeElements();
                        updateProgress();
                    } else if (event.data == YT.PlayerState.PAUSED) {
                        stopWatchTimer();
                        updatePlayButton(true);
                        updateProgress();
                    } else if (event.data == YT.PlayerState.ENDED) {
                        markLessonComplete();
                    }
                }
            }
        });
    };
}

function hideYouTubeElements() {
    // محاولة إخفاء عناصر YouTube (محدود بسبب CORS)
    setTimeout(() => {
        const iframe = document.querySelector('#youtube-player iframe');
        if (iframe) {
            try {
                const style = document.createElement('style');
                style.textContent = `
                    .ytp-share-button,
                    .ytp-watch-later-button,
                    .ytp-title,
                    .ytp-chrome-top,
                    .ytp-show-cards-title,
                    .ytp-youtube-button {
                        display: none !important;
                        visibility: hidden !important;
                    }
                `;
                iframe.contentDocument?.head?.appendChild(style);
            } catch(e) {
                // تجاهل أخطاء CORS
            }
        }
    }, 1000);
}

function setupGenericIframePlayer(iframe) {
    iframe.style.pointerEvents = 'auto';
    iframe.addEventListener('load', function() {
        isVideoReady = true;
        startWatchTimer();
    });
}

function setupDirectVideoPlayer(video) {
    videoElement = video;

    function applySavedPosition() {
        if (savedWatchTimeSeconds > 0 && video.duration && isFinite(video.duration) && savedWatchTimeSeconds < video.duration) {
            video.currentTime = savedWatchTimeSeconds;
        }
        updateProgress();
    }

    video.addEventListener('loadeddata', function() {
        isVideoReady = true;
        if (video.duration && isFinite(video.duration) && video.duration > 0) {
            videoDurationSeconds = Math.floor(video.duration);
            reportLessonDurationOnce(videoDurationSeconds);
            applySavedPosition();
        }
    });
    video.addEventListener('loadedmetadata', function() {
        if (video.duration && isFinite(video.duration) && video.duration > 0 && videoDurationSeconds <= 0) {
            videoDurationSeconds = Math.floor(video.duration);
            reportLessonDurationOnce(videoDurationSeconds);
        }
        applySavedPosition();
    });
    
    video.addEventListener('play', function() {
        startWatchTimer();
        updatePlayButton(false);
    });
    
    video.addEventListener('pause', function() {
        stopWatchTimer();
        updatePlayButton(true);
    });
    
    video.addEventListener('timeupdate', function() {
        updateProgress();
    });
    
    video.addEventListener('ended', function() {
        markLessonComplete();
    });
    
    // منع التحميل
    video.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });
    
    // إزالة controls الافتراضية
    video.controls = false;
}

function startWatchTimer() {
    watchStartTime = Date.now();
}

function stopWatchTimer() {
    if (watchStartTime) {
        totalWatchTime += Math.floor((Date.now() - watchStartTime) / 1000);
        watchStartTime = null;
    }
}

function startProgressTracking() {
    progressInterval = setInterval(function() {
        if (isVideoReady) {
            updateProgress();
            if (watchStartTime) {
                updateWatchProgress();
            }
        }
    }, 400); // كل 400ms لعرض التقدم فوراً دون انتظار (كان 1000ms ويسبب تأخر ظهور الشريط حتى بعد تكبير/تصغير)
}

function updateWatchProgress() {
    const currentWatchTime = totalWatchTime + (watchStartTime ? Math.floor((Date.now() - watchStartTime) / 1000) : 0);
    const totalDuration = getVideoDuration() || videoDurationSeconds || {{ ($lesson->duration_minutes ?? 0) * 60 }};
    
    if (totalDuration > 0) {
        const progressPercent = Math.min(100, (currentWatchTime / totalDuration) * 100);
        var progressEl = document.getElementById('lesson-progress');
        if (progressEl) progressEl.textContent = Math.floor(progressPercent) + '%';
        
        if (currentWatchTime - lastProgressUpdate >= 30) {
            sendProgressUpdate(currentWatchTime, progressPercent, false);
            lastProgressUpdate = currentWatchTime;
        }
        if (!allowFlexibleSubmission && progressPercent >= 90 && !document.body.dataset.completed) {
            document.body.dataset.completed = 'true';
            markLessonComplete();
        }
    }
}

function sendProgressUpdate(watchTime, progressPercent, forceCompleted) {
    var completed = forceCompleted === true || (!allowFlexibleSubmission && progressPercent >= 90);
    return fetch(`/my-courses/{{ $course->id }}/lessons/{{ $lesson->id }}/progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            watch_time: watchTime,
            progress_percent: progressPercent,
            completed: completed
        })
    }).then(function(r) { return r.json(); }).catch(function(error) {
        console.error('Error updating progress:', error);
        return { success: false };
    });
}

function submitLessonManually() {
    try {
        if (document.body.dataset.completed === 'true') {
            showCompletionMessage();
            return;
        }
        var btn = document.getElementById('btn-submit-lesson');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التسليم...'; }
        var finalWatchTime = totalWatchTime + (watchStartTime ? Math.floor((Date.now() - watchStartTime) / 1000) : 0);
        var totalD = videoDurationSeconds || 1;
        try {
            if (typeof getVideoDuration === 'function') { var d = getVideoDuration(); if (d && d > 0) totalD = d; }
        } catch (e) { /* استخدم totalD الحالي */ }
        var progressPercent = (totalD > 0) ? Math.min(100, Math.round((finalWatchTime / totalD) * 100)) : 100;
        var progressUrl = '{{ route("my-courses.lesson.progress", [$course, $lesson]) }}';
        fetch(progressUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ watch_time: finalWatchTime, progress_percent: progressPercent, completed: true })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data && data.success) {
                document.body.dataset.completed = 'true';
                showCompletionMessage();
                if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-check-double ml-1"></i> تم التسليم'; }
            } else {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-double ml-1"></i> تسليم الحصة'; }
                alert('لم يتم التسليم. جرّب مرة أخرى.');
            }
        })
        .catch(function(err) {
            console.error('Submit lesson error:', err);
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-double ml-1"></i> تسليم الحصة'; }
            alert('حدث خطأ في الاتصال. تحقق من الإنترنت وجرّب مرة أخرى.');
        });
    } catch (e) {
        console.error('submitLessonManually:', e);
        var btn = document.getElementById('btn-submit-lesson');
        if (btn) btn.disabled = false;
        alert('حدث خطأ. جرّب مرة أخرى.');
    }
}

function markLessonComplete() {
    var finalWatchTime = totalWatchTime + (watchStartTime ? Math.floor((Date.now() - watchStartTime) / 1000) : 0);
    fetch(`/my-courses/{{ $course->id }}/lessons/{{ $lesson->id }}/progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            completed: true,
            watch_time: finalWatchTime,
            progress_percent: 100
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCompletionMessage();
        }
    })
    .catch(error => console.error('Error:', error));
}

function showCompletionMessage() {
    const message = document.createElement('div');
    message.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    message.innerHTML = `
        <div class="flex items-center space-x-2 space-x-reverse">
            <i class="fas fa-check-circle"></i>
            <span>تم إكمال الدرس بنجاح!</span>
        </div>
    `;
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.remove();
    }, 3000);
}

function showProtectionMessage(text) {
    const message = document.createElement('div');
    message.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    message.innerHTML = `
        <div class="flex items-center space-x-2 space-x-reverse">
            <i class="fas fa-shield-alt"></i>
            <span>${text}</span>
        </div>
    `;
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.remove();
    }, 2000);
}

function togglePlayPause() {
    if (youtubePlayer) {
        const state = youtubePlayer.getPlayerState();
        if (state === YT.PlayerState.PLAYING) {
            youtubePlayer.pauseVideo();
        } else {
            youtubePlayer.playVideo();
        }
    } else if (videoElement) {
        if (videoElement.paused) {
            playVideo();
        } else {
            pauseVideo();
        }
    }
}

function playVideo() {
    if (youtubePlayer) {
        youtubePlayer.playVideo();
    } else if (videoElement) {
        videoElement.play();
        updatePlayButton(false);
        startWatchTimer();
    }
}

function pauseVideo() {
    if (youtubePlayer) {
        youtubePlayer.pauseVideo();
    } else if (videoElement) {
        videoElement.pause();
        updatePlayButton(true);
        stopWatchTimer();
    }
}

function updatePlayButton(isPaused) {
    const icon = document.getElementById('play-pause-icon');
    if (icon) {
        icon.className = isPaused ? 'fas fa-play' : 'fas fa-pause';
    }
}

function toggleMute() {
    if (videoElement) {
        videoElement.muted = !videoElement.muted;
        const icon = document.getElementById('volume-icon');
        icon.className = videoElement.muted ? 'fas fa-volume-mute' : 'fas fa-volume-up';
    }
}

function toggleFullscreen() {
    const container = document.getElementById('video-container');
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        container.requestFullscreen();
    }
}

function seekToFromEvent(event) {
    if (!allowFlexibleSubmission) return;
    var track = event.currentTarget;
    var rect = track.getBoundingClientRect();
    var pos = Math.max(0, Math.min(1, (event.clientX - rect.left) / rect.width));
    var dur = getVideoDuration() || videoDurationSeconds || 1;
    var seconds = pos * dur;
    seekVideo(seconds);
    updateProgress();
}

var timelineDragging = false;
function startTimelineDrag(event) {
    if (event.button !== 0) return;
    event.preventDefault();
    timelineDragging = true;
    var track = document.getElementById('timeline-track');
    if (!track) return;
    function move(e) {
        if (!timelineDragging || !track) return;
        var rect = track.getBoundingClientRect();
        var pos = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
        var dur = getVideoDuration() || videoDurationSeconds || 1;
        seekVideo(pos * dur);
        updateProgress();
    }
    function stop() {
        timelineDragging = false;
        document.removeEventListener('mousemove', move);
        document.removeEventListener('mouseup', stop);
    }
    document.addEventListener('mousemove', move);
    document.addEventListener('mouseup', stop);
    move(event);
}

function getVideoCurrentTime() {
    if (youtubePlayer && typeof youtubePlayer.getCurrentTime === 'function') {
        return youtubePlayer.getCurrentTime();
    }
    if (videoElement) {
        return videoElement.currentTime;
    }
    return 0;
}

function getVideoDuration() {
    if (videoDurationSeconds > 0) return videoDurationSeconds;
    if (youtubePlayer && typeof youtubePlayer.getDuration === 'function') {
        var d = youtubePlayer.getDuration();
        if (d && d > 0) {
            videoDurationSeconds = Math.floor(d);
            return videoDurationSeconds;
        }
    }
    if (videoElement && videoElement.duration && isFinite(videoElement.duration)) {
        videoDurationSeconds = Math.floor(videoElement.duration);
        return videoDurationSeconds;
    }
    return 0;
}

function reportLessonDurationOnce(seconds) {
    if (durationReported || !seconds || seconds < 1) return;
    durationReported = true;
    fetch(reportDurationUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ duration_seconds: seconds })
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success && data.duration_minutes) videoDurationSeconds = data.duration_minutes * 60;
    }).catch(function() { durationReported = false; });
}

function seekVideo(seconds) {
    if (youtubePlayer && typeof youtubePlayer.seekTo === 'function') {
        youtubePlayer.seekTo(seconds, true);
    }
    if (videoElement) {
        videoElement.currentTime = seconds;
    }
}

function seekBy(deltaSeconds) {
    if (!allowFlexibleSubmission) return;
    var now = 0;
    try { now = getVideoCurrentTime(); } catch (e) {}
    var dur = videoDurationSeconds || 0;
    try { if (typeof getVideoDuration === 'function') dur = getVideoDuration() || dur; } catch (e) {}
    if (dur <= 0) dur = 3600;
    var next = Math.max(0, Math.min(dur, now + deltaSeconds));
    seekVideo(next);
    updateProgress();
}

function updateProgress() {
    let currentTime = getVideoCurrentTime();
    let duration = getVideoDuration();
    if (duration <= 0) duration = videoDurationSeconds;
    
    if (duration > 0) {
        const progress = (currentTime / duration) * 100;
        var bar = document.getElementById('progress-bar');
        if (bar) {
            bar.style.width = progress + '%';
            void bar.offsetHeight; // إجبار إعادة رسم الشريط
        }
        if (allowFlexibleSubmission && !timelineDragging) {
            var thumb = document.getElementById('progress-thumb');
            if (thumb) { thumb.style.left = progress + '%'; thumb.style.display = 'block'; thumb.classList.remove('hidden'); }
        }
        const currentTimeFloor = Math.floor(currentTime);
        const durationFloor = Math.floor(duration);
        var timeDisplay = document.getElementById('time-display');
        if (timeDisplay) timeDisplay.textContent = 
            `${Math.floor(currentTimeFloor / 60)}:${(currentTimeFloor % 60).toString().padStart(2, '0')} / ${Math.floor(durationFloor / 60)}:${(durationFloor % 60).toString().padStart(2, '0')}`;
    }

    // التحقق من نقاط التوقف (أسئلة الفيديو)
    if (videoCheckpoints.length && currentCheckpoint === null) {
        const t = Math.floor(currentTime);
        for (let i = 0; i < videoCheckpoints.length; i++) {
            const cp = videoCheckpoints[i];
            if (t >= cp.time_seconds && !videoCheckpointsShown[cp.id]) {
                videoCheckpointsShown[cp.id] = true;
                currentCheckpoint = { checkpoint: cp, index: i };
                pauseVideo();
                showVideoQuestionPopup(cp);
                break;
            }
        }
    }
}

function showVideoQuestionPopup(checkpoint) {
    const body = document.getElementById('video-question-body');
    const feedback = document.getElementById('video-question-feedback');
    const feedbackText = document.getElementById('video-question-feedback-text');
    const actions = document.getElementById('video-question-actions');
    const submitBtn = document.getElementById('video-question-submit');
    feedback.classList.add('hidden');
    feedbackText.textContent = '';
    submitBtn.disabled = false;
    const q = checkpoint.question || {};
    let html = '<p class="text-gray-900 dark:text-white font-medium mb-4">' + (q.question || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p>';
    const type = (q.type || 'multiple_choice').toLowerCase();
    if (type === 'true_false') {
        html += '<div class="space-y-2" data-question-type="true_false">';
        html += '<button type="button" class="video-q-option w-full text-right px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 hover:border-primary-500 dark:hover:border-primary-500 transition-colors" data-answer="صح">صح</button>';
        html += '<button type="button" class="video-q-option w-full text-right px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 hover:border-primary-500 dark:hover:border-primary-500 transition-colors" data-answer="خطأ">خطأ</button>';
        html += '</div>';
    } else if ((type === 'multiple_choice' || type === 'image_multiple_choice') && q.options && q.options.length) {
        html += '<div class="space-y-2" data-question-type="multiple_choice">';
        q.options.forEach(function(opt, idx) {
            html += '<button type="button" class="video-q-option w-full text-right px-4 py-3 rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 hover:border-primary-500 dark:hover:border-primary-500 transition-colors" data-answer="' + String(opt).replace(/"/g, '&quot;') + '" data-index="' + idx + '">' + String(opt).replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</button>';
        });
        html += '</div>';
    } else {
        html += '<div data-question-type="other"><input type="text" id="video-q-text-answer" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" placeholder="اكتب إجابتك"></div>';
    }
    body.innerHTML = html;
    body.querySelectorAll('.video-q-option').forEach(function(btn) {
        btn.addEventListener('click', function() {
            body.querySelectorAll('.video-q-option').forEach(function(b) { b.classList.remove('border-primary-600', 'bg-primary-50', 'dark:bg-primary-900'); });
            this.classList.add('border-primary-600', 'bg-primary-50', 'dark:bg-primary-900');
            body.dataset.selectedAnswer = this.getAttribute('data-answer');
        });
    });
    document.getElementById('video-question-popup').classList.remove('hidden');
    document.getElementById('video-question-popup').classList.add('flex');
}

function closeVideoQuestionPopup() {
    document.getElementById('video-question-popup').classList.add('hidden');
    document.getElementById('video-question-popup').classList.remove('flex');
    currentCheckpoint = null;
    playVideo();
}

function getVideoQuestionAnswer() {
    const body = document.getElementById('video-question-body');
    const selected = body.dataset.selectedAnswer;
    if (selected !== undefined && selected !== '') return selected;
    const input = document.getElementById('video-q-text-answer');
    return (input && input.value) ? input.value : '';
}

function applyOnWrong(onWrong, checkpointIndex) {
    if (onWrong === 'restart_video') {
        seekVideo(0);
        playVideo();
    } else if (onWrong === 'rewind_to_previous') {
        const prevTime = checkpointIndex > 0 ? videoCheckpoints[checkpointIndex - 1].time_seconds : 0;
        seekVideo(prevTime);
        videoCheckpointsShown[videoCheckpoints[checkpointIndex].id] = false;
        playVideo();
    } else {
        playVideo();
    }
}

document.getElementById('video-question-submit').addEventListener('click', function() {
    if (!currentCheckpoint) return;
    const answer = getVideoQuestionAnswer();
    if (!answer && !document.getElementById('video-q-text-answer')?.value) return;
    const submitBtn = document.getElementById('video-question-submit');
    submitBtn.disabled = true;
    const vqId = currentCheckpoint.checkpoint.id;
    const cpIndex = currentCheckpoint.index;
    const onWrong = currentCheckpoint.checkpoint.on_wrong || 'training';
    fetch(videoQuestionAnswerUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ video_question_id: vqId, answer: answer })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        const feedback = document.getElementById('video-question-feedback');
        const feedbackText = document.getElementById('video-question-feedback-text');
        feedback.classList.remove('hidden');
        if (data.correct) {
            feedbackText.textContent = 'إجابة صحيحة!';
            feedbackText.className = 'font-medium text-green-600 dark:text-green-400';
            setTimeout(function() {
                closeVideoQuestionPopup();
            }, 800);
        } else {
            feedbackText.textContent = 'إجابة خاطئة. ' + (onWrong === 'training' ? 'يمكنك المتابعة.' : 'سيتم تطبيق الإجراء المحدد.');
            feedbackText.className = 'font-medium text-red-600 dark:text-red-400';
            setTimeout(function() {
                closeVideoQuestionPopup();
                applyOnWrong(onWrong, cpIndex);
            }, 1200);
        }
    })
    .catch(function() {
        submitBtn.disabled = false;
        alert('حدث خطأ في التحقق من الإجابة.');
    });
});

function exitLesson() {
    var el = document.getElementById('exit-warning');
    if (el) {
        el.classList.remove('hidden');
        el.classList.add('flex');
    }
}

// عند الضغط على السهم الخلفي في الصفحة الأم: الصفحة الأم ترسل رسالة لفتح نافذة التأكيد
window.addEventListener('message', function(event) {
    if (event.data === 'showExitModal' && typeof exitLesson === 'function') {
        exitLesson();
    }
});

function confirmExit() {
    stopWatchTimer();
    if (progressInterval) {
        clearInterval(progressInterval);
    }
    var finalWatchTime = totalWatchTime + (watchStartTime ? Math.floor((Date.now() - watchStartTime) / 1000) : 0);
    var totalD = getVideoDuration() || videoDurationSeconds;
    var progressPercent = (totalD > 0) ? Math.min(100, (finalWatchTime / totalD) * 100) : 0;
    var targetUrl = '{{ route("my-courses.index") }}';
    function doRedirect() {
        if (window.self !== window.top) {
            window.top.location.href = targetUrl;
        } else {
            window.location.href = targetUrl;
        }
    }
    // انتظار حفظ التقدم قبل التوجيه حتى يظهر للطالب والإدمن بعد العودة
    var progressBtn = document.getElementById('confirm-exit-btn');
    if (progressBtn) {
        progressBtn.disabled = true;
        progressBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الحفظ...';
    }
    sendProgressUpdate(finalWatchTime, progressPercent).then(function() {
        doRedirect();
    }).catch(function() {
        doRedirect();
    });
    setTimeout(doRedirect, 5000);
}

function cancelExit() {
    document.getElementById('exit-warning').classList.add('hidden');
}

// عند المغادرة (السهم الخلفي أو إغلاق التبويب): محاولة حفظ التقدم بدون عرض رسالة المتصفح
window.addEventListener('beforeunload', function(e) {
    if (!isVideoReady) return;
    var finalWatchTime = totalWatchTime + (watchStartTime ? Math.floor((Date.now() - watchStartTime) / 1000) : 0);
    var totalD = getVideoDuration ? (getVideoDuration() || videoDurationSeconds) : videoDurationSeconds;
    var progressPercent = (totalD > 0) ? Math.min(100, (finalWatchTime / totalD) * 100) : 0;
    var fd = new FormData();
    fd.append('watch_time', finalWatchTime);
    fd.append('progress_percent', progressPercent);
    fd.append('completed', progressPercent >= 90 ? '1' : '0');
    fd.append('_token', '{{ csrf_token() }}');
    navigator.sendBeacon('{{ route("my-courses.lesson.progress", [$course, $lesson]) }}', fd);
});

// تنظيف عند إغلاق الصفحة
window.addEventListener('unload', function() {
    stopWatchTimer();
    if (progressInterval) {
        clearInterval(progressInterval);
    }
});

// منع نسخ المحتوى
document.addEventListener('copy', function(e) {
    e.preventDefault();
    showProtectionMessage('النسخ معطل');
});

// منع تحديد النص
document.addEventListener('selectstart', function(e) {
    e.preventDefault();
});

// كشف أدوات المطور
let devtools = {open: false, orientation: null};
setInterval(function() {
    if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
        if (!devtools.open) {
            devtools.open = true;
            showProtectionMessage('أدوات المطور معطلة');
            // يمكن إضافة إجراءات إضافية هنا
        }
    } else {
        devtools.open = false;
    }
}, 500);

// حماية شاملة من التسجيل (كمبيوتر + هاتف) — إبلاغ وتعليق
navigator.mediaDevices.getDisplayMedia = function() {
    reportViolationToServer('recording');
    activateScreenshotProtection();
    showProtectionMessage('تسجيل الشاشة ممنوع - تم الإبلاغ عن المخالفة وسيتم تعليق حسابك');
    return Promise.reject(new Error('Screen recording is disabled'));
};

// منع استخدام MediaRecorder لتسجيل الفيديو (سكرين ريكورد من الصفحة أو الهاتف)
var OriginalMediaRecorder = window.MediaRecorder;
if (OriginalMediaRecorder) {
    window.MediaRecorder = function(stream, options) {
        if (stream && stream.getVideoTracks && stream.getVideoTracks().length > 0) {
            reportViolationToServer('recording');
            showProtectionMessage('تسجيل الشاشة ممنوع - تم الإبلاغ عن المخالفة وسيتم تعليق حسابك');
            throw new Error('Screen recording is disabled');
        }
        return new OriginalMediaRecorder(stream, options);
    };
    window.MediaRecorder.prototype = OriginalMediaRecorder.prototype;
}

// منع captureStream على الفيديو والكانفس (لتسجيل المحتوى — كمبيوتر أو هاتف)
if (HTMLVideoElement.prototype.captureStream) {
    var origVideoCapture = HTMLVideoElement.prototype.captureStream;
    HTMLVideoElement.prototype.captureStream = function() {
        reportViolationToServer('recording');
        showProtectionMessage('تسجيل الشاشة ممنوع - تم الإبلاغ وسيتم تعليق حسابك');
        throw new Error('Screen recording is disabled');
    };
}
if (HTMLCanvasElement.prototype.captureStream) {
    var origCanvasCapture = HTMLCanvasElement.prototype.captureStream;
    HTMLCanvasElement.prototype.captureStream = function() {
        reportViolationToServer('recording');
        showProtectionMessage('تسجيل الشاشة ممنوع - تم الإبلاغ وسيتم تعليق حسابك');
        throw new Error('Screen recording is disabled');
    };
}

// كشف الهاتف (سكرين ريكورد من نظام الهاتف = إخفاء الصفحة متكرر)
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
           (navigator.maxTouchPoints && navigator.maxTouchPoints > 2);
}

// مراقبة إخفاء الصفحة المتكرر (سكرين شوت / سكرين ريكورد من الهاتف أو الكمبيوتر)
let visibilityHiddenCount = 0;
let visibilityResetAt = 0;
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        visibilityHiddenCount++;
        if (visibilityResetAt === 0) visibilityResetAt = Date.now();
        var windowMs = isMobileDevice() ? 25000 : 35000;
        var threshold = isMobileDevice() ? 2 : 4;
        if (visibilityHiddenCount >= threshold && (Date.now() - visibilityResetAt) < windowMs) {
            reportViolationToServer(isMobileDevice() ? 'recording' : 'screenshot');
            showProtectionMessage('تم رصد سلوك يشبه التصوير أو تسجيل الشاشة - تم الإبلاغ وسيتم تعليق حسابك');
        }
        activateScreenshotProtection();
    } else {
        if (Date.now() - visibilityResetAt > (isMobileDevice() ? 25000 : 35000)) {
            visibilityHiddenCount = 0;
            visibilityResetAt = 0;
        }
    }
});

// على الهاتف: عند الخروج من الصفحة (سكرين ريكورد من النظام) نعتمد على visibilitychange أعلاه بحد أقسى (٢ في ٢٥ ثانية)

// مراقبة تغيير حجم النافذة (محاولة تصوير)
window.addEventListener('resize', function() {
    if (Math.abs(window.outerWidth - window.innerWidth) > 200 || 
        Math.abs(window.outerHeight - window.innerHeight) > 200) {
        activateScreenshotProtection();
    }
});

// مراقبة Focus/Blur للنافذة
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        activateScreenshotProtection();
        if (youtubePlayer) {
            youtubePlayer.pauseVideo();
        } else if (videoElement && !videoElement.paused) {
            videoElement.pause();
        }
    }
});

// حماية من Copy/Paste (اللصق مع صورة = سكرين شوت معالَج أعلاه)
document.addEventListener('copy', function(e) {
    e.preventDefault();
    activateScreenshotProtection();
    showProtectionMessage('النسخ معطل');
});

// منع السحب
document.addEventListener('dragstart', function(e) {
    e.preventDefault();
    activateScreenshotProtection();
});

// مراقبة محاولات الوصول للـ canvas
Object.defineProperty(HTMLCanvasElement.prototype, 'toDataURL', {
    value: function() {
        activateScreenshotProtection();
        showProtectionMessage('استخراج البيانات معطل');
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
    }
});

// حماية إضافية للفيديو بدون تشويه
setInterval(function() {
    const iframes = document.querySelectorAll('iframe');
    iframes.forEach(iframe => {
        // إزالة أي أزرار مشاركة قد تظهر
        try {
            if (iframe.contentDocument) {
                const shareButtons = iframe.contentDocument.querySelectorAll('[aria-label*="Share"], [title*="Share"], .ytp-share-button');
                shareButtons.forEach(btn => btn.style.display = 'none');
            }
        } catch(e) {
            // تجاهل الأخطاء بسبب CORS
        }
    });
}, 1000);
</script>

<style>
/* منع تحديد النص */
* {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-touch-callout: none;
    -webkit-tap-highlight-color: transparent;
}

/* إخفاء شريط التمرير */
::-webkit-scrollbar {
    display: none;
}

/* منع السحب */
* {
    -webkit-user-drag: none;
    -khtml-user-drag: none;
    -moz-user-drag: none;
    -o-user-drag: none;
    user-drag: none;
}

/* حماية قوية من التصوير */
body {
    -webkit-touch-callout: none !important;
    -webkit-user-select: none !important;
    -khtml-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    overflow: hidden !important;
}

/* حماية Canvas من التصوير */
canvas {
    image-rendering: pixelated !important;
    image-rendering: -moz-crisp-edges !important;
    image-rendering: crisp-edges !important;
}

/* إخفاء أدوات التحكم في الفيديو المدمج */
iframe {
    pointer-events: auto !important;
    border: none !important;
}

/* إخفاء عناصر YouTube */
iframe[src*="youtube"] {
    position: relative !important;
}

iframe[src*="youtube"]::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: transparent;
    z-index: 1;
    pointer-events: none;
}

/* إخفاء شريط العنوان في Vimeo */
iframe[src*="vimeo"] .vp-overlay,
iframe[src*="vimeo"] .vp-title,
iframe[src*="vimeo"] .vp-byline {
    display: none !important;
    visibility: hidden !important;
}

/* منع الطباعة */
@media print {
    body { 
        display: none !important; 
        visibility: hidden !important;
    }
}

/* العلامات المائية المتحركة */
.watermark-1 {
    top: 20%;
    left: 15%;
    transform: rotate(15deg);
    animation: float1 6s ease-in-out infinite;
}

.watermark-2 {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-10deg);
    animation: float2 8s ease-in-out infinite;
}

.watermark-3 {
    bottom: 25%;
    right: 20%;
    transform: rotate(25deg);
    animation: float3 7s ease-in-out infinite;
}

@keyframes float1 {
    0%, 100% { transform: rotate(15deg) translateY(0px); }
    50% { transform: rotate(15deg) translateY(-20px); }
}

@keyframes float2 {
    0%, 100% { transform: translate(-50%, -50%) rotate(-10deg) scale(1); }
    50% { transform: translate(-50%, -50%) rotate(-10deg) scale(1.1); }
}

@keyframes float3 {
    0%, 100% { transform: rotate(25deg) translateX(0px); }
    50% { transform: rotate(25deg) translateX(15px); }
}

/* حماية من تسجيل الشاشة */
#video-container {
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
    will-change: transform;
    backface-visibility: hidden;
}

/* طبقة حماية ديناميكية */
.screenshot-blocker {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: black !important;
    z-index: 9999 !important;
    pointer-events: none !important;
    opacity: 0 !important;
    transition: opacity 0.1s ease !important;
}

.screenshot-blocker.active {
    opacity: 1 !important;
}
</style>
@endpush
@endsection
