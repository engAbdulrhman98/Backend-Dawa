<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicinesImport implements ToCollection, WithHeadingRow
{
    protected array $errors = [];
    protected int $importedCount = 0;
    protected int $updatedCount = 0;

    /**
     * Process the collection of rows.
     */
    public function collection(Collection $rows)
    {
        $rowIndex = 1; // Row 1 is usually the heading row

        foreach ($rows as $row) {
            $rowIndex++;

            // Map aliases (e.g., 'name_en' or 'medicine_name_en')
            $nameEn = $row['medicine_name_en'] ?? $row['name_en'] ?? null;
            $nameAr = $row['medicine_name_ar'] ?? $row['name_ar'] ?? null;
            $price = $row['price'] ?? $row['medicine_price'] ?? null;

            // Form data to validate
            $dataToValidate = [
                'id'            => $row['id'] ?? null,
                'name_en'       => $nameEn,
                'name_ar'       => $nameAr,
                'price'         => $price,
                'category_id'   => $row['category_id'] ?? null,
                'category_slug' => $row['category_slug'] ?? null,
                'expiry_date'   => $row['expiry_date'] ?? null,
            ];

            // Validation rules
            $validator = Validator::make($dataToValidate, [
                'id'            => ['nullable', 'integer'],
                'name_en'       => ['required', 'string', 'min:2', 'max:150'],
                'name_ar'       => ['required', 'string', 'min:2', 'max:150'],
                'price'         => ['required', 'numeric', 'min:0', 'max:99999.99'],
                'category_id'   => ['nullable', 'integer', 'exists:categories,id'],
                'category_slug' => ['nullable', 'string', 'exists:categories,category_slug'],
                'expiry_date'   => ['nullable'],
            ]);

            if ($validator->fails()) {
                $this->errors[] = [
                    'row' => $rowIndex,
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // Resolve Category
            $categoryId = null;
            if (!empty($row['category_id'])) {
                $categoryId = (int) $row['category_id'];
            } elseif (!empty($row['category_slug'])) {
                $category = Category::bySlug($row['category_slug'])->first();
                if ($category) {
                    $categoryId = $category->id;
                }
            } elseif (!empty($row['category_name_en'])) {
                $category = Category::byName($row['category_name_en'], 'en')->first();
                if ($category) {
                    $categoryId = $category->id;
                }
            } elseif (!empty($row['category_name_ar'])) {
                $category = Category::byName($row['category_name_ar'], 'ar')->first();
                if ($category) {
                    $categoryId = $category->id;
                }
            }

            // Parse expiry date
            $expiryDate = null;
            if (!empty($row['expiry_date'])) {
                try {
                    if (is_numeric($row['expiry_date'])) {
                        $expiryDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['expiry_date'])->format('Y-m-d');
                    } else {
                        $expiryDate = date('Y-m-d', strtotime($row['expiry_date']));
                    }
                } catch (\Exception $e) {
                    $this->errors[] = [
                        'row' => $rowIndex,
                        'errors' => ['Invalid expiry date format: ' . $row['expiry_date']],
                    ];
                    continue;
                }
            }

            // Try to find existing record for updating
            $medicine = null;
            if (!empty($row['id'])) {
                $medicine = Medicine::find($row['id']);
            }
            
            // Or look up by slug if slug is provided
            if (!$medicine && !empty($row['slug'])) {
                $medicine = Medicine::bySlug($row['slug'])->first();
            } elseif (!$medicine && !empty($row['medicine_slug'])) {
                $medicine = Medicine::bySlug($row['medicine_slug'])->first();
            }

            $medicineData = [
                'category_id' => $categoryId,
                'medicine_name' => [
                    'en' => $nameEn,
                    'ar' => $nameAr,
                ],
                'medicine_description' => [
                    'en' => $row['description_en'] ?? $row['medicine_description_en'] ?? null,
                    'ar' => $row['description_ar'] ?? $row['medicine_description_ar'] ?? null,
                ],
                'medicine_usage' => [
                    'en' => $row['usage_en'] ?? $row['medicine_usage_en'] ?? null,
                    'ar' => $row['usage_ar'] ?? $row['medicine_usage_ar'] ?? null,
                ],
                'medicine_side_effects' => [
                    'en' => $row['side_effects_en'] ?? $row['medicine_side_effects_en'] ?? null,
                    'ar' => $row['side_effects_ar'] ?? $row['medicine_side_effects_ar'] ?? null,
                ],
                'medicine_price' => $price,
            ];

            // If medicine exists, update it, otherwise create
            if ($medicine) {
                $medicine->update($medicineData);
                // Also update expiry date if it exists in the database columns
                if ($expiryDate) {
                    $medicine->medicine_expiry_date = $expiryDate;
                    $medicine->save();
                }
                $this->updatedCount++;
            } else {
                $newMedicine = Medicine::create($medicineData);
                if ($expiryDate) {
                    $newMedicine->medicine_expiry_date = $expiryDate;
                    $newMedicine->save();
                }
                $this->importedCount++;
            }
        }

        // If there are errors, throw a custom exception so the controller can catch it and report it
        if (!empty($this->errors)) {
            $exception = new \Exception('Validation failed on some rows.');
            // Attach details to the exception
            $exception->validationErrors = $this->errors;
            throw $exception;
        }
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
