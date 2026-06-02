import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

// تكوين أداة البناء Vite لمشروع Laravel (Vite Configuration)
export default defineConfig({
    plugins: [
        // تعريف الملحق الخاص بـ Laravel وتحديد نقاط الدخول لتجميع ملفات CSS و JavaScript
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true, // تفعيل التحديث التلقائي للصفحات في المتصفح عند تعديل ملفات PHP أو Blade أو الأكواد
        }),
        tailwindcss(), // ملحق Tailwind CSS لتجميع التنسيقات وتجهيزها
    ],
    server: {
        watch: {
            // تجاهل مراقبة ملفات العرض المؤقتة لـ Blade لمنع إعادة تشغيل السيرفر بلا داعٍ
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

