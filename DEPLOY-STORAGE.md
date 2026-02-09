# تعليمات رفع المنصة وحل مشكلة عدم ظهور الصور

## قبل الرفع (تم تطبيقه في المشروع)

- [x] **config/filesystems.php**: قرص `local` يحتوي على `'serve' => false` لتفادي تداخل Route الافتراضي `storage.local`.
- [x] **public/.htaccess**: لا توجد قواعد تسمح بالوصول المباشر لـ `/storage/` وتتخطى Laravel (الطلبات تمر إلى `index.php`).
- [x] **routes/web.php**: Route مخصص `/storage/{path}` في **بداية الملف** باسم `storage.file` مع فحص أمان و logging.

---

## لماذا قد لا تظهر الصور بعد الرفع؟

1. **الملفات غير موجودة على السيرفر**: مجلد `storage/app/public` (ومحتوياته مثل `questions/`, `courses/`) **لا يُرفع مع Git** عادةً لأنه في `.gitignore`. لذلك الصور التي رفعتها على جهازك المحلي غير موجودة على السيرفر. الحل: إما نسخ محتويات `storage/app/public` من جهازك إلى السيرفر (مثلاً عبر rsync أو SCP)، أو إعادة رفع الصور من لوحة التحكم بعد الرفع.
2. **صلاحيات**: السيرفر (مستخدم الويب مثل `apache` أو `nobody` أو المستخدم الخاص بالاستضافة) يجب أن يستطيع **قراءة** مجلد `storage/app/public` وملفات بداخله.
3. **المسار أو الـ Route**: التأكد أن طلبات `/storage/...` تصل إلى Laravel (لا يحجبها .htaccess أو خادم الويب).

---

## بعد الرفع على السيرفر

### 1. صلاحيات المجلدات

```bash
# من مجلد المشروع (الجذر الذي فيه artisan)
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage
chown -R www-data:www-data storage bootstrap/cache
```

(استبدل `www-data` باسم مستخدم الويب إن كان مختلفاً، مثلاً `apache` أو `nobody`.)

### 2. التأكد من وجود المجلدات

```bash
# من مجلد المشروع (نفس المجلد الذي فيه artisan)
mkdir -p storage/app/public
mkdir -p storage/app/public/questions
mkdir -p storage/app/public/courses
mkdir -p storage/app/public/course-attachments
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage
# إن وُجد مستخدم الويب (مثلاً www-data أو نفس مستخدم الاستضافة):
# chown -R $USER:www-data storage
```

### 3. مسح جميع الـ Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 4. التحقق من Route التخزين

```bash
php artisan route:list | grep storage
```

المفترض أن يظهر: **`storage.file`** وليس `storage.local`.

### 5. (اختياري) إنشاء الرابط الرمزي

إذا كان السيرفر يدعم symlinks:

```bash
php artisan storage:link
```

إن لم يعمل الـ symlink (مثلاً استضافة مشتركة)، الاعتماد على Route المخصص `/storage/{path}` كافٍ ولا حاجة للرابط الرمزي.

### 6. اختبار الصور

**أ) إنشاء ملف اختبار ثم طلبه:**

```bash
# إنشاء ملف اختبار داخل storage/app/public
echo 'test' > storage/app/public/test.txt

# من السيرفر أو من جهازك (استبدل النطاق)
curl -I https://yourdomain.com/storage/test.txt
```

المفترض: **200 OK**. إن حصلت على 404، راجع السجلات (انظر استكشاف الأخطاء).

**ب) اختبار صورة حقيقية:**  
ضع صورة في `storage/app/public/example.jpg` ثم اطلب:

```bash
curl -I https://yourdomain.com/storage/example.jpg
```

### 7. إذا كانت الصور قديمة (من البيئة المحلية)

الملفات المرفوعة محلياً **لا تنتقل مع git**. لنسخها إلى السيرفر من جهازك:

```bash
# من جهازك (استبدل user و host و path)
rsync -avz storage/app/public/ user@your-server:/path/to/eldagen/storage/app/public/
```

أو ارفع الصور من جديد من لوحة التحكم بعد الرفع.

---

## استكشاف الأخطاء

- **الصورة تُرفع ولا تظهر**: افتح رابط الصورة مباشرة في المتصفح (من زر "فتح في تاب جديد" أو انسخ رابط الصورة من خاصية `src` في العنصر `<img>`). ثم راجع `storage/logs/laravel.log` وابحث عن `Storage file not found` أو `Storage access denied` أو `Storage base path does not exist`. ستجد `requested_path` و `file_path` و `base_path` لمعرفة السبب. على استضافة مشتركة تم تعديل الـ Route ليعمل حتى عند فشل `realpath()`.
- **404 للصور**: نفس الخطوة أعلاه؛ تأكد أن الملف موجود فعلاً في `storage/app/public/` على السيرفر (مثلاً عبر SSH: `ls -la storage/app/public/questions/`).
- **Route خاطئ**: بعد أي تعديل على `routes/web.php` أو `config/filesystems.php` نفّذ:
  `php artisan config:clear` و `php artisan route:clear`.
- **صلاحيات**: تأكد أن مستخدم تشغيل الويب يقرأ مجلد `storage` ومحتوياته. على استضافة مشتركة قد يكون المستخدم هو نفس مستخدم SSH؛ في هذه الحالة `chmod -R 775 storage` عادةً كافٍ.
- **Document root**: تأكد أن جذر الموقع يشير إلى مجلد `public` داخل المشروع حتى تصل طلبات `/storage/...` إلى `index.php`.
- **APP_URL**: في `.env` على السيرفر تأكد أن `APP_URL` يطابق عنوان الموقع (مثلاً `https://yourdomain.com`) دون شرطة نهائية.