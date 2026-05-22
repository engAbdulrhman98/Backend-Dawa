<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User & Notification Language Lines — English
    |--------------------------------------------------------------------------
    | File location: lang/en/user.php
    */

    'messages' => [
        'created'                => 'User created successfully.',
        'updated'                => 'Profile updated successfully.',
        'deleted'                => 'User deleted successfully.',
        'not_found'              => 'User not found.',
        'unauthorized'           => 'You are not authorized to perform this action.',
        'cannot_delete_self'     => 'You cannot delete your own account.',
        'clients_only'           => 'This action is only available to clients.',
        'nearby_processing'      => 'We are finding nearby pharmacies. You will receive a notification shortly.',
        'notification_read'      => 'Notification marked as read.',
        'notifications_read_all' => 'All notifications marked as read.',
        'notification_deleted'   => 'Notification deleted.',
        'notifications_cleared'  => 'All read notifications have been cleared.',
    ],

    'validation' => [

        'name' => [
            'required' => 'Your name is required.',
            'min'      => 'Your name must be at least 2 characters.',
            'max'      => 'Your name must not exceed 100 characters.',
        ],

        'email' => [
            'required' => 'Your email address is required.',
            'email'    => 'Please enter a valid email address.',
            'unique'   => 'This email address is already registered.',
        ],

        'password' => [
            'required'  => 'A password is required.',
            'confirmed' => 'The passwords do not match.',
            'min'       => 'Password must be at least 8 characters and include letters and numbers.',
        ],

        'role' => [
            'required' => 'A role is required.',
            'in'       => 'Invalid role. Valid: super-admin, pharmacy-owner, branch-manager, client.',
        ],

        'pharmacy_id' => [
            'required'   => 'A pharmacy must be assigned for this role.',
            'exists'     => 'The selected pharmacy does not exist.',
            'prohibited' => 'A pharmacy cannot be assigned to this user type.',
        ],

        'branch_id' => [
            'required'   => 'A branch must be assigned for branch managers.',
            'exists'     => 'The selected branch does not exist.',
            'prohibited' => 'A branch cannot be assigned to this user type.',
        ],

    ],

    'fields' => [
        'name'                       => 'Name',
        'email'                      => 'Email',
        'password'                   => 'Password',
        'role'                       => 'Role',
        'pharmacy'                   => 'Pharmacy',
        'branch'                     => 'Branch',
        'email_verified_at'          => 'Email Verified At',
        'unread_notifications_count' => 'Unread Notifications',
        'created_at'                 => 'Joined At',
        'updated_at'                 => 'Last Updated',
    ],

    'roles' => [
        'super-admin'    => 'Super Admin',
        'pharmacy-owner' => 'Pharmacy Owner',
        'branch-manager' => 'Branch Manager',
        'client'         => 'Customer',
    ],

    'notifications' => [
        'low_stock' => [
            'title' => 'Low Stock Alert',
            'body'  => ':medicine is running low at :branch (only :quantity left).',
        ],
        'nearby_pharmacies' => [
            'title'              => 'Nearby Pharmacies Found',
            'body'               => 'We found :count pharmacies within :radius km of your location.',
            'body_with_medicine' => 'We found :count pharmacies near you with :medicine in stock.',
        ],
    ],

];
