<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\School;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // كلمة المرور النصية (نموذج User يستخدم cast 'hashed' فيخزنها مرة واحدة فقط)
        $password = 'password123';

        // إنشاء مستخدم إداري (صيغة مصر: 01xxxxxxxxx)
        $admin = User::firstOrCreate(
            ['email' => 'admin@learningplatform.com'],
            [
                'phone' => '01000000000',
                'name' => 'المدير العام',
                'password' => $password,
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $admin->password = $password;
        $admin->save();

        // إنشاء مدرس تجريبي
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@learningplatform.com'],
            [
                'phone' => '01000000001',
                'name' => 'أحمد محمد',
                'password' => $password,
                'role' => 'teacher',
                'is_active' => true,
                'bio' => 'مدرس رياضيات مع خبرة 10 سنوات في التدريس',
            ]
        );
        $teacher->password = $password;
        $teacher->save();

        // إنشاء طالب تجريبي
        $student = User::firstOrCreate(
            ['email' => 'student@learningplatform.com'],
            [
                'phone' => '01000000002',
                'name' => 'فاطمة علي',
                'password' => $password,
                'role' => 'student',
                'is_active' => true,
            ]
        );
        $student->password = $password;
        $student->save();

        // إنشاء ولي أمر تجريبي
        $parent = User::firstOrCreate(
            ['email' => 'parent@learningplatform.com'],
            [
                'phone' => '01000000003',
                'name' => 'محمد أحمد',
                'password' => $password,
                'role' => 'parent',
                'is_active' => true,
            ]
        );
        $parent->password = $password;
        $parent->save();

        // إنشاء مدرسة تجريبية
        School::firstOrCreate(
            ['name' => 'مدرسة النور الابتدائية'],
            [
                'description' => 'مدرسة ابتدائية متميزة تهتم بتطوير قدرات الطلاب',
                'address' => 'الرياض، المملكة العربية السعودية',
                'phone' => '0112345678',
                'email' => 'info@alnoor.edu.sa',
                'is_active' => true,
            ]
        );

        // إنشاء مواد دراسية تجريبية
        $subjects = [
            [
                'name' => 'الرياضيات',
                'description' => 'تعلم الأرقام والعمليات الحسابية والهندسة',
                'color' => '#3B82F6',
                'icon' => 'fas fa-calculator',
            ],
            [
                'name' => 'العلوم',
                'description' => 'استكشاف الطبيعة والكيمياء والفيزياء',
                'color' => '#10B981',
                'icon' => 'fas fa-flask',
            ],
            [
                'name' => 'اللغة العربية',
                'description' => 'تطوير مهارات القراءة والكتابة والتعبير',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-book',
            ],
            [
                'name' => 'اللغة الإنجليزية',
                'description' => 'تعلم اللغة الإنجليزية من الأساسيات إلى المستوى المتقدم',
                'color' => '#F59E0B',
                'icon' => 'fas fa-globe',
            ],
            [
                'name' => 'التاريخ',
                'description' => 'دراسة الأحداث التاريخية والحضارات',
                'color' => '#EF4444',
                'icon' => 'fas fa-landmark',
            ],
            [
                'name' => 'الجغرافيا',
                'description' => 'دراسة الأرض والبيئة والمناخ',
                'color' => '#06B6D4',
                'icon' => 'fas fa-map',
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['name' => $subject['name']],
                $subject
            );
        }

        $this->command->info('تم إنشاء المستخدمين والبيانات التجريبية بنجاح!');
        $this->command->info('بيانات الدخول (صيغة مصر 01):');
        $this->command->info('المدير: 01000000000 / password123');
        $this->command->info('المدرس: 01000000001 / password123');
        $this->command->info('الطالب: 01000000002 / password123');
        $this->command->info('ولي الأمر: 01000000003 / password123');
    }
}
