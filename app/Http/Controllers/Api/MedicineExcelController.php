<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\MedicinesExport;
use App\Imports\MedicinesImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MedicineExcelController extends Controller
{
    /**
     * Export all medicines to Excel.
     * GET /api/v1/medicines/export
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new MedicinesExport, 'medicines.xlsx');
    }

    /**
     * Import medicines from Excel.
     * POST /api/v1/medicines/import
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'], // max 10MB
        ]);

        try {
            $import = new MedicinesImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => __('medicine.messages.imported_successfully') ?? 'Medicines imported successfully.',
                'imported_count' => $import->getImportedCount(),
                'updated_count' => $import->getUpdatedCount(),
            ], 200);

        } catch (\Exception $e) {
            $validationErrors = property_exists($e, 'validationErrors') ? $e->validationErrors : [];

            if (!empty($validationErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => __('medicine.messages.import_validation_failed') ?? 'Some rows in the Excel file failed validation.',
                    'errors' => $validationErrors,
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => __('medicine.messages.import_failed') ?? 'Failed to import Excel file: ' . $e->getMessage(),
            ], 400);
        }
    }
}
