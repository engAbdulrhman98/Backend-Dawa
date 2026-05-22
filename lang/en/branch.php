<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Branch Language Lines — English
    |--------------------------------------------------------------------------
    | File location: lang/en/branch.php
    */

    // ── Success messages ──────────────────────────────────────────────────
    'messages' => [
        'created'      => 'Branch created successfully.',
        'updated'      => 'Branch updated successfully.',
        'deleted'      => 'Branch deleted successfully.',
        'not_found'    => 'Branch not found.',
        'unauthorized' => 'You are not authorized to perform this action.',
    ],

    // ── Validation messages ───────────────────────────────────────────────
    'validation' => [

        'pharmacy_id' => [
            'required' => 'The pharmacy field is required.',
            'exists'   => 'The selected pharmacy does not exist.',
        ],

        'city_id' => [
            'required' => 'The city field is required.',
            'exists'   => 'The selected city does not exist.',
        ],

        'branch_name' => [
            'required' => 'The branch name is required.',
        ],

        'branch_name_en' => [
            'required' => 'The branch name in English is required.',
            'min'      => 'The branch name in English must be at least 2 characters.',
            'max'      => 'The branch name in English must not exceed 150 characters.',
        ],

        'branch_name_ar' => [
            'required' => 'The branch name in Arabic is required.',
            'min'      => 'The branch name in Arabic must be at least 2 characters.',
            'max'      => 'The branch name in Arabic must not exceed 150 characters.',
        ],

        'branch_address' => [
            'required' => 'The branch address is required.',
        ],

        'branch_address_en' => [
            'required' => 'The branch address in English is required.',
            'min'      => 'The branch address in English must be at least 5 characters.',
            'max'      => 'The branch address in English must not exceed 255 characters.',
        ],

        'branch_address_ar' => [
            'required' => 'The branch address in Arabic is required.',
            'min'      => 'The branch address in Arabic must be at least 5 characters.',
            'max'      => 'The branch address in Arabic must not exceed 255 characters.',
        ],

        'branch_phone_en' => [
            'max' => 'The phone number in English must not exceed 20 characters.',
        ],

        'branch_phone_ar' => [
            'max' => 'The phone number in Arabic must not exceed 20 characters.',
        ],

        'latitude' => [
            'between' => 'Latitude must be between -90 and 90.',
        ],

        'longitude' => [
            'between' => 'Longitude must be between -180 and 180.',
        ],

        'image' => [
            'image' => 'The uploaded file must be an image.',
            'max'   => 'The image must not exceed 2MB.',
        ],

    ],

    // ── Field labels ──────────────────────────────────────────────────────
    'fields' => [
        'pharmacy'       => 'Pharmacy',
        'city'           => 'City',
        'governorate'    => 'Governorate',
        'name'           => 'Branch Name',
        'address'        => 'Branch Address',
        'phone'          => 'Branch Phone',
        'latitude'       => 'Latitude',
        'longitude'      => 'Longitude',
        'map_url'        => 'Map Link',
        'directions_url' => 'Directions Link',
        'image'          => 'Branch Image',
        'total_stock'    => 'Total Stock',
        'has_low_stock'  => 'Has Low Stock',
        'distance_km'    => 'Distance (km)',
        'created_at'     => 'Created At',
        'updated_at'     => 'Updated At',
    ],

    // ── Dashboard section labels ───────────────────────────────────────────
    'dashboard' => [
        'low_stock'     => 'Low Stock Medicines',
        'expiring_soon' => 'Expiring Soon',
        'total_stock'   => 'Total Stock',
        'nearby'        => 'Nearby Branches',
    ],

    // ── Search ────────────────────────────────────────────────────────────
    'search' => [
        'placeholder' => 'Search branches by name...',
        'no_results'  => 'No branches found matching your search.',
        'results_for' => 'Results for ":query"',
    ],

];
