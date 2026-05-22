<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get statistics for the dashboard.
     * GET /api/v1/dashboard/stats
     * Auth: any authenticated user (super-admin, pharmacy-owner)
     */
    public function stats(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'medicines_count'  => Medicine::count(),
                    'pharmacies_count' => Pharmacy::count(),
                    'branches_count'   => Branch::count(),
                    'users_count'      => User::count(),
                ],
            ]);
        }

        if ($user->isPharmacyOwner()) {
            $pharmacyId = $user->pharmacy_id;

            if (!$pharmacyId) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'pharmacies_count' => 0,
                        'branches_count'   => 0,
                        'medicines_count'  => 0,
                    ],
                ]);
            }

            $branchesCount = Branch::where('pharmacy_id', $pharmacyId)->count();
            $medicinesCount = Medicine::whereHas('branches', function ($query) use ($pharmacyId) {
                $query->where('branches.pharmacy_id', $pharmacyId);
            })->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'pharmacies_count' => 1,
                    'branches_count'   => $branchesCount,
                    'medicines_count'  => $medicinesCount,
                ],
            ]);
        }

        // Default fallback for other roles
        return response()->json([
            'status' => 'success',
            'data' => [
                'medicines_count'  => 0,
                'pharmacies_count' => 0,
                'branches_count'   => 0,
                'users_count'      => 0,
            ],
        ]);
    }
}
