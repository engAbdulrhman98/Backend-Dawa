<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    /*
    |--------------------------------------------------------------------------
    | Auth Language Lines — English
    |--------------------------------------------------------------------------
    | File location: lang/en/auth.php
    */

    'messages' => [
        'login_success'    => 'Logged in successfully.',
        'logout_success'   => 'Logged out successfully.',
        'logout_all_success' => 'Logged out from all devices.',
        'register_success' => 'Account created successfully.',
        'token_refreshed'  => 'Token refreshed successfully.',
        'invalid_credentials' => 'The provided credentials are incorrect.',
        'unauthenticated'  => 'You must be logged in to access this.',
        'unauthorized'     => 'You are not authorized to perform this action.',
    ],

    'validation' => [
        'name' => [
            'required' => 'Your name is required.',
            'min'      => 'Your name must be at least 2 characters.',
        ],
        'email' => [
            'required' => 'Your email is required.',
            'email'    => 'Please enter a valid email address.',
            'unique'   => 'This email is already registered.',
        ],
        'password' => [
            'required'  => 'Your password is required.',
            'confirmed' => 'The passwords do not match.',
            'min'       => 'Password must be at least 8 characters including letters and numbers.',
        ],
    ],

    // Role-based redirect hints returned with login response
    'redirects' => [
        'admin.dashboard'    => 'Super Admin Dashboard',
        'pharmacy.dashboard' => 'Pharmacy Dashboard',
        'branch.dashboard'   => 'Branch Dashboard',
        'client.home'        => 'Home',
    ],
    'created'  => 'Medicine created successfully.',
    'updated'  => 'Medicine updated successfully.',
    'deleted'  => 'Medicine deleted successfully.',
    'not_found'=> 'Medicine not found.',

    'validation1' => [
        'failed'               => 'Validation failed.',
        'name_required'        => 'Medicine name is required.',
        'name_en_required'     => 'Medicine name in English is required.',
        'name_en_max'          => 'Medicine name in English must not exceed 255 characters.',
        'name_ar_required'     => 'Medicine name in Arabic is required.',
        'name_ar_max'          => 'Medicine name in Arabic must not exceed 255 characters.',
        'description_array'    => 'Medicine description must be an array.',
        'description_en_string'=> 'Medicine description in English must be a string.',
        'description_ar_string'=> 'Medicine description in Arabic must be a string.',
        'slug_required'        => 'Medicine slug is required.',
        'slug_unique'          => 'This slug is already taken.',
        'slug_regex'           => 'Slug may only contain lowercase letters, numbers, and hyphens.',
        'barcode_unique'       => 'This barcode is already in use.',
        'image_invalid'        => 'The uploaded file must be a valid image.',
        'image_max'            => 'Image size must not exceed 2MB.',
    ],
    
    'failed1'            => 'These credentials do not match our records.',
    'password1'          => 'The provided password is incorrect.',
    'throttle1'          => 'Too many login attempts. Please try again in :seconds seconds.',
    'unauthenticated'   => 'You must be logged in to access this resource.',
    'unauthorized'      => 'You do not have permission to perform this action.',
    'token_invalid'     => 'The authentication token is invalid.',
    'token_expired'     => 'The authentication token has expired.',
    'token_not_found'   => 'Authentication token not found.',
    'logout_success'    => 'Logged out successfully.',
    'logout_all_success'=> 'Logged out from all devices successfully.',

    'roles' => [
        'unauthorized'            => 'You do not have sufficient permissions to access this resource.',
        'required_super_admin'    => 'This action is restricted to super admins only.',
        'required_owner'          => 'This action is restricted to pharmacy owners only.',
        'required_owner_or_admin' => 'This action is restricted to pharmacy owners or super admins only.',
        'required_client'         => 'This action is restricted to registered clients only.',
    ],

    'register' => [
        'success' => 'Account created successfully.',
        'failed'  => 'An error occurred while creating the account. Please try again.',
    ],

    'password_reset' => [
        'sent'           => 'A password reset link has been sent to your email.',
        'success'        => 'Your password has been reset successfully.',
        'failed'         => 'An error occurred while resetting the password.',
        'invalid_token'  => 'The reset token is invalid or has expired.',
        'user_not_found' => 'No user is associated with this email address.',
    ],
];
