<?php

namespace App\Http\Controllers;

use App\Services\DestinationImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * DestinationController
 *
 * Menyediakan endpoint API untuk frontend mendapatkan gambar
 * destinasi yang sudah terverifikasi dari database.
 */
class DestinationController extends Controller
{
    public function __construct(
        protected DestinationImageService $imageService
    ) {}

    /**
     * GET /api/destinations/image?place=Jembatan+Ampera
     *
     * Kembalikan URL gambar untuk satu destinasi.
     */
    public function getImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'place' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Parameter place diperlukan.'], 422);
        }

        $result = $this->imageService->getImageForPlace($request->input('place'));

        return response()->json($result);
    }

    /**
     * POST /api/destinations/images
     *
     * Batch lookup — kirim array nama tempat, terima semua URL sekaligus.
     * Lebih hemat HTTP request dari frontend.
     *
     * Body: { "places": ["Jembatan Ampera", "Benteng Kuto Besak", ...] }
     */
    public function getBatchImages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'places'   => 'required|array|min:1|max:50',
            'places.*' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'  => 'Format tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $results = $this->imageService->getBatchImages($request->input('places'));

        return response()->json([
            'images' => $results,
            'total'  => count($results),
        ]);
    }
}
