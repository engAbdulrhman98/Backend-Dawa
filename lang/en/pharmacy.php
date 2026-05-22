<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pharmacy Language Lines — English
    |--------------------------------------------------------------------------
    | File location: lang/en/pharmacy.php
    */

    // ── Success messages ──────────────────────────────────────────────────
    'messages' => [
        'created'         => 'Pharmacy created successfully.',
        'updated'         => 'Pharmacy updated successfully.',
        'deleted'         => 'Pharmacy deleted successfully.',
        'not_found'       => 'Pharmacy not found.',
        'unauthorized'    => 'You are not authorized to perform this action.',
        'no_branch_found' => 'No nearby branch found for this pharmacy.',
    ],

    // ── Validation messages ───────────────────────────────────────────────
    'validation' => [

        'pharmacy_name' => [
            'required' => 'The pharmacy name is required.',
        ],

        'pharmacy_name_en' => [
            'required' => 'The pharmacy name in English is required.',
            'min'      => 'The pharmacy name in English must be at least 2 characters.',
            'max'      => 'The pharmacy name in English must not exceed 150 characters.',
        ],

        'pharmacy_name_ar' => [
            'required' => 'The pharmacy name in Arabic is required.',
            'min'      => 'The pharmacy name in Arabic must be at least 2 characters.',
            'max'      => 'The pharmacy name in Arabic must not exceed 150 characters.',
        ],

        'image' => [
            'image' => 'The uploaded file must be an image.',
            'max'   => 'The image must not exceed 2MB.',
        ],

    ],

    // ── Field labels ──────────────────────────────────────────────────────
    'fields' => [
        'name'         => 'Pharmacy Name',
        'slug'         => 'Slug',
        'logo'         => 'Logo',
        'branch_count' => 'Branch Count',
        'has_stock'    => 'Has Stock',
        'branches'     => 'Branches',
        'owners'       => 'Owners',
        'managers'     => 'Managers',
        'medicines'    => 'Medicines',
        'created_at'   => 'Created At',
        'updated_at'   => 'Updated At',
    ],

    // ── Dashboard section labels ───────────────────────────────────────────
    'dashboard' => [
        'low_stock'      => 'Low Stock Medicines',
        'expiring_soon'  => 'Expiring Soon',
        'nearest_branch' => 'Nearest Branch',
        'total_branches' => 'Total Branches',
    ],

    // ── Search ────────────────────────────────────────────────────────────
    'search' => [
        'placeholder'  => 'Search pharmacies by name...',
        'no_results'   => 'No pharmacies found matching your search.',
        'results_for'  => 'Results for ":query"',
    ],

];
