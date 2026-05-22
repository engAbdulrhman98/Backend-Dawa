<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Medicine Language Lines — English
    |--------------------------------------------------------------------------
    | File location: lang/en/medicine.php
    */

    // ── Success messages ──────────────────────────────────────────────────
    'messages' => [
        'created'      => 'Medicine created successfully.',
        'updated'      => 'Medicine updated successfully.',
        'deleted'      => 'Medicine deleted successfully.',
        'not_found'    => 'Medicine not found.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],

    // ── Validation messages ───────────────────────────────────────────────
    'validation' => [

        'category_id' => [
            'required' => 'The category field is required.',
            'exists'   => 'The selected category does not exist.',
        ],

        'medicine_name' => [
            'required' => 'The medicine name is required.',
        ],

        'medicine_name_en' => [
            'required' => 'The medicine name in English is required.',
            'min'      => 'The medicine name in English must be at least 2 characters.',
            'max'      => 'The medicine name in English must not exceed 150 characters.',
        ],

        'medicine_name_ar' => [
            'required' => 'The medicine name in Arabic is required.',
            'min'      => 'The medicine name in Arabic must be at least 2 characters.',
            'max'      => 'The medicine name in Arabic must not exceed 150 characters.',
        ],

        'medicine_description_en' => [
            'max' => 'The medicine description in English must not exceed 1000 characters.',
        ],

        'medicine_description_ar' => [
            'max' => 'The medicine description in Arabic must not exceed 1000 characters.',
        ],

        'medicine_usage_en' => [
            'max' => 'The medicine usage in English must not exceed 1000 characters.',
        ],

        'medicine_usage_ar' => [
            'max' => 'The medicine usage in Arabic must not exceed 1000 characters.',
        ],

        'medicine_side_effects_en' => [
            'max' => 'The side effects in English must not exceed 1000 characters.',
        ],

        'medicine_side_effects_ar' => [
            'max' => 'The side effects in Arabic must not exceed 1000 characters.',
        ],

        'medicine_price' => [
            'required' => 'The medicine price is required.',
            'numeric'  => 'The medicine price must be a valid number.',
            'min'      => 'The medicine price must be at least 0.',
            'max'      => 'The medicine price must not exceed 99,999.99.',
        ],

        'image' => [
            'image' => 'The uploaded file must be an image.',
            'max'   => 'The image must not exceed 2MB.',
        ],
    ],

    // ── Field labels ──────────────────────────────────────────────────────
    'fields' => [
        'category'      => 'Category',
        'name'          => 'Medicine Name',
        'description'   => 'Description',
        'usage'         => 'Usage',
        'side_effects'  => 'Side Effects',
        'price'         => 'Price',
        'average_price' => 'Average Price',
        'total_stock'   => 'Total Stock',
        'image'         => 'Medicine Image',
        'slug'          => 'Slug',
        'created_at'    => 'Created At',
        'updated_at'    => 'Updated At',
    ],

];
