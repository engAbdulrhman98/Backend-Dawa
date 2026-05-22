<?php

return [

    'messages' => [
        'created' => 'City created successfully.',
        'updated' => 'City updated successfully.',
        'deleted' => 'City deleted successfully.',
        'not_found' => 'City not found.',
    ],

    'validation' => [
        'failed' => 'The given data was invalid.',
        'governorate_required' => 'The governorate id is required.',
        'governorate_not_found' => 'The selected governorate does not exist.',
        'name_required' => 'The city name field is required and must be an array.',
        'name_en_required' => 'The English city name is required.',
        'name_ar_required' => 'The Arabic city name is required.',
        'name_en_max' => 'The English city name must not exceed 255 characters.',
        'name_ar_max' => 'The Arabic city name must not exceed 255 characters.',
    ],

    'labels' => [
        'id' => 'ID',
        'governorate_id' => 'Governorate',
        'city_name' => 'City Name',
        'city_slug' => 'Slug',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ],
    'created'  => 'City created successfully.',
    'updated'  => 'City updated successfully.',
    'deleted'  => 'City deleted successfully.',
    'not_found' => 'City not found.',

    'validation1' => [
        'failed'          => 'Validation failed.',
        'name_required'   => 'City name is required.',
        'name_en_required' => 'City name in English is required.',
        'name_en_max'     => 'City name in English must not exceed 255 characters.',
        'name_ar_required' => 'City name in Arabic is required.',
        'name_ar_max'     => 'City name in Arabic must not exceed 255 characters.',
    ],
];
