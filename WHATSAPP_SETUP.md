# إعداد WhatsApp Business API

## المتطلبات الأساسية

### 1. إنشاء حساب Facebook Developer
1. اذهب إلى: https://developers.facebook.com
2. أنشئ حساب جديد أو سجل دخول
3. أنشئ تطبيق جديد من نوع "Business"

### 2. إعداد WhatsApp Business API
1. في لوحة تطبيق Facebook، اذهب إلى "WhatsApp" > "API Setup"
2. احصل على:
   - **Phone Number ID** (معرف رقم الهاتف)
   - **WhatsApp Business Account ID**
   - **Access Token**

### 3. تكوين ملف .env
```env
# WhatsApp Business API Configuration
WHATSAPP_ENABLED=true
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_API_TOKEN=your_permanent_access_token_here
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id_here
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_custom_verify_token
WHATSAPP_APP_SECRET=your_app_secret

# Platform Settings
PLATFORM_SUPPORT_PHONE=+201000000000
PLATFORM_SUPPORT_EMAIL=support@platform.com
MONTHLY_REPORTS_ENABLED=true
AUTO_SEND_EXAM_RESULTS=true
```

### 4. التحقق من رقم الهاتف
- يجب التحقق من رقم الهاتف في WhatsApp Business
- الرقم يجب أن يكون مسجل باسم المؤسسة
- تأكد من الموافقة على شروط WhatsApp Business

### 5. أذونات API المطلوبة
- `whatsapp_business_messaging`
- `whatsapp_business_management`

## اختبار النظام

### الأمر للاختبار:
```bash
php artisan tinker

# اختبار إرسال رسالة
$service = app(\App\Services\WhatsAppService::class);
$result = $service->sendMessage('+201234567890', 'رسالة اختبار من المنصة');
dd($result);
```

### التحقق من الإعدادات:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## الأخطاء الشائعة وحلولها

### خطأ "Object with ID 'messages' does not exist"
- تحقق من صحة Access Token
- تأكد من أن Phone Number ID صحيح  
- تحقق من الأذونات المطلوبة

### خطأ "Invalid phone number"
- تأكد من تنسيق الرقم: +countrycode+number
- مثال: +201234567890 (مصر)

### خطأ الأذونات
- تحقق من Business Verification
- تأكد من موافقة Meta على التطبيق

## إعداد البيئة المحلية (للاختبار)

يمكنك أولاً اختبار النظام بدون WhatsApp:

```env
WHATSAPP_ENABLED=false
```

سيتم حفظ الرسائل في قاعدة البيانات فقط بدون إرسال فعلي.
