<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    */

    'validation' => [
        'category_name_required' => 'The category name is required.',
        'category_name_en_required' => 'The English category name is required.',
        'category_name_ar_required' => 'The Arabic category name is required.',
        'category_name_array' => 'The category name must be an array.',
        'category_name_en_string' => 'The English category name must be a string.',
        'category_name_ar_string' => 'The Arabic category name must be a string.',
    ],

    /*
    |--------------------------------------------------------------------------
    | General Messages
    |--------------------------------------------------------------------------
    */

    'messages' => [
        'deleted' => 'Category deleted successfully.',
        'has_medicines' => 'Cannot delete a category that has medicines assigned to it.',
    ],
    'created'  => 'Category created successfully.',
    'updated'  => 'Category updated successfully.',
    'deleted'  => 'Category deleted successfully.',
    'not_found'=> 'Category not found.',

    'validation1' => [
        'failed'          => 'Validation failed.',
        'name_required'   => 'Category name is required.',
        'name_en_required'=> 'Category name in English is required.',
        'name_en_max'     => 'Category name in English must not exceed 255 characters.',
        'name_ar_required'=> 'Category name in Arabic is required.',
        'name_ar_max'     => 'Category name in Arabic must not exceed 255 characters.',
        'slug_required'   => 'Category slug is required.',
        'slug_unique'     => 'This slug is already taken.',
        'slug_regex'      => 'Slug may only contain lowercase letters, numbers, and hyphens.',
        'slug_max'        => 'Slug must not exceed 255 characters.',
    ],
];