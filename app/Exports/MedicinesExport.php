<?php

namespace App\Exports;

use App\Models\Medicine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MedicinesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all medicines with their category relation.
     */
    public function collection()
    {
        return Medicine::with('category')->get();
    }

    /**
     * Map each row of the medicine data.
     *
     * @param Medicine $medicine
     */
    public function map($medicine): array
    {
        return [
            $medicine->id,
            $medicine->category_id,
            $medicine->category?->getTranslation('category_name', 'en'),
            $medicine->category?->getTranslation('category_name', 'ar'),
            $medicine->getTranslation('medicine_name', 'en'),
            $medicine->getTranslation('medicine_name', 'ar'),
            $medicine->getTranslation('medicine_description', 'en'),
            $medicine->getTranslation('medicine_description', 'ar'),
            $medicine->getTranslation('medicine_usage', 'en'),
            $medicine->getTranslation('medicine_usage', 'ar'),
            $medicine->getTranslation('medicine_side_effects', 'en'),
            $medicine->getTranslation('medicine_side_effects', 'ar'),
            $medicine->medicine_slug,
            $medicine->medicine_price,
            $medicine->medicine_expiry_date,
            $medicine->created_at?->toDateTimeString(),
            $medicine->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Headers for the Excel sheet.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Category ID',
            'Category Name (EN)',
            'Category Name (AR)',
            'Medicine Name (EN)',
            'Medicine Name (AR)',
            'Description (EN)',
            'Description (AR)',
            'Usage (EN)',
            'Usage (AR)',
            'Side Effects (EN)',
            'Side Effects (AR)',
            'Slug',
            'Price',
            'Expiry Date',
            'Created At',
            'Updated At',
        ];
    }
}
