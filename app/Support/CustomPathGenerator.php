<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use Illuminate\Support\Str;

class CustomPathGenerator implements PathGenerator
{
    /**
     * مسار الملف الأصلي
     */
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    /**
     * مسار الصور المصغرة (Conversions)
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * مسار الصور المتجاوبة (Responsive Images)
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    /**
     * الدالة المسؤولة عن بناء المسار الديناميكي
     */
    protected function getBasePath(Media $media): string
    {
        $model = $media->model;

        // تخصيص مسار الأدوية
        if ($media->model_type === \App\Models\Medicine::class) {
            return "medicines";
        }

        // تخصيص مسار الفروع
        if ($media->model_type === \App\Models\Branch::class) {
            return "branches";
        }

        // تخصيص مسار الصيدليات
        if ($media->model_type === \App\Models\Pharmacy::class) {
            return "pharmacies";
        }

        // استخراج اسم الكلاس (مثال: User أو Post)
        $modelName = class_basename($media->model_type);

        // تحويل الاسم إلى صيغة الجمع وبأحرف صغيرة (مثال: users أو posts)
        $folderName = Str::plural(strtolower($modelName));

        // الـ ID الخاص بالـ Model (مثال: 1)
        $modelId = $media->model_id;

        // الـ ID الخاص بالميديا نفسها لمنع تداخل الملفات
        $mediaId = $media->id;

        // النتيجة ستكون مثلاً: users/1/5
        return "{$folderName}/{$modelId}/{$mediaId}";
    }
}
