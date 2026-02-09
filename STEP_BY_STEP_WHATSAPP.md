# دليل إعداد WhatsApp API مجاني 100%

## 🎯 الهدف: إرسال رسائل واتساب مجاناً من منصتك

---

## ⚡ **الطريقة الأولى: wppconnect (الأسهل والأسرع)**

### 1. تثبيت Node.js
```bash
# حمل وثبت Node.js من: https://nodejs.org
```

### 2. إنشاء خدمة WhatsApp
```bash
# إنشاء مجلد جديد
mkdir whatsapp-api
cd whatsapp-api

# تثبيت المكتبة
npm init -y
npm install @wppconnect-team/wppconnect express cors
```

### 3. إنشاء ملف server.js
```javascript
const wppconnect = require('@wppconnect-team/wppconnect');
const express = require('express');
const cors = require('cors');

const app = express();
app.use(express.json());
app.use(cors());

let client;

// إنشاء اتصال WhatsApp
wppconnect
  .create({
    session: 'learning-platform',
    headless: false, // سيفتح كروم لمسح QR
    devtools: false,
    debug: false,
    logQR: true
  })
  .then((client) => {
    console.log('✅ WhatsApp متصل بنجاح!');
    global.client = client;
  })
  .catch((error) => {
    console.error('❌ خطأ في الاتصال:', error);
  });

// API endpoint لإرسال الرسائل
app.post('/send-message', async (req, res) => {
    try {
        if (!global.client) {
            return res.json({ 
                success: false, 
                error: 'WhatsApp غير متصل' 
            });
        }

        const { phone, message } = req.body;
        
        // تنسيق الرقم (إضافة @c.us)
        let formattedPhone = phone.replace(/[^0-9]/g, '');
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '2' + formattedPhone; // للأرقام المصرية
        }
        formattedPhone += '@c.us';

        // إرسال الرسالة
        await global.client.sendText(formattedPhone, message);
        
        console.log(`✅ رسالة مرسلة إلى: ${phone}`);
        
        res.json({ 
            success: true, 
            message: 'تم إرسال الرسالة بنجاح',
            phone: formattedPhone
        });
        
    } catch (error) {
        console.error('❌ خطأ في الإرسال:', error);
        res.json({ 
            success: false, 
            error: error.message 
        });
    }
});

// التحقق من حالة الاتصال
app.get('/status', (req, res) => {
    res.json({ 
        connected: !!global.client,
        timestamp: new Date().toISOString()
    });
});

const PORT = 3001;
app.listen(PORT, () => {
    console.log(`🚀 WhatsApp API Server running on port ${PORT}`);
    console.log(`📱 للاختبار: http://localhost:${PORT}/status`);
});
```

### 4. تشغيل الخدمة
```bash
node server.js
```

**📸 ستفتح نافذة كروم - امسح QR Code بواتساب الويب على هاتفك**

---

## ⚙️ **إعداد Laravel للاستخدام**

### 5. تحديث ملف .env
```env
# إعداد الواتساب المجاني
WHATSAPP_TYPE=local
WHATSAPP_LOCAL_API_URL=http://localhost:3001
WHATSAPP_ENABLED=true

# معلومات المنصة
PLATFORM_SUPPORT_PHONE=+201000000000
MONTHLY_REPORTS_ENABLED=true
AUTO_SEND_EXAM_RESULTS=true
```

### 6. اختبار النظام
```bash
# في terminal Laravel
php artisan tinker

# اختبار إرسال رسالة
$service = app(\App\Services\WhatsAppService::class);
$result = $service->sendMessage('01012345678', 'مرحباً! هذه رسالة اختبار من منصة التعلم 🎓');
dd($result);
```

---

## 🎯 **الطريقة الثانية: whatsapp-web.js (أكثر استقراراً)**

### 1. خدمة بديلة
```bash
mkdir whatsapp-service
cd whatsapp-service
npm init -y
npm install whatsapp-web.js qrcode-terminal express cors
```

### 2. ملف app.js محسن
```javascript
const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');
const cors = require('cors');

const app = express();
app.use(express.json());
app.use(cors());

const client = new Client({
    authStrategy: new LocalAuth({
        clientId: "learning-platform"
    }),
    puppeteer: {
        headless: false,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    }
});

let isReady = false;

client.on('qr', (qr) => {
    console.log('📱 امسح QR Code بهاتفك:');
    qrcode.generate(qr, {small: true});
});

client.on('ready', () => {
    console.log('✅ WhatsApp جاهز للإرسال!');
    isReady = true;
});

client.on('authenticated', () => {
    console.log('🔐 تم التحقق بنجاح');
});

client.on('auth_failure', msg => {
    console.error('❌ فشل التحقق:', msg);
});

client.on('disconnected', (reason) => {
    console.log('🔌 انقطع الاتصال:', reason);
    isReady = false;
});

// API Routes
app.post('/send', async (req, res) => {
    if (!isReady) {
        return res.json({ 
            success: false, 
            error: 'WhatsApp غير متصل. تحقق من QR Code' 
        });
    }

    try {
        const { phone, message } = req.body;
        
        if (!phone || !message) {
            return res.json({ 
                success: false, 
                error: 'رقم الهاتف والرسالة مطلوبان' 
            });
        }

        // تنسيق الرقم
        let number = phone.replace(/[^0-9]/g, '');
        
        // للأرقام المصرية
        if (number.startsWith('0')) {
            number = '2' + number;
        } else if (!number.startsWith('2')) {
            number = '2' + number;
        }
        
        number += '@c.us';

        // التحقق من صحة الرقم
        const isValidNumber = await client.isRegisteredUser(number);
        if (!isValidNumber) {
            return res.json({ 
                success: false, 
                error: 'رقم الهاتف غير مسجل في واتساب' 
            });
        }

        // إرسال الرسالة
        const result = await client.sendMessage(number, message);
        
        console.log(`✅ رسالة مرسلة إلى: ${phone}`);
        
        res.json({ 
            success: true, 
            messageId: result.id._serialized,
            phone: number
        });
        
    } catch (error) {
        console.error('❌ خطأ:', error);
        res.json({ 
            success: false, 
            error: error.message 
        });
    }
});

app.get('/status', (req, res) => {
    res.json({ 
        ready: isReady,
        timestamp: new Date()
    });
});

const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
    console.log(`🚀 WhatsApp API running on port ${PORT}`);
});

client.initialize();
```

---

## 🚀 **تشغيل النظام**

### 1. تشغيل خدمة WhatsApp (terminal منفصل)
```bash
cd whatsapp-service
node app.js
```

### 2. مسح QR Code
- ستفتح نافذة كروم
- امسح QR Code بواتساب الويب في هاتفك
- انتظر رسالة "WhatsApp جاهز للإرسال!"

### 3. تحديث Laravel
```env
WHATSAPP_TYPE=local
WHATSAPP_LOCAL_API_URL=http://localhost:3001
```

### 4. اختبار من لوحة الإدارة
- اذهب إلى **الرسائل والتقارير**
- اختر **رسالة جديدة** 
- اختر طالب وأرسل رسالة تجريبية

---

## 🆓 **الطريقة الثالثة: Baileys (أكثر تقدماً)**

### للمطورين المتقدمين فقط
```bash
npm install @whiskeysockets/baileys qrcode-terminal
```

---

## ⚠️ **ملاحظات مهمة:**

### ✅ **المسموح:**
- استخدام رقم واتساب شخصي للمنصة
- إرسال رسائل تعليمية للطلاب
- إرسال تقارير لأولياء الأمور

### ❌ **احذر من:**
- إرسال رسائل عشوائية (Spam)
- استخدام أرقام متعددة بسرعة
- إرسال رسائل تجارية مكثفة

### 🔒 **الأمان:**
- احتفظ بنسخة احتياطية من session
- استخدم رقم مخصص للمنصة
- لا تشارك QR Code مع أحد

---

## 🎯 **اختبار سريع الآن:**

يمكنك تجربة النظام فوراً بوضع التجربة:

```env
WHATSAPP_TYPE=disabled
```

ستحفظ الرسائل في قاعدة البيانات وترى واجهة كاملة!
